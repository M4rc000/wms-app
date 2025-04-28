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
		$this->load->model('Management_model', 'MGModel');
	}

	public function report_raw_material(){
		$data['title'] = 'Report Raw Material';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('management/report_raw_material', $data);
		$this->load->view('templates/footer');
	}
	
	public function load_raw_material(){
		$raw_materials = $this->MGModel->getRawMaterials();
		echo json_encode($raw_materials);
	}
	
	// public report_wip_material(){}

	public function load_wip_material(){
		$raw_materials = $this->MGModel->getRawMaterials();
		echo json_encode($raw_materials);
	}
}
