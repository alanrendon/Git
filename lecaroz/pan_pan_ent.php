<?php
//define ('IDSCREEN',1241); // ID de pantalla
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
$descripcion_error[1] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_pan_ent.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	
	$pibote=date("d");
	for($i=0;$i<=6;$i++){
		$fecha_anterior= date("j/n/Y",mktime(0,0,0,date("m"),$pibote,date("Y")));
		$letra= date("D",mktime(0,0,0,date("m"),$pibote,date("Y")));
		if($letra=="Mon"){
			$tpl->assign("fecha_anterior",$fecha_anterior);
			break;
		}
		$pibote--;
	}
	
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("dia",date("d"));
	
	for($i=1;$i<=12;$i++){
	$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",mes_escrito($i));
		if(date("n")==$i)
			$tpl->assign("selected","selected");
	}

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die();
}

$nombremes[1]="Enero";
$nombremes[2]="Febrero";
$nombremes[3]="Marzo";
$nombremes[4]="Abril";
$nombremes[5]="Mayo";
$nombremes[6]="Junio";
$nombremes[7]="Julio";
$nombremes[8]="Agosto";
$nombremes[9]="Septiembre";
$nombremes[10]="Octubre";
$nombremes[11]="Noviembre";
$nombremes[12]="Diciembre";
$total=0;

$fecha=explode("/",$_GET['fecha']);
$total_facturas=0;
$total_anticipadas=0;
$total_posteriores=0;
$total_general=0;

//if($_GET['tipo_con1']==0){
if($_GET['fecha']!=""){
	$tpl->newBlock("resultado_dia");
	$tpl->assign("dia",$fecha[0]);
	$tpl->assign("mes",strtoupper($nombremes[$fecha[1]]));
	$tpl->assign("anio",$fecha[2]);
	$tpl->assign("num_cia",$_GET['cia']);
	$nombre=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);

	$facturas_pago_anticipado=ejecutar_script("select * from venta_pastel where fecha_entrega='$_GET[fecha]' and fecha != '$_GET[fecha]' and estado != 2 and num_cia=$_GET[cia] and cuenta > 0",$dsn);
	$facturas_cuenta_liquida=ejecutar_script("select * from venta_pastel where fecha='$_GET[fecha]' and resta is not null and cuenta=0 and dev_base is null and num_cia=$_GET[cia]",$dsn);
	$facturas_cuenta_pago=ejecutar_script("select * from venta_pastel where fecha='$_GET[fecha]' and fecha_entrega='$_GET[fecha]' and estado != 2 and num_cia=$_GET[cia]",$dsn);
	$facturas_posteriores=ejecutar_script("select * from venta_pastel where fecha='$_GET[fecha]' and resta is null and cuenta > 0 and dev_base is null and estado != 2 and num_cia=$_GET[cia] and fecha_entrega > '$_GET[fecha]'",$dsn);

	if($facturas_pago_anticipado){
		for($i=0;$i<count($facturas_pago_anticipado);$i++){
			$tpl->newBlock("facturas");
			if($facturas_pago_anticipado[$i]['letra_folio']=='X')
				$tpl->assign("letra_folio","");
			else
				$tpl->assign("letra_folio",$facturas_pago_anticipado[$i]['letra_folio']);
			$tpl->assign("num_remi",$facturas_pago_anticipado[$i]['num_remi']);
			$tpl->assign("fecha_pago",$facturas_pago_anticipado[$i]['fecha']);
			$anticipo=number_format($facturas_pago_anticipado[$i]['cuenta'],2,'.','') - number_format($facturas_pago_anticipado[$i]['base'],2,'.','');
			$tpl->assign("anticipado",number_format($anticipo,2,'.',','));
			$total_anticipadas += number_format($facturas_pago_anticipado[$i]['cuenta'],2,'.','') - number_format($facturas_pago_anticipado[$i]['base'],2,'.','');
		}
	}	

	if($facturas_cuenta_liquida){
		for($i=0;$i<count($facturas_cuenta_liquida);$i++){
			$tpl->newBlock("facturas");
			if($facturas_cuenta_liquida[$i]['letra_folio']=='X')
				$tpl->assign("letra_folio","");
			else
				$tpl->assign("letra_folio",$facturas_cuenta_liquida[$i]['letra_folio']);
			$tpl->assign("num_remi",$facturas_cuenta_liquida[$i]['num_remi']);
			$tpl->assign("fecha_pago",$facturas_cuenta_liquida[$i]['fecha']);
			$tpl->assign("cuenta",number_format($facturas_cuenta_liquida[$i]['resta'],2,'.',','));
			$total_facturas += number_format($facturas_cuenta_liquida[$i]['resta'],2,'.','');
		}
	}	

	if($facturas_cuenta_pago){
		for($i=0;$i<count($facturas_cuenta_pago);$i++){
			$tpl->newBlock("facturas");
			if($facturas_cuenta_pago[$i]['letra_folio']=='X')
				$tpl->assign("letra_folio","");
			else
				$tpl->assign("letra_folio",$facturas_cuenta_pago[$i]['letra_folio']);
			$tpl->assign("num_remi",$facturas_cuenta_pago[$i]['num_remi']);
			$tpl->assign("fecha_pago",$facturas_cuenta_pago[$i]['fecha']);
			$cuenta=number_format($facturas_cuenta_pago[$i]['cuenta'],2,'.','') - number_format($facturas_cuenta_pago[$i]['base'],2,'.','');
			$tpl->assign("cuenta",number_format($cuenta,2,'.',','));
			$total_facturas += number_format($facturas_cuenta_pago[$i]['cuenta'],2,'.','') - number_format($facturas_cuenta_pago[$i]['base'],2,'.','');
		}
	}	

	if($facturas_posteriores){
		for($i=0;$i<count($facturas_posteriores);$i++){
			$tpl->newBlock("facturas");
			if($facturas_posteriores[$i]['letra_folio']=='X')
				$tpl->assign("letra_folio","");
			else
				$tpl->assign("letra_folio",$facturas_posteriores[$i]['letra_folio']);
			$tpl->assign("num_remi",$facturas_posteriores[$i]['num_remi']);
			$posterior=number_format($facturas_posteriores[$i]['cuenta'],2,'.','') - number_format($facturas_posteriores[$i]['base'],2,'.','');
			$tpl->assign("posterior",number_format($posterior,2,'.',','));
			$tpl->assign("fecha_pago",$facturas_posteriores[$i]['fecha']);
			$total_posteriores += number_format($facturas_posteriores[$i]['cuenta'],2,'.','') - number_format($facturas_posteriores[$i]['base'],2,'.','');
		}
	}

	
	$tpl->gotoBlock("resultado_dia");
	$tpl->assign("total_facturas",number_format($total_facturas,2,'.',','));
	$tpl->assign("total_anticipadas",number_format($total_anticipadas,2,'.',','));
	$tpl->assign("total_posteriores",number_format($total_posteriores,2,'.',','));

