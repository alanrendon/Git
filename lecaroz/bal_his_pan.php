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
$descripcion_error[1] = "Lo siento pero ya exite esta reserva para esta compañía";

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_POST['num_cia'])) {
	$meses = 12;
	// Reconstruir datos
	for ($i=0; $i<$meses; $i++) {
		$datos['num_cia'.$i] = $_POST['num_cia'];
		$datos['mes'.$i] = $_POST['mes'.$i];
		$datos['anio'.$i] = $_POST['anio'];
		$datos['utilidad'.$i] = $_POST['utilidad'.$i];
		$datos['venta'.$i] = $_POST['venta'.$i];
		$datos['reparto'.$i] = $_POST['reparto'.$i];
		$datos['clientes'.$i] = $_POST['clientes'.$i];
		$datos['gasto_ext'.$i] = $_POST['ingresos'.$i] > 0?"TRUE":"FALSE";
		$datos['ingresos'.$i] = $_POST['ingresos'.$i];
		
		if ($id = ejecutar_script("SELECT id FROM historico WHERE num_cia=$_POST[num_cia] AND mes=".$_POST['mes'.$i]." AND anio=$_POST[anio]",$dsn)) {
			$sql = "UPDATE historico SET utilidad=".($_POST['utilidad'.$i] != 0?$_POST['utilidad'.$i]:0).",venta=".($_POST['venta'.$i] != 0?$_POST['venta'.$i]:0).",reparto=".($_POST['reparto'.$i] != 0?$_POST['reparto'.$i]:0).",clientes=".($_POST['clientes'.$i] != 0?$_POST['clientes'.$i]:0).",ingresos=".($_POST['ingresos'.$i] != 0?$_POST['ingresos'.$i]:0).",gasto_ext='".($_POST['ingresos'.$i] != 0?"TRUE":"FALSE")."' WHERE id=".$id[0]['id'];
			ejecutar_script($sql,$dsn);
		}
		else {
			$db = new DBclass($dsn,"historico",$datos);
			$db->generar_script_insert($i);
			$db->ejecutar_script();
		}
			
	}
	
	header("location: ./bal_his_pan.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_his_pan.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) {
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
//------------------------------------------------***Reservas***------------------------------------------------------------

$tpl->newBlock("historico");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("anio",$_GET['anio']);
$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre']);

$meses = 12;

$utilidad = 0;
$venta = 0;
$reparto = 0;
$clientes = 0;
$ingresos = 0;

for ($i=0; $i<$meses; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",($i > 0)?$i-1:$meses-1);
	$tpl->assign("next",($i < $meses-1)?$i+1:0);
	
	$tpl->assign("mes",$i+1);
	$tpl->assign("nombre_mes",mes_escrito($i+1,TRUE));
	
	if ($result = ejecutar_script("SELECT * FROM historico WHERE num_cia=$_GET[num_cia] AND mes=".($i+1)." AND anio=$_GET[anio]",$dsn)) {
		$tpl->assign("utilidad",number_format($result[0]['utilidad'],2,".",""));
		$tpl->assign("venta",number_format($result[0]['venta'],2,".",""));
		$tpl->assign("reparto",number_format($result[0]['reparto'],2,".",""));
		$tpl->assign("clientes",number_format($result[0]['clientes'],2,".",""));
		$tpl->assign("ingresos",number_format($result[0]['ingresos'],2,".",""));
		
		$utilidad += $result[0]['utilidad'];
		$venta += $result[0]['venta'];
		$reparto += $result[0]['reparto'];
		$clientes += $result[0]['clientes'];
		$ingresos += $result[0]['ingresos'];
	}
}

$tpl->assign("historico.utilidad",number_format($utilidad,2,".",""));
$tpl->assign("historico.venta",number_format($venta,2,".",""));
$tpl->assign("historico.reparto",number_format($reparto,2,".",""));
$tpl->assign("historico.clientes",number_format($clientes,2,".",""));
$tpl->assign("historico.ingresos",number_format($ingresos,2,".",""));

// Imprimir el resultado
$tpl->printToScreen();
?>