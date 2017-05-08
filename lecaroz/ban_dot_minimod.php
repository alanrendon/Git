<?php
// MODIFICACIÓN DE MOVIMIENTOS AUTORIZADOS
// Tablas 'catalogo_mov_autorizados'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

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

$descripcion_error[1] = 'Error en la fecha';

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dot_minimod.tpl");
$tpl->prepare();

// --------------------------------- Modificar registro en la tabla -------------------------------------------
if (isset($_POST['id'])) {
	// [13-Ene-2010] Solo permitir capturar movimientos dentro de los ultimos 2 meses
	$ts_tope = mktime(0, 0, 0, date('n') - 1, 1, date('Y'));
	$ts_cap = mktime(0, 0, 0, $_POST['mes'], 1, $_POST['anio']);
	if ($ts_cap < $ts_tope) {
		header('location: ./ban_dot_minimod.php?codigo_error=1');
		die;
	}
	
	
	$concepto = trim(strtoupper($_POST['concepto']));
	ejecutar_script("UPDATE otros_depositos SET num_cia=$_POST[num_cia],fecha='$_POST[dia]/$_POST[mes]/$_POST[anio]',importe=$_POST[importe],concepto='$concepto',idnombre=" . ($_POST['idnombre'] > 0 ? $_POST['idnombre'] : "NULL") . ",acre=" . ($_POST['acre'] > 0 ? $_POST['acre'] : "NULL") . ", tsmod = now(), iduser = $_SESSION[iduser] WHERE id=$_POST[id]",$dsn);
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Generar pantalla de modificación
$tpl->newBlock("modificar");

// Asignar tabla de insercion
$tpl->assign("tabla","catalogo_mov_autorizados");

// Asignar ID
$tpl->assign("id",$_GET['id']);

// Obtener datos del registro
$result = ejecutar_script("SELECT * FROM otros_depositos WHERE id = $_GET[id]",$dsn);

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$result[0]['fecha'],$fecha);
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$result[0]['num_cia'],$dsn);

$tpl->assign("num_cia",$result[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("dia",intval($fecha[1]));
$tpl->assign("mes",intval($fecha[2]));
$tpl->assign("anio",intval($fecha[3]));
$tpl->assign("importe",number_format($result[0]['importe'],2,".",""));
$tpl->assign("concepto",$result[0]['concepto']);

// Número máximo de días
switch ($fecha[2]) {
	case 1: $maxdias = 31; break;
	case 2: $maxdias = 29; break;
	case 3: $maxdias = 31; break;
	case 4: $maxdias = 30; break;
	case 5: $maxdias = 31; break;
	case 6: $maxdias = 30; break;
	case 7: $maxdias = 31; break;
	case 8: $maxdias = 31; break;
	case 9: $maxdias = 30; break;
	case 10: $maxdias = 31; break;
	case 11: $maxdias = 30; break;
	case 12: $maxdias = 31; break;
}
$tpl->assign("maxdias",/*$maxdias*/31);

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

$nombres = ejecutar_script("SELECT id, num, nombre FROM catalogo_nombres ORDER BY num",$dsn);
foreach ($nombres as $nombre) {
	$tpl->newBlock('nombre');
	$tpl->assign('num', $nombre['num']);
	$tpl->assign('id', $nombre['id']);
	$tpl->assign('nombre', $nombre['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>