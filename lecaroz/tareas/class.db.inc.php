<?php
// +----------------------------------------------------------------------------------------+
// | class.db.inc.php   (Versión 2.0.0)                                                     |
// | Librería diseñada para el manejo de Base de Datos.                                     |
// +----------------------------------------------------------------------------------------+
// |                                                                                        |
// | Copyright (C) 2005 Carlos Alberto Candelario Corona                                    |
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
// | Author: Ing. Carlos Alberto Candelario Corona, ccandelario@prodigy.net.mx              |
// +----------------------------------------------------------------------------------------+

include_once 'DB.php';

class DBclass {
	var $version = "2.0.0";						// Versión de la librería
	
	var $dsn; 									// Contiene el Data Source Name de la Base de Datos
												// Sintaxis:
												// {manejador}://{usuario}:{contraseña}@{host}:{puerto}/{nombre base}
												// Ejemplo: pgsql://pepito:jose@192.168.1.100:5432/prueba
							
	var $db;									// Contiene la Base de Datos
	
	var $conectado = FALSE;						// TRUE si esta conectado a la Base de Datos, FALSE si no
	
	var $sql; 									// Cadena que contiene el script de SQL
	var $tabla; 								// Nombre de la tabla en uso
	var $info   = array ();						// Información de la tabla en uso
	var $datos  = array ();						// Array con los datos de la tabla en uso
	var $campos = array ();						// Array con los nombres de los campos de la tabla en uso
	var $tipos  = array ();						// Array con los tipos de los campos de la tabla en uso
	var $numfilas; 								// Número de filas en el array de datos
	var $numcols; 								// Número de columnas en el arreglo y en la tabla en uso
	var $error;									// Regresa un código de error
	var $ultimo_error;							// Contiene la cadena del último error
	
	// Banderas de configuración
	var $mostrar_errores      = TRUE;			// TRUE: mostrar errores
	var $en_error_desconectar = TRUE;			// TRUE: desconectar en caso de error
	var $mostrar_script_error = TRUE;			// TRUE: mostrar script que provoco el error
	var $error_html           = TRUE;			// TRUE: mostrar errores en formato HTML
	var $autocommit           = FALSE;			// TRUE: genera una transacción por consulta
	var $autorollback         = TRUE;			// TRUE: En caso de error, ejecuta un ROLLBACK automáticamente
	var $datestyle            = "SQL, DMY";		// Estilo de la fecha para la Base de Datos
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | DBclass(string $dsn) -- Constructor para 'DBclass'.                              |
	// | $dsn -> Cadena con los datos de conexión a la Base de Datos.                     |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function DBclass($dsn, $opciones = "") {
		// Guardar cadena de conección
		$this->dsn = $dsn;
		
		// Conectar a la Base de Datos
		$this->conectar();
		
		// Poner bandera de conección en TRUE
		$this->conectado = TRUE;
		
		// Cambiar el formato de fecha a DD/MM/YYYY
		$this->db->query("SET datestyle = $this->datestyle");
		
		// Configura AUTO-COMMIT en OFF
		$this->db->autoCommit(FALSE);
		
		// Registrar función de desconección de la Base de Datos (DESTRUCTOR)
		register_shutdown_function(array(&$this, "desconectar"));
		
		// Configurar opciones adicionales
		$this->configurar_opciones($opciones);
		
		return TRUE;
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | configurar_opciones(string $cadena) -- Configura las propiedades del objeto.     |
	// | $cadena -> Cadena de opciones.                                                   |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function configurar_opciones($cadena) {
		// Convertir cadena a minúsculas
		$cadena = strtolower($cadena);
		
		// Dividir cadena de opciones
		if (!($opcion = parse_options($cadena)))
			return FALSE;
		
