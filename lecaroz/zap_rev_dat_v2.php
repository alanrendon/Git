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

// Obtener importe de las remisiones solicitadas
if (isset($_GET['num'])) {
	$sql = "SELECT num_fact, total, copia_fac, por_aut, folio FROM facturas_zap WHERE clave = $_GET[num] AND num_fact = $_GET[rem]";
	$result = $db->query($sql);
	
	// No existe la factura
	if (!$result) die("-1|$_GET[i]|$_GET[r]");
	// Ya esta pagada la factura
	if ($result[0]['folio'] != '') die("-2|$_GET[i]|$_GET[r]");
	// No tiene copia de factura
	if ($result[0]['copia_fac'] == 'f') die("-3|$_GET[i]|$_GET[r]");
	// El porcentaje de descuento no esta autorizado
	if ($result[0]['por_aut'] == 'f') die("-4|$_GET[i]|$_GET[r]");
	
	// Buscar depositos que hayan pagado parcialmente la factura en anteriores capturas
	$sql = "SELECT num_fact1, pag1, num_fact2, pag2, num_fact3, pag3, num_fact4, pag4 FROM otros_depositos AS od LEFT JOIN catalogo_nombres AS cn ON";
	$sql .= " (cn.id = od.idnombre) WHERE num = $_GET[num] AND ((num_fact1 = $_GET[rem] AND pag1 > 0) OR (num_fact2 = $_GET[rem] AND pag2 > 0) OR";
	$sql .= " (num_fact3 = $_GET[rem] AND pag3 > 0) OR (num_fact4 = $_GET[rem] AND pag4 > 0))";
	$tmp = $db->query($sql);
	$pag = 0;
	if ($tmp)
		foreach ($tmp as $reg)
			for ($i = 1; $i <= 4; $i++)
				if ($reg['num_fact' . $i] == $_GET['rem'])
					$pag += $reg['pag' . $i];
	
	// Construir cadena de retorno
	$data = "$_GET[i]|$_GET[r]|{$result[0]['total']}|$pag";
	// Terminar programa y enviar resultado
	die($data);
}

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/zap/zap_rev_dat_v2.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

