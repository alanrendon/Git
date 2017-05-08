<?php
// COMPARATIVO DE FIN DE MES
// Tabla 'catalogo_gastos_caja'
// Menu 'Balance->Catálogos Especiales'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- FUNCIONES ---------------------------------------------------------------
function mes($mes) {
	switch($mes) {
		case 1: $string = "Enero"; break;
		case 2: $string = "Febrero"; break;
		case 3: $string = "Marzo"; break;
		case 4: $string = "Abril"; break;
		case 5: $string = "Mayo"; break;
		case 6: $string = "Junio"; break;
		case 7: $string = "Julio"; break;
		case 8: $string = "Agosto"; break;
		case 9: $string = "Septiembre"; break;
		case 10: $string = "Octubre"; break;
		case 11: $string = "Noviembre"; break;
		case 12: $string = "Diciembre"; break;
		default: $string = "";
	}

	return $string;
}

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "La Compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/ban_com_mes.tpl" );
$tpl->prepare();

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'], $fecha);

$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $_POST['fecha'];

// Obtener listado de compañías
$num_cias = 0;
for ($i=0; $i<10; $i++)
	if ($_POST['cia'.$i] > 0)
		$cia[$num_cias++] = $_POST['cia'.$i];

// Obtener listado de compañías que no se tomaran sus depósitos reales
for ($i=1; $i<=10; $i++)
	$dep_alt[$i] = $_POST['num_cia'.($i-1)];

// Obtener todas las compañias
$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE";
if ($num_cias > 0) {
	$sql .= " num_cia IN (";
	for ($i=0; $i<$num_cias; $i++)
		$sql .= $cia[$i].($i < $num_cias-1?",":") AND");
}
if ($_POST['idadmin'])
	$sql .= " idadministrador = $_POST[idadmin] AND";
if ($_SESSION['tipo_usuario'] == 1) {
	$sql .= " (num_cia NOT BETWEEN 600 AND 700 AND num_cia < 800" . ($fecha[3] == 2012 && $fecha[2] == 12 ? ' AND num_cia NOT IN (17)' : '') . ") ORDER BY num_cia";
} else {
	$sql .= " num_cia BETWEEN 900 AND 998 ORDER BY num_cia";
}
$cia = $db->query($sql);



$numfilas_x_hoja = 58;

$mes = $fecha[2];
$anio = $fecha[3];

$t_efectivo = 0;
$total_diferencia1 = 0;
$total_diferencia2 = 0;

