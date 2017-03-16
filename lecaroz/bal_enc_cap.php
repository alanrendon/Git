<?php
// CONTROL DE ENCARGADOS
// Tabla 'nominas'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ---------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Ya se capturaron los encargados del mes dado";
$descripcion_error[2] = "";

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
$tpl->assignInclude("body","./plantillas/bal/bal_enc_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['mes'])) {
	$sql = "DELETE FROM encargados WHERE mes = $_POST[mes] AND anio = $_POST[anio] AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 300') . ";\n";
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$datos['num_cia'.$i] = $_POST['num_cia'.$i];
		$datos['nombre_inicio'.$i] = strtoupper($_POST['nombre_inicio'.$i]);
		$datos['nombre_fin'.$i] = strtoupper($_POST['nombre_fin'.$i]);
		$datos['mes'.$i] = $_POST['mes'];
		$datos['anio'.$i] = $_POST['anio'];
		
		/*if ($id = ejecutar_script("SELECT id FROM encargados WHERE num_cia = {$_POST['num_cia'.$i]} AND mes = $_POST[mes] AND anio = $_POST[anio]",$dsn))
			$sql .= "UPDATE encargados SET nombre_inicio = '{$_POST['nombre_inicio'.$i]}', nombre_fin = '{$_POST['nombre_fin'.$i]}' WHERE id = {$id[0]['id']};\n";
		else*/
			$sql .= "INSERT INTO encargados (num_cia,nombre_inicio,nombre_fin,mes,anio) VALUES ({$datos['num_cia'.$i]},'{$datos['nombre_inicio'.$i]}','{$datos['nombre_fin'.$i]}',$_POST[mes],$_POST[anio]);\n";
	}
	
	ejecutar_script($sql,$dsn);
	
	header("location: ./bal_enc_cap.php");
	die;
}

if (isset($_GET['mes'])) {
	// Verificar que no se haya capturado el control del mes
	/*$sql = "SELECT * FROM encargados WHERE mes = $_GET[mes] AND anio = $_GET[anio] LIMIT 1";
	if (ejecutar_script($sql,$dsn)) {
		header("location: ./bal_enc_cap.php?codigo_error=1");
		die;
	}*/
	
	$tpl->newBlock("captura");
	$tpl->assign("mes",$_GET['mes']);
	$tpl->assign("mes_escrito",mes_escrito($_GET['mes'],TRUE));
	$tpl->assign("anio",$_GET['anio']);
	
	// Obtener compañías
	$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 300') . " ORDER BY num_cia";
	$cia = ejecutar_script($sql,$dsn);
	
	$tpl->assign("numfilas",count($cia));
	
	for ($i=0; $i<count($cia); $i++) {
		$num_cia = $cia[$i]['num_cia'];
		$nombre_cia = $cia[$i]['nombre_corto'];
		
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("back",$i>0?$i-1:count($cia)-1);
		$tpl->assign("next",$i<count($cia)-1?$i+1:0);
		
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre_cia",$nombre_cia);
		// Obtener nombre anterior
		if ($nombre = ejecutar_script("SELECT nombre_inicio,nombre_fin FROM encargados WHERE num_cia = $num_cia AND mes = $_GET[mes] AND anio = $_GET[anio]",$dsn)) {
			$tpl->assign("nombre_inicio", $nombre[0]['nombre_inicio']);
			$tpl->assign("nombre_fin", $nombre[0]['nombre_fin']);
		}
		else {
			$sql = "SELECT * FROM encargados WHERE num_cia = $num_cia AND mes = ".($_GET['mes'] > 1 ? $_GET['mes'] - 1 : 12)." AND anio = ".($_GET['mes'] > 1 ? $_GET['anio'] : $_GET['anio'] - 1);
			$encargado = ejecutar_script($sql,$dsn);
			$tpl->assign("nombre_inicio",$encargado ? $encargado[0]['nombre_inicio'] : "");
			$tpl->assign("nombre_fin",$encargado ? $encargado[0]['nombre_inicio'] : "");
		}
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$tpl->assign("anio",date("Y"));
$tpl->assign(date("n", mktime(0,0,0,date("m")-1,1,date("Y")))," selected");

$tpl->printToScreen();
?> 