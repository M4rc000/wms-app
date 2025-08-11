<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class User extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        // $this->load->model('User_model', 'UModel');
    }
	
	public function index()
	{
        $data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
        $data['name'] = $this->db->get_where('users', ['Name' => $this->session->userdata('name')])->row_array();
        
        $data['menus'] = $this->uri->segment(1);
        
        $data['title'] = 'My Profile';
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar');   
        $this->load->view('templates/sidebar');   
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
	}

	public function update_profile()
	{
		$this->form_validation->set_rules('fullName', 'Full Name', 'required|trim');

		if ($this->form_validation->run() == false) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Failed to update profile!</div>');
			redirect('user');
		} else {
			$fullName = $this->input->post('fullName');
			$email = $this->session->userdata('email');
			$profileImage = null;

			// Handle file upload
			if (!empty($_FILES['profileImage']['name'])) {
				// Set upload path
				$upload_path = FCPATH . 'assets/img/profiles/';
				
				// Create directory if not exists
				if (!is_dir($upload_path)) {
					mkdir($upload_path, 0777, true);
				}
				
				// Ensure directory is writable
				if (!is_writable($upload_path)) {
					chmod($upload_path, 0777);
				}
				
				// Configure upload library
				$config = array(
					'upload_path' => $upload_path,
					'allowed_types' => 'gif|jpg|jpeg|png',
					'max_size' => 2048,
					'file_name' => 'profile_' . time(),
					'overwrite' => TRUE,
					'remove_spaces' => TRUE
				);

				$this->load->library('upload');
				$this->upload->initialize($config);

				// Try to upload file
				if (!$this->upload->do_upload('profileImage')) {
					$error = $this->upload->display_errors('', '');
					$debug_info = sprintf(
						'Upload Path: %s | Path exists: %s | Path writable: %s | File size: %d bytes | File type: %s',
						$upload_path,
						 is_dir($upload_path) ? 'Yes' : 'No',
						 is_writable($upload_path) ? 'Yes' : 'No',
						 isset($_FILES['profileImage']['size']) ? $_FILES['profileImage']['size'] : 0,
						 isset($_FILES['profileImage']['type']) ? $_FILES['profileImage']['type'] : 'unknown'
					);
					
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Failed to upload image: ' . $error . '<br><small>' . $debug_info . '</small></div>');
					redirect('user');
					return;
				}

				// Get uploaded file info
				$uploadData = $this->upload->data();
				$profileImage = $uploadData['file_name'];
			}

			// Update user data in database
			$updateData = ['Name' => $fullName];
			if ($profileImage) {
				$updateData['Image'] = $profileImage;
			}

			$this->db->where('Email', $email);
			$this->db->update('users', $updateData);

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Profile updated successfully!</div>');
			redirect('user');
		}
	}
}