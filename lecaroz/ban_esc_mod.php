<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "Contraseña incorrecta";
$descripcion_error[3] = "Ha cambiado de usuario";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_esc_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Terminar sesion
if (isset($_GET['terminar']))
	unset($_SESSION['esc_mod']);

// Validar usuario
if (isset($_POST['password'])) {
	// Buscar en la base de datos los permisos para la maquina y el usuario
	$sql = "SELECT iduser FROM esc_auth WHERE iduser = $_SESSION[iduser] AND ip_address = '$_SERVER[REMOTE_ADDR]' AND password = '".strtoupper($_POST['password'])."'";
	$id = $db->query($sql);
	
	// Si no hay resultados, reducir el número de oportunidades para autentificarse por 1
	if (!$id) {
		$_SESSION['esc_mod_times']--;
		header("location: ./ban_esc_mod.php?codigo_error=2");
		die;
	}
	
	unset($_SESSION['esc_mod_times']);
	$_SESSION['esc_mod'] = $id[0]['iduser'];
	header("location: ./ban_esc_mod.php");
	die;
}

// Validar Usuario y Dirección IP
if (!isset($_SESSION['esc_mod'])) {
	/*echo "USER: $_SESSION[iduser]<br>";
	echo "IP: $_SERVER[REMOTE_ADDR]<br>";*/
	
	if ($db->query("SELECT id FROM esc_auth WHERE ip_address = '$_SERVER[REMOTE_ADDR]'")) {
		$tpl->newBlock("password");
	}
	else {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","No se puede acceder a la pantalla desde este equipo");
	}
	
	if (!isset($_SESSION['esc_mod_times']))
		$_SESSION['esc_mod_times'] = 3;
	
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
	die;
}

