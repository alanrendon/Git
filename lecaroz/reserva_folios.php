<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$pan = $db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 AND clabe_cuenta2 IS NOT NULL ORDER BY num_cia");
$sql = "";
for ($i = 0; $i < count($pan); $i++) {
	// Obtener ultimo folio
	$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = {$pan[$i]['num_cia']} AND cuenta = 2 ORDER BY folio DESC LIMIT 1");
	if ($temp) {
		$folio = $temp[0]['folio'] + 1;
		for ($j = 0; $j < 10; $j++) {
			$sql .= "INSERT INTO folios_cheque (folio,num_cia,reservado,utilizado,fecha,cuenta) VALUES ($folio,{$pan[$i]['num_cia']},'TRUE','FALSE','2006/12/31',2);\n";
			$folio++;
		}
	}
}

$pan = $db->query("SELECT num_cia FROM catalogo_companias WHERE (num_cia BETWEEN 100 AND 200 OR num_cia IN (700,702,704,800) OR num_cia BETWEEN 600 AND 650) AND clabe_cuenta2 IS NOT NULL ORDER BY num_cia");
for ($i = 0; $i < count($pan); $i++) {
	// Obtener ultimo folio
	$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = {$pan[$i]['num_cia']} AND cuenta = 2 ORDER BY folio DESC LIMIT 1");
	if ($temp) {
		$folio = $temp[0]['folio'] + 1;
		for ($j = 0; $j < 5; $j++) {
			$sql .= "INSERT INTO folios_cheque (folio,num_cia,reservado,utilizado,fecha,cuenta) VALUES ($folio,{$pan[$i]['num_cia']},'TRUE','FALSE','2006/12/31',2);\n";
			$folio++;
		}
	}
}
echo "<pre>$sql</pre>";
$db->query($sql);
?>