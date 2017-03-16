<?php
// LISTADO DE CONSUMO DE AVIO
// Tabla 'mov_inv_real ó mov_inv_virtual'
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1241); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha",date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

// -------------------------------- Mostrar listado ---------------------------------------------------------
// Variables
$num_cia = $_GET['num_cia'];			// Número de Compañía
$fecha = $_GET['fecha'];				// Fecha del listado
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha,$temp);
$fecha1 = "1/$temp[2]/$temp[3]";
$fecha2 = $_GET['fecha'];
$fecha_historico = date("d/m/Y", mktime(0,0,0,$temp[2],0,$temp[3]));
$fecha_fin = date("d/m/Y", mktime(0,0,0,$temp[2],$temp[1]-1,$temp[3]));

// Seleccionar tablas de inventarios y movimientos
if ($_GET['tabla'] == "virtual") {
	$tabla_inventario = /*"inventario_virtual"*/"historico_inventario";	// Tabla de donde se tomara el inventario
	$tabla_movimientos = "mov_inv_virtual";		// Tabla de donde se tomaran los movimientos
}
else if ($_GET['tabla'] == "real") {
	$tabla_inventario = /*"inventario_real"*/"historico_inventario";	// Tabla de donde se tomara el inventario
	$tabla_movimientos = "mov_inv_real";	// Tabla de donde se tomaran los movimientos
}

// Obtener listado de las materias primas
$sql = "SELECT codmp,nombre,existencia,precio_unidad FROM control_avio LEFT JOIN catalogo_mat_primas USING(codmp) LEFT JOIN $tabla_inventario USING(num_cia,codmp) WHERE num_cia=$num_cia AND fecha='$fecha_historico' GROUP BY codmp,nombre,num_orden,existencia,precio_unidad ORDER BY num_orden ASC";
$mp = $db->query($sql);

if (!$mp) {
	header("location: ./pan_avi_con.php?codigo_error=1");
	die;
}

