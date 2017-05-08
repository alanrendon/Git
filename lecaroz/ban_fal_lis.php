<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

if (isset($_REQUEST['c'])) {
	$sql = '
		SELECT
			nombre_corto
		FROM
			catalogo_companias
		WHERE
				num_cia
					BETWEEN
						' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			AND
				num_cia = ' . $_REQUEST['c'] . '
	';
	$result = $db->query($sql);

	if ($result)
		echo $result[0]['nombre_corto'];

	die;
}

function suma($val) {
	$sum = 0;

	if (is_int($val)) {
		for ($i = 1; $i <= $val; $i++)
			$sum += $i;
	}
	else if (is_array($val)) {
		foreach ($val as $v)
			$sum += $v['semana'];
	}

	return $sum;
}

if (isset($_REQUEST['email'])) {
	$dia = date("j");
	$mes = date("n");
	$anio = date("Y");

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes, $dia, $anio));

	$sql = '
		SELECT
			num_cia_primaria,
			num_cia,
			nombre,
			nombre_corto,
			(
				SELECT
					email
				FROM
					catalogo_companias
				WHERE
					num_cia = cc.num_cia_primaria
			)
				AS email,
			fecha,
			deposito,
			importe,
			tipo,
			descripcion,
			importe_comprobante,
			idadministrador
		FROM
			faltantes_cometra fc
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			' . ($_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : 'num_cia BETWEEN 1 AND 899') . '
			AND fecha_con IS NULL
			AND fecha < \'19-11-2014\'
			' . ($_GET['num_cia'] > 0 ? 'AND num_cia = ' . $_GET[num_cia] : '') . '
		ORDER BY
			num_cia_primaria,
			num_cia,
			fecha
	';

	$result = $db->query($sql);

	if ($result) {
		include_once('includes/phpmailer/class.phpmailer.php');

		$num_cia_primaria = NULL;

		foreach ($result as $rec) {
			if ($num_cia_primaria != $rec['num_cia_primaria']) {
				if ($num_cia_primaria != NULL && trim($email) != '') {
					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';

					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

					$mail->AddAddress($email);

					if ($mails) {
						foreach ($mails as $m) {
							$mail->AddBCC($m);
						}
					}

					$mail->Subject = utf8_decode('FALTANTES DE COMETRA');

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					@$mail->Send();
				}

				$num_cia_primaria = $rec['num_cia_primaria'];
				$email = $rec['email'];

				$emails = array();

				if ($rec['idadministrador'] == 13) {
					$emails[] = 'ilarracheai@hotmail.com';
					$emails[] = 'jmjuan68@hotmail.com';
					$emails[] = 'miguelrebuelta@lecaroz.com';
				}

				$tpl = new TemplatePower('plantillas/ban/FaltantesCometraEmail.tpl');
				$tpl->prepare();

				$tpl->assign('fecha', date('d/m/Y H:i'));

				$num_cia = NULL;
			}

			if ($num_cia != $rec['num_cia']) {
				$num_cia = $rec['num_cia'];

				$tpl->newBlock('cia');

				$tpl->assign('num_cia', $num_cia);
				$tpl->assign('nombre_cia', utf8_decode($rec['nombre_corto']));

				$importe_comprobante = 0;
				$deposito = 0;
				$faltante = 0;
				$sobrante = 0;
			}

			$tpl->newBlock('row');
			$tpl->assign('fecha', $rec['fecha']);
			$tpl->assign('comprobante', $rec['importe_comprobante'] != 0 ? number_format($rec['importe_comprobante'], 2) : '&nbsp;');
			$tpl->assign('deposito', $rec['deposito'] != 0 ? number_format($rec['deposito'], 2) : '&nbsp;');
			$tpl->assign('faltante', $rec['tipo'] == 'f' ? number_format($rec['importe'], 2) : '&nbsp;');
			$tpl->assign('sobrante', $rec['tipo'] == 't' ? number_format($rec['importe'], 2) : '&nbsp;');
			$tpl->assign('concepto', $rec['descripcion'] != '' ? utf8_encode($rec['descripcion']) : '&nbsp;');

			$importe_comprobante += $rec['importe_comprobante'];
			$deposito += $rec['deposito'];
			$faltante += $rec['tipo'] == 'f' ? $rec['importe'] : 0;
			$sobrante += $rec['tipo'] == 't' ? $rec['importe'] : 0;

			$tpl->assign('cia.importe_comprobante', number_format($importe_comprobante, 2));
			$tpl->assign('cia.deposito', number_format($deposito, 2));
			$tpl->assign('cia.faltante', number_format($faltante, 2));
			$tpl->assign('cia.sobrante', number_format($sobrante, 2));
			$tpl->assign('cia.diferencia', number_format($faltante - $sobrante, 2));
			$tpl->assign('cia.tipo', $faltante - $sobrante > 0 ? 'FALTANTE' : 'SOBRANTE');
		}

		if ($num_cia_primaria != NULL && trim($email) != '') {
			$mail = new PHPMailer();

			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'mollendo+lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

			$mail->AddAddress($email);

			if ($mails) {
				foreach ($mails as $m) {
					$mail->AddBCC($m);
				}
			}

			$mail->Subject = utf8_decode('FALTANTES DE COMETRA');

			$mail->Body = $tpl->getOutputContent();

			$mail->IsHTML(true);

			@$mail->Send();
		}
	}

	header('location: ban_fal_lis.php?mensaje=Se+han+enviado+los+correos');

	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_lis.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");

	$sql = "SELECT * FROM catalogo_administradores ORDER BY nombre_administrador";
	$admin = $db->query($sql);
	for ($i = 0; $i < count($admin); $i++) {
		$tpl->newBlock("administrador");
		$tpl->assign("idadministrador", $admin[$i]['idadministrador']);
		$tpl->assign("nombre", $admin[$i]['nombre_administrador']);
	}

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die;
}

