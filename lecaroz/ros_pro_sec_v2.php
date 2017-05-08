<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No puede capturar si las demas rosticerias no han terminado el mismo día";

// Cancelar proceso secuencial
if (isset($_GET['cancel'])) {
	unset($_SESSION['psr']);
	header("location: ./ros_pro_sec_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pro_sec_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// PROCESO SECUENCIAL : COMPRA DIRECTA
if (isset($_GET['num_cia']) || isset($_GET['fecha']) || (isset($_POST['next_screen']) && $_POST['next_screen'] == "cd")) {
	// Iniciar variable de sesión y datos de compañía
	if (isset($_GET['num_cia'])) {
		$date = $db->query("SELECT fecha AS fecha FROM total_companias WHERE num_cia = $_GET[num_cia] ORDER BY fecha DESC LIMIT 1");
		
		$_SESSION['psr']['num_cia'] = $_GET['num_cia'];
		$_SESSION['psr']['nombre'] = $_GET['nombre'];
		
		if (!$date) {
			$tpl->newBlock("fecha");
			$tpl->assign("fecha", date("d/m/Y"));
			$tpl->printToScreen();
			die;
		}
		else {
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $date[0]['fecha'], $tmp);
			$_SESSION['psr']['fecha'] = date("d/m/Y", mktime(0, 0, 0, $tmp[2], $tmp[1] + 1, $tmp[3]));
		}
	}
	// Iniciar variable de sesión para fecha de captura
	else if (isset($_GET['fecha']))
		$_SESSION['psr']['fecha'] = $_GET['fecha'];
	
	/*
	** [13-Abr-2010] Validar que no se capture el dia si el resto de las rosticerias no han terminado
	*/
	$sql = '
		SELECT
			num_cia,
			(max(fecha) + INTERVAL \'1 day\')::date
				AS
					fecha
		FROM
			total_companias
		WHERE
			fecha > now() - interval \'1 month\'
		GROUP BY
			num_cia
		ORDER BY
			fecha
		LIMIT
			1
	';
	$LastDay = $db->query($sql);
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $LastDay[0]['fecha'], $Last);
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_SESSION['psr']['fecha'], $Current);
	
//	if (mktime(0, 0, 0, $Current[2], $Current[1], $Current[3]) > mktime(0, 0, 0, $Last[2], $Last[1], $Last[3])) {
//		unset($_SESSION['psr']);
//		die(header('location: ros_pro_sec_v2.php?codigo_error=2'));
//	}
	
	// Almacenar temporalmente datos de hoja diaria
	if (isset($_POST['screen']) && $_POST['screen'] == "hd")
		$_SESSION['psr']['hd'] = $_POST;
	
	$users = array(1, 4, 18, 19);
	
	$tpl->newBlock("compras");
	$tpl->assign("bloqueo", in_array($_SESSION['iduser'], $users) ? "0" : "1");
	$tpl->assign("num_cia", $_SESSION['psr']['num_cia']);
	$tpl->assign("nombre", $_SESSION['psr']['nombre']);
	$tpl->assign("fecha", $_SESSION['psr']['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_SESSION['psr']['fecha'], $tmp);
	$tpl->assign("fecha_pago", date("d/m/Y", mktime(0, 0, 0, $tmp[2], $tmp[1] + 1, $tmp[3])));
	
	// Listado de proveedores
	$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_proveedor");
	foreach ($pros as $pro) {
		$tpl->newBlock("pro");
		$tpl->assign("num_pro", $pro['num_pro']);
		$tpl->assign("nombre", str_replace(array("\""), array("'"), $pro['nombre']));
	}
	
	$sql = "SELECT codmp, nombre, precio_compra, precio_unidad FROM catalogo_mat_primas LEFT JOIN inventario_real USING (codmp) LEFT JOIN precios_guerra USING (codmp)";
	$sql .= " WHERE precios_guerra.num_cia = {$_SESSION['psr']['num_cia']} AND inventario_real.num_cia = {$_SESSION['psr']['num_cia']} AND (tipo_cia = 'FALSE' OR codmp = 170) ORDER BY codmp";
	$mps = $db->query($sql);
	foreach ($mps as $mp) {
		$tpl->newBlock("codmp");
		$tpl->assign("codmp", $mp['codmp']);
		$tpl->assign("nombre", $mp['nombre']);
		$tpl->assign("precio", $mp['precio_compra'] > 0 ? $mp['precio_compra'] : 0);
		$tpl->assign("min", $mp['precio_unidad'] > 0 ? $mp['precio_unidad'] * 0.80 : 0);
		$tpl->assign("max", $mp['precio_unidad'] > 0 ? $mp['precio_unidad'] * 1.20 : 0);
	}
	
	// [2007/Feb/23] Datos automáticos
	$inicio = 0;
	$total = 0;
	if (!isset($_SESSION['psr']['cd'])) {
		$sql = "SELECT mov_inv_tmp.id, codmp, nombre, cantidad, kilos, precio_compra, precio_unidad, aplica, mov_inv_tmp.num_proveedor, num_fact FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas";
		$sql .= " USING (codmp) LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN inventario_real USING (num_cia, codmp) WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND";
		$sql .= " tipomov = 'FALSE' AND codmp NOT IN (160, 600, 700, 297, 363, 352) ORDER BY codmp";
		$movs = $db->query($sql);
		
		if ($movs) {
			foreach ($movs as $i => $mov) {
				$tpl->newBlock('cdrow');
				$tpl->assign('i', $i);
				$tpl->assign('next', $i + 1);
				$tpl->assign("codmp", $mov['codmp']);
				$tpl->assign("nombre_mp", $mov['nombre']);
				$tpl->assign("cantidad", number_format($mov['cantidad'], 2, '.', ','));
				$tpl->assign("precio", $mov['precio_compra']);
				$tpl->assign("importe", number_format($mov['cantidad'] * $mov['precio_compra'], 2, '.', ','));
				$tpl->assign("checked", "checked");
				$tpl->assign("num_pro", 289);
				$tpl->assign("nombre_pro", "COMPRAS DIRECTAS");
				$total += $mov['cantidad'] * $mov['precio_compra'];
			}
			$inicio = count($movs);
		}
	}
	
	$numfilas = 15;
	for ($i = $inicio; $i < $numfilas; $i++) {
		$tpl->newBlock("cdrow");
		$tpl->assign("i", $i);
		$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
		
		if (isset($_SESSION['psr']['cd']) && $_SESSION['psr']['cd']['codmp'][$i] > 0) {
			$tpl->assign("codmp", $_SESSION['psr']['cd']['codmp'][$i]);
			$tpl->assign("nombre_mp", $_SESSION['psr']['cd']['nombre_mp'][$i]);
			$tpl->assign("cantidad", $_SESSION['psr']['cd']['cantidad'][$i]);
			$tpl->assign("kilos", $_SESSION['psr']['cd']['kilos'][$i]);
			$tpl->assign("precio", $_SESSION['psr']['cd']['precio'][$i]);
			$tpl->assign("importe", $_SESSION['psr']['cd']['importe'][$i]);
			$tpl->assign("checked", isset($_SESSION['psr']['cd']['aplica_gasto' . $i]) ? "checked" : "");
			$tpl->assign("num_pro", $_SESSION['psr']['cd']['num_pro'][$i]);
			$tpl->assign("nombre_pro", $_SESSION['psr']['cd']['nombre_pro'][$i]);
			$tpl->assign("folio", $_SESSION['psr']['cd']['folio'][$i]);
		}
	}
	$tpl->assign("compras.total", isset($_SESSION['psr']['cd']) ? $_SESSION['psr']['cd']['total'] : number_format($total, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

// PROCESO SECUENCIAL : HOJA DIARIA
if (isset($_POST['next_screen']) && $_POST['next_screen'] == "hd") {
	// Almacenar temporalmente datos de compra directa
	if (isset($_POST['screen']) && $_POST['screen'] == "cd")
		$_SESSION['psr']['cd'] = $_POST;
	// Almacenar temporalmente datos de gastos
	if (isset($_POST['screen']) && $_POST['screen'] == "gs")
		$_SESSION['psr']['gs'] = $_POST;
	
	// Obtener datos de materia prima
	$sql = "SELECT codmp, nombre_alt AS nombre, existencia, precio_unidad, precio_venta, no_exi FROM inventario_real LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp NOT IN (90, 425, 194, 138, 364, 167, 61, /*170,*/ 169) AND (precio_venta > 0 OR codmp = 925) ORDER BY catalogo_mat_primas.orden, precio_venta";
	$mps = $db->query($sql);
	
	// Obtener entradas mayores a la fecha de captura
	$sql = "SELECT codmp, sum(cantidad) AS cantidad FROM fact_rosticeria WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha_mov > '{$_SESSION['psr']['fecha']}' GROUP BY codmp ORDER BY codmp";
	$facs = $db->query($sql);
	
	function buscarFac($codmp) {
		global $facs;
		
		if (!$facs)
			return FALSE;
		
		foreach ($facs as $fac)
			if ($fac['codmp'] == $codmp)
				return $fac['cantidad'];
		
		return FALSE;
	}
	
	function buscarEntrada($codmp) {
		$cantidad = 0;
		foreach ($_SESSION['psr']['cd']['codmp'] as $i => $mp)
			if ($mp == $codmp && $_SESSION['psr']['cd']['importe'][$i] > 0 && $_SESSION['psr']['cd']['folio'][$i] > 0)
				$cantidad += floatval(str_replace(",", "", $_SESSION['psr']['cd']['cantidad'][$i]));
		
		return $cantidad;
	}
	
	// [2007-May-08] Validar productos
	$sql = "SELECT codmp, nombre, precio FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND codmp NOT IN (SELECT codmp FROM inventario_real LEFT JOIN precios_guerra USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp NOT IN (90, 425, 194, 138, 364, 167, 61, /*170,*/ 169) AND (precio_venta > 0 OR codmp = 925))";
	$news = $db->query($sql);
	if ($news) {
		$tpl->newBlock('nuevos_pro');
		foreach ($news as $reg) {
			$tpl->newBlock('new_pro');
			$tpl->assign('codmp', $reg['codmp']);
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('precio', number_format($reg['precio'], 2));
		}
		$tpl->printToScreen();
		die;
	}
	
	// [2007-Feb-23] Obtener datos temporales *******************
	$sql = "SELECT codmp, cantidad, precio FROM mov_inv_tmp AS mov WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND tipomov = 'TRUE' AND (precio > 0 OR codmp = 925) AND cantidad != 0";
	$movs = $db->query($sql);
	
	// [2007-May-08] Validar precios
	/*$tmp = array();
	foreach ($movs as $reg)
		if ($reg['precio'] != $reg['precio_venta'])
			$reg*/
	
	function buscar_tmp($codmp, $precio_venta) {
		global $movs;
		
		if (!$movs)
			return FALSE;
		
		foreach ($movs as $mov)
			if ($mov['codmp'] == $codmp && $mov['precio'] == $precio_venta)
				return $mov;
		
		return FALSE;
	}
	//***********************************************************
	
	$tpl->newBlock("hoja");
	$tpl->assign("num_cia", $_SESSION['psr']['num_cia']);
	$tpl->assign("nombre", $_SESSION['psr']['nombre']);
	$tpl->assign("fecha", $_SESSION['psr']['fecha']);
	
	// [21-Sep-2010] Validar prestamos conforme a los saldos procedenetes de rosticerias y los saldos en el sistema
	$sql = '
		SELECT
			SUM(
				CASE
					WHEN tipo_mov = \'FALSE\' THEN
						importe
					ELSE
						-importe
				END
			)
				AS
					saldo
		FROM
			prestamos
		WHERE
				num_cia = ' . $_SESSION['psr']['num_cia'] . '
			AND
				pagado = \'FALSE\'
	';
	$tmp = $db->query($sql);
	$saldo_sistema = $tmp[0]['saldo'] != 0 ? $tmp[0]['saldo'] : 0;
	
	$sql = '
		SELECT
			SUM(saldo)
				AS
					saldo
		FROM
			prestamos_tmp
		WHERE
				num_cia = ' . $_SESSION['psr']['num_cia'] . '
			AND
				fecha = \'' . $_SESSION['psr']['fecha'] . '\'
	';
	$tmp = $db->query($sql);
	$saldo_archivo = $tmp[0]['saldo'] != 0 ? $tmp[0]['saldo'] : 0;
	
	if ($saldo_sistema == $saldo_archivo) {
		$sql = '
			SELECT
				id
			FROM
				prestamos_tmp
			WHERE
					num_cia = ' . $_SESSION['psr']['num_cia'] . '
				AND
					fecha = \'' . $_SESSION['psr']['fecha'] . '\'
				AND
					tipo_mov = \'TRUE\'
			LIMIT 1
		';
		$abonos = $db->query($sql);
		
		if ($abonos) {
			$tpl->assign('hoja.prestamos', '<p style="font-size:16pt; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Existen nuevos prestamos por codificar</p>');
			$tpl->assign('hoja.disabled', ' disabled');
		}
	}
	else {
		$tpl->assign('hoja.prestamos', '<p style="font-size:16pt; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Los saldos de prestamos de la rosticeria y el sistema no coinciden [SALDO SISTEMA = ' . number_format($saldo_sistema, 2, '.', ',') . ', SALDO ARCHIVO = ' . number_format($saldo_archivo, 2, '.', ',') . ']</p>');
		//$tpl->assign('hoja.disabled', ' disabled');
	}
	
	// [2007-Feb-25] Revisar si existen prestamos temporales
//	if ((($saldo_ros = $db->query("SELECT sum(CASE WHEN tipo_mov = 'TRUE' THEN importe ELSE 0 END) AS importe, sum(saldo) AS saldo FROM prestamos_tmp WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND tipo_mov = 'TRUE' LIMIT 1")) && !isset($_SESSION['psr']['pp'])) != 0) {
//		// [21-Ago-2009] Validar que los saldos de los prestamos pendientes sean igual a los saldos en rosticeria
//		$sql = 'SELECT sum(CASE WHEN tipo_mov = \'FALSE\' THEN importe ELSE -importe END) AS saldo FROM prestamos WHERE num_cia = ' . $_SESSION['psr']['num_cia'] . ' AND pagado = \'FALSE\'';
//		$saldo_sis = $db->query($sql);
//		
//		$sql = 'SELECT sum(saldo) AS saldo FROM prestamos_tmp';
//		
//		
//		$abonos = 
//		
//		if ($saldo_ros[0]['saldo'] != $saldo_sis[0]['saldo']) {
//			$tpl->assign('hoja.prestamos', '<p style="font-size:16pt; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Los saldos de prestamos de la rosticeria y el sistema no coinciden</p>');
//			$tpl->assign('hoja.disabled', ' disabled');
//		}
//		else if ($saldo_ros[0]['importe'] > 0) {
//			$tpl->assign('hoja.prestamos', '<p style="font-size:16pt; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Existen nuevos prestamos por codificar</p>');
//			$tpl->assign('hoja.disabled', ' disabled');
//		}
//	}
	
	foreach ($mps as $mp)
		$arrastre[$mp['codmp']] = 0;
	
	$i = 0;
	$numfilas = count($mps);
	$total = 0;
	$cod_exc = array(717, 718, 719, 804, 761, 726, 817, 732);	// [01-Nov-2007] Códigos exclusivos que no manejan existencia
	foreach ($mps as $key => $mp) {
		$tpl->newBlock("hdrow");
		$tpl->assign("i", $i);
		$tpl->assign("next", $i < $numfilas - 1 ? "cantidad[" . ($i + 1) . "]" : "otros");
		$tpl->assign("codmp", $mp['codmp']);
		$tpl->assign("nombre", $mp['nombre']);
		$tpl->assign("no_exi", $mp['no_exi']);
		$existencia = $mp['existencia'] - buscarFac($mp['codmp']) + buscarEntrada($mp['codmp']);
		$tpl->assign("existencia", $existencia != 0 && /*!in_array($mp['codmp'], $cod_exc)*/$mp['no_exi'] == 'f' ? number_format($existencia, 0, "", ",") : "");
		$tpl->assign("precio", $mp['precio_venta'] > 0 ? number_format($mp['precio_venta'], 2, ".", ",") : "");
		$tpl->assign("codprecio", $mp['codmp']);
		//$tpl->assign("readonly", $existencia <= 0 ? "readonly" : "");
		
		if (!isset($_SESSION['psr']['hd']) && ($mov = buscar_tmp($mp['codmp'], $mp['precio_venta']))) {
			$tpl->assign("cantidad", number_format($mov['cantidad'], 0, '', ','));
			$tpl->assign("existencia_real", $existencia - $mov['cantidad'] - $arrastre[$mp['codmp']] != 0 && /*!in_array($mp['codmp'], $cod_exc)*/$mp['no_exi'] == 'f' ? number_format($existencia - $mov['cantidad'] - $arrastre[$mp['codmp']], 0, "", ",") : "");
			$tpl->assign("importe", number_format($mov['cantidad'] * $mp['precio_venta'], 2, '.', ','));
			$total += $mov['cantidad'] * $mp['precio_venta'];
			
			$arrastre[$mp['codmp']] += $mov['cantidad'];
		}
		else if (isset($_SESSION['psr']['hd']) && $_SESSION['psr']['hd']['cantidad'][$i] > 0) {
			$cantidad = intval(str_replace(",", "", $_SESSION['psr']['hd']['cantidad'][$i]));
			$tpl->assign("cantidad", $_SESSION['psr']['hd']['cantidad'][$i]);
			$tpl->assign("existencia_real", $existencia - $cantidad != 0 && /*!in_array($mp['codmp'], $cod_exc)*/$mp['no_exi'] == 'f' ? number_format($existencia - $cantidad, 0, "", ",") : "");
			$tpl->assign("importe", $_SESSION['psr']['hd']['importe'][$i]);
		}
		else
			$tpl->assign("existencia_real", $existencia - $arrastre[$mp['codmp']] != 0 && /*!in_array($mp['codmp'], $cod_exc)*/$mp['no_exi'] == 'f' ? number_format($existencia - $arrastre[$mp['codmp']], 0, "", ",") : "");
		
		$i++;
		
		/*if ($mp['codmp'] == 160) {
			$precio_venta = $db->query("SELECT precio_venta FROM precios_guerra WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp = 1601");
			
			$numfilas++;
			$tpl->newBlock("hdrow");
			$tpl->assign("i", $i);
			$tpl->assign("next", $i < $numfilas - 1 ? "cantidad[" . ($i + 1) . "]" : "otros");
			$tpl->assign("codmp", $mp['codmp']);
			$tpl->assign("nombre", "POLLOS ADOBADOS");
			$tpl->assign("no_exi", $mp['no_exi']);
			$existencia = $mp['existencia'] - buscarFac($mp['codmp']) + buscarEntrada($mp['codmp']);
			$tpl->assign("existencia", $existencia != 0 ? number_format($existencia, 0, "", ",") : "");
			$tpl->assign("precio", $precio_venta ? number_format($precio_venta[0]['precio_venta'], 2, ".", ",") : "");
			$tpl->assign("codprecio", 1601);
			$tpl->assign("readonly", $existencia <= 0 ? "readonly" : "");
			
			if (isset($_SESSION['psr']['hd']) && $_SESSION['psr']['hd']['cantidad'][$i] > 0) {
				$cantidad = intval(str_replace(",", "", $_SESSION['psr']['hd']['cantidad'][$i]));
				$tpl->assign("cantidad", $_SESSION['psr']['hd']['cantidad'][$i]);
				$tpl->assign("existencia_real", $existencia - $cantidad != 0 ? number_format($existencia - $cantidad, 0, "", ",") : "");
				$tpl->assign("importe", $_SESSION['psr']['hd']['importe'][$i]);
			}
			else
				$tpl->assign("existencia_real", $existencia != 0 ? number_format($existencia, 0, "", ",") : "");
			
			$i++;
		}*/
	}
	$tpl->assign("hoja.i", $i);
	$tpl->assign("hoja.otros", isset($_SESSION['psr']['hd']) ? $_SESSION['psr']['hd']['otros'] : "");
	$tpl->assign("hoja.total", isset($_SESSION['psr']['hd']) ? $_SESSION['psr']['hd']['total'] : number_format($total, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

// PROCESO SECUENCIAL : GASTOS
if (isset($_POST['next_screen']) && $_POST['next_screen'] == "gs") {
	// Almacenar temporalmente datos de la hoja diaria
	if (isset($_POST['screen']) && $_POST['screen'] == "hd")
		$_SESSION['psr']['hd'] = $_POST;
	
	$tpl->newBlock("gastos");
	$tpl->assign("num_cia", $_SESSION['psr']['num_cia']);
	$tpl->assign("nombre", $_SESSION['psr']['nombre']);
	$tpl->assign("fecha", $_SESSION['psr']['fecha']);
	
	$gastos = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
	foreach ($gastos as $gasto) {
		$tpl->newBlock("gasto");
		$tpl->assign("codgastos", $gasto['codgastos']);
		$tpl->assign("descripcion", $gasto['descripcion']);
	}
	
	// [2007-Feb-23] Obtener gastos temporales
	$inicio = 0;
	if (!isset($_SESSION['psr']['gs'])) {
		$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}'";
		$result = $db->query($sql);
		
		if ($result) {
			foreach ($result as $i => $reg) {
				$tpl->newBlock("gsrow");
				$tpl->assign("i", $i);
				$tpl->assign("next", $i + 1);
				$tpl->assign('concepto', trim(strtoupper($reg['concepto'])));
				$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			}
			$inicio = count($result);
		}
	}
	
	// [25-feb-2007] Revisar si existen prestamos
	if ($db->query("SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND tipo_mov = 'FALSE' LIMIT 1") && !isset($_SESSION['psr']['p'])) {
		$tpl->assign('gastos.prestamos', '<p style="font-size:16pt; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Existen nuevos prestamos por codificar</p>');
		$tpl->assign('gastos.disabled', ' disabled');
	}
	
	$numfilas = 10;
	for ($i = $inicio; $i < $numfilas; $i++) {
		$tpl->newBlock("gsrow");
		$tpl->assign("i", $i);
		$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
		
		if (isset($_SESSION['psr']['gs'])/* && $_SESSION['psr']['gs']['codgastos'][$i] > 0*/) {
			$tpl->assign("codgastos", $_SESSION['psr']['gs']['codgastos'][$i]);
			$tpl->assign("nombre_gasto", $_SESSION['psr']['gs']['nombre_gasto'][$i]);
			$tpl->assign("concepto", $_SESSION['psr']['gs']['concepto'][$i]);
			$tpl->assign("importe", $_SESSION['psr']['gs']['importe'][$i]);
		}
	}
	
	$compras = 0;
	foreach ($_SESSION['psr']['cd']['importe'] as $i => $importe)
		if (isset($_SESSION['psr']['cd']['aplica_gasto' . $i]))
			$compras += floatval(str_replace(",", "", $importe));
	
	$tpl->assign("gastos.total", isset($_SESSION['psr']['gs']) ? $_SESSION['psr']['gs']['total'] : "0.00");
	$tpl->assign("gastos.compras", number_format($compras, 2, ".", ","));
	$tpl->assign("gastos.total_gastos", isset($_SESSION['psr']['gs']) ? $_SESSION['psr']['gs']['total_gastos'] : "0.00");
	
	$tpl->printToScreen();
	die;
}

// PROCESO SECUENCIAL : RESULTADOS
if (isset($_POST['next_screen']) && $_POST['next_screen'] == "result") {
	// Almacenar temporalmente datos de la hoja diaria
	if (isset($_POST['screen']) && $_POST['screen'] == "gs")
		$_SESSION['psr']['gs'] = $_POST;
	
	$tpl->newBlock("result");
	$tpl->assign("num_cia", $_SESSION['psr']['num_cia']);
	$tpl->assign("nombre", $_SESSION['psr']['nombre']);
	$tpl->assign("fecha", $_SESSION['psr']['fecha']);
	
	$ventas = floatval(str_replace(",", "", $_SESSION['psr']['hd']['total']));
	$pago_prestamos = isset($_SESSION['psr']['pp']) ? floatval(str_replace(",", "", $_SESSION['psr']['pp']['total'])) : 0;
	$gastos = floatval(str_replace(",", "", $_SESSION['psr']['gs']['total_gastos']));
	$prestamos = isset($_SESSION['psr']['p']) ? floatval(str_replace(",", "", $_SESSION['psr']['p']['total'])) : 0;
	
	$efectivo = $ventas + $pago_prestamos - $gastos - $prestamos;
	
	$tpl->assign("ventas", number_format($ventas, 2, ".", ","));
	$tpl->assign("pago_prestamos", number_format($pago_prestamos, 2, ".", ","));
	$tpl->assign("gastos", number_format($gastos, 2, ".", ","));
	$tpl->assign("prestamos", number_format($prestamos, 2, ".", ","));
	$tpl->assign("efectivo", $efectivo >= 0 ? number_format($efectivo, 2, ".", ",") : "<span style=\"color: #CC0000;\">" . number_format($efectivo, 2, ".", ",") . "</span>");
	
	$tpl->assign("rventas", $ventas + $pago_prestamos);
	$tpl->assign("rgastos", $gastos + $prestamos);
	$tpl->assign("refectivo", $efectivo);
	
	$tpl->assign("disabled", $efectivo < 0 ? "disabled" : "");
	$tpl->printToScreen();
	die;
}

if (isset($_POST['next_screen']) && $_POST['next_screen'] == "finish") {
	$num_cia = $_SESSION['psr']['num_cia'];
	$fecha = $_SESSION['psr']['fecha'];
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $tmp);
	$fecha_his = date("d/m/Y", mktime(0, 0, 0, $tmp[2] - 1, 0, $tmp[3]));
	$fecha_ven = date("d/m/Y", mktime(0, 0, 0, $tmp[2], $tmp[1] + 1, $tmp[3]));
	$cd = array();
	$hd = array();
	$gs = array();
	$mv = array();
	$p = array();
	$pp = array();
	$sql = "";
	
	$tmp = $_SESSION['psr']['cd'];
	$cont_cd = 0;
	$cont_gs = 0;
	$cont_mv = 0;
	for ($i = 0; $i < count($tmp['codmp']); $i++)
		if ($tmp['codmp'][$i] > 0 && floatval(str_replace(",", "", $tmp['importe'][$i])) > 0 && $tmp['folio'][$i] > 0) {
			$cd[$cont_cd]['codmp'] = $tmp['codmp'][$i];
			$cd[$cont_cd]['num_proveedor'] = $tmp['num_pro'][$i];
			$cd[$cont_cd]['num_cia'] = $num_cia;
			$cd[$cont_cd]['numero_fact'] = $tmp['folio'][$i];
			$cd[$cont_cd]['fecha_mov'] = $fecha;
			$cd[$cont_cd]['cantidad'] = floatval(str_replace(",", "", $tmp['cantidad'][$i]));
			$cd[$cont_cd]['kilos'] = floatval(str_replace(",", "", $tmp['kilos'][$i]));
			$cd[$cont_cd]['precio_unit'] = floatval(str_replace(",", "", $tmp['precio'][$i]));
			$cd[$cont_cd]['aplica_gasto'] = isset($tmp['aplica_gasto'][$i]) ? "TRUE" : "FALSE";
			$cd[$cont_cd]['total'] = floatval(str_replace(",", "", $tmp['importe'][$i]));
			$cd[$cont_cd]['fecha_pago'] = $fecha_ven;
			$cd[$cont_cd]['precio_unidad'] = $cd[$cont_cd]['total'] / $cd[$cont_cd]['cantidad'];
			
			$mv[$cont_mv]['num_cia'] = $num_cia;
			$mv[$cont_mv]['codmp'] = $tmp['codmp'][$i];
			$mv[$cont_mv]['fecha'] = $fecha;
			$mv[$cont_mv]['cod_turno'] = 11;
			$mv[$cont_mv]['tipo_mov'] = "FALSE";
			$mv[$cont_mv]['cantidad'] = $cd[$cont_cd]['cantidad'];
			$mv[$cont_mv]['precio'] = $cd[$cont_cd]['precio_unit'];
			$mv[$cont_mv]['total_mov'] = $cd[$cont_cd]['total'];
			$mv[$cont_mv]['precio_unidad'] = $cd[$cont_cd]['precio_unidad'];
			$mv[$cont_mv]['descripcion'] = "COMPRA F. NO. {$tmp['folio'][$i]}";
			
			if ($mp = $db->query("SELECT idinv, existencia, precio_unidad FROM inventario_real WHERE num_cia = $num_cia AND codmp = {$tmp['codmp'][$i]}")) {
				$precio = ($mv[$cont_mv]['total_mov'] + $mp[0]['existencia'] * $mp[0]['precio_unidad']) / ($mv[$cont_mv]['cantidad'] + $mp[0]['existencia']);
				$sql .= "UPDATE inventario_real SET existencia = existencia + {$mv[$cont_mv]['cantidad']}, precio_unidad = " . ($precio > 0 ? $precio : $mp[0]['precio_unidad']) . " WHERE idinv = {$mp[0]['idinv']};\n";
			}
			else {
				$sql .= "INSERT INTO inventario_real (num_cia, codmp, existencia, precio_unidad) VALUES ($num_cia, {$tmp['codmp'][$i]}, {$mv[$cont_mv]['cantidad']}, {$mv[$cont_mv]['precio_unidad']});\n";
				$sql .= "INSERT INTO historico_inventario (num_cia, codmp, existencia, precio_unidad, fecha) VALUES ($num_cia, {$tmp['codmp'][$i]}, 0, 0, '$fecha_his');\n";
			}
			
			if (isset($tmp['aplica_gasto'][$i])) {
				$gs[$cont_gs]['codgastos'] = 23;
				$gs[$cont_gs]['num_cia'] = $num_cia;
				$gs[$cont_gs]['fecha'] = $fecha;
				$gs[$cont_gs]['importe'] = $cd[$cont_cd]['total'];
				$gs[$cont_gs]['captura'] = "FALSE";
				$gs[$cont_gs]['concepto'] = "COMPRA F. NO. {$tmp['folio'][$i]}";
				$cont_gs++;
			}
			
			$cont_cd++;
			$cont_mv++;
		}
	
	$tmp = $_SESSION['psr']['hd'];
	$cont_hd = 0;
	$cod_exc = array(717, 718, 719, 804, 761, 726, 817, 732);
	for ($i = 0; $i < count($tmp['codmp']); $i++)
		if (floatval(str_replace(",", "", $tmp['cantidad'][$i])) > 0) {
			$hd[$cont_hd]['num_cia'] = $num_cia;
			$hd[$cont_hd]['codmp'] = $tmp['codmp'][$i];
			$hd[$cont_hd]['fecha'] = $fecha;
			$hd[$cont_hd]['unidades'] = floatval(str_replace(",", "", $tmp['cantidad'][$i]));
			$hd[$cont_hd]['precio_unitario'] = floatval(str_replace(",", "", $tmp['precio'][$i]));
			$hd[$cont_hd]['precio_total'] = floatval(str_replace(",", "", $tmp['importe'][$i]));
			
			$mv[$cont_mv]['num_cia'] = $num_cia;
			$mv[$cont_mv]['codmp'] = $tmp['codmp'][$i];
			$mv[$cont_mv]['fecha'] = $fecha;
			$mv[$cont_mv]['cod_turno'] = 11;
			$mv[$cont_mv]['tipo_mov'] = "TRUE";
			$mv[$cont_mv]['cantidad'] = $hd[$cont_hd]['unidades'];
			$mv[$cont_mv]['precio'] = $hd[$cont_hd]['precio_unitario'];
			$mv[$cont_mv]['total_mov'] = $hd[$cont_hd]['precio_total'];
			$mv[$cont_mv]['precio_unidad'] = $hd[$cont_hd]['precio_unitario'];
			$mv[$cont_mv]['descripcion'] = "CONSUMO DEL DIA";
			
			if (($mp = $db->query("SELECT idinv, existencia FROM inventario_real WHERE num_cia = $num_cia AND codmp = {$tmp['codmp'][$i]}")) && /*!in_array($mv[$cont_mv]['codmp'], $cod_exc)*/$tmp['no_exi'][$i] == 'f') {
				$sql .= "UPDATE inventario_real SET existencia = existencia - {$mv[$cont_mv]['cantidad']} WHERE idinv = {$mp[0]['idinv']};\n";
			}
			else {
				//$sql .= "INSERT INTO inventario_real (num_cia, codmp, existencia, precio_unidad) VALUES ($num_cia, 925, {$mv[$cont_mv]['cantidad']}, {$mv[$cont_mv]['precio_unidad']});\n";
				$sql .= "INSERT INTO historico_inventario (num_cia, codmp, existencia, precio_unidad, fecha) VALUES ($num_cia, {$tmp['codmp'][$i]}, 0, 0, '$fecha_his');\n";
			}
			
			$cont_hd++;
			$cont_mv++;
			
			/*
			@ [31-Ene-2011] Para los códigos de pollos descontar automaticamente el código 925 Domo
			*/
			if (/*in_array($tmp['codmp'][$i], array(160, 600, 700, 573, 334))*/FALSE) {
				if (!($id_domo = $db->query('
					SELECT
						idinv
					FROM
						historico_inventario
					WHERE
							num_cia = ' . $num_cia . '
						AND
							codmp = 925
						AND
							fecha = \'' . $fecha_his . '\'
				'))) {
					$sql .= '
						INSERT INTO
							historico_inventario
								(
									num_cia,
									codmp,
									fecha,
									existencia,
									precio_unidad
								)
							VALUES
								(
									' . $num_cia . ',
									925,
									\'' . $fecha_his . '\',
									0,
									0
								)
					' . ";\n";
				}
				
				if (!($id_domo = $db->query('
					SELECT
						idinv
					FROM
						inventario_real
					WHERE
							num_cia = ' . $num_cia . '
						AND
							codmp = 925
				'))) {
					$sql .= '
						INSERT INTO
							inventario_real
								(
									num_cia,
									codmp,
									existencia,
									precio_unidad
								)
							VALUES
								(
									' . $num_cia . ',
									925,
									0,
									0
								)
					' . ";\n";
				}
				
				$precio_domo = $db->query('
					SELECT
						precio_unitario
							AS
								precio
					FROM
						inventario_real
					WHERE
							num_cia = ' . $num_cia . '
						AND
							codmp = 925
				');
				
				$sql = '
					INSERT INTO
						mov_inv_real
							(
								num_cia,
								codmp,
								fecha,
								cod_turno,
								tipo_mov,
								cantidad,
								precio,
								total_mov,
								precio_unidad,
								descripcion
							)
						VALUES
							(
								' . $num_cia . ',
								925,
								\'' . $fecha . '\',
								11,
								\'TRUE\',
								' . get_val($tmp['cantidad'][$i]) . ',
								' . ($precio_domo ? $precio_domo[0]['precio'] : 0) . ',
								' . ($precio_domo ? $precio_domo[0]['precio'] * get_val($tmp['cantidad'][$i]) : 0) . ',
								' . ($precio_domo ? $precio_domo[0]['precio'] : 0) . ',
								\'CONSUMO DEL DIA\'
							)
				' . ";\n";
			}
		}
	
	if (floatval(str_replace(",", "", $tmp['otros'])) > 0) {
		$importe = floatval(str_replace(",", "", $tmp['otros']));
		$sql .= "INSERT INTO hoja_diaria_ros_otros (num_cia, importe) VALUES ($num_cia, $importe);\n";
	}
	
	$tmp = $_SESSION['psr']['gs'];
	for ($i = 0; $i < count($tmp['codgastos']); $i++)
		if ($tmp['codgastos'][$i] > 0 && $tmp['importe'][$i] > 0) {
			$gs[$cont_gs]['codgastos'] = $tmp['codgastos'][$i];
			$gs[$cont_gs]['num_cia'] = $num_cia;
			$gs[$cont_gs]['fecha'] = $fecha;
			$gs[$cont_gs]['importe'] = floatval(str_replace(",", "", $tmp['importe'][$i]));
			$gs[$cont_gs]['captura'] = "FALSE";
			$gs[$cont_gs]['concepto'] = strtoupper($tmp['concepto'][$i]);
			
			// [11-Sep-2007] Código 90 GAS insertar movimiento de entrada en inventario
			if ($gs[$cont_gs]['codgastos'] == 90) {
				$mv[$cont_mv]['num_cia'] = $num_cia;
				$mv[$cont_mv]['codmp'] = 90;
				$mv[$cont_mv]['fecha'] = $fecha;
				$mv[$cont_mv]['tipo_mov'] = "FALSE";
				$mv[$cont_mv]['cantidad'] = get_val($tmp['cantidad'][$i]);
				$mv[$cont_mv]['precio'] = get_val($tmp['importe'][$i]) / get_val($tmp['cantidad'][$i]);
				$mv[$cont_mv]['total_mov'] = get_val($tmp['importe'][$i]);
				$mv[$cont_mv]['precio_unidad'] = get_val($tmp['importe'][$i]) / get_val($tmp['cantidad'][$i]);
				$mv[$cont_mv]['descripcion'] = "COMPRA DIRECTA GAS";
				
				if ($mp = $db->query("SELECT idinv, existencia FROM inventario_real WHERE num_cia = $num_cia AND codmp = 90")) {
					$sql .= "UPDATE inventario_real SET existencia = existencia + {$mv[$cont_mv]['cantidad']} WHERE idinv = {$mp[0]['idinv']};\n";
				}
				else {
					$sql .= "INSERT INTO inventario_real (num_cia, codmp, existencia, precio_unidad) VALUES ($num_cia, 90, {$mv[$cont_mv]['cantidad']}, {$mv[$cont_mv]['precio_unidad']});\n";
					$sql .= "INSERT INTO historico_inventario (num_cia, codmp, existencia, precio_unidad, fecha) VALUES ($num_cia, 90, 0, 0, '$fecha_his');\n";
				}
				
				$cont_mv++;
			}
			
			$cont_gs++;
		}
	
	if (isset($_SESSION['psr']['p'])) {
		$tmp = $_SESSION['psr']['p'];
		
		$cont_p = 0;
		for ($i = 0; $i < count($tmp['id']); $i++)
			if ($tmp['num_emp'][$i] > 0 && floatval(str_replace(",", "", $tmp['importe'][$i])) > 0) {
				$importe = floatval(str_replace(",", "", $tmp['importe'][$i]));
				if ($id = $db->query("SELECT id FROM prestamos WHERE id_empleado = {$tmp['id'][$i]} AND tipo_mov = 'FALSE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1"))
					$sql .= "UPDATE prestamos SET importe = importe + $importe WHERE id = {$id[0]['id']};\n";
				else
					$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $importe, 'FALSE', 'FALSE', {$tmp['id'][$i]});\n";
				
				$gs[$cont_gs]['codgastos'] = 41;
				$gs[$cont_gs]['num_cia'] = $num_cia;
				$gs[$cont_gs]['fecha'] = $fecha;
				$gs[$cont_gs]['importe'] = $importe;
				$gs[$cont_gs]['captura'] = "FALSE";
				$gs[$cont_gs]['concepto'] = "PRESTAMO EMPLEADO ID.: {$tmp['id'][$i]}";
				
				$cont_gs++;
			}
	}
	
	if (isset($_SESSION['psr']['pp'])) {
		$tmp = $_SESSION['psr']['pp'];
		
		$cont_pp = 0;
		for ($i = 0; $i < count($tmp['id']); $i++)
			if (floatval(str_replace(",", "", $tmp['importe'][$i])) > 0) {
				$importe = floatval(str_replace(",", "", $tmp['importe'][$i]));
				$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $importe, 'TRUE', 'FALSE', {$tmp['id'][$i]});\n";
				if (floatval(str_replace(",", "", $tmp['resta_real'][$i])) <= 0)
					$sql .= "UPDATE prestamos SET pagado = 'TRUE' WHERE id_empleado = {$tmp['id'][$i]} AND pagado = 'FALSE';\n";
			}
	}
	
	$sql .= $db->multiple_insert("compra_directa", $cd);
	$sql .= $db->multiple_insert("hoja_diaria_rost", $hd);
	$sql .= $db->multiple_insert("movimiento_gastos", $gs);
	$sql .= $db->multiple_insert("mov_inv_real", $mv);
	
	$sql .= "INSERT INTO total_companias (num_cia, fecha, venta, gastos, efectivo) VALUES ($num_cia, '$fecha', $_POST[ventas], $_POST[gastos], $_POST[efectivo]);\n";
	$db->query($sql);
	
	unset($_SESSION['psr']);
	header("location: ./ros_pro_sec_v2.php");
	die;
}

// PEDIR DATOS INICIALES
$tpl->newBlock("datos");
	
$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 702 AND 720 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
die;
?>