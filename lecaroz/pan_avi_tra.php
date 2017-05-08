<?php
// TRASPASO DE AVIO
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

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
$descripcion_error[1] = "Ya esta actualizado el inventario";
$descripcion_error[2] = "No puede traspasar avio en meses pasados";

// ---------------------------------- Insertar datos en tablas -----------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_tra.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia_traspasa'])) {
	$numfilas = 30;
	$fecha = $_POST['fecha'];
	$num_cia1 = $_POST['num_cia_traspasa'];
	$num_cia2 = $_POST['num_cia_recibe'];
	
	$temp = ejecutar_script("SELECT folio FROM traspaso_avio GROUP BY folio ORDER BY folio DESC LIMIT 1",$dsn);
	$folio = ($temp)?$temp[0]['folio']+1:1;
	
	// Datos para el movimiento de salida y de entrada de las compañías
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['codmp'.$i] > 0 && $_POST['cantidad'.$i] > 0) {
			$codmp = $_POST['codmp'.$i];
			$cantidad = $_POST['cantidad'.$i];
			$precio = abs($_POST['precio'.$i]);
			$total_mov = $cantidad * $precio;
			
			// Movimiento de salida
			$sql  = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) ";
			$sql .= "VALUES ($num_cia1,$codmp,'$fecha',NULL,'FALSE',-$cantidad,$precio,-$total_mov,$precio,'TRASPASO DE AVIO ENTRE PANADERIAS A LA $num_cia2')";
			ejecutar_script($sql,$dsn);
			$sql  = "INSERT INTO mov_inv_virtual (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) ";
			$sql .= "VALUES ($num_cia1,$codmp,'$fecha',NULL,'FALSE',-$cantidad,$precio,-$total_mov,$precio,'TRASPASO DE AVIO ENTRE PANADERIAS A LA $num_cia2')";
			ejecutar_script($sql,$dsn);
			
			// Movimiento de entrada
			$sql  = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) ";
			$sql .= "VALUES ($num_cia2,$codmp,'$fecha',NULL,'FALSE',$cantidad,$precio,$total_mov,$precio,'TRASPASO DE AVIO ENTRE PANADERIAS DE LA $num_cia1')";
			ejecutar_script($sql,$dsn);
			$sql  = "INSERT INTO mov_inv_virtual (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion) ";
			$sql .= "VALUES ($num_cia2,$codmp,'$fecha',NULL,'FALSE',$cantidad,$precio,$total_mov,$precio,'TRASPASO DE AVIO ENTRE PANADERIAS DE LA $num_cia1')";
			ejecutar_script($sql,$dsn);
			
			// Actualiza existencia en inventario
			// Descontar de compañía que traspasa
			$id = NULL;
			// Real
			if ($id = ejecutar_script("SELECT idinv FROM inventario_real WHERE num_cia=$num_cia1 AND codmp=$codmp",$dsn))
				$sql = "UPDATE inventario_real SET existencia=existencia-$cantidad WHERE idinv=".$id[0]['idinv'];
			else
				$sql = "INSERT INTO inventario_real (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia1,$codmp,-$cantidad,$precio)";
			ejecutar_script($sql,$dsn);
			// Virtual
			if ($id = ejecutar_script("SELECT idinv FROM inventario_virtual WHERE num_cia=$num_cia1 AND codmp=$codmp",$dsn))
				$sql = "UPDATE inventario_virtual SET existencia=existencia-$cantidad WHERE idinv=".$id[0]['idinv'];
			else
				$sql = "INSERT INTO inventario_virtual (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia1,$codmp,-$cantidad,$precio)";
			ejecutar_script($sql,$dsn);
			
			// Sumar a la compañía que recibe
			$id = NULL;
			// Real
			if ($id = ejecutar_script("SELECT idinv FROM inventario_real WHERE num_cia=$num_cia2 AND codmp=$codmp",$dsn))
				$sql = "UPDATE inventario_real SET existencia=existencia+$cantidad WHERE idinv=".$id[0]['idinv'];
			else
				$sql = "INSERT INTO inventario_real (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia2,$codmp,$cantidad,$precio)";
			ejecutar_script($sql,$dsn);
			// Virtual
			if ($id = ejecutar_script("SELECT idinv FROM inventario_virtual WHERE num_cia=$num_cia2 AND codmp=$codmp",$dsn))
				$sql = "UPDATE inventario_virtual SET existencia=existencia+$cantidad WHERE idinv=".$id[0]['idinv'];
			else
				$sql = "INSERT INTO inventario_virtual (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia2,$codmp,$cantidad,$precio)";
			ejecutar_script($sql,$dsn);
			
			// Insertar registro de operación
			$sql = "INSERT INTO traspaso_avio (num_cia_traspasa, codmp, cantidad, precio_unidad, num_cia_recibe, fecha, folio, iduser, tsins) VALUES ($num_cia1, $codmp, $cantidad, $precio, $num_cia2, '$fecha', $folio, $_SESSION[iduser], now())";
			ejecutar_script($sql,$dsn);
		}
	}
	
	$tpl->newBlock("listado");
	
	$tpl->assign("num_cia1",$num_cia1);
	$tpl->assign("num_cia2",$num_cia2);
	
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",mes_escrito(date("n")));
	$tpl->assign("anio",date("Y"));
	$tpl->assign("folio",$folio);
	
	$total = 0;
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['codmp'.$i] > 0 && $_POST['cantidad'.$i] > 0) {
			$tpl->newBlock("fila_mp");
			$tpl->assign("codmp",$_POST['codmp'.$i]);
			$nombre = ejecutar_script("SELECT nombre FROM catalogo_mat_primas WHERE codmp=".$_POST['codmp'.$i],$dsn);
			$tpl->assign("nombre",$nombre[0]['nombre']);
			$tpl->assign("precio",number_format($_POST['precio'.$i],2,".",","));
			$tpl->assign("cantidad",number_format($_POST['cantidad'.$i],2,".",","));
			$tpl->assign("total",number_format($_POST['cantidad'.$i]*$_POST['precio'.$i],2,".",","));
			$total += $_POST['cantidad'.$i]*$_POST['precio'.$i];
		}
	}
	$tpl->assign("listado.total",number_format($total,2,".",","));
	$tpl->printToScreen();
	die;
}

