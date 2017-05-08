<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$tpl = new TemplatePower('plantillas/bal/HistoricoBalances.tpl');
$tpl->prepare();

if (isset($_GET['anio'])) {
	$anio = $_GET['anio'];
	$idadmin = $_GET['idadmin'];
	$cias = implode(', ', array_filter($_GET['num_cia']));
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $anio));
	
	$sql = '
		SELECT
			num_cia,
			nombre,
			anio,
			mes,
			venta_puerta,
			bases,
			barredura,
			pastillaje,
			abono_emp,
			otros,
			total_otros,
			abono_reparto,
			errores,
			ventas_netas,
			inv_ant,
			compras,
			mercancias,
			inv_act,
			mat_prima_utilizada,
			mano_obra,
			panaderos,
			gastos_fab,
			costo_produccion,
			utilidad_bruta,
			pan_comprado,
			gastos_generales,
			gastos_caja,
			comisiones,
			reserva_aguinaldos
				AS
					reservas,
			gastos_otras_cias,
			total_gastos,
			ingresos_ext,
			utilidad_neta,
			mp_vtas,
			utilidad_pro,
			mp_pro,
			gas_pro,
			produccion_total,
			ganancia,
			porc_ganancia,
			faltante_pan,
			devoluciones,
			rezago_ini,
			rezago_fin,
			var_rezago,
			efectivo
		FROM
			balances_pan
				LEFT JOIN
					catalogo_companias
						USING
							(
								num_cia
							)
		WHERE
				anio = ' . $anio . '
	';
	$sql .= $idadmin > 0 ? '
			AND
				idadministrador = ' . $idamin : '';
	$sql .= trim($cias) != '' ? '
			AND
				num_cia
					IN
						(
							' . $cias . '
						)' : '';
	$sql .= '
		ORDER BY
			mes
	';
	$balances = $db->query();
	
	if (!$result)
		die(header('location: HistoricoBalances.php'));
	
	$Campos = array(
	);
	
	foreach ($balances as $bal) {
		if () {
			$tpl->newBlock('BalancePan');
			
			$tpl->
		}
		
	}
}
?>
