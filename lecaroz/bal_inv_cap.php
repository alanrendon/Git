<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_inv_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Validar fecha de captura
if (isset($_GET['mes'])) {
	$result = $db->query("SELECT id FROM balances_zap WHERE mes = $_GET[mes] AND anio = $_GET[anio] LIMIT 1");
	
	$mod = $_SESSION['iduser'] == 28 ? 1 : (!$result ? 1 : 0);
	
	// Obtener datos capturados
	$sql = "SELECT num_cia, importe FROM inventario_zap WHERE mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY num_cia";
	$result = $db->query($sql);
	
	$data = "$mod\n";
	if ($result)
		foreach ($result as $reg)
			$data .= "$reg[num_cia]|$reg[importe]||";
	
	echo $data;
	die;
}

// Insertar datos
if (isset($_POST['mes'])) {
	$sql = "DELETE FROM inventario_zap WHERE mes = $_POST[mes] AND anio = $_POST[anio];\n";
	
	$cont = 0;
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if (get_val($_POST['importe'][$i]) > 0) {
			$data[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$data[$cont]['importe'] = get_val($_POST['importe'][$i]);
			$data[$cont]['mes'] = $_POST['mes'];
			$data[$cont]['anio'] = $_POST['anio'];
			$cont++;
		}
	
	$sql .= $db->multiple_insert("inventario_zap", $data);
	$db->query($sql);
	
	header("location: ./bal_inv_cap.php");
	die;
}

$mes = date("n");
$anio = date("Y");

$tpl->newBlock("captura");
$tpl->assign($mes, " selected");
$tpl->assign("anio", $anio);

$sql = "SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia";
$result = $db->query($sql);

$sql = "SELECT num_cia, importe FROM inventario_zap WHERE mes = $mes AND anio = $anio ORDER BY num_cia";
$tmp = $db->query($sql);

if ($tmp)
	foreach ($tmp as $reg)
		$data[$reg['num_cia']] = $reg['importe'];

foreach ($result as $i => $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : count($result) - 1);
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre']);
	
	if (isset($data[$reg['num_cia']])) $tpl->assign("importe", number_format($data[$reg['num_cia']], 2, ".", ","));
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