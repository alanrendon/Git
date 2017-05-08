<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include '/var/www/lecaroz/includes/class.db.inc.php';
include '/var/www/lecaroz/includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = 'SELECT * FROM facturas_zap WHERE tspago IS NULL AND copia_fac = \'TRUE\' AND por_aut = \'TRUE\' AND clave > 0 ORDER BY num_proveedor, num_fact';
$result = $db->query($sql);

$query = '';
$html = '<table><tr><th>ID</th><th>Proveedor</th><th>Remision</th><th>Dif</th></tr>';
foreach ($result as $fac) {
	// Seleccionar id del nombre del proveedor
	$sql = "SELECT id FROM catalogo_proveedores LEFT JOIN catalogo_nombres ON (num = clave_seguridad) WHERE num_proveedor = $fac[num_proveedor]";
	$id = $db->query($sql);
	
	// Si nop tiene clave el proveedor, omitir remisión
	if (!($id[0]['id'] > 0))
		continue;
	
	
	// Buscar depositos acreditados para el proveedor
	$sql = "SELECT fecha, importe, num_fact1, pag1, num_fact2, pag2, num_fact3, pag3, num_fact4, pag4, tsins FROM otros_depositos";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia) WHERE idnombre = {$id[0]['id']} AND fecha >= '$fac[fecha]' AND (num_fact1 = '$fac[num_fact]' OR num_fact2 = '$fac[num_fact]'";
	$sql .= " OR num_fact3 = '$fac[num_fact]' OR num_fact4 = '$fac[num_fact]') ORDER BY fecha";
	$dep = $db->query($sql);
	
	$dif = $fac['total'];
	if (!$dep)
		continue;
	
	$tsins = '';
	foreach ($dep as $d) {
		if ($dif <= 0)
			continue;
		
		if ($d['num_fact1'] == $fac['num_fact'])
			$dif -= $d['pag1'] > 0 ? $d['pag1'] : $dif;
		else if ($d['num_fact2'] == $fac['num_fact'])
			$dif -= $d['pag2'] > 0 ? $d['pag2'] : $dif;
		else if ($d['num_fact3'] == $fac['num_fact'])
			$dif -= $d['pag3'] > 0 ? $d['pag3'] : $dif;
		else if ($d['num_fact4'] == $fac['num_fact'])
			$dif -= $d['pag4'] > 0 ? $d['pag4'] : $dif;
		
		$tsins = $d['fecha'] . ' 12:00:00';
	}
	
	$html .= '<tr>';
	$html .= "<td>$fac[id]</td><td>$fac[num_proveedor]</td><td>$fac[num_fact]</td><td>" . round($dif, 2) . "</td></tr>";
	
	if (round($dif, 2) >= -0.10 && round($dif, 2) <= 0.10)
		$query .= "UPDATE facturas_zap SET folio = 0, cuenta = 0, tspago = '$tsins' WHERE id = $fac[id];\n";
}
$html .= '</table>';

echo $html;
echo "<pre>$query</pre>";
if ($query != '') $db->query($query);
?>
