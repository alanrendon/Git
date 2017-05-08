<?php
//define ('IDSCREEN',1721); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";
$descripcion_error[3] = "ERROR";

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
$tpl->assignInclude("body","./plantillas/ros/ros_fac_cap1.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","fact_rosticeria");

//empieza código para insertar un numero de renglones en un bloque


$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
//echo $diascredito;
$_dt=explode("/",$_POST['fecha_mov']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$dletra=$d2;
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));
$fecha_letra=date( "D", mktime(0,0,0,$m2,$dletra,$y2));
//echo $fecha2;

$ok=true;
$ok1=true;
$okcia=true;
$okprov=true;
$ok_fac=true;
$ok2=true;
$menos=0;
$porcentaje=0;
$mas=0;
$tpl->assign("num_cia1",$_POST['num_cia']);
$tpl->assign("cia",$_POST['num_cia']);
$tpl->assign("num_pro",$_POST['num_proveedor']);

$tpl->assign("fecha_mov1",$_POST['fecha_mov']);
$tpl->assign("fecha_pago1",$fecha2);

if(isset($_SESSION['factura_ros'])) unset($_SESSION['factura_ros']);




if ($_POST['num_cia'] >= '301' and $_POST['num_cia'] <= '599' or $_POST['num_cia'] == "702" or $_POST['num_cia'] == "704")
{
	$tpl->newBlock("cia_ok");
	$tpl->assign("num_cia1",$_POST['num_cia']);
	$tpl->assign("nombre_cia",$nomcia[0]['nombre_corto']);
	$okcia=true;
}
else
{
	$tpl->newBlock("cia_error");
	$tpl->assign("num_cia1",$_POST['num_cia']);
	$tpl->assign("nombre_cia","Esta no es una Rosticeria");
	$okcia=false;
}

// if (/*$_POST['num_proveedor'] == '13'*/in_array($_POST['num_proveedor'], array(13, 482, 1386)))
// {
	$tpl->newBlock("pro_ok");
	$tpl->assign("num_proveedor1",$_POST['num_proveedor']);
	$tpl->assign("nom_proveedor",$nomproveedor[0]['nombre']);
	$okprov=true;
// }
// else
// {
// 	$tpl->newBlock("pro_error");
// 	$tpl->assign("num_proveedor1",$_POST['num_proveedor']);
// 	$tpl->assign("nom_proveedor","No esta valido este proveedor");
// 	$okprov=false;
// }

if($_POST['num_fac']=="")
{
	$tpl->newBlock("fac_error");
	$tpl->assign("num_fac1","ERROR");
	$ok_fac=false;
}
else
{
	if (existe_registro("fact_rosticeria",array("num_fac","num_proveedor"),array($_POST['num_fac'],$_POST['num_proveedor']), $dsn))
	{
		//echo "ya existe factura<br>";
		$tpl->newBlock("fac_error");
		$tpl->assign("num_fac1","ERROR");
		$ok_fac=false;
	}
	else
	{
		$tpl->newBlock("fac_ok");
		$tpl->assign("num_fac1",$_POST['num_fac']);
		$ok_fac=true;
	}
}

$tpl->newBlock("fac_ok");
$ok1 &= $ok_fac;
$ok1 &= $okcia;
$ok1 &= $okprov;

$totales=0;
$total1=0;
$contador=0;
$kmod=true;
$var=0;
$var2=0;
$_SESSION['factura_ros']['num_cia']=$_POST['num_cia'];
$_SESSION['factura_ros']['num_fact']=$_POST['num_fac'];
$_SESSION['factura_ros']['num_proveedor']=$_POST['num_proveedor'];

