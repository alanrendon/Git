<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/cheques.inc.php';
include 'includes/pcl.inc.php';
include 'includes/class.xml.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('Haciendo pruebas, intentelo mas tarde');

// Códigos de escape antigüos MICR
define ('strIni',         ESC . '&%STHPASSWORD$');			// Cadena que inicia el modo MICR de la impresora
define ('strImpIni',      ESC . '&%1B$(12500X');			// Cadena de inicio de impresión de importe con protección especial
define ('strImpIniSmall', ESC . '&%1B$(12600X');			// [22-Abr-2009] Cadena de inicio de impresión de importe con protección especial (pequeño)
define ('strImpFin',      '&%$');							// Cadena de fin de impresión de importe con protección especial
define ('strBanIni',      ESC . '&%SMD');					// Cadena de inicio de impresión de banda MICR
define ('strBanFin',      '$');								// Cadena de fin de impresión de banda MICR

// Nuevos códigos de escape para impresora Lexmark E330
define ('strImpIniE330',      ESC . '(12500X');				// Cadena de inicio de impresión de importe con protección especial
define ('strImpIniE330Small', ESC . '(12600X');				// Cadena de inicio de impresión de importe con protección especial (pequeño)
define ('strBanIniE330',      ESC . '(5X');					// Cadena de inicio de impresión de banda MICR
define ('strFinE330',         '@');							// Cadena de finalización

$layout = array(
	/*
	@@@
	@
	@ Mollendo
	@
	*/
	1 => array(
		'polizas' => array(
			1 => 'XML/PolizaBanorteMollendoLexmark.xml',
			2 => 'XML/PolizaSantanderMollendoLexmark.xml',
		),
		'cheques' => array(
			1 => 'XML/ChequeBanorteMollendo.xml',
			2 => 'XML/ChequeSantanderMollendo.xml'
		)
	),
	/*
	@@@
	@
	@ Elite
	@
	*/
	2 => array(
		'polizas' => array(
			1 => 'XML/PolizaBanorteElite.xml',
			2 => 'XML/PolizaSantanderElite.xml',
		),
		'cheques' => array(
			1 => 'XML/ChequeBanorteElite.xml',
			2 => 'XML/ChequeSantanderElite.xml'
		)
	)
);

$datosBanco = array(
	// Banorte
	1 => array(
		'numBanco' =>          '072',
		'codSeguridad' =>      '000',
		'claveTransaccion' =>  '51',
		'plazaCompensacion' => '115'
	),
	// Santander
	2 => array(
		'numBanco' =>          '014',
		'codSeguridad' =>      '000',
		'claveTransaccion' =>  '51',
		'plazaCompensacion' => '999'
	)
);

$PCLfunctions = array(
	'Campo',
	'Leyenda',
	'Constant',
	'PageSize',
	'PageWidth',
	'PageHeight',
	'LeftMargin',
	'TopMargin',
	'Font',
	'X',
	'Y',
	'FontFace',
	'FontWeight',
	'FontPointSize',
	'FontPitch',
	'modoMICR',
	'Desglose',
	'DefineMacro',
	'CallMacro'
);

