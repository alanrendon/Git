<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

if ( ! isset($_REQUEST['anio']))
{
	return FALSE;
}

$anio = $_REQUEST['anio'];

$fecha1 = "01/01/{$anio}";
$fecha2 = "31/12/{$anio}";

$sql = '';

// Aguinaldos
// $agu = $db->query("select catalogo_trabajadores.num_cia_emp AS num_cia,sum(aguinaldos.importe) from aguinaldos left join catalogo_trabajadores on (catalogo_trabajadores.id=aguinaldos.id_empleado) where aguinaldos.fecha = '28/12/$anio' and aguinaldos.importe >= 20 AND num_cia_emp < 900 group by catalogo_trabajadores.num_cia_emp order by num_cia_emp");

$sql .= "\n-- QUERYS AQUINALDOS\n\n";

$agu = $db->query("SELECT num_cia, total AS sum FROM aguinaldos_totales ORDER BY num_cia");

for ($i = 0; $i < count($agu); $i++) {
	// Obtener la suma de reservas para aguinaldos
	$res = $db->query("select sum(importe) from reservas_cias where num_cia = {$agu[$i]['num_cia']} and cod_reserva = 1 and anio = $anio AND fecha < '01/12/$anio'");
	$dic = $agu[$i]['sum'] - $res[0]['sum'];//echo "{$agu[$i]['sum']} - {$res[0]['sum']} = $dic<br>";
	if ($id = $db->query("select id from reservas_cias where num_cia = {$agu[$i]['num_cia']} and cod_reserva = 1 and fecha = '01/12/$anio'"))
		$sql .= "update reservas_cias set importe = $dic where id = {$id[0]['id']};\n";
	else
		$sql .= "insert into reservas_cias (num_cia,importe,fecha,cod_reserva,anio) values ({$agu[$i]['num_cia']},$dic,'01/12/$anio',1,$anio);\n";
	$sql .= "update reservas_cias set pagado = {$agu[$i]['sum']} where num_cia = {$agu[$i]['num_cia']} and cod_reserva = 1 and anio = $anio;\n";
}
$sql .= "\n";

$sql .= "\n-- QUERYS IMSS\n\n";

// IMSS
$gas = $db->query("SELECT num_cia, sum(importe) FROM cheques WHERE importe > 0 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 141 AND fecha_cancelacion IS NULL AND num_cia BETWEEN 1 AND 899 AND num_cia NOT IN (SELECT num_cia FROM catalogo_filiales_imss) GROUP BY num_cia UNION SELECT num_cia, sum(importe) FROM pagos_imss WHERE importe > 0 AND anio = $anio AND num_cia BETWEEN 1 AND 899 AND num_cia IN (SELECT num_cia FROM catalogo_filiales_imss) GROUP BY num_cia ORDER BY num_cia");
for ($i = 0; $i < count($gas); $i++) {
	// Obtener la suma de reservas para IMSS
	$res = $db->query("SELECT sum(importe) FROM reservas_cias WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 4 AND anio = $anio AND fecha < '01/12/$anio'");
	$dic = $gas[$i]['sum'] - $res[0]['sum'];
	if ($id = $db->query("SELECT id FROM reservas_cias WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 4 AND fecha = '01/12/$anio'"))
		$sql .= "UPDATE reservas_cias SET importe = $dic WHERE id = {$id[0]['id']};\n";
	else
		$sql .= "INSERT INTO reservas_cias (num_cia, importe, fecha, cod_reserva, anio) VALUES ({$gas[$i]['num_cia']}, $dic, '01/12/$anio', 4, $anio);\n";
	$sql .= "UPDATE reservas_cias SET pagado = {$gas[$i]['sum']} WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 4 AND anio = $anio;\n";
}

$sql .= "\n";

$sql .= "\n-- QUERYS SEGUROS\n\n";

// Seguros
$gas = $db->query("SELECT num_cia, sum(importe) FROM cheques WHERE importe > 0 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 84 AND fecha_cancelacion IS NULL AND num_cia BETWEEN 1 AND 899 GROUP BY num_cia ORDER BY num_cia");
for ($i = 0; $i < count($gas); $i++) {
	// Obtener la suma de reservas para IMSS
	$res = $db->query("SELECT sum(importe) FROM reservas_cias WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 5 AND anio = $anio AND fecha < '01/12/$anio'");
	$dic = $gas[$i]['sum'] - $res[0]['sum'];
	if ($id = $db->query("SELECT id FROM reservas_cias WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 5 AND fecha = '01/12/$anio'"))
		$sql .= "UPDATE reservas_cias SET importe = $dic WHERE id = {$id[0]['id']};\n";
	else
		$sql .= "INSERT INTO reservas_cias (num_cia, importe, fecha, cod_reserva, anio) VALUES ({$gas[$i]['num_cia']}, $dic, '01/12/$anio', 5, $anio);\n";
	$sql .= "UPDATE reservas_cias SET pagado = {$gas[$i]['sum']} WHERE num_cia = {$gas[$i]['num_cia']} AND cod_reserva = 5 AND anio = $anio;\n";
}

$sql .= "\n";

//$gas = $db->query("SELECT num_cia, sum(importe) FROM pagos_imss WHERE anio = 2007 GROUP BY num_cia ORDER BY num_cia");
//for ($i = 0; $i < count($gas); $i++) {
//	// Obtener la suma de reservas para aguinaldos
//	$res = $db->query("select sum(importe) from reservas_cias where num_cia = {$gas[$i]['num_cia']} and cod_reserva = 4 and anio = 2007");
//	$dic = $gas[$i]['sum'] - $res[0]['sum'];
//	if ($id = $db->query("select id from reservas_cias where num_cia = {$gas[$i]['num_cia']} and cod_reserva = 4 and fecha = '01/12/2007'"))
//		$sql .= "update reservas_cias set importe = $dic where id = {$id[0]['id']};\n";
//	else
//		$sql .= "insert into reservas_cias (num_cia,importe,fecha,cod_reserva,anio) values ({$gas[$i]['num_cia']},$dic,'2007/12/01',4,2007);\n";
//	$sql .= "update reservas_cias set pagado = {$gas[$i]['sum']} where num_cia = {$gas[$i]['num_cia']} and cod_reserva = 4 and anio = 2007;\n";
//}

echo "<pre>$sql</pre>";
//$db->query($sql);
?>
