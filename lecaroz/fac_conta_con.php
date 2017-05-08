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
$tpl->assignInclude("body","./plantillas/fac/fac_conta_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha",date("d/m/Y"));


//echo date( "d/m/Y", mktime(0,0,0,12,30,1997) );
	for($j=0;$j<12;$j++)
	{
		$tpl->newBlock("mes");
		switch ($j) {
		   case 0:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Enero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 1:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Febrero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 2:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Marzo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
  		   case 3:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Abril");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 4:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Mayo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 5:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Junio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 6:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Julio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 7:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Agosto");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 8:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Septiembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 9:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Octubre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 10:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Noviembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 11:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Diciembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;

		}		
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
// -------------------------------- Mostrar listado ---------------------------------------------------------
$diasxmes[1] = 31; // Enero
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre

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



$fecha_inicio="1/".$_GET['mes']."/".$_GET['anio'];
$fecha_final=$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio'];

//TIPO DE PROVEEDOR   A  V  I  O -- 0

if($_GET['tipo_prov']==0){
	$sql="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 0 ";
	
	if($_GET['cia']!=""){
		$sql.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}
	$facturas=ejecutar_script($sql,$dsn);
}

//TIPO DE PROVEEDOR  O T R O S  --- 1
else if($_GET['tipo_prov']==1){
	$sql="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,	
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 1 ";
	
	if($_GET['cia']!=""){
		$sql.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}
	$facturas=ejecutar_script($sql,$dsn);
}
//TIPO DE PROVEEDOR  E M P A Q U E --- 2 
else if($_GET['tipo_prov']==2){
	$sql="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,	
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 2 ";
	
	if($_GET['cia']!=""){
		$sql.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}
	
	$facturas=ejecutar_script($sql,$dsn);
}
//TIPO DE PROVEEDOR T O D O S
else{
	$sql_avio="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,	
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 0 ";
	
	if($_GET['cia']!=""){
		$sql_avio.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql_avio.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}

	$sql_empaque="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,	
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 2 ";
	
	if($_GET['cia']!=""){
		$sql_empaque.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql_empaque.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}

	$sql_otros="
	SELECT 
	facturas.num_cia, 
	catalogo_companias.nombre_corto,	
	facturas.num_fact, 
	facturas.num_proveedor, 
	catalogo_proveedores.nombre,
	catalogo_proveedores.idtipoproveedor,
	upper(catalogo_proveedores.rfc) as rfc,
	facturas.imp_sin_iva,
	facturas.importe_total, 
	facturas.importe_iva, 
	(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
	(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
	facturas.fecha_mov, 
	facturas_pagadas.folio_cheque,
	estado_cuenta.fecha_con 
	FROM 
	facturas 
	JOIN catalogo_companias using(num_cia)
	JOIN catalogo_proveedores using(num_proveedor)
	LEFT JOIN facturas_pagadas USING(num_fact,num_proveedor) 
	LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
	WHERE 
	facturas.fecha_mov between '".$fecha_inicio."' AND '".$fecha_final."' 
	AND catalogo_proveedores.idtipoproveedor = 1 ";
	
	if($_GET['cia']!=""){
		$sql_otros.=
			"
			AND facturas.num_cia=".$_GET['cia']." 
			ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
			";
	}
	else if($_GET['cia']==""){
		$sql_otros.="ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov ";
	}
	
	$avio=ejecutar_script($sql_avio,$dsn);
	$otros=ejecutar_script($sql_otros,$dsn);
	$empaque=ejecutar_script($sql_empaque,$dsn);
}

$aux_cia=(-1);
$aux_prov=(-1);
$tpl->newBlock("listado");

if($_GET['cia']!=""){
	if($_GET['tipo_prov']!=3){
	
		if(!$facturas){
			header("location: ./fac_conta_con.php?codigo_error=1");
			die();
		}
		$tpl->newBlock("compania");
		$tpl->assign("num_cia",$facturas[0]['num_cia']);
		$tpl->assign("nombre_cia",$facturas[0]['nombre_corto']);
		$tpl->assign("mes",$nombremes[$_GET['mes']]);
		$tpl->assign("anio",$_GET['anio']);
		
		switch($_GET['tipo_prov']){
			case 0:	$tpl->assign("tipo_prov","DE AVIO");
					break;
			case 1:	$tpl->assign("tipo_prov","VARIOS");
					break;
			case 2: $tpl->assign("tipo_prov","DE EMPAQUE");
		}
		$proveedor_sub=0;
		$proveedor_iva=0;
		$proveedor_iva_ret=0;
		$proveedor_isr_ret=0;
		$proveedor_total=0;
		$next=0;

		for($i=0;$i<count($facturas);$i++){
			$next=$i+1;
			$tpl->newBlock("rows");
			
			if($facturas[$i]['num_proveedor']!=$aux_prov){
				$tpl->newBlock("proveedor");
				$tpl->assign("nom_proveedor",$facturas[$i]['nombre']);
				$tpl->assign("rfc",$facturas[$i]['rfc']);
				$aux_prov=$facturas[$i]['num_proveedor'];
				$tpl->gotoBlock("rows");
			}
			$tpl->assign("fecha",$facturas[$i]['fecha_mov']);
			$tpl->assign("factura",$facturas[$i]['num_fact']);
			$tpl->assign("sub_total",number_format($facturas[$i]['imp_sin_iva'],2,'.',','));
			$tpl->assign("ieps","");
			$tpl->assign("num_cheque",$facturas[$i]['folio_cheque']);
			$tpl->assign("fecha_con",$facturas[$i]['fecha_con']);

			if($facturas[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
			else
				$tpl->assign("isr_ret",number_format($facturas[$i]['isr_retenido'],2,'.',','));

			if($facturas[$i]['importe_total']<=0) $tpl->assign("total","");
			else
				$tpl->assign("total",number_format($facturas[$i]['importe_total'],2,'.',','));

			if($facturas[$i]['importe_iva']<=0) $tpl->assign("iva","");
			else
				$tpl->assign("iva",number_format($facturas[$i]['importe_iva'],2,'.',','));

			if($facturas[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
			else
				$tpl->assign("iva_ret",number_format($facturas[$i]['iva_retenido'],2,'.',','));

			$aux_prov=$facturas[$i]['num_proveedor'];
			
			$proveedor_sub+=$facturas[$i]['imp_sin_iva'];
			$proveedor_iva+=$facturas[$i]['importe_iva'];
			$proveedor_iva_ret+=$facturas[$i]['iva_retenido'];
			$proveedor_isr_ret+=$facturas[$i]['isr_retenido'];
			$proveedor_total+=$facturas[$i]['importe_total'];
//			echo $facturas[$i]['num_proveedor']." proveedor i <br>";
//			echo $facturas[$next]['num_proveedor']." proveedor next <br>";
			if($next >= count($facturas)){
				$tpl->newBlock("total_proveedor");
				$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));

				if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
				else 
					$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
					
				if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
				else
					$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
					
				if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
				else
					$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
				
				$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
				$proveedor_sub=0;
				$proveedor_iva=0;
				$proveedor_iva_ret=0;
				$proveedor_isr_ret=0;
				$proveedor_total=0;
			}
			else {
//				echo "proveedor i $aux_prov";
//				echo "proveedor next $facturas[$next]['num_proveedor']";
				if($aux_prov != $facturas[$next]['num_proveedor']){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
			}
		}
	}
	else{
	//******************************** AVIO
		if($avio){
			$tpl->newBlock("compania");
			$tpl->assign("num_cia",$avio[0]['num_cia']);
			$tpl->assign("nombre_cia",$avio[0]['nombre_corto']);
			$tpl->assign("mes",$nombremes[$_GET['mes']]);
			$tpl->assign("anio",$_GET['anio']);
			$tpl->assign("tipo_prov","DE AVIO");

			$proveedor_sub=0;
			$proveedor_iva=0;
			$proveedor_iva_ret=0;
			$proveedor_isr_ret=0;
			$proveedor_total=0;
			$next=0;
	
			for($i=0;$i<count($avio);$i++){
				$next=$i+1;
				$tpl->newBlock("rows");
				
				if($avio[$i]['num_proveedor']!=$aux_prov){
					$tpl->newBlock("proveedor");
					$tpl->assign("nom_proveedor",$avio[$i]['nombre']);
					$tpl->assign("rfc",$avio[$i]['rfc']);
					$aux_prov=$avio[$i]['num_proveedor'];
					$tpl->gotoBlock("rows");
				}
				$tpl->assign("fecha",$avio[$i]['fecha_mov']);
				$tpl->assign("factura",$avio[$i]['num_fact']);
				$tpl->assign("sub_total",number_format($avio[$i]['imp_sin_iva'],2,'.',','));
				$tpl->assign("ieps","");
				$tpl->assign("num_cheque",$avio[$i]['folio_cheque']);
				$tpl->assign("fecha_con",$avio[$i]['fecha_con']);
	
				if($avio[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
				else
					$tpl->avio("isr_ret",number_format($avio[$i]['isr_retenido'],2,'.',','));
	
				if($avio[$i]['importe_total']<=0) $tpl->assign("total","");
				else
					$tpl->assign("total",number_format($avio[$i]['importe_total'],2,'.',','));
	
				if($avio[$i]['importe_iva']<=0) $tpl->assign("iva","");
				else
					$tpl->assign("iva",number_format($avio[$i]['importe_iva'],2,'.',','));
	
				if($avio[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
				else
					$tpl->assign("iva_ret",number_format($avio[$i]['iva_retenido'],2,'.',','));
	
				$aux_prov=$avio[$i]['num_proveedor'];
				
				$proveedor_sub+=$avio[$i]['imp_sin_iva'];
				$proveedor_iva+=$avio[$i]['importe_iva'];
				$proveedor_iva_ret+=$avio[$i]['iva_retenido'];
				$proveedor_isr_ret+=$avio[$i]['isr_retenido'];
				$proveedor_total+=$avio[$i]['importe_total'];
				if($next >= count($avio)){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
	
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
				else {
	//				echo "proveedor i $aux_prov";
	//				echo "proveedor next $facturas[$next]['num_proveedor']";
					if($aux_prov != $avio[$next]['num_proveedor']){
						$tpl->newBlock("total_proveedor");
						$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
						if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
						else 
							$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
							
						if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
						else
							$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
							
						if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
						else
							$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
						$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
						$proveedor_sub=0;
						$proveedor_iva=0;
						$proveedor_iva_ret=0;
						$proveedor_isr_ret=0;
						$proveedor_total=0;
					}
				}
			}
		}
	
	//************************** EMPAQUE
		if($empaque){
			$tpl->newBlock("compania");
			$tpl->assign("num_cia",$empaque[0]['num_cia']);
			$tpl->assign("nombre_cia",$empaque[0]['nombre_corto']);
			$tpl->assign("mes",$nombremes[$_GET['mes']]);
			$tpl->assign("anio",$_GET['anio']);
			$tpl->assign("tipo_prov","DE EMPAQUE");

			$proveedor_sub=0;
			$proveedor_iva=0;
			$proveedor_iva_ret=0;
			$proveedor_isr_ret=0;
			$proveedor_total=0;
			$next=0;
	
			for($i=0;$i<count($empaque);$i++){
				$next=$i+1;
				$tpl->newBlock("rows");
				
				if($avio[$i]['num_proveedor']!=$aux_prov){
					$tpl->newBlock("proveedor");
					$tpl->assign("nom_proveedor",$empaque[$i]['nombre']);
					$tpl->assign("rfc",$empaque[$i]['rfc']);
					$aux_prov=$empaque[$i]['num_proveedor'];
					$tpl->gotoBlock("rows");
				}
				$tpl->assign("fecha",$empaque[$i]['fecha_mov']);
				$tpl->assign("factura",$empaque[$i]['num_fact']);
				$tpl->assign("sub_total",number_format($empaque[$i]['imp_sin_iva'],2,'.',','));
				$tpl->assign("ieps","");
				$tpl->assign("num_cheque",$empaque[$i]['folio_cheque']);
				$tpl->assign("fecha_con",$empaque[$i]['fecha_con']);
	
				if($empaque[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
				else
					$tpl->empaque("isr_ret",number_format($empaque[$i]['isr_retenido'],2,'.',','));
	
				if($empaque[$i]['importe_total']<=0) $tpl->assign("total","");
				else
					$tpl->assign("total",number_format($empaque[$i]['importe_total'],2,'.',','));
	
				if($empaque[$i]['importe_iva']<=0) $tpl->assign("iva","");
				else
					$tpl->assign("iva",number_format($empaque[$i]['importe_iva'],2,'.',','));
	
				if($empaque[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
				else
					$tpl->assign("iva_ret",number_format($empaque[$i]['iva_retenido'],2,'.',','));
	
				$aux_prov=$empaque[$i]['num_proveedor'];
				
				$proveedor_sub+=$empaque[$i]['imp_sin_iva'];
				$proveedor_iva+=$empaque[$i]['importe_iva'];
				$proveedor_iva_ret+=$empaque[$i]['iva_retenido'];
				$proveedor_isr_ret+=$empaque[$i]['isr_retenido'];
				$proveedor_total+=$empaque[$i]['importe_total'];
				if($next >= count($empaque)){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
	
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
				else {
	//				echo "proveedor i $aux_prov";
	//				echo "proveedor next $facturas[$next]['num_proveedor']";
					if($aux_prov != $empaque[$next]['num_proveedor']){
						$tpl->newBlock("total_proveedor");
						$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
						if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
						else 
							$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
							
						if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
						else
							$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
							
						if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
						else
							$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
						$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
						$proveedor_sub=0;
						$proveedor_iva=0;
						$proveedor_iva_ret=0;
						$proveedor_isr_ret=0;
						$proveedor_total=0;
					}
				}
			}
		}
//*********************************VARIOS
		if($otros){
			$tpl->newBlock("compania");
			$tpl->assign("num_cia",$otros[0]['num_cia']);
			$tpl->assign("nombre_cia",$otros[0]['nombre_corto']);
			$tpl->assign("mes",$otros[$_GET['mes']]);
			$tpl->assign("anio",$_GET['anio']);
			$tpl->assign("tipo_prov","VARIOS");

			$proveedor_sub=0;
			$proveedor_iva=0;
			$proveedor_iva_ret=0;
			$proveedor_isr_ret=0;
			$proveedor_total=0;
			$next=0;
	
			for($i=0;$i<count($otros);$i++){
				$next=$i+1;
				$tpl->newBlock("rows");
				
				if($otros[$i]['num_proveedor']!=$aux_prov){
					$tpl->newBlock("proveedor");
					$tpl->assign("nom_proveedor",$otros[$i]['nombre']);
					$tpl->assign("rfc",$otros[$i]['rfc']);
					$aux_prov=$otros[$i]['num_proveedor'];
					$tpl->gotoBlock("rows");
				}
				$tpl->assign("fecha",$otros[$i]['fecha_mov']);
				$tpl->assign("factura",$otros[$i]['num_fact']);
				$tpl->assign("sub_total",number_format($otros[$i]['imp_sin_iva'],2,'.',','));
				$tpl->assign("ieps","");
				$tpl->assign("num_cheque",$otros[$i]['folio_cheque']);
				$tpl->assign("fecha_con",$otros[$i]['fecha_con']);
	
				if($otros[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
				else
					$tpl->assign("isr_ret",number_format($otros[$i]['isr_retenido'],2,'.',','));
	
				if($otros[$i]['importe_total']<=0) $tpl->assign("total","");
				else
					$tpl->assign("total",number_format($otros[$i]['importe_total'],2,'.',','));
	
				if($otros[$i]['importe_iva']<=0) $tpl->assign("iva","");
				else
					$tpl->assign("iva",number_format($otros[$i]['importe_iva'],2,'.',','));
	
				if($otros[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
				else
					$tpl->assign("iva_ret",number_format($otros[$i]['iva_retenido'],2,'.',','));
	
				$aux_prov=$otros[$i]['num_proveedor'];
				
				$proveedor_sub+=$otros[$i]['imp_sin_iva'];
				$proveedor_iva+=$otros[$i]['importe_iva'];
				$proveedor_iva_ret+=$otros[$i]['iva_retenido'];
				$proveedor_isr_ret+=$otros[$i]['isr_retenido'];
				$proveedor_total+=$otros[$i]['importe_total'];
				if($next >= count($otros)){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
	
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
				else {
	//				echo "proveedor i $aux_prov";
	//				echo "proveedor next $facturas[$next]['num_proveedor']";
					if($aux_prov != $otros[$next]['num_proveedor']){
						$tpl->newBlock("total_proveedor");
						$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
						if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
						else 
							$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
							
						if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
						else
							$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
							
						if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
						else
							$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
						$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
						$proveedor_sub=0;
						$proveedor_iva=0;
						$proveedor_iva_ret=0;
						$proveedor_isr_ret=0;
						$proveedor_total=0;
					}
				}
			}
		}


//*************
	}
}

if($_GET['cia']==""){
	if($_GET['tipo_prov']!=3){
		$proveedor_sub=0;
		$proveedor_iva=0;
		$proveedor_iva_ret=0;
		$proveedor_isr_ret=0;
		$proveedor_total=0;
		$next=0;
		$aux_cia=(-1);
		for($i=0;$i<count($facturas);$i++){
			if($facturas[$i]['num_cia']!=$aux_cia){
				$tpl->newBlock("compania");
				$tpl->assign("num_cia",$facturas[$i]['num_cia']);
				$tpl->assign("nombre_cia",$facturas[$i]['nombre_corto']);
				$tpl->assign("mes",$nombremes[$_GET['mes']]);
				$tpl->assign("anio",$_GET['anio']);
				switch($_GET['tipo_prov']){
					case 0:	$tpl->assign("tipo_prov","DE AVIO");
							break;
					case 1:	$tpl->assign("tipo_prov","VARIOS");
							break;
					case 2: $tpl->assign("tipo_prov","DE EMPAQUE");
				}

			}
			$aux_cia=$facturas[$i]['num_cia'];
			$next=$i+1;
			$tpl->newBlock("rows");
			
			if($facturas[$i]['num_proveedor']!=$aux_prov){
				$tpl->newBlock("proveedor");
				$tpl->assign("nom_proveedor",$facturas[$i]['nombre']);
				$tpl->assign("rfc",$facturas[$i]['rfc']);
				$aux_prov=$facturas[$i]['num_proveedor'];
				$tpl->gotoBlock("rows");
			}
			$tpl->assign("fecha",$facturas[$i]['fecha_mov']);
			$tpl->assign("factura",$facturas[$i]['num_fact']);
			$tpl->assign("sub_total",number_format($facturas[$i]['imp_sin_iva'],2,'.',','));
			$tpl->assign("ieps","");
			$tpl->assign("num_cheque",$facturas[$i]['folio_cheque']);
			$tpl->assign("fecha_con",$facturas[$i]['fecha_con']);

			if($facturas[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
			else
				$tpl->assign("isr_ret",number_format($facturas[$i]['isr_retenido'],2,'.',','));

			if($facturas[$i]['importe_total']<=0) $tpl->assign("total","");
			else
				$tpl->assign("total",number_format($facturas[$i]['importe_total'],2,'.',','));

			if($facturas[$i]['importe_iva']<=0) $tpl->assign("iva","");
			else
				$tpl->assign("iva",number_format($facturas[$i]['importe_iva'],2,'.',','));

			if($facturas[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
			else
				$tpl->assign("iva_ret",number_format($facturas[$i]['iva_retenido'],2,'.',','));

			$aux_prov=$facturas[$i]['num_proveedor'];
			
			$proveedor_sub+=$facturas[$i]['imp_sin_iva'];
			$proveedor_iva+=$facturas[$i]['importe_iva'];
			$proveedor_iva_ret+=$facturas[$i]['iva_retenido'];
			$proveedor_isr_ret+=$facturas[$i]['isr_retenido'];
			$proveedor_total+=$facturas[$i]['importe_total'];
			if($next >= count($facturas)){
				$tpl->newBlock("total_proveedor");
				$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));

				if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
				else 
					$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
					
				if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
				else
					$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
					
				if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
				else
					$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
				
				$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
				$proveedor_sub=0;
				$proveedor_iva=0;
				$proveedor_iva_ret=0;
				$proveedor_isr_ret=0;
				$proveedor_total=0;
			}
			else {
//				echo "proveedor i $aux_prov";
//				echo "proveedor next $facturas[$next]['num_proveedor']";
				if($aux_prov != $facturas[$next]['num_proveedor']){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
			}
		}
	}
	
	else{
//*********************************************
		$proveedor_sub=0;
		$proveedor_iva=0;
		$proveedor_iva_ret=0;
		$proveedor_isr_ret=0;
		$proveedor_total=0;
		$next=0;
		$aux_cia=(-1);
		for($i=0;$i<count($avio);$i++){
			if($avio[$i]['num_cia']!=$aux_cia){
				$tpl->newBlock("compania");
				$tpl->assign("num_cia",$avio[$i]['num_cia']);
				$tpl->assign("nombre_cia",$avio[$i]['nombre_corto']);
				$tpl->assign("mes",$nombremes[$_GET['mes']]);
				$tpl->assign("anio",$_GET['anio']);
				$tpl->assign("tipo_prov","DE AVIO");

			}
			$aux_cia=$avio[$i]['num_cia'];
			$next=$i+1;
			$tpl->newBlock("rows");
			
			if($avio[$i]['num_proveedor']!=$aux_prov){
				$tpl->newBlock("proveedor");
				$tpl->assign("nom_proveedor",$avio[$i]['nombre']);
				$tpl->assign("rfc",$avio[$i]['rfc']);
				$aux_prov=$avio[$i]['num_proveedor'];
				$tpl->gotoBlock("rows");
			}
			$tpl->assign("fecha",$avio[$i]['fecha_mov']);
			$tpl->assign("factura",$avio[$i]['num_fact']);
			$tpl->assign("sub_total",number_format($avio[$i]['imp_sin_iva'],2,'.',','));
			$tpl->assign("ieps","");
			$tpl->assign("num_cheque",$avio[$i]['folio_cheque']);
			$tpl->assign("fecha_con",$avio[$i]['fecha_con']);

			if($avio[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
			else
				$tpl->assign("isr_ret",number_format($avio[$i]['isr_retenido'],2,'.',','));

			if($avio[$i]['importe_total']<=0) $tpl->assign("total","");
			else
				$tpl->assign("total",number_format($avio[$i]['importe_total'],2,'.',','));

			if($avio[$i]['importe_iva']<=0) $tpl->assign("iva","");
			else
				$tpl->assign("iva",number_format($avio[$i]['importe_iva'],2,'.',','));

			if($avio[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
			else
				$tpl->assign("iva_ret",number_format($avio[$i]['iva_retenido'],2,'.',','));

			$aux_prov=$avio[$i]['num_proveedor'];
			
			$proveedor_sub+=$avio[$i]['imp_sin_iva'];
			$proveedor_iva+=$avio[$i]['importe_iva'];
			$proveedor_iva_ret+=$avio[$i]['iva_retenido'];
			$proveedor_isr_ret+=$avio[$i]['isr_retenido'];
			$proveedor_total+=$avio[$i]['importe_total'];
			if($next >= count($avio)){
				$tpl->newBlock("total_proveedor");
				$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));

				if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
				else 
					$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
					
				if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
				else
					$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
					
				if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
				else
					$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
				
				$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
				$proveedor_sub=0;
				$proveedor_iva=0;
				$proveedor_iva_ret=0;
				$proveedor_isr_ret=0;
				$proveedor_total=0;
			}
			else {
//				echo "proveedor i $aux_prov";
//				echo "proveedor next $facturas[$next]['num_proveedor']";
				if($aux_prov != $avio[$next]['num_proveedor']){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
			}
		}

//***********************	EMPAQUE
		$proveedor_sub=0;
		$proveedor_iva=0;
		$proveedor_iva_ret=0;
		$proveedor_isr_ret=0;
		$proveedor_total=0;
		$next=0;
		$aux_cia=(-1);
		for($i=0;$i<count($empaque);$i++){
			if($empaque[$i]['num_cia']!=$aux_cia){
				$tpl->newBlock("compania");
				$tpl->assign("num_cia",$empaque[$i]['num_cia']);
				$tpl->assign("nombre_cia",$empaque[$i]['nombre_corto']);
				$tpl->assign("mes",$nombremes[$_GET['mes']]);
				$tpl->assign("anio",$_GET['anio']);
				$tpl->assign("tipo_prov","DE EMPAQUE");

			}
			$aux_cia=$empaque[$i]['num_cia'];
			$next=$i+1;
			$tpl->newBlock("rows");
			
			if($empaque[$i]['num_proveedor']!=$aux_prov){
				$tpl->newBlock("proveedor");
				$tpl->assign("nom_proveedor",$empaque[$i]['nombre']);
				$tpl->assign("rfc",$empaque[$i]['rfc']);
				$aux_prov=$empaque[$i]['num_proveedor'];
				$tpl->gotoBlock("rows");
			}
			$tpl->assign("fecha",$empaque[$i]['fecha_mov']);
			$tpl->assign("factura",$empaque[$i]['num_fact']);
			$tpl->assign("sub_total",number_format($empaque[$i]['imp_sin_iva'],2,'.',','));
			$tpl->assign("ieps","");
			$tpl->assign("num_cheque",$empaque[$i]['folio_cheque']);
			$tpl->assign("fecha_con",$empaque[$i]['fecha_con']);

			if($empaque[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
			else
				$tpl->assign("isr_ret",number_format($empaque[$i]['isr_retenido'],2,'.',','));

			if($empaque[$i]['importe_total']<=0) $tpl->assign("total","");
			else
				$tpl->assign("total",number_format($empaque[$i]['importe_total'],2,'.',','));

			if($empaque[$i]['importe_iva']<=0) $tpl->assign("iva","");
			else
				$tpl->assign("iva",number_format($empaque[$i]['importe_iva'],2,'.',','));

			if($empaque[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
			else
				$tpl->assign("iva_ret",number_format($empaque[$i]['iva_retenido'],2,'.',','));

			$aux_prov=$empaque[$i]['num_proveedor'];
			
			$proveedor_sub+=$empaque[$i]['imp_sin_iva'];
			$proveedor_iva+=$empaque[$i]['importe_iva'];
			$proveedor_iva_ret+=$empaque[$i]['iva_retenido'];
			$proveedor_isr_ret+=$empaque[$i]['isr_retenido'];
			$proveedor_total+=$empaque[$i]['importe_total'];
			if($next >= count($empaque)){
				$tpl->newBlock("total_proveedor");
				$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));

				if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
				else 
					$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
					
				if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
				else
					$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
					
				if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
				else
					$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
				
				$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
				$proveedor_sub=0;
				$proveedor_iva=0;
				$proveedor_iva_ret=0;
				$proveedor_isr_ret=0;
				$proveedor_total=0;
			}
			else {
				if($aux_prov != $empaque[$next]['num_proveedor']){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
			}
		}

//***********************  VARIOS
		$proveedor_sub=0;
		$proveedor_iva=0;
		$proveedor_iva_ret=0;
		$proveedor_isr_ret=0;
		$proveedor_total=0;
		$next=0;
		$aux_cia=(-1);
		for($i=0;$i<count($otros);$i++){
			if($otros[$i]['num_cia']!=$aux_cia){
				$tpl->newBlock("compania");
				$tpl->assign("num_cia",$otros[$i]['num_cia']);
				$tpl->assign("nombre_cia",$otros[$i]['nombre_corto']);
				$tpl->assign("mes",$nombremes[$_GET['mes']]);
				$tpl->assign("anio",$_GET['anio']);
				$tpl->assign("tipo_prov","VARIOS");

			}
			$aux_cia=$otros[$i]['num_cia'];
			$next=$i+1;
			$tpl->newBlock("rows");
			
			if($otros[$i]['num_proveedor']!=$aux_prov){
				$tpl->newBlock("proveedor");
				$tpl->assign("nom_proveedor",$otros[$i]['nombre']);
				$tpl->assign("rfc",$otros[$i]['rfc']);
				$aux_prov=$otros[$i]['num_proveedor'];
				$tpl->gotoBlock("rows");
			}
			$tpl->assign("fecha",$otros[$i]['fecha_mov']);
			$tpl->assign("factura",$otros[$i]['num_fact']);
			$tpl->assign("sub_total",number_format($otros[$i]['imp_sin_iva'],2,'.',','));
			$tpl->assign("ieps","");
			$tpl->assign("num_cheque",$otros[$i]['folio_cheque']);
			$tpl->assign("fecha_con",$otros[$i]['fecha_con']);

			if($otros[$i]['isr_retenido']<=0) $tpl->assign("isr_ret","");
			else
				$tpl->assign("isr_ret",number_format($otros[$i]['isr_retenido'],2,'.',','));

			if($otros[$i]['importe_total']<=0) $tpl->assign("total","");
			else
				$tpl->assign("total",number_format($otros[$i]['importe_total'],2,'.',','));

			if($otros[$i]['importe_iva']<=0) $tpl->assign("iva","");
			else
				$tpl->assign("iva",number_format($otros[$i]['importe_iva'],2,'.',','));

			if($otros[$i]['iva_retenido']<=0) $tpl->assign("iva_ret","");
			else
				$tpl->assign("iva_ret",number_format($otros[$i]['iva_retenido'],2,'.',','));

			$aux_prov=$otros[$i]['num_proveedor'];
			
			$proveedor_sub+=$otros[$i]['imp_sin_iva'];
			$proveedor_iva+=$otros[$i]['importe_iva'];
			$proveedor_iva_ret+=$otros[$i]['iva_retenido'];
			$proveedor_isr_ret+=$otros[$i]['isr_retenido'];
			$proveedor_total+=$otros[$i]['importe_total'];
			if($next >= count($otros)){
				$tpl->newBlock("total_proveedor");
				$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));

				if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
				else 
					$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
					
				if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
				else
					$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
					
				if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
				else
					$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
				
				$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
				$proveedor_sub=0;
				$proveedor_iva=0;
				$proveedor_iva_ret=0;
				$proveedor_isr_ret=0;
				$proveedor_total=0;
			}
			else {
//				echo "proveedor i $aux_prov";
//				echo "proveedor next $facturas[$next]['num_proveedor']";
				if($aux_prov != $otros[$next]['num_proveedor']){
					$tpl->newBlock("total_proveedor");
					$tpl->assign("proveedor_sub_total",number_format($proveedor_sub,2,'.',','));
					if($proveedor_iva <= 0) $tpl->assign("proveedor_iva","");
					else 
						$tpl->assign("proveedor_iva",number_format($proveedor_iva,2,'.',','));
						
					if($proveedor_iva_ret <= 0) $tpl->assign("proveedor_iva_ret","");
					else
						$tpl->assign("proveedor_iva_ret",number_format($proveedor_iva_ret,2,'.',','));
						
					if($proveedor_isr_ret <= 0) $tpl->assign("proveedor_isr_ret","");
					else
						$tpl->assign("proveedor_isr_ret",number_format($proveedor_isr_ret,2,'.',','));
					$tpl->assign("proveedor_total",number_format($proveedor_total,2,'.',','));
					$proveedor_sub=0;
					$proveedor_iva=0;
					$proveedor_iva_ret=0;
					$proveedor_isr_ret=0;
					$proveedor_total=0;
				}
			}
		}

	
	}
}



$tpl->printToScreen();

?>