function LayoutToPCL($layout, $data = NULL) {
	global $PCLfunctions;
	
	$pcl = '';
	
	foreach ($layout as $k => $v)
		// Omitir el campo 'ParaAbono' si no esta activada la opción para el proveedor
		if ($k == 'ParaAbono' && $data['para_abono'] != 't')
			continue;
		// Si la opción del layout esta dentro de las funciones PCL,
		// concatenar opciones a la cadena principal PCL
		else if (in_array($k, $PCLfunctions)) {
			// Validar que el campo tenga datos
			if (!isset($v['data']))
				continue;
			else if (trim($v['data']) == '')
				continue;
			
			switch ($k) {
				// Campo
				case 'Campo':
					if (isset($data[$v['data']])) {
						$campo = trim($data[$v['data']]);
						
						// Aplicar una función al valor del campo
						if (isset($v['function']) && trim($v['function']) != '')
							$campo = $v['function']($campo);
						// Convertir a mayúsculas
						if (isset($v['toUpper']) && constant($v['toUpper']))
							$campo = strtoupper($campo);
						// Formatear número
						if (isset($v['numberFormat']))
							$campo = number_format($campo, $v['numberFormat'], '.', ',');
						
						// Aplicar código de escape inicial
						if (isset($v['escapeCodeIni']) && trim($v['escapeCodeIni']) != '')
							$pcl .= constant($v['escapeCodeIni']);
						// Concatenar una cadena de texto al inicio del valor del campo
						if (isset($v['textIni']) && trim($v['textIni']) != '')
							$pcl .= $v['textIni'];
						
						// Dividir valor del campo
						if (isset($v['explode']) && strlen($v['explode']) > 0) {
							// Dividir valor de campo en piezas usando el separador 'explode'
							$pieces = explode($v['explode'], trim($campo));
							// Número de piezas por fila, predeterminado: 10 piezas
							$piecesPerRow = isset($v['piecesPerRow']) && intval($v['piecesPerRow']) > 0 ? intval($v['piecesPerRow']) : 10;
							// Ajuste vertical para desplazamiento
							$rowOffset = isset($v['rowOffset']) && round(floatval($v['rowOffset']), 2) > 0 ? round(floatval($v['rowOffset']), 2) : 4.00;
							// Contador de piezas
							$cont = 1;
							
							if ($pieces)
								foreach ($pieces as $piece) {
									$pcl .= ($cont > 1 ? '  ' : '') . $piece;
									
									$cont++;
									
									if ($cont > $piecesPerRow) {
										$pcl .= MoveCursorH($rowOffset);
										$pcl .= MoveCursorV($rowOffset, TRUE);
										
										$cont = 1;
									}
								}
						}
						// Concatenar valor de campo
						else
							$pcl .= $campo;
						
						// Concatenar una cadena de texto al final del valor del campo
						if (isset($v['textEnd']) && trim($v['textEnd']) != '')
							$pcl .= $v['textEnd'];
						// Aplicar código de escape final
						if (isset($v['escapeCodeEnd']) && trim($v['escapeCodeEnd']) != '')
							$pcl .= constant($v['escapeCodeEnd']);
					}
				break;
				
				// Leyenda
				case 'Leyenda':
					$pcl .= $v['data'];
				break;
				
				// Concatenar el valor de una constante
				case 'Constant':
					$pcl .= constant($v['data']);
				break;
				
				// Tamaño de página
				case 'PageSize':
					$pcl .= SetPageSize(constant($v['data']));
				break;
				
				// Ancho de página
				case 'PageWidth':
					$pcl .= SetUniversalWidth(intval($v['data']));
				break;
				
				// Alto de página
				case 'PageHeight':
					$pcl .= SetUniversalHeight(intval($v['data']));
				break;
				
				// Margen superior de la página
				case 'TopMargin':
					$pcl .= SetTopMargin(intval($v['data']));
				break;
				
				// Margen izquierdo de la página
				case 'LeftMargin':
					$pcl .= SetLeftMargin(intval($v['data']));
				break;
				
				// Movimiento horizontal
				case 'X':
					$pcl .= MoveCursorH(round(floatval($v['data']), 2));
				break;
				
				// Movimiento vertical
				case 'Y':
					$pcl .= MoveCursorV(round(floatval($v['data']), 2));
				break;
				
				// [15-May-2009] Tipo de fuente
				case 'FontFace':
					if (isset($v['symbolID']))
						$pcl .= SetSymbolSet($v['symbolID']);
					$pcl .= SetFontTypeFace(intval($v['data']));
				break;
				
				// Peso de fuente
				case 'FontWeight':
					$pcl .= SetFontStrokeWeight(constant($v['data']));
				break;
				
				// Tamaño de fuente
				case 'FontPointSize':
					$pcl .= SetFontPointSize(round(floatval($v['data']), 2));
				break;
				
				// [11-Feb-2010] Caracteres por pulgada
				case 'FontPitch':
					$pcl .= SetFontPitch(round(floatval($v['data']), 2));
				break;
				
				// [07-Ene-2009] Inicio de modo MICR (solo impresora de Zapaterias Elite)
				case 'modoMICR':
					$pcl .= strIni;
				break;
				
				// [15-May-2009] Incluir desglose de pagos
				case 'Desglose':
					$pcl .= desglosePagos($data['num_cia'], $data['clave_banco'], $data['folio']);
				break;
				
				// [22-Dic-2009] Definir macro
				case 'DefineMacro':
					$pcl .= MacroLogo($v['data'], $v['file']);
				break;
				
				// [22-Dic-2009] Ejecutar macro
				case 'CallMacro':
					$pcl .= CallMacro(intval($v['data']));
				break;
			}
		}
		else if (is_array($v))
			$pcl .= LayoutToPCL($v, $data);
	
	return $pcl;
}

