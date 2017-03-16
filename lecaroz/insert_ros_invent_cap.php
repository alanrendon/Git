<?php
// CONEXIONES
// Inserción de registros en una tabla
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
$tabla = $_GET['tabla'];

// ---------------------------------------------------------------------------------------
$tabla = $_GET['tabla'];

for($i=0;$i<10;$i++)
{
	if($_POST['codmp'.$i] != "")	
	{	
			// Consultar si existe la compañia
			if(existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),$dsn))
			{
				if(existe_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]),$dsn))
				{
					$datos['num_cia'.$i]=$_POST['num_cia'];
					$datos['codmp'.$i]=$_POST['codmp'.$i];
					$datos['existencia'.$i]=$_POST['existencia'.$i];
					// Insertar registro de operacion de usuario en la tabla 'registro'
					$session->guardar_registro_acceso("Captura de inventarios. ID: $_POST[num_cia]", $dsn);
				}
				else
				{
					// Regresar al formulario y mandar un mensaje de error
					header("location: ./ros_invent_cap.php?codigo_error=2");
					die;
				}
			}
			else
			{
				// Regresar al formulario y mandar un mensaje de error
				header("location: ./ros_invent_cap.php?codigo_error=1");
				die;
			}
	}
}

// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
$db->xinsertar();

// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./ros_invent_cap.php?mensaje=Se+realizo+el+registro+con+exito");
?>