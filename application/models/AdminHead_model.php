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
}
