<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT * FROM \"Directorio\"";
$result = $db->query($sql);

print_r($result);
?>