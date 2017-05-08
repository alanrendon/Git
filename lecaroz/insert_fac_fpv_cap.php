<?php
// CONEXIONES
// Inserción de registros en una tabla
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

// ----------------------- ------------------------------------------- -------------------
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$_POST);

$_SESSION['fpv']['num_proveedor'] = $_POST['num_proveedor'];
$_SESSION['fpv']['concepto'] = $_POST['concepto'];
$_SESSION['fpv']['codgastos'] = $_POST['codgastos'];

// Consultar si existe la compañia
	//if(!existe_registro("facturas",array("num_remi"),array($_POST['num_remi']), $dsn)) {
		if (existe_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos']),$dsn)) {
			if ($_POST['num_cia'] < 900) {
					$db->generar_script_insert("");
					$db->ejecutar_script();
					
					$sql="INSERT INTO pasivo_proveedores (num_cia, num_fact, total, descripcion, fecha_mov, fecha_pago, num_proveedor, codgastos) VALUES (".$_POST['num_cia'].", '".$_POST['num_fact']."', '".$_POST['importe_total']."','FACTURAS DE PROVEEDORES VARIOS NO. ".$_POST['num_fact']."', '".$_POST['fecha_mov']."', '".$_POST['fecha_ven']."', ".$_POST['num_proveedor'].", ".$_POST['codgastos'].")"; 
					ejecutar_script($sql,$dsn);
				
				// Insertar registro de operacion de usuario en la tabla 'registro'
				//$session->guardar_registro_acceso("Facturas proveedor especial. ID: $_POST[num_remi", $dsn);
	
				// Regresar al formulario y mandar un mensaje si se inserto registro con exito
				header("location: ./fac_fpv_cap.php?mensaje=Se+realizo+el+registro+con+exito");
			}
			else {
				$sql = "INSERT INTO facturas_zap (num_cia, num_proveedor, num_fact, fecha, concepto, codgastos, importe, iva, pisr, isr, pivaret, ivaret, total, iduser, tscap, por_aut, copia_fac) VALUES (";
				$sql .= "$_POST[num_cia], $_POST[num_proveedor], $_POST[num_fact], '$_POST[concepto]', $_POST[codgastos], $_POST[imp_sin_iva], $_POST[importe_iva], $_POST[porciento_ret_isr],";
				$sql .= " $_POST[importe_ret_isr], $_POST[porciento_ret_iva], $_POST[importe_ret_iva], $_POST[importe_total], $_SESSION[iduser], now(), 'TRUE', 'FALSE')";
				ejecutar_script($sql, $dsn);
			}
			
		}
		else {
				//Regresar al formulario y mandar un mensaje de error
				header("location: ./fac_fpv_cap.php?codigo_error=3");
				die;
			}
	//}
	//else {
			//header("location: ./fac_fpv_cap.php?codigo_error=4");
			//die;
	//}
?>