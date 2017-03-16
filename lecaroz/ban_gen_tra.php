<?php
// LISTADO DE ESTADOS DE CUENTA
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31, 32);

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gen_tra.tpl");
$tpl->prepare();

if (isset($_GET['gen'])) {
	$pros = array();
	foreach ($_GET['num_pro'] as $pro)
		if ($pro > 0)
			$pros[] = $pro;
	
	$sql = "SELECT te.folio, te.importe, tipo, sucursal, plaza_banxico, clave, cp.idbanco, cp.cuenta AS cuenta_pro, cp.clabe AS clabe_pro, clabe_cuenta2 AS cuenta_cia, cp.nombre AS beneficiario, cp.referencia, cc.nombre AS nombre_cia FROM";
	$sql .= " transferencias_electronicas AS te LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_bancos";
	$sql .= " USING (idbanco) WHERE te.status = 0 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899');
	if (count($pros) > 0) { 
		$sql .= " AND te.num_proveedor IN (";
		foreach ($pros as $i => $pro) 
			$sql .= $pro . ($i < count($pros) - 1 ? ", " : ")");
	}
	//$sql .= $_GET['num_pro'] > 0 ? " AND te.num_proveedor = $_GET[num_pro]" : "";
	$sql .= " ORDER BY /*te.num_proveedor*/beneficiario, te.num_cia, te.folio";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock("no_result");
		$tpl->printToScreen();
		die;
	}
	
	function filler($str, $length, $chr, $side = TRUE) {
		$tmp = "";
		
		for ($i = 0; $i < $length - strlen($str); $i++)
			$tmp .= $chr;
		
		return $side ? $str . $tmp : $tmp . $str;
	}
	
	$tmp = $db->query("SELECT folio_archivo FROM transferencias_electronicas WHERE folio_archivo > 0 ORDER BY folio_archivo DESC LIMIT 1");
	$folio = $tmp ? $tmp[0]['folio_archivo'] + 1 : 3;
	
	// Número de registros por archivo
	$num_reg_x_archivo = 150;
	
	$int = "";
	$ext = "";
	$date = date("dmY");
	// Construir cadenas para archivos
	$index_int = 0;
	$index_ext = 0;
	$cont_int = $num_reg_x_archivo;
	$cont_ext = $num_reg_x_archivo;
	foreach ($result as $reg) {
		$concepto = $reg['referencia'] > 0 ? filler(strtoupper(trim(substr("REF $reg[referencia]  $reg[nombre_cia]", 0, 30))), 30, ' ') : filler("$reg[folio] PAGO PROVEEDOR", 30, " ");
		
		if (/*$reg['tipo'] == "f"*/$reg['idbanco'] == 9) {
			if ($cont_int >= $num_reg_x_archivo) {
				$index_int++;
				$int[$index_int] = "";
				$cont_int = 0;
			}
			
			$int[$index_int] .= "$reg[cuenta_cia]     $reg[cuenta_pro]     " . filler(number_format($reg['importe'], 2, ".", ""), 13, " ", FALSE) . $concepto . "          $date\r\n";
			$cont_int++;
		}
		else {
			if ($cont_ext >= $num_reg_x_archivo) {
				$index_ext++;
				$ext[$index_ext] = "";
				$cont_ext = 0;
			}
			
			$ext[$index_ext] .= "$reg[cuenta_cia]     $reg[clabe_pro]  " . filler($reg['clave'], 5, " ") . filler(substr($reg['beneficiario'], 0, 40), 40, " ");
			$ext[$index_ext] .= $reg['sucursal'] . filler(number_format($reg['importe'], 2, "", ""), 15, "0", FALSE) . $reg['plaza_banxico'] . $concepto . filler("", 90, " ") . "\r\n";
			$cont_ext++;
		}
	}
	
	$tpl->newBlock("archivos");
	$tpl->assign("folio", $folio);
	
	// Archivo para pagos del mismo banco
	if ($index_int > 0) {
		foreach ($int as $i => $string) {
			$f = fopen("trans/trans_int_" . $folio . "_" . $i . ".txt", "w");
			fwrite($f, $string);
			fclose($f);
			
			$tpl->newBlock("int");
			$tpl->assign("folio", $folio);
			$tpl->assign("num", $i);
		}
	}
	// Archivo para pagos a otros bancos
	if ($index_ext > 0) {
		if ($index_int > 0) $tpl->newBlock("extra");
		foreach ($ext as $i => $string) {
			$f = fopen("trans/trans_ext_" . $folio . "_" . $i . ".txt", "w");
			fwrite($f, $string);
			fclose($f);
			
			$tpl->newBlock("ext");
			$tpl->assign("folio", $folio);
			$tpl->assign("num", $i);
		}
	}
	
	// Actualizar status, folio y usuario que genero el archivo
	$sql = "UPDATE transferencias_electronicas SET status = 1, folio_archivo = $folio, iduser = $_SESSION[iduser] WHERE status = 0 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 800');
	if (count($pros) > 0) {
		$sql .= " AND num_proveedor IN (";
		foreach ($pros as $i => $pro)
			$sql .= $pro . ($i < count($pros) - 1 ? ", " : ")");
	}
	$sql .= ";\n";
	
	// [09-Feb-2007] Generar depositos de rentas y honorarios
	$rel = array(
		array(605, 625),
		array(312, 628),
		array(1231, 628),	// Agregado el 12-Nov-2010
		array(423, 601),
		array(434, 617),
		array(417, 614),
		array(435, 611),
		array(576, 618),
		array(174, 603),
		array(441, 613),
		array(644, 605),
		array(171, 604),
		array(173, 607),
		array(422, 610),
		array(436, 606),
		array(609, 623),
		array(290, 627),
		array(945, 615),
		array(948, 616),
		array(176, 612),
		array(433, 619),
		array(172, 602),
		array(230, 700),
		array(229, 800),
		array(35, 625),		// Agregado el 23-Oct-2009
		array(713, 622),	// Agregado el 23-Oct-2009
		array(390, 608),	// Agregado el 23-Oct-2009
		array(715, 620)		// Agregado el 25-May-2010
	);
	
	foreach ($rel as $r) {
		$sql .= '
			INSERT INTO
				estado_cuenta
					(
						num_cia,
						fecha,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						tipo_con
					)
				SELECT
					' . $r[1] . ',
					fecha_gen,
					\'FALSE\',
					te.importe,
					CASE
						WHEN concepto LIKE \'%RENTA%\' THEN
							2
						ELSE
							29
					END,
					CASE
						WHEN concepto LIKE \'%RENTA%\' THEN
							\'RENTA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
						WHEN concepto LIKE \'%HONORARIOS%\' THEN
							\'HONORARIOS (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
						WHEN concepto LIKE \'%OFICINA%\' THEN
							\'OFICINA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
						WHEN concepto LIKE \'%TALLERES LECAROZ%\' THEN
							\'TALLERES (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
						ELSE
							\'TRASPASO (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
					END,
					CASE
						WHEN num_banco = \'072\' THEN
							1
						WHEN num_banco = \'014\' THEN
							2
						ELSE
							1
					END,
					' . $_SESSION['iduser'] . ',
					CURRENT_TIMESTAMP,
					0
				FROM
						transferencias_electronicas te
					LEFT JOIN
						cheques
							USING (num_cia, folio, cuenta)
					LEFT JOIN
						catalogo_proveedores cp
							ON
								(
									cp.num_proveedor = te.num_proveedor
								)
					LEFT JOIN
						catalogo_companias
							USING
								(num_cia)
					LEFT JOIN
						catalogo_bancos
							USING
								(idbanco)
				WHERE
						te.num_proveedor = ' . $r[0] . '
					AND
						folio_archivo = ' . $folio . '
					AND
						te.status = 1
			' . ";\n";
	}
	
