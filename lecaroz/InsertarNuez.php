<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		num_cia,
		codmp
	FROM
		inventario_real
	WHERE
		codmp IN (51, 1019)
		AND (num_cia, codmp) NOT IN (
			SELECT
				num_cia,
				codmp
			FROM
				control_avio
			WHERE
				codmp IN (51, 1019)
			GROUP BY
				num_cia,
				codmp
		)
		AND num_cia NOT IN (62, 105, 108, 117)
	ORDER BY
		codmp,
		num_cia
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$num_cia = NULL;
	
	foreach ($result as $rec) {
		$sql .= 'UPDATE control_avio SET num_orden = num_orden + 1 WHERE num_cia = ' . $rec['num_cia'] . ' AND num_orden ' . ($rec['codmp'] == 51 ? '>=' : '>') . ' (SELECT num_orden FROM control_avio WHERE num_cia = ' . $rec['num_cia'] . ' AND codmp = ' . ($rec['codmp'] == 51 ? 1019 : 51) . ' GROUP BY num_orden)' . ";\n";
		
		$sql .= 'INSERT INTO control_avio (num_cia, codmp, cod_turno, num_orden) SELECT num_cia, ' . $rec['codmp'] . ', cod_turno, num_orden + 1 FROM control_avio WHERE num_cia = ' . $rec['num_cia'] . ' AND codmp = ' . ($rec['codmp'] == 51 ? 1019 : 51) . ";\n";
	}
	
	echo "<pre>$sql</pre>";
}