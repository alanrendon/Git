<?php
// CONEXIONES
// Inserción de registros en una tabla
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
// Validar usuario
$session = new sessionclass($dsn);
// Obtener tabla de trabajo
$tabla = "precios_guerra";
// ---------------------------------------------------------------------------------------
for($i=0;$i<20;$i++)
{
	if($_POST['codmp'.$i] != "")	
	{	
		$datos['num_cia'.$i]=$_POST['num_cia'.$i];
		$datos['codmp'.$i]=$_POST['codmp'.$i];
		$datos['num_proveedor'.$i]=$_POST['proveedor'.$i];
		$datos['precio_compra'.$i]=$_POST['precio_compra'.$i];
		$datos['precio_venta'.$i]=$_POST['precio_venta'.$i];
		
		$nombre = ejecutar_script("SELECT nombre FROM catalogo_mat_primas WHERE codmp = {$_POST['codmp' . $i]}", $dsn);
		
		$datos['nombre_alt' . $i] = trim($_POST['nombre' . $i]) == '' ? $nombre : strtoupper(trim($_POST['nombre' . $i]));
	}
}
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
$db->xinsertar();
// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./ros_precios_cap.php");
?>