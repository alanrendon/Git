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

$nombre_mes[1]= "enero";
$nombre_mes[2]= "febrero";
$nombre_mes[3]= "marzo";
$nombre_mes[4]= "abril";
$nombre_mes[5]= "mayo";
$nombre_mes[6]= "junio";
$nombre_mes[7]= "julio";
$nombre_mes[8]= "agosto";
$nombre_mes[9]= "septiembre";
$nombre_mes[10]= "octubre";
$nombre_mes[11]= "noviembre";
$nombre_mes[12]= "diciembre";


$fecha_inicio=$_GET['anio']."/".$_GET['mes']."/1";

$fecha_final=$_GET['anio']."/".$_GET['mes']."/".$diasxmes[$_GET['mes']];

$sql="
SELECT 
num_cia,
nombre_corto,
letra_folio, 
num_remi, 
fecha_entrega, 
total_factura 
FROM 
venta_pastel 
JOIN 
catalogo_companias 
USING(num_cia) 
WHERE 
estado = 1 AND 
kilos IS NOT NULL AND ";

$nombre_archivo="pasteles_";
if($_GET['tipo_con'] == 0){
	$sql.="fecha_entrega >= '$fecha_inicio' order by num_cia, fecha_entrega, num_remi";
	$nombre_archivo.="desde_".$nombre_mes[$_GET['mes']].".csv";
}
else{
	$sql.="fecha_entrega between '$fecha_inicio' and '$fecha_final' order by num_cia, fecha_entrega, num_remi";
	$nombre_archivo.="solo_".$nombre_mes[$_GET['mes']].".csv";
}

$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();


header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=$nombre_archivo");

echo "Nъmero de compaснa,";
echo "Nombre compaснa,";
echo "Letra de nota,";
echo "Folio de nota,";
echo "Fecha de entrega,";
echo "Monto,";
echo "Calificaciуn,";
echo "Observaciуn\n";
$importe=0;
$blanco="";
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre_corto."\",";
	if($row->letra_folio == 'X')
		echo "\"".$blanco."\",";
	else
		echo "\"".$row->letra_folio."\",";
	echo "\"".$row->num_remi."\",";
	echo "\"".$row->fecha_entrega."\",";
	echo "\"".number_format($row->total_factura,2,'.',',')."\"\n";
}
?>