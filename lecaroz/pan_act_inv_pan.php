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
$tpl->assignInclude("body","./plantillas/pan/pan_act_inv_pan.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (/*isset($_GET['go'])*/isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, codmp, existencia, cast(fecha + interval '1 day' as date) AS fecha, precio_unidad AS costo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING";
	$sql .= " (codmp) WHERE num_cia <= 300 AND fecha = (SELECT fecha FROM historico_inventario WHERE num_cia <= 300 ORDER BY fecha DESC LIMIT 1)";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " AND controlada = 'TRUE' ORDER BY num_cia, codmp";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./pan_act_inv_pan.php?codigo_error=1'));
	
	// Datos de conexión al server FTP
	$ftp_server = '192.168.1.250';
	$ftp_user = 'lecaroz';
	$ftp_pass = 'leca12345';
	
	// Conectarse al servidor FTP
	$ftp = @ftp_connect($ftp_server) or die(header('location: ./pan_act_inv_pan.php?codigo_error=2'));
	
	// Iniciar sesión en el servidor FTP
	if (!@ftp_login($ftp, $ftp_user, $ftp_pass))
		die(header('location: ./pan_act_inv_pan.php?codigo_error=3'));
	
	// Directorio local y remoto
	$rdir = 'recibe';
	$ldir = 'recibe';
	
	// Cambiarse al directorio repositorio de archivos de actualizaciones
	if (!ftp_chdir($ftp, $rdir))
		die(header('location: ./pan_act_inv_pan.php?codigo_error=4'));
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$data .= ");\n\n";
				
				$data .= "\$sql = \"DELETE FROM `inventario` WHERE `num_cia` = '\$num_cia' AND `fecha` = '\$fecha' AND `IdCodMP` < 700\";\r\n";
				$data .= "\$db->query(\$sql);\r\n\r\n";
				
				// [12-Junio-2008] Agregar campo en la base de datos
				$data .= "\$existe = \$db->query(\"SELECT * FROM `inventario` LIMIT 1\");\r\n";
				$data .= "if (!isset(\$existe[0]['costo'])) {\n\r";
				$data .= "\t\$db->query(\"ALTER TABLE `inventario` ADD `costo` DOUBLE NOT NULL\");\r\n";
				$data .= "}\n\r";
				
				$data .= "foreach (\$avio as \$cod => \$exi) {\r\n";
				$data .= "\t\$sql = \"INSERT INTO `inventario` (`num_cia`, `IdCodMP`, `Existencia`, `costo`, `fecha`) VALUES ('\$num_cia', '\$cod',";
				$data .= " '\$exi[existencia]', '\$exi[costo]', '\$fecha')\";\r\n";
				$data .= "\t\$db->query(\$sql);\r\n";
				$data .= "}\n";
				$data .= "?>\n";
				
				// [7-Ago-2008] Código agregado a petición de Mario (¿Qué hace? no tengo ni idea...  ¬¬ )
		/*$data .= '<?php

$servidor_ftp = "lecaroz.homedns.org";

$ftp_nombre_usuario = "lecaroz";

$ftp_contrasenya = "leca12345";



// configurar la conexion basica

$id_con = ftp_connect($servidor_ftp);



// iniciar sesion con nombre de usuario y contrasenya

$resultado_login = ftp_login($id_con, $ftp_nombre_usuario, $ftp_contrasenya);

ftp_pasv($id_con, true);

$archivos=array(\'ReporteAvioFinMes.php\',\'ReporteAvioFinMes.tpl\',\'Reportes.tpl\',\'Reportes.php\',\'ReporteHoja.php\',\'ReporteHoja.tpl\',\'GSQL.php\');

$archivosloc=array(\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\ReporteAvioFinMes.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\ReporteAvioFinMes.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\Reportes.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\Reportes.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\ReporteHoja.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\ReporteHoja.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\GSQL.php\');

for ($g = 0; $g < count($archivos); $g++){

	echo $archivo_local = "recibe/$archivos[$g]";

	echo $archivo_servidor = "recibe/$archivos[$g]";

	if (ftp_get($id_con, $archivo_local, $archivo_servidor, FTP_BINARY)) {

	   echo "Se ha guardado satisfactoriamente en $archivo_local\n";

	   //ftp_delete($id_con, $archivo_servidor);

	   

	} else {

	   $cmd = "cls";

	   system($cmd,$return_value);

	   echo "********************************************************************************\n";

	   echo "Ha ocurrido un problema esn la trasmision del archivo y actualizacion de sistema\n";

	   echo "Favor de comunicarce al 0445524067676 para Avisar\n";

	   echo "Actualizacion Numero 1\n";

	   echo "********************************************************************************\n";  

	   echo "\n";

	   echo "Espere un momento en lo que regresa la configuracion anterior\n";

	   ftp_close($id_con);

	   echo "********************************************************************************\n";

	   echo "***************Porfavor Precione una Tecla para finalizar***********************\n";

	   echo "********************************************************************************\n";

	   $cmd = "pause";

	   system($cmd,$return_value);

	   exit;

	   die;

	}

}

for ($g = 0; $g < count($archivos); $g++){

	$archivo_local = "recibe\\\\" . $archivos[$g];

	$nombre_archivo = "$archivosloc[$g]";

	if (file_exists($nombre_archivo)){

		unlink($nombre_archivo);

		//system(\'copy $archivo_local $nombre_archivo\');

		echo $cmd = "copy $archivo_local $nombre_archivo";

		system($cmd,$return_value);

		unlink($archivo_local);

	}

	else{

		echo $cmd = "copy $archivo_local $nombre_archivo";

		system($cmd,$return_value);

		unlink($archivo_local);

	}

}

?>';*/
				
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
			$fp = fopen("$ldir/$num_cia.php", 'wb+') or die(header('location: ./pan_act_inv_pan.php?codigo_error=5'));
			
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
			
			$data .= '// Inventario de fin de mes, generado el día ' . date('d/m/Y') . "\r\n";
			
			$data .= "\$num_cia = $num_cia;\r\n";
			ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha'], $tmp);
			$fecha = "$tmp[3]-$tmp[2]-$tmp[1]";
			$data .= "\$fecha = '$fecha';\r\n";
			$data .= "\$avio = array(\r\n";
			$cont = 0;
		}
		$data .= ($cont > 0 ? ",\r\n" : '') . "$reg[codmp] => array('existencia' => " . number_format($reg['codmp'] == 1 ? $reg['existencia'] / 44 : $reg['existencia'], 2, '.', '') . ", 'costo' => " . ($reg['costo'] > 0 ? ($reg['codmp'] == 1 ? $reg['costo'] * 44 : $reg['costo']) : 0) . ")";
		$cont++;
	}
	if ($num_cia != NULL) {
		$data .= ");\n\n";
		
		$data .= "\$sql = \"DELETE FROM `inventario` WHERE `num_cia` = '\$num_cia' AND `fecha` = '\$fecha'\";\r\n";
		$data .= "\$db->query(\$sql);\r\n\r\n";
		
		// [12-Junio-2008] Agregar campo en la base de datos
		$data .= "\$existe = \$db->query(\"SELECT * FROM `inventario` LIMIT 1\");\r\n";
		$data .= "if (!isset(\$existe[0]['costo'])) {\n\r";
		$data .= "\t\$db->query(\"ALTER TABLE `inventario` ADD `costo` DOUBLE NOT NULL\");\r\n";
		$data .= "}\n\r";
		
		$data .= "foreach (\$avio as \$cod => \$exi) {\r\n";
		$data .= "\t\$sql = \"INSERT INTO `inventario` (`num_cia`, `IdCodMP`, `Existencia`, `costo`, `fecha`) VALUES ('\$num_cia', '\$cod',";
		$data .= " '\$exi[existencia]', '\$exi[costo]', '\$fecha')\";\r\n";
		$data .= "\t\$db->query(\$sql);\r\n";
		$data .= "}\n";
		$data .= "?>\n";
		
		// [7-Ago-2008] Código agregado a petición de Mario (¿Qué hace? no tengo ni idea...  ¬¬ )
		/*$data .= '<?php

$servidor_ftp = "lecaroz.homedns.org";

$ftp_nombre_usuario = "lecaroz";

$ftp_contrasenya = "leca12345";



// configurar la conexion basica

$id_con = ftp_connect($servidor_ftp);



// iniciar sesion con nombre de usuario y contrasenya

$resultado_login = ftp_login($id_con, $ftp_nombre_usuario, $ftp_contrasenya);

ftp_pasv($id_con, true);

$archivos=array(\'ReporteAvioFinMes.php\',\'ReporteAvioFinMes.tpl\',\'Reportes.tpl\',\'Reportes.php\',\'ReporteHoja.php\',\'ReporteHoja.tpl\',\'GSQL.php\');

$archivosloc=array(\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\ReporteAvioFinMes.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\ReporteAvioFinMes.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\Reportes.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\Reportes.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\ReporteHoja.php\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\tpl\\ReporteHoja.tpl\',

\'c:\\archiv~1\\xampp\\htdocs\\LecarozAdmin\\GSQL.php\');

for ($g = 0; $g < count($archivos); $g++){

	echo $archivo_local = "recibe/$archivos[$g]";

	echo $archivo_servidor = "recibe/$archivos[$g]";

	if (ftp_get($id_con, $archivo_local, $archivo_servidor, FTP_BINARY)) {

	   echo "Se ha guardado satisfactoriamente en $archivo_local\n";

	   //ftp_delete($id_con, $archivo_servidor);

	   

	} else {

	   $cmd = "cls";

	   system($cmd,$return_value);

	   echo "********************************************************************************\n";

	   echo "Ha ocurrido un problema esn la trasmision del archivo y actualizacion de sistema\n";

	   echo "Favor de comunicarce al 0445524067676 para Avisar\n";

	   echo "Actualizacion Numero 1\n";

	   echo "********************************************************************************\n";  

	   echo "\n";

	   echo "Espere un momento en lo que regresa la configuracion anterior\n";

	   ftp_close($id_con);

	   echo "********************************************************************************\n";

	   echo "***************Porfavor Precione una Tecla para finalizar***********************\n";

	   echo "********************************************************************************\n";

	   $cmd = "pause";

	   system($cmd,$return_value);

	   exit;

	   die;

	}

}

for ($g = 0; $g < count($archivos); $g++){

	$archivo_local = "recibe\\\\" . $archivos[$g];

	$nombre_archivo = "$archivosloc[$g]";

	if (file_exists($nombre_archivo)){

		unlink($nombre_archivo);

		//system(\'copy $archivo_local $nombre_archivo\');

		echo $cmd = "copy $archivo_local $nombre_archivo";

		system($cmd,$return_value);

		unlink($archivo_local);

	}

	else{

		echo $cmd = "copy $archivo_local $nombre_archivo";

		system($cmd,$return_value);

		unlink($archivo_local);

	}

}

?>';*/
		
		// Escribir datos de actualización al archivo
		fwrite($fp, $data);
		// Cerrar el archivo
		fclose($fp);
		
		// Enviar el archivo de actualizacion por FTP al servidor
		ftp_put($ftp, "$num_cia.php", "$rdir/$num_cia.php", FTP_BINARY);
	}
	
	// Cerrar conexión al servidor FTP
	ftp_close($ftp);
	die(header('location: ./pan_act_inv_pan.php?ok=1'));
}

$tpl->newBlock('datos');

if (isset($_GET['ok']))	$tpl->assign('onload', 'window.onload = function() { alert("SE GENERARON ARCHIVOS DE ACTUALIZACION CON EXITO"); f.num_cia.select(); }');
else $tpl->assign('onload', 'window.onload = f.num_cia.select();');

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