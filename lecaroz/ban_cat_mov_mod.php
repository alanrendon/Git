<?php
// MODIFICACION DE MOVIMIENTOS BANCARIOS
// Tablas 'catalogo_mov_bancos'
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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en el catálogo de movimientos bancarios";
$descripcion_error[2] = "No hay registros en el catálogo";

// --------------------------------- Modificar registro en la tabla -------------------------------------------
if (isset($_GET['tabla'])) {
	for ($i=1; $i<=3; $i++)
		if ($_POST['cod_banco'.$i] != "") {
			if ($_POST['id'.$i] != "")
				$sql = "UPDATE $_GET[tabla] SET cod_banco=".$_POST['cod_banco'.$i].",descripcion='$_POST[descripcion]',tipo_mov='$_POST[tipo_mov]',entra_bal='$_POST[entra_bal]' WHERE id=".$_POST['id'.$i];
			else
				$sql = "INSERT INTO $_GET[tabla] (cod_mov,cod_banco,descripcion,tipo_mov,entra_bal) VALUES ($_POST[cod_mov],".$_POST['cod_banco'.$i].",'$_POST[descripcion]','$_POST[tipo_mov]','$_POST[entra_bal]')";
			ejecutar_script($sql,$dsn);
		}
		else {
			if ($_POST['id'.$i] != "") {
				$sql = "DELETE FROM $_GET[tabla] WHERE id=".$_POST['id'.$i];
				ejecutar_script($sql,$dsn);
			}
		}
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cat_mov_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mov'])) {
	$tpl->newBlock("datos");
		
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);
	}

	$tpl->printToScreen();
	die;
}

if (isset($_GET['mov']) && $_GET['mov'] == "lis") {
	$result = ejecutar_script("SELECT * FROM catalogo_mov_bancos ORDER BY cod_mov ASC",$dsn);
	if (!$result) {
		header("location: ./ban_cat_mov_mod.php?codigo_error=2");
		die;
	}
	
	$tpl->newBlock("listado");
	$cod_ant = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($cod_ant != $result[$i]['cod_mov']) {
			$cod_ant = $result[$i]['cod_mov'];
			$tpl->newBlock("fila");
			$tpl->assign("cod_mov",$result[$i]['cod_mov']);
			$tpl->newBlock("cod_banco");
			$tpl->assign("cod_banco",$result[$i]['cod_banco']);
			$tpl->gotoBlock("fila");
			$tpl->assign("descripcion",$result[$i]['descripcion']);
			if ($result[$i]['tipo_mov'] == "t")
				$tpl->assign("tipo_mov","CARGO");
			else
				$tpl->assign("tipo_mov","ABONO");
			if ($result[$i]['entra_bal'] == "t")
				$tpl->assign("entra_bal","SI");
			else
				$tpl->assign("entra_bal","NO");
		}
		else {
			$tpl->newBlock("cod_banco");
			$tpl->assign("cod_banco","- ".$result[$i]['cod_banco']);
		}
	}
	$tpl->printToScreen();
	die;
}
else if (isset($_GET['mov']) && $_GET['mov'] == "mod") {
	// Modificar
	if (!existe_registro("catalogo_mov_bancos",array("cod_mov"),array($_GET['cod_mov']),$dsn)) {
		header("location: ./ban_cat_mov_mod.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("modificar");
	
	// Asignar tabla de insercion
	$tpl->assign("tabla","catalogo_mov_bancos");
	
	// Obtener datos del catalogo
	$datos = ejecutar_script("SELECT * FROM catalogo_mov_bancos WHERE cod_mov = $_GET[cod_mov]",$dsn);
	
	// Asignar ID
	$tpl->assign("cod_mov",$datos[0]['cod_mov']);
	$tpl->assign("descripcion",$datos[0]['descripcion']);
	
	for ($i=0; $i<count($datos); $i++) {
		$tpl->assign("id".($i+1),$datos[$i]['id']);
		$tpl->assign("cod_banco".($i+1),$datos[$i]['cod_banco']);
	}
	
	if ($datos[0]['tipo_mov'] == "t")
		$tpl->assign("cargo","selected");
	else
		$tpl->assign("abono","selected");
	if ($datos[0]['entra_bal'] == "t")
		$tpl->assign("si","checked");
	else
		$tpl->assign("no","checked");
	
	$tpl->printToScreen();
	die;
}
else if (isset($_GET['mov']) && $_GET['mov'] == "del") {
	$sql = "DELETE FROM catalogo_mov_bancos WHERE cod_mov = $_GET[cod_mov]";
	ejecutar_script($sql,$dsn);
	header("location: ./ban_cat_mov_mod.php");
	die;
}
?>