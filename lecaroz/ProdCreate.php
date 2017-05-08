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
				price,
				observations,
				description,
				id_user_create,
				date_create,
				type,
				date_ref
			)
			VALUES
				('".$_REQUEST["label"]."',".$_REQUEST["price"].",'".$_REQUEST["observations"]."','".$_REQUEST["description"]."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,1,'".$_REQUEST["fecha"]."');
		";
		$db->query($sql);
		echo '
			<div style=" background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;">
				Nueva Refacción Agregada: "' . $_REQUEST["label"].'"
			</div>';
	}
	if ($_REQUEST['accion']=='verif_label') {
		$sql="
			SELECT a.label FROM reparacion as a WHERE a.type=1 AND a.label='".$_REQUEST["label"]."';
		";
		$result = $db->query($sql);
		if ($result) {
			echo -1;
		}else{
			echo 1;
		}

		
	}

	if ($_REQUEST['accion']=='get_ref') {
		$sql="
			SELECT max(a.id)+1 as refac FROM reparacion as a;
		";
		$result = $db->query($sql);
		$result[0]["refac"]="REF-".sprintf("%05s",$result[0]["refac"]);
		echo $result[0]["refac"];
	}

	die();
}


$tpl = new TemplatePower('plantillas/ref/CreateRefacciones.tpl');
$tpl->prepare();

$sql="
	SELECT max(a.id)+1 as refac FROM reparacion as a;
";
$result = $db->query($sql);
$result[0]["refac"]="REF-".sprintf("%05s",$result[0]["refac"]);
$tpl->assign('ref', $result[0]["refac"] );


$tpl->assign('menucnt', 'ref_cnt.js');


$tpl->printToScreen();
?>
