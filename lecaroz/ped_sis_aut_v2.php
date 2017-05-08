<?php
// SISTEMA DE PEDIDOS AUTOMÁTICO
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
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = 'No hay resultados';

// FUNCIONES
function buscar($array, $codmp, $nombre) {
	if (!$array)
		return 0;
	
	// Recorrer array
	foreach ($array as $reg)
		if ($reg['codmp'] == $codmp)
			return $reg[$nombre];
	
	// Se llego al final del array y no se encontro registro
	return 0;
}

function buscar_porcentajes($array, $codmp) {
	if (!$array)
		return FALSE;
	
	$pro = array();
	foreach ($array as $i => $reg)
		if ($reg['codmp'] == $codmp)
			$pro[] = $i;
	
	return count($pro) > 0 ? $pro : FALSE;
}

// VARIABLES GLOBALES
$numfilas_x_hoja = 40;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_sis_aut_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['dias'])) {
	if (isset($_GET['fecha']) && trim($_GET['fecha']) != '') {
		list($dia_actual, $mes_actual, $anio_actual) = explode('/', $_GET['fecha']);
	}
	else {
		$mes_actual = (int)date("n");
		$anio_actual = (int)date("Y");
		$dia_actual = (int)date('d');
	}
	$dias_mes = (int)date('d', mktime(0, 0, 0, $mes_actual + 1, 0, $anio_actual));
	$numdias = isset($_GET['complemento']) ? ($dias_mes - $dia_actual + 7) : ($_GET['dias'] > 30 ? (int)$_GET['dias'] : 40);
	$mes1 = (int)date("m", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual));
	$anio1 = (int)date("Y", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual));
	$mes2 = (int)date("m", mktime(0, 0, 0, $mes_actual - 2, 1, $anio_actual));
	$anio2 = (int)date("Y", mktime(0, 0, 0, $mes_actual - 2, 1, $anio_actual));
	$fecha_historico = date("d") > 5 ? date("d/m/Y", mktime(0, 0, 0, $mes_actual, 0, $anio_actual)) : date("d/m/Y", mktime(0, 0, 0, $mes_actual - 1, 0, $anio_actual));
	$fecha_fin_mes = date("d") < 5 ? date("d/m/Y", mktime(0, 0, 0, $mes_actual, 0, $anio_actual)) : date("d/m/Y", mktime(0, 0, 0, $mes_actual + 1, 0, $anio_actual));
	
	// Productos con ajuste
	$pro_ajuste = array(44, 45, 47);
	
	$vocales = array("A", "E", "I", "O", "U");
	
	// Obtener listado de productos por proveedor
	$sql = "SELECT num_proveedor, codmp, contenido, unidad, tipo_presentacion.descripcion AS unidad_pedido, porcentaje FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas";
	$sql .= " USING (codmp) LEFT JOIN tipo_presentacion ON (idpresentacion = unidad) WHERE porcentaje > 0 AND procpedautomat = 'TRUE'";
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
	$sql .= " ORDER BY codmp, num_proveedor, porcentaje";
	$ppp = $db->query($sql);
	
	// Obtener inventario al inicio de mes
	$sql = "SELECT num_cia, codmp, catalogo_mat_primas.nombre, inventario AS existencia, tipo_unidad_consumo.descripcion AS unidad_consumo, presentacion, tipo_presentacion.descripcion AS unidad_pedido,";
	$sql .= " controlada FROM inventario_fin_mes LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo)";
	$sql .= " LEFT JOIN tipo_presentacion ON (idpresentacion = presentacion) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia <= 300 AND";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND" : "";
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	$sql .= " fecha = '$fecha_fin_mes' AND procpedautomat = 'TRUE' ORDER BY num_cia, controlada DESC, codmp";
	$pros = $db->query($sql);
	
	if (!$pros) {
		$sql = "SELECT num_cia, codmp, catalogo_mat_primas.nombre, existencia, tipo_unidad_consumo.descripcion AS unidad_consumo, presentacion, tipo_presentacion.descripcion AS unidad_pedido,";
		$sql .= " controlada FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo)";
		$sql .= " LEFT JOIN tipo_presentacion ON (idpresentacion = presentacion) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia <= 300 AND";
		$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND" : "";
		$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
		$sql .= " fecha = '$fecha_historico' AND procpedautomat = 'TRUE' ORDER BY num_cia, controlada DESC, codmp";
		$pros = $db->query($sql);
	}
	
	// Proceso de Pedidos
	$data = array();
	$list = array();
	$cont_data = 0;
	$cont_list = 0;
	$num_cia = NULL;
	foreach ($pros as $pro) {
		if ($num_cia != $pro['num_cia']) {
			$num_cia = $pro['num_cia'];
			
			// Consumos de hace 1 mes
			$sql = "SELECT codmp, consumo FROM consumos_mensuales LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $num_cia AND mes = $mes1 AND anio = $anio1";
			$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
			$sql .= " AND procpedautomat = 'TRUE' ORDER BY num_cia, codmp";
			$con1 = $db->query($sql);
			// Consumos de hace 2 meses
			$sql = "SELECT codmp, consumo FROM consumos_mensuales LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $num_cia AND mes = $mes2 AND anio = $anio2";
			$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
			$sql .= " AND procpedautomat = 'TRUE' ORDER BY num_cia, codmp";
			$con2 = $db->query($sql);
		}
		// Calulo de pedido
		$consumo1 = buscar($con1, $pro['codmp'], "consumo");
		$consumo2 = buscar($con2, $pro['codmp'], "consumo");
		$consumo = $consumo1 >= $consumo2 ? $consumo1 : $consumo2;
		$existencia = $pro['controlada'] == "TRUE" ? $pro['existencia'] : ($pro['existencia'] - $consumo >= 0 ? $pro['existencia'] - $consumo : 0);
		$pedido = $consumo / 30 * $numdias - $existencia;
		//if (in_array($pro['codmp'], $pro_ajuste))
			//$pedido = $pedido + (10 - $pedido % 10);
		
		if (round($pedido, 2) > 0 && ($indexes = buscar_porcentajes($ppp, $pro['codmp'])) !== FALSE) {
			$total_pedido = 0;
			foreach ($indexes as $i) {
				$pedido_pro = ceil(($pedido * ($ppp[$i]['porcentaje'] / 100)) / $ppp[$i]['contenido']);
				if (in_array($pro['codmp'], $pro_ajuste) && $pedido_pro > 0)
					$pedido_pro = $pedido_pro + (10 - $pedido_pro % 10);
				$total_pedido += $pedido_pro * $ppp[$i]['contenido'];
				
				if ($pedido_pro > 0) {
					$data[$cont_data]['num_cia'] = $num_cia;
					$data[$cont_data]['num_proveedor'] = $ppp[$i]['num_proveedor'];
					$data[$cont_data]['codmp'] = $pro['codmp'];
					$data[$cont_data]['mes'] = $mes_actual;
					$data[$cont_data]['anio'] = $anio_actual;
					$data[$cont_data]['cantidad'] = $pedido_pro;
					$data[$cont_data]['unidad'] = /*$pro['presentacion']*/$ppp[$i]['unidad'];
					$data[$cont_data]['contenido'] = $ppp[$i]['contenido'];
					$data[$cont_data]['iduser'] = $_SESSION['iduser'];
					$data[$cont_data]['tsins'] = date('d/m/Y G:i:s');
					$cont_data++;
				}
			}
			
			// Datos para listado
			if ($total_pedido > 0) {
				$list[$cont_list]['num_cia'] = $num_cia;
				$list[$cont_list]['codmp'] = $pro['codmp'];
				$list[$cont_list]['nombre'] = $pro['nombre'];
				$list[$cont_list]['consumo'] = $consumo;
				$list[$cont_list]['unidad_consumo'] = $pro['unidad_consumo'];
				$list[$cont_list]['inventario'] = $existencia;
				$list[$cont_list]['pedido'] = $total_pedido;
				$list[$cont_list]['unidad_pedido'] = $pro['unidad_consumo'];
				$list[$cont_list]['dif'] = $existencia + $total_pedido - $consumo;
				$cont_list++;
			}
		}
	}
	
	if ($cont_list > 0) {
		$num_cia = NULL;
		foreach ($list as $reg) {
			if ($num_cia != $reg['num_cia']) {
				if ($num_cia != NULL)
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				
				$num_cia = $reg['num_cia'];
				
				$tpl->newBlock("listado");
				$tpl->assign("num_cia", $num_cia);
				$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
				$tpl->assign("nombre_cia", $nombre[0]['nombre']);
				$tpl->assign("dia", $dia_actual);
				$tpl->assign("mes", mes_escrito($mes_actual));
				$tpl->assign("anio", $anio_actual);
				$tpl->assign('dias', $numdias);
			}
			$tpl->newBlock("fila");
			$tpl->assign("codmp", $reg['codmp']);
			$tpl->assign("nombre", $reg['nombre']);
			$tpl->assign("unidad", $reg['unidad_pedido'] . ($reg['pedido'] > 1 ? (in_array($reg['unidad_pedido'][strlen($reg['unidad_pedido']) - 1], $vocales) ? "S" : "ES") : ""));
			$tpl->assign("consumo", $reg['consumo'] != 0 ? number_format($reg['consumo'], 2, ".", ",") : "&nbsp;");
//			$tpl->assign("unidad_consumo", $reg['consumo'] != 0 ? $reg['unidad_consumo'] . ($reg['consumo'] > 1 ? (in_array($reg['unidad_consumo'][strlen($reg['unidad_consumo']) - 1], $vocales) ? "S" : "ES") : "") : "&nbsp;");
			$tpl->assign("inventario", $reg['inventario'] != 0 ? number_format($reg['inventario'], 2, ".", ",") : "&nbsp;");
//			$tpl->assign("unidad_inventario", $reg['inventario'] != 0 ? $reg['unidad_consumo'] . ($reg['inventario'] > 1 ? (in_array($reg['unidad_consumo'][strlen($reg['unidad_consumo']) - 1], $vocales) ? "S" : "ES") : "") : "&nbsp;");
			$tpl->assign("pedido", number_format($reg['pedido'], 0, ".", ","));
//			$tpl->assign("unidad_pedido", $reg['unidad_pedido'] . ($reg['pedido'] > 1 ? (in_array($reg['unidad_pedido'][strlen($reg['unidad_pedido']) - 1], $vocales) ? "S" : "ES") : ""));
			$tpl->assign('dif', number_format($reg['dif'], 0, '.', ','));
		}
		
		$tpl->printToScreen();
		
		$sql = "TRUNCATE pedidos;\n";
		$sql .= $db->multiple_insert("pedidos", $data);
		//echo "<pre>$sql</pre>";
		//$db->query($sql);
	}
	else {
		header('location: ped_sis_aut_v2.php?codigo_error=1');
	}
	die;
}

$tpl->newBlock("datos");
//$tpl->assign(date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))), "selected");

$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre_administrador");
foreach ($admins as $admin) {
	$tpl->newBlock("admin");
	$tpl->assign("id", $admin['id']);
	$tpl->assign("nombre", $admin['nombre']);
}

if (in_array($_SESSION['iduser'], array(1, 4))) {
	$tpl->newBlock('fecha');
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
?>