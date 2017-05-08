<?php
// +----------------------------------------------------------------------------------------+
// | class.db3.inc.php                                                                      |
// | Librería diseñada para el manejo de Base de Datos.                                     |
// +----------------------------------------------------------------------------------------+
// |                                                                                        |
// | Copyright (C) 2004 Carlos Alberto Candelario Corona                                    |
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

class DBclass {
	var $dsn; 				// Contiene el Data Source Name de la Base de Datos
	var $sql; 				// Cadena que contiene el script de SQL
	var $tabla; 			// Contiene el nombre de la tabla
	var $info = array ();	// Contiene la informaci� de la tabla
	var $datos = array ();	// Contiene el array con los datos
	var $campos = array ();	// Contiene el array con los nombres de los campos de la tabla
	var $tipos = array ();	// Contiene el array con los tipos de los campos de la tabla
	var $numfilas; 			// Nmero de filas en el array de datos
	var $numcols; 			// Nmero de columnas en el arreglo y en la tabla
	var $error;				// Regresa un c�igo de error

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | DBclass(string $dsn, string $tabla, array $datos) -- Constructor para 'DBclass'. |
	// | $dsn   -> Contiene la cadena con los datos de conexion a la Base de Datos.       |
	// | $tabla -> Contiene el nombre de la tabla de trabajo.                             |
	// | $datos -> contiene los datos para trabajar con la tabla.                         |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function DBclass($dsn, $tabla, $datos) {
		$this->tabla = $tabla;
		$this->datos = $datos;
		$this->dsn = $dsn;

		$this->obtener_info_tabla();
		$this->obtener_campos();
		$this->numCols();
		$this->numFilas();
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | obtener_info_tabla() -- Funci� encargada de obtener informaci� de la tabla     |
	// | en uso.                                                                          |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function obtener_info_tabla() {
		// Conectarse a la base de datos
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			$_SESSION['funcion'] = "DBclass -&#8250; obtener_info_tabla()";
			$_SESSION['db_error'] = $db->getUserInfo();

			header("location: ./sql_error.php?error=db_error");

