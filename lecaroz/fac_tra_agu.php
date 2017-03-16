<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No se pueden generar los aguinaldos de años pasados";
$mensaje[1] = "Se generaron los aguinaldos con exito";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("LO SENTIMOS, PANTALLA EN REMODELACION  ^_^");

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Funciones ---------------------------------------------------------------
function antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;

	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
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

function calcula_aguinaldo($antiguedad, $sueldo_diario) {
	if (!$antiguedad)
		return FALSE;

	$aguinaldo = 0;
	$vacaciones = 0;

	// Calculo de aguinaldo
	// Antigüedad menor o igual a 1 año
	if ($antiguedad[0] <= 1) {
		$meses = $antiguedad[0] == 1 ? 12 : $antiguedad[1];
		$aguinaldo = 0.80 * (15 / 12 * ($sueldo_diario * $meses));
	}
	// Antigüedad mayor a 1 año
	else if ($antiguedad[0] > 1)
		$aguinaldo = $sueldo_diario * 15;

	// Calculo de vacaciones
	// Antigüedad de mas de 1 año y menor a 2
	if ($antiguedad[0] == 1 && $antiguedad[1] > 0)
		$vacaciones = (7 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 2 años y menor a 3
	else if ($antiguedad[0] == 2)
		$vacaciones = (10 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 3 años y menor a 4
	else if ($antiguedad[0] == 3)
		$vacaciones = (12 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 4 años
	else if ($antiguedad[0] > 3)
		$vacaciones = (15 + (($antiguedad[0] - 4) / 5) * 3) * $sueldo_diario;

	$total_aguinaldo = ($aguinaldo + $vacaciones) * 1.10;

	return round($total_aguinaldo);
}

function aguinaldo_por($ultimo_aguinaldo, $incremento) {
	$aguinaldo_por = round($ultimo_aguinaldo * (1 + $incremento / 100));

	return $aguinaldo_por;
}

function nuevo_aguinaldo($antiguedad, $ultimo_aguinaldo, $incremento, $sueldo_diario, $bill, $tipo = 2) {
	$nuevo_aguinaldo['importe'] = 0;
	$nuevo_aguinaldo['tipo'] = $tipo;

	// [17-Dic-2008]

	// Validar fecha de alta
	if (!$antiguedad)
		return FALSE;

	// Si tuvo aguinaldo anterior
	if ($ultimo_aguinaldo > 0) {
		// Calcular aguinaldo por porcentaje
		$aguinaldo_por = aguinaldo_por($ultimo_aguinaldo, $incremento);
		// Calcular por antigüedad
		$aguinaldo_ant = calcula_aguinaldo($antiguedad, $sueldo_diario);

		// El nuevo aguinaldo sera siempre el mayor de los dos calculos
		$nuevo_aguinaldo['importe'] = $aguinaldo_por >= $aguinaldo_ant ? $aguinaldo_por : $aguinaldo_ant;
		$nuevo_aguinaldo['tipo'] = $aguinaldo_por >= $aguinaldo_ant ? 1 : 2;
	}
	// Si no ha tenido aguinaldos anteriores, calcularlo a partir de la antigüedad
	else {
		$nuevo_aguinaldo['importe'] = calcula_aguinaldo($antiguedad, $sueldo_diario);
		$nuevo_aguinaldo['tipo'] = 2;
	}

	if ($tipo > 2) {
		$nuevo_aguinaldo['tipo'] = $tipo;
	}

	// Ajustar aguinaldo al valor de denominacion de los billetes
	$residuo = $nuevo_aguinaldo['importe'];
	for ($i = 0; $i < count($bill); $i++)
		$residuo = $residuo % $bill[$i];


	if ($residuo > 0)
		$nuevo_aguinaldo['importe'] = $residuo < $bill[count($bill) - 1] / 2 ? $nuevo_aguinaldo['importe'] - $residuo : $nuevo_aguinaldo['importe'] + ($bill[count($bill) - 1] - $residuo);

	return $nuevo_aguinaldo;
}

function toInt($value) {
	return intval($value, 10);
}

if (isset($_GET['fecha'])) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $temp);
	if ($temp['3'] != date("Y")) {
		header("location: ./fac_tra_agu.php?codigo_error=2");
		die;
	}

	list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

	$sql = '
		SELECT
			ct.id,
			ct.num_emp,
			ct.fecha_alta,
			puestos.sueldo,
			ct.tipo,
			COALESCE((
				SELECT
					importe
				FROM
					aguinaldos
				WHERE
					id_empleado = ct.id
					AND fecha = \'' . $_REQUEST['fecha'] . '\'::DATE - INTERVAL \'1 YEAR\'
				ORDER BY
					id DESC
				LIMIT
					1
			), 0)
				AS aguinaldo,
			CASE
				WHEN (
					SELECT
						MAX(fecha)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
				) < (NOW() - INTERVAL \'1 MONTH\')::DATE THEN
					TRUE
				ELSE
					FALSE
			END
				AS prestamo,
			CASE
				WHEN (
					SELECT
						MAX(fecha)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
						AND tipo_mov = FALSE
				) < (NOW() - INTERVAL \'2 MONTH\')::DATE AND (
					SELECT
						SUM(importe)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
						AND tipo_mov = TRUE
				) < (
					SELECT
						SUM(importe)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
						AND tipo_mov = FALSE
				) * 0.40 THEN
					TRUE
				ELSE
					FALSE
			END
				AS prestamo_mayor,
			CASE
				WHEN (
					SELECT
						MAX(fecha)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
						AND tipo_mov = FALSE
				) < (NOW() - INTERVAL \'40 DAYS\')::DATE AND (
					SELECT
						SUM(
							CASE
								WHEN tipo_mov = FALSE THEN
									importe
								ELSE
									-importe
							END
						)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
				) > 1000 THEN
					TRUE
				ELSE
					FALSE
			END
				AS prestamo_mayor_1000,
			CASE
				WHEN (
					SELECT
						MIN(id)
					FROM
						infonavit_pendientes
					WHERE
						id_emp = ct.id
						AND status = 0
						AND anio <= ' . $anio . '
				) IS NOT NULL THEN
					TRUE
				ELSE
					FALSE
			END
				AS infonavit,
			(
				COALESCE(firma_contrato, FALSE)
				AND (
					(
						fecha_inicio_contrato IS NOT NULL
						AND fecha_termino_contrato IS NOT NULL
						AND NOW()::DATE < fecha_termino_contrato
					)
					OR (
						fecha_inicio_contrato IS NOT NULL
						AND fecha_termino_contrato IS NULL
					)
				)
			)
				AS contrato
		FROM
			catalogo_trabajadores ct
			LEFT JOIN catalogo_puestos puestos
				USING (cod_puestos)
			LEFT JOIN catalogo_turnos turnos
				USING (cod_turno)
		WHERE
			fecha_alta IS NOT NULL
			AND fecha_baja IS NULL
			AND solo_aguinaldo = TRUE
			AND baja_rh IS NULL
			AND cod_puestos NOT IN (7)
			AND num_cia NOT IN (700, 800)
			AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
		ORDER BY
			id
	';

	$result = $db->query($sql);

	if ($result) {
		$incremento = $_REQUEST['incremento'] > 0 ? $_REQUEST['incremento'] : 0;

		$bill = array();

		foreach ($_REQUEST['bill'] as $value) {
			$bill[] = $value;
		}

		$sql = '';

		foreach ($result as $rec) {
			if ($rec['contrato'] == 'f') {
				// $aguinaldo = array(
				// 	'importe' => 1,
				// 	'tipo' =>    7
				// );
				$tipo = 7;
			}
			else if (($rec['prestamo'] == 't' || $rec['prestamo_mayor'] == 't' || $rec['prestamo_mayor_1000'] == 't') && $rec['infonavit'] == 't') {
				// $aguinaldo = array(
				// 	'importe' => 1,
				// 	'tipo' =>    6
				// );
				$tipo = 6;
			}
			else if ($rec['prestamo'] == 't' || $rec['prestamo_mayor'] == 't' || $rec['prestamo_mayor_1000'] == 't') {
				// $aguinaldo = array(
				// 	'importe' => 1,
				// 	'tipo' =>    4
				// );
				$tipo = 4;
			}
			else if ($rec['infonavit'] == 't') {
				// $aguinaldo = array(
				// 	'importe' => 1,
				// 	'tipo' =>    5
				// );
				$tipo = 5;
			}
			else {
				// $aguinaldo = nuevo_aguinaldo(antiguedad($rec['fecha_alta']), $rec['aguinaldo'], $incremento, $rec['sueldo'], $bill, $rec['tipo']);
				$tipo = $rec['tipo'];
			}

			// $aguinaldo = nuevo_aguinaldo(antiguedad($rec['fecha_alta']), $rec['aguinaldo'], $incremento, $rec['sueldo'], $bill, $rec['tipo']);
			$aguinaldo = nuevo_aguinaldo(antiguedad($rec['fecha_alta']), $rec['aguinaldo'], $incremento, $rec['sueldo'], $bill, $tipo);

			if ($id = $db->query('
				SELECT
					id,
					tipo
				FROM
					aguinaldos
				WHERE
					id_empleado = ' . $rec['id'] . '
					AND fecha = \'' . $_REQUEST['fecha'] . '\'
			')) {
				if ($id[0]['tipo'] != 3) {
					$sql .= '
						UPDATE
							aguinaldos
						SET
							importe = ' . $aguinaldo['importe'] . ',
							tipo = ' . $aguinaldo['tipo'] . '
					' . ";\n";
				}
			}
			else {
				$sql .= '
					INSERT INTO
						aguinaldos
							(
								id_empleado,
								fecha,
								importe,
								tipo
							)
						VALUES
							(
								' . $rec['id'] . ',
								\'' . $_REQUEST['fecha'] . '\',
								' . $aguinaldo['importe'] . ',
								' . $aguinaldo['tipo'] . '
							)
				' . ";\n";
			}
		}
	}

	/************************************************************************************************************/

	/*$sql = "SELECT id, fecha_alta, sueldo, tipo FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING(cod_puestos) LEFT JOIN catalogo_turnos USING(cod_turno)";
	$sql .= " WHERE fecha_alta IS NOT NULL AND fecha_baja IS NULL AND solo_aguinaldo = TRUE AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY id";
	$result = $db->query($sql);

	// [16-Dic-2009] Obtener empleados con problemas de prestamos
	// [17-Dic-2010] Cambiar a 1 mes
	$sql = 'SELECT id_empleado FROM (SELECT id_empleado, sum(CASE WHEN tipo_mov = \'FALSE\' THEN importe ELSE -importe END) AS importe, max(fecha) AS ultimo FROM prestamos p LEFT JOIN catalogo_trabajadores ct ON (ct.id = p.id_empleado) WHERE pagado = FALSE AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . ' GROUP BY id_empleado ORDER BY id_empleado) result LEFT JOIN catalogo_trabajadores ON (id = id_empleado) WHERE importe > 0 AND result.ultimo < now() - interval \'1 months\' AND fecha_alta IS NOT NULL AND fecha_baja IS NULL AND solo_aguinaldo = \'TRUE\'';
	$tmp = $db->query($sql);
	$prestamos = array();
	if ($tmp)
		foreach ($tmp as $reg)
			$prestamos[] = $reg['id_empleado'];

	// Obtener ultima fecha de aguinaldo
	$tmp = $db->query("SELECT fecha FROM aguinaldos WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY fecha DESC LIMIT 1");
	$ultima_fecha = $tmp ? $tmp[0]['fecha'] : NULL;

	// Obtener porcentaje de incremento de aguinaldo
	$incremento = $_GET['incremento'] > 0 ? $_GET['incremento'] : "0";

	// Obtener desglose de aguinaldo
	$bill = array();
	foreach ($_GET['bill'] as $value)
		$bill[] = $value;

	//$db->query("DELETE FROM aguinaldos WHERE fecha = '$_GET[fecha]' AND ");

	$sql = "";
	for ($i = 0; $i < count($result); $i++) {
		// [16-Dic-2009] Si el empleado no tiene adeudos mayores a 2 meses
		if (!in_array($result[$i]['id'], $prestamos)) {
			// Obtener ultimo aguinaldo
			//$temp = $db->query("SELECT importe FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} ORDER BY fecha DESC LIMIT 1");
			$temp = $db->query("SELECT importe FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha = '$ultima_fecha' ORDER BY fecha DESC LIMIT 1");
			$ultimo_aguinaldo = $temp ? $temp[0]['importe'] : 0;
			// Calcular el nuevo aguinaldo
			$nuevo_aguinaldo = nuevo_aguinaldo(antiguedad($result[$i]['fecha_alta']), $ultimo_aguinaldo, $incremento, $result[$i]['sueldo'], $bill, $result[$i]['tipo']);
		}
		// [16-Dic-2009] Si el empleado tiene adeudos mayores a 2 meses, no calcular su aguinaldo y ponerlo a 1 peso
		else {
			$nuevo_aguinaldo['importe'] = 1;
			$nuevo_aguinaldo['tipo'] = 4;
		}

		// Crear query de insercion o actualzacion
		if ($id = $db->query("SELECT id, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha = '$_GET[fecha]'")) {
			if ($id[0]['tipo'] != 3)
				$sql .= "UPDATE aguinaldos SET importe = $nuevo_aguinaldo[importe], tipo = $nuevo_aguinaldo[tipo] WHERE id = {$id[0]['id']};\n";
		}
		else
			$sql .= "INSERT INTO aguinaldos (id_empleado, importe, tipo, fecha) VALUES ({$result[$i]['id']}, $nuevo_aguinaldo[importe], $nuevo_aguinaldo[tipo], '$_GET[fecha]');\n";
	}*/
	$sql .= "INSERT INTO porcentaje_aguinaldo (porcentaje, fecha, fecha_aguinaldo, oficina, ";
	for ($i = 0; $i < count($bill); $i++)
		$sql .= "b$bill[$i]" . ($i < count($bill) - 1 ? ", " : ")");
	$sql .= " VALUES ($incremento, CURRENT_DATE, '$_GET[fecha]', " . $_SESSION['tipo_usuario'] . ', ';
	for ($i = 0; $i < count($bill); $i++)
		$sql .= "'TRUE'" . ($i < count($bill) - 1 ? ", " : ");\n");
	$db->query($sql);

	header("location: ./fac_tra_agu.php?mensaje=1");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_agu_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!in_array($_SESSION['iduser'], array(1, 4, 25, 28))) {
	$tpl->newBlock('disabled');
	$tpl->printToScreen();

	die;
} else {
	$tpl->newBlock('programa');
}

$fecha = date("n") > 3 ? date("28/12/Y") : "28/12/" . (date("Y") - 1);

$tpl->assign("fecha", $fecha);

// Validar que no se hayan generado aguinaldos
$val_agu = $db->query("SELECT * FROM aguinaldos a LEFT JOIN catalogo_trabajadores ct ON (ct.id = id_empleado) WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " AND fecha = '$fecha' LIMIT 1");

if ($val_agu) $tpl->assign("disabled_agu", "disabled");

// Obtener porcentaje de incremento de aguinaldo
$sql = "SELECT * FROM porcentaje_aguinaldo WHERE oficina = $_SESSION[tipo_usuario] ORDER BY id DESC LIMIT 1";
$temp = $db->query($sql);
$incremento = $temp ? $temp[0]['porcentaje'] : 0;

$tpl->assign("incremento", $incremento);

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

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $mensaje[$_GET['mensaje']]);
}

// Imprimir el resultado
$tpl->printToScreen();
?>
