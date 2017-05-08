<?php
// PORCENTAJES FACTURAS
// Tabla 'porcentajes_facturas'
// Menu 'Rosticerias

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
$tabla = $_GET['tabla'];
for ($i=0;$i<10;$i++) 
	{
		//revisa los renglones del bloque, si no encuentra numero de gasto no lo toma en cuenta
		if($_POST['num_cia'.$i] != "")
		{
			//revisa que el gasto se encuentre dentro del catalogo de gastos
			if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn))
			{
				if($_POST['porcentaje_13'.$i] + $_POST['porcentaje_795'.$i] == 100){
					$datos['num_cia'.$i]=$_POST['num_cia'.$i];
					$datos['porcentaje_13'.$i]=$_POST['porcentaje_13'.$i];
					$datos['porcentaje_795'.$i]=$_POST['porcentaje_795'.$i];
					}
				else{
					header("location: ./ros_porc_fact.php?codigo_error=2");
					die;
					}
			}
			else
			{
				header("location: ./ros_porc_fact.php?codigo_error=1");
				die;
			}
		}
	}

$db = new DBclass($dsn, $tabla, $datos);

$db->xinsertar();
header("location: ./ros_porc_fact.php?mensaje=Se+realizo+el+registro+con+exito");
?>