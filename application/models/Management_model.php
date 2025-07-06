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

    public function getWIPMaterials() {
        return $this->db->query("SELECT Material_no, Material_name, Qty, Unit
            FROM 
                storage
            WHERE 
                Material_no NOT LIKE '%RW%'
            GROUP BY 
                Material_no, Material_name
            HAVING 
            (
                SUM(CASE WHEN transaction_type = 'IN' THEN Qty ELSE 0 END) -
                SUM(CASE WHEN transaction_type = 'OUT' THEN Qty ELSE 0 END)
            )")->result_array();
    }
    
    public function getMaterialUsage() {
        return $this->db->query("SELECT 
    Material_no,
    Material_name,
    Qty,
    Unit
FROM
    storage
WHERE 
	Material_no LIKE '%RW%'
AND
    Transaction_type = 'Out'
GROUP BY 
    Material_no
ORDER BY 
    Material_no")->result_array();
    }    

    public function getDemandStock() {
        return $this->db->query("SELECT df.Material_no, df.Material_name, df.Qty_predict, rw.Unit, df.Date
FROM
    demand_forecast AS df
LEFT JOIN
    raw_material AS rw ON df.Material_no = rw.Material_no;
        ")->result_array();
    }    
}
   