//	$total_general=$total_facturas + $total_anticipadas - $total_posteriores;
	$total_general=$total_anticipadas - $total_posteriores;
	$tpl->assign("total_general",number_format($total_general,2,'.',','));
	if($total_general < 0)
		$tpl->assign("color","FF0000");
	else
		$tpl->assign("color","0000FF");
		
	$registro=ejecutar_script("select venta_pastel + abono_pastel as pastel from total_panaderias where num_cia=$_GET[cia] and fecha='$_GET[fecha]'",$dsn);
	
	if($registro){
		$pastel=number_format($registro[0]['pastel'],2,'.','');
		$tpl->assign("pastel",number_format($registro[0]['pastel'],2,'.',','));
		$diferencia= $total_general + $pastel;
		$tpl->assign("diferencia",number_format($diferencia,2,'.',','));
		if($diferencia < 0)
			$tpl->assign("color1","FF0000");
		else
			$tpl->assign("color1","0000FF");
	}
	else{
		$diferencia = $total_general;
		$tpl->assign("diferencia",number_format($diferencia,2,'.',','));
		if($diferencia < 0)
			$tpl->assign("color1","FF0000");
		else
			$tpl->assign("color1","0000FF");
	}
}
// RESULTADOS POR MES------------------------------------------------------------------------------------------------------------------
else{
	$tpl->newBlock("resultado_mes");
	$tpl->assign("mes",strtoupper($nombremes[$_GET['mes']]));
	$tpl->assign("anio",$_GET['anio']);
	
	$fecha_inicial="1/$_GET[mes]/$_GET[anio]";
	$fecha_final=date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
	$_fecha=explode("/",$fecha_final);

	$tpl->assign("num_cia",$_GET['cia']);
	$nombre=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
	
	$total_fac=0;
	$total_ant=0;
	$total_pos=0;
	$gran_total=0;
	$total_dif=0;
	$total_pas=0;
	
	for($i=1;$i<=$_fecha[0];$i++){
		$total_facturas=0;
		$total_anticipadas=0;
		$total_posteriores=0;
		$total_cuenta_liquida=0;
		$total_cuenta_pago=0;

		$tpl->newBlock("dias");
		$tpl->assign("dia",$i);
		$fecha_armada=$i."/".$_GET['mes']."/".$_GET['anio'];

		$facturas_pago_anticipado=ejecutar_script("select sum(cuenta) from venta_pastel where fecha_entrega='$fecha_armada' and fecha != '$fecha_armada' and estado != 2 and num_cia=$_GET[cia]",$dsn);
		$facturas_pago_anticipado_base=ejecutar_script("select sum(base) from venta_pastel where fecha_entrega='$fecha_armada' and fecha != '$fecha_armada' and estado != 2 and num_cia=$_GET[cia]",$dsn);
		
		$facturas_cuenta_liquida=ejecutar_script("select sum(resta) from venta_pastel where fecha='$fecha_armada' and resta is not null and cuenta=0 and dev_base is null and num_cia=$_GET[cia]",$dsn);
		
		$facturas_cuenta_pago=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='$fecha_armada' and fecha_entrega='$fecha_armada' and estado != 2 and num_cia=$_GET[cia]",$dsn);
		$facturas_cuenta_pago_base=ejecutar_script("select sum(base) from venta_pastel where fecha='$fecha_armada' and fecha_entrega='$fecha_armada' and estado != 2 and num_cia=$_GET[cia]",$dsn);
		
		$facturas_posteriores=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='$fecha_armada' and resta is null and cuenta > 0 and dev_base is null and estado != 2 and num_cia=$_GET[cia] and fecha_entrega > '$fecha_armada'",$dsn);
		$facturas_posteriores_base=ejecutar_script("select sum(base) from venta_pastel where fecha='$fecha_armada' and resta is null and cuenta > 0 and dev_base is null and estado != 2 and num_cia=$_GET[cia] and fecha_entrega > '$fecha_armada'",$dsn);

		$pastel=ejecutar_script("select venta_pastel + abono_pastel as pastel from total_panaderias where num_cia=$_GET[cia] and fecha = '$fecha_armada'",$dsn);

		if($facturas_pago_anticipado){
			$total_anticipadas=number_format($facturas_pago_anticipado[0]["sum"],2,'.','') - number_format($facturas_pago_anticipado_base[0]["sum"],2,'.','');
			$total_ant += $total_anticipadas;
		}
		if($facturas_cuenta_liquida){
			$total_cuenta_liquida=number_format($facturas_cuenta_liquida[0]["sum"],2,'.','');
			$total_fac += $total_cuenta_liquida;
		}
		if($facturas_cuenta_pago){
			$total_cuenta_pago=number_format($facturas_cuenta_pago[0]["sum"],2,'.','') - number_format($facturas_cuenta_pago_base[0]["sum"],2,'.','');
			$total_fac += $total_cuenta_pago;
		}
		if($facturas_posteriores){
			$total_posteriores = number_format($facturas_posteriores[0]["sum"],2,'.','') - number_format($facturas_posteriores_base[0]["sum"],2,'.','');
			$total_pos += $total_posteriores;
		}
		
		$total_facturas=$total_cuenta_liquida + $total_cuenta_pago;
		
		if($total_facturas > 0)
			$tpl->assign("cuenta",number_format($total_facturas,2,'.',','));
		else
			$tpl->assign("cuenta","");
		if($total_anticipadas > 0)
			$tpl->assign("anticipado",number_format($total_anticipadas,2,'.',','));
		else
			$tpl->assign("anticipado","");
		if($total_posteriores > 0)
			$tpl->assign("posterior",number_format($total_posteriores,2,'.',','));
		else
			$tpl->assign("posterior","");
//-------
		$total_general= $total_anticipadas - $total_posteriores;
		
		if($total_general == 0)
			$tpl->assign("total","");
		else
			$tpl->assign("total",number_format($total_general,2,'.',','));

		if($total_general < 0)
			$tpl->assign("color","FF0000");
		else
			$tpl->assign("color","0000FF");
		
		if($pastel[0]['pastel'] > 0)
			$tpl->assign("pastel",number_format($pastel[0]['pastel'],2,'.',','));
		else
			$tpl->assign("pastel","");
		$_pastel=number_format($pastel[0]['pastel'],2,'.','');
		$dif=$total_general + $_pastel;
		$total_dif += $dif;
		$total_pas += $_pastel;
		
		$tpl->assign("diferencia",number_format($dif,2,'.',','));
		
		if($dif < 0)
			$tpl->assign("color1","FF0000");
		else
			$tpl->assign("color1","0000FF");
		
	}
//------	TERMINA CICLO DE DIAS
	$tpl->gotoBlock("resultado_mes");
	if($total_fac > 0)
		$tpl->assign("total_cuenta",number_format($total_fac,2,'.',','));
	else
		$tpl->assign("total_cuenta","");
	if($total_ant > 0)
		$tpl->assign("total_anticipado",number_format($total_ant,2,'.',','));
	else
		$tpl->assign("total_anticipado","");
	if($total_pos > 0)
		$tpl->assign("total_posterior",number_format($total_pos,2,'.',','));
	else
		$tpl->assign("total_posterior","");

//	$gran_total=$total_fac + $total_ant - $total_pos;
	$gran_total = $total_ant - $total_pos;
	
	if($gran_total == 0)
		$tpl->assign("total_general","");
	else
		$tpl->assign("total_general",number_format($gran_total,2,'.',','));

	if($gran_total < 0)
		$tpl->assign("color","FF0000");
	else
		$tpl->assign("color","0000FF");
		
	if($total_pas > 0)	
		$tpl->assign("total_pas",number_format($total_pas,2,'.',','));
	else
		$tpl->assign("total_pas","");
	
	if($total_dif==0)
		$tpl->assign("total_dif","");
	else
		$tpl->assign("total_dif",number_format($total_dif,2,'.',','));

	if($total_dif < 0)
		$tpl->assign("color1","FF0000");
	else
		$tpl->assign("color1","0000FF");


//----

}
$tpl->printToScreen();

?>