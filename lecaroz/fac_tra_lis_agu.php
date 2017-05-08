<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

function antiguedad($fecha_alta, $fecha_agu) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha_alta, $fecha))
		return FALSE;

	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha_agu, $fecha);
	$ts_current = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Diferencia
	$diferencia = $ts_current - $ts_alta;
	// Calcular antiguedad
	$antiguedad[0] = date("Y", $diferencia) - 1970;	// Años
	$antiguedad[1] = date("n", $diferencia) - 1;	// Meses
	$antiguedad[2] = date("d", $diferencia) - 1;	// Días

	return $antiguedad;
}

function mostrar_antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;

	$antiguedad = antiguedad($fecha_alta);

	// Construir cadena
	$cadena = "";
	$cadena .= $antiguedad[0] > 0 ? ($antiguedad[0] == 1 ? "$antiguedad[0] Año " : "$antiguedad[0] Años ") : "";
	$cadena .= $antiguedad[1] > 0 ? ($antiguedad[1] == 1 ? "$antiguedad[1] Mes " : "$antiguedad[1] Meses ") : "";
	$cadena .= $antiguedad[2] > 0 ? ($antiguedad[2] == 1 ? "$antiguedad[2] Día" : "$antiguedad[2] Días") : "";

	return $cadena;
}

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['num_cia'])) {
	if ($_GET['anio'] > 2000)
		$fecha_ini = "28/12/$_GET[anio]";
	else
		$fecha_ini = date("n") <= 3 ? "28/12/" . (date("Y") - 1) : date("28/12/Y");

	$cias = array();
	foreach ($_GET['cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;

	$condiciones = array();

	$condiciones[] = '(fecha_baja IS NULL OR fecha_baja > \'' . $fecha_ini . '\')';

	$condiciones[] = 'catalogo_trabajadores.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

	if ($_GET['num_cia'] > 0) {
		$condiciones[] = ($_GET['criterio_orden'][0] == 'cia_aguinaldos' ? 'cia_aguinaldos' : 'num_cia') . ' = ' . $_GET['num_cia'];
	}

	if (count($cias) > 0) {
		$condiciones[] = 'num_cia NOT IN (' . implode(', ', $cias) . ')';
	}

	if (isset($_GET['no_exp'])) {
		$condiciones[] = 'solo_aguinaldo = TRUE';
	}

	if ($_GET['cod_puestos'] > 0) {
		$condiciones[] = 'cod_puestos = ' . $_GET['cod_puestos'];
	}

	if ($_GET['cod_turno'] > 0) {
		$condiciones[] = 'cod_turno = ' . $_GET['cod_turno'];
	}

	$orden = array();

	for ($i = 0; $i < count($_GET['criterio_orden']); $i++) {
		if ($_GET['criterio_orden'][$i]) {
			$orden[] = $_GET['criterio_orden'][$i];
		}
	}

	$orden[] = 'catalogo_trabajadores.num_emp';

	$sql = '
		SELECT
			id,
			num_cia,
			cia_aguinaldos,
			catalogo_companias.nombre_corto
				AS nombre_cia,
			num_emp,
			catalogo_trabajadores.nombre
				AS nombre,
			ap_paterno,
			ap_materno,
			catalogo_puestos.descripcion
				AS puesto,
			catalogo_turnos.nombre_corto
				AS turno,
			fecha_alta
		FROM
			catalogo_trabajadores
			LEFT JOIN catalogo_companias
				USING (num_cia)
			LEFT JOIN catalogo_puestos
				USING (cod_puestos)
			LEFT JOIN catalogo_turnos
				USING (cod_turno)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			' . implode(', ', $orden) . '
	';

	$result = $db->query($sql);//echo $sql;echo "<pre>";print_r($result);echo "</pre>";die;

	if ( ! $result)
	{
		echo "No hay resultados. Revise las compa&ntilde;&iacute;as mancomunadas para aguinaldos.";
		die;
	}

	// Buscar compañías que si tienen empleados con aguinaldo
	$num_cia = NULL;
	$cias_agu = array();
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['cia_aguinaldos']) {
			if ($num_cia != NULL && $ok)
				$cias_agu[] = $num_cia;

			$num_cia = $result[$i]['cia_aguinaldos'];
			$ok = FALSE;
		}
		$temp = $db->query("SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha >= '$fecha_ini' ORDER BY fecha DESC LIMIT 1");
		if ($temp && $temp[0]['importe'] >= 20) $ok = TRUE;
	}
	if ($num_cia != NULL && $ok)
		$cias_agu[] = $num_cia;

	$tpl = new TemplatePower( "./plantillas/fac/listado_trabajadores_2.tpl" );
	$tpl->prepare();

	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->prinToScreen();
		die;
	}

	$temp = $db->query("SELECT * FROM porcentaje_aguinaldo WHERE oficina = $_SESSION[tipo_usuario] ORDER BY id DESC LIMIT 1");
	if (isset($_GET['desglose'])) {
		// Obtener el desglose
		$bill = array();
		if ($temp[0]['b1000'] == "t") $bill[] = 1000;
		if ($temp[0]['b500'] == "t") $bill[] = 500;
		if ($temp[0]['b200'] == "t") $bill[] = 200;
		if ($temp[0]['b100'] == "t") $bill[] = 100;
		if ($temp[0]['b50'] == "t") $bill[] = 50;
		if ($temp[0]['b20'] == "t") $bill[] = 20;
	}

	$fecha_agu = $fecha_ini;

	$numcols = 0;
	$colspan = 0;
	$totales_colspan = 0;
	$numfilas_x_hoja = 48;
	$numfilas = $numfilas_x_hoja;
	$consecutivo = 0;
	$num_cia = NULL;$total_emp = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($orden[0] == 'cia_aguinaldos' && $_GET['cod_puestos'] == "" && $_GET['cod_turno'] == "") {
			if ($num_cia != $result[$i]['cia_aguinaldos']) {
				if ($num_cia != NULL && array_search($num_cia, $cias_agu) !== FALSE) {
					$tpl->newBlock("totales");
					$tpl->assign("totales_colspan", $totales_colspan);
					if (isset($_GET['agu_ant'])) {
						$tpl->newBlock("total_agu_ant");
						$tpl->assign("total_agu_ant", number_format($total_agu_ant, 2, ".", ","));
					}
					if (isset($_GET['status_ant'])) {
						$tpl->newBlock("relleno_status_ant");
					}
					if (isset($_GET['agu_act'])) {
						$tpl->newBlock("total_agu_act");
						$tpl->assign("total_agu_act", number_format($total_agu_act, 2, ".", ","));
					}
					/*if ($colspan > 0) {
						$tpl->newBlock("relleno");
						$tpl->assign("relleno_colspan", $colspan);
					}*/
					// [2006-12-14] Desglosar totales por panaderia y rosticeria
					if ($_SESSION['tipo_usuario'] == 1) {
						$tpl->newBlock("totales_separados");
						$tpl->assign("totales_colspan", $totales_colspan);

						if (isset($_GET['agu_ant'])) {
							$tpl->newBlock("total_agu_ant_pan");
							$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
							$tpl->assign("total_agu_ant_pan", number_format($total_agu_ant_pan, 2, ".", ","));
							$tpl->newBlock("total_agu_ant_ros");
							$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
							$tpl->assign("total_agu_ant_ros", number_format($total_agu_ant_ros, 2, ".", ","));
						}
						if (isset($_GET['status_ant'])) {
							$tpl->newBlock("relleno_status_ant_pan");
							$tpl->newBlock("relleno_status_ant_ros");
						}
						if (isset($_GET['agu_act'])) {
							$tpl->newBlock("total_agu_act_pan");
							$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
							$tpl->assign("total_agu_act_pan", number_format($total_agu_act_pan, 2, ".", ","));
							$tpl->newBlock("total_agu_act_ros");
							$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
							$tpl->assign("total_agu_act_ros", number_format($total_agu_act_ros, 2, ".", ","));
						}
					}
					/*if ($colspan > 0) {
						$tpl->newBlock("relleno_pan");
						$tpl->assign("relleno_colspan", $colspan);
						$tpl->newBlock("relleno_ros");
						$tpl->assign("relleno_colspan", $colspan);
					}*/
					/************************************************************/

					if (isset($_GET['desglose'])) {
						if ($numfilas + count($desglose) + 2 >= $numfilas_x_hoja) {
							$tpl->newBlock("salto");
							$tpl->newBlock("listado");
							$tpl->assign("cia", "Cia.: $num_cia");
							$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
						}

						$tpl->newBlock("desglose");
						$total = 0;
						foreach ($desglose as $key => $value)
							if ($value > 0) {
								$tpl->newBlock("den");
								$tpl->assign("cantidad", $value);
								$tpl->assign("denominacion", $key);
								$tpl->assign("importe", number_format($key * $value), 2, ".", ",");
								$total += $key * $value;
							}
						$tpl->assign("desglose.total", number_format($total, 2, ".", ","));
					}

					$numcols = 0;

					$tpl->newBlock("salto");
				}

				$num_cia = $result[$i]['cia_aguinaldos'];
				if (array_search($num_cia, $cias_agu) !== FALSE) {
					$tpl->newBlock("listado");
					$tpl->assign("cia", "Cia.: $num_cia");
					$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
					$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);

					$tpl->newBlock("titles");
					if (isset($_GET['puesto'])) {
						if (!($_GET['num_cia'] == "" && $_GET['cod_puestos'] > 0 && $_GET['cod_turno'] == "")) {
							$tpl->newBlock("puesto_title");
							$numcols++;
						}
						else
							$tpl->assign("puesto", " ({$result[0]['puesto']})");
					}

					if (isset($_GET['turno'])) {
						$tpl->newBlock("turno_title");
						$numcols++;
					}

					if (isset($_GET['antiguedad'])) {
						$tpl->newBlock("antiguedad_title");
						$numcols++;
					}

					if (isset($_GET['agu_ant'])) {
						$tpl->newBlock("agu_ant_title");
						$total_agu_ant = 0;
						$total_agu_ant_pan = 0;
						$total_agu_ant_ros = 0;
						$total_agu_ant_zap = 0;
						$colspan++;
						$numcols++;
					}
					if (isset($_GET['status_ant'])) {
						$tpl->newBlock("status_ant_title");
						$numcols++;
					}
					if (isset($_GET['agu_act'])) {
						$tpl->newBlock("agu_act_title");
						$total_agu_act = 0;
						$total_agu_act_pan = 0;
						$total_agu_act_ros = 0;
						$total_agu_act_zap = 0;
						$colspan++;
						$numcols++;
					}
					if (isset($_GET['status'])) {
						$tpl->newBlock("status_title");
						$numcols++;
					}
					if (isset($_GET['notes'])) {
						$tpl->newBlock("notes_title");
						$numcols++;
					}

					$consecutivo = 0;
					$desglose = array(1000 => 0, 500 => 0, 200 => 0, 100 => 0, 50 => 0, 20 => 0);
					$totales_colspan = /*6*/3 + (isset($_GET['puesto']) ? 1 : 0) + (isset($_GET['turno']) ? 1 : 0) + (isset($_GET['antiguedad']) ? 1 : 0);
					$numfilas = 0;
				}
			}
			if ($numfilas == $numfilas_x_hoja && array_search($num_cia, $cias_agu) !== FALSE) {
				$tpl->newBlock("salto");
				$tpl->newBlock("listado");

				$tpl->assign("cia", "Cia.: $num_cia");
				$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);

				$tpl->newBlock("titles");
				if (isset($_GET['puesto'])) {
					if (!($_GET['num_cia'] == "" && $_GET['cod_puestos'] > 0 && $_GET['cod_turno'] == "" && isset($_GET['puesto']))) {
						$tpl->newBlock("puesto_title");
						$numcols++;
					}
					else
						$tpl->assign("puesto", " ({$result[0]['puesto']})");
				}

				if (isset($_GET['turno'])) {
						$tpl->newBlock("turno_title");
						$numcols++;
				}

				if (isset($_GET['antiguedad'])) {
					$tpl->newBlock("antiguedad_title");
					$numcols++;
				}

				if (isset($_GET['agu_ant'])) {
					$tpl->newBlock("agu_ant_title");
					$colspan++;
					$numcols++;
				}
				if (isset($_GET['status_ant'])) {
					$tpl->newBlock("status_ant_title");
					$numcols++;
				}
				if (isset($_GET['agu_act'])) {
					$tpl->newBlock("agu_act_title");
					$colspan++;
					$numcols++;
				}
				if (isset($_GET['status'])) {
					$tpl->newBlock("status_title");
					$numcols++;
				}
				if (isset($_GET['notes'])) {
					$tpl->newBlock("notes_title");
					$numcols++;
				}

				$numfilas = 0;
			}
			$agu_act = $db->query("SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha >= '$fecha_ini' ORDER BY fecha DESC LIMIT 1");
			if ($agu_act && $agu_act[0]['importe'] >= 20) {
				$tpl->newBlock("fila");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("consecutivo", ++$consecutivo);
				$tpl->assign("num_emp", $result[$i]['num_emp']);
				$tpl->assign("nombre", "{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");$total_emp++;
				if (isset($_GET['puesto'])) {
					if (!($_GET['num_cia'] == "" && $_GET['cod_puestos'] > 0 && $_GET['cod_turno'] == "")) {
						$tpl->newBlock("puesto");
						$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
						$tpl->assign("puesto", $result[$i]['puesto']);
						$tpl->gotoBlock("fila");
					}
				}
				if (isset($_GET['turno'])) {
					$tpl->newBlock('turno');
					$tpl->assign("turno", $result[$i]['turno']);
				}
				if (isset($_GET['antiguedad'])) {
					$tpl->newBlock('antiguedad');
					if ($antiguedad = antiguedad($result[$i]['fecha_alta'], $fecha_agu))
						$tpl->assign("antiguedad", ($antiguedad[0] > 0 ? "$antiguedad[0] A " : "") . ($antiguedad[1] > 0 ? "$antiguedad[1] M " : ""));
				}

				if (isset($_GET['agu_ant'])) {
					$agu_ant = $db->query("SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha < '$fecha_ini' ORDER BY fecha DESC LIMIT 1");
					$tpl->newBlock("agu_ant");
					$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
					$tpl->assign("agu_ant", $agu_ant ? number_format($agu_ant[0]['importe'], 2, ".", ",") : "&nbsp;");
					$total_agu_ant += $agu_ant[0]['importe'];
					$total_agu_ant_pan += $result[$i]['num_cia'] <= 300 ? $agu_ant[0]['importe'] : 0;
					$total_agu_ant_ros += $result[$i]['num_cia'] > 300 && $result[$i]['num_cia'] < 799 ? $agu_ant[0]['importe'] : 0;
					$total_agu_ant_zap += $result[$i]['num_cia'] >= 900 ? $agu_ant[0]['importe'] : 0;

					if (isset($_GET['status_ant'])) {
						$tpl->newBlock("status_ant");
						$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
						$tpl->assign("status_ant", $agu_ant ? ($agu_ant[0]['tipo'] == 1 ? "P" : ($agu_ant[0]['tipo'] == 2 ? "C" : "M")) : "&nbsp;");
					}
				}
				if (isset($_GET['agu_act'])) {
					$tpl->newBlock("agu_act");
					$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
					$tpl->assign("agu_act", $agu_act ? number_format($agu_act[0]['importe'], 2, ".", ",") : "&nbsp;");
					$total_agu_act += $agu_act[0]['importe'];
					$total_agu_act_pan += $result[$i]['num_cia'] <= 300 ? $agu_act[0]['importe'] : 0;
					$total_agu_act_ros += $result[$i]['num_cia'] > 300 && $result[$i]['num_cia'] < 799 ? $agu_act[0]['importe'] : 0;
					$total_agu_act_zap += $result[$i]['num_cia'] >= 900 ? $agu_act[0]['importe'] : 0;

					if (isset($_GET['status'])) {
						$tpl->newBlock("status");
						$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
						$tpl->assign("status", $agu_act ? ($agu_act[0]['tipo'] == 1 ? "P" : ($agu_act[0]['tipo'] == 2 ? "C" : "M")) : "&nbsp;");
					}
				}
				if (isset($_GET['notes'])) $tpl->newBlock("notes");

				if (isset($_GET['desglose'])) {
					$residuo = $agu_act[0]['importe'];
					for ($j = 0; $j < count($bill); $j++) {
						if (floor($residuo / $bill[$j]) > 0)
							$desglose[$bill[$j]] += floor($residuo / $bill[$j]);
						$residuo = $residuo % $bill[$j];
					}
				}
			}

			$numfilas++;
		}
		else {
			if ($numfilas == $numfilas_x_hoja) {
				$tpl->newBlock("listado");
				$tpl->assign("nombre_cia", $_SESSION['tipo_usuario'] == 2 ? 'Zapaterias Elite' : "Oficinas Administrativas Mollendo S. de R.L. y C.V.");

				$tpl->newBlock("titles");

				if (isset($_GET['puesto'])) {
					if (!($_GET['num_cia'] == "" && $_GET['cod_puestos'] > 0 && $_GET['cod_turno'] == ""))
						$tpl->newBlock("puesto_title");
					else
						$tpl->assign("puesto", " ({$result[0]['puesto']})");
				}

				if (isset($_GET['turno'])) {
					$tpl->newBlock("turno_title");
				}

				if (isset($_GET['antiguedad'])) {
					$tpl->newBlock("antiguedad_title");
				}

				$tpl->newBlock("cia_title");
				if (isset($_GET['agu_ant'])) {
					$tpl->newBlock("agu_ant_title");
					if (empty($total_agu_ant)) $total_agu_ant = 0;
					if (empty($total_agu_ant_pan)) $total_agu_ant_pan = 0;
					if (empty($total_agu_ant_ros)) $total_agu_ant_ros = 0;
					if (empty($total_agu_ant_zap)) $total_agu_ant_zap = 0;
					$colspan++;
					$numcols++;
				}
				if (isset($_GET['agu_act'])) {
					$tpl->newBlock("agu_act_title");
					if (empty($total_agu_act)) $total_agu_act = 0;
					if (empty($total_agu_act_pan)) $total_agu_act_pan = 0;
					if (empty($total_agu_act_ros)) $total_agu_act_ros = 0;
					if (empty($total_agu_act_zap)) $total_agu_act_zap = 0;
					$colspan++;
					$numcols++;
				}
				if (isset($_GET['status'])) {
					$tpl->newBlock("status_title");
					$numcols++;
				}
				if (isset($_GET['notes'])) {
					$tpl->newBlock("notes_title");
					$numcols++;
				}

				$totales_colspan = /*5*//*7*/4 + (isset($_GET['puesto']) ? 1 : 0) + (isset($_GET['turno']) ? 1 : 0) + (isset($_GET['antiguedad']) ? 1 : 0);
				$numfilas = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $result[$i]['num_cia']);
			$tpl->assign("nombre_cia", $result[$i]['nombre_cia']);
			$tpl->gotoBlock("fila");
			$tpl->assign("consecutivo", ++$consecutivo);
			$tpl->assign("num_emp", $result[$i]['num_emp']);
			$tpl->assign("nombre", "{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

			if (isset($_GET['puesto'])) {
				if (!($_GET['num_cia'] == "" && $_GET['cod_puestos'] > 0 && $_GET['cod_turno'] == "")) {
					$tpl->newBlock("puesto");
					$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
					$tpl->assign("puesto", $result[$i]['puesto']);
					$tpl->gotoBlock("fila");
				}
			}
			if (isset($_GET['turno'])) {
				$tpl->newBlock('turno');
				$tpl->assign("turno", $result[$i]['turno']);
			}
			if (isset($_GET['antiguedad'])) {
				$tpl->newBlock('antiguedad');
				if ($antiguedad = antiguedad($result[$i]['fecha_alta'], $fecha_agu))
					$tpl->assign("antiguedad", ($antiguedad[0] > 0 ? "$antiguedad[0] A " : "") . ($antiguedad[1] > 0 ? "$antiguedad[1] M " : ""));
			}

			if (isset($_GET['agu_ant'])) {
				$agu_ant = $db->query("SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha < '$fecha_ini' ORDER BY fecha DESC LIMIT 1");
				$tpl->newBlock("agu_ant");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("agu_ant", $agu_ant ? number_format($agu_ant[0]['importe'], 2, ".", ",") : "&nbsp;");
				$total_agu_ant += $agu_ant[0]['importe'];
				$total_agu_ant_pan += $result[$i]['num_cia'] <= 300 ? $agu_ant[0]['importe'] : 0;
				$total_agu_ant_ros += $result[$i]['num_cia'] > 300 && $result[$i]['num_cia'] <= 799 ? $agu_ant[0]['importe'] : 0;
				$total_agu_ant_zap += $result[$i]['num_cia'] >= 900 ? $agu_ant[0]['importe'] : 0;

				if (isset($_GET['status_ant'])) {
					$tpl->newBlock("status");
					$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
					$tpl->assign("status_ant", $agu_ant ? ($agu_ant[0]['tipo'] == 1 ? "P" : ($agu_ant[0]['tipo'] == 2 ? "C" : ($agu_ant[0]['tipo'] == 3 ? "M" : 'D'))) : "&nbsp;");
				}
			}
			if (isset($_GET['agu_act'])) {
				$agu_act = $db->query("SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha >= '$fecha_ini' ORDER BY fecha DESC LIMIT 1");
				$tpl->newBlock("agu_act");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("agu_act", $agu_act ? number_format($agu_act[0]['importe'], 2, ".", ",") : "&nbsp;");
				$total_agu_act += $agu_act[0]['importe'];
				$total_agu_act_pan += $result[$i]['num_cia'] <= 300 ? $agu_act[0]['importe'] : 0;
				$total_agu_act_ros += $result[$i]['num_cia'] > 300 && $result[$i]['num_cia'] <= 799 ? $agu_act[0]['importe'] : 0;
				$total_agu_act_zap += $result[$i]['num_cia'] >= 900 ? $agu_act[0]['importe'] : 0;

				if (isset($_GET['status'])) {
					$tpl->newBlock("status");
					$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
					$tpl->assign("status", $agu_act ? ($agu_act[0]['tipo'] == 1 ? "P" : ($agu_act[0]['tipo'] == 2 ? "C" : ($agu_act[0]['tipo'] == 3 ? "M" : 'D'))) : "&nbsp;");
				}
			}
			if (isset($_GET['notes'])) $tpl->newBlock("notes");

			$numfilas++;

			if ($numfilas == $numfilas_x_hoja) $tpl->newBlock("salto");
		}
	}
	if (isset($_GET['agu_ant']) || isset($_GET['agu_act'])) {
		$tpl->newBlock("totales");
		$tpl->assign("totales_colspan", $totales_colspan);
		if (isset($_GET['agu_ant'])) {
			$tpl->newBlock("total_agu_ant");
			$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
			$tpl->assign("total_agu_ant", number_format($total_agu_ant, 2, ".", ","));
		}
		if (isset($_GET['status_ant'])) {
			$tpl->newBlock("relleno_status_ant");
		}
		if (isset($_GET['agu_act'])) {
			$tpl->newBlock("total_agu_act");
			$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
			$tpl->assign("total_agu_act", number_format($total_agu_act, 2, ".", ","));
		}
		//if ($colspan > 0) {
			//$tpl->newBlock("relleno");
			//$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
			//$tpl->assign("relleno_colspan", $colspan);
		//}
		// [2006-12-14] Desglosar totales por panaderia y rosticeria
		if ($_SESSION['tipo_usuario'] == 1) {
			$tpl->newBlock("totales_separados");
			$tpl->assign("totales_colspan", $totales_colspan);

			if (isset($_GET['agu_ant'])) {
				$tpl->newBlock("total_agu_ant_pan");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("total_agu_ant_pan", number_format($total_agu_ant_pan, 2, ".", ","));
				$tpl->newBlock("total_agu_ant_ros");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("total_agu_ant_ros", number_format($total_agu_ant_ros, 2, ".", ","));
			}
			if (isset($_GET['status_ant'])) {
				$tpl->newBlock("relleno_status_ant_pan");
				$tpl->newBlock("relleno_status_ant_ros");
			}
			if (isset($_GET['agu_act'])) {
				$tpl->newBlock("total_agu_act_pan");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("total_agu_act_pan", number_format($total_agu_act_pan, 2, ".", ","));
				$tpl->newBlock("total_agu_act_ros");
				$tpl->assign("fontsize", $numcols <= 3 ? /*"10pt"*/"8pt" : "8pt");
				$tpl->assign("total_agu_act_ros", number_format($total_agu_act_ros, 2, ".", ","));
			}
		}
		/*if ($colspan > 0) {
			$tpl->newBlock("relleno_pan");
			$tpl->assign("relleno_colspan", $colspan);
			$tpl->newBlock("relleno_ros");
			$tpl->assign("relleno_colspan", $colspan);
		}*/
		/************************************************************/

		if (isset($_GET['desglose'])) {
			if ($numfilas + count($desglose) + 2 >= $numfilas_x_hoja) {
				$tpl->newBlock("salto");
				$tpl->newBlock("listado");
				$tpl->assign("cia", "Cia.: $num_cia");
				$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
			}

			$tpl->newBlock("desglose");
			$total = 0;
			foreach ($desglose as $key => $value)
				if ($value > 0) {
					$tpl->newBlock("den");
					$tpl->assign("cantidad", $value);
					$tpl->assign("denominacion", $key);
					$tpl->assign("importe", number_format($key * $value), 2, ".", ",");
					$total += $key * $value;
				}
			$tpl->assign("desglose.total", number_format($total, 2, ".", ","));
		}
	}
	//echo $total_emp;
	$tpl->printToScreen();

	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_lis_agu.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$turno = $db->query("SELECT cod_turno, descripcion FROM catalogo_turnos WHERE giro = $_SESSION[tipo_usuario] ORDER BY cod_turno");
$puesto = $db->query("SELECT cod_puestos, descripcion FROM catalogo_puestos WHERE giro = $_SESSION[tipo_usuario] ORDER BY cod_puestos");

for ($i = 0; $i < count($turno); $i++) {
	$tpl->newBlock("turno");
	$tpl->assign("cod_turno", $turno[$i]['cod_turno']);
	$tpl->assign("descripcion", $turno[$i]['descripcion']);
}

for ($i = 0; $i < count($puesto); $i++) {
	$tpl->newBlock("puesto");
	$tpl->assign("cod_puestos", $puesto[$i]['cod_puestos']);
	$tpl->assign("descripcion", $puesto[$i]['descripcion']);
}

$tpl->printToScreen();
?>
