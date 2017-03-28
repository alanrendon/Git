<?php
	require('../../../main.inc.php');
	require_once("dom/dompdf_config.inc.php");
	require_once("dom/numero_a_letra.php");

	require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
	require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";
	require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
	require_once DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php";
	require_once DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php";

	require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";

	dol_include_once('/factory/class/factory.class.php');
	dol_include_once('/factory/core/lib/factory.lib.php');

	$id = $_GET['id'];
	$url_logo = $conf->mycompany->dir_output.'/logos/'.$conf->global->MAIN_INFO_SOCIETE_LOGO;

	$sql = "SELECT f.rowid as idorden, fd.fk_product as idprod, p.ref as ref_prod, f.ref as ref_produccion, label, stock, fd.qty_planned as cant FROM ".MAIN_DB_PREFIX."factory f ";
	$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."factorydet fd ON fd.fk_factory = f.rowid ";
	$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = fd.fk_product WHERE stock <= 0";
	$res = $db->query($sql);
	$num = $db->num_rows($res);
	$contador = 1;

	if( $num > 0 ) {
		
		$table .= '<div id="div_productos_pendientes">';
		$table .= '<table width="100%" cellpadding="0" cellspacing="0">';
		$table .= '<tr>';
		$table .= '<th align="left">Producto</th>';
		$table .= '<th align="left">Etiqueta</th>';
		$table .= '<th>Orden de producción</th>';
		$table .= '<th>Stock</th>';
		$table .= '<th>Cantidad requerida</th>';
		$table .= '</tr>';
		while( $obj = $db->fetch_object($res) ) {
			$table .= '<tr class="';
			$table .= ( $contador%2 == 0 ) ? 'pair': 'impair';
			$table .= '">';
			$table .= '<td align="left">'.$obj->ref_prod.'</td>';
			$table .= '</td>';
			$table .= '<td>'.$obj->label.'</td>';
			$table .= '<td align="center">'.$obj->ref_produccion.'</td>';
			$table .= '<td align="center" style="color:#FF0000">'.$obj->stock.'</td>';
			$table .= '<td align="center">'.$obj->cant.'</td>';
			$table .= '</tr>';
			$contador++;
		}
		$table .= '</table>';
		$table .= '</div>';
	}
	else {
		$table .= '<div><strong>No se encontraron productos pendientes.</strong></div>';
	}

  	$html ='<html>
			<head><link type="text/css" href="style.css" rel="stylesheet" /></head>
			<body>
				<div id="cabecera">
					<table border="0" width="100%">
						<tr>
							<td width="33%" valign="top"><img src="'.$url_logo .'" width="170"></td>
							<td width="40%" valign="top" align="center">
								<h2>Reporte de Productos Pendientes</h2>
							</td>
							<td align="right" valign="top">
							</td>
						</tr>
					</table>
				</div>			
				<div id="cuerpo">
					'.$table.'
				</div>
				<div id="pie">
				</div>';

	header('Content-type: application/pdf');

	$trigger = "
	    <script type='text/javascript'>
	        //this.$table .=();
	        //this.closeDoc(true);
	    </script>
	";

	$dompdf = new DOMPDF();
	$dompdf->set_paper("A4", "portrait");
	$dompdf->load_html($html."{$trigger}</body></html>");
	$dompdf->render();

	$canvas = $dompdf->get_canvas();
	$font = Font_Metrics::get_font("helvetica", "bold");
	$canvas->page_text(535, 825, "Pág. {PAGE_NUM} de {PAGE_COUNT}", $font, 7, array(0,0,0));

	echo $dompdf->output();
?>
