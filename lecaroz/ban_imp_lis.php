<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_imp_lis.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$condiciones = array();

	$condiciones[] = "anio = {$_GET['anio']}";

	$condiciones[] = "mes = {$_GET['mes']}";

	if ($_GET['num_cia'] > 0)
	{
		$condiciones[] = (isset($_GET['fil']) ? 'cf.num_cia_primaria' : 'num_cia') . " = {$_GET['num_cia']}";
	}

	$sql = "SELECT
		if.num_cia,
		COALESCE(cf.num_cia_primaria, if.num_cia) AS pri,
		nombre AS nombre_corto,
		if.isr,
		--if.ietu,
		--if.ieps,
		if.ieps_gravado,
		if.ieps_excento,
		if.ret_isr_ren,
		if.ret_isr_hon,
		if.ret_hon_con,
		if.cre_sal,
		if.isr_pago,
		if.ret_iva_hon,
		if.ret_iva_ren,
		if.ret_iva_fle,
		if.iva_pago,
		if.iva_tras,
		if.iva_acre,
		if.iva_dec,
		COALESCE((
			SELECT
				SUM(iva_dec)
			FROM
				impuestos_federales
			WHERE
				num_cia = if.num_cia
				AND anio = {$_GET['anio']}
				AND mes <= {$_GET['mes']}
		), 0) AS acu_anual
	FROM
		impuestos_federales AS if
		LEFT JOIN catalogo_filiales AS cf USING (num_cia)
		LEFT JOIN catalogo_companias AS cc USING (num_cia)
	WHERE
		" . implode(' AND ', $condiciones) . "
	ORDER BY
		pri,
		cf.first DESC,
		if.num_cia";

	$result = $db->query($sql);

	if (!$result) {
		header("location: ./ban_imp_lis.php?codigo_error=1");
		die;
	}

	$gtotal = array('isr' => 0,
					// 'ietu' => 0,
					// 'ieps' => 0,
					'ieps_gravado' => 0,
					'ieps_excento' => 0,
					'ret_isr_ren' => 0,
					'ret_isr_hon' => 0,
					'ret_hon_con' => 0,
					'cre_sal' => 0,
					'isr_pago' => 0,
					'ret_iva_hon' => 0,
					'ret_iva_ren' => 0,
					'ret_iva_fle' => 0,
					'iva_pago' => 0,
					'iva_tras' => 0,
					'iva_acre' => 0,
					'iva_dec' => 0,
					'acu_anual' => 0);
	$pri = NULL;
	$numfilas = 43;
	$filas = $numfilas + 1;
	foreach ($result as $reg) {
		// Si llega al maximo numero de lineas, crear una hoja nueva
		if ($filas > $numfilas) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			$filas = 1;
		}
		// Crear bloque al cambiar grupo de filiales
		if ($pri != $reg['pri']) {
			// Totales del ultimo bloque
			if ($pri != NULL) {
				// Si los totales no caben al final de la hoja, crear una nueva
				if ($filas + 1 > $numfilas) {
					$tpl->newBlock("listado");
					$tpl->assign("mes", mes_escrito($_GET['mes']));
					$tpl->assign("anio", $_GET['anio']);
					$tpl->newBlock("bloque");
					$filas = 1;
				}

				$tpl->newBlock("totales");
				foreach ($total as $tag => $value)
					$tpl->assign($tag, number_format($value, 2, ".", ","));
				$filas += 2;
			}

			if ($filas > $numfilas) {
				$tpl->newBlock("listado");
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				$filas = 1;
			}

			$pri = $reg['pri'];

			$tpl->newBlock("bloque");
			$total = array('isr' => 0,
							// 'ietu' => 0,
							// 'ieps' => 0,
							'ieps_gravado' => 0,
							'ieps_excento' => 0,
							'ret_isr_ren' => 0,
							'ret_isr_hon' => 0,
							'ret_hon_con' => 0,
							'cre_sal' => 0,
							'isr_pago' => 0,
							'ret_iva_hon' => 0,
							'ret_iva_ren' => 0,
							'ret_iva_fle' => 0,
							'iva_pago' => 0,
							'iva_tras' => 0,
							'iva_acre' => 0,
							'iva_dec' => 0,
							'acu_anual' => 0);
		}
		$tpl->newBlock("fila");
		$tpl->assign("fila", $filas);

		foreach ($reg as $tag => $value)
			if ($tag != 'pri') {
				$tpl->assign($tag, in_array($tag, array('num_cia', 'nombre_corto')) ? $value : ($value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;"));
				if (!in_array($tag, array('num_cia', 'nombre_corto'))) {
					$total[$tag] += $value;
					$gtotal[$tag] += $value;
				}
			}
		$filas++;
	}
	// Totales del ultimo bloque
	if ($pri != NULL) {
		if ($filas + 2 > $numfilas) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			$tpl->newBlock("bloque");
			$filas = 0;
		}

		$tpl->newBlock("totales");
		foreach ($total as $tag => $value)
			$tpl->assign("$tag", number_format($value, 2, ".", ","));
	}
	// Totales generales
	$tpl->newBlock("gtotales");
	foreach ($gtotal as $tag => $value) {
		$tpl->assign("$tag", number_format($value, 2, ".", ","));
	}

	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
