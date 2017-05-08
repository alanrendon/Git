<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_con_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener datos
if (!isset($_GET['cuenta'])) {
	$tpl->newBlock("datos");

	$sql = '
		SELECT
			idadministrador
				AS
					id,
			nombre_administrador
				AS
					nombre
		FROM
			catalogo_administradores
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);

	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}

	$tpl->printToSCreen();
	die;
}

// Fechas de consulta
if (date("d") > 6) {
	$fecha1 = date("1/m/Y");
	$fecha2 = date("d/m/Y");
}
else {
	$fecha1 = date("d/m/Y", mktime(0, 0, 0, date("n") - 1, 1, date("Y")));
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, date("n"), 0, date("Y")));
}

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha2, $temp);
$dia = intval($temp[1], 10);
$mes = intval($temp[2], 10);
$anio = intval($temp[3], 10);

$gt_saldo_bancos = 0;
$gt_saldo_libros = 0;
$gt_pendientes = 0;
$gt_saldo_pro = 0;
$gt_libros_pro = 0;
$gt_dev_iva = 0;
$gt_otros_dep = 0;
$gt_inv = 0;
$gt_gn = 0;

$numfilas_x_hoja = 200;

/******************** FUNCIONES *******************/
// Busca un registro en un arreglo
function buscar($num_cia, $campo, $array) {
	if (!$array)
		return FALSE;

	foreach ($array as $value)
		if ($value['num_cia'] == $num_cia)
			return $value[$campo];

	return FALSE;
}

function buscarFac($num_cia) {
	global $r_ultima_fac;

	if (!$r_ultima_fac)
		return FALSE;

	foreach ($r_ultima_fac as $fac)
		if ($fac['num_cia'] == $num_cia)
			return $fac;

	return FALSE;
}

