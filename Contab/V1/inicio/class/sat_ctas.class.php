<?php
require_once $url[0]."conex/conexion.php";

class Cuenta extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_cuentas() { 
        $sql ="SELECT * FROM ".PREFIX."contab_sat_ctas";
        
		$cuenta = false;
		$result = $this->db->query($sql); 
		if ($result) {

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    $cuenta[$row['cta']] = $row['cta'].' - '. ($row['descta']);
			}
		}
		return $cuenta;
	}

	
}