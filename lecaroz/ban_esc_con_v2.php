<?php
// LISTADO DE ESTADOS DE CUENTA

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
if (!isset($_GET['impresion'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/ban/ban_esc_con_v2.tpl");
	$tpl->prepare();
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	if (isset($_SESSION['menu']))
		$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
	$tpl->gotoBlock("_ROOT");
}
else {
	$tpl = new TemplatePower( "./plantillas/ban/estado_cuenta_v2.tpl" );
	$tpl->prepare();
}

if (isset($_GET['num_cia'])) {
	// [23/06/2006] Validación para zapaterías
	if (in_array($_SESSION['iduser'], $users) && $_GET['num_cia'] < 900) {
		if (isset($_GET['cerrar'])) {
			$tpl->newBlock("cerrar");
			$tpl->printToScreen();
			die;
		}
		else {
			header("location: ./ban_esc_con_v2.php?codigo_error=1");
			die;
		}
	}
	
	$sql = '
		SELECT
			ec.fecha,
			fecha_con,
			ec.importe,
			tipo_mov,
			folio,
			ec.concepto,
			ec.cod_mov,
			a_nombre
				AS
					beneficiario
		FROM
				estado_cuenta ec
			LEFT JOIN
				cheques c
					USING
						(num_cia, cuenta, folio, fecha)
		WHERE
	';
	
//	$sql = "SELECT fecha, fecha_con, importe, tipo_mov, folio, concepto, cod_mov, CASE WHEN tipo_mov = 'TRUE' AND folio > 0 THEN";
//	$sql .= " (SELECT a_nombre FROM CHEQUES WHERE num_cia = estado_cuenta.num_cia AND folio = estado_cuenta.folio AND cuenta = estado_cuenta.cuenta AND importe = estado_cuenta.importe AND fecha_cancelacion IS NULL)";
//	$sql .= " ELSE '&nbsp' END AS beneficiario FROM estado_cuenta WHERE";
	$sql .= " num_cia = $_GET[num_cia]" . ($_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : '');
	$sql .= isset($_GET['fecha1']) ? ($_GET['fecha2'] != "" ? " AND ec.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND ec.fecha = '$_GET[fecha1]'") : "";
	switch ($_GET['tipo']) {
		case 1: $sql .= " AND tipo_mov = 'FALSE'"; break;
		case 2: $sql .= " AND tipo_mov = 'TRUE'"; break;
		case 3: $sql .= " AND cod_mov = $_GET[cod_mov]"; break;
	}
//	if (isset($_GET['che_pen']))	// Solo cheques y transferencias pendientes
//		$sql .= " AND ec.cod_mov IN (5, 41)";
	if (isset($_GET['nocon']))	// Solo movimientos no conciliados
		$sql .= " AND fecha_con IS NULL";
//	if (isset($_GET['noacuenta']))
//		$sql .= ' AND acuenta = FALSE';
	$sql .= " ORDER BY fecha, folio, tipo_mov, importe DESC";
	$result = $db->query($sql);
	
	if (!$result) {
		if (isset($_GET['cerrar'])) {
			$tpl->newBlock("cerrar");
			$tpl->printToScreen();
			die;
		}
		else {
			header("location: ./ban_esc_con_v2.php?codigo_error=1");
			die;
		}
	}
	
	$tpl->newBlock("listado");
	$clabe_cuenta = "clabe_cuenta" . ($_GET['cuenta'] == 2 ? "2" : "");
	$cia = $db->query("SELECT nombre, $clabe_cuenta FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tpl->assign("nombre_cia", $cia[0]['nombre']);
	$tpl->assign("cuenta", $cia[0][$clabe_cuenta]);
	$tpl->assign("banco", $_GET['cuenta'] > 0 ? ($_GET['cuenta'] == 1 ? "BANORTE" : "SANTANDER") : 'AMBOS BANCOS');
	$tpl->assign('fecha', date('d/m/Y'));
	$tpl->assign('hora', date('h:ia'));
	
	// *********************************** CALCULAR SALDO INICIAL Y FINAL DE LIBROS Y BANCOS ***********************************
	if (empty($_GET['nocon'])) {
		// Obtener ultimo saldo de la compañía
		$ultimo_saldo = $db->query("SELECT * FROM saldos WHERE num_cia = $_GET[num_cia] AND cuenta = $_GET[cuenta]");
		
		// Saldo inicial de libros
		$saldo_ini_lib = $ultimo_saldo[0]['saldo_libros'];
		$tmp = $db->query("SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND fecha >= '$_GET[fecha1]' AND cuenta = $_GET[cuenta] GROUP BY tipo_mov ORDER BY tipo_mov");
		if ($tmp)
			foreach ($tmp as $mov)
				$saldo_ini_lib += $mov['tipo_mov'] == "t" ? $mov['sum'] : -$mov['sum'];
		
		// Saldo final de libros
		$saldo_fin_lib = $saldo_ini_lib;
		$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND cuenta = $_GET[cuenta] AND fecha ";
		$sql .= $_GET['fecha2'] != "" ? "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : "= $_GET[fecha1]";
		$sql .= " GROUP BY tipo_mov ORDER BY tipo_mov";
		$tmp = $db->query($sql);
		if ($tmp)
			foreach ($tmp as $mov)
				$saldo_fin_lib += $mov['tipo_mov'] == "f" ? $mov['sum'] : -$mov['sum'];
		
		// Saldo inicial de bancos
		$saldo_ini_ban = $ultimo_saldo[0]['saldo_bancos'];
		$tmp = $db->query("SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND fecha_con >= '$_GET[fecha1]' AND cuenta = $_GET[cuenta] GROUP BY tipo_mov ORDER BY tipo_mov");
		if ($tmp)
			foreach ($tmp as $mov)
				$saldo_ini_ban += $mov['tipo_mov'] == "t" ? $mov['sum'] : -$mov['sum'];
		
		// Saldo final de bancos
		$saldo_fin_ban = $saldo_ini_ban;
		$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND cuenta = $_GET[cuenta] AND fecha_con ";
		$sql .= $_GET['fecha2'] != "" ? "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : "= $_GET[fecha1]";
		$sql .= " GROUP BY tipo_mov ORDER BY tipo_mov";
		$tmp = $db->query($sql);
		if ($tmp)
			foreach ($tmp as $mov)
				$saldo_fin_ban += $mov['tipo_mov'] == "f" ? $mov['sum'] : -$mov['sum'];
	}
	// *****************************************************************************************************************
	
	if ($_GET['tipo'] == 0 && !isset($_GET['che_pen'])) {
		$tpl->newBlock("saldo_ini");
		$tpl->assign("saldo_lib_ini", number_format($saldo_ini_lib, 2, ".", ","));
		$tpl->assign("saldo_ban_ini", number_format($saldo_ini_ban, 2, ".", ","));
		
		$tpl->newBlock("saldo_fin");
		$tpl->assign("saldo_lib_fin", number_format($saldo_fin_lib, 2, ".", ","));
		$tpl->assign("saldo_ban_fin", number_format($saldo_fin_ban, 2, ".", ","));
		$tpl->assign("diferencia", "<font color=\"#" . ($saldo_fin_ban - $saldo_fin_lib > 0 ? "0000CC" : "CC0000") . "\">" . number_format($saldo_fin_ban - $saldo_fin_lib, 2, ".", ",") . "</font>");
	}
	
	$cat_mov = $_GET['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	$cod_mov = $db->query("SELECT cod_mov, descripcion FROM $cat_mov GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	
	function buscarCod($cod) {
		global $cod_mov;
		
		if (!$cod_mov)
			return FALSE;
		
		for ($i = 0; $i < count($cod_mov); $i++)
			if ($cod == $cod_mov[$i]['cod_mov'])
				return $cod_mov[$i]['descripcion'];
		
		return FALSE;
	}
	
	$abonos = 0;
	$cargos = 0;
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("fecha_con", $result[$i]['fecha_con']);
		$tpl->assign($result[$i]['tipo_mov'] == "f" ? "abono" : "cargo", number_format($result[$i]['importe'], 2, ".", ","));
		$tpl->assign("folio", in_array($result[$i]['cod_mov'], array(5, 41, 33, 43, 103)) ? $result[$i]['folio'] : "");
		$tpl->assign("color_folio", $result[$i]['cod_mov'] == 41 ? " style=\"color: #009933;\"" : "");
		$tpl->assign("beneficiario", $result[$i]['beneficiario']);
		$tpl->assign("concepto", $result[$i]['concepto']);
		$tpl->assign("cod_mov", $result[$i]['cod_mov']);
		$tpl->assign("descripcion", buscarCod($result[$i]['cod_mov']));
		
		$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
	}
	$tpl->assign("listado.abonos", number_format($abonos, 2, ".", ","));
	$tpl->assign("listado.cargos", number_format($cargos, 2, ".", ","));
	
	if (isset($_GET['cerrar']))
		$tpl->newBlock("boton_cerrar");
	else if (isset($_GET['efe']))
		$tpl->newBlock("boton_efe");
	else
		$tpl->newBlock("boton_regresar");
	
	$tpl->printToScreen();
	die;
}

// -------------------------------- Tipo de listado -------------------------------------------------------
$tpl->newBlock("datos");

$tpl->assign("fecha1", date("d/m/Y", mktime(0, 0, 0, date("m"), 1, date("Y"))));
$tpl->assign("fecha2", date("d/m/Y"));

$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre']);
}

$cod_mov_ban = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_bancos GROUP BY cod_mov, descripcion ORDER BY descripcion");
$cod_mov_san = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander GROUP BY cod_mov, descripcion ORDER BY descripcion");
for ($i = 0; $i < count($cod_mov_ban); $i++) {
	$tpl->newBlock("banorte");
	$tpl->assign("i", $i);
	$tpl->assign("cod_mov", $cod_mov_ban[$i]['cod_mov']);
	$tpl->assign("des", $cod_mov_ban[$i]['descripcion']);
}
for ($i = 0; $i < count($cod_mov_san); $i++) {
	$tpl->newBlock("santander");
	$tpl->assign("i", $i);
	$tpl->assign("cod_mov", $cod_mov_san[$i]['cod_mov']);
	$tpl->assign("des", $cod_mov_san[$i]['descripcion']);
}

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
?>