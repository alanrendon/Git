<?php
// PAGO DE PRESTAMOS
// Tablas 'prestamos'
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
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pre_pago.tpl");
$tpl->prepare();

if (isset($_GET['tabla'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['importe'.$i] > 0) {
			$datos['id_empleado'] = $_POST['id_empleado'.$i];
			$datos['num_cia'] = $_POST['num_cia'];
			$datos['fecha'] = $_POST['fecha'];
			$datos['importe'] = $_POST['importe'.$i];
			$datos['tipo_mov'] = "TRUE";
			$datos['pagado'] = "TRUE";
			ejecutar_script("INSERT INTO prestamos (id_empleado,num_cia,fecha,importe,tipo_mov,pagado) VALUES (".$_POST['id_empleado'.$i].",$_POST[num_cia],'$_POST[fecha]',".$_POST['importe'.$i].",'TRUE','FALSE')",$dsn);
			
			if ($_POST['falta'.$i] == 0) {
				ejecutar_script("UPDATE prestamos SET pagado = 'TRUE' WHERE id_empleado = ".$_POST['id_empleado'.$i]." AND pagado = 'FALSE'",$dsn);
			}
		}
	}
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Almacenar valores temporalmente
if (isset($_POST['tabla']) && $_POST['tabla'] == "hoja_diaria_rost") {
	if (!isset($_SESSION['hd']))
		$_SESSION['hd'] = array(); // Hoja Diaria
	
	// Almacenar valores de la pantalla de hoja diaria ----------------------
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$_SESSION['hd']['codmp'.$i]           = $_POST['codmp'.$i];
		$_SESSION['hd']['unidades'.$i]        = $_POST['unidades'.$i];
		$_SESSION['hd']['precio_unitario'.$i] = $_POST['precio_unitario'.$i];
		$_SESSION['hd']['precio_total'.$i]    = $_POST['precio_total'.$i];
	}
	$_SESSION['hd']['otros']              = $_POST['precio_total_otros'];
	
	$_SESSION['hd']['numfilas']           = $_POST['numfilas'];
	$_SESSION['hd']['precio_total_otros'] = $_POST['precio_total_otros'];
	$_SESSION['hd']['total']              = $_POST['venta_total'];
}

$sql = "SELECT id_empleado,num_emp,nombre,ap_paterno,ap_materno,importe FROM prestamos JOIN catalogo_trabajadores ON(prestamos.id_empleado=catalogo_trabajadores.id) WHERE prestamos.num_cia = $_SESSION[num_cia] AND tipo_mov = 'FALSE' AND pagado = 'FALSE' ORDER BY num_emp";
$result = ejecutar_script($sql,$dsn);

if (!$result) {
	$tpl->newBlock("error");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("prestamos");
$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_SESSION[num_cia]",$dsn);
$tpl->assign("num_cia",$_SESSION['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_SESSION['fecha']);

$tpl->assign("numfilas",count($result));

for ($i=0; $i<count($result); $i++) {
	$debe = ejecutar_script("SELECT SUM(importe) FROM prestamos WHERE id_empleado = ".$result[$i]['id_empleado']." AND pagado = 'FALSE' AND tipo_mov = 'TRUE'",$dsn);
	
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < count($result)-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0 )
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",count($result)-1);
	
	$tpl->assign("id_empleado",$result[$i]['id_empleado']);
	$tpl->assign("num_emp",$result[$i]['num_emp']);
	$tpl->assign("nombre_trabajador",$result[$i]['nombre']." ".$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']);
	$tpl->assign("debe",$result[$i]['importe']);
	$tpl->assign("fdebe",number_format($result[$i]['importe'],2,".",","));
	if ($debe)
		$tpl->assign("falta",number_format($result[$i]['importe'] - $debe[0]['sum'],2,".",""));
	else
		$tpl->assign("falta",number_format($result[$i]['importe'],2,".",","));
}
$tpl->printToScreen();
?>