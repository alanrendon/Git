<?php
// CAPTURA MANUAL (DEPOSITOS ALTERNATIVOS)
// Tabla 'depositos_alternativos'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_alt.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_POST['num_cia'])) {
	for ($i=0; $i<$_POST['num_dias']; $i++) {
		if ($_POST['dep1_'.$i] > 0 || $_POST['dep2_'.$i] > 0) {
			if ($id = ejecutar_script("SELECT * FROM depositos_alternativos WHERE num_cia=$_POST[num_cia] AND fecha='".$_POST['fecha'.$i]."'",$dsn))
				ejecutar_script("UPDATE depositos_alternativos SET dep1=".(($_POST['dep1_'.$i] > 0)?$_POST['dep1_'.$i]:"NULL").",dep2=".(($_POST['dep2_'.$i] > 0)?$_POST['dep2_'.$i]:"NULL")." WHERE id=".$id[0]['id'],$dsn);
			else
				ejecutar_script("INSERT INTO depositos_alternativos (num_cia,dep1,dep2,fecha) VALUES ($_POST[num_cia],".(($_POST['dep1_'.$i] > 0)?$_POST['dep1_'.$i]:"NULL").",".(($_POST['dep2_'.$i] > 0)?$_POST['dep2_'.$i]:"NULL").",'".$_POST['fecha'.$i]."')",$dsn);
		}
		else
			ejecutar_script("DELETE FROM depositos_alternativos WHERE num_cia=$_POST[num_cia] AND fecha='".$_POST['fecha'.$i]."'",$dsn);
	}
	
	if (isset($_POST['con'])) {
		header("location: ./ban_con_dep_v2.php");
		die;
	}
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	// Imprimir el resultado
	$tpl->printToScreen();
	die;
}

// Obtener el número de dias para el mes seleccionado
if ($_GET['mes'] == date("m") && $_GET['anio'] == date("Y"))
	$num_dias = date("d");
else
	$num_dias = date("d",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$tpl->newBlock("captura");
$tpl->assign("num_dias",$num_dias);
$tpl->assign("num_cia",$_GET['num_cia']);
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("anio",$_GET['anio']);
switch ($_GET['mes']) {
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
if (isset($_GET['con'])) {
	$tpl->assign("con","<input name=\"con\" type=\"hidden\" value=\"1\">");
	$tpl->newBlock("con");
}
else
	$tpl->newBlock("normal");
	

for ($i=0; $i<$num_dias; $i++) {
	$fecha = ($i+1)."/$_GET[mes]/$_GET[anio]";
	
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("dia",$i+1);
	$tpl->assign("fecha",($i+1)."/$_GET[mes]/$_GET[anio]");
	
	$dep = ejecutar_script("SELECT * FROM depositos_alternativos WHERE num_cia=$_GET[num_cia] AND fecha='$fecha'",$dsn);
	if ($dep) {
		$tpl->assign("dep1",($dep[0]['dep1'] > 0)?number_format($dep[0]['dep1'],2,".",""):"");
		$tpl->assign("dep2",($dep[0]['dep2'] > 0)?number_format($dep[0]['dep2'],2,".",""):"");
	}
	
	if ($i < $num_dias - 1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$num_dias-1);
}

$tpl->printToScreen();
?>