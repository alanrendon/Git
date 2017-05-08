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


// -------------------------------------------------------------------------------------
// Hacer un nuevo objeto DBclass

$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
$_dt = explode("/",$_POST['fecha_mov']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));
$cia=0;
// Consultar si existe la compañia
$iva = number_format($_POST['iva_renta'],2,'.','') + number_format($_POST['iva_capital'],2,'.','') + number_format($_POST['iva_interes'],2,'.','');

if (!existe_registro($_POST['num_cia'] <= 800 ? "facturas" : "facturas_zap", array("num_fact","num_proveedor","num_cia"), array(mb_strtoupper($_POST['num_fact']), $_POST['num_proveedor'],$_POST['num_cia']), $dsn)) {

	if($_POST['num_cia']==146) $cia=147;
	else if($_POST['num_cia']==171) $cia==170;
	else $cia=$_POST['num_cia'];

	if ($_POST['num_cia'] <= 800) {
		//INSERTA A FACTURAS
		$fac['num_proveedor']     = $_POST['num_proveedor'];
		$fac['num_cia']           = $_POST['num_cia'];
		$fac['num_fact']          = mb_strtoupper($_POST['num_fact']);
		$fac['fecha']         = $_POST['fecha_mov'];
		$fac['concepto']          = $_POST['concepto'];
		$fac['importe']       = $_POST['importe'];
		$fac['piva']     = "0";
		$fac['iva']       = $iva;
		$fac['pretencion_isr'] = "0";
		$fac['pretencion_iva'] = "0";
		$fac['retencion_isr'] = "0";
		$fac['retencion_iva'] = "0";
		$fac['codgastos']         = $_POST['codgastos'];
		$fac['total']     = $_POST['importe_total'];
		$fac['tipo_factura']      = "0";
		$fac['fecha_captura']     = date("d/m/Y");
		$fac['iduser'] = $_SESSION['iduser'];

		//INSERTA A GASTOS
		$gas['codgastos'] = $_POST['codgastos'];
		$gas['num_cia']   = $_POST['num_cia'];
		$gas['fecha']     = $_POST['fecha_mov'];
		$gas['importe']   = $_POST['importe_total'];
		$gas['concepto']  = $_POST['concepto'];
		$gas['captura']	  = "true";

		//INSERTA A PASIVO A PROVEEDORES
		$sql="
		INSERT INTO
		pasivo_proveedores (num_cia, num_fact, total, descripcion, fecha, num_proveedor, codgastos)
		VALUES (".$cia.", '".mb_strtoupper($_POST['num_fact'])."', '".$_POST['importe_total']."','$_POST[concepto]', '".$_POST['fecha_mov']."', ".$_POST['num_proveedor'].", ".$_POST['codgastos'].")";
		ejecutar_script($sql,$dsn);
		$db = new DBclass($dsn,"facturas",$fac);
		$db->generar_script_insert("");
		$db->ejecutar_script();
	} else {
		$sql = "INSERT INTO facturas_zap (num_cia, num_proveedor, num_fact, fecha, concepto, codgastos, importe, iva, total, iduser) VALUES ($cia, $_POST[num_proveedor], $_POST[num_fact],";
		$sql .= " '$_POST[fecha_mov]', '$_POST[concepto]', $_POST[codgastos], $_POST[importe], $iva, $_POST[importe_total], $_SESSION[iduser])";
		ejecutar_script($sql, $dsn);
	}
/*
	$db1= new DBclass($dsn,"movimiento_gastos",$gas);
	$db1->generar_script_insert("");
	$db1->ejecutar_script();
*/
}
else {
	header("location: ./fac_arren_cap.php?codigo_error=4");
	die;
}

//print_r($_POST);
header("location: ./fac_arren_cap.php");
?>
