<?php
// CAPTURA DIRECTA DE EFECTIVOS DE ROSTICERIAS
// Tabla 'importe_efectivos'
// Menu 'Rosticerias


include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
$fecha = getdate();
 


	for ($i=0;$i<20;$i++) 
	{
		//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
		if($_POST['num_cia'.$i] != "")
		{
			//revisa que el gasto se encuentre dentro del catalogo de gastos
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn))
			{
				$datos['num_cia'.$i]=$_POST['num_cia'.$i];
				$datos['fecha'.$i]=$_POST['fecha'.$i].'/'.$fecha['mon'].'/'.$fecha['year'];
				$datos['importe'.$i]=$_POST['importe'.$i];
				// Insertar registro de operacion de usuario en la tabla 'registro'
				$session->guardar_registro_acceso("Captura de efectivos rosticerias. ID: $_POST[num_cia]", $dsn);
			}
			else
			{
				header("location: ./ros_efe_cap.php?codigo_error=1");
				die;
			}
		}
	}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./ros_efe_cap.php?mensage=Se+realizo+el+registro+con+exito");
?>