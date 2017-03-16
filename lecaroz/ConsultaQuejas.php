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
	if ($_POST['accion'] == 'delete') {
		$sql = '
			UPDATE
				quejas_pedidos
			SET
				status = 2
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
	}
	else if ($_POST['accion'] == 'retrieve') {
		$sql = '
			SELECT
					q.status
				||
					\'|\'
				||
					num_cia
				||
					\'|\'
				||
					nombre_corto
				||
					\'|\'
				||
					CASE
						WHEN idclase IS NULL THEN
							0
						ELSE
							idclase
					END
				||
					\'|\'
				||
					quejoso
				||
					\'|\'
				||
					queja
						AS
							data
			FROM
					quejas_pedidos
						q
				LEFT JOIN
					catalogo_companias
						cc
							USING
								(
									num_cia
								)
			WHERE
				id = ' . $_POST['id'] . '
		';
		$result = $db->query($sql);
		
		echo $result[0]['data'];
	}
	else if ($_POST['accion'] == 'retrieveCat') {
		$sql = '
			SELECT
					id
				||
					\',\'
				||
					concepto
						AS
							data
			FROM
				catalogo_clasificacion_quejas
			WHERE
				status = \'TRUE\'
		';
		if ($_POST['id'] > 0)
			$sql .= '
				OR
					id = ' . $_POST['id'] . '
			';
		$sql .= '
			ORDER BY
				concepto
		';
		$result = $db->query($sql);
		
		$data = array();
		if ($result)
			foreach ($result as $d)
				$data[] = $d['data'];
		$string = implode('|', $data);
		
		echo $string;
	}
	else if ($_POST['accion'] == 'update') {
		$sql = '
			UPDATE
				quejas_pedidos
			SET
				num_cia = ' . $_POST['num_cia'] . ',
				idclase = ' . $_POST['clase'] . ',
				quejoso = \'' . $_POST['quejoso'] . '\',
				queja = \'' . $_POST['queja'] . '\',
				iduser = ' . $_SESSION['iduser'] . ',
				tsmod = now()
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
	}
	
	die;
}