$numfilas = $numfilas_x_hoja;
for ($i=0; $i<count($cia); $i++) {
	if ($numfilas >= $numfilas_x_hoja) {
		$numfilas = 0;
		$tpl->newBlock("hoja");

		$tpl->assign("mes",mes_escrito($mes));
		$tpl->assign("anio",$anio);
	}

	// Declaración de variables
	$num_cia = $cia[$i]['num_cia'];

	// Total de efectivo
	$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$otros = $db->query($sql);
	$sql = "SELECT (SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE') AS ingresos,
	(SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE') AS egresos";
	$gastos = $db->query($sql);
	$total_efectivo = $otros[0]['sum'] + ($gastos[0]['ingresos'] - $gastos[0]['egresos']);

	// Utilidad (este dato proviene del balance)
	$sql = "SELECT utilidad_neta FROM " . ($num_cia <= 300 ? "balances_pan" : ($num_cia >= 900 ? 'balances_zap' : "balances_ros")) . " WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio";
	$temp = $db->query($sql);
	$utilidad = ($temp)?$temp[0]['utilidad_neta']:0;

	// Diferencia 'total_efectivo' - 'utilidad'
	$diferencia1 = $total_efectivo - $utilidad;

	// Efectivo
	$sql = "SELECT SUM(efectivo) FROM " . ($num_cia <= 300 || $num_cia == 703 ? "total_panaderias" : ($num_cia >= 900 ? 'total_zapaterias' : "total_companias")) . " WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$efectivo = $db->query($sql);
	//echo 'efectivo = ' . $efectivo[0]['sum'] . '<br>';
	//echo 'otros + ingresos - egresos = ' . $total_efectivo . '<br>';
	if ($efectivo[0]['sum'] != 0 || $total_efectivo != 0) {
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

		if (in_array($num_cia, array(
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
			$efectivo[0]['sum'] += $importes_agosto_2012[$num_cia] * 30;
		}

		/*
		@ [04-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
		@
		*/

		if (in_array($num_cia, array(
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
					SUM(importe)
						AS importe
				FROM
					cometra
				WHERE
					comprobante IN (41355658, 40759126)
					AND num_cia = ' . $num_cia . '
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			';

			$tmp = $db->query($sql);

			$efectivo[0]['sum'] += $tmp[0]['importe'];
		}

		/*
		@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
		@
		*/

		if (in_array($num_cia, array(33))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 10) {
			$efectivo[0]['sum'] += 10000 * 31;
		}

		/*
		@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
		@
		*/

		if (in_array($num_cia, array(33))
			&& intval($fecha[3], 10) == 2012
			&& intval($fecha[2], 10) == 11) {
			$efectivo[0]['sum'] += 10000 * 30;
		}

		/*
                @ [19-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
                @
                */

                if (in_array($num_cia, array(49, 57, 67, 34, 32, 20, 50))
                        && intval($fecha[3], 10) == 2013
                        && intval($fecha[2], 10) == 10) {
			if (in_array($num_cia, array(49, 57, 67, 34))) {
				$efectivo[0]['sum'] += 10000 * 31;
			} else if (in_array($num_cia, array(32))) {
                                $efectivo[0]['sum'] += 10000 * 11;
                        } else if (in_array($num_cia, array(20, 50))) {
                                $efectivo[0]['sum'] += 10000 * 21;
                        }
                }

		// Depósitos
		// [2006/07/10] Mancomunar cias. 100 a 200 con 201 a 300
		//$tmp = $num_cia >= 100 && $num_cia <= 200 ? "IN ($num_cia, " . ($num_cia + 100) . ")" : "= $num_cia";
		//$sql = "SELECT sum(importe) FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND num_cia $tmp AND fecha BETWEEN '$fecha1' AND '$fecha2'";

		if (mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]) <= mktime(0, 0, 0, 5, 31, 2011) && in_array($num_cia, array(11, 303, 353, 355))) {
			$cias = array(
				11  => '11, 810',
				303 => '303, 811',
				353 => '353, 812',
				355 => '355, 813'
			);

			$sql = "
				SELECT
					SUM(importe)
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia IN ({$cias[$num_cia]})
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec IN ({$cias[$num_cia]})
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
			";
		}
		else {
			$sql = "
				SELECT
					SUM(importe)
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia = $num_cia
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec = $num_cia
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
			";
		}
		$depositos = $db->query($sql);


//		$sql = "SELECT sum(importe) FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND ((num_cia = $num_cia AND num_cia_sec IS NULL) OR num_cia_sec = $num_cia) AND fecha BETWEEN '$fecha1' AND '$fecha2'";
//		$depositos = $db->query($sql);
		/*$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1,16)";
		$depositos = $db->query($sql);*/

		// En caso de que la compañía este dentro de los depositos alternativos
		if (($key = array_search($num_cia, $dep_alt)) > 0) {
			$sql = "SELECT SUM(dep1) AS dep1,SUM(dep2) AS dep2 FROM depositos_alternativos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = $db->query($sql);
			$depositos[0]['sum'] = $temp[0]['dep1'] + $temp[0]['dep2'];
		}
		else {
			$sql = "SELECT SUM(dep1) AS dep1,SUM(dep2) AS dep2 FROM depositos_alternativos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = $db->query($sql);
			$depositos[0]['sum'] += $temp[0]['dep1'] + $temp[0]['dep2'];
		}

		// Otros depósitos
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$otros_dep = $db->query($sql);

		// Faltantes
		if ($anio >= 2015)
		{
			$condiciones = array();

			$condiciones[] = "fecha >= '01-01-2015'";

			$condiciones[] = "num_cia = {$num_cia}";

			$condiciones[] = "cod_mov IN (7, 13, 19, 48)";

			$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$sql = "
				SELECT
					SUM(
						CASE
							WHEN tipo_mov = TRUE THEN
								-importe
							ELSE
								importe
						END
					)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
			";
		}
		else
		{
			$condiciones = array();

			$condiciones[] = "fecha_con IS NULL";

			$condiciones[] = "fecha >= '19-11-2014'";

			$condiciones[] = "num_cia = {$num_cia}";

			$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$sql = "
				SELECT
					SUM(
						CASE
							WHEN tipo = FALSE THEN
								-importe
							WHEN tipo = TRUE THEN
								importe
						END
					)
				FROM
					faltantes_cometra
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
			";
		}

		$faltantes = $db->query($sql);

		// Diferencia 'efectivo' - 'depositos' - 'otros_dep'
		$diferencia2 = $efectivo[0]['sum'] - $depositos[0]['sum'] - $otros_dep[0]['sum'] - $faltantes[0]['sum'];

		$total_diferencia1 += $diferencia1;
		$total_diferencia2 += $diferencia2;
		$t_efectivo += $total_efectivo;

		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		$tpl->assign("total_efectivo",($total_efectivo != 0)?number_format($total_efectivo,2,".",","):"&nbsp;");
		$tpl->assign("utilidad",($utilidad != 0)?number_format($utilidad,2,".",","):"&nbsp;");
		$tpl->assign("diferencia1","<font color='#".($diferencia1 > 0?"0000FF":"FF0000")."'>".(($diferencia1 != 0)?number_format($diferencia1,2,".",","):"&nbsp;")."</font>");
		$tpl->assign("efectivo",($efectivo[0]['sum'] != 0)?number_format($efectivo[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("depositos",($depositos[0]['sum'] != 0)?number_format($depositos[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("otros",($otros_dep[0]['sum'] != 0)?number_format($otros_dep[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("faltantes",($faltantes[0]['sum'] != 0)?number_format($faltantes[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("diferencia2","<font color='#".($diferencia2 > 0?"0000FF":"FF0000")."'>".((round($diferencia2,2) != 0)?number_format($diferencia2,2,".",","):"&nbsp;")."</font>");

		$numfilas++;
	}
}
$tpl->newBlock("total");
$tpl->assign("diferencia1",number_format($total_diferencia1,2,".",","));
$tpl->assign("diferencia2",number_format($total_diferencia2,2,".",","));
$tpl->assign("total_efectivo",number_format($t_efectivo,2,".", ","));

$tpl->printToScreen();
?>
