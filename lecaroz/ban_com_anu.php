<?php
// COMPARATIVO DE FIN DE MES
// Tabla 'catalogo_gastos_caja'
// Menu 'Balance->Catálogos Especiales'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "La Compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_com_anu.tpl");
$tpl->prepare();

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("anio", date("Y"));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("listado");
$tpl->assign("anio", $_GET['anio']);

// *** PANADERIAS ***
$sql = "SELECT num_cia, nombre_corto, saldo_libros FROM saldos LEFT JOIN catalogo_companias USING (num_cia) WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950" : " num_cia < 100") . " AND cuenta = $_GET[cuenta] ORDER BY num_cia";
$result = $db->query($sql);

$mes = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0,0,0,date("n")-1,1,$_GET['anio']));
$dia = date("d",mktime(0,0,0,$mes+1,0,$_GET['anio']));
$fecha1 = "1/$mes/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$mes+1,0,$_GET['anio']));

$tpl->newBlock("bloque");
$tpl->assign("titulo", $_SESSION['iduser'] != 28 ? "PANADERIAS" : "ZAPATERIAS");
$tpl->assign("dia", $dia);
$tpl->assign("mes", mes_escrito($mes));

$total_saldo_ant = 0;
$total_saldo_act = 0;
$total_saldo_pro_ant = 0;
$total_saldo_pro_act = 0;
$total_diferencia = 0;
$total_no_incluidos = 0;
$total_result = 0;

