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
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$_POST);

// Consultar si existe la compañia
if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn)) {
	if (existe_registro("catalogo_trabajadores",array("num_emp"),array($_POST['num_emp']), $dsn)) {
			$db->generar_script_insert("");
			$db->ejecutar_script();
			
			// Insertar registro de operacion de usuario en la tabla 'registro'
			$session->guardar_registro_acceso("Control de Producción. ID: $_POST[num_cia]", $dsn);
			
			// Regresar al formulario y mandar un mensaje si se inserto registro con exito
			header("location: ./pan_pre_altas.php?mensaje=Se+realizo+el+registro+con+exito");
		}else {
				// Regresar al formulario y mandar un mensaje de error
				header("location: ./pan_pre_altas.php?codigo_error=1");
				die;
		}
	}
else {
	// Regresar al formulario y mandar un mensaje de error
	header("location: ./pan_pre_altas.php?codigo_error=2");
	die;
}


// Insertar registro de operacion de usuario en la tabla 'registro'
$session->guardar_registro_acceso("Control de Prestamos. ID: $_POST[idcontrol_prestamo]", $dsn);

// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./pan_agu_cap.php?mensaje=Se+realizo+el+registro+con+exito");
?>
