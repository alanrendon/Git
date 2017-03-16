<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------

$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_che_cmu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$numfilas = 100;

if (isset($_POST['fecha_cancelacion'])) {
	$sql = "";
	for ($i=0; $i<count($_POST['num_cia']); $i++) {
		// Obtener datos del cheque
		$cheque = $db->query("SELECT * FROM cheques WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta] AND folio = {$_POST['folio'][$i]} AND fecha = '{$_POST['fecha'][$i]}' AND fecha >= '01/01/2010'");

		// Obtener timestamp de la fecha del cheque, de la de cancelacion y comparar
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$cheque[0]['fecha'],$temp);
		$cheque_ts = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha_cancelacion'],$temp);
		$cancelacion_ts = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
		$mes_ts = mktime(0,0,0,$temp[2],1,$temp[3]);

		// CASO 1. CANCELACION NORMAL
		if ($cheque_ts >= $mes_ts)
			// Borrar movimiento de gastos
			$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]} AND fecha = '{$cheque[0]['fecha']}';\n";
		// CASO 2. CANCELACION DE UN CHEQUE DE MESES PASADOS
		else {
			// Insertar un cheque negativo con la fecha de cancelacion como fecha de creación
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio, cuenta) ";
			$sql .= "SELECT codgastos, num_cia, '$_POST[fecha_cancelacion]', -importe, concepto, 'TRUE', folio, cuenta FROM cheques WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]} AND cuenta = $_POST[cuenta] AND fecha = '{$cheque[0]['fecha']}';\n";
			// Insertar un gasto negativo con la fecha de cancelacion como fecha de creación
			$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, num_cheque, fecha_cancelacion, codgastos, cuenta) SELECT cod_mov, num_proveedor, num_cia, '$_POST[fecha_cancelacion]', folio, -importe, iduser, a_nombre, imp, concepto, facturas, num_cheque, fecha_cancelacion, codgastos, cuenta FROM cheques WHERE id = {$cheque[0]['id']};\n";
			$sql .= "INSERT INTO pagos_otras_cias (num_cia, cuenta, folio, fecha, num_cia_aplica) SELECT num_cia, cuenta, folio, '{$_POST['fecha_cancelacion']}', num_cia_aplica FROM cheques c LEFT JOIN pagos_otras_cias poc USING (num_cia, cuenta, folio, fecha) WHERE c.id = {$cheque[0]['id']} AND num_cia_aplica IS NOT NULL;\n";
		}

		// Poner la fecha de cancelación al cheque
		$sql .= "UPDATE cheques SET fecha_cancelacion = '$_POST[fecha_cancelacion]' WHERE id = {$cheque[0]['id']};\n";

		// Borrar movimiento del estado de cuenta
		$sql .= "DELETE FROM estado_cuenta WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta] AND folio = {$_POST['folio'][$i]} AND importe = {$_POST['importe'][$i]} AND fecha = '{$cheque[0]['fecha']}';\n";

		// [09-Oct-2007] Quitar movimiento de transferencias
		$sql .= "UPDATE transferencias_electronicas SET status = 2 WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]} AND cuenta = $_POST[cuenta] AND fecha_gen = '{$_POST['fecha'][$i]}';\n";

		// Modificar saldo para la cuenta
		$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque[0]['importe']} WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta];\n";

		// Si esta habilitada la opción de regresar facturas a pasivo...
		if ($_POST['pasivo'] == "TRUE"/*isset($_POST['pasivo'])*/) {
			// Pasar todas las facturas pagadas con el cheque a pasivo a proveedores
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
				COAOLESCE(poc.num_cia_aplica, fp.num_cia),
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
				fp.num_cia = {$_POST['num_cia'][$i]}
				AND fp.folio_cheque = {$_POST['folio'][$i]}
				AND fp.cuenta = {$_POST['cuenta']}
				AND fp.fecha_cheque = '{$_POST['fecha'][$i]}';\n";
			// Borrar de facturas pagadas
			$sql .= "DELETE
			FROM
				facturas_pagadas fp
			WHERE
				num_cia = {$_POST['num_cia'][$i]}
				AND folio_cheque = {$_POST['folio'][$i]}
				AND cuenta = {$_POST['cuenta']}
				AND fecha_cheque = '{$_POST['fecha'][$i]}';\n";
		}
		// [05-Septiembre-2008] NO BORRAR DE TABLAS DE FACTURAS
		/*else {
			// Borrar de facturas
			$sql .= "DELETE FROM facturas WHERE (num_proveedor, num_fact) IN (SELECT num_proveedor, num_fact FROM facturas_pagadas WHERE num_cia = {$_POST['num_cia'][$i]} AND folio_cheque = {$_POST['folio'][$i]} AND cuenta = $_POST[cuenta]);\n";
			// Borrar de facturas pagadas
			$sql .= "DELETE FROM facturas_pagadas WHERE num_cia = {$_POST['num_cia'][$i]} AND folio_cheque = {$_POST['folio'][$i]};\n";
		}*/
	}

	$db->empezar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	$db->desconectar();

	unset($_SESSION['che_can']);

	header("location: ./ban_che_cmu.php");
	die;
}

