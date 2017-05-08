<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$numfilas = 20;

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

// Validación de fecha
if (isset($_GET['fc']) && isset($_GET['fe'])) {
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fc'], $fc);
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fe'], $fe);
	$tsfc = mktime(0, 0, 0, $fc[2], $fc[1], $fc[3]);
	$tsfe = mktime(0, 0, 0, $fe[2], $fe[1], $fe[3]);
	
	if ($tsfe >= $tsfc)
		echo 1;
	else
		echo -1;
	
	die;
}

if (isset($_GET['f'])) {
	$sql = 'SELECT \'' . $_GET['f'] . '\' < (SELECT fecha + interval \'1 month\' - interval \'1 day\' FROM balances_pan ORDER BY fecha DESC LIMIT 1) AS result';
	$result = $db->query($sql);
	
	if ($result[0]['result'] == 't') {
		echo -1;
		die;
	}
	
	$sql = 'SELECT fecha FROM venta_pastel WHERE num_cia = ' . $_GET['c'] . ' AND fecha_entrega < \'' . $_GET['f'] . '\' AND estado = 0 AND tipo = 0';
	$result = $db->query($sql);
	
	if ($result)
		echo -2;
	
	die;
}

if (isset($_REQUEST['getlast'])) {
	$sql = '
		SELECT
			MAX(num_remi)
				AS num_remi
		FROM
			venta_pastel
		WHERE
			num_cia = ' . $_REQUEST['num_cia'] . '
			AND letra_folio = \'' . $_REQUEST['letra'] . '\'
			AND num_remi BETWEEN (
				SELECT
					folio_inicio
				FROM
					bloc
				WHERE
					idcia = ' . $_REQUEST['num_cia'] . '
					AND let_folio = \'' . $_REQUEST['letra'] . '\'
					AND ' . $_REQUEST['num_remi'] . ' BETWEEN folio_inicio AND folio_final
			) AND (
				SELECT
					folio_final
				FROM
					bloc
				WHERE
					idcia = ' . $_REQUEST['num_cia'] . '
					AND let_folio = \'' . $_REQUEST['letra'] . '\'
					AND ' . $_REQUEST['num_remi'] . ' BETWEEN folio_inicio AND folio_final
			)
			AND tipo = 0
	';
	
	$result = $db->query($sql);
	
	if ($result && $result[0]['num_remi'] > 0) {
		echo $result[0]['num_remi'];
	}
	
	die;
}

// Obtener compañía
if (isset($_GET['c'])) {
	if (in_array($_SESSION['iduser'], array(1, 4, 18, 19)))
		$sql = 'SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia <= 300 AND num_cia = ' . $_GET['c'];
	else
		$sql = 'SELECT nombre_corto AS nombre FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE num_cia <= 300 AND iduser = ' . $_SESSION['iduser'] . ' AND num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	
	die;
}

if (isset($_GET['num_remi'])) {
	$sql = "SELECT tipo, estado, idexpendio, total_factura, fecha_entrega, cuenta, resta, fecha, dev_base, kilos, precio_unidad AS precio, otros AS pan, base, pastillaje, otros_efectivos AS otros, resta_pagar FROM venta_pastel WHERE num_cia = $_GET[num_cia] AND num_remi = $_GET[num_remi] AND letra_folio = '$_GET[letra]' ORDER BY tipo, fecha";
	$result = $db->query($sql);
	
	if ($result) {
		$data = '';
		foreach ($result as $i => $reg) {
			$data .= "$reg[tipo]|$reg[estado]|$reg[idexpendio]|$reg[fecha]|$reg[fecha_entrega]|$reg[total_factura]|$reg[cuenta]|$reg[resta]|";
			$data .= "$reg[dev_base]|$reg[kilos]|$reg[precio]|$reg[pan]|$reg[base]|$reg[pastillaje]|$reg[otros]|$reg[resta_pagar]\n";
		}
		
		die($data);
	}
	else {
		// Validar que el block de donde proviene este dado de alta
		$sql = "SELECT id, folio_inicio, folio_final FROM bloc WHERE idcia = $_GET[num_cia] AND $_GET[num_remi] BETWEEN folio_inicio AND folio_final AND let_folio = '$_GET[letra]'";
		$result = $db->query($sql);
		
		if (!$result) {
			echo -1;
			die;
		}
		
		// Validar que la nota no este saltada
		$sql = "SELECT num_remi FROM venta_pastel WHERE num_cia = $_GET[num_cia] AND num_remi BETWEEN {$result[0]['folio_inicio']} AND {$result[0]['folio_final']} AND letra_folio = '$_GET[letra]' AND tipo = 0 ORDER BY num_remi DESC LIMIT 1";
		$last = $db->query($sql);
		
		$dif = $_GET['num_remi'] - ($last ? $last[0]['num_remi'] + 1 : $result[0]['folio_inicio']);
		
		if ($dif > 0)
			echo $dif;
		
		die;
	}
}

