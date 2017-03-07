<?php
require_once $url[0].'conex/conexion.php';

class FactureDet extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 
    
    public function get_serv_pro_rel($facture){
        $rows = false;
        $sql = 'SELECT
                        p.rowid,
                        CASE p.fk_product_type
                    WHEN "0" THEN
                        "3"
                    WHEN "1" THEN
                        "4"
                    END AS societe
                    FROM
                        '.PREFIX.'facturedet AS fdet
                    INNER JOIN '.PREFIX.'product AS p ON p.rowid=fdet.fk_product
                WHERE
                    fdet.fk_facture = '.$facture;

        $query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row ;
			}
		}
		return $rows;
    }
	

}