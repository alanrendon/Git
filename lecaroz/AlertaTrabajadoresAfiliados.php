<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'verificar':
			$sql = '
				SELECT
					id
				FROM
					catalogo_trabajadores ct
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					' . ($_SESSION['iduser'] > 1 ? ('AND ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . '
				LIMIT
					1
			';
			$result = $db->query($sql);
			
			if ($result && in_array($_SESSION['iduser'], array(4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				echo 1;
			}
		break;
		
		case 'listado':
			$sql = '
				SELECT
					ct.num_cia_emp
						AS num_cia,
					cc.nombre_corto
						AS nombre_cia,
					SUM(
						CASE
							WHEN COALESCE(TRIM(num_afiliacion), \'\') <> \'\' THEN
								1
							ELSE
								0
						END
					)
						AS afiliados,
					SUM(
						CASE
							WHEN COALESCE(TRIM(num_afiliacion), \'\') = \'\' THEN
								1
							ELSE
								0
						END
					)
						AS no_afiliados
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = ct.num_cia_emp)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					' . ($_SESSION['iduser'] > 1 ? ('AND ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . '
				GROUP BY
					ct.num_cia_emp,
					nombre_cia
				ORDER BY
					ct.num_cia_emp
			';
			$result = $db->query($sql);
			
			if ($result && in_array($_SESSION['iduser'], array(1, 4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				$tpl = new TemplatePower('plantillas/AlertaTrabajadoresAfiliados.tpl');
				$tpl->prepare();
				
				$afiliados = 0;
				$no_afiliados = 0;
				
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('afiliados', $rec['afiliados'] > 0 ? number_format($rec['afiliados']) : '&nbsp;');
					$tpl->assign('no_afiliados', $rec['no_afiliados'] > 0 ? number_format($rec['no_afiliados']) : '&nbsp;');
					
					$afiliados += $rec['afiliados'];
					$no_afiliados += $rec['no_afiliados'];
				}
				
				$tpl->assign('_ROOT.afiliados', number_format($afiliados));
				$tpl->assign('_ROOT.no_afiliados', number_format($no_afiliados));
				
				$tpl->printToScreen();
			}
		break;
	}
}


?>
