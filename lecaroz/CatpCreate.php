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
			INSERT INTO catp (
				label,
				observations,
				id_user_create,
				date_create,
				clave_sat
			)
			VALUES
				('".$_REQUEST["label"]."','".$_REQUEST["observations"]."',".$_SESSION['iduser'].",now(),'".$_REQUEST["clave"]."');
		";
		$db->query($sql);
		echo '
			<div style=" background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;">
				Nuevo Método de Pago Agregado
			</div>';
	}
	if ($_REQUEST['accion']=='verif_label') {
		$sql="
			SELECT a.label FROM catp as a WHERE a.type=1 AND a.label='".$_REQUEST["label"]."';
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


$tpl = new TemplatePower('plantillas/catp/CreateCatp.tpl');
$tpl->prepare();



$tpl->assign('menucnt', 'catp_cnt.js');


$tpl->printToScreen();
?>
