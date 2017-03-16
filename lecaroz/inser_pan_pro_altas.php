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
	if (existe_registro("catalogo_turnos",array("cod_turno"),array($_POST['cod_turno']), $dsn)) {
		if (existe_registro("catalogo_productos",array("cod_producto"),array($_POST['cod_producto']), $dsn)) {
			$db->generar_script_insert("");
			$db->ejecutar_script();
			
			ejecutar_script('UPDATE control_produccion SET precio_raya = 0 WHERE precio_raya IS NULL', $dsn);
			ejecutar_script('UPDATE control_produccion SET porc_raya = 0 WHERE porc_raya IS NULL', $dsn);
			ejecutar_script('UPDATE control_produccion SET precio_venta = 0 WHERE precio_venta IS NULL', $dsn);
			
			// Regresar al formulario y mandar un mensaje si se inserto registro con exito
			header("location: ./pan_pro_altas.php?mensaje=Se+realizo+el+registro+con+exito");
		}
		else {
			// Regresar al formulario y mandar un mensaje de error
			header("location: ./pan_pro_altas.php?codigo_error=3");
			die;
		}
	}
	else {
		// Regresar al formulario y mandar un mensaje de error
		header("location: ./pan_pro_altas.php?codigo_error=2");
		die;
	}
}
else {
	// Regresar al formulario y mandar un mensaje de error
	header("location: ./pan_pro_altas.php?codigo_error=1");
	die;
}

?>