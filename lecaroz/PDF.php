<?php
		include('includes/class.db.inc.php');
		include('includes/class.session2.inc.php');
		include('includes/class.TemplatePower.inc.php');
		include('includes/dbstatus.php');

		require_once 'mpdf/mpdf.php';

		$pdf=new mPDF('c','A4','','');
		$db = new DBclass($dsn, 'autocommit=yes');
		$cad="";
		if (isset($_REQUEST["mot"])) {
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

			$sql.=" order by date_create desc;";
			$result = $db->query($sql);

			$cad='
				<table class="tabla_captura" style="width: 100%; " border="1" cellpadding="7" cellspacing="0">
					<thead>
						<tr style="background-color: rgb(204, 204, 204);">
							<th>Pregunta</th>
							<th>Departamento</th>
							<th>Periodicidad</th>
							<th>Enviar Correo al Contestar?</th>
							<th>Observaciones</th>
						</tr>
					</thead>
					<tbody id="list" name="list">
			';
			if ($result) {
				foreach ($result as $key) {
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
						</tr>
					';

				}
			}
			$cad.='
					</tbody>
				</table>
			';
			








		}else{
			$sql="
				SELECT
					id,
					label,
					price,
					observations,
					id_user_create,
					date_create,
					description
				FROM
					reparacion AS A
				WHERE
					1=1 
			";
			$type=0;

			if (!empty($_REQUEST["type"])) {
				$sql.=" AND type = ".$_REQUEST["type"]." ";
				$type=$_REQUEST["type"];
			}else{
				$sql.=" AND type = 0 ";
				$type=0;
			}
			if (!empty($_REQUEST["label"])) {
				$sql.=" AND label LIKE '%".$_REQUEST["label"]."%' ";
			}

			if (!empty($_REQUEST["date_range1"])) {
				$sql.=" AND date_create BETWEEN  '".$_REQUEST["date_range1"]."' AND '".$_REQUEST["date_range2"]."'  ";
			}
		


			$cad='
				<table class="tabla_captura" style="width: 100%; " border="1" cellpadding="7" cellspacing="0">
					<thead>
						<tr style="background-color: rgb(204, 204, 204);">
							<th>';

							if ($type==1) {
								$cad.="No de Refacción";
							}else{
								$cad.="No. Reparación";
							}

							

					$cad.='</th>
							<th>Precio</th>

							<th>Fecha de creación</th>
							<th>Descripción</th>
							<th>Observaciones</th>
						</tr>
					</thead>
					<tbody id="list" name="list">
			';

			$result = $db->query($sql);
			if ($result) {
				foreach ($result as $key) {
					
					if ($line=="linea_off") {
						$line="linea_on";
					}else{
						$line="linea_off";
					}

					$cad.='
						<tr id="row4" class="'.$line.'">
							<td align="center" nowrap="nowrap">'.$key["label"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["price"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["date_create"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["description"].'</td>
							<td align="center" valign="top" nowrap="nowrap">'.$key["observations"].'</td>
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

			$cad.='
					</tbody>
				</table>
			';
		}
		


		$pdf->writeHTML($cad);
		$pdf->Output('Reporte.pdf','I');
?>