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
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
$descripcion_error[2] = "LA COMPAÑÍA ESTA DADA DE BAJA";
$descripcion_error[3] = "No hay precios de compra para la rosticería";

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
$tpl->assignInclude("body","./plantillas/ros/ros_fac_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['compania']))
{
	$tpl->newBlock("obtener_dato");

	$sql = "
		SELECT
			num_proveedor
				AS value,
			nombre
				AS text
		FROM
			precios_guerra
			LEFT JOIN catalogo_proveedores
				USING (num_proveedor)
		WHERE
			precio_compra > 0
			AND codmp IN (160, 363, 297, 352, 364, 700, 600, 401, 869, 877, 334, 434, 573, 975, 573, 334, 990, 1083, 303)
		GROUP BY
			value,
			text
		ORDER BY
			value
	";

	$result = ejecutar_script($sql, $dsn);

	if ($result)
	{
		foreach ($result as $row) {
			$tpl->newBlock('pro');

			$tpl->assign('value', $row['value']);
			$tpl->assign('text', $row['text']);
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





$tpl->newBlock("factura");
$tpl->assign("anio_actual",date("Y"));
$_dc=explode("/",date("d/m/Y"));

$tpl->assign("dia",date("d"));
$tpl->assign("mes",date("m"));



if (existe_registro("catalogo_companias",array("num_cia"),array($_GET['compania']), $dsn))
{
	$sql="SELECT * FROM catalogo_companias WHERE num_cia='".$_GET['compania']."'";
	$cias = ejecutar_script($sql,$dsn);
}
else
{
	header("location: ./ros_fac_cap.php?codigo_error=1");
	die;
}

if($cias[0]['status']=='f')
{
	header("location: ./ros_fac_cap.php?codigo_error=2");
	die;
}
//$tpl->assign("anio_actual",$_GET['anio_actual']);
function fecha_insercion($num_cia, $dsn)
{
//$num_cia=101;
$sql="SELECT * FROM fact_rosticeria WHERE num_cia='".$num_cia."' order by fecha_mov";
$cias = ejecutar_script($sql,$dsn);
$i=count($cias);
$fecha_trabajo=$cias[$i-1]['fecha_mov'];
//echo $fecha_trabajo;
$_dt=explode("/",$fecha_trabajo);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+1;
$fecha=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2) );
return $fecha;
}
$fec=fecha_insercion($_GET['compania'],$dsn);
$tpl->assign("fecha", $fec);
$tpl->assign("num_cia",$_GET['compania']);
$tpl->assign("nom_cia",$cias[0]['nombre_corto']);
$tpl->assign("num_pro",$_GET['num_pro']);

$fecha_corriente=date("d/m/Y");
//echo $fecha_corriente;

// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);

//empieza código para insertar un numero de renglones en un bloque

// [09-Abr-2007] se agrego numero de proveedor a la consulta
$sql2="SELECT * FROM precios_guerra WHERE num_cia=".$_GET['compania']." AND codmp IN (160, 363, 297, 352, 364, 700, 600, 401, 869, 877, 334, 434, 573, 975, 573, 334, 990, 1083, 303) AND num_proveedor = $_GET[num_pro] AND precio_compra > 0 ORDER BY id";
//echo $sql2."<br>";
$productos=ejecutar_script($sql2,$dsn);
// [09-Abr-2007] validacion del resultado del query anterior
if (!$productos) {
	header('location: ./ros_fac_cap.php?codigo_error=3');
	die;
}

//print_r ($productos);
//echo "<br>contador".count($productos)."<br>";
$tpl->assign("contador",count($productos));
$var=0;
if(isset($_SESSION['factura_ros']))
{
	$tpl->assign("num_fac",$_SESSION['factura_ros']['num_fact']);
//	echo $_SESSION['factura_ros']['contador'];
//	print_r($_SESSION['factura_ros']);
}

for ($i=0; $i<count($productos); $i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);

	$tpl->assign("codmp",$productos[$i]['codmp']);
//	echo $productos[$i]['codmp'];
	$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($productos[$i]['codmp']),"","",$dsn);

	$tpl->assign("nom_codmp",$mp[0]['nombre']);
	$tpl->assign("precio",$productos[$i]['precio_compra']);
	$tpl->assign("precio1",number_format($productos[$i]['precio_compra'],2,'.',','));

	if(isset($_SESSION['factura_ros']))
	{
		if($var < $_SESSION['factura_ros']['contador']){
			if($productos[$i]['codmp']==$_SESSION['factura_ros']['codmp'.$var])
			{
				$tpl->assign("cantidad",$_SESSION['factura_ros']['cantidad'.$var]);
				$tpl->assign("kilos",$_SESSION['factura_ros']['kilos'.$var]);
				$var++;
			}
		}
	}

}
// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);


// Imprimir el resultado
$tpl->printToScreen();
die();
?>
