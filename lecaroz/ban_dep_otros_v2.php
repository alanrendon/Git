<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31, 32, 33, 34, 35);

$descripcion_error[1] = "No hay resultados";

// Obtener importe de las remisiones solicitadas
if (isset($_GET['num'])) {
	$sql = "SELECT num_fact, total, copia_fac, por_aut, folio, fecha FROM facturas_zap WHERE clave = $_GET[num] AND num_fact = '$_GET[rem]'/* AND fecha >= CURRENT_DATE - interval '10 months'*/ ORDER BY id DESC";
	$result = $db->query($sql);
	
	
	// No existe la factura
	if (!$result) die("-1|$_GET[i]|$_GET[r]");
	// Ya esta pagada la factura
	if ($result[0]['folio'] != '') die("-2|$_GET[i]|$_GET[r]");
	// No tiene copia de factura
	if ($result[0]['copia_fac'] == 'f') die("-3|$_GET[i]|$_GET[r]");
	// El porcentaje de descuento no esta autorizado
	if ($result[0]['por_aut'] == 'f') die("-4|$_GET[i]|$_GET[r]");
	
	// Buscar depositos que hayan pagado parcialmente la factura en anteriores capturas
	$sql = "SELECT num_fact1, pag1, num_fact2, pag2, num_fact3, pag3, num_fact4, pag4 FROM otros_depositos AS od LEFT JOIN catalogo_nombres AS cn ON";
	$sql .= " (cn.id = od.idnombre) WHERE num = $_GET[num] AND fecha >= '{$result[0]['fecha']}' AND ((num_fact1 = '$_GET[rem]' AND pag1 > 0) OR (num_fact2 = '$_GET[rem]' AND pag2 > 0) OR (num_fact3 = '$_GET[rem]' AND pag3 > 0) OR (num_fact4 = '$_GET[rem]' AND pag4 > 0))";
	$tmp = $db->query($sql);
	$pag = 0;
	if ($tmp)
		foreach ($tmp as $reg)
			for ($i = 1; $i <= 4; $i++)
				if ($reg['num_fact' . $i] == $_GET['rem'])
					$pag += $reg['pag' . $i];
	
	// Construir cadena de retorno
	$data = "$_GET[i]|$_GET[r]|{$result[0]['total']}|$pag";
	// Terminar programa y enviar resultado
	die($data);
}

