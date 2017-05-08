<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

// Obtener compañía
if (isset($_GET['c'])) {
	$sql = '
		SELECT
			nombre_corto
				AS
					nombre
		FROM
				catalogo_companias
			LEFT JOIN
				catalogo_operadoras
					USING
						(
							idoperadora
						)
		WHERE
				num_cia
					BETWEEN
							1
						AND
							599
			AND
				num_cia = ' . $_GET['c'];
	if (!in_array($_SESSION['iduser'], array(1, 4, 19)))
		$sql .= '
			AND
				iduser = ' . $_SESSION['iduser'] . '
		';
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	
	die;
}

if (isset($_GET['num_cia'])) {
	$condiciones1 = array();
	
	$condiciones1[] = 'num_cia BETWEEN 1 AND 300';
	
	if ($_REQUEST['num_cia'] > 0) {
		$condiciones1[] = 'num_cia = ' . $_REQUEST['num_cia'];
	}
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 19))) {
		$condiciones1[] = 'iduser = ' . $_SESSION['iduser'];
	}
	
	$condiciones2 = $condiciones1;
	
	$condiciones2[] = 'codgastos IN (5, 159, 152)';
	
	$sql = '
		SELECT
			num_cia,
			codgastos,
			descripcion,
			limite,
			1
				AS tipo,
			0
				AS porcentaje
		FROM
			catalogo_limite_gasto
			LEFT JOIN catalogo_gastos
				USING (codgastos)
			LEFT JOIN catalogo_companias
				USING (num_cia)
			LEFT JOIN catalogo_operadoras
				USING (idoperadora)
		WHERE
			' . implode(' AND ', $condiciones1) . '
		
		UNION
		
		SELECT
			num_cia,
			codgastos,
			descripcion,
			0
				AS limite,
			2
				AS tipo,
			CASE
				WHEN codgastos = 152 THEN
					0
				WHEN codgastos = 159 THEN
					10
				WHEN codgastos = 5 THEN
					20
			END
				AS porcentaje
		FROM
			catalogo_companias cc
			LEFT JOIN catalogo_operadoras co
				USING (idoperadora),
			catalogo_gastos cg
		WHERE
			' . implode(' AND ', $condiciones2) . '
		ORDER BY
			num_cia,
			tipo,
			porcentaje,
			codgastos
	';
	
	$result = $db->query($sql);
	
	if (!$result) {
		echo -1;
		die;
	}
	
	// Datos de conexión al server FTP
	$ftp_server = '192.168.1.251';
	$ftp_user = 'lecaroz';
	$ftp_pass = 'leca12345';
	
	// Conectarse al servidor FTP
	if (!($ftp = @ftp_connect($ftp_server))) {
		echo -2;
		die;
	}
	
	// Iniciar sesión en el servidor FTP
	if (!@ftp_login($ftp, $ftp_user, $ftp_pass)) {
		echo -3;
		die;
	}
	
	// Directorio remoto y local
	$rdir = 'recibe';
	$ldir = 'recibe';
	
	// Cambiarse al directorio repositorio de archivos de actualizaciones
	if (!ftp_chdir($ftp, $rdir)) {
		echo -4;
		die;
	}
	
	$num_cia = NULL;
	foreach ($result as $r) {
		if ($num_cia != $r['num_cia']) {
			if ($num_cia != NULL) {
				$data .= '
);

foreach ($limites as $lim) {
	$sql = "DELETE FROM `catgastos` WHERE `num_cia` = $lim[num_cia] AND `Descripcion` = \'$lim[Descripcion]\'";
	$db->query($sql);
	$sql = "INSERT INTO `catgastos` (`Descripcion`, `num_cia`, `maximo`, `Clave`, `porcentaje`, `tipo`) VALUES (\'$lim[Descripcion]\', \'$lim[num_cia]\', \'$lim[maximo]\', \'$lim[Clave]\', \'$lim[porcentaje]\', \'$lim[tipo]\')";
	$db->query($sql);
}

?>';
				
				// Escribir datos de actualización al archivo
				fwrite($fp, $data);
				// Cerrar el archivo
				fclose($fp);
				
				// Enviar el archivo de actualizacion por FTP al servidor
				ftp_put($ftp, "$num_cia.php", "$rdir/$num_cia.php", FTP_BINARY);
			}
			
			$num_cia = $r['num_cia'];
			
			// Crear un archivo nuevo de actualización
			if (!($fp = fopen("$ldir/$num_cia.php", 'wb+'))) {
				echo -5;
				die;
			}
			
			// Cadena de datos
			$data = '<?php';
			
			// Obtener archivo de actualizaciones (si lo hubiera) y borrarlo del servidor
			if (@ftp_fget($ftp, $fp, "$num_cia.php", FTP_BINARY))
				// Borrar archivo del servidor
				ftp_delete($ftp, "$num_cia.php");
			// No hay archivos posteriores, incluir cabecera en el contenido de actualización
			else {
				$data .= '
include \'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\class.db.inc.php\';
include \'C:\\Archivos de programa\\xampp\\htdocs\\LecarozAdmin\\include\\db\\dbstatus.php\';

$db = new DBclass($dsn, \'autocommit=yes\');
$este_archivo = \'recibe/update.php\';
unlink($este_archivo);';
			}
			
			$data .= '
$num_cia = ' . $num_cia . ';
$limites = array(';
			
			$cont = 0;
		}
		$data .= ($cont > 0 ? ',' : '') . "
	array(
		'Descripcion' => '$r[descripcion]',
		'num_cia' => $r[num_cia],
		'maximo' => $r[limite],
		'Clave' => $r[codgastos],
		'porcentaje' => $r[porcentaje],
		'tipo' => $r[tipo]
	)";
		$cont++;
	}
	
	if ($num_cia != NULL) {
		$data .= '
);

foreach ($limites as $lim) {
	$sql = "DELETE FROM `catgastos` WHERE `num_cia` = $lim[num_cia] AND `Descripcion` = \'$lim[Descripcion]\'";
	$db->query($sql);
	$sql = "INSERT INTO `catgastos` (`Descripcion`, `num_cia`, `maximo`, `Clave`, `porcentaje`, `tipo`) VALUES (\'$lim[Descripcion]\', \'$lim[num_cia]\', \'$lim[maximo]\', \'$lim[Clave]\', \'$lim[porcentaje]\', \'$lim[tipo]\')";
	$db->query($sql);
}

?>';
		
		// Escribir datos de actualización al archivo
		fwrite($fp, $data);
		// Cerrar el archivo
		fclose($fp);
		
		// Enviar el archivo de actualizacion por FTP al servidor
		ftp_put($ftp, "$num_cia.php", "$ldir/$num_cia.php", FTP_BINARY);
	}
	
	// Cerrar conexión al servidor FTP
	ftp_close($ftp);
	
	echo 1;
	die;
}

$tpl = new TemplatePower('plantillas/pan/EnviarLimiteGastosPanaderias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>