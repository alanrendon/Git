<?php
define ('IDSCREEN',6213); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
///$tpl->assign("tabla",$session->tabla);

if(isset($_POST['contador'])){
	$tpl->newBlock("listado");
	$tpl->assign("fecha",$_POST['fecha0']);
	$var=0;
	$ingresos_cap=0;
	$egresos_cap=0;
	$gran_total=0;
	for($i=0;$i<$_POST['contador'];$i++){
		if($_POST['num_cia'.$i] !=""){
			$tpl->newBlock("listado_rows");
			$tpl->assign("i",$var);
			$var++;
			$tpl->assign("num_cia",$_POST['num_cia'.$i]);
			$tpl->assign("concepto",$_POST['concepto'.$i]);
			$tpl->assign("importe",number_format($_POST['importe'.$i],2,'.',''));
			$tpl->assign("importe1",number_format($_POST['importe'.$i],2,'.',','));
			$tpl->assign("balance",$_POST['balance'.$i]);
			$tpl->assign("fecha",$_POST['fecha'.$i]);
			$tpl->assign("tipo",$_POST['tipo_mov'.$i]);
			$tpl->assign("nombre_cia",$_POST['nombre_cia'.$i]);
			$tpl->assign("comentario",strtoupper($_POST['comentario'.$i]));
			
			if($_POST['balance'.$i]==true)
				$tpl->assign("balance1","Afecta a balances");
			else
				$tpl->assign("balance2","No afecta a balances");
			
			if($_POST['tipo_mov'.$i]==0){
				$tpl->assign("tipo1","Egreso");
				$egresos_cap += number_format($_POST['importe'.$i],2,'.','');
			}
			else{
				$tpl->assign("tipo1","Ingreso");
				$ingresos_cap += number_format($_POST['importe'.$i],2,'.','');
			}
				
			$concepto=obtener_registro("catalogo_gastos_caja",array("id"),array($_POST['concepto'.$i]),"","",$dsn);
			if($_POST['comentario'.$i]!="")
				$tpl->assign("concepto1",strtoupper($_POST['comentario'.$i]));
			else
				$tpl->assign("concepto1",$concepto[0]['descripcion']);
		}
	}
	$tpl->gotoBlock("listado");
	$fecha=explode("/",$_POST['fecha0']);
	
	
	//DATOS EXISTENTES EN LA TABLA DE GASTOS DE CAJA PARA SUMAR TOTALES ANTES DE INSERTAR LOS DATOS NUEVOS
	$sql="select sum(importe) as egresos from gastos_caja where fecha between '1/".$fecha[1]."/".$fecha[2]."' and '".$_POST['fecha0']."' and tipo_mov=false";
	$egreso=ejecutar_script($sql,$dsn);
	
	$sql="select sum(importe) as ingresos from gastos_caja where fecha between '1/".$fecha[1]."/".$fecha[2]."' and '".$_POST['fecha0']."' and tipo_mov=true";
	$ingreso=ejecutar_script($sql,$dsn);
	//TOTAL DE LOS INGRESOS MENOS LOS EGRESOS DE LA TABLA
	$total_anterior=number_format($ingreso[0]['ingresos'],2,'.','') - number_format($egreso[0]['egresos'],2,'.','');
	$tpl->assign("total_anterior",number_format($total_anterior,2,'.',','));
	
	$total_ingresos=number_format($ingreso[0]['ingresos'],2,'.','') + number_format($ingresos_cap,2,'.','');
	$total_egresos=number_format($egreso[0]['egresos'],2,'.','') + number_format($egresos_cap,2,'.','');
	
	$total_total=$total_ingresos - $total_egresos;
	$gran_total=$ingresos_cap-$egresos_cap;
	$tpl->assign("contador",$var);
	$tpl->assign("total_egresos",number_format($egresos_cap,2,'.',','));
	$tpl->assign("total_ingresos",number_format($ingresos_cap,2,'.',','));
	$tpl->assign("gran_total",number_format($gran_total,2,'.',','));
	$tpl->assign("total_total",number_format($total_total,2,'.',','));
	$tpl->printToScreen();
	die();
}

// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_gastos'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("fecha",date("d/m/Y"));
	$tpl->assign("anio_actual",date("Y"));
//-----------------------
$db = DB::connect($dsn);
if (DB::isError($db)) 
{
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM catalogo_gastos_caja ORDER BY descripcion ASC";
$result = $db->query($sql);
$db->disconnect();
if (DB::isError($result)) 
{
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) 
{
	$tpl->newBlock("codigo1");
	$tpl->assign("num_gasto",$row->id);
	$tpl->assign("descripcion",$row->descripcion);
}
//----------
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
//------------------------------------------------Gastos Caja------------------------------------------------------------
$tpl->newBlock("gastos_caja");

$tpl->assign("tabla","gastos_caja");
$tpl->assign("fecha",$_GET['fecha_i']);
$tpl->assign("contador",$_GET['num_gastos']);

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	if ($cia[$i]['num_cia'] > 0 && $cia[$i]['num_cia'] < 201 || $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia']==704 || $cia[$i]['num_cia']==703 || $cia[$i]['num_cia']==999) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
}
if($_GET['balance']==0)
	$tpl->assign("selec1","selected");
else
	$tpl->assign("selec","selected");

for ($i=0;$i<$_GET['num_gastos'];$i++) 
{
	$tpl->newBlock("rows");
	if($_GET['balance']==0)
		$tpl->assign("selec1","selected");
	else
		$tpl->assign("selec","selected");

	if($_GET['tipo_mov']==0)
	{
		$tpl->assign("sel","checked");
		$tpl->assign("operador","0");
	}
	else
	{
		$tpl->assign("sel1","checked");
		$tpl->assign("operador","1");
	}
	$tpl->assign("i",$i);
	if(($i+1)==$_GET['num_gastos'])
		$tpl->assign("next",0);
	else
		$tpl->assign("next",$i+1);
	$tpl->assign("fecha",$_GET['fecha_i']);
	$tpl->assign("contador",$_GET['num_gastos']);
			// Generar listado de catalogo
			$db = DB::connect($dsn);
			if (DB::isError($db)) 
			{
				echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
				die($db->getMessage());
			}
			
			$sql = "SELECT * FROM catalogo_gastos_caja ORDER BY descripcion ASC";
			$result = $db->query($sql);
			$db->disconnect();
			if (DB::isError($result)) 
			{
				$db->disconnect();
				echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
				die($result->getMessage());
			}
			while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) 
			{
				$tpl->newBlock("codigo");
				$tpl->assign("num_gasto",$row->id);
				$tpl->assign("descripcion",$row->descripcion);
				if ($_GET['concepto']==$row->id) $tpl->assign("checked","selected");
			}
//	$tpl->gotoBlock("_ROOT");
}

// Imprimir el resultado
$tpl->printToScreen();
?>