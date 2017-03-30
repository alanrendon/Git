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
				b.id,
				b.num_part,
				b.price,
				b.observations,
				b.id_user_upd,
				b.date_upd,
				b.description,
				au.username
			FROM
				ref_upd AS b
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
							<th>Precio</th>
							<th>Descripción</th>
							<th>Observaciones</th>
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
							<td align="center" valign="top" nowrap="nowrap">'.$key["price"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["description"].'</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["observations"].'</td>
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
		id,
		num_part,
		price,
		observations,
		id_user_create,
		date_create,
		description
	FROM
		refaccion AS A
	WHERE
		1=1 
";



if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="buscar") {
		if (!empty($_REQUEST["num_part"])) {
			$sql.=" AND num_part LIKE '%".$_REQUEST["num_part"]."%' ";
		}

		if (!empty($_REQUEST["date_range1"])) {
			$sql.=" AND date_create BETWEEN  '".$_REQUEST["date_range1"]."' AND '".$_REQUEST["date_range2"]."'  ";
		}

	}
	if ($_REQUEST['accion']=="eliminar") {
		if (!empty($_REQUEST["id"])) {
			$sql2="DELETE FROM refaccion as a WHERE a.id=".$_REQUEST["id"].";";

			$db->query($sql2);
		}
	}
	if ($_REQUEST['accion']=="modif") {
		if (!empty($_REQUEST["id"])) {
			$sql2="UPDATE refaccion 
			SET  price=".$_REQUEST["price"].", observations='".$_REQUEST["observations"]."', description='".$_REQUEST["description"]."'
			WHERE id=".$_REQUEST["id"].";";
			$db->query($sql2);
			$sql2="
			INSERT INTO ref_upd (
				price,
				observations,
				description,
				id_user_upd,
				date_upd,
				id_ref
			)
			VALUES
				(".$_REQUEST["price"].",'".$_REQUEST["observations"]."','".$_REQUEST["description"]."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,".$_REQUEST["id"].");
			";
			$db->query($sql2);
			echo "Registro Modificado!";
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
				<td valign="top" nowrap="nowrap">'.$key["num_part"].'</td>
				<td align="center" valign="top" nowrap="nowrap">'.$key["price"].'</td>
				<td align="center" valign="top" nowrap="nowrap">'.$key["date_create"].'</td>
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

		$sql="SELECT
			id,
			label,
			num_part,
			price,
			observations,
			id_user_create,
			date_create,
			description
		FROM
			refaccion AS A
		WHERE
			a.id = ".$_REQUEST['id'];
		$result = $db->query($sql);

		$tpl = new TemplatePower('plantillas/ref/UpdateRefacciones.tpl');
		$tpl->prepare();
		$tpl->assign('id', $result[0]["id"]);
		$tpl->assign('num_part', $result[0]["num_part"]);
		$tpl->assign('price', $result[0]["price"]);
		$tpl->assign('description', $result[0]["description"]);
		$tpl->assign('observations', $result[0]["observations"]);
		$tpl->printToScreen();
		die;
	}
}







$tpl = new TemplatePower('plantillas/ref/ViewRefacciones.tpl');
$tpl->prepare();

$tpl->assign('list', $cad);
$tpl->assign('menucnt', 'ref_cnt.js');


$tpl->printToScreen();
?>
