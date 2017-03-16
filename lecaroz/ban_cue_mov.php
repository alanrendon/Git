<?php
// CONCILIACION AUTOMÁTICA
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
$tpl->assignInclude("body","./plantillas/ban/ban_cue_mov.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pedir archivo de Banorte
if (!isset($_POST['MAX_FILE_SIZE'])) {
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
	$tipo_archivo        = $_FILES['userfile']['type'];
	$tamano_archivo      = $_FILES['userfile']['size'];

	// Comprobar características del fichero
	if (!(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 5242880)) {
		$tpl->newBlock("mensaje");
		$tpl->assign("mensaje","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB.");
		$tpl->printToScreen();
	}
	else {
		// Cargar depositos a la  base de datos
		$fd = fopen($_FILES['userfile']['tmp_name'],"r");
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
						$mov['tipo_mov'.$count]      = (substr($buffer,27,1) == 1)?"TRUE":"FALSE";
						$mov['importe'.$count]       = number_format(substr($buffer,28,12).".".substr($buffer,40,2),2,".","");
						$mov['num_documento'.$count] = ((int)substr($buffer,42,10) >= 0)?(int)substr($buffer,42,10):"";
						$mov['concepto'.$count]      = substr($buffer,52,12).substr($buffer,64,16);
						$mov['fecha_con'.$count]    = "";
						$mov['cod_banco'.$count]     = ((int)substr($buffer,23,4) >= 0)?substr($buffer,23,4):"";
						$mov['cod_mov'.$count]       = "";
						$count++;
					break;
					// Registro final de cuenta
					case 33:
						$saldos[$cia] = number_format(substr($buffer,74,12).".".substr($buffer,86,2),2,".","");

						//echo $cia . ' ' . number_format(substr($buffer,74,12).".".substr($buffer,86,2),2,".","") . '<br />';
					break;
				}
			}
		}
		fclose($fd);

		// Vaciar movimientos anteriores de la base de datos
		ejecutar_script("TRUNCATE TABLE mov_banorte_temp",$dsn);

		// Almacenar movimientos en mov_banorte
		$db = new DBclass($dsn,"mov_banorte_temp",$mov);
		$db->xinsertar();

		// Obtener listado de movimientos
		$sql = "SELECT num_cia,nombre,cuenta,fecha,tipo_mov,importe,num_documento,concepto,cod_banco FROM mov_banorte_temp ORDER BY num_cia,fecha ASC";
		$mov = ejecutar_script($sql,$dsn);

		$tpl->newBlock("listado");
		$tpl->assign('md5', strtoupper(md5_file($_FILES['userfile']['tmp_name'])));
		for ($i=0; $i<count($mov); $i++) {
			$tpl->newBlock("fila");
			$tpl->assign("num_cia",($mov[$i]['num_cia'] > 0)?$mov[$i]['num_cia']:"S/N");
			$tpl->assign("nombre",$mov[$i]['nombre']);
			$tpl->assign("cuenta",$mov[$i]['cuenta']);
			$tpl->assign("fecha",$mov[$i]['fecha']);
			$tpl->assign("deposito",($mov[$i]['tipo_mov'] == "f")?number_format($mov[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("retiro",($mov[$i]['tipo_mov'] == "t")?number_format($mov[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("num_documento",($mov[$i]['num_documento'] > 0)?(int)$mov[$i]['num_documento']:"&nbsp;");
			$tpl->assign("concepto",$mov[$i]['concepto']);
			$tpl->assign("cod_mov",((int)$mov[$i]['cod_banco'] >= 0)?$mov[$i]['cod_banco']:"&nbsp;");
			$tpl->assign('saldo', number_format($saldos[$mov[$i]['num_cia']], 2));
		}
		$tpl->printToScreen();

		foreach ($saldos as $num_cia => $saldo) {
			if (existe_registro("saldo_banorte", array("num_cia"), array($num_cia), $dsn))
				ejecutar_script("UPDATE saldo_banorte SET saldo = $saldo WHERE num_cia = $num_cia", $dsn);
			else
				ejecutar_script("INSERT INTO saldo_banorte (num_cia, saldo) VALUES ($num_cia, $saldo)", $dsn);
		}
	}
}
?>
