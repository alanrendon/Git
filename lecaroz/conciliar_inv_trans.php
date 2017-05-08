<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = 'SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia BETWEEN 1 AND 800 AND cod_banco = 287 ORDER BY num_cia, fecha';
$result = $db->query($sql);

if (!$result) die;

$sql = '';
foreach ($result as $reg) {
	$tmp = $db->query("SELECT folio FROM estado_cuenta WHERE num_cia = $reg[num_cia] AND cuenta = 2 AND cod_mov = 41 AND importe = $reg[importe] ORDER BY folio DESC LIMIT 1");
	$folio = $tmp[0]['folio'];
	
	$sql .= "UPDATE estado_cuenta SET fecha_con = NULL, timestamp = now(), iduser = 1, tipo_con = 0 WHERE num_cia = $reg[num_cia] AND folio = $folio AND importe = $reg[importe] AND cuenta = 2;\n";
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
	$sql .= " SELECT num_cia, fecha, fecha, tipo_mov, importe, 49, 'ANULACION CARGO TRANSFERENCIA RECHAZADA NO $folio', 2, 1, now(), 7 FROM mov_santander WHERE id = $reg[id];\n";
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, timestamp, iduser, tipo_con)";
	$sql .= " SELECT num_cia, fecha, fecha, 'TRUE', importe, 21, 'ANULACION CARGO TRANSFERENCIA RECHAZADA NO $folio', 2, now(), 1, 7 FROM mov_santander WHERE id = $reg[id];\n";
	$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 49, imprimir = 'TRUE', timestamp = now(), iduser = 1 WHERE id = $reg[id];\n";
	$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
}
echo "<pre>$sql</pre>";
$db->query($sql);
?>