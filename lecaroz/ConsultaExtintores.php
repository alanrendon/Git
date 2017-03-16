<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('Modificando');

$text = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ñ');
$html = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;', '&Ntilde;');

if (isset($_POST['accion'])) {
	if ($_POST['accion'] == 'cia') {
		$sql = '
			SELECT
				nombre_corto
					AS
						nombre
			FROM
				catalogo_companias
			WHERE
					num_cia
						BETWEEN
								1
							AND
								899
				AND
					num_cia = ' . $_POST['c'];
		$result = $db->query($sql);
		
		if ($result)
			echo str_replace($text, $html, $result[0]['nombre']);
	}
	else if ($_POST['accion'] == 'search') {
		$sql = '
			SELECT
				id,
				num_cia,
				nombre_corto
					AS
						nombre,
				fecha_caducidad
			FROM
					caducidad_extintores
						ce
				LEFT JOIN
					catalogo_companias
						cc
						USING
							(
								num_cia
							)
			WHERE
					ce.status = 1
		';
		if ($_POST['c'] > 0)
			$sql .= '
				AND
					num_cia = ' . $_POST['c'] . '
			';
		$sql .= '
			ORDER BY
				num_cia,
				id
		';
		$result = $db->query($sql);
		
		if (!$result)
			die;
		
		$html = '    <form class="formulario"><table class="tabla_captura">';
		
		$num_cia = NULL;
		$color = FALSE;
		foreach ($result as $r) {
			if ($num_cia != $r['num_cia']) {
				if ($num_cia != NULL) {
					$html .= '      <tr id="row" class="linea_' . (!$color ? 'off' : 'on') . '">
        <td colspan="3">&nbsp;</td>
      </tr>';
					$color = !$color;
				}
				
				$num_cia = $r['num_cia'];
				
				$html .= '      <tr>
        <th colspan="3" align="left" scope="col">' . $r['num_cia'] . ' ' . $r['nombre'] . ' </th>
      </tr>
      <tr>
        <th>No.</th>
        <th>Fecha Caducidad </th>
        <th><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
      </tr>';
				$i = 1;
			}
			$html .= '      <tr id="row" class="linea_' . (!$color ? 'off' : 'on') . '">
        <td align="center">' . ($i++) . '</td>
        <td align="center">' . $r['fecha_caducidad'] . '</td>
        <td align="center"><img src="imagenes/pencil16x16.png" alt="' . $r['id'] . '" name="mod" width="16" height="16" id="mod" /><img src="imagenes/delete16x16.png" alt="' . $r['id'] . '" name="del" width="16" height="16" id="del" /></td>
      </tr>';
			$color = !$color;
		}
		
		$html .= '    </table></form>';
		
		echo $html;
		
	}
	else if ($_POST['accion'] == 'delete') {
		$sql .= '
			UPDATE
				caducidad_extintores
			SET
				status = 0,
				iduser = ' . $_SESSION['iduser'] . ',
				tsmod = now()
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
	}
	else if ($_POST['accion'] == 'update') {
		$sql = '
			UPDATE
				caducidad_extintores
			SET
				fecha_caducidad = \'' . $_POST['fecha'] . '\',
				iduser = ' . $_SESSION['iduser'] . ',
				tsmod = now()
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ConsultaExtintores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');


$tpl->printToScreen();
?>