<?php
include 'DB.php';


$user = "root";
$pass = "pobgnj";
$host = "127.0.0.1";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

include './includes/class.session2.inc.php';
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}

$diasxmes[1]=31;
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero aсo bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre


$fecha_inicio=$_GET['anio']."/".$_GET['mes']."/1";

$fecha_final=$_GET['anio']."/".$_GET['mes']."/".$diasxmes[$_GET['mes']];

$sql="
SELECT 
num_cia,
nombre,
concepto,
fecha,
importe,
fecha_con, cuenta
FROM estado_cuenta JOIN catalogo_companias using(num_cia)
where";
$sql .= in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950 AND" : "";
$sql .= " fecha between '$fecha_inicio' and '$fecha_final' and cod_mov in(1,16,44) order by num_cia,fecha
";
$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=depositos.csv");

echo "Nъmero de compaснa,";
echo "Nombre compaснa,";
echo "Concepto,";
echo "Fecha del movimiento,";
echo "Importe,";
echo "Banco,";
echo "Fecha conciliaciуn,";
echo "Estado\n";

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->concepto."\",";
	echo "\"".$row->fecha."\",";
	echo "\"".number_format($row->importe,2,'.',',')."\",";
	echo "\"".($row->cuenta == 1 ? "BANORTE" : "SANTANDER")."\",";
	echo "\"".$row->fecha_con."\",";

	if($row->fecha_con!=""){
		$variable="CONCILIADO";
		echo "\"".$variable."\"\n";
	}
	else if($row->fecha_con==""){
		$variable="SIN CONCILIAR";
		echo "\"".$variable."\"\n";
	}
}
?>