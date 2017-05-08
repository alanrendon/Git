<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$inmobiliarias = array(46, 601, 602, 603, 604, 605, 606, 607, 610, 611, 613, 614, 617, 618, 619, 623);
$fecha = '01/12/2006';

$sql = "";
foreach ($inmobiliarias as $inm) {
	$tmp = "SELECT rr.id, num_recibo, cod_arrendador, nombre, num_local, nombre_arrendatario FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS cat ON (cat.id = local) LEFT JOIN catalogo_arrendadores AS car USING (cod_arrendador)";
	$tmp .= " WHERE cod_arrendador = $inm AND fecha = '$fecha' AND num_recibo NOT IN (2445) ORDER BY num_recibo";
	$result = $db->query($tmp);
	
	if ($result && count($result) > 1) {
		$num = array();
		foreach ($result as $reg)
			$num[] = $reg['num_recibo'];
		
		$num = array_reverse($num);
		
		foreach ($result as $i => $reg)
			$sql .= "UPDATE recibos_rentas SET num_recibo = $num[$i] WHERE id = $reg[id];/*arr: $reg[cod_arrendador] $reg[nombre] \t local: $reg[num_local] $reg[nombre_arrendatario] \t\t\t recibo ant: $reg[num_recibo] \t recibo nuevo: $num[$i]*/\n";
		$sql .= "\n";
	}
}
echo "<pre>$sql</pre>";
?>