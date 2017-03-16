<?php
	include 'DB.php';

	class DBclass {
		var $dsn; // Contiene el Data Source Name de la Base de Datos
		var $sql; // Cadena que contiene el script de SQL
		var $tabla; // Contiene el nombre de la tabla
		var $datos = array (); // Contiene el array con los datos
		var $campos = array (); // Contiene el array con los nombres de campos
		var $numrows; // N?mero de filas en el array de datos
		var $numcols; // N?mero de columnas en el arreglo y en la tabla
		var $error; // Regresa un c?digo de error

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
			if (DB::isError($db))
				die($db->getMessage());

			// Si se inserta un dato tipo fecha, cambiar el formato
			// de PostgreSQL a DD/MM/YY
			$db->query("SET datestyle = SQL,European");

			// QUERY
			$result = $db->query($this->sql);

			if (DB::isError($result))
				die($result->getMessage());

			// Desconcetar de la Base de Datos
			$db->disconnect();
			return $result;
		}

		function consultar($campos, $condicion) {
			$db = DB::connect($this->dsn);
			if (DB::isError($db))
				die($db->getMessage());

			$result = $db->query("SELECT $campos FROM $this->tabla WHERE $condicion");
			$rows = $result->fetchRow(DB_FETCHMODE_OBJECT);

			if (DB::isError($result))
				die($result->getMessage());

			// Desconcetar de la Base de Datos
			$db->disconnect();
			return $rows;
		}

		function tabla_info() {
			$db = DB::connect($this->dsn);
			if (DB::isError($db))
    			die($db->getMessage());


			$info = $db->tableInfo($this->tabla);

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
			if ($this->campos)
				$this->numcols = count($this->campos);
			else {
				$db = DB::connect($this->dsn);
				if (DB::isError($db))
					die($db->getMessage());

				$result = $db->query("SELECT * FROM $this->tabla");
				$this->numcols = $result->numCols();

				// Desconectar de la Base de Datos
				$db->disconnect();
			}
		}

		function generar_script_insert($numreg) {
			$this->sql = "INSERT INTO $this->tabla (";

			for ($i=0; $i<$this->numcols-1; $i++)
				$this->sql .= $this->campos[$i].",";
			$this->sql .= $this->campos[$i++].") VALUES ('";

			for ($j=$numreg*$this->numcols; $j<$numreg*$this->numcols+$this->numcols-1; $j++)
					$this->sql .= $this->datos["campo".$j]."','";
			$this->sql .= $this->datos["campo".$j++]."')";
		}
	}

	function cambiar_tipo_fecha() {
		$db = DB::connect($this->dsn);
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
