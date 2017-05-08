<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(25, 7, 12, 43))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		result.*,
		cc.nombre_corto
	FROM
			(
				SELECT
					id,
					num_cia,
					folio,
					UPPER(atencion)
						AS
							atencion,
					UPPER(referencia)
						AS
							referencia,
					fecha,
					(
						SELECT
							fecha_respuesta
						FROM
							cartas_foleadas_seguimiento
						WHERE
							id_carta = cf.id
						ORDER BY
							fecha_respuesta DESC
						LIMIT
							1
					)
						AS
							fecha_respuesta
				FROM
					cartas_foleadas cf
				WHERE
					seguimiento = 1
			)
				result
		LEFT JOIN
			catalogo_companias cc
				USING
					(
						num_cia
					)
	WHERE
			(
					(
							fecha_respuesta IS NULL
						AND
							fecha < now()::date - interval \'2 days\'
					)
				OR
					(
							fecha_respuesta IS NOT NULL
						AND fecha_respuesta < now()::date - interval \'2 days\'
					)
			)
		AND
			num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
	ORDER BY
		num_cia,
		folio
';
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verPenAsu.tpl" );
$tpl->prepare();

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
	$tpl->assign('folio', $reg['folio']);
	$tpl->assign('fecha', $reg['fecha']);
	$tpl->assign('fecha_respuesta', $reg['fecha_respuesta']);
	$tpl->assign('atencion', $reg['atencion']);
	$tpl->assign('referencia', $reg['referencia']);
}

die($tpl->getOutputContent());
?>