<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];

	if (!(existe_registro("catalogo_reservas",array("tipo_res"),array($_POST['tipo_res']), $dsn)))
	{
//		echo "no encontro registro";
		$datos['tipo_res']=$_POST['tipo_res'];
		$datos['descripcion']=$_POST['descripcion'];
		$datos['codgastos']=$_POST['codgastos'];
	}
	else
	{
		//echo "si encontro registro";
		header("location: ./catalogo_reservas.php?codigo_error=1");
		die;
	}
$db = new DBclass($dsn, $tabla, $datos);
$db->generar_script_insert("");
$db->ejecutar_script();
//$db->xinsertar();
//print_r $datos;
header("location: ./catalogo_reservas.php");
?>