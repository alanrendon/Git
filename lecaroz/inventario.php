<?php
// CAPTURA DE INVENTARIO
// Tabla 'inventario_real' e 'inventario_virtual'
// Menu 'No definido'

define ('IDSCREEN',1); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "Fecha de captura ya se encuentra en el sistema";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Insertar datos en la base -----------------------------------------------
if (isset($_GET['ok'])) 
{
	$var=0;
	$var1=0;
	$var2=0;
	for($i=0;$i<100;$i++)
	{
		if($_POST['codmp'.$i]!="" && $_POST['precio_unidad'.$i] > 0)
		{
			if(existe_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['compania'],$_POST['codmp'.$i]),$dsn))
			{
				$sql="UPDATE inventario_virtual SET existencia=".(trim($_POST['existencia'.$i]) != '' ? $_POST['existencia'.$i] : 0).", precio_unidad=".$_POST['precio_unidad'.$i]." WHERE num_cia=".$_POST['compania']." AND codmp=".$_POST['codmp'.$i];
				ejecutar_script($sql,$dsn);
			}
			else
			{
				$inv_V['codmp'.$var]=$_POST['codmp'.$i];
				$inv_V['num_cia'.$var]=$_POST['compania'];
				$inv_V['fecha_entrada'.$var]=$_POST['fecha'];
				$inv_V['fecha_salida'.$var]=$_POST['fecha'];
				
				if(trim($_POST['existencia'.$i])=="")
					$inv_V['existencia'.$var] = '0';
				else
					$inv_V['existencia'.$var]=$_POST['existencia'.$i];
					
				$inv_V['precio_unidad'.$var]=$_POST['precio_unidad'.$i];
				$var++;
			}
			if(existe_registro("inventario_real",array("num_cia","codmp"),array($_POST['compania'],$_POST['codmp'.$i]),$dsn))
				continue;
			else
			{
				$inv_R['codmp'.$var1]=$_POST['codmp'.$i];
				$inv_R['num_cia'.$var1]=$_POST['compania'];
				$inv_R['fecha_entrada'.$var1]=$_POST['fecha'];
				$inv_R['fecha_salida'.$var1]=$_POST['fecha'];

				if(trim($_POST['existencia'.$i])=="")
					$inv_R['existencia'.$var1] = '0';
				else
					$inv_R['existencia'.$var1]=$_POST['existencia'.$i];
				
				$inv_R['precio_unidad'.$var1]=$_POST['precio_unidad'.$i];
				$var1++;
			
			}
			if(existe_registro("historico_inventario",array("num_cia","codmp"),array($_POST['compania'],$_POST['codmp'.$i]),$dsn))
				continue;
			else
			{
				$inv_H['codmp'.$var2]=$_POST['codmp'.$i];
				$inv_H['num_cia'.$var2]=$_POST['compania'];
				$inv_H['fecha'.$var2]=$_POST['fecha'];
				if($_POST['existencia'.$i]=="")
					$inv_H['existencia'.$var2] = '0';
				else
					$inv_H['existencia'.$var2]=$_POST['existencia'.$i];
				$inv_H['precio_unidad'.$var2]=$_POST['precio_unidad'.$i];
				$var2++;
			}
		}
	}

if($var > 0){
	$inventario_virtual = new DBclass($dsn,"inventario_virtual",$inv_V);
	$inventario_virtual->xinsertar();
}
if($var1 > 0){
	$inventario_real = new DBclass($dsn,"inventario_real",$inv_R);
	$inventario_real->xinsertar();
}

if($var2 > 0){
	$inventario_historico = new DBclass($dsn,"historico_inventario",$inv_H);
	$inventario_historico->xinsertar();
}

	header("location: ./inventario.php");
}

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_compania");
	$tpl->assign("fecha",date("d/m/Y"));
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


// ---------------------------------- Trazar pantalla de captura ---------------------------------------------
// Verificar si existe la compañía
if (!$cia = obtener_registro("catalogo_companias", array("num_cia"), array($_GET['compania']), "", "", $dsn)) {
	header("location: ./inventario.php?codigo_error=1");
	die();
}
// Crear bloque de captura
$tpl->newBlock("captura");

$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_GET['fecha']);

// Obtener listado de materias primas
$mp = obtener_registro("catalogo_mat_primas",array(),array(),"codmp","ASC",$dsn);
// Obtener listado de unidades de consumo
$unidades = obtener_registro("tipo_unidad_consumo",array(),array(),"idunidad","ASC",$dsn);

for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("nombre_mp");
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre_mp",$mp[$i]['nombre']);
	$tpl->assign("unidad",$unidades[$mp[$i]['unidadconsumo']-1]['descripcion']);
}

$num_filas = 100;

for ($i=0; $i<$num_filas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);
}

$tpl->printToScreen();
?>
