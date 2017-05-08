<?php
require_once $url[0]."conex/conexion.php";

class Cuenta extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_cuentas() { 
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas"); 
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$cuenta[$row['codagr']] = $row['codagr'].' - '.$row['descripcion'];
		}
		return $cuenta;
	}

	public function get_nom_cuenta($cuenta) { 
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE codagr='".$cuenta."'"); 
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['descripcion'];
	}
	
}