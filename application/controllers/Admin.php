<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
require_once APPPATH . '../vendor/autoload.php';
use Phpml\Regression\LeastSquares;

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
		perform_access_check();
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->model('Admin_model', 'AModel');
    }

    public function receiving_raw()
    {
        $data['title'] = 'Receiving Raw Material';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['materials'] = $this->AModel->getListMaterial();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/receiving_raw', $data);
        $this->load->view('templates/footer');
    }
	
	public function addReceivingRawMaterial($id)
	{
		$materials = $this->input->post('materials');
		if (empty($materials)) {
			$this->session->set_flashdata('ERROR', 'No materials provided.');
			redirect('admin/receiving_raw');
			return;
		}

		$successfulInserts = 0;

		// Start a transaction to ensure atomicity
		$this->db->trans_start();

		foreach ($materials as $material) {
			$DataReceivingRaw = [
				'Material_no'      => $material['Material_no'],
				'Material_name'    => $material['Material_name'],
				'Qty'              => floatval($material['Qty']),
				'Unit'             => $material['Unit'],
				'Transaction_type' => $material['Transaction_type'],
				'Created_at'       => date('Y-m-d H:i:s'),
				'Created_by'       => $this->input->post('user_id'),
				'Updated_at'       => date('Y-m-d H:i:s'),
				'Updated_by'       => $this->input->post('user_id')
			];

			$this->AModel->insertData('storage', $DataReceivingRaw);
			$check_insert = $this->db->affected_rows();

			if ($check_insert > 0) {
				// RECORD BOM LOG
				$query_log = $this->db->last_query();
				$log_data = [
					'affected_table' => 'storage',
					'queries'        => $query_log,
					'Created_at'     => date('Y-m-d H:i:s'),
					'Created_by'     => $this->input->post('user_id')
				];
				$this->db->insert('log', $log_data);
				$successfulInserts++;
			}
		}

		// Complete the transaction
		$this->db->trans_complete();

		// Check if all materials were inserted successfully
		if ($this->db->trans_status() && $successfulInserts == count($materials)) {
			$this->session->set_flashdata('SUCCESS_ADD_RECEIVING_RAW', 'All materials added successfully.');
		} else {
			$this->session->set_flashdata('FAILED_ADD_RECEIVING_RAW', 'Failed to add some or all materials.');
		}

		redirect('admin/receiving_raw');
	}

	public function receiving_wip(){
		$data['title'] = 'Receiving WIP Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['materials'] = $this->AModel->getListWIP();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('admin/receiving_wip', $data);
		$this->load->view('templates/footer');
	}

	public function addReceivingWIPMaterial(){
		
	}

	public function demand_forecasting_stock(){
		$data['title'] = 'Demand Forecast Stock';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('admin/demand_forecast', $data);
		$this->load->view('templates/footer');
	}

	public function demand_forecast() {
		$sample1 = $this->input->post('sample1');
		$sample2 = $this->input->post('sample2');
		$sample3 = $this->input->post('sample3');
		$targetMonth = $this->input->post('target'); // Format "YYYY-MM"
		$date = DateTime::createFromFormat("Y-m", $targetMonth);
		$formattedDate = $date->format("F Y");

		// Cek apakah sample1, sample2, dan sample3 itu sama
		if ($sample1 === $sample2 || $sample2 === $sample3 || $sample1 === $sample3) {
			$this->session->set_flashdata('error_forecasting_stock', "Data Sample 1, 2, dan 3 tidak boleh sama. Silakan pilih tiga bulan historis yang berbeda.");
			redirect('admin/demand_forecasting_stock');
			return;
		}

		$DuplicateMonth = $this->AModel->CheckDuplicateForecast($targetMonth);
		if($DuplicateMonth){
			$this->session->set_flashdata('error_forecasting_stock', "Duplikasi untuk Bulan $formattedDate");
			redirect('admin/demand_forecasting_stock');
			return;
		}

		// Ambil daftar material raw
		$materialList = $this->AModel->getListMaterial();

		// Ambil data historis untuk tiga bulan (dari storage yang sudah di-join)
		$historicalDataAll = $this->AModel->get_historical_data_multi([$sample1, $sample2, $sample3]);
		if (empty($historicalDataAll)) {
			$this->session->set_flashdata('error_forecasting_stock', "Tidak ditemukan data historis untuk bulan $sample1, $sample2, dan $sample3.");
			redirect('admin/demand_forecasting_stock');
			return;
		}

		// Hitung jumlah hari dalam bulan target
		$forecastDays = date('t', strtotime($targetMonth));

		// Susun data prediksi per hari untuk semua material
		$forecastData = [];
		for ($day = 1; $day <= $forecastDays; $day++) {
			$dailyForecast = ['day' => $day, 'materials' => []];
			foreach ($materialList as $material) {
				// Filter historical data untuk material ini
				$materialNo = $material['Material_no'];
				$historicalDataMaterial = array_filter($historicalDataAll, function($item) use ($materialNo) {
					return $item['Material_no'] == $materialNo;
				});
				// Jika tidak ada data historis, gunakan 0 (atau Anda bisa skip material)
				if (empty($historicalDataMaterial)) {
					$predictedQty = 0;
				} else {
					// Urutkan berdasarkan hari (pastikan field 'day' bertipe integer)
					usort($historicalDataMaterial, function($a, $b) {
						return intval($a['day']) - intval($b['day']);
					});
					// Hitung prediksi menggunakan Linear Regression
					// Fungsi mengembalikan array forecast untuk periode forecastDays
					$forecasts = $this->AModel->calculate_linear_regression_forecast_by_material($historicalDataMaterial, $forecastDays);
					$predictedQty = $forecasts[$day - 1];
				}
				// Jika unit adalah Kg, lakukan pembulatan ke bawah
				if (isset($material['Unit']) && $material['Unit'] === 'Kg') {
					$predictedQty = floor($predictedQty);
				}

				$dailyForecast['materials'][] = [
					'Material_no'   => $material['Material_no'],
					'Material_name' => $material['Material_name'],
					'Qty_predict'   => $predictedQty,
					'unit'          => $material['Unit']
				];
			}
			$forecastData[] = $dailyForecast;
		}

		// Simpan hasil forecast ke tabel demand_forecast
		$saveStatus = $this->AModel->save_forecast($targetMonth, $forecastData);

		if ($saveStatus) {
			$this->session->set_flashdata('success_forecasting_stock', "Prediksi untuk bulan $formattedDate berhasil disimpan.");
		} else {
			$this->session->set_flashdata('error_forecasting_stock', "Terjadi kesalahan saat menyimpan prediksi.");
		}

		redirect('admin/demand_forecasting_stock');	
	}
}
