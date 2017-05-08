<?php
include_once 'DB.php';

class DBclass {
	var $dsn; // Contiene el Data Source Name de la Base de Datos
	var $sql; // Cadena que contiene el script de SQL
	var $tabla; // Contiene el nombre de la tabla
	var $datos = array (); // Contiene el array con los datos
	var $campos = array (); // Contiene el array con los nombres de campos
	var $numrows; // Número de filas en el array de datos
	var $numcols; // Número de columnas en el arreglo y en la tabla
	var $error; // Regresa un código de error

	function DBclass($dsn, $tabla, $datos) {
		$this->tabla = $tabla;
		$this->datos = $datos;
		$this->dsn = $dsn;
	}

	function xinsertar() {
		$this->determinar_campos($this->tabla_info());
		$this->determinar_cols();
		$this->determinar_rows();

		for ($i=0; $i<=$this->numrows-1; $i++) {
			$this->generar_script_insert($i);
			$this->insertar();
		}
	}

	function insertar() {
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. class.db2.inc->insertar()<br>";
			die($db->getMessage());
		}
		
		// Si se inserta un dato tipo fecha, cambiar el formato
		// de PostgreSQL a DD/MM/YY
		$db->query("SET datestyle = SQL,European");
		
		// [20-Nov-2010] Cambiar la codificación
		$db->query("SET client_encoding TO LATIN1");

		// QUERY
		$result = $db->query($this->sql);

		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: $this->sql<br>Avisar al administrador. class.db2.inc->insertar()<br>";
			die($result->getMessage());
		}

		// Desconcetar de la Base de Datos
		$db->disconnect();
		return $result;
	}

	function consultar($campos, $condicion) {
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. class.db2.inc->consultar()<br>";
			die($db->getMessage());
		}

		$result = $db->query("SELECT $campos FROM $this->tabla WHERE $condicion");
		$rows = $result->fetchRow(DB_FETCHMODE_OBJECT);

		if (DB::isError($result)) {
			$db->disconnect();
			echo "Error en script SQL: SELECT $campos FROM $this->tabla WHERE $condicion<br>Avisar al administrador. class.db2.inc->consultar()<br>";
			die($result->getMessage());
		}
		// Desconcetar de la Base de Datos
		$db->disconnect();
		return $rows;
	}

	function tabla_info() {
		$db = DB::connect($this->dsn);
		if (DB::isError($db)) {
			echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. class.db2.inc->tabla_info()<br>";
			die($db->getMessage());
		}

		$info = $db->tableInfo($this->tabla);

		if (DB::isError($info)) {
			$db->disconnect();
			echo "Error al obtener informacion de la tabla. class.db2.inc->tabla_info()<br>";
			die($result->getMessage());
		}
		
		// Desconectar de la Base de Datos
		$db->disconnect();
		return $info;
	}

	function determinar_campos($info) {
		$i=0;
		reset($this->campos);
		foreach ($info as $key=>$value)
			// Omitir si es un campo autoincrementable
			if (!ereg("not_null default_nextval",$info[$key]['flags']))
				$this->campos[$i++] = $info[$key]['name'];
		return $this->campos;
	}

	function determinar_rows() {
		if (empty($this->numcols))
			$this->determinar_cols();

		$i=0;
		reset($this->datos);
		foreach ($this->datos as $key=>$value)
			if ($value != NULL && $key == 'campo'.$i)
				$i++;
		$this->numrows = $i/$this->numcols;
	}

	function determinar_cols() {
		if (!isset($this->campos))
			$this->determinar_campos($this->tabla_info);
		$this->numcols = count($this->campos);
	}

	function generar_script_insert($numreg) {
		$this->sql = "INSERT INTO $this->tabla (";

		for ($i=0; $i<$this->numcols-1; $i++)
			$this->sql .= $this->campos[$i].",";
		$this->sql .= $this->campos[$i++].") VALUES (";

		for ($j=$numreg*$this->numcols; $j<$numreg*$this->numcols+$this->numcols-1; $j++)
			if ($this->datos["campo".$j] == '')
				$this->sql .= "NULL,";
			else
				$this->sql .= "'".strtoupper($this->datos["campo".$j])."',";
		if ($this->datos["campo".$j] == '')
			$this->sql .= "NULL)";
		else
			$this->sql .= "'".strtoupper($this->datos["campo".$j++])."')";
	}
}

// NEXTID -- Retorna el proximo ID disponible dentro de una tabla
function nextid($tabla, $campo_id, $dsn) {
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. class.db2.inc->nextid()<br>";
		die($db->getMessage());
	}
	
	$result = $db->query("SELECT $campo_id FROM $tabla ORDER BY $campo_id");
	$db->disconnect();

	if (DB::isError($result)) {
		$db->disconnect();
		echo "Error en script SQL: SELECT $campo_id FROM $tabla ORDER BY $campo_id<br>Avisar al administrador.<br>";
		die($result->getMessage());
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
	return $i;
}

function cambiar_tipo_fecha() {
	$db = DB::connect($dsn);
	if (DB::isError($db))
	die($db->getMessage());

	// Cambiar estilo de fecha a DD/MM/YY
	$db->query("SET datestyle = SQL,European");

	// Desconectar de la Base de Datos
	$db->disconnect();
}

function fecha_sql($fecha) {
	$temp = split($fecha,"/");
	$new_fecha = $temp[2]."/".$temp[1];
}


?>
