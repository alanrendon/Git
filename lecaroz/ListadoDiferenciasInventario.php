<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

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
				tipo_cia IN (1, 2)
			AND
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);

	if ($result)
		echo $result[0]['nombre'];

	die;
}

if (isset($_GET['num_cia'])) {
	$fecha = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

	$sql = '
		SELECT
			num_cia,
			cc.nombre AS nombre_cia,
			codmp,
			cmp.nombre AS producto,
			hi.precio_unidad,
			-- ifm.precio_unidad,
			-- ifm.existencia,
			hi.existencia + COALESCE((SELECT CASE WHEN tipo_mov = FALSE THEN -cantidad ELSE cantidad END FROM mov_inv_real WHERE num_cia = ifm.num_cia AND fecha = ifm.fecha AND codmp = ifm.codmp AND descripcion = \'DIFERENCIA INVENTARIO\'), 0) AS sistema,
			ifm.inventario AS fisica,
			-- round(diferencia::numeric, 4) AS dif
			(hi.existencia + COALESCE((SELECT CASE WHEN tipo_mov = FALSE THEN -cantidad ELSE cantidad END FROM mov_inv_real WHERE num_cia = ifm.num_cia AND fecha = ifm.fecha AND codmp = ifm.codmp AND descripcion = \'DIFERENCIA INVENTARIO\'), 0)) - ifm.inventario AS dif
		FROM
			inventario_fin_mes ifm
			LEFT JOIN historico_inventario hi USING (num_cia, fecha, codmp)
			LEFT JOIN catalogo_companias cc USING (num_cia)
			LEFT JOIN catalogo_mat_primas cmp USING (codmp)
		WHERE
			cc.tipo_cia IN (1, 2)
			AND fecha = \'' . $fecha . '\'
			-- AND round(diferencia::numeric, 4) != 0
			AND (hi.existencia + COALESCE((SELECT CASE WHEN tipo_mov = FALSE THEN -cantidad ELSE cantidad END FROM mov_inv_real WHERE num_cia = ifm.num_cia AND fecha = ifm.fecha AND codmp = ifm.codmp AND descripcion = \'DIFERENCIA INVENTARIO\'), 0)) - ifm.inventario != 0
			AND hi.precio_unidad <> 0
			-- AND ifm.precio_unidad <> 0';
	if ($_GET['num_cia'] > 0)
		$sql .= '
			AND num_cia = ' . $_GET['num_cia'] . '
		';
	if ($_GET['idadmin'] > 0)
		$sql .= '
			AND idadministrador = ' . $_GET['idadmin'] . '
		';
	if ($_GET['tipo'] != '')
		$sql .= '
			AND controlada = \'' . $_GET['tipo'] . '\'
		';
	if (!isset($_GET['gas']))
		$sql .= '
			AND codmp NOT IN (90)
		';
	$sql .= '
		ORDER BY num_cia, codmp
	';
	$result = $db->query($sql);

	if (!$result) die('No hay resultados');

	$tpl = new TemplatePower('plantillas/bal/listado_diferencias_inventario.tpl');
	$tpl->prepare();

	$num_cia = NULL;
	foreach ($result as $r) {
		if ($num_cia != $r['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->newBlock('totales');
				$tpl->assign('faltantes', $faltantes > 0 ? number_format($faltantes, 2, '.', ',') : '&nbsp;');
				$tpl->assign('sobrantes', $sobrantes > 0 ? number_format($sobrantes, 2, '.', ',') : '&nbsp;');
				$tpl->assign('total', '<span style="color:#' . ($total > 0 ? 'C00' : '00C') . '">' . number_format(abs($total), 2, '.', ',') . '</span>');

				if (isset($_GET['gas']) && $tanques = $db->query("
					SELECT
						num_tanque
							AS tanque,
						capacidad,
						capacidad * 0.90
							AS capacidad_90
					FROM
						catalogo_tanques
					WHERE
						num_cia = {$num_cia}
					ORDER BY
						num_tanque
				"))
				{
					foreach ($tanques as $tanque) {
						$tpl->newBlock('tanque');
						$tpl->assign('tanque', $tanque['tanque']);
						$tpl->assign('capacidad', number_format($tanque['capacidad']));
						$tpl->assign('capacidad_90', number_format($tanque['capacidad_90']));
					}
				}

				if ($obs = $db->query("
					SELECT
						observaciones
					FROM
						observaciones_diferencias_inventario
					WHERE
						num_cia = {$num_cia}
						AND anio = {$_REQUEST['anio']}
						AND mes = {$_REQUEST['mes']}
				"))
				{
					if (trim($obs[0]['observaciones']) != '')
					{
						$tpl->newBlock('observaciones');
						$tpl->assign('observaciones', utf8_decode($obs[0]['observaciones']));
					}
				}

				if (isset($_GET['doble_cara']))
					$tpl->assign('listado.salto', $hojas % 2 == 0 ? '<br style="page-break-after:always;" />' : '<br style="page-break-after:always;" /><br style="page-break-after:always;" />');
				else
					$tpl->assign('listado.salto', '<br style="page-break-after:always;" />');
			}

			$num_cia = $r['num_cia'];

			$tpl->newBlock('listado');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $r['nombre_cia']);
			$tpl->assign('mes', mes_escrito($_GET['mes']));
			$tpl->assign('anio', $_GET['anio']);

			$faltantes = 0;
			$sobrantes = 0;
			$total = 0;

			$filas = 1;
			$hojas = 1;
		}
		if ($filas > 60) {
			$tpl->assign('listado.salto', '<br style="page-break-after:always;" />');

			$tpl->newBlock('listado');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $r['nombre_cia']);
			$tpl->assign('mes', mes_escrito($_GET['mes']));
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('continuacion', ' <span style="font-size:6pt;">(continuación)</span>');

			$filas = 1;
			$hojas++;
		}
		$tpl->newBlock('fila');
		$tpl->assign('codmp', $r['codmp']);
		$tpl->assign('producto', $r['producto']);
		$tpl->assign('precio_unidad', number_format($r['precio_unidad'], 4, '.', ','));
		$tpl->assign('sistema', number_format($r['sistema'], 2, '.', ','));
		$tpl->assign('fisica', number_format($r['fisica'], 2, '.', ','));
		$tpl->assign('ufaltantes', $r['dif'] > 0 ? number_format(abs($r['dif']), 2, '.', ',') : '&nbsp;');
		$tpl->assign('vfaltantes', $r['dif'] > 0 ? number_format(abs($r['dif']) * $r['precio_unidad'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('usobrantes', $r['dif'] < 0 ? number_format(abs($r['dif']), 2, '.', ',') : '&nbsp;');
		$tpl->assign('vsobrantes', $r['dif'] < 0 ? number_format(abs($r['dif']) * $r['precio_unidad'], 2, '.', ',') : '&nbsp;');

		$faltantes += $r['dif'] > 0 ? $r['dif'] * $r['precio_unidad'] : 0;
		$sobrantes += $r['dif'] < 0 ? abs($r['dif']) * $r['precio_unidad'] : 0;
		$total += $r['dif'] * $r['precio_unidad'];

		$filas++;
	}
	if ($num_cia != NULL) {
		$tpl->newBlock('totales');
		$tpl->assign('faltantes', $faltantes > 0 ? number_format($faltantes, 2, '.', ',') : '&nbsp;');
		$tpl->assign('sobrantes', $sobrantes > 0 ? number_format($sobrantes, 2, '.', ',') : '&nbsp;');
		$tpl->assign('total', '<span style="color:#' . ($total > 0 ? 'C00' : '00C') . '">' . number_format(abs($total), 2, '.', ',') . '</span>');

		if (isset($_GET['gas']) && $tanques = $db->query("
			SELECT
				num_tanque
					AS tanque,
				capacidad,
				capacidad * 0.90
					AS capacidad_90
			FROM
				catalogo_tanques
			WHERE
				num_cia = {$num_cia}
			ORDER BY
				num_tanque
		"))
		{
			foreach ($tanques as $tanque) {
				$tpl->newBlock('tanque');
				$tpl->assign('tanque', $tanque['tanque']);
				$tpl->assign('capacidad', number_format($tanque['capacidad']));
				$tpl->assign('capacidad_90', number_format($tanque['capacidad_90']));
			}
		}

		if ($obs = $db->query("
			SELECT
				observaciones
			FROM
				observaciones_diferencias_inventario
			WHERE
				num_cia = {$num_cia}
				AND anio = {$_REQUEST['anio']}
				AND mes = {$_REQUEST['mes']}
		"))
		{
			if (trim($obs[0]['observaciones']) != '')
			{
				$tpl->newBlock('observaciones');
				$tpl->assign('observaciones', utf8_decode($obs[0]['observaciones']));
			}
		}
	}

	die($tpl->printToScreen());
}

$tpl = new TemplatePower('plantillas/bal/ListadoDiferenciasInventario.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

$tpl->printToScreen();
?>
