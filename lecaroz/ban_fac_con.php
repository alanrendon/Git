<?php
// CONSULTA DE FACTURAS DE PAN
// Tablas 'auth'
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
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_fac_con.tpl" );
$tpl->prepare();

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));
	
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
	die;
}

$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

if ($_GET['num_cia'] > 0) {
	$sql = "SELECT fecha,SUM(importe) AS importe FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./ban_fac_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("cia");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
	$tpl->assign("mes",mes_escrito($_GET['mes'],TRUE));
	$tpl->assign("anio",$_GET['anio']);
	
	$ventas = 0;
	$fact = 0;
	$pend = 0;
	for ($i=0; $i<count($result); $i++) {
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[$i]['fecha'],$fecha);
		$facturas = ejecutar_script("SELECT SUM(importe_total) FROM facturas_clientes WHERE num_cia = $_GET[num_cia] AND fecha = '{$result[$i]['fecha']}'",$dsn);
		
		$tpl->newBlock("dia");
		$tpl->assign("dia",$fecha[1]);
		$tpl->assign("ventas",number_format($result[$i]['importe'],2,".",","));
		$tpl->assign("facturas",$facturas[0]['sum'] > 0 ? number_format($facturas[0]['sum'],2,".",",") : "&nbsp;");
		$tpl->assign("pendientes",number_format($result[$i]['importe']-$facturas[0]['sum'],2,".",","));
		
		$ventas += $result[$i]['importe'];
		$fact += $facturas[0]['sum'];
		$pend += $result[$i]['importe'] - $facturas[0]['sum'];
	}
	$tpl->assign("cia.ventas",number_format($ventas,2,".",","));
	$tpl->assign("cia.facturas",number_format($fact,2,".",","));
	$tpl->assign("cia.pendientes",number_format($pend,2,".",","));
	
	$tpl->printToScreen();
	die;
}
else {
	$sql = "SELECT num_cia,SUM(importe) AS importe FROM estado_cuenta WHERE num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./ban_fac_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("all");
	$tpl->assign("mes",mes_escrito($_GET['mes'],TRUE));
	$tpl->assign("anio",$_GET['anio']);
	
	$ventas = 0;
	$fact = 0;
	$pend = 0;
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[$i]['num_cia']}",$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
		
		$facturas = ejecutar_script("SELECT SUM(importe_total) FROM facturas_clientes WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'",$dsn);
		
		$tpl->assign("ventas",number_format($result[$i]['importe'],2,".",","));
		$tpl->assign("facturas",$facturas[0]['sum'] > 0 ? number_format($facturas[0]['sum'],2,".",",") : "&nbsp;");
		$tpl->assign("pendientes",number_format($result[$i]['importe']-$facturas[0]['sum'],2,".",","));
		
		$ventas += $result[$i]['importe'];
		$fact += $facturas[0]['sum'];
		$pend += $result[$i]['importe'] - $facturas[0]['sum'];
	}
	/*$tpl->assign("cia.ventas",number_format($ventas,2,".",","));
	$tpl->assign("cia.facturas",number_format($fact,2,".",","));
	$tpl->assign("cia.pendientes",number_format($pend,2,".",","));*/
	
	$tpl->printToScreen();
	die;
}

?>