<?php
// DIFERENCIAS DE SALDOS
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gof_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = $_GET['mes'] > 0 ? "01/$_GET[mes]/$_GET[anio]" : "01/01/$_GET[anio]";
	$fecha2 = $_GET['mes'] > 0 ? date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio'])) : date("d/m/Y");
	
	$sql = "SELECT num_cia, nombre_corto, fecha, cod_gastos, descripcion, tipo_mov, importe, clave_balance, comentario FROM gastos_caja LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " LEFT JOIN catalogo_gastos_caja AS cg ON (cg.id = cod_gastos) WHERE";
	$sql .= $_GET['fecha_cap'] != '' ? " fecha_captura = '$_GET[fecha_cap]'" : " fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= /*$_SESSION['iduser'] != 1 ? */'AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')/* : ''*/;
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= $_GET['cod_gastos'] > 0 ? " AND cod_gastos = $_GET[cod_gastos]" : "";
	$sql .= " ORDER BY num_cia, fecha, descripcion";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_gof_con_v2.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("fecha1", $fecha1);
	$tpl->assign("fecha2", $fecha2);
	
	$gte = 0;
	$gti = 0;
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia.egreso", number_format($te, 2, ".", ","));
				$tpl->assign("cia.ingreso", number_format($ti, 2, ".", ","));
				$tpl->assign("cia.total", number_format($te - $ti, 2, ".", ","));
			}
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $reg['nombre_corto']);
			$te = 0;
			$ti = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $reg['fecha']);
		$tpl->assign("concepto", $reg['descripcion']);
		$tpl->assign("bal", $reg['clave_balance'] == "t" ? "X" : "");
		$tpl->assign("comentario", trim($reg['comentario']) != '' ? trim($reg['comentario']) : '&nbsp;');
		$tpl->assign($reg['tipo_mov'] == "f" ? "egreso" : "ingreso", number_format($reg['importe'], 2, ".", ","));
		$te += $reg['tipo_mov'] == "f" ? $reg['importe'] : 0;
		$ti += $reg['tipo_mov'] == "t" ? $reg['importe'] : 0;
		$gte += $reg['tipo_mov'] == "f" ? $reg['importe'] : 0;
		$gti += $reg['tipo_mov'] == "t" ? $reg['importe'] : 0;
	}
	if ($num_cia != NULL) {
		$tpl->assign("cia.egreso", number_format($te, 2, ".", ","));
		$tpl->assign("cia.ingreso", number_format($ti, 2, ".", ","));
		$tpl->assign("cia.total", number_format($te - $ti, 2, ".", ","));
	}
	$tpl->assign("listado.egreso", number_format($gte, 2, ".", ","));
	$tpl->assign("listado.ingreso", number_format($gti, 2, ".", ","));
	$tpl->assign("listado.total", number_format($gte - $gti, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n", date('d') <= 10 ? mktime(0, 0, 0, date("n"), 0, date("Y")) : time()), " selected");
$tpl->assign("anio", date("Y", date('d') <= 10 ? mktime(0, 0, 0, date("n"), 0, date("Y")) : time()));

$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre");
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $c) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $c['num_cia']);
	$tpl->assign('nombre', $c['nombre']);
}

$cods = $db->query("SELECT id, descripcion FROM catalogo_gastos_caja ORDER BY descripcion");
foreach ($cods as $cod) {
	$tpl->newBlock("cod");
	$tpl->assign("id", $cod['id']);
	$tpl->assign("nombre", $cod['descripcion']);
}

$tpl->printToScreen();
?>