for($i=0;$i<$_POST['contador'];$i++)
{
	if ($_POST['cantidad'.$i] != "")
		{
		$contador++;
		if (existe_registro("pesos_companias",array("num_cia","codmp","num_proveedor"),array($_POST['num_cia'], $_POST['codmp'.$i], $_POST['num_proveedor']), $dsn))
		{
//			echo "encontre pesos<br>";
			$mp = obtener_registro("pesos_companias",array("codmp","num_cia","num_proveedor"),array($_POST['codmp'.$i], $_POST['num_cia'], $_POST['num_proveedor']),"","",$dsn);
			if (($_POST['kilos'.$i] / $_POST['cantidad'.$i]) > $mp[0]['peso_max'])
			{
//				echo "peso es mayor<br>";
				$peso_permitido=$mp[0]['peso_max'];
				$kilos=($peso_permitido * $_POST['cantidad'.$i]);
				$kmod=false;
			}
			else
			{
//			echo "esta bien<br>";
			$peso_permitido = $_POST['kilos'.$i] / $_POST['cantidad'.$i];
			$kilos=$_POST['kilos'.$i];
			$kmod=true;
			}
		}
		else {
			$peso_permitido = $_POST['kilos'.$i] / $_POST['cantidad'.$i];
			$kilos=$_POST['kilos'.$i];
			$kmod=true;
			}

		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("var",$var);//es la que lo manda
		$var++;
		$tpl->assign("num_cia", $_POST['num_cia']);
		$tpl->assign("num_proveedor", $_POST['num_proveedor']);
		$tpl->assign("num_fac", $_POST['num_fac']);
		$tpl->assign("fecha_mov", $_POST['fecha_mov']);
		$tpl->assign("fecha_pago", $fecha2);
		$tpl->assign("cantidad", $_POST['cantidad'.$i]);
		if($_POST['cantidad'.$i]!="")
		{
			$_SESSION['factura_ros']['cantidad'.$var2]=$_POST['cantidad'.$i];
			$_SESSION['factura_ros']['kilos'.$var2]=$kilos;
			$_SESSION['factura_ros']['codmp'.$var2]=$_POST['codmp'.$i];
			$var2++;
			$_SESSION['factura_ros']['contador']=$var2;
		}
//		$tpl->assign("cantidad1",number_format($_POST['cantidad'.$i],2,'.',','));//se metera en un nuevo bloque
		$tpl->assign("kilos", $kilos);
		$tpl->assign("kilos1",number_format($kilos,2,'.',','));
		$tpl->assign("precio", $_POST['precio'.$i]);
		$tpl->assign("precio1",number_format($_POST['precio'.$i],2,'.',','));

		$tpl->assign("codmp",$_POST['codmp'.$i]);
		if($kmod)
		{
			$tpl->newBlock("kilos_ok");
			$tpl->assign("kilos1",number_format($kilos,2,'.',','));
			$tpl->gotoBlock("rows");
		}
		else
		{
			$tpl->newBlock("kilos_mod");
			$tpl->assign("kilos1",number_format($kilos,2,'.',','));
			$tpl->gotoBlock("rows");
		}

		if(existe_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]), $dsn))
		{
			$matp = obtener_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]),"","",$dsn);
			$tpl->newBlock("mp_ok");
			$tpl->assign("codmp1",$_POST['codmp'.$i]);
			$tpl->assign("nom_mp",$matp[0]['nombre']);
			$tpl->gotoBlock("rows");
			$total1=$kilos * $_POST['precio'.$i];
			$tpl->assign("total2",number_format($total1,2,'.',','));
			$tpl->assign("total1",$total1);
			$tpl->assign("precio_unidad", $total1/$_POST['cantidad'.$i]);//se movera
			$totales += $total1;
			$ok1=true;//modifiqué aqui
		}
		else
		{
			$tpl->newBlock("mp_error");
			$tpl->assign("codmp1",$_POST['codmp'.$i]);
			$tpl->assign("nom_mp","no existe materia prima");
			$tpl->gotoBlock("rows");
			$tpl->assign("total1","error");
			$ok1=false;
			$total1=0;
		}
