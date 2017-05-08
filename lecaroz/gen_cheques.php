<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$cuenta = 1;

//$cia = $db->query("SELECT num_cia, nombre, saldo_libros FROM saldos LEFT JOIN catalogo_companias USING (num_cia) WHERE cuenta = $cuenta AND num_cia < 800 AND num_cia NOT IN (623, 624, 625, 626, 627, 628, 630) AND clabe_cuenta IS NOT NULL ORDER BY num_cia");

$cia = $db->query("SELECT num_cia, nombre, saldo_libros FROM saldos LEFT JOIN catalogo_companias USING (num_cia) WHERE cuenta = $cuenta AND num_cia IN (362, 403, 411, 417, 379, 319, 412, 336, 73, 90, 378) AND clabe_cuenta IS NOT NULL ORDER BY num_cia");

$sql = "";
foreach ($cia as $reg) {
	$tmp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $reg[num_cia] AND cuenta = $cuenta ORDER BY folio DESC LIMIT 1");
	$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
	
	$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, codgastos, proceso, cuenta, poliza, archivo) VALUES";
	$sql .= " (5, 1200, $reg[num_cia], CURRENT_DATE, $folio, 1, 1, 'AL PORTADOR', 'FALSE', 'PRUEBA BANORTE', 999, 'FALSE', $cuenta, FALSE, TRUE);\n";
	$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, folio, concepto, cuenta) VALUES (999, $reg[num_cia], CURRENT_DATE, 1, 'TRUE', $folio, 'TRASPASO', $cuenta);\n";
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta) VALUES ($reg[num_cia], CURRENT_DATE, 'TRUE', 1, 5, $folio, 'PRUEBA BANORTE', $cuenta);\n";
	$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ($folio, $reg[num_cia], 'FALSE', 'TRUE', CURRENT_DATE, $cuenta);\n";
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta) VALUES ($reg[num_cia], CURRENT_DATE, 'FALSE', 1, 29, NULL, 'PRUEBA BANORTE', $cuenta);\n";
}

echo "<pre>$sql</pre>";
$db->query($sql);

?>