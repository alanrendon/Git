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
			INSERT INTO depto (
				ref,
				label,
				observations,
				id_user_create,
				date_create
			)
			VALUES
				('".$_REQUEST["ref"]."','".$_REQUEST["label"]."','".$_REQUEST["observations"]."',".$_SESSION['iduser'].",now());
		";
		$db->query($sql);
		echo '
			<div style=" background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;">
				Nuevo Departamento Agregado
			</div>';
	}
	if ($_REQUEST['accion']=='verif_label') {
		$sql="
			SELECT a.label FROM depto as a WHERE a.ref='".$_REQUEST["ref"]."';
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


$tpl = new TemplatePower('plantillas/depto/CreateDepto.tpl');
$tpl->prepare();
$sql="
	SELECT max(a.id_depto)+1 as refac FROM depto as a;
";
$result = $db->query($sql);


$tpl->assign('ref', $result[0]["refac"] );


$tpl->assign('menucnt', 'depto_cnt.js');


$tpl->printToScreen();
?>
