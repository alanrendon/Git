<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$fecha = isset($_GET['fecha']) && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha']) ? $_GET['fecha'] : date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y')));

$sql = "SELECT num_cia, num_expendio, nombre, rezago_oficina, rezago_panaderia, (rezago_oficina - rezago_panaderia) AS dif FROM (SELECT num_cia, num_expendio, nombre, (SELECT round(rezago::numeric, 2) FROM mov_expendios WHERE num_cia = ce.num_cia AND num_expendio = ce.num_expendio AND fecha = '$fecha') AS rezago_oficina, (SELECT round(rezago::numeric, 2) FROM mov_exp_tmp WHERE num_cia = ce.num_cia AND num_expendio = ce.num_referencia AND fecha = '$fecha') AS rezago_panaderia FROM catalogo_expendios ce ORDER BY num_cia, num_expendio) result WHERE rezago_oficina <> rezago_panaderia";
$sql .= isset($_GET['num_cia']) && $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
$sql .= isset($_GET['num_exp']) && $_GET['num_exp'] > 0 ? " AND num_expendio = $_GET[num_exp]" : '';
$sql .= ' ORDER BY num_cia, num_expendio';
$result = $db->query($sql);

if (!$result) die('No hay diferencias');

$sql = '';
$cont = 0;

echo '<table border="1" style="font-size:10pt; border-collapse:collapse;"><caption align="bottom">' . count($result) . '</caption><tr><th colspan="8" style="font-size:14pt;">Diferencias de Rezagos en Expendios \'' . $fecha . '\'</th></tr><tr><th>#</th><th>Cia</th><th>Exp</th><th>Nombre</th><th>Ofi</th><th>Pan</th><th>Dif</th><th>Status</th></tr>' . "\n";
foreach ($result as $r) {
	$cont++;
	echo "<tr><td>$cont</td><td>$r[num_cia]</td><td>$r[num_expendio]</td><td>$r[nombre]</td><td>$r[rezago_oficina]</td><td>$r[rezago_panaderia]</td><td>$r[dif]</td><td>";
	echo (abs($r['dif']) <= 0.04 ? 'OK' : '&nbsp;') . "</td></tr>\n";
	if (abs($r['dif']) <= 0.04)
		$sql .= "UPDATE mov_expendios SET rezago_anterior = rezago_anterior - ($r[dif]), rezago = rezago - ($r[dif]) WHERE num_cia = $r[num_cia] AND fecha > '$fecha' AND num_expendio = $r[num_expendio];\n";
}
echo '</table>';
echo "<pre>$sql</pre>";
echo "<p>Total query's: $cont</p>";
//$db->query($sql);
?>