<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/tipo_operacion_proveedor.class.php";
require_once "../class/operation_societe_fourn.class.php";

$register_operation_societe = new Operation_Societe_Fourn();
if (isset($_POST['id'])) {
	$register_operation_societe->id_societe=(int)$_POST['id'];
	if ($register_operation_societe->delete_operation_societe_fourn()) {
		
	}
}

?>