if ($result)
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		
		// Obtener saldo al primer dia del año
		$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = {$result[$i]['num_cia']} AND fecha >= '01/01/$_GET[anio]' AND cuenta = $_GET[cuenta] GROUP BY tipo_mov ORDER BY tipo_mov";
		$mov = $db->query($sql);
		$saldo_ant = $result[$i]['saldo_libros'];
		for ($j = 0; $j < count($mov); $j++)
			$saldo_ant += $mov[$j]['tipo_mov'] == "f" ? -$mov[$j]['sum'] : $mov[$j]['sum'];
		
		// Calcular saldo actual
		if ($_GET['anio'] < date("Y")) {
			$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '01/01/$_GET[anio]' AND '31/12/$_GET[anio]' AND cuenta = $_GET[cuenta] GROUP BY tipo_mov ORDER BY tipo_mov";
			$mov = $db->query($sql);
			$saldo_act = $saldo_ant;
			for ($j = 0; $j < count($mov); $j++)
				$saldo_act += $mov[$j]['tipo_mov'] == "f" ? $mov[$j]['sum'] : -$mov[$j]['sum'];
		}
		else {
			$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = {$result[$i]['num_cia']} AND fecha >= '" . date("1/m/Y") . "' AND cuenta = $_GET[cuenta] GROUP BY tipo_mov ORDER BY tipo_mov";
			$mov = $db->query($sql);
			$saldo_act = $result[$i]['saldo_libros'];
			for ($j = 0; $j < count($mov); $j++)
				$saldo_act += $mov[$j]['tipo_mov'] == "f" ? -$mov[$j]['sum'] : $mov[$j]['sum'];
		}
		
		// Saldo inicial de proveedores
		$sql = "SELECT sum(total) FROM facturas_pagadas WHERE num_cia = {$result[$i]['num_cia']} AND fecha <= '31/12/" . ($_GET['anio'] - 1) . "' AND fecha_cheque >= '1/1/$_GET[anio]'";
		$saldo_pro_ant = $db->query($sql);
		
		// Saldo actual de proveedores
		if ($_GET['anio'] < date("Y")) {
			$sql = "SELECT sum(total) FROM facturas_pagadas WHERE num_cia = {$result[$i]['num_cia']} AND fecha <= '31/12/$_GET[anio]' AND fecha_cheque >= '1/1/" . ($_GET['anio'] + 1) . "'";
			$temp1= $db->query($sql);
			$sql = "SELECT sum(total) FROM pasivo_proveedores WHERE num_cia = {$result[$i]['num_cia']} AND fecha <= '31/12/$_GET[anio]'";
			$temp2 = $db->query($sql);
			$saldo_pro_act = $temp1[0]['sum'] + $temp2[0]['sum'];
		}
		else {
			$sql = "SELECT sum(total) FROM facturas_pagadas WHERE num_cia = {$result[$i]['num_cia']} AND fecha < '" . date("1/m/Y") . "' AND fecha_cheque >= '" . date("1/m/Y") . "'";
			$temp1 = $db->query($sql);
			$sql = "SELECT sum(total) FROM pasivo_proveedores WHERE num_cia = {$result[$i]['num_cia']} AND fecha < '" . date("1/m/Y") . "'";
			$temp2 = $db->query($sql);
			$saldo_pro_act = $temp1[0]['sum'] + $temp2[0]['sum'];
		}
		
		// General - Balance
		// Obtener Gastos de caja (ingresos y egresos) del mes
		$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
		$ingresos = $db->query($sql);
		$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
		$egresos = $db->query($sql);
		$total_gastos_caja = $egresos[0]['sum'] - $ingresos[0]['sum'];
		// Obtener depositos
		$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = $_GET[cuenta] AND cuenta = $_GET[cuenta]";
		$depositos = $db->query($sql);
		// Otros depositos
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = {$result[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$otros_dep = $db->query($sql);
		// General
		$general = $otros_dep[0]['sum'] + $total_gastos_caja;
		// Utilidad Neta
		$sql = "SELECT utilidad_neta FROM balances_pan WHERE num_cia = {$result[$i]['num_cia']} AND mes = $mes AND anio = $_GET[anio]";
		$temp = $db->query($sql);
		$utilidad_neta = $temp ? $temp[0]['utilidad_neta'] : 0;
		
		// Diferencia
		$diferencia = $general - $utilidad_neta;
		
		// Gastos no incluidos
		$sql = "SELECT sum(importe) FROM cheques LEFT JOIN catalogo_gastos USING (codgastos) WHERE fecha BETWEEN '01/01/$_GET[anio]' AND '$fecha2' AND codigo_edo_resultados = 0 AND codgastos NOT IN (33,134,141,59) AND fecha_cancelacion IS NULL AND num_cia = {$result[$i]['num_cia']}";
		$no_incluidos = $db->query($sql);
	
		$tpl->assign("saldo_ini", $saldo_ant != 0 ? "<font color=\"#" . ($saldo_ant > 0 ? "0000FF" : "FF0000") . "\">" . number_format($saldo_ant, 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("saldo_dia", $saldo_act != 0 ? "<font color=\"#" . ($saldo_act > 0 ? "0000FF" : "FF0000") . "\">" . number_format($saldo_act, 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("saldo_pro_ini", $saldo_pro_ant[0]['sum'] != 0 ? "<font color=\"#" . ($saldo_pro_ant[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($saldo_pro_ant[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("saldo_pro", $saldo_pro_act != 0 ? "<font color=\"#" . ($saldo_pro_act > 0 ? "0000FF" : "FF0000") . "\">" . number_format($saldo_pro_act, 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("gen_bal", $diferencia != 0 ? "<font color=\"#" . ($diferencia > 0 ? "0000FF" : "FF0000") . "\">" . number_format($diferencia, 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("no_incluidos", $no_incluidos[0]['sum'] != 0 ? "<font color=\"#" . ($no_incluidos[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($no_incluidos[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
		
		$res = ($saldo_act - $saldo_ant) - ($saldo_pro_act - $saldo_pro_ant[0]['sum']) + $diferencia + $no_incluidos[0]['sum']/**/;
		$tpl->assign("result", $res != 0 ? "<font color=\"#" . ($res > 0 ? "0000FF" : "FF0000") . "\">" . number_format($res, 2, ".", ",") . "</font>" : "&nbsp;");
		
		$total_saldo_ant += $saldo_ant;
		$total_saldo_act += $saldo_act;
		$total_saldo_pro_ant += $saldo_pro_ant[0]['sum'];
		$total_saldo_pro_act += $saldo_pro_act;
		$total_diferencia += $diferencia;
		$total_no_incluidos += $no_incluidos[0]['sum'];
		$total_result += $res;
	}

$tpl->gotoBlock("bloque");
$tpl->assign("saldo_ini", $total_saldo_ant != 0 ? number_format($total_saldo_ant, 2, ".", ",") : "&nbsp;");
$tpl->assign("saldo_dia", $total_saldo_act != 0 ? number_format($total_saldo_act, 2, ".", ",") : "&nbsp;");
$tpl->assign("saldo_pro_ini", $total_saldo_pro_ant != 0 ? number_format($total_saldo_pro_ant, 2, ".", ",") : "&nbsp;");
$tpl->assign("saldo_pro", $total_saldo_pro_act != 0 ? number_format($total_saldo_pro_act, 2, ".", ",") : "&nbsp;");
$tpl->assign("gen_bal", $total_diferencia != 0 ? number_format($total_diferencia, 2, ".", ",") : "&nbsp;");
$tpl->assign("no_incluidos", $total_no_incluidos != 0 ? number_format($total_no_incluidos, 2, ".", ",") : "&nbsp;");
$tpl->assign("result", $total_result != 0 ? number_format($total_result, 2, ".", ",") : "&nbsp;");

$tpl->printToScreen();
$db->desconectar();
?>