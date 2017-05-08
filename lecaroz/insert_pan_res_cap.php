<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = "mov_expendios";
for ($i=0;$i<$_POST['cont'];$i++) 
{
	if (!existe_registro("mov_expendios",array("num_cia","num_expendio","fecha"),array($_POST['num_cia'],$_POST['num_exp'.$i],$_POST['fecha']), $dsn))
	{
		$datos['num_cia'.$i]=$_POST['num_cia'];
		$datos['fecha'.$i]=$_POST['fecha'];
		$datos['rezago'.$i]=$_POST['importe'.$i];
		$datos['pan_p_venta'.$i]=0;
		$datos['pan_p_expendio'.$i]=0;
		$datos['abono'.$i]=0;
		$datos['devolucion'.$i]=0;
		$datos['num_expendio'.$i]=$_POST['num_exp'.$i];
	}
}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./pan_res_cap.php");
?>