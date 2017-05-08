<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
//$tabla = $_GET['tabla'];
$tabla="historico_inventario_gas";
	for ($i=0;$i<45;$i++) 
	{
		//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
		if($_POST['num_cia'.$i] != "")
		{
			//revisa que el gasto se encuentre dentro del catalogo de gastos
			
				$datos['num_cia'.$i]=$_POST['num_cia'.$i];
				$datos['fecha_entrada'.$i]=$_POST['fecha'.$i];
				$datos['fecha_salida'.$i]=$_POST['fecha'.$i];
				$datos['codmp'.$i]=$_POST['codmp'.$i];
				$datos['existencia'.$i]=$_POST['unidades'.$i];
				$datos['precio_unidad'.$i]=$_POST['costo'.$i];
		}
	}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./hist_inventario.php");
?>