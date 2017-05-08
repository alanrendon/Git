<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_con_bul_har.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$pieces = explode('/', $_GET['fecha_corte']);
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $pieces[1], 1, $pieces[2]));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]));
	
	$condiciones[] = "fecha BETWEEN '$fecha1' AND '$fecha2'";
	
	$condiciones[] = "codmp IN (4, 21, 67, 148, 149, 38, 86, 49, 44, 45, 47)";
	
	$condiciones[] = "tipo_mov = TRUE";
	
	if (isset($_GET['cod_turno'])) {
		$condiciones[] = 'cod_turno IN (' . implode(', ', $_GET['cod_turno']) . ')';
	}
	
	if ($_GET['num_cia'] > 0) {
		$condiciones[] = "num_cia = $_GET[num_cia]";
	}
	
	if ($_GET['idadmin'] > 0) {
		$condiciones[] = "idadministrador = $_GET[idadmin]";
	}
	
	if ($_GET['codmp'] > 0) {
		if (in_array($_GET['codmp'], array(38, 86, 49, 44, 45, 47))) {
			$condiciones[] = "codmp IN (38, 86, 49, 44, 45, 47)";
		}
		else {
			$condiciones[] = "codmp = $_GET[codmp]";
		}
	}
	
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS nombre_cia,
			CASE
				WHEN codmp IN (38, 86, 49, 44, 45, 47) THEN
					38
				ELSE
					codmp
			END
				AS cod,
			CASE
				WHEN codmp IN (38, 86, 49, 44, 45, 47) THEN
					\'GRASAS\'
				ELSE
					cmp.nombre
			END
				AS nombre_mp,
			cod_turno,
			SUM(consumo)
				AS consumo,
			SUM(costo)
				AS costo
		FROM
			(
				SELECT
					num_cia,
					codmp,
					cod_turno,
					SUM(cantidad) / (
						SELECT
							SUM(cantidad) / 44
						FROM
							mov_inv_real
						WHERE
							num_cia = mv.num_cia
							AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							AND codmp = 1
							AND cod_turno = mv.cod_turno
							AND tipo_mov = TRUE
					)
						AS consumo,
					SUM(cantidad) / (
						SELECT
							SUM(cantidad) / 44
						FROM
							mov_inv_real
						WHERE
							num_cia = mv.num_cia
							AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							AND codmp = 1
							AND cod_turno = mv.cod_turno
							AND tipo_mov = TRUE
					) * (
						SELECT
							precio_unidad
						FROM
							inventario_real
						WHERE
							num_cia = mv.num_cia
							AND codmp = mv.codmp
					)
						AS costo
				FROM
					mov_inv_real mv
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					codmp,
					cod_turno
			) result
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
			LEFT JOIN catalogo_mat_primas cmp
				USING (codmp)
		GROUP BY
			num_cia,
			nombre_cia,
			cod,
			nombre_mp,
			cod_turno
		ORDER BY
			cod,
			num_cia,
			cod_turno
	';
	
	$tmp = $db->query($sql);
	
	if (!$tmp) die('location: ./pan_con_bul_har.php?codigo_error=1');
	
	$result = array();
	
	$filas_por_hoja = $_GET['idadmin'] > 0 ? 48 : 50;
	
	$filas = 0;
	$cont = 0;
	
	$codmp = NULL;
	
	foreach ($tmp as $t) {
		if ($codmp != $t['cod']) {
			$codmp = $t['cod'];
			
			$result[$cont] = array(
				'codmp'     => $t['cod'],
				'nombre_mp' => $t['nombre_mp'],
				'cias'      => array()
			);
			
			$num_cia = NULL;
		}
		
		if ($num_cia != $t['num_cia']) {
			$filas++;
			
			if ($filas >= $filas_por_hoja) {
				$filas = 0;
				
				$cont++;
				
				$result[$cont] = array(
					'codmp'     => $t['cod'],
					'nombre_mp' => $t['nombre_mp'],
					'cias'      => array()
				);
			}
			
			$num_cia = $t['num_cia'];
			
			$result[$cont]['cias'][$num_cia] = array(
				'nombre_cia' => $t['nombre_cia'],
				'turnos'     => array_combine($_GET['cod_turno'], array_fill(0, count($_GET['cod_turno']), array(
					'consumo' => 0,
					'costo'   => 0
				)))
			);
		}
		
		$result[$cont]['cias'][$num_cia]['turnos'][$t['cod_turno']] = array(
			'consumo' => $t['consumo'],
			'costo'   => $t['costo']
		);
	}
	
	foreach ($result as $data) {
		$tpl->newBlock('listado');
		$tpl->assign('fecha', $_GET['fecha_corte']);
		$tpl->assign('hora', date('d/m/Y H:i'));
		
		if ($_GET['idadmin'] > 0) {
			$admin = $db->query("SELECT nombre_administrador FROM catalogo_administradores WHERE idadministrador = $_GET[idadmin]");
			$tpl->assign('admin', $admin[0]['nombre_administrador']);
		}
		
		$tpl->newBlock('pro');
		$tpl->assign('codmp', $data['codmp']);
		$tpl->assign('nombre', $data['nombre_mp']);
		
		$tpl->assign('span', count($_GET['cod_turno']) * 2 + 1);
		
		foreach ($_GET['cod_turno'] as $turno) {
			$tpl->newBlock('th');
			
			switch ($turno) {
				case 1:
					$tpl->assign('turno', 'FD');
				break;
				
				case 2:
					$tpl->assign('turno', 'FN');
				break;
				
				case 3:
					$tpl->assign('turno', 'BZ');
				break;
				
				case 4:
					$tpl->assign('turno', 'RP');
				break;
				
				case 8:
					$tpl->assign('turno', 'PC');
				break;
			}
		}
		
		foreach ($data['cias'] as $cia => $data_cia) {
			$tpl->newBlock('fila');
			
			$tpl->assign('num_cia', $cia);
			$tpl->assign('nombre', $data_cia['nombre_cia']);
			
			foreach ($data_cia['turnos'] as $turno => $data_turno) {
				$tpl->newBlock('td');
				
				switch ($turno) {
					case 1:
						$tpl->assign('color', '00C');
					break;
					
					case 2:
						$tpl->assign('color', '00C');
					break;
					
					case 3:
						$tpl->assign('color', '060');
					break;
					
					case 4:
						$tpl->assign('color', 'C00');
					break;
					
					case 8:
						$tpl->assign('color', 'F60');
					break;
					
					default:
						$tpl->assign('color', '000');
				}
				
				$tpl->assign('consumo', $data_turno['consumo'] > 0 ? number_format($data_turno['consumo'], '3', '.', ',') : '&nbsp;');
				$tpl->assign('costo', $data_turno['costo'] > 0 ? '<span style="float:left;">$ </span>' . number_format($data_turno['costo'], '2', '.', ',') : '&nbsp;');
			}
		}
		
		$tpl->assign('listado.salto', '<br style="page-break-after:always;" />');
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$tpl->assign('fecha_corte', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $reg) {
	$tpl->newBlock('a');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['admin']);
}

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query("SELECT codmp AS cod, nombre FROM catalogo_mat_primas WHERE controlada = 'TRUE' ORDER BY cod");
foreach ($result as $reg) {
	$tpl->newBlock('m');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die();
?>