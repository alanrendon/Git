<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$total_fac=0;
$session = new sessionclass($dsn);
//print_r($_POST)."<br><br>";

$fecha=date("j/n/Y");
echo "$fecha<br>";
$fecha2=date("j/n/Y",mktime(0,0,0,date("n"),date("j")-7,date("Y")));
echo $fecha2;

?>