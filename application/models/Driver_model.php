<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Driver_model extends CI_Model {

	public function updateData($table, $id, $Data)
	{
		$this->db->where('Id', $id);
		$this->db->update($table, $Data);
	}
    public function getDeliveryItem(){
		return $this->db->query("SELECT 
			Id,
            Product_no,
			Product_name,
            Qty,
            Unit,
            Status,
            Driver_id,
			Delivery_date
			FROM
				dispatch_note")->result_array();
	}
}
