<?php
// COMPRA DIRECTA DE MATERIA PRIMA
// Tablas varias ''
// Menu ''

//define ('IDSCREEN',1222); // ID de pantalla

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
$descripcion_error[1] = "La compa&ntilde;&iacute;a no existe en la Base de Datos";

$numfilas = 10;

// --------------------------------- Generar pantalla --------------------------------------------------------
if (isset($_POST['num_cia'])) {
	$count = 0;
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['codmp'.$i] > 0 && $_POST['cantidad'.$i] > 0 && $_POST['precio_unidad'.$i] > 0) {
			// Generar todos los datos
			$mov['num_cia'.$count]       = $_POST['num_cia'];
			$mov['codmp'.$count]         = $_POST['codmp'.$i];
			$mov['fecha'.$count]         = $_POST['fecha'];
			$mov['tipo_mov'.$count]      = "FALSE";
			$mov['cantidad'.$count]      = $_POST['cantidad'.$i];
			$mov['precio'.$count]        = $_POST['precio_unidad'.$i];
			$mov['total_mov'.$count]     = round($_POST['cantidad'.$i] * $_POST['precio_unidad'.$i],2);
			$mov['precio_unidad'.$count] = $_POST['precio_unidad'.$i];
			$mov['descripcion'.$count]   = "COMPRA DIRECTA DE AVIO";
			
			// Calcular costo promedio
			$sql = "SELECT existencia FROM inventario_real WHERE num_cia = $_POST[num_cia] AND codmp = {$_POST['codmp'.$i]}";
			$temp = ejecutar_script($sql,$dsn);
			if ($temp && $temp[0]['existencia'] > 0)
				@$precio_unidad = ($_POST['precio_unidad'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / ($_POST['cantidad'.$i] + $temp[0]['existencia']);
			else
				@$precio_unidad = $_POST['precio_unidad'.$i];
			
			// Actualizar inventario
			if ($id = ejecutar_script("SELECT idinv FROM inventario_real WHERE num_cia = $_POST[num_cia] AND codmp = {$_POST['comdp'.$i]}",$dsn))
				$sql = "UPDATE inventario_real SET existencia = existencia + {$_POST['cantidad'.$i]}, precio_unidad = $precio_unidad WHERE idinv = {$id[0]['idinv']}";
			else
				$sql = "INSERT INTO inventario_real (num_cia,codmp,existencia,cantidad) VALUES ($_POST[num_cia],{$_POST['codmp'.$i]},{$_POST['cantidad'.$i]},$precio_unidad)";
			ejecutar_script($sql,$dsn);
			
			$count++;
		}
	}
	$db = new DBclass($dsn,"mov_inv_real",$mov);
	$db->xinsertar();
	
	header("location: ./pan_com_dir.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_com_dir.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Obtener compañías por capturista
	if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia < 100 OR num_cia IN (702,703)) ORDER BY num_cia";
	else
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 OR num_cia IN (702,703) ORDER BY num_cia";
	$num_cia = ejecutar_script($sql,$dsn);
	
	for ($i=0; $i<count($num_cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$num_cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$num_cia[$i]['nombre_corto']);
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
	die();
}

// ----------------------------- Generar pantalla de captura ----------------------------------
$tpl->newBlock("captura");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("fecha",$_GET['fecha']);
$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);

// Generar nombres de materia prima
$sql = "SELECT codmp,nombre FROM catalogo_mat_primas WHERE tipo_cia = 'TRUE' ORDER BY codmp";
$mp = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("mp");
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre",$mp[$i]['nombre']);
}

// Generar filas de captura
for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",$i > 0 ? $i-1 : $numfilas-1);
	$tpl->assign("next",$i < $numfilas-1 ? $i+1 : 0);
}

// Imprimir el resultado
$tpl->printToScreen();
?>