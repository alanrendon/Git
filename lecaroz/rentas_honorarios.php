<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

if (!isset($_GET['folio'])) die;

$db = new DBclass($dsn, "autocommit=yes");

$rel = array(
	array(605, 625),
	array(312, 628),
	array(423, 601),
	array(434, 617),
	array(417, 614),
	array(435, 611),
	array(576, 618),
	array(174, 603),
	array(441, 613),
	array(644, 605),
	array(171, 604),
	array(173, 607),
	array(422, 610),
	array(436, 606),
	array(609, 623),
	array(290, 627),
	array(945, 615),
	array(948, 616),
	array(176, 612),
	array(433, 619),
	array(172, 602),
	array(230, 700),
	array(229, 800)
);

$sql = "";
foreach ($rel as $r) {
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con) SELECT $r[1], fecha_gen, 'FALSE', te.importe,";
	$sql .= " CASE WHEN concepto LIKE '%RENTA%' THEN 2 WHEN concepto LIKE '%HONORARIOS%' OR concepto LIKE '%OFICINA%' OR concepto LIKE '%TALLERES LECAROZ%' THEN 29 END, CASE WHEN concepto LIKE '%RENTA%' THEN 'RENTA (' || nombre_corto || ')' WHEN";
	$sql .= " concepto LIKE '%HONORARIOS%' THEN 'HONORARIOS (' || nombre_corto || ')' WHEN concepto LIKE '%OFICINA%' THEN 'OFICINA (' || nombre_corto || ')' WHEN concepto LIKE '%TALLERES LECAROZ%' THEN 'TALLERES (' || nombre_corto || ')' END, CASE WHEN tipo = 'FALSE' THEN 2 ELSE 1 END, 1, CURRENT_TIMESTAMP, 0 FROM";
	$sql .= " transferencias_electronicas AS te LEFT JOIN cheques USING (num_cia, folio, cuenta) LEFT JOIN catalogo_companias USING (num_cia) WHERE te.num_proveedor = $r[0] AND";
	$sql .= " folio_archivo = $_GET[folio] AND te.status = 1;\n";
}

echo "<pre>$sql</pre>";
$db->query($sql);
?>