<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

if (trim($_GET['valor']) == '') die;

$sql = "SELECT $_GET[campo] FROM $_GET[tabla] WHERE $_GET[criterio] = $_GET[valor]";
$result = $db->query($sql);

if (!$result) die;

echo $result[0][$_GET['campo']];
?>