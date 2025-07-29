<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin_model extends CI_Model {
	public function getListMaterial()
	{
		return $this->db->get('raw_material')->result_array();
	}
	
	public function getListWIP(){
		return $this->db->get('wip_material')->result_array();
	}

	public function insertData($table, $Data)
	{
		return $this->db->insert($table, $Data);
	}
    public function getUsersDriver() 
    {
        // Mengambil semua pengguna dengan Role_id 3 (misalnya, role untuk driver)
        // Pastikan Role_id sesuai dengan yang Anda inginkan
	    return $this->db->get_where('users', ['Role_id' => 3])->result_array();
    }

	public function deleteData($table, $id)
	{
		$this->db->where('Id', $id);
		$this->db->delete($table);
	}

	public function updateData($table, $id, $Data)
	{
		$this->db->where('Id', $id);
		$this->db->update($table, $Data);
	}

	public function CheckDuplicateForecast($targetMonth){
		$likeValue = "%$targetMonth%";
		$query = $this->db->query("SELECT * FROM demand_forecast WHERE Date LIKE ?", array($likeValue));
		$result = $query->result_array();
		if($result){
			return 1;
		}
		return null;
	}

	public function get_historical_data_multi($months) {
        $sql = "SELECT 
                    s.Material_no,
                    m.Material_name,
                    m.Unit,
                    DATE_FORMAT(s.Created_at, '%d') AS day,
                    SUM(s.Qty) AS total_usage
                FROM storage s
                JOIN raw_material m ON s.Material_no = m.Material_no
                WHERE s.Transaction_type = 'Out'
                  AND s.Material_no LIKE 'RW%'
                  AND DATE_FORMAT(s.Created_at, '%Y-%m') IN (?, ?, ?)
                GROUP BY s.Material_no, day
                ORDER BY s.Material_no, day ASC";
        $query = $this->db->query($sql, $months);
        return $query->result_array();
    }

	private function impute_missing_values($data_series) {
        $n = count($data_series);
        // Pass 1: Cek hari terdekat
        for ($i = 0; $i < $n; $i++) {
            if ($data_series[$i] === null) {
                $prev = ($i > 0 ? $data_series[$i - 1] : null);
                $next = ($i < $n - 1 ? $data_series[$i + 1] : null);
                if ($prev !== null && $next !== null) {
                    $data_series[$i] = ($prev + $next) / 2;
                } elseif ($prev !== null) {
                    $data_series[$i] = $prev;
                } elseif ($next !== null) {
                    $data_series[$i] = $next;
                }
            }
        }
        // Pass 2: Jika masih null, gunakan moving average dengan window=5
        for ($i = 0; $i < $n; $i++) {
            if ($data_series[$i] === null) {
                $sum = 0;
                $count = 0;
                for ($j = max(0, $i - 2); $j <= min($n - 1, $i + 2); $j++) {
                    if ($data_series[$j] !== null) {
                        $sum += $data_series[$j];
                        $count++;
                    }
                }
                $data_series[$i] = ($count > 0) ? $sum / $count : 0;
            }
        }
        return $data_series;
    }

	public function calculate_linear_regression_forecast_by_material($historicalDataByMaterial, $forecastDays) {
        // Buat array untuk setiap hari (1 sampai max)
        $daysData = [];
        foreach($historicalDataByMaterial as $record) {
            $day = intval($record['day']);
            if (!isset($daysData[$day])) {
                $daysData[$day] = [];
            }
            $daysData[$day][] = floatval($record['total_usage']);
        }
        

        // Tentukan max hari dari data historis
        $maxDay = empty($daysData) ? 0 : max(array_keys($daysData));
        // Buat series lengkap, indeks 1 sampai $maxDay.
        // Jika ada data, ambil nilai rata-rata untuk hari tersebut, jika tidak ada, set sebagai null.
        $data_series = [];
        for ($i = 1; $i <= $maxDay; $i++) {
            if (isset($daysData[$i])) {
                $data_series[] = array_sum($daysData[$i]) / count($daysData[$i]);
            } else {
                $data_series[] = null;
            }
        }

        
        // Lakukan imputasi untuk data yang hilang
        $data_series = $this->impute_missing_values($data_series);
        
        // Lakukan regresi linear pada data_series
        $N = count($data_series);
        $x = [];
        $y = [];
        for ($i = 0; $i < $N; $i++) {
            $x[] = $i + 1;
            $y[] = $data_series[$i];
        }
        
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;
        for ($i = 0; $i < $N; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumXX += $x[$i] * $x[$i];
        }
        
        $m = ($N * $sumXY - $sumX * $sumY) / ($N * $sumXX - $sumX * $sumX);
        $c = ($sumY - $m * $sumX) / $N;
        
        // Prediksi untuk periode forecast: x_pred mulai dari (N + 1) hingga (N + forecastDays)
        $forecasts = [];
        for ($i = 1; $i <= $forecastDays; $i++) {
            $x_pred = $N + $i;
            $predicted = $m * $x_pred + $c;
            $forecasts[] = round($predicted, 2);
        }
        return $forecasts;
    }

    public function save_forecast($targetMonth, $forecastData) {
        $this->db->trans_start();
        foreach ($forecastData as $dayData) {
            $date = date('Y-m-d', strtotime($targetMonth . '-' . str_pad($dayData['day'], 2, '0', STR_PAD_LEFT)));
            foreach ($dayData['materials'] as $material) {
                // Jika unit adalah Kg, pembulatan ke bawah
                if (isset($material['unit']) && $material['unit'] === 'Kg') {
                    $material['Qty_predict'] = floor($material['Qty_predict']);
                }
                $data = [
                    'Material_no'    => $material['Material_no'],
                    'Material_name'  => $material['Material_name'],
                    'Qty_predict'    => $material['Qty_predict'],
                    'Date'           => $date,
                    'Active'         => 1,
                    'Created_at'     => date('Y-m-d H:i:s'),
                    'Created_by'     => 'System',
                    'Updated_at'     => date('Y-m-d H:i:s'),
                    'Updated_by'     => 'System'
                ];
                $this->db->insert('demand_forecast', $data);
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function getManageStorage() {
    return $this->db->query("
        SELECT 
            Id,
            Material_no,
            Material_name,
            Qty,
            Unit,
            Transaction_type,
            Updated_at
        FROM storage
    ")->result_array();
}

	public function getDeliveryItem(){
		return $this->db->query("SELECT 
			Id,
			Product_no,
			Product_name,
			Qty,
			Unit,
			Active,
			Status,
			Driver_id,
			Delivery_date
			FROM
				dispatch_note")->result_array();
	}

	public function getDeliveryById($id)
    {
        // return $this->db->get_where('dispatch_note', ['id' => $id])->result_array();
        
        $this->db->select('dn.*, c.Name AS Client_name, c.Short_name');
        $this->db->from('dispatch_note dn');
        $this->db->join('client c', 'LEFT(dn.Product_no, 3) = c.Short_name', 'left');
        $this->db->where('dn.Id', $id);
        return $this->db->get()->result_array();
    }
}
