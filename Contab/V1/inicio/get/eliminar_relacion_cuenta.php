<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class relacion extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function eliminar($rowid) { 
		$result = $this->db->query("DELETE FROM ".PREFIX."contab_cuentas_rel WHERE rowid=".$rowid);

		return $result;
	} 
}

$relacion = new relacion(); 

$rowid = $_POST['id'];

if( $relacion->eliminar($rowid) ) {
	$contador++;
	$error = "Error: Problema al registrar la cuenta en la base de datos<br />";
}
exit();
$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
echo json_encode($return);

