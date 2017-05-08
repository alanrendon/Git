<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/listar_emp.tpl");
$tpl->prepare();

$sql = "
	SELECT
		*
	FROM
		(
			SELECT
				id,
				num_emp,
				ap_paterno,
				ap_materno,
				ct.nombre,
				(
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
				)
					AS saldo_emp
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				(
					num_cia = $_GET[num_cia]
					OR num_cia_primaria = $_GET[num_cia]
				)
				AND (
					fecha_baja IS NULL
					OR id IN (
						SELECT
							id_empleado
						FROM
							prestamos
						WHERE
							num_cia = $_GET[num_cia]
							AND pagado = 'FALSE'
						GROUP BY
							id_empleado
					)
				)
		) result
	ORDER BY
		ap_paterno,
		ap_materno,
		nombre
";
$result = $db->query($sql);

if ($result)
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num', $reg['num_emp']);
		$tpl->assign('nombre', trim("$reg[ap_paterno] $reg[ap_materno] $reg[nombre]"));
		$tpl->assign('saldo', $reg['saldo_emp'] != 0 ? number_format($reg['saldo_emp'], 2) : '&nbsp;');
	}

$tpl->printToScreen();
?>
