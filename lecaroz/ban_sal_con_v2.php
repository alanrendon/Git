 <?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

if (isset($_REQUEST['check']))
{
	if (/*$_SESSION['iduser'] == 48*/FALSE)
	{
		echo 1;
	}

	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_con_v2.tpl");
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

	$sql = '
		SELECT
			idcontador
				AS
					id,
			nombre_contador
				AS
					nombre
		FROM
			catalogo_contadores
		ORDER BY
			nombre
	';
	$contas = $db->query($sql);

	foreach ($contas as $c) {
		$tpl->newBlock('conta');
		$tpl->assign('id', $c['id']);
		$tpl->assign('nombre', $c['nombre']);
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

preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha2, $temp);
$dia = intval($temp[1], 10);
$mes = intval($temp[2], 10);
$anio = intval($temp[3], 10);

$dias_x_mes = array(
	1 => 31,
	2 => $anio % 4 == 0 ? 29 : 28,
	3 => 31,
	4 => 30,
	5 => 31,
	6 => 30,
	7 => 31,
	8 => 31,
	9 => 30,
	10 => 31,
	11 => 30,
	12 => 31
);

$gt_saldo_bancos = 0;
$gt_saldo_libros = 0;
$gt_pendientes = 0;
$gt_pendientes_cta = 0;
$gt_saldo_pro = 0;
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

// [23/06/2006] SOLO USUARIOS DE OFICINA
if ($_SESSION['tipo_usuario'] == 1) {
	/******************** PANADERIAS *******************/
	// Datos de compañías y saldos
	if ($_GET['cuenta'] > 0)
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, saldo_libros, saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia < 300 AND cuenta = $_GET[cuenta]" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " ORDER BY num_cia ASC");
	else
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, sum(saldo_libros) AS saldo_libros, sum(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia < 300" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC");

	if ($cia) {
		/*
		@ [03-Feb-2010] Obtener cheques a cuenta para sumar al saldo en libros
		*/
		$sql = "SELECT num_cia, sum(ec.importe) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta,fecha) WHERE num_cia < 300 AND cuenta = $_GET[cuenta] AND fecha_con IS NULL AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'TRUE'" . ($_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : '') . " GROUP BY num_cia ORDER BY num_cia";
		$r_acuenta = $db->query($sql);

		// Cheques pendientes
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE num_cia < 300 AND cuenta = $_GET[cuenta] AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE num_cia < 300 AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		$r_pendientes = $db->query($sql);
		// Cheques pendientes a cuenta
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE num_cia < 300 AND cuenta = $_GET[cuenta] AND fecha_con IS NULL AND (ec.concepto LIKE '%CTA%' OR ec.concepto LIKE '%CUENTA%')/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE num_cia < 300 AND fecha_con IS NULL AND (ec.concepto LIKE '%CTA%' OR ec.concepto LIKE '%CUENTA%')/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		$r_pendientes_cta = $db->query($sql);

		// [04-Oct-2016] Gastos en reserva
		$sql = "SELECT num_cia, SUM(importe) AS importe FROM reserva_gastos WHERE/* anio = {$anio} AND*/ num_cia < 300 GROUP BY num_cia ORDER BY num_cia";
		$r_reserva_gastos = $db->query($sql);

		// Saldo de Proveedores
		$r_saldo_pro = $db->query("SELECT num_cia, sum(total) AS importe FROM pasivo_proveedores WHERE num_cia < 300 AND total > 0 AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014')  GROUP BY num_cia ORDER BY num_cia");
		// Ultima Factura
		$sql = "SELECT num_cia, min(id) AS id, min(fecha) AS fecha FROM pasivo_proveedores WHERE num_cia < 300 AND num_proveedor NOT IN (15, 792) AND total > 0 AND (num_cia, fecha) IN";
		$sql .= " (SELECT num_cia, min(fecha) FROM pasivo_proveedores WHERE total > 0 AND num_proveedor NOT IN (15, 792) AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014') GROUP BY num_cia) GROUP BY num_cia ORDER BY num_cia";
		$r_ultima_fac = $db->query($sql);
		// Perdidas
		$r_perdidas = $db->query("SELECT num_cia, monto FROM perdidas WHERE num_cia < 300");
		// Devoluciones de IVA
		/*if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE num_cia < 100 AND cuenta = $_GET[cuenta] AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2'";
			$sql .= " GROUP BY num_cia ORDER BY num_cia";
		}
		else*/
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE num_cia < 300 AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia";
		$r_dev_iva = $db->query($sql);
		// Otros Depósitos
		$r_otros_dep = $db->query("SELECT num_cia, sum(importe) AS importe FROM otros_depositos WHERE num_cia < 300 AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia");
		// Promedio de Efectivo
		$sql = "SELECT num_cia, avg(efectivo) AS prom FROM total_panaderias WHERE num_cia < 300 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE' GROUP BY num_cia ORDER BY num_cia";
		$r_prom_efe = $db->query($sql);
		// Promedio de Efectivo sin otros depositos
		$sql = "SELECT num_cia, avg(efectivo - COALESCE((SELECT SUM(importe) FROM otros_depositos WHERE num_cia = tp.num_cia AND fecha = tp.fecha AND importe > 0 AND comprobante IS NOT NULL), 0)) AS prom FROM total_panaderias tp WHERE num_cia < 300 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE' GROUP BY num_cia ORDER BY num_cia";
		$r_prom_efe_sin = $db->query($sql);
		// [11-Ene-2007] Costo de inventario
		// [26-Feb-2014] Obtener tambien costo de materia prima utilizada
		$sql = "SELECT num_cia, inv_act, mat_prima_utilizada FROM balances_pan WHERE mes = " . date('n', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " AND anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " ORDER BY num_cia";
		$r_inv = $db->query($sql);
		// [28-Mayo-2008] Gastos y Nominas
		$sql = "SELECT num_cia, sum(importe) AS importe FROM movimiento_gastos WHERE num_cia < 300 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 134";
		$sql .= " GROUP BY num_cia ORDER BY num_cia";
		$r_gn = $db->query($sql);

		$t_saldo_bancos = 0;
		$t_saldo_libros = 0;
		$t_pendientes = 0;
		$t_pendientes_cta = 0;
		$t_saldo_pro = 0;
		$t_dev_iva = 0;
		$t_otros_dep = 0;
		$t_inv = 0;
		$t_gn = 0;

		$leyenda = "Panaderias";
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
				$tpl->assign("listado.leyenda", $leyenda);
				$tpl->assign('listado.title', 'Devoluciones<br />de I.V.A.');
				$tpl->assign('salto', $_GET['admin'] > 0 ? '<br />' : '<br style="page-break-after:always;">');
				$tpl->assign('listado.leyenda_saldo_pro', 'Proveedores');

				$numfilas = 0;
			}
			// Buscar los datos para la compañía
			//$acuenta = buscar($cia[$i]['num_cia'], "importe", $r_acuenta);
			$pendientes = buscar($cia[$i]['num_cia'], "importe", $r_pendientes);
			$pendientes_cta = buscar($cia[$i]['num_cia'], "importe", $r_pendientes_cta) + buscar($cia[$i]['num_cia'], "importe", $r_reserva_gastos);
			$reserva_gastos = buscar($cia[$i]['num_cia'], "importe", $r_reserva_gastos);
			$saldo_pro = buscar($cia[$i]['num_cia'], "importe", $r_saldo_pro);
			$ultima_fac = buscarFac($cia[$i]['num_cia']);
			$perdidas = buscar($cia[$i]['num_cia'], "monto", $r_perdidas);
			$dev_iva = buscar($cia[$i]['num_cia'], "importe", $r_dev_iva);
			$prom_efe = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe);
			$prom_efe_sin = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe_sin);
			$otros_dep = buscar($cia[$i]['num_cia'], "importe", $r_otros_dep);
			$inv = buscar($cia[$i]['num_cia'], 'inv_act', $r_inv);
			$consumo = buscar($cia[$i]['num_cia'], 'mat_prima_utilizada', $r_inv);
			$gn = buscar($cia[$i]['num_cia'], 'importe', $r_gn);

			if ($prom_efe <= 0) {
				$sql = "SELECT avg(efectivo) AS prom FROM total_panaderias tp WHERE num_cia = {$cia[$i]['num_cia']} AND fecha BETWEEN '$fecha1'::date - interval '1 month' AND '$fecha1'::date - interval '1 day' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
				$tmp = $db->query($sql);
				$prom_efe = $tmp[0]['prom'] > 0 ? $tmp[0]['prom'] : 0;

				$sql = "SELECT avg(efectivo - COALESCE((SELECT SUM(importe) FROM otros_depositos WHERE num_cia = tp.num_cia AND fecha = tp.fecha AND importe > 0 AND comprobante IS NOT NULL), 0)) AS prom FROM total_panaderias tp WHERE num_cia = {$cia[$i]['num_cia']} AND fecha BETWEEN '$fecha1'::date - interval '1 month' AND '$fecha1'::date - interval '1 day' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
				$tmp = $db->query($sql);
				$prom_efe_sin = $tmp[0]['prom'] > 0 ? $tmp[0]['prom'] : 0;
			}

			$dias = $prom_efe_sin > 0 ? floor(($saldo_pro - ($cia[$i]['saldo_libros'] - $reserva_gastos)) / $prom_efe_sin) : 0;

			if (abs(round($cia[$i]['saldo_bancos'], 2)) == 0
				&& abs(round($cia[$i]['saldo_libros'], 2)) == 0
				&& abs(round($pendientes, 2)) == 0
				&& abs(round($pendientes_cta, 2)) == 0
				&& abs(round($saldo_pro, 2)) == 0
				&& abs(round($prom_efe, 2)) == 0)
			{
				continue;
			}

			if ($_GET['cuenta'] > 0)
				$tpl->newBlock("fila");
			else
				$tpl->newBlock("fila_con");
			$tpl->assign("dia", $dia);
			$tpl->assign("mes", $mes);
			$tpl->assign("anio", $anio);
			$tpl->assign("num_cia", $cia[$i]['num_cia']);
			$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
			$tpl->assign("nombre_completo", $cia[$i]['nombre']);
			$tpl->assign("cuenta", $_GET['cuenta']);
			$tpl->assign("saldo_bancos", abs(round($cia[$i]['saldo_bancos'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_bancos'] > 0 ? "000000" : "CC0000") . "\">" . number_format($cia[$i]['saldo_bancos'], 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("saldo_libros", abs(round($cia[$i]['saldo_libros'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_libros'] > 0 ? "0000CC" : "CC0000") . "\">" . number_format($cia[$i]['saldo_libros']/* + $acuenta*/, 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("pendientes", $pendientes != 0 ? "<font color=\"#" . ($pendientes > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes, 2, ".", ",") . "</font>" : "&nbsp;");
			$tpl->assign("pendientes_cta", $pendientes_cta != 0 ? "<font color=\"#" . ($pendientes_cta > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes_cta, 2, ".", ",") . "</font>" : "&nbsp;");
			$tpl->assign("saldo_pro", $saldo_pro > 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");
			if ($ultima_fac)
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $ultima_fac['fecha'], $fecha_pago);
			else
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", date('d/m/Y'), $fecha_pago);

			$dif1 = mktime(0, 0, 0, $fecha_pago[2], $fecha_pago[1], $fecha_pago[3]);
			$dif2 = mktime(0, 0, 0, $mes, $dia, $anio);

			$dif = ($dif2 - $dif1) / 86400;

			$tpl->assign("id", $ultima_fac ? $ultima_fac['id'] : "");
			$tpl->assign("ultima_fac", $ultima_fac ? "<font color='#" . ($dif > 90 ? "CC0000" : "0000CC") . "'>" . $ultima_fac['fecha'] . "</font>" : "&nbsp;");
			$tpl->assign('inv', $inv > 0 && date('d') >= 5 ? number_format($inv, 2, '.', ',') : '&nbsp;');

			$consumo_promedio = $consumo / $dias_x_mes[$mes];

			$dias_consumo = $consumo_promedio > 0 ? round($inv / $consumo_promedio) : 0;

			$tpl->assign('dias_consumo', $dias_consumo > 0 && date('d') >= 5 ? number_format($dias_consumo, 0, '.', ',') : '&nbsp;');

			$tpl->assign("perdidas", $perdidas ? number_format($perdidas, 2, ".", ",") : "&nbsp;");
			$tpl->assign("dev_iva", $dev_iva ? number_format($dev_iva, 2, ".", ",") : "&nbsp;");
			$tpl->assign("prom_efe", $prom_efe > 0 ? number_format($prom_efe, 2, ".", ",") : "&nbsp;");
			$tpl->assign("otros_dep", $otros_dep > 0 ? number_format($otros_dep, 2, ".", ",") : "&nbsp");
			$tpl->assign('nom', $gn > 0 ? number_format($gn, 2, '.', ',') : '&nbsp;');
			$tpl->assign("dias", $dias > 0 ? $dias : "&nbsp;");

			$t_saldo_bancos += $cia[$i]['saldo_bancos'];
			$t_saldo_libros += $cia[$i]['saldo_libros'];
			$t_pendientes += $pendientes;
			$t_pendientes_cta += $pendientes_cta;
			$t_saldo_pro += $saldo_pro;
			$t_dev_iva += $dev_iva;
			$t_otros_dep += $otros_dep;
			$t_inv += $inv;
			$t_gn += $gn;

			$numfilas++;
		}
		// Totales
		$tpl->newBlock("total");
		$tpl->assign("saldo_bancos", number_format($t_saldo_bancos, 2, ".", ","));
		$tpl->assign("saldo_libros", number_format($t_saldo_libros, 2, ".", ","));
		$tpl->assign("pendientes", number_format($t_pendientes, 2, ".", ","));
		$tpl->assign("pendientes_cta", number_format($t_pendientes_cta, 2, ".", ","));
		$tpl->assign("saldo_pro", number_format($t_saldo_pro, 2, ".", ","));
		$tpl->assign("dev_iva", number_format($t_dev_iva, 2, ".", ","));
		$tpl->assign("otros_dep", number_format($t_otros_dep, 2, ".", ","));
		$tpl->assign("inv", number_format($t_inv, 2, '.', ','));
		$tpl->assign("nom", number_format($t_gn, 2, '.', ','));

		$gt_saldo_bancos += $t_saldo_bancos;
		$gt_saldo_libros += $t_saldo_libros;
		$gt_pendientes += $t_pendientes;
		$gt_pendientes_cta += $t_pendientes_cta;
		$gt_saldo_pro += $t_saldo_pro;
		$gt_dev_iva += $t_dev_iva;
		$gt_otros_dep += $t_otros_dep;
		$gt_inv += $t_inv;
		$gt_gn += $t_gn;

		$sql = "
			SELECT
				num_cia,
				SUM(total)
					AS importe,
				COALESCE((
					SELECT
						SUM(saldo_libros)
					FROM
						saldos
					WHERE
						num_cia = pp.num_cia
						" . ($_REQUEST['cuenta'] > 0 ? "AND cuenta = {$_REQUEST['cuenta']}" : '') . "
				), 0)
					AS saldo,
				CASE
					WHEN fecha_solicitud IS NOT NULL AND fecha_aclaracion IS NULL THEN
						3
					WHEN copia_fac = TRUE THEN
						2
					ELSE
						1
				END
					AS status
			FROM
				pasivo_proveedores pp
				LEFT JOIN facturas_pendientes pen
					USING (num_proveedor, num_fact)
			WHERE
				num_cia <= 300
				AND total > 0
				AND (num_proveedor, num_fact) NOT IN (
					SELECT
						num_proveedor,
						num_fact
					FROM
						pasivo_proveedores
					WHERE
						num_proveedor = 283
						AND fecha < '01/01/2014'
				)
			GROUP BY
				num_cia,
				status
			ORDER BY
				num_cia,
				status DESC
		";

		$status_pasivo = $db->query($sql);

		if ($status_pasivo)
		{
			$por_aclarar = 0;
			$factura_completa = 0;
			$sin_copia = 0;
			$factura_sin_saldo = 0;
			$factura_con_saldo = 0;

			$num_cia = NULL;

			foreach ($status_pasivo as $status)
			{
				if ($num_cia != $status['num_cia'])
				{
					$num_cia = $status['num_cia'];
				}

				if ($status['status'] == 3)
				{
					$por_aclarar += $status['importe'];
				}
				else if ($status['status'] == 2)
				{
					$factura_completa += $status['importe'];

					$factura_con_saldo_cia = $status['saldo'] - $status['importe'] >= 0 ? $status['importe'] : $status['saldo'];
					$factura_sin_saldo_cia = $status['importe'] - $factura_con_saldo_cia;

					$factura_sin_saldo += $factura_sin_saldo_cia;
					$factura_con_saldo += $factura_con_saldo_cia;
				}
				else if ($status['status'] == 1)
				{
					$sin_copia += $status['importe'];
				}
			}

			$tpl->newBlock('pendientes');

			$tpl->assign('status_pasivo_3', number_format($por_aclarar, 2));
			$tpl->assign('status_pasivo_2', number_format($factura_completa, 2));
			$tpl->assign('status_pasivo_1', number_format($sin_copia, 2));
			$tpl->assign('factura_sin_saldo', number_format($factura_sin_saldo, 2));
			$tpl->assign('factura_con_saldo', number_format($factura_con_saldo, 2));
		}
	}

	/******************** ROSTICERIAS *******************/
	// Datos de compañías y saldos
	if ($_GET['cuenta'] > 0)
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, saldo_libros, saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cuenta = $_GET[cuenta]" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " ORDER BY num_cia ASC");
	else
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, sum(saldo_libros) AS saldo_libros, sum(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799)" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC");

	if ($cia) {
		/*
		@ [03-Feb-2010] Obtener cheques a cuenta para sumar al saldo en libros
		*/
		$sql = "SELECT num_cia, sum(ec.importe) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cuenta = $_GET[cuenta] AND fecha_con IS NULL AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'TRUE'" . ($_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : '') . " GROUP BY num_cia ORDER BY num_cia";
		$r_acuenta = $db->query($sql);

		// Cheques pendientes
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cuenta = $_GET[cuenta] AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		$r_pendientes = $db->query($sql);
		// Cheques pendientes a cuenta
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cuenta = $_GET[cuenta] AND fecha_con IS NULL AND (ec.concepto LIKE '%CTA%' OR ec.concepto LIKE '%CUENTA%')/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha_con IS NULL AND (ec.concepto LIKE '%CTA%' OR ec.concepto LIKE '%CUENTA%')/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		$r_pendientes_cta = $db->query($sql);

		// [04-Oct-2016] Gastos en reserva
		$sql = "SELECT num_cia, SUM(importe) AS importe FROM reserva_gastos WHERE/* anio = {$anio} AND*/ (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) GROUP BY num_cia ORDER BY num_cia";
		$r_reserva_gastos = $db->query($sql);

		// Saldo de Proveedores
		$r_saldo_pro = $db->query("SELECT num_cia, sum(total) AS importe FROM pasivo_proveedores WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND total > 0 GROUP BY num_cia ORDER BY num_cia");
		// Ultima Factura
		$sql = "SELECT num_cia, min(id) AS id, min(fecha) AS fecha FROM pasivo_proveedores WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND total > 0 AND num_proveedor NOT IN (15, 792) AND (num_cia, fecha) IN";
		$sql .= " (SELECT num_cia, min(fecha) FROM pasivo_proveedores WHERE total > 0 AND num_proveedor NOT IN (15, 792) GROUP BY num_cia) GROUP BY num_cia ORDER BY num_cia";
		$r_ultima_fac = $db->query($sql);
		// Perdidas
		$r_perdidas = $db->query("SELECT num_cia, monto FROM perdidas WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799)");
		// Devoluciones de IVA
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cuenta = $_GET[cuenta] AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2'";
			$sql .= " GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia";
		$r_dev_iva = $db->query($sql);
		// Otros Depósitos
		$r_otros_dep = $db->query("SELECT num_cia, sum(importe) AS importe FROM otros_depositos WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia");
		// Promedio de Efectivo
		$sql = "SELECT num_cia, avg(efectivo) AS prom FROM total_companias WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " GROUP BY num_cia ORDER BY num_cia";
		$r_prom_efe = $db->query($sql);
		// Promedio de Efectivo sin otros depositos
		$sql = "SELECT num_cia, avg(efectivo - COALESCE((SELECT SUM(importe) FROM otros_depositos WHERE num_cia = tp.num_cia AND fecha = tp.fecha AND importe > 0 AND comprobante IS NOT NULL), 0)) AS prom FROM total_companias tp WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " GROUP BY num_cia ORDER BY num_cia";
		$r_prom_efe_sin = $db->query($sql);
		// [11-Ene-2007] Costo de inventario
		// [26-Feb-2014] Obtener tambien costo de materia prima utilizada
		$sql = "SELECT num_cia, inv_act, mat_prima_utilizada FROM balances_ros WHERE mes = " . date('n', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " AND anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " ORDER BY num_cia";
		$r_inv = $db->query($sql);
		// [28-Mayo-2008] Gastos y Nominas
			$sql = "SELECT num_cia, sum(importe) AS importe FROM movimiento_gastos WHERE (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799) AND fecha BETWEEN
	'$fecha1' AND '$fecha2' AND codgastos = 134";
			$sql .= " GROUP BY num_cia ORDER BY num_cia";
			$r_gn = $db->query($sql);


		$t_saldo_bancos = 0;
		$t_saldo_libros = 0;
		$t_pendientes = 0;
		$t_pendientes_cta = 0;
		$t_saldo_pro = 0;
		$t_dev_iva = 0;
		$t_otros_dep = 0;
		$t_inv = 0;
		$t_gn = 0;

		$leyenda = "Rosticerías";
		$numfilas = $numfilas_x_hoja;
		for ($i=0; $i<count($cia); $i++) {
			if ($numfilas >= $numfilas_x_hoja) {
				$tpl->newBlock("listado");
				$tpl->assign("leyenda", $leyenda);
				$tpl->assign("hora", date('h:ia'));
				$tpl->assign('listado.title', 'Devoluciones<br />de I.V.A.');
				$tpl->assign('salto', $_GET['admin'] > 0 ? '<br />' : '<br style="page-break-after:always;">');
				$tpl->assign('leyenda_saldo_pro', 'Proveedores');

				$numfilas = 0;
			}
			// Buscar los datos para la compañía
			//$acuenta = buscar($cia[$i]['num_cia'], "importe", $r_acuenta);
			$pendientes = buscar($cia[$i]['num_cia'], "importe", $r_pendientes);
			$pendientes_cta = buscar($cia[$i]['num_cia'], "importe", $r_pendientes_cta) + buscar($cia[$i]['num_cia'], "importe", $r_reserva_gastos);
			$reserva_gastos = buscar($cia[$i]['num_cia'], "importe", $r_reserva_gastos);
			$saldo_pro = buscar($cia[$i]['num_cia'], "importe", $r_saldo_pro);
			$ultima_fac = buscarFac($cia[$i]['num_cia']);
			$perdidas = buscar($cia[$i]['num_cia'], "monto", $r_perdidas);
			$dev_iva = buscar($cia[$i]['num_cia'], "importe", $r_dev_iva);
			$prom_efe = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe);
			$prom_efe_sin = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe_sin);
			$otros_dep = buscar($cia[$i]['num_cia'], "importe", $r_otros_dep);
			$inv = buscar($cia[$i]['num_cia'], 'inv_act', $r_inv);
			$consumo = buscar($cia[$i]['num_cia'], 'mat_prima_utilizada', $r_inv);
			$gn = buscar($cia[$i]['num_cia'], 'importe', $r_gn);

			if ($prom_efe <= 0) {
				$sql = "SELECT avg(efectivo) AS prom FROM total_companias tp WHERE num_cia = {$cia[$i]['num_cia']} AND fecha BETWEEN '$fecha1'::date - interval '1 month' AND '$fecha1'::date - interval '1 day'";
				$tmp = $db->query($sql);
				$prom_efe = $tmp[0]['prom'] > 0 ? $tmp[0]['prom'] : 0;

				$sql = "SELECT avg(efectivo - COALESCE((SELECT SUM(importe) FROM otros_depositos WHERE num_cia = tp.num_cia AND fecha = tp.fecha AND importe > 0 AND comprobante IS NOT NULL), 0)) AS prom FROM total_companias tp WHERE num_cia = {$cia[$i]['num_cia']} AND fecha BETWEEN '$fecha1'::date - interval '1 month' AND '$fecha1'::date - interval '1 day'";
				$tmp = $db->query($sql);
				$prom_efe_sin = $tmp[0]['prom'] > 0 ? $tmp[0]['prom'] : 0;
			}

			$dias = $prom_efe_sin > 0 ? floor(($saldo_pro - ($cia[$i]['saldo_libros'] - $reserva_gastos)) / $prom_efe_sin) : 0;

			if (abs(round($cia[$i]['saldo_bancos'], 2)) == 0
				&& abs(round($cia[$i]['saldo_libros'], 2)) == 0
				&& abs(round($pendientes, 2)) == 0
				&& abs(round($pendientes_cta, 2)) == 0
				&& abs(round($saldo_pro, 2)) == 0
				&& abs(round($prom_efe, 2)) == 0)
			{
				continue;
			}

			if ($_GET['cuenta'] > 0)
				$tpl->newBlock("fila");
			else
				$tpl->newBlock("fila_con");
			$tpl->assign("dia", $dia);
			$tpl->assign("mes", $mes);
			$tpl->assign("anio", $anio);
			$tpl->assign("num_cia", $cia[$i]['num_cia']);
			$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
			$tpl->assign("nombre_completo", $cia[$i]['nombre']);
			$tpl->assign("cuenta", $_GET['cuenta']);
			$tpl->assign("saldo_bancos", abs(round($cia[$i]['saldo_bancos'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_bancos'] > 0 ? "000000" : "CC0000") . "\">" . number_format($cia[$i]['saldo_bancos'], 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("saldo_libros", abs(round($cia[$i]['saldo_bancos'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_libros'] > 0 ? "0000CC" : "CC0000") . "\">" . number_format($cia[$i]['saldo_libros']/* + $acuenta*/, 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("pendientes", $pendientes != 0 ? "<font color=\"#" . ($pendientes > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes, 2, ".", ",") . "</font>" : "&nbsp;");
			$tpl->assign("pendientes_cta", $pendientes_cta != 0 ? "<font color=\"#" . ($pendientes_cta > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes_cta, 2, ".", ",") . "</font>" : "&nbsp;");
			$tpl->assign("saldo_pro", $saldo_pro > 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");
			if ($ultima_fac)
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $ultima_fac['fecha'], $fecha_pago);
			else
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", date('d/m/Y'), $fecha_pago);

			$dif1 = mktime(0, 0, 0, $fecha_pago[2], $fecha_pago[1], $fecha_pago[3]);
			$dif2 = mktime(0, 0, 0, $mes, $dia, $anio);

			$dif = ($dif2 - $dif1) / 86400;

			$tpl->assign("id", $ultima_fac ? $ultima_fac['id'] : "");
			$tpl->assign("ultima_fac", $ultima_fac ? "<font color='#" . ($dif > 90 ? "CC0000" : "0000CC") . "'>" . $ultima_fac['fecha'] . "</font>" : "&nbsp;");
			$tpl->assign('inv', $inv > 0 && date('d') >= 5 ? number_format($inv, 2, '.', ',') : '&nbsp;');

			$consumo_promedio = $consumo / $dias_x_mes[$mes];

			$dias_consumo = $consumo_promedio > 0 ? round($inv / $consumo_promedio) : 0;

			$tpl->assign('dias_consumo', $dias_consumo > 0 && date('d') >= 5 ? number_format($dias_consumo, 0, '.', ',') : '&nbsp;');

			$tpl->assign("perdidas", $perdidas ? number_format($perdidas, 2, ".", ",") : "&nbsp;");
			$tpl->assign("dev_iva", $dev_iva ? number_format($dev_iva, 2, ".", ",") : "&nbsp;");
			$tpl->assign("prom_efe", $prom_efe > 0 ? number_format($prom_efe, 2, ".", ",") : "&nbsp;");
			$tpl->assign("otros_dep", $otros_dep > 0 ? number_format($otros_dep, 2, ".", ",") : "&nbsp");
			$tpl->assign('nom', $gn > 0 ? number_format($gn, 2, '.', ',') : '&nbsp;');
			$tpl->assign("dias", $dias > 0 ? $dias : "&nbsp;");

			$t_saldo_bancos += $cia[$i]['saldo_bancos'];
			$t_saldo_libros += $cia[$i]['saldo_libros'];
			$t_pendientes += $pendientes;
			$t_pendientes_cta += $pendientes_cta;
			$t_saldo_pro += $saldo_pro;
			$t_dev_iva += $dev_iva;
			$t_otros_dep += $otros_dep;
			$t_inv += $inv;
			$t_gn += $gn;

			$numfilas++;
		}
		// Totales
		$tpl->newBlock("total");
		$tpl->assign("saldo_bancos", number_format($t_saldo_bancos, 2, ".", ","));
		$tpl->assign("saldo_libros", number_format($t_saldo_libros, 2, ".", ","));
		$tpl->assign("pendientes", number_format($t_pendientes, 2, ".", ","));
		$tpl->assign("pendientes_cta", number_format($t_pendientes_cta, 2, ".", ","));
		$tpl->assign("saldo_pro", number_format($t_saldo_pro, 2, ".", ","));
		$tpl->assign("dev_iva", number_format($t_dev_iva, 2, ".", ","));
		$tpl->assign("otros_dep", number_format($t_otros_dep, 2, ".", ","));
		$tpl->assign("inv", number_format($t_inv, 2, '.', ','));
		$tpl->assign("nom", number_format($t_gn, 2, '.', ','));

		$gt_saldo_bancos += $t_saldo_bancos;
		$gt_saldo_libros += $t_saldo_libros;
		$gt_pendientes += $t_pendientes;
		$gt_pendientes_cta += $t_pendientes_cta;
		$gt_saldo_pro += $t_saldo_pro;
		$gt_dev_iva += $t_dev_iva;
		$gt_otros_dep += $t_otros_dep;
		$gt_inv += $t_inv;
		$gt_gn += $t_gn;

		$sql = "
			SELECT
				num_cia,
				SUM(total)
					AS importe,
				COALESCE((
					SELECT
						SUM(saldo_libros)
					FROM
						saldos
					WHERE
						num_cia = pp.num_cia
						" . ($_REQUEST['cuenta'] > 0 ? "AND cuenta = {$_REQUEST['cuenta']}" : '') . "
				), 0)
					AS saldo,
				CASE
					WHEN fecha_solicitud IS NOT NULL AND fecha_aclaracion IS NULL THEN
						3
					WHEN copia_fac = TRUE THEN
						2
					ELSE
						1
				END
					AS status
			FROM
				pasivo_proveedores pp
				LEFT JOIN facturas_pendientes pen
					USING (num_proveedor, num_fact)
			WHERE
				(num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 701 AND 799)
				AND total > 0
				AND (num_proveedor, num_fact) NOT IN (
					SELECT
						num_proveedor,
						num_fact
					FROM
						pasivo_proveedores
					WHERE
						num_proveedor = 283
						AND fecha < '01/01/2014'
				)
			GROUP BY
				num_cia,
				status
			ORDER BY
				num_cia,
				status DESC
		";

		$status_pasivo = $db->query($sql);

		if ($status_pasivo)
		{
			$por_aclarar = 0;
			$factura_completa = 0;
			$sin_copia = 0;
			$factura_sin_saldo = 0;
			$factura_con_saldo = 0;

			$num_cia = NULL;

			foreach ($status_pasivo as $status)
			{
				if ($num_cia != $status['num_cia'])
				{
					$num_cia = $status['num_cia'];
				}

				if ($status['status'] == 3)
				{
					$por_aclarar += $status['importe'];
				}
				else if ($status['status'] == 2)
				{
					$factura_completa += $status['importe'];

					$factura_con_saldo_cia = $status['saldo'] - $status['importe'] >= 0 ? $status['importe'] : $status['saldo'];
					$factura_sin_saldo_cia = $status['importe'] - $factura_con_saldo_cia;

					$factura_sin_saldo += $factura_sin_saldo_cia;
					$factura_con_saldo += $factura_con_saldo_cia;
				}
				else if ($status['status'] == 1)
				{
					$sin_copia += $status['importe'];
				}
			}

			$tpl->newBlock('pendientes');

			$tpl->assign('status_pasivo_3', number_format($por_aclarar, 2));
			$tpl->assign('status_pasivo_2', number_format($factura_completa, 2));
			$tpl->assign('status_pasivo_1', number_format($sin_copia, 2));
			$tpl->assign('factura_sin_saldo', number_format($factura_sin_saldo, 2));
			$tpl->assign('factura_con_saldo', number_format($factura_con_saldo, 2));
		}
	}

	/******************** INMOBILIARIAS *******************/
	// Datos de compañías y saldos
	if ($_GET['cuenta'] > 0)
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, saldo_libros, saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND cuenta = $_GET[cuenta]" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " ORDER BY num_cia ASC");
	else
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, sum(saldo_libros) AS saldo_libros, sum(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899)" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . ($_GET['conta'] > 0 ? " AND idcontador = $_GET[conta]" : '') . " GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC");

	if ($cia) {
		// Cheques pendientes
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND cuenta = $_GET[cuenta] AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN ec.importe ELSE -ec.importe END) AS importe FROM estado_cuenta ec LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha) WHERE (num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND ec.cod_mov IN (5, 41) AND acuenta = 'FALSE'*/ GROUP BY num_cia ORDER BY num_cia";
		$r_pendientes = $db->query($sql);
		// Saldo de Proveedores
		$r_saldo_pro = $db->query("SELECT num_cia, sum(total) AS importe FROM pasivo_proveedores WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND total > 0 AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014') GROUP BY num_cia ORDER BY num_cia");
		// Ultima Factura
		$sql = "SELECT num_cia, min(id) AS id, min(fecha) AS fecha FROM pasivo_proveedores WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND total > 0 AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014') AND (num_cia, fecha) IN";
		$sql .= " (SELECT num_cia, min(fecha) FROM pasivo_proveedores WHERE total > 0 GROUP BY num_cia) GROUP BY num_cia ORDER BY num_cia";
		$r_ultima_fac = $db->query($sql);
		// Perdidas
		$r_perdidas = $db->query("SELECT num_cia, monto FROM perdidas WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899)");
		// Devoluciones de IVA
		if ($_GET['cuenta'] > 0) {
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND cuenta = $_GET[cuenta] AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2'";
			$sql .= " GROUP BY num_cia ORDER BY num_cia";
		}
		else
			$sql = "SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND cod_mov = 18 AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia";
		$r_dev_iva = $db->query($sql);
		// Otros Depósitos
		$r_otros_dep = $db->query("SELECT num_cia, sum(importe) AS importe FROM otros_depositos WHERE (/*num_cia BETWEEN 400 AND 550 OR */num_cia BETWEEN 600 AND 700 OR num_cia BETWEEN 800 AND 899) AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia");

		$t_saldo_bancos = 0;
		$t_saldo_libros = 0;
		$t_pendientes = 0;
		$t_saldo_pro = 0;
		$t_dev_iva = 0;
		$t_otros_dep = 0;
		$t_gn = 0;

		$totales_inm = array();

		$leyenda = "Inmobiliarias";
		$numfilas = $numfilas_x_hoja;
		for ($i=0; $i<count($cia); $i++) {
			if ($numfilas >= $numfilas_x_hoja) {
				$tpl->newBlock("listado");
				$tpl->assign("leyenda", $leyenda);
				$tpl->assign("hora", date('h:ia'));
				$tpl->assign('listado.title', 'Devoluciones<br />de I.V.A.');
				$tpl->assign('salto', $_GET['admin'] > 0 ? '<br />' : '<br style="page-break-after:always;">');
				$tpl->assign('leyenda_saldo_pro', 'Proveedores');

				$numfilas = 0;
			}
			// Buscar los datos para la compañía
			$pendientes = buscar($cia[$i]['num_cia'], "importe", $r_pendientes);
			$saldo_pro = buscar($cia[$i]['num_cia'], "importe", $r_saldo_pro);
			$ultima_fac = buscarFac($cia[$i]['num_cia']);
			$perdidas = buscar($cia[$i]['num_cia'], "monto", $r_perdidas);
			$dev_iva = buscar($cia[$i]['num_cia'], "importe", $r_dev_iva);
			$prom_efe = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe);
			$otros_dep = buscar($cia[$i]['num_cia'], "importe", $r_otros_dep);

			$dias = $prom_efe > 0 ? floor(($saldo_pro - $cia[$i]['saldo_libros']) / $prom_efe) : 0;

			if (abs(round($cia[$i]['saldo_bancos'], 2)) == 0
				&& abs(round($cia[$i]['saldo_libros'], 2)) == 0
				&& abs(round($pendientes, 2)) == 0
				// && abs(round($pendientes_cta, 2)) == 0
				&& abs(round($saldo_pro, 2)) == 0
				&& abs(round($prom_efe, 2)) == 0)
			{
				continue;
			}

			if ($_GET['cuenta'] > 0)
				$tpl->newBlock("fila");
			else
				$tpl->newBlock("fila_con");
			$tpl->assign("dia", $dia);
			$tpl->assign("mes", $mes);
			$tpl->assign("anio", $anio);
			$tpl->assign("num_cia", $cia[$i]['num_cia']);
			$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
			$tpl->assign("nombre_completo", $cia[$i]['nombre']);
			$tpl->assign("cuenta", $_GET['cuenta']);
			$tpl->assign("saldo_bancos", abs(round($cia[$i]['saldo_bancos'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_bancos'] > 0 ? "000000" : "CC0000") . "\">" . number_format($cia[$i]['saldo_bancos'], 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("saldo_libros", abs(round($cia[$i]['saldo_libros'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_libros'] > 0 ? "0000CC" : "CC0000") . "\">" . number_format($cia[$i]['saldo_libros'], 2, ".", ",") . "</font>" : '&nbsp;');
			$tpl->assign("pendientes", $pendientes != 0 ? "<font color=\"#" . ($pendientes > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes, 2, ".", ",") . "</font>" : "&nbsp;");
			$tpl->assign("saldo_pro", $saldo_pro > 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");
			if ($ultima_fac)
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $ultima_fac['fecha'], $fecha_pago);
			else
				preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", date('d/m/Y'), $fecha_pago);

			$dif1 = mktime(0, 0, 0, $fecha_pago[2], $fecha_pago[1], $fecha_pago[3]);
			$dif2 = mktime(0, 0, 0, $mes, $dia, $anio);

			$dif = ($dif2 - $dif1) / 86400;

			$tpl->assign("id", $ultima_fac ? $ultima_fac['id'] : "");
			$tpl->assign("ultima_fac", $ultima_fac ? "<font color='#" . ($dif > 90 ? "CC0000" : "0000CC") . "'>" . $ultima_fac['fecha'] . "</font>" : "&nbsp;");
			$tpl->assign("perdidas", $perdidas ? number_format($perdidas, 2, ".", ",") : "&nbsp;");
			$tpl->assign("dev_iva", $dev_iva ? number_format($dev_iva, 2, ".", ",") : "&nbsp;");
			$tpl->assign("prom_efe", "&nbsp;");
			$tpl->assign("otros_dep", $otros_dep > 0 ? number_format($otros_dep, 2, ".", ",") : "&nbsp");
			$tpl->assign("dias", $dias > 0 ? $dias : "&nbsp;");

			$t_saldo_bancos += $cia[$i]['saldo_bancos'];
			$t_saldo_libros += $cia[$i]['saldo_libros'];
			$t_pendientes += $pendientes;
			$t_saldo_pro += $saldo_pro;
			$t_dev_iva += $dev_iva;
			$t_otros_dep += $otros_dep;

			$numfilas++;

			if (in_array($cia[$i]['num_cia'], array(626, 627, 633, 636))) {
				$totales_inm[] = array(
					'num_cia' => $cia[$i]['num_cia'],
					'nombre_cia' => $cia[$i]['nombre_corto'],
					'saldo_libros' => $cia[$i]['saldo_libros'],
					'saldo_bancos' => $cia[$i]['saldo_bancos']
				);
			}
		}
		// Totales
		$tpl->newBlock("total");
		$tpl->assign("saldo_bancos", number_format($t_saldo_bancos, 2, ".", ","));
		$tpl->assign("saldo_libros", number_format($t_saldo_libros, 2, ".", ","));
		$tpl->assign("pendientes", number_format($t_pendientes, 2, ".", ","));
		$tpl->assign("saldo_pro", number_format($t_saldo_pro, 2, ".", ","));
		$tpl->assign("dev_iva", number_format($t_dev_iva, 2, ".", ","));
		$tpl->assign("otros_dep", number_format($t_otros_dep, 2, ".", ","));
		$tpl->assign("nom", number_format($t_gn, 2, '.', ','));

		$gt_saldo_bancos += $t_saldo_bancos;
		$gt_saldo_libros += $t_saldo_libros;
		$gt_pendientes += $t_pendientes;
		$gt_saldo_pro += $t_saldo_pro;
		$gt_dev_iva += $t_dev_iva;
		$gt_otros_dep += $t_otros_dep;
		$gt_gn += $t_gn;
	}
}

// [23/06/2006] SOLO USUARIOS DE ZAPATERIAS
if ($_SESSION['tipo_usuario'] == 2 || $_SESSION['iduser'] == 1) {
	// Datos de compañías y saldos
	if ($_GET['cuenta'] > 0)
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, saldo_libros, saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia BETWEEN 900 AND 998 AND cuenta = $_GET[cuenta] ORDER BY num_cia ASC");
	else
		$cia = $db->query("SELECT num_cia, nombre_corto, nombre, sum(saldo_libros) AS saldo_libros, sum(saldo_bancos) AS saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE num_cia BETWEEN 900 AND 998 GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC");
	// Cheques pendientes
	if ($_GET['cuenta'] > 0) {
		$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN importe ELSE -importe END) AS importe FROM estado_cuenta WHERE num_cia BETWEEN 900 AND 998 AND cuenta = $_GET[cuenta] AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND cod_mov IN (5, 41)*/";
		$sql .= " GROUP BY num_cia ORDER BY num_cia";
	}
	else
		$sql = "SELECT num_cia, sum(CASE WHEN tipo_mov = TRUE THEN importe ELSE -importe END) AS importe FROM estado_cuenta WHERE num_cia BETWEEN 900 AND 998 AND fecha_con IS NULL/* AND tipo_mov = 'TRUE' AND cod_mov IN (5, 41)*/ GROUP BY num_cia ORDER BY num_cia";
	$r_pendientes = $db->query($sql);
	// Saldo de Proveedores
	$r_saldo_pro = $db->query("SELECT num_cia, sum(total) AS importe FROM facturas_zap WHERE num_cia BETWEEN 900 AND 998 AND total > 0 AND folio IS NULL AND sucursal <> 'TRUE' AND clave = 0 GROUP BY num_cia ORDER BY num_cia");
	// Saldo Remisiones
	$r_saldo_rem = $db->query("SELECT num_cia, sum(total) AS importe FROM facturas_zap WHERE num_cia BETWEEN 900 AND 998 AND total > 0 AND folio IS NULL AND sucursal <> 'TRUE' AND clave > 0 GROUP BY num_cia ORDER BY num_cia");
	// Ultima Factura
	$sql = "SELECT num_cia, min(id) AS id, min(fecha) AS fecha FROM facturas_zap WHERE num_cia BETWEEN 900 AND 998 AND total > 0 AND folio IS NULL AND sucursal <> 'TRUE' AND (num_cia, fecha) IN";
	$sql .= " (SELECT num_cia, min(fecha) FROM facturas_zap WHERE total > 0 AND folio IS NULL AND sucursal <> 'TRUE' GROUP BY num_cia) GROUP BY num_cia ORDER BY num_cia";
	$r_ultima_fac = $db->query($sql);
	// Perdidas
	$r_perdidas = $db->query("SELECT num_cia, monto FROM perdidas WHERE num_cia BETWEEN 900 AND 998");

	// [24-Sep-2007] Facturas pendientes de autorizar
	$sql = "SELECT num_cia, sum(total) AS total FROM facturas_zap WHERE tspago IS NULL AND (por_aut = 'FALSE' OR copia_fac = 'FALSE') AND sucursal <> 'TRUE' GROUP BY num_cia ORDER BY num_cia";
	$r_fac_pen = $db->query($sql);

	// Otros Depósitos
	$r_otros_dep = $db->query("SELECT num_cia, sum(importe) AS importe FROM otros_depositos WHERE num_cia BETWEEN 900 AND 998 AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia");

	// [20-Jul-2007] Promedio de efectivos
	$sql = "SELECT num_cia, avg(efectivo) AS prom FROM total_zapaterias WHERE fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia ORDER BY num_cia";
	$r_prom_efe = $db->query($sql);

	// [11-Ene-2008] Costo de inventario
	$sql = "SELECT num_cia, importe FROM inventario_zap WHERE mes = " . date('n', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " AND anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anio)) . " ORDER BY num_cia";
	$r_inv = $db->query($sql);

	$t_saldo_bancos = 0;
	$t_saldo_libros = 0;
	$t_pendientes = 0;
	$t_saldo_pro = 0;
	$t_saldo_rem = 0;
	$t_fac_pen = 0;
	$t_otros_dep = 0;
	$t_inv = 0;

	$leyenda = "Zapaterias";
	$numfilas = $numfilas_x_hoja;
	for ($i=0; $i<count($cia); $i++) {
		if ($numfilas >= $numfilas_x_hoja) {
			$tpl->newBlock("listado");
			$tpl->assign("leyenda", $leyenda);
			$tpl->assign("hora", date('h:ia'));
			$tpl->assign('listado.title', 'Facturas<br />Pendientes');
			$tpl->assign('leyenda_saldo_pro', 'Proveedores');
			$tpl->newBlock('leyenda_saldo_rem');
			$tpl->gotoBlock('listado');

			$numfilas = 0;
		}
		// Buscar los datos para la compañía
		$pendientes = buscar($cia[$i]['num_cia'], "importe", $r_pendientes);
		$saldo_pro = buscar($cia[$i]['num_cia'], "importe", $r_saldo_pro);
		$saldo_rem = buscar($cia[$i]['num_cia'], "importe", $r_saldo_rem);
		$ultima_fac = buscarFac($cia[$i]['num_cia']);
		$perdidas = buscar($cia[$i]['num_cia'], "monto", $r_perdidas);
		$prom_efe = buscar($cia[$i]['num_cia'], "prom", $r_prom_efe);
		$otros_dep = buscar($cia[$i]['num_cia'], "importe", $r_otros_dep);
		$fac_pen = buscar($cia[$i]['num_cia'], 'total', $r_fac_pen);
		$inv = buscar($cia[$i]['num_cia'], 'importe', $r_inv);

		$dias = $prom_efe > 0 ? floor(($saldo_pro - $cia[$i]['saldo_libros']) / $prom_efe) : 0;

		/*if ($_GET['cuenta'] > 0)
			$tpl->newBlock("fila");
		else
			$tpl->newBlock("fila_con");*/
		$tpl->newBlock('fila_zap');
		$tpl->assign("dia", $dia);
		$tpl->assign("mes", $mes);
		$tpl->assign("anio", $anio);
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
		$tpl->assign("nombre_completo", $cia[$i]['nombre']);
		$tpl->assign("cuenta", $_GET['cuenta']);
		$tpl->assign("saldo_bancos", abs(round($cia[$i]['saldo_bancos'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_bancos'] > 0 ? "000000" : "CC0000") . "\">" . number_format($cia[$i]['saldo_bancos'], 2, ".", ",") . "</font>" : '&nbsp;');
		$tpl->assign("saldo_libros", abs(round($cia[$i]['saldo_libros'], 2)) != 0 ? "<font color=\"#" . ($cia[$i]['saldo_libros'] > 0 ? "0000CC" : "CC0000") . "\">" . number_format($cia[$i]['saldo_libros'], 2, ".", ",") . "</font>" : '&nbsp;');
		$tpl->assign("pendientes", $pendientes != 0 ? "<font color=\"#" . ($pendientes > 0 ? "CC0000" : "0000CC") . "\">" . number_format($pendientes, 2, ".", ",") . "</font>" : "&nbsp;");
		$tpl->assign("saldo_pro", $saldo_pro > 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");
		$tpl->assign("saldo_rem", $saldo_rem > 0 ? number_format($saldo_rem, 2, ".", ",") : "&nbsp;");
		if ($ultima_fac)
			preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $ultima_fac['fecha'], $fecha_pago);
		else
			preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", date('d/m/Y'), $fecha_pago);

		$dif1 = mktime(0, 0, 0, intval($fecha_pago[2], 10), intval($fecha_pago[1], 10), intval($fecha_pago[3], 10));
		$dif2 = mktime(0, 0, 0, $mes, $dia, $anio);

		$dif = ($dif2 - $dif1) / 86400;

		$tpl->assign("id", $ultima_fac ? $ultima_fac['id'] : "");
		$tpl->assign("ultima_fac", $ultima_fac ? "<font color='#" . ($dif > 90 ? "CC0000" : "0000CC") . "'>" . $ultima_fac['fecha'] . "</font>" : "&nbsp;");
		$tpl->assign('inv', $inv > 0 ? number_format($inv, 2, '.', ',') : '&nbsp;');
		$tpl->assign("perdidas", $perdidas ? number_format($perdidas, 2, ".", ",") : "&nbsp;");
		$tpl->assign("fac_pen", $fac_pen ? number_format($fac_pen, 2, ".", ",") : "&nbsp;");
		$tpl->assign("prom_efe", $prom_efe != 0 ? number_format($prom_efe, 2, '.', ',') : '&nbsp;');
		$tpl->assign("otros_dep", $otros_dep > 0 ? number_format($otros_dep, 2, ".", ",") : "&nbsp");
		$tpl->assign("dias", $dias > 0 ? $dias : "&nbsp;");

		$t_saldo_bancos += $cia[$i]['saldo_bancos'];
		$t_saldo_libros += $cia[$i]['saldo_libros'];
		$t_pendientes += $pendientes;
		$t_saldo_pro += $saldo_pro;
		$t_saldo_rem += $saldo_rem;
		$t_fac_pen += $fac_pen;
		$t_otros_dep += $otros_dep;
		$t_inv += $inv;

		$numfilas++;
	}
	// Totales
	$tpl->newBlock("total");
	$tpl->assign("saldo_bancos", number_format($t_saldo_bancos, 2, ".", ","));
	$tpl->assign("saldo_libros", number_format($t_saldo_libros, 2, ".", ","));
	$tpl->assign("pendientes", number_format($t_pendientes, 2, ".", ","));
	$tpl->assign("saldo_pro", number_format($t_saldo_pro, 2, ".", ","));
	$tpl->assign("dev_iva", number_format($t_fac_pen, 2, ".", ","));
	$tpl->assign("prom_efe", $prom_efe > 0 ? number_format($prom_efe, 2, ".", ",") : "&nbsp;");
	$tpl->assign("otros_dep", number_format($t_otros_dep, 2, ".", ","));
	$tpl->assign("inv", number_format($t_inv, 2, ".", ","));
	$tpl->newBlock('total_saldo_rem');
	$tpl->assign("saldo_rem", number_format($t_saldo_rem, 2, ".", ","));

	$gt_saldo_bancos += $t_saldo_bancos;
	$gt_saldo_libros += $t_saldo_libros;
	$gt_pendientes += $t_pendientes;
	$gt_saldo_pro += $t_saldo_pro + $t_saldo_rem;
	$gt_otros_dep += $t_otros_dep;
	$gt_inv += $t_inv;
}

// Gran Total
$tpl->newBlock("gran_total");
$tpl->assign("gt_saldo_bancos", number_format($gt_saldo_bancos, 2, ".", ","));
$tpl->assign("gt_saldo_libros", number_format($gt_saldo_libros, 2, ".", ","));
$tpl->assign("gt_pendientes", number_format($gt_pendientes, 2, ".", ","));
$tpl->assign("gt_saldo_pro", number_format($gt_saldo_pro, 2, ".", ","));
$tpl->assign("gt_dev_iva", number_format($gt_dev_iva, 2, ".", ","));
$tpl->assign("gt_otros_dep", number_format($gt_otros_dep, 2, ".", ","));
$tpl->assign("gt_inv", number_format($gt_inv, 2, ".", ","));

if (isset($totales_inm)) {
	$tpl->newBlock('total_inm');

	$total_libros = 0;
	$total_bancos = 0;

	foreach ($totales_inm as $t) {
		$tpl->newBlock('inm');

		$tpl->assign('num_cia', $t['num_cia']);
		$tpl->assign('nombre_cia', $t['nombre_cia']);
		$tpl->assign('saldo_libros', number_format($t['saldo_libros'], 2));
		$tpl->assign('saldo_bancos', number_format($t['saldo_bancos'], 2));

		$total_libros += $t['saldo_libros'];
		$total_bancos += $t['saldo_bancos'];
	}

	$tpl->assign('total_inm.saldo_libros', number_format($total_libros, 2));
	$tpl->assign('total_inm.saldo_bancos', number_format($total_bancos, 2));
}

$tpl->newBlock("functions");

$tpl->printToScreen();
?>
