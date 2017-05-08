<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$sql = "SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

// Copia inventario virtual a una tabla temporal
//$sql = "insert into inventario_virtual_temp (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_virtual WHERE num_cia < 100;";
//$sql = "insert into inventario_real_temp (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_real WHERE num_cia < 100;";
$sql = "delete from inventario_virtual where num_cia < 100;";
$sql .= "insert into inventario_virtual (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_real where num_cia < 100;";

ejecutar_script($sql,$dsn);


for ($c=0; $c<count($cia); $c++) {
	// Variables
	$num_cia = $cia[$c]['num_cia'];			// Número de Compañía
	$fecha = '01/02/2005';				// Fecha del listado
	$fecha_his = '31/12/2004';
	
	$tablas = array(0=>"real"/*,1=>"virtual"*/);
	
	for ($j=0; $j<count($tablas); $j++) {
		$tabla = $tablas[$j];
		
		// Seleccionar tablas de inventarios y movimientos
		if ($tabla == "virtual") {
			$tabla_inventario = "inventario_virtual";	// Tabla de donde se tomara el inventario
			$tabla_movimientos = "mov_inv_virtual";		// Tabla de donde se tomaran los movimientos
		}
		else if ($tabla == "real") {
			//$tabla_inventario = "inventario_real";	// Tabla de donde se tomara el inventario
			//$tabla_movimientos = "mov_inv_real";	// Tabla de donde se tomaran los movimientos
			$tabla_inventario = "inventario_virtual";
			$tabla_movimientos = "mov_inv_real";
		}
		
		/*if ($tabla == "virtual") {
			$sql = "DELETE FROM mov_inv_virtual_temp WHERE num_cia=$num_cia AND fecha>='$fecha';
			DELETE FROM inventario_virtual_temp WHERE num_cia=$num_cia;";
			ejecutar_script($sql,$dsn);
			
			// Copia inventario virtual a una tabla temporal
			$sql = "insert into inventario_virtual_temp (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_virtual where num_cia=$num_cia;";
			// Copia movimientos de inventario virtual a una tabla temporal
			$sql .= "insert into mov_inv_virtual_temp (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) select num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion from mov_inv_virtual where num_cia=$num_cia and fecha='$fecha';";
			ejecutar_script($sql,$dsn);
		}
		else if ($tabla == "real") {
			$sql = "DELETE FROM mov_inv_real_temp WHERE num_cia=$num_cia AND fecha>='$fecha';
			DELETE FROM inventario_real WHERE num_cia=$num_cia;";
			ejecutar_script($sql,$dsn);
			
			// Copia inventario real a una tabla temporal
			$sql .= "insert into inventario_real_temp (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_real where num_cia=$num_cia;";
			// Copia movimientos de inventario real a una tabla temporal
			$sql .= "insert into mov_inv_real_temp (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) select num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion from mov_inv_real where num_cia=$num_cia and fecha='$fecha' and tipo_mov='TRUE';";
			ejecutar_script($sql,$dsn);
		}*/
		
		// Obtener listado de las materias primas
		$sql = "SELECT codmp FROM $tabla_movimientos WHERE num_cia=$num_cia AND fecha>='$fecha' GROUP BY codmp ORDER BY codmp";
		$mp = ejecutar_script($sql,$dsn);
		
		for ($i=0; $i<count($mp); $i++) {
			$codmp = $mp[$i]['codmp'];
			
			$sql = "SELECT existencia FROM $tabla_inventario WHERE num_cia=$num_cia AND codmp=$codmp";
			$temp = ejecutar_script($sql,$dsn);
			$existencia_inv = ($temp)?$temp[0]['existencia']:0;
			// Obtener entradas y salidas de avio despues de la fecha del listado
			$sql = "SELECT 
			(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='FALSE') AS entradas,
			(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='TRUE') AS salidas";
			$dif = ejecutar_script($sql,$dsn);
			// Obtener la existencia para la fecha en específico
			$existencia = $existencia_inv;
			if ($tabla == "virtual")
				$existencia = $existencia - /*$dif[0]['salidas'] +*/ $dif[0]['entradas'];
			else if ($tabla == "real")
				$existencia = $existencia - /*$dif[0]['salidas'] +*/ $dif[0]['entradas'];
			
			//$sql = "SELECT AVG(precio_unidad) AS precio_unidad FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha'";
			//$temp = ejecutar_script($sql,$dsn);
			//$precio_unidad = ($temp[0]['precio_unidad'] > 0)?$temp[0]['precio_unidad']:"0";
			
			// Actualizar existencia
			if (existe_registro($tabla_inventario,array("num_cia","codmp"),array($num_cia,$codmp),$dsn))
				//$sql = "UPDATE $tabla_inventario SET existencia=$existencia,precio_unidad=$precio_unidad WHERE num_cia=$num_cia AND codmp=$codmp";
				$sql = "UPDATE $tabla_inventario SET existencia=$existencia WHERE num_cia=$num_cia AND codmp=$codmp";
			//else
				//$sql = "INSERT INTO $tabla_inventario (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia,$codmp,$existencia,$precio_unidad);
				//INSERT INTO historico_inventario (num_cia,codmp,existencia,fecha,precio_unidad) VALUES ($num_cia,$codmp,0,'$fecha_his',$precio_unidad);";
			//echo "SQL => $sql<br>";
			ejecutar_script($sql,$dsn);
		}
	}
}
/*$sql = "DELETE FROM mov_inv_virtual WHERE num_cia=$num_cia AND fecha>='$fecha';
DELETE FROM mov_inv_real WHERE num_cia=$num_cia AND fecha>='$fecha' AND tipo_mov='TRUE'";
ejecutar_script($sql,$dsn);*/
?>