/************************************************************************/
/************************************************************************/
// HOJA DE DATOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'hoja') {
	// ***** VENTAS *****
	$sql = "SELECT * FROM ventadia_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY tipo, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat_v2.php");
		die;
	}
	
	$tpl->newBlock('hoja');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	$total = 0;
	$tipo_tmp = NULL;
	foreach ($result as $reg) {
		if ($tipo_tmp != $reg['tipo']) {
			$tipo_tmp = $reg['tipo'];
			$tpl->newBlock('bloque_venta');
			$subtotal = 0;
		}
		$tpl->newBlock('venta_row');
		switch ($reg['tipo']) {
			case 1: $tipo = 'EFECTIVO'; break;
			case 2: $tipo = 'TARJETA'; break;
			case 3: $tipo = 'AMERICAN EXPRESS'; break;
			case 4: $tipo = 'DEPOSITO'; break;
		}
		$tpl->assign('tipo', $tipo);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$subtotal += $reg['importe'];
		$tpl->assign('bloque_venta.subtotal', number_format($subtotal, 2, '.', ','));
		$total += $reg['importe'];
	}
	$ventas = $total;
	
	//  Esquilmos
	$sql = "SELECT * FROM acreditado_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND trim(nombre) = '' ORDER BY nombre";
	$esq = $db->query($sql);
	$total_esq = 0;
	if ($esq) {
		$tpl->newBlock('esquilmos');
		foreach ($esq as $reg) {
			$tpl->newBlock('esquilmo');
			$tpl->assign('con', $reg['concepto']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$total_esq += $reg['importe'];
			$total += $reg['importe'];
		}
		$tpl->assign('esquilmos.subtotal', number_format($total_esq, 2, '.', ','));
	}
	
	$tpl->assign('hoja.total_venta', number_format($total, 2, '.', ','));
	
	// ***** GASTOS *****
	$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);
	
	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'";
	$pres = $db->query($sql);
	
	$total = 0;
	if ($result || $pres) {
		if ($result)
			foreach ($result as $i => $reg) {
				$tpl->newBlock('gasto_hoja');
				$tpl->assign('concepto', strtoupper($reg['concepto']));
				$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
				$total += $reg['importe'];
			}
		if ($pres)
			foreach ($pres as $reg) {
				$tpl->newBlock('gasto_hoja');
				$tpl->assign('concepto', $reg['nombre']);
				$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
				$total += $reg['importe'];
			}
		
		$tpl->assign('hoja.total_gastos', number_format($total, 2, '.', ','));
	}
	$gastos = $total;
	
	// ***** NOMINAS *****
	$sql = "SELECT * FROM nomina_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND dias > 0 ORDER BY nombre";
	$result = $db->query($sql);
	
	if ($result) {
		$gtotal = 0;
		foreach ($result as $reg) {
			$tpl->newBlock('nom_r');
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
		
		$tpl->assign('hoja.total_nom', number_format($gtotal, 2, '.', ','));
	}
	
	// ***** ACREDITADO *****
	$sql = '
		SELECT
			nombre,
			concepto,
			importe,
			acreditado
		FROM
			acreditado_tmp
		WHERE
				num_cia = ' . $_GET['num_cia'] . '
			AND
				fecha = \'' . $_GET['fecha'] . '\'
			AND
				TRIM(nombre) <> \'\'
		
		UNION
		
		SELECT
			nombre,
			concepto,
			importe,
			acre::text
		FROM
				otros_depositos
					od
			LEFT JOIN
				catalogo_nombres
					cn
						ON
							(
								cn.id = od.idnombre
							)
		WHERE
				num_cia = ' . $_GET['num_cia'] . '
			AND
				fecha = \'' . $_GET['fecha'] . '\'
			AND
				TRIM(nombre) <> \'\'
			AND
				(
					importe
				)
					NOT IN
						(
							SELECT
								importe
							FROM
								acreditado_tmp
							WHERE
									num_cia = ' . $_GET['num_cia'] . '
								AND
									fecha = \'' . $_GET['fecha'] . '\'
								AND
									TRIM(nombre) <> \'\'
						)
		
		ORDER BY
			nombre
	';
	
	$result = $db->query($sql);
	
	$total = 0;
	if ($result) {
		foreach ($result as $reg) {
			$tpl->newBlock('acre');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('concepto', $reg['concepto']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$tpl->assign('acreditado', $reg['acreditado']);
			$total += $reg['importe'];
		}
		
		$tpl->assign('hoja.total_acre', number_format($total, 2, '.', ','));
	}
	$acreditados = $total;
	
	// ***** INTERCAMBIOS *****
	$in = $db->query("SELECT * FROM intercambio_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 1");
	$out = $db->query("SELECT * FROM intercambio_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 2");
	if ($in || $out) {
		$total_in = 0;
		$total_out = 0;
		$rows = count($total_in) >= count($total_out) ? count($total_in) : count($total_out);
		for ($i = 0; $i < $rows; $i++) {
			$tpl->newBlock('inter');
			$tpl->assign('entrada', isset($in[0]['importe']) ? number_format($in[0]['importe'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('salida', isset($out[0]['importe']) ? number_format($out[0]['importe'], 2, '.', ',') : '&nbsp;');
			$total_in += isset($in[0]['importe']) ? $in[0]['importe'] : 0;
			$total_out += isset($out[0]['importe']) ? $out[0]['importe'] : 0;
		}
		$tpl->assign('hoja.entradas', number_format($total_in, 2, '.', ','));
		$tpl->assign('hoja.salidas', number_format($total_out, 2, '.', ','));
	}
	
	// ***** OBSERVACIONES *****
	$sql = "SELECT observacion FROM observaciones_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$obs = $db->query($sql);
	
	if ($obs)
		foreach($obs as $reg) {
			$tpl->newBlock('obs');
			$tpl->assign('obs', strtoupper($reg['observacion']));
		}
	
	// ***** TOTALES ***** [24-Ene-2007]
	$venta_total = $ventas + $gastos;
	$venta_gral = $venta_total + $total_esq;
	$tpl->assign('hoja.venta_total', number_format($venta_total, 2, '.', ','));
	$tpl->assign('hoja.venta_gral', number_format($venta_gral, 2, '.', ','));
	
	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// ACREDITADOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'acre') {
	$sql = "SELECT a.id, a.nombre AS nombre_arc, concepto, acreditado, importe, idnombre, num, cn.nombre AS nombre_cat, acre, cc.nombre_corto AS nombre_acre, num_fact1, pag1, imp1, sal1, num_fact2, pag2, imp2, sal2, num_fact3, pag3, imp3, sal3, num_fact4, pag4, imp4, sal4 FROM acreditado_tmp AS a LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = acre) LEFT JOIN catalogo_nombres AS cn ON (cn.id = idnombre) WHERE a.num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND trim(a.nombre) != ''";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'gastos' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	$tpl->newBlock('acreditados');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	foreach ($result as $i => $reg) {
		$tpl->newBlock('acre_row');
		
		$tpl->assign('i', $i);
		$tpl->assign('index', count($result) > 1 ? "[$i]" : '');
		$tpl->assign('back', count($result) > 1 ? ($i > 0 ? '[' . ($i - 1) . ']' : '[' . (count($result) - 1) . ']') : '');
		$tpl->assign('next', count($result) > 1 ? ($i < count($result) - 1 ? '[' . ($i + 1) . ']' : '[0]') : '');
		
		$tpl->assign('id', $reg['id']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('acreditado', $reg['acreditado']);
		$tpl->assign('acre', $reg['acre']);
		$tpl->assign('nombre_acre', $reg['nombre_acre']);
		$tpl->assign('nombre_arc', $reg['nombre_arc']);
		$tpl->assign('idnombre', $reg['idnombre']);
		$tpl->assign('num', $reg['num']);
		$tpl->assign('nombre', $reg['nombre_cat']);
		$tpl->assign('concepto', $reg['concepto']);
		$tpl->assign('num_fact1', $reg['num_fact1']);
		$tpl->assign('pag1', $reg['pag1']);
		$tpl->assign('imp1', $reg['imp1']);
		$tpl->assign('sal1', $reg['sal1']);
		$tpl->assign('num_fact2', $reg['num_fact2']);
		$tpl->assign('pag2', $reg['pag2']);
		$tpl->assign('imp2', $reg['imp2']);
		$tpl->assign('sal2', $reg['sal2']);
		$tpl->assign('num_fact3', $reg['num_fact3']);
		$tpl->assign('pag3', $reg['pag3']);
		$tpl->assign('imp3', $reg['imp3']);
		$tpl->assign('sal3', $reg['sal3']);
		$tpl->assign('num_fact4', $reg['num_fact4']);
		$tpl->assign('pag4', $reg['pag4']);
		$tpl->assign('imp4', $reg['imp4']);
		$tpl->assign('sal4', $reg['sal4']);
	}
	
	$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia");
	foreach ($cias as $cia) {
		$tpl->newBlock('c_acre');
		$tpl->assign('num', $cia['num_cia']);
		$tpl->assign('nombre', $cia['nombre']);
	}
	
	$nombres = $db->query("SELECT id, num, nombre FROM catalogo_nombres WHERE status = 1");
	if ($nombres)
		foreach ($nombres as $reg) {
			$tpl->newBlock("nom");
			$tpl->assign("num", $reg['num']);
			$tpl->assign("id", $reg['id']);
			$tpl->assign("nombre", $reg['nombre']);
		}
	
	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'acre_mod') {
	$sql = "";
	
	if (!isset($_POST['id'])) {
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'gastos' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['id']); $i++) {
		$sql .= "UPDATE acreditado_tmp SET idnombre = " . ($_POST['idnombre'][$i] > 0 ? $_POST['idnombre'][$i] : 'NULL') . ",";
		$sql .= " acre = " . ($_POST['acre'][$i] > 0 ? $_POST['acre'][$i] : 'NULL') . ",";
		$sql .= " num_fact1 = " . ($_POST['num_fact1'][$i] > 0 ? $_POST['num_fact1'][$i] : 'NULL') . ",";
		$sql .= " num_fact2 = " . ($_POST['num_fact2'][$i] > 0 ? $_POST['num_fact2'][$i] : 'NULL') . ",";
		$sql .= " num_fact3 = " . ($_POST['num_fact3'][$i] > 0 ? $_POST['num_fact3'][$i] : 'NULL') . ",";
		$sql .= " num_fact4 = " . ($_POST['num_fact4'][$i] > 0 ? $_POST['num_fact4'][$i] : 'NULL') . ",";
		$sql .= " pag1 = " . (get_val($_POST['pag1'][$i]) > 0 ? get_val($_POST['pag1'][$i]) : 'NULL') . ",";
		$sql .= " pag2 = " . (get_val($_POST['pag2'][$i]) > 0 ? get_val($_POST['pag2'][$i]) : 'NULL') . ",";
		$sql .= " pag3 = " . (get_val($_POST['pag3'][$i]) > 0 ? get_val($_POST['pag3'][$i]) : 'NULL') . ",";
		$sql .= " pag4 = " . (get_val($_POST['pag4'][$i]) > 0 ? get_val($_POST['pag4'][$i]) : 'NULL') . ",";
		$sql .= " imp1 = " . (get_val($_POST['imp1'][$i]) > 0 ? get_val($_POST['imp1'][$i]) : 'NULL') . ",";
		$sql .= " imp2 = " . (get_val($_POST['imp1'][$i]) > 0 ? get_val($_POST['imp2'][$i]) : 'NULL') . ",";
		$sql .= " imp3 = " . (get_val($_POST['imp1'][$i]) > 0 ? get_val($_POST['imp3'][$i]) : 'NULL') . ",";
		$sql .= " imp4 = " . (get_val($_POST['imp1'][$i]) > 0 ? get_val($_POST['imp4'][$i]) : 'NULL') . ",";
		$sql .= " sal1 = " . (get_val($_POST['sal1'][$i]) > 0 ? get_val($_POST['sal1'][$i]) : 'NULL') . ",";
		$sql .= " sal2 = " . (get_val($_POST['sal2'][$i]) > 0 ? get_val($_POST['sal2'][$i]) : 'NULL') . ",";
		$sql .= " sal3 = " . (get_val($_POST['sal3'][$i]) > 0 ? get_val($_POST['sal3'][$i]) : 'NULL') . ",";
		$sql .= " sal4 = " . (get_val($_POST['sal4'][$i]) > 0 ? get_val($_POST['sal4'][$i]) : 'NULL');
		$sql .= " WHERE id = {$_POST['id'][$i]};\n";
	}
	
	$db->query($sql);
	
	header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'gastos' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'venta') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['codgastos']); $i++) {
		$sql .= "UPDATE gastos_tmp SET codgastos = {$_POST['codgastos'][$i]}, omitir = '" . (isset($_POST['omitir' . $i]) ? 'TRUE' : 'FALSE') . "'";
		$sql .= " WHERE id = {$_POST['id'][$i]};\n";
	}
	
	$db->query($sql);
	
	header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
	
	if (/*!$result*/1) {
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
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
	
	/*$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");*/
	
	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'pres_mod') {
	$sql = "";
	
	if (!isset($_POST['id'])) {
		header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}
	
	for ($i = 0; $i < count($_POST['id_emp']); $i++)
		$sql .= "UPDATE prestamos_tmp SET idemp = {$_POST['id_emp'][$i]} WHERE id = {$_POST['id'][$i]};\n";
	
	$db->query($sql);
	header("location: ./zap_rev_dat_v2.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
	die;
}

/************************************************************************/
/************************************************************************/
// RESULTADO FINAL
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'result') {
	$result = $db->query("SELECT sum(importe) AS importe FROM ventadia_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	
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
	
	$tmp1 = $db->query("SELECT sum(importe) AS importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND omitir = 'FALSE'");
	$tmp2 = $db->query("SELECT sum(importe) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'");
	$gastos = $tmp1[0]['importe'] + $tmp2[0]['importe'];
	
	$venta_total = $result[0]['importe'] + $gastos;
	
	$efectivo = $venta_total - $gastos;
	
	$tpl->assign('venta', $venta_total != 0 ? number_format($venta_total, 2, '.' ,',') : '&nbsp;');
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
	
	$tmp = $db->query("SELECT sum(importe) AS importe FROM ventadia_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	$ventas = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	$tmp = $db->query("SELECT sum(importe) AS importe FROM acreditado_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND trim(nombre) = ''");
	$otros = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	$tmp = $db->query("SELECT sum(importe) AS importe FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND omitir = 'FALSE'");
	$gastos = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	$tmp = $db->query("SELECT sum(importe) AS importe FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha = '$fecha' AND captura = 'FALSE'");
	$_gastos = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	$efectivo = ($ventas + $gastos + $_gastos + $otros) - $gastos - $_gastos;
	
	// Ventas
	if ($id = $db->query("SELECT id FROM efectivos_zap WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql = "UPDATE efectivos_zap SET venta = $ventas + $gastos, otros = $otros WHERE id = {$id[0]['id']};\n";
	else
		$sql = "INSERT INTO efectivos_zap (num_cia, fecha, venta, otros, errores, pares, clientes, nota1, nota2, nota3, nota4) VALUES ($num_cia, '$fecha', $ventas + $gastos, $otros, 0, 0, 0, 0, 0, 0, 0);\n";
	
	// Gastos
	$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, cod_turno, concepto, importe, captura) SELECT num_cia, fecha, codgastos, cod_turno, upper(concepto), importe, 'FALSE' FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND omitir = 'FALSE';\n";
	
	// Acreditados
	//$sql .= "";
	
	// Efectivo
	if ($id = $db->query("SELECT id FROM total_zapaterias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql .= "UPDATE total_zapaterias SET venta = $ventas + $gastos + $_gastos, otros = $otros, gastos = $gastos + $_gastos, efectivo = $efectivo WHERE id = {$id[0]['id']};\n";
	else
		$sql .= "INSERT INTO total_zapaterias (num_cia, fecha, venta, otros, gastos, efectivo) VALUES ($num_cia, '$fecha', $ventas + $gastos + $_gastos, $otros, $gastos + $_gastos, $efectivo);\n";
	
	// Prestamos
	/*$pres = $db->query("SELECT p.num_cia, fecha, idemp, ct.nombre, ap_paterno, ap_materno, tipo_mov, importe FROM prestamos_tmp AS p LEFT JOIN catalogo_trabajadores AS ct ON (ct.id = idemp) WHERE p.num_cia = $num_cia AND fecha = '$fecha' AND importe != 0");
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
	*/
	
	// Actualiza timestamps de autorizacion
	$sql .= "UPDATE ventadia_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE gastos_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE prestamos_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	
	//echo "<pre>$sql</pre>";die;
	$db->query($sql);
	header("location: ./zap_rev_dat_v2.php");
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