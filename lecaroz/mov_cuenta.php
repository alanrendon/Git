<?php
// VACIADO DE DEPOSITOS COMETRA
// Tabla 'depositos_cometra'
// Menu 'Banco->Depósitos de COMETRA'

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
$tpl->assignInclude("body","./plantillas/ban/mov_cuenta.tpl");
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
	if (!(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 5242880)) {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB.");
		$tpl->printToScreen();
	}
	else {
		// Cambiar permisos al directorio
		chmod("/var/www/html/prueba/mov",0777);
		// Construir el nombre para el archivo 'dc[año][mes][dia][hora][minutos][segundos].txt'
		$nombre_archivo_server = "/var/www/html/lecaroz/mov/mov".date("Ymd").".txt";
		// Mover archivo al subdirectorio
		if (move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)) {
			// Cargar depositos a la  base de datos
			$fd = fopen($nombre_archivo_server,"r");
			$count=0;
			while (!feof($fd)) {
				// Obtener cadena del archivo y almacenarlo en el buffer
				$buffer = fgets($fd);//echo $buffer."<br>";

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
						// Registro principal de movimientos
						case 22:
							$mov['num_cia'.$count]       = $cia;
							$mov['nombre'.$count]        = $nombre;
							$mov['cuenta'.$count]        = $cuenta;
							$mov['fecha'.$count]         = substr($buffer,14,2)."/".substr($buffer,12,2)."/".substr($buffer,10,2);
							$mov['tipo_mov'.$count]      = (substr($buffer,27,1) == 1)?"FALSE":"TRUE";
							$mov['importe'.$count]       = number_format(substr($buffer,28,12).".".substr($buffer,40,2),2,".","");
							$mov['num_documento'.$count] = (substr($buffer,42,10) > 0)?substr($buffer,42,10):"";
							$mov['concepto'.$count]      = substr($buffer,52,12).substr($buffer,64,16);
							$mov['cod_mov'.$count]       = (substr($buffer,23,4) > 0)?substr($buffer,23,4):"";
							$mov['conciliado'.$count]    = "FALSE";
							$count++;
						break;
						// Registro final de cuenta
						/*case 33:
							ereg("33########([0-9]{3})@([0-9]{4})#######([0-9]{10})([0-9]{5})([0-9]{12})([0-9]{2})([0-9]{5})([0-9]{12})([0-9]{2})([1-2]{1})([0-9]{12})([0-9]{2})([0-9]{3})    ",$buffer,$reg);
						break;*/
					}
				}
			}
			fclose($fd);
			
			// Almacenar movimientos en mov_banorte
			$db = new DBclass($dsn,"mov_banorte",$mov);
			$db->xinsertar();
			
			$tpl->newBlock("listado");
			for ($i=0; $i<$count; $i++) {
				$tpl->newBlock("fila");
				$tpl->assign("num_cia",($mov['num_cia'.$i] > 0)?$mov['num_cia'.$i]:"&nbsp;");
				$tpl->assign("nombre",$mov['nombre'.$i]);
				$tpl->assign("cuenta",$mov['cuenta'.$i]);
				$tpl->assign("fecha",$mov['fecha'.$i]);
				$tpl->assign("tipo_mov",($mov['tipo_mov'.$i] == "TRUE")?"DEPOSITO":"CHEQUE");
				$tpl->assign("importe",number_format($mov['importe'.$i],2,".",","));
				$tpl->assign("num_documento",($mov['num_documento'.$i] > 0)?$mov['num_documento'.$i]:"&nbsp;");
				$tpl->assign("concepto",$mov['concepto'.$i]);
				$tpl->assign("cod_mov",$mov['cod_mov'.$i]);
			}
			$tpl->printToScreen();
		}
		else {
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","Ocurrió algún error al subir el fichero. No pudo guardarse");
			$tpl->printToScreen();
		}
		// Cambiar permisos del directorio
		chmod("/var/www/html/prueba/mov",0000);
	}
}
?>