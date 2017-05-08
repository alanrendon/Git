<?php
// CARTA DE CERTIFICACION DE CHEQUES
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/cheques.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$numfilas = 10;

// ---------------------------------- Insertar datos en tablas -----------------------------------------------
if (isset($_GET['carta'])) {
	// Almacenar datos temporalmente
	$_SESSION['cer'] = $_POST;

	$tpl = new TemplatePower( "./plantillas/ban/certificacion.tpl" );
	$tpl->prepare();

	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['num_cia'.$i] > 0 && $_POST['fecha'.$i] != "" && $_POST['folio'.$i] && $_POST['nombre'.$i] != "") {
			$tpl->newBlock("carta");

			$cia = ejecutar_script("SELECT nombre, rfc, direccion, clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia = {$_POST['num_cia'.$i]}", $dsn);

			$tpl->assign("banco", $_POST['cuenta'] == 1 ? "BANCO MERCANTIL DEL NORTE S.A." : "BANCO SANTANDER MEXICO, S.A.");
			$tpl->assign("nombre_cia",$_POST['nombre_cia'.$i]);
			$tpl->assign('rfc', $cia[0]['rfc']);
			$tpl->assign('dir', $cia[0]['direccion']);

			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha'.$i],$fecha);
			$tpl->assign("dia",$fecha[1]);
			$tpl->assign("mes",mes_escrito($fecha[2],TRUE));
			$tpl->assign("anio",$fecha[3]);
			$tpl->assign("folio",$_POST['folio'.$i]);
			// Buscar datos del cheque
			$cheque = ejecutar_script("SELECT importe,a_nombre FROM cheques WHERE num_cia = {$_POST['num_cia'.$i]} AND folio = {$_POST['folio'.$i]} AND cuenta = $_POST[cuenta] AND fecha_cancelacion IS NULL",$dsn);
			if (!$cheque) {
				$tpl->newBlock("error");
				$tpl->assign("num_cia",$_POST['num_cia'.$i]);
				$tpl->assign("folio",$_POST['folio'.$i]);
				$tpl->printToScreen();
				die;
			}
			$tpl->assign("importe",number_format($cheque[0]['importe'],2,".",","));
			$tpl->assign("importe_escrito",num2string($cheque[0]['importe']));
			$tpl->assign("cuenta",$_POST['cuenta'] == 1 ? $cia[0]['clabe_cuenta'] : $cia[0]['clabe_cuenta2']);
			$tpl->assign("a_nombre",$cheque[0]['a_nombre']);
			$tpl->assign("nombre",$_POST['nombre'.$i]);
		}
	}
	$tpl->printToScreen();
	unset($_SESSION['cer']);
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_cer.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$cia = ejecutar_script("SELECT num_cia,nombre,clabe_cuenta,clabe_cuenta2 FROM catalogo_companias WHERE clabe_cuenta IS NOT NULL ORDER BY num_cia",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre']);
	$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
	$tpl->assign("cuenta2",$cia[$i]['clabe_cuenta2']);
}

$nombres = array('FELIPE GUZMAN FREGOSO', 'CARLOS VEGA GONZALEZ', 'CRISTIAN ERNESTO GONZALEZ RODRIGUEZ', 'JESUS MARIA ZUBIZARRETA CEBERIO');

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",$i > 0 ? $i-1 : $numfilas-1);
	$tpl->assign("next",$i < $numfilas-1 ? $i+1 : 0);
	if (isset($_SESSION['cer'])) {
		$tpl->assign("num_cia",$_SESSION['cer']['num_cia'.$i]);
		$tpl->assign("cuenta",$_SESSION['cer']['cuenta'.$i]);
		$tpl->assign("nombre_cia",$_SESSION['cer']['nombre_cia'.$i]);
		$tpl->assign("fecha",$_SESSION['cer']['fecha'.$i]);
		$tpl->assign("folio",$_SESSION['cer']['folio'.$i]);
		if ($_SESSION['tipo_usuario'] == 2) {
			$tpl->newBlock('nombre');
			$tpl->assign('nombre', 'RAMON IRIGOYEN LERCHUNDI');
			$tpl->assign('selected', ' selected');
		}
		else {
			foreach ($nombres as $n) {
				$tpl->newBlock('nombre');
				$tpl->assign('nombre', $n);
				if ($n == $_SESSION['cer']['nombre' . $i])
					$tpl->assign('selected', ' selected');
			}
		}
	}
	else {
		$tpl->assign("fecha",date("d/m/Y"));

		if ($_SESSION['tipo_usuario'] == 2) {
			$tpl->newBlock('nombre');
			$tpl->assign('nombre', 'RAMON IRIGOYEN LERCHUNDI');
			$tpl->assign('selected', ' selected');
		}
		else {
			foreach ($nombres as $j => $n) {
				$tpl->newBlock('nombre');
				$tpl->assign('nombre', $n);
				if ($j == 0)
					$tpl->assign('selected', ' selected');
			}
		}
	}
}

unset($_SESSION['cer']);

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
