<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

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

	public function addReceivingRawMaterial()
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
}
