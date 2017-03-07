<?php
$url[0] = "../";
require_once "../class/facturas.class.php";

if (isset($_POST['societe_type'])) {
	$societe_type=addslashes(trim($_POST['societe_type']));
	switch ($societe_type) {
		case '1':
			$facturas = new Facturas_Cliente();
			print(json_encode($facturas->get_facturasClientes()));
			break;
		case '2':
			$facturas = new Facturas_Cliente();
			print(json_encode($facturas->get_facturasProveedor()));
			break;
		default:
			print(json_encode(array('datos' => 'false')));
	}
}

?>
