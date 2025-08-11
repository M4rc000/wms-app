<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class AdminHead extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
		perform_access_check();
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->model('AdminHead_model','AHModel');   
    }
	
	//Dashboard
	public function dashboard(){
		$data['title'] = 'Dashboard';
		$data['user'] = $this->db->get_where('users', [
			'Email' => $this->session->userdata('email')
		])->row_array();
		
		$data['material_name'] = $this->AHModel->get_material_list();
		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);   
		$this->load->view('templates/sidebar', $data);   
		$this->load->view('adminhead/dashboard', $data);
		$this->load->view('templates/footer');
	}

	public function load_material_receiving(){
		$material_no = $this->input->post('material_no');
		$period = $this->input->post('period');

		$materials = $this->AHModel->get_material_receiving($material_no, $period);

		echo json_encode($materials);
	}

	public function load_material_usage(){
		$material_no = $this->input->post('material_no');
		$period = $this->input->post('period');

		$materials = $this->AHModel->get_material_usage($material_no, $period);

		echo json_encode($materials);
	}

	public function load_demand_forecast(){
		$material_no = $this->input->post('material_no');
		$period = $this->input->post('period');

		$materials = $this->AHModel->get_demand_forecast($material_no, $period);

		echo json_encode($materials);
	}
	//End of Dashboard

	public function manage_user()
	{
		$data['title'] = 'Manage User';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

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
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['roles'] = $this->AHModel->getAllRoles();
		$data['menu'] = $this->AHModel->getAllMenu();
		$data['mensub'] = $this->AHModel->getMenSub();
		$data['lastRoleId'] = $this->AHModel->getLastRoleId();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('adminhead/manage_user_role', $data);
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
		$data['title'] = 'Manage Submenu';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

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
		//Security check
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		
		$Data = array(
			'Name' => $this->input->post('name'),
			'Email' => $this->input->post('email'),
			'Password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'Role_id' => $this->input->post('role'),
			'Active' => $this->input->post('active'),
			'Created_at' => date('d-m-Y H:i'),
			'Created_by' => $usersession['Id']
		);
		// Insert user data
		$this->AHModel->insertData('users', $Data);
		// Check if the insert was successful
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'users',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_AddUser', 'New user has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddUser', 'Failed to add a new user');
		}

		redirect('adminhead/manage_user');
	}

	public function EditUser(){
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		$id = $this->input->post('id');
		$Data = array(
			'Name' => $this->input->post('name'),
			'Email' => $this->input->post('email'),
			'Password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'Role_id' => $this->input->post('role'),
			'Active' => $this->input->post('active'),
			'Updated_at' => date('Y-m-d H:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->AHModel->updateData('users', $id, $Data);
		$check_insert = $this->db->affected_rows();

		// if ($check_insert > 0) {
		// 	// LOG
		// 	$query_log = $this->db->last_query();
		// 	$log_data = [
		// 		'affected_table' => 'users',
		// 		'queries' => $query_log,
		// 		'Created_at' => date('Y-m-d H:i:s'),
		// 		'Created_by' => $usersession['Id']
		// 	];
		// 	$this->AHModel->insertData('log', $id, $log_data);
		// 	$this->session->set_flashdata('SUCCESS_EditUser', 'User has been successfully updated');
		// } else {
		// 	$this->session->set_flashdata('FAILED_EditUser', 'Failed to update a user');
		// }

		redirect('adminhead/manage_user');
	}

	public function deleteUser()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
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
				'Created_by' => $usersession['Id']
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
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$role = $this->input->post('role');

		$Data = array(
			'Id' => $id,
			'Name' => $role,
			'Created_at' => date('Y-m-d h:i:s'),
			'Created_by' => $usersession['Id'],
			'Updated_at' => date('Y-m-d h:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->AHModel->insertData('user_role', $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_addRole', 'New role has successfully added');
		} else {
			$this->session->set_flashdata('FAILED_addRole', 'Failed to add a new role');
		}

		redirect('adminhead/manage_role');
	}

	public function editRole()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$role = $this->input->post('role');

		$Data = array(
			'Name' => $role,
			'Updated_at' => date('Y-m-d h:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->AHModel->updateData('user_role', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_updateRole', 'Role has successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_updateRole', 'Failed to update a role');
		}

		redirect('adminhead/manage_role');
	}

	public function deleteRole()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$role = $this->input->post('role');
		$this->AHModel->deleteData('user_role', $id);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// RECORD MANAGE ROLE LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_role',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_deleteRole', $role);
		} else {
			$this->session->set_flashdata('FAILED_deleteRole', $role);
		}

		header("Location: " . base_url('adminhead/manage_role'));
	}

	public function roleAccess($role_id)
	{
		$data['title'] = 'Role Access';
		$data['user'] = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		$data['role'] = $this->db->get_where('user_role', ['id' => $role_id])->row_array();

		$data['menu'] = $this->db->get('user_menu')->result_array();
		$data['accessmenu'] = $this->AHModel->getMenuAccess($role_id);
		$data['accesssubmenu'] = $this->AHModel->getSubMenuAccess($role_id);
		$data['roles'] = $this->AHModel->getAllRoles();

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
		$this->load->view('adminhead/role-access', $data);
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
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$role_id = $this->input->post('role_id');
		$data = [
			'Role_id' => $role_id,
			'Menu_id' => $this->input->post('menu_id')
		];

		$this->AHModel->insertData('user_access_menu', $data);

		$check_insert = $this->db->affected_rows();


		if($check_insert){
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_access_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_ADD_ROLE_ACCESS_MENU', 'New menu Access permissions have been added');
		}
		else{
			$this->session->set_flashdata('FAILED_ADD_ROLE_ACCESS_MENU', 'Failed to add menu Access permissions');
		}

		redirect('adminhead/roleAccess/' . $role_id);
	}

	function DeleteRoleAccessMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$role_id = $this->input->post('role_id');
		$this->AHModel->deleteData('user_access_menu', $id);

		$check_insert = $this->db->affected_rows();
		if($check_insert){
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_access_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_DELETE_ROLE_ACCESS_MENU', 'Menu Access permissions have been deleted');
		}
		else{
			$this->session->set_flashdata('FAILED_DELETE_ROLE_ACCESS_MENU', 'Failed to delete Menu Access permissions');
		}

		redirect('adminhead/roleAccess/' . $role_id);
	}

	function addRoleAccessSubMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$role_id = $this->input->post('role_id');
		$data = [
			'Role_id' => $role_id,
			'Menu_id' => $this->input->post('meenu_id'),
			'Submenu_id' => $this->input->post('submenu_id'),
		];

		$this->AHModel->insertData('user_access_submenu', $data);

		$check_insert = $this->db->affected_rows();

		if($check_insert){
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_access_submenu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
	
			$this->db->insert('log', $log_data);
	
			$this->session->set_flashdata('SUCCESS_ADD_ROLE_ACCESS_SUBMENU', 'New Submenu Access permissions have been added');
		}
		else{
			$this->session->set_flashdata('FAILED_ADD_ROLE_ACCESS_SUBMENU', 'Failed to add Submenu Access permissions');
		}

		redirect('adminhead/roleAccess/' . $role_id);
	}

	function DeleteRoleAccessSubMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		$id = $this->input->post('id');
		$role_id = $this->input->post('role_id');
		$this->AHModel->deleteData('user_access_submenu', $id);

		$check_insert = $this->db->affected_rows();
		if($check_insert){
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_access_submenu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->db->insert('log', $log_data);
	
			$this->session->set_flashdata('SUCCESS_ADD_ROLE_ACCESS_SUBMENU', 'Submenu Access permissions have been deleted');
		}
		else{
			$this->session->set_flashdata('FAILED_ADD_ROLE_ACCESS_SUBMENU', 'Failed to delete Submenu Access permissions');
		}

		redirect('adminhead/roleAccess/' . $role_id);
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
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$Data = array(
			'Id' => $this->input->post('id'),
			'Name' => $this->input->post('menu'),
			'Created_at' => date('d-m-Y H:i:s'),
			'Created_by' => $usersession['Id'],
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $usersession['Id']
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
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_AddMenu', 'New menu has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddMenu', 'Failed to add new menu');
		}
		redirect('adminhead/manage_menu');
	}

	public function EditMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$Data = array(
			'Name' => $this->input->post('menu'),
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $usersession['Id']
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
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_editMenu', 'Menu has been successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_editMenu', 'Failed to update the menu');
		}

		redirect('adminhead/manage_menu');
	}

	public function DeleteMenu(){
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');

		$this->db->where('Menu_id', $id);
		$this->db->delete('user_sub_menu');

		$this->AHModel->deleteData('user_menu', $id);

		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query(); 
			$log_data = [
				'affected_table' => 'user_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s', time()),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_deleteMenu', 'Menu and its sub-menus have been successfully deleted.');
		} else {
			$this->session->set_flashdata('FAILED_deleteMenu', 'Failed to delete the menu. It might have already been removed.');
		}

		redirect('adminhead/manage_menu');
	}

	// MANAGE SUBMENU
	public function AddSubMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$Data = array(
			'Menu_id' => $this->input->post('menu_id'),
			'Name' => $this->input->post('name'),
			'Url' => $this->input->post('url'),
			'Icon' => $this->input->post('icon'),
			'Active' => $this->input->post('active'),
			'Created_at' => date('d-m-Y H:i:s'),
			'Created_by' => $usersession['Id'],
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->AHModel->insertData('user_sub_menu', $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_sub_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_AddSubMenu', 'New a submenu has been successfully added');
		} else {
			$this->session->set_flashdata('FAILED_AddSubMenu', 'Failed to add a new submenu');
		}

		redirect('adminhead/manage_submenu');
	}

	public function editSubMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}

		$id = $this->input->post('id');
		$Data = array(
			'Menu_id' => $this->input->post('menu_id'),
			'Name' => $this->input->post('name'),
			'Url' => $this->input->post('url'),
			'Icon' => $this->input->post('icon'),
			'Active' => $this->input->post('active'),
			'Updated_at' => date('d-m-Y H:i:s'),
			'Updated_by' => $usersession['Id']
		);

		$this->AHModel->updateData('user_sub_menu', $id, $Data);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_sub_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_editSubMenu', 'Submenu has been successfully updated');
		} else {
			$this->session->set_flashdata('FAILED_editSubMenu', 'Failed to update a submenu');
		}

		redirect('adminhead/manage_submenu');
	}

	public function DeleteSubMenu()
	{
		$usersession = $this->db->get_where('users', ['Email' => $this->session->userdata('email')])->row_array();

		if (empty($usersession['Role_id']) || empty($usersession['Name'])) {
			$this->session->set_flashdata('ERROR', 'Session expired or user not found.');
			redirect('auth');
			return;
		}
		
		$id = $this->input->post('id');

		$this->AHModel->deleteData('user_sub_menu', $id);
		$check_insert = $this->db->affected_rows();

		if ($check_insert > 0) {
			// LOG
			$query_log = $this->db->last_query();
			$log_data = [
				'affected_table' => 'user_sub_menu',
				'queries' => $query_log,
				'Created_at' => date('Y-m-d H:i:s'),
				'Created_by' => $usersession['Id']
			];
			$this->AHModel->insertData('log', $log_data);
			$this->session->set_flashdata('SUCCESS_DeleteSubMenu', 'Submenu has been successfully deleted');
		} else {
			$this->session->set_flashdata('FAILED_DeleteSubMenu', 'Failed to delete a submenu');
		}

		redirect('adminhead/manage_submenu');
	}
}
