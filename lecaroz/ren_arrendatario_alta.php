<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ren/ren_arrendatario_alta.tpl");
$tpl->prepare();
// Seleccionar script para menu

$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_POST['cod_arrendatario'])) {

	$tpl->newBlock("obtener_datos");
	$tpl->assign("id",nextID2("catalogo_arrendatarios","num_arrendatario",$dsn));
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("danos","20000.00");
	$tpl->assign("termino","15000.00");
	

	$local=ejecutar_script("select num_local, catalogo_locales.nombre,num_cia, direccion, cod_arrendador, catalogo_arrendadores.nombre as nombre_arrendador, catalogo_arrendadores.tipo_persona, cta_predial,bloque from catalogo_locales join catalogo_companias using (num_cia) join catalogo_arrendadores using(cod_arrendador) where ocupado=false order by num_local",$dsn);
	for($i=0;$i<count($local);$i++){
		$tpl->newBlock("nombre_local");
		$tpl->assign("num_local",$local[$i]['num_local']);
		$tpl->assign("nombre_local",$local[$i]['nombre']);
		$tpl->assign("direccion",$local[$i]['direccion']);
		$tpl->assign("nombre_arrendador",$local[$i]['nombre_arrendador']);
		$tpl->assign("predial",$local[$i]['cta_predial']);
		
		if($local[$i]['bloque']==1)
			$tpl->assign("bloque","PROPIO");
		elseif($local[$i]['bloque']==2)
			$tpl->assign("bloque","AJENO");
			
		if($local[$i]['tipo_persona']=='f')
//			$tpl->assign("contrato","\'./contrato_renta2.php\'");
			$tpl->assign("contrato","contrato_renta2.php");
		else
//			$tpl->assign("contrato","\'./contrato_renta.php\'");
			$tpl->assign("contrato","contrato_renta.php");
	}
	
	$arrendatarios = ejecutar_script("select num_arrendatario from catalogo_arrendatarios order by num_arrendatario",$dsn);
	for($i=0;$i<count($arrendatarios);$i++){
		$tpl->newBlock("ocupados");
		$tpl->assign("i",$i);
		$tpl->assign("cod_arrendatario",$arrendatarios[$i]['num_arrendatario']);
	}

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();

	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	$tpl->printToScreen();
	die();

	}

$tpl->printToScreen();
die();
}
//---------------------------------------------------------


ejecutar_script("INSERT INTO catalogo_arrendatarios
(num_arrendatario,num_local,nombre_arrendatario,representante,nombre_aval, bien_avaluo,rfc,giro,fecha_inicio,fecha_final,renta_con_recibo,renta_sin_recibo,agua,mantenimiento,rentas_en_deposito,incremento_anual,retencion_isr,retencion_iva,fianza,tipo_persona,cargo_daños,cargo_termino, direccion_fiscal, recibo_mensual,descripcion_local) 
VALUES(".$_POST['cod_arrendatario'].", ".$_POST['num_local'].",'".$_POST['nombre_arrendatario']."', '".$_POST['representante']."', '".$_POST['nombre_aval']."', '".$_POST['bien_avaluo']."', '".$_POST['rfc']."', '".$_POST['giro']."','".$_POST['fecha_inicial']."','".$_POST['fecha_final']."', ".number_format($_POST['con_recibo'],2,'.','').", ".number_format($_POST['sin_recibo'],2,'.','').", ".number_format($_POST['agua'],2,'.','').", ".number_format($_POST['mantenimiento'],2,'.','').", ".$_POST['rentas_deposito'].", '".$_POST['incremento']."','".$_POST['retencion_isr']."', '".$_POST['iva']."','".$_POST['fianza']."','".$_POST['tipo_persona']."',".$_POST['daños'].", ".$_POST['termino'].", '".$_POST['dir_fiscal']."', '".$_POST['recibo_mensual']."', '".strtoupper($_POST['descripcion_local'])."')",$dsn);

$local=ejecutar_script("select * from catalogo_locales where num_local = ".$_POST['num_local'],$dsn);

if( ($local[0]['locales_ocupados'] + 1) == $local[0]['locales'])
	ejecutar_script("UPDATE catalogo_locales set ocupado = true, locales_ocupados = locales_ocupados + 1 where num_local=".$_POST['num_local'],$dsn);

else
	ejecutar_script("UPDATE catalogo_locales set ocupado = false, locales_ocupados = locales_ocupados + 1 where num_local=".$_POST['num_local'],$dsn);
	





header("location: ./ren_arrendatario_alta.php");
die();
?>