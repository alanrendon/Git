<?php
// CONEXIONES
// Inserción de registros en una tabla

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
$tabla = $_GET['tabla'];

// ----------------------- ------------------------------------------- -------------------
$tabla = $_GET['tabla'];
for ($i=0;$i<10;$i++) {
	if($_POST['num_cia'.$i] != ""){
		// Consultar si existe la compañia
		if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn)){
		
			$datos['num_cia'.$i]=$_POST['num_cia'.$i];
			$datos['num_empleado'.$i]=$_POST['num_empleado'.$i];
			$datos['importe'.$i]=$_POST['importe'.$i];
			$datos['fecha'.$i]=$_POST['fecha'.$i];
		}
		else{
			// Regresar al formulario y mandar un mensaje de error
			header("location: ./pan_agu_cap.php?codigo_error=1");
			die;
		}
	}
	// Insertar registro de operacion de usuario en la tabla 'registro'
    $session->guardar_registro_acceso("Medidor de agua. ID: $_POST['num_cia'.$i]", $dsn);
}
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
//echo $db->numfilas;
$db->xinsertar();



// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./pan_agu_cap.php?mensaje=Se+realizo+el+registro+con+exito");

?>

?>
