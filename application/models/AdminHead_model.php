<?php

class AdminHead_model extends CI_Model {
	public function getAllUsers(){
		return $this->db->get_where('users')->result_array();
	}

	public function getAllRoles(){
		return $this->db->get('user_role')->result_array();
	}

	public function insertData($table, $Data){
		return $this->db->insert($table, $Data);
	}

	public function deleteData($table, $id)
	{
		$this->db->where('Id', $id);
		$this->db->delete($table);
	}

	public function updateData($table, $id, $Data)
	{
		$this->db->where('Id', $id);
		$this->db->update($table, $Data);
	}

	public function getLastMenuId(){
		$result = $this->db->query("SELECT Id FROM `user_menu` ORDER BY Id DESC LIMIT 1")->row_array();
		return $result ?: ['Id' => 0];
	}


	public function getAllMenu(){
		return $this->db->get('user_menu')->result_array();
	}

	public function getAllSubMenu()
	{
		return $this->db->get('user_sub_menu')->result_array();
	}

	public function getMenSub()
	{
		return $this->db->query('SELECT user_sub_menu.Id AS Submenu_id, user_sub_menu.Name, user_menu.Id AS Menu_id, user_menu.Name
            FROM `user_sub_menu`
            LEFT JOIN `user_menu` ON user_sub_menu.Menu_id = user_menu.Id
            WHERE Active = 1')->result_array();
	}

	public function getLastRoleId()
	{
		$result = $this->db->query("SELECT * FROM `user_role` ORDER BY `Id` DESC LIMIT 1")->row_array();
		return $result ?: ['Id' => 0];
	}

	public function getMenuAccess($role_id)
	{
		return $this->db->query("SELECT user_access_menu.Id, user_role.Id as Role_id, user_role.Name, user_menu.Name
            FROM `user_access_menu`
            LEFT JOIN `user_role` ON user_role.Id = user_access_menu.role_Id
            LEFT JOIN `user_menu` ON user_menu.Id = user_access_menu.menu_Id
            WHERE user_access_menu.Role_id = '$role_id'
            ORDER BY role_id")->result_array();
	}

	public function getSubMenuAccess($role_id)
	{
		return $this->db->query("SELECT user_access_submenu.Id, user_sub_menu.Icon, user_role.Id as Role_id, user_role.Name AS Role_Name, user_menu.Name AS Menu_Name, user_sub_menu.Name AS Submenu_Name
            FROM `user_access_submenu`
            LEFT JOIN `user_role` ON user_role.Id = user_access_submenu.Role_id
            LEFT JOIN `user_menu` ON user_menu.Id = user_access_submenu.Menu_id
            LEFT JOIN `user_sub_menu` ON user_sub_menu.Id = user_access_submenu.Submenu_id
            WHERE user_access_submenu.Role_id = '$role_id'
            ORDER BY role_id")->result_array();
	}
}