// Realizar busqueda
if (isset($_SESSION['esc_mod']) && $_SESSION['esc_mod'] == $_SESSION['iduser'] && isset($_GET['buscar'])) {
	// Construir script
	$sql = "SELECT * FROM estado_cuenta WHERE";
	if (!in_array($_SESSION['iduser'], $users)) {
		if ($_GET['num_cia'] > 0) {
			$sql .= " num_cia = $_GET[num_cia]";
			if ($_GET['cuenta'] > 0 || $_GET['fecha1'] != "" || $_GET['tipo_mov'] != "todos" || $_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
				$sql .= " AND";
		}
	}
	else {
		if ($_GET['num_cia'] > 0) {
			$sql .= " num_cia = $_GET[num_cia] AND num_cia BETWEEN 900 AND 998";
			if ($_GET['cuenta'] > 0 || $_GET['fecha1'] != "" || $_GET['tipo_mov'] != "todos" || $_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
				$sql .= " AND";
		}
		else {
			$sql .= " num_cia BETWEEN 900 AND 998";
			if ($_GET['cuenta'] > 0 || $_GET['fecha1'] != "" || $_GET['tipo_mov'] != "todos" || $_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
				$sql .= " AND";
		}
	}
	if ($_GET['cuenta'] > 0) {
		$sql .= " cuenta = $_GET[cuenta]";
		if ($_GET['fecha1'] != "" || $_GET['tipo_mov'] != "todos" || $_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['fecha1'] != "") {
		if ($_GET['fecha2'] != "")
			$sql .= " fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'";
		else
			$sql .= " fecha >= '$_GET[fecha1]'";
		if ($_GET['tipo_mov'] != "todos" || $_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['tipo_mov'] == "abonos") {
		$sql .= " tipo_mov = 'FALSE'";
		if ($_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	else if ($_GET['tipo_mov'] == "retiros") {
		$sql .= " tipo_mov = 'TRUE'";
		if ($_GET['importe'] > 0 || $_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['importe'] > 0) {
		$sql .= " importe = $_GET[importe]";
		if ($_GET['cod_mov'] > 0 || $_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['cod_mov'] > 0) {
		$sql .= " cod_mov = $_GET[cod_mov]";
		if ($_GET['folio'] > 0 || $_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['folio'] > 0) {
		$sql .= " folio = $_GET[folio]";
		if ($_GET['concepto'] != "" || $_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['concepto'] != "") {
		$sql .= " concepto LIKE '%$_GET[concepto]%'";
		if ($_GET['con'] > 0)
			$sql .= " AND";
	}
	if ($_GET['con'] > 0)
		$sql .= " fecha_con IS " . ($_GET['con'] == 1 ? "NOT NULL" : "NULL");
	$sql .= " ORDER BY num_cia,fecha";
	
	// Ejecutar query
	$result = $db->query($sql);
	
	// Si no hay resultados, regresar un error
	if (!$result) {
		header("location: ./ban_esc_mod.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	
	$num_cia = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = $db->query("SELECT clabe_cuenta,clabe_cuenta2,nombre,nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("cuenta",$nombre_cia[0]['clabe_cuenta' . ($result[$i]['cuenta'] == 1 ? "" : "2")]);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("nombre_corto",$nombre_cia[0]['nombre_corto']);
		}
		// Omitir movimientos conciliados y cargos
		//if (($result[$i]['fecha_con'] != "" || $result[$i]['cod_mov'] == 5) && $result[$i]['tipo_mov'] == "t")
			//continue;
		$tpl->newBlock("fila");
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("banco", $result[$i]['cuenta'] == 1 ? "<font color='#0000CC'>BANORTE</font>" : "<font color='#CC0000'>SANTANDER</font>");
		$tpl->assign("fecha",$result[$i]['fecha']);
		$tpl->assign("fecha_con",$result[$i]['fecha_con'] != "" ? $result[$i]['fecha_con'] : "&nbsp;");
		$tpl->assign("deposito",$result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
		$tpl->assign("retiro",$result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'],2,".",",") : "&nbsp;");
		$tpl->assign("folio",$result[$i]['folio'] > 0 ? $result[$i]['folio'] : "&nbsp;");
		$tpl->assign("cod_mov",$result[$i]['cod_mov']);
		$cod_mov = $db->query("SELECT descripcion FROM catalogo_mov_" . ($result[$i]['cuenta'] == 1 ? "bancos" : "santander") . " WHERE cod_mov = ".$result[$i]['cod_mov']." LIMIT 1");
		$tpl->assign("descripcion",$cod_mov[0]['descripcion']);
		$tpl->assign("concepto",$result[$i]['concepto']);
		
		if ($result[$i]['fecha_con'] != "" || $result[$i]['cod_mov'] == 5) $tpl->assign("disabled_del","disabled");
		//if ($result[$i]['tipo_mov'] == "t") $tpl->assign("disabled_mod","disabled");
	}
	$tpl->printToScreen();
	die;
}

// Datos de búsqueda
if (isset($_SESSION['esc_mod']) && $_SESSION['esc_mod'] == $_SESSION['iduser']) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha", date("1/m/Y"));
	
	$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
	foreach ($cias as $c) {
		$tpl->newBlock('c');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre', $c['nombre_corto']);
	}
	
	$cod_mov_ban = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_bancos GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	$cod_mov_san = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	for ($i = 0; $i < count($cod_mov_ban); $i++) {
		$tpl->newBlock("banorte");
		$tpl->assign("i", $i);
		$tpl->assign("cod_mov", $cod_mov_ban[$i]['cod_mov']);
		$tpl->assign("des", $cod_mov_ban[$i]['descripcion']);
	}
	for ($i = 0; $i < count($cod_mov_san); $i++) {
		$tpl->newBlock("santander");
		$tpl->assign("i", $i);
		$tpl->assign("cod_mov", $cod_mov_san[$i]['cod_mov']);
		$tpl->assign("des", $cod_mov_san[$i]['descripcion']);
	}
	
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
	die;
}
else {
	/*echo "USER: $_SESSION[iduser]<br>";
	echo "IP: $_SERVER[REMOTE_ADDR]<br>";
	echo "ESC_MOD: $_SESSION[esc_mod]<br>";*/
	unset($_SESSION['esc_mod']);
	header("location: ./ban_esc_mod.php?codigo_error=3");
	die;
}

?>