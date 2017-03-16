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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_cap1.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_POST['registros'])) {
	
	if(isset($_SESSION['recibos']))
		unset($_SESSION['recibos']);
	
	$tpl->newBlock("revision");
	$bloque=0;
	$arrendador=0;
	$_SESSION['recibos']['mes']=$_POST['mes'];
	$_SESSION['recibos']['anio']=$_POST['anio'];
	$ok=true;

	$tpl->assign("cont",$_POST['contador']);
	$tpl->assign("anio",$_POST['anio']);
	$tpl->assign("mes",$_POST['mes']);
	$tpl->assign("nombre_mes",mes_escrito($_POST['mes']));
	
	$arreglo=array();
	
	for($i=0;$i<$_POST['contador'];$i++){
		$arreglo[$i]['cod_arrendador']=	$_POST['cod_arrendador'.$i];
		$arreglo[$i]['folio'] = 		$_POST['folios'.$i];
		
		$_SESSION['recibos']['arrendador'.$i] = $_POST['cod_arrendador'.$i];
		$_SESSION['recibos']['folios'.$i] = $_POST['folios'.$i];
	}
	$arrendatario=ejecutar_script("select catalogo_arrendatarios.*, catalogo_locales.nombre, catalogo_locales.bloque, catalogo_arrendadores.cod_arrendador, catalogo_arrendadores.nombre from catalogo_arrendatarios join catalogo_locales using(num_local) join catalogo_arrendadores using (cod_arrendador) where recibo_mensual=true ORDER BY cod_arrendador, num_arrendatario",$dsn);
//	print_r($arrendatario);	
	for($i=0;$i<count($arrendatario);$i++){
		if($arrendatario[$i]['cod_arrendador'] != $arrendador){
			$tpl->newBlock("arrendadores");
			$tpl->assign("cod_arrendador",$arrendatario[$i]['cod_arrendador']);
			$tpl->assign("nombre_arrendador",strtoupper($arrendatario[$i]['nombre']));
			$arrendador=$arrendatario[$i]['cod_arrendador'];

			for($j=0;$j<count($arreglo);$j++)
				if($arreglo[$j]['cod_arrendador']==$arrendatario[$i]['cod_arrendador'])
					break;
			$folio=$arreglo[$j]['folio'];
		}
		
		$subotal = 0;
		$total = 0;
		$subtotal= number_format($arrendatario[$i]['renta_con_recibo'],2,'.','') + number_format($arrendatario[$i]['mantenimiento'],2,'.','');
		$iva = $subtotal * 0.15;
		$retencion = $arrendatario[$i]['renta_con_recibo'] * 0.10;
		$total = $subtotal + $iva;
		$total += number_format($arrendatario[$i]['agua'],2,'.','');
		
		$tpl->newBlock("arrendatarios");
		$tpl->assign("i",$i);
		$tpl->assign("nombre_arrendatario",strtoupper($arrendatario[$i]['nombre_arrendatario']));
		$tpl->assign("arrendatario",$arrendatario[$i]['num_arrendatario']);
		$tpl->assign("arrendador",$arrendatario[$i]['cod_arrendador']);
		$tpl->assign("nombre_arrendador",strtoupper($arrendatario[$i]['nombre']));
		$tpl->assign("bloque1",$arrendatario[$i]['bloque']);
		$tpl->assign("renta",$arrendatario[$i]['renta_con_recibo']);
		$tpl->assign("agua",$arrendatario[$i]['agua']);
		$tpl->assign("mantenimiento",$arrendatario[$i]['mantenimiento']);
		$tpl->assign("iva",number_format($iva,2,'.',''));
		
		if($arrendatario[$i]['retencion_isr']=='t'){
			$tpl->assign("isr_ret",$retencion);
			$tpl->assign("isr_ret1",number_format($retencion,2,'.',','));
			$total -= $retencion;
		}
		else
			$tpl->assign("isr_ret","0");
		if($arrendatario[$i]['retencion_iva']=='t'){
			$tpl->assign("iva_ret",$retencion);
			$tpl->assign("iva_ret1",number_format($retencion,2,'.',','));
			$total -= $retencion;
		}
		else
			$tpl->assign("iva_ret","0");

		$tpl->assign("neto",$total);
		
		$tpl->assign("renta1",number_format($arrendatario[$i]['renta_con_recibo'],2,'.',','));
		if($arrendatario[$i]['agua'] !="" and $arrendatario[$i]['agua'] > 0)
			$tpl->assign("agua1",number_format($arrendatario[$i]['agua'],2,'.',','));
		if($arrendatario[$i]['mantenimiento'] !="" and $arrendatario[$i]['mantenimiento'] > 0)
			$tpl->assign("mantenimiento1",number_format($arrendatario[$i]['mantenimiento'],2,'.',','));
		$tpl->assign("iva1",number_format($iva,2,'.',','));

		$tpl->assign("neto1",number_format($total,2,'.',','));
		
		if($arrendatario[$i]['bloque']==1)
			$tpl->assign("bloque2","INTERNO");
		else
			$tpl->assign("bloque2","EXTERNO");
		
		if(existe_registro("recibos_rentas",array("cod_arrendador","num_recibo"),array($arrendatario[$i]['cod_arrendador'],$folio),$dsn)){
			$tpl->assign("color","FF0000");
			$ok=false;
		}
		$tpl->assign("recibo",$folio);
		$folio++;

	}
	$tpl->gotoBlock("revision");
	$tpl->assign("registros",$i);
	if($ok==false)
		$tpl->assign("disabled","disabled");
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


$tpl->newBlock("impresion");
$fecha="1/".$_POST['mes']."/".$_POST['anio'];
$tpl->assign("anio",$_POST['anio']);
//$tpl->assign("mes",$_POST['mes']);
$tpl->assign("nombre_mes",mes_escrito($_POST['mes']));


$arreglo1=array();
$contador=0;
$aux_arrendador=0;
$aux=0;
//print_r($_POST);
for($i=0;$i<$_POST['registros_arrendatarios'];$i++){

	if($aux_arrendador != $_POST['arrendador'.$i]){
		$arreglo1[$contador]['numero']=$_POST['arrendador'.$i];
		$arreglo1[$contador]['nombre']=$_POST['nombre_arrendador'.$i];
		$arreglo1[$contador]['finicio'] = $_POST['recibo'.$i];
		
		$aux= $i-1;
		if($i > 0)
			$arreglo1[$contador - 1]['ffinal'] = $_POST['recibo'.$aux];

		$contador++;
		$aux_arrendador = $_POST['arrendador'.$i];
	}

	ejecutar_script(
	"insert into recibos_rentas
	(num_arrendatario,num_recibo,renta,agua,mantenimiento,iva,isr_retenido,iva_retenido,neto,fecha,cod_arrendador,bloque,impreso,fecha_pago) 
	VALUES(".$_POST['arrendatario'.$i].", ".$_POST['recibo'.$i].", ".$_POST['renta'.$i].", ".$_POST['agua'.$i].", ".$_POST['mantenimiento'.$i].", ".$_POST['iva'.$i].", ".$_POST['isr_ret'.$i].", ".$_POST['iva_ret'.$i].", ".$_POST['neto'.$i].", '".$fecha."', ".$_POST['arrendador'.$i].", ".$_POST['bloque'.$i].",'false','".$fecha."')",$dsn);

}
//echo "contador; $contador <br>";
//echo "i: $i<br>";

$aux=$i-1;
$arreglo1[$contador - 1]['ffinal']=$_POST['recibo'.$aux];
//print_r($arreglo1);
$tpl->assign("valor",count($arreglo1));
for($i=0;$i<count($arreglo1);$i++){
	$tpl->newBlock("recibos_arrendador");
	$tpl->assign("cod_arrendador",$arreglo1[$i]['numero']);
	$tpl->assign("nombre_arrendador",$arreglo1[$i]['nombre']);
	$tpl->assign("finicio",$arreglo1[$i]['finicio']);
	$tpl->assign("ffinal",$arreglo1[$i]['ffinal']);
}

$tpl->printToScreen();
die();

?>