if (isset($_GET['list'])) {
	$sql = '
		SELECT
			nombre_administrador
				AS
					admin,
			num_cia,
			nombre_corto
				AS
					nombre,
			date_trunc(\'second\', time_queja)
				AS
					fecha,
			concepto,
			quejoso,
			queja
		FROM
				quejas_pedidos
					q
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
			LEFT JOIN
				catalogo_administradores
					ca
						USING
							(
								idadministrador
							)
			LEFT JOIN
				catalogo_clasificacion_quejas
					ccq
						ON
							(
								ccq.id = q.idclase
							)
	';
	
	$condiciones[] = 'q.status = ' . $_GET['status'];
	if (isset($_GET['num_cia']) && $_GET['num_cia'] > 0)
		$condiciones[] = 'num_cia = ' . $_GET['num_cia'];
	if (isset($_GET['idadmin']) && $_GET['idadmin'] > 0)
		$condiciones[] = 'idadministrador = ' . $_GET['idadmin'];
	if (isset($_GET['tipo']) && $_GET['tipo'] > 0)
		$condiciones[] = 'tipo = ' . $_GET['tipo'];
	if (isset($_GET['tipo']) && $_GET['tipo'] > 0)
		$condiciones[] = 'tipo = ' . $_GET['tipo'];
	if (isset($_GET['idclase']) && $_GET['idclase'] > 0)
		$condiciones[] = 'idclase = ' . $_GET['idclase'];
	if (isset($_GET['fecha1']) && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha1']))
		$condiciones[] = isset($_GET['fecha2']) && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha2']) ? 'date_trunc(\'day\', time_queja) BETWEEN \'' . $_GET['fecha1'] . '\' AND \'' . $_GET['fecha2'] . '\'' : 'date_trunc(\'day\', time_queja) = \'' . $_GET['fecha1'] . '\'';
	
	if (isset($condiciones))
		$sql .= '
			WHERE
				' . implode(' AND ', $condiciones) . '
		';
	
	$sql .= '
		ORDER BY
			admin,
			concepto,
			num_cia,
			fecha
	';
	$result = $db->query($sql);
	
	$tpl = new TemplatePower('plantillas/ped/carta_quejas.tpl');
	$tpl->prepare();
	
	$class = NULL;
	$admin = NULL;
	foreach ($result as $r) {
		if ($admin != $r['admin'] || $class != $r['concepto']) {
			if ($class != NULL) {
				$tpl->assign('carta.salto', '<br style="page-break-after:always;">');
			}
			
			$admin = $r['admin'];
			$class = $r['concepto'];
			
			$tpl->newBlock('carta');
			$tpl->assign('dia', date('d'));
			$tpl->assign('mes', mes_escrito(date('n')));
			$tpl->assign('anio', date('Y'));
			$tpl->assign('clase', $class);
			$tpl->assign('admin', $r['admin']);
		}
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $r['num_cia']);
		$tpl->assign('nombre', $r['nombre']);
		$tpl->assign('fecha', $r['fecha']);
		$tpl->assign('quejoso', $r['quejoso']);
		$tpl->assign('queja', $r['queja']);
	}
	$tpl->printToScreen();
	die;
}

// Obtener compañía
if (isset($_GET['c'])) {
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
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo str_replace($text, $html, $result[0]['nombre']);
	
	die;
}

if (isset($_GET['id'])) {
	$sql = '
		SELECT
			queja
		FROM
			quejas_pedidos
		WHERE
			id = ' . $_GET['id'] . '
	';
	$queja = $db->query($sql);
	
	if ($_GET['accion'] == '+')
		echo str_replace($text, $html, $queja[0]['queja']);
	else
		echo str_replace($text, $html, substr($queja[0]['queja'], 0, 30)) . (strlen($queja[0]['queja']) > 33 ? '...' : '');
	die;
}

if (isset($_GET['num_cia'])) {
	$sql = '
		SELECT
			q.id,
			num_cia,
			nombre_corto
				AS
					nombre,
			date_trunc(\'second\', time_queja)
				AS
					fecha,
			concepto,
			quejoso,
			queja
		FROM
				quejas_pedidos
					q
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
			LEFT JOIN
				catalogo_clasificacion_quejas
					ccq
						ON
							(
								ccq.id = q.idclase
							)
	';
	
	$condiciones[] = 'q.status = ' . $_GET['status'];
	if ($_GET['num_cia'] > 0)
		$condiciones[] = 'num_cia = ' . $_GET['num_cia'];
	if ($_GET['idadmin'] > 0)
		$condiciones[] = 'idadministrador = ' . $_GET['idadmin'];
	if (isset($_GET['tipo']) && $_GET['tipo'] > 0)
		$condiciones[] = 'tipo = ' . $_GET['tipo'];
	if ($_GET['idclase'] > 0)
		$condiciones[] = 'idclase = ' . $_GET['idclase'];
	if (ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha1']))
		$condiciones[] = ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha2']) ? 'date_trunc(\'day\', time_queja) BETWEEN \'' . $_GET['fecha1'] . '\' AND \'' . $_GET['fecha2'] . '\'' : 'date_trunc(\'day\', time_queja) = \'' . $_GET['fecha1'] . '\'';
	
	if (isset($condiciones))
		$sql .= '
			WHERE
				' . implode(' AND ', $condiciones) . '
		';
	
	$sql .= '
		ORDER BY
			num_cia,
			fecha
	';
	$result = $db->query($sql);
	
	if (!$result)
		die;
	
	$data = '<form action="ConsultaQuejas.php" method="get" name="Listado" class="formulario" id="Listado">
	  <table class="tabla_captura">
        <tr>
          <th scope="col"><input name="checkall" type="checkbox" class="checkbox" id="checkall" /></th>
          <th scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
          <th scope="col">Compa&ntilde;&iacute;a</th>
		  <th scope="col">Clasificaci&oacute;n</th>
          <th scope="col">Fecha</th>
          <th scope="col">Reporta</th>
          <th scope="col">Mensaje</th>
        </tr>';
	foreach ($result as $k => $v)
		$data .= '<tr class="linea_' . ($k % 2 == 0 ? 'off' : 'on') . '">
          <td align="center" valign="top"><input name="id[]" type="checkbox" class="checkbox" id="id" value="' . $v['id'] . '" /></td>
          <td align="center" valign="top"><img src="imagenes/pencil16x16.png" name="mod" width="16" height="16" id="mod" /><img src="imagenes/delete16x16.png" name="del" width="16" height="16" id="del" /></td>
          <td valign="top">' . $v['num_cia'] . ' ' . str_replace($text, $html, $v['nombre']) . '</td>
		  <td valign="top">' . $v['concepto'] . '</td>
          <td align="center" valign="top">' . $v['fecha'] . '</td>
          <td valign="top">' . $v['quejoso'] . '</td>
          <td valign="top">
		    <div id="queja' . $k . '" style="float:left;width:300px;text-align:left">' . str_replace($text, $html, substr($v['queja'], 0, 30)) . (strlen($v['queja']) > 33 ? '...' : '') . '</div>
		    <div style="margin-left:300px;"><a href="javascript:cambiar(' . $k . ',' . $v['id'] . ',\'+\')" id="cambiar' . $k . '" class="aplus">[+]</a></div>
		  </td>
        </tr>';
	
	$data .= '</table>
      <p>
	  	<input name="nueva" type="button" class="boton" id="nueva" value="Nueva B&uacute;squeda" />
		&nbsp;&nbsp;&nbsp;
        <input name="imprimir" type="button" class="boton" id="imprimir" value="Imprimir" />
      </p>
	</form>';
	
	echo $data;
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/ConsultaQuejas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				admin
	FROM
		catalogo_administradores
	ORDER BY
		admin
';
$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

$sql = '
	SELECT
		id,
		concepto
	FROM
		catalogo_clasificacion_quejas
	WHERE
		status = \'TRUE\'
	ORDER BY
		concepto,
		id
';
$clasificaciones = $db->query($sql);

if ($clasificaciones)
	foreach ($clasificaciones as $c) {
		$tpl->newBlock('clase');
		$tpl->assign('id', $c['id']);
		$tpl->assign('concepto', $c['concepto']);
	}

$tpl->printToScreen();
?>