function MacroLogo($id, $file) {
	$pcl = SetMacroID(intval($id));
	$pcl .= StartMacroDefinition();
	shell_exec("chmod ugo=rwx pcl");
	$pcl .= file_get_contents($file);
	shell_exec("chmod ugo=r pcl");
	$pcl .= EndMacroDefinition();
	$pcl .= MakeMacroIDPermanent();
	
	return $pcl;
}

function chequePCL($data, $cuenta = NULL, $layout) {
	global $datosBanco;
	
	$pcl = '';
	
	if ($cuenta != NULL) {
		$data['bandaMICR']   = bandaMICR($datosBanco[$cuenta]['numBanco'], $data['cuenta'], $data['folio'], $datosBanco[$cuenta]['codSeguridad'], $datosBanco[$cuenta]['claveTransaccion'], $datosBanco[$cuenta]['plazaCompensacion']);
		$data['pseudoBanda'] = pseudoBanda($data['bandaMICR'], $data['importe']);
	}
	
	$pcl .= LayoutToPCL($layout, $data);
	
	return $pcl;
}

function desglosePagos($num_cia, $cuenta, $folio) {
	$sql = '
		SELECT
			num_fact,
			importe - faltantes - dev AS importe,
			faltantes,
			dev,
			desc1 + desc2 + desc3 + desc4 AS desc,
			iva,
			ivaret
				AS
					ret,
			isr,
			fletes,
			otros,
			total
		FROM
			facturas_zap
				fz
		WHERE
				fz.num_cia = ' . $num_cia . '
			AND
				fz.cuenta = ' . $cuenta . '
			AND
				fz.folio = ' . $folio . '
		ORDER BY
			num_fact,
			sucursal
				DESC
	';
	$result = $GLOBALS['db']->query($sql);
	
	if (!$result)
		return;
	
	$xml = new XMLClass('XML/DesgloseElite.xml', 'file');
	$xml->parse();
	
	$pcl = LayoutToPCL($xml->data['DesglosePagos']['Titulos']);
	
	if ($result)
		foreach ($result as $i => $r) {
			$pcl .= MoveCursorV(135 + $xml->data['DesglosePagos']['Datos']['incrementoV'] * ($i + 1));
			$pcl .= chequePCL($r, NULL, $xml->data['DesglosePagos']['Datos']);
		}
	
	return $pcl;
}

