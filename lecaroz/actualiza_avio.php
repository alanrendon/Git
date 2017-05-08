<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

// Variables
$num_cia = 75;			// Número de Compañía
$fecha = '15/06/2005';				// Fecha del listado

$tablas = array(0=>"real",1=>"virtual");

for ($j=0; $j<count($tablas); $j++) {
	$tabla = $tablas[$j];
	
	// Seleccionar tablas de inventarios y movimientos
	if ($tabla == "virtual") {
		$tabla_inventario = "inventario_virtual";	// Tabla de donde se tomara el inventario
		$tabla_movimientos = "mov_inv_virtual";		// Tabla de donde se tomaran los movimientos
	}
	else if ($tabla == "real") {
		$tabla_inventario = "inventario_real";	// Tabla de donde se tomara el inventario
		$tabla_movimientos = "mov_inv_real";	// Tabla de donde se tomaran los movimientos
	}
	
	if ($tabla == "virtual") {
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
		DELETE FROM inventario_real_temp WHERE num_cia=$num_cia;";
		ejecutar_script($sql,$dsn);
		
		// Copia inventario real a una tabla temporal
		$sql .= "insert into inventario_real_temp (num_cia,codmp,existencia,precio_unidad) select num_cia,codmp,existencia,precio_unidad from inventario_real where num_cia=$num_cia;";
		// Copia movimientos de inventario real a una tabla temporal
		$sql .= "insert into mov_inv_real_temp (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) select num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion from mov_inv_real where num_cia=$num_cia and fecha='$fecha' and tipo_mov='TRUE';";
		ejecutar_script($sql,$dsn);
	}
	
	// Obtener listado de las materias primas
	$sql = "SELECT codmp,nombre,existencia,precio_unidad FROM control_avio LEFT JOIN catalogo_mat_primas USING(codmp) LEFT JOIN inventario_virtual USING(num_cia,codmp) WHERE num_cia=$num_cia GROUP BY codmp,nombre,num_orden,existencia,precio_unidad ORDER BY num_orden ASC";
	$mp = ejecutar_script($sql,$dsn);
	
	for ($i=0; $i<count($mp); $i++) {
		$codmp = $mp[$i]['codmp'];
		// Obtener entradas y salidas de avio despues de la fecha del listado
		$sql = "SELECT 
		(SELECT SUM(cantidad".(isset($_GET['costeado'])?"*precio_unidad":"").") FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='FALSE') AS entradas,
		(SELECT SUM(cantidad".(isset($_GET['costeado'])?"*precio_unidad":"").") FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='TRUE') AS salidas";
		$dif = ejecutar_script($sql,$dsn);
		// Obtener la existencia para la fecha en específico
		$existencia = $mp[$i]['existencia'];
		if ($tabla == "virtual")
			$existencia = $existencia + $dif[0]['salidas'] - $dif[0]['entradas'];
		else if ($tabla == "real")
			$existencia = $existencia + $dif[0]['salidas'];
		
		// Actualizar existencia
		$sql = "UPDATE $tabla_inventario SET existencia=$existencia WHERE num_cia=$num_cia AND codmp=$codmp";
		ejecutar_script($sql,$dsn);
	}
}
$sql = "DELETE FROM mov_inv_virtual WHERE num_cia=$num_cia AND fecha>='$fecha';
DELETE FROM mov_inv_real WHERE num_cia=$num_cia AND fecha>='$fecha' AND tipo_mov='TRUE'";
ejecutar_script($sql,$dsn);
?>