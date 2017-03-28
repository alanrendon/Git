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
	$factory = new Factory($db);
	$product = new Product($db);
	$factory->fetch($id, $ref);
	$product->fetch($factory->fk_product);

	$ref = $product->ref;
	$qty_planned = $factory->qty_planned;
	$qty_made = $factory->qty_made;
	$date_start_made = $factory->date_start_made;
	$date_end_made = $factory->date_end_made;
	$no_serie = $factory->no_serie;

	$prods_arbo = $factory->getChildsOF($id); 
	$langs->load("factory@factory");

	$table .= '<table  width="100%" cellspacing="0">';
	$table .= '<tr>';
	$table .= '<th width="15%">'.$langs->trans("Ref").'</th>';
	$table .= '<th width="30%">'.$langs->trans("Label").'</th>';
	$table .= '<th>'.$langs->trans("QtyUnitNeed").'</th>';
	$table .= '<th>'.$langs->trans("QtyFactoryNeed").'</th>';
	$table .= '<th>'.$langs->trans("QtyConsummed").'</th>';
	$table .= '<th>'.$langs->trans("QtyLosed").'</th>';
	$table .= '<th>'.$langs->trans("QtyUsed").'</th>';
	$table .= '<th>'.$langs->trans("QtyRestocked").'</th>';
	$table .= '</tr>';
	
	$mntTot = 0;
	$pmpTot = 0;

	foreach( $prods_arbo as $value ) {

		$tmpChildArbo = $factory->getChildsArbo($value['id']);

		$table .= '<tr>';
		$table .= '<td align="left">'.$value['refproduct'].'</td>';
		$table .= '<td align="left" title="'.$value['description'].'">';
		$table .= $value['label'].'</td>';
		$table .= '<td align="center">'.$value['nb'];
		if ($value['globalqty'] == 1)
			$table .= "&nbsp;G";
		$table .= '</td>';
		$table .= '<td align="center">'.($value['qtyplanned']).'</td>';

		if ( $factory->fk_statut == 1 ) {
			if ( $value['qtyused'] ) {
				$table .= '<td align="center"><input type=text size=4 name="qtyused_'.$value['id'].'"  value="'.($value['qtyused']).'"></td>';
				$table .= '<td align="center"><input type=text size=4 name="qtydeleted_'.$value['id'].'"  value="'.($value['qtydeleted']).'"></td>';
				$table .= '<td align="center">'.($value['qtyused']+$value['qtydeleted']).'</td>';
				$table .= '<td align="center">'.($value['qtyplanned']-($value['qtyused']+$value['qtydeleted'])).'</td>'; 
			}
			else {
				$table .= '<td align="center"><input type=text size=4 name="qtyused_'.$value['id'].'"  value="'.($value['qtyplanned']).'"></td>';
				$table .= '<td align="center"><input type=text size=4 name="qtydeleted_'.$value['id'].'"  value="0"></td>';
				$table .= '<td ></td>';
				$table .= '<td ></td>';
			}
		}
		else {
			$table .= '<td align="center">'.$value['qtyused'].'</td>'; 
			$table .= '<td align="center">'.$value['qtydeleted'].'</td>'; 
			$table .= '<td align="center">'.($value['qtyused']+$value['qtydeleted']).'</td>'; 
			$table .= '<td align="center">'.($value['qtyplanned']-($value['qtyused']+$value['qtydeleted'])).'</td>'; 
		}
		$table .= '</tr>';
	}
	$table .= '</table>';
	
  	$html ='<html>
			<head><link type="text/css" href="style.css" rel="stylesheet" /></head>
			<body>
				<div id="cabecera">
					<table border="0" width="100%">
						<tr>
							<td width="33%" valign="top"><img src="'.$url_logo .'" width="170"></td>
							<td width="40%" valign="top" align="center">
								<h2>Reporte de Ordén de Producción</h2>
								'.$langs->trans("DateStartMade").': '.dol_print_date($date_start_made,'day').'<br />
								'.$langs->trans("DateEndMade").': '.dol_print_date($date_end_made,'day').'
							</td>
							<td align="right" valign="top">
								<span style="font-size:14px"><strong>'.$factory->ref.'</strong></span><br />
								'.$langs->trans("Product").': '.$ref.'<br />
								'.$langs->trans("QuantityPlanned").': '.$qty_planned.'<br />
								'.$langs->trans("QuantityMade").': '.$qty_made.'<br />
								Estatus: Finalizada
							</td>
						</tr>
					</table>
				</div>			
				<div id="cuerpo">
					'.$table.'
					<div><br />No. de serie: '.$no_serie.'</div>
				</div>
				<div id="pie">

				</div>';

	header('Content-type: application/pdf');

	$trigger = "
	    <script type='text/javascript'>
	        //this.print();
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
