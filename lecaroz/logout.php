<?php
include 'DB.php';
include './includes/dbstatus.php';
include './includes/class.session.inc.php';

$session = new sessionclass();
$session->validar_sesion();

// Insertar registro de salida de usuario en la tabla 'registro'
$session->guardar_registro_acceso('Salida de sistema', $dsn);

// Destruir sesion activa
session_destroy();

// Redireccionar a la pantalla de login
header("location: ./index.php");
?>