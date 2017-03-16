<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/carta_contrato_vencido.tpl" );
$tpl->prepare();

$ids = array();
foreach ($_POST['inpc'] as $i => $inpc)
	if (isset($_POST['id' . $i])) {
		$ids[] = $_POST['id' . $i];
		$inpcs[$_POST['id' . $i]] = $inpc;
	}

$sql = 'SELECT l.id, a.nombre AS inmobiliaria, nombre_arrendatario AS arrendatario, l.representante AS contacto, direccion_local AS direccion, fecha_inicio, fecha_final, por_incremento, clausula, parrafo, renta_con_recibo AS renta FROM catalogo_arrendatarios l LEFT JOIN catalogo_arrendadores a USING (cod_arrendador) WHERE l.id IN (' . implode(', ', $ids) . ') ORDER BY num_local';
$result = $db->query($sql);

// Obtener ultimo folio de las cartas foleadas
$sql = 'SELECT folio FROM cartas_foleadas WHERE num_cia = 700 ORDER BY folio DESC LIMIT 1';
$tmp = $db->query($sql);
$folio = $tmp ? $tmp[0]['folio'] : 1;

$sql = '';
foreach ($result as $reg) {
	$tpl->newBlock('carta');
	$tpl->assign('_inmobiliaria', ucwords(strtolower($reg['inmobiliaria'])));
	$tpl->assign('inmobiliaria', $reg['inmobiliaria']);
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anio', date('Y'));
	$tpl->assign('arrendatario', $reg['arrendatario']);
	$tpl->assign('direccion', $reg['direccion']);
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha_inicio'], $tmp);
	$fecha_inicio = $tmp[1] . ' de ' . mes_escrito(intval($tmp[2], 10)) . ' de ' . $tmp[3];
	$tpl->assign('fecha_inicio', $fecha_inicio);
	
	$tpl->assign('clausula', $reg['clausula']);
	$tpl->assign('parrafo', $reg['parrafo']);
	
	$por_incremento = $inpcs[$reg['id']] + $reg['por_incremento'];
	$importe_incremento = round($reg['renta'] * $por_incremento / 100, 2);
	$nueva_renta = $reg['renta'] + $importe_incremento;
	
	$tpl->assign('por_incremento', number_format($por_incremento, 2, '.', ','));
	$tpl->assign('renta_anterior', number_format($reg['renta'], 2, '.', ','));
	$tpl->assign('importe_incremento', number_format($importe_incremento, 2, '.', ','));
	$tpl->assign('nueva_renta', number_format($nueva_renta, 2, '.', ','));
	
	$anio = $tmp[2] > date('n') + 2 ? date('Y') - 1 : date('Y');
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha_inicio'], $tmp);
	$fecha_inicio_ant = date('d/m/Y', mktime(0, 0, 0, $tmp[2], $tmp[1], $anio - 1));
	$fecha_termino_ant = date('d/m/Y', mktime(0, 0, 0, $tmp[2] + 12, $tmp[1] - 1, $anio - 1));
	$tpl->assign('fecha_inicio_ant', $fecha_inicio_ant);
	$tpl->assign('fecha_termino_ant', $fecha_termino_ant);
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha_inicio'], $tmp);
	$nueva_fecha_inicio = $tmp[1] . ' DE ' . mes_escrito(intval($tmp[2]), TRUE) . ' DE ' . $anio;
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', date('d/m/Y', mktime(0, 0, 0, $tmp[2] + 12, $tmp[1] - 1, $anio)), $tmp);
	$nueva_fecha_termino = $tmp[1] . ' DE ' . mes_escrito(intval($tmp[2]), TRUE) . ' DE ' . $tmp[3];
	$tpl->assign('nueva_fecha_inicio', $nueva_fecha_inicio);
	$tpl->assign('nueva_fecha_termino', $nueva_fecha_termino);
	
	$sql .= 'INSERT INTO cartas_foleadas (num_cia, folio, fecha, atencion, referencia, cuerpo, iduser, tscap, seguimiento, local) VALUES (';
	$sql .= '700, ';
	$sql .= $folio . ', ';
	$sql .= '\'' . date('d/m/Y') . '\', ';
	$sql .= '\'' . $reg['arrendatario'] . '\', ';
	$sql .= '\'AUMENTO DE RENTA\', ';
	$sql .= "'Derivado del contrato de arrendamiento suscrito entre <strong>$reg[arrendatario] EN SU CAR&Aacute;CTER DE ARRENDATARIO Y POR LA OTRA PARTE $reg[inmobiliaria] COMO ARRENDADOR</strong>, con fecha <strong>$fecha_inicio</strong> por el inmueble ubicado en <strong>$reg[direccion]</strong>.
<br /><br/>
Y derivado de la <srtong>CLAUSULA $reg[clausula] PARRAFO $reg[parrafo]</strong> del contrato, referente al incremento anual por aniversario, se procede aplicar lo ah&iacute; dispuesto de acuerdo a la variaci&oacute;n resultante del <strong>INDICE NACIONAL DE PRECIOS AL CONSUMIDOR (INPC)</strong> por el periodo a continuaci&oacute;n descrito:
<br /><br />
<strong>PERIODO DE CALCULO $fecha_inicio_ant AL $fecha_termino_ant = " . number_format($por_incremento, 2, '.', ',') . "%
<br /><br />
RENTA ANTERIOR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . number_format($reg['renta'], 2, '.', ',') . "
<br /><br />
PORCENTAJE DE INCREMENTO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . number_format($por_incremento, 2, '.', ',') . "
<br /><br />
IMPORTE DE INCREMENTO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . number_format($importe_incremento, 2, '.', ',') . "
<br /><br />
NUEVA RENTA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . number_format($nueva_renta, 2, '.', ',') . "
<br /><br />
VIGENCIA NUEVA RENTA DEL $nueva_fecha_inicio AL $nueva_fecha_termino</strong>
<br /><br />
Suscrito por las partes de conformidad el presente documento formara parte integrante del contrato de referencia.', ";
	$sql .= "$_SESSION[iduser], now(), 1, $reg[id]);\n";
}

$db->query($sql);
$tpl->printToScreen();
?>