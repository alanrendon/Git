<?php
$url[0] = "../";

require_once "../class/tipo_operacion_proveedor.class.php";
require_once "../class/operation_societe_fourn.class.php";

if (isset($_POST['datos']) && isset($_POST['operation_societe']) && isset($_POST['typeoperation'])) {
	$registred                              = false;
	$operacion                              = new Tipo_Operacion();
	$register_operation_societe             = new Operation_Societe_Fourn();
	$operacion                              = $operacion->get_id_operacion_proveedor((int)$_POST['typeoperation'], (int) $_POST['operation_societe']);
	$register_operation_societe->id_operation_societe = $operacion->rowid;
	foreach ($_POST['datos'] as $key => $value) {
		$register_operation_societe->id_societe = (int)$value;
		$registred                              = $register_operation_societe->put_operation_societe_fourn();
	}
	$registred ? exit(json_encode(array('correcto'=>'Se agregaron todos los proveedores'))): exit(json_encode(array('error'=>'Hubo problemas al agregar la relación')));

}
?>