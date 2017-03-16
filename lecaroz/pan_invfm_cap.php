<?php
//define ('IDSCREEN',6213); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Lo siento pero no hay registros de inventarios para esta compañía";
$descripcion_error[2] = "Ya existen inventarios para esa fecha";
$descripcion_error[3] = "Ya se han generado diferencias para el periodo solicitado";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_invfm_cap.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


// Seleccionar tabla
$tpl->assign("tabla","inventario_fin_mes");


// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y", mktime(0,0,0,date("m"),0,date("Y"))));
	$tpl->assign("mes",date("m", mktime(0,0,0,date("m"),0,date("Y"))));

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++)
	{
			$tpl->newBlock("nom_cia");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}

	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

if (ejecutar_script("SELECT id FROM balances_pan WHERE anio = {$_REQUEST['anio']} AND mes = {$_REQUEST['mes']} LIMIT 1", $dsn))
{
	header("location: ./pan_invfm_cap.php?codigo_error=3");
	die;
}

$diasxmes[1] = 31; // Enero
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

$fecha = date("d/m/Y", mktime(0,0,0,$_GET['mes'],$diasxmes[(int)$_GET['mes']],$_GET['anio']));
$fecha_mov = date("d/m/Y", mktime(0, 0, 0, date("m") - 2, 1, date("Y")));
$fecha_mov2 = date("d/m/Y", mktime(0, 0, 0, date("m"), 0, date("Y")));

$sql = '
	UPDATE
		inventario_fin_mes
	SET
		existencia = result.existencia,
		diferencia = result.existencia - inventario,
		precio_unidad = result.precio_unidad
	FROM
		(
			SELECT
				num_cia,
				codmp,
				existencia,
				precio_unidad
			FROM
				inventario_real
			WHERE
				num_cia = ' . $_GET['num_cia'] . '
		)
			AS
				result
	WHERE
			inventario_fin_mes.num_cia = ' . $_GET['num_cia'] . '
		AND
			fecha >= \'' . date('d/m/Y', mktime(0, 0, 0, date('n') - 1, 1, date('Y'))) . '\'
		AND
			inventario_fin_mes.num_cia = result.num_cia
		AND
			inventario_fin_mes.codmp = result.codmp
';

//$sql = "UPDATE inventario_fin_mes SET existencia=inventario_real.existencia,diferencia=inventario_real.existencia-inventario,precio_unidad=inventario_real.precio_unidad WHERE";
//$sql .= " num_cia = $_GET[num_cia]";
//$sql .= " AND fecha >= '".date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")))."' AND num_cia=inventario_real.num_cia AND codmp=inventario_real.codmp";
ejecutar_script($sql,$dsn);

$tpl->newBlock("inventario");

$sql = "SELECT num_cia, codmp, existencia, nombre, controlada, precio_unidad FROM inventario_fin_mes LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $_GET[num_cia]";
$sql .= " AND fecha = '$fecha' ORDER BY tipo, nombre, codmp";
$inv = ejecutar_script($sql, $dsn);
if (!$inv) {
	$sql="SELECT num_cia, codmp, existencia, nombre, controlada, precio_unidad FROM inventario_real LEFT JOIN catalogo_mat_primas USING(codmp) WHERE num_cia=$_GET[num_cia]";
	$sql .= " AND (codmp IN (SELECT codmp FROM mov_inv_real WHERE fecha >= '$fecha_mov' AND num_cia = $_GET[num_cia] GROUP BY codmp) OR existencia != 0)";
	$sql .= " ORDER BY tipo, nombre, codmp";
	$inv=ejecutar_script($sql,$dsn);
}

$cia1 = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);

/*if(existe_registro("inventario_fin_mes",array("num_cia","fecha"),array($_GET['num_cia'],$fecha),$dsn))
{
	header("location: ./pan_invfm_cap.php?codigo_error=2");
	die;
}*/

$tpl->assign("nombre_cia",$cia1[0]['nombre_corto']);
$tpl->assign("fecha",$fecha);
$tpl->assign("numfilas",count($inv));

if(!$inv)
{
	header("location: ./pan_invfm_cap.php?codigo_error=1");
	die;
}

else{
	function buscar($codmp) {
		global $result;

		if (!$result)
			return FALSE;

		for ($i = 0; $i < count($result); $i++)
			if ($codmp == $result[$i]['codmp'])
				return $i;

		return FALSE;
	}

	$sql = "SELECT codmp, inventario, diferencia FROM inventario_fin_mes WHERE num_cia=$_GET[num_cia] AND fecha='$fecha' ORDER BY codmp";
	$result = ejecutar_script($sql, $dsn);

	for($i=0;$i<count($inv);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("back",($i > 0)?$i-1:count($inv)-1);
		$tpl->assign("next",($i < count($inv)-1)?$i+1:0);

		$tpl->assign("fecha",/*date("d/m/Y", mktime(0,0,0,$_GET['mes'],$diasxmes[$_GET['mes']],$_GET['anio']))*/$fecha);
		$tpl->assign("num_cia",$inv[$i]['num_cia']);
		$tpl->assign("codmp",$inv[$i]['codmp']);
		$tpl->assign("existencia",$inv[$i]['existencia']);
		$tpl->assign("fexistencia",number_format($inv[$i]['existencia'],2,".",","));
		//$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($inv[$i]['codmp']),"","",$dsn);
		$tpl->assign("nombre_mp", "<font color=\"#" . ($inv[$i]['controlada'] == "TRUE" ? "0000FF" : "FF0000") . "\">" . $inv[$i]['nombre'] . "</font>");
		//$pu = obtener_registro("inventario_real",array("num_cia","codmp"),array($inv[$i]['num_cia'], $inv[$i]['codmp']),"","",$dsn);
		$tpl->assign("precio_unidad",$inv[$i]['precio_unidad']);

		//$sql = "SELECT inventario,diferencia FROM inventario_fin_mes WHERE num_cia=$_GET[num_cia] AND codmp=".$inv[$i]['codmp']." AND fecha='$fecha'";
		//$result = ejecutar_script($sql,$dsn);
		if (/*$result*/($index = buscar($inv[$i]['codmp'])) !== FALSE) {
			/*$tpl->assign("inventario",($result[0]['inventario'] != 0)?number_format($result[0]['inventario'],2,".",""):"");
			$tpl->assign("diferencia",number_format($result[0]['diferencia'],2,".",""));*/
			$tpl->assign("inventario", $result[$index]['inventario'] != 0?number_format($result[$index]['inventario'],2,".",""):"");
			$tpl->assign("diferencia", number_format($result[$index]['diferencia'],2,".",""));
		}
		else
			$tpl->assign("diferencia",number_format($inv[$i]['existencia'],2,".",""));
	}

	$numfilas = 30;
	// Generar listado de materias primas
	$mp = ejecutar_script("SELECT codmp,nombre FROM catalogo_mat_primas WHERE " . ($cia1[0]['tipo_cia'] == 2 ? "tipo_cia=FALSE OR codmp = 90" : "tipo_cia=TRUE") . " ORDER BY codmp",$dsn);
	for ($i=0; $i<count($mp); $i++) {
		$tpl->newBlock("mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre",$mp[$i]['nombre']);
	}
	// Generar bloques extras
	for ($i=0; $i<$numfilas; $i++) {
		$tpl->newBlock("new_mp");
		$tpl->assign("i",$i);
		$tpl->assign("back",($i > 0)?$i-1:$numfilas-1);
		$tpl->assign("next",($i < $numfilas-1)?$i+1:0);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("fecha",$fecha);
	}


	$tpl->printToScreen();
}
?>
