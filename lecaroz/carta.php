<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia, nombre, direccion, persona_fis_moral FROM catalogo_companias WHERE num_cia NOT IN (124,167,175,999) ORDER BY num_cia";
$result = $db->query($sql);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/carta.tpl" );
$tpl->prepare();

$dia = date("d");
$mes = date("m");
$anio = date("Y");

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("carta");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre']);
	$tpl->assign("direccion", $result[$i]['direccion']);
	$tpl->assign("dia", $dia);
	$tpl->assign("mes", mes_escrito($mes, TRUE));
	$tpl->assign("anio", $anio);
	$tpl->assign("persona", $result[$i]['persona_fis_moral'] == "t" ? $result[$i]['nombre'] : "JULIAN EUGENIO LARRACHEA ECHENIQUE");
	$tpl->assign("firma", $result[$i]['persona_fis_moral'] == "t" ? "ILDEFONSO LARRACHEA ECHENIQUE" : "JULIAN EUGENIO LARRACHEA ECHENIQUE");
}

$tpl->printToScreen();
$db->desconectar();
?>