// [14-Ene-2010] Actualizar bandera de 'acuenta' a FALSE para los registros en NULL
$db->query('UPDATE cheques SET acuenta = \'FALSE\' WHERE acuenta IS NULL');

	// Datos de compañías y saldos
	if ($_GET['cuenta'] > 0)
		$cia = $db->query("SELECT num_cia_saldos AS num_cia, SUM(saldo_libros) AS saldo_libros, SUM(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia < 900 AND cuenta = $_GET[cuenta]" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . " GROUP BY num_cia_saldos ORDER BY num_cia_saldos ASC");
	else
		$cia = $db->query("SELECT num_cia_saldos AS num_cia, sum(saldo_libros) AS saldo_libros, sum(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia < 900" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . " GROUP BY num_cia_saldos ORDER BY num_cia_saldos ASC");

	if ($cia) {
		/*
		@ [03-Feb-2010] Obtener cheques a cuenta para sumar al saldo en libros
		*/
		$sql = "SELECT num_cia_saldos AS num_cia, sum(ec.importe) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia < 900 AND fecha_con IS NULL AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'TRUE'" . ($_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : '') . " GROUP BY num_cia_saldos ORDER BY num_cia_saldos";
		$r_acuenta = $db->query($sql);

		// Cheques pendientes
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia_saldos AS num_cia, sum(ec.importe) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia < 900 AND cuenta = $_GET[cuenta] AND fecha_con IS NULL AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE' GROUP BY num_cia_saldos ORDER BY num_cia_saldos";
		}
		else
			$sql = "SELECT num_cia_saldos AS num_cia, sum(ec.importe) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia < 900 AND fecha_con IS NULL AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE' GROUP BY num_cia_saldos ORDER BY num_cia_saldos";
		$r_pendientes = $db->query($sql);
		// Saldo de Proveedores
		$r_saldo_pro = $db->query("SELECT num_cia_saldos AS num_cia, sum(total) AS importe FROM pasivo_proveedores LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 900 AND total > 0 GROUP BY num_cia_saldos ORDER BY num_cia_saldos");
		// Perdidas
		$r_perdidas = $db->query("SELECT num_cia_saldos AS num_cia, SUM(monto) AS monto FROM perdidas LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 900 GROUP BY num_cia_saldos ORDER BY num_cia_saldos");
		// Devoluciones de IVA
		$sql = "SELECT num_cia_saldos AS num_cia, sum(importe) AS importe FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 900 AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY num_cia_saldos ORDER BY num_cia_saldos";
		$r_dev_iva = $db->query($sql);
		// [11-Ene-2007] Costo de inventario
		$sql = "SELECT num_cia, SUM(inv_act) AS inv_act FROM (SELECT num_cia_saldos AS num_cia, SUM(inv_act) AS inv_act FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE mes = " . date('n', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " AND anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " GROUP BY num_cia_saldos UNION SELECT num_cia_saldos AS num_cia, SUM(inv_act) AS inv_act FROM balances_ros LEFT JOIN catalogo_companias USING (num_cia) WHERE mes = " . date('n', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " AND anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " GROUP BY num_cia_saldos) result GROUP BY num_cia ORDER BY num_cia";
		$r_inv = $db->query($sql);

		$t_saldo_bancos = 0;
		$t_saldo_libros = 0;
		$t_pendientes = 0;
		$t_saldo_pro = 0;
		$t_libros_pro = 0;
		$t_dev_iva = 0;
		$t_inv = 0;

		$numfilas = $numfilas_x_hoja;
		for ($i=0; $i<count($cia); $i++) {
			if ($numfilas >= $numfilas_x_hoja) {
				$tpl->newBlock("listado");
				$tpl->newBlock("encabezado");
				$tpl->assign("dia", /*$dia*/date('d'));
				$tpl->assign("mes", mes_escrito(/*$mes*/date('n')));
				$tpl->assign("anio", /*$anio*/date('Y'));
				$tpl->assign("hora", date('h:ia'));
				$tpl->assign("banco", $_GET['cuenta'] > 0 ? ($_GET['cuenta'] == 1 ? "Banorte" : "Santander") : "Consolidado");
				$tpl->assign('salto', $_GET['admin'] > 0 ? '<br />' : '<br style="page-break-after:always;">');

				$numfilas = 0;
			}
			// Buscar los datos para la compañía
			$acuenta = buscar($cia[$i]['num_cia'], "importe", $r_acuenta);
			$pendientes = buscar($cia[$i]['num_cia'], "importe", $r_pendientes);
			$saldo_pro = buscar($cia[$i]['num_cia'], "importe", $r_saldo_pro);
			$perdidas = buscar($cia[$i]['num_cia'], "monto", $r_perdidas);
			$dev_iva = buscar($cia[$i]['num_cia'], "importe", $r_dev_iva);
			$inv = buscar($cia[$i]['num_cia'], 'inv_act', $r_inv);

			if ($_GET['cuenta'] > 0)
				$tpl->newBlock("fila");
			else
				$tpl->newBlock("fila_con");
			$tpl->assign("dia", $dia);
			$tpl->assign("mes", $mes);
			$tpl->assign("anio", $anio);
			$tpl->assign("num_cia", $cia[$i]['num_cia']);
			$nombre_cia = $db->query('SELECT nombre FROM catalogo_companias WHERE num_cia = ' . $cia[$i]['num_cia']);
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
			$tpl->assign("cuenta", $_GET['cuenta']);
			$tpl->assign("saldo_bancos", "<font color=\"#" . ($cia[$i]['saldo_bancos'] > 0 ? "000000" : "CC0000") . "\">" . number_format($cia[$i]['saldo_bancos'], 2, ".", ",") . "</font>");
			$tpl->assign("saldo_libros", "<font color=\"#" . ($cia[$i]['saldo_libros'] > 0 ? "0000CC" : "CC0000") . "\">" . number_format($cia[$i]['saldo_libros'] + $acuenta, 2, ".", ",") . "</font>");
			$tpl->assign("pendientes", $pendientes > 0 ? number_format($pendientes, 2, ".", ",") : "&nbsp;");
			$tpl->assign("saldo_pro", $saldo_pro > 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");

			$libros_pro = $saldo_pro > 0 && $cia[$i]['saldo_libros'] > 0 ? $cia[$i]['saldo_libros'] - $saldo_pro : 0;

			$tpl->assign("libros_pro", $libros_pro != 0 ? '<span style="color:#' . ($libros_pro > 0 ? '00C' : 'C00') . ';">' . number_format($libros_pro, 2, ".", ",") . '</span>' : "&nbsp;");

			$tpl->assign('inv', $inv > 0 && date('d') > 5 ? number_format($inv, 2, '.', ',') : '&nbsp;');
			$tpl->assign("perdidas", $perdidas ? number_format($perdidas, 2, ".", ",") : "&nbsp;");
			$tpl->assign("dev_iva", $dev_iva ? number_format($dev_iva, 2, ".", ",") : "&nbsp;");

			$t_saldo_bancos += $cia[$i]['saldo_bancos'];
			$t_saldo_libros += $cia[$i]['saldo_libros'];
			$t_pendientes += $pendientes;
			$t_saldo_pro += $saldo_pro;
			$t_libros_pro += $libros_pro;
			$t_dev_iva += $dev_iva;
			$t_inv += $inv;

			$numfilas++;
		}
		// Totales
		$tpl->newBlock("total");
		$tpl->assign("saldo_bancos", number_format($t_saldo_bancos, 2, ".", ","));
		$tpl->assign("saldo_libros", number_format($t_saldo_libros, 2, ".", ","));
		$tpl->assign("pendientes", number_format($t_pendientes, 2, ".", ","));
		$tpl->assign("saldo_pro", number_format($t_saldo_pro, 2, ".", ","));
		$tpl->assign("libros_pro", number_format($t_libros_pro, 2, ".", ","));
		$tpl->assign("dev_iva", number_format($t_dev_iva, 2, ".", ","));
		$tpl->assign("inv", number_format($t_inv, 2, '.', ','));

		$gt_saldo_bancos += $t_saldo_bancos;
		$gt_saldo_libros += $t_saldo_libros;
		$gt_pendientes += $t_pendientes;
		$gt_saldo_pro += $t_saldo_pro;
		$gt_libros_pro += $t_libros_pro;
		$gt_dev_iva += $t_dev_iva;
		$gt_inv += $t_inv;
	}

// Gran Total
$tpl->newBlock("gran_total");
$tpl->assign("gt_saldo_bancos", number_format($gt_saldo_bancos, 2, ".", ","));
$tpl->assign("gt_saldo_libros", number_format($gt_saldo_libros, 2, ".", ","));
$tpl->assign("gt_pendientes", number_format($gt_pendientes, 2, ".", ","));
$tpl->assign("gt_saldo_pro", number_format($gt_saldo_pro, 2, ".", ","));
$tpl->assign("gt_libros_pro", number_format($gt_libros_pro, 2, ".", ","));
$tpl->assign("gt_dev_iva", number_format($gt_dev_iva, 2, ".", ","));
$tpl->assign("gt_inv", number_format($gt_inv, 2, ".", ","));

$tpl->newBlock("functions");

$tpl->printToScreen();
?>
