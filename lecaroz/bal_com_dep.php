<?php
// VACIADO DE DEPOSITOS COMETRA
// Tabla 'depositos_cometra'
// Menu 'Balance->Depósitos de COMETRA'

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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "Fecha de captura ya se encuentra en el sistema";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/bal/bal_com_dep.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['MAX_FILE_SIZE'])) {
	$tpl->newBlock("enviar_archivo");
	$tpl->printToScreen();
}
else {
	// Datos del archivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo   = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	
	// Comprobar características del fichero
	if (!(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 1048576)) {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB.");
		$tpl->printToScreen();
	}
	else {
		// Cambiar permisos al directorio
		chmod("/var/www/html/lecaroz/dc",0777);
		// Construir el nombre para el archivo 'dc[año][mes][dia][hora][minutos][segundos].txt'
		$nombre_archivo_server = "/var/www/html/lecaroz/dc/dc".date("YmdHis").".txt";
		// Mover archivo al subdirectorio
		if (move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)) {
			// Cargar depositos a la  base de datos
			$fd = fopen($nombre_archivo_server,"rb");
			$i=0;
			while (!feof($fd)) {
				// Obtener cadena del archivo y almacenarlo en el buffer
				$buffer = fgets($fd,36);

				// Dividir cadena en secciones y almacenarlas en variables
				if ($buffer != "") {
					$datos['num_cia'.$i] = number_format(substr($buffer,0,3),0,"","");
					$datos['fecha'.$i]   = substr($buffer,9,2)."/".substr($buffer,7,2)."/".substr($buffer,3,4);
					$datos['cod'.$i]     = number_format(substr($buffer,11,2),0,"","");
					$datos['importe'.$i] = number_format(substr($buffer,13,18).".".substr($buffer,31,2),2,".","");
					$i++;
				}
			}
			fclose($fd);
			// Almacenar datos en la base
			$db = new DBclass($dsn,"depositos_cometra",$datos);
			$db->xinsertar();
			
			// Desplegar listado de depositos
			$tpl->newBlock("listado");
			
			for ($j=0; $j<$i; $j++) {
				$tpl->newBlock("fila");
				$tpl->assign("num_cia",$datos['num_cia'.$j]);
				$nombre = obtener_registro("catalogo_companias",array("num_cia"),array($datos['num_cia'.$j]),"","",$dsn);
				$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
				$tpl->assign("fecha",$datos['fecha'.$j]);
				$tpl->assign("codigo",$datos['cod'.$j]);
				$tpl->assign("importe",number_format($datos['importe'.$j],2,".",","));
			}
			$tpl->printToScreen();
		}
		else {
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","Ocurrió algún error al subir el fichero. No pudo guardarse");
			$tpl->printToScreen();
		}
		// Cambiar permisos del directorio
		chmod("/var/www/html/lecaroz/dc",0000);
	}
}
?>