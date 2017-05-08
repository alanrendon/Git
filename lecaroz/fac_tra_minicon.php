<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_tra_minicon.tpl");
$tpl->prepare();

$db = new DBclass($dsn);

$sql = "SELECT * FROM catalogo_trabajadores WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
$tpl->assign("nombre", $result[0]['nombre']);
$tpl->assign("ap_paterno", $result[0]['ap_paterno']);
$tpl->assign("ap_materno", $result[0]['ap_materno']);
$tpl->assign("fecha_nac", $result[0]['fecha_nac']);
$tpl->assign("lugar_nac", $result[0]['lugar_nac']);
$tpl->assign("sexo", $result[0]['sexo'] == "f" ? "Hombre" : $result[0]['sexo'] == "t" ? "Mujer" : "&nbsp;");
$tpl->assign("rfc", $result[0]['rfc'] . "-" . $result[0]['homo_clave']);
$tpl->assign("calle", $result[0]['calle']);
$tpl->assign("colonia", $result[0]['colonia']);
$tpl->assign("cod_postal", $result[0]['cod_postal']);
$tpl->assign("del_mun", $result[0]['del_mun']);
$tpl->assign("entidad", $result[0]['entidad']);
$puesto = $db->query("SELECT descripcion FROM catalogo_puestos WHERE cod_puestos = " . ($result[0]['cod_puestos'] > 0 ? $result[0]['cod_puestos'] : 1));
$tpl->assign("puesto", $puesto[0]['descripcion']);
$horario = $db->query("SELECT descripcion FROM catalogo_horarios WHERE cod_horario = " . ($result[0]['cod_horario'] > 0 ? $result[0]['cod_horario'] : 1));
$tpl->assign("horario", $horario[0]['descripcion']);
$turno = $db->query("SELECT descripcion FROM catalogo_turnos WHERE cod_turno = " . ($result[0]['cod_turno'] > 0 ? $result[0]['cod_turno'] : 1));
$tpl->assign("turno", $turno[0]['descripcion']);
$tpl->assign("salario", number_format($result[0]['salario'], 2, ".", ","));
$tpl->assign("fecha_alta_imss", $result[0]['fecha_alta_imss']);
$tpl->assign("fecha_baja_imss", $result[0]['fecha_baja_imss']);
$tpl->assign("num_afiliacion", $result[0]['num_afiliacion']);

if ($result[0]['pendiente_baja'] != "") $status = "PENDIENTE BAJA";
else if ($result[0]['fecha_baja'] != "") $status = "BAJA DEFINITIVA";
else if ($result[0]['pendiente_alta'] != "") $status = "PENDIENTE ALTA";
else if ($result[0]['num_afiliacion'] != "") $status = "EN NOMINA";
else if ($result[0]['solo_aguinaldo'] == "t") $status = "SOLO AGUINALDO";
else $status = "&nbsp;";
$tpl->assign("estatus",$status);

$tpl->printToScreen();
$db->desconectar();
?>