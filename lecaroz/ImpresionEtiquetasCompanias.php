<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/pcl.inc.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'buscar':
			$condiciones = array();
			
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					' . $_REQUEST['campo'] . '
						AS
							nombre_cia
				FROM
					catalogo_companias cc
			';
			
			if (count($condiciones) > 0) {
				$sql .= '
					WHERE
						' . implode(' AND ', $condiciones) . '
				';
			}
			
			$sql .= '
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/ImpresionEtiquetasCompaniasResultado.tpl');
				$tpl->prepare();
				
				$color = FALSE;
				
				foreach ($result as $rec) {
					$tpl->newBlock('cia');
					$tpl->assign('color', $color ? 'on' : 'off');
					$color = !$color;
					
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'imprimir':
			$sql = '
				SELECT
					TRIM(regexp_replace(num_cia || \' \' || ' . $_REQUEST['campo'] . ', \'\s+\', \' \', \'g\'))
						AS
							nombre_cia
				FROM
					catalogo_companias cc
				WHERE
					num_cia IN (' . implode(', ', $_REQUEST['num_cia']) . ')
				ORDER BY
					num_cia
			';
			
			$tmp = $db->query($sql);
			
			if ($tmp) {
				$copias = isset($_REQUEST['copias']) && $_REQUEST['copias'] > 0 ? $_REQUEST['copias'] : 1;
				
				$result = array();
				if (isset($_REQUEST['intercalar'])) {
					for ($i = 0; $i < $copias; $i++) {
						$result = array_merge($result, $tmp);
					}
				}
				else {
					foreach ($tmp as $t) {
						$result = array_merge($result, array_fill(0, $copias, $t));
					}
				}
				
				$labels_per_column = 3;
				$labels_per_row = 10;
				$labels_per_sheet = $labels_per_column * $labels_per_row;
				
				$column_size = 72.2;
				$row_size = 25.4;
				
				$top_offset = 12;
				$left_offset = 1;
				
				shell_exec("chmod ugo=rwx pcl");
				$fp = fopen("pcl/labels.pcl", "w");
				
				$pcl = '';
				
				$pcl .= HEADER;
				$pcl .= SetPageSize(2);
				$pcl .= SetTopMargin(1);
				$pcl .= SetLeftMargin(0);
				$pcl .= DEFAULT_FONT;
				$pcl .= SetFontPointSize(16);
				$pcl .= SetFontStrokeWeight(BOLD);
				
				$column = $_REQUEST['etiqueta'] > 0 ? ($_REQUEST['etiqueta'] - 1) % $labels_per_column : 0;
				$row = $_REQUEST['etiqueta'] > 0 ? floor(($_REQUEST['etiqueta'] - 1) / $labels_per_column) : 0;
				
				$x = $left_offset + $column * $column_size;
				$y = $top_offset + $row_size * $row;
				
				$labels = $_REQUEST['etiqueta'] > 0 ? $_REQUEST['etiqueta'] - 1 : 0;
				
				foreach ($result as $i => $rec) {
					$pcl .= MoveCursorV($y + 5);
					$pcl .= MoveCursorH($x);
					
					if (strlen($rec['nombre_cia']) <= 16) {
						$pcl .= $rec['nombre_cia'];
					}
					else {
						$pieces = explode(' ', $rec['nombre_cia']);
						
						$string = '';
						foreach ($pieces as $piece) {
							if (strlen(trim($string . ' ' . $piece)) <= 16) {
								$string .= ' ' . $piece;
							}
							else {
								$pcl .= trim($string);
								
								/*$y += 10*/;
								
								$pcl .= MoveCursorV(/*$y*/5, TRUE);
								$pcl .= MoveCursorH($x);
								
								$string = $piece;
							}
						}
						
						$pcl .= $string;
					}
					
					$labels++;
					
					$column = $labels % $labels_per_column;
					$row += $column == 0 ? 1 : 0;
					
					$x = $left_offset + $column * $column_size;
					$y = $top_offset + $row_size * $row;
					
					if ($labels == $labels_per_sheet) {
						$pcl .= FORM_FEED;
						
						$column = 0;
						$row = 0;
						
						$x = $left_offset + $column * $column_size;
						$y = $top_offset + $row_size * $row;
						
						$labels = 0;
					}
				}
				
				$pcl .= RESET;
				
				fwrite($fp, $pcl);
				fclose($fp);
				
				shell_exec('lp -d general pcl/labels.pcl');
				shell_exec('chmod ugo=r pcl');
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ImpresionEtiquetasCompanias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
