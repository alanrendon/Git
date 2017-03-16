<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Los impuestos ya han sido generados con anterioridad";
$numfilas = 25;

$users = array(28, 29, 30, 31, 32);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_imp_des.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['mes'])) {
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
	$fecha1 = "01/$mes/$anio";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes + 1, 0, $anio));
	
	if ($db->query("SELECT idmovimiento_gastos FROM movimiento_gastos WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " AND codgastos IN (179, 180, 181, 183, 187, 189, 190) AND fecha BETWEEN '$fecha1' AND '$fecha2' LIMIT 1")) {
		die(header('location: ./ban_imp_des.php?codigo_error=1'));
	}
	
	// Buscar diferencias entre el desglose de impuestos y lo que realmente ha sido pagado
	$sql = "
		SELECT
			*
		FROM
			(
				SELECT
					cf.num_cia_primaria
						AS num_cia,
					nombre_corto,
					(CASE WHEN SUM(isr_pago) < 0 THEN 0 ELSE ROUND(SUM(isr_pago)::NUMERIC, 2) END)
					+ (CASE WHEN SUM(iva_pago) < 0 THEN 0 ELSE ROUND(SUM(iva_pago)::NUMERIC, 2) END)
					+ (CASE WHEN SUM(iva_dec) < 0 THEN 0 ELSE ROUND(SUM(iva_dec)::NUMERIC, 2) END)
					+ (CASE WHEN SUM(dec_anu) < 0 THEN 0 ELSE ROUND(SUM(dec_anu)::NUMERIC, 2) END)
					/*+ (CASE WHEN SUM(isr_neto) < 0 THEN 0 ELSE ROUND(SUM(isr_neto)::NUMERIC, 2) END)*/
						AS importe
				FROM
					impuestos_federales AS if
					LEFT JOIN catalogo_filiales AS cf
						USING (num_cia)
					LEFT JOIN catalogo_companias AS cc
						ON (cc.num_cia = cf.num_cia_primaria)
				WHERE
					mes = $mes
					AND anio = $anio
					AND cf.num_cia_primaria BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
				GROUP BY
					cf.num_cia_primaria,
					nombre_corto
			) AS result
		WHERE
			importe <> 0
		
		EXCEPT
		
		SELECT
			cf.num_cia_primaria
				AS num_cia,
			nombre_corto,
			sum(importe)
				AS importe
		FROM
			estado_cuenta AS ec
			LEFT JOIN catalogo_filiales AS cf
				USING (num_cia)
			LEFT JOIN catalogo_companias AS cc
				ON (cc.num_cia = cf.num_cia_primaria)
		WHERE
			fecha BETWEEN '$fecha1' AND '$fecha2'
			AND cod_mov = 33
			AND ec.num_cia IN (
				SELECT
					num_cia
				FROM
					catalogo_filiales
				WHERE
					num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
			)
		GROUP BY
			cf.num_cia_primaria,
			nombre_corto
		ORDER BY
			num_cia
	";
	$des = $db->query($sql);
	
	// Obtener diferencias en el estado de cuenta
	$sql = "SELECT cf.num_cia_primaria AS num_cia, nombre_corto, SUM(importe) AS importe FROM estado_cuenta AS ec LEFT JOIN catalogo_filiales AS cf USING (num_cia) LEFT JOIN";
	$sql .= " catalogo_companias AS cc ON (cc.num_cia = cf.num_cia_primaria) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 33 AND ec.num_cia IN (SELECT num_cia FROM";
	$sql .= " catalogo_filiales WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . ") GROUP BY cf.num_cia_primaria, nombre_corto";
	$sql .= " EXCEPT ";
	$sql .= "SELECT cf.num_cia_primaria AS num_cia, nombre_corto, (CASE WHEN SUM(isr_pago) < 0 THEN 0 ELSE ROUND(SUM(isr_pago)::NUMERIC, 2) END)";
	$sql .= " + (CASE WHEN SUM(iva_pago) < 0 THEN 0 ELSE ROUND(SUM(iva_pago)::NUMERIC, 2) END) + (CASE WHEN SUM(iva_dec) < 0 THEN 0 ELSE ROUND(SUM(iva_dec)::NUMERIC, 2) END) + (CASE WHEN SUM(dec_anu) < 0 THEN 0 ELSE ROUND(SUM(dec_anu)::NUMERIC, 2) END)/* + (CASE WHEN SUM(isr_neto) < 0 THEN 0 ELSE ROUND(SUM(isr_neto)::NUMERIC, 2) END)*/ AS importe FROM impuestos_federales AS if";
	$sql .= " LEFT JOIN catalogo_filiales AS cf USING (num_cia) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = cf.num_cia_primaria) WHERE mes = $mes AND anio = $anio";
	$sql .= " AND cf.num_cia_primaria BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$sql .= " GROUP BY cf.num_cia_primaria, nombre_corto";

	$ec = $db->query($sql);
	
	//*** Si existen diferencias, mostrar un listado de las mismas y para el proceso de desglose de gastos de impuestos
	if ($des || $ec) {
		// Crear un arreglo nuevo a partir de los desgloses y los pagos
		$dif = array();
		// Importes desglosados
		if ($des)
			foreach ($des as $r) {
				$dif[$r['num_cia']]['nombre'] = $r['nombre_corto'];
				$dif[$r['num_cia']]['des'] = $r['importe'];
			}
		// Importes pagados
		if ($ec)
			foreach ($ec as $r) {
				$dif[$r['num_cia']]['nombre'] = $r['nombre_corto'];
				$dif[$r['num_cia']]['ec'] = $r['importe'];
			}
		
		// Mostrar un listado con las diferencias
		$tpl->newBlock("dif");
		foreach ($dif as $cia => $reg) {
			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $cia);
			$tpl->assign("nombre", $reg['nombre']);
			$cap = isset($reg['des']) ? $reg['des'] : 0;
			$ec = isset($reg['ec']) ? $reg['ec'] : 0;
			$dif = $cap - $ec;
			$tpl->assign("cap", $cap != 0 ? number_format($cap, 2, ".", ",") : "&nbsp;");
			$tpl->assign("pagado", $ec != 0 ? number_format($ec, 2, ".", ",") : "&nbsp;");
			$tpl->assign("dif", number_format($dif, 2, ".", ","));
		}
		$tpl->printToScreen();
		die;
	}
	
	//*** Si no hubo diferencias, proceder con el desglose de los gastos
	
	// Obtener compañías matriz que pagaron impuestos
	$sql = "
		SELECT
			*
		FROM
			(
				SELECT
					cf.num_cia_primaria
						AS num_cia,
					nombre_corto,
					CASE
						WHEN SUM(ieps) < 0 THEN
							0
						ELSE
							ROUND(SUM(ieps)::NUMERIC, 2)
					END
						AS ieps,
					CASE
						WHEN SUM(isr_pago) < 0 THEN
							0
						ELSE
							ROUND(SUM(isr_pago)::NUMERIC, 2)
					END
						AS isr_pago,
					CASE
						WHEN SUM(iva_pago) < 0 THEN
							0
						ELSE
							ROUND(SUM(iva_pago)::NUMERIC)
					END
						AS iva_pago,
					CASE
						WHEN SUM(iva_dec) < 0 THEN
							0
						ELSE
							ROUND(SUM(iva_dec)::NUMERIC, 2)
					END
						AS iva_dec,
					CASE
						WHEN SUM(dec_anu) < 0 THEN
							0
						ELSE
							ROUND(SUM(dec_anu)::NUMERIC, 2)
					END
						AS dec_anu,
					CASE
						WHEN SUM(isr_neto) < 0 THEN
							0
						ELSE
							ROUND(SUM(isr_neto)::NUMERIC, 2)
					END
						AS isr_neto,
					/*(CASE WHEN SUM(ieps) < 0 THEN 0 ELSE SUM(ieps) END) + */
					(CASE
						WHEN SUM(isr_pago) < 0 THEN
							0
						ELSE
							ROUND(SUM(isr_pago)::NUMERIC, 2)
					END) + (CASE
						WHEN SUM(iva_pago) < 0 THEN
							0
						ELSE
							ROUND(SUM(iva_pago)::NUMERIC, 2)
					END) + (CASE
						WHEN SUM(iva_dec) < 0 THEN
							0
						ELSE
							ROUND(SUM(iva_dec)::NUMERIC, 2)
					END) + (CASE
						WHEN SUM(dec_anu) < 0 THEN
							0
						ELSE
							ROUND(SUM(dec_anu)::NUMERIC, 2)
					END)/* + (CASE WHEN SUM(isr_neto) < 0 THEN 0 ELSE SUM(isr_neto) END)*/
						AS importe
				FROM
					impuestos_federales AS if
					LEFT JOIN catalogo_filiales AS cf
						USING (num_cia)
					LEFT JOIN catalogo_companias AS cc
						ON (cc.num_cia = cf.num_cia_primaria)
				WHERE
					anio = {$anio}
					AND mes = {$mes}
					AND cf.num_cia_primaria BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
				GROUP BY
					cf.num_cia_primaria,
					nombre_corto
			) AS impuestos
		WHERE
			importe > 0
		ORDER BY
			num_cia
	";

	$result = $db->query($sql);
	// impuestos pagados por cada matriz
	foreach ($result as $reg) {
		$imp[$reg['num_cia']]['nombre'] = $reg['nombre_corto'];
		$imp[$reg['num_cia']]['ieps'] = $reg['ieps'];
		$imp[$reg['num_cia']]['isr_pago'] = $reg['isr_pago'];
		$imp[$reg['num_cia']]['iva_pago'] = $reg['iva_pago'];
		$imp[$reg['num_cia']]['iva_dec'] = $reg['iva_dec'];
		$imp[$reg['num_cia']]['dec_anu'] = $reg['dec_anu'];
		// $imp[$reg['num_cia']]['isr_neto'] = $reg['isr_neto'];
		// Contiene las matrices que pagaron
		$matriz[] = $reg['num_cia'];
	}
	
	// Obtener todos los impuestos del mes
	$sql = "SELECT num_cia_primaria AS matriz, num_cia AS filial, ieps, isr_pago, iva_pago, iva_dec, dec_anu, isr_neto FROM impuestos_federales LEFT JOIN catalogo_filiales USING (num_cia) WHERE";
	$sql .= " mes = $mes AND anio = $anio AND num_cia_primaria IN (";
	foreach ($matriz as $i => $cia)
		$sql .= $cia . ($i < count($matriz) - 1 ? ", " : "");
	$sql .= ") AND num_cia_primaria BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899') . " ORDER BY matriz, first DESC, filial";
	$result = $db->query($sql);
	
	// Recorrer impuestos para luego alojarlos en un arreglo con los desgloses que se insertaran en la tabla de gastos
	$data = array();
	$cont = 0;
	$matriz = NULL;
	foreach ($result as $reg) {
		if ($matriz != $reg['matriz'])
			$matriz = $reg['matriz'];
		// IMPAC => IETU
		// if ($imp[$matriz]['ietu'] > 0 && $reg['ietu'] > 0) {
		// 	$data[$cont]['num_cia'] = $reg['filial'];
		// 	$data[$cont]['codgastos'] = /*183*/189;	// [22-May-2008] Cambiado IMPAC => IETU código 189
		// 	$data[$cont]['fecha'] = $fecha1;
		// 	$data[$cont]['importe'] = $reg['ietu'];
		// 	$data[$cont]['captura'] = "TRUE";
		// 	$data[$cont]['concepto'] = "IETU";	// [22-May-2008] Cambiado IMPAC => IETU código 189
		// 	$cont++;
		// }
		// IEPS
		// if ($imp[$matriz]['ieps'] > 0 && $reg['ieps'] > 0) {
		// 	$data[$cont]['num_cia'] = $reg['filial'];
		// 	$data[$cont]['codgastos'] = 223;
		// 	$data[$cont]['fecha'] = $fecha1;
		// 	$data[$cont]['importe'] = $reg['ieps'];
		// 	$data[$cont]['captura'] = "TRUE";
		// 	$data[$cont]['concepto'] = "IEPS";
		// 	$cont++;
		// }
		// ISR
		// [01-Oct-2008] Ya no se genera gasto de ISR
		// [04-Abr-2014] Restaurado a partir de marzo de 2014
		if ($imp[$matriz]['isr_pago'] > 0 && $reg['isr_pago'] > 0) {
			$data[$cont]['num_cia'] = $reg['filial'];
			$data[$cont]['codgastos'] = 179;
			$data[$cont]['fecha'] = $fecha1;
			$data[$cont]['importe'] = $reg['isr_pago'];
			$data[$cont]['captura'] = "TRUE";
			$data[$cont]['concepto'] = "ISR A PAGAR";
			$cont++;
		}
		// IVA Retenido
		if ($imp[$matriz]['iva_pago'] > 0 && $reg['iva_pago'] > 0) {
			$data[$cont]['num_cia'] = $reg['filial'];
			$data[$cont]['codgastos'] = 180;
			$data[$cont]['fecha'] = $fecha1;
			$data[$cont]['importe'] = $reg['iva_pago'];
			$data[$cont]['captura'] = "TRUE";
			$data[$cont]['concepto'] = "IVA RETENIDO";
			$cont++;
		}
		// IVA Declarado
		if ($imp[$matriz]['iva_dec'] > 0 && $reg['iva_dec'] > 0) {
			$data[$cont]['num_cia'] = $reg['filial'];
			$data[$cont]['codgastos'] = 181;
			$data[$cont]['fecha'] = $fecha1;
			$data[$cont]['importe'] = $reg['iva_dec'];
			$data[$cont]['captura'] = "TRUE";
			$data[$cont]['concepto'] = "IVA DECLARADO";
			$cont++;
		}
		// [03-Abr-2007] Declaración Anual
		if ($imp[$matriz]['dec_anu'] > 0 && $reg['dec_anu'] > 0) {
			$data[$cont]['num_cia'] = $reg['filial'];
			$data[$cont]['codgastos'] = 187;
			$data[$cont]['fecha'] = $fecha1;
			$data[$cont]['importe'] = $reg['dec_anu'];
			$data[$cont]['captura'] = "TRUE";
			$data[$cont]['concepto'] = "DECLARACION ANUAL";
			$cont++;
		}
		// [01-Sep-2008] ISR neto a cargo
		// [04-Abr-2014] Ya no se generara este gasto a partir de marzo de 2014
		// if ($imp[$matriz]['isr_neto'] > 0 && $reg['isr_neto'] > 0) {
		// 	$data[$cont]['num_cia'] = $reg['filial'];
		// 	$data[$cont]['codgastos'] = 190;
		// 	$data[$cont]['fecha'] = $fecha1;
		// 	$data[$cont]['importe'] = $reg['isr_neto'];
		// 	$data[$cont]['captura'] = "TRUE";
		// 	$data[$cont]['concepto'] = "ISR NETO A CARGO";
		// 	$cont++;
		// }
	}
	// Insertar movimientos de gastos
	$sql = $db->multiple_insert("movimiento_gastos", $data);//echo '<pre>' . print_r($sql, TRUE) . '</pre>';die;
	$db->query($sql);
	
	$sql = "SELECT num_cia, nombre_corto, concepto, importe FROM movimiento_gastos AS mg LEFT JOIN catalogo_filiales AS cf USING (num_cia) LEFT JOIN catalogo_companias AS cc USING";
	$sql .= " (num_cia) WHERE fecha = '$fecha1' AND codgastos IN (179, 180, 181, 183, 187, 190) AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800') . " ORDER BY cf.num_cia_primaria, first DESC, num_cia";
	$result = $db->query($sql);
	
	$tpl->newBlock("list");
	$tpl->assign("mes", mes_escrito($mes));
	$tpl->assign("anio", $anio);
	foreach ($result as $reg) {
		$tpl->newBlock("gasto");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_corto']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
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