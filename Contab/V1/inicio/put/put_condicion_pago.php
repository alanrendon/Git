<?php 
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/tipos_pagos.class.php";
$contador = 0;

$carga = new tipos_pagos(); 

$datos = $_POST['datos'];
$type  = $_POST['type'];

if( count($datos) > 0 && $type > 0 ) {
    foreach($datos as $dato){
        $resultado = $carga->insert_paiement((int)$dato, (int)$type);
        if( $resultado == 0 ) {
            $contador++;
        }
    }
	
}
else {
	$contador++;
}

$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
echo json_encode($return);

