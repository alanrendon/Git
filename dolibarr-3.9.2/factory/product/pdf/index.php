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
	$fk_id = $_GET['fk_id'];
	$url_logo = $conf->mycompany->dir_output.'/logos/'.$conf->global->MAIN_INFO_SOCIETE_LOGO;
	$factory = new Factory($db);
	$product = new Product($db);
	$factory->fetch($id, $ref);
	$product->fetch($factory->fk_product);

	$ref = $product->ref;
	$qty_planned = $factory->qty_planned;

	$sql = "SELECT f.ref as ref_orden, date_start_planned, p.ref as ref_prod, label, umed, fd.qty_planned as cant, stock ";
	$sql .= "FROM ".MAIN_DB_PREFIX."factory f ";
	$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."factorydet fd ON fd.fk_factory = f.rowid ";
	$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = fd.fk_product ";
	$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields pe ON pe.fk_object = p.rowid WHERE f.rowid=".$id;
	$res = $db->query($sql);
	$num = $db->num_rows($res);

	if( $num > 0 ) {
		while ( $obj = $db->fetch_object($res) ) {
			$datos .= '<tr>';
			$datos .= '<td>'.$obj->ref_prod.'</td>';
			$datos .= '<td>'.$obj->label.'</td>';
			$datos .= '<td align="center"></td>';
			$datos .= '<td align="center">'.$obj->umed.'</td>';
			$datos .= '<td align="center">'.number_format($obj->cant, '2','.',',').'</td>';
			$datos .= '<td align="center">'.number_format($obj->stock, '2','.',',').'</td>';
			$datos .= '<td align="center"></td>';
			$datos .= '<td align="center">'.number_format(($obj->cant-$obj->stock), '2','.',',').'</td>';
			$datos .= '</tr>';
		}
	}

  	$html ='<html>
			<head><link type="text/css" href="style.css" rel="stylesheet" /></head>
			<body>
				<div id="cabecera">
					<table border="0" width="100%">
						<tr>
							<td width="33%" valign="top"><img src="'.$url_logo .'" width="170"></td>
							<td width="40%" align="top"><h2>Reporte de Ordén de Producción</h2></td>
							<td align="right" valign="top">
								<span style="font-size:14px"><strong>'.$factory->ref.'</strong></span><br />
								'.$langs->trans("Product").': '.$ref.'<br />
								'.$langs->trans("QuantityPlanned").': '.$qty_planned.'<br />
								No.Componentes: </strong>'.$num.'<br />
								Estatus: En curso
							</td>
						</tr>
					</table>
				</div>			
				<div id="cuerpo">
					<table width="100%" cellspacing="0">
						<tr>
							<th align="left">Clave</th>
							<th align="left">Descripción</th>
							<th>T.C.</th>
							<th>U.M.</th>
							<th>Requerido</th>
							<th>Existencias</th>
							<th>Inv. Fabric.</th>
							<th>Faltante</th>
						</tr>
						'.$datos.'
					</table>
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

	$carpeta = DOL_DATA_ROOT.'/factory/'.$factory->ref;
	
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}

	$file_location = $carpeta.'/'.$factory->ref.'.pdf';
	file_put_contents($file_location,$pdf);

	if( isset($fk_id) ) {
		header("Location: ../list.php?fk_status=1&id=".$fk_id);
	}
	else {
		header("Location: ../../fiche.php?id=".$id);	
	}

	//echo $dompdf->output();
?>