			die;
		}

		// Obtener informaci� de la tabla
		$this->info = $db->tableInfo($this->tabla);

		if (DB::isError($this->info)) {
			$db->disconnect();
			$_SESSION['funcion'] = "DBclass -&#8250; obtener_info_tabla()";
			$_SESSION['db_error'] = "No se pudo obtener informaci&oacute;n de la tabla '$this->tabla'";

			header("location: ./sql_error.php?error=db_error");

			die;
		}

		// Desconectar de la Base de Datos
		$db->disconnect();

		// Retornar informacion de la tabla
		return $this->info;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | obtener_campos() -- Funci� encargada de obtener los campos de la tabla.         |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function obtener_campos() {
		$i=0;
		foreach ($this->info as $key=>$value)
			// Omitir si es un campo autoincrementable
			// if (!ereg("not_null default_nextval",$this->info[$key]['flags'])) {
			if (!preg_match("/not_null default_nextval/",$this->info[$key]['flags'])) {
				$this->campos[$i] = $this->info[$key]['name'];
				$this->tipos[$i]  = $this->info[$key]['type'];
				$i++;
			}
		return $this->campos;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | numCols() -- Funci� encargada de obtener el nmero de columnas en una tabla.    |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function numCols() {
		$this->numcols = count($this->campos);
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | numFilas() -- Funci� encargada de obtener el nmero de filas en una tabla.         |
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
	// | generar_script_insert(int $numreg) -- Funci� encargada de generar el script SQL   |
	// | para inserci� de datos.                                                           |
	// | $numreg -> Contiene el nmero de registro dentro de $this->datos.                 |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function generar_script_insert($numreg) {
		$this->sql = "INSERT INTO $this->tabla (";

		// Campos
		for ($i=0; $i<$this->numcols-1; $i++)
			$this->sql .= $this->campos[$i].",";
		$this->sql .= $this->campos[$i++].") VALUES (";

		// Datos
		for ($i=0; $i<$this->numcols-1; $i++) {
			// Si existe el campo, evaluarlo, si no insertar NULL en ese campo
			if (isset($this->datos[$this->campos[$i].$numreg]))
				// Si el campo esta vacío, insertar NULL, si no insertar el valor del campo
				if (/*$this->datos[$this->campos[$i].$numreg] == NULL ||*/ $this->datos[$this->campos[$i].$numreg] === NULL || trim($this->datos[$this->campos[$i].$numreg]) === "")
					$this->sql .= "NULL,";
				else
					$this->sql .= "'".strtoupper($this->datos[$this->campos[$i].$numreg])."',";
			else
				$this->sql .= "NULL,";
		}
		// Si existe el campo, evaluarlo, si no insertar NULL en ese campo
		if (isset($this->datos[$this->campos[$i].$numreg]))
			// Si el campo esta vacío, insertar NULL, si no insertar el valor del campo
			if (/*$this->datos[$this->campos[$i].$numreg] == NULL ||*/ $this->datos[$this->campos[$i].$numreg] == '')
				$this->sql .= "NULL)";
			else
				$this->sql .= "'".strtoupper($this->datos[$this->campos[$i].$numreg])."')";
		else
			$this->sql .= "NULL)";

		return $this->sql;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | generar_script_update(int $numreg) -- Funci� encargada de generar el script SQL |
	// | para actualizacion de datos.                                                     |
	// | $numreg -> Contiene el nmero de registro dentro de $this->datos.                |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function generar_script_update($numreg, $campos, $valores) {
		$this->sql = "UPDATE $this->tabla SET ";

		for ($i=0; $i<$this->numcols-1; $i++) {
			$this->sql .= $this->campos[$i]." = ";
			if ($this->datos[$this->campos[$i].$numreg] == NULL || $this->datos[$this->campos[$i].$numreg] == '')
				$this->sql .= "NULL, ";
			else
				$this->sql .= "'".strtoupper($this->datos[$this->campos[$i].$numreg])."', ";
		}
		$this->sql .= $this->campos[$i]." = ";
		if ($this->datos[$this->campos[$i].$numreg] === NULL || $this->datos[$this->campos[$i].$numreg] == '')
			$this->sql .= "NULL";
		else
			$this->sql .= "'".strtoupper($this->datos[$this->campos[$i].$numreg])."' ";

		if (count($campos) >  0 && count($valores) > 0) {
			$this->sql .= " WHERE ";
			for ($i=0; $i<count($campos)-1; $i++) {
				$this->sql .= $campos[$i]." = '".strtoupper($valores[$i])."' AND ";
			}
			$this->sql .= $campos[$i]." = '".strtoupper($valores[$i])."'";
		}

		//$this->sql .= "WHERE ".$this->campos[0]." = '".strtoupper($this->datos[$this->campos[0].$numreg])."'";

		return $this->sql;
	}

	function generar_script_select() {
		$this->sql = "SELECT * FROM $this->tabla WHERE ".$this->campos[0]." = '".strtoupper($this->datos[$this->campos[0]])."' ";
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | ejecutar_script() -- Funci� encargada de ejecutar los scripts.                  |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function ejecutar_script() {
		// Conectarse a la base de datos
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			$_SESSION['funcion'] = "DBclass -&#8250; ejecutar_script()";
			$_SESSION['db_error'] = $db->getUserInfo();

			header("location: ./sql_error.php?error=db_error");

			die;
		}

		// Si se inserta un dato tipo fecha, cambiar el formato
		// de PostgreSQL a DD/MM/YY
		$db->query("SET datestyle = SQL,European");

		// [20-Nov-2010] Cambiar la codificación
		$db->query("SET client_encoding TO LATIN1");

		$db->query("BEGIN");

		// QUERY
		$result = $db->query($this->sql);

		$db->query("COMMIT");

		// Desconcetar de la Base de Datos
		$db->disconnect();

		if (DB::isError($result)) {
			$_SESSION['funcion'] = "DB -&#8250; ejecutar_script()";
			$_SESSION['sql_error'] = $result->getUserInfo();
			$_SESSION['script'] = $this->sql;

			header("location: ./sql_error.php?error=sql_error");

			die;
		}

		return $result;
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | xinsertar() -- Función� encargada de insertar multiples registros en una tabla.   |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function xinsertar() {
		for ($i=0; $i<=$this->numfilas-1; $i++) {
			$this->generar_script_insert($i);
			$this->ejecutar_script();
		}
	}

	// +----------------------------------------------------------------------------------+
	// |                                                                                  |
	// | xactualizar() -- Función� encargada de actualizar multiples registros en una      |
	// | tabla.                                                                           |
	// |                                                                                  |
	// +----------------------------------------------------------------------------------+
	function xactualizar($campos_busqueda) {
		for ($i=0; $i<=$this->numfilas-1; $i++)  {
			foreach ($campos_busqueda as $key=>$value) {
				$campos[$key] = $this->campos[$value];
				$valores[$key] = $this->datos[$this->campos[$value].$i];
			}
			$this->generar_script_update($i,$campos,$valores);
			$this->ejecutar_script();
		}
	}
}

/******************************** Funciones externas ************************************/

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | nextID(string $tabla, string $campo_id, string $dsn) -- Funci� encargada de     |
// | buscar un ID disponible en una Tabla.                                            |
// | $tabla    -> El nombre de la tabla a consultar.                                  |
// | $campo_id -> El campo ID a consultar.                                            |
// | $dsn      -> Datos para la conexi� a la base de datos.                          |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function nextID($tabla, $dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "nextID()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// Obtener informaci� de la tabla
	$info = $db->tableInfo($tabla);

	if (DB::isError($info)) {
		$db->disconnect();
		$_SESSION['funcion'] = "nextID()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// QUERY
	$sql = "SELECT ".$info[0]['name']." FROM $tabla ORDER BY ".$info[0]['name'];
	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "nextID()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	// Si no existen registros retornar indice 1
	if ($result->numRows() == 0)
		return 1;

	// Indice inicialmente es 1
	$i = 1;

	while ($row = $result->fetchRow(DB_FETCHMODE_ORDERED)) {
		// Si indice es diferente de llave primaria, existe un hueco en la tabla
		// retornar indice
		if ($row[0] != $i)
			return $i;
		// Incrementar indice
		$i++;
	}

	// Retornar ultimo indice si se llego al final de la tabla
	$result->free();
	return $i;
}

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | nextID2(string $tabla, string $campo_id, string $dsn) -- Funci� encargada de      |
// | buscar un ID disponible en una Tabla.                                            |
// | $tabla    -> El nombre de la tabla a consultar.                                  |
// | $campo_id -> El campo ID a consultar.                                            |
// | $dsn      -> Datos para la conexi� a la base de datos.                            |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function nextID2($tabla, $campo, $dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "nextID2()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// QUERY
	$sql = "SELECT $campo FROM $tabla GROUP BY $campo ORDER BY $campo ASC";
	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "nextID2()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	// Si no existen registros retornar indice 1
	if ($result->numRows() == 0)
		return 1;

	// Indice inicialmente es 1
	$i = 1;

	while ($row = $result->fetchRow(DB_FETCHMODE_ORDERED)) {
		// Si indice es diferente de llave primaria, existe un hueco en la tabla
		// retornar indice
		if ($row[0] != $i) {
			$result->free();
			return $i;
		}
		// Incrementar indice
		$i++;
	}

	// Retornar ultimo indice si se llego al final de la tabla
	$result->free();
	return $i;
}

