<?php
include 'DB.php';

$user = "root";
$pass = "hola12";
$host = "192.168.2.1";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

include './includes/class.session2.inc.php';
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}


$sql="
SELECT
cheques.num_cia, 
catalogo_companias.nombre as nombre_cia, 
num_proveedor, 
catalogo_proveedores.nombre, 
cheques.folio, 
cheques.fecha, 
cheques.importe,
cheques.concepto,
facturas
FROM
cheques
LEFT JOIN estado_cuenta using(folio,num_cia) 
LEFT JOIN catalogo_companias using(num_cia)
LEFT JOIN catalogo_proveedores using(num_proveedor)
WHERE ";
$sql .= in_array($_SESSION['iduser'], $users) ? "num_cia BETWEEN 900 AND 950 AND " : "";
$sql .= "fecha_con IS NULL and cheques.importe > 0 and fecha_cancelacion IS NULL
order by cheques.num_cia,folio";
$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=cheques_sin_conciliar.csv");

echo "Número de compañía,";
echo "Nombre compañía,";
echo "Número de proveedor,";
echo "Nombre proveedor,";
echo "Folio de cheque,";
echo "Fecha,";
echo "Importe,";
echo "Concepto\n";

$importe=0;

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre_cia."\",";
	echo "\"".$row->num_proveedor."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->folio."\",";
	echo "\"".$row->fecha."\",";
	echo "\"".number_format($row->importe,2,'.',',')."\",";

	if(strlen($row->facturas) <= 2)
		echo "\"".$row->concepto."\"\n";
	
	else
		echo "\"".$row->facturas."\"\n";
	
}
?>