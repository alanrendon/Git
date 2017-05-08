<?php
// CAPTURA DE NOMINAS RECIBIDAS
// Tabla 'nominas'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ---------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por imprimir";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$numfilas = 30;

// --------------------------------- Generar pantalla --------------------------------------------------------
if (isset($_POST['anio'])) {
	$anio = $_POST['anio'];
	$fecha_cap = date("d/m/Y");
	$numsemanas = 54;
	
	for ($i=0; $i<$numfilas; $i++) {
		$num_cia = $_POST['num_cia'.$i];
		if ($num_cia > 0)
			for ($j=1; $j<=10; $j++) {
				$semana = $_POST['semana'.$j.'_'.$i];
				if ($semana > 0 && $semana < $numsemanas)
					if (!ejecutar_script("SELECT * FROM nominas WHERE num_cia = $num_cia AND semana = $semana AND anio = $anio",$dsn))
						ejecutar_script("INSERT INTO nominas (num_cia,semana,anio,fecha_cap) VALUES ($num_cia,$semana,$anio,'$fecha_cap')",$dsn);
			}
	}
	
	header("location: ./fac_nom_cap.php");
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_nom_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("anio",date("Y"));

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (303,318,329,335,339,350,369,700,702,704,800) ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",($i > 0)?$i-1:$numfilas-1);
	$tpl->assign("next",($i < $numfilas-1)?$i+1:0);
}

$tpl->printToScreen();
?>