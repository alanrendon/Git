<?php
include './includes/class.session2.inc.php';
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$tabla = $_GET['tabla'];

switch ($tabla) {
	case "catalogo_expendios":
		$db = new DBclass($dsn,$tabla,$_POST);
		$db->generar_script_update("",array("num_cia","num_expendio"),array($db->datos['num_cia'],$db->datos['num_expendio']));
		//echo $db->sql;
		$db->ejecutar_script();
		header("location: ./pan_exp_mod.php?mensaje=Se+actualizo+el+expendio+".$db->datos['num_expendio']."+con+exito");
	break;
}
?>