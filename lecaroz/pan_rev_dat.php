<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

function toInt($val) {
	return intval($val);
}

// [4-Dic-2007] Poner marca de que se recibio la nomina
/*if (isset($_GET['nom'])) {
	$sql = "UPDATE efectivos_tmp SET nomina = now() WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$db->query($sql);
	echo "Autorizado: " . date('d/m/Y h:ia');
	die();
}*/

$dias_festivos = array(
	'01/01',
	'04/01',
	'05/01',
	'06/01',
	'07/01',
	'14/02',
	'30/04',
	'10/05',
	'30/10',
	'31/10',
	'01/11',
	'02/11',
	'12/12',
	'24/12',
	'25/12',
	'31/12'
);

// [28-Dic-2007] Obtener nomina del mes
if (isset($_GET['getNomina'])) {
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $tmp);

	if (!in_array($tmp[1], array(10, 20, date('n', mktime(0, 0, 0, $tmp[2] + 1, 0, $tmp[3]))))) die(0);

	switch ($tmp[1]) {
		case 10:
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 10, $tmp[3]));
			break;
		case 20:
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 11, $tmp[3]));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 20, $tmp[3]));
			break;
		case date('n', mktime(0, 0, 0, $tmp[2] + 1, 0, $tmp[3])):
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 21, $tmp[3]));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $tmp[2] + 1, 0, $tmp[3]));
			break;
	}

	$sql = "SELECT sum(importe) AS importe FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (1, 160)";
	$result = $db->query($sql);

	echo get_val($result[0]['importe']);
	die();
}

// [4-Dic-2007] Validar que el total de prestamos de la hoja sea igual al del sistema
if (isset($_GET['pres'])) {
	// Saldo de prestamos en sistema
	$sql = "SELECT tipo_mov, sum(importe) AS importe FROM prestamos WHERE num_cia = $_GET[num_cia] AND pagado = 'FALSE' GROUP BY tipo_mov ORDER BY tipo_mov";
	$tmp = $db->query($sql);
	$sis = 0;
	if ($tmp)
		foreach ($tmp as $reg)
			$sis += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];

	$sql = "SELECT sum(saldo) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$tmp = $db->query($sql);
	$hoja = get_val($tmp[0]['importe']);

	$dif = $sis - $hoja;

	echo $dif;
	die();
}

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/pan/pan_rev_dat.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['action']) && $_GET['action'] == 'saltar') {
	$num_cia = $_REQUEST['num_cia'];
	$fecha = $_REQUEST['fecha'];

	$sql = "UPDATE produccion_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
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
	$sql .= "UPDATE his_aut_con_avio SET status = 1, iduser = $_SESSION[iduser], tsmod = now() WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE pasteles_tmp SET status = 1, iduser = $_SESSION[iduser], tsmod = now() WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE tanques_gas_lecturas_tmp SET status = 1, iduser = $_SESSION[iduser], tsmod = now() WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE tanques_gas_entradas_tmp SET status = 1, iduser = $_SESSION[iduser], tsmod = now() WHERE num_cia = $num_cia AND fecha = '$fecha';\n";

	$db->query($sql);

	header('location: pan_rev_dat.php');
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'borrar') {
	$num_cia = $_REQUEST['num_cia'];
	$fecha = $_REQUEST['fecha'];

	$sql = "
		DELETE FROM produccion_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha';
		DELETE FROM mov_exp_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM prueba_pan_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM corte_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM mediciones_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM camionetas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM facturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM prestamos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM pastillaje_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM pasteles_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM tanques_gas_lecturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
		DELETE FROM tanques_gas_entradas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';
	";

	$db->query($sql);

	header('location: pan_rev_dat.php');
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'duplicado') {
	$num_cia = $_REQUEST['num_cia'];
	$fecha = $_REQUEST['fecha'];

	$sql = "
		DELETE FROM produccion_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM produccion_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY cod_producto, cod_turnos, piezas);
		DELETE FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha' AND id NOT IN (SELECT min(id) FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha' GROUP BY codturno);
		DELETE FROM mov_exp_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM mov_exp_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY num_expendio);
		DELETE FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY codmp, cod_turno, tipomov);
		DELETE FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY concepto, importe);
		DELETE FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM prueba_pan_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM prueba_pan_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM corte_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM corte_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY tipo, ticket);
		DELETE FROM mediciones_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM mediciones_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM camionetas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM camionetas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM facturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM facturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY factura);
		DELETE FROM prestamos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM prestamos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY nombre, tipo_mov);
		DELETE FROM pastillaje_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM pastillaje_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM pasteles_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM pasteles_tmp WHERE num_cia = $num_cia AND fecha = '$fecha');
		DELETE FROM tanques_gas_lecturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM tanques_gas_lecturas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY num_cia, fecha, idtanque);
		DELETE FROM tanques_gas_entradas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND id NOT IN (SELECT min(id) FROM tanques_gas_entradas_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' GROUP BY num_cia, fecha, idtanque, nota);
	";

	$db->query($sql);

	header('location: pan_rev_dat.php');
	die;
}