		foreach ($opcion as $key => $value) {
			switch ($key) {
				// Si se activa esta opción, se mostrará en pantalla un error descriptivo del método ejecutado
				case "mostrar_errores":
					$this->mostrar_errores = $value == "true" || $value == "yes" || $value == "si" || $value == "1" ? TRUE : FALSE;
					break;
				// Si se activa esta opción, se desconectara de la base de datos
				case "en_error_desconectar":
					$this->en_error_desconectar = $value == "true" || $value == "yes" || $value == "si" || $value == "1" ? TRUE : FALSE;
					break;
				// Si se activa esta opción, se mostrara en un recuadro o en su defecto el script que provoco el error
				case "mostrar_script_error":
					$this->mostrar_script_error = $value == "true" || $value == "yes" || $value == "si" || $value == "1" ? TRUE : FALSE;
					break;
				// Si se activa esta opción, se mostraran los errores en formato HTML
				case "error_html":
					$this->error_html = $value == "true" || $value == "yes" || $value == "si" || $value == "1" ? TRUE : FALSE;
					break;
				// Si se activa esta opción, se usará COMMIT automático
				case "autocommit":
					if ($value == "true" || $value == "yes" || $value == "si" || $value == "1") {
						$this->autocommit == TRUE;
						$this->db->autoCommit(TRUE);
					}
					else {
						$this->autocommit == FALSE;
						$this->db->autoCommit(FALSE);
					}
					break;
				// Si se activa esta opción, se usará ROLLBACK automáticamente después de un error
				case "autorollback":
					$this->autorollback = $value == "true" || $value == "yes" || $value == "si" || $value == "1" ? TRUE : FALSE;
					break;
				/*case "datestyle":
					$result = $this->db->query("SET datestyle = $value");
					$this->datestyle = $result ? $value : $this->datestyle;
					break;*/
			}
		}
		
