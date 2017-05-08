<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';
include './includes/class.session2.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$sql = '
	SELECT
		nivel
	FROM
		balances_aut
	WHERE
		iduser = ' . $_SESSION['iduser'] . '
';
$nivel = $db->query($sql);

if (!$nivel || $nivel[0]['nivel'] == 0 || $nivel[0]['nivel'] == 1) {
	die('NO TIENEN AUTORIZACION PARA GENERAR BALANCES.');
}

$anyo = $_GET['anyo'];
$mes = $_GET['mes'];

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
$fecha_his = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo));
$dias = date('j', mktime(0, 0, 0, $mes + 1, 0, $anyo));

$condiciones = array();
$condiciones[] = 'num_cia BETWEEN 600 AND 699';
$condiciones[] = 'num_cia IN (SELECT num_cia FROM estado_cuenta WHERE fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' AND tipo_mov = \'FALSE\' GROUP BY num_cia)';

/*
@ Intervalo de compañías
*/
if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
	$cias = array();
	
	$pieces = explode(',', $_REQUEST['cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$cias[] = implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$cias[] = $piece;
		}
	}
	
	if (count($cias) > 0) {
		$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
	}
}

$sql = '
	SELECT
		num_cia
	FROM
		catalogo_companias
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

if (!$cias) die('NO HAY RESULTADOS');

