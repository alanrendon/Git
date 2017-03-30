 <?php

$res=@include("../main.inc.php");                   // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");        // For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

$langs->load("bills");
$langs->load("products");
$langs->load("stocks");
$langs->load("factory@factory");

$morejs = array("/factory/js/funciones.js");
llxHeader('',$langs->trans("Factory"),'','','','',$morejs,'',0,0);

$idPropal=GETPOST('id','int');
$action=GETPOST('action');

$objectstatic=new Propal($db);
$companystatic=new Societe($db);
$fact= NEW Factory($db);
$product = new Product($db);

$sql='SELECT
		pe.nom_proyecto,
		pe.no_proyecto,
		pe.no_proveedor,
		pe.solic,
		s.rowid,
		s.nom AS NAME,
		s.client,
		p.ref_client,
		p.fk_statut,
		p.fk_status_factory,
		p.fk_user_author,
		p.datep AS dp,
		s.code_client,
		p.fin_validite AS dfv,
		p.ref,
		p.rowid
	FROM
		llx_societe AS s,
		llx_propal AS p,
		llx_propal_extrafields AS pe
	WHERE
		p.fk_soc = s.rowid
	AND pe.fk_object = p.rowid
	AND p.rowid ='.$idPropal;
	//echo $sql;

$query=$db->query($sql);
$n=$db->num_rows($query);

if($n>0){
	$dat=$db->fetch_object($query);
	$statusFac=$dat->fk_status_factory;
	$refPropal=$dat->ref;
}

print '<fieldset>';
	print '<br/>';
	print load_fiche_titre($langs->trans("FactoryReport"));		
	print '<table class="border"  width="100%">';
		print '<tr>';
			print '<td class="nobordernopadding" colspan="4">';		
				print '<div style="vertical-align: middle">
					<div class="inline-block floatleft"></div>
					<div class="inline-block floatleft valignmiddle refid refidpadding"></div>
					<div class="pagination">
						<ul>
							<li class="pagination"></li>
							<li class="pagination">		
								<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="factory_list.php">&lt;</a>
							</li>
						</ul>
					</div>
				</div>';
			print '</td>';
		print '</tr>';
		print '<tr>';
			print '<td width=20% align="left">'.$langs->trans("Ref").'</td>';
			print '</td>';
			print '<td>';
				$objectstatic->id=$idPropal;
				$objectstatic->ref=$dat->ref;
				print $objectstatic->getNomUrl(1);
			print '</td>';	
			print '<td width=20% align="left">'.$langs->trans("Customer").'</td>';
			print '</td>';
			print '<td>';
				$companystatic->id=$dat->rowid;
				$companystatic->name=$dat->NAME;
				$companystatic->client=$dat->client;
				$companystatic->code_client=$dat->code_client;
				print $companystatic->getNomUrl(1,'customer');
			print '</td>';
		print '</tr>';	
		print '<tr>';
			print '<td  width=100px align="left">'.$langs->trans("NameProject").'</td>';
			print '</td>';
			print '<td>';
				print $dat->nom_proyecto;
			print '</td>';
			print '<td  width=100px align="left">'.$langs->trans("NumProject").'</td>';
			print '</td>';
			print '<td>';
				print $dat->no_proyecto;
			print '</td>';
		print '</tr>';	
		print '<tr>';
			print '<td  width=100px align="left">'.$langs->trans("NumProv").'</td>';
			print '</td>';
			print '<td>';
				print $dat->no_proveedor;
			print '</td>';
			print '<td  width=100px align="left">'.$langs->trans("Sol").'</td>';
			print '</td>';
			print '<td>';
				print $dat->solic;
			print '</td>';
		print '</tr>';	

		// print '<tr>';
		// 	print '<td  width=100px align="left">'.$langs->trans("DateStartPlanned").'</td>';
		// 	print '</td>';
		// 	print '<td>';
		// 	print '</td>';
		// 	print '<td  width=100px align="left">'.$langs->trans("DateStartMade").'</td>';
		// 	print '</td>';
		// 	print '<td>';
		// 	print '</td>';
		// print '</tr>';	

		print '<tr>';
			print '<td  width=100px align="left">'.$langs->trans("Warehouse").'</td>';
			print '</td>';
			print '<td>';
				$fact->entrepot();
				$entrepotstatic=new Entrepot($db);
				$entrepotstatic->id=$fact->fk_entrepot;
				$entrepotstatic->libelle=$fact->label;
				$entrepotstatic->lieu=$fact->lieu;
				print $entrepotstatic->getNomUrl(1);
			print '</td>';
			print '<td  width=100px align="left">'.$langs->trans("Status").'</td>';
			print '</td>';				
			print '<td>';
				print $fact->LibStatutFactory($idPropal);
			print '</td>';
		print '</tr>';	
	print '</table>';

	print '<br/>';

	print load_fiche_titre($langs->trans("Products"));		
	print '<form name="productsFac" action="' . $_SERVER["PHP_SELF"] .'?id='.$idPropal.'&action=validFactory" method="post">';
	print '<table class="border"  width="100%">';		
		print '<tr class="liste_titre">';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Label").'</td>';
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("QtyNeed").'</td>';
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("Stock").'</td>'; 
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("QtyPen").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("PriceCost").'</td>';
		print '</tr>';	

		$listProduct=array();
		$listProduct=$fact->get_product_propal($idPropal);
		$band=0;		
		$i=0;
		$pesoTeoriUnit=0;
		$cantNeed=0;
		$pend=0;

		foreach ($listProduct as $dat) {
			
			$stoc= $fact->get_qty_stock($dat->rowid);				
 
			if($listProduct[$i]->rowid==$listProduct[$i+1]->rowid){
				$pesoTeoriUnit=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);						//$cantNeed=$dat->umTotal;				
				$cantNeed+=$pesoTeoriUnit*$dat->sumPropal;
				$cantTT=$pesoTeoriUnit*$dat->sumPropal;				
				
				if($cantTT>$stoc){
					$pend+=$cantTT-$stoc;
					$band=1;					
				}
		
			}else{
				
				

				$pesoTeoriUnit=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);	//$cantNeed=$dat->sumTotal;
				$cantNeed+=$pesoTeoriUnit*$dat->sumPropal;
				$cantTT=$pesoTeoriUnit*$dat->sumPropal;				

				print '<tr >';
					print '<td align="left">'.$fact->getNomUrlFactory($dat->rowid, 1,'index').'</td>';
					print '<td align="left">'.$dat->label.'</td>';
					print '<td align="right">';			
						print number_format($cantNeed/1000,2,'.',',')." K";						
					print '</td>';		
					print '<td align="right">';						
						$qtyStoc = ($stoc>0) ? $stoc : 0 ;	
						print $fact->getUrlStock($dat->rowid, 1, $qtyStoc);			 
					print '</td>';
					print '<td align="right">';	
						
						if($cantTT>$stoc){
							$pend+=$cantTT-$stoc;
							$band=1;						
						}						
						print number_format($pend/1000,0,'.',',')." K";
					print '</td>';	
					print '<td align="right">';						
						$price=$dat->cost_price;					
						print price($price);					
					print '</td>';
					print '<td align="right">';
						$pricett= $price*$cantNeed;							
						print price($pricett);
					print '</td>';
				print '</tr>';
				$pesoTeoriUnit=0;
				$cantNeed=0;
				$pend=0;
			}

			
			$i++;
		}	

	print '</table>';
	if ($user->rights->factory->add_factory && $band==0 && $statusFac==1) {
		print '<br/>';
		print '<div  align="center" >';
			print '<input type="submit" class="button"  align="right" value="'.$langs->trans('Factory').'">';
		print '</div>';

	}

	print '<form>';
