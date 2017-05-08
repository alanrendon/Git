<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');


$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);





if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="view_updates" ) {
		if (!empty($_REQUEST["id"])) {
			$sql="
			SELECT
				b.id_depto_upd,
				b.observations,
				au.username,
				date_upd,
				label
			FROM
				depto_upd AS b
			INNER JOIN auth as au on au.iduser=b.id_user_upd
			WHERE b.id_ref=".$_REQUEST["id"];

			$sql.=" ORDER BY b.date_upd desc";
			$result = $db->query($sql);

			$ht='
			<div id="titulo">Lista de Actualizaciones</div>
			<div id="captura" align="center">
				<table class="tabla_captura" style="min-width: 800px; max-width: 1300px;">
					<thead>
						<tr>
							<th>Fecha de Actualizacion</th>
							<th>Nombre</th>
							<th>Observaciones</th>
							<th>Usuario Modificó</th>
						</tr>
					</thead>
					<tbody id="list" name="list">
			';


			

			if ($result) {
				$line="linea_off";
				foreach ($result as $key ) {
					if ($line=="linea_off") {
						$line="linea_on";
					}else{
						$line="linea_off";
					}

					$ht.='
						<tr id="row4" class="'.$line.'">
							<td align="center" valign="top" nowrap="nowrap">'.$key["date_upd"].'</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["label"].'</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["observations"].'</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["username"].'</td>
						</tr>
					';
				}
			}else{

				$ht.='
					<tr id="row4" class="'.$line.'">
						<td colspan="4" align="center" valign="top" nowrap="nowrap">Sin Resultados</td>
					</tr>
				';
			}
			$ht.='
					</tbody>
				</table>
			</div>
			';
			echo $ht;
			die();

		}

	}
}






$sql="
	SELECT
		id_depto as id,
		label,
		observations,
		id_user_create,
		date_create,
		ref
	FROM
		depto AS A
	WHERE
		1=1 
";



if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="buscar") {
		if (!empty($_REQUEST["label"])) {
			$sql.=" AND label LIKE '%".$_REQUEST["label"]."%' ";
		}

		if (!empty($_REQUEST["date_range1"])) {
			$sql.=" AND date_create BETWEEN  '".$_REQUEST["date_range1"]."' AND '".$_REQUEST["date_range2"]."'  ";
		}
	}

	if ($_REQUEST['accion']=="eliminar") {
		if (!empty($_REQUEST["id"])) {
			$sql2="DELETE FROM depto as a WHERE a.id_depto=".$_REQUEST["id"].";";

			$db->query($sql2);
		}
	}
	if ($_REQUEST['accion']=="modif") {
		if (!empty($_REQUEST["id"])) {
			$sql2="UPDATE depto 
			SET  label='".$_REQUEST["label"]."',  observations='".$_REQUEST["observations"]."'
			WHERE id_depto=".$_REQUEST["id"].";";
			$db->query($sql2);
			$sql2="
			INSERT INTO depto_upd (
				label,
				observations,
				id_user_upd,
				date_upd,
				id_ref
			)
			VALUES
				('".$_REQUEST["label"]."','".$_REQUEST["observations"]."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,".$_REQUEST["id"].");
			";
			$db->query($sql2);
			echo "
			<div style='margin-left:44.8%; background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;' >
				Registro Modificado!
			</div>
			";
			die();
		}
	}
}

$sql.=" order by date_create desc;";

$result = $db->query($sql);


$cad="";
$line="linea_off";
if ($result) {
	foreach ($result as $key) {
		
		if ($line=="linea_off") {
			$line="linea_on";
		}else{
			$line="linea_off";
		}

		$cad.='
			<tr id="row4" class="'.$line.'">
				<td valign="top" nowrap="nowrap"><b>'.$key["ref"].'</b></td>
				<td valign="top" nowrap="nowrap">'.$key["label"].'</td>
				<td align="center" valign="top" nowrap="nowrap">'.$key["observations"].'</td>
				<td align="center" valign="top" nowrap="nowrap">
					'.( (!empty($key["date_create"])) ? date('d/m/Y',strtotime($key["date_create"])) : "" ).'
				</td>
				
				<td align="center" valign="top" nowrap="nowrap" >
					<img src="/lecaroz/imagenes/pencil16x16.png" class="mod" width="16" height="16" style="cursor: pointer;" atrib="'.$key["id"].'">
					<img src="/lecaroz/iconos/cancel_round.png" class="elim"  width="16" height="16" style="cursor: pointer;" atrib="'.$key["id"].'">
					<img src="/lecaroz/iconos/calendar2.png" class="updates"  width="16" height="16" style="cursor: pointer;" atrib="'.$key["id"].'">
				</td>
			</tr>
		';
	}
}else{
	$cad.='
		<tr id="row4" class="linea_off">
			<td valign="top" nowrap="nowrap" colspan="5">Sin Resultados</td>
		</tr>
	';
}


if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="buscar" || $_REQUEST['accion']=="eliminar") {
		echo $cad;
		die;
	}
}

if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="actualizar_view" ) {

		$sql="
		SELECT
			id_depto,
			label,
			observations,
			id_user_create,
			date_create,
			ref
		FROM
			depto 
		WHERE
			id_depto = ".$_REQUEST['id'];
		$result = $db->query($sql);

		$tpl = new TemplatePower('plantillas/depto/UpdateDepto.tpl');
		$tpl->prepare();
		$tpl->assign('id', $result[0]["id_depto"]);
		$tpl->assign('ref', $result[0]["ref"]);
		$tpl->assign('label', $result[0]["label"]);
		$tpl->assign('price', $result[0]["price"]);
		$tpl->assign('fecha', ( (!empty($result[0]["date_ref"]))?date('d/m/Y',strtotime($result[0]["date_ref"] )):""));
		$tpl->assign('description', $result[0]["description"]);
		$tpl->assign('observations', $result[0]["observations"]);
		$tpl->printToScreen();
		die;
	}
}







$tpl = new TemplatePower('plantillas/depto/ViewDepto.tpl');
$tpl->prepare();

$fecha=date('01/m/Y');
$fecha2=date('d/m/Y');


$tpl->assign('date_range1', $fecha);
$tpl->assign('date_range2', $fecha2);

$tpl->assign('list', $cad);
$tpl->assign('menucnt', 'depto_cnt.js');


$tpl->printToScreen();
?>
