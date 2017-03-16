<?php
// LISTADO DE EFECTIVOS (COMPLETO)
// Tablas 'estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocomitt=yes");
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/ban_efe_com.tpl" );
$tpl->prepare();

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'], $fecha);

// Dias por mes
$diasxmes[1] = 31;
$diasxmes[2] = ($fecha[3] % 4 == 0) ? 29 : 28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;

// Rangos de fecha
$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $_POST['fecha'];

$num_cias = 0;
// Obtener listado de compañías
for ($i = 0; $i < 30; $i++)
	if ($_POST['cia' . $i] > 0)
		$cia[$num_cias++] = $_POST['cia' . $i];

// Obtener listado de compañías que no se tomaran sus depósitos reales
for ($i = 1; $i <= 10; $i++)
	$num_cia[$i] = $_POST['num_cia' . ($i - 1)];

// Obtener todas las compañias
$sql = "SELECT num_cia, tipo_cia FROM catalogo_companias WHERE";
if ($num_cias > 0) {
	$sql .= " num_cia IN (";
	for ($i = 0; $i < $num_cias; $i++)
		$sql .= $cia[$i] . ($i < $num_cias - 1 ? "," : ") AND");
}
if ($_POST['a_partir'])
	$sql .= " num_cia >= $_POST[a_partir] AND";
if ($_POST['idadmin'])
	$sql .= " idadministrador = $_POST[idadmin] AND";
$sql .= ' num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
$sql .= ' ORDER BY num_cia/* LIMIT 60*/';

$cia = $db->query($sql);

function buscarDep($dia) {
	global $depositos, $alternativos;

	if (!$depositos && !$alternativos)
		return FALSE;

	$dep = array();
	$count = 0;
	for ($i = 0; $i < count($depositos); $i++)
		if ($dia == $depositos[$i]['dia']) {
			$dep[] = array('importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con']);
			$count++;
		}

	for ($i = 0; $i < count($alternativos); $i++)
		if ($dia == $alternativos[$i]['dia']) {
			if ($alternativos[$i]['dep1'] > 0) {
				$dep[] = array('importe' => $alternativos[$i]['dep1'], 'fecha_con' => $alternativos[$i]['fecha']);
				$count++;
			}
			if ($alternativos[$i]['dep2'] > 0) {
				$dep[] = array('importe' => $alternativos[$i]['dep2'], 'fecha_con' => $alternativos[$i]['fecha']);
				$count++;
			}
		}

	return $count > 0 ? $dep : FALSE;
}

