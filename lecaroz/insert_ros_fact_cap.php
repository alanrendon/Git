<?php
// CAPTURA DE FACTURAS
// Tabla 'porcentajes_facturas'
// Menu 'Rosticerias
// CONEXION

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

// Consultar si existe la compañia
	for($i=0; $i<10; $i++)
	{
		if($_POST['mpcod'.$i] != ""){
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn)){
				if (existe_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor'.$i]), $dsn)){
					if (existe_registro("catalogo_proveedores",array("codmp"),array($_POST['mpnum'.$i]), $dsn)){
							$datos['num_cia'.$i] = $_POST['num_cia'];
							$datos['num_proveedor'.$i] = $_POST['num_proveedor'.$i];
							$datos['mpnum'.$i] = $_POST['mpnum'.$i];
							$datos['numero_fact'.$i] = $_POST['numero_fact'.$i];
							$datos['fecha'.$i] = $_POST['fecha'.$i];
							$datos['cantidad'.$i] = $_POST['cantidad'.$i];
							$datos['kilos'.$i] = $_POST['kilos'.$i];
							$datos['precio_unit'.$i] = $_POST['precio_unit'.$i];
							$datos['total'.$i]= $_POST['total'];
							// Insertar registro de operacion de usuario en la tabla 'registro'
							//$session->guardar_registro_acceso("Porcentaje facturas. ID: $_POST[num_cia]", $dsn);
					else{
						header("location: ./ros_fact_cap.php?codigo_error=3");
						die;
					}
				}
				else{
					header("location ./ros_fact_cap.php?codigo_error=2");
					die;
				}
		}
		else{
			// Regresar al formulario y mandar un mensaje de error
			header("location: ./ros_fact_cap.php?codigo_error=1");
			die;
		}
	}

// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
//echo $db->numfilas;
$db->xinsertar();
// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./ros_fact_cap.php?mensaje=Se+realizo+el+registro+con+exito"

?>