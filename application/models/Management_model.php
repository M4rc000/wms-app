<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Management_model extends CI_Model {
   public function getRawMaterials(){
		return $this->db->query("SELECT
    Material_no,
    Material_name,
    Unit,
    SUM(CASE WHEN Transaction_type = 'In' THEN Qty ELSE 0 END) AS Total_Qty_In,
    SUM(CASE WHEN Transaction_type = 'Out' THEN Qty ELSE 0 END) AS Total_Qty_Out,
    (SUM(CASE WHEN Transaction_type = 'In' THEN Qty ELSE 0 END) -
     SUM(CASE WHEN Transaction_type = 'Out' THEN Qty ELSE 0 END)) AS Qty,
    MAX(Updated_at) AS Latest_Updated_at, -- Mengambil tanggal pembaruan terbaru untuk grup
    MAX(Updated_by) AS Latest_Updated_by  -- Mengambil pengguna terbaru yang memperbarui untuk grup
FROM
    storage
WHERE
    Material_no LIKE '%RW%'
GROUP BY
    Material_no,
    Material_name")->result_array();
	}

    public function getWIPMaterials() {
        return $this->db->query("SELECT
    Material_no,
    Material_name,
    Unit,
    SUM(CASE WHEN Transaction_type = 'In' THEN Qty ELSE 0 END) AS Total_Qty_In,
    SUM(CASE WHEN Transaction_type = 'Out' THEN Qty ELSE 0 END) AS Total_Qty_Out,
    (SUM(CASE WHEN Transaction_type = 'In' THEN Qty ELSE 0 END) -
     SUM(CASE WHEN Transaction_type = 'Out' THEN Qty ELSE 0 END)) AS Qty,
    MAX(Updated_at) AS Latest_Updated_at, -- Mengambil tanggal pembaruan terbaru untuk grup
    MAX(Updated_by) AS Latest_Updated_by  -- Mengambil pengguna terbaru yang memperbarui untuk grup
FROM
    storage
WHERE
    Material_no NOT LIKE '%RW%'
GROUP BY
    Material_no,
    Material_name")->result_array();
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
   