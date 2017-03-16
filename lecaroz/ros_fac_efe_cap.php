<?php
// CAPTURA DE FACTURAS DE ROSTICERIAS
// Tabla ''
// Menu ''

//define ('IDSCREEN',1721); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, o bien esta dada de baja, revisa bien la compañia";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

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
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_fac_efe_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['compania']))
{
	$num_pro = isset($_GET['num_pro']) && $_GET['num_pro'] > 0 ? $_GET['num_pro'] : 2456;

	$tpl->newBlock("obtener_dato");
	$sql="select distinct(num_cia) from control_expendios_rosticerias order by num_cia";
	$cia=ejecutar_script($sql,$dsn);

	$tpl->assign("num_pro_{$num_pro}", ' selected=""');

	$sql2="select distinct(codmp) from precios_guerra where codmp in(160,363,297,352,364,600,700,434,573,334, 303) and precio_compra > 0 and num_proveedor = {$num_pro}";
	$productos=ejecutar_script($sql2,$dsn);
	$tpl->assign("cont",count($productos));

	if(isset($_SESSION['factura_ros_esp'])) $tpl->assign("num_fac",$_SESSION['factura_ros_esp']['num_fac']);

	for($i=0;$i<count($cia);$i++)
	{
		$tpl->newBlock("CIA");

		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$nom_cia = obtener_registro("catalogo_companias",array("num_cia"),array($cia[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nom_cia",$nom_cia[0]['nombre_corto']);

		if(isset($_SESSION['factura_ros_esp']))
			if($cia[$i]['num_cia'] == $_SESSION['factura_ros_esp']['num_cia'])
				$tpl->assign("selected","selected");

	}


	$var=0;
	for ($i=0; $i<count($productos); $i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
		$tpl->assign("codmp",$productos[$i]['codmp']);
		$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($productos[$i]['codmp']),"","",$dsn);
		$tpl->assign("nom_codmp",$mp[0]['nombre']);

		if(isset($_SESSION['factura_ros_esp']))
		{
			if($var < $_SESSION['factura_ros_esp']['contador']){
				if($productos[$i]['codmp']==$_SESSION['factura_ros_esp']['codmp'.$var])
				{
					$tpl->assign("cantidad",$_SESSION['factura_ros_esp']['cantidad'.$var]);
					$tpl->assign("kilos",$_SESSION['factura_ros_esp']['kilos'.$var]);
					$var++;
				}
			}
		}


	}

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
//----------------------------------------------------------------------------------------------------------------------------------

$tpl->assign("anio_actual",date("Y"));
$_dc=explode("/",date("d/m/Y"));
$tpl->assign("dia",date("d"));
$tpl->assign("mes",date("m"));




if (existe_registro("catalogo_companias",array("num_cia","status"),array($_GET['compania'],"true"), $dsn))
{
	$sql="SELECT * FROM catalogo_companias WHERE num_cia='".$_GET['compania']."' AND status=true";
	$cias = ejecutar_script($sql,$dsn);
}
else
{
	header("location: ./ros_fac_efe_cap.php?codigo_error=1");
	die;
}

$dependencias = obtener_registro("control_expendios_rosticerias",array("num_cia"),array($_GET['compania']),"","",$dsn);

//----------------------------------------------------------------------------------------------------------------------------------
$tpl->newBlock("factura");

if(isset($_SESSION['factura_ros_esp'])) unset($_SESSION['factura_ros_esp']);

	$_SESSION['factura_ros_esp']['num_fac']=$_GET['num_fac'];
	$_SESSION['factura_ros_esp']['num_cia']=$_GET['compania'];
	$_SESSION['factura_ros_esp']['num_pro']=$_GET['num_pro'];

	$tpl->assign("num_pro",$_GET['num_pro']);
	$tpl->assign("num_fac",$_GET['num_fac']);
	$tpl->assign("anio_actual",date("Y"));
	$_dc=explode("/",date("d/m/Y"));
	$tpl->assign("dia",date("d"));

	$tpl->assign("mes",date("m"));

//	echo $_dc[1];


	function fecha_insercion($num_cia, $dsn)
	{
	$sql="SELECT * FROM fact_rosticeria WHERE num_cia='".$num_cia."' order by fecha_mov";
	$cias = ejecutar_script($sql,$dsn);
	$i=count($cias);
	$fecha_trabajo=$cias[$i-1]['fecha_mov'];
	$_dt=explode("/",$fecha_trabajo);
	$d2 = $_dt[0];
	$m2 = $_dt[1];
	$y2 = $_dt[2];
	$d2 =$d2+1;
	$fecha=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2) );
	return $fecha;
	}

	$sql2="select * from precios_guerra where num_cia='".$_GET['compania'] ."' and codmp in(160,363,297,352,364,600,700,434,573,334) and precio_compra > 0 and num_proveedor = {$_GET['num_pro']} order by id";
	$productos=ejecutar_script($sql2,$dsn);
	$var1=0;
	$total_fac=0;
	$kilos=0;
	$aux_sesion=0;

	for($i=0;$i<$_GET['contador'];$i++)
	{
		if($_GET['cantidad'.$i]!="" and $_GET['kilos'.$i]!="")
		{
//*************************************************
			$_SESSION['factura_ros_esp']['codmp'.$aux_sesion]		= $_GET['codmp'.$i];
			$_SESSION['factura_ros_esp']['cantidad'.$aux_sesion]	= $_GET['cantidad'.$i];
			$_SESSION['factura_ros_esp']['kilos'.$aux_sesion]		= $_GET['kilos'.$i];
			$_SESSION['factura_ros_esp']['contador']				= $aux_sesion + 1;
//*************************************************
			$aux_sesion++;



			for ($j=0;$j<count($productos);$j++)
			{
				if ($productos[$j]['codmp']==$_GET['codmp'.$i])
				{
					$tpl->newBlock("rows1");
					$tpl->assign("var1",$var1);
					$tpl->assign("codmp",$_GET['codmp'.$i]);
					$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET['codmp'.$i]),"","",$dsn);
					$tpl->assign("nom_codmp",$mp[0]['nombre']);
					$tpl->assign("cantidad",$_GET['cantidad'.$i]);
					$tpl->assign("cantidad1",number_format($_GET['cantidad'.$i],2,'.',','));
//----------------------------------
					if (existe_registro("pesos_companias",array("num_cia","codmp"),array($_GET['compania'], $_GET['codmp'.$i]), $dsn))
					{
						$mp = obtener_registro("pesos_companias",array("codmp","num_cia"),array($_GET['codmp'.$i], $_GET['compania']),"","",$dsn);
						if ($_GET['kilos'.$i] / $_GET['cantidad'.$i] > $mp[0]['peso_max'])
						{
							$peso_permitido=$mp[0]['peso_max'];
							$kilos=($peso_permitido * $_GET['cantidad'.$i]);
							$kmod=false;//hizo ajuste
							$tpl->assign("kilos",$kilos);
							$tpl->newBlock("kilos_mod");
							$tpl->assign("kilos1",number_format($kilos,2,'.',','));
							$tpl->gotoBlock("rows1");
						}
						else
						{
						$peso_permitido = $_GET['kilos'.$i] / $_GET['cantidad'.$i];
						$kilos=$_GET['kilos'.$i];
						$kmod=true;//salio bien
						$tpl->assign("kilos",$_GET['kilos'.$i]);
						$tpl->newBlock("kilos_ok");
						$tpl->assign("kilos1",number_format($kilos,2,'.',','));
						$tpl->gotoBlock("rows1");
						}
					}
					else {
						$peso_permitido = $_GET['kilos'.$i] / $_GET['cantidad'.$i];
						$kilos=$_GET['kilos'.$i];
						$kmod=true;
						$tpl->assign("kilos",$kilos);
						$tpl->newBlock("kilos_ok");
						$tpl->assign("kilos1",number_format($kilos,2,'.',','));
						$tpl->gotoBlock("rows1");

						}

					$tpl->assign("precio",$productos[$j]['precio_compra']);
					$tpl->assign("precio1",number_format($productos[$j]['precio_compra'],2,'.',','));
					$total1=$kilos * $productos[$j]['precio_compra'];
					$tpl->assign("total1",number_format($total1,2,'.',','));
					$tpl->assign("total",$total1);
					$total_fac += $total1;
//-------------
					$var1++;
				}
			}
		}
	}
