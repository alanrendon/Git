<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class clientes extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_clientes() { 
		$result = $this->db->query("SELECT * FROM ".PREFIX."societe WHERE client = 1");
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$user[$row['rowid']] = $row['nom'];
		}
		return $user;
	} 

	public function existe($rowid) { 
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cuentas_rel WHERE fk_type = 1 AND fk_object=".$rowid);
		$row = mysqli_fetch_array($result);
		return $row['no'];
	}
}

$clientes = new clientes(); 
$arreglo = $clientes->get_clientes();

print '<label class="">Seleccione Cliente</label>
		<select class="select2_single form-control" name="cliente">
		<option></option>';

foreach ($arreglo as $key => $value) {
	print ($clientes->existe($key) <= 0) ? '<option value="'.$key.'" >'. ($value).'</option>' : '';
	//print '<option value="'.$key.'" >'.$value.'</option>';
}

print '</select>';
