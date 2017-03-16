<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT imagen FROM imagenes WHERE id_img = 5";
$result = $db->query($sql);

header("Content-Type: image/jpeg");
echo pg_unescape_bytea($result[0]['imagen']);

?>