function nextID3($tabla, $campo, $dsn, $condicion = NULL) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "nextID3()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// QUERY
	$sql = "SELECT $campo FROM $tabla";
	if ($condicion != NULL)
		$sql .= " WHERE $condicion";
	$sql .= " GROUP BY $campo ORDER BY $campo ASC";
	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "nextID3()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	// Si no existen registros retornar indice 1
	if ($result->numRows() == 0)
		return 1;

	// Indice inicialmente es 1
	$i = 1;

	while ($row = $result->fetchRow(DB_FETCHMODE_ORDERED)) {
		// Si indice es diferente de llave primaria, existe un hueco en la tabla
		// retornar indice
		if ($row[0] != $i) {
			$result->free();
			return $i;
		}
		// Incrementar indice
		$i++;
	}

	// Retornar ultimo indice si se llego al final de la tabla
	$result->free();
	return $i;
}

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | existe_registro(string $tabla, string $campo, mixed $valor, string $dsn)         |
// | Funci� encargada de buscar la primera ocurrencia de un campo en una tabla.        |
// | $tabla   -> El nombre de la tabla a consultar.                                   |
// | $campos  -> Campos de comparaci�.                                                 |
// | $valores -> Valores de comparaci� (mismo orden que $campos).                      |
// | $dsn     -> Datos para la conexi� a la base de datos.                             |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function existe_registro($tabla, $campos, $valores, $dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "existe_registro()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// Si se inserta un dato tipo fecha, cambiar el formato
	// de PostgreSQL a DD/MM/YY
	$db->query("SET datestyle = SQL,European");

	// [20-Nov-2010] Cambiar la codificación
	$db->query("SET client_encoding TO LATIN1");

	// QUERY
	$sql = "SELECT * FROM $tabla WHERE ";
	for ($i=0; $i<count($campos)-1; $i++) {
		$sql .= $campos[$i]." ".($valores[$i] != NULL ? "= '$valores[$i]'" : 'IS NULL')." AND ";
	}
	$sql .= $campos[$i]." ".($valores[$i] != NULL ? "= '$valores[$i]'" : 'IS NULL')." LIMIT 5";

	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "existe_registro()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	if ($result->numRows()) {
		$result->free();
		return TRUE;	// Devuelve TRUE si encontro una ocurrencia en la tabla
	}
	else {
		$result->free();
		return FALSE;	// Devuelve FALSE si no encontro nada
	}
}

