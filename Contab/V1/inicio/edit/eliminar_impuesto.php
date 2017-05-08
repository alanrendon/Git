<?php
$url[0] = "../";

require_once "../class/impuestos_registrados.class.php";
$impuesto = new impuestos();

$rowid =(int)$_POST['id'];

if( $impuesto->eliminar_impuesto($rowid) ) {
	$return["json"] = json_encode(1);
}
else{
    $return["json"] = json_encode(2);
}

echo json_encode($return);
