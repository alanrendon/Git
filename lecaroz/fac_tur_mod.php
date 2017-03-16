<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para este proveedor";
//$descripcion_error[2] = "N�mero de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener informaci�n de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_tur_mod.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una p�gina que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->assign("tabla","catalogo_turnos");
$sql="select * from catalogo_horarios order by cod_horario";
$hor=ejecutar_script($sql,$dsn);

$var=0;
if($_POST['cont'] >0)
{
	for($i=0;$i<$_POST['cont'];$i++)
	{
		if($_POST['modificar'.$i]==1)
		{
			$tpl->newBlock("rows");
			$tpl->assign("i",$var);
			$tpl->assign("next",$var+1);
			$var++;
			$tpl->assign('cod_turno',$_POST['cod_turno'.$i]);
			$tpl->assign('descripcion',$_POST['descripcion'.$i]);
			for($j=0;$j<count($hor);$j++)
			{
				$tpl->newBlock("horario");
				$tpl->assign("cod_horario",$hor[$j]['cod_horario']);
				$tpl->assign("desc",$hor[$j]['descripcion']);
				if($_POST['cod_horario'.$i]==$hor[$j]['cod_horario']) $tpl->assign("selected","selected");
			}
		}
	}
	$tpl->newBlock("contador");
	$tpl->assign("cont",$var);
	
}
else
{
	header("location: ./fac_pue_con.php");
	die;

}
// Imprimir el resultado
$tpl->printToScreen();

?>