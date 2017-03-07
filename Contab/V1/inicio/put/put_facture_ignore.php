<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once $url[0]."class/admin.class.php";
require_once $url[0]."class/facture_ignore.class.php";

if(isset($_POST['factid']) && isset($_POST['tipo'])){

	$array_facts           =$_POST['factid'];
	$facture_ignore        =new Facture_Ignore();
	$facture_ignore->type =(int)$_POST['tipo'];

	foreach ($array_facts as $key => $value) {
		$facture_ignore->id_facture = (int)$value;
		$facture_ignore->insert();
	}
	exit(json_encode(array('mensaje' =>'Se ingorarán las facturas que seleccionó.')));
}
?>