// +----------------------------------------------------------------------------------+
// |                                                                                  |
// | obtener_registro(string $tabla, string $campo, mixed $valores,                   |
// |                  string $ordenar_por,string $dir,string $dsn)                    |
// | Funci� encargada de obtener la primera ocurrencia de un campo en una tabla.     |
// | $tabla       -> El nombre de la tabla a consultar.                               |
// | $campos      -> Campos de comparaci�.                                           |
// | $valores     -> Valores de comparaci� (mismo orden que $campos).                |
// | $ordenar_por -> Campo por el cual se ordenan los registros.                      |
// | $dir         -> "ASC" o "DESC".                                                  |
// | $dsn         -> Datos para la conexi� a la base de datos.                       |
// |                                                                                  |
// +----------------------------------------------------------------------------------+
function obtener_registro($tabla, $campos, $valores, $ordenar_por, $dir, $dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "obtener_registro()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// Si se inserta un dato tipo fecha, cambiar el formato
	// de PostgreSQL a DD/MM/YY
	$db->query("SET datestyle = SQL,European");

	// [20-Nov-2010] Cambiar la codificación
	$db->query("SET client_encoding TO LATIN1");

	// QUERY
	$sql = "SELECT * FROM $tabla";
	if (count($campos) >  0 && count($valores) > 0) {
		$sql .= " WHERE ";
		for ($i=0; $i<count($campos)-1; $i++) {
			$sql .= $campos[$i]." ".($valores[$i] != NULL ? "= '$valores[$i]'" : 'IS NULL')." AND ";
		}
		$sql .= $campos[$i]." ".($valores[$i] != NULL ? "= '$valores[$i]'" : 'IS NULL');
	}

	// Ordenar por...
	if ($ordenar_por != "" && $dir != "")
		$sql .= " ORDER BY $ordenar_por $dir";

	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "obtener_registro()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	for ($i=0; $i<$result->numRows(); $i++) {
		$row[$i] = $result->fetchRow(DB_FETCHMODE_ASSOC);
	}

	if ($result->numRows() > 0) {
		$result->free();
		return $row;	// Devuelve el primer registro resultado de la consulta
	}
	else {
		$result->free();
		return FALSE;	// Devuelve FALSE si no encontro nada
	}
}

function ejecutar_script($sql,$dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		$_SESSION['funcion'] = "ejecutar_script()";
		$_SESSION['db_error'] = $db->getUserInfo();

		header("location: ./sql_error.php?error=db_error");

		die;
	}

	// Si se inserta un dato tipo fecha, cambiar el formato
	// de PostgreSQL a DD/MM/YY
	$db->query("SET datestyle = SQL,European");

	// [20-Nov-2010] Cambiar la codificación
	$db->query("SET client_encoding TO LATIN1");

	// Ejecutar script
	$result = $db->query($sql);
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		$_SESSION['funcion'] = "ejecutar_script()";
		$_SESSION['sql_error'] = $result->getUserInfo();
		$_SESSION['script'] = $sql;

		header("location: ./sql_error.php?error=sql_error");

		die;
	}

	if (stristr($sql,"INSERT") || stristr($sql,"UPDATE") || stristr($sql,"DELETE") || stristr($sql,"TRUNCATE")) {
			return TRUE;
	}
	else {
		for ($i=0; $i<$result->numRows(); $i++)
			$row[$i] = $result->fetchRow(DB_FETCHMODE_ASSOC);

		if ($result->numRows() > 0) {
			$result->free();
			return $row;	// Devuelve los registros resultados de la consulta
		}
		else {
			$result->free();
			return FALSE;	// Devuelve FALSE si no encontro nada
		}
	}
}

function mes_escrito($mes, $mayusculas = FALSE) {
	// Evaluar $mes
	switch ($mes) {
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

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function recorta_cadena($cadena, $longitud) {
	if (strlen($cadena) > $longitud) {
		$cadena = substr($cadena,0,$longitud-3) . "...";
		return $cadena;
	}
	else
		return $cadena;
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
