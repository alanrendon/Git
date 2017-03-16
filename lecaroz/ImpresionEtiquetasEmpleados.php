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
			
			$condiciones[] = 'fecha_baja IS NULL';
			
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
					cc.nombre_corto
						AS nombre_cia,
					id,
					LPAD(num_emp::varchar, 5, \'0\')
						AS
							num_emp,
					TRIM(regexp_replace(COALESCE(ct.ap_paterno, \'\') || \' \' || COALESCE(ct.ap_materno, \'\') || \' \' || COALESCE(ct.nombre, \'\'), \'\s+\', \' \', \'g\'))
						AS
							empleado
				FROM
						catalogo_trabajadores ct
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					empleado
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/ImpresionEtiquetasEmpleadosResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$color = FALSE;
					}
					
					$tpl->newBlock('empleado');
					$tpl->assign('color', $color ? 'on' : 'off');
					$color = !$color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('empleado', utf8_encode($rec['empleado']));
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'imprimir':
			$sql = '
				SELECT
					TRIM(regexp_replace(COALESCE(ct.ap_paterno, \'\') || \' \' || COALESCE(ct.ap_materno, \'\') || \' \' || COALESCE(ct.nombre, \'\'), \'\s+\', \' \', \'g\'))
						AS
							empleado
				FROM
						catalogo_trabajadores ct
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					num_cia,
					empleado
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$labels_per_column = 2;
				$labels_per_row = 10;
				$labels_per_sheet = $labels_per_column * $labels_per_row;
				
				$column_size = 107;
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
					
					if (strlen($rec['empleado']) <= 26) {
						$pcl .= $rec['empleado'];
					}
					else {
						$pieces = explode(' ', $rec['empleado']);
						
						$string = '';
						foreach ($pieces as $piece) {
							if (strlen(trim($string . ' ' . $piece)) <= 26) {
								$string .= ' ' . $piece;
							}
							else {
								$pcl .= trim($string);
								
								$pcl .= MoveCursorV($y + 10);
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

$tpl = new TemplatePower('plantillas/fac/ImpresionEtiquetasEmpleados.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
