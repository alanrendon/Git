<?php
// CONSUMO DE AVIO
// Tablas varias ''
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1222); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compa&ntilde;&iacute;a no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_mod_table.tpl");
$tpl->prepare();

// Conectar a la base de datos
$db = new DBclass($dsn);

// ----------------------------- Generar pantalla de captura ----------------------------------
$fecha = $_GET['fecha'];

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha,$fecha_des);

// Fechas para obtener promedios de consumo
$fecha1 = $fecha_des[1] > 5 ? "1/$fecha_des[2]/$fecha_des[3]" : date("d/m/Y",mktime(0,0,0,$fecha_des[2]-1,1,$fecha_des[3]));
$fecha2 = $fecha_des[1] > 5 ? $fecha : date("d/m/Y",mktime(0,0,0,$fecha_des[2],0,$fecha_des[3]));

// Asignar numero y nombre de compañia, asi como la fecha de captura
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("fecha",$fecha);
$tpl->assign("tabla","mov_inv_virtual");

// Obtener materias primas controladas por turno para la compañía en uso
$avio = $db->query("SELECT * FROM control_avio LEFT JOIN catalogo_mat_primas USING(codmp) LEFT JOIN inventario_virtual USING(num_cia,codmp) WHERE num_cia=$_GET[num_cia] ORDER BY num_orden,cod_turno ASC");
$result = $db->query("SELECT count(codmp) FROM (SELECT codmp FROM control_avio WHERE num_cia=$_GET[num_cia] GROUP BY codmp) AS elementos");
$numfilas = $result[0]['count'];

// Obtener consumos del dia especificado
$sql = "SELECT codmp,cod_turno,tipo_mov,cantidad FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha' ORDER BY codmp,cod_turno,tipo_mov";
$mov = $db->query($sql);

// Funcion de busqueda de consumos
function buscar($array, $codmp, $cod_turno, $tipo_mov) {
	if (!is_array($array))
		return 0;
	
	$numfilas = count($array);
	for ($i=0; $i<$numfilas; $i++)
		if ($codmp == $array[$i]['codmp'] && $cod_turno == $array[$i]['cod_turno'] && $tipo_mov[$i]['tipo_mov'])
			return $array[$i]['cantidad'];
	
	return 0;
}

$tpl->assign("numelementos",count($avio));
$tpl->assign("numfilas",$numfilas);

$codmp = NULL;
$lines = 0;
for ($i=0; $i<count($avio); $i++) {
	if ($avio[$i]['codmp'] != $codmp) {
		$codmp = $avio[$i]['codmp'];
		$lines++;
	}
	switch ($avio[$i]['cod_turno']) {
		case 1:  $turno[$lines]['fd']  = $i; break;
		case 2:  $turno[$lines]['fn']  = $i; break;
		case 3:  $turno[$lines]['bd']  = $i; break;
		case 4:  $turno[$lines]['rep'] = $i; break;
		case 8:  $turno[$lines]['pic'] = $i; break;
		case 9:  $turno[$lines]['gel'] = $i; break;
		case 10: $turno[$lines]['des'] = $i; break;
	}
}

// Porcentaje de incremento del promedio de consumo diario
$pi = 1.15;

