<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');echo 'aki';

$sql = '
	SELECT
		*
	FROM
		catalogo_trabajadores
	WHERE
		num_cia < 900
		AND fecha_baja IS NULL
		AND num_emp IN (
			SELECT
				num_emp
			FROM
				catalogo_trabajadores
			WHERE
				num_cia < 900
				AND fecha_baja IS NULL
				AND id NOT IN (
					SELECT
						MIN(id)
					FROM
						catalogo_trabajadores
					WHERE
						num_cia < 900
						AND fecha_baja IS NULL
					GROUP BY
						num_emp
				)
		)
	ORDER BY
		num_emp
';

$result = $db->query($sql);

//foreach ($result as $rec) {
//	echo $rec['num_emp'] . '<br />';
//}