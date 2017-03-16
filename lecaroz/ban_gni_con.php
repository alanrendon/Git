<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
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
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gni_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha_inicial'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha1","1/".date("n")."/".date("Y"));
	$tpl->assign("fecha2",date("j/n/Y"));
	
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
// -------------------------------- GENERAR ARREGLO ---------------------------------------------------------

$nombremes[1]="ENERO";
$nombremes[2]="FEBRERO";
$nombremes[3]="MARZO";
$nombremes[4]="ABRIL";
$nombremes[5]="MAYO";
$nombremes[6]="JUNIO";
$nombremes[7]="JULIO";
$nombremes[8]="AGOSTO";
$nombremes[9]="SEPTIEMBRE";
$nombremes[10]="OCTUBRE";
$nombremes[11]="NOVIEMBRE";
$nombremes[12]="DICIEMBRE";
$total=0;
$_d1=explode("/",$_GET['fecha_inicial']);
$_d2=explode("/",$_GET['fecha_final']);

//echo $sql;
//CHEQUES POR CODIGO DE GASTO
if($_GET['tipo']==0){

	$sql="select cheques.*, codigo_edo_resultados from cheques join catalogo_gastos using(codgastos) where fecha between '$_GET[fecha_inicial]' and '$_GET[fecha_final]' and codigo_edo_resultados=0 and codgastos not in(23,33,134,141,59) and fecha_cancelacion is null";
	
	if($_GET['num_cia']!="")
		$sql.=" and num_cia=".$_GET['num_cia'];
	else
		$sql.=" and (num_cia < 200 or num_cia in (select num_cia from catalogo_companias where num_cia >700 and num_cia <800))";
		
	$sql.=" order by num_cia, num_proveedor, codgastos,fecha";
	
	$cheques=ejecutar_script($sql,$dsn);
	if(!$cheques){
		header("location: ./ban_gni_con.php?codigo_error=1");
		die();
	}

	$total=0;
	$tpl->newBlock("por_gasto");
	$total_general=0;
	$aux_cia=0;	

	$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
	$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
	for($i=0;$i<count($cheques);$i++){
		if($aux_cia!=$cheques[$i]["num_cia"]){
			$total=0;
			$aux_proveedor=0;
			$tpl->newBlock("compania2");

			$tpl->assign("num_cia",$cheques[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array('num_cia'),array($cheques[$i]['num_cia']),"","",$dsn);
			$tpl->assign('nombre_cia',$cia[0]['nombre']);
		}
		$aux_cia=$cheques[$i]["num_cia"];
		$tpl->newBlock("row_gasto");
		$tpl->assign("num_cia",$cheques[$i]['num_cia']);
		$cia=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]["nombre_corto"]);
		$tpl->assign("folio",$cheques[$i]['folio']);
		$tpl->assign("num_proveedor",$cheques[$i]['num_proveedor']);
		$tpl->assign("nombre_proveedor",$cheques[$i]['a_nombre']);
		$tpl->assign("concepto",$cheques[$i]['concepto']);
		$tpl->assign("fecha",$cheques[$i]['fecha']);
		$gasto=obtener_registro("catalogo_gastos",array("codgastos"),array($cheques[$i]['codgastos']),"","",$dsn);
		$tpl->assign("codgastos",$gasto[0]['codgastos']);
		$tpl->assign("descripcion",$gasto[0]['descripcion']);

		
		if($cheques[$i]['fecha_cancelacion']!="")
			$tpl->assign("cantidad","CANCELADO");
		else{
			$tpl->assign("cantidad",number_format($cheques[$i]['importe'],2,'.',','));
			$total+=number_format($cheques[$i]['importe'],2,'.','');
			$total_general += number_format($cheques[$i]['importe'],2,'.','');
		}
		$tpl->gotoBlock("compania2");
		$tpl->assign("total_cia",number_format($total,2,'.',','));
	}

	$tpl->gotoBlock("por_gasto");
	$tpl->assign("total_general",number_format($total_general,2,'.',','));
}
else{
	$tpl->newBlock("anualizado");
	$tpl->assign("anio",$_GET['anio']);
	if($_GET['num_cia']!="")
		$cias=ejecutar_script("select num_cia, nombre_corto from catalogo_companias where num_cia=$_GET[num_cia]",$dsn);
	else
		$cias=ejecutar_script("select num_cia, nombre_corto from catalogo_companias where num_cia < 200 or num_cia in (select num_cia from catalogo_companias where num_cia >700 and num_cia <800) order by num_cia",$dsn);
	
	$total=array();
	for($i=1;$i<=12;$i++){
		$total[$i]=0;
	}
	
	for($i=0;$i < count($cias);$i++){
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		$tpl->assign("anio",$_GET['anio']);
		
		for($j=1;$j<=12;$j++){
			$fecha_inicial="1/$j/$_GET[anio]";
			$fecha_final=date("j/n/Y",mktime(0,0,0,$j+1,0,$_GET['anio']));
			$cheques=ejecutar_script("select sum(importe) from cheques join catalogo_gastos using(codgastos) where fecha between '$fecha_inicial' and '$fecha_final' and codigo_edo_resultados=0 and codgastos not in(33,134,141,59) and fecha_cancelacion is null and num_cia=".$cias[$i]['num_cia'],$dsn);
			if($cheques){
				if($cheques[0]['sum']==0)
					$tpl->assign($j,"");
				else
					$tpl->assign($j,number_format($cheques[0]['sum'],2,'.',','));
				
				$total[$j] += number_format($cheques[0]['sum'],2,'.','');
			}
		}
	}
	$tpl->gotoBlock("anualizado");
	for($i=1;$i<=12;$i++){
		if($total[$i] > 0)
			$tpl->assign("t".$i,number_format($total[$i],2,'.',','));
	}
}
$tpl->printToScreen();
?>