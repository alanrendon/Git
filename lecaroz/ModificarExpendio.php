<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cia':
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
						num_cia = ' . $_REQUEST['num_cia'] . '
			';

			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62))) {
				$sql .= '
					AND
						iduser = ' . $_SESSION['iduser'] . '
				';
			}

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
		break;

		case 'buscar':
			$conditions[] = 'num_cia = ' . $_REQUEST['num_cia'];

			if (isset($_REQUEST['num_expendio'])) {
				$conditions[] = 'num_expendio = ' . $_REQUEST['num_expendio'];
			}

			$sql = '
				SELECT
					id,
					num_expendio,
					nombre
				FROM
					catalogo_expendios
				WHERE
					' . implode(' AND ', $conditions) . '
				ORDER BY
					num_expendio
			';
			$result = $db->query($sql);

			if (!$result) {
				echo '{"status":"-1"}';
			}
			else if (count($result) > 1) {
				$json = '{\'status\':\'1\',\'html\':\'';
				$json .= '<table id="resultTable" class="tabla_captura"><tr><th scope="col">Resultados</th></tr>';

				foreach ($result as $i => $r) {
					$json .= '<tr><td class="linea_' . ($i % 2 == 0 ? 'off' : 'on') . '"><input name="id" type="radio" value="' . $r['id'] . '" />' . $r['num_expendio'] . ' ' . $r['nombre'] . '</td></tr>';
				}

				$json .= '</table><p><input name="modificar" type="button" class="boton_no_form" id="modificar" value="Modificar" /></p>\'}';

				echo $json;
			}
			else {
				echo '{"status":"2","id":"' . $result[0]['id'] . '"}';
			}
		break;

		case 'modificar':
			$sql = '
				SELECT
					id,
					ce.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					ce.nombre,
					ce.direccion,
					porciento_ganancia,
					num_expendio,
					num_referencia,
					tipo_expendio,
					importe_fijo,
					total_fijo,
					notas,
					aut_dev,
					tipo_devolucion,
					devolucion_maxima,
					devolucion_fin_mes,
					idagven,
					num_cia_exp,
					cce.nombre_corto
						AS
							nombre_cia_exp,
					COALESCE(paga_renta, FALSE)
						AS paga_renta
				FROM
						catalogo_expendios
							ce
					LEFT JOIN
						catalogo_companias
							cc
								ON
									(
										cc.num_cia = ce.num_cia
									)
					LEFT JOIN
						catalogo_companias
							cce
								ON
									(
										cce.num_cia = ce.num_cia_exp
									)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			$r = $result[0];

			$sql = '
				SELECT
					rezago
				FROM
					mov_expendios
				WHERE
						num_cia = ' . $r['num_cia'] . '
					AND
						num_expendio = ' . $r['num_expendio'] . '
					AND
						nombre_expendio = \'' . $r['nombre'] . '\'
				ORDER BY
					fecha DESC
				LIMIT
					1
			';
			$result = $db->query($sql);

			$rezago = $result ? round($result[0]['rezago'], 2) : 0;

			$html = '<form name="Datos" class="formulario" id="Datos"><table class="tabla_captura"><input name="id" id="id" type="hidden" value="' . $r['id'] . '" />
      <tr>
        <th align="left">Compa&ntilde;&iacute;a</th>
        <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="3" value="' . $r['num_cia'] . '" readonly />
          <input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" value="' . $r['nombre_cia'] . '" /></td>
      </tr>
      <tr>
        <th align="left">No. expendio</th>
        <td class="linea_on"><input name="num_expendio" type="text" class="cap toPosInt alignCenter" id="num_expendio" size="3" value="' . $r['num_expendio'] . '" readonly /></td>
      </tr>
      <tr>
        <th align="left">No. expendio (panader&iacute;a) </th>
        <td class="linea_off"><input name="num_referencia" type="text" class="cap toPosInt alignCenter" id="num_referencia" size="3" value="' . $r['num_referencia'] . '" /></td>
      </tr>
      <tr>
        <th align="left">Nombre expendio </th>
        <td class="linea_on"><input name="nombre" type="text" class="cap toText toUpper clean" id="nombre" size="45" maxlength="45" value="' . $r['nombre'] . '"' . ($rezago != 0 ? ' readonly' : '') . ' />' . ($rezago != 0 ? ' <span style="color:#C00;font-weight:bold;font-size:6pt;">(Tiene rezago, no se puede cambiar)</span>' : '') . '</td>
      </tr>
      <tr>
        <th align="left">Tipo</th>
        <td class="linea_off"><select name="tipo" id="tipo">';

			$sql = '
				SELECT
					idtipoexpendio
						AS
							id,
					descripcion
						AS
							nombre
				FROM
					tipo_expendio
				ORDER BY
					id
			';
			$tipos = $db->query($sql);

			foreach ($tipos as $t) {
				$html .= '<option value="' . $t['id'] . '"' . ($t['id'] == $r['tipo_expendio'] ? ' selected' : '') . '>' . $t['nombre'] . '</option>';
			}

			$html .= '
        </select></td>
      </tr>
      <tr>
        <th align="left">Direcci&oacute;n</th>
        <td class="linea_on"><textarea name="direccion" cols="30" rows="3" class="cap toText toUpper clean" id="direccion" style="width:98%;">' . $r['direccion'] . '</textarea></td>
      </tr>
      <tr>
        <th align="left">% de ganancia </th>
        <td class="linea_off"><input name="porciento_ganancia" type="text" class="cap numPosFormat3 alignRight" id="porciento_ganancia" value="' . number_format($r['porciento_ganancia'], 2, '.', ',') . '" size="6" maxlength="6"' . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' readonly' : '') . ' />' . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' <span style="color:#C00;font-weight:bold;font-size:6pt;">(Autorizado por Miguel Rebuelta)</span>' : '') . '</td>
      </tr>
      <tr>
        <th align="left">Importe fijo </th>
        <td class="linea_on"><input name="importe_fijo" type="text" class="cap numPosFormat2 alignRight" id="importe_fijo" size="10" value="' . ($r['importe_fijo'] > 0 ? number_format($r['importe_fijo'], 2, '.', ',') : '') . '" /></td>
      </tr>
      <tr>
        <th align="left">Total fijo </th>
        <td class="linea_off"><input name="total_fijo" type="checkbox" class="checkbox" id="total_fijo" value="1"' . ($r['total_fijo'] == 't' ? ' checked' : '') . ' />
          Si</td>
      </tr>
      <tr>
        <th align="left">Expendio por notas de pastel </th>
        <td class="linea_on"><input name="notas" type="checkbox" class="checkbox" id="notas" value="1"' . ($r['notas'] == 't' ? ' checked' : '') . ' />
          Si</td>
      </tr>
      <tr>
        <th align="left">Autoriza devoluci&oacute;n </th>
        <td class="linea_off"><input name="aut_dev" type="checkbox" class="checkbox" id="aut_dev" value="1"' . ($r['aut_dev'] == 't' ? ' checked' : '') . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' disabled' : '') . ' />
          Si' . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' <span style="color:#C00;font-weight:bold;font-size:6pt;">(Autorizado por Miguel Rebuelta)</span>' : '') . '</td>
      </tr>
      <tr>
        <th align="left">Autoriza devoluci&oacute;n total en fin de mes</th>
        <td class="linea_off"><input name="devolucion_fin_mes" type="checkbox" class="checkbox" id="devolucion_fin_mes" value="1"' . ($r['devolucion_fin_mes'] == 't' ? ' checked' : '') . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' disabled' : '') . ' />
          Si' . (!in_array($_SESSION['iduser'], array(1, 4, 19, 42, 57, 48, 62)) ? ' <span style="color:#C00;font-weight:bold;font-size:6pt;">(Autorizado por Miguel Rebuelta)</span>' : '') . '</td>
      </tr>
      <tr>
        <th align="left">Tipo de devoluci&oacute;n </th>
        <td class="linea_on">
          <input name="tipo_devolucion" type="radio" id="tipo_devolucion_0" value="0"' . ($r['tipo_devolucion'] == 0 ? ' checked="checked"' : '') . ' />Porcentaje<br />
          <input name="tipo_devolucion" type="radio" id="tipo_devolucion_1" value="1"' . ($r['tipo_devolucion'] == 1 ? ' checked="checked"' : '') . ' />Importe<br />
        </td>
      </tr>
      <tr>
        <th align="left"><span id="tipo_devolucion_span">' . ($r['tipo_devolucion'] == 0 ? 'Porcentaje' : 'Importe') . '</span> de devoluci&oacute;n m&aacute;ximo</th>
        <td class="linea_off"><input name="devolucion_maxima" type="text" class="cap numPosFormat2 alignRight" id="devolucion_maxima" size="10" value="' . ($r['devolucion_maxima'] > 0 ? number_format($r['devolucion_maxima'], 2) : '') . '" /></td></td>
      </tr>
      <tr>
        <th align="left">Agente de ventas </th>
        <td class="linea_on"><select name="idagven" id="idagven">
          <option value=""></option>';

			$sql = '
				SELECT
					idagven
						AS
							id,
					nombre
				FROM
					catalogo_agentes_venta
				ORDER BY
					nombre
			';
			$agentes = $db->query($sql);

			foreach ($agentes as $a) {
				$html .= '<option value="' . $a['id'] . '"' . ($a['id'] == $r['idagven'] ? ' selected' : '') . '>' . $a['nombre'] . '</option>';
			}

			$html .= '
        </select>
        </td>
      </tr>
      <tr>
        <th align="left">Reparte a </th>
        <td class="linea_off"><input name="num_cia_exp" type="text" class="cap toPosInt alignCenter" id="num_cia_exp" size="3" value="' . $r['num_cia_exp'] . '" />
          <input name="nombre_cia_exp" type="text" class="disabled" id="nombre_cia_exp" size="30" value="' . $r['nombre_cia_exp'] . '" /></td>
      </tr>
	  <tr>
        <th align="left">Paga renta</th>
        <td class="linea_on"><input name="paga_renta" type="radio" class="checkbox" value="FALSE" ' . ($r['paga_renta'] == 'f' ? 'checked="checked"' : '') . ' />
          No
          <input name="paga_renta" type="radio" class="checkbox" value="TRUE" ' . ($r['paga_renta'] == 't' ? 'checked="checked"' : '') . ' />
          Si</td>
      </tr>
    </table><p>
	    <input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;&nbsp;
        <input name="modificar" type="button" class="boton" id="modificar" value="Modificar" />
      </p>
    </form>';

			echo $html;
		break;

		case 'actualizar':
			$sql = '
				UPDATE
					catalogo_expendios
				SET
					num_referencia = ' . (isset($_REQUEST['num_referencia']) ? $_REQUEST['num_referencia'] : 'NULL') . ',
					nombre = \'' . $_REQUEST['nombre'] . '\',
					tipo_expendio = ' . $_REQUEST['tipo'] . ',
					direccion = ' . (isset($_REQUEST['direccion']) ? '\'' . $_REQUEST['direccion'] . '\'' : 'NULL') . ',
					porciento_ganancia = ' . (isset($_REQUEST['porciento_ganancia']) ? $_REQUEST['porciento_ganancia'] : 0) . ',
					importe_fijo = ' . (isset($_REQUEST['importe_fijo']) && get_val($_REQUEST['importe_fijo']) > 0 ? get_val($_REQUEST['importe_fijo']) : 'NULL') . ',
					total_fijo = \'' . (isset($_REQUEST['total_fijo']) ? 'TRUE' : 'FALSE') . '\',
					notas = \'' . (isset($_REQUEST['notas']) ? 'TRUE' : 'FALSE') . '\',
					' . (in_array($_SESSION['iduser'], array(1, 4, 19, 42, 62)) ? 'aut_dev = ' . (isset($_REQUEST['aut_dev']) ? 'TRUE' : 'FALSE') . ',' : '') . '
					' . (in_array($_SESSION['iduser'], array(1, 4, 19, 42, 62)) ? 'devolucion_fin_mes = ' . (isset($_REQUEST['devolucion_fin_mes']) ? 'TRUE' : 'FALSE') . ',' : '') . '
					' . (in_array($_SESSION['iduser'], array(1, 4, 19, 42, 62)) ? 'tipo_devolucion = ' . (isset($_REQUEST['aut_dev']) ? $_REQUEST['tipo_devolucion'] : '0') . ',' : '') . '
					' . (in_array($_SESSION['iduser'], array(1, 4, 19, 42, 62)) ? 'devolucion_maxima = ' . (isset($_REQUEST['aut_dev']) ? get_val($_REQUEST['devolucion_maxima']) : '0') . ',' : '') . '
					idagven = ' . (isset($_REQUEST['idagven']) && $_REQUEST['idagven'] > 0 ? $_REQUEST['idagven'] : 'NULL') . ',
					num_cia_exp = ' . (isset($_REQUEST['num_cia_exp']) && $_REQUEST['num_cia_exp'] > 0 ? $_REQUEST['num_cia_exp'] : 'NULL') . ',
					paga_renta = ' . (isset($_REQUEST['paga_renta']) && $_REQUEST['paga_renta'] != '' ? $_REQUEST['paga_renta'] : 'NULL') . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			// [11-Mar-2014] Guardar movimiento en la tabla de modificaciones de panaderias
			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						iduser,
						metodo,
						parametros
					)
					VALUES (
						{$_REQUEST['num_cia']},
						{$_SESSION['iduser']},
						'modificar_expendio',
						'num_cia={$_REQUEST['num_cia']}&num_expendio={$_REQUEST['num_expendio']}'
					);\n
			";

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ModificarExpendio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
