<?php
// ACTUALIZAR SALDOS
// Tabla 'estado_cuenta'
// Menu 'Banco->Conciliación automática'

//define ('IDSCREEN',1221); // ID de pantalla

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
$descripcion_error[1] = "El archivo ya fue cargado en el sistema";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/actualiza_saldo.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pedir archivo de Banorte
if (!isset($_POST['MAX_FILE_SIZE'])) {
	unset($_SESSION['mov_con']);
	unset($_SESSION['mov_aut']);
	
	$tpl->newBlock("enviar_archivo");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
}
// Analizar archivo, obtener movimientos
else if (isset($_POST['MAX_FILE_SIZE'])) {
	// Datos del archivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo   = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	$archivo_temp   = $_FILES['userfile']['tmp_name'];
	
	// Comprobar características del fichero
	// El archivo debe ser texto plano, no mayor a 5 Megabytes
	if (!(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 5242880)) {
		echo $tipo_archivo." ". $tamano_archivo;
		$tpl->newBlock("message");
		$tpl->assign("message","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 5 MB.");
		$tpl->printToScreen();
		die;
	}
	else {
		// Cambiar permisos al directorio
		@chmod("/var/www/html/lecaroz/mov",0777);
		// Especificar la ruta de almacenamiento y el nombre del archivo
		$nombre_archivo_server = "/var/www/html/lecaroz/mov/$nombre_archivo";
		$_SESSION['filename'] = $nombre_archivo_server;
		// Si ya existe el archivo en el servidor, no sobreescribir
		if (/*!existe_registro("upload_files",array("hash"),array(md5_file($archivo_temp)),$dsn)*/1) {
			// Mover archivo al subdirectorio
			if (@move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)) {
				// Crear cadena hash MD5
				$_SESSION['hash'] = md5_file($nombre_archivo_server);
				
				// Cargar depositos a la  base de datos
				$fd = fopen($nombre_archivo_server,"r");
				$count=0;
				$count_cia=0;
				while (!feof($fd)) {
					// Obtener cadena del archivo y almacenarlo en el buffer
					$buffer = fgets($fd);
	
					// Dividir cadena en secciones y almacenarlas en variables
					if ($buffer != "") {
						$tipo_reg = substr($buffer,0,2);
						switch ($tipo_reg) {
							// Registro de cabecera de cuenta
							case 11:
								$cuenta = substr($buffer,25,10);
								$result = ejecutar_script("SELECT num_cia,nombre FROM catalogo_companias WHERE clabe_cuenta='0$cuenta'",$dsn);
								if ($result) {
									$cia    = $result[0]['num_cia'];
									$nombre = $result[0]['nombre'];
								}
								else {
									$cia    = "0";
									$nombre = substr($buffer,66,26);
								}
							break;
							case 33:
								$saldo_fin['num_cia'] = $cia;
								$saldo_fin['saldo'] = number_format(substr($buffer,74,12).".".substr($buffer,86,2),2,".","");
								
								echo "$cia - $saldo_fin[saldo]<br>";
								
								if (existe_registro("saldo_banorte",array("num_cia"),array($cia),$dsn))
									ejecutar_script("UPDATE saldo_banorte SET saldo=$saldo_fin[saldo] WHERE num_cia=$cia",$dsn);
								else
									ejecutar_script("INSERT INTO saldo_banorte (num_cia,saldo) VALUES ($cia,$saldo_fin[saldo])",$dsn);
							break;
						}
					}
				}
				fclose($fd);
			}
			else {
				$tpl->newBlock("message");
				$tpl->assign("message","Ocurrió algún error al cargar el archivo. No pudo guardarse");
				$tpl->printToScreen();
				die;
			}
			// Cambiar permisos del directorio
			@chmod("/var/www/html/lecaroz/mov",0000);
		}
		// Si ya existe el archivo en el server, mandarlo a la página de resultados
		else {
			// Crear cadena hash MD5
			//header("location: ./ban_con_aut.php?codigo_error=1");
			die;
		}
		
		//header("location: ./ban_con_aut.php?val_cue=1");
		die;
	}
}
?>