/************************************************************************/
/************************************************************************/
// HOJA DIARIA
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'hoja') {
	$sql = "SELECT * FROM efectivos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./pan_rev_dat.php");
		die;
	}

	$tpl->newBlock('hoja');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	// Efectivos
	$tpl->assign('am', $result[0]['cajaam'] != 0 ? number_format($result[0]['cajaam'], 2, '.', ',') : '');
	$tpl->assign('clientes_am', $result[0]['clientesam'] != 0 ? number_format($result[0]['clientesam']) : '');
	$tpl->assign('error_am', $result[0]['erroramcaja'] != 0 ? number_format($result[0]['erroramcaja'], 2, '.', ',') : '');
	$tpl->assign('error_clientes_am', $result[0]['erroramclientes'] != 0 ? number_format($result[0]['erroramclientes']) : '');
	$tpl->assign('pm', $result[0]['cajapm'] != 0 ? number_format($result[0]['cajapm'], 2, '.', ',') : '');
	$tpl->assign('clientes_pm', $result[0]['clientespm'] != 0 ? number_format($result[0]['clientespm']) : '');
	$tpl->assign('error_pm', $result[0]['errorpmcaja'] != 0 ? number_format($result[0]['errorpmcaja'], 2, '.', ',') : '');
	$tpl->assign('error_clientes_pm', $result[0]['errorpmclientes'] != 0 ? number_format($result[0]['errorpmclientes']) : '');
	$tpl->assign('pastel_am', $result[0]['pastelam'] != 0 ? number_format($result[0]['pastelam'], 2, '.', ',') : '');
	$tpl->assign('clientes_am_pastel', $result[0]['clientespastelam'] != 0 ? number_format($result[0]['clientespastelam']) : '');
	$tpl->assign('pastel_pm', $result[0]['pastelpm'] != 0 ? number_format($result[0]['pastelpm'], 2, '.', ',') : '');
	$tpl->assign('clientes_pm_pastel', $result[0]['clientespastelpm'] != 0 ? number_format($result[0]['clientespastelpm']) : '');

	$total_caja = $result[0]['cajaam'] - $result[0]['erroramcaja'] + $result[0]['cajapm'] - $result[0]['errorpmcaja'] + $result[0]['pastelam'] + $result[0]['pastelpm'];
	$total_clientes = $result[0]['clientesam'] - $result[0]['erroramclientes'] + $result[0]['clientespm'] - $result[0]['errorpmclientes'] + $result[0]['clientespastelam'] + $result[0]['clientespastelpm'];

	if ($result[0]['erroramcaja'] + $result[0]['errorpmcaja'] > 50) {
		$tpl->newBlock('error_leyenda');

		$sql = '
			UPDATE
				efectivos_tmp
			SET
				status = -1
			WHERE
					num_cia = ' . $_GET['num_cia'] . '
				AND
					fecha = \'' . $_GET['fecha'] . '\'
		';
		$db->query($sql);

		$tpl->gotoBlock('hoja');
	}

	// [07-Oct-2010] Bloquear en caso de que el total de clientes difiera promedio diario +/- 15%
	$sql = '
		SELECT
			ROUND(AVG(ctes))
				AS
					prom
		FROM
			captura_efectivos
		WHERE
				num_cia = ' . $_GET['num_cia'] . '
			AND
				fecha BETWEEN \'' . $_GET['fecha'] . '\'::date - interval \'30 days\' AND \'' . $_GET['fecha'] . '\'
	';
	$tmp = $db->query($sql);
	$promedio_clientes = $tmp[0]['prom'] != 0 ? $tmp[0]['prom'] : 0;
	$promedio_clientes_min = $promedio_clientes - $promedio_clientes * 0.15;
	$promedio_clientes_max = $promedio_clientes + $promedio_clientes * 0.15;

	// [11-Nov-2010] No tomar en cuenta dias festivos
	$pieces = explode('/', $_GET['fecha']);

	$dia = $pieces[0] . '/' . $pieces[1];

	if ($total_clientes < $promedio_clientes_min || $total_clientes > $promedio_clientes_max) {
		$tpl->newBlock('error_clientes');
		$tpl->assign('prom_clientes', number_format($promedio_clientes));

		$sql = '
			UPDATE
				efectivos_tmp
			SET
				status = (CASE WHEN erroramcaja + errorpmcaja > 50 THEN -3 ELSE -2 END)
			WHERE
					num_cia = ' . $_GET['num_cia'] . '
				AND
					fecha = \'' . $_GET['fecha'] . '\'
		';

		if (!in_array($dia, $dias_festivos)) {
			$db->query($sql);
		}

		$tpl->gotoBlock('hoja');
	}

	$tpl->assign('total_caja', $total_caja != 0 ? number_format($total_caja, 2, '.', ',') : '');
	$tpl->assign('total_clientes', $total_clientes != 0 ? number_format($total_clientes) : '');

	// Cortes
	$corte_pan = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 1");
	$corte_pastel = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 2");
	if ($corte_pan || $corte_pastel) {
		if ($corte_pan)
			foreach ($corte_pan as $i => $reg)
				$tpl->assign('corte_pan_' . ($i + 1), $reg['ticket'] != 0 ? $reg['ticket'] : '');
		if ($corte_pastel)
			foreach ($corte_pastel as $i => $reg)
				$tpl->assign('corte_pastel_' . ($i + 1), $reg['ticket'] != 0 ? $reg['ticket'] : '');
	}

	// Producción -- En lugar de raya_pagada se utiliza raya_ganada porque los valores de los campos estan invertidos
	$sql = "SELECT codturno, /*raya_pagada*/raya_ganada AS raya_pagada, total_produccion FROM total_produccion_tmp WHERE num_cia = $_GET[num_cia]";
	$sql .= " AND fecha_total = '$_GET[fecha]'";
	$prod = $db->query($sql);
	$pro = 0;
	$raya = 0;
	$pro_array = array();
	if ($prod) {
		foreach ($prod as $reg) {
			$tpl->assign('pro' . $reg['codturno'], $reg['total_produccion'] != 0 ? number_format($reg['total_produccion'], 2, '.', ',') : '');
			$tpl->assign('raya' . $reg['codturno'], $reg['raya_pagada'] != 0 ? number_format($reg['raya_pagada'], 2, '.', ',') : '');
			$pro += $reg['total_produccion'];
			$raya += $reg['raya_pagada'];
		}
		$tpl->assign('pro', $pro != 0 ? number_format($pro, 2, '.', ',') : '');
		$tpl->assign('raya', $raya != 0 ? number_format($raya, 2, '.', ',') : '');

		foreach ($prod as $reg)
			$pro_array[$reg['codturno']] = $reg['total_produccion'];
	}

	// Rendimientos
	$sql = "SELECT cod_turno, sum(cantidad) AS bultos FROM mov_inv_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND codmp = 1 AND tipomov = 'TRUE' AND cod_turno < 10";
	$sql .= " GROUP BY cod_turno ORDER BY cod_turno";
	$con = $db->query($sql);
	if ($con) {
		foreach ($con as $reg) {
			$tpl->assign('bultos' . $reg['cod_turno'], number_format($reg['bultos'], 2, '.', ','));
			$tpl->assign('ren' . $reg['cod_turno'], number_format($pro_array[$reg['cod_turno']] / $reg['bultos'], 2, '.', ','));
		}
	}

	// Agua
	$med = $db->query("SELECT * FROM mediciones_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	if ($med) {
		$tpl->assign('med1', $med[0]['toma1'] != 0 ? number_format($med[0]['toma1'], 2, '.', ',') : '');
		$tpl->assign('hora1', $med[0]['toma1'] != 0 ? substr($med[0]['horatoma1'], 0, 5) : '');
		$tpl->assign('med2', $med[0]['toma2'] != 0 ? number_format($med[0]['toma2'], 2, '.', ',') : '');
		$tpl->assign('hora2', $med[0]['toma2'] != 0 ? substr($med[0]['horatoma2'], 0, 5) : '');
		$tpl->assign('med3', $med[0]['toma3'] != 0 ? number_format($med[0]['toma3'], 2, '.', ',') : '');
		$tpl->assign('hora3', $med[0]['toma3'] != 0 ? substr($med[0]['horatoma3'], 0, 5) : '');
	}

	// Camionetas
	$cam = $db->query("SELECT * FROM camionetas_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	if ($cam) {
		$tpl->assign('km1', $cam[0]['medunidad1'] != 0 ? number_format($cam[0]['medunidad1'], 2, '.', ',') : '');
		$tpl->assign('din1', $cam[0]['dinunidad1'] != 0 ? number_format($cam[0]['dinunidad1'], 2, '.', ',') : '');
		$tpl->assign('km2', $cam[0]['medunidad2'] != 0 ? number_format($cam[0]['medunidad2'], 2, '.', ',') : '');
		$tpl->assign('din2', $cam[0]['dinunidad2'] != 0 ? number_format($cam[0]['dinunidad2'], 2, '.', ',') : '');
		$tpl->assign('km3', $cam[0]['medunidad3'] != 0 ? number_format($cam[0]['medunidad3'], 2, '.', ',') : '');
		$tpl->assign('din3', $cam[0]['dinunidad3'] != 0 ? number_format($cam[0]['dinunidad3'], 2, '.', ',') : '');
		$tpl->assign('km4', $cam[0]['medunidad4'] != 0 ? number_format($cam[0]['medunidad4'], 2, '.', ',') : '');
		$tpl->assign('din4', $cam[0]['dinunidad4'] != 0 ? number_format($cam[0]['dinunidad4'], 2, '.', ',') : '');
		$tpl->assign('km5', $cam[0]['medunidad5'] != 0 ? number_format($cam[0]['medunidad5'], 2, '.', ',') : '');
		$tpl->assign('din5', $cam[0]['dinunidad5'] != 0 ? number_format($cam[0]['dinunidad5'], 2, '.', ',') : '');
	}

	// Lecturas de tanque de gas
	$tanques = $db->query("
		SELECT
			num_tanque,
			nombre,
			capacidad,
			(
				SELECT
					cantidad
				FROM
					tanques_gas_lecturas_tmp
				WHERE
					idtanque = ct.id
				AND fecha = '{$_REQUEST['fecha']}'
				LIMIT 1
			) AS lectura,
			(
				SELECT
					cantidad
				FROM
					tanques_gas_entradas_tmp
				WHERE
					idtanque = ct.id
				AND fecha = '{$_REQUEST['fecha']}'
				LIMIT 1
			) AS entrada,
			(
				SELECT
					nota
				FROM
					tanques_gas_entradas_tmp
				WHERE
					idtanque = ct.id
				AND fecha = '{$_REQUEST['fecha']}'
				LIMIT 1
			) AS nota_entrada
		FROM
			catalogo_tanques ct
		WHERE
			num_cia = {$_REQUEST['num_cia']}
	");
	if ($tanques)
	{
		$tpl->newBlock('tanques');

		foreach ($tanques as $row) {
			$tpl->newBlock('tanque');
			$tpl->assign('num_tanque', $row['num_tanque']);
			$tpl->assign('nombre_tanque', $row['nombre']);
			$tpl->assign('capacidad', number_format($row['capacidad']));
			$tpl->assign('lectura', $row['lectura'] > 0 ? number_format($row['capacidad']) : '<span style="color:#C00;font-weight:bold;">SIN LECTURA</span>');
			$tpl->assign('entrada', $row['entrada'] > 0 ? number_format($row['entrada']) : '&nbsp;');
			$tpl->assign('nota_entrada', $row['nota_entrada'] != '' ? $row['nota_entrada'] : '&nbsp;');
		}
	}

	// Avio recibido
	$fac = $db->query("SELECT * FROM facturas_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	if ($fac) {
		foreach ($fac as $reg) {
			$tpl->newBlock('avio_rec');
			$tpl->assign('id', $reg['id']);
			$tpl->assign('checked', $reg['valid'] == 't' ? ' checked' : '');
			$tpl->assign('prov', $reg['proveedor']);
			$tpl->assign('fac', $reg['factura']);
		}
	}

	// Desglose de gastos
	$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$gastos = $db->query($sql);
	$pan_comprado = 0;
	$total_gastos = 0;
	if ($gastos)
		foreach ($gastos as $reg) {
			$tpl->newBlock('gasto_hoja');
			$tpl->assign('concepto', trim($reg['concepto']) != '' ? trim($reg['concepto']) : '&nbsp;');
			$tpl->assign('importe', $reg['importe'] != 0 ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
			$total_gastos += $reg['importe'];
			if (trim($reg['concepto']) == 'PAN COMPRADO') $pan_comprado = $reg['importe'];
		}
	$pres = $db->query("SELECT 'PRETAMO ' || nombre AS concepto, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'");
	if ($pres)
		foreach ($pres as $reg) {
			$tpl->newBlock('gasto_hoja');
			$tpl->assign('concepto', trim($reg['concepto']) != '' ? trim($reg['concepto']) : '&nbsp;');
			$tpl->assign('importe', $reg['importe'] != 0 ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
			$total_gastos += $reg['importe'];
		}
	$tpl->gotoBlock('hoja');
	$tpl->assign('total_gastos', $total_gastos != 0 ? number_format($total_gastos, 2, '.', ',') : '&nbsp;');

	// Prueba de Efectivo
	$tpl->assign('cambio_ayer', $result[0]['cambioayer'] != 0 ? number_format($result[0]['cambioayer'], 2, '.', ',') : '');
	$tpl->assign('barredura', $result[0]['barredura'] != 0 ? number_format($result[0]['barredura'], 2, '.', ',') : '');
	$tpl->assign('pasteles', $result[0]['pasteles'] != 0 ? number_format($result[0]['pasteles'], 2, '.', ',') : '');
	$tpl->assign('bases', $result[0]['bases'] != 0 ? number_format($result[0]['bases'], 2, '.', ',') : '');
	$tpl->assign('esquilmos', $result[0]['esquilmos'] != 0 ? number_format($result[0]['esquilmos'], 2, '.', ',') : '');
	$tpl->assign('obs', $result[0]['obs_esquilmos']);
	$tpl->assign('tiempo_aire', $result[0]['tiempo_aire'] != 0 ? number_format($result[0]['tiempo_aire'], 2, '.', ',') : '');
	$tpl->assign('botes', $result[0]['botes'] != 0 ? number_format($result[0]['botes'], 2, '.', ',') : '');
	$tpl->assign('pastillaje', $result[0]['pastillaje'] != 0 ? number_format($result[0]['pastillaje'], 2, '.', ',') : '');
	$tpl->assign('costales', $result[0]['costales'] != 0 ? number_format($result[0]['costales'], 2, '.', ',') : '');
	$tpl->assign('efectivo', $result[0]['efectivo'] != 0 ? number_format($result[0]['efectivo'], 2, '.', ',') : '');
	$suma1 = /*$result[0]['cambioayer'] + */$result[0]['barredura'] + $result[0]['pasteles'] + $result[0]['bases'] + $result[0]['esquilmos'] + $result[0]['botes'] + $result[0]['pastillaje'] + $result[0]['costales'] + $result[0]['tiempo_aire'];

	// Pastillaje
	$past = $db->query("SELECT * FROM pastillaje_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	if ($past) {
		$tpl->assign('existencia_inicial', $past[0]['existenciainicial'] != 0 ? number_format($past[0]['existenciainicial'], 2, '.', ',') : '');
		$tpl->assign('venta_pastillaje', $past[0]['venta'] != 0 ? number_format($past[0]['venta'], 2, '.', ',') : '');
		$tpl->assign('compra_pastillaje', $past[0]['compra'] != 0 ? number_format($past[0]['compra'], 2, '.', ',') : '');
		$tpl->assign('existencia_final', $past[0]['existenciafinal'] != 0 ? number_format($past[0]['existenciafinal'], 2, '.', ',') : '');
	}

	// Prueba de Pan
	$vpuerta = $result[0]['efectivo'] + $result[0]['pasteles'];
	$tmp = $db->query("SELECT sum(pan_p_venta) AS pan_venta, sum(abono) AS abono FROM mov_exp_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	$vreparto = $tmp[0]['pan_venta'] != 0 ? $tmp[0]['pan_venta'] : 0;
	$abono_exp = $tmp[0]['abono'] != 0 ? $tmp[0]['abono'] : 0;
	$prueba = $db->query("SELECT descuentos, pan_contado, sobranteayer FROM prueba_pan_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	$tpl->assign('sobrante_ayer', $prueba[0]['sobranteayer'] != 0 ? number_format($prueba[0]['sobranteayer'], 2, '.', ',') : '');
	$tpl->assign('pan_comprado', $pan_comprado != 0 ? number_format($pan_comprado, 2, '.', ',') : '');
	$total_dia = $prueba[0]['sobranteayer'] + $pro + $pan_comprado;
	$tpl->assign('total_dia', $total_dia != 0 ? number_format($total_dia, 2, '.', ',') : '');
	$tpl->assign('venta_puerta', /*$vpuerta*/$total_caja + $result[0]['pasteles'] != 0 ? number_format(/*$vpuerta*/$total_caja + $result[0]['pasteles'], 2, '.', ',') : '');
	$tpl->assign('reparto', $vreparto != 0 ? number_format($vreparto, 2, '.', ',') : '');
	$tpl->assign('desc', $prueba[0]['descuentos'] != 0 ? number_format($prueba[0]['descuentos'], 2, '.', ',') : '');
	$sobrante = $total_dia - /*$vpuerta*/$total_caja - $result[0]['pasteles'] - $vreparto - $prueba[0]['descuentos'];
	$tpl->assign('sobrante_manana', $sobrante != 0 ? number_format($sobrante, 2, '.', ',') : '');
	$tpl->assign('pan_contado', $prueba[0]['pan_contado'] != 0 ? number_format($prueba[0]['pan_contado'], 2, '.', ',') : '');
	// [17-Feb-2009] Valor real para pan contado
	$tpl->assign('_pan_contado', round($prueba[0]['pan_contado'], 2) != 0 ? round($prueba[0]['pan_contado'], 2) : 0);
	// [17-Feb-2009] Produccion del mes
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha[2] - ($fecha[1] == 1 ? 1 : 0), 1, $fecha[3]));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $fecha[2], $fecha[1] == 1 ? 0 : $fecha[1], $fecha[3]));
	$dias = date('j', mktime(0, 0, 0, $fecha[2], $fecha[1] == 1 ? 0 : $fecha[1], $fecha[3]));
	$limite = $db->query("SELECT sum(total_produccion) / $dias AS limite FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total BETWEEN '$fecha1' AND '$fecha2'");
	$tpl->assign('_limite_pan_contado', round($limite[0]['limite'] / 2, 2));

	// *****
	$faltante = $sobrante - $prueba[0]['pan_contado'];
	$tpl->assign('faltante', $faltante != 0 ? number_format($faltante, 2, '.', ',') : '');

	// Prestamos a plazo
	$pres = $db->query("SELECT * FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	$saldo_ant = 0;
	$cargos = 0;
	$abonos = 0;
	$saldo_act = 0;
	$pres_max = FALSE;
	if ($pres) {
		foreach ($pres as $reg) {
			$tpl->newBlock('prestamo_hoja');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('saldo_ant', $reg['saldo'] != 0 ? number_format($reg['saldo'], 2, '.', ',') : '');
			if ($reg['tipo_mov'] != '') {
				$tpl->assign($reg['tipo_mov'] == 'f' ? 'cargo' : 'abono', $reg['importe'] != 0 ? number_format($reg['importe'], 2, '.', ',') : '');

				if ($reg['tipo_mov'] == 'f' && $reg['importe'] >= 1000)
					$pres_max = TRUE;
			}
			$tmp = $reg['saldo'] + ($reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe']);
			$tpl->assign('saldo_act', $tmp != 0 ? number_format($tmp, 2, '.', ',') : '');
			$cargos += $reg['tipo_mov'] == 'f' ? $reg['importe'] : 0;
			$abonos += $reg['tipo_mov'] == 't' ? $reg['importe'] : 0;
			$saldo_ant += $reg['saldo'];
			$saldo_act += $tmp;
		}
		$tpl->assign('hoja.saldo_ant', number_format($saldo_ant, 2, '.', ','));
		$tpl->assign('hoja.cargo', number_format($cargos, 2, '.', ','));
		$tpl->assign('hoja.abono_obreros', number_format($abonos, 2, '.', ','));
		$tpl->assign('hoja.saldo_act', number_format($saldo_act, 2, '.', ','));
		if ($pres_max) {
			$tpl->assign('hoja.pres_max', '<span style="font-weight:bold;font-size:18pt;color:#C00;font-family:Arial, Helvetica, sans-serif">Tiene prestamos mayores a 1,000 pesos. Debe de estar autorizado por el administrador o usted ser&aacute; responsable del importe prestado</span>');
		}
	}

	$suma1 += $abonos + $abono_exp + $total_caja;
	$tpl->assign('hoja.abonos', number_format($abono_exp, 2, '.', ','));
	$tpl->assign('hoja.suma_prueba1', number_format($suma1, 2, '.', ','));

	$suma2 = $result[0]['efectivo'] - $cargos + $total_gastos + $raya;
	$tpl->assign('hoja.suma_prueba2', number_format($suma2, 2, '.', ','));
	$tpl->assign('hoja.efectivo', number_format($result[0]['efectivo'] - $cargos, 2, '.', ','));

	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'fac_mod') {
	$sql = "";
	if (!isset($_POST['facid']))
		$sql .= "UPDATE facturas_tmp SET valid = 'FALSE' WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]';\n";
	else
		foreach ($_POST['facid'] as $id)
			$sql .= "UPDATE facturas_tmp SET valid = 'TRUE' WHERE id = $id;\n";

	if (isset($_GET['obs']) && trim($_GET['obs']) != '') {
		$sql .= "UPDATE efectivos_tmp SET obs_esquilmos = '$_GET[obs]' WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]';\n";
	}

	$db->query($sql);
	header("location: ./pan_rev_dat.php?action=compra_pastillaje&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]&compra_pastillaje=$_GET[compra_pastillaje]");
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'compra_pastillaje') {
	if (get_val($_REQUEST['compra_pastillaje'] > 0) && !in_array($_REQUEST['num_cia'], array(80, 133, 101, 62, 113, 122, 108, 51))) {
		$cia = $db->query('
			SELECT
				nombre_corto
					AS nombre
			FROM
				catalogo_companias
			WHERE
				num_cia = ' . $_REQUEST['num_cia'] . '
		');

		$tpl->newBlock('pastillaje_compras');

		$tpl->assign('num_cia', $_REQUEST['num_cia']);
		$tpl->assign('nombre_cia', $cia[0]['nombre']);
		$tpl->assign('fecha', $_REQUEST['fecha']);
		list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));
		$tpl->assign('_fecha', "$dia DE " . mes_escrito($mes, TRUE) . " DE $anio");
		$tpl->assign('nom', $_REQUEST['nom']);

		$sql = '
			SELECT
				proveedor,
				factura,
				importe
			FROM
				pastillaje_tmp_compras
			WHERE
				num_cia = ' . $_REQUEST['num_cia'] . '
				AND fecha = \'' . $_REQUEST['fecha'] . '\'
			ORDER BY
				id
		';

		$result = $db->query($sql);

		$total = 0;

		if ($result) {
			foreach ($result as $rec) {
				$tpl->newBlock('row_fac_pastillaje');

				$tpl->assign('proveedor', $rec['proveedor']);
				$tpl->assign('factura', $rec['factura']);
				$tpl->assign('importe', number_format($rec['importe'], 2));

				$total += $rec['importe'];
			}
		}

		$tpl->newBlock('row_fac_pastillaje');

		$tpl->assign('pastillaje_compras.total_facs', number_format($total, 2));
		$tpl->assign('pastillaje_compras.total_compras', number_format($_REQUEST['compra_pastillaje'], 2));

		$tpl->printToScreen();
	} else {
		header("location: ./pan_rev_dat.php?action=pro&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
	}

	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'compra_pastillaje_guardar') {
	$sql = '
		DELETE FROM
			pastillaje_tmp_compras
		WHERE
			num_cia = ' . $_REQUEST['num_cia'] . '
			AND fecha = \'' . $_REQUEST['fecha'] . '\'
	' . ";\n";

	foreach ($_REQUEST['proveedor'] as $i => $proveedor) {
		if ($proveedor != ''
			&& $_REQUEST['factura'][$i] != ''
			&& get_val($_REQUEST['importe'][$i]) > 0) {
			$sql .= '
				INSERT INTO
					pastillaje_tmp_compras (
						num_cia,
						fecha,
						proveedor,
						factura,
						importe
					) VALUES (
						' . $_REQUEST['num_cia'] . ',
						\'' . $_REQUEST['fecha'] . '\',
						\'' . $proveedor . '\',
						\'' . $_REQUEST['factura'][$i] . '\',
						' . get_val($_REQUEST['importe'][$i]) . '
					)
			' . ";\n";
		}
	}

	$db->query($sql);

	header("location: pan_rev_dat.php?action=" . ($_REQUEST['dir'] == 'l' ? 'hoja' : 'pro') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");

	die;
}

/************************************************************************/
/************************************************************************/
// PRODUCCION
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'pro_mod') {
	$sql = "SELECT produccion_tmp.id, cod_turno, descripcion, cod_producto, nombre, piezas, precio_raya, porc_raya, imp_raya, precio_venta, imp_produccion FROM produccion_tmp LEFT JOIN catalogo_productos USING (cod_producto) LEFT JOIN catalogo_turnos ON (cod_turno = cod_turnos) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND piezas > 0 ORDER BY cod_turno, cod_producto";
	$pro = $db->query($sql);

	// Se cambio la raya_ganada por raya_pagada y viceversa porque los datos estan al reves
	$sql = "SELECT id, codturno, raya_ganada/* AS raya_pagada*/, raya_pagada/* AS raya_ganada*/, total_produccion FROM total_produccion_tmp WHERE num_cia = $_GET[num_cia] AND fecha_total = '$_GET[fecha]' ORDER BY codturno";
	$tmp = $db->query($sql);

	$tot = array();
	foreach ($tmp as $reg) {
		$tot[$reg['codturno']]['id'] = $reg['id'];
		$tot[$reg['codturno']]['raya_ganada'] = $reg['raya_ganada'];
		$tot[$reg['codturno']]['raya_pagada'] = $reg['raya_pagada'];
		$tot[$reg['codturno']]['total_produccion'] = $reg['total_produccion'];
	}

	$tpl->newBlock('mod_pro');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	$turno = NULL;
	$raya_ganada = 0;
	$raya_pagada = 0;
	$total_produccion = 0;
	foreach ($pro as $i => $reg) {
		if ($turno != $reg['cod_turno']) {
			$turno = $reg['cod_turno'];

			$tpl->newBlock('turno_mod');
			$tpl->assign('turno', $reg['descripcion']);
			$tpl->assign('total_imp_raya', number_format($tot[$turno]['raya_ganada'], 2, '.', ','));
			$tpl->assign('total_raya_pagada', number_format($tot[$turno]['raya_pagada'], 2, '.', ','));
			$tpl->assign('total_imp_produccion', number_format($tot[$turno]['total_produccion'], 2, '.', ','));

			$tpl->assign('cod_turno', $turno);
			$tpl->assign('idtot', $tot[$turno]['id']);

			$raya_ganada += $tot[$turno]['raya_ganada'];
			$raya_pagada += $tot[$turno]['raya_pagada'];
			$total_produccion += $tot[$turno]['total_produccion'];
		}
		$tpl->newBlock('producto_mod');
		$tpl->assign('idpro', $reg['id']);

		$tpl->assign('i', $i);
		if (count($pro) > 1) {
			$tpl->assign('index', "[$i]");
			$tpl->assign('next', $i < count($pro) - 1 ? '[' . ($i + 1) . ']' : '[0]');
			$tpl->assign('back', $i > 0 ? '[' . ($i - 1) . ']' : '[' . (count($pro) - 1) . ']');

			$tpl->assign('turno_mod.index', "[$i]");
			$tpl->assign('turno_mod.next', $i < count($pro) - 1 ? '[' . ($i + 1) . ']' : '[0]');
			$tpl->assign('turno_mod.back', $i > 0 ? '[' . ($i) . ']' : '[' . (count($pro) - 1) . ']');
		}

		$tpl->assign('cod_turno', $reg['cod_turno']);
		$tpl->assign('cod_producto', $reg['cod_producto']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('piezas', number_format($reg['piezas']));
		$tpl->assign('precio_raya', $reg['precio_raya'] != 0 ? number_format($reg['precio_raya'], 4, '.', ',') : '%' . number_format($reg['porc_raya'], 2, '.', ','));
		$tpl->assign('imp_raya', $reg['imp_raya'] != 0 ? number_format($reg['imp_raya'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('precio_venta', $reg['precio_venta'] != 0 ? number_format($reg['precio_venta'], 3, '.', ',') : '&nbsp;');
		$tpl->assign('imp_produccion', $reg['imp_produccion'] != 0 ? number_format($reg['imp_produccion'], 2, '.', ',') : '&nbsp;');
	}

	$tpl->assign('mod_pro.raya_ganada', number_format($raya_ganada, 2, '.', ','));
	$tpl->assign('mod_pro.raya_pagada', number_format($raya_pagada, 2, '.', ','));
	$tpl->assign('mod_pro.produccion_total', number_format($total_produccion, 2, '.', ','));

	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'mod_pro') {
	$sql = '';

	for ($i = 0; $i < count($_POST['idpro']); $i++) {
		$sql .= 'UPDATE produccion_tmp SET';
		$sql .= ' piezas = ' . get_val($_POST['piezas'][$i]) . ',';
		$sql .= ' imp_raya = ' . get_val($_POST['imp_raya'][$i]) . ',';
		$sql .= ' imp_produccion = ' . get_val($_POST['imp_produccion'][$i]) . ',';
		$sql .= ' precio_raya = ' . (strpos($_POST['precio_raya'][$i], '%') !== FALSE ? '0' : get_val($_POST['precio_raya'][$i])) . ',';
		$sql .= ' porc_raya = ' . (strpos($_POST['precio_raya'][$i], '%') !== FALSE ? get_val(str_replace('%', '', $_POST['precio_raya'][$i])) : '0') . ',';
		$sql .= ' precio_venta = ' . get_val($_POST['precio_venta'][$i]);
		$sql .= ' WHERE id = ' . $_POST['idpro'][$i] . ";\n";
	}

	for ($i = 0; $i < count($_POST['idtot']); $i++) {
		$sql .= 'UPDATE total_produccion_tmp SET';
		$sql .= ' raya_ganada = ' . get_val($_POST['total_imp_raya' . $_POST['codturno'][$i]]) . ',';
		$sql .= ' raya_pagada = ' . get_val($_POST['total_raya_pagada' . $_POST['codturno'][$i]]) . ',';
		$sql .= ' total_produccion = ' . get_val($_POST['total_imp_produccion' . $_POST['codturno'][$i]]);
		$sql .= ' WHERE id = ' . $_POST['idtot'][$i] . ";\n";
	}

	$db->query($sql);

	header("location: ./pan_rev_dat.php?action=pro&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&nom=$_GET[nom]");
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'pro') {
	// Se cambio la raya_ganada por raya_pagada y viceversa porque los datos estan al reves
	$sql = "SELECT codturno, raya_ganada/* AS raya_pagada*/, raya_pagada/* AS raya_ganada*/, total_produccion FROM total_produccion_tmp WHERE num_cia = $_GET[num_cia]";
	$sql .= " AND fecha_total = '$_GET[fecha]' ORDER BY codturno";
	$tmp = $db->query($sql);

	if (!$tmp) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'exp' : 'hoja') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}

	// Buscar productos que hayan cambiado de precio, se hayan eliminado o se hayan insertado
	$sql = "SELECT cod_turno, descripcion, cod_producto, nombre, precio_raya, porc_raya, precio_venta FROM produccion_tmp LEFT JOIN catalogo_productos USING (cod_producto) LEFT JOIN catalogo_turnos ON (cod_turno = cod_turnos) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND (cod_producto, cod_turnos, ROUND(precio_raya::numeric, 4), ROUND(porc_raya::numeric, 2), ROUND(precio_venta::numeric, 2)) NOT IN (SELECT cod_producto, cod_turno, ROUND(precio_raya::numeric, 4), ROUND(porc_raya::numeric, 2), ROUND(precio_venta::numeric, 2) FROM control_produccion WHERE num_cia = $_GET[num_cia]) ORDER BY cod_turno, cod_producto";
	$new = $db->query($sql);
	if ($new) {
		$tpl->newBlock('pro_new');

		$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

		$tpl->assign('num_cia', $_GET['num_cia']);
		$tpl->assign('nombre_cia', $nombre_cia);
		$tpl->assign('fecha', $_GET['fecha']);
		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
		$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
		// Importe de nómina, arrastrarlo hasta la pantalla de gastos
		$tpl->assign('nom', $_GET['nom']);

		$turno = NULL;
		foreach ($new as $reg) {
			if ($turno != $reg['cod_turno']) {
				$turno = $reg['cod_turno'];

				$tpl->newBlock('turno_new');
				$tpl->assign('turno', $reg['descripcion']);
			}
			$tpl->newBlock('producto_new');
			$tpl->assign('cod', $reg['cod_producto']);
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('precio_raya', $reg['precio_raya'] != 0 ? number_format($reg['precio_raya'], 4, '.', ',') : '% ' . number_format($reg['porc_raya'], 2, '.', ','));
			$tpl->assign('precio_venta', $reg['precio_venta'] != 0 ? number_format($reg['precio_venta'], 3, '.', ',') : '&nbsp;');
		}

		$tpl->printToScreen();
		die;
	}

	$tot = array();
	foreach ($tmp as $reg) {
		$tot[$reg['codturno']]['raya_ganada'] = $reg['raya_ganada'];
		$tot[$reg['codturno']]['raya_pagada'] = $reg['raya_pagada'];
		$tot[$reg['codturno']]['total_produccion'] = $reg['total_produccion'];
	}

	$sql = "SELECT cod_turno, descripcion, cod_producto, nombre, piezas, precio_raya, porc_raya, imp_raya, precio_venta, imp_produccion FROM produccion_tmp LEFT JOIN catalogo_productos";
	$sql .= " USING (cod_producto) LEFT JOIN catalogo_turnos ON (cod_turno = cod_turnos) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND piezas > 0 ORDER BY cod_turno,/* orden,*/ cod_producto";
	$pro = $db->query($sql);

	$tpl->newBlock('pro');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	$turno = NULL;
	$raya_ganada = 0;
	$raya_pagada = 0;
	$total_produccion = 0;
	foreach ($pro as $reg) {
		if ($turno != $reg['cod_turno']) {
			$turno = $reg['cod_turno'];

			$tpl->newBlock('turno');
			$tpl->assign('turno', $reg['descripcion']);
			$tpl->assign('raya_ganada', number_format($tot[$turno]['raya_ganada'], 2, '.', ','));
			$tpl->assign('raya_pagada', number_format($tot[$turno]['raya_pagada'], 2, '.', ','));
			$tpl->assign('total_produccion', number_format($tot[$turno]['total_produccion'], 2, '.', ','));

			$raya_ganada += $tot[$turno]['raya_ganada'];
			$raya_pagada += $tot[$turno]['raya_pagada'];
			$total_produccion += $tot[$turno]['total_produccion'];
		}
		$tpl->newBlock('producto');
		$tpl->assign('cod_producto', $reg['cod_producto']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('piezas', number_format($reg['piezas']));
		$tpl->assign('precio_raya', $reg['precio_raya'] != 0 ? number_format($reg['precio_raya'], 4, '.', ',') : '% ' . number_format($reg['porc_raya'], 2, '.', ','));
		$tpl->assign('imp_raya', $reg['imp_raya'] != 0 ? number_format($reg['imp_raya'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('precio_venta', $reg['precio_venta'] != 0 ? number_format($reg['precio_venta'], 3, '.', ',') : '&nbsp;');
		$tpl->assign('imp_produccion', $reg['imp_produccion'] != 0 ? number_format($reg['imp_produccion'], 2, '.', ',') : '&nbsp;');
	}

	$tpl->assign('pro.raya_ganada', number_format($raya_ganada, 2, '.', ','));
	$tpl->assign('pro.raya_pagada', number_format($raya_pagada, 2, '.', ','));
	$tpl->assign('pro.produccion_total', number_format($total_produccion, 2, '.', ','));

	$tpl->printToScreen();
	die;
}

// [20-Oct-2009] Mostrar valores de códigos 150 y 468 para validación
if (isset($_GET['action']) && $_GET['action'] == 'pro_muestras') {
	$sql = '
		SELECT
			cod_producto
				AS
					cod,
			nombre
				AS
					producto,
			descripcion
				AS
					turno,
			piezas,
			precio_raya,
			imp_raya
		FROM
				produccion_tmp
					p
			LEFT JOIN
				catalogo_productos
					cp
					USING
						(
							cod_producto
						)
			LEFT JOIN
				catalogo_turnos
					ct
						ON
							(
								cod_turno = cod_turnos
							)
		WHERE
				fecha = \'' . $_GET['fecha'] . '\'
			AND
				num_cia = ' . $_GET['num_cia'] . '
			AND
				cod_producto
					IN
						(
							150,
							468
						)
		ORDER BY
			cod_producto
	';
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'exp' : 'pro') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
		die;
	}

	$tpl->newBlock('pro_muestras');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	$tpl->assign('nom', $_GET['nom']);

	foreach ($result as $r) {
		$tpl->newBlock('muestras');
		$tpl->assign('cod', $r['cod']);
		$tpl->assign('producto', $r['producto']);
		$tpl->assign('turno', $r['turno']);
		$tpl->assign('piezas', number_format($r['piezas']));
		$tpl->assign('precio_raya', number_format($r['precio_raya'], 4, '.', ','));
		$tpl->assign('imp_raya', number_format($r['imp_raya'], 2, '.', ','));
	}

	$tpl->printToScreen();

	die;
}

/************************************************************************/
/************************************************************************/
// EXPENDIOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'exp') {
	$sql = "
		SELECT
			tmp.num_expendio
				AS num_tmp,
			num_referencia
				AS num_cat,
			cat.num_expendio
				AS num_exp,
			nombre_expendio
				AS nombre_tmp,
			nombre
				AS nombre_cat,
			porc_ganancia
				AS por_tmp,
			porciento_ganancia
				AS por_cat,
			importe_fijo
				AS fijo,
			rezago_anterior,
			pan_p_venta,
			pan_p_expendio,
			abono,
			devolucion,
			rezago,
			num_cia_exp,
			CASE
				WHEN num_cia_exp IS NOT NULL AND (
					SELECT
						SUM(importe)
					FROM
						gastos_tmp
					WHERE
						num_cia = cat.num_cia_exp
						AND fecha = tmp.fecha
						AND codgastos IN (5, 152)
				) IS NOT NULL THEN
					(
						SELECT
							SUM(importe)
						FROM
							gastos_tmp
						WHERE
							num_cia = cat.num_cia_exp
							AND fecha = tmp.fecha
							AND codgastos IN (5, 152)
					)
				ELSE
					NULL
			END
				AS pan_comprado
		FROM
			mov_exp_tmp AS tmp
			LEFT JOIN catalogo_expendios AS cat
				ON (
					cat.num_cia = tmp.num_cia
					AND num_referencia = tmp.num_expendio
				)
		WHERE
			tmp.num_cia = $_GET[num_cia]
			AND fecha = '$_GET[fecha]'
		ORDER BY
			num_tmp
	";

	$result = $db->query($sql);

	if (!$result) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? /*'avio'*/'pasteles' : 'pro') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
		die;
	}

	// Validar expendios
	$new = array();
	$mod = array();
	$por = array();
	foreach ($result as $reg)
		if ($reg['num_cat'] == '')
			$new[] = $reg;
		else {
			if (trim(strtoupper($reg['nombre_tmp'])) != trim(strtoupper($reg['nombre_cat']))) {
				$mod[] = $reg;
			}
			if ($reg['por_tmp'] != $reg['por_cat'])
				$por[] = $reg;
		}

	if (count($new) > 0 || count($mod) > 0 || count($por) > 0) {
		$tpl->newBlock('cambio_exp');
		$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

		$tpl->assign('num_cia', $_GET['num_cia']);
		$tpl->assign('nombre_cia', $nombre_cia);
		$tpl->assign('fecha', $_GET['fecha']);
		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
		$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
		// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
		$tpl->assign('nom', $_GET['nom']);

		// Expendios nuevo
		if (count($new) > 0) {
			$tpl->newBlock('new_exps');
			foreach ($new as $reg) {
				$tpl->newBlock('new_exp');
				$tpl->assign('num', $reg['num_tmp']);
				$tpl->assign('nombre', $reg['nombre_tmp']);
				$tpl->assign('por', number_format($reg['por_tmp'], 2, '.', ','));
			}
		}
		// Cambio de nombre en expendios
		if (count($mod) > 0) {
			$tpl->newBlock('mod_exps');
			foreach ($mod as $reg) {
				$tpl->newBlock('mod_exp');
				$tpl->assign('num', $reg['num_tmp']);
				$tpl->assign('nombre_tmp', $reg['nombre_tmp']);
				$tpl->assign('nombre_cat', $reg['nombre_cat']);
			}
		}
		// Cambio de porcentaje de ganancia
		if (count($por) > 0) {
			$tpl->newBlock('por_exps');
			foreach ($por as $reg) {
				$tpl->newBlock('por_exp');
				$tpl->assign('num', $reg['num_tmp']);
				$tpl->assign('nombre', $reg['nombre_tmp']);
				$tpl->assign('por_cat', number_format($reg['por_cat'], 2, '.', ','));
				$tpl->assign('por_tmp', number_format($reg['por_tmp'], 2, '.', ','));
			}
		}

		$tpl->printToScreen();
		die;
	}

	$tpl->newBlock('exp');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	$pan_p_venta = 0;
	$pan_p_exp = 0;
	$abono = 0;
	$devuelto = 0;
	$rezago = 0;
	$rezago_ant_total = 0;

	$ok = TRUE;

	foreach ($result as $reg) {
		$tpl->newBlock('mov_exp');
		$tpl->assign('num', $reg['num_cat']);
		$tpl->assign('nombre', $reg['nombre_tmp']);

		// Rezago anterior
		$tpl->assign('rezago_ant', $reg['rezago_anterior'] != 0 ? number_format($reg['rezago_anterior'], 2, '.', ',') : '-----');
		// - Ultimo rezago -
		$tmp = $db->query("SELECT fecha, round(rezago::numeric, 2) AS rezago FROM mov_expendios WHERE num_cia = $_GET[num_cia] AND fecha < '$_GET[fecha]' AND num_expendio = $reg[num_exp] ORDER BY fecha DESC LIMIT 1");
		$rezago_ant = $tmp ? $tmp[0]['rezago'] : 0;
		// - Validar que el ultimo rezago en sistema concuerde con el rezago de la panaderia -
		if (round($reg['rezago_anterior'], 2) < round($rezago_ant, 2) - 0.01 || round($reg['rezago_anterior'], 2) > round($rezago_ant, 2) + 0.01) {
			$tpl->assign('color_rezago_ant', ' bgcolor="#FFFF66"');
			$tpl->assign('mensaje_rezago_ant', "Rezago día {$tmp[0]['fecha']}: " . number_format($rezago_ant, 2, '.', ','));
			$ok = FALSE;
		}

		// [30-Nov-2009] Validar que para las panaderias que son expendios el pan comprado sea igual a lo abonado
		/*if ($reg['abono'] > 0 && $reg['num_cia_exp'] > 0) {
			$sql = "SELECT sum(importe) AS importe FROM gastos_tmp WHERE num_cia = $reg[num_cia_exp] AND fecha = '$_GET[fecha]' AND codgastos IN (5, 152)";
			$pan_comprado = $db->query($sql);

			if ($pan_comprado[0]['importe'] != $reg['abono']) {
				$tpl->assign('color_abono', ' bgcolor="#FFFF66"');
				$tpl->assign('mensaje_abono', 'El abono es menor al pan comprado de la panaderia ' . $reg['num_cia_exp']);
				$ok = FALSE;
			}
		}*/

		// Pan para venta
		$tpl->assign('pan_p_venta', $reg['pan_p_venta'] != 0 ? number_format($reg['pan_p_venta'], 2, '.', ',') : '&nbsp;');

		// Devolución
		$tpl->assign('dev', $reg['devolucion'] != 0 ? number_format($reg['devolucion'], 2, '.', ',') : '-----');
		// - Validar que Devolución sea menor a Abonos -
		if ($reg['devolucion'] > $reg['abono']) {
			$tpl->assign('color_dev', ' bgcolor="#FFFF66"');
			$tpl->assign('mensaje_dev', 'Devolución debe ser menor o igual a Abono');
		}

		/*
		* [29-May-2012] La devolucion no puede pasar el 5% de las partidas
		*/
//		if ($reg['devolucion'] > round($reg['pan_p_venta'] * 0.05, 2)) {
//			$tpl->assign('color_dev', ' bgcolor="#FFFF66"');
//			$tpl->assign('mensaje_dev', 'La devolucion excede lo autorizado');
//			$ok = FALSE;
//		}

		// Pan para expendio
		$tpl->assign('por', $reg['por_tmp'] != 0 ? '% ' . number_format($reg['por_tmp']) : '&nbsp;');
		$tpl->assign('pan_p_exp', $reg['pan_p_expendio'] != 0 ? number_format($reg['pan_p_expendio'], 2, '.', ',') : '-----');
		// - Validar que el Pan para expendio sea congruente con los topes establecidos en el catálogo -
		$tope = $reg['fijo'] > 0 ? $reg['pan_p_venta'] - $reg['fijo'] - 0.30 : ($reg['pan_p_venta'] * (100 - $reg['por_tmp']) / 100) - 0.30;	// Calculo del tope
		if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] == 0 && $reg['por_tmp'] == 0 && $reg['pan_p_expendio'] > $reg['pan_p_venta']) {
			$tpl->assign('color_pan_exp', ' bgcolor="#FFFF66"');	// Pan para expendio no puede ser mayor a Pan para venta
			$tpl->assign('mensaje_pan_exp', 'Total debe ser igual o menor a ' . number_format($reg['pan_p_venta'], 2, '.', ','));
		}
		if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] > 0 && $reg['por_tmp'] == 0 && $reg['pan_p_expendio'] > $tope) {
			$tpl->assign('color_pan_exp', ' bgcolor="#FFFF66"');	// Tope fijo
			$tpl->assign('mensaje_pan_exp', 'Total debe ser igual o menor a ' . number_format($tope, 2, '.', ','));
		}
		if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] == 0 && $reg['por_tmp'] > 0 && $reg['pan_p_expendio'] < $tope) {
			$tpl->assign('color_pan_exp', ' bgcolor="#FFFF66"');	// Tope por porcentaje de ganancia
			$tpl->assign('mensaje_pan_exp', 'Total debe ser mayor o igual a ' . number_format($tope, 2, '.', ','));
		}
		if ($reg['pan_p_expendio'] <= 0 && $reg['pan_p_venta'] > 0) {
			$tpl->assign('color_pan_exp', ' bgcolor="#FFFF66"');
			$tpl->assign('mensaje_pan_exp', 'No puede haber haber partidas sin total.');
			$ok = FALSE;
		}

		$tpl->assign('abono', $reg['abono'] != 0 ? number_format($reg['abono'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('rezago', $reg['rezago'] != 0 ? number_format($reg['rezago'], 2, '.', ',') : '-----');
		if ($reg['rezago'] < 0) $tpl->assign('color_rezago', ' bgcolor="#FFFF66"');

		$pan_p_venta += $reg['pan_p_venta'];
		$pan_p_exp += $reg['pan_p_expendio'];
		$abono += $reg['abono'];
		$devuelto += $reg['devolucion'];
		$rezago_ant_total += $reg['rezago_anterior'];
		$rezago += $reg['rezago'];
	}
	$tpl->assign('exp.rezago_ant', number_format($rezago_ant_total, 2, '.', ','));
	$tpl->assign('exp.pan_p_venta', number_format($pan_p_venta, 2, '.', ','));
	$tpl->assign('exp.dev', number_format($devuelto, 2, '.', ','));
	$tpl->assign('exp.pan_p_exp', number_format($pan_p_exp, 2, '.', ','));
	$tpl->assign('exp.abono', number_format($abono, 2, '.', ','));
	$tpl->assign('exp.rezago', number_format($rezago, 2, '.', ','));

	// [25-Jul-2008] Bloquear boton 'Siguiente' si hay diferencias de rezagos
	if (!$ok)
		$tpl->assign('exp.disabled', ' disabled');

	$tpl->printToScreen();
	die;
}

// Actualizar datos de expendios
if (isset($_GET['action']) && $_GET['action'] == 'mod_exp') {
	$sql = "";

	$exp = $db->query("SELECT num_expendio, num_referencia FROM catalogo_expendios WHERE num_cia = $_GET[num_cia] ORDER BY num_expendio DESC LIMIT 1");
	$num = $exp ? $exp[0]['num_expendio'] + 1 : 1;

	// Expendios nuevos
	if (isset($_POST['nombre_new']))
		for ($i = 0; $i < count($_POST['nombre_new']); $i++) {
			$sql .= "INSERT INTO catalogo_expendios (num_cia, num_expendio, num_referencia, nombre, porciento_ganancia, tipo_expendio) VALUES ($_GET[num_cia], $num, {$_POST['num_new'][$i]},";
			$sql .= " upper('{$_POST['nombre_new'][$i]}'), {$_POST['por_new'][$i]}, 1);\n";
			$num++;
		}

	// Expendios con cambio de nombre
	if (isset($_POST['nombre_mod']))
		for ($i = 0; $i < count($_POST['nombre_mod']); $i++)
			$sql .= "UPDATE catalogo_expendios SET nombre = upper('{$_POST['nombre_mod'][$i]}') WHERE num_cia = $_GET[num_cia] AND num_referencia = {$_POST['num_mod'][$i]};\n";

	// Expendios con cambio de porcentaje
	if (isset($_POST['por_tmp']))
		for ($i = 0; $i < count($_POST['por_tmp']); $i++) {
			// Solo actualizar el catalogo cuando el porcentaje de la panaderia es menor
			if ($_POST['por_tmp'][$i] < $_POST['por_cat'][$i])
				$sql .= "UPDATE catalogo_expendios SET porciento_ganancia = {$_POST['por_tmp'][$i]} WHERE num_cia = $_GET[num_cia] AND num_referencia = {$_POST['num_por'][$i]};\n";
			// Actualizar el porcentaje en el archivo y actualizar datos del movimiento del expendio
			else {
				$mov = $db->query("SELECT * FROM mov_exp_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND num_expendio = {$_POST['num_por'][$i]}");
				$pan_exp = round($mov[0]['pan_p_venta'] * (100 - $_POST['por_cat'][$i]) / 100, 2);
				$rezago = $mov[0]['rezago_anterior'] + $pan_exp - $mov[0]['abono'] - $mov[0]['devolucion'];
				$sql .= "UPDATE mov_exp_tmp SET porc_ganancia = {$_POST['por_cat'][$i]}, pan_p_expendio = $pan_exp, rezago = $rezago WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
				$sql .= " AND num_expendio = {$_POST['num_por'][$i]};\n";
			}
		}

	//echo "<pre>$sql</pre>";die;
	if (trim($sql) != '') $db->query($sql);
	header("location: ./pan_rev_dat.php?action=exp&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&nom=$_GET[nom]");
	die;
}

/************************************************************************/
/************************************************************************/
// AVIO
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'avio') {
	$sql = "SELECT mov_inv_tmp.id, codmp, nombre AS nombre_mp, cod_turno, descripcion AS nombre_turno, CASE WHEN cod_turno > 0 THEN num_orden ELSE (SELECT num_orden FROM control_avio WHERE";
	$sql .= " num_cia = mov_inv_tmp.num_cia AND codmp = mov_inv_tmp.codmp LIMIT 1) END AS num_orden, tipomov, cantidad FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " LEFT JOIN catalogo_turnos USING (cod_turno) LEFT JOIN control_avio USING (num_cia, codmp, cod_turno) WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$sql .= " ORDER BY num_orden, codmp, tipomov, cod_turno";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'gastos' : 'exp') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
		die;
	}

	// Obtener existencias anteriores
	$sql = "SELECT codmp, nombre, iv.existencia, ir.existencia AS real FROM inventario_virtual AS iv LEFT JOIN inventario_real AS ir USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $_GET[num_cia]";
	$ex = $db->query($sql);

	function buscar_existencia($codmp) {
		global $ex;

		if (!$ex)
			return array(0, false);

		foreach ($ex as $reg)
			if ($codmp == $reg['codmp'])
				return array($codmp == 1 ? $reg['existencia'] / 44 : $reg['existencia'], $reg['existencia'] != $reg['real'] ? false : true);

		return array(0, false);
	}

	if (!isset($_GET['avg'])) {
		// [05-Jul-2007] Obtener los promedios de consumo de los últimos 30 días
		// [09-Abr-2009] Cambiado a consumo de 3 meses
		$sql = "SELECT codmp, cod_turno, round(cast(avg(cantidad) as numeric), 2) AS avg FROM mov_inv_real WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$_GET[fecha]'::date - interval '3 months' AND '$_GET[fecha]' AND tipo_mov = 'TRUE' AND descripcion NOT LIKE '%DIFERENCIA%' GROUP BY /*id, */codmp, cod_turno ORDER BY codmp, cod_turno";
		$tmp = $db->query($sql);

		// [05-Jul-2007] Reordenar promedios de consumos
		$avg = array();
		foreach ($tmp as $reg)
			$avg[$reg['codmp']][$reg['cod_turno']] = $reg['avg'];

		// [05-Jul-2007] Validar que los consumos no esten por encima del 20% mayor al promedio de consumo diario
		$pro = array();
		$no_avg = array();
		foreach ($result as $reg)
			if ($reg['tipomov'] == 't' && isset($avg[$reg['codmp']][$reg['cod_turno']])) {
				if ($reg['cantidad'] > round($avg[$reg['codmp']][$reg['cod_turno']] * 1.20, 2))
					$pro[] = $reg;
			}
			else if ($reg['tipomov'] == 't' && !isset($avg[$reg['codmp']][$reg['cod_turno']])) {
				$no_avg[] = $reg;
			}

		// [17-Feb-2014] Validar que la existencia no sobrepase el consumo promedio de 25 días
		$over_avg = array();
		foreach ($ex as $e)
		{
			if (isset($avg[$e['codmp']]) && $e['existencia'] > array_sum($avg[$e['codmp']]) * 25)
			{
				$over_avg[] = array_merge($e, array('consumo' => array_sum($avg[$e['codmp']]) * 25));
			}
		}

		// [20-Jul-2007] Si hubo consumos por arriba del promedio, mandar a una pantalla de aviso
		// [17-Feb-2014] Agregada la condicion para los productos con exceso de inventario
		if (count($pro) > 0 || count($no_avg) > 0 || count($over_avg) > 0) {
			$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
			$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

			$tpl->newBlock('avio_error');
			$tpl->assign('num_cia', $_GET['num_cia']);
			$tpl->assign('nombre_cia', $nombre_cia);
			$tpl->assign('fecha', $_GET['fecha']);
			preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
			$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
			// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
			$tpl->assign('nom', $_GET['nom']);

			function turno($turno) {
				switch ($turno) {
					case 1: $label = 'FRANCES DE DIA'; break;
					case 2: $label = 'FRANCES DE NOCHE'; break;
					case 3: $label = 'BIZCOCHERO'; break;
					case 4: $label = 'PASTELERO'; break;
					case 8: $label = 'PICONERO'; break;
					case 9: $label = 'GELATINERO'; break;
					case 10: $label = 'DESPACHO'; break;
				}
				return $label;
			}

			$ids = array();

			$sql = "DELETE FROM his_aut_con_avio WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]';\n";
			//$sql = "DELETE FROM mov_inv_pen_aut WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]';\n";
			if (count($pro) > 0) {
				$tpl->newBlock('avg');
				foreach ($pro as $reg) {
					$tpl->newBlock('avg_row');
					$tpl->assign('cod', $reg['codmp']);
					$tpl->assign('nombre', $reg['nombre_mp']);
					$tpl->assign('turno', turno($reg['cod_turno']));
					$tpl->assign('consumo', number_format($reg['cantidad'], 2, '.', ','));
					$tpl->assign('promedio', number_format($avg[$reg['codmp']][$reg['cod_turno']] * 1.20, 2, '.', ','));
					$dif = ($reg['cantidad'] * 100 / ($avg[$reg['codmp']][$reg['cod_turno']] * 1.20)) - 100 + 20;
					$tpl->assign('color', $dif >= 200 ? 'style="color:#FF0000;"' : '');
					$tpl->assign('dif', number_format($dif, 2, '.', ','));

					$sql .= "INSERT INTO his_aut_con_avio (num_cia, fecha, codmp, cod_turno, consumo, promedio, diferencia, iduser, tsmod) VALUES ($_GET[num_cia], '$_GET[fecha]', $reg[codmp], $reg[cod_turno], $reg[cantidad], {$avg[$reg['codmp']][$reg['cod_turno']]}, $dif, $_SESSION[iduser], now());\n";

					/*$sql .= "INSERT INTO mov_inv_pen_aut (num_cia, fecha, codmp, cod_turno, tipo_mov, cantidad) VALUES ($_GET[num_cia], '$_GET[fecha]', $reg[codmp], $reg[cod_turno], $reg[tipomov], $reg[cantidad]);\n";
					$sql .= "UPDATE mov_inv_tmp SET no_ingresar = 'TRUE' WHERE id = $reg[id];\n";*/
				}
			}
			if (count($no_avg) > 0) {
				$tpl->newBlock('no_avg');
				foreach ($no_avg as $reg) {
					$tpl->newBlock('no_avg_row');
					$tpl->assign('cod', $reg['codmp']);
					$tpl->assign('nombre', $reg['nombre_mp']);
					$tpl->assign('turno', turno($reg['cod_turno']));
					$tpl->assign('consumo', number_format($reg['cantidad'], 2, '.', ','));

					$sql .= "INSERT INTO his_aut_con_avio (num_cia, fecha, codmp, cod_turno, consumo, promedio, diferencia, iduser, tsmod) VALUES ($_GET[num_cia], '$_GET[fecha]', $reg[codmp], $reg[cod_turno], $reg[cantidad], 0, 0, $_SESSION[iduser], now());\n";
				}
			}

			if (count($over_avg) > 0) {
				$tpl->newBlock('over_avg');
				foreach ($over_avg as $reg) {
					$tpl->newBlock('over_avg_row');
					$tpl->assign('cod', $reg['codmp']);
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('consumo', number_format($reg['consumo'], 2, '.', ','));
					$tpl->assign('existencia', number_format($reg['existencia'], 2, '.', ','));

					// $sql .= "INSERT INTO his_aut_con_avio (num_cia, fecha, codmp, cod_turno, consumo, promedio, diferencia, iduser, tsmod) VALUES ($_GET[num_cia], '$_GET[fecha]', $reg[codmp], $reg[cod_turno], $reg[cantidad], 0, 0, $_SESSION[iduser], now());\n";
				}
			}

			$db->query($sql);
			$tpl->printToScreen();
			die;
		}
	}

	$tpl->newBlock('avio');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	// [08-Abr-2009] Validar que los consumos por producto esten en el control de avio
	$sql = 'SELECT codmp, nombre, cod_turno, descripcion FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN catalogo_turnos USING (cod_turno) WHERE num_cia = ' . $_GET['num_cia'] . ' AND fecha = \'' . $_GET['fecha'] . '\' AND (codmp, cod_turno) NOT IN (SELECT codmp, cod_turno FROM control_avio WHERE num_cia = ' . $_GET['num_cia'] . ') ORDER BY codmp, cod_turno';
	$tmp = $db->query($sql);

	if ($tmp) {
		$tpl->assign('disabled', ' disabled');

		$tpl->newBlock('no_control_avio');

		foreach ($tmp as $t) {
			$tpl->newBlock('no_control_avio_row');
			$tpl->assign('codmp', $t['codmp']);
			$tpl->assign('nombre', $t['nombre']);
			$tpl->assign('turno', $t['descripcion'] != '' ? $t['descripcion'] : 'ENTRADA');
		}
	}

	$no_tur = array();
	if ($tmp)
		foreach ($tmp as $t)
			$no_tur[$t['codmp']][$t['cod_turno']] = array('producto' => $t['nombre'], 'turno' => $t['descripcion']);

	$codmp = NULL;
	foreach ($result as $reg) {
		if ($codmp != $reg['codmp']) {
			$codmp = $reg['codmp'];

			$tpl->newBlock('avi_row');
			$tpl->assign('codmp', $codmp);
			$tpl->assign('nombre', $reg['nombre_mp']);
			$ext_ini = buscar_existencia($codmp);
			$ext_fin = $ext_ini[0];
			$tpl->assign('ext_ini', $ext_ini[0] != 0 ? number_format($ext_ini[0], 2, '.', ',') : '');
			$tpl->assign('color_ini', /*$ext_ini > 0 ? '006600' : '660000'*/ $ext_ini[1] ? '006600' : '660000');
			$consumo = 0;
		}
		$tpl->assign($reg['cod_turno'] > 0 ? $reg['cod_turno'] : 'entrada', number_format($reg['cantidad'], 2, '.', ','));

		if ($reg['cod_turno'] > 0 && isset($no_tur[$codmp][$reg['cod_turno']]))
			$tpl->assign('bgcolor' . $reg['cod_turno'], ' background-color:#FFCC00;');

		$consumo += $reg['tipomov'] == 't' ? $reg['cantidad'] : 0;
		$tpl->assign('consumo', $consumo != 0 ? number_format($consumo, 2, '.', ',') : '');
		if ($reg['tipomov'] == 'f') {
			$tpl->assign('total', number_format($ext_ini[0] + $reg['cantidad'], 2, '.', ','));
			$tpl->assign('color_total', $ext_ini[0] + $reg['cantidad'] > 0 ? '006600' : '660000');
			$ext_fin += $reg['cantidad'];
		}
		else
			$ext_fin -= $reg['cantidad'];
		$tpl->assign('ext_fin', $ext_fin != 0 ? number_format($ext_fin, 2, '.', ',') : '');
		$tpl->assign('color_fin', $ext_fin > 0 ? '006600' : '660000');
	}

	$tpl->printToScreen();
	die;
}

