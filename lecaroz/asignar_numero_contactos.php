<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = 'SELECT * FROM "Directorio" WHERE "Status" = 1 ORDER BY "IdContacto"';
$result = $db->query($sql);

$sql = '';
foreach ($result as $k => $r)
	$sql .= 'UPDATE "Directorio" SET "Numero" = ' . ($k + 1) . ' WHERE "IdContacto" = ' . $r['IdContacto'] . ";\n";

echo "<pre>$sql</pre>";
$db->query($sql);
?>