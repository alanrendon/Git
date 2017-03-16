<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

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
		num_emp,
		id
';

$result = $db->query($sql);

if ($result) {
	$sql = '
		SELECT
			MAX(num_emp) + 1
				AS number
		FROM
			catalogo_trabajadores
		WHERE
			num_cia < 900
	';
	
	$tmp = $db->query($sql);
	
	$number = $tmp[0]['number'];
	
	$sql = '';
	
	$num_emp = NULL;
	foreach ($result as $rec) {
		if ($num_emp != $rec['num_emp']) {
			$num_emp = $rec['num_emp'];
		}
		else {
			$sql .= 'UPDATE catalogo_trabajadores SET num_emp = ' . $number . ' WHERE id = ' . $rec['id'] . ";\n";
			
			$number++;
		}
	}
	
	echo "<pre>$sql</pre>";
}