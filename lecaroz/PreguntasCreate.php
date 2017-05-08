<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');


$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=='alta') {
		if ($_REQUEST["correo"]) {
			$correo="t";
		}else{
			$correo="f";
		}
		$sql="
			INSERT INTO preguntas (
				pregunta,
				departamento,
				observations,
				correo,
				id_user_create,
				date_create,
				periodicidad
			)
			VALUES
				('".$_REQUEST["pregunta"]."','".$_REQUEST["departamento"]."','".$_REQUEST["observations"]."','".$correo."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,".$_REQUEST['peri'].");
		";
		$db->query($sql);
		echo '
		<div style="background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;">
			Nueva Pregunta Registrada
		</div>';
	}
	if ($_REQUEST['accion']=='verif_label') {
		$sql="
			SELECT a.pregunta FROM preguntas as a WHERE a.pregunta='".$_REQUEST["pregunta"]."';
		";
		$result = $db->query($sql);
		if ($result) {
			echo -1;
		}else{
			echo 1;
		}

		
	}
	if ($_REQUEST['accion']=='validarDep') {
		$sql = " 
			SELECT a.label AS nombre_pro,a.id_depto FROM depto as a WHERE a.ref= '" . $_REQUEST['departamento'] . "'
		";

		$result = $db->query($sql);

		if ($result) {
			$data = array();

			$num_pro = NULL;
			foreach ($result as $rec) {
				$data = array(
					'nombre_pro' => utf8_encode($rec['nombre_pro']),
					'id_depto' => utf8_encode($rec['id_depto'])
				);
				
			}

			echo json_encode($data);
		}
	}

	die();
}


$tpl = new TemplatePower('plantillas/pre/CreatePreguntas.tpl');
$tpl->prepare();


$tpl->assign('menucnt', 'pre_cnt.js');


$tpl->printToScreen();
?>
