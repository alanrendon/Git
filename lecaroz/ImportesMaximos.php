<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = 'SELECT num_cia, nombre_corto, clabe_cuenta FROM catalogo_companias ORDER BY num_cia';
$result = $db->query($sql);

$data = "No,Nombre,Cuenta,Importe,Importe x 4\n";
foreach ($result as $reg) {
	$sql = "SELECT sum(importe) / 15 AS importe FROM (SELECT importe FROM estado_cuenta WHERE num_cia = $reg[num_cia] AND fecha BETWEEN '2008/01/01' AND '2008/12/31' AND cod_mov IN (1, 16, 44, 99) ORDER BY importe DESC LIMIT 15) result";
	$importe = $db->query($sql);
	
	if ($importe[0]['importe'] <= 0)
		continue;
	
	$data .= "$reg[num_cia],$reg[nombre_corto],$reg[clabe_cuenta]," . round($importe[0]['importe'], 2) . ',' .round($importe[0]['importe'] * 4, 2) . "\n";
}

header('Content-Type: application/download');
header('Content-Disposition: attachment; filename=Maximos.csv');
echo $data;
?>