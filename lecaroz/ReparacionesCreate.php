<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');


$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {

	if ($_REQUEST['accion']=='alta') {
		$sql="
			INSERT INTO reparacion (

				label,
				proveedor,
				price,
				observations,
				description,
				id_user_create,
				date_create,
				type
			)
			VALUES
				('".$_REQUEST["label"]."',".$_REQUEST["proveedor"].",".$_REQUEST["price"].",'".$_REQUEST["observations"]."','".$_REQUEST["description"]."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,0);
		";
		$db->query($sql);
		echo '
			<div style=" background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;">
				Nueva Reparación Agregada: "' . $_REQUEST["label"].'"
			</div>';
	}
	if ($_REQUEST['accion']=='verif_label') {


		$sql="
			SELECT a.label,a.type FROM reparacion as a WHERE a.type=0 AND a.label='".$_REQUEST["label"]."';
		";

		$result = $db->query($sql);
		if ($result) {
			echo "La Etiqueta : ".$_REQUEST["label"]." ya existe.";
		}else{
			
			$sql="
				SELECT num_proveedor FROM catalogo_proveedores WHERE num_proveedor= '".$_REQUEST["proveedor"]."';
			";
			$result = $db->query($sql);
			if ($result) {
				echo -1;
			}else{
				echo "Ingrese un numero de proveedor valido.";
			}
		}
	}

	if ($_REQUEST['accion']=='validarPro') {
		$sql = '
			SELECT nombre AS nombre_pro FROM catalogo_proveedores WHERE num_proveedor= ' . $_REQUEST['departamento'] . '
		';

		$result = $db->query($sql);

		if ($result) {
			$data = array();

			$num_pro = NULL;
			foreach ($result as $rec) {
				$data = array(
					'nombre_pro' => utf8_encode($rec['nombre_pro'])
				);
				
			}

			echo json_encode($data);
		}
	}

	die();
}

$sql="SELECT num_proveedor,nombre FROM catalogo_proveedores ORDER BY nombre ASC";

$result = $db->query($sql);
$cad="";
if ($result) {
	foreach ($result as $key) {
		
		$cad.='
			<option value="'.$key["num_proveedor"].'">'.$key["nombre"].'</option>
		';
	}
}


$tpl = new TemplatePower('plantillas/rep/CreateReparaciones.tpl');
$tpl->prepare();


$tpl->assign('menucnt', 'rep_cnt.js');
$tpl->assign('options', $cad);


$tpl->printToScreen();
?>
