<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$fecha_inicio = '01/01/2008';

$sql = 'SELECT num_cia FROM catalogo_companias WHERE num_cia < 600 OR num_cia IN (702, 703, 704, 705) ORDER BY num_cia';
$cias = $db->query($sql);

$fechas = array('31/01/2008', '29/02/2008', '31/03/2008', '30/04/2008', '31/05/2008', '30/06/2008', '31/07/2008', '31/08/2008', '30/09/2008', '31/10/2008', '30/11/2008', '31/12/2008', '31/01/2009', '28/02/2009', '31/03/2009', '30/04/2009', '31/05/2009');

$query = "SET datestyle = DMY, SQL;\n";
$query .= "DELETE FROM his_sal_pro WHERE fecha >= '$fecha_inicio';\n\n";

foreach ($cias as $c) {
	foreach ($fechas as $f) {
		$sql = "SELECT sum(total) AS importe FROM pasivo_proveedores pp WHERE num_cia = $c[num_cia] AND fecha_mov <= '$f'";
		$tmp = $db->query($sql);
		$pasivo = $tmp ? $tmp[0]['importe'] : 0;
		
		$sql = "SELECT sum(total) AS importe FROM facturas_pagadas fp LEFT JOIN cheques c ON (c.num_cia = fp.num_cia AND c.cuenta = fp.cuenta AND c.folio = fp.folio_cheque) WHERE fp.num_cia = $c[num_cia] AND fecha_mov <= '$f' AND fecha_cheque > '$f'";
		$tmp = $db->query($sql);
		$pagado = $tmp ? $tmp[0]['importe'] : 0;
		
		$saldo = $pasivo + $pagado;
		
		if ($saldo > 0)
			$query .= "INSERT INTO his_sal_pro (num_cia, fecha, saldo) VALUES ($c[num_cia], '$f', $saldo);\n";
	}
	$query .= "\n";
}

echo "<pre>$query</pre>";
$db->query($query);
?>