<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_mod.tpl");
$tpl->prepare();

if (isset($_GET['documentos'])) {
	$tpl->newBlock("documentos");
	$tpl->assign("id", $_GET['id']);
	
	$sql = "SELECT id_doc, tipo_doc, fecha FROM doc_camionetas WHERE idcamioneta = $_GET[id] ORDER BY tipo_doc";
	$result = $db_scans->query($sql);
	//$db->desconectar();
	
	if ($result) {
		$tpl->newBlock("result");
		for ($i = 0; $i < count($result); $i++) {
			$tpl->newBlock("fila");
			$tpl->assign("iddoc", $result[$i]['id_doc']);
			$tipo_doc = $db->query("SELECT descripcion FROM catalogo_doc_camionetas WHERE tipo_doc = {$result[$i]['tipo_doc']}");
			$tpl->assign("tipo", $tipo_doc[0]['descripcion']);
			$tpl->assign("fecha", $result[$i]['fecha']);
		}
	}
	else
		$tpl->newBlock("no_result");
	
	$tpl->printToScreen();
	die;
}

if (isset($_POST['id'])) {
	$datos = $_POST;
	
	unset($datos['id']);
	
	$datos['modelo'] = strtoupper($datos['modelo']);
	$datos['placas'] = strtoupper($datos['placas']);
	$datos['propietario'] = strtoupper($datos['propietario']);
	$datos['usuario'] = strtoupper($datos['usuario']);
	$datos['num_serie'] = strtoupper($datos['num_serie']);
	$datos['num_motor'] = strtoupper($datos['num_motor']);
	$datos['clave_vehicular'] = strtoupper($datos['clave_vehicular']);
	$datos['num_poliza'] = strtoupper($datos['num_poliza']);
	$datos['inciso'] = strtoupper($datos['inciso']);
	$datos['localizacion_fac'] = strtoupper($datos['localizacion_fac']);
	
	$db->query($db->preparar_update("catalogo_camionetas", $datos, "\"idcamioneta\" = $_POST[id]"));
	$db->desconectar();
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("modificar");

$sql = "SELECT modelo,anio,placas,num_cia,nombre_corto,propietario,usuario,num_serie,num_motor,tipo_combustible,entidad,estatus,fecha_venta,clave_vehicular,num_poliza,ven_poliza,inciso,plan,localizacion_fac,vencimiento,cambio_motor, tipo_unidad, fecha_tarjeta_circulacion FROM catalogo_camionetas LEFT JOIN catalogo_companias USING (num_cia) WHERE idcamioneta = $_GET[id]";
$result = $db->query($sql);

// Datos generales
$tpl->assign("id", $_GET['id']);
$tpl->assign("modelo", $result[0]['modelo']);
$tpl->assign("anio", $result[0]['anio']);
$tpl->assign("placas", $result[0]['placas']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre_cia", $result[0]['nombre_corto']);
$tpl->assign("propietario", $result[0]['propietario']);
$tpl->assign("usuario", $result[0]['usuario']);
$tpl->assign("num_serie", $result[0]['num_serie']);
$tpl->assign("num_motor", $result[0]['num_motor']);
$tpl->assign(($result[0]['tipo_combustible'] == "t" ? "gasolina" : "gas"), "selected");
$tpl->assign("ent" . $result[0]['entidad'], "selected");
$tpl->assign("est" . $result[0]['estatus'], "selected");
$tpl->assign("est_dis", $result[0]['estatus'] == "t" ? "disabled=\"true\"" : "");
$tpl->assign("fecha_venta", $result[0]['fecha_venta']);
$tpl->assign("fecha_tarjeta_circulacion", $result[0]['fecha_tarjeta_circulacion']);
$tpl->assign("clave_vehicular", $result[0]['clave_vehicular']);
$tpl->assign("num_poliza", $result[0]['num_poliza']);
$tpl->assign("ven_poliza", $result[0]['ven_poliza']);
$tpl->assign("inciso", $result[0]['inciso']);
$tpl->assign("plan" . $result[0]['plan'], "selected");
$tpl->assign("localizacion_fac", $result[0]['localizacion_fac']);
$tpl->assign("vencimiento", $result[0]['vencimiento']);
$tpl->assign($result[0]['cambio_motor'] == "t" ? "motor1" : "motor2", "selected");
$tpl->assign('tipo_unidad_' . $result[0]['tipo_unidad'], ' selected');

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia < 900 ORDER BY num_cia";
$cia = $db->query($sql);
$db->desconectar();
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$tpl->printToScreen();
?>