<?php
/* Funciones para control y acceso a la Base de Datos */
class dbclass {
	var $db; // Variable que apunta a la base de datos
	var $numcols;
	var $numrows;

	function insert($tabla, $datos) {
		include 'DB.php';
		include 'dbstatus.php';

		$db = DB::connect($dsn);

		$result = $db->query("SELECT * FROM $tabla");

		$numcols = $result->numCols($sql);
		$numrows = (count($datos))/$numcols;
		$info = $db->tableInfo($tabla);

		// Ciclo for para insertar registros
		for ($i=0; $i<$numrows*$numcols; $i+=$numcols) {
			// Ciclo for para crear 1a estructura del script
			$isnull = 0;
			$sql = "INSERT INTO $tabla (";
			for ($j=0; $j<$numcols-1; $j++)
				$sql .= $info[$j]['name'].",";
			$sql .= $info[$j++]['name'].") VALUES ('";
			// Ciclo for para crear 2a estructura del script
			for ($j=0; $j<$numcols-1; $j++) {
				// Si datos diferente de NULL generar script
				if ($datos[$i+$j] != "") {
					$sql .= $datos[$i+$j]."','";
					$isnull++;
				}
				else
					$sql .= "','";
			}
			if ($isnull > 0) {
				$sql .= $datos[$i+($j++)]."')";
				// Ejecutar script de SQL
				echo $sql;
				$result = $db->query($sql);
			}
		}

		// Error de Acceso a la Base de Datos
		if(DB::isError($result)) {
			echo $result->getMessage();
			exit;
		}

		// Desconectar de la Base de Datos
		$db->disconnect();
	}

	function determinar_campos($tabla) {
		$result = $db->query("SELECT * FROM $tabla");
		$cols = $result->numCols($sql);
		$info = $db->tableInfo($tabla);

		$campos = "";
		for ($i=0; $i<$cols-1; $i++)
			if ($info[$j]['flags'] == "")
				$campos .= $info[$i]['name'].",";
		$campos .= $info[$i++]['name'];


}
?>
