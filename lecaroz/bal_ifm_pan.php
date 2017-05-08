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
$tpl->assignInclude("body", "./plantillas/bal/bal_ifm_pan.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha_archivo = date('Y-m-d', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));
	$fecha = date('d/m/Y', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));

	$sql = "SELECT num_cia, '$fecha_archivo' AS fecha, codmp, nombre, cu.descripcion AS unidad FROM inventario_real AS inv LEFT JOIN catalogo_mat_primas AS cm USING (codmp) LEFT JOIN tipo_unidad_consumo AS cu ON (idunidad = unidadconsumo) WHERE num_cia ";
	$sql .= $_GET['num_cia'] > 0 ? "= $_GET[num_cia]" : '<= 300';
	$sql .= " AND ((num_cia, codmp) IN (SELECT num_cia, codmp FROM mov_inv_real WHERE fecha >= cast('$fecha' as date) - interval '2 months' AND num_cia <= 300 GROUP BY num_cia, codmp) OR existencia != 0) ORDER BY num_cia, tipo, cm.nombre";
	$result = $db->query($sql);//echo $sql; die;

	if (!$result)
		die(header('location: ./bal_ifm_pan.php?codigo_error=1'));

	// Datos de conexión al server FTP
	$ftp_server = '192.168.1.250';
	$ftp_user = 'lecaroz';
	$ftp_pass = 'leca12345';

	// Conectarse al servidor FTP
	$ftp = @ftp_connect($ftp_server) or die(header('location: ./bal_ifm_pan.php?codigo_error=2'));

	// Iniciar sesión en el servidor FTP
	if (!@ftp_login($ftp, $ftp_user, $ftp_pass))
		die(header('location: ./bal_ifm_pan.php?codigo_error=3'));

	// Directorio local y remoto
	$rdir = 'recibe';
	$ldir = 'recibe';

	// Cambiarse al directorio repositorio de archivos de actualizaciones
	if (!ftp_chdir($ftp, $rdir))
		die(header('location: ./bal_ifm_pan.php?codigo_error=4'));

	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$data .= ");\n\n";

				$data .= "\$sql = \"DELETE FROM `inv_fin_mes` WHERE 1;\r\n";
				$data .= "\$db->query(\$sql);\r\n\r\n";

				$data .= "foreach (\$avio as \$cod => \$exi) {\r\n";
				$data .= "\t\$sql = \"INSERT INTO `inv_fin_mes` (`num_cia`, `fecha`, `codmp`, `nombre`, `unidad`) VALUES ('\$num_cia', '\$exi[fecha]', '\$cod',";
				$data .= " '\$exi[nombre]', '\$exi[unidad]')\";\r\n";
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
			$fp = fopen("$ldir/$num_cia.php", 'wb+') or die(header('location: ./bal_ifm_pan.php?codigo_error=5'));

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
			$data .= "\$avio = array(\r\n";
			$cont = 0;
		}
		$data .= ($cont > 0 ? ",\r\n" : '') . "$reg[codmp] => array('nombre' => '" . trim($reg['nombre']) . "', 'fecha' => '$reg[fecha]', 'unidad' => '$reg[unidad]')";
		$cont++;
	}
	if ($num_cia != NULL) {
		$data .= ");\n\n";

		$data .= "\$sql = \"DELETE FROM `inv_fin_mes`;\r\n";
		$data .= "\$db->query(\$sql);\r\n\r\n";

		$data .= "foreach (\$avio as \$cod => \$exi) {\r\n";
		$data .= "\t\$sql = \"INSERT INTO `inv_fin_mes` (`num_cia`, `fecha`, `codmp`, `nombre`, `unidad`) VALUES ('\$num_cia', '\$exi[fecha]', '\$cod', '\$exi[nombre]', '\$exi[unidad]')\";\r\n";
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
	die(header('location: ./bal_ifm_pan.php?ok=1'));
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
