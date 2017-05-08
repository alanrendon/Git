<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

if (isset($_REQUEST['visualizar_cfd']) && isset($_REQUEST['id']) && $_REQUEST['id'] > 0)
{
	$path = 'cfds_proveedores/';

	$sql = '
		SELECT
			pdf_file
		FROM
			facturas_zap f
		WHERE
			id = ' . $_REQUEST['id'] . '
	';
	$result = $db->query($sql);

	header('Content-type: application/pdf');
	header('Content-Disposition: inline; filename="factura.pdf"');

	readfile($path . '/' . utf8_encode($result[0]['pdf_file']));

	die;
}

if (isset($_REQUEST['imprimir_cfd']) && isset($_REQUEST['id']) && $_REQUEST['id'] > 0)
{
	$path = 'cfds_proveedores/';

	$sql = '
		SELECT
			pdf_file
		FROM
			facturas_zap f
		WHERE
			id = ' . $_REQUEST['id'] . '
	';
	$result = $db->query($sql);

	$printer = $_SESSION['tipo_usuario'] == 2 ? 'elite' : 'general';

	shell_exec('lp -d ' . $printer . ' ' . $path . '/' . $result[0]['pdf_file']);

	die;
}

if (isset($_POST['id'])) {
	$sql = "DELETE FROM faltantes_zap WHERE (num_cia, num_proveedor, num_fact) IN (SELECT num_cia, num_proveedor, num_fact FROM facturas_zap WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ', ' : "));\n");

	$sql .= "DELETE FROM facturas_zap WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ', ' : ");\n");

	$db->query($sql);

	return 1;
}

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_fac_con_conta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "
		SELECT
			f.id,
			num_cia,
			cc.nombre_corto
				AS nombre_cia,
			f.num_proveedor
				AS num_pro,
			clave,
			cp.nombre
				AS nombre_pro,
			num_fact,
			entrada,
			f.fecha,
			fecha_rec,
			f.concepto,
			f.codgastos,
			descripcion
				AS desc,
			f.importe,
			c.importe
				AS importe_cheque,
			c.fecha
				AS fecha_cheque,
			f.desc1,
			f.desc2,
			f.desc3,
			f.desc4,
			pdesc1,
			pdesc2,
			pdesc3,
			pdesc4,
			faltantes,
			dif_precio,
			dev,
			iva,
			ivaret,
			isr,
			fletes,
			otros,
			total,
			folio,
			f.cuenta,
			fecha_con,
			c.fecha_cancelacion,
			f.sucursal,
			(
				SELECT
					SUM(importe)
				FROM
					notas_credito_zap
				WHERE
					num_proveedor = f.num_proveedor
					AND num_fact = f.num_fact
			)
				AS nota_credito,
			xml_file,
			pdf_file
		FROM
			facturas_zap f
			LEFT JOIN cheques c
				USING (num_cia, folio, cuenta)
			LEFT JOIN estado_cuenta ec
				USING (num_cia, folio, cuenta)
			LEFT JOIN catalogo_gastos cg
				ON (cg.codgastos = f.codgastos)
			LEFT JOIN catalogo_proveedores cp
				ON (cp.num_proveedor = f.num_proveedor)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			total > 0
			AND clave = 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND f.num_proveedor = $_GET[num_pro]" : '';
	$sql .= $_GET['num_fact'] != '' ? " AND num_fact = '$_GET[num_fact]'" : '';
	$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND f.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND f.fecha = '$_GET[fecha1]'") : '';
	$sql .= $_GET['fecha_cobro1'] != '' ? ($_GET['fecha_cobro2'] != '' ? " AND ec.fecha_con BETWEEN '$_GET[fecha_cobro1]' AND '$_GET[fecha_cobro2]'" : " AND ec.fecha_con = '$_GET[fecha_cobro1]'") : '';
	$sql .= $_GET['fecha_captura1'] != '' ? ($_GET['fecha_captura2'] != '' ? " AND f.tscap::DATE BETWEEN '$_GET[fecha_captura1]' AND '$_GET[fecha_captura2]'" : " AND f.tscap::DATE = '$_GET[fecha_captura1]'") : '';
	$sql .= $_GET['codgastos'] > 0 ? " AND f.codgastos = $_GET[codgastos]" : '';
	$sql .= $_GET['criterio'] > 0 ? ($_GET['criterio'] == 1 ? ' AND f.folio IS NULL' : ' AND f.folio IS NOT NULL') : '';
	$sql .= " ORDER BY " . ($_GET['orden'] == 1 ? 'num_cia, num_pro, clave' : 'num_pro, clave, num_cia') . ", f.fecha, num_fact";
	$result = $db->query($sql);

	if (!$result)
		die(header('location: ./zap_fac_con_conta.php?codigo_error=1'));

	$tpl->newBlock('result');
	$numb = NULL;
	$clave = NULL;
	$fnumb = $_GET['orden'] == 1 ? 'num_cia' : 'num_pro';
	$fnombreb = $_GET['orden'] == 1 ? 'nombre_cia' : 'nombre_pro';
	$fnumsb = $_GET['orden'] == 1 ? 'num_pro' : 'num_cia';
	$fnombresb = $_GET['orden'] == 1 ? 'nombre_pro' : 'nombre_cia';

	$mega_gran_importe = 0;
	$mega_gran_faltantes = 0;
	$mega_gran_dif_precio = 0;
	$mega_gran_dev = 0;
	$mega_gran_descuentos = 0;
	$mega_gran_iva = 0;
	$mega_gran_ret_iva = 0;
	$mega_gran_ret_isr = 0;
	$mega_gran_fletes = 0;
	$mega_gran_otros = 0;
	$mega_gran_total = 0;
	foreach ($result as $i => $reg) {
		if ($numb != $reg[$fnumb] || ($_GET['orden'] == 2 && $clave != $reg['clave'])) {
			$numb = $reg[$fnumb];

			if ($_GET['orden'] == 2) $clave = $reg['clave'];
			else $clave = NULL;

			$tpl->newBlock('bloque');
			$tpl->assign('num', $numb . ($_GET['orden'] == 2 && $clave > 0 ? "-$clave" : ''));
			$tpl->assign('nombre', $reg[$fnombreb]);

			$numsb = NULL;

			$gran_importe = 0;
			$gran_faltantes = 0;
			$gran_dif_precio = 0;
			$gran_dev = 0;
			$gran_descuentos = 0;
			$gran_iva = 0;
			$gran_ret_iva = 0;
			$gran_ret_isr = 0;
			$gran_fletes = 0;
			$gran_otros = 0;
			$gran_total = 0;
		}
		if ($numsb != $reg[$fnumsb] || ($_GET['orden'] == 1 && $clave != $reg['clave'])) {
			$numsb = $reg[$fnumsb];

			if ($_GET['orden'] == 1) $clave = $reg['clave'];

			$tpl->newBlock('subbloque');
			$tpl->assign('num', $numsb . ($_GET['orden'] == 1 && $clave > 0 ? "-$clave" : ''));
			$tpl->assign('nombre', $reg[$fnombresb]);

			$importe = 0;
			$faltantes = 0;
			$dif_precio = 0;
			$dev = 0;
			$descuentos = 0;
			$iva = 0;
			$ret_iva = 0;
			$ret_isr = 0;
			$fletes = 0;
			$otros = 0;
			$total = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('dis', $reg['folio'] > 0 && $_SESSION['iduser'] != 1 ? ' disabled' : '');
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('fecha_rec', $reg['fecha_rec']);
		$tpl->assign('concepto', trim($reg['concepto']) != '' ? trim($reg['concepto']) : '&nbsp;');
		$tpl->assign('codgastos', $reg['codgastos']);
		$tpl->assign('desc', $reg['desc']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('faltantes', $reg['faltantes'] != 0 ? number_format($reg['faltantes'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('dif_precio', $reg['dif_precio'] != 0 ? number_format($reg['dif_precio'], 2, '.', ',') : '&nbsp;');
		// [10-Dic-2007] Devoluciones
		if ($reg['dev'] > 0)
			$tmp_dev = get_val($reg['dev']);
		else if (($tmp = $db->query("SELECT sum(importe) FROM devoluciones_zap WHERE num_proveedor = $reg[num_pro] AND num_fact = '$reg[num_fact]'")) && get_val($tmp[0]['sum']) > 0) {
			$tmp_dev = get_val($tmp[0]['sum']);

			$subimporte = $reg['importe'] - $reg['faltantes'] - $reg['dif_precio'] - $tmp_dev;
			$desc1 = $reg['pdesc1'] > 0 ? round($subimporte * $reg['pdesc1'] / 100, 2) : ($reg['desc1'] > 0 ? $reg['desc1'] : 0);
			$desc2 = $reg['pdesc2'] > 0 ? round(($subimporte - $desc1) * $reg['pdesc2'] / 100, 2) : ($reg['desc2'] > 0 ? $reg['desc2'] : 0);
			$desc3 = $reg['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $reg['pdesc3'] / 100, 2) : ($reg['desc3'] > 0 ? $reg['desc3'] : 0);
			$desc4 = $reg['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $reg['pdesc4'] / 100, 2) : ($reg['desc4'] > 0 ? $reg['desc4'] : 0);
			$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
			$iva = $reg['iva'] > 0 ? $subtotal * /*0.15*/0.16 : 0;
			$total_tmp = $subtotal + $iva - $reg['fletes'] + $reg['otros'];
			$reg['desc1'] = $desc1;
			$reg['desc2'] = $desc2;
			$reg['desc3'] = $desc3;
			$reg['desc4'] = $desc4;
			$reg['iva'] = $iva;
			$reg['total'] = $total_tmp;
		}
		else
			$tmp_dev = 0;

		$tpl->assign('dev', $tmp_dev > 0 ? number_format($tmp_dev, 2, '.', ',') : '&nbsp;');
		$desc = $reg['desc1'] + $reg['desc2'] + $reg['desc3'] + $reg['desc4'];
		$tpl->assign('descuentos', $desc != 0 ? number_format($desc, 2, '.', ',') : '&nbsp;');
		$tpl->assign('iva', $reg['iva'] != 0 ? number_format($reg['iva'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('ret_iva', $reg['ivaret'] != 0 ? number_format($reg['ivaret'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('ret_isr', $reg['isr'] != 0 ? number_format($reg['isr'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('fletes', $reg['fletes'] != 0 ? number_format($reg['fletes'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('otros', $reg['otros'] != 0 ? number_format($reg['otros'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('total', number_format($reg['total'], 2, '.', ','));
		$tpl->assign('cheque', $reg['folio'] > 0 ? "<span style=\"color:#660099\">$reg[folio]</span>" : ($reg['folio'] === '0' ? '<span style="color:#0000CC">EFECTIVO</span>' : ($reg['sucursal'] == 't' ? '<span style="color:#660033">PAGO LA MATRIZ</span>' : '&nbsp;')));
		$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
		if ($reg['fecha_cancelacion'] != '') {
			$tpl->assign('fecha_con', 'CANCELADO ' . $reg['fecha_cancelacion']);
		} else if (/*$reg['folio'] > 0 && $reg['importe_cheque'] == 0*/$reg['nota_credito'] > 0) {
			$tpl->assign('fecha_con', 'ACREDITADO ' . $reg['fecha_cheque']);
		} else {
			$tpl->assign('fecha_con', $reg['fecha_con'] != '' ? $reg['fecha_con'] : '&nbsp;');
		}

		$tpl->assign('cfd_disabled', $reg['xml_file'] == '' ? '_gray' : '');
		$tpl->assign('icono_class', $reg['xml_file'] != '' ? ' class="icono"' : '');

		$importe += $reg['importe'];
		$faltantes += $reg['faltantes'];
		$dif_precio += $reg['dif_precio'];
		$dev += $tmp_dev;
		$descuentos += $desc;
		$iva += $reg['iva'];
		$ret_iva += $reg['ivaret'];
		$ret_isr += $reg['isr'];
		$fletes += $reg['fletes'];
		$otros += $reg['otros'];

		$gran_importe += $reg['importe'];
		$gran_faltantes += $reg['faltantes'];
		$gran_dif_precio += $reg['dif_precio'];
		$gran_dev += $tmp_dev;
		$gran_descuentos += $desc;
		$gran_iva += $reg['iva'];
		$gran_ret_iva += $reg['ivaret'];
		$gran_ret_isr += $reg['isr'];
		$gran_fletes += $reg['fletes'];
		$gran_otros += $reg['otros'];

		$mega_gran_importe += $reg['importe'];
		$mega_gran_faltantes += $reg['faltantes'];
		$mega_gran_dif_precio += $reg['dif_precio'];
		$mega_gran_dev += $tmp_dev;
		$mega_gran_descuentos += $desc;
		$mega_gran_iva += $reg['iva'];
		$mega_gran_ret_iva += $reg['ivaret'];
		$mega_gran_ret_isr += $reg['isr'];
		$mega_gran_fletes += $reg['fletes'];
		$mega_gran_otros += $reg['otros'];

		$total += $reg['total'];
		$gran_total += $reg['total'];
		$mega_gran_total += $reg['total'];

		$tpl->assign('subbloque.importe', number_format($importe, 2, '.', ','));
		$tpl->assign('bloque.importe', number_format($gran_importe, 2, '.', ','));
		$tpl->assign('subbloque.faltantes', number_format($faltantes, 2, '.', ','));
		$tpl->assign('bloque.faltantes', number_format($gran_faltantes, 2, '.', ','));
		$tpl->assign('subbloque.dif_precio', number_format($dif_precio, 2, '.', ','));
		$tpl->assign('bloque.dif_precio', number_format($gran_dif_precio, 2, '.', ','));
		$tpl->assign('subbloque.dev', number_format($dev, 2, '.', ','));
		$tpl->assign('bloque.dev', number_format($gran_dev, 2, '.', ','));
		$tpl->assign('subbloque.descuentos', number_format($descuentos, 2, '.', ','));
		$tpl->assign('bloque.descuentos', number_format($gran_descuentos, 2, '.', ','));
		$tpl->assign('subbloque.iva', number_format($iva, 2, '.', ','));
		$tpl->assign('bloque.iva', number_format($gran_iva, 2, '.', ','));
		$tpl->assign('subbloque.ret_iva', number_format($ret_iva, 2, '.', ','));
		$tpl->assign('subbloque.ret_isr', number_format($ret_isr, 2, '.', ','));
		$tpl->assign('bloque.ret_iva', number_format($gran_ret_iva, 2, '.', ','));
		$tpl->assign('bloque.ret_isr', number_format($gran_ret_isr, 2, '.', ','));
		$tpl->assign('subbloque.fletes', number_format($fletes, 2, '.', ','));
		$tpl->assign('bloque.fletes', number_format($gran_fletes, 2, '.', ','));
		$tpl->assign('subbloque.otros', number_format($otros, 2, '.', ','));
		$tpl->assign('bloque.otros', number_format($gran_otros, 2, '.', ','));

		$tpl->assign('subbloque.total', number_format($total, 2, '.', ','));
		$tpl->assign('bloque.total', number_format($gran_total, 2, '.', ','));
	}
	$tpl->assign('result.importe', number_format($mega_gran_importe, 2, '.', ','));
	$tpl->assign('result.faltantes', number_format($mega_gran_faltantes, 2, '.', ','));
	$tpl->assign('result.dif_precio', number_format($mega_gran_dif_precio, 2, '.', ','));
	$tpl->assign('result.dev', number_format($mega_gran_dev, 2, '.', ','));
	$tpl->assign('result.descuentos', number_format($mega_gran_descuentos, 2, '.', ','));
	$tpl->assign('result.iva', number_format($mega_gran_iva, 2, '.', ','));
	$tpl->assign('result.ret_iva', number_format($mega_gran_ret_iva, 2, '.', ','));
	$tpl->assign('result.ret_isr', number_format($mega_gran_ret_isr, 2, '.', ','));
	$tpl->assign('result.fletes', number_format($mega_gran_fletes, 2, '.', ','));
	$tpl->assign('result.otros', number_format($mega_gran_otros, 2, '.', ','));
	$tpl->assign('result.total', number_format($mega_gran_total, 2, '.', ','));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query("SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos");
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', $reg['desc']);
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
