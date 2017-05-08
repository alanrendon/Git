<?php
$url[0] = '../';
require_once '../conex/conexion.php';
require_once ('../class/cat_cuentas.class.php');

$cuenta = new Cuenta();
$rowid = (int)$_POST['id'];
//exit(json_encode(1));
if( $cuenta->eliminar_cuenta($rowid) ){
    $array = array();
    $array['eliminar']=true;
    exit(json_encode($array));
}
else {
    $array = array();
    $array['eliminar']=false;
    exit(json_encode($array));
}
?>