if ((isset($_POST['id']) && count($_POST['id']) > 0) || (isset($_GET['polizas']) && $_GET['polizas'] > 0)) {
	if (isset($_GET['polizas'])) {
		$sql = '
			SELECT
				id,
				c.num_cia,
				cc.rfc
					AS
						rfc,
				cc.' . ($_GET['polizas'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
					AS
						cuenta,
				c.cuenta
					AS
						clave_banco,
				cc.nombre
					AS
						nombre_cia,
				c.num_proveedor
					AS
						num_proveedor,
				fecha,
				extract
					(
						day
							from
								fecha
					)
						AS
							dia,
				extract
					(
						month
							from
								fecha
					)
						AS
							mes,
				extract
					(
						year
							from
								fecha
					)
						AS
							anio,
				folio,
				a_nombre,
				concepto,
				importe,
				facturas,
				para_abono,
				lpad
					(
						c.num_cia,
						4,
						\'0\'
					)
						||
							lpad
								(
									c.cuenta,
									2,
									\'0\'
								)
									||
										lpad
											(
												c.folio,
												6,
												\'0\'
											)
												AS
													barcode,
				CASE
					WHEN codgastos = 134 THEN
						\'t\'
					ELSE
						\'f\'
				END
					AS
						nomina
			FROM
					cheques
						c
				LEFT JOIN
					catalogo_companias
						cc
							ON
								(
									cc.num_cia = c.num_cia
								)
				LEFT JOIN
					catalogo_proveedores
						cp
							ON
								(
									cp.num_proveedor = c.num_proveedor
								)
			WHERE
					c.num_cia
						BETWEEN
								' . ($_SESSION['iduser'] >= 28 ? 900 : 1) . '
							AND
								' . ($_SESSION['iduser'] >= 28 ? 998 : 899) . '
				AND
					imp = \'FALSE\'
				AND
					c.cuenta = ' . $_GET['polizas'] . '
				AND
					fecha_cancelacion IS NULL
				AND
					(
							poliza = \'TRUE\'
						OR
							(
									codgastos
										IN
											(
												140,
												141
											)
								AND
									poliza = \'TRUE\'
							)
					)
			ORDER BY
				a_nombre,
				c.num_cia,
				folio
					ASC
		';
	}
	else {
		$sql = '
			SELECT
				id,
				num_cia,
				cc.rfc
					AS
						rfc,
				cc.' . ($_POST['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
					AS
						cuenta,
				c.cuenta
					AS
						clave_banco,
				cc.nombre
					AS
						nombre_cia,
				c.num_proveedor
					AS
						num_proveedor,
				fecha,
				extract
					(
						day
							from
								fecha
					)
						AS
							dia,
				extract
					(
						month
							from
								fecha
					)
						AS
							mes,
				extract
					(
						year
							from
								fecha
					)
						AS
							anio,
				folio,
				a_nombre,
				concepto,
				importe,
				facturas,
				para_abono,
				lpad
					(
						c.num_cia,
						4,
						\'0\'
					)
						||
							lpad
								(
									c.cuenta,
									2,
									\'0\'
								)
									||
										lpad
											(
												c.folio,
												6,
												\'0\'
											)
												AS
													barcode,
				CASE
					WHEN codgastos = 134 THEN
						\'t\'
					ELSE
						\'f\'
				END
					AS
						nomina
			FROM
					cheques
						c
				LEFT JOIN
					catalogo_companias
						cc
							USING
								(
									num_cia
								)
				LEFT JOIN
					catalogo_proveedores
						cp
							ON
								(
									cp.num_proveedor = c.num_proveedor
								)
			WHERE
				id
					IN
						(
							' . implode(', ', $_POST['id']) . '
						)
			ORDER BY
				a_nombre,
				num_cia,
				folio
					ASC
		';
	}
	
	$result = $db->query($sql);
	
	$orden = isset($_POST['orden']) ? get_val($_POST['orden']) : NULL;
	
	if (isset($_POST['cuenta']))
		$cuenta = $_POST['cuenta'];
	else if (isset($_GET['polizas']))
		$cuenta = $_GET['polizas'];
	
	/*
	@@@
	@
	@ 1 = Mollendo
	@ 2 = Elite
	@
	*/
	$oficina = $_SESSION['iduser'] >= 28 ? 2 : 1;
	
	$folio = isset($_POST['folio']) && get_val($_POST['folio']) > 0 ? get_val($_POST['folio']) : NULL;
	$num_cheque_ini = $folio;
	$num_cheque_fin = $folio + count($result) - 1;
	$num_cheque = $orden < 0 ? $num_cheque_fin : $num_cheque_ini;
	
	$tipo = $folio > 0 ? 'cheques' : 'polizas';
	
	$xml = new XMLClass($layout[$oficina][$tipo][$cuenta], 'file');
	$xml->parse();
	
	$cheques = array();
	
	$nomina = FALSE;
	
	$pcl = HEADER;
	$pcl .= LayoutToPCL($xml->data['Documento']['Cabecera']);
	foreach ($result as $i => $reg) {
		// [16-Mar-2010] Si es nómina poner bandera a TRUE
		if ($reg['nomina'] == 't') {
			$nomina = TRUE;
		}
		
		// Anexar al registro los datos del banco
		$reg = $reg + $datosBanco[$cuenta];
		
		if ($folio > 0) {
			$pcl .= chequePCL($reg, $cuenta, $xml->data['Documento']['Poliza']);
			$pcl .= chequePCL($reg, $cuenta, $xml->data['Documento']['Cheque']);
			$pcl .= $i < count($result) - 1 ? FORM_FEED : '';
			
			$cheques[] = array(
				'num_cheque' => $num_cheque,
				'id'         => $reg['id']
			);
			
			$num_cheque += $orden;
		}
		else {
			$pcl .= chequePCL($reg, $cuenta, $xml->data['Documento']['PseudoPoliza']);
			$pcl .= $i < count($result) - 1 ? FORM_FEED : '';
			
			$cheques[] = array(
				'num_cheque' => 'NULL',
				'id'         => $reg['id']
			);
		}
	}
	$pcl .= RESET;
	
	$sql = '
		INSERT INTO
			estatus_cheques
				(
					oficina,
					cuenta,
					folio_inicial,
					folio_final,
					orden,
					estatus,
					iduser,
					tsmod
				)
			VALUES
				(
					' . $oficina . ',
					' . $cuenta . ',
					' . ($num_cheque_ini > 0 ? $num_cheque_ini : 'NULL') . ',
					' . ($num_cheque_fin > 0 ? $num_cheque_fin : 'NULL') . ',
					' . ($orden != 0 ? $orden : 'NULL') . ',
					' . ($folio > 0 || $nomina ? '0' : '1') . ',
					' . $_SESSION['iduser'] . ',
					now()
				);' . "\n";
	
	foreach ($cheques as $cheque) {
		$sql .= '
			UPDATE
				cheques
			SET
				imp = \'TRUE\',
				archivo = \'' . ($folio > 0 ? 'TRUE' : 'FALSE') . '\',
				num_cheque = ' . $cheque['num_cheque']. ',
				idstatus = currval(\'estatus_cheques_id_seq\')
			WHERE
				id = ' . $cheque['id'] . ";\n";
	}
	
	/*if ($_SESSION['iduser'] != 1) */$db->query($sql);
	
	shell_exec("chmod ugo=rwx pcl");
	if ($_SESSION['iduser'] >= 28) {
		$fp = fopen('pcl/Elite' . ($folio > 0 ? 'Cheques' : 'Polizas') . '.pcl', 'w');
		fwrite($fp, $pcl);
		fclose($fp);
		shell_exec('lp -d elite pcl/Elite' . ($folio > 0 ? 'Cheques' : 'Polizas') . '.pcl');
	}
	else {
		$fp = fopen('pcl/Mollendo' . ($folio > 0 ? 'Cheques' : 'Polizas') . '.pcl', 'w');
		fwrite($fp, $pcl);
		fclose($fp);
		shell_exec('lp -d ' . ($folio > 0 ? 'T622' : 'S1855'/*'polizas'*/) . ' pcl/Mollendo' . ($folio > 0 ? 'Cheques' : 'Polizas') . '.pcl');
	}
	shell_exec("chmod ugo=r pcl");
	
	die(header('location: ./ImpresionCheques.php'));
}

if (isset($_POST['folio_ini'])) {
	if ($_POST['orden'] == 'ASCENDENTE') {
		// Acomodar folios
		$folios = array_filter($_POST['folio']);
		rsort($folios);
		
		$rango = $_POST['folio_fin'] - $_POST['folio_ini'] + 1;
		
		$sql = '
			UPDATE
				cheques
			SET
				num_cheque = num_cheque + ' . $rango . '
			WHERE
				num_cheque
					BETWEEN
							' . $_POST['folio_ini'] . '
						AND
							' . ($_POST['folio_ini'] + count($folios) - 1) . '
		' . ";\n";
		
		foreach ($folios as $f)
			$sql .= '
				UPDATE
					cheques
				SET
					num_cheque = num_cheque - 1
				WHERE
					num_cheque
						BETWEEN
								' . $_POST['folio_ini'] . '
							AND
								' . $f . '
			' . ";\n";
		
		$sql .= '
			UPDATE
				estatus_cheques
			SET
				folio_final = folio_final + ' . count($folios) . '
			WHERE
				id = ' . $_POST['idstatus'] . '
		' . ";\n";
	}
	else {
		// Acomodar folios
		$folios = array_filter($_POST['folio']);
		sort($folios);
		
		$rango = $_POST['folio_fin'] - $_POST['folio_ini'] + 1;
		
		$sql = '
			UPDATE
				cheques
			SET
				num_cheque = num_cheque + ' . count($folios) . '
			WHERE
				num_cheque
					BETWEEN
							' . ($_POST['folio_fin'] - count($folios) + 1) . '
						AND
							' . $_POST['folio_fin'] . '
		' . ";\n";
		
		foreach ($folios as $f)
			$sql .= '
				UPDATE
					cheques
				SET
					num_cheque = num_cheque + 1
				WHERE
					num_cheque
						BETWEEN
								' . $f . '
							AND
								' . $_POST['folio_fin'] . '
			' . ";\n";
		
		$sql .= '
			UPDATE
				estatus_cheques
			SET
				folio_final = folio_final + ' . count($folios) . '
			WHERE
				id = ' . $_POST['idstatus'] . '
		' . ";\n";
	}
	
	$db->query($sql);
	
	header('location: ImpresionCheques.php');
	die;
}

$tpl = new TemplatePower('plantillas/ban/ImpresionCheques.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

if (isset($_GET['idstatus'])) {
	$sql = '
		SELECT
			id,
			folio_inicial
				AS
					folio_ini,
			folio_final
				AS
					folio_fin,
			CASE
				WHEN cuenta = 1 THEN
					\'BANORTE\'
				ELSE
					\'SANTANDER\'
			END
				AS 
					banco,
			CASE
				WHEN folio_inicial IS NOT NULL THEN
					folio_inicial || \' AL \' || folio_final
				ELSE
					\'POLIZAS\'
			END
				AS
					rango,
			CASE
				WHEN orden < 0 THEN
					\'ASCENDENTE\'
				WHEN orden > 0 THEN
					\'DESCENDENTE\'
				ELSE
					\'&nbsp;\'
			END
				AS
					orden,
			nombre
				AS
					usuario,
			date_trunc(\'second\', tsmod)
				AS
					ts
		FROM
				estatus_cheques
			LEFT JOIN
				auth USING
					(
						iduser
					)
		WHERE
			id = ' . $_GET['idstatus'];
	$estatus = $db->query($sql);
	
	if (isset($_GET['accion'])) {
		// Recorrer folios dentro del rango
		if ($_GET['accion'] == 'shift') {
			$tpl->newBlock('recorrer');
			
			foreach ($estatus[0] as $k => $v)
				$tpl->assign($k, $v);
			
			die($tpl->printToScreen());
		}
		// Reimprimir dentro del rango
		else if ($_GET['accion'] == 'print') {
			
		}
		else if ($_GET['accion'] == 'finish') {
			$sql = '
				UPDATE
					estatus_cheques
				SET
					estatus = 1
				WHERE
					id = ' . $_GET['idstatus'] . '
			';
			$db->query($sql);
			
			die(header('location: ImpresionCheques.php'));
		}
	}
	
	$tpl->newBlock('estatus');
	foreach ($estatus[0] as $k => $v)
		$tpl->assign($k, $v);
	
	// [16-Mar-2010] Buscar cheques de nomina para generar cartas
	$sql = '
		SELECT
			id
		FROM
			cheques
		WHERE
				idstatus = ' . $_GET['idstatus'] . '
			AND
				codgastos = 134
			AND
				fecha_cancelacion IS NULL
			AND
				importe > 0
			AND
				cuenta = 2
		LIMIT
			1
	';
	$result = $db->query($sql);
	
	if ($result) {
		$tpl->newBlock('cartas_nomina');
		$tpl->assign('id', $_GET['idstatus']);
	}
	
	die($tpl->printToScreen());
}

if (isset($_GET['cuenta'])) {
	$sql = '
		SELECT
			id,
			c.num_proveedor
				AS
					num_pro,
			a_nombre,
			num_cia,
			nombre_corto
				AS
					nombre_cia,
			cuenta,
			clabe_cuenta
				AS
					cuenta1,
			clabe_cuenta2
				AS
					cuenta2,
			fecha,
			folio,
			concepto,
			importe
		FROM
			cheques
				c
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
		WHERE
				num_cia
					BETWEEN
							' . ($_SESSION['iduser'] >= 28 ? 900 : 1) . '
						AND
							' . ($_SESSION['iduser'] >= 28 ? 998 : 899) . '
			AND
				imp = \'FALSE\'
			AND
				cuenta = ' . $_GET['cuenta'] . '
			AND
				poliza <> \'TRUE\'
			AND
				fecha_cancelacion
					IS NULL
			AND
				NOT
					(
							codgastos
								IN
									(
										140,
										141
									)
						AND
							poliza = \'TRUE\'
					)
		ORDER BY
			a_nombre,
			num_cia,
			folio
				ASC
	';
	$result = $db->query($sql);
	
	if (!$result) {
		die(header('location: ImpresionCheques.php'));
	}
	
	$tpl->newBlock('result');
	$tpl->assign('cuenta', $_GET['cuenta']);
	$tpl->assign('folio', $_GET['folio']);
	$tpl->assign('orden', $_GET['orden']);
	
	$pro = NULL;
	$block = -1;
	$gran_total = 0;
	foreach ($result as $i => $reg) {
		if ($pro != $reg['num_pro']) {
			$pro = $reg['num_pro'];
			
			$tpl->newBlock('pro');
			$tpl->assign('num_pro', $pro);
			$tpl->assign('nombre', $reg['a_nombre']);
			
			$tpl->assign('ini', $i);
			
			$total = 0;
			$block++;
			$color = FALSE;
		}
		$tpl->newBlock('fila');
		$tpl->assign('color', !$color ? 'linea_off' : 'linea_on');
		$color = !$color;
		$tpl->assign('block', $block);
		$tpl->assign('pro.fin', $i);
		
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('cuenta', $reg['cuenta' . $_GET['cuenta']]);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('concepto', $reg['concepto']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		
		$total += $reg['importe'];
		$gran_total += $reg['importe'];
		$tpl->assign('pro.total', number_format($total, 2, '.', ','));
	}
	$tpl->assign('result.gran_total', number_format($gran_total, 2, '.', ','));
	$tpl->assign('result.num_docs', number_format(count($result)));
	$tpl->assign('result.rango', $_GET['folio'] > 0 ? 'Inserte cheques en la impresora del folio ' . $_GET['folio'] . ' al ' . ($_GET['folio'] + count($result) - 1) : 'Inserte papel poliza en la impresora');
	
	die($tpl->printToScreen());
}

$sql = '
	SELECT
		id,
		cuenta,
		folio_inicial,
		folio_final,
		orden,
		nombre
	FROM
			estatus_cheques
		LEFT JOIN
			auth USING
				(
					iduser
				)
	WHERE
			estatus = 0
		AND
			iduser ' . ($_SESSION['iduser'] >= 28 ? '>= 28' : '< 28') . '
';
$estatus = $db->query($sql);

if ($estatus)
	die(header('location: ImpresionCheques.php?idstatus=' . $estatus[0]['id']));

// Pantalla principal
$tpl->newBlock('datos');

$sql = '
	SELECT
		cuenta
	FROM
		cheques
	WHERE
			num_cia
				BETWEEN
						' . ($_SESSION['iduser'] >= 28 ? 900 : 1) . '
					AND
						' . ($_SESSION['iduser'] >= 28 ? 998 : 899) . '
		AND
			imp = \'FALSE\'
		AND
			fecha_cancelacion IS NULL
		AND
			(
					poliza = \'TRUE\'
				OR
					(
							codgastos
								IN
									(
										140,
										141
									)
						AND
							poliza = \'TRUE\'
					)
			)
	GROUP BY
		cuenta
	ORDER BY
		cuenta
';
$polizas = $db->query($sql);

if ($polizas) {
	$tpl->newBlock('polizas');
	foreach ($polizas as $i => $p) {
		$tpl->newBlock('cuenta');
		$tpl->assign('cuenta', $p['cuenta']);
		$tpl->assign('banco', $p['cuenta'] == 1 ? 'Banorte' : 'Santander');
		if ($i > 0)
			$tpl->assign('nbsp', '&nbsp;&nbsp;');
	}
}

$tpl->printToScreen();
?>
