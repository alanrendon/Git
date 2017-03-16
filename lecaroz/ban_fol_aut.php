<?php
// CANCELACION DE CHEQUES
// Tabla 'cheques,estado_cuenta,pasivo_proveedores,facturas_pagadas'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe registro de cheque o ya ha sido cancelado";
$descripcion_error[2] = "No se puede cancelar el cheque debido a que ya ha sido conciliado";

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
$tpl->assignInclude("body","./plantillas/ban/ban_fol_aut.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener fecha inicial y final
$fecha1 = date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")));
$fecha2 = date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")));

// Obtener Compañías
$sql = "SELECT num_cia,nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE clabe_cuenta != '' AND num_cia NOT IN (999,619) AND num_cia NOT BETWEEN 201 AND 300 ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

$numfilas_x_hoja = 59;
$numfilas = 59;

for ($i=0; $i<count($cia); $i++) {
	if ($cia[$i]['clabe_cuenta'] > 0) {
		if ($numfilas >= $numfilas_x_hoja) {
			$tpl->newBlock("hoja");
			
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito(date("n")));
			$tpl->assign("anio",date("Y"));
			
			$numfilas = 0;
		}
		
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre']." (".$cia[$i]['nombre_corto'].")");
		$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
		
		// Obtener el número de cheques que se imprimieron el mes pasado para esta compañia
		$sql = "SELECT count(id) FROM cheques WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha BETWEEN '$fecha1' AND '$fecha2' AND imp = 'TRUE' AND cuenta = 1";
		$num_cheques = ejecutar_script($sql,$dsn);
		// Obtener último folio generado
		$sql = "SELECT folio FROM cheques WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha IS NOT NULL AND cuenta = 1 ORDER BY fecha DESC,folio DESC LIMIT 1";
		$temp = ejecutar_script($sql,$dsn);
		$ultimo_folio = $temp ? $temp[0]['folio'] : 1;
		
		if ($num_cheques[0]['count'] <= 10)
			$folio = $ultimo_folio + 50;
		else
			$folio = $ultimo_folio + ceil($num_cheques[0]['count'] * 1.20);/*$folio = $ultimo_folio + 500*/;
		
		$tpl->assign("folio",$folio);
		
		$numfilas++;
	}
}

$tpl->printToScreen();
?>