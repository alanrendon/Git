<?php
// CAPTURA DE MOVIMIENTO DE GASTOS
// Tabla 'movimiento_gastos'
// Menu 'Panaderias->Gastos'

//define ('IDSCREEN',1721); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

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
$tpl->assignInclude("body","./plantillas/pan/pan_mga_cap1.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","movimiento_gastos");

//empieza código para insertar un numero de renglones en un bloque
$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);

//VERIFICACION DE COMPAÑÍAS POR USUARIO

$i=0;
$tpl->assign("num_cia",$_POST['num_cia']);
$tpl->assign("fecha",$_POST['fecha']);
$tpl->assign("nombre_cia",$nomcia[0]['nombre_corto']);
$totales=0;
$ok=true;
$var=0;

$sql="select num_cia,idoperadora FROM catalogo_companias WHERE num_cia=".$_POST['num_cia'];
$id=ejecutar_script($sql,$dsn);

if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
	$ok=true;	
}

else{
	$operadora=obtener_registro("catalogo_operadoras",array("iduser"),array($_SESSION['iduser']),"","",$dsn);
	if($id[0]['idoperadora']!=$operadora[0]['idoperadora']){
		$ok=false;
		$tpl->assign("mensaje","ESTA COMPAÑÍA NO TE CORRESPONDE");
	}
}


// Almacenar datos en variables de sesion
if (isset($_SESSION['gastos'])) unset($_SESSION['gastos']);
$_SESSION['gastos']['num_cia'] = $_POST['num_cia'];
$_SESSION['gastos']['fecha'] = $_POST['fecha'];
for ($i=0; $i<25; $i++) {
	$_SESSION['gastos']['codgastos'.$i] = $_POST['codgastos'.$i];
	$_SESSION['gastos']['concepto'.$i] = $_POST['concepto'.$i];
	$_SESSION['gastos']['importe'.$i] = $_POST['importe'.$i];
}

$fecha1=explode("/",$_POST['fecha']);


for ($i=0;$i<25;$i++)
{
	if ($_POST['codgastos'.$i] != "")
	{
		$tpl->newBlock("rows");
		if(!(existe_registro("catalogo_gastos",array('codgastos'),array($_POST['codgastos'.$i]),$dsn)))
		{
			$tpl->newBlock("gasto_error");
			$tpl->assign("color","FF0000");
			$tpl->assign("codgastos",$_POST['codgastos'.$i]);
			$tpl->gotoBlock('rows');
			$ok &=false;
		}
//****************************************************************
		if($_POST['codgastos'.$i]==114)
		{
			if($_POST['num_cia']==28)
			{
				if(existe_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($_POST['num_cia'],$_POST['codgastos'.$i]),$dsn)){
					$limite=obtener_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($_POST['num_cia'],$_POST['codgastos'.$i]),"","",$dsn);

					$sql="select sum(importe) from movimiento_gastos where num_cia=".$_POST['num_cia']." and fecha between '1/".$fecha1[1]."/".$fecha1[2]."' and '".$_POST['fecha']."' and codgastos=2 and captura=false";
					$gastos=ejecutar_script($sql,$dsn);
					$total_acumulado=$gastos[0]['sum'] + $_POST['importe'.$i];

					if($limite[0]['limite']){
						$tpl->newBlock("gasto_ok");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=true;
					}
					else if($total_acumulado > $limite[0]['limite']){
						$tpl->newBlock("gasto_error");
						$tpl->assign("color","9900CC");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=false;
					}
					else{
						$tpl->newBlock("gasto_ok");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=true;
					}
				}
				else{
					$tpl->newBlock("gasto_ok");
					$tpl->assign("codgastos",$_POST['codgastos'.$i]);
					$ok &=true;
				}
				$tpl->gotoBlock("rows");
				$rows = obtener_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos'.$i]),"","",$dsn);
				$tpl->assign("i",$var);
				$var++;
				$tpl->assign("codgastos",$_POST['codgastos'.$i]);
				$tpl->assign("num_cia",$_POST['num_cia']);
				$tpl->assign("fecha",$_POST['fecha']);
				$tpl->assign("descripcion",$rows[0]['descripcion']);
				$tpl->assign("concepto",$_POST['concepto'.$i]);
				$tpl->assign("importe", $_POST['importe'.$i]);
				$tpl->assign("importe2", number_format($_POST['importe'.$i],2,'.',','));
				$totales += $_POST['importe'.$i];
				

			}
			else
			{
				$tpl->newBlock("gasto_error");
				$tpl->assign("color","FF0000");
				$tpl->assign("codgastos",$_POST['codgastos'.$i]);
				$tpl->gotoBlock("rows");
				$rows = obtener_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos'.$i]),"","",$dsn);
				$tpl->assign("i",$var);
				$var++;
				$tpl->assign("codgastos",$_POST['codgastos'.$i]);
				$tpl->assign("num_cia",$_POST['num_cia']);
				$tpl->assign("fecha",$_POST['fecha']);
				$tpl->assign("descripcion",$rows[0]['descripcion']);
				$tpl->assign("concepto",$_POST['concepto'.$i]);
				$tpl->assign("importe", $_POST['importe'.$i]);
				$tpl->assign("importe2", number_format($_POST['importe'.$i],2,'.',','));
				$totales += $_POST['importe'.$i];
				$ok &=false;
			}
			if(!(existe_registro("catalogo_gastos",array('codgastos'),array($_POST['codgastos'.$i]),$dsn)))
			{
				$tpl->newBlock("gasto_error");
				$tpl->assign("color","FF0000");
				$tpl->assign("codgastos",$_POST['codgastos'.$i]);
				$tpl->gotoBlock('rows');
				$ok &=false;
			}

		}