/************************************************************************/
/************************************************************************/
// PASTELES
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'pasteles') {
	$sql = "
		SELECT
			*
		FROM
			venta_pastel
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			AND fecha_entrega < '{$_REQUEST['fecha']}'
			AND estado = 0
			AND tipo = 0
		ORDER BY
			num_remi
	";

	$result = $db->query($sql);

	if ($result)
	{
		$tpl->newBlock('pasteles_error');

		$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");
		$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

		$tpl->assign('num_cia', $_REQUEST['num_cia']);
		$tpl->assign('nombre_cia', $nombre_cia);
		$tpl->assign('fecha', $_GET['fecha']);

		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_REQUEST['fecha'], $fecha);

		$tpl->assign('_fecha', "{$fecha[1]} DE " . mes_escrito($fecha[2], TRUE) . " DE {$fecha[3]}");
		// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
		$tpl->assign('nom', $_REQUEST['nom']);

		foreach ($result as $row)
		{
			$tpl->newBlock('pastel_error_row');

			$tpl->assign('num_remision', $row['num_remi']);
			$tpl->assign('fecha_entrega', $row['fecha_entrega']);
			$tpl->assign('kilos', $row['kilos'] > 0 ? number_format($row['kilos'], 2) : '&nbsp;');
			$tpl->assign('precio_kilo', $row['precio_unidad'] > 0 ? number_format($row['precio_unidad'], 2) : '&nbsp;');
			$tpl->assign('importe_pastel', $row['kilos'] > 0 ? number_format($row['kilos'] * $row['precio_unidad'], 2) : '&nbsp;');
			$tpl->assign('importe_pan', $row['otros'] > 0 ? number_format($row['otros'], 2) : '&nbsp;');
			$tpl->assign('base', $row['base'] > 0 ? number_format($row['base'], 2) : '&nbsp;');
			$tpl->assign('pastillaje', $row['pastillaje'] > 0 ? number_format($row['pastillaje'], 2) : '&nbsp;');
			$tpl->assign('otros_efectivos', $row['otros_efectivos'] > 0 ? number_format($row['otros_efectivos'], 2) : '&nbsp;');
			$tpl->assign('total', number_format($row['total_factura'], 2));
			$tpl->assign('a_cuenta', number_format($row['cuenta'], 2));
			$tpl->assign('resta_pagar', number_format($row['resta_pagar'], 2));
		}

		$tpl->printToScreen();

		die;
	}

	$sql = "
		SELECT
			tmp.tipo_pedido,
			tmp.num_remision,
			tmp.tipo_control,
			tmp.kilos,
			tmp.precio_kilo,
			tmp.descuento,
			tmp.importe_pastel,
			tmp.importe_pan,
			tmp.base,
			tmp.pastillaje,
			tmp.bocadillos,
			tmp.flete,
			COALESCE(vp.total_factura, tmp.total)
				AS total,
			tmp.fecha_entrega,
			tmp.importe,
			vp.id,
			COALESCE(vp.cuenta, 0)
				AS cuenta,
			COALESCE(vp.resta_pagar, 0)
				AS resta_pagar,
			COALESCE(vp.base, 0)
				AS importe_base
		FROM
			pasteles_tmp tmp
			LEFT JOIN venta_pastel vp
				ON (vp.num_cia = tmp.num_cia AND vp.num_remi = tmp.num_remision AND vp.letra_folio = (CASE WHEN tmp.tipo_pedido = 1 THEN 'X' WHEN tmp.tipo_pedido = 2 THEN 'P' END) AND vp.tipo = 0)
		WHERE
			tmp.num_cia = {$_REQUEST['num_cia']}
			AND tmp.fecha = '{$_REQUEST['fecha']}'
		ORDER BY
			tmp.num_remision,
			tmp.id
	";

	$result = $db->query($sql);

	if ( ! $result) {
		header("location: ./pan_rev_dat.php?action=" . ($_REQUEST['dir'] == 'r' ? 'gastos' : /*'avio'*/'exp') . "&num_cia={$_REQUEST['num_cia']}&fecha={$_REQUEST['fecha']}&dir={$_REQUEST['dir']}&nom={$_REQUEST['nom']}");
		die;
	}

	$tpl->newBlock('pasteles');

	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_REQUEST['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);

	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_REQUEST['fecha'], $fecha);

	$tpl->assign('_fecha', "{$fecha[1]} DE " . mes_escrito($fecha[2], TRUE) . " DE {$fecha[3]}");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_REQUEST['nom']);

	foreach ($result as $row) {
		$tpl->newBlock('pastel_row');

		$a_cuenta = in_array($row['tipo_control'], array(1, 2)) ? ($row['tipo_control'] == 1 ? $row['importe'] : $row['total'] - $row['resta_pagar']) : 0;
		$resta_pagar = in_array($row['tipo_control'], array(1, 2)) ? $row['total'] - ($row['tipo_control'] == 2 ? $a_cuenta : 0) - $row['importe'] : 0;

		$tpl->assign('num_remision', ($row['tipo_pedido'] == 2 ? 'P' : '') . $row['num_remision']);
		$tpl->assign('fecha_entrega', $row['tipo_control'] == 1 && $row['fecha_entrega'] != '' ? $row['fecha_entrega'] : '&nbsp;');
		$tpl->assign('kilos', $row['tipo_control'] == 1 && $row['kilos'] > 0 ? number_format($row['kilos'], 2) : '&nbsp;');
		$tpl->assign('precio_kilo', $row['tipo_control'] == 1 && $row['precio_kilo'] > 0 ? number_format($row['precio_kilo'], 2) : '&nbsp;');
		$tpl->assign('descuento', $row['tipo_control'] == 1 && $row['descuento'] > 0 ? number_format($row['descuento'], 2) : '&nbsp;');
		$tpl->assign('importe_pastel', $row['tipo_control'] == 1 && $row['importe_pastel'] > 0 ? number_format($row['importe_pastel'], 2) : '&nbsp;');
		$tpl->assign('importe_pan', $row['tipo_control'] == 1 && $row['importe_pan'] > 0 ? number_format($row['importe_pan'], 2) : '&nbsp;');
		$tpl->assign('base', $row['tipo_control'] == 1 && $row['base'] > 0 ? number_format($row['base'], 2) : '&nbsp;');
		$tpl->assign('pastillaje', $row['tipo_control'] == 1 && $row['pastillaje'] > 0 ? number_format($row['pastillaje'], 2) : '&nbsp;');
		$tpl->assign('bocadillos', $row['tipo_control'] == 1 && $row['bocadillos'] > 0 ? number_format($row['bocadillos'], 2) : '&nbsp;');
		$tpl->assign('flete', $row['tipo_control'] == 1 && $row['flete'] > 0 ? number_format($row['flete'], 2) : '&nbsp;');
		$tpl->assign('total', in_array($row['tipo_control'], array(1, 2)) && $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');
		$tpl->assign('a_cuenta', in_array($row['tipo_control'], array(1, 2)) && $a_cuenta > 0 ? number_format($a_cuenta, 2) : '&nbsp;');
		$tpl->assign('abono', $row['tipo_control'] == 2 && $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
		$tpl->assign('resta_pagar', in_array($row['tipo_control'], array(1, 2)) && $resta_pagar > 0 ? number_format($resta_pagar, 2) : '&nbsp;');
		$tpl->assign('devolucion_base', in_array($row['tipo_control'], array(3)) && $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
		$tpl->assign('importe_cancelacion', in_array($row['tipo_control'], array(4)) && $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
	}

	$tpl->printToScreen();

	die;
}

/************************************************************************/
/************************************************************************/
// GASTOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'gastos') {
	$sql = "SELECT id, codgastos, descripcion, fecha, concepto, importe, cod_turno, idrenexp, valid, omitir FROM gastos_tmp LEFT JOIN catalogo_gastos USING (codgastos) WHERE";
	$sql .= " num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'";
	$result = $db->query($sql);

	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'";
	$pres = $db->query($sql);

	$sql = "SELECT tipo_pedido, num_remision, importe FROM pasteles_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_control = 4";
	$pas = $db->query($sql);

	if (!($result || $pres || $pas)) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'avio') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
		die;
	}

	$tpl->newBlock('gastos');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	// [17-Dic-2007] Importe de nómina, arrastrarlo hasta la pantalla de gastos
	$tpl->assign('nom', $_GET['nom']);

	// [11-Jul-2007] Si hay error en el límite de gastos, mandar una alerta de error
	if (isset($_GET['cod'])) {
		if (isset($_GET['lim'])) {
			$desc = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = $_GET[cod]");
			$msg = "El importe total del mes para \\'{$desc[0]['descripcion']}\\' no puede ser mayor al limite " . number_format($_GET['lim'], 2, '.', ',');
			$field = 'codgastos' . (count($result) > 1 ? "[$_GET[i]]" : '');
			$tpl->assign('init', "msg('$msg', '$field');");
		} else if (isset($_GET['abono'])) {
			$msg = "El pan comprado es diferente de los abonado al expendio (" . number_format($_GET['abono'], 2, '.', ',') . ')';
			$field = 'codgastos' . (count($result) > 1 ? "[$_GET[i]]" : '');
			$tpl->assign('init', "msg('$msg', '$field');");
		}

	}
	else
		$tpl->assign('init', 'function () { f.codgastos.length == undefined ? f.codgastos.select() : f.codgastos[0].select(); obtenerNomina()}');

	// [14-Nov-2007] Obtener catálogo de límites de renta de expendios
	$renexp = $db->query("SELECT * FROM catalogo_renta_exp WHERE num_cia = $_GET[num_cia] AND status = 1");

	// [11-Ene-2008] Importe de sueldo para la decena
	/*if (in_array($fecha[1], array(10, 20, date('d', mktime(0, 0, 0, $fecha[2] + 1, 0, $fecha[3]))))) {
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha[2], $fecha[1] > 20 ? 21 : $fecha[1] - 9, $fecha['3']));
		$fecha2 = date('d/m/Y', mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]));

		$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND codgastos IN (1, 160) AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sueldo = $db->query($sql);

		$tpl->assign('sueldo', number_format($sueldo[0]['sum'], 2, '.', ''));
	}*/

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
			$tpl->assign($reg['codgastos'] == 49 ? 'display_turno' : 'display_exp', ' style="display:none;"');
			$tpl->assign('desc', $reg['descripcion']);
			$tpl->assign($reg['cod_turno'] > 0 ? $reg['cod_turno'] : '-', ' selected');
			$tpl->assign('concepto', trim(strtoupper($reg['concepto'])));
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			// [14-Nov-2007] Crear select de catalogo de límite de renta de expendios
			if ($renexp)
				foreach ($renexp as $re) {
					$tpl->newBlock('ren');
					$tpl->assign('id', $re['id']);
					$tpl->assign('nombre', $re['nombre']);
					if ($re['id'] == $reg['idrenexp']) $tpl->assign('ren_sel', ' selected');
				}
			$total += $reg['importe'];
		}
	if ($pres)
		foreach ($pres as $reg) {
			$tpl->newBlock('gas_pre');
			$tpl->assign('concepto', $reg['nombre']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$total += $reg['importe'];
		}
	if ($pas)
		foreach ($pas as $reg) {
			$tpl->newBlock('gas_pas');
			$tpl->assign('tipo_pedido', $reg['tipo_pedido'] == 2 ? 'PAN' : 'PASTEL');
			$tpl->assign('num_remi', $reg['num_remision']);
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

	// [14-Nov-2007] Arreglo de importes de límites de renta para expendios
	if ($renexp)
		foreach ($renexp as $reg) {
			$tpl->newBlock('re');
			$tpl->assign('id', $reg['id']);
			$tpl->assign('importe', $reg['importe']);
		}

	$tpl->printToScreen();
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'gastos_mod') {
	$sql = "";

	if (!isset($_POST['id'])) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'avio') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
		die;
	}

	for ($i = 0; $i < count($_POST['codgastos']); $i++) {
		$sql .= "UPDATE gastos_tmp SET concepto='" . trim(strtoupper($_POST['concepto'][$i])) . "', codgastos = " . ($_POST['codgastos'][$i] > 0 ? $_POST['codgastos'][$i] : 'NULL') . ", cod_turno = " . ($_POST['turno'][$i] > 0 ? $_POST['turno'][$i] : 'NULL') . ",";
		$sql .= " idrenexp = " . ($_POST['idrenexp'][$i] > 0 ? $_POST['idrenexp'][$i] : 'NULL') . ",";
		$sql .= " valid = '" . (isset($_POST['valid' . $i]) ? 'TRUE' : 'FALSE') . "', omitir = '" . (isset($_POST['omitir' . $i]) ? 'TRUE' : 'FALSE') . "',";
		$sql .= " aut = " . (isset($_POST['valid' . $i]) ? 0 : 1) . " WHERE id = {$_POST['id'][$i]};\n";
	}

	$db->query($sql);

	// [11-Jul-2007] Validar limites de gastos
	// Obtener limites para la panadería
	$tmp = $db->query("SELECT num_cia, codgastos, limite FROM catalogo_limite_gasto WHERE num_cia = $_GET[num_cia]");
	if ($tmp) {
		$lim = array();
		foreach ($tmp as $reg)
			$lim[$reg['codgastos']] = $reg['limite'];

		preg_match('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/', $_GET['fecha'], $tmp);
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
		$fecha2 = $_GET['fecha'];

		for ($i = 0; $i < count($_POST['codgastos']); $i++)
			if (get_val($_POST['codgastos'][$i]) > 0 && get_val($_POST['importe'][$i]) > 0 && isset($lim[get_val($_POST['codgastos'][$i])]) && !isset($_POST['omitir' . $i])) {
				// Obtener importe capturado del mes
				$tmp = $db->query("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND codgastos = {$_POST['codgastos'][$i]} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND captura = 'FALSE'");
				$imp = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] + get_val($_POST['importe'][$i]) : get_val($_POST['importe'][$i]);

				if ($imp > $lim[get_val($_POST['codgastos'][$i])]) {
					header("location: ./pan_rev_dat.php?action=gastos&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]&cod={$_POST['codgastos'][$i]}&lim={$lim[$_POST['codgastos'][$i]]}&i=$i");
					die;
				}
			}
	}

	/*
	@ [06-Dic-2012] Validar que el pan comprado sea igual a lo abonado del expendio relacionado
	*/
	/*foreach ($_POST['codgastos'] as $i => $codgastos) {
		if (in_array($codgastos, array(5, 152))) {
			$sql = '
				SELECT
					SUM(abono)
						AS abono
				FROM
					mov_exp_tmp mov
					LEFT JOIN catalogo_expendios ce
						ON (
							ce.num_cia = mov.num_cia
							AND ce.num_referencia = mov.num_expendio
						)
				WHERE
					mov.fecha = \'' . $_GET['fecha'] . '\'
					AND ce.num_cia_exp = ' . $_GET['num_cia'] . '
			';

			$tmp = $db->query($sql);

			if ($tmp[0]['abono'] > 0 && $tmp[0]['abono'] != get_val($_POST['importe'][$i])) {//echo $tmp[0]['abono'] . ' ' . get_val($_POST['importe'][$i]);die;
				header("location: ./pan_rev_dat.php?action=gastos&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]&cod={$codgastos}&abono={$tmp[0]['abono']}&i=$i");
					die;
			}
		}
	}*/

	header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'pres' : 'pasteles') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]&nom=$_GET[nom]");
	die;
}

