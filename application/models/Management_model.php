<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Management_model extends CI_Model {
	public function getRawMaterials(){
		return $this->db->query("SELECT DISTINCT Material_no, Material_name, Qty, Unit
			FROM 
				storage
			WHERE
				Material_no LIKE '%RW%'
			GROUP BY 
				material_no, material_name
			HAVING 
				(SUM(CASE WHEN transaction_type = 'IN' THEN Qty ELSE 0 END) - 
				SUM(CASE WHEN transaction_type = 'OUT' THEN Qty ELSE 0 END))")->result_array();
	}
}
