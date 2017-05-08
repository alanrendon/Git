<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT id, num_cia, tipo_mov, importe FROM mov_banorte WHERE cod_banco IN (539, 540, 517, 803) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899 ORDER BY num_cia";
//$sql = "SELECT id, num_cia, tipo_mov, importe FROM mov_santander WHERE cod_banco IN (810, 571) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899 ORDER BY num_cia";
$result = $db->query($sql);

$sql = "";

//$sql .= "UPDATE mov_banorte SET cod_mov = 9 WHERE cod_banco = 537 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
$sql .= "UPDATE mov_banorte SET cod_mov = 9 WHERE cod_banco = 803 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
$sql .= "UPDATE mov_banorte SET cod_mov = 38 WHERE cod_banco = 540 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
$sql .= "UPDATE mov_banorte SET cod_mov = 10 WHERE cod_banco = 517 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
$sql .= "UPDATE mov_banorte SET cod_mov = 3 WHERE cod_banco = 539 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_banorte SET cod_mov = 78 WHERE cod_banco = 590 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_banorte SET cod_mov = 46 WHERE cod_banco = 600 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_banorte SET cod_mov = 10 WHERE cod_banco = 601 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";

// CAMBIA CODIGO DE MOVIMIENTO
//$sql .= "UPDATE mov_santander SET cod_mov = 12 WHERE cod_banco IN (570, 510) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 11 WHERE cod_banco = 60 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 38 WHERE cod_banco = 805 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 14 WHERE cod_banco = 810 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 10 WHERE cod_banco = 571 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 38 WHERE cod_banco = 501 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 42 WHERE cod_banco = 857 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 46 WHERE cod_banco = 864 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 44 WHERE cod_banco = 38 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 3 WHERE cod_banco = 816 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 15 WHERE cod_banco = 812 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 17 WHERE cod_banco = 858 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 52 WHERE cod_banco = 806 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 78 WHERE cod_banco = 1790 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 81 WHERE cod_banco = 514 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 74 WHERE cod_banco = 10 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 78 WHERE cod_banco = 1790 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 21 WHERE cod_banco = 514 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 29 WHERE cod_banco = 10 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 72 WHERE cod_banco = 868 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 73 WHERE cod_banco = 833 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 38 WHERE cod_banco = 835 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 104 WHERE cod_banco = 877 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//$sql .= "UPDATE mov_santander SET cod_mov = 72 WHERE cod_banco = 870 AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";

// INSERTA MOVIMIENTOS AL SISTEMA
//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser, timestamp, tipo_con)";
//$sql .= " SELECT num_cia, fecha, fecha, tipo_mov, importe, cod_mov, num_documento, concepto, 2, 1, now(), 8 FROM mov_santander WHERE id IN";
//$sql .= " (SELECT id FROM mov_santander WHERE cod_banco IN (810, 571) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899);\n";

$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser, timestamp, tipo_con)";
$sql .= " SELECT num_cia, fecha, fecha, tipo_mov, importe, cod_mov, num_documento, concepto, 1, 1, now(), 8 FROM mov_banorte WHERE id IN";
$sql .= " (SELECT id FROM mov_banorte WHERE cod_banco IN (539, 540, 517, 803) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899);\n";

// GENERA BONFICACIONES
$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 34, 'BONIF ' || concepto, 1, 1, now(), 0 FROM mov_banorte WHERE cod_banco IN (517)";
$sql .= " AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";

$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 35, 'BONIF ' || concepto, 1, 1, now(), 0 FROM mov_banorte WHERE cod_banco IN (539, 540, 803)";
$sql .= " AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";

//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
//$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 34, 'BONIF ' || concepto, 2, 1, now(), 0 FROM mov_santander WHERE cod_banco IN (571)";
//$sql .= " AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//
//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
//$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 35, 'BONIF ' || concepto, 2, 1, now(), 0 FROM mov_santander WHERE cod_banco IN (816, 805, 806, 858, 833, 877)";
//$sql .= " AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";

// ACTUALIZA SALDOS
foreach ($result as $id)
//	$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos " . ($id['tipo_mov'] == "f" ? "+" : "-") . " $id[importe], saldo_libros = saldo_libros " . ($id['tipo_mov'] == "f" ? "+" : "-") . " $id[importe] WHERE num_cia = $id[num_cia] AND cuenta = 2;\n";
	$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos " . ($id['tipo_mov'] == "f" ? "+" : "-") . " $id[importe], saldo_libros = saldo_libros " . ($id['tipo_mov'] == "f" ? "+" : "-") . " $id[importe] WHERE num_cia = $id[num_cia] AND cuenta = 1;\n";

//$sql .= "UPDATE mov_santander SET imprimir = 'TRUE', fecha_con = fecha, iduser = 1, timestamp = now() WHERE cod_banco IN (810, 571) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
$sql .= "UPDATE mov_banorte SET imprimir = 'TRUE', fecha_con = fecha, iduser = 1, timestamp = now() WHERE cod_banco IN (539, 540, 517, 803) AND fecha_con IS NULL AND num_cia BETWEEN 1 AND 899;\n";
//echo "<pre>$sql</pre>";die;

$db->query($sql);
header("location: ./ban_mov_pen_v2.php?list=1&cuenta=1");
?>