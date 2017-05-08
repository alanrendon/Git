<?php
// DIFERENCIAS DE SALDOS
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, 'autocommit=yes');

$users = array(28, 29, 30, 31);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

function buscar_mov($array, $num_cia, $tipo_mov) {
	if ($array === FALSE)
		return 0;
	
	for ($i = 0; $i < count($array); $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['tipo_mov'] == $tipo_mov)
			return number_format($array[$i]['sum'], 2, ".", "");
	
	return 0;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dif_sal_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['cuenta'])) {
	$tpl->newBlock("datos");
	$tpl->printToScreen();
	die;
}

$cuenta = $_GET['cuenta'];
$dia = date("w");
switch ($dia) {
	case 1: $dia_semana = "LUNES"; break;
	case 2: $dia_semana = "MARTES"; break;
	case 3: $dia_semana = "MIERCOLES"; break;
	case 4: $dia_semana = "JUEVES"; break;
	case 5: $dia_semana = "VIERNES"; break;
	case 6: $dia_semana = "SABADO"; break;
	case 0: $dia_semana = "DOMINGO"; break;
	default: $dia_semana = "";
}
$dia = date("d");
$mes = date("n");
$anio = date("Y");

if ($cuenta > 0) {
	$tabla_saldo = $cuenta == 1 ? "saldo_banorte" : "saldo_santander";
	$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	$tabla_movs = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$banco = $cuenta == 1 ? "BANORTE" : "SANTANDER";
	
	$sql = "SELECT num_cia, nombre_corto, $clabe_cuenta, saldo_bancos, saldo FROM saldos LEFT JOIN $tabla_saldo USING (num_cia) LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " WHERE cuenta = $cuenta" . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? " AND num_cia BETWEEN 900 AND 998" : " AND num_cia BETWEEN 1 AND 899") : '') . ($cuenta == 1 ? ' AND num_cia NOT IN (52, 631, 632, 633, 634, 635, 636, 637, 853)' : '') . " ORDER BY num_cia";
	$result = $db->query($sql);
	
	$tpl->newBlock("dif");
	$tpl->assign("banco", $banco);
	$tpl->assign("fecha", "$dia_semana $dia DE " . mes_escrito($mes, TRUE) . " DEL $anio, " . date("h:ia"));
	
	if ($result) {
		$cont = 0;
		
		// [07-Ene-2009] Borrar tabla de ultima diferencia de saldos
		$sql = 'DELETE FROM dif_saldos';
		$db->query($sql);
		
		$sql = "SELECT num_cia, tipo_mov, sum(importe) FROM $tabla_movs WHERE fecha_con IS NULL" . ($_SESSION['iduser'] != 1 ? ' AND num_cia BETWEEN ' . ($_SESSION['iduser'] < 28 ? "1 AND 899" : "900 AND 998") : '') . " GROUP BY num_cia, tipo_mov ORDER BY num_cia, tipo_mov";
		$mov_pen = $db->query($sql);
		
		$sql = '';
		$total = 0;
		foreach ($result as $saldo) {
			if (round($saldo['saldo_bancos'] + buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't') - $saldo['saldo'], 2) != 0 || trim($saldo['saldo']) === '') {
				$pendientes = buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't');
				$saldo_final = $saldo['saldo_bancos'] + $pendientes;
				
				$tpl->newBlock("fila");
				$tpl->assign("dia", $dia);
				$tpl->assign("mes", $mes);
				$tpl->assign("anio", $anio);
				$tpl->assign("num_cia", $saldo['num_cia']);
				$tpl->assign("cuenta", $cuenta);
				$tpl->assign("clabe_cuenta", $saldo[$clabe_cuenta]);
				$tpl->assign("nombre", $saldo['nombre_corto']);
				$tpl->assign("saldo_con", round($saldo['saldo_bancos'], 2) != 0 ? number_format($saldo['saldo_bancos'], 2, ".", ",") : "&nbsp;");
				$tpl->assign("pendientes", round($pendientes) != 0 ? number_format($pendientes, 2, ".", ",") : "&nbsp;");
				$tpl->assign("saldo_final", round($saldo_final, 2) != 0 ? number_format($saldo_final, 2, ".", ",") : "&nbsp;");
				$tpl->assign("saldo_cap", round($saldo['saldo'], 2) != 0 ? number_format($saldo['saldo'], 2, ".", ",") : "&nbsp;");
				$tpl->assign("diferencia", round($saldo['saldo'], 2) != 0 ? number_format($saldo_final - $saldo['saldo'], 2, ".", ",") : "(NO SE CAPTURO SALDO)");
				
				$sql .= "UPDATE saldos SET tsdif = now() WHERE num_cia = $saldo[num_cia] AND cuenta = $cuenta AND tsdif IS NULL;\n";
				$sql .= "INSERT INTO dif_saldos (num_cia, cuenta, saldo_sistema, saldo_banco) VALUES ($saldo[num_cia], $cuenta, " . round($saldo_final, 2) . ", " . round($saldo['saldo'], 2) . ");\n";
					
				
				$total += $saldo_final - $saldo['saldo'];
			}
			else {
				$sql .= "UPDATE saldos SET tsdif = NULL WHERE num_cia = $saldo[num_cia] AND cuenta = $cuenta AND tsdif IS NOT NULL;\n";
			}
		}
		
		if (trim($sql) != '') $db->query($sql);
		$tpl->assign("dif.total", number_format($total, 2, ".", ","));
	}
	$tpl->printToScreen();
}
?>