if (isset($_GET['acumulado'])) {
	$tpl->newBlock("acumulado");
	$tpl->assign("num_cia",$num_cia);
	$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha,$fecha_des);
	$tpl->assign("dia",$fecha_des[1]);
	$tpl->assign("mes",mes_escrito($fecha_des[2]));
	$tpl->assign("anio",$fecha_des[3]);
	
	for ($i=0; $i<count($mp); $i++) {
		$codmp = $mp[$i]['codmp'];
		$consumo = 0;
		// Obtener entradas y salidas de avio despues de la fecha del listado
		$sql = "SELECT 
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha1' AND tipo_mov='FALSE') AS entradas,
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha1' AND tipo_mov='TRUE') AS salidas";
		$dif = $db->query($sql);
		// Obtener la existencia para la fecha en específico
		$existencia = $mp[$i]['existencia'];
		//$existencia = $existencia + $dif[0]['salidas'] - $dif[0]['entradas'];
		// Obtener las entradas
		$sql = "SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov='FALSE' AND descripcion NOT LIKE '%DIFERENCIA%'";
		$temp = $db->query($sql);
		$entrada = $temp[0]['sum'];
		// Asignar a la plantilla
		$tpl->newBlock("fila_acu");
		$tpl->assign("codmp",$codmp);
		$tpl->assign("nombre",$mp[$i]['nombre']);
		$tpl->assign("existencia",($existencia != 0)?number_format($existencia,2,".",","):"&nbsp;");
		$tpl->assign("entrada",($entrada != 0)?number_format($entrada,2,".",","):"&nbsp;");
		$total = $existencia+$entrada;
		$tpl->assign("total",($total != 0)?number_format($total,2,".",","):"&nbsp;");
		// Obtener las salidas
		$sql = "SELECT cod_turno,SUM(cantidad) AS salida FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov='TRUE' AND descripcion NOT LIKE '%DIFERENCIA%' GROUP BY cod_turno ORDER BY cod_turno";
		$salida = $db->query($sql);
		for ($j=0; $j<count($salida); $j++) {
			switch ($salida[$j]['cod_turno']) {
				case 1:  $turno = "fd";   break;
				case 2:  $turno = "fn";   break;
				case 3:  $turno = "bd";   break;
				case 4:  $turno = "rep";  break;
				case 8:  $turno = "pic";  break;
				case 9:  $turno = "gel";  break;
				case 10: $turno = "desp"; break;
				default: $turno = "";     break;
			}
			$tpl->assign($turno,($salida[$j]['salida'] > 0)?number_format($salida[$j]['salida'],2,".",","):"&nbsp;");
			$consumo += $salida[$j]['salida'];
		}
		$tpl->assign("consumo",($consumo > 0)?number_format($consumo,2,".",","):"&nbsp;");
		$final = $total - $consumo;
		$tpl->assign("final",($final > 0)?number_format($final,2,".",","):"&nbsp;");
	}
	$tpl->printToScreen();
}
else {
	$tpl->newBlock("listado");
	$tpl->assign("num_cia",$num_cia);
	$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha,$fecha_des);
	$tpl->assign("dia",$fecha_des[1]);
	$tpl->assign("mes",mes_escrito($fecha_des[2]));
	$tpl->assign("anio",$fecha_des[3]);
	
	//$inicio = time();
	for ($i=0; $i<count($mp); $i++) {
		$codmp = $mp[$i]['codmp'];
		$consumo = 0;
		// Obtener entradas y salidas de avio despues de la fecha del listado
		/*$sql = "SELECT 
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='FALSE') AS entradas,
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha>='$fecha' AND tipo_mov='TRUE') AS salidas";*/
		$sql = "SELECT 
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha BETWEEN '$fecha1' AND '$fecha_fin' AND tipo_mov='FALSE') AS entradas,
		(SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha BETWEEN '$fecha1' AND '$fecha_fin' AND tipo_mov='TRUE') AS salidas";
		$dif = $db->query($sql);
		// Obtener la existencia para la fecha en específico
		$existencia = $mp[$i]['existencia'] - $dif[0]['salidas'] + $dif[0]['entradas'];
		//$existencia = $existencia + $dif[0]['salidas'] - $dif[0]['entradas'];
		// Obtener las entradas
		$sql = "SELECT SUM(cantidad) FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha='$fecha' AND tipo_mov='FALSE'";
		$temp = $db->query($sql);
		$entrada = $temp[0]['sum'];
		// Asignar a la plantilla
		$tpl->newBlock("fila");
		$tpl->assign("codmp",$codmp);
		$tpl->assign("nombre",$mp[$i]['nombre']);
		$tpl->assign("existencia",($existencia != 0)?number_format($existencia,2,".",","):"&nbsp;");
		$tpl->assign("entrada",($entrada != 0)?number_format($entrada,2,".",","):"&nbsp;");
		$total = $existencia+$entrada;
		$tpl->assign("total",($total != 0)?number_format($total,2,".",","):"&nbsp;");
		// Obtener las salidas
		$sql = "SELECT cod_turno,cantidad AS salida FROM $tabla_movimientos WHERE num_cia=$num_cia AND codmp=$codmp AND fecha='$fecha' AND tipo_mov='TRUE' ORDER BY cod_turno";
		$salida = $db->query($sql);
		for ($j=0; $j<count($salida); $j++) {
			switch ($salida[$j]['cod_turno']) {
				case 1:  $turno = "fd";   break;
				case 2:  $turno = "fn";   break;
				case 3:  $turno = "bd";   break;
				case 4:  $turno = "rep";  break;
				case 8:  $turno = "pic";  break;
				case 9:  $turno = "gel";  break;
				case 10: $turno = "desp"; break;
				default: $turno = "";     break;
			}
			$tpl->assign($turno,($salida[$j]['salida'] > 0)?number_format($salida[$j]['salida'],2,".",","):"&nbsp;");
			$consumo += $salida[$j]['salida'];
		}
		$tpl->assign("consumo",($consumo > 0)?number_format($consumo,2,".",","):"&nbsp;");
		$manana = $total - $consumo;
		$tpl->assign("manana",($manana > 0)?number_format($manana,2,".",","):"&nbsp;");
	}
	//$fin = time();
	//echo "Tiempo de ejecución: ".date("i:s",$fin-$inicio);
	
	$tpl->printToScreen();
}
?>