<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'


include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
$fecha = "31/07/2004";
 


	for ($i=0;$i<20;$i++) 
	{
		//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
		if($_POST['num_cia'.$i] != "")
		{
			//revisa que el gasto se encuentre dentro del catalogo de gastos
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn))
			{
				$datos['num_cia'.$i]=$_POST['num_cia'.$i];
				$datos['fecha'.$i]=$fecha;
				$datos['cost_inv'.$i]=$_POST['importe'.$i];
			}
			else
			{
				header("location: ./mario.php?codigo_error=1");
				die;
			}
		}
	}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./mario.php");
?>