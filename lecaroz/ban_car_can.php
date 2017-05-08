<?php
// CARTA DE CERTIFICACION DE CHEQUES
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/cheques.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$numfilas = 50;

// ---------------------------------- Insertar datos en tablas -----------------------------------------------
if (isset($_POST['carta'])) {
	// Almacenar datos temporalmente
	$_SESSION['can'] = $_POST;
	
	$clabe_cuenta = $_POST['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	$tpl = new TemplatePower( "./plantillas/ban/cancelacion.tpl" );
	$tpl->prepare();
	
	$tpl->newBlock("carta");
	$tpl->assign('oficina', $_SESSION['tipo_usuario'] == 2 ? 'Zapaterias Elite' : 'Oficinas Administrativas Mollendo S. de R.L. de C.V. ');
	
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n"), TRUE));
	$tpl->assign("anio", date("Y"));
	$tpl->assign("banco", $_POST['cuenta'] == 1 ? "BANCO MERCANTIL DEL NORTE S.A." : "BANCO SANTANDER SERFIN, S.A.");
	$tpl->assign("firma", $_POST['firma']);
	
	/*$sql = "SELECT $clabe_cuenta, nombre, importe, folio, a_nombre FROM cheques LEFT JOIN catalogo_companias USING (num_cia) WHERE (num_cia, cuenta, folio) IN (SELECT num_cia, cuenta, folio FROM cheques LEFT JOIN estado_cuenta USING (num_cia, cuenta, folio) WHERE fecha_con IS NULL AND num_proveedor = 584 AND fecha_cancelacion IS NULL AND cuenta = 2 AND cheques.importe > 0 AND (num_cia, folio) NOT IN ({12, 40}, {18, 85}, {42, 20}, {42, 53}, {60, 45}, {66, 32}, {67, 61})) ORDER BY num_cia, folio";
	$result = $db->query($sql);*/
	
	/*foreach ($result as $row) {
		$tpl->newBlock("fila");
		$tpl->assign("cuenta", $row[$clabe_cuenta]);
		$tpl->assign("nombre_cia",$row['nombre']);
		$tpl->assign("folio",$row['folio']);
		$tpl->assign("importe", number_format($row['importe'],2,".",","));
		$tpl->assign("a_nombre", $row['a_nombre']);
	}*/
	
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['num_cia'][$i] > 0 && $_POST['folio'][$i]) {
			$tpl->newBlock("fila");
			$cheque = $db->query("SELECT $clabe_cuenta,importe,a_nombre,fecha FROM cheques LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]} AND cuenta = $_POST[cuenta]");
			$tpl->assign("cuenta", $cheque[0][$clabe_cuenta]);
			$tpl->assign("nombre_cia",$_POST['nombre_cia'][$i]);
			$tpl->assign("folio",$_POST['folio'][$i]);
			$tpl->assign("importe", number_format($cheque[0]['importe'],2,".",","));
			$tpl->assign("a_nombre",$cheque[0]['a_nombre']);
			$tpl->assign("fecha", $cheque[0]['fecha']);
		}
	}
	$tpl->printToScreen();
	$db->desconectar();
	
	unset($_SESSION['can']);
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_can.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$cia = $db->query("SELECT num_cia,nombre FROM catalogo_companias WHERE clabe_cuenta IS NOT NULL ORDER BY num_cia");
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre']);
}

$tpl->newBlock($_SESSION['tipo_usuario'] == 2 ? 'zap' : 'pan');

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i < $numfilas-1 ? $i+1 : 0);
	if (isset($_SESSION['can'])) {
		$tpl->assign("num_cia",$_SESSION['can']['num_cia'.$i]);
		$tpl->assign("nombre_cia",$_SESSION['can']['nombre_cia'.$i]);
		$tpl->assign("folio",$_SESSION['can']['folio'.$i]);
	}
}

unset($_SESSION['can']);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "No existe el cheque con folio $_GET[folio] para la compañía $_GET[num_cia]");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>