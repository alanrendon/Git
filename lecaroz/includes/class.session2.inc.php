<?php
// +----------------------------------------------------------------------------------------+
// | class.session.inc.php                                                                  |
// | Librería diseñada para el manejo de sesiones.                                          |
// +----------------------------------------------------------------------------------------+
// |                                                                                        |
// | Copyright (C) 2004 Carlos Alberto Candelario Corona                                    |
// |                                                                                        |
// | This program is free software; you can redistribute it and/or                          |
// | modify it under the terms of the GNU General Public License                            |
// | as published by the Free Software Foundation; either version 2                         |
// | of the License, or (at your option) any later version.                                 |
// |                                                                                        |
// | This program is distributed in the hope that it will be useful,                        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                          |
// | GNU General Public License for more details.                                           |
// |                                                                                        |
// | You should have received a copy of the GNU General Public License                      |
// | along with this program; if not, write to the Free Software                            |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA                              |
// | 02111-1307, USA.                                                                       |
// |                                                                                        |
// | Autor: Ing. Carlos Alberto Candelario Corona, p_master@tutopia.com                     |
// +----------------------------------------------------------------------------------------+


include_once 'DB.php';

class sessionclass {
	var $dsn;
	var $idscreen;
	var $tabla;
	var $ruta;
	var $plantilla;
	
	function sessionclass($dsn) {
		// Tiempo de duración de sesión
		session_cache_expire(540);
		
		// Iniciar seción
		session_start();
		
		$this->dsn = $dsn;
		
		$this->validar_session();
		return;
	}
	
	function validar_session() {
		if (!isset($_SESSION['authlevel'])) {
			header("location: session_error.php?error=1");
			exit();
		}
	}
	
	function validar_pantalla($idscreen) {
		$this->idscreen = $idscreen;	// Verificar si el usuario tiene acceso a la pantalla
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "<b>class.session.inc -> sessionclas -> validar_pantalla().</b><br>";
			echo "Error al intentar acceder a la Base de Datos.<br>";
			echo "Avisar al administrador.<br>";
			die($db->getUserInfo());
		}

		// Solicitar permisos de usuario para la pantalla seleccionada
		$sql = "SELECT permiso FROM permisos WHERE id_user = $_SESSION[iduser] AND authlevel = $_SESSION[authlevel] AND idscreen = $this->idscreen";
		$result = $db->query($sql);
		$db->disconnect();
		
		if (DB::isError($result)) {
			echo "<b>class.session.inc -> sessionclass -> validar_pantalla().</b><br>";
			echo "Error en script SQL:<br>";
			echo "$sql<br>";
			echo "Avisar al administrador.<br>";
			die($result->getUserInfo());
		}
		
		$ver_pantalla = $result->fetchRow(DB_FETCHMODE_OBJECT);
		
		// Si usuario tiene permisos, mostrar pantalla, en caso contrario, denegar el acceso
		if (!($result->numRows() > 0 && $ver_pantalla->permiso == TRUE) && $_SESSION['authlevel'] != 1) {
			header("location: ./access_denied.php?idscreen=$this->idscreen");
		}
	}
	
	function info_pantalla() {
		// Obtener información de la pantalla
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "<b>class.session.inc -> sessionclas -> info_pantalla().</b><br>";
			echo "Error al intentar acceder a la Base de Datos.<br>";
			echo "Avisar al administrador.<br>";
			die($db->getUserInfo());
		}
		
		$sql = "SELECT * FROM screens WHERE idscreen = $this->idscreen";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			$db->disconnect();
			echo "<b>class.session.inc -> sessionclas -> info_pantalla().</b><br>";
			echo "Error en script SQL:<br>";
			echo "$sql<br>";
			echo "Avisar al administrador.<br>";
			die($result->getUserInfo());
		}
		
		$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);
		
		$sql = "SELECT * FROM menus WHERE idmenu = $screen->idmenu";
		$result = $db->query($sql);
		$db->disconnect();
		if (DB::isError($result)) {
			$db->disconnect();
			echo "<b>class.session.inc -> sessionclas -> info_pantalla().</b><br>";
			echo "Error en script SQL:<br>";
			echo "$sql<br>";
			echo "Avisar al administrador.<br>";
			die($result->getUserInfo());
		}

		$menu = $result->fetchRow(DB_FETCHMODE_OBJECT);
		
		// Asignar valores a propiedades del objeto
		$this->tabla     = $screen->tabla;
		$this->ruta      = $menu->path;
		$this->plantilla = $screen->plantilla;
	}
	
	function guardar_registro_acceso($operacion, $dsn) {
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "<b>class.session.inc.php -> sessionclass -> guardar_registro_acceso()</b><br>";
			echo "Error al intentar acceder a la Base de Datos.<br>";
			echo "Avisar al administrador.<br>";
			die($db->getUserInfo());
		}

		// Insertar registro de acceso de usuario en la tabla 'registro'
		$sql = "INSERT INTO registro (iduser,fecha,ip,navegador,operacion) VALUES ($_SESSION[iduser],CURRENT_TIMESTAMP,'$_SERVER[REMOTE_ADDR]','$_SERVER[HTTP_USER_AGENT]','$operacion')";
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