<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Access_model extends CI_Model
{
	public function checkAccess($role_id, $menu_id, $submenu_id = null)
	{
		// Example: match the menu slug to the corresponding menu in your user_menu table
		$this->db->where('Id', $menu_id);
		$menu = $this->db->get('user_menu')->row();

		if (!$menu) {
			return false;
		}
		
		// Check access for the main menu in user_access_menu table
		$this->db->where('Role_id', $role_id);
		$this->db->where('Menu_id', $menu->Id);
		$accessMain = $this->db->get('user_access_menu')->row();

		if (!$accessMain) {
			return false;
		}


		// If no submenu is specified, access is granted based on the menu only.
		if (empty($submenu_id)) {
			return true;
		}

		// Check that the submenu exists and belongs to the menu.
		$this->db->where('Menu_id', $menu->Id);
		$this->db->where('Id', $submenu_id);
		$submenu = $this->db->get('user_sub_menu')->row();


		if (!$submenu) {
			return false;
		}

		// Finally, check if the role also has access to the submenu.
		$this->db->where('Role_id', $role_id);
		$this->db->where('Submenu_id', $submenu->Id);
		$accessSub = $this->db->get('user_access_submenu')->row();

		return $accessSub ? true : false;
	}

	public function getMenuId($menu_name){
		$query = $this->db->query("SELECT Id FROM user_menu WHERE Name LIKE ?", array("%$menu_name%"));
		$row = $query->row(); // gets just the first row as an object
		return isset($row->Id) ? $row->Id : false;
	}

	public function getSubMenuId($submenu_name)
	{
		// Using query binding ensures SQL injection protection
		$query = $this->db->query("SELECT Id FROM user_sub_menu WHERE Name LIKE ?", array("%$submenu_name%"));
		$row = $query->row(); // gets just the first row as an object

		// Return the Id if found, or false if not.
		return isset($row->Id) ? $row->Id : false;
	}
}