for ($c = 0; $c < count($cia); $c++) {
	// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
	// if (($cia[$c]['num_cia'] >= 301 && $cia[$c]['num_cia'] <= 599) || $cia[$c]['num_cia'] == 702 || $cia[$c]['num_cia'] == 705 || $cia[$c]['num_cia'] == 704)
	if ($cia[$c]['tipo_cia'] == 2)
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, 't' AS efe, 't' AS exp, 't' AS gas, 't' AS pro, 't' AS pas FROM total_companias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	// else if ($cia[$c]['num_cia'] >= 900 && $cia[$c]['num_cia'] <= 998)
	else if ($cia[$c]['tipo_cia'] == 4)
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, 't' AS efe, 't' AS exp, 't' AS gas, 't' AS pro, 't' AS pas FROM total_zapaterias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	// else
	else if ($cia[$c]['tipo_cia'] == 1)
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, efe, exp, gas, pro, pas FROM total_panaderias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	$efectivo = $db->query($sql);

	if (!$efectivo && $db->query('
		SELECT
			id
		FROM
			gastos_caja
		WHERE
			num_cia = ' . $cia[$c]['num_cia'] . '
			AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
	')) {
		$efectivo = array();

		for ($dia = 1; $dia <= $fecha[1]; $dia++) {
			$efectivo[] = array(
				'num_cia' => $cia[$c]['num_cia'],
				'efectivo' => 0,
				'dia' => $dia,
				'fecha' => date('d/m/Y', mktime(0, 0, 0, $fecha[2], $dia, $fecha[3])),
				'efe' => 'f',
				'exp' => 'f',
				'gas' => 'f',
				'pro' => 'f',
				'pas' => 'f'
			);
		}
	}

	if ($efectivo) {
		if ($c > 0) $tpl->newBlock("salto");
		$tpl->newBlock("tabla");

		$tpl->assign("num_cia",$cia[$c]['num_cia']);
		$nombre_cia = $db->query("SELECT nombre, nombre_corto FROM catalogo_companias WHERE num_cia = {$cia[$c]['num_cia']}");
		$tpl->assign("nombre_cia", substr($nombre_cia[0]['nombre'], 0, strpos($nombre_cia[0]['nombre'], "(") !== FALSE ? strpos($nombre_cia[0]['nombre'], "(") - 1 : strlen($nombre_cia[0]['nombre'])));
		$tpl->assign("nombre_corto", $nombre_cia[0]['nombre_corto']);
		$tpl->assign('mes_escrito', substr(mes_escrito($fecha[2], TRUE), 0, 3));
		$tpl->assign('anio_escrito', substr($fecha[3], 2));

		// [2006/07/10] Mancomunar cias. 100 a 200 con 201 a 300
		//$tmp = $cia[$c]['num_cia'] >= 100 && $cia[$c]['num_cia'] <= 200 ? "IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ")" : "= {$cia[$c]['num_cia']}";
		//$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1, 16, 44) AND num_cia $tmp AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		//$tmp = $cia[$c]['num_cia'] >= 100 && $cia[$c]['num_cia'] <= 200 ? "(num_cia IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ") AND num_cia_sec IS NULL) OR num_cia_sec IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ")" : "(num_cia = {$cia[$c]['num_cia']} AND num_cia_sec IS NULL) OR num_cia_sec = {$cia[$c]['num_cia']}";
		//$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND ($tmp) AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		//$depositos = $db->query($sql);
		/*$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1,16,44) AND num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		$depositos = $db->query($sql);*/

		/*
		@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
		@
		@ 21 - 10,000.00
		@ 31 -  4,000.00
		@ 32 -  3,000.00
		@ 34 -  5,000.00
		@ 49 -  3,000.00
		@ 73 -  3,000.00
		@ 79 -  2,000.00
		@ 121 - 5,000.00
		*/

		if (in_array($cia[$c]['num_cia'], array(
			21,
			31,
			32,
			34,
			49,
			73,
			79,
			121
			))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 8) {
			$importes_agosto_2012 = array(
				21  => 10000,
				31  => 4000,
				32  => 3000,
				34  => 5000,
				49  => 3000,
				73  => 3000,
				79  => 2000,
				121 => 5000
			);

			for ($i = 0; $i < count($efectivo); $i++) {
				if ($efectivo[$i]['dia'] < 31) {
					$efectivo[$i]['efectivo'] += $importes_agosto_2012[$cia[$c]['num_cia']];
				}
			}
		}

		/*
		@ [05-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
		@
		*/

		if (in_array($cia[$c]['num_cia'], array(
			31,
			32,
			33,
			34,
			73,
			121
			))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 9) {

			$sql = '
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					importe
				FROM
					cometra
				WHERE
					comprobante IN (41355658, 40759126)
					AND num_cia = ' . $cia[$c]['num_cia'] . '
				ORDER BY
					num_cia,
					fecha,
					importe
			';

			$tmp = $db->query($sql);

			$importes_septiembre_2012 = array();

			if ($tmp) {
				foreach ($tmp as $t) {
					$importes_septiembre_2012[$t['dia']] = $t['importe'];
				}
			}

			for ($i = 0; $i < count($efectivo); $i++) {
				if ($efectivo[$i]['dia'] < 31 && isset($importes_septiembre_2012[$efectivo[$i]['dia']])) {
					$efectivo[$i]['efectivo'] += $importes_septiembre_2012[$efectivo[$i]['dia']];
				}
			}
		}

		/*
		@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
		@
		*/

		if (in_array($cia[$c]['num_cia'], array(33))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 10) {
			for ($i = 0; $i < count($efectivo); $i++) {
				$efectivo[$i]['efectivo'] += 10000;
			}
		}

		/*
		@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
		@
		*/

		if (in_array($cia[$c]['num_cia'], array(33))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 11) {
			for ($i = 0; $i < count($efectivo); $i++) {
				$efectivo[$i]['efectivo'] += 10000;
			}
		}

		/*
		@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
		@
		*/

		if (in_array($cia[$c]['num_cia'], array(49, 57, 67, 34))
			&& intval($fecha[3], 10) == 2013
			&& intval($fecha[2], 10) == 10) {
			for ($i = 0; $i < count($efectivo); $i++) {
				$efectivo[$i]['efectivo'] += 10000;
			}
		}

		/*
		@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
		@
		*/

		if (in_array($cia[$c]['num_cia'], array(32))
			&& intval($fecha[3], 10) == 2013
			&& intval($fecha[2], 10) == 10) {
			for ($i = 0; $i < count($efectivo); $i++) {
				if ($efectivo[$i]['dia'] <= 11)
				{
					$efectivo[$i]['efectivo'] += 10000;
				}
			}
		}

		/*
                @ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
                @
                */

                if (in_array($cia[$c]['num_cia'], array(20, 50))
                        && intval($fecha[3], 10) == 2013
                        && intval($fecha[2], 10) == 10) {
                        for ($i = 0; $i < count($efectivo); $i++) {
                                if ($efectivo[$i]['dia'] <= 21)
                                {
                                        $efectivo[$i]['efectivo'] += 10000;
                                }
                        }
                }

		if (mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]) <= mktime(0, 0, 0, 5, 31, 2011) && in_array($cia[$c]['num_cia'], array(11, 303, 353, 355))) {
			$cias = array(
				11  => '11, 810',
				303 => '303, 811',
				353 => '353, 812',
				355 => '355, 813'
			);

			$sql = "
				SELECT
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia IN ({$cias[$cia[$c]['num_cia']]})
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec IN ({$cias[$cia[$c]['num_cia']]})
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
				ORDER BY
					fecha,
					importe DESC
			";
		}
		else {
			$sql = "
				SELECT
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia = {$cia[$c]['num_cia']}
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec = {$cia[$c]['num_cia']}
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
				ORDER BY
					fecha,
					importe DESC
			";
		}
		$depositos = $db->query($sql);

		$sql = "SELECT dep1, dep2, extract(day FROM fecha) AS dia, fecha FROM depositos_alternativos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
		$alternativos = $db->query($sql);

		// Obtener el total de otros depósitos del mes
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$temp = $db->query($sql);
		$otros_depositos = $temp[0]['sum'] != 0 ? $temp[0]['sum'] : 0;

		// En caso de que los depositos sean alternativos
		if ($key = array_search($cia[$c]['num_cia'], $num_cia)) {
			$depositos = FALSE;
			$num_dep = 0;
		}

		// Faltantes
		$faltantes = array();

		if ($fecha[3] >= 2015)
		{
			$condiciones = array();

			$condiciones[] = "fecha >= '01-01-2015'";

			$condiciones[] = "cod_mov IN (7, 13, 19, 48)";

			$condiciones[] = "num_cia = {$cia[$c]['num_cia']}";

			$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$sql = "
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					SUM(
						CASE
							WHEN tipo_mov = TRUE THEN
								-importe
							ELSE
								importe
						END
					)
						AS faltante
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				GROUP BY
					num_cia,
					dia
				ORDER BY
					num_cia,
					dia
			";

			$query = $db->query($sql);

			if ($query) {
				foreach ($query as $row) {
					$faltantes[$row['dia']] = floatval($row['faltante']);
				}
			}
		}
		else
		{
			$condiciones = array();

			$condiciones[] = "fecha_con IS NULL";

			$condiciones[] = "fecha >= '19-11-2014'";

			$condiciones[] = "num_cia = {$cia[$c]['num_cia']}";

			$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$sql = "
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					SUM(
						CASE
							WHEN tipo = FALSE THEN
								-importe
							WHEN tipo = TRUE THEN
								importe
						END
					)
						AS faltante
				FROM
					faltantes_cometra
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				GROUP BY
					num_cia,
					dia
				ORDER BY
					num_cia,
					dia
			";

			$query = $db->query($sql);

			if ($query) {
				foreach ($query as $row) {
					$faltantes[$row['dia']] = floatval($row['faltante']);
				}
			}
		}

		// Trazar datos
		$total_efectivos = 0;
		$total_otros = 0;
		$total_faltantes = 0;
		$total_diferencias = 0;
		$gran_total = 0;
		$total_depositos = 0;
		$total_mayoreo = 0;

		for ($i = 0; $i < count($efectivo); $i++) {
			// Buscar los depositos para x día
			$deposito = isset($efectivo[$i]['dia']) ? buscarDep($efectivo[$i]['dia']) : 0;

			// Trazar nueva fila
			$tpl->assign("dia$i", isset($efectivo[$i]['dia']) ? (int)$efectivo[$i]['dia'] : '&nbsp;');
			$tpl->assign("efectivo$i", isset($efectivo[$i]['dia']) && $efectivo[$i]['efectivo'] /*>*/!= 0 ? number_format($efectivo[$i]['efectivo'], 2, ".", ",") : "&nbsp;");

			// Si hay depositos
			$depositos_total = 0;
			$mayoreo_total = 0;
			if ($deposito) {
				$tpl->assign("deposito$i", number_format($deposito[0]['importe'], 2, ".", ","));
				$total_depositos += $deposito[0]['importe'];
				$depositos_total += $deposito[0]['importe'];

				if (count($deposito) > 1) {
					for ($j = 1; $j < count($deposito); $j++) {
						$mayoreo_total += $deposito[$j]['importe'];
						$total_mayoreo += $deposito[$j]['importe'];
						$depositos_total += $deposito[$j]['importe'];
					}
					$tpl->assign("mayoreo$i", number_format($mayoreo_total, 2, ".", ","));
				}
			}

			// Diferencia de efectivo contra depositos
			$diferencia = isset($efectivo[$i]['dia']) ? $efectivo[$i]['efectivo'] - $depositos_total : 0;
			// Si la diferencia es mayor a 0, tomar de otros depósitos la diferencia para compensar
			$otro_deposito = 0;
			if ($diferencia > 0) {
				// Si los depósitos cubren la diferencia y no se ha llegado al último día...
				if (($otros_depositos - $diferencia) > 0 && $i < count($efectivo) - 1) {
					// Si no es el último efectivo, hacer la diferencia, si lo es, asignar el resto de depósitos
					if ($i < count($efectivo)-1) {
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

				$tpl->assign("oficina$i", $otro_deposito > 0 ? number_format($otro_deposito, 2, ".", ",") : "&nbsp;");
			}
			else if ($i == count($efectivo) - 1) {
				$otro_deposito = $otros_depositos;
				$otros_depositos = 0;
				$tpl->assign("oficina$i", $otro_deposito > 0 ? number_format($otro_deposito, 2, ".", ",") : "&nbsp;");
			}
			else
				$tpl->assign("oficina$i", "&nbsp;");

			$faltante = isset($faltantes[$efectivo[$i]['dia']]) && $faltantes[$efectivo[$i]['dia']] != 0 ? $faltantes[$efectivo[$i]['dia']] : 0;

			$tpl->assign("faltante$i", $faltante != 0 ? number_format($faltantes[$efectivo[$i]['dia']], 2) : '&nbsp;');
			$tpl->assign("fal_color$i", $faltante > 0 ? '00F' : 'F00');

			$total_faltantes += $faltante;

			// Mostrar diferencia
			$depositos_total += $otro_deposito + $faltante;
			$tpl->assign("diferencia$i", isset($efectivo[$i]['dia']) && number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", "") != 0 ? number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", ",") : "&nbsp;");
			if (isset($efectivo[$i]['dia']) && number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", "") >= 0)
				$tpl->assign("dif_color$i", "000000");
			else
				$tpl->assign("dif_color$i", "FF0000");

			$tpl->assign("total$i", number_format($depositos_total, 2, ".", ","));
			$total_diferencias += isset($efectivo[$i]['dia']) ? $efectivo[$i]['efectivo'] - $depositos_total : 0;
			$gran_total += $depositos_total;

			// Sumar total de efectivos
			$total_efectivos += isset($efectivo[$i]['dia']) ? $efectivo[$i]['efectivo'] : 0;
			// Sumar total de otros depositos
			$total_otros += $otro_deposito;
		}

		$tpl->gotoBlock("tabla");
		// Trazar totales
		$tpl->assign("total_efectivos", round($total_efectivos, 2) != 0 ? number_format($total_efectivos, 2, ".", ",") : "&nbsp;");
		$tpl->assign("total_depositos", round($total_depositos, 2) != 0 ? number_format($total_depositos, 2, ".", ",") : "&nbsp;");
		$tpl->assign("total_mayoreo", round($total_mayoreo, 2) != 0 ? number_format($total_mayoreo, 2, ".", ",") : "&nbsp;");
		$tpl->assign("total_oficina", round($total_otros, 2) != 0 ? number_format($total_otros, 2, ".", ",") : "&nbsp;");
		$tpl->assign("total_faltantes", round($total_faltantes, 2) != 0 ? number_format($total_faltantes, 2) : "&nbsp;");
		$tpl->assign("total_diferencias", round($total_diferencias, 2) != 0 ? number_format($total_diferencias, 2, ".", ",") : "&nbsp;");
		if ($total_diferencias >= 0)
			$tpl->assign("color_dif","0000FF");
		else
			$tpl->assign("color_dif","FF0000");
		$tpl->assign("gran_total", round($gran_total, 2) != 0 ? number_format($gran_total, 2, ".", ",") : "&nbsp;");

		// Trazar promedios
		$dias = count($efectivo);
		$tpl->assign("promedio_efectivos",$total_efectivos != 0 ? number_format($total_efectivos/$dias,2,".",",") : "&nbsp;");
		$tpl->assign("promedio_depositos",$total_depositos != 0 ? number_format($total_depositos/$dias,2,".",",") : "&nbsp;");
		$tpl->assign("promedio_mayoreo",$total_mayoreo != 0 ? number_format($total_mayoreo/$dias,2,".",",") : "&nbsp;");
		$tpl->assign("promedio_oficina",$total_otros != 0 ? number_format($total_otros/$dias,2,".",",") : "&nbsp;");
		$tpl->assign("promedio_total",$gran_total != 0 ? number_format($gran_total/$dias,2,".",",") : "&nbsp;");

		// Datos para los enlaces
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha2, $temp);
		// Estado de cuenta
		$tpl->assign("tabla.num_cia", $cia[$c]['num_cia']);
		$tpl->assign("tabla.dia", $temp[1]);
		$tpl->assign("tabla.mes", $temp[2]);
		$tpl->assign("tabla.anio", $temp[3]);

		// Bloque de gastos y promedios
		@$tpl->assign("porcentaje_depositos", number_format((($total_depositos + $total_mayoreo) / $total_efectivos) * 100, 2, ".", ","));
		@$tpl->assign("porcentaje_oficinas", number_format(($total_otros / $total_efectivos) * 100, 2, ".", ","));
		@$tpl->assign("suma_porcentajes", number_format(((($total_depositos + $total_mayoreo) / $total_efectivos) + ($total_otros / $total_efectivos)) * 100, 2, ".", ","));

		// Bloque de gastos
		$sql = "SELECT * FROM gastos_caja JOIN catalogo_gastos_caja ON(catalogo_gastos_caja.id=cod_gastos) WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$gasto = $db->query($sql);

		if ($cia[$c]['num_cia'] >= 900) {
			$sql = "SELECT 'PAGO PROVEEDORES' AS descripcion, 'f' AS tipo_mov, sum(importe) AS importe FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND idnombre > 0 AND (num_fact1 != '' OR num_fact2 != '' OR num_fact3 != '' OR num_fact4 != '')";
			$tmp = $db->query($sql);

			if ($tmp[0]['importe'] > 0) {
				if (!$gasto)
					$gasto = array();

				array_unshift($gasto, $tmp[0]);
			}
		}

		$total_ingreso = 0;
		$total_egreso = 0;
		$total_gastos = 0;

		if ($gasto/* && count($gasto) < 20*/) {
			if (count($gasto) >= 20) {
				$sql = "SELECT descripcion, tipo_mov, sum(importe) AS importe FROM gastos_caja JOIN catalogo_gastos_caja ON(catalogo_gastos_caja.id=cod_gastos) WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY descripcion, tipo_mov";
				$gasto = $db->query($sql);

				if ($cia[$c]['num_cia'] >= 900) {
					$sql = "SELECT 'PAGO PROVEEDORES' AS descripcion, 'FALSE' AS tipo_mov, sum(importe) AS importe FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND idnombre > 0 AND (num_fact1 != '' OR num_fact2 != '' OR num_fact3 != '' OR num_fact4 != '')";
					$tmp = $db->query($sql);

					if ($tmp[0]['importe'] > 0) {
						array_unshift($gasto, $tmp[0]);
					}
				}
			}



			for ($i = 0; $i < count($gasto); $i++) {
				$tpl->assign("mov$i", $i + 1);
				$tpl->assign("concepto$i", $gasto[$i]['descripcion']);
				if ($gasto[$i]['tipo_mov'] == "t") {
					$tpl->assign("ingreso$i", number_format($gasto[$i]['importe'], 2, ".", ","));
					$total_ingreso += $gasto[$i]['importe'];
				}
				else if ($gasto[$i]['tipo_mov'] == "f") {
					$tpl->assign("egreso$i", number_format($gasto[$i]['importe'], 2, ".", ","));
					$total_egreso += $gasto[$i]['importe'];
				}
			}
			$tpl->assign("total_ingreso", number_format($total_ingreso, 2, ".", ","));
			$tpl->assign("total_egreso", number_format($total_egreso, 2, ".", ","));
			$total_gastos = $total_ingreso - $total_egreso;
			$tpl->assign("total_gastos", number_format($total_gastos, 2, ".", ","));
			$tpl->assign("total_gastos_color", $total_gastos >= 0 ? "000000" : "FF0000");
		}
		$repartido = $total_otros + $total_gastos;
		$tpl->assign("repartido", "<font color='#" . ($repartido >= 0 ? "000000" : "FF0000") . "'>" . number_format($repartido, 2, ".", ",") . "</font>");

		if ($cia[$c]['num_cia'] <= 300) {
			$tpl->assign("tventas", "Ventas");
			$tpl->assign("tmp_ventas", "MP/Ventas");
			$tpl->assign("tut_produccion", "UT/Producci&oacute;n");
			$tpl->assign("tmp_produccion", "MP/Producci&oacute;n");
			$tpl->assign("tproduccion", "Producci&oacute;n");
			$tpl->assign("tfaltante", "Faltante pan");
			$tpl->assign("trezago", "Rezago");

			$sql = "SELECT
				ventas_netas,
				mp_vtas,
				utilidad_pro,
				mp_pro,
				produccion_total,
				faltante_pan,
				rezago_fin,
				utilidad_neta + COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
						LEFT JOIN catalogo_companias ccec
							USING (num_cia)
					WHERE
						((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta
			FROM
				balances_pan bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				num_cia = {$cia[$c]['num_cia']}
				AND mes = $fecha[2]
				AND anio = $fecha[3]";

			$bal = $db->query($sql);

			if ($bal) {
				$tpl->assign("ventas", "<font color='#" . (($bal[0]['ventas_netas'] >= 0) ? "000000" : "FF0000") . "'>" . number_format($bal[0]['ventas_netas'], 2, ".", ",") . "</font>");
				$tpl->assign("mp_ventas", number_format($bal[0]['mp_vtas'], 2, ".", ","));
				$tpl->assign("ut_produccion", number_format($bal[0]['utilidad_pro'], 2, ".", ","));
				$tpl->assign("mp_produccion", number_format($bal[0]['mp_pro'], 2, ".", ","));
				$tpl->assign("produccion", "<font color='#" . ($bal[0]['produccion_total'] >= 0 ? "000000" : "FF0000")."'>" . number_format($bal[0]['produccion_total'], 2, ".", ",") . "</font>");
				$tpl->assign("faltante_pan", "<font color='#" . ($bal[0]['faltante_pan'] >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['faltante_pan'], 2, ".", ",") . "</font>");
				$tpl->assign("rezago", "<font color='#" . ($bal[0]['rezago_fin'] >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['rezago_fin'], 2, ".", ",") . "</font>");
				$tpl->assign("general", "<font color='#" . ($bal[0]['utilidad_neta'] >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'], 2, ".", ",") . "</font>");
				$tpl->assign("diferencia", "<font color='#" . ($bal[0]['utilidad_neta'] - $repartido >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'] - $repartido, 2, ".", ",") . "</font>");
			}
		}
		else if (($cia[$c]['num_cia'] > 301 && $cia[$c]['num_cia'] < 599) || ($cia[$c]['num_cia'] > 700 && $cia[$c]['num_cia'] < 750) ) {
			$sql = "SELECT
				utilidad_neta - COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
					WHERE
						((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta
			FROM
				balances_ros bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				num_cia = {$cia[$c]['num_cia']}
				AND mes = $fecha[2]
				AND anio = $fecha[3]";

			$bal = $db->query($sql);

			if ($bal) {
				$tpl->assign("general", "<font color='#" . ($bal[0]['utilidad_neta'] >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'], 2, ".", ",") . "</font>");
				$tpl->assign("diferencia", "<font color='#" . ($bal[0]['utilidad_neta'] - $repartido >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'] - $repartido, 2, ".", ",") . "</font>");
			}
		}
		else if ($cia[$c]['num_cia'] >= 900 && $cia[$c]['num_cia'] <= 998) {
			$sql = "SELECT * FROM balances_zap WHERE num_cia = {$cia[$c]['num_cia']} AND mes = $fecha[2] AND anio = $fecha[3]";
			$bal = $db->query($sql);

			if ($bal) {
				$tpl->assign("general", "<font color='#" . ($bal[0]['utilidad_neta'] >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'], 2, ".", ",") . "</font>");
				$tpl->assign("diferencia", "<font color='#" . ($bal[0]['utilidad_neta'] - $repartido >= 0 ? "000000" : "FF0000") . "'>" . number_format($bal[0]['utilidad_neta'] - $repartido, 2, ".", ",") . "</font>");
			}
		}
	}
}
$tpl->printToScreen();
?>
