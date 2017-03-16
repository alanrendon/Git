<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ros/ros_pro_aut.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

/************************************************************************/
/************************************************************************/
// COMPRAS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'compras') {
	$sql = "SELECT mov_inv_tmp.id, codmp, nombre, cantidad, kilos, precio_compra, precio_unidad, aplica, mov_inv_tmp.num_proveedor, num_fact FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas";
	$sql .= " USING (codmp) LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN inventario_real USING (num_cia, codmp) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND";
	$sql .= " tipomov = 'FALSE' AND codmp NOT IN (160, 600, 700, 297, 363, 352) ORDER BY codmp";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ros_pro_aut.php");
		die;
	}
	
	$tpl->newBlock('compras');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('compra');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('codmp', $reg['codmp']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('cantidad', $reg['cantidad'] != 0 ? number_format($reg['cantidad'], 2, '.', ',') : '');
		$tpl->assign('kilos', $reg['kilos'] != 0 ? number_format($reg['kilos']) : '');
		$tpl->assign('precio', $reg['precio_compra'] != 0 ? number_format($reg['precio_compra'], 2, '.', ',') : '');
		$importe = $reg['cantidad'] * $reg['precio_compra'];
		$tpl->assign('importe', $importe != 0 ? number_format($importe, 2, '.', ',') : '');
		$tpl->assign('num_pro', $reg['aplica'] == 't' ? 289 : $reg['num_proveedor']);
		if ($reg['aplica'] == 't')
			$tpl->assign('nombre_pro', 'COMPRAS DIRECTAS');
		else {
			$nombre_pro = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $reg[num_proveedor]");
			$tpl->assign('nombre_pro', $nombre_pro ? $nombre_pro[0]['nombre'] : '');
		}
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('checked', $reg['aplica'] == 't' ? ' checked' : '');
		$total += $importe;
	}
	$tpl->assign('compras.total', number_format($total, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// VENTAS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'compras') {
	$sql = "SELECT mov.id, codmp, nombre, existencia, cantidad, precio_unidad, precio_venta FROM inventario_real LEFT JOIN mov_inv_tmp AS mov USING (num_cia, codmp)";
	$sql .= " LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$sql .= " AND tipomov = 'TRUE' AND cantidad != 0 ORDER BY orden";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ros_pro_aut.php");
		die;
	}
	
	$tpl->newBlock('compras');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('venta');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('codmp', $reg['codmp']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('existencia', $reg['existencia'] != 0 ? number_format($reg['existencia']) : '');
		$tpl->assign('cantidad', number_format($reg['cantidad'], 2, '.', ','));
		$tpl->assign('precio_venta', number_format($reg['precio_venta'], 2, '.', ','));
		$tpl->assign('');
	}
	$tpl->assign('compras.total', number_format($total, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// GASTOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'gastos') {
	$sql = "SELECT id, codgastos, descripcion, fecha, concepto, importe FROM gastos_tmp LEFT JOIN catalogo_gastos USING (codgastos) WHERE";
	$sql .= " num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);
	
	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'";
	$pres = $db->query($sql);
	
	if (!($result || $pres)) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pre' : 'avio') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('gastos');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	if ($result)
		foreach ($result as $i => $reg) {
			$tpl->newBlock('gasto_row');
			$tpl->assign('i', $i);
			$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
			$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
			$tpl->assign('id', $reg['id']);
			$tpl->assign('codgastos', $reg['codgastos']);
			$tpl->assign('desc', $reg['descripcion']);
			$tpl->assign('concepto', strtoupper($reg['concepto']));
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$total += $reg['importe'];
		}
	$tpl->assign('gastos');
	
	$result = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
	foreach ($result as $reg) {
		$tpl->newBlock('gasto');
		$tpl->assign('codgastos', $reg['codgastos']);
		$tpl->assign('desc', $reg['descripcion']);
	}
	
	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'gastos_mod') {
	$sql = "";
	
	if (!isset($_POST['id'])) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'avio') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['codgastos']); $i++) {
		$sql .= "UPDATE gastos_tmp SET codgastos = {$_POST['codgastos'][$i]}, cod_turno = " . ($_POST['turno'][$i] > 0 ? $_POST['turno'][$i] : 'NULL') . ",";
		$sql .= " valid = '" . (isset($_POST['valid' . $i]) ? 'TRUE' : 'FALSE') . "', omitir = '" . (isset($_POST['omitir' . $i]) ? 'TRUE' : 'FALSE') . "'";
		$sql .= " WHERE id = {$_POST['id'][$i]};\n";
	}
	
	$db->query($sql);
	header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'avio') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
	die;
}

