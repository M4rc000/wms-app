<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Driver extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		perform_access_check();
		$this->load->library('form_validation');
		$this->load->library('pagination');
		$this->load->model('Driver_model', 'DModel');
	}

	public function monitoring_delivery()
	{
		$data['title'] = 'Monitoring Delivery';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('driver/monitoring_delivery', $data);
		$this->load->view('templates/footer');
	}

	public function EditDeliveryStatus(){
		$id = $this->input->post('material_id');
		$data = [
			'status' => $this->input->post('StatusEdit'),
			// '...' => $this->input->post('NameEditModal'),
		];

		$this->DModel->updateData('dispatch_note', $id, $data);
		
		//  
		$success = $this->db->affected_rows(); 

		if ($success > 0) {
			$this->session->set_flashdata('SUCCESS', 'Status Successfully Updated.');
		} else {
			$this->session->set_flashdata('ERROR', 'Update Status Failed.');
		}
		
		redirect('driver/monitoring_delivery');
	}

	public function load_monitoring_delivery(){
		$monitoring_delivery = $this->DModel->getDeliveryItem();
		echo json_encode($monitoring_delivery);
	}
}