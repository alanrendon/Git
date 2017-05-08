<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

/*if ($_SESSION['iduser'] != 1) {
	die('<div style="font-size:16pt; border:solid 2px #000; padding:30px 10px;">ESTOY HACIENDO MODIFICACIONES AL PROGRAMA, NO ME LLAMEN PARA PREGUNTAR CUANDO QUEDARA, YO LES AVISO.</div>');
}*/

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'consulta':
			$condiciones = array();
			
			$condiciones[] = 'tsbaja IS NULL';
			
			$sql = '
				SELECT
					idtipobaja
						AS id,
					num || \' \' || nombre_tipo_baja
						AS tipo_baja,
					CASE
						WHEN permite_reingreso = TRUE THEN
							\'accept\'
						ELSE 
							\'cancel\'
					END
						AS permite_reingreso
				FROM
					catalogo_tipos_baja
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/TiposBajaConsultaResultado.tpl');
			$tpl->prepare();
			
			if (in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58))) {
				$tpl->assign('alta_disabled', ' disabled');
			}
			
			if ($result) {
				$row_color = FALSE;
				
				foreach ($result as $i => $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('tipo_baja', utf8_encode($rec['tipo_baja']));
					$tpl->assign('permite_reingreso', $rec['permite_reingreso']);
					$tpl->assign('disabled', in_array($_SESSION['iduser'], array(40, 47, 46, 39, 47, 52, 53, 54, 58)) ? '_gray' : '');
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/nom/TiposBajaConsultaAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'insertar':
			$sql = '
				SELECT
					num
				FROM
					catalogo_tipos_baja
				WHERE
					tsbaja IS NULL
				ORDER BY
					num
			';
			
			$numeros = $db->query($sql);
			
			$num = 1;
			
			if ($numeros) {
				foreach ($numeros as $n) {
					if ($num == $n['num']) {
						$num++;
					}
					else {
						break;
					}
				}
			}
			
			$sql = '
				INSERT INTO
					catalogo_tipos_baja
						(
							nombre_tipo_baja,
							permite_reingreso,
							num,
							idalta
						)
					VALUES
						(
							\'' . $_REQUEST['nombre_tipo_baja'] . '\',
							' . $_REQUEST['permite_reingreso'] . ',
							' . $num . ',
							' . $_SESSION['iduser'] . '
						)
			' . ";\n";
			
			$db->query($sql);
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					idtipobaja
						AS id,
					nombre_tipo_baja,
					permite_reingreso
				FROM
					catalogo_tipos_baja
				WHERE
					idtipobaja = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$rec = $result[0];
				
				$tpl = new TemplatePower('plantillas/nom/TiposBajaConsultaModificacion.tpl');
				$tpl->prepare();
				
				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('nombre_tipo_baja', utf8_encode($rec['nombre_tipo_baja']));
				$tpl->assign('permite_reingreso_' . $rec['permite_reingreso'], ' checked');
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'actualizar':
			$sql = '
				UPDATE
					catalogo_tipos_baja
				SET
					nombre_tipo_baja = \'' . utf8_decode($_REQUEST['nombre_tipo_baja']) . '\',
					permite_reingreso = ' . $_REQUEST['permite_reingreso'] . ',
					idmod = ' . $_SESSION['iduser'] . ',
					tsmod = NOW()
				WHERE
					idtipobaja = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
		break;
		
		case 'baja':
			$sql = '
				UPDATE
					catalogo_tipos_baja
				SET
					idbaja = ' . $_SESSION['iduser'] . ',
					tsbaja = NOW()
				WHERE
					idtipobaja = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TiposBajaConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
