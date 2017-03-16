<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

/*$sql = '
	SELECT
		id,
		fecha,
		cuenta,
		concepto,
		comprobante,
		importe
	FROM
		estado_cuenta
		LEFT JOIN catalogo_companias
			USING (num_cia)
	WHERE
		comprobante IN (41355658)
	ORDER BY
		num_cia,
		id
';*/

$sql = '
	SELECT
		id,
		num_cia,
		fecha
	FROM
		cometra
	WHERE
		comprobante IN (40759126)
	ORDER BY
		num_cia,
		id
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$num_cia = NULL;
	
	foreach ($result as $rec) {
		if ($num_cia != $rec['num_cia']) {
			$num_cia = $rec['num_cia'];
			
			$dia = 1;
		}
		
		$sql .= 'UPDATE cometra SET fecha = \'' . date('Y/m/d', mktime(0, 0, 0, 9, $dia, 2012)) . '\' WHERE id = ' . $rec['id'] . ";\n";
		
		$dia++;
	}
	
	echo "<pre>$sql</pre>";
}