		return TRUE;
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | conectar() -- Conecta a la Base de Datos.                                        |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function conectar() {
		// Conectarse a la Base de Datos
		$this->db =& DB::connect($this->dsn);
		if (DB::isError($this->db))
			$this->error($this->db->getUserInfo());
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | desconectar() -- Desconecta de la Base de Datos.                                 |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function desconectar() {
		if ($this->conectado == TRUE)
			$this->db->disconnect();
		$this->conectado = FALSE;
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | error() -- Manejador de errores de clase y base de datos.                        |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function error($mensaje = "", $sql = "") {
		// Ejecutar un ROLLBACK a todos los query's ejecutados en la transacción
		if ($this->conectado == TRUE && $this->autorollback == TRUE)
			$this->cancelar_transaccion();
		
		// Desconectar de la base de datos
		if ($this->en_error_desconectar == TRUE)
			$this->desconectar();
		
		// Si esta habilitada la opción de mostrar errores
		if ($this->mostrar_errores == TRUE) {
			// Cuerpo de la página de error
			$html = "<htnl>\n<head>\n\t<title>Error</title>\n</head>\n<body>\n\t<p>El siguiente error a ocurrido:</p>\n\t<table bgcolor=\"#CCCCCC\">\n\t\t<tr>\n\t\t\t<td><strong>";
			// Concatenar mensaje descriptivo del error
			if (strpos($mensaje,"ERROR:") === FALSE) {
				$html .= $mensaje;
				$this->ultimo_error = $mensaje;
			}
			else {
				$html .= ucfirst(trim(substr($mensaje, strpos($mensaje, "ERROR:") + 8), " ]"));
				$this->ultimo_error = ucfirst(trim(substr($mensaje, strpos($mensaje, "ERROR:") + 8), " ]"));
			}
			// Cuerpo de la página de error (continuación...)
			$html .= "</strong></td>\n\t\t</tr>\n\t</table>\n";
			// Concatenar script SQL ejecutado
			if ($sql != "" && $this->mostrar_script_error) {
				$html .= "\t<p>en la declaración</p>\n\t<p><textarea name=\"textarea\" cols=\"60\" rows=\"5\" readonly>";
				$html .= $sql;
				$html .= "</textarea></p>\n";
			}
			// Cuerpo de la página de error (continuación...)
			$html .= "</body>\n</html>";
			
			echo $html;
			
			// Terminar aplicación
			exit(-1);
		}
		else {
			if (strpos($mensaje,"ERROR:") === FALSE)
				$this->ultimo_error = $mensaje;
			else
				$this->ultimo_error = ucfirst(trim(substr($mensaje, strpos($mensaje, "ERROR:") + 8), " ]"));
			return -1;
		}
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | query(string $sql_script, int $fetchmode) -- Ejecuta un script SQL.              |
	// | $sql_script -> Cadena que contiene el script.                                    |
	// | $fetchmode  -> Modo de asignación (default = DB_FETCHMODE_ASSOC).                |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function query($sql_script, $fetchmode = DB_FETCHMODE_ASSOC) {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		// Verificar que $sql_script sea un dato tipo string
		if (!is_string($sql_script)) {
			trigger_error("DBclass->query() El primer parámetro debe ser una cadena de texto", E_USER_WARNING);
			return FALSE;
		}
		
		// Ejecutar script
		$result = $this->db->query($sql_script);
		
		// Llamar al manejador de errores si hay un error
		if (DB::isError($result)) {
			$this->error($result->getUserInfo(), $sql_script);
			return -1;
		}
		
		if (stristr($sql_script, "INSERT") || stristr($sql_script, "UPDATE") || stristr($sql_script, "DELETE") || stristr($sql_script, "TRUNCATE")
		 || stristr($sql_script, "BEGIN") || stristr($sql_script, "COMMIT") || stristr($sql_script, "ROLLBACK") || stristr($sql_script, "SET"))
			return TRUE;
		else {
			if ($result->numRows() > 0) {
				for ($i=0; $i<$result->numRows(); $i++)
					$row[$i] = $result->fetchRow($fetchmode);
				
				$result->free();
				return $row;	// Devuelve los registros resultados de la consulta
			}
			else {
				$result->free();
				return FALSE;	// Devuelve FALSE si no encontro nada
			}
		}
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | comenzar_transaccion() -- Empieza una transaccion.                               |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function comenzar_transaccion() {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		// Empezar transacción
		$this->query("BEGIN");
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | empezar_transaccion() -- Clon de comenzar_transaccion().                         |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function empezar_transaccion() {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		// Empezar transacción
		$this->query("BEGIN");
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | terminar_transaccion() -- Termina la transacción en curso.                       |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function terminar_transaccion() {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		// Empezar transacción
		$this->query("COMMIT");
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | cancelar_transaccion() -- Cancela la transaccion en curso.                       |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function cancelar_transaccion() {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		// Cancelar transacción
		$this->query("ROLLBACK");
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | obtener_info_tabla() -- Obtiene información de la tabla en uso.                  |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function obtener_info_tabla($tabla) {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		$this->tabla = $tabla;
		
		// Obtener información de la tabla
		$info = $this->db->tableInfo($tabla);
		
		if (DB::isError($info))
			$this->error($info->getUserInfo());
		
		// Retornar información de la tabla
		return $info;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | obtener_campos() -- Obtiene los campos de una tabla.                             |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function obtener_campos($info) {
		$this->campos = array();
		
		$i=0;
		foreach ($info as $key=>$value)
			// Omitir si es un campo autoincrementable
			if (!ereg("not_null default_nextval", $info[$key]['flags'])) {
				$campos[$i]['nombre'] = $info[$key]['name'];
				$campos[$i]['tipo']   = $info[$key]['type'];
				$i++;
			}
		
		$this->campos = $campos;
		
		return $campos;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | numCols() -- Función encargada de obtener el nmero de columnas en una tabla.     |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function numCols() {
		$this->numcols = count($this->campos);
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | numFilas() -- Función encargada de obtener el nmero de filas en una tabla.       |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function numFilas() {
		if (empty($this->numcols))
			$this->numCols();

		$i=0;
		foreach ($this->datos as $key=>$value)
			if ($key == $this->campos[0].$i && $value != NULL && $value != '')
				$i++;
		$this->numfilas = $i;
	}
	
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | script_insert(int $numreg) -- Genera el script SQL para insertar datos.          |
	// | $numreg -> Número de registro dentro de $this->datos.                            |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function preparar_insert($tabla, $datos) {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		if (!is_array($datos))
			return FALSE;
		
		// Obtener información de la tabla
		if ($this->tabla != $tabla) {
			$info   = $this->obtener_info_tabla($tabla);
			$campos = $this->obtener_campos($info);
		}
		else
			$campos = $this->campos;
		
		// Contar número de campos
		$num_campos = count($campos);
		
		// Generar script
		$sql = "INSERT INTO \"$tabla\" (";
		// Campos
		for ($i=0; $i<$num_campos; $i++)
			$sql .= "\"".$campos[$i]['nombre']."\"".($i < $num_campos - 1 ? "," : ")");
		// Datos
		$sql .= " VALUES (";
		for ($i=0; $i<$num_campos; $i++)
			// Si existe el campo, evaluarlo, si no insertar NULL en ese campo
			if (isset($datos[$campos[$i]['nombre']]))
				// Si el campo esta vacío, insertar NULL, si no insertar el valor del campo
				if ($datos[$campos[$i]['nombre']] /*== ""*/=== NULL || trim($datos[$campos[$i]['nombre']]) == "")
					$sql .= "NULL" . ($i < $num_campos - 1 ? "," : ")");
				else
					$sql .= "'" . $datos[$campos[$i]['nombre']] . "'" . ($i < $num_campos - 1 ? "," : ")");
			else
				$sql .= "NULL" . ($i < $num_campos - 1 ? "," : ")");
		
		return $sql;
	}
	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | script_insert(int $numreg) -- Genera el script SQL para insertar datos.          |
	// | $numreg -> Número de registro dentro de $this->datos.                            |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function preparar_update($tabla, $datos, $condicion = "") {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		if (!is_array($datos))
			return FALSE;
		
		// Generar script
		$sql = "UPDATE \"$tabla\" SET";
		// Campos y valores
		$i = 0;
		foreach ($datos as $key => $value) {
			$sql .= " \"$key\" = " . ($value != "" ? "'$value'" : "NULL") . ($i < count($datos) - 1 ? "," : "");
			$i++;
		}
		// Condición
		if ($condicion != "")
			$sql .= " WHERE $condicion";
		
		return $sql;
	}
	
	function multiple_insert($tabla, $datos) {
		// Verificar si esta conectado a la Base de Datos
		if (!$this->conectado)
			return FALSE;
		
		if (!is_array($datos))
			return FALSE;
		
		$sql = "";
		$num_reg = count($datos);
		
		for ($i=0; $i<$num_reg; $i++)
			$sql .= $this->preparar_insert($tabla, $datos[$i]) . ";\n";
		
		return $sql;
	}
}

// ********************************************************************************************************************************
// *                                                                                                                              *
// * FUNCIONES AUXILIARES DE LA CLASE                                                                                             *
// *                                                                                                                              *
// ********************************************************************************************************************************

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | parse_options(string $cadena) -- Divide una cadena con formato                   |
// | "opcion1=valor1,opcion2=valor2,..." en un arreglo de opciones.                   |
// | $cadena -> Cadena de opciones.                                                   |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function parse_options($cadena) {
	if (!is_string($cadena)) {
		trigger_error("parse_options() El primer parámetro debe ser una cadena de texto", E_USER_WARNING);
		return FALSE;
	}
	
	// Retornar FALSE si es una cadena vacía
	if ($cadena == "")
		return FALSE;
	
	// Dividir cadena
	$piezas = explode(",", trim($cadena));
	
	// Arreglo que almacenara las opciones
	$opcion = array();
	
	// Recorrer cada una de las piezas y asigar las opciones
	foreach ($piezas as $value) {
		list($nombre, $valor) = explode("=", $value);
		$opcion[$nombre] = $valor;
	}
	
	// Retornar arreglo de opciones
	return $opcion;
}

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | sql_timestamp() -- Genera un SQL Time Stamp.                                     |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function sql_timestamp() {
	return date("Y-m-d H:i:s");
}

function mes_escrito($mes, $mayusculas = FALSE) {
	// Evaluar $mes
	switch ((int)$mes) {
		case 1:  $string = "Enero";      break;
		case 2:  $string = "Febrero";    break;
		case 3:  $string = "Marzo";      break;
		case 4:  $string = "Abril";      break;
		case 5:  $string = "Mayo";       break;
		case 6:  $string = "Junio";      break;
		case 7:  $string = "Julio";      break;
		case 8:  $string = "Agosto";     break;
		case 9:  $string = "Septiembre"; break;
		case 10: $string = "Octubre";    break;
		case 11: $string = "Noviembre";  break;
		case 12: $string = "Diciembre";  break;
		default: $string = "";           break;
	}
	
	// Si $mayusculas = TRUE, retornar la cadena en mayúsculas
	if ($mayusculas == TRUE)
		return strtoupper($string);
	else
		return $string;
}

function get_val($string)  {
	$chars = array(',');
	
	if (strpos($string, '.') !== FALSE)
		$val = floatval(str_replace($chars, '', $string));
	else
		$val = intval(str_replace($chars, '', $string));
	
	return $val;
}
?>