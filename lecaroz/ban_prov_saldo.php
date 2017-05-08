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
$tpl->assignInclude("body","./plantillas/ban/ban_prov_saldo.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['proveedor'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	
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

$sql="SELECT * FROM pasivo_proveedores";
if($_GET['tipo_con']==0){
	
	if($_GET['proveedor']!="")
		$sql.=" WHERE num_proveedor=".$_GET['proveedor']." ORDER BY num_cia,fecha_mov";
	else
		$sql.=" ORDER BY num_proveedor,num_cia,fecha_mov";
}
else
	if($_GET['cia']!="")
		$sql.=" WHERE num_cia=".$_GET['cia']." ORDER BY num_cia, num_proveedor,fecha_mov";
	else
		$sql.=" ORDER BY num_cia,num_proveedor,fecha_mov";

$pasivo=ejecutar_script($sql,$dsn);

//print_r($pasivo);
//----------------------------------------------------------------------------------------------------------
if(!$pasivo){
	header("location: ./ban_prov_saldo.php?codigo_error=1");
	die();
}

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

$aux_cia= -1;
$aux_prov= -1;

$total_proveedor=0;
$total_prov=0;
$total_cia=0;
//ENTRA PARA PROVEEDORES

if($_GET['tipo_con']==0){
$tope=count($pasivo);
$tope -=1;
	for($i=0;$i<count($pasivo);$i++){
		if($aux_prov != $pasivo[$i]['num_proveedor']){
		$total_proveedor=0;
			$tpl->newBlock("proveedor");
			$tpl->assign("num_proveedor",$pasivo[$i]['num_proveedor']);
			$proveedor=obtener_registro("catalogo_proveedores",array("num_proveedor"),array($pasivo[$i]['num_proveedor']),"","",$dsn);
			$tpl->assign("nom_proveedor",$proveedor[0]['nombre']);
			$aux_prov=$pasivo[$i]['num_proveedor'];
		}
		
		if($aux_cia!=$pasivo[$i]['num_cia']){
			$tpl->newBlock("total_cia");
			$tpl->assign("total_cia",number_format($total_cia,2,'.',','));
			$total_cia=0;
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$pasivo[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array("num_cia"),array($pasivo[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
			$aux_cia=$pasivo[$i]['num_cia'];
		}
		
		$tpl->newBlock("rows");
		$tpl->assign("num_fact",$pasivo[$i]['num_fact']);
		$tpl->assign("descripcion",$pasivo[$i]['descripcion']);
		$tpl->assign("fecha_mov",$pasivo[$i]['fecha_mov']);
		$tpl->assign("fecha_pago",$pasivo[$i]['fecha_pago']);
		$tpl->assign("codgastos",$pasivo[$i]['codgastos']);
		$tpl->assign("importe",number_format($pasivo[$i]['total'],2,'.',','));
		$total_proveedor += $pasivo[$i]['total'];
		$total_cia +=$pasivo[$i]['total'];
		
		if($i==$tope){
			$tpl->newBlock("total_cia");
			$tpl->assign("total_cia",number_format($total_cia,2,'.',','));
		}

		
		
		$tpl->gotoBlock("proveedor");
		$tpl->assign("total_proveedor",number_format($total_proveedor,2,'.',','));
	}
}

else{
$tope=count($pasivo);
$tope -=1;
	for($i=0;$i<count($pasivo);$i++){
		if($aux_cia != $pasivo[$i]['num_cia']){
			$total_cia=0;
			$tpl->newBlock("compania");
			$tpl->assign("num_cia",$pasivo[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array("num_cia"),array($pasivo[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nom_compania",$cia[0]['nombre_corto']);
			$aux_cia=$pasivo[$i]['num_cia'];
		}
		
		if($aux_prov!=$pasivo[$i]['num_proveedor']){
			$tpl->newBlock("total_prov");
			$tpl->assign("total_proveedor",number_format($total_prov,2,'.',','));
			$total_prov=0;
			$tpl->newBlock("prov");
			$tpl->assign("num_proveedor",$pasivo[$i]['num_proveedor']);
			$proveedor=obtener_registro("catalogo_proveedores",array("num_proveedor"),array($pasivo[$i]['num_proveedor']),"","",$dsn);
			$tpl->assign("nombre_proveedor",$proveedor[0]['nombre']);
			$aux_prov=$pasivo[$i]['num_proveedor'];
		}
		
		$tpl->newBlock("rows1");
		$tpl->assign("num_fact",$pasivo[$i]['num_fact']);
		$tpl->assign("descripcion",$pasivo[$i]['descripcion']);
		$tpl->assign("fecha_mov",$pasivo[$i]['fecha_mov']);
		$tpl->assign("fecha_pago",$pasivo[$i]['fecha_pago']);
		$tpl->assign("codgastos",$pasivo[$i]['codgastos']);
		$tpl->assign("importe",number_format($pasivo[$i]['total'],2,'.',','));
		$total_prov += $pasivo[$i]['total'];
		$total_cia +=$pasivo[$i]['total'];
		
		if($i==$tope){
			$tpl->newBlock("total_prov");
			$tpl->assign("total_proveedor",number_format($total_prov,2,'.',','));
		}
		$tpl->gotoBlock("compania");
		$tpl->assign("total_cia",number_format($total_cia,2,'.',','));
	}
}

$tpl->printToScreen();
?>