$dia = date("j");
$mes = date("n");
$anio = date("Y");

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes, $dia, $anio));

$fecha_con = $dia > 4 ? date("1/m/Y") : date("1/m/Y", mktime(0,0,0,$mes - 1,1,$anio));

if ($_GET['tipo'] == "todas" && $_GET['num_cia'] == '') {
	$sql = "SELECT num_cia, nombre, nombre_corto, fecha, deposito, importe, tipo, descripcion, importe_comprobante FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE" . ($_SESSION['tipo_usuario'] == 2 ? " num_cia BETWEEN 900 AND 998 AND" : " num_cia BETWEEN 1 AND 899 AND") . " fecha_con IS NULL" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . " AND fecha < '19-11-2014' ORDER BY num_cia, fecha";
}
else if ($_GET['tipo'] == "admin" || $_GET['tipo'] == "memo" || ($_GET['tipo'] == 'todas' && $_GET['num_cia'] > 0)) {
	$sql = "SELECT num_cia, nombre, nombre_corto, fecha, deposito, importe, tipo, descripcion, idadministrador, nombre_administrador, comprobante, importe_comprobante FROM faltantes_cometra LEFT JOIN catalogo_companias LEFT JOIN catalogo_administradores USING (idadministrador) USING (num_cia) WHERE" . ($_SESSION['tipo_usuario'] == 2 ? " num_cia BETWEEN 900 AND 998 AND" : " num_cia BETWEEN 1 AND 899 AND") . " fecha_con IS NULL AND fecha < '19-11-2014'";
	$sql .= isset($_GET['idadministrador']) && $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " ORDER BY idadministrador, num_cia, fecha";
}
else if ($_GET['tipo'] == "aclarados") {
	$sql = "SELECT num_cia, nombre_corto, fecha, deposito, importe, tipo, descripcion, importe_comprobante, idadministrador, nombre_administrador FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE" . ($_SESSION['tipo_usuario'] == 2 ? " num_cia BETWEEN 900 AND 998 AND" : " num_cia BETWEEN 1 AND 899 AND") . " fecha_con >= CURRENT_DATE - interval '5 months' AND fecha < '19-11-2014'";
	$sql .= isset($_GET['idadministrador']) && $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " ORDER BY num_cia, fecha";
}
$result = $db->query($sql);

