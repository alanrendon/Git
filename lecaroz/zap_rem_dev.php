<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No hay devoluciones por aplicar";
$descripcion_error[3] = "No se pudo descontar devoluciones a las remisiones";

if (isset($_POST['devs'])) {
	// Ultimo folio de devoluciones
	$tmp = $db->query('SELECT folio FROM devoluciones_zap WHERE folio > 0 ORDER BY folio DESC LIMIT 1');
	$folio_dev = $tmp ? $tmp[0]['folio'] + 1 : 1;
	
	$sql = '';
	for ($i = 0; $i < count($_POST['devs']); $i++)
		if (isset($_POST['id' . $i])) {
			$sql .= "UPDATE facturas_zap SET dev = " . get_val($_POST['dev'][$i]) . ", dev_apl = '" . substr($_POST['devs'][$i], 0, strlen($_POST['devs'][$i]) - 2) . "', desc1 = " . get_val($_POST['desc1'][$i]) . ", desc2 = " . get_val($_POST['desc2'][$i]) . ", desc3 = " . get_val($_POST['desc3'][$i]) . ", desc4 = " . get_val($_POST['desc4'][$i]) . ", total = " . get_val($_POST['total'][$i]) . ", imp = 'TRUE' WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = '{$_POST['num_fact'][$i]}' AND fecha = '{$_POST['fecha'][$i]}';\n";
			$ids = explode(',', $_POST['devs'][$i]);
			$sql .= "UPDATE devoluciones_zap SET folio_cheque = 0, cuenta = 0, num_cia_cheque = {$_POST['num_cia'][$i]}, num_fact = '{$_POST['num_fact'][$i]}', fecha_fac = '{$_POST['fecha'][$i]}', imp = 'TRUE', folio = $folio_dev WHERE id IN (";
			foreach ($ids as $j => $id)
				if ($id > 0)
					$sql .= $id . ($j < count($ids) - 2 ? ', ' : ");\n");
			
			$folio_dev++;
		}
	
	$db->query($sql);
	die(header('location: ./zap_rem_dev.php?list=1'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_rem_dev.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['list'])) {
	$sql = "SELECT id, num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, clave, cp.nombre AS nombre_pro, num_fact, fecha, importe, faltantes, dif_precio, dev, pdesc1, pdesc2, pdesc3, pdesc4, fz.desc1, fz.desc2, fz.desc3, fz.desc4, iva, fletes, otros, total, dev_apl FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE imp = 'TRUE' ORDER BY num_cia, num_pro";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./zap_rem_dev.php'));
	
	$db->query("UPDATE facturas_zap SET imp = 'FALSE' WHERE imp = 'TRUE'");
	
	$tpl->newBlock('listado');
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anio', date('Y'));
	
	$num_cia = NULL;
	$num_pro = NULL;
	foreach ($result as $i => $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia_imp');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_cia']);
			
			$total_cia = 0;
		}
		if ($num_pro != $reg['num_pro']) {
			$num_pro = $reg['num_pro'];
			
			$tpl->newBlock('pro_imp');
			$total_pro = 0;
		}
		$tpl->newBlock('fac_imp');
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('clave', $reg['clave']);
		$tpl->assign('nombre', $reg['nombre_pro']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('faltantes', $reg['faltantes'] > 0 ? number_format($reg['faltantes'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('dif_precio', $reg['dif_precio'] > 0 ? number_format($reg['dif_precio'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('dev', number_format($reg['dev'], 2, '.', ','));
		$desc = $reg['desc1'] + $reg['desc2'] + $reg['desc3'] + $reg['desc4'];
		$tpl->assign('desc', $desc > 0 ? number_format($desc, 2, '.', ',') : '&nbsp;');
		$tpl->assign('iva', $reg['iva'] > 0 ? number_format($reg['iva'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('fletes', $reg['fletes'] > 0 ? number_format($reg['fletes'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('otros', $reg['otros'] > 0 ? number_format($reg['otros'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('total', number_format($reg['total'], 2, '.', ','));
		
		$total_pro += $reg['total'];
		$tpl->assign('pro_imp.total', number_format($total_pro, 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, clave, cp.nombre AS nombre_pro, num_fact, fecha, importe, faltantes, dif_precio, 0 AS dev, pdesc1, pdesc2, pdesc3, pdesc4, fz.desc1, fz.desc2, fz.desc3, fz.desc4, iva, fletes, otros, total, dev_apl FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE por_aut = 'TRUE' AND copia_fac = 'TRUE' AND clave > 0 AND fz.sucursal = 'FALSE' AND tspago IS NULL AND dev_apl IS NULL AND fz.num_proveedor NOT IN (SELECT num_proveedor FROM facturas_zap WHERE clave = 0 AND sucursal = 'FALSE' AND tspago IS NULL GROUP BY num_proveedor)";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND fz.num_proveedor = $_GET[num_pro]" : '';
	$num_fact = array();
	foreach ($_GET['num_fact'] as $num)
		if ($num > 0)
			$num_fact[] = $num;
	if (count($num_fact) > 0) {
		$sql .= " AND num_fact IN ('";
		foreach ($num_fact as $i => $num)
			$sql .= $num . ($i < count($num_fact) - 1 ? "', '" : "')");
	}
	$sql .= $_GET['fecha1'] != '' ? " AND fecha " . ($_GET['fecha2'] != '' ? "BETWEEN '$_GET[fecha1]' AND 'fecha2'" : " = '$_GET[fecha1]'") : '';
	$sql .= " ORDER BY num_cia, num_pro";
	$result = $db->query($sql);//echo $sql;print_r($result);die;
	
	if (!$result) die(header('location: ./zap_rem_dev.php?codigo_error=1'));
	
	// Obtener las devoluciones que se encuentren dentro de las remisiones a descontar
	$sql = "SELECT id, num_cia, num_proveedor, importe FROM devoluciones_zap WHERE num_proveedor IN (SELECT num_proveedor FROM facturas_zap WHERE id IN (";
	foreach ($result as $i => $reg)
		$sql .= $reg['id'] . ($i < count($result) - 1 ? ', ' : ')');
	$sql .= " GROUP BY num_proveedor) AND folio_cheque IS NULL ORDER BY num_cia, num_proveedor";
	$tmp = $db->query($sql);
	
	if (!$tmp) die(header('location: ./zap_rem_dev.php?codigo_error=2'));
	
	$dev = array();
	$cia = NULL;
	$pro = NULL;
	foreach ($tmp as $d) {
		if ($cia != $d['num_cia'])
			$cia = $d['num_cia'];
		if ($pro != $d['num_proveedor'])
			$pro = $d['num_proveedor'];
		$dev[$cia][$pro][$d['id']] = array('importe' => $d['importe'], 'usado' => FALSE);
	}
	
	$dev_rez = array();
	$no_fac = array();
	foreach ($result as $reg)
		if (!isset($no_fac[$reg['num_cia']][$reg['num_pro']]))
			$no_fac[$reg['num_cia']][$reg['num_pro']] = true;
	foreach ($dev as $cia => $pro)
		foreach ($pro as $num => $reg)
			if (!isset($no_fac[$cia][$num]))
				foreach ($reg as $id => $r)
					$dev_rez[$num][$id] = $r;
	
	$dev_cont = 0;
	foreach ($result as $i => $factura) {
		$importe_dev = 0;
		if (isset($dev[$factura['num_cia']][$factura['num_pro']]))
			// Descontar devoluciones misma compañía
			foreach ($dev[$factura['num_cia']][$factura['num_pro']] as $id => $reg)
				if (!$reg['usado']) {
					$subimporte = $factura['importe'] - $factura['faltantes'] - $factura['dif_precio'] - $importe_dev - $reg['importe'];
					$desc1 = $factura['pdesc1'] > 0 ? round($subimporte * $factura['pdesc1'] / 100, 2) : ($factura['desc1'] > 0 ? $factura['desc1'] : 0);
					$desc2 = $factura['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura['pdesc2'] / 100, 2) : ($factura['desc2'] > 0 ? $factura['desc2'] : 0);
					$desc3 = $factura['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura['pdesc3'] / 100, 2) : ($factura['desc3'] > 0 ? $factura[$i]['desc3'] : 0);
					$desc4 = $factura['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura['pdesc4'] / 100, 2) : ($factura['desc4'] > 0 ? $factura['desc4'] : 0);
					$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
					$iva = $factura['iva'] > 0 ? $subtotal * 0.15 : 0;
					$total_tmp = $subtotal + $iva - $factura['fletes'] + $factura['otros'];
					
					if ($total_tmp > 0) {
						$importe_dev += $reg['importe'];
						$dev[$factura['num_cia']][$factura['num_pro']][$id]['usado'] = TRUE;
						$result[$i]['dev_apl'] .= "$id,";
						$dev_cont++;
					}
				}
			
		// Descontar devoluciones otra compañía
		if (isset($dev_rez[$factura['num_pro']]))
			foreach ($dev_rez[$factura['num_pro']] as $id => $reg)
				if (!$reg['usado']) {
					$subimporte = $factura['importe'] - $factura['faltantes'] - $factura['dif_precio'] - $importe_dev - $reg['importe'];
					$desc1 = $factura['pdesc1'] > 0 ? round($subimporte * $factura['pdesc1'] / 100, 2) : ($factura['desc1'] > 0 ? $factura['desc1'] : 0);
					$desc2 = $factura['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura['pdesc2'] / 100, 2) : ($factura['desc2'] > 0 ? $factura['desc2'] : 0);
					$desc3 = $factura['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura['pdesc3'] / 100, 2) : ($factura['desc3'] > 0 ? $factura['desc3'] : 0);
					$desc4 = $factura['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura['pdesc4'] / 100, 2) : ($factura['desc4'] > 0 ? $factura['desc4'] : 0);
					$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
					$iva = $factura['iva'] > 0 ? $subtotal * 0.15 : 0;
					$total_tmp = $subtotal + $iva - $factura['fletes'] + $factura['otros'];
					if ($total_tmp > 0) {
						$importe_dev += $reg['importe'];
						$dev_rez[$factura['num_pro']][$id]['usado'] = TRUE;
						$result[$i]['dev_apl'] .= "$id,";
						$dev_cont++;
					}
				}
			
		// Recalcular total de la factura
		if ($importe_dev > 0) {
			$subimporte = $factura['importe'] - $factura['faltantes'] - $factura['dif_precio'] - $importe_dev;
			$desc1 = $factura['pdesc1'] > 0 ? round($subimporte * $factura['pdesc1'] / 100, 2) : ($factura['desc1'] > 0 ? $factura['desc1'] : 0);
			$desc2 = $factura['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura['pdesc2'] / 100, 2) : ($factura['desc2'] > 0 ? $factura['desc2'] : 0);
			$desc3 = $factura['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura['pdesc3'] / 100, 2) : ($factura['desc3'] > 0 ? $factura['desc3'] : 0);
			$desc4 = $factura['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura['pdesc4'] / 100, 2) : ($factura['desc4'] > 0 ? $factura['desc4'] : 0);
			$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
			$iva = $factura['iva'] > 0 ? $subtotal * 0.15 : 0;
			$total_fac = $subtotal + $iva - $factura['fletes'] + $factura['otros'];
			
			$result[$i]['desc1'] = $desc1;
			$result[$i]['desc2'] = $desc2;
			$result[$i]['desc3'] = $desc3;
			$result[$i]['desc4'] = $desc4;
			$result[$i]['iva'] = $iva;
			$result[$i]['total'] = $total_fac;
			$result[$i]['dev'] = $importe_dev;
		}
	}
	
	if ($dev_cont == 0) die(header('location: ./zap_rem_dev.php?codigo_error=3'));
	
	$tpl->newBlock('resultado');
	
	$num_cia = NULL;
	$i = 0;
	foreach ($result as $reg) {
		if ($reg['dev'] > 0) {
			if ($num_cia != $reg['num_cia']) {
				$num_cia = $reg['num_cia'];
				
				$tpl->newBlock('cia');
				$tpl->assign('num_cia', $reg['num_cia']);
				$tpl->assign('nombre', $reg['nombre_cia']);
				
				$num_pro = NULL;
			}
			if ($num_pro != $reg['num_pro']) {
				$num_pro = $reg['num_pro'];
				
				$tpl->newBlock('pro');
			}
			$tpl->newBlock('fac');
			$tpl->assign('i', $i++);
			$tpl->assign('id', $reg['id']);
			$tpl->assign('devs', $reg['dev_apl']);
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('num_fact', $reg['num_fact']);
			$tpl->assign('num_pro', $reg['num_pro']);
			$tpl->assign('clave', $reg['clave']);
			$tpl->assign('nombre', $reg['nombre_pro']);
			$tpl->assign('fecha', $reg['fecha']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$tpl->assign('faltantes', $reg['faltantes'] > 0 ? number_format($reg['faltantes'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('dif_precio', $reg['dif_precio'] > 0 ? number_format($reg['dif_precio'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('dev', number_format($reg['dev'], 2, '.', ','));
			$desc = $reg['desc1'] + $reg['desc2'] + $reg['desc3'] + $reg['desc4'];
			$tpl->assign('desc1', $reg['desc1']);
			$tpl->assign('desc2', $reg['desc2']);
			$tpl->assign('desc3', $reg['desc3']);
			$tpl->assign('desc4', $reg['desc4']);
			$tpl->assign('desc', $desc > 0 ? number_format($desc, 2, '.', ',') : '&nbsp;');
			$tpl->assign('iva', $reg['iva'] > 0 ? number_format($reg['iva'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('fletes', $reg['fletes'] > 0 ? number_format($reg['fletes'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('otros', $reg['otros'] > 0 ? number_format($reg['otros'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('total', number_format($reg['total'], 2, '.', ','));
			
			//$
		}
	}
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia AS num, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT num_proveedor AS num, nombre FROM catalogo_proveedores WHERE clave_seguridad > 0 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
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