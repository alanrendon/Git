<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$sql .= ' AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 800');
	$result = $db->query($sql);
	
	if (!$result) die("$_GET[i]|");
	else die("$_GET[i]|{$result[0]['nombre']}");
}

if (isset($_POST['cuenta'])) {
	// Hacer comprobaciones e inserciones de datos
	$sql = '';
	$reg = array();
	for ($i = 0; $i < 10; $i++)
		if ($_POST['num_cia_dep'][$i] > 0 && $_POST['num_cia_des'][$i] > 0 && $_POST['fecha'][$i] != '' && get_val($_POST['importe'][$i]) > 0) {
			$num_cia_dep = $_POST['num_cia_dep'][$i];
			$num_cia_des = $_POST['num_cia_des'][$i];
			$fecha = $_POST['fecha'][$i];
			$importe = get_val($_POST['importe'][$i]);
			$cod_mov = $num_cia_des <= 300 || $num_cia_des >= 900 ? 1 : 16;
			
			if ($id = $db->query("SELECT id FROM estado_cuenta WHERE num_cia = $num_cia_dep AND fecha = '$fecha' AND tipo_mov = 'FALSE' AND importe = $importe AND cuenta = $_POST[cuenta]")) {
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) SELECT num_cia, fecha, 'TRUE', importe, 21, 'ERROR CIA $num_cia_des', $_POST[cuenta], $_SESSION[iduser], now() FROM estado_cuenta WHERE id = {$id[0]['id']};\n";
				if (!$db->query("SELECT id FROM estado_cuenta WHERE num_cia = $num_cia_des AND fecha = '$fecha' AND tipo_mov = 'FALSE' AND importe = $importe AND cuenta = $_POST[cuenta]"))
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) SELECT $num_cia_des, fecha, tipo_mov, importe, $cod_mov, concepto, $_POST[cuenta], $_SESSION[iduser], now() FROM estado_cuenta WHERE id = {$id[0]['id']};\n";
				$sql .= "UPDATE estado_cuenta SET cod_mov = 29, concepto = 'ERROR CIA $num_cia_des' WHERE id = {$id[0]['id']};\n";
			}
			else {
				$tmp = $db->query("SELECT num_cia, nombre_corto, clabe_cuenta, clabe_cuenta2, '$fecha' AS fecha, $importe AS importe FROM catalogo_companias WHERE num_cia = $num_cia_dep");
				$reg[] = $tmp[0];
			}
		}
		
		if (count($reg) > 0) {
			// Hacer un nuevo objeto TemplatePower
			$tpl = new TemplatePower('./plantillas/header.tpl');
			
			// Incluir el cuerpo del documento
			$tpl->assignInclude('body', './plantillas/ban/ban_car_bon_for.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('error');
			foreach ($reg as $r) {
				$tpl->newBlock('fila_error');
				$tpl->assign('banco', $_POST['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
				$tpl->assign('num_cia', $r['num_cia']);
				$tpl->assign('nombre', $r['nombre_corto']);
				$tpl->assign('cuenta', $_POST['cuenta'] == 1 ? $r['clabe_cuenta'] : $r['clabe_cuenta2']);
				$tpl->assign('fecha', $r['fecha']);
				$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
			}
			
			die($tpl->printToScreen());
		}
	
	if ($_SESSION['iduser'] != 1) $db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower('./plantillas/ban/carta_bonificacion.tpl');
	$tpl->prepare();
	
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anio', date('Y'));
	
	$tpl->assign('banco', $_POST['cuenta'] == 1 ? 'BANCO MERCANTIL DEL NORTE S.A.' : 'SANTANDER');
	$tpl->assign('contacto', strtoupper($_POST['contacto']));
	
	for ($i = 0; $i < 10; $i++) {
		if ($_POST['num_cia_dep'][$i] > 0 && $_POST['num_cia_des'][$i] > 0 && $_POST['fecha'][$i] != '' && get_val($_POST['importe'][$i]) > 0) {
			$tpl->newBlock('fila');
			
			$sql = "SELECT nombre, clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia = {$_POST['num_cia_dep'][$i]}";
			$cia_dep = $db->query($sql);
			
			$sql = "SELECT nombre, clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia = {$_POST['num_cia_des'][$i]}";
			$cia_des = $db->query($sql);
			
			$tpl->assign('fecha', $_POST['fecha'][$i]);
			$tpl->assign('importe', $_POST['importe'][$i]);
			$tpl->assign('num_cia_dep', $_POST['num_cia_dep'][$i]);
			$tpl->assign('nombre_dep', $cia_dep[0]['nombre']);
			$tpl->assign('cuenta_dep', $_POST['cuenta'] == 1 ? $cia_dep[0]['clabe_cuenta'] : $cia_dep[0]['clabe_cuenta2']);
			$tpl->assign('num_cia_des', $_POST['num_cia_des'][$i]);
			$tpl->assign('nombre_des', $cia_des[0]['nombre']);
			$tpl->assign('cuenta_des', $_POST['cuenta'] == 1 ? $cia_des[0]['clabe_cuenta'] : $cia_des[0]['clabe_cuenta2']);
		}
	}
	
	die($tpl->printToScreen());
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower('./plantillas/header.tpl');

// Incluir el cuerpo del documento
$tpl->assignInclude('body', './plantillas/ban/ban_car_bon_for.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

$tpl->newBlock('datos');

for ($i = 0; $i < 10; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < 10 - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : 10 - 1);
}

$tpl->printToScreen();
?>