<?php
defined('BASEPATH') or exit('No direct script access allowed');
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

	public function deleteMenu($table, $id){
		$this->db->where('Menu_id', $id);
		$this->db->delete('user_sub_menu');
		$this->db->where('Id', $id);
		$this->db->delete($table);
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

	public function get_material_receiving($material_no, $period){
		if (empty($period)) {
			$period = date("Y");
		}

		$material_details = [];

		// Get all relevant material_no based on filter
		if ($material_no !== 'All') {
			$this->db->select('Material_no');
			$this->db->from('storage');
			$this->db->where('Transaction_type', 'In');
			$this->db->where('Material_no', $material_no);
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		} else {
			$this->db->select('Material_no');
			$this->db->from('storage');
			$this->db->where('Transaction_type', 'In');
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		}

		foreach ($results as $row) {
			$mat_no = $row['Material_no'];

			$monthly_qty = array_fill(0, 12, 0); 

			// Get monthly quantity per material
			$this->db->select("MONTH(Created_at) as month, SUM(Qty) as total_qty");
			$this->db->from('storage');
			$this->db->where('Material_no', $mat_no);
			$this->db->where('Transaction_type', 'In');
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('MONTH(Created_at)');
			$qty_result = $this->db->get()->result_array();

			foreach ($qty_result as $q) {
				$month_index = intval($q['month']) - 1;
				$monthly_qty[$month_index] = floatval($q['total_qty']);
			}

			// Get material details
			if (strpos($mat_no, 'RW') !== false) {
				$detail = $this->db->get_where('raw_material', ['Material_no' => $mat_no])->row();
			} else {
				$detail = $this->db->get_where('wip_material', ['Material_no' => $mat_no])->row();
			}

			if (!$detail) {
				$detail = (object)[
					'Material_no' => $mat_no,
					'Unit'        => '',
					'Material_name' => 'N/A'
				];
			}

			$material_details[] = [
				'Material_no'  => $mat_no,
				'Unit'         => $detail->Unit ?? '',
				'name'         => $detail->Material_name ?? 'N/A',
				'monthly_qty'  => $monthly_qty
			];
		}

		return $material_details;
	}

	public function get_material_usage($material_no, $period){
		if (empty($period)) {
			$period = date("Y");
		}

		$material_details = [];

		// Get all relevant material_no based on filter
		if ($material_no !== 'All') {
			$this->db->select('Material_no');
			$this->db->from('storage');
			$this->db->where('Transaction_type', 'Out');
			$this->db->where('Material_no', $material_no);
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		} else {
			$this->db->select('Material_no');
			$this->db->from('storage');
			$this->db->where('Transaction_type', 'Out');
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		}

		foreach ($results as $row) {
			$mat_no = $row['Material_no'];

			$monthly_qty = array_fill(0, 12, 0); // Jan to Dec

			// Get monthly quantity per material
			$this->db->select("MONTH(Created_at) as month, SUM(Qty) as total_qty");
			$this->db->from('storage');
			$this->db->where('Material_no', $mat_no);
			$this->db->where('Transaction_type', 'Out');
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('MONTH(Created_at)');
			$qty_result = $this->db->get()->result_array();

			foreach ($qty_result as $q) {
				$month_index = intval($q['month']) - 1; // 0-based index
				$monthly_qty[$month_index] = floatval($q['total_qty']);
			}

			// Get material details
			if (strpos($mat_no, 'RW') !== false) {
				$detail = $this->db->get_where('raw_material', ['Material_no' => $mat_no])->row();
			} else {
				$detail = $this->db->get_where('wip_material', ['Material_no' => $mat_no])->row();
			}

			if (!$detail) {
				$detail = (object)[
					'Material_no' => $mat_no,
					'Unit'        => '',
					'Material_name' => 'N/A'
				];
			}

			$material_details[] = [
				'Material_no'  => $mat_no,
				'Unit'         => $detail->Unit ?? '',
				'name'         => $detail->Material_name ?? 'N/A',
				'monthly_qty'  => $monthly_qty
			];
		}

		return $material_details;
	}

	public function get_demand_forecast($material_no, $period){
		if (empty($period)) {
			$period = date("Y");
		}

		$material_details = [];

		// Get all relevant material_no based on filter
		if ($material_no !== 'All') {
			$this->db->select('Material_no');
			$this->db->from('demand_forecast');
			$this->db->where('Material_no', $material_no);
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		} else {
			$this->db->select('Material_no');
			$this->db->from('demand_forecast');
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('Material_no');
			$results = $this->db->get()->result_array();
		}

		foreach ($results as $row) {
			$mat_no = $row['Material_no'];

			$monthly_qty = array_fill(0, 12, 0); // Jan to Dec

			// Get monthly quantity per material
			$this->db->select("MONTH(Created_at) as month, SUM(Qty_predict) as total_qty");
			$this->db->from('demand_forecast');
			$this->db->where('Material_no', $mat_no);
			$this->db->where('YEAR(Created_at)', $period);
			$this->db->group_by('MONTH(Created_at)');
			$qty_result = $this->db->get()->result_array();

			foreach ($qty_result as $q) {
				$month_index = intval($q['month']) - 1; // 0-based index
				$monthly_qty[$month_index] = floatval($q['total_qty']);
			}

			// Get material details
			if (strpos($mat_no, 'RW') !== false) {
				$detail = $this->db->get_where('raw_material', ['Material_no' => $mat_no])->row();
			} else {
				$detail = $this->db->get_where('wip_material', ['Material_no' => $mat_no])->row();
			}

			if (!$detail) {
				$detail = (object)[
					'Material_no' => $mat_no,
					'Unit'        => '',
					'Material_name' => 'N/A'
				];
			}

			$material_details[] = [
				'Material_no'  => $mat_no,
				'Unit'         => $detail->Unit ?? '',
				'name'         => $detail->Material_name ?? 'N/A',
				'monthly_qty'  => $monthly_qty
			];
		}

		return $material_details;
	}

	public function get_material_list() {
		$sql = "
			SELECT Material_no, Material_name FROM raw_material
			UNION
			SELECT Material_no, Material_name FROM wip_material
		";
		return $this->db->query($sql)->result_array();
	}
}
