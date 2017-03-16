<?php
// CARGA DE ARCHIVO DE INVENTARIO DE FIN DE MES
// Tabla 'inventario_fin_mes'
// Menu 'pendiente'

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
$descripcion_error[1] = "La compañía ";
$descripcion_error[2] = "El archivo ya fue cargado en el sistema";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/bal/bal_inv_arc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['MAX_FILE_SIZE'])) {
	$tpl->newBlock("enviar_archivo");
	
	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		if ($_GET['codigo_error'] == -1)
			$tpl->assign( "message", $descripcion_error[2]);
		else
			$tpl->assign( "message", "La compañía ".$_GET['codigo_error']." no existe en el catalogo de compañías");
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
}
else {
	// Datos del archivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo   = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	$archivo_temp   = $_FILES['userfile']['tmp_name'];
	
	/*if (existe_registro("upload_files",array("hash"),array(md5_file($archivo_temp)),$dsn)) {
		header("location: ./bal_inv_arc.php?codigo_error=-1");
		die;
	}*/
	
	// Comprobar características del fichero Edite Aqui porque le tipo de archivo con Firefox es application/download, Verificar y corregir esto!!
	if (!(stristr($tipo_archivo,"application/download") && $tamano_archivo < 1048576)) {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB. $tipo_archivo y $tamano_archivo");
		$tpl->printToScreen();
	}
	else {
		// Cambiar permisos al directorio
		@chmod("/var/www/html/prueba/inv",0777);
		// Construir el nombre para el archivo 'dc[año][mes][dia][hora][minutos][segundos].txt'
		$nombre_archivo_server = "/var/www/html/lecaroz/inv/inv".date("Ymd").".txt";
		// Mover archivo al subdirectorio
		if (/*@move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)*/1) {
			$fecha = date("d/m/Y",mktime(0,0,0,$_POST['mes']+1,0,$_POST['anio']));
			
			// Cargar inventarios a la  base de datos
			$fd = fopen(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name'],"rb");
			$count=0;
			while (!feof($fd)) {
				// Obtener cadena del archivo y almacenarlo en el buffer
				$buffer = fgets($fd);

				// Dividir cadena en secciones y almacenarlas en variables
				if ($buffer != "") {
					// Datos del archivo
					$datos['num_cia'.$count]       = number_format(substr($buffer,0,3),0,"","");
					$datos['codmp'.$count]         = number_format(substr($buffer,3,8),0,"","");
					$datos['inventario'.$count]    = number_format(substr($buffer,11,21)/10,0,"","");
					// Fecha de 
					$datos['fecha'.$count]         = $fecha;
					// Obtener existencia computada y precios del inventario real
					$sql = "SELECT existencia,precio_unidad FROM inventario_real WHERE num_cia=".$datos['num_cia'.$count]." AND codmp=".$datos['codmp'.$count];
					$existencia = ejecutar_script($sql,$dsn);
					$datos['existencia'.$count]    = ($existencia)?$existencia[0]['existencia']:"0";
					$datos['precio_unidad'.$count] = ($existencia)?$existencia[0]['precio_unidad']:"0";
					// Diferencia
					$datos['diferencia'.$count]    = $datos['existencia'.$count] - $datos['inventario'.$count];
					
					$count++;
				}
			}
			fclose($fd);
			$db = new DBclass($dsn,"inventario_fin_mes",$datos);
			$db->xinsertar();
			
			// Almacenar entrada de archivo
			ejecutar_script("INSERT INTO upload_files (hash) VALUES ('".md5_file(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name'])."')",$dsn);
		}
		else {
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","Ocurrió algún error al subir el fichero. No pudo guardarse");
			$tpl->printToScreen();
		}
		// Cambiar permisos del directorio
		@chmod("/var/www/html/prueba/inv",0000);
	}
	header("location: ./bal_inv_arc.php");
}
?>
