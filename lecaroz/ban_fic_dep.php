<?php
// IMPRESIÓN DE FICHAS DE DEPÓSITO
// Tabla 'depositos'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/ficha_deposito.tpl" );
$tpl->prepare();


// --------------------------------- Almacenar datos ---------------------------------------------------------
$sql = "SELECT * FROM depositos WHERE ficha = 'TRUE' ORDER BY num_cia";
$result = ejecutar_script($sql,$dsn);

if (!$result) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

ejecutar_script("UPDATE depositos SET ficha = 'FALSE' WHERE ficha = 'TRUE'",$dsn);
$ficha = 1;
for ($i=0; $i<count($result); $i++) {
	if ($ficha == 1) {
		$tpl->newBlock("ficha");
		$tpl->newBlock("ficha1");
		$ficha = 2;
	}
	else if ($ficha == 2) {
		$tpl->newBlock("ficha2");
		$ficha = 1;
		
		if ($i < count($result)-1)
			$tpl->newBlock("salto");
		$tpl->gotoBlock("ficha2");
	}
	
	$tpl->assign("num_cia",$result[$i]['num_cia']);
	$cia = ejecutar_script("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
	$tpl->assign("cliente",$cia[0]['nombre']);
	$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[0]['fecha_mov'],$fecha);
	$tpl->assign("d",(int)$fecha[1]);
	$tpl->assign("m",(int)$fecha[2]);
	$tpl->assign("y",(int)$fecha[3]);
	
	$tpl->assign("concepto",$result[$i]['concepto']);
	
	// Cuenta
	$tpl->assign("1",$cia[0]['clabe_cuenta'][1]);
	$tpl->assign("2",$cia[0]['clabe_cuenta'][2]);
	$tpl->assign("3",$cia[0]['clabe_cuenta'][3]);
	$tpl->assign("4",$cia[0]['clabe_cuenta'][4]);
	$tpl->assign("5",$cia[0]['clabe_cuenta'][5]);
	$tpl->assign("6",$cia[0]['clabe_cuenta'][6]);
	$tpl->assign("7",$cia[0]['clabe_cuenta'][7]);
	$tpl->assign("8",$cia[0]['clabe_cuenta'][8]);
	$tpl->assign("9",$cia[0]['clabe_cuenta'][9]);
	$tpl->assign("10",$cia[0]['clabe_cuenta'][10]);
	
	if ($result[$i]['num_cia'] < 100)
		$tpl->assign("tipo_cia","PAN");
	else if (($result[$i]['num_cia'] > 100 && $result[$i]['num_cia'] < 200) || ($result[$i]['num_cia'] > 701 && $result[$i]['num_cia'] < 800))
		$tpl->assign("tipo_cia","POLLO");
}

$tpl->printToScreen();
?>