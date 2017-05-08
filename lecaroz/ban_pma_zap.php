<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// [20-Mar-2009] Obtener status de nota de crédito
if (isset($_GET['n'])) {
	$sql = "SELECT status, importe FROM notas_credito_zap WHERE num_proveedor = $_GET[p] AND folio = $_GET[n]";
	$result = $db->query($sql);

	if (!$result)
		echo '-1|' . $_GET['i'];
	else if ($result[0]['status'] == 0)
		echo '-2|' . $_GET['i'];
	else if ($result[0]['status'] == 2)
		echo '-3|' . $_GET['i'];
	else if ($_GET['imp'] < $result[0]['importe'])
		echo '-4|' . $_GET['i'];
	else
		echo '0|' . $_GET['i'] . '|' . $result[0]['importe'];
	die;
}

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

function pago_proveedores($factura) {
	global $dev, $notas;
	$query = "";			// Variable para almacenar todos los querys de pago a proveedores

	// Declaración de variables
	$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
	$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
	$monto_min    = 0;

	$num_cia = NULL;		// Última compañía revisada
	$num_proveedor = NULL;	// Último proveedor revisado
	$num_fac = 0;			// Número de facturas actual para el cheque
	$importe_cheque = 0;	// Importe del cheque

	$fac_count = 0;			// Contador de facturas
	$che_count = 0;			// Contador de cheques
	$trans_cont = 0;		// Contador de transferencias

	$cosgastos = NULL;		// Código de gasto para el cheque
	$nombre_gasto = NULL;	// Nombre del gasto para el cheque

	// Ultimo folio de devoluciones
	$tmp = $GLOBALS['db']->query('SELECT folio FROM devoluciones_zap WHERE folio > 0 ORDER BY folio DESC LIMIT 1');
	$folio_dev = $tmp ? $tmp[0]['folio'] + 1 : 1;

	// [28-Jun-2007] Contador de devoluciones
	$dev_cont = 0;

	// [16-Jun-2008] Almacenaje temporal de las devoluciones rezagadas
	$dev_rez = array();
	// [16-Jun-2008] Obtener devoluciones de tiendas y proveedores de los cuales no se ha pagado facturas
	$no_fac = array();
	foreach ($factura as $reg)
		if (!isset($no_fac[$reg['num_cia']][$reg['num_proveedor']]))
			$no_fac[$reg['num_cia']][$reg['num_proveedor']] = true;
	// [16-Jun-2008] Almacenar en el temporal de devoluciones rezagadas los registros que no esten dentro de las facturas que se han pagado
	foreach ($dev as $cia => $pro)
		foreach ($pro as $num => $reg)
			if (!isset($no_fac[$cia][$num]))
				foreach ($reg as $id => $r)
					$dev_rez[$num][$id] = $r;

	for ($i=0; $i<count($factura); $i++) {
		// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
		if ($factura[$i]['num_cia'] != $num_cia || $factura[$i]['num_proveedor'] != $num_proveedor || $num_fac == $fac_x_cheque) {
			// Organizar datos para almacenar cheque
			if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
				// Si el importe del cheque es mayor o igual al monto minimo, generarlo
				if ($importe_cheque >= $monto_min) {
					// Si existen devoluciones, restarlas al importe del cheque ([28-Jun-2007] Removido debido a que las devoluciones deben restarse antes de aplicar los descuentos en las facturas)
					/*if (isset($dev[$num_cia][$num_proveedor])) {
						$importe_dev = 0;
						foreach ($dev[$num_cia][$num_proveedor] as $id => $reg)
							if (!$reg['usado'] && $importe_dev + $reg['importe'] < $importe_cheque) {
								$importe_dev += $reg['importe'];
								$reg['usado'] = TRUE;
								$query .= "UPDATE devoluciones_zap SET folio = $folio_dev, folio_cheque = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, imp = 'TRUE' WHERE id = $id;\n";
							}
						if ($importe_dev > 0) {
							$importe_cheque = $importe_cheque - $importe_dev;
							$folio_dev++;
						}
					}*/

					// Datos para la tabla de 'cheques'
					$cheque[$che_count]['cod_mov']           = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
					$cheque[$che_count]['num_proveedor']     = $num_proveedor;
					$cheque[$che_count]['num_cia']           = $num_cia;
					$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
					$cheque[$che_count]['concepto']          = $nombre_gasto;
					$cheque[$che_count]['facturas']          = $facturas;
					$cheque[$che_count]['fecha']             = $_SESSION['pmp']['fecha_cheque'];
					$cheque[$che_count]['folio']             = $folio_cheque;
					$cheque[$che_count]['importe']           = number_format($importe_cheque, 2, ".", "");
					$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
					$cheque[$che_count]['imp']               = "FALSE";
					$cheque[$che_count]['codgastos']         = $codgastos;
					$cheque[$che_count]['proceso']           = "TRUE";
					$cheque[$che_count]['cuenta']            = $_SESSION['pmp']['cuenta'];
					$cheque[$che_count]['poliza']            = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? "TRUE" : "FALSE";

					if ($_SESSION['pmp']['cuenta'] == 2 && $trans) {
						$transfer[$trans_cont]['num_cia']       = $num_cia;
						$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
						$transfer[$trans_cont]['folio']         = $folio_cheque;
						$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
						$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
						$transfer[$trans_cont]['tipo']          = !$san ? "TRUE" : "FALSE";
						$transfer[$trans_cont]['status']        = "0";
						$transfer[$trans_cont]['folio_archivo'] = 0;
						$transfer[$trans_cont]['cuenta'] = 2;
						$transfer[$trans_cont]['gen_dep'] = 'TRUE';
						$trans_cont++;
					}
					else if ($_SESSION['pmp']['cuenta'] == 1 && $trans) {
						$transfer[$trans_cont]['num_cia']       = $num_cia;
						$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
						$transfer[$trans_cont]['folio']         = $folio_cheque;
						$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
						$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
						$transfer[$trans_cont]['tipo']          = "FALSE";
						$transfer[$trans_cont]['status']        = "0";
						$transfer[$trans_cont]['folio_archivo'] = 0;
						$transfer[$trans_cont]['cuenta']        = 1;
						$transfer[$trans_cont]['concepto']      = "PAGO FOLIO $folio_cheque";
						$transfer[$trans_cont]['gen_dep'] = 'TRUE';
						$trans_cont++;
					}

					// Datos para la tabla de 'estado_cuenta'
					$cuenta[$che_count]['num_cia'] = $num_cia;
					$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
					if ($importe_cheque == 0)
						$cuenta[$che_count]['fecha_con'] = $_SESSION['pmp']['fecha_cheque'];
					$cuenta[$che_count]['concepto'] = $facturas;
					$cuenta[$che_count]['tipo_mov'] = "TRUE";
					$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
					$cuenta[$che_count]['cod_mov'] = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
					$cuenta[$che_count]['folio'] = $folio_cheque;
					$cuenta[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

					// Datos para la tabla de folios_cheque
					$folio[$che_count]['folio']     = $folio_cheque;
					$folio[$che_count]['num_cia']   = $num_cia;
					$folio[$che_count]['reservado'] = "FALSE";
					$folio[$che_count]['utilizado'] = "TRUE";
					$folio[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
					$folio[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

					// Datos para la tabla de movimiento_gastos
					$gasto[$che_count]['codgastos'] = $codgastos;
					$gasto[$che_count]['num_cia']   = $num_cia;
					$gasto[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
					$gasto[$che_count]['importe']   = number_format($importe_cheque, 2, ".", "");
					$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
					$gasto[$che_count]['captura']   = "TRUE";
					$gasto[$che_count]['factura']   = "";
					$gasto[$che_count]['folio']     = $folio_cheque;

					// Actualizar facturas
					$query .= "UPDATE facturas_zap SET folio = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, tspago = now() WHERE id IN (";
					for ($f = 0; $f < $fac_count; $f++)
						$query .= $fac_upd[$f] . ($f < $fac_count - 1 ? "," : ");\n");

					// Actualizar saldo en libros
					$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']};\n";

					// [28-Jun-2007] Incrementar número de folio de vale de devolución
					if ($dev_cont > 0) $folio_dev++;

					// [28-Jun-2007] Reestablecer contador de devoluciones
					$dev_cont = 0;

					// Imcrementar número de folio
					$folio_cheque++;
					// Incrementar contador de cheques
					$che_count++;
				}
			}

			// Verificar el cambio de compañía
			if ($factura[$i]['num_cia'] != $num_cia) {
				// Cambiar la compañía
				$num_cia = $factura[$i]['num_cia'];
				// Resetear número de proveedor
				$num_proveedor = NULL;
				// Resetear contador de facturas a cero
				$num_fac = 0;
				// Resetear importe de cheque a cero
				$importe_cheque = 0;

				// Obtener el último folio para los cheques de esta cuenta
				$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia AND \"cuenta\" = {$_SESSION['pmp']['cuenta']} ORDER BY \"folio\" DESC LIMIT 1";
				$result = $GLOBALS['db']->query($sql);
				$folio_cheque = ($result) ? $result[0]['folio'] + 1 : 1;
			}

			// Cambiar proveedor
			$num_proveedor    = $factura[$i]['num_proveedor'];
			$nombre_proveedor = $factura[$i]['nombre'];
			$trans = $factura[$i]['trans'] == "t" ? TRUE : FALSE;
			$san = $factura[$i]['san'] == "t" ? TRUE : FALSE;
			$tipo_saldo = $factura[$i]['trans'] == "t" ? "saldo_real" : "saldo";

			// Si el proveedor es el 13 (Pollos Guerra) cambiar el número de facturas por cheque a 15, si no, regresarlo a 10
			if ($num_proveedor == 13)
				$fac_x_cheque = 22;
			else
				$fac_x_cheque = 10;

			// Vaciar variables que almacenan facturas
			unset($fac_pag);

			// Resetear contador de registros de facturas a cero
			$fac_count = 0;
			// Resetear contador de facturas a cero
			$num_fac = 0;
			// Resetear importe de cheque a cero
			$importe_cheque = 0;
			// Resetear concepto del cheque
			$facturas = "";
		}

		// Proceso de generado de cheque
		// Se considera el caso de el máximo número de facturas pagadas por un cheque

		// [28-Jun-2007] Descontar devoluciones de la factura antes de aplicar los descuentos
		// [16-Jun-2008] Modificado para que tome tambien las devoluciones de otras tiendas
		$importe_dev = 0;

		if (isset($dev[$num_cia][$num_proveedor])) {
			foreach ($dev[$num_cia][$num_proveedor] as $id => $reg)
				/*if (!$reg['usado'] && $importe_dev + $reg['importe'] < $factura[$i]['importe'] - $factura[$i]['faltantes']) {
					$importe_dev += $reg['importe'];
					$dev[$num_cia][$num_proveedor][$id]['usado'] = TRUE;
					$query .= "UPDATE devoluciones_zap SET num_fact = {$factura[$i]['num_fact']}, folio = $folio_dev, num_cia_cheque = $num_cia, folio_cheque =";
					$query .= " $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, imp = 'TRUE' WHERE id = $id;\n";
					$dev_cont++;
				}*/
				if (!$reg['usado']) {
					$subimporte = $factura[$i]['importe'] - $factura[$i]['faltantes'] - $factura[$i]['dif_precio'] - $importe_dev - $reg['importe'];
					$desc1 = $factura[$i]['pdesc1'] > 0 ? round($subimporte * $factura[$i]['pdesc1'] / 100, 2) : ($factura[$i]['desc1'] > 0 ? $factura[$i]['desc1'] : 0);
					$desc2 = $factura[$i]['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura[$i]['pdesc2'] / 100, 2) : ($factura[$i]['desc2'] > 0 ? $factura[$i]['desc2'] : 0);
					$desc3 = $factura[$i]['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura[$i]['pdesc3'] / 100, 2) : ($factura[$i]['desc3'] > 0 ? $factura[$i]['desc3'] : 0);
					$desc4 = $factura[$i]['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura[$i]['pdesc4'] / 100, 2) : ($factura[$i]['desc4'] > 0 ? $factura[$i]['desc4'] : 0);
					$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
					$iva = $factura[$i]['iva'] > 0 ? $subtotal * /*0.15*/0.16 : 0;
					$total_tmp = $subtotal + $iva - $factura[$i]['fletes'] + $factura[$i]['otros'];

					if ($total_tmp > 0) {
						$importe_dev += $reg['importe'];
						$dev[$num_cia][$num_proveedor][$id]['usado'] = TRUE;
						$query .= "UPDATE devoluciones_zap SET num_fact = '{$factura[$i]['num_fact']}', fecha_fac = '{$factura[$i]['fecha']}', folio = $folio_dev, num_cia_cheque = $num_cia, folio_cheque = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, imp = 'TRUE' WHERE id = $id;\n";
						$dev_cont++;
					}
				}

			// Recalcular total de la factura
			/*if ($importe_dev > 0) {
				$subimporte = $factura[$i]['importe'] - $factura[$i]['faltantes'] - $factura[$i]['dif_precio'] - $importe_dev;
				$desc1 = $factura[$i]['pdesc1'] > 0 ? round($subimporte * $factura[$i]['pdesc1'] / 100, 2) : ($factura[$i]['desc1'] > 0 ? $factura[$i]['desc1'] : 0);
				$desc2 = $factura[$i]['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura[$i]['pdesc2'] / 100, 2) : ($factura[$i]['desc2'] > 0 ? $factura[$i]['desc2'] : 0);
				$desc3 = $factura[$i]['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura[$i]['pdesc3'] / 100, 2) : ($factura[$i]['desc3'] > 0 ? $factura[$i]['desc3'] : 0);
				$desc4 = $factura[$i]['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura[$i]['pdesc4'] / 100, 2) : ($factura[$i]['desc4'] > 0 ? $factura[$i]['desc4'] : 0);
				$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
				$iva = $factura[$i]['iva'] > 0 ? $subtotal * 0.15 : 0;
				$total_fac = $subtotal + $iva - $factura[$i]['fletes'] + $factura[$i]['otros'];
			}
			else
				$total_fac = $factura[$i]['total'];*/
		}

		// Descontar devoluciones de otras tiendas
		if (isset($dev_rez[$num_proveedor]))
			foreach ($dev_rez[$num_proveedor] as $id => $reg)
				if (!$reg['usado']) {
					$subimporte = $factura[$i]['importe'] - $factura[$i]['faltantes'] - $factura[$i]['dif_precio'] - $importe_dev - $reg['importe'];
					$desc1 = $factura[$i]['pdesc1'] > 0 ? round($subimporte * $factura[$i]['pdesc1'] / 100, 2) : ($factura[$i]['desc1'] > 0 ? $factura[$i]['desc1'] : 0);
					$desc2 = $factura[$i]['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura[$i]['pdesc2'] / 100, 2) : ($factura[$i]['desc2'] > 0 ? $factura[$i]['desc2'] : 0);
					$desc3 = $factura[$i]['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura[$i]['pdesc3'] / 100, 2) : ($factura[$i]['desc3'] > 0 ? $factura[$i]['desc3'] : 0);
					$desc4 = $factura[$i]['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura[$i]['pdesc4'] / 100, 2) : ($factura[$i]['desc4'] > 0 ? $factura[$i]['desc4'] : 0);
					$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
					$iva = $factura[$i]['iva'] > 0 ? $subtotal * /*0.15*/0.16 : 0;
					$total_tmp = $subtotal + $iva - $factura[$i]['fletes'] + $factura[$i]['otros'];
					if ($total_tmp > 0) {
						$importe_dev += $reg['importe'];
						$dev_rez[$num_proveedor][$id]['usado'] = TRUE;
						$query .= "UPDATE devoluciones_zap SET num_fact = '{$factura[$i]['num_fact']}', fecha_fac = '{$factura[$i]['fecha']}', folio = $folio_dev, num_cia_cheque = $num_cia, folio_cheque = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, imp = 'TRUE' WHERE id = $id;\n";
						$dev_cont++;
					}
				}

		// Recalcular total de la factura
		if ($importe_dev > 0) {
			$subimporte = $factura[$i]['importe'] - $factura[$i]['faltantes'] - $factura[$i]['dif_precio'] - $importe_dev;
			$desc1 = $factura[$i]['pdesc1'] > 0 ? round($subimporte * $factura[$i]['pdesc1'] / 100, 2) : ($factura[$i]['desc1'] > 0 ? $factura[$i]['desc1'] : 0);
			$desc2 = $factura[$i]['pdesc2'] > 0 ? round(($subimporte - $desc1) * $factura[$i]['pdesc2'] / 100, 2) : ($factura[$i]['desc2'] > 0 ? $factura[$i]['desc2'] : 0);
			$desc3 = $factura[$i]['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $factura[$i]['pdesc3'] / 100, 2) : ($factura[$i]['desc3'] > 0 ? $factura[$i]['desc3'] : 0);
			$desc4 = $factura[$i]['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $factura[$i]['pdesc4'] / 100, 2) : ($factura[$i]['desc4'] > 0 ? $factura[$i]['desc4'] : 0);
			$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
			$iva = $factura[$i]['iva'] > 0 ? $subtotal * /*0.15*/0.16 : 0;
			$total_fac = $subtotal + $iva - $factura[$i]['fletes'] + $factura[$i]['otros'];
		}
		else
			$total_fac = $factura[$i]['total'];

		// [20-Mar-2009] Descontar notas de crédito
		if (isset($notas[$factura[$i]['num_fact']]) && $total_fac >= $notas[$factura[$i]['num_fact']]['importe']) {
			$total_fac -= $notas[$factura[$i]['num_fact']]['importe'];
			$query .= "UPDATE notas_credito_zap SET status = 2, num_cia_apl = $num_cia, folio_cheque = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, num_fact = '{$factura[$i]['num_fact']}', iduser = $_SESSION[iduser], lastmod = now() WHERE num_proveedor = $num_proveedor AND folio = {$notas[$factura[$i]['num_fact']]['nota']};\n";
		}

		if ($num_fac < $fac_x_cheque) {
			// Sumar importe de factura al importe del cheque
			$importe_cheque = round($importe_cheque, 2) + round(/*$factura[$i]['total']*/$total_fac, 2);

			// Factura a borrar de pasivo
			$fac_upd[$fac_count] = $factura[$i]['id'];

			// Agregar el número de factura al concepto del cheque
			if ($fac_count > 0)
				$facturas .= " ";
			// $facturas .= fillZero($factura[$i]['num_fact'], 7);
			$facturas .= $factura[$i]['num_fact'];

			$codgastos = $factura[$i]['codgastos'];
			$nombre_gasto = $factura[$i]['nombre_gasto'];

			// Incrementar contador de facturas
			$fac_count++;
			// Incrementar contador de facturas por cheque
			$num_fac++;
		}
	}

	// Organizar datos para almacenar cheque
	if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
		// Si el importe del cheque es mayor o igual al monto minimo, generarlo
		if ($importe_cheque >= $monto_min) {
			// Datos para la tabla de 'cheques'
			$cheque[$che_count]['cod_mov']           = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
			$cheque[$che_count]['num_proveedor']     = $num_proveedor;
			$cheque[$che_count]['num_cia']           = $num_cia;
			$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
			$cheque[$che_count]['concepto']          = $nombre_gasto;
			$cheque[$che_count]['facturas']          = $facturas;
			$cheque[$che_count]['fecha']             = $_SESSION['pmp']['fecha_cheque'];
			$cheque[$che_count]['folio']             = $folio_cheque;
			$cheque[$che_count]['importe']           = number_format($importe_cheque, 2, ".", "");
			$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
			$cheque[$che_count]['imp']               = "FALSE";
			$cheque[$che_count]['codgastos']         = $codgastos;
			$cheque[$che_count]['proceso']           = "TRUE";
			$cheque[$che_count]['cuenta']            = $_SESSION['pmp']['cuenta'];
			$cheque[$che_count]['poliza']            = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? "TRUE" : "FALSE";

			if ($_SESSION['pmp']['cuenta'] == 2 && $trans) {
				$transfer[$trans_cont]['num_cia']       = $num_cia;
				$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
				$transfer[$trans_cont]['folio']         = $folio_cheque;
				$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
				$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
				$transfer[$trans_cont]['tipo']          = !$san ? "TRUE" : "FALSE";
				$transfer[$trans_cont]['status']        = "0";
				$transfer[$trans_cont]['folio_archivo'] = 0;
				$transfer[$trans_cont]['cuenta'] = 2;
				$transfer[$trans_cont]['gen_dep'] = 'TRUE';
				$trans_cont++;
			}
			else if ($_SESSION['pmp']['cuenta'] == 1 && $trans) {
				$transfer[$trans_cont]['num_cia']       = $num_cia;
				$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
				$transfer[$trans_cont]['folio']         = $folio_cheque;
				$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
				$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
				$transfer[$trans_cont]['tipo']          = "FALSE";
				$transfer[$trans_cont]['status']        = "0";
				$transfer[$trans_cont]['folio_archivo'] = 0;
				$transfer[$trans_cont]['cuenta']        = 1;
				$transfer[$trans_cont]['concepto']      = "PAGO FOLIO $folio_cheque";
				$transfer[$trans_cont]['gen_dep'] = 'TRUE';
				$trans_cont++;
			}

			// Datos para la tabla de 'estado_cuenta'
			$cuenta[$che_count]['num_cia'] = $num_cia;
			$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
			if ($importe_cheque == 0)
				$cuenta[$che_count]['fecha_con'] = $_SESSION['pmp']['fecha_cheque'];
			$cuenta[$che_count]['concepto'] = $facturas;
			$cuenta[$che_count]['tipo_mov'] = "TRUE";
			$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
			$cuenta[$che_count]['cod_mov'] = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
			$cuenta[$che_count]['folio'] = $folio_cheque;
			$cuenta[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

			// Datos para la tabla de folios_cheque
			$folio[$che_count]['folio']     = $folio_cheque;
			$folio[$che_count]['num_cia']   = $num_cia;
			$folio[$che_count]['reservado'] = "FALSE";
			$folio[$che_count]['utilizado'] = "TRUE";
			$folio[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
			$folio[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

			$gasto[$che_count]['codgastos'] = $codgastos;
			$gasto[$che_count]['num_cia']   = $num_cia;
			$gasto[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
			$gasto[$che_count]['importe']   = number_format($importe_cheque, 2, ".", "");
			$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
			$gasto[$che_count]['captura']   = "TRUE";
			$gasto[$che_count]['factura']   = "";
			$gasto[$che_count]['folio']     = $folio_cheque;

			// Actualizar facturas
			$query .= "UPDATE facturas_zap SET folio = $folio_cheque, cuenta = {$_SESSION['pmp']['cuenta']}, tspago = now() WHERE id IN (";
			for ($f = 0; $f < $fac_count; $f++)
				$query .= $fac_upd[$f] . ($f < $fac_count - 1 ? "," : ");\n");

			// Actualizar saldo en libros
			//$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia;\n";
			$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']};\n";

			// [28-Jun-2007] Incrementar número de folio de vale de devolución
			if ($dev_cont > 0) $folio_dev++;

			// [28-Jun-2007] Reestablecer contador de devoluciones
			$dev_cont = 0;

			// Imcrementar número de folio
			$folio_cheque++;
			// Incrementar contador de cheques
			$che_count++;
		}
	}

	// Insertar datos en la base
	$query .= $GLOBALS['db']->multiple_insert("cheques",$cheque);
	$query .= $GLOBALS['db']->multiple_insert("folios_cheque",$folio);
	$query .= $GLOBALS['db']->multiple_insert("estado_cuenta",$cuenta);
	$query .= $GLOBALS['db']->multiple_insert("movimiento_gastos",$gasto);
	if (isset($transfer)) $query .= $GLOBALS['db']->multiple_insert("transferencias_electronicas",$transfer);

	return $query;
}

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por pagar";

if (isset($_GET['cancelar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_zap.php");
	die;
}

if (isset($_GET['generar'])) {
	$id = $_POST['id'];

	$clabe_cuenta = $_SESSION['pmp']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	// Construir script sql
	$sql = "SELECT id, num_cia, num_fact, importe, pdesc1, pdesc2, pdesc3, pdesc4, fz.desc1, fz.desc2, fz.desc3, fz.desc4, faltantes, dif_precio, iva, fletes, otros, total,";
	$sql .= " concepto AS descripcion, fecha, num_proveedor, nombre, codgastos, cg.descripcion AS nombre_gasto, trans, san FROM facturas_zap AS fz";
	$sql .= " LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_gastos AS cg USING (codgastos) WHERE id IN (";
	// Añade todas las facturas seleccionadas para pagar
	for ($i=0; $i < count($id); $i++)
		$sql .= $id[$i] . ($i < count($id) - 1 ? "," : ")");
	$sql .= " ORDER BY num_cia ASC, num_proveedor, fecha ASC, total DESC";
	// Obtener facturas
	$facturas = $db->query($sql);

	// Ejecutar función de pago
	if ($facturas) {
		// [17-Jul-2007] Devoluciones para pagos obligados
		$sql = "SELECT id, num_cia, num_proveedor, importe FROM devoluciones_zap WHERE folio IS NULL AND (/*num_cia, */num_proveedor) IN (SELECT /*num_cia,*/";
		$sql .= " num_proveedor FROM facturas_zap WHERE folio IS NULL AND clave = 0";
		$sql .= " AND id IN (";
		for ($i = 0; $i < count($id); $i++)
			$sql .= $id[$i] . ($i < count($id) - 1 ? "," : ")");
		$sql .= " GROUP BY num_cia, num_proveedor) ORDER BY num_cia, num_proveedor";
		$tmp = $db->query($sql);
		$dev = array();
		$cia = NULL;
		$pro = NULL;
		if ($tmp)
			foreach ($tmp as $d) {
				if ($cia != $d['num_cia'])
					$cia = $d['num_cia'];
				if ($pro != $d['num_proveedor'])
					$pro = $d['num_proveedor'];
				$dev[$cia][$pro][$d['id']] = array('importe' => $d['importe'], 'usado' => FALSE);
			}

		// [20-Mar-2009] Notas de crédito
		$notas = array();
		for ($i = 0; $i < count($_POST['nota']); $i++)
			if ($_POST['nota'][$i] > 0 && $_POST['importe_nota'][$i] > 0)
				$notas[$_POST['num_fact_nota'][$i]] = array('nota' => $_POST['nota'][$i], 'importe' => $_POST['importe_nota'][$i]);//echo '<pre>' . print_r($_POST['num_fact_nota'], TRUE) . print_r($_POST['nota'], TRUE) . print_r($_POST['importe_nota'], TRUE) . print_r($notas, TRUE) . '</pre>';

		$sql = pago_proveedores($facturas);//echo "<pre>$sql</pre>";die;
		$db->query($sql);
	}

	// Generar nuevamente el listado de proveedores o compañias
	$no_pago = array();
	for ($i = 0; $i < count($_SESSION['pmp']['no_pago']); $i++)
		if ($_SESSION['pmp']['no_pago'][$i] > 0)
			$no_pago[] = $_SESSION['pmp']['no_pago'][$i];

	if ($_SESSION['pmp']['tipo'] == 1) {
		$sql = "SELECT fz.num_proveedor AS num_proveedor, cp.nombre AS nombre, trans FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " fz.num_cia = {$_SESSION['pmp']['num_cia']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " fz.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND cc.$clabe_cuenta IS NOT NULL AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND fz.clave = 0 AND fz.sucursal = 'FALSE' GROUP BY fz.num_proveedor, cp.nombre, trans ORDER BY num_proveedor ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_zap.php?codigo_error=1");
			die;
		}

		$_SESSION['pmp']['proveedores'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
			$_SESSION['pmp']['tipo_pro'][$i] = $result[$i]['trans'];
		}
	}
	else {
		$sql = "SELECT num_cia, nombre_corto FROM facturas_zap AS fz LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " fz.num_proveedor = {$_SESSION['pmp']['num_proveedor']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " fz.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND $clabe_cuenta IS NOT NULL AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND clave = 0 AND fz.sucursal = 'FALSE' GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_zap.php?codigo_error=1");
			die;
		}

		$_SESSION['pmp']['compañias'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}
	}
}

if (isset($_GET['terminar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_zap.php");
	die;
}

if (isset($_GET['siguiente'])) {
	$list = $_SESSION['pmp']['tipo'] == 1 ? "proveedores" : "compañias";
	$field = $_SESSION['pmp']['tipo'] == 1 ? "num_proveedor" : "num_cia";

	if ($_POST[$field] > 0) {
		if (($key = array_search($_POST[$field], $_SESSION['pmp'][$list])) !== FALSE)
			$_SESSION['pmp']['next'] = $key;
		else
			$_SESSION['pmp']['next'] = 0;
	}
	else if ($_SESSION['pmp']['next'] < count($_SESSION['pmp'][$list]) - 1)
		$_SESSION['pmp']['next'] = $_SESSION['pmp']['next'] + 1;
	else
		$_SESSION['pmp']['next'] = 0;

	header("location: ./ban_pma_zap.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pma_zap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tipo'])) {
	$_SESSION['pmp'] = $_GET;

	$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	$no_pago = array();
	for ($i = 0; $i < count($_GET['no_pago']); $i++)
		if ($_GET['no_pago'][$i] > 0)
			$no_pago[] = $_GET['no_pago'][$i];

	if ($_GET['tipo'] == 1) {
		$sql = "SELECT fz.num_proveedor, cp.nombre, trans FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " fz.num_cia = $_GET[num_cia] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " fz.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '$_GET[fecha_corte]' AND cc.$clabe_cuenta IS NOT NULL AND total > 0 AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND fz.clave = 0 AND fz.sucursal = 'FALSE'";
		$sql .= " GROUP BY fz.num_proveedor, cp.nombre, trans ORDER BY fz.num_proveedor ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_zap.php?codigo_error=1");
			die;
		}

		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
			$_SESSION['pmp']['tipo_pro'][$i] = $result[$i]['trans'];
		}

		if ($key = array_search($_GET['num_proveedor'], $_SESSION['pmp']['proveedores']))
			$next = $key;
		else
			$next = 0;

		$_SESSION['pmp']['next'] = $next;
	}
	else {
		$sql = "SELECT num_cia, nombre_corto FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
		$sql .= $_GET['num_proveedor'] > 0 ? " fz.num_proveedor = $_GET[num_proveedor] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " fz.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '$_GET[fecha_corte]' AND $clabe_cuenta IS NOT NULL AND total > 0 AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND fz.clave = 0 AND fz.sucursal = 'FALSE' GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_zap.php?codigo_error=1");
			die;
		}

		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}

		if ($key = array_search($_GET['num_cia'], $_SESSION['pmp']['compañias']))
			$next = $key;
		else
			$next = 0;

		$_SESSION['pmp']['next'] = $next;
	}
}

if (!isset($_SESSION['pmp'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha_corte", date("d/m/Y"));
	$tpl->assign("fecha_cheque", date("d/m/Y"));

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

if (isset($_SESSION['pmp'])) {
	$clabe_cuenta = $_SESSION['pmp']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	$no_pago = array();
	for ($i = 0; $i < count($_SESSION['pmp']['no_pago']); $i++)
		if ($_SESSION['pmp']['no_pago'][$i] > 0)
			$no_pago[] = $_SESSION['pmp']['no_pago'][$i];

	if ($_SESSION['pmp']['tipo'] == 1) {
		$sql = "SELECT id, num_cia, nombre_corto, $clabe_cuenta, fecha, num_fact, concepto, total AS importe FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia)";
		$sql .= " WHERE fz.num_proveedor = {$_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " AND num_cia = {$_SESSION['pmp']['num_cia']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND $clabe_cuenta IS NOT NULL AND fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND total > 0 AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND fz.clave = 0 AND fz.sucursal = 'FALSE' ORDER BY num_cia, fecha ASC";
		$result = $db->query($sql);

		$tpl->newBlock("proveedor");
		$tpl->assign("num_proveedor", $_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_proveedor", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		$tpl->assign("tipo_pago", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "f" ? "CHEQUE" : "TRANSFERENCIA");
		$tpl->assign("tipo", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? 1 : 0);
		$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : "checked");

		$num_cia = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				$num_cia = $result[$i]['num_cia'];

				$tpl->newBlock("block_cia");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
				$tpl->assign("clabe_cuenta", $result[$i][$clabe_cuenta]);

				$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : "checked");

				$tmp = $db->query("SELECT saldo_libros FROM saldos WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']}");
				$saldo = $tmp ? $tmp[0]['saldo_libros'] : 0;
				$depositos = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']} AND tipo_mov = 'FALSE' AND fecha_con IS NULL");

				$tpl->assign("saldo", number_format($saldo - ($_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? $depositos[0]['sum'] : 0), 2, ".", ","));

				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);

				$total = 0;
			}
			$tpl->newBlock("fac_cia");
			$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : " checked");
			$tpl->assign('disabled', $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == 't' ? ' disabled' : '');
			$tpl->assign("i", $i);
			$tpl->assign('next', count($result) > 1 ? 'nota[' . ($i < count($result) - 1 ? $i + 1 : 0) . ']' : 'null');
			$tpl->assign('back', count($result) > 1 ? 'nota[' . ($i > 0 ? $i - 1 : count($result) - 1) . ']' : 'null');
			$tpl->assign('p', $_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]);
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", trim($result[$i]['concepto']) != '' ? trim($result[$i]['concepto']) : '&nbsp;');
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ""));
			$tpl->assign("fimporte", number_format($result[$i]['importe'], 2, ".", ","));

			$total += $result[$i]['importe'];
			$tpl->assign("block_cia.total", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "0.00" : number_format($total, 2, ".", ""));

			$tpl->assign("block_cia.fin", $i);
		}

		for ($i = 0; $i < count($_SESSION['pmp']['proveedores']); $i++) {
			$tpl->newBlock("pro");
			$tpl->assign("num_pro", $_SESSION['pmp']['proveedores'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}
	}
	else {
		$sql = "SELECT id, fz.num_proveedor, cp.nombre, $clabe_cuenta, fecha, num_fact, descripcion AS concepto, total AS importe, trans FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor)";
		$sql .= " LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " AND fz.num_proveedor = {$_SESSION['pmp']['num_cia']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND fz.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND total > 0 AND (verfac = FALSE OR copia_fac = TRUE) AND por_aut = 'TRUE' AND folio IS NULL AND fz.clave = 0 AND fz.sucursal = 'FALSE' AND $clabe_cuenta IS NOT NULL ORDER BY fz.num_proveedor, fecha ASC";
		$result = $db->query($sql);

		$tmp = $db->query("SELECT saldo_libros FROM saldos WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]} AND cuenta = {$_SESSION['pmp']['cuenta']}");
		$saldo = $tmp ? $tmp[0]['saldo_libros'] : 0;
		$depositos = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]} AND cuenta = {$_SESSION['pmp']['cuenta']} AND tipo_mov = 'FALSE' AND fecha_con IS NULL");

		$tpl->newBlock("compania");
		$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_cia", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		$tpl->assign("saldo", number_format($saldo - ($_SESSION['pmp']['tipo'] == "t" ? $depositos[0]['sum'] : 0), 2, ".", ","));

		$num_pro = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_pro != $result[$i]['num_proveedor']) {
				$num_pro = $result[$i]['num_proveedor'];

				$tpl->newBlock("block_pro");
				$tpl->assign("num_proveedor", $num_pro);
				$tpl->assign("nombre_proveedor", $result[$i]['nombre']);
				$tpl->assign("tipo_pago", $result[$i]['trans'] == "f" ? "CHEQUE" : "TRANSFERENCIA");
				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);
				$tpl->assign("checked", $result[$i]['trans'] == "f" ? "checked" : "");

				$total = 0;
			}
			$tpl->newBlock("fac_pro");
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("checked", $result[$i]['trans'] == "f" ? "checked" : "");
			$tpl->assign("i", $i);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", trim($result[$i]['concepto']) != '' ? trim($result[$i]['concepto']) : '&nbsp;');
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
			$tpl->assign("tipo", $result[$i]['trans'] == "f" ? 0 : 1);

			$total += $result[$i]['importe'];
			$tpl->assign("block_pro.total", $result[$i]['trans'] == "f" ? number_format($total, 2, ".", ",") : "0.00");

			$tpl->assign("block_pro.fin", $i);
		}

		for ($i = 0; $i < count($_SESSION['pmp']['compañias']); $i++) {
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}
	}

	$tpl->printToScreen();
	die;
}
?>
