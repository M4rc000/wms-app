<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class AdminHead extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->model('AdminHead_model','AHModel');
    }
	
	public function dashboard(){
        $data['title'] = 'Dashboard';

        $data['email'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
        $data['name'] = $this->db->get_where('users', ['Name' => $this->session->userdata('name')])->row_array();
        
        // $data['user'] = $this->AModel->getAllUsers();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);   
        $this->load->view('templates/sidebar', $data);   
        $this->load->view('adminhead/dashboard', $data);
        $this->load->view('templates/footer');
	}

	public function manage_user()
	{
		$data['title'] = 'Manage User';

		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$data['name'] = $this->db->get_where('users', ['Name' => $this->session->userdata('name')])->row_array();


		$data['users'] = $this->AHModel->getAllUsers();
		$data['roles'] = $this->AHModel->getAllRoles();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('adminhead/manage_user', $data);
		$this->load->view('templates/footer');
	}

	public function manage_role()
	{
		$data['title'] = 'Manage Role';

		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['name'] = $this->db->get_where('user', ['name' => $this->session->userdata('name')])->row_array();

		$this->load->model('Admin_model', 'AModel');

		$data['roles'] = $this->AModel->getAllRoles();
		$data['menu'] = $this->AModel->getAllMenu();
		$data['mensub'] = $this->AModel->getMenSub();
		$data['lastRoleId'] = $this->AModel->getLastRoleId();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('admin/manage_user_role', $data);
		$this->load->view('templates/footer');
	}

	public function UpdateConfigRole()
	{
		$role_id = $this->input->post('role');
		$sub_menu = $this->input->post('sub_menu');
		$menu_ids = $this->input->post('menu_ids');
		$submenu_ids = $this->input->post('submenu_ids');
		$all_sub_menus = $this->input->post('all_sub_menus');

		// Delete existing data
		$this->db->where('role_id', $role_id);
		$this->db->delete('user_access_submenu');

		// Insert new access configuration
		foreach ($all_sub_menus as $index => $submenu_id) {
			$menu_id = $menu_ids[$index];
			if (isset($sub_menu[$submenu_id])) {
				$data = [
					'role_id' => $role_id,
					'menu_id' => $menu_id,
					'submenu_id' => $submenu_id,
				];
				$this->db->insert('user_access_submenu', $data);

				// RECORD MANAGE ROLE LOG
				$query_log = $this->db->last_query();
				$log_data = [
					'affected_table' => 'user_access_submenu',
					'queries' => $query_log,
					'Crtdt' => date('Y-m-d H:i:s'),
					'Crtby' => $this->input->post('user')
				];
				$this->db->insert('manage_role_log', $log_data);
			}
		}

		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Role configuration updated!</div>');
		redirect('admin/manage_role');
	}

	public function manage_menu()
	{
		$data['title'] = 'Manage Menu';

		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$data['name'] = $this->db->get_where('users', ['Name' => $this->session->userdata('name')])->row_array();

		$data['menus'] = $this->AHModel->getAllMenu();
		$data['lastMenuId'] = $this->AHModel->getLastMenuId();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('adminhead/manage_menu', $data);
		$this->load->view('templates/footer');
	}

	public function manage_submenu()
	{
		$data['title'] = 'Manage Sub-Menu';

		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();
		$data['name'] = $this->db->get_where('users', ['Name' => $this->session->userdata('name')])->row_array();


		$data['menus'] = $this->AHModel->getAllMenu();
		$data['submenus'] = $this->AHModel->getAllSubMenu();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('adminhead/manage_sub_menu', $data);
		$this->load->view('templates/footer');
	}


	// ACTION
	// MANAGE USER
	public function AddUser(){
		$Data = array(
			'Name' => $this->input->post('name'),
			'Email' => $this->input->post('email'),
			'Password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'Role_id' => $this->input->post('role'),
			'Active' => $this->input->post('active'),
			'Created_at' => date('d-m-Y H:i'),
			'Created_by' => $this->input->post('user_id')
		);

		$this->AHModel->insertData('users', $Data);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'users',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_AddUser', 'New user has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddUser', 'Failed to add a new user');
		}

		redirect('adminhead/manage_user');
	}

	public function EditUser(){
		$id = $this->input->post('id');
		$Data = array(
			'Name' => $this->input->post('name'),
			'Email' => $this->input->post('email'),
			'Password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'Role_id' => $this->input->post('role'),
			'Active' => $this->input->post('active'),
			'Updated_at' => date('Y-m-d H:i:s'),
			'Updated_by' => $this->input->post('user_id')
		);

		$this->AHModel->updateData('users', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'users',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $id, $log_data);
			$this->session->set_flashdata('SUCCESS_EditUser', 'User has been successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_EditUser', 'Failed to update a user');
		}

		redirect('adminhead/manage_user');
	}

	public function deleteUser()
	{
		$id = $this->input->post('id');
		$this->AHModel->deleteData('users', $id);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'users',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_deleteUser', 'User has been successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_deleteUser', 'Failed to delete a user');
		}

		redirect('adminhead/manage_user');
	}


	// MANAGE USER ROLE
	public function addRole()
	{
		$id = $this->input->post('role_id');
		$role = $this->input->post('role');

		$Data = array(
			'id' => $id,
			'role' => $role,
			'crtdt' => date('d-m-Y h:i'),
			'crtby' => $this->input->post('user'),
			'upddt' => date('d-m-Y h:i'),
			'updby' => $this->input->post('user')
		);

		$this->AModel->insertData('user_role', $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD MANAGE ROLE LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Crtdt' => date('Y-m-d H:i:s'),
				'Crtby' => $this->input->post('user')
			];
			$this->db->insert('manage_role_log', $log_data);
			$this->session->set_flashdata('SUCCESS_addRole', 'New role has successfully added');
		} else {
			$this->session->set_flashdata('FAILED_addRole', 'Failed to add a new role');
		}

		redirect('admin/manage_role');
	}

	public function editRole()
	{
		$id = $this->input->post('id');
		$role = $this->input->post('role');

		$Data = array(
			'role' => $role,
			'upddt' => date('d-m-Y h:i'),
			'updby' => $this->input->post('user')
		);

		$this->AModel->updateData('user_role', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD MANAGE ROLE LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Crtdt' => date('Y-m-d H:i:s'),
				'Crtby' => $this->input->post('user')
			];
			$this->db->insert('manage_role_log', $log_data);
			$this->session->set_flashdata('SUCCESS_updateRole', 'Role has successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_updateRole', 'Failed to update a role');
		}

		redirect('admin/manage_role');
	}

	public function deleteRole()
	{
		$this->load->model('Admin_model', 'AModel');

		$id = $this->input->post('id');
		$role = $this->input->post('role');
		$this->AModel->deleteData('user_role', $id);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD MANAGE ROLE LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Crtdt' => date('Y-m-d H:i:s'),
				'Crtby' => $this->input->post('user')
			];
			$this->db->insert('manage_role_log', $log_data);
			$this->session->set_flashdata('SUCCESS_deleteRole', $role);
		} else {
			$this->session->set_flashdata('FAILED_deleteRole', $role);
		}

		header("Location: " . base_url('admin/manage_role'));
	}

	public function roleAccess($role_id)
	{
		$data['title'] = 'Role Access';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['name'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();


		$data['role'] = $this->db->get_where('user_role', ['id' => $role_id])->row_array();

		$data['menu'] = $this->db->get('user_menu')->result_array();
		$data['accessmenu'] = $this->AModel->getMenuAccess($role_id);
		$data['accesssubmenu'] = $this->AModel->getSubMenuAccess($role_id);
		$data['roles'] = $this->AModel->getAllRoles();

		$this->db->select('user_menu.*');
		$this->db->from('user_menu');
		$this->db->join('user_access_menu', 'user_menu.id = user_access_menu.menu_id AND user_access_menu.role_id = ' . $role_id, 'left');
		$this->db->where('user_access_menu.menu_id IS NULL');
		$data['menus'] = $this->db->get()->result_array();

		$this->db->select('user_sub_menu.*');
		$this->db->from('user_sub_menu');
		$this->db->join('user_access_submenu', 'user_sub_menu.id = user_access_submenu.submenu_id AND user_access_submenu.role_id = ' . $role_id, 'left');
		$this->db->where('user_access_submenu.submenu_id IS NULL');
		$data['submenus'] = $this->db->get()->result_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/role-access', $data);
		$this->load->view('templates/footer');

		$this->session->set_flashdata('role_access', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            The access has been changed!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>');
	}
	function addRoleAccessMenu()
	{
		$role_id = $this->input->post('role_id');
		$data = [
			'role_id' => $role_id,
			'menu_id' => $this->input->post('menu_id')
		];

		$this->AModel->insertData('user_access_menu', $data);

		// RECORD MANAGE ROLE LOG
		$query_log = $this->db->last_query();
		$log_data = [
			'affected_table' => 'user_access_menu',
			'queries' => $query_log,
			'Crtdt' => date('Y-m-d H:i:s'),
			'Crtby' => $this->input->post('user')
		];
		$this->db->insert('manage_role_log', $log_data);
		$this->session->set_flashdata('success', 'New menu Access permissions have been added');
		redirect('admin/roleAccess/' . $role_id);
	}

	function DeleteRoleAccessMenu()
	{
		$id = $this->input->post('id');
		$role_id = $this->input->post('role_id');
		$this->AModel->deleteData('user_access_menu', $id);

		// RECORD MANAGE ROLE LOG
		$query_log = $this->db->last_query();
		$log_data = [
			'affected_table' => 'user_access_menu',
			'queries' => $query_log,
			'Crtdt' => date('Y-m-d H:i:s'),
			'Crtby' => $this->input->post('user')
		];
		$this->db->insert('manage_role_log', $log_data);

		$this->session->set_flashdata('success', 'Menu Access permissions have been deleted');
		redirect('admin/roleAccess/' . $role_id);
	}

	function addRoleAccessSubMenu()
	{
		$role_id = $this->input->post('role_id');
		$data = [
			'role_id' => $role_id,
			'menu_id' => $this->input->post('meenu_id'),
			'submenu_id' => $this->input->post('submenu_id'),
		];

		$this->AModel->insertData('user_access_submenu', $data);

		// RECORD MANAGE ROLE LOG
		$query_log = $this->db->last_query();
		$log_data = [
			'affected_table' => 'user_access_submenu',
			'queries' => $query_log,
			'Crtdt' => date('Y-m-d H:i:s'),
			'Crtby' => $this->input->post('user')
		];
		$this->db->insert('manage_role_log', $log_data);

		$this->session->set_flashdata('success', 'New Submenu Access permissions have been added');
		redirect('admin/roleAccess/' . $role_id);
	}

	function DeleteRoleAccessSubMenu()
	{
		$id = $this->input->post('id');
		$role_id = $this->input->post('role_id');
		$this->AModel->deleteData('user_access_submenu', $id);

		// RECORD MANAGE ROLE LOG
		$query_log = $this->db->last_query();
		$log_data = [
			'affected_table' => 'user_access_submenu',
			'queries' => $query_log,
			'Crtdt' => date('Y-m-d H:i:s'),
			'Crtby' => $this->input->post('user')
		];
		$this->db->insert('manage_role_log', $log_data);

		$this->session->set_flashdata('success', 'Submenu Access permissions have been deleted');
		redirect('admin/roleAccess/' . $role_id);
	}

	function getSubMenuBasedOnMenu()
	{
		$menu_id = $this->input->post('menu_id');
		$role_id = $this->input->post('role_id');

		$this->db->select("user_sub_menu.*");
		$this->db->from("user_sub_menu");
		$this->db->join("user_access_submenu", "user_sub_menu.id = user_access_submenu.submenu_id AND user_access_submenu.role_id = $role_id", "left outer");
		$this->db->where("user_access_submenu.submenu_id IS NULL");
		$this->db->where("user_sub_menu.menu_id", $menu_id);
		$query = $this->db->get();
		$result = $query->result_array();
		echo json_encode($result);
	}

	// MANAGE MENU
	public function AddMenu()
	{
		$Data = array(
			'Id' => $this->input->post('id'),
			'Name' => $this->input->post('menu'),
			'Created_at' => date('d-m-Y H:i:s'),
			'Created_by' => $this->input->post('user_id'),
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $this->input->post('user_id')
		);

		$this->AHModel->insertData('user_menu', $Data);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_AddMenu', 'New menu has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddMenu', 'Failed to add new menu');
		}
		redirect('adminhead/manage_menu');
	}

	public function editMenu()
	{
		$id = $this->input->post('id');
		$Data = array(
			'Name' => $this->input->post('menu'),
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $this->input->post('user_id')
		);

		$this->AHModel->updateData('user_menu', $id, $Data);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_editMenu', 'Menu has been successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_editMenu', 'Failed to update the menu');
		}

		redirect('adminhead/manage_menu');
	}

	public function deleteMenu()
	{
		$id = $this->input->post('id');

		$this->AHModel->deleteData('user_menu', $id);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $this->input->post('user_id')
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_deleteMenu', 'Menu has been successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_deleteMenu', 'Failed to delete the menu');
		}

		redirect('adminhead/manage_menu');
	}

	// MANAGE SUBMENU
	public function AddSubMenu()
	{
		$Data = array(
			'menu_id' => $this->input->post('menu_id'),
			'title' => $this->input->post('title'),
			'url' => $this->input->post('url'),
			'icon' => $this->input->post('icon'),
			'is_active' => $this->input->post('active'),
			'crtdt' => date('d-m-Y H:i'),
			'crtby' => $this->input->post('user'),
			'upddt' => date('d-m-Y H:i'),
			'updby' => $this->input->post('user')
		);

		$this->AModel->insertData('user_sub_menu', $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			$this->session->set_flashdata('SUCCESS_AddSubMenu', 'New a submenu has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddSubMenu', 'Failed to add a new submenu');
		}

		redirect('admin/manage_sub_menu');
	}

	public function editSubMenu()
	{
		$id = $this->input->post('id');
		$Data = array(
			'menu_id' => $this->input->post('menu_id'),
			'title' => $this->input->post('title'),
			'url' => $this->input->post('url'),
			'icon' => $this->input->post('icon'),
			'is_active' => $this->input->post('active'),
			'upddt' => date('d-m-Y H:i'),
			'updby' => $this->input->post('user')
		);

		$this->AModel->updateData('user_sub_menu', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			$this->session->set_flashdata('SUCCESS_editSubMenu', 'Submenu has been successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_editSubMenu', 'Failed to update a submenu');
		}

		redirect('admin/manage_sub_menu');
	}

	public function DeleteSubMenu()
	{
		$this->load->model('Admin_model', 'AModel');

		$id = $this->input->post('id');

		$this->AModel->deleteData('user_sub_menu', $id);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			$this->session->set_flashdata('SUCCESS_DeleteSubMenu', 'Submenu has been successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_DeleteSubMenu', 'Failed to delete a submenu');
		}

		redirect('admin/manage_sub_menu');
	}

	public function manage_storage()
	{

		$data['title'] = 'Manage Storage';

		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['name'] = $this->db->get_where('user', ['name' => $this->session->userdata('name')])->row_array();

		$this->load->model('Warehouse_model', 'WModel');
		$data['storage'] = $this->AModel->getListStorage();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/manage_storage', $data);
		$this->load->view('templates/footer');
	}

	function getBoxBySloc()
	{
		$idStorage = $this->input->post('idStorage');

		$query = $this->db->query("SELECT b.id_box, b.no_box, b.weight, b.box_type
                FROM box b
                LEFT JOIN list_storage ls ON ls.id_box = b.id_box
                WHERE ls.sloc = '$idStorage'")->row_array();
		$query_length = $this->db->query("SELECT b.id_box, b.no_box, b.weight, b.box_type
                FROM box b
                LEFT JOIN list_storage ls ON ls.id_box = b.id_box
                WHERE ls.sloc = '$idStorage'")->num_rows();

		$result = [
			'result' => $query,
			'result_length' => $query_length
		];

		echo json_encode($result);
	}

	public function manage_box()
	{
		$data['title'] = 'Manage Box';

		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['name'] = $this->db->get_where('user', ['name' => $this->session->userdata('name')])->row_array();

		$config['base_url'] = base_url('admin/manage_box');
		$config['total_rows'] = $this->AModel->countAllBoxes();
		$config['per_page'] = 50;

		// Add these two lines for pagination styling (Bootstrap 4)
		$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">';
		$config['full_tag_close'] = '</ul></nav>';
		$config['first_link'] = '&laquo;';
		$config['first_tag_open'] = '<li class="page-item">';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '&raquo;';
		$config['last_tag_open'] = '<li class="page-item">';
		$config['last_tag_close'] = '</li>';
		$config['next_link'] = '&rsaquo;';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&lsaquo;';
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
		$config['attributes'] = array('class' => 'page-link');

		$this->pagination->initialize($config);

		$data['start'] = $this->uri->segment(3, 0);
		$data['list_box'] = $this->AModel->getBoxes($config['per_page'], $data['start']);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/manage_box', $data);
		$this->load->view('templates/footer');
	}

	public function get_box_data()
	{
		$data = $this->AModel->getBox();

		echo json_encode($data);
	}

	public function history_log()
	{
		$data['title'] = 'History log';

		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['name'] = $this->db->get_where('user', ['name' => $this->session->userdata('name')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('admin/history_log', $data);
		$this->load->view('templates/footer');
	}

	public function get_log_data()
	{
		$table = $this->input->post('data');
		$data = $this->AModel->getLogData($table);

		echo json_encode($data);
	}
}
