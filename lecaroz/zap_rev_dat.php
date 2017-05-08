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
$tpl->assignInclude("body", "./plantillas/zap/zap_rev_dat.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

/************************************************************************/
/************************************************************************/
// VENTAS DIARIAS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'venta') {
	$sql = "SELECT * FROM ventadia_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat.php");
		die;
	}
	
	$tpl->newBlock('venta');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('venta_row');
		switch ($reg['tipo']) {
			case 1: $tipo = 'EFECTIVO'; break;
			case 2: $tipo = 'TARJETA'; break;
			case 3: $tipo = 'AMERICAN EXPRESS'; break;
			case 4: $tipo = 'DEPOSITO'; break;
		}
		$tpl->assign('tipo', $tipo);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$total += $reg['importe'];
	}
	$tpl->assign('venta.total', number_format($total, 2, '.', ','));
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// GASTOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'gastos') {
	$sql = "SELECT id, codgastos, descripcion, fecha, concepto, importe, cod_turno, valid, omitir FROM gastos_tmp LEFT JOIN catalogo_gastos USING (codgastos) WHERE";
	$sql .= " num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);
	
	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'";
	$pres = $db->query($sql);
	
	if (!($result || $pres)) {
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'venta') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('gastos');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	if ($result)
		foreach ($result as $i => $reg) {
			$tpl->newBlock('gas_row');
			$tpl->assign('i', $i);
			$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
			$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
			$tpl->assign('id', $reg['id']);
			$tpl->assign('valid', $reg['valid'] == 't' ? 'checked' : '');
			$tpl->assign('omitir', $reg['omitir'] == 't' ? 'checked' : '');
			$tpl->assign('codgastos', $reg['codgastos']);
			$tpl->assign('desc', $reg['descripcion']);
			$tpl->assign($reg['cod_turno'] > 0 ? $reg['cod_turno'] : '-', ' selected');
			$tpl->assign('concepto', strtoupper($reg['concepto']));
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$total += $reg['importe'];
		}
	if ($pres)
		foreach ($pres as $reg) {
			$tpl->newBlock('gas_pre');
			$tpl->assign('concepto', $reg['nombre']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$total += $reg['importe'];
		}
	$tpl->assign('gastos.total', number_format($total, 2, '.', ','));
	
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
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'venta') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['codgastos']); $i++) {
		$sql .= "UPDATE gastos_tmp SET codgastos = {$_POST['codgastos'][$i]}, omitir = '" . (isset($_POST['omitir' . $i]) ? 'TRUE' : 'FALSE') . "'";
		$sql .= " WHERE id = {$_POST['id'][$i]};\n";
	}
	
	$db->query($sql);
	header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'venta') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'nomina' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
	
	$cat = $db->query("SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[num_cia] AND fecha_baja IS NULL ORDER BY num_emp");
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
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'nomina' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['id_emp']); $i++)
		$sql .= "UPDATE prestamos_tmp SET idemp = {$_POST['id_emp'][$i]} WHERE id = {$_POST['id'][$i]};\n";
	
	$db->query($sql);
	header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'nomina' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
	die;
}

/************************************************************************/
/************************************************************************/
// NOMINAS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'nomina') {
	$sql = "SELECT * FROM nomina_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND dias > 0 ORDER BY nombre";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'acre' : 'pres') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('nomina');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$gtotal = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('nom');
		$tpl->assign('nombre', trim($reg['nombre']));
		$tpl->assign('sueldo', number_format($reg['sueldo'], 2, '.', ','));
		$dias = 0;
		for ($i = 1; $i <= 64; $i = $i * 2) {
			$dia = intval($reg['dias']) & $i;
			if ($dia > 0) {
				$tpl->assign($dia, 'X');
				$dias++;
			}
		}
		$subtotal = $dias * $reg['sueldo'];
		$tpl->assign('subtotal', number_format($subtotal, 2, '.', ','));
		$tpl->assign('comision', $reg['comision'] > 0 ? number_format($reg['comision'], 2, '.', ',') : '&nbsp;');
		$total = $subtotal + $reg['comision'];
		$tpl->assign('total', number_format($total, 2, '.', ','));
		$gtotal += $total;
	}
	$tpl->assign('nomina.total', number_format($gtotal, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// ACREDITADO
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'acre') {
	$sql = "SELECT * FROM acreditado_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY nombre";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'inter' : 'nomina') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('acreditado');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('acre');
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('concepto', $reg['concepto']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('acreditado', $reg['acreditado']);
		$total += $reg['importe'];
	}
	$tpl->assign('acreditado.total', number_format($total, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// INTERCAMBIOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'inter') {
	$sql = "SELECT * FROM intercambio_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY nombre";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'acre') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('intercambios');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	die;
}

/************************************************************************/
/************************************************************************/
// RESULTADO FINAL
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'result') {
	$result = $db->query("SELECT * FROM ventadia_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	
	if (!$result) {
		header("location: ./zap_rev_dat.php");
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
	//$db->query($sql);
	header("location: ./zap_rev_dat.php");
	die;
}

/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/

$tpl->newBlock("cias");

if (!in_array($_SESSION['iduser'], array(1, 4, 28))) {
	$tmp = $db->query("SELECT nombre_operadora FROM catalogo_operadoras WHERE iduser = $_SESSION[iduser]");
	$usuario = $tmp[0]['nombre_operadora'];
}
else
	$usuario = "ADMINISTRADOR";

$tpl->assign('usuario', $usuario);

$sql = "SELECT num_cia, nombre_corto AS nombre, fecha FROM ventadia_tmp LEFT JOIN catalogo_companias USING (num_cia) WHERE ts_aut IS NULL";
$sql .= " GROUP BY num_cia, nombre_corto, fecha ORDER BY num_cia, fecha";
$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	foreach ($result as $cia) {
		if ($num_cia != $cia['num_cia']) {
			if ($num_cia != NULL)
				$tpl->newBlock('void');
			
			$num_cia = $cia['num_cia'];
		}
		$tpl->newBlock('cia');
		$tpl->assign('opt', "$cia[num_cia]|$cia[fecha]");
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign("nombre", $cia['nombre']);
		$tpl->assign('fecha', $cia['fecha']);
	}
}
else
	$tpl->newBlock("no_cias");

$tpl->printToScreen();
?>