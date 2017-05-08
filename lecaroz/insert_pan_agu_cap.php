<?php
// CONEXIONES
// Inserción de registros en una tabla

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
//$tabla = $_GET['tabla'];

$tabla="medidor_agua";

//print_r($_POST);

$db = new Dbclass($dsn,$tabla,$_POST);
$db->xinsertar();
unset($_SESSION['agua']);
// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./pan_agu_cap.php");
?>