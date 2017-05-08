<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

if (isset($_REQUEST['accion']) && $_REQUEST['accion'] != '') {
	
	switch ($_REQUEST['accion']) {
		
		case 'enviar':
			
			$sql = '
				SELECT
					nombre,
					ap_paterno,
					ap_materno,
					/*CASE
						WHEN TRIM(observaciones) = \'\' THEN
							nombre_tipo_baja
						ELSE
							TRIM(\'[\' || nombre_tipo_baja || \'] \' || observaciones)
					END*/
					num
						AS observaciones
				FROM
					lista_negra_trabajadores
					LEFT JOIN catalogo_tipos_baja
						USING (idtipobaja)
				WHERE
					tsdel IS NULL
				ORDER BY
					ap_paterno,
					ap_materno,
					nombre
			';
			
			$lista_negra = $db->query($sql);
			
			// Datos de conexión al server FTP
			$ftp_server = '192.168.1.250';
			$ftp_user   = 'lecaroz';
			$ftp_pass   = 'leca12345';
			
			// Directorio remoto
			$rdir = 'recibe/';
			
			// Directorio local
			$ldir = 'recibe/';
			
			// Conectarse al servidor FTP
			$ftp = @ftp_connect($ftp_server);
			
			if (!$lista_negra) {
				echo -1;
			} else if (!$ftp) {
				echo -2;
			}
			else if (!@ftp_login($ftp, $ftp_user, $ftp_pass)) {
				echo -3;
			}
			else if (!@ftp_chdir($ftp, $rdir)) {
				echo -4;
			}
			else {
				$sql = '
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
						num_cia <= 300
					ORDER BY
						num_cia
				';
				
				$cias = $db->query($sql);
				
				if (!$cias) {
					echo -5;
				}
				else {
					$lista = array();
					
					// Preparar lista de trabajadores boletinados
					foreach ($lista_negra as $rec) {
						$lista[] = "array('nombre'=>'$rec[nombre]','ap_paterno'=>'$rec[ap_paterno]','ap_materno'=>'$rec[ap_materno]','observaciones'=>'$rec[observaciones]')";
					}
					
					foreach ($cias as $cia) {
						// Crear un archivo nuevo de actualización
						$fp = fopen($ldir . $cia['num_cia'] . '.php', 'wb+');
						
						if (!$fp) {
							echo -6;
						}
						
						$data = '
<?php
/* ' . date('Y/m/d H:i:s') . ' */
						';
						
						// Obtener archivo de actualizaciones (si lo hubiera) y borrarlo del servidor
						if (@ftp_fget($ftp, $fp, $cia['num_cia'] . '.php', FTP_BINARY)) {
							// Borrar archivo del servidor
							ftp_delete($ftp, $cia['num_cia'] . '.php');
						}
						else {
							$data .= '
//include $_SERVER[\'DOCUMENT_ROOT\'] . \'\LecarozAdmin\include\db\class.db.inc.php\';
//include $_SERVER[\'DOCUMENT_ROOT\'] . \'\LecarozAdmin\include\db\dbstatus.php\';
include \'C:\Archivos de programa\xampp\htdocs\LecarozAdmin\include\db\class.db.inc.php\';
include \'C:\Archivos de programa\xampp\htdocs\LecarozAdmin\include\db\dbstatus.php\';
							';
						}
						
						$data .= '
$db = new DBclass($dsn, \'autocommit=yes\');
$este_archivo = \'recibe/update.php\';
unlink($este_archivo);

$num_cia = ' . $cia['num_cia'] . ';

$db->query("DELETE FROM `lista_negra_trabajadores`");

$lista_negra = array(' . implode(',', $lista) . ');

foreach ($lista_negra as $rec) {
	$sql = "INSERT INTO `lista_negra_trabajadores` (`nombre`, `ap_paterno`, `ap_materno`, `observaciones`) VALUES (\'$rec[nombre]\', \'$rec[ap_paterno]\', \'$rec[ap_materno]\', \'$rec[observaciones]\')";
	$db->query($sql);
}

?>
						';
						
						// Escribir datos de actualización al archivo
						fwrite($fp, $data);
						
						// Cerrar el archivo
						fclose($fp);
						
						// Enviar el archivo de actualizacion por FTP al servidor
						ftp_put($ftp, $cia['num_cia'] . '.php', $ldir . $cia['num_cia'] . '.php', FTP_BINARY);
					}
				}
				
				ftp_close($ftp);
			}
			
		break;
		
	}
	
	die;
	
}

$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		tsins::date
			AS
				fecha,
		nombre,
		ap_paterno,
		ap_materno,
		observaciones,
		num
			AS num_tipo,
		nombre_tipo_baja
			AS tipo_baja,
		permite_reingreso
	FROM
		lista_negra_trabajadores
		LEFT JOIN catalogo_tipos_baja
			USING (idtipobaja)
	WHERE
		tsdel IS NULL
	ORDER BY
		ap_paterno,
		ap_materno,
		nombre
';
$result = $db->query($sql);

$tpl->assign('titulo', in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58, 59, 61)) ? 'Trabajadores dados de baja' : 'Lista Negra de Trabajadores');

$tpl->assign('dir', !in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58, 59, 61)) ? 'left' : 'right');
$tpl->assign('display', !in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58, 59, 61)) ? 'show' : 'hide');

if ($result) {
	$color = FALSE;
	foreach ($result as $r) {
		$tpl->newBlock('row');
		$tpl->assign('color', $color ? 'on' : 'off');
		$tpl->assign('fecha', $r['fecha']);
		$tpl->assign('nombre', $r['nombre']);
		$tpl->assign('ap_paterno', $r['ap_paterno']);
		$tpl->assign('ap_materno', $r['ap_materno']);
		$tpl->assign('tipo_baja', $r['tipo_baja'] != '' ? $r['num_tipo'] . (!in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58, 59, 61)) ? ' ' . $r['tipo_baja'] : '') : '&nbsp;');
		$tpl->assign('display', !in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58, 59, 61)) ? 'show' : 'hide');
		$tpl->assign('tipo_color', $r['permite_reingreso'] == 't' ? 'green' : 'red');
		$tpl->assign('observaciones', $r['observaciones']);
		
		$color = !$color;
	}
}

$tpl->printToScreen();
?>
