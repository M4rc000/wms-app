<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Management_model extends CI_Model {
	public function getRawMaterials(){
		return $this->db->query("SELECT 
    Material_no,
    Material_name,
    SUM(CASE WHEN Transaction_type = 'In' THEN Qty ELSE 0 END) -
    SUM(CASE WHEN Transaction_type = 'Out' THEN Qty ELSE 0 END) AS Qty, Unit
FROM
    storage
WHERE 
	Material_no LIKE '%RW%'
GROUP BY 
    Material_no
ORDER BY 
    Material_no")->result_array();
	}
}
