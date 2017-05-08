<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_POST['accion'])) {
	if ($_POST['accion'] == 'cia') {
		$sql = '
			SELECT
				nombre_corto
			FROM
					catalogo_companias
				LEFT JOIN
					catalogo_operadoras
						USING
							(
								idoperadora
							)
			WHERE
					num_cia <= 300
				AND
					num_cia = ' . $_POST['num_cia'] . '
		';
		$result = $db->query($sql);
		
		echo $result[0]['nombre_corto'];
	}
	else if ($_POST['accion'] == 'search') {
		$sql = '
			SELECT
				id,
				nombre_operadora,
				num_cia,
				cc.nombre
					AS
						nombre_cia,
				letra_folio,
				num_remi,
				descripcion,
				modificar,
				kilos,
				precio,
				base,
				date_trunc(\'second\', ts_solicitud)
					AS
						fecha
			FROM
					solicitudes_modificacion_pastel
						smp
				LEFT JOIN
					catalogo_companias
						cc
							USING
								(
									num_cia
								)
				LEFT JOIN
					catalogo_operadoras
						co
							USING
								(
									idoperadora
								)
				WHERE
					ts_autorizacion
						IS NULL
				ORDER BY
					nombre_operadora,
					num_cia,
					ts_solicitud
		';
		$result = $db->query($sql);
		
		if ($result) {
			$html = '
   <form action="ConsultaSolicitudesModificacionNotasPastel.php" method="post" name="Resultado" class="formulario" id="Resultado">
      <table class="tabla_captura">';
			
			$operadora = NULL;
			$num_cia = NULL;
			foreach ($result as $i => $r) {
				if ($operadora != $r['nombre_operadora']) {
					if ($operadora != NULL) {
						$html .= '
        <tr>
          <td colspan="5">&nbsp;</td>
        </tr>';
					}
					
					$operadora = $r['nombre_operadora'];
					
					$html .= '
        <tr>
          <th colspan="5" scope="col">' . $r['nombre_operadora'] . '</th>
        </tr>
					';
				}
				if ($num_cia != $r['num_cia']) {
					if ($num_cia != NULL) {
						$html = substr_replace($html, $i, strrpos($html, '{fin}'), 5);
						
						$html .= '
        <tr>
          <td colspan="5">&nbsp;</td>
        </tr>';
					}
					
					$num_cia = $r['num_cia'];
					
					$html .= '
        <tr>
          <th scope="col">' . ($i == 0 ? '<input name="checkall" type="checkbox" class="checkbox" id="checkall" checked="checked" />' : '&nbsp;') . '</th>
          <th colspan="4" align="left" scope="col">' . $r['num_cia'] . ' ' . $r['nombre_cia'] . ' </th>
        </tr>
        <tr>
          <th><input name="" type="checkbox" class="checkbox" onclick="checkBlock(' . $i . ',{fin},this.checked)" value="" checked="checked" /></th>
          <th>Factura</th>
          <th>Descripci&oacute;n</th>
          <th>Modificar</th>
          <th>Solicitado</th>
        </tr>';
				}
				$html .= '
        <tr>
          <td align="center"><input name="id[]" type="checkbox" class="checkbox" id="id" value="' . $r['id'] . '" checked="checked" /></td>
          <td>' . ($r['letra_folio'] != 'X' ? $r['letra_folio'] : '') . $r['num_remi'] . '</td>
          <td>' . $r['descripcion'] . '</td>
          <td>';
			 	switch ($r['modificar']) {
					case 1:
						$opt = array();
						if ($r['kilos'] != 0)
							$opt[] = $r['kilos'] > 0 ? 'KILOS DE MAS' : 'KILOS DE MENOS';
						if ($r['precio'] == 't')
							$opt[] = 'PRECIO';
						if ($r['base'] == 't')
							$opt[] = 'BASE';
						
						$html .= implode(', ', $opt);
					break;
					case 2:
						$html .= 'PAN';
					break;
					case 3:
						$html .= 'FECHA';
					break;
					case 4:
						$html .= 'FECHA DE ENTREGA';
					break;
					case 5:
						$html .= 'CANCELACION';
					break;
					case 6:
						$html .= 'EXTRAVIADA';
					break;
				}
				$html .= '</td>
          <td align="center">' . $r['fecha'] . '</td>
        </tr>';
			}
			if ($num_cia != NULL)
				$html = substr_replace($html, $i, strrpos($html, '{fin}'), 5);
			
			$html .= '
      </table>
      <p>
        <input name="autorizar" type="button" class="boton" id="autorizar" value="Autorizar" />
      </p>
    </form>';
	 		
			echo $html;
		}
	}
	else if ($_POST['accion'] == 'authorize') {
		$sql .= '
			UPDATE
				solicitudes_modificacion_pastel
			SET
				iduser_autorizacion = ' . $_SESSION['iduser'] . ',
				ts_autorizacion = now()
			WHERE
				id
					IN
						(
							' . implode(', ', $_POST['id']) . '
						)
		';
		$db->query($sql);
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/ConsultaSolicitudesModificacionNotasPastel.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idoperadora
			AS
				id,
		nombre_operadora
			AS
				nombre
	FROM
			solicitudes_modificacion_pastel
		LEFT JOIN
			catalogo_companias
				USING
					(
						num_cia
					)
		LEFT JOIN
			catalogo_operadoras
				USING
					(
						idoperadora
					)
	WHERE
		ts_autorizacion
			IS NULL
	GROUP BY
		idoperadora,
		nombre_operadora
	ORDER BY
		nombre_operadora
';
$result = $db->query($sql);

if ($result)
	foreach ($result as $r) {
		$tpl->newBlock('operadora');
		$tpl->assign('id', $r['id']);
		$tpl->assign('nombre', $r['nombre']);
	}

$tpl->printToScreen();
?>