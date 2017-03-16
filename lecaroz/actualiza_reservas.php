<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia FROM reservas_cias WHERE fecha >= '01/01/2006' GROUP BY num_cia ORDER BY num_cia";
$result = $db->query($sql);

$sql = "";
foreach ($result as $reg) {
	$imp = $db->query("SELECT cod_reserva, importe FROM reservas_cias WHERE num_cia = $reg[num_cia] AND fecha = '2006/09/01' ORDER BY cod_reserva");
	foreach ($imp as $i)
		$sql .= "UPDATE reservas_cias SET importe = $i[importe] WHERE num_cia = $reg[num_cia] AND cod_reserva = $i[cod_reserva] AND fecha BETWEEN '2006/10/01' AND '2006/11/30';\n";
}

echo "<pre>$sql</pre>";
$db->query($sql);
?>