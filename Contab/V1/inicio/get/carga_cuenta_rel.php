<?php 
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/cuentas_rel.class.php";
$contador = 0;

$carga     = new Rel_Cuenta(); 

$datos     = $_POST['datos'];
$fk_cuenta = $_POST['cuenta'];
$tipo      = $_POST['tipo'];

if( count($datos) > 0 && $fk_cuenta > 0 ) {
    foreach($datos as $dato){
        $resultado = $carga->insert((int)$dato, (int)$fk_cuenta,(int)$tipo);
        if( $resultado == 0 ) {
            $contador++;
            $error = "Error: Problema al registrar la cuenta en la base de datos<br />";
        }
    }
	
}
else {
	$contador++;
	$error = "No se ha registrado la cuenta con codigo ".$cod.", ya existe en el catalogo de cuentas.<br />";
}

$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
echo json_encode($return);