$mp = NULL;
$fila = 0;
for ($i=0; $i<count($avio); $i++) {
	// Crear bloque de materia prima
	if ($avio[$i]['codmp'] != $mp) {
		$mp = $avio[$i]['codmp'];
		$tpl->newBlock("mp");
		$tpl->assign("mp",$avio[$i]['nombre']);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("mes",$fecha_des[2]);
		$tpl->assign("anio",$fecha_des[3]);
		$tpl->assign("codmp",$avio[$i]['codmp']);
		$tpl->assign("i",$fila);
		
		// Poner bandera de que es el principio de linea
		$first = TRUE;
		
		$tpl->assign("codmp_entrada",$avio[$i]['codmp']);
		$tpl->assign("precio_unidad_entrada",$avio[$i]['precio_unidad']);
		
		$tpl->assign("next",($fila<$numfilas/*-1*/)?$i:0);
		$tpl->assign("back",($fila>0)?$i-1:count($avio)-1);
		
		$tpl->assign("bottom",($fila<$numfilas-1)?$fila+1:0);
		$tpl->assign("top",($fila>0)?$fila-1:$numfilas-1);
		
		if ($avio[$i]['codmp'] == 1)
			$existencia = $avio[$i]['existencia'] / 44;
		else
			$existencia = $avio[$i]['existencia'];
		
		$tpl->assign("existencia",number_format($existencia,2,".",""));
		$temp = $db->query("SELECT existencia FROM inventario_real WHERE num_cia = $_GET[num_cia] AND codmp = $mp");
		if ($avio[$i]['codmp'] == 1)
			$existencia_real = $temp[0]['existencia'] / 44;
		else
			$existencia_real = $temp[0]['existencia'];
		
		$tpl->assign("fexistencia",number_format($existencia,2,".",","));
		
		if (round($existencia,2) == round($existencia_real,2))
			$tpl->assign("color","0000FF");
		else
			$tpl->assign("color","FF0000");
		
		$fila++;
		
		// Crear celdas vacias
		if (!isset($turno[$fila]['fd']))
			$tpl->newBlock("no_fd");
		if (!isset($turno[$fila]['fn']))
			$tpl->newBlock("no_fn");
		if (!isset($turno[$fila]['bd']))
			$tpl->newBlock("no_bd");
		if (!isset($turno[$fila]['rep']))
			$tpl->newBlock("no_repostero");
		if (!isset($turno[$fila]['pic']))
			$tpl->newBlock("no_piconero");
		if (!isset($turno[$fila]['gel']))
			$tpl->newBlock("no_gelatinero");
		if (!isset($turno[$fila]['des']))
			$tpl->newBlock("no_despacho");
	}
	
	// Desplazamientos
	switch ($avio[$i]['cod_turno']) {
		case 1:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 1 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			// Crear bloque
			$tpl->newBlock("fd");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['fn']) || isset($turno[$fila]['bd']) || isset($turno[$fila]['rep']) || isset($turno[$fila]['pic']) || isset($turno[$fila]['gel']) || isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 2:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 2 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
		
			// Crear bloque
			$tpl->newBlock("fn");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['bd']) || isset($turno[$fila]['rep']) || isset($turno[$fila]['pic']) || isset($turno[$fila]['gel']) || isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 3:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 3 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			// Crear bloque
			$tpl->newBlock("bd");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']) || isset($turno[$fila]['fn']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['rep']) || isset($turno[$fila]['pic']) || isset($turno[$fila]['gel']) || isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 4:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 4 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			// Crear bloque
			$tpl->newBlock("repostero");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']) || isset($turno[$fila]['fn']) || isset($turno[$fila]['bd']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['pic']) || isset($turno[$fila]['gel']) || isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 8:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 8 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			// Crear bloque
			$tpl->newBlock("piconero");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']) || isset($turno[$fila]['fn']) || isset($turno[$fila]['bd']) || isset($turno[$fila]['rep']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['gel']) || isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 9:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 9 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			// Crear bloque
			$tpl->newBlock("gelatinero");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']) || isset($turno[$fila]['fn']) || isset($turno[$fila]['bd']) || isset($turno[$fila]['rep']) || isset($turno[$fila]['pic']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			if (isset($turno[$fila]['des']))
				$tpl->assign("right","form.consumo".($i+1).".select()");
			else
				$tpl->assign("right","form.entrada".($fila).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
		case 10:
			// Obtener promedio de consumo mensual
			/*$sql = "SELECT AVG(cantidad) FROM mov_inv_virtual WHERE num_cia = $_GET[num_cia] AND codmp = $mp AND cod_turno = 10 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = ejecutar_script($sql,$dsn);
			$avg = $temp[0]['avg'] > 0 ? ($mp != 1 ? round($temp[0]['avg'] * $pi,3) : round($temp[0]['avg'] * $pi / 44,3)) : 0;
			$tpl->assign("avg",$avg);*/
			
			$tpl->newBlock("despacho");
			
			// Obtener consumo de este turno
			$consumo = buscar($mov,$mp,$avio[$i]['cod_turno'],"t");
			$tpl->assign("consumo",$consumo != 0 ? number_format($mp == 1 ? $consumo / 44 : $consumo) : "");
			
			// Desplazamiento hacia la izquierda
			if (isset($turno[$fila]['fd']) || isset($turno[$fila]['fn']) || isset($turno[$fila]['bd']) || isset($turno[$fila]['rep']) || isset($turno[$fila]['pic']) || isset($turno[$fila]['gel']))
				$tpl->assign("left","form.consumo".($i-1).".select()");
			else
				$tpl->assign("left","form.entrada".($fila-1).".select()");
			// Desplazamiento hacia la derecha
			$tpl->assign("right","form.entrada".($fila < $numfilas ? $fila : 0).".select()");
			// Desplazamiento hacia abajo
			if (isset($turno[$fila+1]['des']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['des'].".select()");
			else if (isset($turno[$fila+1]['gel']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['gel'].".select()");
			else if (isset($turno[$fila+1]['pic']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['pic'].".select()");
			else if (isset($turno[$fila+1]['rep']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['rep'].".select()");
			else if (isset($turno[$fila+1]['bd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['bd'].".select()");
			else if (isset($turno[$fila+1]['fn']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fn'].".select()");
			else if (isset($turno[$fila+1]['fd']))
				$tpl->assign("down","form.consumo".$turno[$fila+1]['fd'].".select()");
			else
				$tpl->assign("down","form.entrada0.select()");
			// Desplazamiento hacia arriba
			if (isset($turno[$fila-1]['des']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['des'].".select()");
			else if (isset($turno[$fila-1]['gel']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['gel'].".select()");
			else if (isset($turno[$fila-1]['pic']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['pic'].".select()");
			else if (isset($turno[$fila-1]['rep']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['rep'].".select()");
			else if (isset($turno[$fila-1]['bd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['bd'].".select()");
			else if (isset($turno[$fila-1]['fn']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fn'].".select()");
			else if (isset($turno[$fila-1]['fd']))
				$tpl->assign("up","form.consumo".$turno[$fila-1]['fd'].".select()");
			else
				$tpl->assign("up","form.entrada".($fila-1).".select()");
		break;
	}
	$tpl->assign("i",$i);
	$tpl->assign("fila",$fila-1);
	
	$tpl->assign("codmp",$avio[$i]['codmp']);
	$tpl->assign("precio_unidad",$avio[$i]['precio_unidad']);
}

// Imprimir el resultado
$tpl->printToScreen();

// Desconectar de la base de datos
$db->desconectar();
?>