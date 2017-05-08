<?php
// IMPRESIÓN DE FAXES DE PEDIDOS
// Tablas ''
// Menu 'No definido'

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

// FUNCIONES
function buscar($array, $num_cia, $codmp, $nombre) {
	$num_elementos = count($array);	// Contar número de elementos en el arreglo
	if ($num_elementos < 1)
		return 0;
	
	// Recorrer array
	for ($i=0; $i<$num_elementos; $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['codmp'] == $codmp)
			return $array[$i][$nombre];
	
	// Se llego al final del array y no se encontro registro
	return 0;
}

function buscar_porcentajes($array, $codmp) {
	$num_elementos = count($array);
	if ($num_elementos < 1)
		return FALSE;
	
	$pro = array();
	$count = 0;
	for ($i=0; $i<$num_elementos; $i++)
		if ($array[$i]['codmp'] == $codmp)
			$pro[$count++] = $i;
	
	return $pro;
}

// VARIABLES GLOBALES
$numfilas_x_hoja = 20;
$numcols_x_hoja  = 5;

if (isset($_GET['fax'])) {
	$mes = (int)date("m");
	$anio = (int)date("Y");
	
	// Obtener pedidos
	$sql = "SELECT pedidos.num_proveedor AS num_proveedor,codmp,catalogo_mat_primas.nombre AS nombremp,num_cia,catalogo_companias.nombre_corto AS nombre_cia,tipo_presentacion.descripcion AS unidad,contenido,cantidad";
	$sql .= " FROM pedidos LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN tipo_presentacion ON (idpresentacion = unidad) WHERE";
	if ($_GET['num_cia'] > 0)
		$sql .= " num_cia = $_GET[num_cia] AND";
	if ($_GET['codmp'] > 0)
		$sql .= " codmp = $_GET[codmp] AND";
	if ($_GET['num_proveedor'] > 0)
		$sql .= " pedidos.num_proveedor = $_GET[num_proveedor] AND";
	$sql .= " mes = $mes AND anio = $anio ORDER BY num_proveedor,codmp,num_cia";
	$pedido = ejecutar_script($sql,$dsn);
	
	$tpl = new TemplatePower( "./plantillas/ped/fax_pedido.tpl" );
	$tpl->prepare();
	
	if (!$pedido) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}

	$num_proveedor = NULL;
	for ($i=0; $i<count($pedido); $i++) {
		if ($num_proveedor != $pedido[$i]['num_proveedor'] || $numfilas == $numfilas_x_hoja) {
			$num_proveedor = $pedido[$i]['num_proveedor'];
			
			$tpl->newBlock("fax");
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito($mes));
			$tpl->assign("anio",$anio);
			
			$tpl->assign("num_proveedor",$num_proveedor);
			// Obtener datos del proveedor
			$proveedor = ejecutar_script("SELECT nombre,direccion,rfc,telefono1,telefono2 FROM catalogo_proveedores WHERE num_proveedor = $num_proveedor",$dsn);
			$tpl->assign("nombre_proveedor",$proveedor[0]['nombre']);
			$tpl->assign("direccion",$proveedor[0]['direccion']);
			$tpl->assign("rfc",$proveedor[0]['rfc']);
			$tpl->assign("telefono",$proveedor[0]['telefono1'].($proveedor[0]['telefono2'] != "" ? ", ".$proveedor[0]['telefono2'] : ""));
			
			$codmp = NULL;
			$numfilas = 0;
			$numcols = 0;
		}
		if ($codmp != $pedido[$i]['codmp'] || $numcols == $numcols_x_hoja) {
			$codmp = $pedido[$i]['codmp'];
			
			$tpl->newBlock("fila");
			$tpl->assign("codmp",$codmp);
			$tpl->assign("nombre",$pedido[$i]['nombremp']);
			$tpl->assign("unidad",$pedido[$i]['unidad']);
			$tpl->assign("contenido",$pedido[$i]['contenido']);
			
			$numcols = 0;
		}
		$tpl->newBlock("cia");
		$tpl->assign("entrega","{$pedido[$i]['nombre_cia']}<br>{$pedido[$i]['cantidad']}");
		
		$numcols++;
		
		if ($numcols == $numcols_x_hoja)
			$numfilas++;
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_imp_fax.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
die;
?>