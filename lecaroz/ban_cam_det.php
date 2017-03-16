<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_det.tpl");
$tpl->prepare();

$sql = "SELECT * FROM catalogo_camionetas WHERE idcamioneta = $_GET[id]";
$result = $db->query($sql);
$db->desconectar();

$tpl->assign("id", $_GET['id']);
$tpl->assign("modelo", $result[0]['modelo'] != "" ? $result[0]['modelo'] : "&nbsp;");
$tpl->assign("anio", $result[0]['anio'] > 0 ? $result[0]['anio'] : "&nbsp;");
$tpl->assign("placas", $result[0]['placas'] != "" ? $result[0]['placas'] : "&nbsp;");
$tpl->assign("num_cia", $result[0]['num_cia'] > 0 ? $result[0]['num_cia'] : "&nbsp;");
if ($result[0]['num_cia'] > 0)
	$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
else
	$nombre_cia[0]['nombre_corto'] = "&nbsp;";
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("propietario", $result[0]['propietario'] != "" ? $result[0]['propietario'] : "&nbsp;");
$tpl->assign("usuario", $result[0]['usuario'] != 0 ? $result[0]['usuario'] : "&nbsp;");
$tpl->assign("num_serie", $result[0]['num_serie'] != "" ? $result[0]['num_serie'] : "&nbsp;");
$tpl->assign("num_motor", $result[0]['num_motor'] != "" ? $result[0]['num_motor'] : "&nbsp;");
$tpl->assign("tipo_combustible", $result[0]['tipo_combustible'] == "t" ? "GASOLINA" : "GAS");
$tpl->assign("entidad", $result[0]['entidad'] == 1 ? "DISTRITO FEDERAL" : ($result[0]['entidad'] == 2 ? "ESTADO" : "OTROS"));
$tpl->assign("estatus", $result[0]['estatus'] == "t" ? "EN USO" : "VENDIDA");
$tpl->assign("fecha_venta", $result[0]['fecha_venta'] != "" : $result[0]['fecha_venta'] : "&nbsp;");
$tpl->assign("clave_vehicular", $result[0]['clave_vehicular'] != "" ? $result[0]['clave_vehicular'] : "&nbsp;");
$tpl->assign("num_poliza", $result[0]['num_poliza'] != "" ? $result[0]['num_poliza'] : "&nbsp;");
$tpl->assign("inciso", $result[0]['inciso'] != "" ? $result[0]['inciso'] : "&nbsp;");
$tpl->assign("plan", $result[0]['plan'] == 1 ? "TERCEROS" : ($result[0]['plan'] == 2 ? "TODO RIESGO" : ""));
$tpl->assign("localizacion_fac", $result[0]['localizacion_fac'] != "" ? $result[0]['localizacion_fac'] : "&nbsp;");
$tpl->assign("vencimiento", $result[0]['vencimiento'] != "" ? $result[0]['vencimiento'] : "&nbsp;");
$tpl->assign("cambio_motor", $result[0]['cambio_motor'] == "t" ? "SI" : "NO");

$tpl->printToScreen();
?>