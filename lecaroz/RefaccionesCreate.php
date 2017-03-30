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
			INSERT INTO refaccion (

				num_part,
				price,
				observations,
				description,
				id_user_create,
				date_create
			)
			VALUES
				('".$_REQUEST["num_part"]."',".$_REQUEST["price"].",'".$_REQUEST["observations"]."','".$_REQUEST["description"]."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP);
		";
		$db->query($sql);
		echo 'Registrado nuevo refacciones: "' . $_REQUEST["num_part"].'"';
	}
	if ($_REQUEST['accion']=='verif_label') {
		$sql="
			SELECT a.num_part FROM refaccion as a WHERE a.num_part='".$_REQUEST["num_part"]."';
		";
		$result = $db->query($sql);
		if ($result) {
			echo -1;
		}else{
			echo 1;
		}

		
	}

	die();
}


$tpl = new TemplatePower('plantillas/ref/CreateRefacciones.tpl');
$tpl->prepare();


$tpl->assign('menucnt', 'ref_cnt.js');


$tpl->printToScreen();
?>