if (isset($_GET['num_exp'])) {
	$sql = "SELECT porciento_ganancia AS por_exp FROM catalogo_expendios WHERE num_cia = $_GET[num_cia] AND num_expendio = $_GET[num_exp]";
	$result = $db->query($sql);
	
	echo $result[0]['por_exp'];
	die;
}

if (isset($_POST['num_cia'])) {
	$num_cia = $_POST['num_cia'];
	$fecha = $_POST['fecha'];
	
	$sql = '';
	
	// @@@ Si no hay efectivo del día capturado crear el registro
	if (!$db->query("SELECT id FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql .= "INSERT INTO total_panaderias (num_cia, fecha, venta_puerta, pastillaje, otros, abono, gastos, raya_pagada, venta_pastel, abono_pastel, efectivo, efe, exp, gas, pro, pas) VALUES ($num_cia, '$fecha', 0, 0, 0, 0, 0, 0, 0, 0, 0, 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'TRUE');\n";
	
	for ($i = 0; $i < $numfilas; $i++) {
		// @ Número de factura
		$num_remi = $_POST['num_remi'][$i];
		// @ Tipo de factura
		$tipo = $_POST['tipo'][$i];
		
		// @@@ Si no hay número de remisión y no esta definido el tipo de factura omitir todo el proceso de validación e inserción para este registro
		if (!($num_remi > 0 && $tipo > 0))
			continue;
		
		// @ Letra del número de factura
		$letra_folio = $_POST['letra_folio'][$i] != '' ? $_POST['letra_folio'][$i] : 'X';
		
		// @@@ Si solo esta el número de factura pero no esta definido el tipo, grabar solo para cancelación
		if ($tipo == 1 && get_val($_POST['total_factura'][$i]) == 0) {
			$sql .= 'INSERT INTO venta_pastel (num_cia, fecha, letra_folio, num_remi, tipo, estado, idexpendio, kilos, precio_unidad, otros, base, cuenta, dev_exp, fecha_entrega, total_factura, resta_pagar, pastillaje, otros_efectivos, resta, dev_base) VALUES ';
			$sql .= "($num_cia, '$fecha', '$letra_folio', $num_remi, 0, 1, 0, 0, 0, 0, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 0);\n";
			continue;
		}
		
		switch ($tipo) {
			// @@@ CONTROL 1 AMARILLO
			case 1:
				// @@@ Obtener valores reales de los campos del registro
				$idexp = get_val($_POST['idexpendio'][$i]);
				$por_exp = get_val($_POST['porExp'][$i]);
				$kilos = get_val($_POST['kilos'][$i]);
				$precio = get_val($_POST['precio_unidad'][$i]);
				$otros = get_val($_POST['otros'][$i]);
				$base = get_val($_POST['base'][$i]);
				$cuenta = get_val($_POST['cuenta'][$i]);
				$dev_exp = get_val($_POST['dev_exp'][$i]);
				$fecha_entrega = $_POST['fecha_entrega'][$i];
				$pastillaje = get_val($_POST['pastillaje'][$i]);
				$otros_efectivos = get_val($_POST['otros_efectivos'][$i]);
				$total_factura = get_val($_POST['total_factura'][$i]);
				$resta_pagar = get_val($_POST['resta_pagar'][$i]);
				
				// @@@ Si resta_pagar es mayor a cero la factura no esta liquidada, en caso contrario poner como pagada
				$estado = $resta_pagar > 0 ? 0 : 1;
				
				// @@@ Registro de inserción a la 'venta_pastel' control amarillo
				$sql .= 'INSERT INTO venta_pastel (num_cia, fecha, letra_folio, num_remi, tipo, estado, idexpendio, kilos, precio_unidad, otros, base, cuenta, dev_exp, fecha_entrega, total_factura, resta_pagar, pastillaje, otros_efectivos, resta, dev_base) VALUES ';
				$sql .= "($num_cia, '$fecha', '$letra_folio', $num_remi, 0, $estado, $idexp, $kilos, $precio, $otros, $base, $cuenta, $dev_exp, '$fecha_entrega', $total_factura, $resta_pagar, $pastillaje, $otros_efectivos, 0, 0);\n";
				
				// @@@ Si es nota de expendio abonar el importe dejado a cuenta y actualizar el rezago del expendio
				if ($idexp > 0) {
					// @@@ Partidas (pan para venta) es igual a los kilos de pan vendidos
					$pan_p_venta = ($kilos * $precio) + $otros;
					// @@@ Total (pan para expendio) es igual las partidas por el porcentaje de ganancia
					$pan_p_expendio = round($pan_p_venta * ((100 - $por_exp) / 100), 2);
					// @@@ Abono es igual a el importe dejado a cuenta menos los importes que no entran en venta en puerta por el porcentaje de ganancia
					$abono = round(($cuenta - $base - $pastillaje - $otros_efectivos - $dev_exp) * ((100 - $por_exp) / 100), 2);
					// @@@ La diferecia entre el pan para expendio y el abono es igual a la variación del rezago del expendio
					$dif = $pan_p_expendio - $abono;
					// @@@ Actualizar los importes del expendio
					$sql .= "UPDATE mov_expendios SET pan_p_venta = pan_p_venta + $pan_p_venta, pan_p_expendio = pan_p_expendio + $pan_p_expendio, abono = abono + $abono, rezago = rezago + $dif";
					$sql .= " WHERE num_cia = $num_cia AND fecha = '$fecha' AND num_expendio = $idexp;\n";
					// @@@ Actualizar los rezagos del expendio para días posteriores
					$sql .= "UPDATE mov_expendios SET rezago_anterior = rezago_anterior + $dif, rezago = rezago + $dif";
					$sql .= " WHERE num_cia = $num_cia AND fecha > '$fecha' AND num_expendio = $idexp;\n";
					
					// @@@ El importe sumado a la venta en puerta es cero
					$vta = 0;
					// @@@ El importe sumado al efectivo es igual al importe del abono
					$efe = $abono + $base + $pastillaje + $otros_efectivos;
				}
				else {
					// @@@ El importe sumado a la venta en puerta es la diferencia de lo dejado a cuenta menos la base, el pastillaje y otros efectivos
					$vta = $cuenta - $base - $pastillaje - $otros_efectivos;
					// @@@ El importe sumado al abono es cero
					$abono = 0;
					// @@@ El importe sumado al efectivo es igual al importe dejado a cuenta
					$efe = $cuenta;
				}
				
				// @@@ Actualizar el efectivo de la panadería
				$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + $vta, pastillaje = pastillaje + $pastillaje, otros = otros + $base + $otros_efectivos, abono = abono + $abono, efectivo = efectivo + $efe, venta_pastel = venta_pastel + $vta, abono_pastel = abono_pastel + $abono";
				$sql .= " WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
				
				// @@@ Si la factura esta liquidada actualizar el estado del block de notas
				if ($estado == 1) {
					$sql .= "UPDATE bloc SET folios_usados = folios_usados + 1 WHERE idcia = $num_cia AND $num_remi BETWEEN folio_inicio AND folio_final AND let_folio = '$letra_folio';\n";
					$sql .= "UPDATE bloc SET estado = 'TRUE' WHERE idcia = $num_cia AND $num_remi BETWEEN folio_inicio AND folio_final AND let_folio = '$letra_folio' AND folios_usados >= num_folios;\n";
				}
			break;
			
			// @@@ CONTROL 2 VERDE
			case 2:
				// @@@ Obtener valores reales de los campos del registro
				$idexp = get_val($_POST['idexpendio'][$i]);
				$por_exp = get_val($_POST['porExp'][$i]);
				$dev_exp = get_val($_POST['dev_exp'][$i]);
				$total_factura = get_val($_POST['total_factura'][$i]);
				$resta_pagar = get_val($_POST['resta_pagar'][$i]);
				$resta = get_val($_POST['resta'][$i]);
				
				// @@@ Si resta_pagar es mayor a cero la factura no esta liquidada, en caso contrario poner como pagada
				$estado = $resta_pagar > 0 ? 0 : 1;
				
				// @@@ Registro de inserción a la 'venta_pastel' control verde
				$sql .= 'INSERT INTO venta_pastel (num_cia, fecha, letra_folio, num_remi, tipo, estado, idexpendio, kilos, precio_unidad, otros, base, cuenta, dev_exp, fecha_entrega, total_factura, resta_pagar, pastillaje, otros_efectivos, resta, dev_base) VALUES ';
				$sql .= "($num_cia, '$fecha', '$letra_folio', $num_remi, 1, $estado, $idexp, 0, 0, 0, 0, 0, $dev_exp, NULL, 0, 0, 0, 0, $resta, 0);\n";
				
				// @@@ Actualizar el resto a pagar de la factura
				$sql .= "UPDATE venta_pastel SET resta_pagar = $resta_pagar WHERE num_cia = $num_cia AND num_remi = $num_remi AND letra_folio = '$letra_folio' AND tipo = 0;\n";
				
				// @@@ Actualizar el estado de la factura si ya esta liquidada
				if ($estado == 1)
					$sql .= "UPDATE venta_pastel SET estado = 1 WHERE num_cia = $num_cia AND num_remi = $num_remi AND letra_folio = '$letra_folio' AND tipo = 0;\n";
				
				// @@@ Si es nota de expendio abonar el importe dejado a cuenta y actualizar el rezago del expendio
				if ($idexp > 0) {
					// @@@ Abono es igual a el importe del resto pagado menos devoluciones por el porcentaje de ganancia
					$abono = round(($resta - $dev_exp) * ((100 - $por_exp) / 100), 2);
					// @@@ La diferecia del importe abonado es igual a la variación del rezago del expendio
					$dif =  -$abono;
					// @@@ Actualizar los importes del expendio
					$sql .= "UPDATE mov_expendios SET abono = abono + $abono, rezago = rezago + $dif";
					$sql .= " WHERE num_cia = $num_cia AND fecha = '$fecha' AND num_expendio = $idexp;\n";
					// @@@ Actualizar los rezagos del expendio para días posteriores
					$sql .= "UPDATE mov_expendios SET rezago_anterior = rezago_anterior + $dif, rezago = rezago + $dif";
					$sql .= " WHERE num_cia = $num_cia AND fecha > '$fecha' AND num_expendio = $idexp;\n";
					
					$vta = 0;
				}
				else {
					$vta = $resta;
					// @@@ El abono de expendio es cero
					$abono = 0;
				}
				
				// @@@ Actualizar el efectivo de la panadería
				$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + $vta, abono = abono + $abono, venta_pastel = venta_pastel + $vta, abono_pastel = abono_pastel + $abono, efectivo = efectivo + $vta + $abono";
				$sql .= " WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
				
				// @@@ Si la factura esta liquidada actualizar el estado del block de notas
				if ($estado == 1) {
					$sql .= "UPDATE bloc SET folios_usados = folios_usados + 1 WHERE idcia = $num_cia AND $num_remi BETWEEN folio_inicio AND folio_final AND let_folio = '$letra_folio';\n";
					$sql .= "UPDATE bloc SET estado = 'TRUE' WHERE idcia = $num_cia AND $num_remi BETWEEN folio_inicio AND folio_final AND let_folio = '$letra_folio' AND folios_usados >= num_folios;\n";
				}
			break;
			
			// @@@ CONTROL 3 AZUL
			case 3:
				// @@@ Obtener valores reales de los campos del registro
				$dev_base = get_val($_POST['dev_base'][$i]);
				
				// @@@ Registro de inserción a la 'venta_pastel' control azul
				$sql .= 'INSERT INTO venta_pastel (num_cia, fecha, letra_folio, num_remi, tipo, estado, idexpendio, kilos, precio_unidad, otros, base, cuenta, dev_exp, fecha_entrega, total_factura, resta_pagar, pastillaje, otros_efectivos, resta, dev_base) VALUES ';
				$sql .= "($num_cia, '$fecha', '$letra_folio', $num_remi, 2, 1, 0, 0, 0, 0, 0, 0, 0, NULL, 0, 0, 0, 0, 0, $dev_base);\n";
				
				// @@@ Insertar gasto de devolución de base
				$sql .= 'INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, importe, captura) VALUES ';
				$sql .= "($num_cia, '$fecha', 114, 'DEVOLUCION DE BASE NOTA $letra_folio$num_remi', $dev_base, 'FALSE');\n";
				
				// @@@ Actualizar el efectivo de la panadería
				$sql .= "UPDATE total_panaderias SET gastos = gastos + $dev_base, efectivo = efectivo - $dev_base WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
			break;
		}
	}
	if ($_SESSION['iduser'] == 1) {
		echo "<pre>$sql</pre>";
		die;
	}
	else
		$db->query($sql);
	
	die(header('location: RegistroFacturasPastel.php'));
}

$tpl = new TemplatePower('plantillas/pan/RegistroFacturasPastel.tpl');
$tpl->prepare();

$tpl->newBlock("captura");

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('color_row', ($i + 1) % 2 == 0 ? 'on' : 'off');
	
	$tpl->assign('i', $i);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
}

$tpl->printToScreen();
die();
?>