<?php
// DIFERENCIAS DE SALDOS
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

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
$tpl->assignInclude("body","./plantillas/ban/ban_dif_sal.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
// Obtener ultimo
$sql = "SELECT * FROM saldos WHERE cuenta = 1 ORDER BY num_cia ASC";
$saldo = $db->query($sql);

// Obtener movimientos pendientes de conciliar
$sql = "SELECT num_cia, tipo_mov, sum(importe) FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 GROUP BY num_cia,tipo_mov ORDER BY num_cia,tipo_mov";
$mov_pen = $db->query($sql);

function buscar_mov($array, $num_cia, $tipo_mov) {
	if ($array === FALSE)
		return 0;
	
	for ($i = 0; $i < count($array); $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['tipo_mov'] == $tipo_mov)
			return number_format($array[$i]['sum'], 2, ".", "");
	
	return 0;
}

$tpl->assign("dia",date("d"));
$tpl->assign("anio",date("Y"));
switch (date("m")) {
	case 1: $mes = "Enero"; break;
	case 2: $mes = "Febrero"; break;
	case 3: $mes = "Marzo"; break;
	case 4: $mes = "Abril"; break;
	case 5: $mes = "Mayo"; break;
	case 6: $mes = "Junio"; break;
	case 7: $mes = "Julio"; break;
	case 8: $mes = "Agosto"; break;
	case 9: $mes = "Septiembre"; break;
	case 10: $mes = "Octubre"; break;
	case 11: $mes = "Noviembre"; break;
	case 12: $mes = "Diciembre"; break;
}
$tpl->assign("mes",$mes);

$num_cia = NULL;
$total = 0;
for ($i=0; $i<count($saldo); $i++) {
	$temp = $db->query("SELECT saldo FROM saldo_banorte WHERE num_cia = {$saldo[$i]['num_cia']}");
	$saldo_archivo = ($temp)?$temp[0]['saldo']:"";
	if (number_format(number_format($saldo[$i]['saldo_bancos'],2,".","") + buscar_mov($mov_pen, $saldo[$i]['num_cia'], "f") - buscar_mov($mov_pen, $saldo[$i]['num_cia'], "t") - number_format($saldo_archivo,2,".",""), 2, ".", "") != 0) {
		$tpl->newBlock("fila");
		$cia = $db->query("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia = ".$saldo[$i]['num_cia'] );
		$tpl->assign("num_cia",$saldo[$i]['num_cia']);
		
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",date("m"));
		$tpl->assign("anio",date("Y"));
		
		$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
		$tpl->assign("nombre",$cia[0]['nombre']);
		$tpl->assign("saldo_con",number_format($saldo[$i]['saldo_bancos'],2,".",","));
		$tpl->assign("saldo_cap",($saldo_archivo != "")?number_format($saldo_archivo,2,".",","):"&nbsp;");
		$tpl->assign("diferencia",($saldo_archivo != "")?number_format($saldo[$i]['saldo_bancos']-$saldo_archivo,2,".",","):"(NO SE CAPTURO SALDO)");
		
		$total += $saldo[$i]['saldo_bancos']-$saldo_archivo;
	}
}
$tpl->assign("_ROOT.total",number_format($total,2,".",","));

$tpl->printToScreen();
?>