/************************************************************************/
/************************************************************************/
// PRESTAMOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'pres') {
	$sql = "SELECT tmp.id, tmp.nombre AS nombre_tmp, ct.id AS id_emp, num_emp, ap_paterno, ap_materno, ct.nombre, saldo, tipo_mov, importe FROM prestamos_tmp AS tmp LEFT JOIN";
	$sql .= " catalogo_trabajadores AS ct ON (ct.id = idemp) WHERE tmp.num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND importe > 0 ORDER BY tmp.nombre";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('pres');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$saldo_ini = 0;
	$cargos = 0;
	$abonos = 0;
	$saldo_fin = 0;
	foreach ($result as $i => $reg) {
		$nombre = trim("$reg[ap_paterno] $reg[ap_materno] $reg[nombre]");
		
		$tpl->newBlock('pres_row');
		$tpl->assign('i', $i);
		$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
		$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
		$tpl->assign('id', $reg['id']);
		$tpl->assign('id_emp', $reg['id_emp']);
		$tpl->assign('num_emp', $reg['num_emp']);
		$tpl->assign('nombre_real', $nombre);
		$tpl->assign('nombre', $reg['nombre_tmp']);
		$tpl->assign('saldo_ini', $reg['saldo'] != 0 ? number_format($reg['saldo'], 2, '.', ',') : '');
		$tpl->assign($reg['tipo_mov'] == 'f' ? 'cargo' : 'abono', number_format($reg['importe'], 2, '.', ','));
		$saldo = $reg['saldo'] + ($reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe']);
		$tpl->assign('saldo_fin', $saldo != 0 ? number_format($saldo, 2, '.', ',') : '');
		
		$saldo_ini += $reg['saldo'];
		$cargos += $reg['tipo_mov'] == 'f' ? $reg['importe'] : 0;
		$abonos += $reg['tipo_mov'] == 't' ? $reg['importe'] : 0;
		$saldo_fin += $saldo;
	}
	$tpl->assign('pres.saldo_ini', number_format($saldo_ini, 2, '.', ','));
	$tpl->assign('pres.cargos', number_format($cargos, 2, '.', ','));
	$tpl->assign('pres.abonos', number_format($abonos, 2, '.', ','));
	$tpl->assign('pres.saldo_fin', number_format($saldo_fin, 2, '.', ','));
	
	$cat = $db->query("SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[num_cia] ORDER BY num_emp");
	if ($cat)
		foreach ($cat as $reg) {
			$tpl->newBlock('emp');
			$tpl->assign('num_emp', $reg['num_emp']);
			$tpl->assign('id_emp', $reg['id']);
			$tpl->assign('nombre', trim("$reg[ap_paterno] $reg[ap_materno] $reg[nombre]"));
		}
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'pres_mod') {
	$sql = "";
	
	if (!isset($_POST['id'])) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['id_emp']); $i++)
		$sql .= "UPDATE prestamos_tmp SET idemp = {$_POST['id_emp'][$i]} WHERE id = {$_POST['id'][$i]};\n";
	
	$db->query($sql);
	header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
	die;
}