//----------------
		$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'], $_POST['codmp'.$i]),"","",$dsn);
		$porcentaje=$invReal[0]['precio_unidad'] * 0.40;
		$mas=$invReal[0]['precio_unidad'] + $porcentaje;
		$menos=$invReal[0]['precio_unidad'] - $porcentaje;
		$unidad=$total1/$_POST['cantidad'.$i];
		if($unidad >= $menos and $unidad <= $mas)
		{
			$tpl->newBlock("cantidad_ok");
			$tpl->assign("cantidad1",number_format($_POST['cantidad'.$i],2,'.',','));//se metera en un nuevo bloque
			$tpl->gotoBlock("rows");
			$ok2=true;
		}

		else if($invReal[0]['precio_unidad']==0)
		{
			$tpl->newBlock("cantidad_ok");
			$tpl->assign("cantidad1",number_format($_POST['cantidad'.$i],2,'.',','));//se metera en un nuevo bloque
			$tpl->gotoBlock("rows");
			$ok2=true;
		}
		else
		{
			//echo "error en porcentaje<br>";
			//echo "kilos --> $kilos<br> total1 ---> $total1 <br> unidad ---> $unidad <br> menos permitido ---> $menos <br> mas permitido --> $mas";

			$tpl->newBlock("cantidad_error");
			$tpl->assign("cantidad1",number_format($_POST['cantidad'.$i],2,'.',','));//se metera en un nuevo bloque
			$tpl->gotoBlock("rows");
			$ok2=false;

		}
		if (!($fecha_letra=='Sat' or $fecha_letra=='Sun')) //BLOQUE DE REVISION DE CANTIDAD DE POLLOS COMPRADOS
		{
			if($fecha_letra=='Mon') $dia="lunes";
			if($fecha_letra=='Tue') $dia="martes";
			if($fecha_letra=='Wed') $dia="miercoles";
			if($fecha_letra=='Thu') $dia="jueves";
			if($fecha_letra=='Fri') $dia="viernes";

			$fecha_pasada=date("j/n/Y",mktime(0,0,0,$_dt[1],$_dt[0]-7,$_dt[2]));
//			echo "$fecha_pasada <br>";
			$pollos=ejecutar_script("select num_cia, codmp,cantidad from fact_rosticeria where fecha_mov = '$fecha_pasada' and codmp=".$_POST['codmp'.$i]." and num_cia=".$_POST['num_cia'],$dsn);
//			$pollos=obtener_registro("control_pollos",array("num_cia","codmp"),array($_POST['num_cia'],$_POST['codmp'.$i]),"","",$dsn);

			if($pollos)
			{
//				$cantidad_pollos=	$pollos[0][$dia];
				$cantidad_pollos=	$pollos[0]['cantidad'];
				$porc20mas=			$cantidad_pollos+($cantidad_pollos*0.2);
/*				$porc20menos=		$cantidad_pollos-($cantidad_pollos*0.2);

				if($_POST['cantidad'.$i] <= $porc20menos)
				{
					$tpl->newBlock("control_pollo");
					$tpl->assign("aviso","ESTA COMPRANDO UNIDADES DE MENOS PARA EL CODIGO ".$_POST['codmp'.$i]);
					$tpl->gotoBlock("rows");
				}
				else*/ if($_POST['cantidad'.$i] >= $porc20mas)
				{
					$tpl->newBlock("control_pollo");
					$tpl->assign("aviso","ESTA COMPRANDO UNIDADES DE MAS PARA EL CODIGO ".$_POST['codmp'.$i]);
					$tpl->gotoBlock("rows");
				}
			}
		}

//----------------
		}
$ok &= $ok1;
$ok &= $ok2;
}
if (/*$ok*/$ok1 && $ok2 && $okcia && $okprov && $ok_fac)
{
	$tpl->newBlock("total_ok");
	$tpl->assign("total",$totales);
	$tpl->assign("total",number_format($totales,2,'.',','));
	$tpl->newBlock("boton");
}
else
{
	$tpl->newBlock("total_error");
	$tpl->assign("total","----");
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

//print_r($_SESSION['factura_ros']);
// Imprimir el resultado
$tpl->printToScreen();
?>