if (isset($_POST['fecha'])) {
	// Respaldar datos
	$_SESSION['che_can'] = $_POST;

	$tpl->newBlock("listado");
	$tpl->assign("cuenta", $_POST['cuenta']);
	$tpl->assign("fecha_cancelacion", $_POST['fecha']);
	$tpl->assign("pasivo", isset($_POST['pasivo']) ? "TRUE" : "FALSE");

	$count = 0;

	for ($i=0; $i<$numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['folio'][$i] > 0) {
			$sql = "SELECT num_cia,nombre AS nombre_cia,clabe_cuenta AS cuenta,importe,folio,a_nombre,facturas, fecha FROM cheques JOIN catalogo_companias USING(num_cia) WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta] AND folio = {$_POST['folio'][$i]} AND importe > 0 AND fecha_cancelacion IS NULL AND fecha >= '01/01/2010'";
			$cheque = $db->query($sql);

			if ($cheque) {
				// Validar que no este conciliado
				$con = $db->query("SELECT fecha_con FROM estado_cuenta WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta] AND folio = {$_POST['folio'][$i]} AND fecha = '{$cheque[0]['fecha']}'");

				if ($con[0]['fecha_con'] == "") {
					$tpl->newBlock("cheque");
					$tpl->assign("num_cia",$cheque[0]['num_cia']);
					$tpl->assign("nombre_cia",$cheque[0]['nombre_cia']);
					$tpl->assign("cuenta",$cheque[0]['cuenta']);
					$tpl->assign("importe",$cheque[0]['importe']);
					$tpl->assign("fimporte",number_format($cheque[0]['importe'],2,".",","));
					$tpl->assign("folio",$cheque[0]['folio']);
					$tpl->assign("fecha",$cheque[0]['fecha']);
					$tpl->assign("beneficiario",$cheque[0]['a_nombre']);
					$tpl->assign("facturas",$cheque[0]['facturas']);
					$tpl->assign("estatus","OK");
				}
				else {
					$tpl->newBlock("no_cheque");
					$tpl->assign("num_cia",$cheque[0]['num_cia']);
					$tpl->assign("nombre_cia",$cheque[0]['nombre_cia']);
					$tpl->assign("cuenta",$cheque[0]['cuenta']);
					$tpl->assign("importe",$cheque[0]['importe']);
					$tpl->assign("fimporte",number_format($cheque[0]['importe'],2,".",","));
					$tpl->assign("folio",$cheque[0]['folio']);
					$tpl->assign("fecha",$cheque[0]['fecha']);
					$tpl->assign("beneficiario",$cheque[0]['a_nombre']);
					$tpl->assign("facturas",$cheque[0]['facturas']);
					$tpl->assign("estatus","Conciliado");
				}
			}
			else {
				$tpl->newBlock("no_cheque");
				$tpl->assign("num_cia",$_POST['num_cia'][$i]);
				$tpl->assign("nombre_cia",$_POST['nombre_cia'][$i]);
				$tpl->assign("cuenta","&nbsp;");
				$tpl->assign("importe","&nbsp;");
				$tpl->assign("fimporte","&nbsp;");
				$tpl->assign("folio",$_POST['folio'][$i]);
				$tpl->assign("fecha",'&nbsp;');
				$tpl->assign("beneficiario","&nbsp;");
				$tpl->assign("facturas","&nbsp;");
				$tpl->assign("estatus", "No existe o esta cancelado");
			}
		}

	$tpl->printToScreen();
	$db->desconectar();
	die;
}

$tpl->newBlock("captura");
$tpl->assign("fecha", date("d/m/Y"));

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = $db->query($sql);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	if (isset($_SESSION['che_can'])) {
		$tpl->assign("num_cia", $_SESSION['che_can']['num_cia'][$i]);
		$tpl->assign("nombre_cia", $_SESSION['che_can']['nombre_cia'][$i]);
		$tpl->assign("folio", $_SESSION['che_can']['folio'][$i]);
	}
}

unset($_SESSION['che_can']);

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
$db->desconectar();
?>
