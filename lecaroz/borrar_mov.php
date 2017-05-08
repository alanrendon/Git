<?php
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$cia = ejecutar_script("SELECT num_cia FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200 OR num_cia IN (702,703,704)",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$sql = "SELECT folio FROM folios_cheque WHERE num_cia=".$cia[$i]['num_cia']." ORDER BY folio ASC LIMIT 1";
	echo $sql."<br>";
	$temp = ejecutar_script($sql,$dsn);
	$folio[$cia[$i]['num_cia']] = ($temp)?$temp[0]['folio']:0;
}
echo "<br>";
for ($i=0; $i<count($cia); $i++) {
	$sql = "DELETE FROM mov_banorte_temp WHERE num_cia=".$cia[$i]['num_cia']." AND num_documento<=".$folio[$cia[$i]['num_cia']];
	echo $sql."<br>";
	ejecutar_script($sql,$dsn);
}
?>