<?
include $_SERVER['DOCUMENT_ROOT'] . '/lecaroz/includes/class.db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/lecaroz/includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = '
	UPDATE
		saldos
	SET
		saldo_libros = saldo_bancos + movs.importe
	FROM
		(
			SELECT
				num_cia,
				cuenta,
				SUM(
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							importe
						ELSE
							-importe
					END
				)
					AS importe
			FROM
				estado_cuenta
			WHERE
				fecha_con IS NULL
			GROUP BY
				num_cia,
				cuenta
		)
			movs
	WHERE
			saldos.num_cia = movs.num_cia
		AND
			saldos.cuenta = movs.cuenta
' . ";\n";

/****** GUARDAR HISTORICO DE SALDOS ******/
$sql .= '
INSERT INTO
	his_sal_ban
		(
			num_cia,
			fecha,
			cuenta,
			saldo_libros,
			saldo_bancos
		)
	SELECT
		num_cia,
		now()::date,
		cuenta,
		saldo_libros,
		saldo_bancos
	FROM
		saldos
	ORDER BY
		num_cia
' . ";\n";

/****** GUARDAR HISTORICO DE SALDO A PROVEEDORES ******/
$sql .= '
INSERT INTO
	his_sal_pro
		(
			num_cia,
			fecha,
			saldo
		)
	SELECT
		num_cia,
		now()::date,
		sum(total)
	FROM
		pasivo_proveedores
	GROUP BY
		num_cia
	ORDER BY
		num_cia
' . ";\n";

$db->query($sql);
?>