if (!isset($_GET['num_cia_traspasa'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d") <= 5 ? date("d/m/Y", mktime(0,0,0,date("m"),0,date("Y"))) : date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die;
}

$sql = '
	SELECT
		\'' . $_GET['fecha'] . '\' < max(fecha)
			AS
				dif
	FROM
		diferencias_inventario
';
$result = ejecutar_script($sql, $dsn);
if ($result[0]['dif'] == 't') {
	header('location: pan_avi_tra.php?codigo_error=2');
	die;
}

$tpl->newBlock("captura");

// Obtener nombres de las compañías
$cia1 = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia=$_GET[num_cia_traspasa]",$dsn);
$cia2 = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia=$_GET[num_cia_recibe]",$dsn);

$tpl->assign("num_cia_traspasa",$_GET['num_cia_traspasa']);
$tpl->assign("nombre_cia_traspasa",$cia1[0]['nombre']);
$tpl->assign("num_cia_recibe",$_GET['num_cia_recibe']);
$tpl->assign("nombre_cia_recibe",$cia2[0]['nombre']);
$tpl->assign("fecha",$_GET['fecha']);

// Obtener inventario de la compañía que traspasa
$sql = "SELECT codmp,nombre,precio_unidad,existencia FROM inventario_real JOIN catalogo_mat_primas USING (codmp) WHERE num_cia=$_GET[num_cia_traspasa]";
$mp = ejecutar_script($sql,$dsn);

for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("mp");
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre",$mp[$i]['nombre']);
	$tpl->assign("precio",number_format($mp[$i]['precio_unidad'],4,".",""));
	$tpl->assign("existencia",$mp[$i]['existencia'] != 0 ? $mp[$i]['existencia'] : 0);
}

$numfilas = 30;
for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",($i > 0)?$i-1:$numfilas-1);
	$tpl->assign("next",($i < $numfilas-1)?$i+1:0);
}

$tpl->printToScreen();
?>