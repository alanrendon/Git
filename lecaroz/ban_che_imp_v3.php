<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';
include './includes/pcl.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31, 32);

// Códigos de escape antigüos MICR
define ("strIni",    "&%STHPASSWORD$");				// Cadena que inicia el modo MICR de la impresora
define ("strImpIni", "&%STP12500$&%1B$(12500X$");	// Cadena de inicio de impresión de importe con protección especial
define ("strImpFin", "&%$");						// Cadena de fin de impresión de importe con protección especial
define ("strBanIni", "&%SMD");						// Cadena de inicio de impresión de banda MICR
define ("strBanFin", "$");							// Cadena de fin de impresión de banda MICR

// [30-Ago-2007] Nuevos códigos de escape para impresora Lexmark E330
define ("strImpIniE330", "(12500X");		// Cadena de inicio de impresión de importe con protección especial
define ("strImpIniE330Small", "(12600X");	// Cadena de inicio de impresión de importe con protección especial (pequeño)
define ("strImpFinE330", "@");				// Cadena de fin de impresión de importe con protección especial
define ("strBanIniE330", "(5X");			// Cadena de inicio de impresión de banda MICR
define ("strBanFinE330", "@");				// Cadena de fin de impresión de importe con protección especial

define ("AJUSTE_V", -2.00);
define ("AJUSTE_H", 2.00);

