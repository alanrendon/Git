<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class grupo extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function eliminar($rowid) { 
		$result = $this->db->query("DELETE FROM ".PREFIX."contab_grupos WHERE rowid=".$rowid);
		return mysqli_fetch_array($result);
	} 
}

$grupo = new grupo(); 
$rowid = $_POST['id'];

if( $grupo->eliminar($rowid) ) {
	$contador++;
	$error = "Error: Problema al registrar la cuenta en la base de datos<br />";
}

$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
echo json_encode($return);

