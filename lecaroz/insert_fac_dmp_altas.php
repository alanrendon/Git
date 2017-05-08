<?php
/*
 CAPTURA DEL CATALOGO DE PRODUCTOS DE PROVEEDOR
 Tabla 'catalogo_productos_proveedor'
 Menu ''
*/
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];


$db = new Dbclass($dsn,$tabla,$_POST);
$db->xinsertar();
header("location: ./fac_dmp_altas.php");
?>

