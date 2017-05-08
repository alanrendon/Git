<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];

	if (!(existe_registro("catalogo_gastos_caja",array("num_gasto"),array($_POST['num_gasto']), $dsn)))
	{
//		echo "no encontro registro";
		$datos['id']=$_POST['num_gasto'];
		$datos['num_gasto']=$_POST['num_gasto'];
		$datos['descripcion']=$_POST['descripcion'];
	}
	else
	{
		//echo "si encontro registro";
		header("location: ./catalogo_gastos_caja.php?codigo_error=1");
		die;
	}
	
	$sql = "INSERT INTO catalogo_gastos_caja (id,descripcion,num_gasto) VALUES ($_POST[num_gasto],'".strtoupper($_POST['descripcion'])."',$_POST[num_gasto])";
	ejecutar_script($sql,$dsn);
/*$db = new DBclass($dsn, $tabla, $datos);
$db->generar_script_insert("");
$db->ejecutar_script();*/
//$db->xinsertar();
//print_r $datos;
header("location: ./catalogo_gastos_caja.php");
?>