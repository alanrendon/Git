<?php
include_once 'DB.php';

class sessionclass {
	function sessionclass() {
		session_start();
	}
	
	function validar_sesion() {
		if (!isset($_SESSION['authlevel'])) {
			header("location: session_error.php?error=1");
			exit();
		}
	}
	
	function validar_pantalla($idscreen, $dsn) {
		// Verificar si el usuario tiene acceso a la pantalla
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "<b>class.session.inc -> sessionclas -> validar_pantalla().<br>";
			echo "Error al intentar acceder a la Base de Datos.<br>";
			echo "Avisar al administrador.<br>";
			die($db->getUserInfo());
		}

		// Solicitar permisos de usuario para la pantalla seleccionada
		$sql = "SELECT permiso FROM permisos WHERE id_user = $_SESSION[iduser] AND authlevel = $_SESSION[authlevel] AND idscreen = $idscreen";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "<b>class.session.inc -> sessionclass -> validar_pantalla().</b><br>";
			echo "Error en script SQL:<br>";
			echo "$sql<br>";
			echo "Avisar al administrador.<br>";
			die($result->getUserInfo());
		}
		
		$ver_pantalla = $result->fetchRow(DB_FETCHMODE_OBJECT);
		
		// Si usuario tiene permisos, mostrar pantalla, en caso contrario, denegar el acceso
		if (!($result->numRows() > 0 && $ver_pantalla->permiso == TRUE) && $_SESSION['authlevel'] != 1) {
			header("location: ./access_denied.php?idscreen=$idscreen");
		}
	}
	
	function guardar_registro_acceso($operacion, $dsn) {
		$db = DB::connect($dsn);
		if (DB::isError($db)) {
			echo "<b>class.session.inc.php -> sessionclass -> guardar_registro_acceso()</b><br>";
			echo "Error al intentar acceder a la Base de Datos.<br>";
			echo "Avisar al administrador.<br>";
			die($db->getUserInfo());
		}

		// Insertar registro de acceso de usuario en la tabla 'registro'
		$sql = "INSERT INTO registro (iduser,fecha,ip,navegador,operacion) VALUES ($_SESSION[iduser],CURRENT_TIMESTAMP,'$_SERVER[REMOTE_ADDR]','" . substr($_SERVER['HTTP_USER_AGENT'], 0, 100) . "','$operacion')";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			$db->disconnect();
			echo "<b>class.session.inc.php -> sessionclass -> guardar_registro_acceso()</b><br>";
			echo "Error en script SQL:<br>";
			echo "$sql<br>";
			echo "Avisar al administrador.<br>";
			die($result->getUserInfo());
		}
	}
}
?>