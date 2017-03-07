<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

require_once "../class/cuentas_rel.class.php";


$carga     = new Rel_Cuenta(); 

$fk_cuenta = $_POST['cuenta'];
$tipo      = 10;

$contador =0;
$resultado =0;

$fk_cuenta = $_POST['cuenta'];

if($fk_cuenta > 0 ) {
    $rowid=$carga->get_cuenta_iva();
   
    if($rowid){
         $resultado = $carga->update_cuenta_rel($rowid, $fk_cuenta);
    }else{
        $resultado = $carga->insert(0,$fk_cuenta,$tipo);
    }

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

