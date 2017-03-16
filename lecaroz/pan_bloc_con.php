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
$descripcion_error[1] = "No se encontraron blocks para la compañía";
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
$tpl->assignInclude("body","./plantillas/pan/pan_bloc_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


//********************DESCOMENTAR PARA REALIZAR MANTENIMIENTO A LA PANTALLA, UNICAMENTE EL ADMINISTRADOR PODRA USARLA
/*
if($_SESSION['iduser']!=1){
	header("location:./mantenimiento.php");
	die();
}
*/

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['tipo_list']))
{

	$tpl->newBlock("obtener_datos");
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


if($_GET['tipo_list']==0){

	
	// Imprimir el resultado
	$sql="select * from bloc where idcia=".$_GET['cia'];
	
	if ($_GET['stat']==0) $sql.=" and estado=true order by folio_inicio";
	else if($_GET['stat']==1) $sql.=" and estado=false and folios_usados > 0 order by folio_inicio";
	else if($_GET['stat']==2) $sql.=" and estado=false and folios_usados = 0 order by folio_inicio";
	else if($_GET['stat']==3) $sql.=" order by folio_inicio";
	
	$bloc=ejecutar_script($sql,$dsn);
	
	if(!$bloc)
	{
		header("location: ./pan_bloc_con.php?codigo_error=1");
		die();
	}
	
	$tpl->newBlock("bloc");
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	
	$operadora=obtener_registro("catalogo_operadoras",array("idoperadora"),array($cia[0]['idoperadora']),"","",$dsn);
	$tpl->assign("operadora",$operadora[0]['nombre_operadora']);
	
	
	for($i=0;$i<count($bloc);$i++)
	{
		$tpl->newBlock("rows");
		if($bloc[$i]['let_folio']=="X")
			$tpl->assign("let_folio","");
		else
			$tpl->assign("let_folio",$bloc[$i]['let_folio']);
		$tpl->assign("let_folio1",$bloc[$i]['let_folio']);
		$tpl->assign("folio_inicial",$bloc[$i]['folio_inicio']);
		$tpl->assign("folio_final",$bloc[$i]['folio_final']);
		$tpl->assign("num_folios",$bloc[$i]['num_folios']);
		$tpl->assign("fecha",$bloc[$i]['fecha']);
		$tpl->assign("num_cia",$_GET['cia']);
		$tpl->assign("idbloc",$bloc[$i]['id']);
	
		if($bloc[$i]['estado']=='t') $tpl->assign("status","TERMINADO");
		else if($bloc[$i]['estado']=='f' and $bloc[$i]['folios_usados'] > 0) $tpl->assign("status","EN PROCESO");
		else if($bloc[$i]['estado']=='f' and $bloc[$i]['folios_usados'] == 0) $tpl->assign("status","SIN USAR");
		
		if($bloc[$i]['estado']=='t')
		{	
			$tpl->newBlock("borrado");
			$tpl->assign("id",$bloc[$i]['id']);
			$tpl->assign("id_user",$_SESSION['iduser']);
		}
	}
}

else{
	$sql="SELECT * FROM bloc order by idcia, let_folio,folio_inicio";
	$blocs=ejecutar_script($sql,$dsn);

	if($_GET['tipo_list2']==1){
		$tpl->newBlock("listado");
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",mes_escrito(date("m")));
		$tpl->assign("anio",date("Y"));
		
		
		$aux_cia=0;
		$total_blocs=0;
		$total_usados=0;
		$total_proceso=0;
		$total_terminados=0;
		
		for($i=0;$i<count($blocs);$i++){
			if($aux_cia != $blocs[$i]['idcia']){
				$tpl->newBlock("cias");
				$tpl->assign("num_cia",$blocs[$i]['idcia']);
				$nombre_cia=obtener_registro("catalogo_companias",array("num_cia"),array($blocs[$i]['idcia']),"","",$dsn);
				$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
				$total_blocs=0;
				$total_usados=0;
				$total_proceso=0;
				$total_terminados=0;
				
			}
			$aux_cia=$blocs[$i]['idcia'];
			$tpl->newBlock("rows1");
			$total_blocs += 1;
			if($blocs[$i]['let_folio']=='X')
				$tpl->assign("let_folio","");
			else
				$tpl->assign("let_folio",$blocs[$i]['let_folio']);
			$tpl->assign("num_remi",$blocs[$i]['folio_inicio']);
			$tpl->assign("fecha",$blocs[$i]['fecha']);
	
			if($blocs[$i]['estado']=='t'){
				$tpl->assign("terminados","X");
				$total_terminados += 1;
			}
			else{
				if($blocs[$i]['folios_usados']==0){
					$tpl->assign("sin_usar","X");
					$total_usados += 1;
				}
				else{
					$tpl->assign("proceso","X");
					$total_proceso += 1;
				}
			}
			$tpl->gotoBlock("cias");
			if($total_usados==0) $tpl->assign("total_sin_usar","");
			else $tpl->assign("total_sin_usar",$total_usados);
			
			if($total_terminados==0) $tpl->assign("total_terminados","");
			else $tpl->assign("total_terminados",$total_terminados);
			
			if($total_proceso==0) $tpl->assign("total_proceso","");
			else $tpl->assign("total_proceso",$total_proceso);
			
			$tpl->assign("total_blocs",$total_blocs);
		}
	}
	
	else{
//********
		$tpl->newBlock("total");
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",mes_escrito(date("m")));
		$tpl->assign("anio",date("Y"));
		
		$aux_cia=0;
		$total_blocs=0;
		$total_usados=0;
		$total_proceso=0;
		$total_terminados=0;
		for($i=0;$i<count($blocs);$i++){
			if($aux_cia != $blocs[$i]['idcia']){
				$tpl->newBlock("rows3");
				$tpl->assign("num_cia",$blocs[$i]['idcia']);
				$nombre_cia=obtener_registro("catalogo_companias",array("num_cia"),array($blocs[$i]['idcia']),"","",$dsn);
				$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
				$total_blocs=0;
				$total_usados=0;
				$total_proceso=0;
				$total_terminados=0;
			}
			$aux_cia=$blocs[$i]['idcia'];
			$total_blocs += 1;

			if($blocs[$i]['estado']=='t'){
				$total_terminados += 1;
			}
			else{
				if($blocs[$i]['folios_usados']==0){
					$total_usados += 1;
				}
				else{
					$total_proceso += 1;
				}
			}
			if($total_usados==0) $tpl->assign("total_sin_usar","");
			else $tpl->assign("total_sin_usar",$total_usados);
			
			if($total_terminados==0) $tpl->assign("total_terminados","");
			else $tpl->assign("total_terminados",$total_terminados);
			
			if($total_proceso==0) $tpl->assign("total_proceso","");
			else $tpl->assign("total_proceso",$total_proceso);
			
			$tpl->assign("total_blocs",$total_blocs);
			
		}
	}
	
}
$tpl->printToScreen();

?>