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
		$this->load->model('Master_model', 'AHModel');
	}
}
