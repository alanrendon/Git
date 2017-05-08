<?php
// IMPRESIÓN DE CHEQUES
// Tabla 'cheques'
// Menu ''

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay cheques por imprimir";
$descripcion_error[2] = "ADVERTENCIA: Hay folios de cheque duplicados, se detendra el proceso de impresión. Avisar al administrador.";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_imp2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha1",date("d/m/Y"));
	$tpl->assign("fecha2",date("d/m/Y"));
	
	// Si viene de una página que genero error
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

// -------------------------------- Mostrar listado de cheques que se imprimiran ------------------------
// Verificar que no haya cheques duplicados
$sql = "SELECT SUM(importe) FROM cheques WHERE id NOT IN (SELECT MIN(id) FROM cheques GROUP BY num_cia,folio) AND imp='FALSE'";
$result = ejecutar_script($sql,$dsn);
if ($result[0]['sum'] > 0) {
	header("location: ./ban_che_imp.php?codigo_error=2");
	die;
}


// Generar script sql
$sql  = "SELECT id,num_cia,clabe_cuenta AS cuenta,nombre AS nombre_cia,fecha,folio,a_nombre,concepto,facturas,importe ";
$sql .= "FROM cheques JOIN catalogo_companias USING (num_cia) WHERE imp = 'FALSE'";
// Por folio
if ($_GET['tipo'] == "folio")
	$sql .= " AND folio >= $_GET[folio1] AND folio <= $_GET[folio2] AND num_cia = $_GET[num_cia_folio]";
// Por compañía
else if ($_GET['tipo'] == "cia")
	$sql .= " AND num_cia = $_GET[num_cia]";
// Por proveedor
else if ($_GET['tipo'] == "proveedor")
	$sql .= " AND num_proveedor = $_GET[num_proveedor]";
// Por fecha
else if ($_GET['tipo'] == "fecha")
	$sql .= " AND fecha >= '$_GET[fecha1]' AND fecha <= '$_GET[fecha2]'";
// Organizar registros
$sql .= " ORDER BY num_cia,folio ASC";

// Obtener registros
$reg = ejecutar_script($sql,$dsn);
// Si no hay resultados, regresar a la pantalla inicial y mostrar un error
if (!$reg) {
	header("location: ./ban_che_imp.php?codigo_error=1");
	die;
}

// Mostrar listado con los cheques que se imprimiran
$tpl->newBlock("listado");
$tpl->assign("tipo",$_GET['tipo']);
if ($_GET['tipo'] == "todos") {
	$param1 = "";
	$param2 = "";
	$param3 = "";
}
else if ($_GET['tipo'] == "folio") {
	$param1 = $_GET['folio1'];
	$param2 = $_GET['folio2'];
	$param3 = $_GET['num_cia_folio'];
}
else if ($_GET['tipo'] == "cia") {
	$param1 = $_GET['num_cia'];
	$param2 = "";
	$param3 = "";
}
else if ($_GET['tipo'] == "proveedor") {
	$param1 = $_GET['num_proveedor'];
	$param2 = "";
	$param3 = "";
}
else if ($_GET['tipo'] == "fecha") {
	$param1 = $_GET['fecha1'];
	$param2 = $_GET['fecha2'];
	$param3 = "";
}
$tpl->assign("param1",$param1);
$tpl->assign("param2",$param2);
$tpl->assign("param3",$param3);
$tpl->assign("ultimo_cheque",$_GET['ultimo_folio']);
$tpl->assign("orden",$_GET['orden']);
$tpl->assign("numfilas",count($reg));

$cia = NULL;
for ($i=0; $i<count($reg); $i++) {
	if ($cia != $reg[$i]['num_cia']) {
		if ($cia != NULL)
			$tpl->assign("cia.total",number_format($total,2,".",","));
		
		$cia = $reg[$i]['num_cia'];
		$total = 0;
		
		$tpl->newBlock("cia");
		$tpl->assign("num_cia",$reg[$i]['num_cia']);
		$tpl->assign("cuenta",$reg[$i]['cuenta']);
		$tpl->assign("nombre_cia",$reg[$i]['nombre_cia']);
	}
	$tpl->newBlock("cheque");
	$tpl->assign("i",$i);
	$tpl->assign("id",$reg[$i]['id']);
	$tpl->assign("fecha",$reg[$i]['fecha']);
	$tpl->assign("folio",$reg[$i]['folio']);
	$tpl->assign("a_nombre",$reg[$i]['a_nombre']);
	$tpl->assign("concepto",$reg[$i]['concepto']." ".$reg[$i]['facturas']);
	$tpl->assign("importe",number_format($reg[$i]['importe'],2,".",","));
	$total += $reg[$i]['importe'];
}
if ($cia != NULL)
	$tpl->assign("cia.total",number_format($total,2,".",","));

$tpl->printToScreen();
?>