<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

function buscar_dep($array, $fecha) {
	if (!$array)
		return FALSE;
	
	for ($i=0; $i<count($array); $i++)
		if ($array[$i]['fecha'] == $fecha)
			return $i;
	
	return FALSE;
}

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas";
$descripcion_error[2] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Procesar datos
//$num_cia = $_GET['num_cia'];
$mes = $_GET['mes'];
$anio = $_GET['anio'];
$origen = $_GET['origen'];

// Seleccionar plantilla
$plantilla = "factura_carta.tpl";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/$plantilla" );
$tpl->prepare();

// Obtener importes para facturas
$sql = "SELECT num_cia, nombre, direccion, rfc, importe, aplica_iva FROM importes_$origen LEFT JOIN catalogo_companias USING (num_cia) WHERE ";
$sql .= $_GET['num_cia1'] > 0 && $_GET['num_cia2'] > 0 ? "num_cia BETWEEN $_GET[num_cia1] AND $_GET[num_cia2] AND" : ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] == '' ? "num_cia = $_GET[num_cia1] AND" : '');
$sql .= " mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// [02-Ene-2009] A partir del 1º de enero de 2010 el IVA es del 16% en lugar del 15%
for ($i=0; $i<count($result); $i++) {
	$subtotal =  $result[$i]['importe'] / /*1.15*/1.16;
	$iva = $subtotal * /*0.15*/0.16;
	
	$tpl->newBlock("factura");
	$tpl->assign("dia1", date("d",mktime(0,0,0,$_GET['mes'] + 1,0,$_GET['anio'])));
	$tpl->assign("dia2", date("d",mktime(0,0,0,$_GET['mes'] + 1,0,$_GET['anio'])));
	$tpl->assign("mes", mes_escrito($_GET['mes']));
	$tpl->assign("anio", $_GET['anio']);
	$tpl->assign("nombre", $result[$i]['nombre']);
	$tpl->assign("direccion", $result[$i]['direccion']);
	$tpl->assign("rfc", $result[$i]['rfc']);
	$tpl->assign("descripcion1", "COBRO DE ADMINISTRACION DEL MES DE " . mes_escrito($_GET['mes']/* . ' (EXTRA)'*/, TRUE));
	$tpl->assign("importe1", number_format($subtotal,2,".",","));
	$tpl->assign("subtotal", number_format($subtotal,2,".",","));
	$tpl->assign("iva", $iva != 0 ? number_format($iva,2,".",",") : "&nbsp;");
	$tpl->assign("total", number_format($result[$i]['importe'],2,".",","));
}

$tpl->printToScreen();
?>