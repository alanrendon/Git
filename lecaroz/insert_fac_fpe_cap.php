<?php

// FACTURAS PROVEEDORES VARIAS
// Tabla 'facturas'
// Menu Proveedores y Facturas -> Proveedores

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
$tabla = $_GET['tabla'];

// -------------------------------------------------------------------------------------
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$_POST);


$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
$_dt=explode("/",$_POST['fecha_mov']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));
$cia=0;
// Consultar si existe la compañia
if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn)) {
	if (existe_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']), $dsn)) {
		if (existe_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos']),$dsn)) {
			if (!existe_registro("facturas", array("num_fact","num_proveedor","num_cia"), array($_POST['num_fact'], $_POST['num_proveedor'],$_POST['num_cia']), $dsn)) {

					/*if($_POST['num_cia']==146) $cia=147;
					else if($_POST['num_cia']==171) $cia==170;
					else*/ $cia=$_POST['num_cia'];

					$_SESSION['fpe']['num_proveedor']=$_POST['num_proveedor'];
					$_SESSION['fpe']['nombre_proveedor']=$_POST['nombre_proveedor'];
					$_SESSION['fpe']['concepto']=$_POST['concepto'];
					$_SESSION['fpe']['codgastos']=$_POST['codgastos'];
					$_SESSION['fpe']['fecha_mov']=$_POST['fecha_mov'];

					$db->generar_script_insert("");
					$db->ejecutar_script();

					$sql="INSERT INTO pasivo_proveedores (num_cia, num_fact, total, descripcion, fecha_mov, fecha_pago, num_proveedor, codgastos) VALUES (".$cia.", '".$_POST['num_fact']."', '".$_POST['importe_total']."','FACTURAS DE PROVEEDORES ESPECIALES NO. ".$_POST['num_fact']."', '".$_POST['fecha_mov']."', '".$fecha2."', ".$_POST['num_proveedor'].", ".$_POST['codgastos'].")"; 
					ejecutar_script($sql,$dsn); 
					header("location: ./fac_fpe_cap.php?mensaje=Se+realizo+el+registro+con+exito");
			}
			else {
					// Regresar al formulario y mandar un mensaje de error
					header("location: ./fac_fpe_cap.php?codigo_error=4");
					die;
				}
		}
		else {
				//Regresar al formulario y mandar un mensaje de error
				header("location: ./fac_fpe_cap.php?codigo_error=3");
				die;
			}
	}
	else {
			//Regresar al formulario y mandar un mensaje de error
			header("location: ./fac_fpe_cap.php?codigo_error=2");
			die;
		}
	}
else {
	// Regresar al formulario y mandar un mensaje de error
	header("location: ./fac_fpe_cap.php?codigo_error=1");
	die;
}
?>