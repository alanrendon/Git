<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia, nombre, sum(saldo_libros) AS saldo FROM saldos LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 100 GROUP BY num_cia, nombre ORDER BY num_cia";
$cias = $db->query($sql);

$data = array();
foreach ($cias as $cia) {
	$data[$cia['num_cia']]['nombre'] = $cia['nombre'];
	$data[$cia['num_cia']]['saldo'] = round($cia['saldo']);
	
	$tmp = $db->query("SELECT sum(total) FROM pasivo_proveedores WHERE num_cia = $cia[num_cia]");
	$data[$cia['num_cia']]['pas'] = $tmp[0]['sum'] != 0 ? round($tmp[0]['sum']) : 0;
	
	$data[$cia['num_cia']]['dif'] = round($data[$cia['num_cia']]['saldo'] - $data[$cia['num_cia']]['pas']);
}

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=saldos.csv");

echo '"No","Nombre","Saldo Banco","Saldo Proveedores","Diferencia"' . "\n";
foreach ($data as $cia => $reg) {
	echo "\"$cia\",";
	echo "\"$reg[nombre]\",";
	echo "\"$reg[saldo]\",";
	echo "\"$reg[pas]\",";
	echo "\"$reg[dif]\"\n";
}
?>