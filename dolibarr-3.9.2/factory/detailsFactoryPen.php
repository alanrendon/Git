 <?php
$res=@include("../main.inc.php");                    // For root directory
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

$idProd=GETPOST('id','int');
$action=GETPOST('action');

$objectstatic=new Propal($db);
$companystatic=new Societe($db);
$fact= NEW Factory($db);
$product = new Product($db);

// $sql='SELECT
// 			d.nom,
// 			d.rowid as socid,
// 			d.code_client,
// 			c.rowid as propalid,
// 			c.ref,';
// 			//SUM(a.qty * b.qty) AS suma,
// $sql.='  c.datep as dp
// 		FROM
// 			llx_product_factory AS a
// 		INNER JOIN llx_propaldet AS b ON a.fk_product_father = b.fk_product
// 		INNER JOIN llx_propal AS c ON b.fk_propal = c.rowid
// 		INNER JOIN llx_societe as d on c.fk_soc=d.rowid
// 		WHERE
// 			a.fk_product_children ='.$idProd.'
// 		AND c.fk_status_factory = 0
// 		AND c.fk_statut = 2';
// 		//GROUP BY			c.rowid;';
// 	//echo $sql;

// $query=$db->query($sql);
// $n=$db->num_rows($query);
// if($n>0){
	
print '<fieldset>';
	print '<br/>';
	print load_fiche_titre($langs->trans("Product"));		
	print '<table class="border"  width="100%">';
		print '<tr>';
			print '<td class="nobordernopadding" colspan="2">';		
				print '<div style="vertical-align: middle">
					<div class="inline-block floatleft"></div>
					<div class="inline-block floatleft valignmiddle refid refidpadding"></div>
					<div class="pagination">
						<ul>
							<li class="pagination"></li>
							<li class="pagination">								
								<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="factory_list_pen.php">&lt;</a>
							</li>
						</ul>
					</div>
				</div>';
			print '</td>';
		print '</tr>';
		print '<tr>';
			print '<td width=25% align="left">'.$langs->trans("Ref").'</td>';			
			print '<td align="left">'.$fact->getNomUrlFactory($idProd, 1,'index').'</td>';				
		print '</tr>';		
		print '<tr>';
			print '<td  width=100px align="left">'.$langs->trans("Product").'</td>';
			print '</td>';
			print '<td>';
				$string2='SELECT label, cost_price from llx_product where rowid='.$idProd;
				$query2=$db->query($string2);
				$res=$db->fetch_object($query2);
				$cost_price=$res->cost_price;				
				print $res->label;
			print '</td>';			
		print '</tr>';					
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
		print '</tr>';
		print '<tr>';
			print '<td  width=100px align="left">'.$langs->trans("Stock").'</td>';
			print '</td>';
			print '<td>';
				$stoc= $fact->get_qty_stock($idProd);		
				$qtyStoc = ($stoc>0) ? $stoc : 0 ;					
				print $fact->getUrlStock($idProd, 1, $qtyStoc);		
			print '</td>';			
		print '</tr>';	
	print '</table>';

	print '<br/>';	
	print load_fiche_titre($langs->trans("Propals"));			
		
	print '<form name="productsFac" action="' . $_SERVER["PHP_SELF"] .'?id='.$idPropal.'&action=validFactory" method="post">';
	print '<table class="border"  width="100%">';		
		print '<tr class="liste_titre">';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Customer").'</td>';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Date").'</td>';
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("QtyNeed").'</td>';	
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("Stock").'</td>';					
			print '<td class="liste_titre" width=50px align="right">'.$langs->trans("QtyPen").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("PriceCost").'</td>';
		print '</tr>';	

		$listP=array();
		$listP=$fact->get_propals_product($idProd);
		$band=0;
		$i=0;

		//while ($dat=$db->fetch_object($query)) {	
		foreach ($listP as $dat) {								
 			
			if($listP[$i]->propalid==$listP[$i+1]->propalid){
				$pesoTeoriUnit=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);						//$cantNeed=$dat->umTotal;				
				$cantNeed+=$pesoTeoriUnit*$dat->qty;
				$cantTT=$pesoTeoriUnit*$dat->qty;					
				
		
			}else{

				$pesoTeoriUnit=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);	//$cantNeed=$dat->sumTotal;
				$cantNeed+=$pesoTeoriUnit*$dat->qty;
				$cantTT=$pesoTeoriUnit*$dat->qty;		

				$objectstatic->id=$dat->propalid;
				$objectstatic->ref=$dat->ref;			
				
				print '<tr >';
					print '<td align="left">'.$objectstatic->getNomUrl(1).'</td>';
					print '<td>';
						$url = DOL_URL_ROOT.'/comm/card.php?socid='.$dat->socid;
						// Company
						$companystatic->id=$dat->socid;
						$companystatic->name=$dat->nom;
						//$companystatic->client=$objp->client;
						$companystatic->code_client=$dat->code_client;
						print $companystatic->getNomUrl(1,'customer');
					print '</td>';

					print '<td align="center">';
						print dol_print_date($db->jdate($dat->dp), 'day');
					print "</td>\n";

					print '<td align="right">';												
						print $cantNeed;
					print '</td>';	
					print '<td align="right">';												
						$qtyStoc = ($stoc>0) ? $stoc : 0 ;					
						print $fact->getUrlStock($idProd, 1, $qtyStoc);	
					print '</td>';									
					print '<td align="right">';							
						if($cantNeed>$stoc){
							$pend+=$cantNeed-$stoc;
							$band=1;						
						}						
//						echo $cantTT.' '.$stoc.' '.$pend.'<br/>';
						print $pend;
					print '</td>';	
					print '<td align="right">';
						//$price= $fact->priceFather($idProd);							
						$price=$cost_price;
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
	print '<form>';
print '</fieldset>';
//}

llxFooter();
$db->close();


?>