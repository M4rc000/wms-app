<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends CI_Model {
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

	public function getRawMaterials(){
		return $this->db->query("SELECT 
			id,
			Material_no,
			Material_name,
			Unit
		FROM
			raw_material")->result_array();
	}

	public function generateNewMaterialNo($lastMaterialNo = null) {
		if (empty($lastMaterialNo)) {
			return "RW0001";
		}
		$numberPart = substr($lastMaterialNo, 2);
		$newNumber = intval($numberPart) + 1;
		$newMaterialNo = "RW" . str_pad($newNumber, strlen($numberPart), "0", STR_PAD_LEFT);
		return $newMaterialNo;
	}

	public function check_duplicate_raw_material($material_no){
		return $this->db->query("SELECT Material_no FROM raw_material WHERE material_no = '$material_no'")->num_rows();
	}

	public function getMaterialById($material_id){
		return $this->db->query("SELECT Id, Material_no, Material_name, Unit FROM raw_material WHERE Id = '$material_id' LIMIT 1")->result_array();
	}

	public function update_data_storage($material_no, $unit) {
		$sql = "UPDATE storage SET Unit = ? WHERE Material_no = ?";
		return $this->db->query($sql, array($unit, $material_no));
	}

}
