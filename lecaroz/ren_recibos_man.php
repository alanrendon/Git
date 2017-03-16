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
$descripcion_error[1] = "Ya existe el recibo en la base de datos";
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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_man.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if(isset($_POST['fecha0'])){
	$tpl->newBlock("impresion");
	for($i=0;$i<$_POST['contador'];$i++){
		ejecutar_script(
		"insert into recibos_rentas
		(num_arrendatario,num_recibo,renta,agua,mantenimiento,iva,isr_retenido,iva_retenido,neto,fecha,cod_arrendador,bloque,impreso,fecha_pago,concepto) 
		VALUES(".$_POST['arrendatario'.$i].", ".$_POST['recibo'.$i].", ".number_format($_POST['renta'.$i],2,'.','').", ".number_format($_POST['agua'.$i],2,'.','').", ".number_format($_POST['mantenimiento'.$i],2,'.','').", ".number_format($_POST['iva'.$i],2,'.','').", ".number_format($_POST['isr_ret'.$i],2,'.','').", ".number_format($_POST['iva_ret'.$i],2,'.','').", ".number_format($_POST['neto'.$i],2,'.','').", '".$_POST['fecha'.$i]."', ".$_POST['arrendador'.$i].", ".$_POST['bloque'.$i].",'false','".$_POST['fecha_pago'.$i]."','".$_POST['comentario'.$i]."')",$dsn);
		
		$tpl->newBlock("recibos_imp");
		$tpl->assign("nombre_arrendador",$_POST['nombre_arrendador'.$i]);
		$tpl->assign("num_arrendador",$_POST['arrendador'.$i]);
		$tpl->assign("finicio",$_POST['recibo'.$i]);
	}
	$tope=$_POST['contador'] - 1;
	$finicio=$_POST['recibo0'];
	$ffinal=$_POST['recibo'.$tope];
	$tpl->gotoBlock("impresion");
	$tpl->assign("num_arrendador",$_POST['arrendador0']);
	$tpl->assign("num_arrendatario",$_POST['arrendatario0']);
	$tpl->assign("finicio",$finicio);
	$tpl->assign("ffinal",$ffinal);
	
	
	
	$tpl->printToScreen();
	die();
}

if (!isset($_GET['anio'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($i)));
		if(date("n")==$i)
			$tpl->assign("selected","selected");
	}
	
	$sql="select * from catalogo_arrendatarios order by num_arrendatario";
	$arrendatarios=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($arrendatarios);$i++)	{
		$tpl->newBlock("nombre_arrendador");
		$tpl->assign("num_arr",$arrendatarios[$i]['num_arrendatario']);
		$tpl->assign("nombre_arrendador",$arrendatarios[$i]['nombre_arrendatario']);
	}
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

$arrendatario=ejecutar_script("select catalogo_arrendatarios.*, catalogo_locales.nombre, catalogo_locales.bloque, catalogo_arrendadores.cod_arrendador, catalogo_arrendadores.nombre from catalogo_arrendatarios join catalogo_locales using(num_local) join catalogo_arrendadores using (cod_arrendador) where num_arrendatario=".$_GET['arrendatario']." ORDER BY cod_arrendador, num_arrendatario",$dsn);
$contador=0;
$mes_pago=0;
$bandera=0;
switch($_GET['tipo_recibo1']){
	case 0:
			$contador = 1;
			$mes_pago = 1;
			break;
	case 1:
			$contador = 3;
			$mes_pago = 3;
			break;
	case 2:
			$contador = 6;
			$mes_pago = 6;
			break;
	case 3:
			$contador=$_GET['meses'];
			$mes_pago=$_GET['meses'];
			break;
}

if($_GET['tipo_recibo'] != 0){
	if($_GET['recibos'] == 1){
		$contador=1;
		$bandera=1;
	}
	else{
		$mes_pago=1;
	}
}

$tpl->newBlock("revision");
$tpl->assign("mes",$_GET['mes']);
$tpl->assign("nombre_mes",mes_escrito($_GET['mes']));
$tpl->assign("anio",$_GET['anio']);
$fecha="1/".$_GET['mes']."/".$_GET['anio'];

$mes = $_GET['mes'];
$anio = $_GET['anio'];

$tpl->assign("fecha",$fecha);
$tpl->assign("contador",$contador);

if($contador > 1 or $bandera==1){
	$tpl->newBlock("aviso");
}

