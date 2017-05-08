<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class carga_proveedor extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function insert($fk_proveedor, $fk_cuenta) { 
		$result = $this->db->query("INSERT INTO ".PREFIX."contab_cuentas_rel ( entity, fk_type, fk_object, fk_cuenta ) 
						VALUES(1,2,".$fk_proveedor.",".$fk_cuenta.")");
		return ( $result ) ? 1: 0;
	}
}
$contador =0;
$carga_proveedor = new carga_proveedor(); 

$fk_proveedor = $_POST['proveedor'];
$fk_cuenta = $_POST['cuenta'];

if( $fk_proveedor > 0 && $fk_cuenta > 0 ) {
	$resultado = $carga_proveedor->insert($fk_proveedor, $fk_cuenta);
	if( $resultado == 0 ) {
		$contador++;
		$error = "Error: Problema al registrar la cuenta en la base de datos<br />";
	}
}
else {
	$contador++;
	$error = "No se ha registrado la cuenta con codigo ".$cod.", ya existe en el catalogo de cuentas.<br />";
}

$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
echo json_encode($return);

