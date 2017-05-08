<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$cuentas = array(1, 2);
$sql = "";
foreach ($cuentas as $cuenta) {
	$query = "SELECT num_cia, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = $cuenta AND fecha_con IS NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia";
	$movs = $db->query($query);
	
	$sql .= "UPDATE saldos SET saldo_libros = saldo_bancos WHERE cuenta = $cuenta;\n";
	
	if ($movs)
		foreach ($movs as $mov)
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($mov['tipo_mov'] == "f" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = $cuenta;\n";
}
$db->query($sql);

$fecha_inicio = '01/01/2008';

$sql = 'SELECT num_cia, cuenta, saldo_libros, saldo_bancos FROM saldos WHERE num_cia < 900 ORDER BY num_cia, cuenta';
$saldos = $db->query($sql);

$fechas = array();
for ($i = mktime(0, 0, 0, 1, 1, 2008); $i <= mktime(0, 0, 0, 2, 15, 2009); $i += 86400)
	$fechas[] = date('d/m/Y', $i);

$query = "SET datestyle = DMY, SQL;\n";
$query .= "DELETE FROM his_sal_ban WHERE fecha >= '$fecha_inicio';\n";

foreach ($saldos as $s) {
	foreach ($fechas as $f)
		$query .= "INSERT INTO his_sal_ban (num_cia, fecha, cuenta, saldo_bancos, saldo_libros) VALUES ($s[num_cia], '$f', $s[cuenta], 0, 0);\n";
	
	// Calcular saldo inicial libros
	$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN -importe ELSE importe END) AS importe FROM estado_cuenta WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha_inicio'";
	$tmp = $db->query($sql);
	$dif_lib = $tmp ? $tmp[0]['importe'] : 0;
	
	$saldo_libros_ini = $s['saldo_libros'] + $dif_lib;
	
	$sql = "SELECT fecha, CASE WHEN tipo_mov = 'FALSE' THEN importe ELSE -importe END AS importe FROM estado_cuenta WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha_inicio' ORDER BY fecha";
	$movs = $db->query($sql);
	
	$saldo_libros = $saldo_libros_ini;
	$query .= "UPDATE his_sal_ban SET saldo_libros = $saldo_libros WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha_inicio';\n";
	if ($movs) {
		$fecha = NULL;
		foreach ($movs as $m) {
			if ($fecha != $m['fecha']) {
				if ($fecha != NULL) {
					$query .= "UPDATE his_sal_ban SET saldo_libros = $saldo_libros WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha';\n";
				}
				
				$fecha = $m['fecha'];
			}
			
			$saldo_libros += round($m['importe'], 2);
		}
		if ($fecha != NULL) {
			$query .= "UPDATE his_sal_ban SET saldo_libros = $saldo_libros WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha';\n";
		}
	}
	
	// Calcular saldo inicial bancos
	$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN -importe ELSE importe END) AS importe FROM estado_cuenta WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha_con >= '$fecha_inicio'";
	$tmp = $db->query($sql);
	$dif_ban = $tmp ? $tmp[0]['importe'] : 0;
	
	$saldo_bancos_ini = $s['saldo_bancos'] + $dif_ban;
	
	$sql = "SELECT fecha_con, CASE WHEN tipo_mov = 'FALSE' THEN importe ELSE -importe END AS importe FROM estado_cuenta WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha_con >= '$fecha_inicio' ORDER BY fecha_con";
	$movs = $db->query($sql);
	
	$saldo_bancos = $saldo_bancos_ini;
	$query .= "UPDATE his_sal_ban SET saldo_bancos = $saldo_bancos WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha_inicio';\n";
	if ($movs) {
		$fecha = NULL;
		foreach ($movs as $m) {
			if ($fecha != $m['fecha_con']) {
				if ($fecha != NULL) {
					$query .= "UPDATE his_sal_ban SET saldo_bancos = $saldo_bancos WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha';\n";
				}
				
				$fecha = $m['fecha_con'];
			}
			
			$saldo_bancos += round($m['importe'], 2);
		}
		if ($fecha != NULL) {
			$query .= "UPDATE his_sal_ban SET saldo_bancos = $saldo_bancos WHERE num_cia = $s[num_cia] AND cuenta = $s[cuenta] AND fecha >= '$fecha';\n";
		}
	}
	
	$query .= "\n";
}

//echo "<pre>$query</pre>";
$db->query($query);
echo 'TERMINADO';
?>