print '</fieldset>';

if($action=='confirmValidFactory'){
	$listProduct=array();
	$listProduct=$fact->get_product_propal($idPropal);	
	$fact->entrepot();			

	foreach ($listProduct as $dat) {
		$id_product=$dat->rowid;    
        $id_tw=$fact->fk_entrepot;
        $qty=$dat->sumTotal;      
        $stlabel='Decremento de stock para producci&oacute;n del presupuesto '.$refPropal;       

        $result=$product->fetch($id_product);
        $product->load_stock();    // Load array product->stock_warehouse   

        // Define value of products moved
        $pricesrc=0;
        if (! empty($product->pmp)) $pricesrc=$product->pmp;
        $pricedest=$pricesrc;           

            // Add stock
        $result=$product->correct_stock(
                $user,
                $id_tw,
                $qty,
                1,
                $stlabel,
                $pricedest               

        );	        
	}

	$string='UPDATE  llx_propal set fk_status_factory=2 where rowid='.$idPropal;
	$query=$db->query($string);	
	// echo $string;
	// die();
	print '<script>location.href="factory_list_produc.php";</script>';
}



if($action=='validFactory'){
	$fact->entrepot();
	$form =	new Form($db);
	$formconfirm = $form->formconfirm('detailsFactory.php?id='.$idPropal, 'Desea decrementar el stock ?', 'Se retirara el stock del almacen <b>'.$fact->label.'</b>', 'confirmValidFactory', $formquestion, 0, 1);
	print $formconfirm;
}

llxFooter();
$db->close();

?>