<?php
// CONSUMO DE AVIO
// Tablas varias ''
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1222); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compa&ntilde;&iacute;a no existe en la Base de Datos";
$descripcion_error[2] = "No se han generado las diferencias de fin de mes";

// --------------------------------- Capturar datos ----------------------------------------------------------
if (isset($_GET['tabla'])) {
	// Ordenar datos para mov_inv_real y mov_inv_virtual
	$count1 = 0;
	$count2 = 0;
	// Movimientos de entrada
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['entrada'.$i] > 0) {
			if ($_POST['codmp_entrada'.$i] == 1)
				$existencia = $_POST['entrada'.$i] * 44;
			else
				$existencia = $_POST['entrada'.$i];
			
			$mov_virtual['num_cia'.$count1]       = $_POST['num_cia'];
			$mov_virtual['codmp'.$count1]         = $_POST['codmp_entrada'.$i];
			$mov_virtual['fecha'.$count1]         = $_POST['fecha'];
			$mov_virtual['cod_turno'.$count1]     = "";
			$mov_virtual['tipo_mov'.$count1]      = "FALSE";
			$mov_virtual['cantidad'.$count1]      = $existencia;
			$mov_virtual['existencia'.$count1]    = "0";
			$mov_virtual['precio'.$count1]        = $_POST['precio_unidad_entrada'.$i];
			$mov_virtual['total_mov'.$count1]     = $existencia * $_POST['precio_unidad_entrada'.$i];
			$mov_virtual['precio_unidad'.$count1] = $_POST['precio_unidad_entrada'.$i];
			$mov_virtual['descripcion'.$count1]   = "ENTRADA VIRTUAL DE AVIO";
			
			// Actualizar inventario virtual
			//if (existe_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'],$_POST['codmp_entrada'.$i]),$dsn))
			ejecutar_script("UPDATE inventario_virtual SET existencia=existencia+".$existencia.",fecha_entrada='$_POST[fecha]' WHERE num_cia=$_POST[num_cia] AND codmp=".$_POST['codmp_entrada'.$i],$dsn);
			/*else
				ejecutar_script("INSERT INTO inventario_virtual (num_cia,codmp,fecha_entrada,fecha_salida,existencia,precio_unidad) VALUES ($_POST[num_cia],".$_POST['codmp'.$i].",'$_POST[fecha]',NULL,".$_POST['entrada'.$i].",0)",$dsn);*/
			
			$count1++; 
		}
	}
	// Movimientos de salida
	for ($i=0; $i<$_POST['numelementos']; $i++) {
		if ($_POST['consumo'.$i] > 0) {
			if ($_POST['codmp'.$i] == 1)
				$consumo = $_POST['consumo'.$i] * 44;
			else
				$consumo = $_POST['consumo'.$i];
			
			$mov_virtual['num_cia'.$count1]       = $_POST['num_cia'];
			$mov_virtual['codmp'.$count1]         = $_POST['codmp'.$i];
			$mov_virtual['fecha'.$count1]         = $_POST['fecha'];
			$mov_virtual['cod_turno'.$count1]     = $_POST['cod_turno'.$i];
			$mov_virtual['tipo_mov'.$count1]      = "TRUE";
			$mov_virtual['cantidad'.$count1]      = $consumo;
			$mov_virtual['existencia'.$count1]    = "0";
			$mov_virtual['precio'.$count1]        = $_POST['precio_unidad'.$i];
			$mov_virtual['total_mov'.$count1]     = $consumo * $_POST['precio_unidad'.$i];
			$mov_virtual['precio_unidad'.$count1] = $_POST['precio_unidad'.$i];
			$mov_virtual['descripcion'.$count1]   = "SALIDA VIRTUAL DE AVIO";
			
			$mov_real['num_cia'.$count2]       = $_POST['num_cia'];
			$mov_real['codmp'.$count2]         = $_POST['codmp'.$i];
			$mov_real['fecha'.$count2]         = $_POST['fecha'];
			$mov_real['cod_turno'.$count2]     = $_POST['cod_turno'.$i];
			$mov_real['tipo_mov'.$count2]      = "TRUE";
			$mov_real['cantidad'.$count2]      = $consumo;
			$mov_real['existencia'.$count2]    = "0";
			$mov_real['precio'.$count2]        = $_POST['precio_unidad'.$i];
			$mov_real['total_mov'.$count2]     = $consumo * $_POST['precio_unidad'.$i];
			$mov_real['precio_unidad'.$count2] = $_POST['precio_unidad'.$i];
			$mov_real['descripcion'.$count2]   = "SALIDA DE AVIO";
			
			//if (existe_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'],$_POST['codmp'.$i]),$dsn)) {
				ejecutar_script("UPDATE inventario_virtual SET existencia=existencia-$consumo,fecha_salida='$_POST[fecha]' WHERE num_cia=$_POST[num_cia] AND codmp=".$_POST['codmp'.$i],$dsn);
				ejecutar_script("UPDATE inventario_real SET existencia=existencia-$consumo,fecha_salida='$_POST[fecha]' WHERE num_cia=$_POST[num_cia] AND codmp=".$_POST['codmp'.$i],$dsn);
			//}
			
			$count1++;
			$count2++;
		}
	}
	
	// Insertar movimientos
	if (isset($mov_virtual)) {
		$db_virtual = new DBclass($dsn,"mov_inv_virtual",$mov_virtual);
		$db_virtual->xinsertar();
	}
	else {
		$sql = "INSERT INTO mov_inv_virtual (num_cia, fecha, tipo_mov, descripcion) VALUES ($_POST[num_cia], '$_POST[fecha]', 'TRUE', 'SALTO DE DIA')";
		ejecutar_script($sql,$dsn);
	}
	
	if (isset($mov_real)) {
		$db_real = new DBclass($dsn,"mov_inv_real",$mov_real);
		$db_real->xinsertar();
	}
	
	unset($db_virtual);
	unset($db_real);
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
	// Obtener compañías por capturista
	if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia <= 300 OR num_cia IN (702,703)) ORDER BY num_cia";
	else
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (702,703) ORDER BY num_cia";
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
// Verificar si existe la compañía
if (!$cia = obtener_registro("catalogo_companias", array("num_cia"), array($_GET['num_cia']),"","",$dsn)) {
	header("location: ./pan_avi_cap.php?codigo_error=1");
	die();
}

// Obtener ultima fecha de inventario virtual
if ($ultima_fecha = ejecutar_script("SELECT fecha FROM mov_inv_virtual WHERE num_cia=$_GET[num_cia] AND tipo_mov = 'TRUE' ORDER BY fecha DESC LIMIT 1",$dsn)) {
	if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fecha[0]['fecha'],$temp))
	// Si es dia primero de mes, consultar si ya se hicieron las diferencias
	/*if ($temp[1] == date("d",mktime(0,0,0,))) {
		if (!ejecutar_script("SELECT * FROM mov_inv_real WHERE fecha = '".$ultima_fecha[0]['fecha']."' AND descripcion = 'DIFERENCIA INVENTARIO' LIMIT 1",$dsn)) {
			header("location: ./pan_avi_cap.php?codigo_error=2");
			die;
		}
	}*/
	
	$fecha = date("d/m/Y",mktime(0,0,0,$temp[2],$temp[1]+1,$temp[3]));
}
else {
	$fecha = date("d/m/Y",mktime(0,0,0,date("m"),1,date("Y")));
}

// Asignar numero y nombre de compañia, asi como la fecha de captura
$tpl->newBlock("hoja");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$fecha);
$tpl->assign("tabla","mov_inv_virtual");

// Imprimir el resultado
$tpl->printToScreen();
?>