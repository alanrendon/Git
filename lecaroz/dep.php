<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT * FROM estado_cuenta WHERE num_cia = 702 AND fecha >= '01/04/2005' AND cod_mov IN (1,16) AND importe <= 500 ORDER BY fecha";
$dep = $db->query($sql);

?>
<table border="1">
<tr><td>Compañía</td><td>importe</td><td>depositos</td><td>efectivo</td><td>Fecha</td></tr>
<?php
for ($i = 0; $i < count($dep); $i++) {
	?>
	<tr>
	<?php
	echo "<td>" . $dep[$i]['num_cia'] . "</td>"; 
	echo "<td>" . number_format($dep[$i]['importe'],2,".",",") . "</td>";
	$deps = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$dep[$i]['num_cia']} AND fecha = '{$dep[$i]['fecha']}' AND cod_mov IN (1,16) AND importe > 500 GROUP BY fecha ORDER BY fecha");
	echo "<td>" . number_format($deps[0]['sum'],2,".",",") . "</td>";
	$efe = $db->query("SELECT efectivo FROM total_panaderias WHERE num_cia = {$dep[$i]['num_cia']} AND fecha = '{$dep[$i]['fecha']}'");
	echo "<td>" . number_format($efe[0]['efectivo'],2, ".",",") . "</td>";
	echo "<td>" . $dep[$i]['fecha'] . "</td>";
	?>
	</tr>
	<?php
}
?>
</table>