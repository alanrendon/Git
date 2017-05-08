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
$descripcion_error[1] = "No hay registros";


// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_hoja_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
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

// -------------------------------- Mostrar listado ---------------------------------------------------------

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

$hoja=false;
$compra=false;
if(existe_registro("hoja_diaria_rost",array("num_cia","fecha"),array($_GET['cia'],$_GET['fecha']),$dsn))
	$hoja=true;
if(existe_registro("compra_directa",array("num_cia","fecha_mov"),array($_GET['cia'],$_GET['fecha']),$dsn))
	$compra=true;

if($compra==false and $hoja==false){
	header("location: ./ros_hoja_con.php?codigo_error=1");
	die();
}
/*
$sql="select num_cia, existencia, inventario_real.codmp, nombre from inventario_real 
join catalogo_mat_primas on(inventario_real.codmp=catalogo_mat_primas.codmp) 
where num_cia=".$_GET['cia']." and existencia is not null order by orden";
*/
$sql="select distinct(codmp), nombre,orden from mov_inv_real JOIN catalogo_mat_primas using(codmp) where num_cia=$_GET[cia] and fecha='$_GET[fecha]' order by orden";
$productos=ejecutar_script($sql,$dsn);

//-------------------------BLOQUE DE CALCULO DE FECHAS
$fecha=explode("/",$_GET['fecha']);
$total_venta=0;
$dia=$fecha[0]-1;
$fecha_atras=date("d/m/Y", mktime(0,0,0,$fecha[1],$dia,$fecha[2]));
$fecha_inicio_mes="1/".$fecha[1]."/".date("Y");
$fecha_mes_anterior=date("d/m/Y",mktime(0,0,0,$fecha[1],0,$fecha[2]));

//-------------------------LISTADO
$tpl->newBlock("listado_dia");
$tpl->assign("num_cia", $_GET['cia']);
$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
$tpl->assign("dia",$fecha[0]);
$tpl->assign("mes",$nombremes[$fecha[1]]);
$tpl->assign("anio",$fecha[2]);

//DATOS DE GASTOS
$gas="select * from movimiento_gastos where num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' and captura=false and codgastos not in(33)";
//GASTOS DESGLOZADOS
$gastos=ejecutar_script($gas,$dsn);
$sql="select sum(importe) from movimiento_gastos where num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' and captura=false and codgastos not in(33)";
//TOTAL DE GASTOS
$total_gastos=ejecutar_script($sql,$dsn);
$tot_gasto=0;

//DATOS DE PRESTAMOS
$sql="select prestamos.num_cia, prestamos.id_empleado, fecha, importe, nombre || ' ' || ap_paterno || ' ' || ap_materno as nombre, tipo_mov from prestamos join catalogo_trabajadores on(prestamos.id_empleado = catalogo_trabajadores.id) where fecha='".$_GET['fecha']."' and prestamos.num_cia=".$_GET['cia'];
$prestamos=ejecutar_script($sql,$dsn);


