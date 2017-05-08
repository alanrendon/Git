<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					tipo_cia IN (1, 2)
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
			
			break;

		case 'obtener_gasto':
			$sql = '
				SELECT
					descripcion
				FROM
					catalogo_gastos
				WHERE
					codgastos = ' . $_REQUEST['codgastos'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['descripcion']);
			}
			
			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/GastosPorcentajesDistribucionInicio.tpl');
			$tpl->prepare();

			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

			$sql = "
				SELECT
					codgastos,
					descripcion
				FROM
					gastos_porcentajes_distribucion
					LEFT JOIN catalogo_gastos
						USING (codgastos)
				GROUP BY
					codgastos,
					descripcion
				ORDER BY
					descripcion
			";

			$result = $db->query($sql);

			if ($result)
			{
				$tpl->newBlock('gastos');

				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('codgastos', $row['codgastos']);
					$tpl->assign('descripcion', utf8_encode($row['descripcion']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$tpl = new TemplatePower('plantillas/bal/GastosPorcentajesDistribucionConsulta.tpl');
			$tpl->prepare();

			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

			$gasto = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$_REQUEST['codgastos']}");

			$tpl->assign('codgastos', $_REQUEST['codgastos']);
			$tpl->assign('descripcion', utf8_encode($gasto[0]['descripcion']));

			$sql = "
				SELECT
					num_cia,
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					tipo_cia IN (1, 2)
				ORDER BY
					tipo_cia,
					num_cia
			";

			$result = $db->query($sql);

			$porcentajes = array();

			if ($result)
			{
				foreach ($result as $row)
				{
					$porcentajes[$row['num_cia']] = array(
						'nombre_cia'	=> utf8_encode($row['nombre_corto']),
						'porcentajes'	=> array()
					);
				}
			}

			$sql = "
				SELECT
					gpd.id,
					gpd.num_cia,
					gpd.ros,
					cc.nombre_corto
						AS nombre_ros,
					porc
				FROM
					gastos_porcentajes_distribucion gpd
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = gpd.ros)
				WHERE
					codgastos = {$_REQUEST['codgastos']}
				ORDER BY
					gpd.num_cia,
					gpd.ros
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row)
				{
					if (isset($porcentajes[$row['num_cia']]))
					{
						$porcentajes[$row['num_cia']]['porcentajes'][] = array(
							'id'			=> $row['id'],
							'num_ros'		=> $row['ros'],
							'nombre_ros'	=> utf8_encode($row['nombre_ros']),
							'porc'			=> $row['porc']
						);
					}
				}
			}

			if ($porcentajes)
			{
				foreach ($porcentajes as $num_cia => $cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $cia['nombre_cia']);

					if ($cia['porcentajes'])
					{
						foreach ($cia['porcentajes'] as $col => $porc)
						{
							$tpl->assign('id_' . $col, $porc['id']);
							$tpl->assign('num_ros_' . $col, $porc['num_ros']);
							$tpl->assign('nombre_ros_' . $col, $porc['nombre_ros']);
							$tpl->assign('porc_' . $col, $porc['porc'] > 0 ? number_format($porc['porc'], 2) : '');
						}
					}
				}
			}

			echo $tpl->getOutputContent();

			break;
		
		case 'actualizar_porcentaje':
			if ($_REQUEST['id'] > 0)
			{
				if ($_REQUEST['ros'] > 0 && $_REQUEST['porc'] > 0)
				{
					$db->query("UPDATE gastos_porcentajes_distribucion SET ros = {$_REQUEST['ros']}, porc = {$_REQUEST['porc']}, iduser = {$_SESSION['iduser']}, tsmod = NOW() WHERE id = {$_REQUEST['id']}");

					echo json_encode(array('status' => 2, 'id' => $_REQUEST['id']));
				}
				else
				{
					$db->query("DELETE FROM gastos_porcentajes_distribucion WHERE id = {$_REQUEST['id']}");

					echo json_encode(array('status' => 3, 'id' => $_REQUEST['id']));
				}
			}
			else if ($_REQUEST['num_cia'] > 0 && $_REQUEST['porc'] > 0)
			{
				$db->query("INSERT INTO gastos_porcentajes_distribucion (num_cia, ros, codgastos, porc, iduser) VALUES ({$_REQUEST['num_cia']}, {$_REQUEST['ros']}, {$_REQUEST['codgastos']}, {$_REQUEST['porc']}, {$_SESSION['iduser']})");

				$result = $db->query("SELECT MAX('id') AS id FROM gastos_porcentajes_distribucion");

				echo json_encode(array('status' => 1, 'id' => $result[0]['id']));
			}
			
			break;

	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/GastosPorcentajesDistribucion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
