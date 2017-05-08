<?php
// CONEXION
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obetener tabla de trabajo
$tabla = $_GET['tabla'];
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$_POST);


// ----------------------- ------------------------------------------- -------------------
$tabla = $_GET['tabla'];
if (existe_registro("catalogo_mov_bancos",array("id"),array($_POST['id']), $dsn))
{
		// Regresar al formulario y mandar un mensaje de error
		header("location: ./ban_cat_movimientos.php?codigo_error=1");
		die;
}
else 
{

				$db->generar_script_insert("");
		$db->ejecutar_script();
		
		// Insertar registro de operacion de usuario en la tabla 'registro'
		$session->guardar_registro_acceso("Catalogo de movimientos. ID: $_POST[id]", $dsn);
		
		// Regresar al formulario y mandar un mensaje si se inserto registro con exito
		header("location: ./ban_cat_movimientos.php?mensaje=Se+realizo+el+registro+con+exito");
}


// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
?>