for($i=0;$i<count($productos);$i++)
{
	$existencia=0;
	$total=0;
	$sobrante=0;
	
	//DATOS DE HOJA DIARIA, COMPRAS DIRECTA, INVENTARIO REAL, MOVIMIENTO DE INVENTARIOS
	$hd=ejecutar_script("select * from hoja_diaria_rost WHERE num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' and codmp=".$productos[$i]['codmp'],$dsn);
	$mov=ejecutar_script("select * from mov_inv_real WHERE num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' and codmp=".$productos[$i]['codmp'],$dsn);

	$com="select sum(cantidad) from mov_inv_real where fecha >='".$fecha_inicio_mes."' and fecha < '".$_GET['fecha']."' and tipo_mov = false and num_cia=".$_GET['cia']." and codmp=".$productos[$i]['codmp'];
	$compras=ejecutar_script($com,$dsn);
	$ven="select sum(cantidad) from mov_inv_real where fecha >='".$fecha_inicio_mes."' and fecha < '".$_GET['fecha']."' and tipo_mov = true and num_cia=".$_GET['cia']." and codmp=".$productos[$i]['codmp'];
	$ventas=ejecutar_script($ven,$dsn);
	$inv="select * from inventario_fin_mes where num_cia=".$_GET['cia']." and fecha='".$fecha_mes_anterior."' and codmp=".$productos[$i]['codmp'];
	$inventario=ejecutar_script($inv,$dsn);

	$existencia=number_format($compras[0]['sum'],2,'.','')+number_format($inventario[0]['inventario'],2,'.','')-number_format($ventas[0]['sum'],2,'.','');

//	if($existencia <=0) continue;
	if($productos[$i]['codmp']==90) continue;
	
	//BLOQUE DE HOJA DIARIA
	$tpl->newBlock("fila");
	$tpl->assign("codmp",$productos[$i]['codmp']);
	$tpl->assign("nom_mp",$productos[$i]['nombre']);
	$tpl->assign("existencia",$existencia);
	$total=$existencia;
	$sobrante=$existencia;

//--------------------------------
	for($j=0;$j<count($mov);$j++)
	{
		if($mov[$j]['tipo_mov']=='f') 
		{
			$tpl->assign("compra",$mov[$j]['cantidad']);
			$total += $mov[$j]['cantidad'];
			$sobrante += $mov[$j]['cantidad'];
		}
		else{ 
			$tpl->assign("venta",$mov[$j]['cantidad']);
			$tpl->assign("total_vendido",$mov[$j]['cantidad']);
			$sobrante -= $mov[$j]['cantidad'];
		}
	}
	if($hd[0]['precio_unitario']<=0) $tpl->assign("precio_venta"," ");
	else $tpl->assign("precio_venta",number_format($hd[0]['precio_unitario'],2,'.',','));
	
	if($hd[0]['precio_total']<=0) $tpl->assign("importe_venta"," ");
	else $tpl->assign("importe_venta",number_format($hd[0]['precio_total'],2,'.',','));

	$tpl->assign("total",$total);
	$tpl->assign("sobrante",$sobrante);
	$total_venta += $hd[0]['precio_total'];//VENTA TOTAL DENTRO DE LA HOJA DIARIA
	
}
//---------------------------

$tpl->gotoBlock("listado_dia");
$sql="select * from total_companias where num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."'";
$otros=ejecutar_script($sql,$dsn);
$aux=0;
$efectivo=0;
$total_de_gasto=0;
$total_de_gasto = number_format($total_gastos[0]['sum'],2,'.','');

	for($j=0;$j<count($prestamos);$j++)
	{
		if($prestamos[$j]['tipo_mov']=='t')
			$total_venta += $prestamos[$j]['importe'];
		else
			$total_de_gasto += $prestamos[$j]['importe'];
	}

$efectivo=$total_venta - $total_de_gasto;

//INSERTA EL RENGLON CORRESPONDIENTE A   O  T  R  O  S

$aux=number_format($otros[0]['venta'],2,'.','') - number_format($total_venta,2,'.','');
if($aux > 0)
{
	$tpl->newBlock("otros");
	$tpl->assign("otros",number_format($aux,2,'.',''));
	$total_venta += $aux;
	$efectivo += $aux;
}

$tpl->gotoBlock("listado_dia");

//TOTALES
$tpl->assign("total_venta",number_format($total_venta,2,'.',','));
//$tpl->assign("total_gastos",number_format($total_gastos[0]['sum'],2,'.',','));
$tpl->assign("total_gastos",number_format($total_de_gasto,2,'.',','));

$tpl->assign("efectivo",number_format($efectivo,2,'.',','));
//--------------

//SI EXISTEN GASTOS CREA EL BLOQUE CORRESPONDIENTE A ELLOS
if($gastos)
{
	$tpl->newBlock("gastos");
	for($i=0;$i<count($gastos);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("codgastos",$gastos[$i]['codgastos']);

		$tpl->assign("importe",number_format($gastos[$i]['importe'],2,'.',','));
		$tpl->assign("concepto",$gastos[$i]['concepto']);
		$nombre=obtener_registro("catalogo_gastos", array('codgastos'),array($gastos[$i]['codgastos']),"","",$dsn);
		$tpl->assign("nom_gasto",$nombre[0]['descripcion']);
		$tot_gasto+=$gastos[$i]['importe'];
	}
	$tpl->gotoBlock("gastos");
	$tpl->assign("total_gastos",number_format($tot_gasto,2,'.',','));
}

//SI EXISTEN PRESTAMOS O PAGOS DE PRESTAMOS
if($prestamos)
{
	$tpl->newBlock("prestamos");
	for($i=0;$i<count($prestamos);$i++)
	{
		$tpl->newBlock("renglones");
		$tpl->assign("nombre",$prestamos[$i]['nombre']);
		if($prestamos[$i]['tipo_mov']=='t')
			$tpl->assign("abono",number_format($prestamos[$i]['importe'],2,'.',','));
		else
			$tpl->assign("cantidad",number_format($prestamos[$i]['importe'],2,'.',','));
	}
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>