<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Management extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		$this->load->library('form_validation');
		$this->load->library('pagination');
		$this->load->model('Management_model', 'MGModel');
	}

	public function index()
	{
		$data['title'] = 'Dashboard';

		$data['email'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
		$data['name'] = $this->db->get_where('user', ['name' => $this->session->userdata('name')])->row_array();

		// $data['user'] = $this->AModel->getAllUsers();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('admin/index', $data);
		$this->load->view('templates/footer');
	}
}
