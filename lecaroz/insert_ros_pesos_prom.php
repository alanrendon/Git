<?php
// PESOS PROMEDIOS
// Tabla 'porcentajes_facturas'
// Tabla 'pesos_companias'
//en la tabla de porcentajes_facturas se insertan los datos de compania y los porcentajes
//en la tabla de pesos_companias se insertan los pesos y la compania
// Menu 'Rosticerias

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
// ------------------------------------------------------------------------------------------------------------

$tabla = $_GET['tabla'];
for ($i=0;$i<10;$i++){
	if($_POST['codmp'.$i] != ""){
			//revisa que la compañia se encuentra en el catalogo de gastos
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']), $dsn))
			{
				if (existe_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']), $dsn))
				{
					if (existe_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]), $dsn))
					{
						$datos['num_cia'.$i]=$_POST['num_cia'];
						$datos['codmp'.$i]=$_POST['codmp'.$i];
						$datos['peso_max'.$i]=$_POST['peso_max'.$i];
						$datos['peso_min'.$i]=$_POST['peso_min'.$i];
						$datos['num_proveedor'.$i]=$_POST['num_proveedor'];
		
						// Insertar registro de operacion de usuario en la tabla 'registro'
//						$session->guardar_registro_acceso("Pesos promedios. ID: $_POST[num_cia]", $dsn);
					}
					else{
						header("location: ./ros_pesos_prom.php?codigo_error=2");
						die;
					}
				}
				else{
						header("location: ./ros_pesos_prom.php?codigo_error=3");
						die;
				}
			}
			else{
					header("location: ./ros_pesos_prom.php?codigo_error=1");
					die;
			}
	}
}

$db = new DBclass($dsn,$tabla,$datos);

$db->xinsertar();
header("location: ./ros_pesos_prom.php?mensaje=Se+realizo+el+registro+con+exito");
?>