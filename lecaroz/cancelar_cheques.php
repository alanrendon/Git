<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$folios = array(
/*array(901, 2081, 2550),
array(902, 4177, 4450),
array(903, 5483, 5950),
array(904, 2992, 3150),
array(905, 2553, 2800),
array(906, 1487, 1650),
array(907, 1541, 1950),
array(908, 1426, 1620),
array(909, 432, 670),
array(910, 728, 920),
array(911, 292, 420),
array(912, 231, 420),
array(913, 498, 620),
array(914, 458, 720),
array(916, 217, 720),
array(917, 122, 220)*/
array(900, 528, 670)
);

$sql = '';

foreach ($folios as $f)
	for ($i = $f[1]; $i <= $f[2]; $i++) {
		$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ($i, $f[0], 'FALSE', 'TRUE', CURRENT_DATE, 1);\n";
		$sql .= "INSERT INTO cheques (num_cia, fecha, folio, concepto, imp, fecha_cancelacion, archivo, poliza, cuenta) VALUES ($f[0], CURRENT_DATE, $i, 'CANCELADO', 'TRUE', CURRENT_DATE, 'FALSE', 'FALSE', 1);\n";
	}
	//$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES (320, 915, 'FALSE', 'TRUE', CURRENT_DATE, 1);\n";

echo "<pre>$sql</pre>";
$db->query($sql);
?>