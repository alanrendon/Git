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

if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

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
$tpl->assignInclude("body","./plantillas/ban/ban_com_dep.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['MAX_FILE_SIZE'])) {
	$tpl->newBlock("enviar_archivo");
	
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

	if (existe_registro("upload_files",array("hash"),array(md5_file($archivo_temp)),$dsn)) {
		header("location: ./ban_com_dep.php?codigo_error=-1");
		die;
	}

	// Comprobar características del fichero
	if (!(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 1048576)) {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB.");
		$tpl->printToScreen();
	}
	else {
		// Cambiar permisos al directorio
		@chmod("/var/www/html/prueba/dc",0777);
		// Construir el nombre para el archivo 'dc[año][mes][dia][hora][minutos][segundos].txt'
		$nombre_archivo_server = "/var/www/html/lecaroz/dc/dc".date("Ymd").".txt";
		// Mover archivo al subdirectorio
		if (/*@move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)*/1) {
			// Cargar depositos a la  base de datos
			$fd = fopen(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name'],"rb");
			$count=0;
			while (!feof($fd)) {
				// Obtener cadena del archivo y almacenarlo en el buffer (MODIFICADO 06/09/2005. EL TAMAÑO DE LA CADENA CAMBIO DE 36 A 46)
				$buffer = fgets($fd,46);

				// Dividir cadena en secciones y almacenarlas en variables
				if ($buffer != "") {
					$temp_cia = number_format(substr($buffer,0,3),0,"","");
					if ($temp_cia < 900) {
					// Si la compañía es 140 o 146, cambiar a 147, si es 171, cambiar a 170, de lo contrario sera la compañía obtenida del archivo
					$datos['num_cia'.$count]   = ($temp_cia == 140 || $temp_cia == 146)?147:($temp_cia == 171)?170:$temp_cia;
					$datos['fecha_mov'.$count] = substr($buffer,9,2)."/".substr($buffer,7,2)."/".substr($buffer,3,4);
					$datos['fecha_con'.$count] = "";
					$temp_cod = number_format(substr($buffer,11,2),0,"","");
					$temp_cod = $temp_cod > 0 ? $temp_cod : 1;
					//$datos['cod_mov'.$count]   = (($datos['num_cia'.$count] > 100 && $datos['num_cia'.$count] < 200) || $datos['num_cia'.$count] == 702 || $datos['num_cia'.$count] == 703 || $datos['num_cia'.$count] == 704)?16:$temp_cod;
					$datos['importe'.$count]   = number_format(str_replace("-", "", substr($buffer,13,18)).".".substr($buffer,31,2),2,".","");
					$datos['tipo_mov'.$count]  = strpos(substr($buffer,13,18),"-") !== FALSE ? "TRUE" : "FALSE";
					//$datos['cod_mov'.$count]   = $datos['tipo_mov'.$count] == "TRUE" ? 19 : $temp_cod;
					$datos['cod_mov'.$count]   = $temp_cod;
					//$datos['concepto'.$count]  = $datos['cod_mov'.$count] != 19 ? "DEPOSITO COMETRA" : "FAL REP CAJA";
					$datos['concepto'.$count]  = $datos['cod_mov'.$count] != 19 && $datos['cod_mov'.$count] != 13 ? "DEPOSITO COMETRA" : ($datos['cod_mov'.$count] == 13 ? "SOBRANTE CAJA GENERAL" : "FAL REP CAJA");
					$count++;
					}
				}
			}
			fclose($fd);
			
			// Revisar si existen todas las compañías contenidas en el archivo
			for ($i=0; $i<$count; $i++)
				if (!existe_registro("catalogo_companias",array("num_cia"),array($datos['num_cia'.$i]),$dsn)) {
					header("location: ./ban_com_dep.php?codigo_error=".$datos['num_cia'.$i]);
					die;
				}
			
			// Revisar y descartar repetidos
			$index = 0;
			$index2 = 0;
			$index3 = 0;
			for ($i=0; $i<$count; $i++) {
				$rep = 0; // Variable contador para variables repetidas
				for ($j=$i; $j<$count; $j++)
					if ($datos['num_cia'.$i] == $datos['num_cia'.$j] && $datos['fecha_mov'.$i] == $datos['fecha_mov'.$j] && $datos['cod_mov'.$i] == $datos['cod_mov'.$j] && $datos['importe'.$i] == $datos['importe'.$j])
						$rep++;
				if ($rep == 1 && $datos['importe'.$i] > 0) {
					if ($datos['cod_mov'.$i] != 19) {
						$dep['num_cia'.$index] = $datos['num_cia'.$i];
						$dep['fecha_mov'.$index] = $datos['fecha_mov'.$i];
						$dep['cod_mov'.$index] = $datos['cod_mov'.$i];
						$dep['importe'.$index] = $datos['importe'.$i];
						$dep['concepto'.$index] = $datos['concepto'.$i];
						$dep['fecha_cap'.$index] = date("d/m/Y");
						$dep['manual'.$index] = "FALSE";
						$dep['imprimir'.$index] = "TRUE";
						$dep['ficha'.$index] = "FALSE";
						$dep['cuenta'.$index] = $_POST['cuenta'];
						$index++;
					}
					else {
						$ret['num_cia'.$index2] = $datos['num_cia'.$i];
						$ret['fecha_mov'.$index2] = $datos['fecha_mov'.$i];
						$ret['cod_mov'.$index2] = $datos['cod_mov'.$i];
						$ret['importe'.$index2] = $datos['importe'.$i];
						$ret['concepto'.$index2] = $datos['concepto'.$i];
						$ret['fecha_cap'.$index2] = date("d/m/Y");
						$ret['manual'.$index2] = "FALSE";
						$ret['imprimir'.$index2] = "TRUE";
						$ret['cuenta'.$index2] = $_POST['cuenta'];
						$index2++;
					}
					
					$est['num_cia'.$index3]   = $datos['num_cia'.$i];
					$est['fecha'.$index3]     = $datos['fecha_mov'.$i];
					$est['fecha_con'.$index3] = "";
					$est['concepto'.$index3]  = $datos['cod_mov'.$i] != 19 && $datos['cod_mov'.$i] != 13 ? "DEPOSITO COMETRA" : ($datos['cod_mov'.$i] == 13 ? "SOBRANTE CAJA GENERAL" : "FAL REP CAJA");
					$est['tipo_mov'.$index3]  = $datos['cod_mov'.$i] != 19 ? "FALSE" : "TRUE";
					$est['importe'.$index3]   = $datos['importe'.$i];
					$est['saldo_ini'.$index3] = "";
					$est['saldo_fin'.$index3] = "";
					$est['cod_mov'.$index3]   = $datos['cod_mov'.$i];
					$est['folio'.$index3]     = "";
					$est['cuenta'.$index3]    = $_POST['cuenta'];

					// Actualizar saldo en libros
					if (existe_registro("saldos",array("num_cia","cuenta"),array($datos['num_cia'.$i],$_POST['cuenta']),$dsn))
						ejecutar_script("UPDATE saldos SET saldo_libros=saldo_libros".($est['tipo_mov'.$index3]=="FALSE"?"+{$est['importe'.$index3]}":"-{$est['importe'.$index3]}")." WHERE cuenta = $_POST[cuenta] AND num_cia=".$est['num_cia'.$index3],$dsn);
					else
						ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos,cuenta) VALUES (".$est['num_cia'.$index3].",".($est['tipo_mov'.$index3]=="FALSE"?"+{$est['importe'.$index3]}":"-{$est['importe'.$index3]}").",0,$_POST[cuenta])",$dsn);

					$index3++;
				}
			}
			
			// Almacenar datos en la base
			$db_dep = new DBclass($dsn,"depositos",$dep);
			$db_dep->xinsertar();
			$db_dep = new DBclass($dsn,"retiros",$ret);
			$db_dep->xinsertar();
			// Almacenar datos en estado_cuenta
			$db_est = new DBclass($dsn,"estado_cuenta",$est);
			$db_est->xinsertar();
			
			// ****************************** ELIMINAR DEPOSITOS (TEMPORAL)**********************
			//ejecutar_script("DELETE FROM estado_cuenta WHERE num_cia >= 900 AND tipo_mov='FALSE'",$dsn);
			//ejecutar_script("DELETE FROM depositos WHERE num_cia >= 900",$dsn);
			//***********************************************************************************
			
			// Almacenar entrada de archivo
			ejecutar_script("INSERT INTO upload_files (hash) VALUES ('".md5_file(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name'])."')",$dsn);
			
			// Desplegar listado de depositos
			$tpl->newBlock("listado");
			
			$sql = "SELECT num_cia, cod_mov, fecha_mov, importe, 'f' AS tipo_mov FROM depositos WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'FALSE' AND imprimir = 'TRUE'";
			$sql .= " UNION SELECT num_cia, cod_mov, fecha_mov, importe, 't' AS tipo_mov FROM retiros WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'FALSE' AND imprimir = 'TRUE' ORDER BY num_cia,fecha_mov";
			$result = ejecutar_script($sql,$dsn);
			
			ejecutar_script("UPDATE depositos SET imprimir = 'FALSE' WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'FALSE' AND imprimir = 'TRUE'",$dsn);
			ejecutar_script("UPDATE retiros SET imprimir = 'FALSE' WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'FALSE' AND imprimir = 'TRUE'",$dsn);
			//ejecutar_script("DELETE FROM depositos WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'FALSE' AND imprimir = 'TRUE'",$dsn);
			
			$tpl->assign("dia",date("d"));
			$tpl->assign("anio",date("Y"));
			switch (date("m")) {
				case 1: $mes = "Enero"; break;
				case 2: $mes = "Febrero"; break;
				case 3: $mes = "Marzo"; break;
				case 4: $mes = "Abril"; break;
				case 5: $mes = "Mayo"; break;
				case 6: $mes = "Junio"; break;
				case 7: $mes = "Julio"; break;
				case 8: $mes = "Agosto"; break;
				case 9: $mes = "Septiembre"; break;
				case 10: $mes = "Octubre"; break;
				case 11: $mes = "Noviembre"; break;
				case 12: $mes = "Diciembre"; break;
			}
			$tpl->assign("mes",$mes);
			
			$total = 0;
			for ($i=0; $i<count($result); $i++) {
				$tpl->newBlock("fila");
				
				$cia = ejecutar_script("SELECT nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
				$tpl->assign("num_cia",$result[$i]['num_cia']);
				$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
				$tpl->assign("nombre",$cia[0]['nombre_corto']);
				$tpl->assign("cod_mov",$result[$i]['cod_mov']);
				$cod_mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) descripcion FROM catalogo_mov_bancos WHERE cod_mov = ".$result[$i]['cod_mov'],$dsn);
				$tpl->assign("descripcion",$cod_mov[0]['descripcion']);
				$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
				$tpl->assign("fecha",$result[$i]['fecha_mov']);
				$total += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'];
			}
			$tpl->assign("listado.total",number_format($total,2,".",","));
			$tpl->printToScreen();
		}
		else {
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","Ocurrió algún error al subir el fichero. No pudo guardarse");
			$tpl->printToScreen();
		}
		// Cambiar permisos del directorio
		@chmod("/var/www/html/prueba/dc",0000);
	}
}
?>