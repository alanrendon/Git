<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(7, 25))) die;

$fecha = date('d/m/Y', mktime(0, 0, 0, date('n') + 1, 1, date('Y')));

// Buscar contratos vencidos
if (isset($_GET['buscar'])) {
	$sql = "
		SELECT
			*
		FROM
			(
				SELECT
					id,
					fecha_inicio,
					fecha_final,
					(
						SELECT
							sum
								(
									subr.renta
								)
									AS
										renta
						FROM
							recibos_rentas
								subr
						WHERE
								subr.local = c.id
							AND
								fecha = '$fecha'::date - interval '1 year'
							AND
								subr.status = 1
					)
						AS
							renta1,
					renta_con_recibo
						AS
							renta2
				FROM
					catalogo_arrendatarios
						c
				WHERE
						bloque = 2
					AND
						status = 1
					AND
						incremento_anual = 'TRUE'
			)
				rentas
		WHERE
				renta2 > 0
			AND
				(
						/* [08-Jun-2009] Ahora se revisa que la diferencia de importes no sea mayor a 1 peso */
						/*renta2 = renta1*/
						abs(renta2 - renta1) <= 1
					OR
						fecha_final < '$fecha'
				)
		LIMIT 1";
	$result = $db->query($sql);
	
	if ($result)
		echo 1;
	else
		echo 0;
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verRenVen.tpl" );
$tpl->prepare();

$sql = "
	SELECT
		*,
		CASE
			/* [08-Jun-2009] Ahora se revisa que la diferencia de importes no sea mayor a 1 peso */
			WHEN /*renta1 = renta2*/abs(renta2 - renta1) <= 1 AND fecha_final > '$fecha' THEN
				'I'
			/* [09-Jun-2009] Local nuevo */
			WHEN nuevo = 1 THEN
				'N'
			ELSE
				'C'
		END
			AS
				tipo
	FROM
		(
			SELECT
				id,
				num_local,
				nombre_local,
				nombre_arrendatario
					AS
						nombre_arr,
				giro,
				fecha_inicio,
				/* [09-Jun-2009] Agregada fecha de corte */
				CASE
					WHEN age('$fecha', fecha_inicio) BETWEEN interval '1 year' AND '1 year 2 month' THEN
						1
					ELSE
						0
				END
					AS
						nuevo,
				fecha_final,
				(
					SELECT
						sum
							(
								subr.renta
							)
								AS
									renta
					FROM
						recibos_rentas
							subr
					WHERE
							subr.local = c.id
						AND
							fecha = '$fecha'::date - interval '1 year'
						AND
							subr.status = 1
				)
					AS
						renta1,
				renta_con_recibo
					AS
						renta2
			FROM
				catalogo_arrendatarios
					c
			WHERE
					bloque = 2
				AND
					status = 1
				AND
					incremento_anual = 'TRUE'
		)
			rentas
	WHERE
			renta2 > 0
		AND
			(
					/* [08-Jun-2009] Ahora se revisa que la diferencia de importes no sea mayor a 1 peso */
					/*renta2 = renta1*/
					abs(renta2 - renta1) <= 1
				OR
					/* [09-Jun-2009] Local nuevo */
					(
							renta1 IS NULL
						AND
							nuevo = 1
					)
				OR
					fecha_final < '$fecha'
			)
		AND
			(
				id
			)
				NOT IN
					(
						SELECT
							local
						FROM
							cartas_foleadas
						WHERE
								local
									IS NOT NULL
							AND
								fecha >= '$fecha'::date - interval '1 month'
				)
	ORDER BY
		num_local
";
$result = $db->query($sql);

foreach ($result as $i => $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign('id', $reg['id']);
	$tpl->assign('num_local', $reg['num_local']);
	$tpl->assign('nombre_local', $reg['nombre_local']);
	$tpl->assign('nombre_arr', $reg['nombre_arr']);
	$tpl->assign('giro', $reg['giro']);
	$tpl->assign('fecha_ini', $reg['fecha_inicio']);
	$tpl->assign('fecha_fin', $reg['fecha_final']);
	$tpl->assign('tipo', $reg['tipo']);
}

$tpl->printToScreen();
?>