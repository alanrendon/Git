<?php
// MODIFICACION DE AVIO
// Tablas varias ''
// Menu 'Panaderías->Producción'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compa&ntilde;&iacute;a no existe en la Base de Datos";
$descripcion_error[2] = "No se han generado las diferencias de fin de mes";

$db = new DBclass($dsn);

// --------------------------------- MODIFCAR DATOS ----------------------------------------------------------
if (isset($_GET['modificar'])) {
	$sql = "";
	
	// Recorrer todos las entradas y buscar cambios
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['entrada'.$i] != $_POST['entrada_ant'.$i]) {
			// Borrar movimiento de inventario virtual e insertar nuevo movimiento
			if ($_POST['entrada_ant'.$i] > 0)
				$sql .= "DELETE FROM \"mov_inv_virtual\" WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp_entrada'.$i]} AND \"fecha\" = '$_POST[fecha]' AND \"tipo_mov\" = 'FALSE';\n";
			// Ordenar datos para insertar en la tabla
			if ($_POST['codmp_entrada'.$i] == 1)
				$existencia = $_POST['entrada'.$i] * 44;
			else
				$existencia = $_POST['entrada'.$i];
			
			$mov['num_cia']       = $_POST['num_cia'];
			$mov['codmp']         = $_POST['codmp_entrada'.$i];
			$mov['fecha']         = $_POST['fecha'];
			$mov['tipo_mov']      = "FALSE";
			$mov['cantidad']      = $existencia;
			$mov['precio']        = $_POST['precio_unidad_entrada'.$i];
			$mov['total_mov']     = $existencia * $_POST['precio_unidad_entrada'.$i];
			$mov['precio_unidad'] = $_POST['precio_unidad_entrada'.$i];
			$mov['descripcion']   = "ENTRADA VIRTUAL DE AVIO";
			if ($existencia > 0)
				$sql .= $db->preparar_insert("mov_inv_virtual",$mov) . ";\n";
			
			// Si la entrada anterior es mayor a 0, restarselo al inventario virtual
			if ($_POST['entrada_ant'.$i] > 0)
				$sql .= "UPDATE inventario_virtual SET \"existencia\" = \"existencia\" - ".($_POST['codmp_entrada'.$i] == 1 ? $_POST['entrada_ant'.$i] / 44 : $_POST['entrada_ant'.$i])." WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp_entrada'.$i]};\n";
			// Si la entrada es mayor a 0, sumarala al inventario virtual
			if ($_POST['entrada'.$i] > 0)
				$sql .= "UPDATE inventario_virtual SET \"existencia\" = \"existencia\" + $existencia WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp_entrada'.$i]};\n";
		}
	}
	
	// Recorrer todas las salidas y buscar cambios
	for ($i=0; $i<$_POST['numelementos']; $i++) {
		if ($_POST['consumo'.$i] != $_POST['consumo_ant'.$i]) {
			// Borrar movimiento de inventario virtual y real e insertar nuevo movimiento
			if ($_POST['consumo_ant'.$i] > 0) {
				$sql .= "DELETE FROM \"mov_inv_virtual\" WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]} AND \"cod_turno\" = {$_POST['cod_turno'.$i]} AND \"fecha\" = '$_POST[fecha]' AND \"tipo_mov\" = 'TRUE';\n";
				$sql .= "DELETE FROM \"mov_inv_real\" WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]} AND \"cod_turno\" = {$_POST['cod_turno'.$i]} AND \"fecha\" = '$_POST[fecha]' AND \"tipo_mov\" = 'TRUE';\n";
			}
			// Ordenar datos para insertar en la tabla
			if ($_POST['codmp'.$i] == 1)
				$consumo = $_POST['consumo'.$i] * 44;
			else
				$consumo = $_POST['consumo'.$i];
			
			// mov_inv_virtual
			$mov['num_cia']       = $_POST['num_cia'];
			$mov['codmp']         = $_POST['codmp'.$i];
			$mov['fecha']         = $_POST['fecha'];
			$mov['cod_turno']     = $_POST['cod_turno'.$i];
			$mov['tipo_mov']      = "TRUE";
			$mov['cantidad']      = $consumo;
			$mov['precio']        = $_POST['precio_unidad'.$i];
			$mov['total_mov']     = $consumo * $_POST['precio_unidad'.$i];
			$mov['precio_unidad'] = $_POST['precio_unidad'.$i];
			$mov['descripcion']   = "SALIDA VIRTUAL DE AVIO";
			if ($consumo > 0)
				$sql .= $db->preparar_insert("mov_inv_virtual",$mov) . ";\n";
			
			// mov_inv_real
			$mov['descripcion']   = "SALIDA DE AVIO";
			if ($consumo > 0)
				$sql .= $db->preparar_insert("mov_inv_real",$mov) . ";\n";
			
			// Si el consumo anterior es mayor a 0, sumarselo al inventario virtual y real
			if ($_POST['consumo_ant'.$i] > 0) {
				$sql .= "UPDATE inventario_virtual SET \"existencia\" = \"existencia\" + ".($_POST['codmp'.$i] == 1 ? $_POST['consumo_ant'.$i] * 44 : $_POST['consumo_ant'.$i])." WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]};\n";
				$sql .= "UPDATE inventario_real SET \"existencia\" = \"existencia\" + ".($_POST['codmp'.$i] == 1 ? $_POST['consumo_ant'.$i] * 44 : $_POST['consumo_ant'.$i])." WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]};\n";
			}
			// Si el consumo es mayor a 0, restarlo al inventario virtual y real
			if ($_POST['consumo'.$i] > 0) {
				$sql .= "UPDATE inventario_virtual SET \"existencia\" = \"existencia\" - $consumo WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]};\n";
				$sql .= "UPDATE inventario_real SET \"existencia\" = \"existencia\" - $consumo WHERE \"num_cia\" = $_POST[num_cia] AND \"codmp\" = {$_POST['codmp'.$i]};\n";
			}
		}
	}
	
	// Generar registro de error
	$sql .= "INSERT INTO registro_errores (iduser,captura,fecha_error,fecha_mod) VALUES ($_SESSION[iduser],'CORRECCION DE AVIO - CIA: $_POST[num_cia]','$_POST[fecha]','".date("d/m/Y")."');\n";
	
	// Ejecutar scripts
	$db->comenzar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	// desconectar de la base de datos
	$db->desconectar();
	// Ir a la página principal
	header("location: ./pan_avi_mod.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Obtener compañías por capturista
	if (!$db->query('
		SELECT
			*
		FROM
			catalogo_operadoras
		WHERE
			iduser = ' . $_SESSION['iduser'] . '
	')) {
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (702,703) ORDER BY num_cia";
	}
	else {
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia <= 300 OR num_cia IN (702,703)) ORDER BY num_cia";
		
		//$tpl->assign('disabled', ' disabled');
		//$tpl->newBlock('leyenda');
	}
		
	$num_cia = $db->query($sql);
	
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
	
	$db->desconectar();
	
	die();
}

// ----------------------------- Generar pantalla de captura ----------------------------------
$fecha = $_GET['fecha'];

// Asignar numero y nombre de compañia, asi como la fecha de captura
$tpl->newBlock("hoja");
$tpl->assign("num_cia",$_GET['num_cia']);
$cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$fecha);
$tpl->assign("tabla","mov_inv_virtual");

// Imprimir el resultado
$tpl->printToScreen();
$db->desconectar();
?>