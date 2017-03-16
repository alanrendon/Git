<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = "historico";
$db = new Dbclass($dsn,$tabla,$_POST);//INSERTA DATOS A LA TABLA DE "FACT_ROSTICERIAS"
$db->xinsertar();
header("location: ./historico.php");
?>
