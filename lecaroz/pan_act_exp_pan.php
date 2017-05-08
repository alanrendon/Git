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
$descripcion_error[2] = "No ha sido posible conectarse al servidor FTP";
$descripcion_error[3] = "No ha sido posible conectarse con el usuario y contraseña dados";
$descripcion_error[4] = "No se ha podido acceder al directorio repositorio de archivos de actualización";
$descripcion_error[5] = "No se ha podido crear el archivo temporal de actualización";

$admin_users = array(1, 28);

// Conectarse a la base de datos
$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_act_exp_pan.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha_archivo = date('Y-m-d', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));
	$fecha = date('d/m/Y', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));
	
	// Obtener expendios
	$sql = "SELECT num_cia, num_referencia, ce.nombre, ce.direccion, tipo_expendio, porciento_ganancia, importe_fijo, aut_dev, devolucion_maxima, tipo_devolucion FROM catalogo_expendios ce LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_operadoras USING (idoperadora) ";
	$sql .= $_SESSION['iduser'] != 1 || $_GET['num_cia'] > 0 ? ' WHERE ' : '';
	$sql .= $_SESSION['iduser'] != 1 ? " iduser = $_SESSION[iduser]" : '';
	$sql .= $_GET['num_cia'] > 0 ? ($_SESSION['iduser'] != 1 ? ' AND' : '') . " num_cia = $_GET[num_cia]" : '';
	//$sql .= " num_cia NOT IN (27, 70)";
	$sql .= " ORDER BY num_cia, num_referencia";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./pan_act_exp_pan.php?codigo_error=1'));
	
	// Datos de conexión al server FTP
	$ftp_server = '192.168.1.250';
	$ftp_user = 'lecaroz';
	$ftp_pass = 'leca12345';
	
	// Conectarse al servidor FTP
	$ftp = @ftp_connect($ftp_server) or die(header('location: ./pan_act_exp_pan.php?codigo_error=2'));
	
	// Iniciar sesión en el servidor FTP
	if (!@ftp_login($ftp, $ftp_user, $ftp_pass))
		die(header('location: ./pan_act_exp_pan.php?codigo_error=3'));
	
	// Directorio local y remoto
	$rdir = 'recibe';
	$ldir = 'recibe';
	
	// Cambiarse al directorio repositorio de archivos de actualizaciones
	if (!ftp_chdir($ftp, $rdir))
		die(header('location: ./pan_act_exp_pan.php?codigo_error=4'));
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$data .= ");\n\n";
				
				$data .= "\$existe = \$db->query(\"SELECT * FROM `catexpendios` LIMIT 1\");\r\n";
				$data .= "if (!isset(\$existe[0]['tipo_devolucion'])) {\n\r";
				$data .= "\t\$db->query(\"ALTER TABLE `catexpendios` ADD `tipo_devolucion` INT NOT NULL;\");\r\n";
				$data .= "\t\$db->query(\"ALTER TABLE `catexpendios` ADD `devolucion_maxima` DOUBLE NOT NULL;\");\r\n";
				$data .= "}\n\r";
				
				$data .= "\$sql = \"DELETE FROM `catexpendios` WHERE `num_cia` = $num_cia\";\r\n";
				//$data .= "\$sql = \"TRUNCATE TABLE `catexpendios`\";\r\n";
				$data .= "\$db->query(\$sql);\r\n\r\n";
				
				$data .= "foreach (\$exp as \$i => \$e) {\r\n";
				$data .= "\t\$sql = \"INSERT INTO `catexpendios` (`IdExpendio`, `Nombre`, `Direccion`, `NumExpendio`, `TipoExpendio`, `PorGanancia`, `ImporteFijo`, `Status`, `num_cia`, `devol`, `tipo_devolucion`, `devolucion_maxima`) VALUES ('\$e[IdExpendio]', '\$e[Nombre]', '\$e[Direccion]', '\$e[NumExpendio]', '\$e[TipoExpendio]', \" . (\$e['ImporteFijo'] > 0 ? 'NULL' : \"'\$e[PorGanancia]'\") . \", \" . (\$e['PorGanancia'] > 0 ? 'NULL' : \"'\$e[ImporteFijo]'\") . \", '\$e[Status]', '\$e[num_cia]', '\$e[devol]', '\$e[tipo_devolucion]', '\$e[devolucion_maxima]')\";\r\n";
				$data .= "\t\$db->query(\$sql);\r\n";
				$data .= "}\n";
				
				$data .= "?>\n";
				
				// Escribir datos de actualización al archivo
				fwrite($fp, $data);
				// Cerrar el archivo
				fclose($fp);
				
				// Enviar el archivo de actualizacion por FTP al servidor
				ftp_put($ftp, "$num_cia.php", "$rdir/$num_cia.php", FTP_BINARY);
			}
			
			// Cambia de compañía
			$num_cia = $reg['num_cia'];
			
			// Crear un archivo nuevo de actualización
			$fp = fopen("$ldir/$num_cia.php", 'wb+') or die(header('location: ./pan_act_exp_pan.php?codigo_error=5'));
			
			// Contenedor de datos de actualización
			$data = "<?php\r\n";
			
			// Obtener archivo de actualizaciones (si lo hubiera) y borrarlo del servidor
			if (@ftp_fget($ftp, $fp, "$num_cia.php", FTP_BINARY))
				// Borrar archivo del servidor
				ftp_delete($ftp, "$num_cia.php");
			// No hay archivos posteriores, incluir cabecera en el contenido de actualización
			else {
				$data .= "include 'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\class.db.inc.php';\r\n";
				$data .= "include 'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\dbstatus.php';\r\n\r\n";
				$data .= "\$db = new DBclass(\$dsn, \"autocommit=yes\");\r\n\r\n";
				$data .= "\$este_archivo = 'recibe/update.php';\r\n";
				$data .= "unlink(\$este_archivo);\r\n\r\n";
			}
			
			$data .= "\$num_cia = $num_cia;\r\n";
			
			$data .= "\$exp = array(\r\n";
			$cont = 0;
		}
		$data .= ($cont > 0 ? ",\r\n" : '') . "array('IdExpendio' => '" . ($reg['num_referencia'] > 0 ? $reg['num_referencia'] : '') . "', 'Nombre' => '$reg[nombre]', 'Direccion' => '$reg[direccion]', 'NumExpendio' => '" . ($reg['num_referencia'] > 0 ? $reg['num_referencia'] : '') . "', 'TipoExpendio' => 'tipo_expendio', 'PorGanancia' => '$reg[porciento_ganancia]', 'ImporteFijo' => '$reg[importe_fijo]', 'Status' => '1', 'num_cia' => '$reg[num_cia]', 'devol' => '" . ($reg['aut_dev'] == 't' ? '0' : '1') . "', 'devolucion_maxima' => " . get_val($reg['devolucion_maxima']) . ", 'tipo_devolucion' => " . get_val($reg['tipo_devolucion']) . ")";
		
		$cont++;
	}
	if ($num_cia != NULL) {
		$data .= ");\n\n";
		
		$data .= "\$existe = \$db->query(\"SELECT * FROM `catexpendios` LIMIT 1\");\r\n";
		$data .= "if (!isset(\$existe[0]['tipo_devolucion'])) {\n\r";
		$data .= "\t\$db->query(\"ALTER TABLE `catexpendios` ADD `tipo_devolucion` INT NOT NULL;\");\r\n";
		$data .= "\t\$db->query(\"ALTER TABLE `catexpendios` ADD `devolucion_maxima` DOUBLE NOT NULL;\");\r\n";
		$data .= "}\n\r";
		
		$data .= "\$sql = \"DELETE FROM `catexpendios` WHERE `num_cia` = $num_cia\";\r\n";
		//$data .= "\$sql = \"TRUNCATE TABLE `catexpendios`\";\r\n";
		$data .= "\$db->query(\$sql);\r\n\r\n";
		
		$data .= "foreach (\$exp as \$i => \$e) {\r\n";
		$data .= "\t\$sql = \"INSERT INTO `catexpendios` (`IdExpendio`, `Nombre`, `Direccion`, `NumExpendio`, `TipoExpendio`, `PorGanancia`, `ImporteFijo`, `Status`, `num_cia`, `devol`, `tipo_devolucion`, `devolucion_maxima`) VALUES ('\$e[IdExpendio]', '\$e[Nombre]', '\$e[Direccion]', '\$e[NumExpendio]', '\$e[TipoExpendio]', \" . (\$e['ImporteFijo'] > 0 ? 'NULL' : \"'\$e[PorGanancia]'\") . \", \" . (\$e['PorGanancia'] > 0 ? 'NULL' : \"'\$e[ImporteFijo]'\") . \", '\$e[Status]', '\$e[num_cia]', '\$e[devol]', '\$e[tipo_devolucion]', '\$e[devolucion_maxima]')\";\r\n";
		$data .= "\t\$db->query(\$sql);\r\n";
		$data .= "}\n";
		
		$data .= "?>\n";
		
		// Escribir datos de actualización al archivo
		fwrite($fp, $data);
		// Cerrar el archivo
		fclose($fp);
		
		// Enviar el archivo de actualizacion por FTP al servidor
		ftp_put($ftp, "$num_cia.php", "$rdir/$num_cia.php", FTP_BINARY);
	}
	
	// Cerrar conexión al servidor FTP
	ftp_close($ftp);
	die(header('location: ./pan_act_exp_pan.php?ok=1'));
}

if (isset($_GET['ok']))	$tpl->assign('onload', 'window.onload = alert("SE GENERARON ARCHIVOS DE ACTUALIZACION CON EXITO");');
else $tpl->assign('onload', 'window.onload = f.num_cia.select();');

$result = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia <= 300");
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
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
