<?php
// OTROS MOVIMIENTOS
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mov_cap.tpl");
$tpl->prepare();

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['tabla'])) {
	// Buscar ultima id de la tabla de depositos
	$result = ejecutar_script("SELECT id FROM estado_cuenta ORDER BY id DESC LIMIT 1",$dsn);
	$id = $result[0]['id'] + 1;
	
	for ($i=0; $i<10; $i++) {
		if ($_POST['importe'.$i] && $_POST['concepto'.$i]) {
			$cuenta['num_cia'] = $_SESSION['con']['num_cia'.$_SESSION['con']['next']];
			$cuenta['fecha'] = $_SESSION['con']['fecha_con'];
			$cuenta['fecha_con'] = /*$_SESSION['con']['fecha_con']*/"";
			$cuenta['concepto'] = $_POST['concepto'.$i];
			$tipo_mov = ejecutar_script("SELECT tipo_mov FROM catalogo_mov_bancos WHERE cod_mov=".$_POST['cod_mov'.$i],$dsn);
			$cuenta['tipo_mov'] = $tipo_mov[0]['tipo_mov'];
			$cuenta['importe'] = $_POST['importe'.$i];
			$cuenta['saldo_ini'] = "";
			$cuenta['saldo_fin'] = "";
			$cuenta['cod_mov'] = $_POST['cod_mov'.$i];
			$cuenta['folio'] = "";
			
			$_SESSION['check']['id'.count($_SESSION['check'])] = $id;
			$id++;
			
			// Actualizar saldo en libros
			ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros + $cuenta[importe] WHERE cuenta = 1 AND num_cia = $cuenta[num_cia]",$dsn);
			
			$db_cuenta = new DBclass($dsn,"estado_cuenta",$cuenta);
			$db_cuenta->generar_script_insert("");
			$db_cuenta->ejecutar_script();
			
			unset($db_cuenta);
		}
	}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// ------------------------------------------- Generar pantalla --------------------------------------------

// Almacenar temporalmente los ID's
if (isset($_SESSION['check']))
	unset($_SESSION['check']);
// Almacenar ID's de cheques
$count = 0;
if (isset($_POST['num_che']))
	for ($i=0; $i<$_POST['num_che']; $i++)
		if (isset($_POST['che_con'.$i])) {
			$_SESSION['check']['id'.$count] = $_POST['che_con'.$i];
			$count++;
		}
// Almacenar ID's de depósitos
if (isset($_POST['num_dep']))
	for ($i=0; $i<$_POST['num_dep']; $i++)
		if (isset($_POST['dep_con'.$i])) {
			$_SESSION['check']['id'.$count] = $_POST['dep_con'.$i];
			$count++;
		}

$tpl->newBlock("captura");
$tpl->assign("tabla","depositos");

$mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) cod_mov,descripcion FROM catalogo_mov_bancos ORDER BY cod_mov",$dsn);

for ($i=0; $i<10; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",10-1);
	
	if ($i < 10-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
		
	$tpl->assign("importe",isset($_SESSION['dep'])?$_SESSION['dep']['importe'.$i]:"");
	$tpl->assign("concepto",isset($_SESSION['dep'])?$_SESSION['dep']['concepto'.$i]:"");

	for ($j=0; $j<count($mov); $j++) {
		$tpl->newBlock("mov");
		$tpl->assign("id",$mov[$j]['cod_mov']);
		$tpl->assign("descripcion",$mov[$j]['descripcion']);
		$tpl->assign("selected",isset($_SESSION['dep'])?(($_SESSION['dep']['cod_mov'.$i] == $mov[$j]['cod_mov'])?"selected":""):"");
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>