<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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
		$this->load->library('pdf');
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

	public function addReceivingRawMaterial(){
		// Get user session
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		// Check if user session is valid
		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		// Get materials from POST request
		$materials = $this->input->post('materials');
		if (empty($materials)) {
			$this->session->set_flashdata('ERROR', 'No materials provided.');
			redirect('admin/receiving_wip');
			return;
		}

		$successfulInserts = 0;

		// Start a transaction to ensure atomicity
		$this->db->trans_start();
		// Loop through each material and prepare data for insertion
		foreach ($materials as $material) {
			$DataReceivingRaw = [
				'Material_no'      => $material['Material_no'],
				'Material_name'    => $material['Material_name'],
				'Qty'              => floatval($material['Qty']),
				'Unit'             => $material['Unit'],
				'Transaction_type' => $material['Transaction_type'],
				'Created_at'       => date('Y-m-d H:i:s'),
				'Created_by'       => $usersession['Id'],
				'Updated_at'       => date('Y-m-d H:i:s'),
				'Updated_by'       => $usersession['Id']
			];
			// Insert data into storage table
			$this->AModel->insertData('storage', $DataReceivingRaw);
			$check_insert = $this->db->affected_rows();

			if ($check_insert > 0) {
				// RECORD BOM LOG
				$query_log = $this->db->last_query();
				$log_data = [
					'affected_table' => 'storage',
					'queries'        => $query_log,
					'Created_at'     => date('Y-m-d H:i:s'),
					'Created_by'     => $usersession['Id']
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

	public function add_delivery_item(){

		$data['title'] = 'New Delivery Item';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['materials'] = $this->AModel->getListWIP();
		$data['users'] = $this->AModel->getUsersDriver();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/add_delivery_item', $data);
        $this->load->view('templates/footer');
    }

	public function addDeliveryNote()
	{
		// Ambil data user dari session
    $usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

    if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
        $this->session->set_flashdata('ERROR', 'Session expired or user not found.');
        redirect('auth');
        return;
    }

    // Ambil data materials dari POST
    $delivery = $this->input->post('materials');
    if (empty($delivery)) {
        $this->session->set_flashdata('ERROR', 'Tidak ada data dikirim.');
        redirect('admin/delivery_item');
        return;
    }

    $successfulInserts = 0;
    // Mulai transaksi database
    $this->db->trans_start();

    foreach ($delivery as $item) {
        // Validasi minimum
        if (empty($item['Product_no']) || empty($item['Product_name']) || empty($item['Qty']) || empty($item['Delivery_date'])) {
            continue;
        }

        // Format tanggal
        $deliveryDate = date('Y-m-d', strtotime($item['Delivery_date']));

        $DataDeliveryStatus = [
            'Product_no'     => $item['Product_no'],
            'Product_name'   => $item['Product_name'],
            'No_SJ'          => $item['No_SJ'] ?? null,
            'No_PO'          => $item['No_PO'] ?? null,
            'Qty'            => floatval($item['Qty']),
            'Unit'           => $item['Unit'] ?? null,
            'Status'         => $item['Status'] ?? 'Pending',
            'Driver_id'      => $item['Driver_id'] ?? null,
            'Delivery_date'  => $deliveryDate,
            'Active'         => 1,
            'Created_at'     => date('Y-m-d H:i:s'),
            'Created_by'     => $usersession['Id'],
            'Updated_at'     => date('Y-m-d H:i:s'),
            'Updated_by'     => $usersession['Id']
        ];

        // Insert ke database
        $this->AModel->insertData('dispatch_note', $DataDeliveryStatus);
        $check_insert = $this->db->affected_rows();

        // Jika berhasil insert, log query-nya
        if ($check_insert > 0) {
            $query_log = $this->db->last_query();
            $log_data = [
                'affected_table' => 'dispatch_note',
                'queries'        => $query_log,
                'Created_at'     => date('Y-m-d H:i:s'),
                'Created_by'     => $usersession['Id']
            ];
            $this->db->insert('log', $log_data);
            $successfulInserts++;

			$dataStorage = [
				'Material_no'      => $item['Product_no'],
				'Material_name'    => $item['Product_name'],
				'Transaction_type' => 'Out',
				'Qty'              => floatval($item['Qty']),
				'Unit'             => $item['Unit'] ?? null,
				'Created_at'       => date('Y-m-d H:i:s'),
				'Created_by'       => $usersession['Id']
			];

			$this->AModel->insertData('storage', $dataStorage);

        }
    }

    // Selesaikan transaksi
    $this->db->trans_complete();
	// Cek apakah transaksi berhasil
    if ($this->db->trans_status() === FALSE) {
        log_message('error', 'Transaksi gagal saat insert dispatch_note');
        $this->session->set_flashdata('ERROR', 'Terjadi kesalahan saat menyimpan data.');
        redirect('admin/add_delivery_item');
        return;
    }
	// Set flashdata untuk sukses
    if ($successfulInserts > 0) {
        $this->session->set_flashdata('SUCCESS_ADD_DELIVERY_ITEM', "$successfulInserts item berhasil disimpan.");
    } else {
        $this->session->set_flashdata('ERROR', 'Tidak ada data yang berhasil disimpan.');
    }

    redirect('admin/delivery_item');

	// 	$materials = $this->input->post('materials');

    // if (!$materials || count($materials) == 0) {
    //     $this->session->set_flashdata('ERROR', 'Tidak ada data dikirim.');
    //     redirect('admin/delivery_item');
    // }

    // $data = [];

    // foreach ($materials as $item) {
    //     if (empty($item['Delivery_date']) || empty($item['Product_no'])) {
    //         log_message('error', 'Item kosong: ' . print_r($item, true));
    //         continue;
    //     }

    //     $deliveryDate = date('Y-m-d', strtotime($item['Delivery_date']));

    //     $data[] = [
    //         'Product_no'     => $item['Product_no'],
    //         'Product_name'   => $item['Product_name'],
    //         'No_SJ'          => $item['No_SJ'],
    //         'No_PO'          => $item['No_PO'],
    //         'Qty'            => floatval($item['Qty']),
    //         'Delivery_date'  => $deliveryDate,
    //     ];
    // }

    // log_message('debug', 'Data akan diinsert: ' . print_r($data, true));

    // if (count($data) > 0) {
    //     $result = $this->db->insert_batch('dispatch_note', $data);

    //     if ($result) {
    //         $this->session->set_flashdata('SUCCESS', 'Data berhasil disimpan.');
    //     } else {
    //         $error = $this->db->error();
    //         log_message('error', 'Gagal insert: ' . print_r($error, true));
    //         $this->session->set_flashdata('ERROR', 'Terjadi kesalahan saat menyimpan.');
    //     }
    // } else {
    //     $this->session->set_flashdata('ERROR', 'Data kosong/tidak valid.');
    // }
	}

	public function delete_delivery_item($id) 
	{
		$this->db->where('Id', $id);	
		$this->db->delete('dispatch_note'); // or soft delete

		$this->session->set_flashdata('SUCCESS_DELETE', 'Item successfuly removed.');
		redirect('admin/delivery_item');
	}

    public function delete_storage_item($id)
	{
		$this->db->where('Id', $id);
		$this->db->delete('storage'); // or soft delete

		$this->session->set_flashdata('SUCCESS_DELETE', 'Item successfully removed.');
		redirect('admin/manage_storage');
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
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$materials = $this->input->post('materials');
		if (empty($materials)) {
			$this->session->set_flashdata('ERROR', 'No materials provided.');
			redirect('admin/receiving_wip');
			return;
		}

		$successfulInserts = 0;

		// Start a transaction to ensure atomicity
		$this->db->trans_start();

		foreach ($materials as $material) {
			$DataReceivingWip = [
				'Material_no'      => $material['Material_no'],
				'Material_name'    => $material['Material_name'],
				'Qty'              => floatval($material['Qty']),
				'Unit'             => $material['Unit'],
				'Transaction_type' => $material['Transaction_type'],
				'Created_at'       => date('Y-m-d H:i:s'),
				'Created_by'       => $usersession['Id'],
				'Updated_at'       => date('Y-m-d H:i:s'),
				'Updated_by'       => $usersession['Id']
			];

			$this->AModel->insertData('storage', $DataReceivingWip);
			$check_insert = $this->db->affected_rows();

			if ($check_insert > 0) {
				// RECORD BOM LOG
				$query_log = $this->db->last_query();
				$log_data = [
					'affected_table' => 'storage',
					'queries'        => $query_log,
					'Created_at'     => date('Y-m-d H:i:s'),
					'Created_by'     => $usersession['Id']
				];
				$this->db->insert('log', $log_data);
				$successfulInserts++;
			}
		}

		// Complete the transaction
		$this->db->trans_complete();

		// Check if all materials were inserted successfully
		if ($this->db->trans_status() && $successfulInserts == count($materials)) {
			$this->session->set_flashdata('SUCCESS_ADD_RECEIVING_WIP', 'All materials added successfully.');
		} else {
			$this->session->set_flashdata('FAILED_ADD_RECEIVING_WIP', 'Failed to add some or all materials.');
		}

		redirect('admin/receiving_wip');
	}

	public function delivery_item(){
		$data['title'] = 'Delivery Item';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/delivery_item', $data);
		$this->load->view('templates/footer');

		// $this->load->view('pdf/pdf_delivery_view', $data);
	}

	public function manage_storage(){
		$data['title'] = 'Manage Storage';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/manage_storage', $data);
		$this->load->view('templates/footer');
	}

	public function load_manage_storage(){
		$manage_storage = $this->AModel->getManageStorage();
		echo json_encode($manage_storage);
	}

	public function load_delivery_item(){
		$delivery_item = $this->AModel->getDeliveryItem();
		echo json_encode($delivery_item);
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
	
	public function EditReceivingMaterial(){
		$id = $this->input->post('user_id');
		$data = [
			'Material_no' => $this->input->post('MaterialIdEdit'),
			'Material_name' => $this->input->post('MaterialNameEdit'),
			'Qty' => $this->input->post('QtyEdit'),
			'Transaction_type' => $this->input->post('TransactionTypeEdit'),
			// '...' => $this->input->post('NameEditModal'),
		];

		$this->AModel->updateData('storage', $id, $data);
		
		//  
		$success = $this->db->affected_rows();

		if ($success > 0) {
			$this->session->set_flashdata('SUCCESS', 'Data Successfully Updated.');
		} else {
			$this->session->set_flashdata('ERROR', 'Update failed or data did not change.');
		}
		
		redirect('admin/manage_storage');
	}

	public function print_delivery_pdf($id){

		// Ambil data berdasarkan ID
		$delivery = $this->AModel->getDeliveryById($id);
		
		if (!$delivery) {
			$this->session->set_flashdata('ERROR', "Data is not found.");
			redirect('admin/delivery_item');	
		}

		// Kirim ke view
		$data = [
			'delivery' => $delivery
		];

		$html = $this->load->view('pdf/pdf_delivery_view', $data, true);

		$this->pdf->loadHtml($html);
		$this->pdf->setPaper('A4', 'portrait');
		$this->pdf->render();
		$this->pdf->stream('surat_jalan_' . $id . '.pdf', ["Attachment" => false]);
	}

}