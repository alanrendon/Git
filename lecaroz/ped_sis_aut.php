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
$numfilas_x_hoja = 40;
$numcols_x_hoja  = 2;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_sis_aut.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['generar'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
	
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
}

// Vaciar tabla de pedidos
$db->query("TRUNCATE TABLE pedidos");

$mes_actual = (int)date("m");
$anio_actual = (int)date("Y");

$numdias = $_GET['dias'] > 30 ? (int)$_GET['dias'] : 40;

/*$diasxmes[1] = 31;
$diasxmes[2] = $anio % 4 == 0 ? 29 : 28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;*/

$anio_consumo = $mes_actual > 2 ? $anio_actual : $anio_actual - 1;

$mes_ini = (int)date("m", mktime(0, 0, 0, $mes_actual - 2, 1, $anio_actual));
$anio_ini = (int)date("Y", mktime(0, 0, 0, $mes_actual - 2, 1, $anio_actual));

$mes_fin = (int)date("m", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual));
$anio_fin = (int)date("Y", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual));

//$mes_anterior  = (int)date("m",mktime(0,0,0,$mes-1,1,$anio));
//$anio_anterior = (int)date("Y",mktime(0,0,0,$mes-1,1,$anio));

$fecha_historico = date("d/m/Y", mktime(0, 0, 0, $mes_actual, 0, $anio_actual));
//$fecha_historico = "$diasxmes[$mes_anterior]/$mes_anterior/$anio_anterior";

// Obtener listado de productos por proveedor
$sql = "SELECT num_proveedor,codmp,contenido,porcentaje FROM catalogo_productos_proveedor";
if ($_GET['codmp'] > 0)
	$sql .= " WHERE codmp = $_GET[codmp]";
$sql .= " ORDER BY codmp,num_proveedor,porcentaje";
$por = $db->query($sql);

// Obtener consumos del mes pasado
$sql = "SELECT num_cia,codmp,consumo FROM consumos_mensuales LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia] AND";
else
	$sql .= " num_cia < 100 AND";
if ($_GET['codmp'] > 0)
	$sql .= " codmp = $_GET[codmp] AND";
$sql .= " mes = $mes_fin AND anio = $anio_fin AND procpedautomat = 'TRUE' ORDER BY num_cia,codmp";
$con_pro = $db->query($sql);

// Obtener consumos del mes antepasado
$sql = "SELECT num_cia,codmp,consumo FROM consumos_mensuales LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia] AND";
else
	$sql .= " num_cia < 100 AND";
if ($_GET['codmp'] > 0)
	$sql .= " codmp = $_GET[codmp] AND";
$sql .= " mes = $mes_ini AND anio = $anio_ini AND procpedautomat = 'TRUE' ORDER BY num_cia,codmp";
$con_ant = $db->query($sql);

// Obtener inventario al inicio de mes
$sql = "SELECT num_cia,codmp,nombre,existencia,unidadconsumo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia] AND";
else
	$sql .= " num_cia < 100 AND";
if ($_GET['codmp'] > 0)
	$sql .= " codmp = $_GET[codmp] AND";
$sql .= " fecha = '$fecha_historico' AND procpedautomat = 'TRUE' ORDER BY num_cia,codmp";
$inv = $db->query($sql);

// Arreglo que contendra los datos de pedidos
$datos = array();

$num_cia = NULL;
$count = 0;
// Recorrer el inventario de las compañías
$sql = "";
for ($i=0; $i<count($inv); $i++) {
	if ($num_cia != $inv[$i]['num_cia'] || $numcols == $numcols_x_hoja || $numfilas == $numfilas_x_hoja) {
		if ($num_cia != $inv[$i]['num_cia']) {
			if (isset($numcols) && $numcols == 0)
				$tpl->newBlock("vacio");
			
			$num_cia = $inv[$i]['num_cia'];
			$tpl->newBlock("listado");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito($mes_actual));
			$tpl->assign("anio",$anio_actual);
			
			$numcols = 0;
		}
		if ($numcols == $numcols_x_hoja) {
			$tpl->newBlock("listado");
			$tpl->assign("num_cia",$num_cia);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito($mes_actual));
			$tpl->assign("anio",$anio_actual);
			
			$numcols = 0;
		}
		
		$tpl->newBlock("columna");
		
		$numfilas = 0;
	}
	
	$consumo_pro = buscar($con_pro,$num_cia,$inv[$i]['codmp'],"consumo");
	$consumo_ant = buscar($con_ant,$num_cia,$inv[$i]['codmp'],"consumo");
	$consumo = $consumo_pro >= $consumo_ant ? $consumo_pro : $consumo_ant;
	$pedido = $consumo / 30 * $numdias - (double)$inv[$i]['existencia'];
	
	if ($pedido > 0) {
		$total_pedido = 0;
		
		// Desglozar pedido por proveedor
		if ($index = buscar_porcentajes($por,$inv[$i]['codmp']))
			for ($j=0; $j<count($index); $j++) {
				$temp = ceil(($pedido * ($por[$index[$j]]['porcentaje'] / 100)) / $por[$index[$j]]['contenido']);
				
				$total_pedido += $temp;
				
				if ($temp > 0) {
					if ($id = $db->query("SELECT id FROM pedidos WHERE num_cia = $num_cia AND num_proveedor = {$por[$index[$j]]['num_proveedor']} AND codmp = {$inv[$i]['codmp']} AND mes = $mes_actual AND anio = $anio_actual"))
						$sql .= "UPDATE pedidos SET cantidad = $temp WHERE id = {$id[0]['id']};\n";
					else
						$sql .= "INSERT INTO pedidos (num_cia,num_proveedor,codmp,mes,anio,cantidad,unidad,contenido) VALUES ($num_cia,{$por[$index[$j]]['num_proveedor']},{$inv[$i]['codmp']},$mes_actual,$anio_actual,$temp,{$inv[$i]['unidadconsumo']},{$por[$index[$j]]['contenido']});\n";
				}
			}
		
		$tpl->newBlock("fila");
		$tpl->assign("codmp",$inv[$i]['codmp']);
		$tpl->assign("nombre",$inv[$i]['nombre']);
		$tpl->assign("inventario",$inv[$i]['existencia'] != 0 ? number_format($inv[$i]['existencia'],2,".",",") : "&nbsp;");
		$tpl->assign("consumo",$consumo != 0 ? number_format($consumo,2,".",",") : "&nbsp;");
		$tpl->assign("pedido",$total_pedido != 0 ? number_format($total_pedido,2,".",",") : "&nbsp;");
		
		$numfilas++;
	}
	
	if ($numfilas == $numfilas_x_hoja)
		$numcols++;
}
if (isset($numcols) && $numcols == 0)
	$tpl->newBlock("vacio");

//$db->query($sql);
$db->desconectar();

$tpl->printToScreen();
?>