if ($result) {
	if ($_GET['tipo'] != "memo") {
		if ($_GET['tipo'] != "admin" && $_GET['num_cia'] == '') {
			$tpl->newBlock("listado");
			$tpl->assign("fecha", date("d/m/Y"));
		}

		$num_cia = NULL;
		$admin = NULL;
		$count = 0;
		for ($i = 0; $i < count($result); $i++) {
			if (($_GET['tipo'] == "admin" && $admin != $result[$i]['nombre_administrador']) || (($_GET['tipo'] == 'todas' || $_GET['tipo'] == 'aclarados') && $_GET['num_cia'] > 0 && $admin != $result[$i]['nombre_administrador'])) {
				if ($admin != NULL) {
					/*if ($num_cia != NULL && $count > 1) {
						$tpl->newBlock("totales");
						$tpl->assign("deposito", number_format($deposito,2,".",","));
						$tpl->assign("faltante", number_format($faltante,2,".",","));
						$tpl->assign("sobrante", number_format($sobrante,2,".",","));
					}*/
					$tpl->newBlock("mensaje");
					$tpl->assign("dias_retraso", ceil($dias_retraso));
					$tpl->newBlock("salto");
				}

				$admin = $result[$i]['nombre_administrador'];

				$tpl->newBlock("listado");
				/*$tpl->assign("dia", $dia);
				$tpl->assign("mes", mes_escrito($mes));
				$tpl->assign("anio", $anio);*/
				$tpl->assign("fecha", date("d/m/Y"));


				// Block de pasteles terminados
				$sql = "SELECT num_cia, nombre_corto, count(bloc.estado) AS blocks FROM bloc LEFT JOIN catalogo_companias ON (num_cia = idcia) WHERE idadministrador = {$result[$i]['idadministrador']} AND bloc.estado = 'TRUE'" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '') . " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
				$blocks = $db->query($sql);

				if ($blocks) {
					$tpl->newBlock('pasteles');
					foreach ($blocks as $block) {
						$tpl->newBlock('block');
						$tpl->assign('num_cia', $block['num_cia']);
						$tpl->assign('nombre', $block['nombre_corto']);
						$tpl->assign('blocks', $block['blocks']);

						// [14-May-2009] Obetner folios iniciales de los blocks terminados
						$sql = "SELECT (CASE WHEN upper(let_folio) = 'X' OR let_folio IS NULL THEN '' ELSE let_folio END) || folio_inicio AS folio FROM bloc WHERE idcia = $block[num_cia] AND estado = 'TRUE' ORDER BY folio";
						$tmp = $db->query($sql);
						$blocks_cia = array();
						if ($tmp)
							foreach ($tmp as $t)
								$blocks_cia[] = $t['folio'];
						$tpl->assign('folios', count($blocks_cia) > 0 ? implode(', ', $blocks_cia) : '&nbsp;');
					}
				}

				// [04-Feb-2009] Nominas pendientes
				$nom_week = date('W') > 1 ? date('W') - 1 : 52;
				$nom_year = date('W') > 1 ? date('Y') : (date('n') == 12 ? date('Y') : date('Y') - 1);

				$sql = "SELECT * FROM (SELECT num_cia, nombre_corto, (SELECT count(semana) FROM nominas WHERE num_cia = c.num_cia AND anio = $nom_year) AS semanas FROM catalogo_companias c WHERE idadministrador = {$result[$i]['idadministrador']} AND (num_cia <= 300 OR num_cia IN (303, 318, 329, 335, 339, 350, 700, 702, 704, 800))" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '') . ") result WHERE semanas < $nom_week ORDER BY num_cia";
				$nominas = $db->query($sql);

				if ($nominas) {
					$tpl->newBlock('nominas');
					foreach ($nominas as $nom) {
						$tpl->newBlock('nom');
						$tpl->assign('num_cia', $nom['num_cia']);
						$tpl->assign('nombre', $nom['nombre_corto']);

						if ($nom['semanas'] == 0)
							$tpl->assign('nominas', 'NO HA ENTREGADO NOMINAS');
						else {
							$sql = "SELECT semana FROM nominas WHERE num_cia = $nom[num_cia] AND semana <= $nom_week AND anio = $nom_year";
							$tmp = $db->query($sql);

							$noms = array();
							foreach ($tmp as $t)
								$noms[$t['semana']] = $t;

							$data = '';
							for ($s = 1; $s <= $nom_week; $s++)
								if (!isset($noms[$s]))
									$data .= $s . '&nbsp;&nbsp;';

							$tpl->assign('nominas', $data);
						}
					}
				}

				// [05-Feb-2009] Pendientes de Infonavit
				$sql = "SELECT pen.num_cia, cc.nombre_corto AS nombre_cia, num_emp, ct.nombre, ap_paterno, ap_materno, mes, anio, importe FROM infonavit_pendientes pen LEFT JOIN catalogo_trabajadores ct ON (ct.id = pen.id_emp) LEFT JOIN catalogo_companias cc ON (cc.num_cia = pen.num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE pen.status = 0 AND cc.idadministrador = {$result[$i]['idadministrador']}" . ($_GET['num_cia'] > 0 ? " AND ct.num_cia = $_GET[num_cia]" : '') . " ORDER BY num_cia, nombre, mes";
				$infonavit = $db->query($sql);

				if ($infonavit) {
					// Acomodar registros por compañía, empleado, fecha, importe
					$data = array();
					foreach ($infonavit as $inf) {
						$data[$inf['num_cia']]['nombre'] = $inf['nombre_cia'];
						$data[$inf['num_cia']]['empleados'][$inf['num_emp']]['nombre'] = "$inf[nombre] $inf[ap_paterno] $inf[ap_materno]";
						$data[$inf['num_cia']]['empleados'][$inf['num_emp']]['pendientes'][$inf['anio']][$inf['mes']] = $inf['importe'];
					}

					$tpl->newBlock('infonavit');

					// Obtener número de columnas y sus títulos
					$sql = "SELECT anio, mes FROM infonavit_pendientes pen LEFT JOIN catalogo_trabajadores ct ON (ct.id = pen.id_emp) LEFT JOIN catalogo_companias cc ON (cc.num_cia = pen.num_cia) WHERE pen.status = 0 AND idadministrador = {$result[$i]['idadministrador']}" . ($_GET['num_cia'] > 0 ? " AND ct.num_cia = $_GET[num_cia]" : '') . " GROUP BY anio, mes ORDER BY anio, mes";
					$titulos = $db->query($sql);
					$tpl->assign('colspan_total', count($titulos) + 1);

					$total_admin = 0;

					foreach ($data as $cia_inf => $cia) {
						$tpl->newBlock('cia_inf');
						$tpl->assign('num_cia', $cia_inf);
						$tpl->assign('nombre', $cia['nombre']);
						$tpl->assign('colspan', count($titulos) + 2);
						$tpl->assign('infonavit.colspan', count($titulos) + 2);
						$tpl->assign('colspan_total', count($titulos) + 1);
						$total_cia = 0;

						// Trazar títulos
						foreach ($titulos as $t) {
							$tpl->newBlock('column_name');
							$tpl->assign('mes', substr(mes_escrito($t['mes']), 0, 3) . substr($t['anio'], 2, 2));
						}
						$tpl->newBlock('column_name');
						$tpl->assign('mes', 'Total');

						// Trazar empleados
						foreach ($cia['empleados'] as $num => $emp) {
							$tpl->newBlock('row');
							$tpl->assign('num', $num);
							$tpl->assign('nombre', $emp['nombre']);

							// Trazar pendientes
							$total_emp = 0;
							foreach ($titulos as $t) {
								$tpl->newBlock('cel');
								$tpl->assign('importe', isset($emp['pendientes'][$t['anio']][$t['mes']]) ? number_format($emp['pendientes'][$t['anio']][$t['mes']], 2, '.', ',') : '&nbsp;');
								$total_emp += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
								$total_cia += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
								$total_admin += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
								$tpl->assign('cia_inf.total', number_format($total_cia, 2, '.', ','));
								$tpl->assign('infonavit.total', number_format($total_admin, 2, '.', ','));
							}
							$tpl->newBlock('cel');
							$tpl->assign('importe', '<span style="font-weight:bold;">' . number_format($total_emp, 2, '.', ',') . '</span>');
						}
					}
				}

				// [20-Abr-2009] Depósitos menores al 60% del promedio mensual
				$sql = 'SELECT num_cia, nombre_corto FROM catalogo_companias WHERE idadministrador = ' . $result[$i]['idadministrador'] . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '') . ' AND num_cia <= 300 ORDER BY num_cia';
				$dep_min_cias = $db->query($sql);
				if ($dep_min_cias) {
					$datos = array();
					$promedios = array();
					$status_dia = array();
					$cont = 0;

					foreach ($dep_min_cias as $c) {
						// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
						if ($c['num_cia'] <= 300)
							$sql = "SELECT num_cia, efectivo, fecha, CASE WHEN efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE' THEN 't' ELSE 'f' END AS status FROM total_panaderias WHERE num_cia = $c[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia <= 300 ORDER BY fecha";
						else if (($c['num_cia'] > 300 && $c['num_cia'] < 600)/* || in_array($c['num_cia'], array(702, 704, 705))*/)
							$sql = "SELECT num_cia, efectivo, fecha, 't' AS status FROM total_companias WHERE num_cia = $c[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia BETWEEN 301 AND 599 ORDER BY fecha";
						else if ($c['num_cia'] >= 900 && $c['num_cia'] <= 998)
							$sql = "SELECT num_cia, efectivo, fecha, 't' AS status FROM total_zapaterias WHERE num_cia = $c[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
						$efectivo = $db->query($sql);

						if ($efectivo) {
							// Obtener el total de otros depósitos del mes
							$sql = "SELECT sum(importe) FROM otros_depositos WHERE num_cia = $c[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
							$tmp = $db->query($sql);
							$otros_depositos = ($tmp[0]['sum'] != 0) ? $tmp[0]['sum'] : 0;

							$total_depositos = 0;

							foreach ($efectivo as $e) {
								// Buscar los depositos para x día
								$sql = "SELECT sum(importe) AS importe, fecha_con FROM estado_cuenta WHERE num_cia = $c[num_cia] AND fecha = '$e[fecha]' AND cod_mov IN (1, 16, 44, 99) GROUP BY fecha_con ORDER BY importe DESC";
								$deposito_e = $db->query($sql);

								// Desglozar fecha
								ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $e['fecha'], $fecha);
								$dia = $fecha[1];

								// Almacenar estado del efectivo
								$status_dia[$c['num_cia']][$dia] = $e['status'] == "t" ? TRUE : FALSE;

								$datos[$cont]['num_cia'] = $c['num_cia'];
								$datos[$cont]['nombre'] = $c['nombre_corto'];
								$datos[$cont]['dia'] = $dia;
								$datos[$cont]['efectivo'] = $e['efectivo'];

								// Si hay depositos
								$depositos_total = 0;
								$mayoreo_total = 0;
								if ($deposito_e) {
									$datos[$cont]['deposito'] = $deposito_e[0]['importe'];
									$depositos_total += $deposito_e[0]['importe'];
									$total_depositos += $deposito_e[0]['importe'];

									if (count($deposito_e) > 1) {
										$mayoreo = 0;
										for ($j = 1; $j < count($deposito_e); $j++)
											$mayoreo += $deposito_e[$j]['importe'];
										$datos[$cont]['mayoreo'] = $mayoreo;
										$depositos_total += $mayoreo;
									}
									else
										$datos[$cont]['mayoreo'] = 0;
								}
								else {
									$datos[$cont]['deposito'] = 0;
									$datos[$cont]['mayoreo'] = 0;
								}

								// Diferencia de efectivo contra depositos
								$diferencia = $e['efectivo'] - $depositos_total;
								// Si la diferencia es mayor a 0, tomar de otros depósitos la diferencia para compensar
								$otro_deposito = 0;
								if ($diferencia > 0) {
									// Si los depósitos cubren la diferencia y no se ha llegado al último día...
									if (($otros_depositos - $diferencia) > 0 && $i < count($efectivo) - 1) {
										// Si no es el último efectivo, hacer la diferencia, si lo es, asignar el resto de depósitos
										if ($i < count($efectivo) - 1) {
											$otro_deposito = $diferencia;
											$otros_depositos -= $diferencia;
										}
										else {
											$otro_deposito = $otros_depositos;
											$otros_depositos = 0;
										}
									}
									else {
										$otro_deposito = $otros_depositos;
										$otros_depositos = 0;
									}

									$datos[$cont]['oficina'] = $otro_deposito;
								}
								else if ($i == count($efectivo) - 1) {
									$otro_deposito = $otros_depositos;
									$otros_depositos = 0;
									$datos[$cont]['oficina'] = $otro_deposito;
								}
								else
									$datos[$cont]['oficina'] = 0;

								// Mostrar diferencia
								$depositos_total += $otro_deposito;
								$datos[$cont]['diferencia'] = $e['efectivo'] - $depositos_total;
								$datos[$cont]['total_depositos'] = $depositos_total;

								$cont++;
							}

							// Calcular promedio
							$dias = count($efectivo);
							$promedios[$c['num_cia']] = $total_depositos / $dias;
						}
					}

					// Buscar movimientos por abajo de 60% del promedio de depositos diarios
					$dep_min = FALSE;
					foreach ($datos as $d)
						if ($status_dia[$d['num_cia']][$d['dia']] && ($d['deposito'] + $d['mayoreo']) > 0 && ($d['deposito'] + $d['mayoreo']) < $promedios[$d['num_cia']] * 0.60) {
							if (!$dep_min)
								$dep_min = array();
							$dep_min[] = $d;
						}

					if ($dep_min) {
						$tpl->newBlock('dep_min');

						$num_cia_e = NULL;
						foreach ($dep_min as $d) {
							if ($num_cia_e != $d['num_cia']) {
								$num_cia_e = $d['num_cia'];

								$tpl->newBlock("cia_dep_min");
								$tpl->assign('num_cia', $num_cia_e);
								$tpl->assign('nombre_cia', $d['nombre']);
								$tpl->assign('promedio', number_format($promedios[$d['num_cia']], 2, '.', ','));
							}
							$tpl->newBlock('dia_dep_min');
							$tpl->assign('dia', $d['dia']);
							$tpl->assign('efectivo', $d['efectivo'] != 0 ? number_format($d['efectivo'], 2, '.', ',') : '&nbsp;');
							$tpl->assign('deposito', $d['deposito'] != 0 ? number_format($d['deposito'], 2, '.', ',') : '&nbsp;');
						}
					}
				}


				$tpl->newBlock("encargado");
				$tpl->assign("encargado", $admin);

				$temp = $db->query("SELECT fecha FROM faltantes_cometra LEFT JOIN catalogo_companias LEFT JOIN catalogo_administradores USING (idadministrador) USING (num_cia) WHERE fecha_con IS NULL AND /*idadministrador = {$result[$i]['idadministrador']}*/num_cia = {$result[$i]['num_cia']} ORDER BY fecha LIMIT 1");
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $temp[0]['fecha'], $temp_fecha);
				$ts1 = mktime(0, 0, 0, $temp_fecha[2], $temp_fecha[1], $temp_fecha[3]);
				$ts2 = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
				$dias_retraso = ($ts2 - $ts1) / 86400;//echo "$ts2 - $ts1 = " . ($ts2 - $ts1) . " / 86400 = $dias_retraso<br>";
			}
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL && $count > 1) {
					$tpl->newBlock("totales");
					$tpl->assign("importe_comprobante", number_format($importe_comprobante,2,".",","));
					$tpl->assign("deposito", number_format($deposito,2,".",","));
					$tpl->assign("faltante", number_format($faltante,2,".",","));
					$tpl->assign("sobrante", number_format($sobrante,2,".",","));
					$tpl->assign("diferencia", number_format($faltante - $sobrante,2,".",","));
					$tpl->assign("tipo", $faltante - $sobrante > 0 ? "FALTANTE" : "SOBRANTE");
				}

				$num_cia = $result[$i]['num_cia'];

				$tpl->newBlock("cia");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);

				$importe_comprobante = 0;
				$deposito = 0;
				$faltante = 0;
				$sobrante = 0;
				$count = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign('importe_comprobante', $result[$i]['importe_comprobante'] != 0 ? number_format($result[$i]['importe_comprobante'], 2, '.', ',') : '&nbsp;');
			$tpl->assign("deposito", $result[$i]['deposito'] != 0 ? number_format($result[$i]['deposito'],2,".",",") : "&nbsp;");
			$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
			$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
			$tpl->assign("descripcion", $result[$i]['descripcion'] != "" ? $result[$i]['descripcion'] : "&nbsp;");

			$importe_comprobante += $result[$i]['importe_comprobante'];
			$deposito += $result[$i]['deposito'];
			$faltante += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
			$sobrante += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
			$count++;
		}
		if ($admin != NULL) {
			$tpl->newBlock("mensaje");
			$tpl->assign("dias_retraso", ceil($dias_retraso));
		}
		if ($num_cia != NULL && $count > 1) {
			$tpl->newBlock("totales");
			$tpl->assign("importe_comprobante", number_format($importe_comprobante,2,".",","));
			$tpl->assign("deposito", number_format($deposito,2,".",","));
			$tpl->assign("faltante", number_format($faltante,2,".",","));
			$tpl->assign("sobrante", number_format($sobrante,2,".",","));
			$tpl->assign("diferencia", number_format($faltante - $sobrante,2,".",","));
			$tpl->assign("tipo", $faltante - $sobrante > 0 ? "FALTANTE" : "SOBRANTE");
		}
	}
	else {
		$num_cia = NULL;
		$encargado = NULL;
		$texto1 = "ENTREGARLE EL <strong>DINERO</strong> A SU <strong>ADMINISTRADOR</strong>";
		$texto2 = "RETIRAR ESTE MONTO DE SU SIGUIENTE ENVASE";
		$dia = date("d");
		$mes = mes_escrito(date("n"), TRUE);
		$anio = date("Y");
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL && $count > 1) {
					$tpl->newBlock("totales_memo");
					$tpl->assign("deposito", number_format($deposito,2,".",","));
					$tpl->assign("faltante", number_format($faltante,2,".",","));
					$tpl->assign("sobrante", number_format($sobrante,2,".",","));
					$tpl->assign("diferencia", number_format($faltante - $sobrante,2,".",","));
					$tpl->assign("tipo", $faltante - $sobrante > 0 ? "FALTANTE" : "SOBRANTE");
					$tpl->assign("memo.texto", $faltante - $sobrante > 0 ? $texto1 : $texto2);
				}
				else if ($num_cia != NULL && $count == 1) {
					$tpl->assign("memo.texto", $faltante - $sobrante > 0 ? $texto1 : $texto2);
				}

				$num_cia = $result[$i]['num_cia'];

				$tpl->newBlock("memo");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre']);
				$encargado = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = $num_cia ORDER BY anio DESC, mes DESC LIMIT 1");
				$tpl->assign("encargado", strtoupper($encargado[0]['nombre_fin']));
				$tpl->assign("admin", $result[$i]['nombre_administrador']);
				$tpl->assign("dia", $dia);
				$tpl->assign("mes", $mes);
				$tpl->assign("anio", $anio);

				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[$i]['fecha'], $temp_fecha);
				$ts1 = mktime(0, 0, 0, $temp_fecha[2], $temp_fecha[1], $temp_fecha[3]);
				$ts2 = time();
				$dias_retraso = ($ts2 - $ts1) / 86400;
				$tpl->assign("dias_retraso", ceil($dias_retraso));

				$deposito = 0;
				$faltante = 0;
				$sobrante = 0;
				$count = 0;
			}
			$tpl->newBlock("fila_memo");
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("deposito", $result[$i]['deposito'] != 0 ? number_format($result[$i]['deposito'],2,".",",") : "&nbsp;");
			$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
			$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
			$tpl->assign("descripcion", $result[$i]['descripcion'] != "" ? $result[$i]['descripcion'] : "&nbsp;");

			$deposito += $result[$i]['deposito'];
			$faltante += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
			$sobrante += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
			$count++;
		}
		if ($num_cia != NULL && $count > 1) {
			$tpl->newBlock("totales_memo");
			$tpl->assign("deposito", number_format($deposito,2,".",","));
			$tpl->assign("faltante", number_format($faltante,2,".",","));
			$tpl->assign("sobrante", number_format($sobrante,2,".",","));
			$tpl->assign("diferencia", number_format($faltante - $sobrante,2,".",","));
			$tpl->assign("tipo", $faltante - $sobrante > 0 ? "FALTANTE" : "SOBRANTE");
			$tpl->assign("memo.texto", $faltante - $sobrante > 0 ? $texto1 : $texto2);
		}
		else if ($num_cia != NULL && $count == 1) {
			$tpl->assign("memo.texto", $faltante - $sobrante > 0 ? $texto1 : $texto2);
		}
	}
}
else
	$tpl->newBlock("no_result");

if ($_GET['tipo'] == "todas") {
	$tpl->newBlock('email');

	$tpl->assign('num_cia', $_REQUEST['num_cia']);
}

$tpl->printToScreen();
$db->desconectar();
?>