/************************************************************************/
/************************************************************************/
// PRESTAMOS
/************************************************************************/
/************************************************************************/
if (isset($_GET['action']) && $_GET['action'] == 'pres') {
	$sql = "SELECT
		SUM (
			CASE
				WHEN p.tipo_mov = FALSE THEN
					p.importe
				ELSE
					- p.importe
			END
		) AS importe
	FROM
		prestamos p
		LEFT JOIN catalogo_trabajadores ct ON (ct. ID = p.id_empleado)
	WHERE
		ct.num_cia_emp = {$_GET['num_cia']}
		AND p.pagado = FALSE";

	$tmp = $db->query($sql);

	$saldo_oficina = $tmp && round($tmp[0]['importe'], 2) != 0 ? round($tmp[0]['importe'], 2) : 0;

	$sql = "SELECT
		SUM(saldo) AS importe
	FROM
		prestamos_tmp
	WHERE
		num_cia = {$_GET['num_cia']}
		AND fecha = '{$_GET['fecha']}'";

	$tmp = $db->query($sql);

	$saldo_panaderia = $tmp && round($tmp[0]['importe'], 2) != 0 ? round($tmp[0]['importe'], 2) : 0;

	$error = FALSE;

	if ($saldo_oficina != $saldo_panaderia/* && !in_array($_GET['num_cia'], array(1, 2, 53, 54, 106, 96, 97))*/) {
		$error = TRUE;
	}
	else {
		$error = FALSE;
	}

	$sql = "SELECT tmp.id, tmp.nombre AS nombre_tmp, ct.id AS id_emp, num_emp, ap_paterno, ap_materno, ct.nombre, saldo, tipo_mov, importe FROM prestamos_tmp AS tmp LEFT JOIN";
	$sql .= " catalogo_trabajadores AS ct ON (ct.id = idemp) WHERE tmp.num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'/* AND importe > 0*/ ORDER BY tmp.nombre";
	$result = $db->query($sql);

	if (!$result && !$error) {
		header("location: ./pan_rev_dat.php?action=" . ($_GET['dir'] == 'r' ? 'result' : 'gastos') . "&num_cia=$_GET[num_cia]&fecha=$_GET[fecha]&dir=$_GET[dir]");
		die;
	}

	$tpl->newBlock('pres');
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $_GET['fecha']);
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");

	$saldo_ini = 0;
	$cargos = 0;
	$abonos = 0;
	$saldo_fin = 0;

	$cont = 0;

	if ($result)
		foreach ($result as $i => $reg) {
			$nombre = trim("$reg[ap_paterno] $reg[ap_materno] $reg[nombre]");

			$tpl->newBlock('pres_row');

			if ($reg['importe'] != 0)
			{
				$tpl->newBlock('cell_pres');

				$tpl->assign('i', $cont);
				$tpl->assign('next', $cont < count($result) - 1 ? $cont + 1 : 0);
				$tpl->assign('back', $cont > 0 ? $i - 1 : count($result) - 1);
				$tpl->assign('id', $reg['id']);
				$tpl->assign('id_emp', $reg['id_emp']);
				$tpl->assign('num_emp', $reg['num_emp']);
				$tpl->assign('nombre_real', $nombre);

				$cont++;
			}
			else
			{
				$tpl->newBlock('cell_no_pres');
			}

			$tpl->gotoBlock('pres_row');

			$tpl->assign('nombre', $reg['nombre_tmp']);
			$tpl->assign('saldo_ini', $reg['saldo'] != 0 ? number_format($reg['saldo'], 2, '.', ',') : '');
			$tpl->assign($reg['tipo_mov'] == 'f' ? 'cargo' : 'abono', $reg['importe'] != 0 ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
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

	$cat = $db->query("
		SELECT
			*
		FROM
			(
				SELECT
					id,
					num_emp,
					ap_paterno,
					ap_materno,
					ct.nombre,
					(
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										importe
									ELSE
										-importe
								END
							)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
					)
						AS saldo_emp
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					(
						(num_cia = $_GET[num_cia] AND num_cia_emp = $_GET[num_cia])
						OR (num_cia != $_GET[num_cia] AND num_cia_emp = $_GET[num_cia])
					)
					AND (
						fecha_baja IS NULL
						OR id IN (
							SELECT
								id_empleado
							FROM
								prestamos
							WHERE
								num_cia = $_GET[num_cia]
								AND pagado = 'FALSE'
							GROUP BY
								id_empleado
						)
					)
			) result
		ORDER BY
			num_emp
	");
	if ($cat)
		foreach ($cat as $reg) {
			$tpl->newBlock('emp');
			$tpl->assign('num_emp', $reg['num_emp']);
			$tpl->assign('id_emp', $reg['id']);
			$tpl->assign('nombre', trim("$reg[ap_paterno] $reg[ap_materno] $reg[nombre]"));
			$tpl->assign('saldo_emp', number_format($reg['saldo_emp'], 2, '.', ''));
		}

	if ($error) {
		$tpl->assign('pres.disabled', ' disabled');
		$tpl->newBlock('error_prestamos');
		$tpl->assign('saldo', number_format($saldo_oficina, 2, '.', ','));
	}

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

	/*
	@ [05-Nov-2012] Validar que el nombre del empleado en panadería y en sistema sean exactamente iguales
	*/

	$ok = TRUE;

	$errores = array();

	foreach ($_POST['id'] as $i => $id) {
		$sql = '
			SELECT
				CASE
					WHEN CONCAT_WS(\' \', ct.nombre, ct.ap_paterno, ct.ap_materno) = TRIM(REGEXP_REPLACE(p.nombre, \'\s+\', \' \', \'g\')) THEN
						TRUE
					WHEN CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre) = TRIM(REGEXP_REPLACE(p.nombre, \'\s+\', \' \', \'g\')) THEN
						TRUE
					ELSE
						FALSE
				END
					AS status,
				ct.num_emp,
				CASE
					WHEN CONCAT_WS(\' \', ct.nombre, ct.ap_paterno, ct.ap_materno) = TRIM(REGEXP_REPLACE(p.nombre, \'\s+\', \' \', \'g\')) THEN
						CONCAT_WS(\' \', ct.nombre, ct.ap_paterno, ct.ap_materno)
					WHEN CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre) = TRIM(REGEXP_REPLACE(p.nombre, \'\s+\', \' \', \'g\')) THEN
						CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre)
					ELSE
						ct.nombre_completo
				END
					AS nombre_sistema,
				p.nombre
					AS nombre_panaderia
			FROM
				prestamos_tmp p
				LEFT JOIN catalogo_trabajadores ct
					ON (ct.id = idemp)
			WHERE
				p.id = ' . $id . '
		';

		$status = $db->query($sql);

		if ($status[0]['status'] == 'f') {
			$errores[] = $status[0];

			$ok = FALSE;
		}
	}

	/*if (!$ok) {
		$tpl->newBlock('pres_error');
		$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';

		$tpl->assign('num_cia', $_GET['num_cia']);
		$tpl->assign('nombre_cia', $nombre_cia);
		$tpl->assign('fecha', $_GET['fecha']);
		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
		$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");

		foreach ($errores as $error) {
			$tpl->newBlock('pres_error_row');
			$tpl->assign('num_emp', $error['num_emp']);
			$tpl->assign('nombre_sistema', $error['nombre_sistema']);
			$tpl->assign('nombre_panaderia', $error['nombre_panaderia']);
		}

		$tpl->printToScreen();
		die;
	}*/

	/*---------------------------------------------------------------------------------------------------*/

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
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $_GET['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");

	$abonos = $db->query("SELECT sum(abono) AS abono FROM mov_exp_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'");
	$raya_pagada = $db->query("SELECT sum(/*raya_ganada*/raya_pagada) AS raya_pagada FROM total_produccion_tmp WHERE num_cia = $_GET[num_cia] AND fecha_total = '$_GET[fecha]'");
	$tmp1 = $db->query("SELECT sum(importe) AS importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND omitir = 'FALSE'");
	$tmp2 = $db->query("SELECT sum(importe) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE'");
	$tmp3 = $db->query("SELECT sum(importe) AS importe FROM pasteles_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_control IN (3, 4)");
	$pasteles = $db->query("SELECT sum(importe) AS importe, SUM(COALESCE(base, 0)) AS bases, SUM(COALESCE(pastillaje, 0)) AS pastillajes, SUM(COALESCE(flete, 0)) AS fletes FROM pasteles_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_control IN (1, 2)");
	$gastos = $tmp1[0]['importe'] + $tmp2[0]['importe'] + $tmp3[0]['importe'];
	$venta_puerta = $result[0]['cajaam'] - $result[0]['erroramcaja'] + $result[0]['cajapm'] - $result[0]['errorpmcaja'] + $result[0]['pastelam'] + $result[0]['pastelpm'];
	$abono_obreros = $db->query("SELECT sum(importe) AS importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'TRUE'");
	$otros = $result[0]['barredura'] + $result[0]['bases'] + $result[0]['esquilmos'] + $result[0]['botes'] + $result[0]['costales'] + $abono_obreros[0]['importe'];
	$efectivo = $venta_puerta + ($pasteles[0]['importe'] - $pasteles[0]['bases'] - $pasteles[0]['pastillajes'] - $pasteles[0]['fletes']) + $abonos[0]['abono'] + $result[0]['pastillaje'] + $otros - $raya_pagada[0]['raya_pagada'] - $gastos;

	$tpl->assign('venta_puerta', $venta_puerta != 0 ? number_format($venta_puerta, 2, '.' ,',') : '&nbsp;');
	$tpl->assign('pasteles', $pasteles[0]['importe'] != 0 ? number_format($pasteles[0]['importe'] - $result[0]['bases'] - $pasteles[0]['pastillajes'] - $pasteles[0]['fletes'], 2, '.' ,',') : '&nbsp;');
	$tpl->assign('abonos', $abonos[0]['abono'] != 0 ? number_format($abonos[0]['abono'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('pastillaje', $result[0]['pastillaje'] != 0 ? number_format($result[0]['pastillaje'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('otros', $otros != 0 ? number_format($otros, 2, '.', ',') : '&nbsp;');
	$tpl->assign('raya_pagada', $raya_pagada[0]['raya_pagada'] != 0 ? number_format($raya_pagada[0]['raya_pagada'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('gastos', $gastos != 0 ? number_format($gastos, 2, '.', ',') : '&nbsp;');
	$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2, '.', ',') : '&nbsp;');

	if (isset($_GET['error'])) {
		$tpl->newBlock('error');
	}

//	if ($_SESSION['iduser'] == 11) {
//		$tpl->assign('disabled_terminar', ' disabled');
//
//		$tpl->newBlock('horror');
//	}

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
	preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $fecha_des);

	// Produccion
	$sql = "INSERT INTO produccion (cod_producto, cod_turnos, num_cia, fecha, piezas, imp_raya, imp_produccion, precio_raya, precio_venta, porc_raya) SELECT cod_producto, cod_turnos,";
	$sql .= " num_cia, fecha, piezas, imp_raya, imp_produccion, precio_raya, precio_venta, porc_raya FROM produccion_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';\n";

	// Totales de produccion (raya_ganada => raya_pagada, raya_pagada => raya_ganada)
	$sql .= "INSERT INTO total_produccion (numcia, codturno, fecha_total, raya_ganada, raya_pagada, total_produccion) SELECT num_cia, codturno, fecha_total, raya_ganada, raya_pagada,";
	$sql .= " total_produccion FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha';\n";

	// Pan Contado
	$fecha1 = "01/$fecha_des[2]/$fecha_des[3]";
	$tmp1 = $db->query("SELECT pan_contado FROM prueba_pan_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	$tmp2 = $db->query("SELECT sum(total_produccion) AS pro FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha'");
	$tmp3 = $db->query("SELECT sum(total_produccion) AS pro FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha'");
	$suma = $tmp2[0]['pro'] + $tmp3[0]['pro'];
	$sql .= "INSERT INTO prueba_pan (num_cia, fecha, importe, produccion) VALUES ($num_cia, '$fecha', {$tmp1[0]['pan_contado']}, $suma / $fecha_des[1]);\n";

	// Expendios
	//$sql .= "INSERT INTO mov_expendios (num_cia, fecha, num_expendio, nombre_expendio, pan_p_venta, pan_p_expendio, porc_ganancia, abono, devolucion, rezago, rezago_anterior) SELECT";
	//$sql .= " mov.num_cia, fecha, cat.num_expendio, nombre, pan_p_venta, pan_p_expendio, porc_ganancia, abono, devolucion, rezago, rezago_anterior FROM mov_exp_tmp AS mov LEFT JOIN";
	//$sql .= " catalogo_expendios AS cat ON (cat.num_cia = mov.num_cia AND cat.num_referencia = mov.num_expendio) WHERE mov.num_cia = $num_cia AND fecha = '$fecha';\n";

	$query = "SELECT tmp.num_expendio AS num_tmp, num_referencia AS num_cat, cat.num_expendio AS num_exp, nombre_expendio AS nombre_tmp, nombre AS nombre_cat, porc_ganancia AS por_tmp,";
	$query .= " porciento_ganancia AS por_cat, importe_fijo AS fijo, rezago_anterior, pan_p_venta, pan_p_expendio, abono, devolucion, rezago, notas, aut_dev, rezago FROM mov_exp_tmp AS tmp LEFT JOIN";
	$query .= " catalogo_expendios AS cat ON (cat.num_cia = tmp.num_cia AND num_referencia = tmp.num_expendio) WHERE tmp.num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY num_tmp";
	$result = $db->query($query);

	$exp = array();
	$cont = 0;
	if ($result) {
		foreach ($result as $reg) {
			$exp[$cont]['num_cia'] = $num_cia;
			$exp[$cont]['fecha'] = $fecha;
			$exp[$cont]['num_expendio'] = $reg['num_exp'];
			$exp[$cont]['nombre_expendio'] = $reg['nombre_cat'];
			$exp[$cont]['porc_ganancia'] = round($reg['por_cat'], 2);

			$tmp = $db->query("SELECT fecha, rezago FROM mov_expendios WHERE num_cia = $_GET[num_cia] AND fecha < '$_GET[fecha]' AND num_expendio = $reg[num_exp] ORDER BY fecha DESC LIMIT 1");
			$rezago_ant = $tmp ? $tmp[0]['rezago'] : 0;
			if ($reg['rezago_anterior'] < $rezago_ant - 0.30 || $reg['rezago_anterior'] > $rezago_ant + 0.30)
				$exp[$cont]['rezago_anterior'] = round($rezago_ant, 2);
			else
				$exp[$cont]['rezago_anterior'] = round($reg['rezago_anterior'], 2);

			if ($reg['notas'] == 't') {
				$dev = $reg['por_tmp'] != 0 ? $reg['devolucion'] * ((100 - $reg['por_cat']) / 100) : $reg['devolucion'];

				$exp[$cont]['pan_p_venta'] = 0;
				$exp[$cont]['devolucion'] = $reg['aut_dev'] == 't' ? round($dev, 2) : 0;
				$exp[$cont]['abono'] = 0;
				$exp[$cont]['pan_p_expendio'] = 0;
				$exp[$cont]['rezago'] = $reg['aut_dev'] == 't' ? round($exp[$cont]['rezago_anterior'] - $dev, 2) : round($exp[$cont]['rezago_anterior'], 2);
			}
			else {
				$exp[$cont]['pan_p_venta'] = round($reg['pan_p_venta'], 2);
				$exp[$cont]['devolucion'] = round($reg['devolucion'], 2);
				$exp[$cont]['abono'] = round($reg['abono'], 2);

				// - Validar que el Pan para expendio sea congruente con los topes establecidos en el catálogo -
				$tope = round($reg['fijo'] > 0 ? $reg['pan_p_venta'] - $reg['fijo'] - 0.30 : ($reg['pan_p_venta'] * (100 - $reg['por_tmp']) / 100) - 0.30, 2);	// Calculo del tope
				if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] == 0 && $reg['por_tmp'] == 0 && $reg['pan_p_expendio'] > $reg['pan_p_venta'])
					$exp[$cont]['pan_p_expendio'] = $tope;
				else if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] > 0 && $reg['por_tmp'] == 0 && $reg['pan_p_expendio'] > $tope)
					$exp[$cont]['pan_p_expendio'] = $tope;
				else if ($reg['pan_p_expendio'] > 0 && $reg['fijo'] == 0 && $reg['por_tmp'] > 0 && $reg['pan_p_expendio'] < $tope)
					$exp[$cont]['pan_p_expendio'] = $tope;
				else
					$exp[$cont]['pan_p_expendio'] = round($reg['pan_p_expendio'], 2);

				// [04-Abr-2007] Recalcular devolucion
				$dev = round($reg['por_tmp'] != 0 ? $reg['devolucion'] * ((100 - $reg['por_cat']) / 100) : $reg['devolucion'], 2);

				$exp[$cont]['rezago'] = /*round($exp[$cont]['rezago_anterior'] + $reg['pan_p_expendio'] - $exp[$cont]['abono'] - $dev, 2)*/round($reg['rezago'], 2);
			}
			$cont++;
		}

		$sql .= $db->multiple_insert("mov_expendios", $exp);
	}

	// Gastos
	$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, cod_turno, concepto, importe, captura) SELECT num_cia, fecha, codgastos, cod_turno, upper(concepto), importe,";
	$sql .= " 'FALSE' FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND omitir = 'FALSE' AND codgastos NOT IN (114, 115);\n";

	// Movimientos de Avio
	// $sql .= "INSERT INTO mov_inv_virtual (num_cia, fecha, codmp, cod_turno, tipo_mov, cantidad, descripcion) SELECT num_cia, fecha, codmp, cod_turno, tipomov, CASE WHEN codmp = 1 THEN";
	// $sql .= " cantidad * 44 ELSE cantidad END, CASE WHEN tipomov = 'FALSE' THEN 'ENTRADA VIRTUAL DE AVIO' ELSE 'SALIDA VIRTUAL DE AVIO' END FROM mov_inv_tmp WHERE num_cia = $num_cia AND";
	// $sql .= " fecha = '$fecha' ORDER BY codmp, tipomov;\n";
	// $sql .= "INSERT INTO mov_inv_real (num_cia, fecha, codmp, cod_turno, tipo_mov, cantidad, descripcion) SELECT num_cia, fecha, codmp, cod_turno, tipomov, CASE WHEN codmp = 1 THEN";
	// $sql .= " cantidad * 44 ELSE cantidad END, 'SALIDA DE AVIO' FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND tipomov = 'TRUE' ORDER BY codmp, tipomov;\n";

	// Actualizar inventario real y virtual
	// $movs = $db->query("SELECT codmp, tipomov, cantidad FROM mov_inv_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	// if ($movs)
	// 	foreach ($movs as $mov)
	// 		if ($mov['tipomov'] == 'f')
	// 			$sql .= "UPDATE inventario_virtual SET existencia = existencia + " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
	// 		else {
	// 			$sql .= "UPDATE inventario_virtual SET existencia = existencia - " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
	// 			$sql .= "UPDATE inventario_real SET existencia = existencia - " . ($mov['codmp'] == 1 ? $mov['cantidad'] * 44 : $mov['cantidad']) . " WHERE num_cia = $num_cia AND codmp = $mov[codmp];\n";
	// 		}

	// Prestamos
	$pres = $db->query("SELECT p.num_cia, fecha, idemp, ct.nombre, ap_paterno, ap_materno, tipo_mov, importe FROM prestamos_tmp AS p LEFT JOIN catalogo_trabajadores AS ct ON (ct.id = idemp) WHERE p.num_cia = $num_cia AND fecha = '$fecha' AND importe != 0 ORDER BY idemp, tipo_mov");
	if ($pres)
		foreach ($pres as $reg) {
			if ($reg['tipo_mov'] == 'f') {
				if ($id = $db->query("SELECT id FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'FALSE' AND pagado = 'FALSE'"))
					$sql .= "UPDATE prestamos SET importe = importe + $reg[importe] WHERE id = {$id[0]['id']};\n";
				else
					$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $reg[importe], 'FALSE', 'FALSE', $reg[idemp]);\n";
				$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, importe, captura) VALUES ($num_cia, '$fecha', 41, 'PRESTAMO EMPLEADO NO. $reg[idemp]";
				$sql .= " $reg[nombre] $reg[ap_paterno] $reg[ap_materno]', $reg[importe], 'FALSE');\n";
			}
			if ($reg['tipo_mov'] == 't') {
				$sql .= "INSERT INTO prestamos (num_cia, fecha, importe, tipo_mov, pagado, id_empleado) VALUES ($num_cia, '$fecha', $reg[importe], 'TRUE', 'FALSE', $reg[idemp]);\n";
				// Verificar si ya se liquido el saldo
				$prestamo = $db->query("SELECT importe FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'FALSE' AND pagado = 'FALSE'");
				$abonos = $db->query("SELECT sum(importe) AS importe FROM prestamos WHERE id_empleado = $reg[idemp] AND tipo_mov = 'TRUE' AND pagado = 'FALSE'");
				if (round($prestamo[0]['importe'], 2) == round($abonos[0]['importe'], 2) + round($reg['importe'], 2))
					$sql .= "UPDATE prestamos SET pagado = 'TRUE' WHERE id_empleado = $reg[idemp] AND pagado = 'FALSE';\n";
			}
		}

	$efectivo_tmp = $db->query("SELECT * FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	// Cortes de caja (captura_efectivos)
	$sql .= "INSERT INTO captura_efectivos (num_cia, fecha, am, am_error, pm, pm_error, pastel, venta_pta, pastillaje, otros, ctes, corte1, corte2, desc_pastel) SELECT num_cia, fecha,";
	$sql .= " cajaam, erroramcaja, cajapm, errorpmcaja, pastelam + pastelpm, cajaam + cajapm - erroramcaja - errorpmcaja + pastelam + pastelpm, pastillaje, barredura + bases + esquilmos";
	$sql .= " + botes + costales + (CASE WHEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'TRUE')";
	$sql .= " IS NOT NULL THEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'TRUE') ELSE 0 END),";
	$sql .= " clientesam + clientespm - erroramclientes - errorpmclientes + clientespastelam + clientespastelpm, (SELECT ticket FROM corte_tmp WHERE num_cia = efectivos_tmp.num_cia";
	$sql .= " AND fecha = efectivos_tmp.fecha AND tipo = 1 ORDER BY ticket DESC LIMIT 1), (SELECT ticket FROM corte_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha =";
	$sql .= " efectivos_tmp.fecha AND tipo = 2 ORDER BY ticket DESC LIMIT 1), (SELECT descuentos FROM prueba_pan_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha)";
	$sql .= " FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';\n";

	// Barredura
	if ($efectivo_tmp[0]['barredura'])
		$sql .= "INSERT INTO barredura (num_cia, fecha_cap, fecha_pago, importe) VALUES ($num_cia, CURRENT_DATE, '$fecha', {$efectivo_tmp[0]['barredura']});\n";

	// [17-Feb-2009] Guardar historico de autorizacion de pan contado
	$tmp = $db->query("SELECT pan_contado FROM prueba_pan_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");
	$pan_con = $tmp ? round($tmp[0]['pan_contado'], 2) : 0;
	preg_match('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/', $fecha, $f);
	$fecha_pro1 = date('d/m/Y', mktime(0, 0, 0, $f[2] - ($f[1] == 1 ? 1 : 0), 1, $f[3]));
	$fecha_pro2 = date('d/m/Y', mktime(0, 0, 0, $f[2], $f[1] == 1 ? 0 : $f[1], $f[3]));
	$dias_lim = date('j', mktime(0, 0, 0, $f[2], $f[1] == 1 ? 0 : $f[1], $f[3]));
	$tmp = $db->query("SELECT sum(total_produccion) / $dias_lim AS limite FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha_pro1' AND '$fecha_pro2'");
	$limite = $tmp ? round($tmp[0]['limite'], 2) : 0;

	if ($pan_con > $limite)
		$sql .= "INSERT INTO pan_contado_aut (num_cia, fecha, pan_contado, produccion, iduser) VALUES ($num_cia, '$fecha', $pan_con, $limite, $_SESSION[iduser]);\n";


	// Efectivo Total (total_panaderias)
	if ($id = $db->query("SELECT id, CASE WHEN efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE' THEN 't' ELSE 'f' END AS status FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$fecha'")) {
		if ($id[0]['status'] == 't') {
			header("location: pan_rev_dat.php?action=result&num_cia=$num_cia&fecha=$fecha&dir=r&error=1");
			die;
		}

		$gastos_tmp = $db->query("SELECT sum(importe) FROM gastos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND omitir = 'FALSE' AND codgastos NOT IN (114, 115)");
		$exp_tmp = $db->query("SELECT sum(abono) FROM mov_exp_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND num_expendio IN (SELECT num_referencia FROM catalogo_expendios WHERE num_cia = $num_cia AND notas = 'FALSE')");
		$pro_tmp = $db->query("SELECT sum(raya_ganada) FROM total_produccion_tmp WHERE num_cia = $num_cia AND fecha_total = '$fecha'");
		$pre_car = $db->query("SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND tipo_mov = 'FALSE'");
		$pre_abo = $db->query("SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha' AND tipo_mov = 'TRUE'");
		$venta_puerta = $efectivo_tmp[0]['cajaam'] + $efectivo_tmp[0]['cajapm'] - $efectivo_tmp[0]['erroramcaja'] - $efectivo_tmp[0]['errorpmcaja'] + $efectivo_tmp[0]['pastelam'] + $efectivo_tmp[0]['pastelpm'];
		$otros = $efectivo_tmp[0]['barredura'] + $efectivo_tmp[0]['esquilmos'] + $efectivo_tmp[0]['botes'] + $efectivo_tmp[0]['costales'] + $pre_abo[0]['sum'] + $efectivo_tmp[0]['tiempo_aire'];
		$abono = $exp_tmp[0]['sum'] > 0 ? $exp_tmp[0]['sum'] : 0;
		$gastos = ($gastos_tmp[0]['sum'] > 0 ? $gastos_tmp[0]['sum'] : 0) + ($pre_car[0]['sum'] > 0 ? $pre_car[0]['sum'] : 0);
		$raya_pagada = $pro_tmp[0]['sum'] > 0 ? $pro_tmp[0]['sum'] : 0;
		$efectivo = $venta_puerta + $efectivo_tmp[0]['pastillaje'] + $abono + $otros - $raya_pagada - $gastos;

		$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + $venta_puerta, pastillaje = {$efectivo_tmp[0]['pastillaje']}, otros = otros + $otros, abono = $abono, gastos = gastos + $gastos,";
		$sql .= " raya_pagada = $raya_pagada, efectivo = efectivo + $efectivo, efe = 'TRUE', exp = 'TRUE', gas = 'TRUE', pro = 'TRUE', pas = 'TRUE', status = {$efectivo_tmp[0]['status']} WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	}
	else {
		$sql .= "INSERT INTO total_panaderias (num_cia, fecha, venta_puerta, pastillaje, otros, abono, gastos, raya_pagada, efectivo, venta_pastel, abono_pastel, efe, exp, gas, pro, pas, status)";
		$sql .= " SELECT num_cia, fecha, cajaam + cajapm - erroramcaja - errorpmcaja + pastelam + pastelpm, pastillaje, barredura /*+ bases*/ + esquilmos + botes + costales + (CASE WHEN (SELECT";
		$sql .= " sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'TRUE') IS NOT NULL THEN (SELECT sum(importe) FROM";
		$sql .= " prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'TRUE') ELSE 0 END) + tiempo_aire, CASE WHEN (SELECT sum(abono) FROM mov_exp_tmp WHERE";
		$sql .= " num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND num_expendio IN (SELECT num_referencia FROM catalogo_expendios WHERE num_cia = $num_cia AND notas = 'FALSE')) IS NOT NULL THEN (SELECT sum(abono) FROM mov_exp_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha =";
		$sql .= " efectivos_tmp.fecha AND num_expendio IN (SELECT num_referencia FROM catalogo_expendios WHERE num_cia = $num_cia AND notas = 'FALSE')) ELSE 0 END, (CASE WHEN (SELECT sum(importe) FROM gastos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND omitir = 'FALSE' AND codgastos NOT IN (114, 115)) IS NOT NULL THEN";
		$sql .= " (SELECT sum(importe) FROM gastos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND omitir = 'FALSE' AND codgastos NOT IN (114, 115)) ELSE 0 END) + (CASE WHEN (SELECT sum(importe) FROM prestamos_tmp";
		$sql .= " WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'FALSE') IS NOT NULL THEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia =";
		$sql .= " efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'FALSE') ELSE 0 END), (SELECT sum(/*raya_ganada*/raya_pagada) FROM total_produccion_tmp WHERE num_cia =";
		$sql .= " efectivos_tmp.num_cia AND fecha_total = efectivos_tmp.fecha), (cajaam + cajapm - erroramcaja - errorpmcaja + pastelam + pastelpm) + pastillaje + (barredura /*+ bases*/ +";
		$sql .= " esquilmos + botes + costales + COALESCE(tiempo_aire, 0) + (CASE WHEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov =";
		$sql .= " 'TRUE') IS NOT NULL THEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'TRUE') ELSE 0 END))";
		$sql .= " + (CASE WHEN (SELECT sum(abono) FROM mov_exp_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND num_expendio IN (SELECT num_referencia FROM catalogo_expendios WHERE num_cia = $num_cia AND notas = 'FALSE')) IS NOT NULL THEN (SELECT sum(abono) FROM mov_exp_tmp";
		$sql .= " WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND num_expendio IN (SELECT num_referencia FROM catalogo_expendios WHERE num_cia = $num_cia AND notas = 'FALSE')) ELSE 0 END) - ((CASE WHEN (SELECT sum(importe) FROM gastos_tmp WHERE num_cia = efectivos_tmp.num_cia";
		$sql .= " AND fecha = efectivos_tmp.fecha AND omitir = 'FALSE' AND codgastos NOT IN (114, 115)) IS NOT NULL THEN (SELECT sum(importe) FROM gastos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND omitir = 'FALSE' AND codgastos NOT IN (114, 115)) ELSE 0 END)";
		$sql .= " + (CASE WHEN (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'FALSE') IS NOT NULL THEN";
		$sql .= " (SELECT sum(importe) FROM prestamos_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha = efectivos_tmp.fecha AND tipo_mov = 'FALSE') ELSE 0 END)) - (CASE WHEN (SELECT";
		$sql .= " sum(/*raya_ganada*/raya_pagada) FROM total_produccion_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha_total = efectivos_tmp.fecha) IS NOT NULL THEN (SELECT sum(/*raya_ganada*/raya_pagada) FROM";
		$sql .= " total_produccion_tmp WHERE num_cia = efectivos_tmp.num_cia AND fecha_total = efectivos_tmp.fecha) ELSE 0 END), 0, 0, 'TRUE', 'TRUE', 'TRUE', 'TRUE', CASE WHEN pasteles > 0";
		$sql .= " THEN FALSE ELSE TRUE END, status FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	}

	// Actualiza timestamps de autorizacion
	$sql .= "UPDATE produccion_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE total_produccion_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha_total = '$fecha';\n";
	// $sql .= "UPDATE mov_inv_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
	$sql .= "UPDATE pasteles_tmp SET ts_aut = now(), iduser = $_SESSION[iduser] WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
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
	// $sql .= "UPDATE his_aut_con_avio SET status = 1, iduser = $_SESSION[iduser], tsmod = now() WHERE num_cia = $num_cia AND fecha = '$fecha';\n";

	//echo "<pre>$sql</pre>";
	$db->query($sql);

	// Si hay registros de pasteles insertarlos posterior al efectivo
	$result = $db->query("
		SELECT
			tmp.num_cia,
			tmp.fecha,
			tmp.tipo_pedido,
			tmp.num_remision,
			tmp.tipo_control,
			tmp.kilos,
			tmp.precio_kilo,
			tmp.descuento,
			tmp.importe_pastel,
			tmp.importe_pan,
			tmp.base,
			tmp.pastillaje,
			tmp.bocadillos,
			tmp.flete,
			COALESCE(vp.total_factura, tmp.total)
				AS total,
			tmp.fecha_entrega,
			tmp.importe,
			vp.id,
			COALESCE(vp.cuenta, 0)
				AS cuenta,
			COALESCE(vp.resta_pagar, 0)
				AS resta_pagar,
			COALESCE(vp.base, 0)
				AS importe_base
		FROM
			pasteles_tmp tmp
			LEFT JOIN venta_pastel vp
				ON (vp.num_cia = tmp.num_cia AND vp.num_remi = tmp.num_remision AND vp.letra_folio = (CASE WHEN tmp.tipo_pedido = 1 THEN 'X' WHEN tmp.tipo_pedido = 2 THEN 'P' END) AND vp.tipo = 0)
		WHERE
			tmp.num_cia = {$_REQUEST['num_cia']}
			AND tmp.fecha = '{$_REQUEST['fecha']}'
		ORDER BY
			tmp.num_remision,
			tmp.id
	");

	if ($result)
	{
		foreach ($result as $row)
		{
			if ($row['tipo_control'] == 1)
			{
				$otros_efectivos = $row['bocadillos'] + $row['flete'];
				$precio_kilo = $row['kilos'] > 0 ? $row['importe_pastel'] / $row['kilos'] : 0;
				$resta = $row['total'] - $row['importe'];
				$venta = $row['importe'] > 0 ? $row['importe'] - $row['base'] - $row['pastillaje'] - $otros_efectivos : 0;

				$sql = "INSERT INTO venta_pastel (num_cia, fecha, num_remi, kilos, precio_unidad, otros, base, cuenta, fecha_entrega, letra_folio, total_factura, resta_pagar, pastillaje, otros_efectivos, estado, tipo) VALUES ({$row['num_cia']}, '{$row['fecha']}', {$row['num_remision']}, {$row['kilos']}, {$precio_kilo}, {$row['importe_pan']}, {$row['base']}, {$row['importe']}, '{$row['fecha_entrega']}', '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "', {$row['total']}, {$resta}, {$row['pastillaje']}, {$otros_efectivos}, 0, 0);\n";

				$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + {$venta}, otros = otros + {$row['base']} + {$otros_efectivos}, efectivo = efectivo + {$venta} + {$row['base']}, venta_pastel = venta_pastel + {$venta} WHERE num_cia = {$_REQUEST['num_cia']} AND fecha = '{$_REQUEST['fecha']}';\n";

				if ($resta == 0)
				{
					$sql .= "UPDATE venta_pastel SET estado = 1 WHERE num_cia = {$row['num_cia']} AND num_remi = {$row['num_remision']} AND letra_folio = '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "';\n";
				}

				$db->query($sql);
			}
			else if ($row['tipo_control'] == 2)
			{
				$control1 = $db->query("SELECT id, resta_pagar FROM venta_pastel WHERE num_cia = {$row['num_cia']} AND letra_folio = '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "' AND num_remi = {$row['num_remision']} AND tipo = 0");

				$resta_pagar = $control1[0]['resta_pagar'] - $row['importe'];

				$sql = "INSERT INTO venta_pastel (num_cia, fecha, num_remi, letra_folio, resta, estado, tipo) VALUES ({$row['num_cia']}, '{$row['fecha']}', {$row['num_remision']}, '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "', {$row['importe']}, 0, 1);\n";

				$sql .= "UPDATE venta_pastel SET resta_pagar = {$resta_pagar} WHERE id = {$control1[0]['id']};\n";

				if ($resta_pagar == 0)
				{
					$sql .= "UPDATE venta_pastel SET estado = 1 WHERE num_cia = {$row['num_cia']} AND num_remi = {$row['num_remision']} AND letra_folio = '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "';\n";
				}

				$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + {$row['importe']}, efectivo = efectivo + {$row['importe']}, venta_pastel = venta_pastel + {$row['importe']} WHERE num_cia = {$_REQUEST['num_cia']} AND fecha = '{$_REQUEST['fecha']}';\n";

				$db->query($sql);
			}
			else if ($row['tipo_control'] == 3)
			{
				$sql = "INSERT INTO venta_pastel (num_cia, fecha, num_remi, letra_folio, dev_base, estado, tipo) VALUES ({$row['num_cia']}, '{$row['fecha']}', {$row['num_remision']}, '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "', {$row['importe']}, 1, 1);\n";

				$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, importe, captura) VALUES ({$row['num_cia']}, '{$row['fecha']}', 114, 'DEVOLUCION DE BASE NOTA " . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "{$row['num_remision']}', {$row['importe']}, FALSE);\n";

				$sql .= "UPDATE total_panaderias SET gastos = gastos + {$row['importe']}, efectivo = efectivo - {$row['importe']} WHERE num_cia = {$_REQUEST['num_cia']} AND fecha = '{$_REQUEST['fecha']}';\n";

				$db->query($sql);
			}
			else if ($row['tipo_control'] == 4)
			{
				$sql = "UPDATE venta_pastel SET estado = 2 WHERE num_cia = {$row['num_cia']} AND num_remi = {$row['num_remision']} AND letra_folio = '" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "';\n";

				$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, importe, captura) VALUES ({$row['num_cia']}, '{$row['fecha']}', 115, 'CANCELACION PEDIDO " . ($row['tipo_pedido'] == 2 ? 'PAN' : 'PASTEL') . " #" . ($row['tipo_pedido'] == 2 ? 'P' : 'X') . "{$row['num_remision']}', {$row['importe']}, FALSE);\n";

				$sql .= "UPDATE total_panaderias SET gastos = gastos + {$row['importe']}, efectivo = efectivo - {$row['importe']} WHERE num_cia = {$_REQUEST['num_cia']} AND fecha = '{$_REQUEST['fecha']}';\n";

				$db->query($sql);
			}
		}

	}

	/*
	@ [05-Nov-2012] Enviar correo en caso de movimientos de prestamos
	*/

	$sql = '
		SELECT
			p.num_cia,
			cc.nombre_corto
				AS nombre_cia,
			ct.num_emp,
			ct.nombre_completo
				AS nombre_emp,
			COALESCE((
				SELECT
					SUM(
						CASE
							WHEN tipo_mov = FALSE THEN
								importe
							ELSE
								-importe
						END
					)
				FROM
					prestamos
				WHERE
					id_empleado = p.idemp
					AND pagado = FALSE
			), 0)
				AS saldo,
			CASE
				WHEN p.tipo_mov = FALSE THEN
					\'<span style="color:#C00;">PRESTAMO</span>\'
				WHEN p.tipo_mov = TRUE THEN
					\'<span style="color:#00C;">ABONO</span>\'
			END
				AS tipo,
			tipo_mov,
			importe,
			ca.email
				AS email_admin
		FROM
			prestamos_tmp p
			LEFT JOIN catalogo_trabajadores ct
				ON (ct.id = p.idemp)
			LEFT JOIN catalogo_companias cc
				ON (cc.num_cia = p.num_cia)
			LEFT JOIN catalogo_administradores ca
				USING (idadministrador)
		WHERE
			p.num_cia = ' . $num_cia . '
			AND p.fecha = \'' . $fecha . '\'
			AND p.importe != 0
		ORDER BY
			p.id
	';

	$prestamos = $db->query($sql);

	if ($prestamos) {
		include_once('includes/phpmailer/class.phpmailer.php');

		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'mollendo@lecaroz.com';
		$mail->Password = 'L3c4r0z*';

		$mail->From = 'mollendo@lecaroz.com';
		$mail->FromName = 'Oficinas Administrativas Mollendo, S.A de R.L.';

		$mail->AddAddress('miguelrebuelta@lecaroz.com');
		//$mail->AddBCC('carlos.candelario@lecaroz.com');

		if ($prestamos[0]['email_admin'] != '') {
			$mail->AddAddress($prestamos[0]['email_admin']);
		}

		$mail->Subject = '[' . $prestamos[0]['num_cia'] . ' ' . $prestamos[0]['nombre_cia'] . '] Prestamos del día ' . $fecha;

		$tpl = new TemplatePower('plantillas/pan/email_prestamos_dia.tpl');
		$tpl->prepare();

		$tpl->assign('num_cia', $emp[0]['num_cia']);
		$tpl->assign('nombre_cia', $emp[0]['nombre_cia']);
		$tpl->assign('fecha', $fecha);

		foreach ($prestamos as $p) {
			$tpl->newBlock('row');
			$tpl->assign('num_emp', $p['num_emp']);
			$tpl->assign('nombre_emp', $p['nombre_emp']);

			$saldo = $p['saldo'] + ($p['tipo_mov'] == 'f' ? -$p['importe'] : $p['importe']);

			$tpl->assign('saldo', $saldo != 0 ? number_format($saldo, 2) : '&nbsp;');
			$tpl->assign('tipo', $p['tipo']);
			$tpl->assign('importe', number_format($p['importe'], 2));

			$resta = $saldo + ($p['tipo_mov'] == 'f' ? $p['importe'] : -$p['importe']);

			$tpl->assign('resta', $resta != 0 ? number_format($resta, 2) : '&nbsp;');
		}

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		if(!$mail->Send()) {
			//echo $mail->ErrorInfo;
		}
	}


	header("location: ./pan_rev_dat.php");
	die;
}

/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/

$tpl->newBlock("cias");

if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 57, 42))) {
	$tmp = $db->query("SELECT nombre_operadora FROM catalogo_operadoras WHERE iduser = $_SESSION[iduser]");
	$usuario = $tmp[0]['nombre_operadora'];
}
else
	$usuario = "ADMINISTRADOR";

$tpl->assign('usuario', $usuario);

$sql = "SELECT num_cia, nombre_corto AS nombre, fecha FROM efectivos_tmp LEFT JOIN catalogo_companias USING (num_cia) WHERE ts_aut IS NULL";
if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20)))
	$sql .= " AND num_cia IN (SELECT num_cia FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser])";
$sql .= " GROUP BY num_cia, nombre_corto, fecha ORDER BY num_cia, fecha";
$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	foreach ($result as $cia) {
		if ($num_cia != $cia['num_cia']) {
			if ($num_cia != NULL)
				$tpl->newBlock('void');

			$num_cia = $cia['num_cia'];
			$cont = 0;
		}
		$tpl->newBlock('cia');
		$tpl->assign('opt', "$cia[num_cia]|$cia[fecha]");
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign("nombre", $cia['nombre']);
		$tpl->assign('fecha', $cia['fecha']);

		// Desglosar la fecha del archivo
		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $cia['fecha'], $tmp1);
		// Obtener la ultima fecha capturada en el sistema
		$last_date = $db->query("SELECT fecha FROM total_panaderias WHERE num_cia = $cia[num_cia] AND fecha < '$cia[fecha]' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE' ORDER BY fecha DESC LIMIT 1");
		preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $last_date[0]['fecha'], $tmp2);
		$ts_db = mktime(0, 0, 0, $tmp2[2], $tmp2[1], $tmp2[3]);
		$ts_lim = mktime(0, 0, 0, $tmp1[2], $tmp1[1] - 1, $tmp1[3]);

		$tpl->assign('disabled', $last_date && ($cont > 0 || $ts_db != $ts_lim) ? ' disabled' : '');

		$avio = $db->query("SELECT * FROM mov_inv_tmp WHERE num_cia = {$cia['num_cia']} AND fecha = '{$cia['fecha']}' LIMIT 1");

		if (($last_date && $cont == 0 && $ts_db == $ts_lim) && $avio[0]['ts_aut'] == '')
		{
			$tpl->assign('bgcolor', ' style="background-color:#FFD7D6"');
			$tpl->assign('disabled', ' disabled="disabled"');
		}

		// [02-Dic-2009] Bloquear si hay notas de pastel pendientes
		/*if ($cont == 0) {
			$sql = "SELECT id FROM venta_pastel WHERE num_cia = $cia[num_cia] AND tipo = 0 AND estado = 0 AND fecha_entrega < '$cia[fecha]' ORDER BY fecha_entrega LIMIT 1";
			$pendientes = $db->query($sql);

			$sql = "SELECT id FROM catalogo_expendios WHERE num_cia = $cia[num_cia] AND notas = 'TRUE' LIMIT 1";
			$notas = $db->query($sql);

			if ($pendientes && !$notas) {
				$tpl->assign('disabled', ' disabled');
				$tpl->assign('bgcolor', ' bgcolor="#FFFF66"');
			}
		}*/

		$cont++;
	}
}
else
	$tpl->newBlock("no_cias");

$tpl->printToScreen();
?>
