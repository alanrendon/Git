<?php
include 'DB.php';

$user = "root";
$pass = "hola12";
$host = "192.168.2.1";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}

$day = date("d");
$month = date("n");
$year = date("Y");
$fecha = date("d/m/Y", mktime(0, 0, 0, $month - 1, 1, $year));

$sql="select num_cia, codmp, existencia, unidadconsumo, upper(catalogo_mat_primas.nombre) as nombre, upper(descripcion) as tipo from inventario_real join catalogo_mat_primas using(codmp)";
//$sql.=" join tipo_unidad_consumo on(unidadconsumo=idunidad) where (num_cia between 1 and 200 or num_cia=702 or num_cia=703 or num_cia=704) and codmp in (";
$sql.=" join tipo_unidad_consumo on(unidadconsumo=idunidad) where num_cia < 100 and ((num_cia, codmp) in (";
$sql.="SELECT num_cia, codmp FROM mov_inv_real WHERE fecha >= '$fecha' AND num_cia < 100 GROUP BY num_cia, codmp";
$sql.=") OR existencia != 0) order by inventario_real.num_cia, catalogo_mat_primas.tipo, catalogo_mat_primas.nombre";
$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=inventario.csv");

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->codmp."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->tipo."\"\n";
}
?>