//*************************************************************************		
		else{
				if(existe_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($_POST['num_cia'],$_POST['codgastos'.$i]),$dsn)){
					$limite=obtener_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($_POST['num_cia'],$_POST['codgastos'.$i]),"","",$dsn);
					
					$sql="select sum(importe) from movimiento_gastos where num_cia=".$_POST['num_cia']." and fecha between '1/".$fecha1[1]."/".$fecha1[2]."' and '".$_POST['fecha']."' and codgastos=".$_POST['codgastos'.$i]." and captura=false";
					$gastos_acumulados=ejecutar_script($sql,$dsn);
					$total_acumulado=$gastos_acumulados[0]['sum'] + $_POST['importe'.$i];
					
					if($limite[0]['limite']==0){
						$tpl->newBlock("gasto_ok");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=true;
					}
					else if($total_acumulado > $limite[0]['limite']){
						$tpl->newBlock("gasto_error");
						$tpl->assign("color","9900CC");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=false;
					}
					else{
						$tpl->newBlock("gasto_ok");
						$tpl->assign("codgastos",$_POST['codgastos'.$i]);
						$ok &=true;
					}
				}
				else{
					$tpl->newBlock("gasto_ok");
					$tpl->assign("codgastos",$_POST['codgastos'.$i]);
					$ok &=true;
				}
			$tpl->gotoBlock("rows");
			$rows = obtener_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos'.$i]),"","",$dsn);
			$tpl->assign("i",$var);
			$var++;
			$tpl->assign("codgastos",$_POST['codgastos'.$i]);
			$tpl->assign("num_cia",$_POST['num_cia']);
			$tpl->assign("fecha",$_POST['fecha']);
			$tpl->assign("descripcion",$rows[0]['descripcion']);
			$tpl->assign("concepto",$_POST['concepto'.$i]);
			$tpl->assign("importe", $_POST['importe'.$i]);
			$tpl->assign("importe2", number_format($_POST['importe'.$i],2,'.',','));
			$totales += $_POST['importe'.$i];
		}
	}
}
if($ok){
	$tpl->newBlock("totales");
	$tpl->assign("total",$totales);
	$tpl->assign("total1",number_format($totales,2,'.',','));
	$tpl->newBlock("capturar");
}
//$numrows = count($rows);

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

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

// Imprimir el resultado
$tpl->printToScreen();
?>