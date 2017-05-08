<?php
require_once 'DB.php';
require_once './includes/dbstatus.php';

$db = DB::connect($dsn);//variable con conexión a base de datos
if(DB::isError($db))//manda un error por si algo no salio bien en la conexion
{
	echo "Error al intentar acceder a la Base de Datos.<br>";
	die($db->getMessage());
}

$sql="SELECT iduser, username, password, authlevel, tipo_usuario FROM auth WHERE username = '".strtoupper($_POST['username'])."' AND password = '".strtoupper($_POST['password'])."'";
$result = $db->query($sql); //se ejecuta el query

if(DB::isError($result))//manda un error por si algo no salio bien en la conexion
{
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}

$user = $result->fetchRow(DB_FETCHMODE_OBJECT);

if(!$result->numRows()) { // No existe el usuario o los datos son erroneos
	header("location: ./index.php?loginerror=1");
}
else {
	// [7-Feb-2007] Bloquear para el usuario LILIA
	if ($_SERVER['REMOTE_ADDR'] == '10.24.0.2' && $user->iduser != 3) die(header("location: ./index.php?loginerror=1"));
	
	// Inicia sesión para el usuario y almacena sus datos en variables de sesión
	session_start();
	$_SESSION['iduser'] = $user->iduser;
	$_SESSION['username'] = $user->username;
	$_SESSION['authlevel'] = $user->authlevel;
	$_SESSION['tipo_usuario'] = $user->tipo_usuario;
	
	// [06-Abr-2007] BROMA!!! BROMA!!! BROMA!!!
	/*$users = array(28, 29, 30, 31, 32);
	if (in_array($_SESSION['iduser'], $users)) {
		header('location: ./eval_end.htm');
		die;
	}*/
	
	// Insertar registro de acceso de usuario en la tabla 'registro'
	$sql = "insert into registro (iduser,fecha,ip,navegador,operacion) values($_SESSION[iduser],CURRENT_TIMESTAMP,'$_SERVER[REMOTE_ADDR]','" . substr($_SERVER['HTTP_USER_AGENT'], 0, 100) . "','Entrada al sistema')";
	$result = $db->query($sql);
	$db->disconnect();

	if(DB::isError($result))//manda un error por si algo no salio bien en la conexión
	{
		$db->disconnect();
		echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
		die($result->getMessage());
	}

	// Una vez autentificado mandar a la página principal
	header("location: ./main.php");
}
?>
