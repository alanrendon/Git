<?php
// MODIFICACION RAPIDA DE DEPOSITO
// Tabla 'estado_cuenta'
// Menu 'Panaderías->Producción'

//define ('IDSCREEN',1241); // ID de pantalla

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
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "Contraseña incorrecta";
$descripcion_error[3] = "Ha cambiado de usuario";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_esc_minimod.tpl");
$tpl->prepare();

if (!(isset($_SESSION['esc_mod']) && $_SESSION['esc_mod'] == $_SESSION['iduser'])) {
	$tpl->newBlock("cerrar_error");
	$tpl->printToScreen();
}

// Modificar datos
if (isset($_POST['id'])) {
	// Obtener datos anteriores del movimiento
	$sql = "SELECT * FROM estado_cuenta WHERE id = $_POST[id]";
	$mov = ejecutar_script($sql,$dsn);
	
	/*$db = new DBclass($dsn,"estado_cuenta",$_POST);
	$db->generar_script_update("",array("id"),array($_POST['id']));
	$db->ejecutar_script();*/
	$sql = "UPDATE estado_cuenta SET fecha = '$_POST[fecha]',cod_mov = $_POST[cod_mov],importe = $_POST[importe],concepto = '".strtoupper($_POST['concepto'])."',iduser=$_SESSION[iduser],timestamp=now(), local = " . (isset($_REQUEST['local']) && $_REQUEST['local'] > 0 ? $_REQUEST['local'] : 'NULL') . ", fecha_renta = " . ($_REQUEST['anio'] > 0 && $_REQUEST['mes'] > 0 ? "'" . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . "'" : 'NULL') . " WHERE id = $_POST[id]";
	ejecutar_script($sql,$dsn);
	
	if ($mov[0]['importe'] != $_POST['importe'] && isset($_POST['saldo_libros'])) {
		// Actualizar saldo en libros
		$sql = "UPDATE saldos SET saldo_libros = saldo_libros - ".$mov[0]['importe']." + $_POST[importe] WHERE num_cia = {$mov[0]['num_cia']} AND cuenta = {$mov[0]['cuenta']}";
		ejecutar_script($sql,$dsn);
	}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Mostrar pantalla de modificación
$sql = "SELECT * FROM estado_cuenta WHERE id = $_GET[id]";
$mov = ejecutar_script($sql,$dsn);

$cat_movs = $mov[0]['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";

$tpl->newBlock("modificar");
$tpl->assign("nombre_mov", $mov[0]['tipo_mov'] == "f" ? "Abono" : "Cargo");
$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$mov[0]['num_cia']);
$nombre_cia = ejecutar_script("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia = ".$mov[0]['num_cia'],$dsn);
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
$tpl->assign("cuenta",$nombre_cia[0]['clabe_cuenta']);
$tpl->assign("fecha",$mov[0]['fecha']);
$tpl->assign("importe",number_format($mov[0]['importe'],2,".",""));
$tpl->assign("concepto",$mov[0]['concepto']);

// Si el movimiento esta conciliado
if ($mov[0]['fecha_con'] == "")
	$tpl->assign("disabled","disabled");
else
	$tpl->assign("readonly","readonly");

$cod_mov = ejecutar_script("SELECT cod_mov,descripcion FROM $cat_movs WHERE tipo_mov = '" . ($mov[0]['tipo_mov'] == "t" ? "TRUE" : "FALSE") . "' GROUP BY cod_mov,descripcion ORDER BY cod_mov",$dsn);
for ($i=0; $i<count($cod_mov); $i++) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("cod_mov",$cod_mov[$i]['cod_mov']);
	$tpl->assign("descripcion",$cod_mov[$i]['descripcion']);
	if ($cod_mov[$i]['cod_mov'] == $mov[0]['cod_mov']) $tpl->assign("selected","selected");
}

$sql = '
	SELECT
		id
			AS value,
		num_local || \' \' || nombre_local
			AS text
	FROM
		catalogo_arrendatarios
	WHERE
		cod_arrendador = ' . $mov[0]['num_cia'] . '
		AND status = 1
	ORDER BY
		nombre_local DESC
';

$locales = ejecutar_script($sql, $dsn);

if ($locales) {
	foreach ($locales as $l) {
		$tpl->newBlock('local');
		$tpl->assign('value', $l['value']);
		$tpl->assign('text', $l['text']);
		
		if ($l['value'] == $mov[0]['local']) {
			$tpl->assign('selected', ' selected');
		}
	}
}

if ($mov[0]['fecha_renta'] != '') {
	list($dia, $mes, $anio) = explode('/', $mov[0]['fecha_renta']);
}
else {
	list($dia, $mes, $anio) = array(NULL, NULL, NULL);
}

$tpl->assign('modificar.anio', $anio);

if (intval($mes, 10) > 0) {
	$tpl->assign('modificar.' . intval($mes, 10), ' selected');
}

$tpl->printToScreen();
?>