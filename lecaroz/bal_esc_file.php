<?php
include 'DB.php';

$user = "mollendo";
$pass = "pobgnj";
$host = "localhost";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

include './includes/class.session2.inc.php';
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}

//$fecha1="1/".$_GET['mes']."/".$_GET['anio'];

$fecha1=$_REQUEST['tipo'] == 'fecha_con' ? $_GET['anio_con']."/".$_GET['mes_con']."/1" : $_GET['anio']."/".$_GET['mes']."/1";
$fecha2=$_REQUEST['tipo'] == 'fecha_con' ? date("Y/n/j",mktime(0,0,0,$_GET['mes_con']+1,0,$_GET['anio_con'])) : date("Y/n/j",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$num_cias = 0;
// Obtener listado de compañías
for ($i=0; $i<10; $i++)
	if ($_GET['num_cia'.$i] > 0)
		$cia[$num_cias++] = $_GET['num_cia'.$i];

if ($num_cias > 0) {
	$sql="SELECT estado_cuenta.num_cia AS num_cia, catalogo_companias.nombre, estado_cuenta.fecha AS fecha,fecha_con,estado_cuenta.importe AS importe,tipo_mov,estado_cuenta.folio AS folio,(SELECT a_nombre FROM cheques WHERE num_cia = estado_cuenta.num_cia AND folio = estado_cuenta.folio AND cuenta = estado_cuenta.cuenta AND importe = estado_cuenta.importe AND fecha = estado_cuenta.fecha AND fecha_cancelacion IS NULL) AS a_nombre,estado_cuenta.concepto AS concepto FROM estado_cuenta LEFT JOIN catalogo_companias USING(num_cia) WHERE ";
	$sql .= /*in_array($_SESSION['iduser'], $users)*/$_SESSION['tipo_usuario'] == 2 ? " num_cia BETWEEN 900 AND 998 AND" : "";
	$sql .= " num_cia IN (";
	for ($i=0; $i<$num_cias; $i++)
		$sql .= $cia[$i].($i < $num_cias-1?",":") AND");

	$sql.=" estado_cuenta.{$_REQUEST['tipo']} >= '$fecha1' AND estado_cuenta.{$_REQUEST['tipo']} <= '$fecha2' AND cuenta = $_GET[cuenta] ORDER BY estado_cuenta.num_cia,estado_cuenta.{$_REQUEST['tipo']},tipo_mov ASC";
}
else
	$sql="SELECT estado_cuenta.num_cia AS num_cia, catalogo_companias.nombre, estado_cuenta.fecha AS fecha,fecha_con,estado_cuenta.importe AS importe,tipo_mov,estado_cuenta.folio AS folio,(SELECT a_nombre FROM cheques WHERE num_cia = estado_cuenta.num_cia AND folio = estado_cuenta.folio AND cuenta = estado_cuenta.cuenta AND importe = estado_cuenta.importe AND fecha = estado_cuenta.fecha AND fecha_cancelacion IS NULL) AS a_nombre,estado_cuenta.concepto AS concepto FROM estado_cuenta LEFT JOIN catalogo_companias USING(num_cia) WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 998 AND" : "") . " estado_cuenta.{$_REQUEST['tipo']} >= '$fecha1' AND estado_cuenta.{$_REQUEST['tipo']} <= '$fecha2' AND cuenta = $_GET[cuenta] ORDER BY estado_cuenta.num_cia,estado_cuenta.{$_REQUEST['tipo']},tipo_mov ASC";



$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

$nombre_mes[1]="enero";
$nombre_mes[2]="febrero";
$nombre_mes[3]="marzo";
$nombre_mes[4]="abril";
$nombre_mes[5]="mayo";
$nombre_mes[6]="junio";
$nombre_mes[7]="julio";
$nombre_mes[8]="agosto";
$nombre_mes[9]="septiembre";
$nombre_mes[10]="octubre";
$nombre_mes[11]="noviembre";
$nombre_mes[12]="diciembre";

//echo $sql;

$mes=$nombre_mes[$_GET['mes']];

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=estado_cuenta_$mes.csv");

echo "Número de compañía,";
echo "Nombre compañía,";
echo "Fecha del movimiento,";
echo "Fecha conciliación,";
echo "Importe,";
echo "Movimiento,";
echo "Cheque,";
echo "Beneficiario,";
echo "Concepto\n";

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->fecha."\",";
	echo "\"".$row->fecha_con."\",";
	echo "\"".number_format($row->importe,2,'.',',')."\",";
	if($row->tipo_mov == 't'){
		$tipo="RETIRO";
		echo "\"".$tipo."\",";
	}
	else{
		$tipo="DEPOSITO";
		echo "\"".$tipo."\",";
	}
	echo "\"".$row->folio."\",";
	echo "\"".$row->a_nombre."\",";
	echo "\"".$row->concepto."\"\n";
}

?>
