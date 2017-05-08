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
				b.pregunta,
				b.departamento,
				b.observations,
				b.id_user_upd,
				b.date_upd,
				b.correo,
				b.periodicidad,
				au.username
			FROM
				pre_upd AS b
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
							<th>Departamento</th>
							<th>Enviar Correo?</th>
							<th>Observaciones</th>
							<th>Periodicidad</th>
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
					if ($key["correo"]) {
						$correo="Si";
					}else{
						$correo="No";
					}
					$ht.='
						<tr id="row4" class="'.$line.'">
							<td align="center" valign="top" nowrap="nowrap">'.$key["date_upd"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["departamento"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$correo.'</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["observations"].'</td>
							<td align="center" valign="top" nowrap="nowrap" >';

							if ($key["periodicidad"]==1) {
								$ht.='DIARIO';
							}

							if ($key["periodicidad"]==2) {
								$ht.='SEMANAL';
							}

							if ($key["periodicidad"]==3) {
								$ht.='MARZO';
							}


							if ($key["periodicidad"]==4) {
								$ht.='MENSUAL';
							}

							if ($key["periodicidad"]==5) {
								$ht.='MAYO';
							}

							if ($key["periodicidad"]==6) {
								$ht.='ANUAL';
							}

					$ht.='	</td>
							<td align="center" valign="top" nowrap="nowrap" >'.$key["username"].'</td>
						</tr>
					';
				}
			}else{

				$ht.='
					<tr id="row4" class="'.$line.'">
						<td colspan="4" align="center" valign="top" nowrap="nowrap">Sin Resultados</td>
					</tr>';
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
		a.id,
		a.pregunta,
		b.label AS departamento,
		a.observations,
		a.id_user_create,
		a.date_create,
		a.correo,
		a.periodicidad
	FROM
		preguntas AS a
	LEFT JOIN depto AS b ON a.departamento = b.id_depto
	WHERE
		1=1 
";



if (isset($_REQUEST['accion'])) {
	if ($_REQUEST['accion']=="buscar") {
		if (!empty($_REQUEST["departamento"])) {
			$sql.=" AND b.label LIKE '%".$_REQUEST["departamento"]."%' ";
		}

		if (!empty($_REQUEST["peri"]) && $_REQUEST["peri"]>0) {
			$sql.=" AND a.periodicidad=".$_REQUEST["peri"]." ";
		}

		if ($_REQUEST["correo"]=="true") {
			$correo="t";
		}else{
			$correo="f";
		}

		if (!empty($correo )) {
			$sql.=" AND a.correo='".$correo."'  ";
		}


	}
	if ($_REQUEST['accion']=="eliminar") {
		if (!empty($_REQUEST["id"])) {
			$sql2="DELETE FROM preguntas as a WHERE a.id=".$_REQUEST["id"].";";

			$db->query($sql2);
		}
	}
	if ($_REQUEST['accion']=="modif") {
		if (!empty($_REQUEST["id"])) {
			if ($_REQUEST["correo"]) {
				$correo="t";
			}else{
				$correo="f";
			}
			

			
			$sql2="UPDATE preguntas 
			SET  departamento='".$_REQUEST["departamento"]."', observations='".$_REQUEST["observations"]."', correo='".$correo."', periodicidad='".$_REQUEST["peri"]."'
			WHERE id=".$_REQUEST["id"].";";
			$db->query($sql2);
			$sql2="
			INSERT INTO pre_upd (
				departamento,
				observations,
				correo,
				id_user_upd,
				date_upd,
				id_ref,
				periodicidad
			)
			VALUES
				('".$_REQUEST["departamento"]."','".$_REQUEST["observations"]."','".$correo."',".$_SESSION['iduser'].",CURRENT_TIMESTAMP,".$_REQUEST["id"].",'".$_REQUEST["peri"] ."');
			";
			$db->query($sql2);
			echo "
				<div style='background:#32d732; color:#FFFFFF; font-weight:bold; padding:4px; text-align:center; width:10%;'>
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
				<td valign="top" nowrap="nowrap">'.$key["pregunta"].'</td>
				<td align="center" valign="top" nowrap="nowrap">'.$key["departamento"].'</td>
				<td align="center" valign="top" nowrap="nowrap">';

				if ($key["periodicidad"]==1) {
					$cad.='DIARIO';
				}

				if ($key["periodicidad"]==2) {
					$cad.='SEMANAL';
				}

				if ($key["periodicidad"]==3) {
					$cad.='MARZO';
				}

				if ($key["periodicidad"]==4) {
					$cad.='MENSUAL';
				}

				if ($key["periodicidad"]==5) {
					$cad.='MAYO';
				}

				if ($key["periodicidad"]==6) {
					$cad.='ANUAL';
				}
		$cad.=
				'</td>
				<td align="center" valign="top" nowrap="nowrap">'.(($key["correo"]=="t")?"SI": "NO" ).'</td>
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
			a.id,
			a.pregunta,
			a.departamento,
			a.observations,
			a.id_user_create,
			a.date_create,
			a.correo,
			a.periodicidad,
			b.ref,
			b.label
		FROM
			preguntas AS a
		LEFT JOIN depto AS b ON a.departamento = b.id_depto
		WHERE
			a.id = ".$_REQUEST['id'];
		$result = $db->query($sql);

		if ($result[0]["correo"]=="t") {
			$correo='<input type="checkbox" name="correo" id="correo" checked >';
		}else{
			$correo='<input type="checkbox" name="correo" id="correo" >';
		}

		if (!empty($result[0]["periodicidad"])) {
			$peri="";
			if ($result[0]["periodicidad"]==1) {
				$peri.='<option value="1" selected>DIARIO</option>';
			}else{
				$peri.='<option value="1" >DIARIO</option>';
			}

			if ($result[0]["periodicidad"]==2) {
				$peri.='<option value="2" style="background-color:#EEE;" selected>SEMANAL</option>';
			}else{
				$peri.='<option value="2" style="background-color:#EEE;">SEMANAL</option>';
			}

			if ($result[0]["periodicidad"]==3) {
				$peri.='<option value="3" selected>MARZO</option>';
			}else{
				$peri.='<option value="3">MARZO</option>';
			}


			if ($result[0]["periodicidad"]==4) {
				$peri.='<option value="4" style="background-color:#EEE;" selected>MENSUAL</option>';
			}else{
				$peri.='<option value="4" style="background-color:#EEE;">MENSUAL</option>';
			}
			if ($result[0]["periodicidad"]==5) {
				$peri.='<option value="5" selected>MAYO</option>';
			}else{
				$peri.='<option value="5">MAYO</option>';
			}

			if ($result[0]["periodicidad"]==6) {
				$peri.='<option value="6" style="background-color:#EEE;" selected>ANUAL</option>';
			}else{
				$peri.='<option value="6" style="background-color:#EEE;">ANUAL</option>';
			}

		}


		$tpl = new TemplatePower('plantillas/pre/UpdatePreguntas.tpl');
		$tpl->prepare();
		$tpl->assign('id', $result[0]["id"]);
		$tpl->assign("peri",$peri);


		$tpl->assign('pregunta', $result[0]["pregunta"]);
		$tpl->assign('ref', $result[0]["ref"]);
		$tpl->assign('label', $result[0]["label"]);
		$tpl->assign('departamento', $result[0]["departamento"]);
		$tpl->assign('correo', $correo);
		$tpl->assign('observations', $result[0]["observations"]);
		$tpl->printToScreen();
		die;
	}
}







$tpl = new TemplatePower('plantillas/pre/ViewPreguntas.tpl');
$tpl->prepare();


$fecha=date('01/m/Y');
$fecha2=date('d/m/Y');


$tpl->assign('date_range1', $fecha);
$tpl->assign('date_range2', $fecha2);

$tpl->assign('list', $cad);
$tpl->assign('menucnt', 'pre_cnt.js');


$tpl->printToScreen();
?>
