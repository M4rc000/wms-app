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
        $this->load->model('AdminHead_model', 'AHModel');
    }

    public function receiving_raw()
    {
        $data['title'] = 'Receiving Raw Material';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/receiving_raw', $data);
        $this->load->view('templates/footer');
    }

    public function receiving_wip()
    {
        $data['title'] = 'Receiving WIP Material';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/receiving_wip', $data);
        $this->load->view('templates/footer');
    }

    public function manage_storage()
	{

		$data['title'] = 'Manage Storage';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->model('Warehouse_model', 'WModel');
		$data['storage'] = $this->AModel->getListStorage();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/manage_storage', $data);
		$this->load->view('templates/footer');
	}

    public function delivery_item()
	{

		$data['title'] = 'Manage Delivery Item';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/delivery_item', $data);
		$this->load->view('templates/footer');
	}

    public function demand_forecasting_stock()
    {
        $data['title'] = 'Demand Forecasting Stock';
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/demand_forecasting_stock', $data);
		$this->load->view('templates/footer');
    }
}
