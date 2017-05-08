<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['anio'])) {
	$tpl = new TemplatePower( "./plantillas/ban/carta_solicitud_estado_cuenta.tpl" );
	$tpl->prepare();
	
	$cuenta_field = $_GET['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2';
	
	$sql = "SELECT * FROM (SELECT num_cia, nombre, $cuenta_field AS cuenta, (SELECT count(*) FROM estados_cuenta_recibidos WHERE num_cia = cc.num_cia AND cuenta = $_GET[cuenta] AND anio = $_GET[anio] AND mes <= $_GET[mes]) AS recibidos FROM catalogo_companias AS cc WHERE (num_cia BETWEEN 1 AND 300 OR num_cia BETWEEN 600 AND 628 OR num_cia BETWEEN 900 AND 998) AND $cuenta_field IS NOT NULL ORDER BY num_cia) AS esc_rec WHERE recibidos < $_GET[mes] ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock('close');
		$tpl->printToScreen();
		die;
	}
	
	$tpl->newBlock('carta');
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n'), TRUE));
	$tpl->assign('anio', date('Y'));
	$tpl->assign('banco', $_GET['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
	
	foreach ($result as $reg)
		if (strlen(trim($reg['cuenta'])) > 0) {
			$sql = "SELECT mes FROM estados_cuenta_recibidos WHERE num_cia = $reg[num_cia] AND cuenta = $_GET[cuenta] AND anio = $_GET[anio] AND mes <= $_GET[mes] ORDER BY mes";
			$tmp = $db->query($sql);
			
			$rec = array(1 => FALSE, 2 => FALSE, 3 => FALSE, 4 => FALSE, 5 => FALSE, 6 => FALSE, 7 => FALSE, 8 => FALSE, 9 => FALSE, 10 => FALSE, 11 => FALSE, 12 => FALSE);
			if ($tmp)
				foreach ($tmp as $t)
					$rec[$t['mes']] = TRUE;
			
			foreach ($rec as $i => $r)
				if (!$r && $i <= $_GET['mes'] && $db->query("SELECT id FROM estado_cuenta WHERE num_cia = $reg[num_cia] AND cuenta = $_GET[cuenta] AND fecha BETWEEN '" . date('d/m/Y', mktime(0, 0, 0, $i, 1, $_GET['anio'])) . "' AND '" . date('d/m/Y', mktime(0, 0, 0, $i + 1, 0, $_GET['anio'])) . "' LIMIT 1")) {
					$tpl->newBlock('fila');
					$tpl->assign('num_cia', $reg['num_cia']);
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('cuenta', $reg['cuenta']);
					$tpl->assign('mes', mes_escrito($i));
					$tpl->assign('anio', $_GET['anio']);
				}
		}
	die($tpl->printToScreen());
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_esc_car.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date('n'), 'selected');
$tpl->assign('anio', date('Y'));

$tpl->printToScreen();
?>