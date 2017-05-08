<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		*
	FROM
		otros_depositos
	WHERE
		fecha >= \'2012/01/01\'
	ORDER BY
		num_cia,
		fecha
';

$result = $db->query($sql);

$sql = '';

foreach ($result as $rec) {
	if (preg_match_all('/(?:\d{2})\/(?:\d{2})\/(?:\d{4}) \((\d{8})\)/', $rec['concepto'], $matches) && intval($matches[1][0]) > 0) {
		$sql .= 'UPDATE otros_depositos SET comprobante = ' . $matches[1][0] . ' WHERE id = ' . $rec['id'] . ";\n";
	}
}

echo "<pre>$sql</pre>";