<?php
include 'DB.php';

$user = "root";
$pass = "hola12";
$host = "192.168.1.250";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}


$sql="
SELECT 
codmp,
nombre
FROM 
catalogo_mat_primas
WHERE
controlada='FALSE'
order by tipo,nombre
";
$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=mp_no_controlada.csv");

echo "Número,";
echo "Nombre\n";

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->codmp."\",";
	echo "\"".$row->nombre."\"\n";
}
?>