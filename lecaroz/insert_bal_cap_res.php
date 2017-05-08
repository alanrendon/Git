<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];

	for ($i=0;$i<12;$i++) 
	{
		if($_POST['num_cia'] != "")
		{
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn))
			{
				$datos['num_cia'.$i]=$_POST['num_cia'];
				$datos['fecha'.$i]='1/'.$_POST['mes'.$i].'/'.$_POST['anio'];
				$datos['importe'.$i]=$_POST['importe'.$i];
				$datos['cod_reserva'.$i]=$_POST['cod_reserva'.$i];
				$datos['anio'.$i]=$_POST['anio'];
				$datos['pagado'.$i]=false;
			}
			else
			{
				header("location: ./bal_cap_res.php?codigo_error=1");
				die;
			}
		}
	}
//print_r($datos);
$db = new DBclass($dsn, $tabla, $datos);
$db->xinsertar();
header("location: ./bal_cap_res.php");
?>