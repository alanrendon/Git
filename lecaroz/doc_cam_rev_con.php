<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

function color($placa) {
	$ultimo_digito = NULL;
	
	if ($placa == "")
		return "&nbsp;";
	
	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};
	
	switch ($ultimo_digito) {
		case 5:
		case 6: $color = "<img src='./imagenes/amarillo.GIF'>"; break;
		case 7:
		case 8: $color = "<img src='./imagenes/rosa.GIF'>"; break;
		case 3:
		case 4: $color = "<img src='./imagenes/rojo.GIF'>"; break;
		case 1:
		case 2: $color = "<img src='./imagenes/verde.GIF'>"; break;
		case 9:
		case 0: $color = "<img src='./imagenes/azul.GIF'>"; break;
		default: $color = "&nbsp;";
	}
	
	return $color;
}

function regresa_color($placa) {
	$ultimo_digito = NULL;
	
	if ($placa == "")
		return 0;
	
	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};
	
	switch ($ultimo_digito) {
		case 5:
		case 6: $color = 1; break;
		case 7:
		case 8: $color = 2; break;
		case 3:
		case 4: $color = 3; break;
		case 1:
		case 2: $color = 4; break;
		case 9:
		case 0: $color = 5; break;
		default: $color = 0;
	}
	
	return $color;
}

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_rev_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pedir datos del documento
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	$db->desconectar();
	die;
}

$sql = "SELECT idcamioneta,modelo,anio,placas,num_cia,nombre_corto FROM catalogo_camionetas LEFT JOIN catalogo_companias USING (num_cia) WHERE estatus = 1";
$sql .= $_GET['idcamioneta'] > 0 ? " AND idcamioneta = $_GET[idcamioneta]" : "";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['color'] > 0 ? " AND color_placa(placas) = $_GET[color]" : '';
$sql .= " ORDER BY num_cia, idcamioneta";
$result = $db->query($sql);

if (!$result) {
	header("location: ./doc_cam_rev_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
$tpl->assign("anio", $_GET['anio']);

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("idcamioneta", $result[$i]['idcamioneta']);
	$tpl->assign("modelo", $result[$i]['modelo']);
	$tpl->assign("anio", $result[$i]['anio']);
	$tpl->assign("placas", $result[$i]['placas']);
	$tpl->assign("color", color($result[$i]['placas']));
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
	
	// Obtener datos de revisiones para el año especificado
	$sql = "SELECT * FROM revisiones_vehiculares WHERE idcamioneta = {$result[$i]['idcamioneta']} AND anio = $_GET[anio]";
	$rev = $db->query($sql);
	
	if ($rev) {
		$tpl->assign("ver1_ok", $rev[0]['ver1_ok'] == "t" ? "<strong>HECHA</strong>" : "");
		$tpl->assign("ver1_rec", $rev[0]['ver1_rec'] == "t" ? "" : ($rev[0]['ver1_ok'] == "t" ? " - <strong>NO HAY RECIBO</strong>" : ""));
		$tpl->assign("ver1_obs", $rev[0]['ver1_obs'] != "" ? "<br><strong>" . strtoupper($rev[0]['ver1_obs']) . "</strong>" : "");
		$tpl->assign("ver2_ok", $rev[0]['ver2_ok'] == "t" ? "<strong>HECHA</strong>" : "");
		$tpl->assign("ver2_rec", $rev[0]['ver2_rec'] == "t" ? "" : ($rev[0]['ver2_ok'] == "t" ? " - <strong>NO HAY RECIBO</strong>" : ""));
		$tpl->assign("ver2_obs", $rev[0]['ver2_obs'] != "" ? "<br><strong>" . strtoupper($rev[0]['ver2_obs']) . "</strong>" : "");
		$tpl->assign("rev_ok", $rev[0]['rev_ok'] == "t" ? "<strong>HECHA</strong>" : "");
		$tpl->assign("rev_rec", $rev[0]['rev_rec'] == "t" ? "" : ($rev[0]['rev_ok'] == "t" ? " - <strong>NO HAY RECIBO</strong>" : ""));
		$tpl->assign("rev_obs", $rev[0]['rev_obs'] != "" ? "<br><strong>" . strtoupper($rev[0]['rev_obs']) . "</strong>" : "");
		$tpl->assign("ten_ok", $rev[0]['ten_ok'] == "t" ? "<strong>HECHA</strong>" : "");
		$tpl->assign("ten_rec", $rev[0]['ten_rec'] == "t" ? "" : ($rev[0]['ten_ok'] == "t" ? " - <strong>NO HAY RECIBO</strong>" : ""));
		$tpl->assign("ten_obs", $rev[0]['ten_obs'] != "" ? "<br><strong>" . strtoupper($rev[0]['ten_obs']) . "</strong>" : "");
	}
}

$tpl->printToScreen();
$db->desconectar();
?>