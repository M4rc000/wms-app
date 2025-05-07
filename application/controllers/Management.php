<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Management extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		perform_access_check();
		$this->load->library('form_validation');
		$this->load->library('pagination');
		$this->load->model('Management_model', 'MGModel');
	}
	
	public function report_raw_material()
	{

		$data['title'] = 'Raw Material Report';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('management/report_raw_material', $data);
		$this->load->view('templates/footer');
	}

	public function report_wip_material()
	{

		$data['title'] = 'WIP Material Report';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('management/report_wip_material', $data);
		$this->load->view('templates/footer');
	}

	public function report_material_usage()
	{

		$data['title'] = 'Material Usage Report';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('management/report_material_usage', $data);
		$this->load->view('templates/footer');
	}

	public function report_demand_stock()
	{

		$data['title'] = 'Demand Forecast Report';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('management/report_demand_stock', $data);
		$this->load->view('templates/footer');
	}
}
