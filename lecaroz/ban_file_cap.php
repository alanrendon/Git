<?php
include 'DB.php';

$user = "root";
$pass = "pobgnj";
$host = "127.0.0.1";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}

$diasxmes[1]=31;
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
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
cheques.num_cia, 
catalogo_companias.nombre as nombre_cia, 
cheques.num_proveedor, 
catalogo_proveedores.nombre, 
cheques.folio, 
cheques.fecha, 
cheques.importe,
fecha_con,
fecha_cancelacion,
facturas
FROM
cheques
LEFT JOIN estado_cuenta USING (num_cia, folio, cuenta) 
LEFT JOIN catalogo_companias ON (cheques.num_cia = catalogo_companias.num_cia)
LEFT JOIN catalogo_proveedores ON (cheques.num_proveedor = catalogo_proveedores.num_proveedor)
WHERE
cheques.fecha between '$fecha_inicio' AND '$fecha_final'";
$sql .= $_GET['tipo'] > 0 ? " AND fecha_cancelacion IS " . ($_GET['tipo'] == 1 ? 'NULL' : 'NOT NULL') : '';
$sql .= " ORDER BY cheques.num_cia, folio";
$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=cheques.csv");

echo "Número de compañía,";
echo "Nombre compañía,";
echo "Número de proveedor,";
echo "Nombre proveedor,";
echo "Folio de cheque,";
echo "Fecha de elaboracion,";
echo "Importe cheque,";
echo "Fecha conciliación,";
echo "Fecha cancelación,";
echo "Facturas que paga,";
echo "Estado\n";
$importe=0;

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre_cia."\",";
	echo "\"".$row->num_proveedor."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->folio."\",";
	echo "\"".$row->fecha."\",";
	if($row->importe < 0)
		echo "\"".$importe."\",";
	else
		echo "\"".number_format($row->importe,2,'.',',')."\",";
		
	echo "\"".$row->fecha_con."\",";
	echo "\"".$row->fecha_cancelacion."\",";
	echo "\"".$row->facturas." \",";

	if($row->fecha_cancelacion =="" and $row->fecha_con!=""){
		$variable="CONCILIADO";
		echo "\"".$variable."\"\n";
	}
	else if($row->fecha_cancelacion =="" and $row->fecha_con=="" and $row->importe > 0){
		$variable="SIN CONCILIAR";
		echo "\"".$variable."\"\n";
	}
	else if($row->fecha_cancelacion !="" or $row->importe < 0 ){
		$variable="CANCELADO";
		echo "\"".$variable."\"\n";
	}
}
?>