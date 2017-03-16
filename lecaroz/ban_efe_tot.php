<?php
// LISTADO DE TOTALES DE EFECTIVOS
// Tablas 'estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/ban_efe_tot.tpl" );
$tpl->prepare();

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'], $fecha);

switch ($fecha[2]) {
	case 1: $mes = "Enero"; break;
	case 2: $mes = "Febrero"; break;
	case 3: $mes = "Marzo"; break;
	case 4: $mes = "Abril"; break;
	case 5: $mes = "Mayo"; break;
	case 6: $mes = "Junio"; break;
	case 7: $mes = "Julio"; break;
	case 8: $mes = "Agosto"; break;
	case 9: $mes = "Septiembre"; break;
	case 10: $mes = "Octubre"; break;
	case 11: $mes = "Noviembre"; break;
	case 12: $mes = "Diciembre"; break;
}
$tpl->assign("mes",$mes);
$tpl->assign("anio",$fecha[3]);

// Dias por mes
$diasxmes[1] = 31;
$diasxmes[2] = ($fecha[3] % 4 == 0)?29:28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;

// Filas por columna
$numfilas = 33;

// Rangos de fecha
$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $_POST['fecha'];

$num_cias = 0;
// Obtener listado de compañías
for ($i=0; $i<30; $i++)
	if ($_POST['cia'.$i] > 0)
		$cia[$num_cias++] = $_POST['cia'.$i];

// Obtener todas las compañias
$sql = "SELECT num_cia FROM catalogo_companias WHERE";
/*if ($_POST['num_cia'] > 0)
	$sql .= " num_cia = $_POST[num_cia] AND";*/
if ($num_cias > 0) {
	$sql .= " num_cia IN (";
	for ($i=0; $i<$num_cias; $i++)
		$sql .= $cia[$i].($i < $num_cias-1?",":") AND");
	
	if (in_array($_SESSION['iduser'], $users))
		$sql .= " num_cia BETWEEN 900 AND 998 AND";
	else
		$sql .= " num_cia BETWEEN 1 AND 899 AND";
}
else if (in_array($_SESSION['iduser'], $users))
	$sql .= " num_cia BETWEEN 900 AND 998 AND";
else if ($_SESSION['iduser'] < 28)
	$sql .= " num_cia BETWEEN 1 AND 899 AND";
if (isset($_POST['idadmin']) && $_POST['idadmin'] > 0)
	$sql .= " idadministrador = $_POST[idadmin] AND";
$sql .= " num_cia != 999 ORDER BY num_cia";

$cia = $db->query($sql);

// Obtener listado de compañías que no se tomaran sus depósitos reales
if (!in_array($_SESSION['iduser'], $users) && isset($_POST['num_cia0']))
	for ($i=0; $i<10; $i++)
		$num_cia[$i] = $_POST['num_cia'.$i];

$filas_count = $numfilas;
for ($i=0; $i<count($cia); $i++) {
	if ($filas_count == $numfilas) {
		$tpl->newBlock("columna");
		
		if ($i > $numfilas-1)
			$tpl->newBlock("columna_vacia");
		
		$filas_count = 0;
	}
	if (isset($num_cia) && $key = array_search($cia[$i]['num_cia'],$num_cia)) {
		$sql = "SELECT SUM(dep1) AS dep1, SUM(dep2) AS dep2 FROM depositos_alternativos WHERE num_cia = {$cia[0]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$result = $db->query($sql);
		if ($result)
			$importe = $result[0]['dep1'] + $result[0]['dep2'];
		else
			$importe = 0;
	}
	else {
		$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE ((num_cia = {$cia[$i]['num_cia']} AND num_cia_sec IS NULL) OR num_cia_sec = {$cia[$i]['num_cia']}) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 16, 44, 99)";
		$result = $db->query($sql);
		if ($result)
			$importe = $result[0]['sum'];
		else
			$importe = 0;
	}
	if ($importe > 0) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("importe",number_format($importe,2,".",","));
		
		$filas_count++;
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>