//	foreach ($rel as $r) {
//		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con) SELECT $r[1], fecha_gen, 'FALSE', te.importe,";
//		$sql .= " CASE WHEN concepto LIKE '%RENTA%' THEN 2 WHEN concepto LIKE '%HONORARIOS%' OR concepto LIKE '%OFICINA%' OR concepto LIKE '%TALLERES LECAROZ%' THEN 29 ELSE 29 END, CASE WHEN concepto LIKE '%RENTA%' THEN 'RENTA" . (in_array($_SESSION['iduser'], $users) ? ' ZAPATERIAS' : '') . " (' || nombre_corto || ')' WHEN";
//		$sql .= " concepto LIKE '%HONORARIOS%' THEN 'HONORARIOS" . (in_array($_SESSION['iduser'], $users) ? ' ZAPATERIAS' : '') . " (' || nombre_corto || ')' WHEN concepto LIKE '%OFICINA%' THEN 'OFICINA" . (in_array($_SESSION['iduser'], $users) ? ' ZAPATERIAS' : '') . " (' || nombre_corto || ')' WHEN concepto LIKE '%TALLERES LECAROZ%' THEN 'TALLERES" . (in_array($_SESSION['iduser'], $users) ? ' ZAPATERIAS' : '') . " (' || nombre_corto || ')' END, CASE WHEN tipo = 'FALSE' THEN 2 ELSE 1 END, 1, CURRENT_TIMESTAMP, 0 FROM";
//		$sql .= " transferencias_electronicas AS te LEFT JOIN cheques USING (num_cia, folio, cuenta) LEFT JOIN catalogo_companias USING (num_cia) WHERE te.num_proveedor = $r[0] AND";
//		$sql .= " folio_archivo = $folio AND te.status = 1;\n";
//	}
	
	//$db->query("" . ($_GET['num_pro'] > 0 ? " AND num_proveedor = $_GET[num_pro]" : ""));
	$db->query($sql);
	$tpl->printToScreen();
	die;
}

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['folio'])) {
	$sql = "SELECT num_cia AS num_cia, cc.nombre AS nombre_cia, te.num_proveedor AS num_pro, a_nombre AS nombre_pro, folio, facturas, te.importe, fecha_gen, ch.concepto AS concepto,";
	$sql .= " folio_archivo, tipo FROM transferencias_electronicas AS te LEFT JOIN catalogo_companias AS cc USING (num_cia) LEFT JOIN cheques AS ch USING (num_cia, folio, cuenta)";
	$sql .= " WHERE folio_archivo = $_GET[folio] AND te.status = 1";
	//$sql .= isset($_GET['num_pro']) && $_GET['num_pro'] > 0 ? " AND te.num_proveedor = $_GET[num_pro]" : '';
	$pros = array();
	if (isset($_GET['num_pro'])) {
		foreach ($_GET['num_pro'] as $p)
			if ($p > 0)
				$pros[] = $p;
	}
	if (count($pros) > 0) {
		$sql .= " AND te.num_proveedor IN (";
		foreach ($pros as $i => $pro)
			$sql .= $pro . ($i < count($pros) - 1 ? ", " : ")");
	}
	$sql .= " ORDER BY nombre_pro, num_pro, num_cia, folio";
	$result = $db->query($sql);
	
	// Listados para proveedores
	$num_pro = NULL;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			if ($num_pro != NULL) {
				$tpl->assign("listado.total", number_format($total, 2, ".", ","));
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			}
			
			$num_pro = $reg['num_pro'];
			
			// [16-Jun-2009] Obtener contraseña para site de Lecaroz
			$pass = $db->query("SELECT pass_site FROM catalogo_proveedores WHERE num_proveedor = $num_pro");
			
			$tpl->newBlock("listado");
			$tpl->assign("num_pro", $num_pro);
			$tpl->assign("nombre", $reg['nombre_pro']);
			$tpl->assign("folio", $reg['folio_archivo']);
			$tpl->assign('user', str_pad($num_pro, 5, '0', STR_PAD_LEFT));
			$tpl->assign('pass', $pass[0]['pass_site']);
			$total = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_cia']);
		$tpl->assign("fecha", $reg['fecha_gen']);
		$tpl->assign("folio", $reg['folio']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("facturas", $reg['facturas']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
		$total += $reg['importe'];
	}
	if ($num_pro != NULL) {
		$tpl->assign("listado.total", number_format($total, 2, ".", ","));
		$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
	}
	
	// Listado copia para la oficina
	/*$tpl->newBlock("concentrado");
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->assign("folio", $_GET['folio']);
	
	$num_pro = NULL;
	$gran_total = 0;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			if ($num_pro != NULL)
				$tpl->assign("pro.total", number_format($total, 2, ".", ","));
			
			$num_pro = $reg['num_pro'];
			
			$tpl->newBlock("pro");
			$tpl->assign("num_pro", $num_pro);
			$tpl->assign("nombre", $reg['nombre_pro']);
			$total = 0;
		}
		$tpl->newBlock("fila_con");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_cia']);
		$tpl->assign("fecha", $reg['fecha_gen']);
		$tpl->assign("folio", $reg['folio']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("facturas", $reg['facturas']);
		$tpl->assign("tipo", $reg['tipo'] == "t" ? "EXT" : "INT");
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
		$total += $reg['importe'];
		$gran_total += $reg['importe'];
	}
	if ($num_pro != NULL)
		$tpl->assign("pro.total", number_format($total, 2, ".", ","));
	$tpl->assign("concentrado.total", number_format($gran_total, 2, ".", ","));*/
	
	// Listado de Totales
	$sql = "SELECT num_proveedor AS num_pro, nombre, tipo, sum(importe) AS importe FROM transferencias_electronicas LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE";
	$sql .= " folio_archivo = $_GET[folio] AND status = 1 GROUP BY num_pro, nombre, tipo ORDER BY /*num_pro*/nombre";
	$totales = $db->query($sql);
	
	$tpl->newBlock("totales");
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->assign("folio", $_GET['folio']);
	
	$total_int = 0;
	$total_ext = 0;
	foreach ($totales as $total) {
		$tpl->newBlock("total");
		$tpl->assign("num_pro", $total['num_pro']);
		$tpl->assign("nombre", $total['nombre']);
		$tpl->assign("tipo", $total['tipo'] == "t" ? "EXT" : "INT");
		$tpl->assign("importe", number_format($total['importe'], 2, ".", ","));
		$total_int += $total['tipo'] == "f" ? $total['importe'] : 0;
		$total_ext += $total['tipo'] == "t" ? $total['importe'] : 0;
	}
	$tpl->assign("totales.total_int", number_format($total_int, 2, ".", ","));
	$tpl->assign("totales.total_ext", number_format($total_ext, 2, ".", ","));
	$tpl->assign("totales.total", number_format($total_int + $total_ext, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$tpl->printToScreen();
?>