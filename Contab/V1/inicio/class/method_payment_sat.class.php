<?php 
require_once $url[0]."conex/conexion.php";
 /**
 * Clase para los métodos de pago del sat actualizado 2015
 */
 class Payment_Sat extends conexion
 {
 	
 	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_list_payment(){
		$rows = array();

		$sql = 'SELECT
					*
				FROM
					`llx_contab_method_sat_payment`
				ORDER BY
					`name`';

		$query= $this->db->query($sql); 

		if ($query)
			while($row = $query->fetch_object())
    			$rows[] = $row;
    	return $rows;
	}
 }
 ?>