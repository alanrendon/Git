<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

//$sql = "SELECT * FROM cheques WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006' ORDER BY num_cia";
//$result = $db->query($sql);

$sql = "DELETE FROM estado_cuenta WHERE (num_cia, folio, cuenta) IN (SELECT num_cia, folio, cuenta FROM cheques";
$sql .= " WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006') AND fecha = '20/03/2006';\n";
$sql .= "DELETE FROM movimiento_gastos WHERE (num_cia, folio) IN (SELECT num_cia, folio FROM cheques";
$sql .= " WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006') AND fecha = '20/03/2006';\n";
$sql .= "INSERT INTO pasivo_proveedores (num_cia, num_fact, total, descripcion, fecha_mov, fecha_pago, num_proveedor, codgastos)";
$sql .= " SELECT num_cia, num_fact, total, descripcion, fecha_mov, fecha_pago, num_proveedor, codgastos FROM facturas_pagadas WHERE (num_cia, folio_cheque) IN";
$sql .= "(SELECT num_cia, folio FROM cheques WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006');\n";
$sql .= "DELETE FROM facturas_pagadas WHERE (num_cia, folio_cheque) IN (SELECT num_cia, folio FROM cheques";
$sql .= " WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006') AND fecha = '20/03/2006';\n";
$sql .= "UPDATE cheques SET fecha_cancelacion = CURRENT_DATE WHERE num_cheque BETWEEN 32320 AND 32370 AND cuenta = 1 AND fecha = '20/03/2006';\n";

/*$num_cia = NULL;
foreach ($result as $i => $mov) {
	if ($num_cia != $mov['num_cia']) {
		$num_cia = $mov['num_cia'];
		
		$tmp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 1 ORDER BY folio DESC LIMIT 1");
		$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
	}
	$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, proceso, cuenta)";
	$sql .= " SELECT cod_mov, num_proveedor, num_cia, CURRENT_DATE, $folio, importe, iduser, a_nombre, 'FALSE', concepto, facturas, codgastos, proceso, cuenta FROM cheques WHERE";
	$sql .= " id = $mov[id];\n";
	
	$folio++;
}*/
echo "<pre>$sql</pre>";
$db->query($sql);

?>