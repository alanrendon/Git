<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/recibo.tpl" );
$tpl->prepare();

if (!isset($_REQUEST['fecha'])) {
	$fecha = $db->query("SELECT fecha FROM cheques WHERE codgastos = $_GET[codgastos] AND fecha_cancelacion IS NULL" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . " AND num_cia < 900 ORDER BY fecha DESC LIMIT 1");
	
	$pieces = explode('/', $fecha[0]['fecha']);
	
	$dia = intval($pieces[0], 10);
	$mes = mes_escrito(intval($pieces[1], 10), TRUE);
	$anio = intval($pieces[2], 10);
}
else {
	$fecha[0]['fecha'] = $_REQUEST['fecha'];
	
	$dia = isset($_REQUEST['dia']) ? $_REQUEST['dia'] : date("j");
	$mes = mes_escrito(isset($_REQUEST['mes']) ? $_REQUEST['mes'] : date("n"), TRUE);
	$anio = isset($_REQUEST['anio']) ? $_REQUEST['anio'] : date("Y");
}

if (!$fecha) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT catalogo_companias.nombre, cheques.concepto, extract(month FROM fecha) AS mes_cheque, extract(year FROM fecha) AS anio_cheque, pre_cheques.importe, pre_cheques.iva, pre_cheques.ret_iva, pre_cheques.isr, pre_cheques.total, folio, cuenta, a_nombre";
$sql .= " FROM cheques LEFT JOIN pre_cheques USING (num_cia, num_proveedor, codgastos) LEFT JOIN catalogo_companias USING (num_cia) WHERE codgastos = $_GET[codgastos] AND fecha = '{$fecha[0]['fecha']}' AND fecha_cancelacion IS NULL";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['num_pro'] > 0 ? " AND cheques.num_proveedor = $_GET[num_pro]" : "";
$sql .= isset($_GET['folio']) && $_GET['folio'] > 0 ? " AND cheques.folio = $_GET[folio]" : "";
$sql .= " ORDER BY a_nombre, num_cia";

// $sql = "
// 	SELECT
// 		cc.nombre,
// 		c.concepto,
// 		extract(day FROM fecha)
// 			AS dia_cheque,
// 		extract(month FROM fecha)
// 			AS mes_cheque,
// 		extract(year FROM fecha)
// 			AS anio_cheque,
// 		ROUND((c.importe / 0.9)::NUMERIC, 2)
// 			AS importe,
// 		0
// 			AS iva,
// 		0
// 			AS ret_iva,
// 		ABS(c.importe - ROUND((c.importe / 0.9)::NUMERIC, 2))
// 			AS isr,
// 		c.importe
// 			AS total,
// 		folio,
// 		cuenta,
// 		a_nombre
// 	FROM
// 		cheques c
// 		LEFT JOIN catalogo_companias cc
// 			USING (num_cia)
// 	WHERE
// 		codgastos = 155
// 		AND c.num_proveedor = 609
// 		AND fecha >= '29/03/2011'
// 		AND fecha_cancelacion IS NULL
// 		AND importe > 0
// 		AND num_cia IN (44, 16, 123)
// 	ORDER BY
// 		a_nombre,
// 		num_cia,
// 		c.fecha
// ";


$result = $db->query($sql);

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("recibo");
	$tpl->assign("dia", $dia);
	$tpl->assign("mes", $mes);
	$tpl->assign("anio", $anio);
	// $tpl->assign("dia", /*$dia*/$result[$i]['dia_cheque']);
	// $tpl->assign("mes", /*$mes*/mes_escrito($result[$i]['mes_cheque'], TRUE));
	// $tpl->assign("anio", /*$anio*/$result[$i]['anio_cheque']);
	
	$tpl->assign("total", number_format($result[$i]['total'], 2, ".", ","));
	$tpl->assign("nombre_cia", $result[$i]['nombre']);
	$tpl->assign("total_escrito", num2string($result[$i]['total']));
	$tpl->assign("gasto", $result[$i]['concepto']);
	$tpl->assign("mes_cheque", mes_escrito($result[$i]['mes_cheque'], TRUE));
	$tpl->assign("anio_cheque", $result[$i]['anio_cheque']);
	$tpl->assign("importe_gasto", number_format($result[$i]['importe'], 2, ".", ","));
	$tpl->assign("folio", $result[$i]['folio']);
	$tpl->assign("banco", $result[$i]['cuenta'] == 1 ? "BANORTE" : "SANTANDER");
	$tpl->assign("a_nombre", $result[$i]['a_nombre']);
	if ($result[$i]['iva'] > 0) {
		$tpl->newBlock("imp");
		$tpl->assign("imp", "I.V.A.");
		$tpl->assign("importe_imp", number_format($result[$i]['iva'], 2, ".", ","));
	}
	if ($result[$i]['ret_iva'] > 0) {
		$tpl->newBlock("imp");
		$tpl->assign("imp", "RET. I.V.A.");
		$tpl->assign("importe_imp", number_format($result[$i]['ret_iva'], 2, ".", ","));
	}
	if ($result[$i]['isr'] > 0) {
		$tpl->newBlock("imp");
		$tpl->assign("imp", "I.S.R.");
		$tpl->assign("importe_imp", number_format($result[$i]['isr'], 2, ".", ","));
	}
}

$tpl->printToScreen();
?>