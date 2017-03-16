<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
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
$tpl->assignInclude("body","./plantillas/adm/admin_gas_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['temp']))
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));
	$tpl->assign("temp","1");
	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($i)));
		if(date("n")==$i){
			$tpl->assign("selected","selected");
		}
	}

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


if($_GET['status']==0){
	$sql="select * from movimiento_gastos_cancelados join catalogo_gastos using(codgastos) WHERE revisado=false order by num_cia,fecha_can";
	$gastos=ejecutar_script($sql,$dsn);
	if(!$gastos){
		header("location: ./admin_gas_con.php?codigo_error=1");
		die();
	}
	$tpl->newBlock("no_revisados");
	$aux_cia=0;
	for($i=0;$i<count($gastos);$i++){
		if($aux_cia!=$gastos[$i]['num_cia']){
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$gastos[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array("num_cia"),array($gastos[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
			$operadora=ejecutar_script("select * from catalogo_operadoras where idoperadora={$cia[0]['idoperadora']}",$dsn);
			$tpl->assign("operadora",$operadora[0]['nombre']);
			
		}
		$aux_cia=$gastos[$i]['num_cia'];
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("id",$gastos[$i]['id']);
		$tpl->assign("fecha_can",$gastos[$i]['fecha_can']);
		$tpl->assign("codgastos",$gastos[$i]['codgastos']);
		$tpl->assign("nombre_gasto",$gastos[$i]['descripcion']);
		$tpl->assign("concepto_gasto",strtoupper($gastos[$i]['concepto_gasto']));
		$tpl->assign("concepto_can",strtoupper($gastos[$i]['concepto_cancela']));
		$tpl->assign("importe",number_format($gastos[$i]['importe'],2,'.',','));

	}
	$tpl->gotoBlock("no_revisados");
	$tpl->assign("contador",$i);
}

else if($_GET['status']==1){
	$tpl->newBlock("revisados");
	$fecha=date("d/m/Y");
	$_fecha=explode("/",$fecha);

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


//	$fecha1=date("d/m/Y", mktime(0,0,0,$_fecha[1],1,$_fecha[2]));
//	$fecha2=date( "d/m/Y", mktime(0,0,0,$_fecha[1]+1,0,$_fecha[2]));

	$fecha1=date("d/m/Y", mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
	$fecha2=date( "d/m/Y", mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

	$tpl->assign("mes",$nombremes[$_GET['mes']]);
	$tpl->assign("anio",$_fecha[2]);
	
	$sql="select * from movimiento_gastos_cancelados join catalogo_gastos using(codgastos) WHERE revisado=true and fecha_can between '".$fecha1."' and '".$fecha2."' order by num_cia,fecha_can";
	$gastos=ejecutar_script($sql,$dsn);
	if(!$gastos){
		header("location: ./admin_gas_con.php?codigo_error=1");
		die();
	}
	
	$aux_cia=0;
	for($i=0;$i<count($gastos);$i++){
		if($aux_cia!=$gastos[$i]['num_cia']){
			$tpl->newBlock("cia1");
			$tpl->assign("num_cia",$gastos[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array("num_cia"),array($gastos[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		}
		$aux_cia=$gastos[$i]['num_cia'];
		$tpl->newBlock("rows1");
		$tpl->assign("fecha_can",$gastos[$i]['fecha_can']);
		$tpl->assign("codgastos",$gastos[$i]['codgastos']);
		$tpl->assign("nombre_gasto",$gastos[$i]['descripcion']);
		$tpl->assign("importe",number_format($gastos[$i]['importe'],2,'.',','));
		$tpl->assign("descripcion",strtoupper($gastos[$i]['concepto_cancela']));
		

	}
	
}


$tpl->printToScreen();
?>