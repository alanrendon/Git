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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";
$descripcion_error[5] = "No tiene panaderias a su cargo";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_efe_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// ---------	----------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

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
// -------------------------------- SCRIPT ---------------------------------------------------------
// -------------------------------- Mostrar listado ---------------------------------------------------------
$tpl->newBlock("prueba_pan");
//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");
$fech=explode("/",$_GET['fecha_mov']);
$fecha_inicio='1/'.$fech[1].'/'.$fech[2];

if($_GET['tipo_cia'] == 0)
	$cia="select num_cia, nombre_corto from catalogo_companias where num_cia='".$_GET['num_cia']."'";
else if($_GET['tipo_cia'] == 1){
	if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
		$cia="select num_cia, nombre_corto from catalogo_companias where num_cia < 100 or num_cia in(702,703) order by num_cia";
	}
	else{
		$opera=ejecutar_script("SELECT * FROM catalogo_operadoras WHERE iduser=$_SESSION[iduser]",$dsn);
		if(!$opera){
			header("location: ./pan_efe_con.php?codigo_error=5");
			die();
		}
		$cia="select num_cia, nombre_corto from catalogo_companias where idoperadora=".$opera[0]['idoperadora']." and num_cia < 999 order by num_cia";
	}
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

$promedio_produccion = 0;
$promedio_venta_pta = 0;
$promedio_abonos = 0;
$promedio_otros = 0;
$promedio_ingresos = 0;
$promedio_raya = 0;
$promedio_empleados = 0;
$promedio_encargados = 0;
$promedio_panaderos = 0;
$promedio_otros2 = 0;
$promedio_gastos = 0;
$promedio_efectivo = 0;
$promedio_clientes = 0;

$salto=1;

$companias=ejecutar_script($cia,$dsn);
//print_r($companias);
for($j=0;$j<count($companias);$j++)
{
//-----------------------------------------------NUEVAS VARIABLES---------
	$total_produccion =0;
	$total_venta_pta = 0;
	$total_abonos=0;
	$total_otros=0;
	$total_ingresos=0;
	$total_raya=0;
	$total_empleados=0;
	$total_encargados=0;
	$total_panaderos=0;
	$total_otros2=0;
	$total_gastos=0;
	$total_efectivo=0;
	$total_clientes=0;
	$total_abono=0;
	$efec=0;
	$diferencia=0;
	$ingresos=0;
	$gastos=0;
	$var=0;
//-----------------------------------------------------------------------
	
//--------------------------------------------
	$sql="select count(distinct(fecha)) from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' AND '".$_GET['fecha_mov']."'";	
	$contador=ejecutar_script($sql,$dsn);
	$cont = $contador[0]['count'];
	if($cont <=0) continue;

	$sql2="select distinct(fecha) as fecha_total from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."'";
	$fechas=ejecutar_script($sql2,$dsn);

	if($_GET['tipo_total']==0)
	{
		$tpl->newBlock("compania");
		$dia_1=explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1[1]]);
		$tpl->assign("hora",date("G:i:s"));
		
		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
	
		for($i=0;$i<$cont;$i++){
			$tpl->newBlock("rows");
			$var++;
			$dia=explode("/",$fechas[$i]['fecha_total']);
			$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
			$sobrante = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),"","",$dsn);
			$sql="SELECT * FROM total_panaderias WHERE num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
			$efectivos=ejecutar_script($sql,$dsn);
			
			//OBTIENE LOS VALORES DE PAGO A EMPLEADOS, PAGO A ENCARGADOS Y PAGO A PANADEROS (EMPLEADOS,ENCARGADO,PANADERO,OTROS)
			$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=1  and fecha ='".$fechas[$i]['fecha_total']."') as empleados, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=2  and fecha ='".$fechas[$i]['fecha_total']."') as encargado, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=3  and fecha ='".$fechas[$i]['fecha_total']."') as panadero, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos not in(1,2,3,30) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as otros
			FROM movimiento_gastos
			WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
			$gastos=ejecutar_script($sql2,$dsn);
			
			$sql="SELECT ctes FROM captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
			$efe=ejecutar_script($sql,$dsn);
			
			//PRODUCCION INCLUYE TOTAL PRODUCCION Y TOTAL RAYA (TOTAL,RAYA)
			$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
			FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
			$produccion=ejecutar_script($sql,$dsn);
			
			
			$ingresos=$efectivos[0]['venta_puerta']+$efectivos[0]['otros']+$efectivos[0]['abono']+$efectivos[0]['pastillaje'];

