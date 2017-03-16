<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla='modificacion_pastel';

	for ($i=0;$i<$_POST['contador'];$i++) 
	{
		$datos['num_cia'.$i]=$_POST['num_cia'.$i];
		$datos['let_folio'.$i]=$_POST['let_folio'.$i];
		$datos['num_remi'.$i]=$_POST['num_fact'.$i];
		$datos['estado'.$i]='false';
		$datos['descripcion'.$i]=$_POST['descripcion'.$i];
		$datos['fecha_solicitud'.$i]=date("d/m/Y");
		$datos['fecha_autorizacion'.$i]=NULL;
		$datos['fecha_modificacion'.$i]=NULL;

		if($_POST['cancelar1'.$i]==1)
			$datos['cancelar'.$i]='true';
		else
			$datos['cancelar'.$i]='false';
			
		if($_POST['kilos1'.$i]==1)
		{
			$datos['kilos_mas'.$i]='true';
			$datos['kilos_menos'.$i]='false';
		}
		else if($_POST['kilos1'.$i]==0)
		{
			$datos['kilos_mas'.$i]='false';
			$datos['kilos_menos'.$i]='true';
		}
		else
		{	
			$datos['kilos_mas'.$i]='false';
			$datos['kilos_menos'.$i]='false';
		}
		if($_POST['base1'.$i]==1)
			$datos['base'.$i]='true';
		else
			$datos['base'.$i]='false';
		
		if($_POST['precio_unidad1'.$i]==1)
			$datos['precio_unidad'.$i]='true';
		else
			$datos['precio_unidad'.$i]='false';
		
		if($_POST['otros1'.$i]==1)
			$datos['otros'.$i]='true';
		else
			$datos['otros'.$i]='false';

		if($_POST['perdida1'.$i]==1)
			$datos['perdida'.$i]='true';
		else
			$datos['perdida'.$i]='false';

		if($_POST['cambio_fecha1'.$i]==1)
			$datos['cambio_fecha'.$i]='true';
		else
			$datos['cambio_fecha'.$i]='false';
		
		if($_POST['fecha_nueva1'.$i]==1)
			$datos['fecha_nueva'.$i]='true';
		else
			$datos['fecha_nueva'.$i]='false';
	}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./pan_pastel_sol.php");
?>