<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$query = "";

$sql = "SELECT num_cia, importe FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 10 AND importe = 2.25 ORDER BY num_cia;";
$result = $db->query($sql);
foreach ($result as $reg)
	$query .= "UPDATE saldos SET saldo_bancos = saldo_bancos + 2.25 WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";

$sql = "SELECT num_cia, importe FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 17 AND importe = 15 ORDER BY num_cia;";
$result = $db->query($sql);
foreach ($result as $reg)
	$query .= "UPDATE saldos SET saldo_bancos = saldo_bancos + 15 WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";

$query .= "INSERT INTO mov_santander (num_cia, fecha, tipo_mov, importe, concepto, cod_banco, aut, descripcion)";
$query .= " SELECT num_cia, fecha, tipo_mov, importe, concepto, 571, 'FALSE', concepto FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 10 AND importe = 2.25;\n";
$query .= "INSERT INTO mov_santander (num_cia, fecha, tipo_mov, importe, concepto, cod_banco, aut, descripcion)";
$query .= " SELECT num_cia, fecha, tipo_mov, importe, concepto, 858, 'FALSE', concepto FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 17 AND importe = 15;\n";
$query .= "DELETE FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 10 AND importe = 2.25;\n";
$query .= "DELETE FROM estado_cuenta WHERE fecha = '2006/04/28' AND cod_mov = 17 AND importe = 15;\n";

echo "<pre>$query</pre>";
$db->query($query);
?>