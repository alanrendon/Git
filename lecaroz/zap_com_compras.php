<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_com_compras.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_FILES['archivo'])) {
	// Abrir archivo de datos
	$fp = fopen($_FILES['archivo']['tmp_name'], "r");
	
	$cod_desc = array();
	foreach ($_POST['cod_desc'] as $c)
		if ($c > 0)
			$cod_desc[] = $c;
	
	$facs = array();
	$cont = 0;
	$num_cia = NULL;
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'], 1, $_POST['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'] + 1, 0, $_POST['anio']));
	
	while (!feof($fp)) {
		$buffer = fgets($fp);
		
		// Si el buffer esta vacio, pasar a la siguiente linea del archivo
		if (trim($buffer) == '')
			continue;
		
		// Partir linea a partir del patron 'tabulador (\t)'
		$data = split("\t", $buffer);
		
		// Si el primer elemento de la linea contiene la palabra 'SUCURSAL' tomar el primer número del siguiente elemento
		// y sumarle 900 para componer el número de compañía
		if (trim($data[0]) == 'SUCURSAL') {
			preg_match('/^(\d{1,})/', trim($data[1]), $tmp);
			$num_cia = intval($tmp[1], 10) >= 900 ? intval($tmp[1], 10) : intval($tmp[1], 10) + 900;
		}
		// Si el primer elemento de la linea contiene algun dato sobre el número de factura o remision desglosarlo
		else if (preg_match('/^([F|R]|S\/N|RECUP X DEVOLUC)?[\-\s]?[A-Z]?(\d{1,})?[\/]?(\d{1,})?[\/]?(\d{1,})?\s?([F|R])?[\-\s]?(\d{1,})?/', trim($data[0]), $doc) > 0 && count($doc) > 1) {
			$tipo_doc = '';	// Almacena el tipo de documento
			
			$fecha = $data[1];
			$prov = $data[4];
			$cond = $data[5];
			$cantidad = intval($data[6], 10);
			$importe = floatval(preg_replace('/[^\d\.]/', '', $data[7]));
			
			$div = 0;
			for ($i = 1; $i < count($doc); $i++)
				if (preg_match('/^\d+$/', $doc[$i]) > 0)
					$div++;
			
			$num_doc_tmp = NULL;
			for ($i = 1; $i < count($doc); $i++) {
				$num_doc = '';
				
				// Documento sin tipo y sin número
				if (preg_match('/^S\/N$/', $doc[$i]) > 0) {
					$tipo_doc = '';
				}
				else if (preg_match('/^RECUP X DEVOLUC$/', $doc[$i]) > 0) {
					$tipo_doc = '';
				}
				// Determinar el tipo de documento
				else if (preg_match('/^F|R$/', $doc[$i]) > 0) {
					$tipo_doc = $doc[$i];
				}
				// Determinar el número de documento y crear nuevo registro de factura o remisión
				else if (preg_match('/^\d+$/', $doc[$i]) > 0) {
					if ($num_doc_tmp == NULL)
						$num_doc_tmp = $doc[$i];
					
					// Si hay número de documento anterior y la longitud del actual es menor
					// agregar sección faltante
					if ($num_doc_tmp != '' && strlen($num_doc_tmp) > strlen($doc[$i])) {
						// Obtener la diferencia en caracteres
						$dif = strlen($num_doc_tmp) - strlen($doc[$i]);
						
						// Concatenar sección del número de factura que falta
						$num_doc = intval(substr_replace($doc[$i], substr($num_doc_tmp, 0, $dif), 0, 0), 10);
						
						// Si la factura o remisión ya esta repetida, omitir registro
						if ($num_doc == $num_doc_tmp)
							continue;
					}
					// Número de documento
					else
						$num_doc = intval($doc[$i], 10);
					
					$facs[$cont]['num_cia'] = $num_cia;
					$facs[$cont]['tipo'] = $tipo_doc;
					$facs[$cont]['num'] = $num_doc;
					$facs[$cont]['fecha'] = $fecha;
					$facs[$cont]['prov'] = $prov;
					$facs[$cont]['cond'] = $cond;
					$facs[$cont]['cantidad'] = $cantidad;
					$facs[$cont]['importe'] = $div > 1 ? round($importe / $div, 2) : $importe;
					
					$cont++;
				}
			}
		}
	}
	//echo '<pre>' . print_r($facs, true) . '</pre>';die;
	//echo 'Número de registros = ' . count($facs) . '<br />';
	//$cont_ok = 0;
	//$cont_dif = 0;
	//$cont_no = 0;
	
	$tpl->newBlock('result');
	
	// Recorrer arreglo de facturas y remisiones
	$num_cia_ok = NULL;
	$num_cia_dif = NULL;
	$num_cia_no = NULL;
	$num_cia = NULL;
	$db->query('TRUNCATE facturas_zap_tmp');
	foreach ($facs as $i => $fac) {
		if ($num_cia != $fac['num_cia']) {
			if ($num_cia != NULL) {
				if ($ins != '')
					$db->query($ins);
				
				$sql = "SELECT num_proveedor, nombre, num_fact, fecha_inv, importe, f.desc1 + f.desc2 + f.desc3 + f.desc4 AS desc FROM facturas_zap f LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = $num_cia AND fecha_inv BETWEEN '$fecha1' AND '$fecha2' AND (num_proveedor, num_fact) NOT IN (SELECT num_pro, num_fact FROM facturas_zap_tmp WHERE num_cia = $num_cia) ORDER BY num_cia, num_proveedor, num_fact";
				$sis = $db->query($sql);
				
				if ($sis) {
					$tpl->newBlock('no_sis');
					
					$total_no_sis = array('importe' => 0, 'desc' => 0, 'subtotal' => 0, 'iva' => 0, 'total' => 0);
					foreach ($sis as $s) {
						$tpl->newBlock('fila_no_sis');
						$tpl->assign('num_fact', $s['num_fact']);
						$tpl->assign('num_pro', $s['num_proveedor']);
						$tpl->assign('nombre_pro', $s['nombre']);
						$tpl->assign('fecha', $s['fecha_inv']);
						$tpl->assign('importe', number_format($s['importe'], 2, '.', ','));
						$tpl->assign('desc', $s['desc'] > 0 ? number_format($s['desc'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('subtotal', number_format($s['importe'] - $s['desc'], 2, '.', ','));
						$tpl->assign('iva', number_format(round(($s['importe'] - $s['desc']) * 0.15, 2), 2, '.', ','));
						$tpl->assign('total', number_format(($s['importe'] - $s['desc']) + round(($s['importe'] - $s['desc']) * 0.15, 2), 2, '.', ','));
						
						$total_no_sis['importe'] += $s['importe'];
						$total_no_sis['desc'] += $s['desc'];
						$total_no_sis['subtotal'] += $s['importe'] - $s['desc'];
						$total_no_sis['iva'] += round(($s['importe'] - $s['desc']) * 0.15, 2);
						$total_no_sis['total'] += ($s['importe'] - $s['desc']) + round(($s['importe'] - $s['desc']) * 0.15, 2);
						
						$tpl->assign('no_sis.importe', number_format($total_no_sis['importe'], 2, '.', ','));
						$tpl->assign('no_sis.desc', number_format($total_no_sis['desc'], 2, '.', ','));
						$tpl->assign('no_sis.subtotal', number_format($total_no_sis['subtotal'], 2, '.', ','));
						$tpl->assign('no_sis.iva', number_format($total_no_sis['iva'], 2, '.', ','));
						$tpl->assign('no_sis.total', number_format($total_no_sis['total'], 2, '.', ','));
					}
				}
			}
			
			$num_cia = $fac['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign('nombre_cia', $nombre[0]['nombre_corto']);
			
			$total_ok = array('cantidad' => 0, 'desc1' => 0, 'desc2' => 0, 'desc3' => 0, 'desc4' => 0, 'importe_inv' => 0, 'importe_sis' => 0, 'dif' => 0);
			$total_dif = array('cantidad' => 0, 'desc1' => 0, 'desc2' => 0, 'desc3' => 0, 'desc4' => 0, 'importe_inv' => 0, 'importe_sis' => 0, 'dif' => 0);
			$total_no = array('cantidad' => 0, 'subtotal' => 0, 'iva' => 0, 'total' => 0);
			
			$ins = '';
		}
		
		// Buscar factura/remisión en el sistema
		if ($fac['num'] > 0) {
			$sql = "SELECT num_cia, num_proveedor, nombre, num_fact, fecha, fecha_rec, fecha_inv, concepto, importe, f.cod_desc1, f.pdesc1, NULL AS desc1, f.cod_desc2, f.pdesc2, NULL AS desc2, f.cod_desc3, f.pdesc3, NULL AS desc3, f.cod_desc4, f.pdesc4, NULL AS desc4 FROM facturas_zap f LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = $fac[num_cia] AND num_fact = $fac[num]/* AND importe BETWEEN $fac[importe] - 999 AND $fac[importe] + 999*/";
			$reg = $db->query($sql);
			
			if ($reg) {
				// Si hay descuentos autorizados, aplicarlos a las facturas segun sea el caso
				if (count($cod_desc) > 0) {
					$imp = $reg[0]['importe'];	// Almacena temporalmente el importe de la factura
					
					// Recorrer códigos de descuento de la factura y si se encuentra dentro de los códigos autorizados calcularlos
					for ($d = 1; $d <= 4; $d++)
						if (in_array($reg[0]['cod_desc' . $d], $cod_desc) && $reg[0]['pdesc' . $d] > 0) {
							$desc = round($imp * $reg[0]['pdesc' . $d] / 100, 2);
							$imp = $imp - $desc;
							
							$reg[0]['desc' . $d] = $desc;
						}
				}
				
				if ($fac['importe'] == floatval($reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4'])) {
					if ($num_cia_ok != $fac['num_cia']) {
						$num_cia_ok = $fac['num_cia'];
						
						//$tpl->newBlock('ok');
					}
					$tpl->newBlock('fila_ok');
					$tpl->assign('num_fact', "$fac[tipo]-$fac[num]");
					$tpl->assign('num_pro', $reg[0]['num_proveedor']);
					$tpl->assign('nombre_pro', $reg[0]['nombre']);
					$tpl->assign('fecha_inv', $fac['fecha']);
					$tpl->assign('fecha_sis', $reg[0]['fecha_inv']);
					$tpl->assign('cantidad', $fac['cantidad']);
					$tpl->assign('desc1', $reg[0]['desc1'] > 0 ? $reg[0]['desc1'] : '&nbsp;');
					$tpl->assign('desc2', $reg[0]['desc2'] > 0 ? $reg[0]['desc2'] : '&nbsp;');
					$tpl->assign('desc3', $reg[0]['desc3'] > 0 ? $reg[0]['desc3'] : '&nbsp;');
					$tpl->assign('desc4', $reg[0]['desc4'] > 0 ? $reg[0]['desc4'] : '&nbsp;');
					$tpl->assign('importe_inv', number_format($fac['importe'], 2, '.', ','));
					$tpl->assign('importe_sis', number_format($reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4'], 2, '.', ','));
					$dif = $fac['importe'] - floatval($reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4']);
					$tpl->assign('dif', number_format($dif, 2, '.', ','));
					
					$total_ok['desc1'] += $reg[0]['desc1'];
					$total_ok['desc2'] += $reg[0]['desc2'];
					$total_ok['desc3'] += $reg[0]['desc3'];
					$total_ok['desc4'] += $reg[0]['desc4'];
					$total_ok['cantidad'] += $fac['cantidad'];
					$total_ok['importe_inv'] += $fac['importe'];
					$total_ok['importe_sis'] += $reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4'];
					$total_ok['dif'] += $dif;
					
					$tpl->assign(/*'cia_ok.cantidad'*/'ok.cantidad_ok', number_format($total_ok['cantidad']));
					$tpl->assign(/*'cia_ok.desc1'*/'ok.desc1_ok', number_format($total_ok['desc1'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.desc2'*/'ok.desc2_ok', number_format($total_ok['desc2'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.desc3'*/'ok.desc3_ok', number_format($total_ok['desc3'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.desc4'*/'ok.desc4_ok', number_format($total_ok['desc4'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.importe_inv'*/'ok.importe_inv_ok', number_format($total_ok['importe_inv'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.importe_sis'*/'ok.importe_sis_ok', number_format($total_ok['importe_sis'], 2, '.', ','));
					$tpl->assign(/*'cia_ok.dif'*/'ok.dif_ok', number_format($total_ok['dif'], 2, '.', ','));
					
					$ins .= 'INSERT INTO facturas_zap_tmp (num_cia, num_pro, nombre_pro, num_fact, fecha, cantidad, importe, iduser) VALUES';
					$ins .= " ($num_cia, {$reg[0]['num_proveedor']}, '$fac[prov]', $fac[num], '$fac[fecha]', $fac[cantidad], $fac[importe], $_SESSION[iduser]);\n";
					
					//$cont_ok++;
				}
				else {
					if ($num_cia_dif != $fac['num_cia']) {
						$num_cia_dif = $fac['num_cia'];
						
						//$tpl->newBlock('dif');
					}
					$tpl->newBlock('fila_dif');
					$tpl->assign('num_fact', "$fac[tipo]-$fac[num]");
					$tpl->assign('num_pro', $reg[0]['num_proveedor']);
					$tpl->assign('nombre_pro', $reg[0]['nombre']);
					$tpl->assign('fecha_inv', $fac['fecha']);
					$tpl->assign('fecha_sis', $reg[0]['fecha_inv']);
					$tpl->assign('cantidad', $fac['cantidad']);
					$tpl->assign('desc1', $reg[0]['desc1'] > 0 ? $reg[0]['desc1'] : '&nbsp;');
					$tpl->assign('desc2', $reg[0]['desc2'] > 0 ? $reg[0]['desc2'] : '&nbsp;');
					$tpl->assign('desc3', $reg[0]['desc3'] > 0 ? $reg[0]['desc3'] : '&nbsp;');
					$tpl->assign('desc4', $reg[0]['desc4'] > 0 ? $reg[0]['desc4'] : '&nbsp;');
					$tpl->assign('importe_inv', number_format($fac['importe'], 2, '.', ','));
					$tpl->assign('importe_sis', number_format($reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4'], 2, '.', ','));
					$dif = $fac['importe'] - floatval($reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4']);
					$tpl->assign('dif', number_format($dif, 2, '.', ','));
					
					$total_dif['desc1'] += $reg[0]['desc1'];
					$total_dif['desc2'] += $reg[0]['desc2'];
					$total_dif['desc3'] += $reg[0]['desc3'];
					$total_dif['desc4'] += $reg[0]['desc4'];
					$total_dif['cantidad'] += $fac['cantidad'];
					$total_dif['importe_inv'] += $fac['importe'];
					$total_dif['importe_sis'] += $reg[0]['importe'] - $reg[0]['desc1'] - $reg[0]['desc2'] - $reg[0]['desc3'] - $reg[0]['desc4'];
					$total_dif['dif'] += $dif;
					
					$tpl->assign(/*'cia_dif.cantidad'*/'dif.cantidad_dif', number_format($total_dif['cantidad']));
					$tpl->assign(/*'cia_dif.desc1'*/'dif.desc1_dif', number_format($total_dif['desc1'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.desc2'*/'dif.desc2_dif', number_format($total_dif['desc2'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.desc3'*/'dif.desc3_dif', number_format($total_dif['desc3'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.desc4'*/'dif.desc4_dif', number_format($total_dif['desc4'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.importe_inv'*/'dif.importe_inv_dif', number_format($total_dif['importe_inv'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.importe_sis'*/'dif.importe_sis_dif', number_format($total_dif['importe_sis'], 2, '.', ','));
					$tpl->assign(/*'cia_dif.dif'*/'dif.dif_dif', number_format($total_dif['dif'], 2, '.', ','));
					
					$ins .= 'INSERT INTO facturas_zap_tmp (num_cia, num_pro, nombre_pro, num_fact, fecha, cantidad, importe, iduser) VALUES';
					$ins .= " ($num_cia, {$reg[0]['num_proveedor']}, '$fac[prov]', $fac[num], '$fac[fecha]', $fac[cantidad], $fac[importe], $_SESSION[iduser]);\n";
					
					//$cont_dif++;
				}
			}
			else {
				if ($num_cia_no != $fac['num_cia']) {
					$num_cia_no = $fac['num_cia'];
					
					$tpl->newBlock('no');
				}
				$tpl->newBlock('fila_no');
				$tpl->assign('num_fact', "$fac[tipo]-$fac[num]");
				$tpl->assign('nombre_pro', $fac['prov']);
				$tpl->assign('fecha', $fac['fecha']);
				$tpl->assign('cond', $fac['cond']);
				$tpl->assign('cantidad', $fac['cantidad']);
				$tpl->assign('subtotal', number_format($fac['importe'], 2, '.', ','));
				$iva = $fac['importe'] * 0.15;
				$tpl->assign('iva', number_format($iva, 2, '.', ','));
				$total = $fac['importe'] + $iva;
				$tpl->assign('total', number_format($total, 2, '.', ','));
				
				$total_no['cantidad'] += $fac['cantidad'];
				$total_no['subtotal'] += $fac['importe'];
				$total_no['iva'] += $iva;
				$total_no['total'] += $total;
				
				$tpl->assign(/*'cia_no.cantidad'*/'no.cantidad_no', number_format($total_no['cantidad']));
				$tpl->assign(/*'cia_no.subtotal'*/'no.subtotal', number_format($total_no['subtotal'], 2, '.', ','));
				$tpl->assign(/*'cia_no.iva'*/'no.iva', number_format($total_no['iva'], 2, '.', ','));
				$tpl->assign(/*'cia_no.total'*/'no.total', number_format($total_no['total'], 2, '.', ','));
				
				$ins .= 'INSERT INTO facturas_zap_tmp (num_cia, num_pro, nombre_pro, num_fact, fecha, cantidad, importe, iduser) VALUES';
				$ins .= " ($num_cia, NULL, '$fac[prov]', " . ($fac['num'] > 0 ? $fac['num'] : 'NULL') . ", '$fac[fecha]', $fac[cantidad], $fac[importe], $_SESSION[iduser]);\n";
				
				//$cont_no++;
			}
		}
		else {
			if ($num_cia_no != $fac['num_cia']) {
				$num_cia_no = $fac['num_cia'];
				
				$tpl->newBlock('no');
			}
			$tpl->newBlock('fila_no');
			$tpl->assign('num_fact', "$fac[tipo]-$fac[num]");
			$tpl->assign('nombre_pro', $fac['prov']);
			$tpl->assign('fecha', $fac['fecha']);
			$tpl->assign('cond', $fac['cond']);
			$tpl->assign('cantidad', $fac['cantidad']);
			$tpl->assign('subtotal', number_format($fac['importe'], 2, '.', ','));
			$iva = $fac['importe'] * 0.15;
			$tpl->assign('iva', number_format($iva, 2, '.', ','));
			$total = $fac['importe'] + $iva;
			$tpl->assign('total', number_format($total, 2, '.', ','));
			
			$total_no['cantidad'] += $fac['cantidad'];
			$total_no['subtotal'] += $fac['importe'];
			$total_no['iva'] += $iva;
			$total_no['total'] += $total;
			
			$tpl->assign('no.cantidad', number_format($total_no['cantidad']));
			$tpl->assign('no.subtotal', number_format($total_no['subtotal'], 2, '.', ','));
			$tpl->assign('no.iva', number_format($total_no['iva'], 2, '.', ','));
			$tpl->assign('no.total', number_format($total_no['total'], 2, '.', ','));
			
			$ins .= 'INSERT INTO facturas_zap_tmp (num_cia, num_pro, nombre_pro, num_fact, fecha, cantidad, importe, iduser) VALUES';
			$ins .= " ($num_cia, NULL, '$fac[prov]', " . ($fac['num'] > 0 ? $fac['num'] : 'NULL') . ", '$fac[fecha]', $fac[cantidad], $fac[importe], $_SESSION[iduser]);\n";
			
			//$cont_no++;
		}
	}
	if ($num_cia != NULL) {
		if ($ins != '')
			$db->query($ins);
		
		$sql = "SELECT num_proveedor, nombre, num_fact, fecha_inv, importe, f.desc1 + f.desc2 + f.desc3 + f.desc4 AS desc FROM facturas_zap f LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = $num_cia AND fecha_inv BETWEEN '$fecha1' AND '$fecha2' AND (num_proveedor, num_fact) NOT IN (SELECT num_pro, num_fact FROM facturas_zap_tmp WHERE num_cia = $num_cia) ORDER BY num_cia, num_proveedor, num_fact";
		$sis = $db->query($sql);
		
		if ($sis) {
			$tpl->newBlock('no_sis');
			
			$total_no_sis = array('importe' => 0, 'desc' => 0, 'subtotal' => 0, 'iva' => 0, 'total' => 0);
			foreach ($sis as $s) {
				$tpl->newBlock('fila_no_sis');
				$tpl->assign('num_fact', $s['num_fact']);
				$tpl->assign('num_pro', $s['num_proveedor']);
				$tpl->assign('nombre_pro', $s['nombre']);
				$tpl->assign('fecha', $s['fecha_inv']);
				$tpl->assign('importe', number_format($s['importe'], 2, '.', ','));
				$tpl->assign('desc', $s['desc'] > 0 ? number_format($s['desc'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('subtotal', number_format($s['importe'] - $s['desc'], 2, '.', ','));
				$tpl->assign('iva', number_format(round(($s['importe'] - $s['desc']) * 0.15, 2), 2, '.', ','));
				$tpl->assign('total', number_format(($s['importe'] - $s['desc']) + round(($s['importe'] - $s['desc']) * 0.15, 2), 2, '.', ','));
				
				$total_no_sis['importe'] += $s['importe'];
				$total_no_sis['desc'] += $s['desc'];
				$total_no_sis['subtotal'] += $s['importe'] - $s['desc'];
				$total_no_sis['iva'] += round(($s['importe'] - $s['desc']) * 0.15, 2);
				$total_no_sis['total'] += ($s['importe'] - $s['desc']) + round(($s['importe'] - $s['desc']) * 0.15, 2);
				
				$tpl->assign('no_sis.importe', number_format($total_no_sis['importe'], 2, '.', ','));
				$tpl->assign('no_sis.desc', number_format($total_no_sis['desc'], 2, '.', ','));
				$tpl->assign('no_sis.subtotal', number_format($total_no_sis['subtotal'], 2, '.', ','));
				$tpl->assign('no_sis.iva', number_format($total_no_sis['iva'], 2, '.', ','));
				$tpl->assign('no_sis.total', number_format($total_no_sis['total'], 2, '.', ','));
			}
		}
	}
	
	//echo 'Número de registros iguales = ' . $cont_ok . '<br />';
	//echo 'Número de registros diferentes = ' . $cont_dif . '<br />';
	//echo 'Número de registros no encontrados = ' . $cont_no . '<br />';
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$result = $db->query('SELECT cod, concepto FROM cat_conceptos_descuentos ORDER BY cod');
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', "$reg[concepto]");
}

$tpl->printToScreen();
?>