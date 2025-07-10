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
		$material_id = $this->input->post('material_id');
		$status = $this->input->post('status');

		// Tambahkan log untuk melihat data POST
		log_message('debug', 'material_id: ' . $material_id);
		log_message('debug', 'status: ' . $status);

		if (empty($material_id) || empty($status)) {
			log_message('debug', 'Missing input, redirecting...');
			$this->session->set_flashdata('ERROR', 'Cannot update: missing ID or status.');
			redirect('driver/monitoring_delivery');
			return;
		}

		// Jalankan update
		$this->db->where('Id', $material_id); // <- pastikan 'Id' sesuai dengan nama kolom DB kamu
		$this->db->update('dispatch_note', ['Status' => $status]);

		// Tambahkan log untuk melihat apakah update berhasil
		if ($this->db->affected_rows() > 0) {
			log_message('debug', 'Status updated successfully for ID ' . $material_id);
			$this->session->set_flashdata('SUCCESS', 'Delivery status updated.');
		} else {
			log_message('debug', 'No rows updated. ID might be wrong or status same as before.');
			$this->session->set_flashdata('ERROR', 'No data updated.');
		}

		redirect('driver/monitoring_delivery');
	}

	public function load_monitoring_delivery(){
		$monitoring_delivery = $this->DModel->getDeliveryItem();
		echo json_encode($monitoring_delivery);
	}
}