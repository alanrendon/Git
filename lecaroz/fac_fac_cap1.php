<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> 

//define ('IDSCREEN',); //ID de pantalla sin ID


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
$descripcion_error[1] = "Número de proveedor no existe en la Base de Datos.";
$descripcion_error[2] = "Número de producto no existe en la Base de Datos.";
$descripcion_error[3] = "El número de factura ya existe en la Base de Datos.";
$descripcion_error[4] = "Número de compañia no existe en la Base de Datos.";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_fac_cap1.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","entrada_mp");

if (!existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn)) {
	header("location: ./fac_fac_cap.php?codigo_error=4");
	die;
}
if (!existe_registro("catalogo_productos_proveedor",array("num_proveedor"),array($_POST['num_proveedor']), $dsn)) {
	header("location: ./fac_fac_cap.php?codigo_error=1");
	die;
}
if (existe_registro("entrada_mp",array("num_proveedor","num_documento"),array($_POST["num_proveedor"],$_POST['num_documento']), $dsn)) {
	header("location: ./fac_fac_cap.php?codigo_error=3");
	die;
}

$cia  = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","", $dsn);
$sql = "SELECT codmp,nombre,contenido,descripcion AS unidad,precio,desc1,desc2,desc3,iva,ieps FROM catalogo_productos_proveedor JOIN catalogo_mat_primas USING(codmp) JOIN tipo_unidad_consumo ON(idunidad = unidadconsumo) WHERE num_proveedor=$_POST[num_proveedor] ORDER BY codmp ASC";
$rows = ejecutar_script($sql,$dsn);

// Asignar valores a los campos del formulario		
$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nombre_corto",strtoupper($cia[0]['nombre_corto']));
$tpl->assign("num_proveedor",$prov[0]['num_proveedor']);
$tpl->assign("nombre",strtoupper($prov[0]['nombre']));
$tpl->assign("num_documento",$_POST['num_documento']);				
$tpl->assign("fecha",$_POST['fecha']);
$tpl->assign("totalf",$_POST['totalf']);
$tpl->assign("ftotalf",number_format($_POST['totalf'],2,".",","));

$numrows = count($rows);
	
// Crear los renglones
for ($i=0; $i<$numrows; $i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$numrows-1);
	if ($i < $numrows-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	
	$tpl->assign("codmp",$rows[$i]['codmp']);
	$tpl->assign("nombre",$rows[$i]['nombre']);
	$tpl->assign("contenido",$rows[$i]['contenido']);
	$tpl->assign("unidad",$rows[$i]['unidad']);
	$tpl->assign("precio",$rows[$i]['precio']);
	$tpl->assign("fprecio",number_format($rows[$i]['precio'],2,".",","));
	$tpl->assign("desc1",$rows[$i]['desc1']);
	if ($rows[$i]['desc1'] > 0)
		$tpl->assign("fdesc1",number_format($rows[$i]['desc1'],2,".",",")."%");
	else
		$tpl->assign("fdesc1","&nbsp;");
	$tpl->assign("desc2",$rows[$i]['desc2']);
	if ($rows[$i]['desc2'] > 0)
		$tpl->assign("fdesc2",number_format($rows[$i]['desc2'],2,".",",")."%");
	else
		$tpl->assign("fdesc2","&nbsp;");
	$tpl->assign("desc3",$rows[$i]['desc3']);
	if ($rows[$i]['desc3'] > 0)
		$tpl->assign("fdesc3",number_format($rows[$i]['desc3'],2,".",",")."%");
	else
		$tpl->assign("fdesc3","&nbsp;");
	$tpl->assign("iva",$rows[$i]['iva']);
	if ($rows[$i]['iva'] > 0)
		$tpl->assign("fiva",number_format($rows[$i]['iva'],2,".",",")."%");
	else
		$tpl->assign("fiva","&nbsp;");
	if ($rows[$i]['ieps'] > 0)
		$tpl->assign("ieps",number_format($rows[$i]['ieps'],2,".","")."%");
}

$tpl->printToScreen();
?>