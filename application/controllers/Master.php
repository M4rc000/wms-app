<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Master extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		perform_access_check();
		$this->load->library('form_validation');
		$this->load->library('pagination');
		$this->load->model('Master_model', 'MModel');
	}

	public function raw_material()
	{
		$data['title'] = 'Raw Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/raw_material', $data);
		$this->load->view('templates/footer');
	}

	public function load_raw_material(){
		$raw_materials = $this->MModel->getRawMaterials();
		echo json_encode($raw_materials);
	}

	public function add_raw_material()
	{
		$data['title'] = 'New Raw Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/add_raw_material', $data);
		$this->load->view('templates/footer');
	}

	public function new_raw_material()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$material_no = $this->MModel->generateNewMaterialNo();
		$material_name =  htmlspecialchars($this->input->post('Material_name'));
		$unit = htmlspecialchars($this->input->post('Unit'));
		$duplicate_material = $this->MModel->check_duplicate_raw_material($material_name);


		if (empty($material_no) || empty($material_name) || empty($unit)) {
			$this->session->set_flashdata('ERROR', 'No material provided.');
			redirect('master/raw_material');
			return;
		}

		if($duplicate_material > 0){
			$this->session->set_flashdata('ERROR', 'Data Material is already exists.');
			redirect('master/raw_material');
			return;
		}

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		

		$successfulInserts = 0;

		// Start a transaction to ensure atomicity
		$this->db->trans_start();

		$DataReceivingRaw = [
			'Material_no'      => $material_no,
			'Material_name'    => $material_name,
			'Unit'             => $unit,
			'Created_at'       => date('Y-m-d H:i:s'),
			'Created_by'       => $usersession['Id'],
		];

		$this->MModel->insertData('raw_material', $DataReceivingRaw);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD BOM LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'raw_material',
				'queries'        => $query_log,
				'Created_at'     => date('Y-m-d H:i:s'),
				'Created_by'     => $usersession['Id']
			];
			$this->db->insert('log', $log_data);
			$successfulInserts += 1;
		}
		
		// Complete the transaction
		$this->db->trans_complete();

		// Check if all materials were inserted successfully
		if ($this->db->trans_status() && $successfulInserts == 1) {
			$this->session->set_flashdata('SUCCESS_ADD_RAW_MATERIAL', 'New RAW Material added successfully.');
		} else {
			$this->session->set_flashdata('FAILED_ADD_RAW_MATERIAL', 'Failed to add new RAW material.');
		}

		redirect('master/raw_material');
	}

	public function edit_raw_material($id)
	{
		$data['title'] = 'Edit Raw Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['materials'] = $this->MModel->getMaterialById($id);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/edit_raw_material', $data);
		$this->load->view('templates/footer');
	}

	public function update_raw_material(){
		$Material_no = $this->input->post('Material_no');
		$Material_name = $this->input->post('Material_name');
		$Unit = $this->input->post('Unit');
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$id = $this->input->post('id');
		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		$Data = array(
			'Material_no' => $Material_no,
			'Material_name' => $Material_name,
			'Unit' => $Unit,
			'Updated_at' => date('Y-m-d H:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->MModel->updateData('raw_material', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'raw_material',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_EDIT_RAW_MATERIAL', 'Raw material successfully updated');
			
			$update_storage = $this->MModel->update_data_storage($Material_no, $Unit);
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'storage',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
		} else {
			$this->session->set_flashdata('FAILED_EDIT_RAW_MATERIAL', 'Failed to update a raw material');
		}

		redirect('master/raw_material');
	}

	public function delete_raw_material($id)
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$this->MModel->deleteData('raw_material', $id);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'raw_material',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_DELETE_RAW_MATERIAL', 'Raw Material successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_DELETE_RAW_MATERIAL', 'Failed to delete Raw Material');
		}

		redirect('master/raw_material');
	}

	public function wip_material()
	{
		$data['title'] = 'WIP Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();


		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/wip_material', $data);
		$this->load->view('templates/footer');
	}

	public function load_wip_material(){
		$wip_materials = $this->MModel->getWIPMaterials();
		echo json_encode($wip_materials);
	}

	public function add_wip_material()
	{
		$data['title'] = 'New WIP Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['clients'] = $this->MModel->GetAllClients();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/add_wip_material', $data);
		$this->load->view('templates/footer');
	}

	public function new_wip_material()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$client =  htmlspecialchars($this->input->post('Client'));
		$material_name =  htmlspecialchars($this->input->post('Material_name'));
		$unit = htmlspecialchars($this->input->post('Unit'));
		$duplicate_material = $this->MModel->check_duplicate_wip_material($material_name);

		// CHECK APAKAH DATA MATERIAL DARI USER MASIH KOSONG
		if (empty($material_name) || empty($unit)) {
			$this->session->set_flashdata('ERROR', 'No material provided.');
			redirect('master/wip_material');
			return;
		}

		//  CHECK DUPLICATE DATA MATERIAL NAME DI DB
		if($duplicate_material > 0){
			$this->session->set_flashdata('ERROR', 'Data Material is already exists.');
			redirect('master/wip_material');
			return;
		}

		// CHECK JIKA SESSION USER SUDAH HABIS
		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		
		// GENERATE LAST MATERIAL NO, BERDASARKAN CLIENT
		$material_no = $this->MModel->generateNewWipMaterialNo($client);
		$successfulInserts = 0;

		// Start a transaction to ensure atomicity
		$this->db->trans_start();

		$DataReceivingWIP = [
			'Material_no'      => $material_no,
			'Material_name'    => $material_name,
			'Unit'             => $unit,
			'Created_at'       => date('Y-m-d H:i:s'),
			'Created_by'       => $usersession['Id'],
		];

		$this->MModel->insertData('wip_material', $DataReceivingWIP);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD BOM LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'wip_material',
				'queries'        => $query_log,
				'Created_at'     => date('Y-m-d H:i:s'),
				'Created_by'     => $usersession['Id']
			];
			$this->db->insert('log', $log_data);
			$successfulInserts += 1;
		}
		
		// Complete the transaction
		$this->db->trans_complete();

		// Check if all materials were inserted successfully
		if ($this->db->trans_status() && $successfulInserts == 1) {
			$this->session->set_flashdata('SUCCESS_ADD_WIP_MATERIAL', 'New WIP Material added successfully.');
		} else {
			$this->session->set_flashdata('FAILED_ADD_WIP_MATERIAL', 'Failed to add new WIP material.');
		}

		redirect('master/wip_material');
	}

	public function edit_wip_material($id)
	{
		$data['title'] = 'Edit WIP Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['materials'] = $this->MModel->getWipMaterialById($id);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('master/edit_wip_material', $data);
		$this->load->view('templates/footer');
	}

	public function update_wip_material(){
		$Material_no = $this->input->post('Material_no');
		$Material_name = $this->input->post('Material_name');
		$Unit = $this->input->post('Unit');
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$id = $this->input->post('id');
		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		$Data = array(
			'Material_no' => $Material_no,
			'Material_name' => $Material_name,
			'Unit' => $Unit,
			'Updated_at' => date('Y-m-d H:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->MModel->updateData('wip_material', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'raw_material',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_EDIT_WIP_MATERIAL', 'WIP material successfully updated');
			
			$update_storage = $this->MModel->update_data_storage($Material_no, $Unit);
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'storage',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
		} else {
			$this->session->set_flashdata('FAILED_EDIT_WIP_MATERIAL', 'Failed to update a WIP material');
		}

		redirect('master/wip_material');
	}

	public function delete_wip_material($id)
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$this->MModel->deleteData('wip_material', $id);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'wip_material',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->MModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_DELETE_WIP_MATERIAL', 'WIP Material successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_DELETE_WIP_MATERIAL', 'Failed to delete WIP Material');
		}

		redirect('master/wip_material');
	}
}