/************************************************************************/
/************************************************************************/
// RESULTADO FINAL
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'result') {
	$result = $db->query("SELECT * FROM efectivos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	
	if (!$result) {
		header("location: ./pan_rev_dat.php");
		die;
	}
	
	$tpl->newBlock('result');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$abonos = $db->query("SELECT sum(abono) AS abono FROM mov_exp_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	$raya_pagada = $db->query("SELECT sum(raya_ganada) AS raya_pagada FROM total_produccion_tmp WHERE num_cia = $_GET[num_cia] AND fecha_total = '$_GET[fecha]'");
	$tmp1 = $db->query("SELECT sum(importe) AS importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND omitir = 'FALSE'");
	$tmp2 = $db->query("SELECT sum(importe) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'");
	$gastos = $tmp1[0]['importe'] + $tmp2[0]['importe'];
	$venta_puerta = $result[0]['cajaam'] - $result[0]['erroramcaja'] + $result[0]['cajapm'] - $result[0]['errorpmcaja'] + $result[0]['pastelam'] + $result[0]['pastelpm'] + $result[0]['pasteles'];
	$abono_obreros = $db->query("SELECT sum(importe) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'TRUE'");
	$otros = $result[0]['barredura'] + $result[0]['bases'] + $result[0]['esquilmos'] + $result[0]['botes'] + $result[0]['costales'] + $abono_obreros[0]['importe'];
	$efectivo = $venta_puerta + $abonos[0]['abono'] + $result [0]['pastillaje'] + $otros - $raya_pagada[0]['raya_pagada'] - $gastos;
	
	$tpl->assign('venta_puerta', $venta_puerta != 0 ? number_format($venta_puerta, 2, '.' ,',') : '&nbsp;');
	$tpl->assign('abonos', $abonos[0]['abono'] != 0 ? number_format($abonos[0]['abono'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('pastillaje', $result[0]['pastillaje'] != 0 ? number_format($result[0]['pastillaje'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('otros', $otros != 0 ? number_format($otros, 2, '.', ',') : '&nbsp;');
	$tpl->assign('raya_pagada', $raya_pagada[0]['raya_pagada'] != 0 ? number_format($raya_pagada[0]['raya_pagada'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('gastos', $gastos != 0 ? number_format($gastos, 2, '.', ',') : '&nbsp;');
	$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2, '.', ',') : '&nbsp;');
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// INSERCION Y ACTUALIZACION DE DATOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'finish') {
	$num_cia = $_GET['num_cia'];
	$fecha = $_GET['fecha'];
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $fecha_des);
	
	// Gastos
	$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, cod_turno, concepto, importe, captura) SELECT num_cia, fecha, codgastos, cod_turno, upper(concepto), importe,";
	$sql .= " 'FALSE' FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND omitir = 'FALSE' AND codgastos NOT IN (114, 115);\n";
	
	// Movimientos de Avio
	$sql .= "INSERT INTO mov_inv_virtual (num_cia, fecha, codmp, cod_turno, tipo_mov, cantidad, descripcion) SELECT num_cia, fecha, codmp, cod_turno, tipomov, CASE WHEN codmp = 1 THEN";
	$sql .= " cantidad * 44 ELSE cantidad END, CASE WHEN tipomov = 'FALSE' THEN 'ENTRADA VIRTUAL DE AVIO' ELSE 'SALIDA VIRTUAL DE AVIO' END FROM mov_inv_tmp WHERE num_cia = $num_cia AND";
	$sql .= " fecha = '$fecha' ORDER BY codmp, tipomov;\n";
	$sql .= "INSERT INTO mov_inv_real (num_cia, fecha, codmp, cod_turno, tipo_mov, cantidad, descripcion) SELECT num_cia, fecha, codmp, cod_turno, tipomov, CASE WHEN codmp = 1 THEN";
	$sql .= " cantidad * 44 ELSE cantidad END, 'SALIDA DE AVIO' FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND tipomov = 'TRUE' ORDER BY codmp, tipomov;\n";
	
	// Actualizar inventario real y virtual
	$movs = $db->query("SELECT codmp, tipomov, cantidad FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	if ($movs)
		foreach ($movs as $mov)
			if ($mov['tipomov'] == 'f')
				$sql .= "UPDATE inventario_virtual SET existencia = existencia + " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
			else {
				$sql .= "UPDATE inventario_virtual SET existencia = existencia - " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
				$sql .= "UPDATE inventario_real SET existencia = existencia - " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
			}
	
	// Prestamos
	$pres = $db->query("SELECT p.num_cia, fecha, idemp, ct.nombre, ap_paterno, ap_materno, tipo_mov, importe FROM prestamos_tmp AS p LEFT JOIN catalogo_trabajadores AS ct ON (ct.id = idemp) WHERE p.num_cia = $num_cia AND fecha = '$fecha' AND importe != 0");
	if ($pres)
		foreach ($pres as $reg)
			if ($reg['tipo_mov'] == 'f') {
				if ($id = $db->query("SELECT id FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'FALSE' AND pagado = 'FALSE'"))
					$sql .= "UPDATE prestamos SET importe = importe + $reg[importe] WHERE id = {$id[0]['id']};\n";
				else
					$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $reg[importe], 'FALSE', 'FALSE', $reg[idemp]);\n";
				$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, importe, captura) VALUES ($num_cia, '$fecha', 41, 'PRESTAMO EMPLEADO NO. $reg[idemp]";
				$sql .= " $reg[nombre] $reg[ap_paterno] $reg[ap_materno]', $reg[importe], 'FALSE');\n";
			}
			else {
				$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $reg[importe], 'TRUE', 'FALSE', $reg[idemp]);\n";
				// Verificar si ya se liquido el saldo
				$prestamo = $db->query("SELECT importe FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'FALSE' AND pagado = 'FALSE'");
				$abonos = $db->query("SELECT sum(importe) AS importe FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'TRUE' AND pagado = 'FALSE'");
				if (round($prestamo[0]['importe'], 2) == round($abonos[0]['importe'], 2) + round($reg['importe'], 2))
					$sql .= "UPDATE prestamos SET pagado = 'TRUE' WHERE id_empleado = $reg[idemp] AND pagado = 'FALSE';\n";
			}
	
	$efectivo_tmp = $db->query("SELECT * FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	
	// Actualiza timestamps de autorizacion
	$sql .= "UPDATE produccion_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE total_produccion_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha_total = '$fecha';\n";
	$sql .= "UPDATE mov_inv_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE gastos_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE mov_exp_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE efectivos_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE prueba_pan_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE corte_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE mediciones_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE camionetas_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE facturas_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE prestamos_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE pastillaje_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	
	//echo "<pre>$sql</pre>";
	$db->query($sql);
	header("location: ./pan_rev_dat.php");
	die;
}

/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/

$tpl->newBlock("cias");

$sql = "SELECT num_cia, nombre_corto AS nombre, fecha FROM mov_inv_tmp LEFT JOIN catalogo_companias USING (num_cia) WHERE ts_aut IS NULL AND num_cia > 100";
$sql .= " GROUP BY num_cia, nombre_corto, fecha ORDER BY num_cia, fecha";
$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	foreach ($result as $cia) {
		if ($num_cia != $cia['num_cia']) {
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $cia['num_cia']);
			$tpl->assign("nombre", $cia['nombre']);
			$num_cia = $cia['num_cia'];
			$cont = 0;
		}
		$tpl->newBlock('dia');
		$tpl->assign('opt', "$cia[num_cia]|$cia[fecha]");
		$tpl->assign('fecha', $cia['fecha']);
		
		// Desglosar la fecha del archivo
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $cia['fecha'], $tmp1);
		// Obtener la ultima fecha capturada en el sistema
		$last_date = $db->query("SELECT fecha FROM total_companias WHERE num_cia = $cia[num_cia] AND fecha < '$cia[fecha]' ORDER BY fecha DESC LIMIT 1");
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $last_date[0]['fecha'], $tmp2);
		$ts_db = mktime(0, 0, 0, $tmp2[2], $tmp2[1], $tmp2[3]);
		$ts_lim = mktime(0, 0, 0, $tmp1[2], $tmp1[1] - 1, $tmp1[3]);
		
		//$tpl->assign('disabled', $cont > 0 || $ts_db != $ts_lim ? ' disabled' : '');
		$cont++;
	}
}
else
	$tpl->newBlock("no_cias");

$tpl->printToScreen();
?>