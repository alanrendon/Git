<?php
// LISTADO DE MOVIMIENTOS NO CONCILIADOS
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mpc_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['cias'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d/m/Y", mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	
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

// Segun las opciones, generar los scripts sql
$sql  = "SELECT num_cia,clabe_cuenta AS cuenta,nombre AS nombre_cia,fecha,tipo_mov AS tipo_mov,importe,cod_mov,folio,concepto";
$sql .= " FROM estado_cuenta JOIN catalogo_companias USING(num_cia)";
$sql .= " WHERE fecha_con IS NULL AND cuenta = 1";
if ($_GET['fecha'] != "")
	$sql .= " AND fecha <= '$_GET[fecha]'";
// Movimientos a seleccionar
switch ($_GET['mov']) {
	case "dep":
		$sql .= " AND tipo_mov = 'FALSE'";
	break;
	case "ret":
		$sql .= " AND tipo_mov = 'TRUE'";
	break;
}
// Rango de compañías según criterio seleccionado
switch ($_GET['cias']) {
	case "pan":
		$sql .= " AND (num_cia < 100 OR num_cia > 200 AND num_cia NOT IN (702,703,704))";
	break;
	case "ros":
		$sql .= " AND (num_cia > 100 AND num_cia < 200 OR num_cia IN (702,703,704))";
	break;
	case "cia":
		$sql .= " AND num_cia = $_GET[num_cia]";
	break;
}
$sql .= " ORDER BY num_cia,fecha,tipo_mov ASC";

$result = ejecutar_script($sql,$dsn);

if (!$result) {
	header("location: ./ban_mpc_con.php?codigo_error=1");
	die;
}

// Crear bloque para el listado
$tpl->newBlock("listado");

// Iniciar ciclo de recorrido de movimientos
$current_cia = NULL;
$total_depositos = 0;
$total_retiros = 0;
for ($i=0; $i<count($result); $i++) {
	if ($current_cia != $result[$i]['num_cia']) {
		if ($current_cia != NULL) {
			$total_depositos = 0;
			$total_retiros = 0;
		}
		
		$current_cia = $result[$i]['num_cia'];
				
		$tpl->newBlock("cia");
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$tpl->assign("nombre_cia",$result[$i]['nombre_cia']);
		$tpl->assign("cuenta",$result[$i]['cuenta']);
	}
	// Crear fila de movimiento
	$tpl->newBlock("fila");
	$tpl->assign("fecha",$result[$i]['fecha']);
	$tpl->assign("deposito",($result[$i]['tipo_mov'] == "f")?number_format($result[$i]['importe'],2,".",","):"&nbsp;");
	$tpl->assign("retiro",($result[$i]['tipo_mov'] == "t")?number_format($result[$i]['importe'],2,".",","):"&nbsp;");
	$tpl->assign("folio",($result[$i]['folio'] > 0)?$result[$i]['folio']:"&nbsp;");
	//$tpl->assign("cod_mov",$result[$i]['cod_mov']);
	//$descripcion = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$result[$i]['cod_mov'],$dsn);
	//$tpl->assign("descripcion",$descripcion[0]['descripcion']);
	$beneficiario = ejecutar_script("SELECT a_nombre FROM cheques WHERE num_cia = ".$result[$i]['num_cia']." AND folio = ".(($result[$i]['folio'] > 0)?$result[$i]['folio']:"0"),$dsn);
	$tpl->assign("beneficiario",($beneficiario)?$beneficiario[0]['a_nombre']:"&nbsp;");
	$tpl->assign("concepto",$result[$i]['concepto']);
	if ($result[$i]['tipo_mov'] == "f")
		$total_depositos += $result[$i]['importe'];
	else
		$total_retiros += $result[$i]['importe'];
	// Totales
	$tpl->assign("cia.total_depositos",number_format($total_depositos,2,".",","));
	$tpl->assign("cia.total_retiros",number_format($total_retiros,2,".",","));
}

$tpl->printToScreen();
?>