$balance = '';
foreach ($cias as $c) {
	$balance .= '
		DELETE FROM
			balances_inm
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";
	
	$balance .= '
		DELETE FROM
			historico
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";
	
	$data = array(
		'num_cia' => $c['num_cia'],
		'anio' => $anyo,
		'mes' => $mes,
		'fecha' => '\'' . date('d/m/Y',  mktime(0, 0, 0, $mes, 1, $anyo)) . '\''
	);
	
	/*
	@
	@@ RENTAS
	@
	*/
	
	$sql = '
		SELECT
			sum(importe)
				AS
					rentas_cobradas
		FROM
			estado_cuenta
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				cod_mov
					IN
						(
							2
						)
	';
	$tmp = $db->query($sql);
	$data['rentas_cobradas'] = $tmp[0]['rentas_cobradas'] != 0 ? $tmp[0]['rentas_cobradas'] : 0;
	
	/*
	@ Utilidad Bruta
	@
	@ = 'Rentas Pagadas'
	*/
	$data['utilidad_bruta'] = $data['rentas_cobradas'];
	
	/*
	@
	@@ GASTOS
	@
	*/
	
	/*
	@ Gastos Generales
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						gastos_generales
		FROM
				movimiento_gastos
			LEFT JOIN
				catalogo_gastos
					USING
						(
							codgastos
						)
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codigo_edo_resultados = 2
			AND
				codgastos
					NOT IN
						(
							141
						)
	';
	/*
	@ Si la fecha de consulta es posterior al Febrero de 2007 no incluir código 140 IMPUESTOS
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 2, 1, 2007))
		$sql .= '
			AND
				codgastos
					NOT IN
						(
							140
						)
		';
	$tmp = $db->query($sql);
	$data['gastos_generales'] = $tmp[0]['gastos_generales'] != 0 ? -$tmp[0]['gastos_generales'] : 0;
	
	/*
	@ Incluir movimientos de estado de cuenta (33 IMPUESTOS, 12 RETENCION ISR, 78 IDE) en gastos generales
	*/
	$sql = '
		SELECT
			sum(importe)
				AS
					impuestos
		FROM
			estado_cuenta
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				cod_mov
					IN
						(
							33,
							12,
							78
						)
	';
	$tmp = $db->query($sql);
	
	/*
	@ Sumar impuesto IDE a gastos generales
	*/
	$data['gastos_generales'] -= $tmp[0]['impuestos'];
	
	/*
	@ Gastos de Caja
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							-importe
						ELSE
							importe
						END
				)
					AS
						gastos_caja
		FROM
			gastos_caja
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				clave_balance = \'TRUE\'
	';
	$tmp = $db->query($sql);
	$data['gastos_caja'] = $tmp[0]['gastos_caja'] != 0 ? $tmp[0]['gastos_caja'] : 0;
	
	/*
	@ Comisiones Bancarias
	@
	@ Válido a partir de Marzo de 2007
	@
	@ No incluir código 33 IMPUESTOS, 12 RETENCION ISR, 78 IDE
	*/
	$data['comisiones'] = 0;
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 3, 1, 2007)) {
		$sql = '
			SELECT
				sum
					(
						CASE
							WHEN tipo_mov = \'TRUE\' THEN
								-importe
							ELSE
								importe
						END
					)
						AS
							comisiones
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND \'' . $fecha2 . '\'
				AND
					(
							(

									cuenta = 1
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_bancos
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	33,
																	12,
																	78
																)
												GROUP BY
													cod_mov
											)
							)
						OR
							(
									cuenta = 2
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_santander
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	33,
																	12,
																	78
																)
												GROUP BY
													cod_mov
											)
							)
					)
		';
		$tmp = $db->query($sql);
		$data['comisiones'] = $tmp[0]['comisiones'] != 0 ? $tmp[0]['comisiones'] : 0;
	}
	
	/*
	@ Reservas
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						reserva
		FROM
			reservas_cias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha = \'' . $fecha1 . '\'
	';
	$tmp = $db->query($sql);
	$data['reserva_aguinaldos'] = $tmp[0]['reserva'] != 0 ? -$tmp[0]['reserva'] : 0;
	
	/*
	@ Gastos Pagados por otras Compañías
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN num_cia_egreso = ' . $c['num_cia'] . ' THEN
							monto
						WHEN num_cia_ingreso = ' . $c['num_cia'] . ' THEN
							-monto
					END
				)
					AS
						gastos_otras_cias
		FROM
			gastos_otras_cia
		WHERE
				(
						num_cia_egreso = ' . $c['num_cia'] . '
					OR
						num_cia_ingreso = ' . $c['num_cia'] . '
				)
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['gastos_otras_cias'] = $tmp[0]['gastos_otras_cias'] != 0 ? $tmp[0]['gastos_otras_cias'] : 0;
	
	/*
	@ Total de Gastos
	*/
	$data['total_gastos'] = $data['gastos_generales'] + $data['gastos_caja'] + $data['comisiones'] + $data['reserva_aguinaldos'] + $data['gastos_otras_cias'];
	
	/*
	@ Ingresos Extraordinarios
	*/
	$data['ingresos_ext'] = 0;
	
	/*
	@ Utilidad Neta
	*/
	$data['utilidad_neta'] = $data['utilidad_bruta'] + $data['total_gastos'] + $data['ingresos_ext'];
	
	/*
	@ Saldos
	*/
	$sql = '
		SELECT
			saldo + (
					CASE
						WHEN movs_ini IS NOT NULL THEN
							movs_ini
						ELSE
							0
					END
				)
					AS
						saldo_inicial,
			saldo + (
					CASE
						WHEN movs_fin IS NOT NULL THEN
							movs_fin
						ELSE
							0
					END
				)
					AS
						saldo_final
			FROM
				(
					SELECT
						sum(saldo_libros)
							AS
								saldo,
						(
							SELECT
								sum(
									CASE
										WHEN tipo_mov = \'FALSE\' THEN
											-importe
										ELSE
											importe
									END
								)
							FROM
								estado_cuenta
							WHERE
									num_cia = s.num_cia
								AND
									fecha >= \'' . $fecha1 . '\'
						)
							AS
								movs_ini,
						(
							SELECT
								sum(
									CASE
										WHEN tipo_mov = \'FALSE\' THEN
											-importe
										ELSE
											importe
									END
								)
							FROM
								estado_cuenta
							WHERE
									num_cia = s.num_cia
								AND
									fecha > \'' . $fecha2 . '\'
						)
							AS
								movs_fin
					FROM
						saldos
							s
					WHERE
							num_cia
								BETWEEN
										600
									AND
										699
						AND
							num_cia = ' . $c['num_cia'] . '
					GROUP BY
						num_cia
				)
					result
	';
	$tmp = $db->query($sql);
	$data['saldo_inicial'] = $tmp[0]['saldo_inicial'];
	$data['saldo_final'] = $tmp[0]['saldo_final'];
	$data['diferencia_saldo'] = $tmp[0]['saldo_final'] - $tmp[0]['saldo_inicial'];
	
	
	/*
	@ Crear querys de inserción
	*/
	
	/*
	@ Tabla: balances_ros
	*/
	$balance .= 'INSERT INTO balances_inm (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $data) . ');' . "\n";
	$balance .= 'INSERT INTO historico (num_cia, anio, mes, utilidad) SELECT num_cia, anio, mes, utilidad_neta FROM balances_inm WHERE num_cia = ' . $data['num_cia'] . ' AND anio = ' . $data['anio'] . ' AND mes = ' . $data['mes'] . ";\n";
}

$db->query($balance);

echo '<strong>SE HAN GENERADO/ACTUALIZADO TODOS LOS DATOS DE BALANCE SOLICITADOS</strong>';
?>