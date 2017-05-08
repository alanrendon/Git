<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class cuentas extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_cuentas() { 
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE afectacion = 1"); 
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$cuenta[$row['rowid']] = $row['codagr'].' - '.$row['descripcion'];
		}
		return $cuenta;
	}

	public function existe($rowid) { 
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cuentas_rel WHERE fk_cuenta=".$rowid);
		$row = mysqli_fetch_array($result);
		return $row['no'];
	}
}

$cuentas = new cuentas(); 
$arreglo = $cuentas->get_cuentas();

print '<label>Cuenta</label>
			<select class="select2_single form-control" name="cuenta">
				<option></option>';

foreach ($arreglo as $key => $value) {
	print  '<option value="'.$key.'" >'. ($value).'</option>';
	//print '<option value="'.$key.'" >'. ($value).'</option>';
}

print '</select>';
