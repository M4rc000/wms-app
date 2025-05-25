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

	public function editDeliveryStatus(){
		$delivery_status = $this->input->post('delivery_status');
		if (empty($delivery_status)) {
			$this->session->set_flashdata('ERROR', 'Cant update delivery status.');
			redirect('driver/monitoring_delivery');
			return;
		}
	}
}