$renta=0;
$neto=0;
$aux_folio=$_GET['folio'];
for($i=0;$i<$contador;$i++){
	$tpl->newBlock("arrendatario");
	$tpl->assign("i",$i);
	$sql="select * from recibos_rentas where cod_arrendador=".$arrendatario[0]['cod_arrendador']." and num_recibo = $aux_folio";
	$folio=ejecutar_script($sql,$dsn);
	if($folio)
		$tpl->assign("color","FF0000");

	$tpl->assign("cod_arrendador",$arrendatario[0]['cod_arrendador']);
	$tpl->assign("nombre_arrendador",strtoupper($arrendatario[0]['nombre']));
	$tpl->assign("recibo",$aux_folio);
	$tpl->assign("nombre_arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
	$tpl->assign("arrendatario",$arrendatario[0]['num_arrendatario']);
	$tpl->assign("arrendador",$arrendatario[0]['cod_arrendador']);
	$tpl->assign("nombre_arrendador",strtoupper($arrendatario[0]['nombre']));
	$tpl->assign("bloque1",$arrendatario[0]['bloque']);
	$tpl->assign("fecha_pago",$fecha);
	$tpl->assign("comentario",strtoupper($_GET['concepto']));

	$aux_folio++;
	$subtotal=0;
	$total=0;
	if($_GET['renta']==1){
		if($_GET['renta_mod'] == 1)
			$subtotal=$_GET['importe_nuevo'] * $mes_pago;
		else
			$subtotal = $arrendatario[0]['renta_con_recibo'] * $mes_pago;
		
		$retencion = $subtotal * 0.10;
		$tpl->assign("renta1",number_format($subtotal,2,'.',','));
		$tpl->assign("renta",number_format($subtotal,2,'.',''));
	}
	else{
		$retencion=0;
	}
	
	if($_GET['mantenimiento']==1){
		$tpl->assign("mantenimiento",$arrendatario[0]['mantenimiento']);
		if($arrendatario[0]['mantenimiento'] !="" and $arrendatario[0]['mantenimiento'] > 0)
			$tpl->assign("mantenimiento1",number_format($arrendatario[0]['mantenimiento'],2,'.',','));
		$subtotal += number_format($arrendatario[0]['mantenimiento'],2,'.','');
	}

	if($_GET['iva']==1){
		$iva=($subtotal) * 0.15;
		$subtotal += $iva;
		$tpl->assign("iva",number_format($iva,2,'.',''));
	}
	else
		$iva=0;
	
	$total=$subtotal;
	

	if($_GET['agua']==1){
		$tpl->assign("agua",$arrendatario[0]['agua']);
		if($arrendatario[0]['agua'] !="" and $arrendatario[0]['agua'] > 0)
			$tpl->assign("agua1",number_format($arrendatario[0]['agua'],2,'.',','));
			
		$total += number_format($arrendatario[0]['agua'],2,'.','');
	}
	
	
	if($_GET['isr_ret']==1)	{
		if($arrendatario[0]['retencion_isr']=='t'){
			if($_GET['isr_mant']==1){
				$isr_mantenimiento = ($arrendatario[0]['mantenimiento']) * 0.10;
				$tpl->assign("isr_ret",$isr_mantenimiento);
				$tpl->assign("isr_ret1",number_format($isr_mantenimiento,2,'.',','));
				$total -= $isr_mantenimiento;
			}
			else{
				$tpl->assign("isr_ret",$retencion);
				$tpl->assign("isr_ret1",number_format($retencion,2,'.',','));
				$total -= $retencion;
			}
		}
		else
			$tpl->assign("isr_ret","0");
	}
	else
		$tpl->assign("isr_ret","0");
	
	if($_GET['iva_ret']==1){
		if($arrendatario[0]['retencion_iva']=='t'){
			if($_GET['iva_mant']==1){
				$iva_mantenimiento = ($arrendatario[0]['mantenimiento']) * 0.10;
				$tpl->assign("iva_ret",$iva_mantenimiento);
				$tpl->assign("iva_ret1",number_format($iva_mantenimiento,2,'.',','));
				$total -= $iva_mantenimiento;
			}
			else{
				$tpl->assign("iva_ret",$retencion);
				$tpl->assign("iva_ret1",number_format($retencion,2,'.',','));
				$total -= $retencion;
			}
		}
		else
			$tpl->assign("iva_ret","0");
	}
	
	$tpl->assign("neto",$total);
	$tpl->assign("iva1",number_format($iva,2,'.',','));
	$tpl->assign("neto1",number_format($total,2,'.',','));
	
	if($arrendatario[0]['bloque']==1)
		$tpl->assign("bloque2","INTERNO");
	else
		$tpl->assign("bloque2","EXTERNO");
		
	if($_GET['tipo_recibo'] == 0){
		$cadena=mes_escrito($_GET['mes'])." del ".$_GET['anio'];
		$tpl->assign("fecha1",$cadena);
		$tpl->assign("fecha",$fecha);
	}
	else{
		if($_GET['recibos']==1){
			if(($mes + $mes_pago) > 12){
				$mes_siguiente= ($mes + $mes_pago) - 13;
				$anio_siguiente=$anio+1;
			}
			else{
				$mes_siguiente=$mes + $mes_pago - 1;
				$anio_siguiente=$anio;
			}
			$fecha1="1/".$mes."/".$anio;
			$tpl->assign("fecha",$fecha1);
			$cadena=mes_escrito($mes)." del ".$anio." <br>a ".mes_escrito($mes_siguiente)." del ".$anio_siguiente;
			$tpl->assign("fecha1",$cadena);
			
		}
		else{
			$fecha1="1/".$mes."/".$anio;
			$cadena=mes_escrito($mes)." del ".$anio;
			$tpl->assign("fecha",$fecha1);
			$tpl->assign("fecha1",$cadena);
			$mes++;
			if($mes > 12){
				$mes=1;
				$anio++;
			}
		}
	}
		
}
if($_GET['con1']==1){
	$tpl->newBlock("comentario");
	$tpl->assign("comentario",strtoupper($_GET['concepto']));

}

$tpl->printToScreen();
die();
?>