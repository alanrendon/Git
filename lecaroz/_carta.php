<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$descripcion_error[1] = "No hay resultados";

$session = new sessionclass($dsn);

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT * FROM cartas_foleadas WHERE id = $_GET[id]";
$result = $db->query($sql);
$reg = $result[0];

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower('./plantillas/ban/' . ($_SESSION['tipo_usuario'] == 2 ? '_carta_zap.tpl' : '_carta.tpl'));
$tpl->prepare();

$tpl->newBlock('hoja');

$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $reg[num_cia]");

$html_characters = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ñ', "\n");
$html_character_entities = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;', '&Ntilde;', '<br />');

$tpl->assign('folio', $reg['folio']);
ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha'], $fecha);
$tpl->assign('dia', $fecha[1]);
$tpl->assign('mes', mes_escrito($fecha[2]));
$tpl->assign('anyo', $fecha[3]);
$tpl->assign('empresa', $nombre_cia[0]['nombre']);
$tpl->assign('atencion', $reg['atencion']);
$tpl->assign('referencia', $reg['referencia']);
$tpl->assign('cuerpo', str_replace($html_characters, $html_character_entities, $reg['cuerpo']));
$tpl->assign('fecha', '&nbsp;');

$tpl->printToScreen();
?>