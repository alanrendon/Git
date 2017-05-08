<?php
// CANCELACION DE CHEQUES
// Tabla 'cheques,estado_cuenta,pasivo_proveedores,facturas_pagadas'
// Menu
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe registro de cheque o ya ha sido cancelado";
$descripcion_error[2] = "No se puede cancelar el cheque debido a que ya ha sido conciliado";

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO PANTALLA");

// Cancelar un cheque
if (isset($_POST['id'])) {
	// Obtener datos del cheque
	$sql = "SELECT * FROM cheques WHERE id = $_POST[id]";
	$cheque = $db->query($sql);

	// [24-Mar-2009] Obtener datos del proveedor
	$sql = "SELECT * FROM catalogo_proveedores WHERE num_proveedor = {$cheque[0]['num_proveedor']}";
	$tmp = $db->query($sql);
	$pro = $tmp[0];

	$cod_mov = in_array($cheque[0]['cod_mov'], array(5, 41)) ? ($pro['trans'] == 't' ? 41 : 5) : $cheque[0]['cod_mov'];
	$poliza = $pro['trans'] == 't' ? 'TRUE' : 'FALSE';
	$archivo = $pro['trans'] == 't' ? 'FALSE' : 'TRUE';

	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $cheque[0]['fecha'], $temp);
	$cheque_ts = mktime(0, 0, 0, $temp[2], $temp[1], $temp[3]);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha_cancelacion'], $temp);
	$mes_ts = mktime(0, 0, 0, $temp[2], 1, $temp[3]);

	if ($_POST['dev'] == "") {
		$sql = "UPDATE cheques SET fecha_cancelacion = '{$_POST['fecha_cancelacion']}', tscan = NOW(), iduser_can = {$_SESSION['iduser']}, site = TRUE, tssite = NULL WHERE id = {$_POST['id']};\n";
		$sql .= "UPDATE transferencias_electronicas SET status = 2 WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']} AND fecha_gen = '{$cheque[0]['fecha']}';\n";

		if ($cheque_ts >= $mes_ts) {
			$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$cheque[0]['num_cia']} and fecha = '{$cheque[0]['fecha']}' AND codgastos = {$cheque[0]['codgastos']} AND folio = {$cheque[0]['folio']};\n";
			if ($_POST['inv'] == "")
				$sql .= "DELETE FROM estado_cuenta WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND importe = {$cheque[0]['importe']} AND cuenta = {$cheque[0]['cuenta']};\n";
			else
				$sql .= "UPDATE estado_cuenta SET concepto = concepto || ' (CANCELADO $_POST[fecha_cancelacion] FOLIO ' || folio || ')', folio = NULL WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND importe = {$cheque[0]['importe']} AND cuenta = {$cheque[0]['cuenta']};\n";
		}
		else {
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio, cuenta) SELECT codgastos, num_cia, fecha_cancelacion, importe * -1, concepto, 'TRUE', folio, cuenta FROM cheques WHERE id = $_POST[id];\n";
			$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta) SELECT cod_mov, num_proveedor, num_cia, fecha_cancelacion, folio, importe * -1, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta FROM cheques WHERE id = $_POST[id];\n";
			$sql .= "INSERT INTO pagos_otras_cias (num_cia, cuenta, folio, fecha, num_cia_aplica) SELECT num_cia, cuenta, folio, '{$_POST['fecha_cancelacion']}', num_cia_aplica FROM cheques c LEFT JOIN pagos_otras_cias poc USING (num_cia, cuenta, folio, fecha) WHERE c.id = {$_POST['id']} AND num_cia_aplica IS NOT NULL;\n";
			if ($_POST['inv'] == "")
				$sql .= "DELETE FROM estado_cuenta WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND importe = {$cheque[0]['importe']} AND cuenta = {$cheque[0]['cuenta']};\n";
			else
				$sql .= "UPDATE estado_cuenta SET concepto = concepto || ' (CANCELADO $_POST[fecha_cancelacion] FOLIO ' || folio || ')', folio = NULL WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND importe = {$cheque[0]['importe']} AND cuenta = {$cheque[0]['cuenta']};\n";
		}

		if ($_POST['pas'] == 1) {
			if ($cheque[0]['num_cia'] < 900) {
				$sql .= "INSERT INTO pasivo_proveedores (
					num_cia,
					num_fact,
					total,
					descripcion,
					fecha,
					num_proveedor,
					codgastos,
					copia_fac
				)
				SELECT
					COALESCE(poc.num_cia_aplica, fp.num_cia),
					fp.num_fact,
					fp.total,
					fp.descripcion,
					fp.fecha,
					fp.num_proveedor,
					fp.codgastos,
					TRUE
				FROM
					facturas_pagadas fp
					LEFT JOIN pagos_otras_cias poc ON (
						poc.num_cia = fp.num_cia
						AND poc.cuenta = fp.cuenta
						AND poc.folio = fp.folio_cheque
						AND poc.fecha = fp.fecha_cheque
					)
				WHERE
					fp.num_cia = {$cheque[0]['num_cia']}
					AND fp.folio_cheque = {$cheque[0]['folio']}
					AND fp.cuenta = {$cheque[0]['cuenta']}
					AND fp.fecha_cheque = '{$cheque[0]['fecha']}';\n";

				$sql .= "DELETE
				FROM
					facturas_pagadas
				WHERE
					num_cia = {$cheque[0]['num_cia']}
					AND folio_cheque = {$cheque[0]['folio']}
					AND cuenta = {$cheque[0]['cuenta']}
					AND fecha_cheque = '{$cheque[0]['fecha']}';\n";
			}
			else {
				$sql .= "UPDATE facturas_zap SET folio = NULL, cuenta = NULL, tspago = NULL WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
				$sql .= "UPDATE devoluciones_zap SET folio_cheque = NULL, cuenta = NULL, imp = 'FALSE', folio = NULL, num_cia_cheque = NULL, num_fact = NULL WHERE num_cia_cheque = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
				$sql .= "UPDATE notas_credito_zap SET status = 1, num_cia_apl = NULL, folio_cheque = NULL, cuenta = NULL, num_fact = NULL WHERE num_cia = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
			}
		}

		if ($_POST['reim'] == 1) {
			$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = {$cheque[0]['num_cia']} AND cuenta = {$cheque[0]['cuenta']} ORDER BY id DESC LIMIT 1");
			$ultimo_folio = $temp[0]['folio'] + 1;

			$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta, poliza, archivo) SELECT $cod_mov, num_proveedor, num_cia, '$_POST[fecha_cancelacion]', $ultimo_folio, importe, iduser, a_nombre, 'FALSE', concepto, facturas, codgastos, cuenta, '$poliza', '$archivo' FROM cheques WHERE id = $_POST[id];\n";
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio) SELECT codgastos, num_cia, '$_POST[fecha_cancelacion]', importe, concepto, 'TRUE', $ultimo_folio FROM cheques WHERE id = $_POST[id];\n";
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta) SELECT num_cia, '$_POST[fecha_cancelacion]', 'TRUE', importe, $cod_mov, $ultimo_folio, facturas, cuenta FROM cheques WHERE id = $_POST[id];\n";
			$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ($ultimo_folio, {$cheque[0]['num_cia']}, 'FALSE', 'TRUE', CURRENT_DATE, {$cheque[0]['cuenta']});\n";
			if ($cheque[0]['num_cia'] < 900)
				$sql .= "UPDATE facturas_pagadas SET folio_cheque = $ultimo_folio WHERE num_cia = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND fecha_cheque = '{$cheque[0]['fecha']}';\n";
			else {
				$sql .= "UPDATE facturas_zap SET folio = $ultimo_folio WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
				$sql .= "UPDATE devoluciones_zap SET folio_cheque = $ultimo_folio WHERE num_cia_cheque = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
				$sql .= "UPDATE notas_credito_zap SET folio_cheque = $ultimo_folio WHERE num_cia = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND cuenta = {$cheque[0]['cuenta']};\n";
			}
		}
		else
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque[0]['importe']} WHERE num_cia = {$cheque[0]['num_cia']} AND cuenta = {$cheque[0]['cuenta']};\n";
	}
	else {
		$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = {$cheque[0]['num_cia']} AND cuenta = {$cheque[0]['cuenta']} ORDER BY id DESC LIMIT 1");
		$ultimo_folio = $temp[0]['folio'] + 1;

		$sql = "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta, poliza, archivo) SELECT $cod_mov, num_proveedor, num_cia, '$_POST[fecha_cancelacion]', $ultimo_folio, importe, iduser, a_nombre, 'FALSE', 'SUSTITUYE A ' || folio, facturas, codgastos, cuenta, '$poliza', '$archivo' FROM cheques WHERE id = $_POST[id];\n";
		$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio, cuenta) SELECT codgastos, num_cia, '$_POST[fecha_cancelacion]', importe, concepto, 'TRUE', $ultimo_folio, cuenta FROM cheques WHERE id = $_POST[id];\n";
		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta) SELECT num_cia, '$_POST[fecha_cancelacion]', 'TRUE', importe, $cod_mov, $ultimo_folio, '(SUSTITUYE A ' || folio || ') ' || facturas, cuenta FROM cheques WHERE id = $_POST[id];\n";
		$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ($ultimo_folio, {$cheque[0]['num_cia']}, 'FALSE', 'TRUE', CURRENT_DATE, {$cheque[0]['cuenta']});\n";
		if ($cheque[0]['num_cia'] < 900)
			$sql .= "UPDATE facturas_pagadas SET folio_cheque = $ultimo_folio WHERE num_cia = {$cheque[0]['num_cia']} AND folio_cheque = {$cheque[0]['folio']} AND fecha_cheque = '{$cheque[0]['fecha']}';\n";
		else
			$sql .= "UPDATE facturas_zap SET folio = $ultimo_folio WHERE num_cia = {$cheque[0]['num_cia']} AND folio = {$cheque[0]['folio']};\n";
	}//echo "<pre>$sql</pre>";die;
	$db->query($sql);

	header("location: ./ban_che_can_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_can_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Generar pantalla de datos
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$tpl->assign("fecha", date("d/m/Y"));
	/*if (!in_array($_SESSION['iduser'], $users))*/ $tpl->newBlock("options");


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

$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

// Buscar cheque
$sql = "SELECT id, num_cia, nombre_corto, $clabe_cuenta, fecha, folio, num_cheque, a_nombre, concepto, importe FROM cheques LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = $_GET[num_cia] AND folio = $_GET[folio] AND fecha_cancelacion IS NULL AND cuenta = $_GET[cuenta] AND importe > 0 AND fecha >= '2010/01/01'";
$sql .= in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 998" : "";
$cheque = $db->query($sql);

if (!$cheque) {
	header("location: ./ban_che_can_v2.php?codigo_error=1");
	die;
}

$tpl->newBlock("info");
$tpl->assign("id", $cheque[0]['id']);
$tpl->assign("fecha_cancelacion", $_GET['fecha_cancelacion']);
$tpl->assign("num_cia", $cheque[0]['num_cia']);
$tpl->assign("nombre_cia", $cheque[0]['nombre_corto']);
$tpl->assign("cuenta", $cheque[0][$clabe_cuenta]);
$tpl->assign("fecha", $cheque[0]['fecha']);
$tpl->assign("folio", $cheque[0]['folio']);
$tpl->assign("num_cheque", $cheque[0]['num_cheque']);
$tpl->assign("a_nombre", $cheque[0]['a_nombre']);
$tpl->assign("concepto", $cheque[0]['concepto']);
$tpl->assign("importe", number_format($cheque[0]['importe'], 2, ".", ","));
$tpl->assign("pas", isset($_GET['pas']) ? 1 : "");
$tpl->assign("reim", isset($_GET['reim']) ? 1 : "");

// Buscar cheque en estados de cuenta
$sql = "SELECT * FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND folio = $_GET[folio] AND cuenta = $_GET[cuenta] AND importe = {$cheque[0]['importe']}";
$estado = $db->query($sql);

if ($estado[0]['fecha_con'] != "") {
	// Buscar un depósito equivalente
	//$sql = "SELECT * FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND importe = {$estado[0]['importe']} AND tipo_mov = 'FALSE' AND fecha_con = '{$estado[0]['fecha_con']}' AND concepto LIKE '%DEV.CORRECC. DE CARGO%' AND cuenta = $_GET[cuenta]";
	// [31-Oct-2008] Se incluye código 49 'DEVOLUCIÓN SPEI' para el caso de transferencias
	$sql = "SELECT * FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND importe = {$estado[0]['importe']} AND tipo_mov = FALSE AND fecha_con >= '{$estado[0]['fecha_con']}' AND cod_mov IN (24, 25, 49) AND cuenta = $_GET[cuenta]";
	$dep = $db->query($sql);

	if (!$dep) {
		$tpl->assign("disabled", "disabled");
		$tpl->assign("mensaje", "<p><strong><font face=\"Arial, Helvetica, sans-serif\">No es posible cancelar el cheque porque esta conciliado y no hay una devolución para correción de cargo</font></strong></p>");
	}
	else if ($dep[0]['fecha_con'] != "" && isset($_GET['reim'])) {
		$tpl->assign("dev", "1");
		$tpl->assign("inv", "1");
		$tpl->assign("mensaje", "<p><strong><font face=\"Arial, Helvetica, sans-serif\">NOTA: Se hara una sustitución por este cheque</font></strong></p>");
	}
	else
		$tpl->assign("inv", "1");
}

$tpl->printToScreen();
?>
