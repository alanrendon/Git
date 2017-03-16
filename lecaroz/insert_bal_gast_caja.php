<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
// Validar usuario
$session = new sessionclass($dsn);
// Obtener tabla de trabajo
//$tabla = $_GET['tabla'];
$tabla="gastos_caja";
// -------------------------------------------------------------------------------------
for ($i=0;$i<$_POST['contador1'];$i++) {
	if($_POST['num_cia'.$i] != ""){
		if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn)){

			$datos['num_cia'.$i]=$_POST['num_cia'.$i];
			$datos['cod_gastos'.$i]=$_POST['concepto'.$i];
			$datos['importe'.$i]=$_POST['importe'.$i];
			$datos['clave_balance'.$i]=$_POST['balance'.$i];
			$datos['fecha'.$i]=$_POST['fecha'.$i];
			$datos['tipo_mov'.$i]=$_POST['tipo_mov'.$i];
			$datos['fecha_captura'.$i]=date("d/m/Y");
			$datos['comentario'.$i]=strtoupper($_POST['comentario'.$i]);
			// Insertar registro de operacion de usuario en la tabla 'registro'
			//$session->guardar_registro_acceso("Gastos de oficina. ID: $_POST[num_cia]", $dsn);
		}
		else{
			// Regresar al formulario y mandar un mensaje de error
			header("location: ./bal_gast_caja.php?codigo_error=1");
			die;
		}
	}
}
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
$db->xinsertar();
// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./bal_gast_caja_v2.php");
?>