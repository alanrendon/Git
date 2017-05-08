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
$descripcion_error[2] = "No existe el accionista";
$descripcion_error[3] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/adm/admin_porc_list.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	
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


//LA CONSULTA SERA POR NUMERO DE COMPAÑÍA
if($_GET['con']==0)
{
	$paginas=0;
	$aux=0;
	$aux1=0;
	$salto=0;
//	$contador2=0;
	$r=0;
	if($_GET['cia']!="")
	{
		if(!existe_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),$dsn))//SI NO EXISTE COMPAÑÍA MANDA CODIGO DE ERROR
		{
			header("location: ./admin_porc_list.php?codigo_error=1");
			die();
		}
		if($_GET['acc']==0)
		{
			$sql="SELECT * FROM accionistas where num_cia=".$_GET['cia']." order by accionista";
			$registro=ejecutar_script($sql,$dsn);
		}
		else 
		{
			$sql="SELECT * FROM distribuciones where num_cia=".$_GET['cia']. " order by accionista";
			$registro=ejecutar_script($sql,$dsn);
		}
		
		if(!$registro)//SI NO EXISTEN REGISTROS MANDA CODIGO DE ERROR
		{
			header("location: ./admin_porc_list.php?codigo_error=3");
			die();
		}
		
		$tpl->newBlock("compania");
		if($_GET['acc']==0) $tpl->assign("consulta","Listado de porcentajes de Accionistas");
		else $tpl->assign("consulta","Listado de porcentajes de Distribuciones");
		$e=(-1);
		for($i=0;$i<count($registro);$i++)
		{
			if($e!=$registro[$i]['num_cia'])
			{
				$tpl->newBlock("cia");
				$tpl->assign("num_cia",$registro[$i]['num_cia']);
				$nombre_cia=obtener_registro("catalogo_companias",array("num_cia"),array($registro[$i]['num_cia']),"","",$dsn);
				$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
			}
			$tpl->newBlock("accionista_registro");
			$nombre_acc=obtener_registro("catalogo_accionistas",array("num"),array($registro[$i]['accionista']),"","",$dsn);		
			$tpl->assign("accionista",$nombre_acc[0]['nombre_corto']);
			$tpl->assign("porcentaje",$registro[$i]['porcentaje']);
			$e=$registro[$i]['num_cia'];
		}
	}
//*******************************************************************************
	else
	{
		if($_GET['acc']==0) //CONSULTA POR COMPAÑÍA DE PORCENTAJES DE ACCIONISTAS
		{
			$sql="select count(num_cia)from (select distinct(num_cia) from accionistas) as consulta";
			$contador=ejecutar_script($sql,$dsn);
			$sql="select distinct(num_cia) from accionistas order by num_cia";
			$cia=ejecutar_script($sql,$dsn);
		}
		else
		{
			$sql="select count(num_cia)from (select distinct(num_cia) from distribuciones) as consulta";
			$contador=ejecutar_script($sql,$dsn);
			$sql="select distinct(num_cia) from distribuciones order by num_cia";
			$cia=ejecutar_script($sql,$dsn);

		}
		
		$paginas = ($contador[0]['count']) / 8;
		$paginas=ceil($paginas);
		$aux=$cia[0]['num_cia'];
		
		if($contador[0]['count'] < 7){
			$salto=$contador[0]['count'];
			$aux1=$cia[$contador[0]['count']-1]['num_cia'];
			}
		else{
			$salto=7;
			$aux1=$cia[7]['num_cia'];
			}
//		echo $paginas."<br>";
		for($i=0; $i<$paginas; $i++)
		{
//			echo "de la $aux a la $aux1 <br>";
			if($_GET['acc']==0)
				$sql="SELECT * FROM accionistas where num_cia between ".$aux." and ".$aux1." order by num_cia";
			else
				$sql="SELECT * FROM distribuciones where num_cia between ".$aux." and ".$aux1." order by num_cia";
				

			$cias2=ejecutar_script($sql,$dsn);
			if(!$cias2)
			{
				header("location: ./admin_porc_list.php?codigo_error=3");
				die();
			}
			$tpl->newBlock("compania");
			if($_GET['acc']==0) $tpl->assign("consulta","Listado de porcentajes de Accionistas");
			else $tpl->assign("consulta","Listado de porcentajes de Distribuciones");
			$e=(-1);
			for($j=0;$j<count($cias2);$j++)
			{
				if($e!=$cias2[$j]['num_cia'])
				{
					$tpl->newBlock("cia");
					$tpl->assign("num_cia",$cias2[$j]['num_cia']);
					$nombre_cia=obtener_registro("catalogo_companias",array("num_cia"),array($cias2[$j]['num_cia']),"","",$dsn);
					$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
				}
				$tpl->newBlock("accionista_registro");
				$nombre_acc=obtener_registro("catalogo_accionistas",array("num"),array($cias2[$j]['accionista']),"","",$dsn);		
				$tpl->assign("accionista",$nombre_acc[0]['nombre_corto']);
				$tpl->assign("porcentaje",$cias2[$j]['porcentaje']);
				$e=$cias2[$j]['num_cia'];
			}
			
			if(($salto+1) >= $contador[0]['count'])
				$aux=$cia[$contador[0]['count']-1]['num_cia'];
			else
				$aux=$cia[$salto+1]['num_cia'];

			if(($salto+8) > $contador[0]['count'])
				$aux1=$cia[$contador[0]['count'] -1]['num_cia'];
			else{
				if(($salto+1) >= $contador[0]['count'])
					$aux1=$cia[$contador[0]['count'] -1]['num_cia'];
				else
					$aux1=$cia[$salto+8]['num_cia'];
			}
//			echo "$aux ; $aux1 <br>";
				
			$salto+=8;
		}
	}
}
//**********************************************************************************************************************************
else if($_GET['con']==1)
{
	if(!existe_registro("catalogo_accionistas",array("num"),array($_GET['accionista']),$dsn))
	{
		header("location: ./admin_porc_list.php?codigo_error=2");
		die();
	}
	if($_GET['acc']==0)
	{
		$sql="SELECT * from accionistas where accionista=".$_GET['accionista']." order by num_cia";
		$registro=ejecutar_script($sql,$dsn);
	}
	else if ($_GET['acc']==1)
	{
		$sql="SELECT * from distribuciones where accionista=".$_GET['accionista']." order by num_cia";
		$registro=ejecutar_script($sql,$dsn);
	}
	
	if(!$registro)
	{
		header("location: ./admin_porc_list.php?codigo_error=3");
		die();
	}

//print_r($registro);
	$tpl->newBlock("accionista");
	if($_GET['acc']==0) $tpl->assign("consulta","Listado de porcentajes de Accionistas");
	else $tpl->assign("consulta","Listado de porcentajes de Distribuciones");
	$e=(-1);
	
	for($i=0;$i<count($registro);$i++)
	{
		if($e!=$registro[$i]['accionista'])
		{
			$tpl->newBlock("accion");
			$nombre_acc=obtener_registro("catalogo_accionistas",array("num"),array($registro[$i]['accionista']),"","",$dsn);		
			$tpl->assign("accionista",$nombre_acc[0]['nombre_corto']);
		}
		$tpl->newBlock("cia_registro");
		$tpl->assign("porcentaje",$registro[$i]['porcentaje']);
		$tpl->assign("num_cia",$registro[$i]['num_cia']);
		$nombre_cia=obtener_registro("catalogo_companias",array("num_cia"),array($registro[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);

		$e=$registro[$i]['accionista'];
	}

}


$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>