<?php
include '/var/www/lecaroz/includes/class.db.inc.php';
include '/var/www/lecaroz/includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

//$cuenta = 1;
$cuentas = array(1, 2);
$sql = "";
foreach ($cuentas as $cuenta) {
	$query = "SELECT num_cia, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = $cuenta AND fecha_con IS NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia";
	$movs = $db->query($query);
	
	$sql .= "UPDATE saldos SET saldo_libros = saldo_bancos WHERE cuenta = $cuenta;\n";
	
	if ($movs) {
		foreach ($movs as $mov)
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($mov['tipo_mov'] == "f" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = $cuenta;\n";
	}
}
echo "<pre>$sql</pre>";
$db->query($sql);
?>