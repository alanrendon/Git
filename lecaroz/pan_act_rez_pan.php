<?php
// COMPARATIVO DE GAS MENSUAL
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "La factura ha sido autorizada y no tiene privilegios para modificarla";
$descripcion_error[3] = "La factura ya ha sido pagada y no es posible modificarla";

$admin_users = array(1, 28);

// Conectarse a la base de datos
$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_act_rez_pan.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	// Validar que no haya archivos pendientes por validar
	if ($db->query("SELECT id FROM efectivos_tmp WHERE num_cia = $_GET[num_cia] AND ts_aut IS NULL LIMIT 1")) {
		$tpl->newBlock('result');
		$mensaje = "No se pueden corregir rezagos debido a que no se han validado todos los archivos de la panadería";
		$tpl->assign('mensaje', $mensaje);
		$tpl->printToScreen();
		die;
	}
	
	$tmp = $db->query("SELECT fecha FROM efectivos_tmp WHERE num_cia = $_GET[num_cia] ORDER BY fecha DESC LIMIT 1");
	$fecha = $tmp[0]['fecha'];
	
	// Buscar el último día de rezagos para la panadería seleccionada
	$sql = "SELECT tmp.num_expendio, exp.rezago AS rezago_exp, tmp.rezago AS rezago_tmp FROM mov_expendios AS exp LEFT JOIN";
	$sql .= " mov_exp_tmp AS tmp ON (tmp.num_cia = exp.num_cia AND tmp.fecha = exp.fecha AND tmp.nombre_expendio =";
	$sql .= " exp.nombre_expendio) WHERE exp.num_cia = $_GET[num_cia] AND exp.fecha = '$fecha' AND tmp.num_expendio > 0 AND";
	$sql .= " exp.rezago <> tmp.rezago ORDER BY tmp.num_expendio";
	$result = $db->query($sql);
	
	// Si no hay diferencias, terminar operación
	if (!$result) {
		$tpl->newBlock('result');
		$mensaje = "La panadería seleccionada no tiene diferencia de rezagos";
		$tpl->assign('mensaje', $mensaje);
		$tpl->printToScreen();
		die;
	}
	
	$dir = 'pcl/';
	$filename = $_GET['num_cia'] . '.php';
	
	// Crear script PHP para el archivo de actualización
	$data = "<?php\n";
	// Encabezado
	if (!file_exists($dir . $filename)) {
		$data .= "include 'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\class.db.inc.php';\n";
		$data .= "include 'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\dbstatus.php';\n\n";
		$data .= "\$db = new DBclass(\$dsn, \"autocommit=yes\");\n\n";
		$data .= "\$este_archivo = 'recibe/update.php';\n";
		$data .= "unlink(\$este_archivo);\n\n";
	}
	
	// Diferencias de expendios
	$data .= "\$num_cia = $_GET[num_cia];\n";
	$data .= "\$exp = array(";
	foreach ($result as $i => $reg)
		$data .= $reg['num_expendio'] . ($i < count($result) - 1 ? ', ' : ");\n");
	$data .= "\$dif = array(";
	foreach ($result as $i => $reg) {
		$dif = $reg['rezago_exp'] - $reg['rezago_tmp'];
		$data .= $dif . ($i < count($result) - 1 ? ', ' : ");\n");
	}
	
	$data .= "\$status = \$db->query(\"SELECT `Fecha` FROM `statuscapturas` WHERE `StatusCaptura` = 0 AND `num_cia` = \$num_cia\");\n";
	$data .= "for (\$i = 0; \$i < count(\$exp); \$i++) {\n";
	$data .= "\t\$catexp = \$db->query(\"SELECT `IdExpendio` FROM `catexpendios` WHERE `IdExpendio` = '\$exp[\$i]' AND `num_cia` = \$num_cia\");\n";
	$data .= "\t\$exp[\$i] = \$catexp[0]['IdExpendio'];\n";
	$data .= "}\n";
	$data .= "for (\$i = 0; \$i < count(\$exp); \$i++) {\n";
	$data .= "\t\$rezago = \$db->query(\"SELECT * FROM `movexpendios` WHERE `IdExpendio` = '\$exp[\$i]' AND `Fecha` = '{\$status[0]['Fecha']}' AND `num_cia` = \$num_cia\");\n";
	$data .= "\t\$rezagoini= \$rezago[0]['RezagoInicial'] + \$dif[\$i];\n";
	$data .= "\t\$rezagofin= \$rezago[0]['RezagoFinal'] + \$dif[\$i];\n";
	$data .= "\t\$sql=\"UPDATE `movexpendios` SET `RezagoInicial` = \$rezagoini,`RezagoFinal` = \$rezagofin WHERE `IdMovExpendios` = {\$rezago[0]['IdMovExpendios']} AND `num_cia` = \$num_cia\";\n";
	$data .= "\t\$db->query(\$sql);\n";
	$data .= "}\n";
	$data .= "?>\n";
	
	shell_exec("chmod 777 pcl");
	
	// Crear apuntador al archivo de actualizaciones
	$fp = fopen($dir . $filename, 'a+');
	
	// Escribir datos en el archivo
	fwrite($fp, $data);
	
	// Cerrar archivo
	fclose($fp);
	
	shell_exec("chmod 755 pcl");
	
	$tpl->newBlock('result');
	$mensaje = "Se han generado los datos de actualización";
	$tpl->assign('mensaje', $mensaje);
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias";
if (!in_array($_SESSION['iduser'], array(1, 4, 19)))
	$sql .= " LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser]";
$sql .= " ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre', $cia['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>