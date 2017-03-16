<?php
// IMPRESIÓN DE FAXES DE PEDIDOS V2
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

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

function cmp_cia($a, $b) {
	if ($a['num_cia'] == $b['num_cia'])
		return 0;
	else
		return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
}

function cmp_mp($a, $b) {
	if ($a['codmp'] == $b['codmp'])
		return 0;
	else
		return $a['codmp'] < $b['codmp'] ? -1 : 1;
}

function obtener_cias($result) {
	usort($result, "cmp_cia");
	
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++)
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			$cia[] = $num_cia;
		}
	
	return $cia;
}

function obtener_productos($result) {
	usort($result, "cmp_mp");
	
	$codmp = NULL;
	for ($i = 0; $i < count($result); $i++)
		if ($codmp != $result[$i]['codmp']) {
			$codmp = $result[$i]['codmp'];
			$mp[] = array('codmp' => $codmp, 'nombremp' => $result[$i]['nombremp'], 'unidad' => $result[$i]['unidad'], 'contenido' => $result[$i]['contenido']);
		}
	
	return $mp;
}

function buscar_interseccion($cia, $mp, $result) {
	for ($i = 0; $i < count($result); $i++)
		if ($result[$i]['num_cia'] == $cia && $result[$i]['codmp'] == $mp)
			return $i;
	
	return FALSE;
}

// VARIABLES GLOBALES
$mes = (int)date("m");
$anio = (int)date("Y");

$numfilas_x_hoja = 20;
$numcols_x_hoja  = 8;

if (isset($_GET['fax'])) {
	$mes = (int)date("m");
	$anio = (int)date("Y");
	
	// Obtener todos los proveedores con pedidos
	$sql = "SELECT p.num_proveedor, cp.nombre, cp.direccion, cp.rfc, cp.telefono1, cp.telefono2 FROM pedidos p LEFT JOIN catalogo_proveedores cp USING (num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia)";
	$sql .= $_GET['num_cia'] > 0 || $_GET['admin'] > 0 || $_GET['codmp'] > 0 || $_GET['num_proveedor'] > 0 ? " WHERE" : "";
	$sql .= $_GET['num_cia'] > 0 ? " p.num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['admin'] > 0 ? ($_GET['num_cia'] > 0 ? " AND" : "") . " idadministrador = $_GET[admin]" : '';
	$sql .= $_GET['codmp'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['admin'] > 0 ? " AND" : "") . " codmp = $_GET[codmp]" : "";
	$sql .= $_GET['num_proveedor'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['admin'] > 0 || $_GET['codmp'] > 0 ? " AND" : "") . " p.num_proveedor = $_GET[num_proveedor]" : "";
	$sql .= " GROUP BY p.num_proveedor, cp.nombre, cp.direccion, cp.rfc, cp.telefono1, cp.telefono2 ORDER BY p.num_proveedor";
	
	$pro = $db->query($sql);
	
	$tpl = new TemplatePower( "./plantillas/ped/fax_pedido_v2.tpl" );
	$tpl->prepare();
	
	if (!$pro) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}

	for ($i=0; $i<count($pro); $i++) {
		// Obtener compañias que relizaron pedido
		$sql = "SELECT num_cia, nombre_corto FROM pedidos LEFT JOIN catalogo_companias USING (num_cia) WHERE pedidos.num_proveedor = {$pro[$i]['num_proveedor']} GROUP BY num_cia, nombre_corto ORDER BY num_cia";
		$cia = $db->query($sql);
		
		$num_bloques = (int)ceil(count($cia) / $numcols_x_hoja);
		
		$tpl->newBlock("fax");
		$tpl->assign("dia", (int)date("d"));
		$tpl->assign("mes", mes_escrito($mes));
		$tpl->assign("anio", $anio);
		
		$tpl->assign("num_proveedor", $pro[$i]['num_proveedor']);
		$tpl->assign("nombre_proveedor", $pro[$i]['nombre']);
		$tpl->assign("direccion", $pro[$i]['direccion']);
		$tpl->assign("rfc", $pro[$i]['rfc']);
		$tpl->assign("telefono", $pro[$i]['telefono1'] . ($pro[$i]['telefono2'] != "" ? ", " . $pro[$i]['telefono2'] : ""));
		
		for ($j = 0; $j < $num_bloques; $j++) {
			$sql = "SELECT codmp,catalogo_mat_primas.nombre AS nombremp, num_cia, catalogo_companias.nombre_corto AS nombre_cia, tipo_presentacion.descripcion AS unidad, contenido, cantidad";
			$sql .= " FROM pedidos LEFT JOIN catalogo_mat_primas USING(codmp) LEFT JOIN catalogo_companias USING(num_cia) LEFT JOIN tipo_presentacion ON (idpresentacion = unidad)";
			$sql .= " WHERE pedidos.num_proveedor = {$pro[$i]['num_proveedor']} AND ";
			$sql .= isset($cia[$j * $numcols_x_hoja + $numcols_x_hoja - 1]['num_cia']) ? "num_cia BETWEEN " . $cia[$j * $numcols_x_hoja]['num_cia'] . " AND " . $cia[$j * $numcols_x_hoja + $numcols_x_hoja - 1]['num_cia'] : "num_cia >= " . $cia[$j * $numcols_x_hoja]['num_cia'];
			$sql .= " ORDER BY codmp, num_cia";
			$result = $db->query($sql);
			
			// Obtener compañías del bloque
			$num_cia = obtener_cias($result);
			// Obtener productos del bloque
			$mp = obtener_productos($result);
			
			// Crear bloque
			$tpl->newBlock("bloque");
			// Poner titulos de compañías
			for ($k = $j * $numcols_x_hoja; $k <= $j * $numcols_x_hoja + $numcols_x_hoja - 1; $k++)
				if (isset($cia[$k]['nombre_corto'])) {
					$tpl->newBlock("nombre_cia");
					$tpl->assign("cia", $cia[$k]['nombre_corto']);
				}
			
			$codmp = NULL;
			$numfilas = 0;
			$numcols = 0;
			
			for ($k = 0; $k < count($mp); $k++) {
				$tpl->newBlock("fila");
				$tpl->assign("codmp", $mp[$k]['codmp']);
				$tpl->assign("nombre", $mp[$k]['nombremp']);
				$tpl->assign("unidad", $mp[$k]['unidad']);
				//$tpl->assign("contenido", $mp[$k]['contenido']);
				
				for ($l = 0; $l < count($num_cia); $l++) {
					$tpl->newBlock("cia");
					if (($index = buscar_interseccion($num_cia[$l], $mp[$k]['codmp'], $result)) !== FALSE)
						$tpl->assign("entrega", $result[$index]['cantidad']);
					else
						$tpl->assign("entrega", "&nbsp;");
				}
			}
			
			
			/*for ($k = 0; $k < count($result); $k++) {
				if ($codmp != $result[$k]['codmp']) {
					$codmp = $result[$k]['codmp'];
					
					$tpl->newBlock("fila");
					$tpl->assign("codmp", $codmp);
					$tpl->assign("nombre", $result[$k]['nombremp']);
					$tpl->assign("unidad", $result[$k]['unidad']);
					$tpl->assign("contenido", $result[$k]['contenido']);
				}
				$tpl->newBlock("cia");
				$tpl->assign("entrega", "{$result[$k]['cantidad']}");
			}*/
		}
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_imp_fax_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $r) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $r['id']);
	$tpl->assign('admin', $r['admin']);
}

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