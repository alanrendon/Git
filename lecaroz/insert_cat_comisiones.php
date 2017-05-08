<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'


include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
for ($i=0;$i<20;$i++) 
{
	//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
	if($_POST['codmp'.$i] != "")
	{
		//revisa que el gasto se encuentre dentro del catalogo de gastos
		if (existe_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]), $dsn))
		{
			$datos['codmp'.$i]=$_POST['codmp'.$i];
			$datos['comision'.$i]=$_POST['comision'.$i];
		}
		else
		{
			header("location: ./catalogo_comisiones.php?codigo_error=1");
			die;
		}
	}
}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./catalogo_comisiones.php");
?>