//			$efec +=$ingresos - $efectivos[0]['gastos'] - $efectivos[0]['raya_pagada'];

			$tpl->assign("dia",$dia[0]);
			$tpl->assign("produccion",number_format($produccion[0]['total'],2,'.',',')); //A
			
			if($efectivos[0]['venta_puerta']<=0) $tpl->assign("venta_puerta","");
			else $tpl->assign("venta_puerta",number_format($efectivos[0]['venta_puerta'],2,'.',','));//B
			
			if($efectivos[0]['abono']<=0) $tpl->assign("abonos","");
			else $tpl->assign("abonos",number_format($efectivos[0]['abono'],2,'.',','));//C
			
			$otros = $efectivos[0]['otros']+$efectivos[0]['pastillaje'];
			if($otros<=0) $tpl->assign("otros","");
			else $tpl->assign("otros",number_format($otros,2,'.',','));//D
			
			if($ingresos<=0) $tpl->assign("ingresos","");
			else $tpl->assign("ingresos",number_format($ingresos,2,'.',','));//E
			
			if($efectivos[0]['raya_pagada']<=0) $tpl->assign("raya","");
			else $tpl->assign("raya",number_format($efectivos[0]['raya_pagada'],2,'.',','));//F
			
			if($gastos[0]['empleados']<=0) $tpl->assign("sueldo_emp","");
			else $tpl->assign("sueldo_emp",number_format($gastos[0]['empleados'],2,'.',','));//G
			
			if($gastos[0]['encargado']<=0) $tpl->assign("sueldo_enc","");
			else $tpl->assign("sueldo_enc",number_format($gastos[0]['encargado'],2,'.',','));//H
			
			if($gastos[0]['panadero']<=0) $tpl->assign("panadero","");
			else $tpl->assign("panadero",number_format($gastos[0]['panadero'],2,'.',','));//I
			
			if($gastos[0]['otros']<=0) $tpl->assign("otros2","");
			else $tpl->assign("otros2",number_format($gastos[0]['otros'],2,'.',','));//J
			
			if($efectivos[0]['gastos']<=0) $tpl->assign("gastos","");
			else $tpl->assign("gastos",number_format($efectivos[0]['gastos'],2,'.',','));//K

			if($efectivos[0]['efectivo']==0) $tpl->assign("efectivos","");
			else $tpl->assign("efectivos",number_format($efectivos[0]['efectivo'],2,'.',','));
			
			if($efe[0]['ctes']==0) $tpl->assign("clientes","");
			else $tpl->assign("clientes",number_format($efe[0]['ctes'],0,'.',','));//L

			$total_abono += $efectivos[0]['abono'];
			$total_produccion += $produccion[0]['total'];
			$total_venta_pta += $efectivos[0]['venta_puerta'];
			$total_abonos += $efectivos[0]['abono'];
			$total_otros += $otros;
			$total_ingresos += $ingresos;
			$total_raya += $efectivos[0]['raya_pagada'];
			$total_empleados += $gastos[0]['empleados'];
			$total_encargados += $gastos[0]['encargado'];
			$total_panaderos += $gastos[0]['panadero'];
			$total_otros2 += $gastos[0]['otros'];
			$total_gastos += $efectivos[0]['gastos'];
			$total_efectivo += $efectivos[0]['efectivo'];
			$total_clientes +=$efe[0]['ctes'];
		}

		$promedio_produccion =$total_produccion/$var;
		$promedio_venta_pta = $total_venta_pta/$var;
		$promedio_abonos=$total_abonos/$var;
		$promedio_otros=$total_otros/$var;
		$promedio_ingresos=$total_ingresos/$var;
		$promedio_raya=$total_raya/$var;
		$promedio_empleados=$total_empleados/$var;
		$promedio_encargados=$total_encargados/$var;
		$promedio_panaderos=$total_panaderos/$var;
		$promedio_otros2=$total_otros2/$var;
		$promedio_gastos=$total_gastos/$var;
		$promedio_efectivo=$total_efectivo/$var;
		$promedio_clientes=$total_clientes/$var;

		$tpl->gotoBlock("compania");
		$tpl->assign("total_produccion",number_format($total_produccion,2,'.',','));
		$tpl->assign("total_venta_pta",number_format($total_venta_pta,2,'.',','));
		$tpl->assign("total_abonos",number_format($total_abonos,2,'.',','));
		$tpl->assign("total_otros",number_format($total_otros,2,'.',','));
		$tpl->assign("total_ingresos",number_format($total_ingresos,2,'.',','));
		$tpl->assign("total_raya",number_format($total_raya,2,'.',','));
		$tpl->assign("total_empleados",number_format($total_empleados,2,'.',','));
		$tpl->assign("total_encargados",number_format($total_encargados,2,'.',','));
		$tpl->assign("total_panaderos",number_format($total_panaderos,2,'.',','));
		$tpl->assign("total_otros2",number_format($total_otros2,2,'.',','));
		$tpl->assign("total_gastos",number_format($total_gastos,2,'.',','));
		$tpl->assign("total_efectivo",number_format($total_efectivo,2,'.',','));
		$tpl->assign("total_clientes",number_format($total_clientes,2,'.',','));

		$tpl->assign("promedio_produccion",number_format($promedio_produccion,2,'.',','));
		$tpl->assign("promedio_venta",number_format($promedio_venta_pta,2,'.',','));
		$tpl->assign("promedio_abonos",number_format($promedio_abonos,2,'.',','));
		$tpl->assign("promedio_otros",number_format($promedio_otros,2,'.',','));
		$tpl->assign("promedio_ingresos",number_format($promedio_ingresos,2,'.',','));
		$tpl->assign("promedio_raya",number_format($promedio_raya,2,'.',','));
		$tpl->assign("promedio_emp",number_format($promedio_empleados,2,'.',','));
		$tpl->assign("promedio_enc",number_format($promedio_encargados,2,'.',','));
		$tpl->assign("promedio_pan",number_format($promedio_panaderos,2,'.',','));
		$tpl->assign("promedio_otros2",number_format($promedio_otros2,2,'.',','));
		$tpl->assign("promedio_gastos",number_format($promedio_gastos,2,'.',','));
		$tpl->assign("promedio_efectivo",number_format($promedio_efectivo,2,'.',','));
		$tpl->assign("promedio_clientes",number_format($promedio_clientes,2,'.',','));

	}
	
	else if($_GET['tipo_total']==1)
	{
		$tpl->newBlock("totales");
		$dia_1=explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1[1]]);
		$tpl->assign("hora",date("G:i:s"));
		
		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
	
		for($i=0;$i<$cont;$i++){
			$tpl->newBlock("rows");
			$var++;
			$dia=explode("/",$fechas[$i]['fecha_total']);
			$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
			
			$sobrante = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),"","",$dsn);
			
			//OBTIENE LOS VALORES DE PAGO A EMPLEADOS, PAGO A ENCARGADOS Y PAGO A PANADEROS (EMPLEADOS,ENCARGADO,PANADERO,OTROS)
			$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=1  and fecha ='".$fechas[$i]['fecha_total']."') as empleados, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=2  and fecha ='".$fechas[$i]['fecha_total']."') as encargado, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=3  and fecha ='".$fechas[$i]['fecha_total']."') as panadero, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos not in(1,2,3,30) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as otros
			FROM movimiento_gastos
			WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
			$gastos=ejecutar_script($sql2,$dsn);
			
			$ingresos=$efectivos[0]['venta_puerta']+$efectivos[0]['otros']+$expendios[0]['abono']+$efectivos[0]['pastillaje'];
			$otros = $efectivos[0]['otros']+$efectivos[0]['pastillaje'];
			$sql="SELECT ctes FROM captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha='".$companias[$j]['num_cia']."'";
			$efe=ejecutar_script($sql,$dsn);
			
			//PRODUCCION INCLUYE TOTAL PRODUCCION Y TOTAL RAYA (TOTAL,RAYA)
			$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
			FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
			$produccion=ejecutar_script($sql,$dsn);
			

			$total_gastos += $efectivos[0]['gastos'];
			$total_abono += $efectivos[0]['abono'];
			$total_produccion += $produccion[0]['total'];
			$total_venta_pta += $efectivos[0]['venta_puerta'];
			$total_abonos +=$efectivos[0]['abono'];
			$total_otros +=$otros;
			$total_ingresos += $ingresos;
			$total_raya +=$efectivos[0]['raya_pagada'];
			$total_empleados +=$gastos[0]['empleados'];
			$total_encargados +=$gastos[0]['encargado'];
			$total_panaderos +=$gastos[0]['panadero'];
			$total_otros2 +=$gastos[0]['otros'];
			$total_gastos +=$efectivos[0]['gastos'];
			$total_efectivo += $efectivos[0]['efectivo'];
			$total_clientes +=$efe[0]['ctes'];
			$efec=0;
		}

		$promedio_produccion =$total_produccion/$var;
		$promedio_venta_pta = $total_venta_pta/$var;
		$promedio_abonos=$total_abonos/$var;
		$promedio_otros=$total_otros/$var;
		$promedio_ingresos=$total_ingresos/$var;
		$promedio_raya=$total_raya/$var;
		$promedio_empleados=$total_empleados/$var;
		$promedio_encargados=$total_encargados/$var;
		$promedio_panaderos=$total_panaderos/$var;
		$promedio_otros2=$total_otros2/$var;
		$promedio_gastos=$total_gastos/$var;
		$promedio_efectivo=$total_efectivo/$var;
		$promedio_clientes=$total_clientes/$var;

		$tpl->gotoBlock("totales");
		$tpl->assign("total_produccion",number_format($total_produccion,2,'.',','));
		$tpl->assign("total_venta",number_format($total_venta_pta,2,'.',','));
		$tpl->assign("total_abonos",number_format($total_abonos,2,'.',','));
		$tpl->assign("total_otros",number_format($total_otros,2,'.',','));
		$tpl->assign("total_ingreso",number_format($total_ingresos,2,'.',','));
		$tpl->assign("total_raya",number_format($total_raya,2,'.',','));
		$tpl->assign("total_empleados",number_format($total_empleados,2,'.',','));
		$tpl->assign("total_encargados",number_format($total_encargados,2,'.',','));
		$tpl->assign("total_panaderos",number_format($total_panaderos,2,'.',','));
		$tpl->assign("total_otros2",number_format($total_otros2,2,'.',','));
		$tpl->assign("total_gastos",number_format($total_gastos,2,'.',','));
		$tpl->assign("total_efectivo",number_format($total_efectivo,2,'.',','));
		$tpl->assign("total_clientes",number_format($total_clientes,2,'.',','));

		$tpl->assign("promedio_produccion",number_format($promedio_produccion,2,'.',','));
		$tpl->assign("promedio_venta",number_format($promedio_venta_pta,2,'.',','));
		$tpl->assign("promedio_abonos",number_format($promedio_abonos,2,'.',','));
		$tpl->assign("promedio_otros",number_format($promedio_otros,2,'.',','));
		$tpl->assign("promedio_ingresos",number_format($promedio_ingresos,2,'.',','));
		$tpl->assign("promedio_raya",number_format($promedio_raya,2,'.',','));
		$tpl->assign("promedio_emp",number_format($promedio_empleados,2,'.',','));
		$tpl->assign("promedio_enc",number_format($promedio_encargados,2,'.',','));
		$tpl->assign("promedio_pan",number_format($promedio_panaderos,2,'.',','));
		$tpl->assign("promedio_otros2",number_format($promedio_otros2,2,'.',','));
		$tpl->assign("promedio_gastos",number_format($promedio_gastos,2,'.',','));
		$tpl->assign("promedio_efectivo",number_format($promedio_efectivo,2,'.',','));
		$tpl->assign("promedio_clientes",number_format($promedio_clientes,2,'.',','));
	}
	if($salto % 2 == 0)
		$tpl->newBlock("salto");
	$salto++;
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>