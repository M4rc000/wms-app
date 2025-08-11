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
			Id,
			Material_no,
			Material_name,
			Unit
		FROM
			raw_material")->result_array();
	}

	public function generateNewMaterialNo() {
		$result = $this->db->query("SELECT Id, Material_no FROM raw_material ORDER BY Id DESC LIMIT 1")->result_array();
		$lastMaterialNo = $result[0]['Material_no'];
		if (empty($lastMaterialNo)) {
			return "RW0001";
		}
		$numberPart = substr($lastMaterialNo, 2);
		$newNumber = intval($numberPart) + 1;
		$newMaterialNo = "RW" . str_pad($newNumber, strlen($numberPart), "0", STR_PAD_LEFT);
		return $newMaterialNo;
	}

	public function check_duplicate_raw_material($material_name){
		return $this->db->query("SELECT Material_name FROM raw_material WHERE material_name = '$material_name'")->num_rows();
	}

	public function getMaterialById($material_id){
		return $this->db->query("SELECT Id, Material_no, Material_name, Unit FROM raw_material WHERE Id = '$material_id' LIMIT 1")->result_array();
	}

	
	public function getWipMaterials(){
		return $this->db->query("SELECT 
			Id,
			Material_no,
			Material_name,
			Unit
		FROM
			wip_material")->result_array();
	}

	public function generateNewWipMaterialNo($client_id) {
		$client = $this->db->query("SELECT Short_name FROM client WHERE Id = '$client_id'")->row_array();

		if (!$client || !isset($client['Short_name'])) {
			return null;
		}

		$clientNo = $client['Short_name'];

		// Ambil material terakhir yang dimulai dengan No client
		$result = $this->db->query("
			SELECT Material_no 
			FROM wip_material 
			WHERE Client_id = '$client_id' AND Material_no LIKE '{$clientNo}%' 
			ORDER BY Id DESC 
			LIMIT 1
		")->row_array();

		// Jika belum ada material sebelumnya
		if (!$result || empty($result['Material_no'])) {
			return $clientNo . "0001";
		}

		// Ekstrak angka dari belakang
		$lastMaterialNo = $result['Material_no'];
		$numberPart = substr($lastMaterialNo, strlen($clientNo));
		$newNumber = intval($numberPart) + 1;

		// Generate dengan padding 4 digit
		$newMaterialNo = $clientNo . str_pad($newNumber, strlen($numberPart), "0", STR_PAD_LEFT);
		return $newMaterialNo;
	}

	public function check_duplicate_wip_material($material_name){
		return $this->db->query("SELECT Material_name FROM wip_material WHERE material_name = '$material_name'")->num_rows();
	}

	public function getWipMaterialById($material_id){
		return $this->db->query("SELECT Id, Material_no, Material_name, Unit FROM wip_material WHERE Id = '$material_id' LIMIT 1")->result_array();
	}


	public function update_data_storage($Material_no, $Material_name, $Unit) {
		$sql = "UPDATE storage SET Unit = ?, Material_name = ? WHERE Material_no = ?";
		return $this->db->query($sql, array($Unit, $Material_name, $Material_no));
	}

	public function delete_data_storage($Material_no) {
		$sql = "DELETE storage  WHERE Material_no = ?";
		return $this->db->query($sql, array($Material_no));
	}

	public function GetAllClients(){
		return $this->db->query("SELECT Id, Name from client")->result_array();
	}

}