//	print_r($_SESSION['factura_ros_esp']);
	$tpl->gotoBlock("factura");
	$tpl->assign("total_fac",number_format($total_fac,2,'.',','));
	$tpl->assign("cont",count($productos));
	$fec1=fecha_insercion($_GET['compania'],$dsn);
	$tpl->assign("fecha", $fec1);


	$tpl->assign("cont_cias",count($dependencias));
	for ($i=0;$i<count($dependencias);$i++)
	{
		$tpl->newBlock("companias");
		$cias = obtener_registro("catalogo_companias",array("num_cia"),array($dependencias[$i]['num_exp']),"","",$dsn);
		$tpl->assign("nom_cia",$cias[0]['nombre_corto']);
		$tpl->assign("i",$i);
//		$fec=fecha_insercion($dependencias[0]['num_exp'],$dsn);
//		$tpl->assign("fecha", $fec);
		$tpl->assign("num_cia",$dependencias[$i]['num_exp']);
	}


//	for ($i=0; $i<count($productos); $i++)
//	{
	$d=0;
	for($i=0;$i<$_GET['contador'];$i++)
	{
		if($_GET['cantidad'.$i]!="" and $_GET['kilos'.$i]!="")
		{
			for ($a=0;$a<count($productos);$a++)
			{
				if ($productos[$a]['codmp']==$_GET['codmp'.$i])
				{
					$tpl->newBlock("renglones");
					$tpl->assign("d",$d);
					$next=$d+1;
					$tpl->assign("next",$d+1);
					$tpl->assign("codmp",$productos[$a]['codmp']);
					$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($productos[$a]['codmp']),"","",$dsn);
					$tpl->assign("nom_codmp",$mp[0]['nombre']);
					for ($j=0;$j<count($dependencias);$j++)
					{
						$tpl->newBlock("cmp");
						$tpl->assign("next",$next);
						$tpl->assign("con",count($dependencias));
						$tpl->assign("d",$d);
						$tpl->assign("j",$j);
						$tpl->assign("sig",$j+1);
					}
					$d++;
				}
			}
		}
	}
	$tpl->gotoBlock("factura");
	$tpl->assign("cont_productos",$d);
	for ($i=0;$i<count($dependencias);$i++)
	{
		$tpl->newBlock("totales");
		$tpl->assign("d",$i);
	}

$tpl->printToScreen();
die();
?>