function chequePCL($datos, $cuenta, $poliza = FALSE) {
	$pcl = "";
	
	$bandaMICR   = bandaMICR(numBanco, $datos['cuenta'], $datos['folio'], codSeguridad, claveTransaccion, plazaCompensacion);
	$pseudoBanda = pseudoBanda($bandaMICR, $datos['importe']);
	
	$fac = explode(" ", $datos['facturas']);
	
	// *********************** POLIZA ***********************
	// Nombre de la compañía
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(5.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= SetFontPointSize(10.00);
	$pcl .= strtoupper($datos['nombre_cia']);
	// RFC
	$pcl .= MoveCursorH(22.00 - AJUSTE_H);
	$pcl .= MoveCursorV(9.00 - AJUSTE_V);
	$pcl .= SetFontPointSize(8.00);
	$pcl .= strRFC($datos['rfc'], TRUE);
	// Leyenda PARA ABONO A CUENTA BANCARIA DE:
	if ($datos['para_abono'] == "t") {
		$pcl .= MoveCursorH(2.00 - AJUSTE_H);
		$pcl .= MoveCursorV(15.00 - AJUSTE_V);
		$pcl .= SetFontStrokeWeight(MEDIUM);
		$pcl .= SetFontPointSize(10.00);
		$pcl .= "PARA ABONO EN CUENTA BANCARIA DE:";
	}
	// Fecha de expedicíón
	$pcl .= MoveCursorH(118.00 - AJUSTE_H - 10.00);
	$pcl .= MoveCursorV(21.00 - AJUSTE_V - 1);
	$pcl .= $datos['para_abono'] == "f" ? SetFontStrokeWeight(MEDIUM): "";
	$pcl .= SetFontPointSize(10.00);
	$pcl .= strFecha($datos['fecha']);
	// Beneficiario
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(31.00 - AJUSTE_V - 2);
	$pcl .= $datos['a_nombre'];
	// Importe formateado
	$pcl .= MoveCursorH(100.00 - AJUSTE_H);
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= "*" . number_format($datos['importe'], 2, ".", ",") . "*";
	// Importe escrito
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(34.00 - AJUSTE_V);
	$pcl .= SetFontPointSize(8.00);
	$pcl .= SetFontStrokeWeight(MEDIUM);
	$pcl .= "*" . num2string($datos['importe']) . "*";
	// Leyendas de Banorte
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(38.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= SetFontPointSize(10.00);
	$pcl .= $cuenta == 1 ? "BANCO MERCANTIL DEL NORTE, S.A." : "BANCO SANTANDER MEXICANO, S.A.";
	// PseudoBanda
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(65.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(MEDIUM);
	$pcl .= $pseudoBanda;
	// Facturas pagadas por el cheque
	$pcl .= MoveCursorH(0.00);
	$pcl .= MoveCursorV(76.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(BOLD);
	if ($fac[0] != "") {
		$num_facs_x_rows = 11;
		$num_facs = 0;
		for ($i = 0; $i < count($fac); $i++) {
			$pcl .= $fac[$i];
			$pcl .= MoveCursorH(1.00, TRUE);
			$num_facs++;
			if ($num_facs >= $num_facs_x_rows) {
				$pcl .= MoveCursorH(0.00);
				$pcl .= MoveCursorV(4.00 - AJUSTE_V, TRUE);
				$num_facs = 0;
			}
		}
		$pcl .= MoveCursorH(0.00);
		$pcl .= MoveCursorV(4.00 - AJUSTE_V, TRUE);
		$pcl .= $datos['concepto'];
	}
	else
		$pcl .= $datos['concepto'];
	
	// [11-Jun-2007] [ZAPATERIAS] Desglosar faltantes, descuentos, fletes y devoluciones
	if ($datos['num_cia'] >= 900) {
		// Desglose de facturas
		if (count($fac) > 0) {
			// [29-Jun-2007] Modificación de script para que incluya las devoluciones, fletes y otros de cada factura
			$sql = "SELECT num_fact, fz.importe, faltantes, sum(dz.importe) AS dev, pdesc1, pdesc2, pdesc3, pdesc4, desc1, desc2, desc3, desc4, iva, ivaret, isr, fletes, otros, total FROM";
			$sql .= " facturas_zap AS fz LEFT JOIN devoluciones_zap AS dz USING (num_proveedor, num_fact) WHERE num_proveedor = $datos[num_proveedor] AND num_fact IN (";
			foreach ($fac as $i => $f)
				$sql .= intval($f, 10) . ($i < count($fac) - 1 ? ', ' : ')');
			$sql .= " GROUP BY num_fact, fz.importe, faltantes, pdesc1, pdesc2, pdesc3, pdesc4, desc1, desc2, desc3, desc4, iva, ivaret, isr, fletes, otros, total";
			$des = $GLOBALS['db']->query($sql);
			
			if ($des) {
				$pcl .= MoveCursorH(0.00);
				$pcl .= MoveCursorV(135.00);
				$pcl .= SetFontStrokeWeight(BOLD);
				$pcl .= SetFontPointSize(6.00);
				$pcl .= "FAC.";
				$pcl .= MoveCursorH(10.00);
				$pcl .= "SUB.";
				$pcl .= MoveCursorH(20.00);
				$pcl .= "FAL.";
				$pcl .= MoveCursorH(30.00);
				$pcl .= "DEV.";
				$pcl .= MoveCursorH(40.00);
				$pcl .= "DESC.";
				$pcl .= MoveCursorH(50.00);
				$pcl .= "IVA";
				$pcl .= MoveCursorH(60.00);
				$pcl .= "RET.";
				$pcl .= MoveCursorH(70.00);
				$pcl .= "ISR";
				$pcl .= MoveCursorH(80.00);
				$pcl .= "FLETES";
				$pcl .= MoveCursorH(90.00);
				$pcl .= "OTROS";
				$pcl .= MoveCursorH(100.00);
				$pcl .= "TOTAL";
				$pcl .= SetFontStrokeWeight(MEDIUM);
				
				foreach ($des as $i => $d) {
					if ($d['dev'] > 0) {
						$importe = $d['importe'] - $d['faltantes'] - $d['dev'];
						$desc1 = $d['pdesc1'] > 0 ? round($importe * $d['pdesc1'] / 100, 2) : ($d['desc1'] > 0 ? $d['desc1'] : 0);
						$desc2 = $d['pdesc2'] > 0 ? round(($importe - $desc1) * $d['pdesc2'] / 100, 2) : ($d['desc2'] > 0 ? $d['desc2'] : 0);
						$desc3 = $d['pdesc3'] > 0 ? round(($importe - $desc1 - $desc2) * $d['pdesc3'] / 100, 2) : ($d['desc3'] > 0 ? $d['desc3'] : 0);
						$desc4 = $d['pdesc4'] > 0 ? round(($importe - $desc1 - $desc2 - $desc3) * $d['pdesc4'] / 100, 2) : ($d['desc4'] > 0 ? $d['desc4'] : 0);
						$subtotal = $importe - $desc1 - $desc2 - $desc3 - $desc4;
						$iva = $d['iva'] > 0 ? $subtotal * 0.15 : 0;
						$total_fac = $subtotal + $iva - $d['fletes'] + $d['otros'];
					}
					else {
						$importe = $d['importe'];
						$desc1 = $d['desc1'];
						$desc2 = $d['desc2'];
						$desc3 = $d['desc3'];
						$desc4 = $d['desc4'];
						$iva = $d['iva'];
						$total_fac = $d['total'];
					}
					
					
					$pcl .= MoveCursorH(0.0);
					$pcl .= MoveCursorV(135 + 3.5 * ($i + 1));
					$pcl .= fillZero($d['num_fact'], 7);
					$pcl .= MoveCursorH(10.00);
					$pcl .= $importe != 0 ? number_format($importe, 2, '.', ',') : '';
					$pcl .= MoveCursorH(20.00);
					$pcl .= $d['faltantes'] != 0 ? number_format($d['faltantes'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(30.00);
					$pcl .= $d['dev'] != 0 ? number_format($d['dev'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(40.00);
					$pcl .= $desc1 + $desc2 + $desc3 + $desc4 != 0 ? number_format($desc1 + $desc2 + $desc3 + $desc4, 2, '.', ',') : '';
					$pcl .= MoveCursorH(50.00);
					$pcl .= $iva != 0 ? number_format($iva, 2, '.', ',') : '';
					$pcl .= MoveCursorH(60.00);
					$pcl .= $d['ivaret'] != 0 ? number_format($d['ivaret'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(70.00);
					$pcl .= $d['isr'] != 0 ? number_format($d['isr'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(80.00);
					$pcl .= $d['fletes'] != 0 ? number_format($d['fletes'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(90.00);
					$pcl .= $d['otros'] != 0 ? number_format($d['otros'], 2, '.', ',') : '';
					$pcl .= MoveCursorH(100.00);
					$pcl .= $total_fac != 0 ? number_format($total_fac, 2, '.', ',') : '';
				}
			}
		}
	}
	
	// *********************** CHEQUE ***********************
	// Nombre de la compañía
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(187.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= SetFontPointSize(10.00);
	$pcl .= strtoupper($datos['nombre_cia']);
	// Leyenda G.OAM
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(191.00 - AJUSTE_V);
	$pcl .= SetFontPointSize(8.00);
	$pcl .= "G.OAM";
	// RFC
	$pcl .= MoveCursorH(22.00 - AJUSTE_H);
	$pcl .= "RFC: " . strRFC($datos['rfc'], TRUE);	// [30/11/2005] Se agrego 'RFC: '
	// Leyenda PARA ABONO A CUENTA BANCARIA DE:
	if ($datos['para_abono'] == "t") {
		$pcl .= MoveCursorH(2.00);
		$pcl .= MoveCursorV(197.00 - AJUSTE_V);
		$pcl .= SetFontStrokeWeight(MEDIUM);
		$pcl .= SetFontPointSize(10.00);
		$pcl .= "PARA ABONO EN CUENTA BANCARIA DE:";
	}
	// Leyenda LUGAR DE EXPEDICION
	$pcl .= MoveCursorH(89.00 - AJUSTE_H);	// [30/11/2005] Se modifico de 74mm a 84mm
	$pcl .= MoveCursorV(203.00 - AJUSTE_V + ($cuenta == 1 ? -8 : 0));
	$pcl .= $datos['para_abono'] == "f" ? SetFontStrokeWeight(MEDIUM): "";
	$pcl .= SetFontPointSize(6.00);
	$pcl .= "MEXICO, D.F.";	// [30/11/2005] Se cambio de 'LUGAR DE EXPEDICION' a 'MEXICO, D.F.'
	// Fecha de expedicíón
	if ($cuenta == 1) $pcl .= MoveCursorV(203.00 - 6.00);
	$pcl .= MoveCursorH(110.00 - AJUSTE_H + ($cuenta == 1 ? -8 : 0));
	$pcl .= SetFontPointSize(10.00);
	$pcl .= strFecha($datos['fecha']);
	// Beneficiario
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(211.00 - AJUSTE_V);
	$pcl .= $datos['a_nombre'];
	// Importe formateado
	if ($cuenta == 1) $pcl .= MoveCursorV(212.00 - 1.00);
	$pcl .= MoveCursorH(100.00 + ($cuenta == 1 ? 12 : 0));
	$pcl .= (!$poliza ? ESC . ($_SESSION['iduser'] >= 28 ? strImpIni : ($cuenta == 1 ? strImpIniE330Small : strImpIniE330)) : "*") . number_format($datos['importe'], 2, ".", ",") . (!$poliza ?  ($_SESSION['iduser'] >= 28 ? strImpFin : strImpFinE330) : "*");
	// Importe escrito
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(218.00 - AJUSTE_V);
	$pcl .= DEFAULT_FONT;
	$pcl .= SetFontPointSize(8.00);
	$pcl .= "*" . num2string($datos['importe']) . "*";
	// Leyendas de Banorte
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(225.00 - AJUSTE_V);	// [30/11/2005] Modificado de 222mm a 225mm
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= SetFontPointSize(8.00);
	$pcl .= $cuenta == 1 ? "BANCO MERCANTIL DEL NORTE, S.A." : "";
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(227.00 - AJUSTE_V);	// [30/11/2005] Modificado de 225mm a 227mm
	$pcl .= SetFontStrokeWeight(MEDIUM);
	$pcl .= SetFontPointSize(5.00);	// [30/11/2005] Modificado de 6Pts a 5Pts
	$pcl .= $cuenta == 1 ? "INSTITUCION DE BANCA MULTIPLE." : "";
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(229.00 - AJUSTE_V);	// [30/11/2005] Modificado de 227mm a 229mm
	$pcl .= $cuenta == 1 ? "GRUPO FINANCIERO BANORTE." : "";
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(232.00 - AJUSTE_V);
	$pcl .= SetFontPointSize(6.00);	// [30/11/2005] Agregado para cambiar tamaño de fuente
	$pcl .= $cuenta == 1 ? "SUC. 0679 LINDA VISTA MEXICO" : "SUC. 014 TACUBAYA";
	$pcl .= MoveCursorH(40.00 - AJUSTE_H);
	$pcl .= SetFontStrokeWeight(BOLD);
	$pcl .= "CTA " . cuenta($datos['cuenta']);
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(235.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(MEDIUM);
	$pcl .= "MEXICO, D.F.";
	// Banda MICR
	$pcl .= MoveCursorH(2.00 - AJUSTE_H);
	$pcl .= MoveCursorV(243.00 - AJUSTE_V);
	$pcl .= $poliza ? SetFontPointSize(10.00) : "";
	$pcl .= (!$poliza ? ESC . ($_SESSION['iduser'] >= 28 ? strBanIni : strBanIniE330) : "") . (!$poliza ? $bandaMICR : $pseudoBanda) . (!$poliza ? ($_SESSION['iduser'] >= 28 ? strBanFin : strBanFinE330) : "");
	$pcl .= DEFAULT_FONT;
	// Facturas pagadas por el cheque
	/*$pcl .= MoveCursorH(0.00);
	$pcl .= MoveCursorV(260.00 - AJUSTE_V);
	$pcl .= SetFontStrokeWeight(BOLD);
	
	if ($fac[0] != "") {
		$num_facs_x_rows = 11;
		$num_facs = 0;
		for ($i = 0; $i < count($fac); $i++) {
			$pcl .= $fac[$i];
			$pcl .= MoveCursorH(1.00, TRUE);
			$num_facs++;
			if ($num_facs >= $num_facs_x_rows) {
				$pcl .= MoveCursorH(0.00);
				$pcl .= MoveCursorV(4.00 - AJUSTE_V, TRUE);
				$num_facs = 0;
			}
		}
		$pcl .= MoveCursorH(0.00);
		$pcl .= MoveCursorV(4.00 - AJUSTE_V, TRUE);
		$pcl .= $datos['concepto'];
	}
	else
		$pcl .= $datos['concepto'];*/
	
	return $pcl;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_che_imp_v3.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Impresión de comprobantes de transferencias electrónicas
if (isset($_GET['pol'])) {
	$sql  = "SELECT id, num_cia, catalogo_companias.rfc AS rfc, catalogo_companias.clabe_cuenta2 AS cuenta, catalogo_companias.nombre AS nombre_cia, cheques.num_proveedor";
	$sql .= " AS num_proveedor, fecha, folio, a_nombre, concepto, importe, facturas, para_abono FROM cheques LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " LEFT JOIN catalogo_proveedores ON (catalogo_proveedores.num_proveedor = cheques.num_proveedor) WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800') . " AND imp = 'FALSE' AND cheques.cuenta = 2 AND poliza = 'TRUE'";
	$sql .= " AND fecha_cancelacion IS NULL ORDER BY a_nombre, num_cia, folio ASC";
	$result = $db->query($sql);
	
	define ("numBanco",          "014");
	define ("codSeguridad",      "000");
	define ("claveTransaccion",  "51");
	define ("plazaCompensacion", "999");
	
	$pcl = "";		// Variable contenedora de la cadena PCL
	
	$pcl .= HEADER;
	$pcl .= SetPageSize(LETTER);	// Fijar tamaño de página a carta
	$pcl .= SetTopMargin(1);		// Poner margen superior a 1 renglon
	$pcl .= SetLeftMargin(0);		// Poner margen izquierdo a 0
	$pcl .= DEFAULT_FONT;			// Usar fuente por default
	$sql = "";
	for ($i = 0; $i < count($result); $i++) {
		$pcl .= chequePCL($result[$i], 2, TRUE);
		$pcl .= $i < count($result) - 1 ? FORM_FEED : "";
		
		$sql .= "UPDATE cheques SET imp = 'TRUE', archivo = 'FALSE' WHERE id = {$result[$i]['id']};\n";
	}
	$pcl .= RESET;
	
	// Actualizar la base
	$db->query($sql);
	
	// Generar archivo PCL
	shell_exec("chmod 777 pcl");
	$fp = fopen("pcl/cheques" . (in_array($_SESSION['iduser'], $users) ? '_zap' : '') . ".pcl", "w");
	fwrite($fp, $pcl);
	fclose($fp);
	
	// Imprimir cheques
	//shell_exec("lp -d T622 pcl/cheques.pcl");
	// [12/09/2006] Cambio provisional
	if (in_array($_SESSION['iduser'], $users))
		shell_exec("lp -d elite pcl/cheques_zap.pcl");
	else
		shell_exec("lp -d S1855 pcl/cheques.pcl");
	shell_exec("chmod 755 pcl");
	
	$tpl->newBlock("alert");
	
	$tpl->printToScreen();
	die;
}

// Proceso de impresión
if (isset($_POST['imp'])) {
	// Obtener datos de los cheques a imprimir
	$sql  = "SELECT id, num_cia, catalogo_companias.rfc AS rfc, catalogo_companias.clabe_cuenta" . ($_POST['cuenta'] == 1 ? "" : "2") . " AS cuenta, catalogo_companias.nombre";
	$sql .= " AS nombre_cia, cheques.num_proveedor AS num_proveedor, fecha, folio, a_nombre, concepto, importe, facturas, para_abono FROM cheques LEFT JOIN catalogo_companias";
	$sql .= " USING (num_cia) LEFT JOIN catalogo_proveedores ON (catalogo_proveedores.num_proveedor = cheques.num_proveedor) WHERE id IN (";
	for ($i = 0; $i < count($_POST['id']); $i++)
		$sql .= $_POST['id'][$i] . ($i < count($_POST['id']) - 1 ? ", " : ")");
	$sql .= " ORDER BY a_nombre, num_cia, folio ASC";
	$result = $db->query($sql);
	
	// Definir constantes
	if ($_POST['cuenta'] == 1) {	// BANORTE
		define ("numBanco",          "072");
		define ("codSeguridad",      "000");
		define ("claveTransaccion",  "51");
		define ("plazaCompensacion", "115");
	}
	else {							// SANTANDER SERFIN
		define ("numBanco",          "014");
		define ("codSeguridad",      "000");
		define ("claveTransaccion",  "51");
		define ("plazaCompensacion", "999");
	}
	
	$num_cheque1 = $_POST['ultimo_folio'] > 0 ? $_POST['ultimo_folio'] : NULL;						// Folio inicial de los cheques (ficha)
	$num_cheque2 = $_POST['ultimo_folio'] > 0 ? $_POST['ultimo_folio'] + count($result) - 1 : NULL;		// Folio final de los cheques (ficha)
	$num_cheque  = $_POST['ultimo_folio'] > 0 ? ($_POST['orden'] == "asc" ? $num_cheque2 : $num_cheque1) : NULL;	// Folio de los cheques (ficha)
	
	$pcl = "";		// Variable contenedora de la cadena PCL
	
	$pcl .= HEADER;
	$pcl .= SetPageSize(LETTER);	// Fijar tamaño de página a carta
	$pcl .= SetTopMargin(1);		// Poner margen superior a 1 renglon
	$pcl .= SetLeftMargin(0);		// Poner margen derecho a 0
	$pcl .= DEFAULT_FONT;			// Usar fuente por default
	$pcl .= $_POST['ultimo_folio'] > 0 && $_SESSION['iduser'] >= 28 ? ESC . strIni : "";			// En caso de no ser polizas, iniciar el modo MICR de la impresora
	$sql = "";
	for ($i = 0; $i < count($result); $i++) {
		$pcl .= chequePCL($result[$i], $_POST['cuenta'], $_POST['ultimo_folio'] > 0 ? FALSE : TRUE);
		$pcl .= $i < count($result) - 1 ? FORM_FEED : "";
		
		if ($_POST['ultimo_folio'] > 0)
			//$sql .= "UPDATE cheques SET imp = 'TRUE'" . ($_POST['ultimo_folio'] > 0 ? ", num_cheque = $num_cheque" : "") . " WHERE id = {$result[$i]['id']};\n";
			$sql .= "UPDATE cheques SET imp = 'TRUE', num_cheque = $num_cheque, archivo = 'TRUE' WHERE id = {$result[$i]['id']};\n";
		else
			$sql .= "UPDATE cheques SET imp = 'TRUE' WHERE id = {$result[$i]['id']};\n";
		
		$num_cheque += $_POST['ultimo_folio'] > 0 ? ($_POST['orden'] == "asc" ? -1 : 1) : NULL;
	}
	$pcl .= RESET;
	
	if (empty($_POST['reim']))
		$sql .= "UPDATE status_cheques SET ok = 'FALSE', folio1 = " . ($num_cheque1 > 0 ? $num_cheque1 : "NULL") . ", folio2 = " . ($num_cheque2 > 0 ? $num_cheque2 : "NULL") . ", orden = " . ($_POST['ultimo_folio'] > 0 ? ($_POST['orden'] == "asc" ? 1 : 2) : "NULL") . ";\n";
	
	// Actualizar la base
	$db->query($sql);
	
	// Generar archivo PCL
	shell_exec("chmod 777 pcl");
	$fp = fopen("pcl/cheques" . (in_array($_SESSION['iduser'], $users) ? '_zap' : '') . ".pcl", "w");
	fwrite($fp, $pcl);
	fclose($fp);
	
	// Imprimir cheques
	//shell_exec("lp -d T622 pcl/cheques.pcl");
	// [12/09/2006] Cambio provisional
	if (in_array($_SESSION['iduser'], $users))
		shell_exec("lp -d elite pcl/cheques_zap.pcl");
		//shell_exec("lp -d elite pcl/cheques_tmp.pcl");
	else
		shell_exec("lp -d " . ($_POST['ultimo_folio'] > 0 ? "T622" : "S1855") . " pcl/cheques.pcl");
		//shell_exec("lp -d T622 pcl/cheques_tmp.pcl");
	
	shell_exec("chmod 755 pcl");
	
	header("location: ./ban_che_error.php");
	die;
}

if (isset($_POST['id'])) {
	$tpl->newBlock("num_cheque");
	
	$tpl->assign("cuenta", $_POST['cuenta']);
	$tpl->assign("ultimo_folio", $_POST['ultimo_folio']);
	$tpl->assign("orden", $_POST['orden']);
	$tpl->assign("poliza", $_POST['ultimo_folio'] == "" ? "&poliza=1" : "");
	
	for ($i = 0; $i < count($_POST['id']); $i++) {
		$tpl->newBlock("id");
		$tpl->assign("id", $_POST['id'][$i]);
	}
	$tpl->gotoBlock("num_cheque");
	
	if ($_POST['ultimo_folio'] != "") {
		$num_cheque1 = $_POST['ultimo_folio'];
		$num_cheque2 = $_POST['ultimo_folio'] + count($_POST['id']) - 1;
		$mensaje = "<font face=\"Arial, Helvetica, sans-serif\" size=\"+1\">Inserte los cheques en la impresora del folio </font>";
		$mensaje .= "<strong><font face=\"Arial, Helvetica, sans-serif\" size=\"+2\">$num_cheque1</font></strong>";
		$mensaje .= "<font face=\"Arial, Helvetica, sans-serif\" size=\"+1\"> al </font>";
		$mensaje .= "<strong><font face=\"Arial, Helvetica, sans-serif\" size=\"+2\">$num_cheque2</font></strong>";
		$mensaje .= "<font face=\"Arial, Helvetica, sans-serif\" size=\"+1\"> y presione \"Imprimir\".</font>";
		$mensaje .= "<br><font face=\"Arial, Helvetica, sans-serif\" size=\"+1\">(Usar Toner Magn&eacute;tico)</font>";
	}
	else {
		$mensaje = "<font face=\"Arial, Helvetica, sans-serif\" size=\"+1\">Inserte las polizas en la impresora y presione \"Imprimir\".</font>";
		$mensaje .= "<br><font face=\"Arial, Helvetica, sans-serif\" size=\"+1\">(Usar Toner Normal)</font>";
	}
	
	$tpl->assign("mensaje", $mensaje);
	$tpl->printToScreen();
	die;
}

if (isset($_GET['ultimo_folio'])) {
	// Validar ultimo folio
	$sql = "SELECT num_cheque FROM cheques WHERE num_cheque IS NOT NULL AND fecha >= '31/10/2005' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 899') . " AND cuenta = $_GET[cuenta] ORDER BY num_cheque DESC LIMIT 1";
	$ultimo_cheque = $db->query($sql);
	/*if ($_GET['ultimo_folio'] > 0 && $_GET['ultimo_folio'] != $ultimo_cheque[0]['num_cheque'] + 1) {
		$tpl->newBlock("error_folio");
		$tpl->printToScreen();
		die;
	}*/
	
	// Obtener cheques por imprimir
	$sql = "SELECT id, cheques.num_proveedor AS num_proveedor, a_nombre, num_cia, nombre_corto, clabe_cuenta" . ($_GET['cuenta'] == 2 ? "2" : "") . ", fecha, folio, concepto, importe";
	$sql .= " FROM cheques LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 899') . " AND imp = 'FALSE' AND cuenta = $_GET[cuenta] " . (!isset($_GET['poliza']) ? "AND poliza != 'TRUE'" : '') . " AND fecha_cancelacion IS NULL ORDER BY a_nombre, num_cia, folio ASC";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock("no_result");
		$tpl->printToScreen();
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("cuenta", $_GET['cuenta']);
	$tpl->assign("num_cheques", count($result));
	if (empty($_GET['poliza'])) {
		$tpl->assign("ultimo_folio", $_GET['ultimo_folio']);
		$tpl->assign("orden", $_GET['orden']);
	}
	
	$num_pro = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_pro != $result[$i]['num_proveedor']) {
			$num_pro = $result[$i]['num_proveedor'];
			
			$tpl->newBlock("proveedor");
			$tpl->assign("num_proveedor", $num_pro);
			$tpl->assign("nombre_proveedor", $result[$i]['a_nombre']);
			$tpl->assign("ini", $i);
			
			$total = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("proveedor.fin", $i);
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("cuenta", $result[$i]['clabe_cuenta' . ($_GET['cuenta'] == 2 ? "2" : "")]);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("folio", $result[$i]['folio']);
		$tpl->assign("concepto", $result[$i]['concepto']);
		$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
		
		$total += $result[$i]['importe'];
		
		$tpl->assign("proveedor.total", number_format($total, 2, ".", ","));
	}
	
	$tpl->printToScreen();
	die;
}

// Verificar comprobantes de transferencia electrónica
$result = $db->query("SELECT id FROM cheques WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800') . " AND imp = 'FALSE'/* AND cuenta = 2*/ AND poliza = 'TRUE' AND fecha_cancelacion IS NULL LIMIT 1");
//$result = $db->query("SELECT id FROM transferencias_electronicas WHERE status = 0");
if (!isset($_GET['no_pol']) && $result) {
	$tpl->newBlock("polizas");
	$tpl->printToScreen();
	die;
}

// Verificar si existen comprobantes de impuestos por imprimir
$result = $db->query("SELECT id FROM cheques WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800') . " AND imp = 'FALSE' AND codgastos IN (140, 141) AND fecha_cancelacion IS NULL LIMIT 1");

// Verificar bandera de error
$status = $db->query("SELECT ok FROM status_cheques");
if ($status[0]['ok'] == "f") {
	header("location: ./ban_che_error.php");
	die;
}

$tpl->newBlock("datos");

if ($result) $tpl->assign('mensaje', '<p style="font-family:Arial, Helvetica, sans-serif; font-size:14pt;">Existen polizas de impuestos pendientes de imprimir</p>');

$tpl->printToScreen();
?>
