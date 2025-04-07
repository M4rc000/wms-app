<?php

function is_logged_in()
{
	$ci = get_instance();
	if (!$ci->session->userdata('email')) {
		redirect('auth');
	} else {
		$menu = $ci->uri->segment(1);
		
		if($menu == 'user'){
			return true;
		}
		else{
			$role_id = $ci->session->userdata('role_id');
	
			$queryMenu = $ci->db->get_where('user_menu', ['Name' => $menu])->row_array();
			$menu_id = $queryMenu['Id'];
			$userAccess = $ci->db->get_where('user_access_menu', [
				'Role_id' => $role_id,
				'Menu_id' => $menu_id
			]);
	
			// echo "Role ID: ", $role_id;
			// echo '<br>';
			// echo '<br>';
			// echo "Menu: ", $menu;
			// echo '<br>';
			// echo '<br>';
			// echo "Menu ID: ", $menu_id;
			// echo '<br>';
			// echo '<br>';
			// var_dump($queryMenu);
			// echo '<br>';
			// echo '<br>';
			// var_dump($menu_id);
			// echo '<br>';
			// echo '<br>';
			// var_dump($userAccess);
			// die;
	
			if ($userAccess->num_rows() < 1) {
				redirect('auth/blocked');
			}
		}

	}
}

// function check_access($role_id, $menu_id)
// {
// 	$ci = get_instance();

// 	$ci->db->where('role_id', $role_id);
// 	$ci->db->where('menu_id', $menu_id);
// 	$result = $ci->db->get('user_access_menu');

// 	if ($result->num_rows() > 0) {
// 		return "checked='checked'";
// 	}
// }

// function check_access_submenu($role_id, $menu_id, $submenu_id)
// {
// 	$ci = get_instance();

// 	$ci->db->where('role_id', $role_id);
// 	$ci->db->where('menu_id', $menu_id);
// 	$ci->db->where('submenu_id', $submenu_id);
// 	$result = $ci->db->get('user_access_submenu');

// 	if ($result->num_rows() > 0) {
// 		return "checked='checked'";
// 	}
// }