// Insertar depositos y pagar facturas
if (isset($_POST['mes'])) {
	$sql = '';
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['dia'][$i] > 0 && get_val($_POST['importe'][$i]) > 0) {
			$data['num_cia'] = $_POST['num_cia'][$i];
			$data['fecha'] = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'], $_POST['dia'][$i], $_POST['anio']));
			$data['importe'] = get_val($_POST['importe'][$i]);
			$data['fecha_cap'] = date('d/m/Y');
			$data['acumulado'] = 'TRUE';
			$data['concepto'] = strtoupper(trim($_POST['concepto'][$i]));
			$data['idnombre'] = $_POST['id'][$i];
			$data['iduser'] = $_SESSION['iduser'];
			$data['acre']= $_POST['acre'][$i];
			$data['ficha'] = 'FALSE';
			// Remisiones que se pagaron. Si lo pagado es 0 la remisión se pago completa, si no se pondra lo que se pago de la factura.
			// Tambien poner marca de pagado a las remisiones
			$data['num_fact1'] = $_POST['num_fact1'][$i];
			$data['pag1'] = $_POST['num_fact1'][$i] != '' ? ($_POST['sal1'][$i] == 0 ? 0 : round(get_val($_POST['imp1'][$i]) - get_val($_POST['pag1'][$i]) - get_val($_POST['sal1'][$i]), 2)) : '';
			if ($_POST['num_fact1'][$i] != '' && $_POST['sal1'][$i] == 0) $sql .= "UPDATE facturas_zap SET folio = 0, cuenta = 0, tspago = now() WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact1'][$i]}' AND fecha = (SELECT fecha FROM facturas_zap WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact1'][$i]}' ORDER BY fecha DESC LIMIT 1);\n";
			$data['num_fact2'] = $_POST['num_fact2'][$i];
			$data['pag2'] = $_POST['num_fact2'][$i] != '' ? ($_POST['sal2'][$i] == 0 ? 0 : round(get_val($_POST['imp2'][$i]) - get_val($_POST['pag2'][$i]) - get_val($_POST['sal2'][$i]), 2)) : '';
			if ($_POST['num_fact2'][$i] != '' && $_POST['sal2'][$i] == 0) $sql .= "UPDATE facturas_zap SET folio = 0, cuenta = 0, tspago = now() WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact2'][$i]}' AND fecha = (SELECT fecha FROM facturas_zap WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact2'][$i]}' ORDER BY fecha DESC LIMIT 1);\n";
			$data['num_fact3'] = $_POST['num_fact3'][$i];
			$data['pag3'] = $_POST['num_fact3'][$i] != '' ? ($_POST['sal3'][$i] == 0 ? 0 : round(get_val($_POST['imp3'][$i]) - get_val($_POST['pag3'][$i]) - get_val($_POST['sal3'][$i]), 2)) : '';
			if ($_POST['num_fact3'][$i] != '' && $_POST['sal3'][$i] == 0) $sql .= "UPDATE facturas_zap SET folio = 0, cuenta = 0, tspago = now() WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact3'][$i]}' AND fecha = (SELECT fecha FROM facturas_zap WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact3'][$i]}' ORDER BY fecha DESC LIMIT 1);\n";
			$data['num_fact4'] = $_POST['num_fact4'][$i];
			$data['pag4'] = $_POST['num_fact4'][$i] != '' ? ($_POST['sal4'][$i] == 0 ? 0 : round(get_val($_POST['imp4'][$i]) - get_val($_POST['pag4'][$i]) - get_val($_POST['sal4'][$i]), 2)) : '';
			if ($_POST['num_fact4'][$i] != '' && $_POST['sal4'][$i] == 0) $sql .= "UPDATE facturas_zap SET folio = 0, cuenta = 0, tspago = now() WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact4'][$i]}' AND fecha = (SELECT fecha FROM facturas_zap WHERE clave = {$_POST['num'][$i]} AND num_fact = '{$_POST['num_fact4'][$i]}' ORDER BY fecha DESC LIMIT 1);\n";
			
			$sql .= $db->preparar_insert('otros_depositos', $data) . ";\n";
		}
		if ($sql != '') $db->query($sql);
		die(header('location: ./ban_dep_otros_v2.php'));
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_otros_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['mes'])) {
	// Imprimir listado de datos capturados
	if ($_GET['tipo'] == 'listado') {
		$sql = "SELECT num_cia, cc.nombre, fecha, concepto, cn.nombre AS nom, importe, fecha_cap, num_cia_primaria, acre, 0 as status, num_fact1, num_fact2";
		$sql .= " num_fact3, num_fact4 FROM otros_depositos LEFT JOIN catalogo_nombres AS cn ON (cn.id=idnombre) LEFT JOIN catalogo_companias AS cc";
		$sql .= " USING(num_cia) WHERE acumulado = 'TRUE'" . ($_SESSION['iduser'] != 1 ? " AND iduser = $_SESSION[iduser]" : '') . " AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899");
		$sql .= $_GET['idnombre'] > 0 ? " AND idnombre = $_GET[idnombre]" : "";
		$sql .= " ORDER BY num_cia_primaria, fecha, num_cia";
		$result = $db->query($sql);
		
		if (!$result) die(header('location: ./ban_dep_otros_v2.php'));
		
		$gran_total = 0;
		
		$fecha = NULL;
		$num_cia = NULL;
		$rows = 0;
		foreach ($result as $i => $reg) {
			if ($fecha != $reg['fecha'] || $num_cia != $reg['num_cia_primaria']) {
				if ($rows > 1) {
					$tpl->newBlock('total');
					$tpl->assign('total', number_format($total,2,".",","));
				}
				
				$fecha = $reg['fecha'];
				$num_cia = $reg['num_cia_primaria'];
				
				$rows = 0;
				$total = 0;
				
				$tpl->newBlock('grupo');
			}
			if ($reg['status'] == 0) {
				$tpl->newBlock('dep');
				$tpl->assign('num_cia', $reg['num_cia']);
				$tpl->assign('nombre_cia', $reg['nombre']);
				$tpl->assign('fecha', $reg['fecha']);
				if ($reg['acre'] > 0) $nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$reg['acre']}");
				$tpl->assign('acre', $result[0]['acre'] > 0 ? $nombre[0]['nombre_corto'] : '&nbsp;');
				$tpl->assign('nombre', $reg['nom']);
				$tpl->assign('concepto', $reg['concepto']);
				$tpl->assign('deposito', number_format($reg['importe'], 2, '.', ','));
				
				$total += $reg['importe'];
				$gran_total += $reg['importe'];
				$reg['status'] = 1;
				$rows++;
				
				// [2007/04/26] Si la compañía es Cantera (16), buscar en los depositos de la 44 uno con concepto CANTERA y moverlo de lugar
				if ($reg['num_cia'] == 16) {
					foreach ($result as $i => $tmp)
						if ($tmp['status'] == 0 && $tmp['num_cia'] == 44 && $reg['fecha'] == $tmp['fecha'] && strpos($tmp['concepto'], 'CANTERA') !== FALSE) {
							$tpl->newBlock('dep');
							$tpl->assign('num_cia', $tmp['num_cia']);
							$tpl->assign('nombre_cia', $tmp['nombre']);
							$tpl->assign('fecha', $tmp['fecha']);
							if ($tmp['acre'] > 0) $nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$tmp['acre']}");
							$tpl->assign('acre',$result[0]['acre'] > 0 ? $nombre[0]['nombre_corto'] : '&nbsp;');
							$tpl->assign('nombre', $tmp['nom']);
							$tpl->assign('concepto', $tmp['concepto']);
							$tpl->assign('deposito', number_format( $tmp['importe'],2,'.',','));
							
							$total += $tmp['importe'];
							$gran_total += $tmp['importe'];
							$tmp['status'] = 1;
							$rows++;
						}
				}
			}
		}
		if ($rows > 1) {
			$tpl->newBlock('total');
			$tpl->assign('total', number_format($total, 2, '.', ','));
		}
		
		$tpl->assign('listado.gran_total', number_format($gran_total, 2, ".", ","));
		$total_mes = $db->query("SELECT sum(importe) FROM otros_depositos WHERE fecha >= '1/$_GET[mes]/$_GET[anio]' AND fecha <= '" . date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio'])) . "' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899") . ($_GET['idnombre'] > 0 ? " AND idnombre = $_GET[idnombre]" : ''));
		$total_mes_ant = $db->query("SELECT sum(importe) FROM otros_depositos WHERE fecha >= '" . date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] - 1, 1, $_GET['anio'])) . "' AND fecha <= '".date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio']))."' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899") . ($_GET['idnombre'] > 0 ? " AND idnombre = $_GET[idnombre]" : ""));
		$tpl->assign("listado.mes_ant", number_format($total_mes_ant[0]['sum'], 2, '.', ','));
		$tpl->assign("listado.mes_act", number_format($total_mes[0]['sum'], 2, '.', ','));
		$tpl->printToScreen();
	}
	
	if ($_GET['tipo'] == 'nuevo')
		$db->query("UPDATE otros_depositos SET acumulado = 'FALSE' WHERE acumulado = 'TRUE' AND iduser = $_SESSION[iduser]");
	
	$tpl->newBlock('captura');
	$tpl->assign('mes', $_GET['mes']);
	$tpl->assign('mes_escrito', mes_escrito($_GET['mes'], TRUE));
	$tpl->assign('anio', $_GET['anio']);
	$tpl->assign('con', isset($_GET['con']) ? 1 : '');
	$tpl->assign('cancelar', isset($_GET['con']) ? './ban_con_dep_v2.php' : './ban_dep_otros_v2.php');
	
	for ($i = 0; $i < $_GET['filas']; $i++) {
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('index', $_GET['filas'] > 1 ? "[$i]" : '');
		$tpl->assign('back', $_GET['filas'] > 1 ? ($i > 0 ? '[' . ($i - 1) . ']' : '[' . ($_GET['filas'] - 1) . ']') : '');
		$tpl->assign('next', $_GET['filas'] > 1 ? ($i < $_GET['filas'] - 1 ? '[' . ($i + 1) . ']' : '[0]') : '');
	}
	
	$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias" . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899') : ''));
	foreach ($cias as $cia) {
		$tpl->newBlock('cia');
		$tpl->assign('num', $cia['num_cia']);
		$tpl->assign('nombre', $cia['nombre']);
	}
	
	$nombres = $db->query("SELECT id, num, nombre FROM catalogo_nombres WHERE status = 1");
	if ($nombres)
		foreach ($nombres as $reg) {
			$tpl->newBlock("nom");
			$tpl->assign("num", $reg['num']);
			$tpl->assign("id", $reg['id']);
			$tpl->assign("nombre", $reg['nombre']);
		}
	
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");
$tpl->assign(date('d') < 3 ? date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('n'), 'selected');
$tpl->assign('anio', date('d') < 3 ? date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('Y'));
$tpl->assign('filas', 10);

$result = $db->query('SELECT id, num, nombre FROM catalogo_nombres ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('n');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('nombre', "$reg[num] $reg[nombre]");
}

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
?>