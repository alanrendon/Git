<?php
/*
 CAPTURA DE MOVIMIENTO DE GASTOS
 Tabla 'movimiento_gastos'
 Menu 'Panaderias->Gastos'
*/
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

//print_r($_POST);
$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];


$db = new Dbclass($dsn,$tabla,$_POST);
$db->xinsertar();

if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn))
{
	$sql="UPDATE total_panaderias set gastos= gastos +".$_POST['total'].", efectivo= efectivo-".$_POST['total'].",  gas=true WHERE num_cia=".$_POST['num_cia0']." and fecha='".$_POST['fecha0']."'";
	ejecutar_script($sql,$dsn);
}
else
{
	$sql="INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) VALUES(".$_POST['num_cia0'].", '".$_POST['fecha0']."',0,0,0,0,".$_POST['total'].",0,0,0,-".$_POST['total'].",false,false,true,false,false)";
	ejecutar_script($sql,$dsn);
}
unset($_SESSION['gastos']);


header("location: ./pan_mga_cap.php");
?>