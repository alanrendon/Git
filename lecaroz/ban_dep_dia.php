<?php
// LISTADO DE DEPÓSITOS POR DÍA
// Tablas 'estado_cuenta'
// Menu 'No definido'

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

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_dep_dia.tpl");
$tpl->prepare();

// Prepara fecha
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['efe']['fecha1'],$temp);
$fecha = "$_GET[dia]/$temp[2]/$temp[3]";

// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
if (($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] > 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200) || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 702)
	$sql = "SELECT efectivo FROM total_companias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='$fecha'";
else if ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 900 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 950)
	$sql = "SELECT efectivo FROM total_zapaterias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='$fecha'";
else {
	$sql = "SELECT efectivo FROM total_panaderias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='$fecha'";
}
$efectivo = ejecutar_script($sql,$dsn);

// Obtener depósitos
$sql = "SELECT estado_cuenta.id AS id,concepto,importe,fecha_con,fecha,cod_mov,descripcion,cuenta FROM estado_cuenta LEFT JOIN catalogo_mov_santander USING(cod_mov) WHERE num_cia".($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200 ? " IN ({$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}, {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} + 100)" : "={$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}")." AND fecha='$fecha' AND cod_mov IN (1,16,44,99) GROUP BY estado_cuenta.id,concepto,importe,fecha_con,fecha,cod_mov,descripcion,cuenta ORDER BY importe DESC";
$deposito = ejecutar_script($sql,$dsn);

// Trazar encabezado de pantalla
$tpl->assign("num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
$tpl->assign("nombre_cia",$_SESSION['efe']['nombre_cia'.$_SESSION['efe']['next']]);
$tpl->assign("efectivo",number_format($efectivo[0]['efectivo'],2,".",","));

// Trazar depósitos
if ($deposito) {
	$tpl->newBlock("depositos");
	$total_depositos = 0;
	for ($i=0; $i<count($deposito); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$deposito[$i]['id']);
		$tpl->assign("cuenta", $deposito[$i]['cuenta'] == 1 ? "<font color='#0000CC'>BANORTE</font>" : "<font color='#CC0000'>SANTANDER</font>");
		$tpl->assign("concepto",$deposito[$i]['concepto']);
		$tpl->assign("importe",number_format($deposito[$i]['importe'],2,".",","));
		$tpl->assign("fecha_con",($deposito[$i]['fecha_con'] != "")?$deposito[$i]['fecha_con']:"&nbsp;");
		$tpl->assign("cod_mov",$deposito[$i]['cod_mov']);
		$tpl->assign("descripcion",$deposito[$i]['descripcion']);
		$tpl->assign("fecha",$deposito[$i]['fecha']);
		
		$total_depositos += $deposito[$i]['importe'];
	}
	$tpl->assign("depositos.total",number_format($total_depositos,2,".",","));
	$tpl->assign("depositos.diferencia",number_format($efectivo[0]['efectivo']-$total_depositos,2,".",","));
}
else {
	$tpl->newBlock("no_depositos");
}

$tpl->printToScreen();
?>