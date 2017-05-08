<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/jpgraph/jpgraph.php';
include './includes/jpgraph/jpgraph_line.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	0  => 'ENE',
	1  => 'FEB',
	2  => 'MAR',
	3  => 'ABR',
	4  => 'MAY',
	5  => 'JUN',
	6  => 'JUL',
	7  => 'AGO',
	8  => 'SEP',
	9 => 'OCT',
	10 => 'NOV',
	11 => 'DIC'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$_colors = array(
	'firebrick3',
	'aquamarine4',
	'dodgerblue3',
	'goldenrod3',
	'orange',
	'brown',
	'purple',
	'coral2',
	'magenta3'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			/*
			@ Intervalo de años
			*/
			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();
				
				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				if (count($anios) > 0) {
					$condiciones[] = 'b.anio IN (' . implode(', ', $anios) . ')';
				}
			}
			
			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones[] = 'b.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					b.num_cia,
					cc.nombre_corto
						AS
							nombre_cia
				FROM
						balances_pan b
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/bal/PruebaGraficasBalanceReporte.tpl');
				$tpl->prepare();
				
				switch ($_REQUEST['campo']) {
					case 'ventas_netas':
						$titulo_campo = 'Ventas Netas';
					break;
				}
				
				foreach ($result as $rec) {
					$tpl->newBlock('reporte');
					
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', $rec['nombre_cia']);
					//$tpl->assign('anio', $_REQUEST['anios']);
					
					$argumentos = array(
						'num_cia' => $rec['num_cia'],
						'nombre_cia' => $rec['nombre_cia'],
						'anios' => $_REQUEST['anios'],
						'campo' => $_REQUEST['campo'],
						'titulo_campo' => $titulo_campo
					);
					
					$tpl->assign('url', 'PruebaGraficasBalance.php?accion=grafico&arg=' . urlencode(json_encode($argumentos)));
				}
				
				$tpl->printToScreen();
			}
		break;
		
		case 'grafico':
			$argumentos = json_decode(stripslashes($_REQUEST['arg']));
			
			$condiciones[] = 'num_cia = ' . $argumentos->num_cia;
			
			/*
			@ Intervalo de años
			*/
			if (isset($argumentos->anios) && trim($argumentos->anios) != '') {
				$anios = array();
				
				$pieces = explode(',', $argumentos->anios);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				if (count($anios) > 0) {
					$condiciones[] = 'anio IN (' . implode(', ', $anios) . ')';
				}
			}
			
			$sql = '
				SELECT
					anio,
					mes,
					' . $argumentos->campo . '
						AS
							campo
				FROM
					balances_pan
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					anio,
					mes
			';
			$result = $db->query($sql);
			
			$consecutivo = 0;
			foreach ($result as $r) {
				if ($r['mes'] - 1 == $consecutivo) {
					$datos[$r['anio']][$r['mes'] - 1] = round(floatval($r['campo']));
					
					$consecutivo++;
				}
				else {
					for ($i = $consecutivo; $i < $r['mes'] - 1; $i++) {
						$datos[$r['anio']][$i] = NULL;
						
						$consecutivo++;
					}
					
					$datos[$r['anio']][$r['mes'] - 1] = round(floatval($r['campo']));
					
					$consecutivo++;
				}
			}
			
			$graph = new Graph(800, 300, 'auto');
			$graph->SetScale('textlin');
			$graph->img->SetAntiAliasing();
			$graph->xgrid->Show();
			
			$lineplot = array();
			$color = 0;
			foreach ($datos as $a => $d) {
				$lineplot[$a] = new LinePlot($d);
				$lineplot[$a]->SetColor($_colors[$color]);
				$lineplot[$a]->SetWeight(2);
				$lineplot[$a]->SetLegend($a);
				$lineplot[$a]->mark->SetType(MARK_X);
				//$lineplot[$a]->value->Show();
				
				$graph->Add($lineplot[$a]);
				
				$color = $color < count($_colors) ? $color + 1 : 0;
			}
			
			$graph->img->SetMargin(90, 90, 20, 40);
			$graph->title->Set($argumentos->titulo_campo/* . ' ' . $argumentos->anios*/);
			
			$graph->xaxis->title->Set('Meses');
			$graph->xaxis->SetTickLabels($_meses);
			
			$graph->yaxis->SetTitleMargin(60); 
			$graph->yaxis->title->Set($argumentos->titulo_campo);
			$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#F9BB64@0.5');
			
			$graph ->legend->Pos(0.02, 0.5, 'right', 'center');
			
			$graph->Stroke();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/PruebaGraficasBalance.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
