<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$query = "UPDATE saldos SET saldo_bancos = 0, saldo_libros = 0 WHERE cuenta = 2;\n";

$sql = "SELECT num_cia, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = 2 AND fecha_con IS NOT NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia, tipo_mov";
$movs = $db->query($sql);

$saldo = array();
foreach ($movs as $mov) {
	if (empty($saldo[$mov['num_cia']]))
		$saldo[$mov['num_cia']] = 0;
	
	$saldo[$mov['num_cia']] += $mov['tipo_mov'] == "f" ? $mov['importe'] : -$mov['importe'];
	$query .= "UPDATE saldos SET saldo_bancos = saldo_bancos " . ($mov['tipo_mov'] == "f" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = 2;\n";
}

echo "<pre>$query";
print_r($saldo);
echo "</pre>";

$db->query($query);

?>