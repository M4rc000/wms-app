<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin_model extends CI_Model {
	public function getListMaterial()
	{
		return $this->db->get('raw_material')->result_array();
	}

	public function insertData($table, $Data)
	{
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
}
