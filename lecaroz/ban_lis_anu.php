<?php
// LISTADO DE BANCOS ANUAL
// Tablas varias ''
// Menu ''

//define ('IDSCREEN',1222); // ID de pantalla

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

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_lis_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	
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
	die();
}

if ($_GET['tipo'] == "cod_mov") {
	$fecha_ini = "1/1/$_GET[anio]";
	$fecha_fin = $_GET['anio'] == date("Y") ? date("d/m/Y") : "31/12/$_GET[anio]";
	$meses = $_GET['anio'] == date("Y") ? (int)date("n",mktime(0,0,0,date("n")-1,1,date("Y"))) : 12;
	
	// Obtener todos los códigos de movimiento habidos durante el año
	$sql = "SELECT cod_mov,descripcion FROM estado_cuenta JOIN catalogo_mov_bancos USING(cod_mov) WHERE num_cia = $_GET[num_cia] and fecha BETWEEN '$fecha_ini' and '$fecha_fin' AND cod_mov NOT IN (1,5,16) GROUP BY cod_mov,descripcion ORDER BY cod_mov";
	$cod_mov = ejecutar_script($sql,$dsn);
	
	if (!$cod_mov) {
		header("location: ./ban_lis_anu.php?codigo_error=1");
		die;
	}
	
	$num_mov = count($cod_mov);
	
	$tpl->newBlock("cod_mov");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	$tpl->assign("anio",$_GET['anio']);
	
	// Mostrar columna de códigos
	for ($i=0; $i<$num_mov; $i++) {
		$tpl->newBlock("codigo");
		$tpl->assign("cod_mov",$cod_mov[$i]['cod_mov']);
		$tpl->assign("descripcion",$cod_mov[$i]['descripcion']);
	}
	
	$total_cod = array();
	for ($i=0; $i<$num_mov; $i++)
		$total_cod[$i] = 0;
	// Recorrer todos los meses
	for ($m=1; $m<=$meses; $m++) {
		$fecha1 = "1/$m/$_GET[anio]";
		$fecha2 = date("d/m/Y",mktime(0,0,0,$m+1,0,$_GET['anio']));
		
		$tpl->newBlock("mes_mov");
		$tpl->assign("mes",mes_escrito($m));
		
		$total_mes = 0;
		// Buscar los importes por código para el mes en ciclo
		for ($c=0; $c<$num_mov; $c++) {
			$sql = "SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = {$cod_mov[$c]['cod_mov']}";
			$importe = ejecutar_script($sql,$dsn);
			
			$tpl->newBlock("fila_mov");
			$tpl->assign("importe",$importe[0]['sum'] > 0 ? number_format($importe[0]['sum'],2,".",",") : "&nbsp;");
			
			$total_mes += $importe[0]['sum'];
			$total_cod[$c] += $importe[0]['sum'];
		}
		// Mostrar total por mes
		$tpl->assign("mes_mov.total",number_format($total_mes,2,".",","));
	}
	
	// Mostrar totales por código
	$tpl->newBlock("mes_mov");
	$tpl->assign("mes","Total");
	for ($i=0; $i<$num_mov; $i++) {
		$tpl->newBlock("fila_mov");
		$tpl->assign("importe","<strong>".number_format($total_cod[$i],2,".",",")."</strong>");
	}
	$tpl->assign("mes_mov.total","&nbsp;");
	
	$tpl->printToScreen();
	die;
}
else if ($_GET['tipo'] == "cia") {
	$fecha_ini = "1/1/$_GET[anio]";
	$fecha_fin = $_GET['anio'] == date("Y") ? date("d/m/Y") : "31/12/$_GET[anio]";
	$meses = $_GET['anio'] == date("Y") ? (int)date("n",mktime(0,0,0,date("n")-1,1,date("Y"))) : 12;
	
	$numfilas = 30;
	
	// Obtener todas la compañías que tuvieron movimientos durante el año
	$sql = "SELECT num_cia,nombre_corto FROM estado_cuenta JOIN catalogo_companias USING(num_cia) WHERE fecha BETWEEN '$fecha_ini' AND '$fecha_fin' AND cod_mov IN (1,16) GROUP BY num_cia,nombre_corto ORDER BY num_cia";
	$cia = ejecutar_script($sql,$dsn);
	
	if (!$cia) {
		header("location: ./ban_lis_anu.php?codigo_error=1");
		die;
	}
	
	$num_cias = count($cia);
	
	$tpl->newBlock("cias");
	$tpl->assign("anio",$_GET['anio']);
	
	// Mostrar columna de códigos
	for ($i=0; $i<$num_cias; $i++) {
		$tpl->newBlock("cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
	
	$total_cia = array();
	for ($i=0; $i<$num_cias; $i++)
		$total_cia[$i] = 0;
	
	// Recorrer todos los meses
	for ($m=1; $m<=$meses; $m++) {
		$fecha1 = "1/$m/$_GET[anio]";
		$fecha2 = date("d/m/Y",mktime(0,0,0,$m+1,0,$_GET['anio']));
		
		$tpl->newBlock("mes_cia");
		$tpl->assign("mes",mes_escrito($m));
		
		$total_mes = 0;
		// Buscar los importes por código para el mes en ciclo
		for ($c=0; $c<$num_cias; $c++) {
			$sql = "SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1,16)";
			$importe = ejecutar_script($sql,$dsn);
			
			$tpl->newBlock("fila_cia");
			$tpl->assign("importe",$importe[0]['sum'] > 0 ? number_format($importe[0]['sum'],2,".",",") : "&nbsp;");
			
			$total_cia[$c] += $importe[0]['sum'];
		}
	}
	
	// Mostrar totales por código
	$tpl->newBlock("mes_cia");
	$tpl->assign("mes","Total");
	for ($i=0; $i<$num_cias; $i++) {
		$tpl->newBlock("fila_cia");
		$tpl->assign("importe","<strong>".number_format($total_cia[$i],2,".",",")."</strong>");
	}
	
	$tpl->printToScreen();
	die;
}

?>