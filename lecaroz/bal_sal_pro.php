<?php
// LISTADO DE SALDO DE PROVEEDORES
// Tabla ''
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ---------------------------------------------------------------<strong>-</strong>
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay registros";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_sal_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
	$tpl->assign("anio",date("Y",mktime(0,0,0,date("m")-1,1,date("Y"))));
	
	$tpl->printToScreen();
	die;
}

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
$fecha_historico = date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio']));
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha2,$fecha);

$numfilas_x_hoja = 30;

$tpl->newBlock("listado");

$numfilas = $numfilas_x_hoja;

$total_saldo = 0;
$total_saldo_prom = 0;
$total_pagos = 0;
$total_pagos_prom = 0;
$total_compras = 0;
$total_compras_prom = 0;
$total_inventario = 0;
$total_bancos = 0;
for ($i=0; $i<count($cia); $i++) {
	$num_cia = $cia[$i]['num_cia'];
	
	if ($numfilas >= $numfilas_x_hoja) {
		$tpl->newBlock("hoja");
		$tpl->assign("dia",$fecha[1]);
		$tpl->assign("mes",mes_escrito($fecha[2]));
		$tpl->assign("anio",$fecha[3]);
		
		$numfilas = 0;
	}
	
	$sql = "SELECT SUM(total),AVG(total) FROM pasivo_proveedores WHERE num_cia = $num_cia AND fecha <= '$fecha2' AND codgastos != 134";
	$saldo1 = ejecutar_script($sql,$dsn);
	$sql = "SELECT SUM(total),AVG(total) FROM facturas_pagadas WHERE num_cia = $num_cia AND fecha <= '$fecha2' AND codgastos != 134 AND fecha_cheque > '$fecha2'";
	$saldo2 = ejecutar_script($sql,$dsn);
	$saldo = $saldo1[0]['sum'] + $saldo2[0]['sum'];
	$saldo_pro = ($saldo1[0]['avg'] + $saldo2[0]['avg']) / 2;
	
	$sql = "SELECT SUM(total),AVG(total) FROM facturas_pagadas WHERE num_cia = $num_cia AND fecha_cheque BETWEEN '$fecha1' AND '$fecha2' AND codgastos != 134";
	$pagos = ejecutar_script($sql,$dsn);
	
	$sql = "SELECT SUM(total),AVG(total) FROM facturas WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos != 134";
	$compras = ejecutar_script($sql,$dsn);
	
	$sql = "SELECT SUM(existencia*precio_unidad) FROM historico_inventario WHERE num_cia = $num_cia AND fecha = '$fecha2'";
	$inventario = ejecutar_script($sql,$dsn);
	
	// Saldo al dia especificado
	// Entradas
	$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha > '$fecha2' AND tipo_mov = 'FALSE'";
	$entradas = ejecutar_script($sql,$dsn);
	// Salidas
	$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha > '$fecha2' AND tipo_mov = 'TRUE'";
	$salidas = ejecutar_script($sql,$dsn);
	// Saldo actual
	$sql = "SELECT saldo_libros FROM saldos WHERE num_cia = $num_cia";
	$saldo_actual = ejecutar_script($sql,$dsn);
	// Saldo al mes
	$saldo_bancos = $saldo_actual[0]['saldo_libros'] - $entradas[0]['sum'] + $salidas[0]['sum'];
	
	$tpl->newBlock("fila");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	$tpl->assign("saldo",($saldo != "")?number_format($saldo,2,".",","):"&nbsp;");
	$tpl->assign("prom_saldo",($saldo_pro != "")?number_format($saldo_pro,2,".",","):"&nbsp;");
	$tpl->assign("pagos",($pagos[0]['sum'] != "")?number_format($pagos[0]['sum'],2,".",","):"&nbsp;");
	$tpl->assign("prom_pagos",($pagos[0]['avg'] != "")?number_format($pagos[0]['avg'],2,".",","):"&nbsp;");
	$tpl->assign("compras",($compras[0]['sum'] != "")?number_format($compras[0]['sum'],2,".",","):"&nbsp;");
	$tpl->assign("prom_compras",($compras[0]['avg'] != "")?number_format($compras[0]['avg'],2,".",","):"&nbsp;");
	$tpl->assign("inventario",($inventario[0]['sum'] != "")?number_format($inventario[0]['sum'],2,".",","):"&nbsp;");
	$tpl->assign("bancos",($saldo_bancos != "")?number_format($saldo_bancos,2,".",","):"&nbsp;");
	
	$numfilas++;
	
	$total_saldo += $saldo;
	$total_saldo_prom += $saldo_pro;
	$total_pagos += $pagos[0]['sum'];
	$total_pagos_prom += $pagos[0]['avg'];
	$total_compras += $compras[0]['sum'];
	$total_compras_prom += $compras[0]['avg'];
	$total_inventario += $inventario[0]['sum'];
	$total_bancos += $saldo_bancos;
}
$tpl->gotoBlock("listado");
$tpl->assign("dia",$fecha[1]);
$tpl->assign("mes",mes_escrito($fecha[2]));
$tpl->assign("anio",$fecha[3]);
$tpl->assign("saldo",number_format($total_saldo,2,".",","));
$tpl->assign("prom_saldo",number_format($total_saldo_prom,2,".",","));
$tpl->assign("pagos",number_format($total_pagos,2,".",","));
$tpl->assign("prom_pagos",number_format($total_pagos_prom,2,".",","));
$tpl->assign("compras",number_format($total_compras,2,".",","));
$tpl->assign("prom_compras",number_format($total_compras_prom,2,".",","));
$tpl->assign("inventario",number_format($total_inventario,2,".",","));
$tpl->assign("bancos",number_format($total_bancos,2,".",","));

$tpl->assign("diferencia",number_format($total_saldo-$total_inventario,2,".",","));
$tpl->assign("menos",number_format($total_saldo-$total_inventario,2,".",","));
$tpl->assign("igual",number_format($total_bancos-($total_saldo-$total_inventario),2,".",","));

$tpl->printToScreen();
?>