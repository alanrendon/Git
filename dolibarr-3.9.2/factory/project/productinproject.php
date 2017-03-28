<?php
/* Copyright (C) 2014		Charles-Fr BENKE	<charles.fr@benke.fr>
*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       	htdocs/factory/factorytask.php
 *	\ingroup    	taskproduct
 *	\brief      	Page of product used in a task
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/factory/class/factory.class.php';
dol_include_once('/factory/core/lib/factory.lib.php');

$langs->load('companies');
$langs->load('task');
$langs->load('factory@factory');
$langs->load('products');
if (! empty($conf->margin->enabled))
  $langs->load('margins');

$error=0;

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$lineid=GETPOST('lineid','int');



$mine = $_REQUEST['mode']=='mine' ? 1 : 0;
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects
$withproject=GETPOST('withproject','int');
$project_ref = GETPOST('project_ref','alpha');

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();
//$result = restrictedArea($user, 'projet', $id, '', 'task'); // TODO ameliorer la verification


//print "withproject=".$withproject."<br>";

// Nombre de ligne pour choix de produit/service predefinis
$NBLINES=4;


$taskstatic = new Task($db);
$projectstatic = new Project($db);
$factory = new Factory($db);
$productstatic = new Product($db);
$entrepot = new Entrepot($db);

/*
 * Actions
 */

$parameters=array('socid'=>$socid);

if ($id || $ref)
	$result = $projectstatic->fetch($id,$ref);

/*
 * Actions
 */

if ($action=='sendtoproduct')
{
		$error=0;

	if (GETPOST("entrepotid")==-1)
	{
		$error++;
		$mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentities("Warehouse")).'</div>';
		$action='edit';
		
	}
	if (! $error)
	{
		// on ajoute un mouvement de stock d'entr�e de produit
		require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
		$mouvP = new MouvementStock($db);
		$idmv=$mouvP->livraison($user, GETPOST("idproduct"), GETPOST("entrepotid"), 
									GETPOST("qtylefted"), 0, 
									$langs->trans("ProjectFactory", $projectstatic->ref), $date);
		if ($idmv == 1)
		{ 	// si on a une ancienne version se dolibarr, on sera oblig� d'ajuster, pas possible de modifier l'ancien mouvement pr�vue
			// ce sera � modifier pour les prochaines versions de factory
			$idmv = -1;
		}
		// 
		$factory->createmvtproject($id, GETPOST("idproduct"), GETPOST("entrepotid"), GETPOST("qtylefted"), $idmv);
	}
}




/*
 * View
 */



llxHeader("","",$langs->trans("Project"));

dol_htmloutput_mesg($mesg);


/*
 * View
 */


$form = new Form($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);
$companystatic=new Societe($db);

$now=dol_now();

/*
 * Show object in view mode
 */

// Tabs for project
$head=project_prepare_head($projectstatic);
dol_fiche_head($head, 'factory', $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

$param=($mode=='mine'?'&mode=mine':'');

print '<table class="border" width="100%">';

// Ref
print '<tr><td width="30%">';
print $langs->trans("Ref");
print '</td><td>';

$linkback = '<a href="'.DOL_URL_ROOT.'/projet/liste.php">'.$langs->trans("BackToList").'</a>';

// Define a complementary filter for search of next/prev ref.
if (! $user->rights->projet->all->lire)
{
    $projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
    $projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
}
print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref');
print '</td></tr>';

// Project
print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

// Company
print '<tr><td>'.$langs->trans("Company").'</td><td>';
if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
else print '&nbsp;';
print '</td>';
print '</tr>';

// Visibility
print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
if ($projectstatic->public) print $langs->trans('SharedProject');
else print $langs->trans('PrivateProject');
print '</td></tr>';

// Statut
print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

print '</table>';

dol_fiche_end();

print '<br>';




$formconfirm='';


// indique si on a d�j� une composition de pr�sente ou pas
$compositionpresente=0;


$prods_arbo = $factory->getChildsTasks($projectstatic->id, '');
// something wrong in recurs, change id of object

print_fiche_titre($langs->trans("ProductsUsedInProject"),'','');

// List of subproducts
if (count($prods_arbo) > 0)
{
	$compositionpresente=1;

	print '<table class="border" >';
	print '<tr class="liste_titre">';
	print '<td  colspan="8"></td>';
	print '<td  colspan="5">Cant. Prevista</td>';
	print '<td  colspan="5">Cant. Usada</td>';
	print '</tr>';
	print '<tr class="liste_titre">';
	print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
	print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QuantityPlannedShort").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyFromStock").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyUsedInTask").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyLosed").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyLefted").'</td>';
	// on affiche la colonne stock m�me si cette fonction n'est pas active
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("Stock").'</td>'; 
	if ($user->rights->factory->showprice )
	{
		if ($conf->stock->enabled)
		{ 	// we display vwap titles
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
		}
		else
		{ 	// we display price as latest purchasing unit price title
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitHA").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostHA").'</td>';
		}
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
	}
	if ($user->rights->factory->showprice )
	{
		if ($conf->stock->enabled)
		{ 	// we display vwap titles
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
		}
		else
		{ 	// we display price as latest purchasing unit price title
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitHA").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostHA").'</td>';
		}
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
	}
	print '</tr>';
	$mntTot=0;
	$pmpTot=0;
	$mntUse=0;
	$pmpUse=0;
	$szligne="";

	$qtyplannedtotal=0;
	$qtyusedtotal=0;
	$qtyfromstock=0;
	$idproduct=0;
	$var=True;
	
	foreach($prods_arbo as $value)
	{
		if ($idproduct==0)
		{
			$idproduct=$value['id'];
			$labelproduct=$value['label'];
			$price=$value['price'];
			$pmp=$value['pmp'];
		}
		if ($idproduct != $value['id'])
		{ // on affiche la ligne
			if (GETPOST("idproduct") == $idproduct)
				print "<tr bgcolor=orange>";
			else
				print "<tr $bc[$var]>";

			print "<td><a href=# onclick=\"$('.detailligne".$idproduct."').toggle();\" >".img_picto("","edit_add")."</a>&nbsp;";
			$tmpChildArbo=$factory->getChildsArbo($idproduct);
			$nbChildArbo="";
			if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";
			// on r�cup�re le stock qui a �t� mouvement� sur le projet

			print $factory->getNomUrlFactory($idproduct, 1,'index').$nbChildArbo.'</td>';
			print '<td>'.$labelproduct.'</td>';
			$qtyfromstock=$factory->getQtyFromStock($id, $idproduct);
			print '<td align=right><b>'.$qtyplannedtotal.'</b></td>';
			print '<td align=right><b>'.$qtyfromstock.'</b></td>';
			print '<td align=right><b>'.$qtyusedtotal.'</b></td>';
			print '<td align=right><b>'.$qtydeletedtotal.'</b></td>';
			if ($qtyplannedtotal - ($qtyusedtotal+$qtydeletedtotal) > 0 )
				$qtylefted = $qtyplannedtotal - $qtyfromstock;
			else
				$qtylefted = ($qtyusedtotal+$qtydeletedtotal) - $qtyfromstock;
				
			if ($user->rights->factory->creer )
			{
				//Le stock doit �tre actif et le projet en activit�
				if ($conf->stock->enabled && $projectstatic->statut == 1)
				{
					$url='<a href="'.DOL_URL_ROOT.'/factory/project/productinproject.php?action=edit&withproject=1';
					$url.='&id='.$id.'&idproduct='.$idproduct.'&qtylefted='.$qtylefted.'">';
					$qtylefted = $url.$qtylefted." ".img_object($langs->trans("GetFromStock"), "sending").'</a>';
				}
			}
			print '<td align=right><b>'.$qtylefted .'</b></td>';

			if ($conf->stock->enabled)
			{	// we store vwap in variable pmp and display stock
				$productstatic->fetch($idproduct);
				if ($value['fk_product_type']==0)
				{ 	// if product
					$productstatic->load_stock();
					print '<td align=center><b>'.$factory->getUrlStock($idproduct, 1, $productstatic->stock_reel).'</b></td>';
				}
				else	// no stock management for services
					print '<td></td>';
			}
			else	// no stock management for services
				print '<td></td>';

			

			if ($user->rights->factory->showprice )
			{
				print '<td align="right"><b>'.price($pmp).'</b></td>'; // display else vwap or else latest purchasing price
				print '<td align="right"><b>'.price($pmp*$qtyproducttotal).'</b></td>'; // display total line
				print '<td align="right"><b>'.price($price).'</b></td>';
				print '<td align="right"><b>'.price($price*$qtyproducttotal).'</b></td>';
				print '<td align="right"><b>'.price(($price-$pmp)*$qtyproducttotal).'</b></td>'; 
				
				print '<td align="right"><b>'.price($pmp).'</b></td>'; // display else vwap or else latest purchasing price
				print '<td align="right"><b>'.price($pmp*$qtyusedtotal).'</b></td>'; // display total line
				print '<td align="right"><b>'.price($price).'</b></td>';
				print '<td align="right"><b>'.price($price*$qtyusedtotal).'</b></td>';
				print '<td align="right"><b>'.price(($price-$pmp)*$qtyusedtotal).'</b></td>';
			}
			print "</tr>";
			// on affiche le d�tail des taches
			print $szligne;
			$var=!$var;
			$szligne="";
			$idproduct=$value['id'];
			$labelproduct=$value['label'];
			$price=$value['price'];
			$pmp=$value['pmp'];
			$qtyplannedtotal=0;
			$qtyusedtotal=0;
			$qtydeletedtotal=0;
			$qtyproducttotal=0;
		}
		// verify if product have child then display it after the product name

		if ($bc[$var]=='class="pair"')
			$szligne.="<tr style='display:none' class='pair detailligne".$idproduct."'>";
		else
			$szligne.="<tr style='display:none' class='impair detailligne".$idproduct."'>";

		$taskstatic->fetch($value['idtask']);

		$szligne.='<td align="right">'.'</td>';
		// on affiche les infos de la tache
		$szligne.='<td align="right">'.$taskstatic->getNomUrl(1,'withproject');
		$szligne.=' ('.$taskstatic->progress." %)";
		$szligne.='</td>';
		$szligne.='<td align="center">'.$value['qtyplanned'].'</td>';
		$szligne.='<td align="right">'.'</td>';
		$szligne.='<td align="center">'.$value['qtyused'].'</td>';
		$szligne.='<td align="center">'.$value['qtydeleted'].'</td>';
		$szligne.='<td align="right">'.'</td>';
		$price=$value['price'];
		$pmp=$value['pmp'];

		$szligne.='<td></td>';

		if ($user->rights->factory->showprice )
		{
			$szligne.='<td align="right">'.price($pmp).'</td>'; // display else vwap or else latest purchasing price
			$szligne.='<td align="right">'.price($pmp*$value['qtyplanned']).'</td>'; // display total line
			$szligne.='<td align="right">'.price($price).'</td>';
			$szligne.='<td align="right">'.price($price*$value['qtyplanned']).'</td>';
			$szligne.='<td align="right">'.price(($price-$pmp)*$value['qtyplanned']).'</td>'; 
			
			$szligne.='<td align="right">'.price($pmp).'</td>'; // display else vwap or else latest purchasing price
			$szligne.='<td align="right">'.price($pmp*$value['qtyused']).'</td>'; // display total line
			$szligne.='<td align="right">'.price($price).'</td>';
			$szligne.='<td align="right">'.price($price*$value['qtyused']).'</td>';
			$szligne.='<td align="right">'.price(($price-$pmp)*$value['qtyused']).'</td>';
			//dol_syslog('ESTE PRICE::'.$productstatic->price);
			$mntTot+=$price*$value['qtyplanned'];
			$pmpTot+=$pmp*$value['qtyplanned']; // sub total calculation
			
			
			$mntUse+=$price*$value['qtyused'];
			$pmpUse+=$pmp*$value['qtyused'];
		}
		$qtyplannedtotal+=$value['qtyplanned'];
		$qtyusedtotal+=$value['qtyused'];
		$qtydeletedtotal+=$value['qtydeleted'];
		
		// on utilise toujours le plus grand
		if ($value['qtyused'] > $value['qtyplanned'])
			$qtyproducttotal+=$value['qtyused'];
		else
			$qtyproducttotal+=$value['qtyplanned'];

		$szligne.='</tr>';

		//var_dump($value);
		//print '<pre>'.$productstatic->ref.'</pre>';
		//print $productstatic->getNomUrl(1).'<br>';
		//print $value[0];	// This contains a tr line.

	}
	// on affiche le dernier produit
	if (GETPOST("idproduct") == $idproduct)
		print "<tr bgcolor=orange>";
	else
		print "<tr $bc[$var]>";
	print "<td><a href=# onclick=\"$('.detailligne".$idproduct."').toggle();\" >".img_picto("","edit_add")."</a>&nbsp;";
	$tmpChildArbo=$factory->getChildsArbo($idproduct);
	$nbChildArbo="";
	if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";

	print $factory->getNomUrlFactory($idproduct, 1,'index').$nbChildArbo.'</td>';
	print '<td>'.$labelproduct.'</td>';
	print '<td align=right><b>'.$qtyplannedtotal.'</b></td>';
	print '<td align=right><b>'.$factory->getQtyFromStock($id, $idproduct).'</b></td>';
	print '<td align=right><b>'.$qtyusedtotal.'</b></td>';
	print '<td align=right><b>'.$qtydeletedtotal.'</b></td>';
	if ($qtyplannedtotal - ($qtyusedtotal+$qtydeletedtotal) > 0 )
		$qtylefted = $qtyplannedtotal - $qtyfromstock;
	else
		$qtylefted = ($qtyusedtotal+$qtydeletedtotal) - $qtyfromstock;
		
	if ($user->rights->factory->creer )
	{
		//Le stock doit �tre actif et le projet en activit�
		if ($conf->stock->enabled && $projectstatic->statut == 1)
		{
			$url='<a href="'.DOL_URL_ROOT.'/factory/project/productinproject.php?action=edit&withproject=1';
			$url.='&id='.$id.'&idproduct='.$idproduct.'&qtylefted='.$qtylefted.'">';
			$qtylefted = $url.$qtylefted." ".img_object($langs->trans("GetFromStock"), "sending").'</a>';
		}
	}
	print '<td align=right><b>'.$qtylefted .'</b></td>';
	
	if ($conf->stock->enabled)
	{	// we store vwap in variable pmp and display stock
		$productstatic->fetch($idproduct);
		if ($value['fk_product_type']==0)
		{ 	// if product
			$productstatic->load_stock();
			print '<td align=center>'.$factory->getUrlStock($idproduct, 1, $productstatic->stock_reel).'</b></td>';
		}
		else	// no stock management for services
			print '<td></td>';
	}
	else	// no stock management for services
		print '<td></td>';
		
	if ($user->rights->factory->showprice )
	{
		print '<td align="right"><b>'.price($pmp).'</b></td>'; // display else vwap or else latest purchasing price
		print '<td align="right"><b>'.price($pmp*$qtyproducttotal).'</b></td>'; // display total line
		print '<td align="right"><b>'.price($price).'</b></td>';
		print '<td align="right"><b>'.price($price*$qtyproducttotal).'</b></td>';
		print '<td align="right"><b>'.price(($price-$pmp)*$qtyproducttotal).'</b></td>'; 
		
		print '<td align="right"><b>'.price($pmp).'</b></td>'; // display else vwap or else latest purchasing price
		print '<td align="right"><b>'.price($pmp*$qtyusedtotal).'</b></td>'; // display total line
		print '<td align="right"><b>'.price($price).'</b></td>';
		print '<td align="right"><b>'.price($price*$qtyusedtotal).'</b></td>';
		print '<td align="right"><b>'.price(($price-$pmp)*$qtyusedtotal).'</b></td>';
	}

	print "</tr>";
	print $szligne;

	if ($user->rights->factory->showprice )
	{
		print '<tr class="liste_total" >';
		print '<th colspan=8 align=right >'.$langs->trans("Total").'</th>';
		print '<td ></td>';
		print '<th align="right" >'.price($pmpTot).'</th>';
		print '<td ></td>';
		print '<th align="right" >'.price($mntTot).'</th>';
		print '<th align="right" >'.price($mntTot-$pmpTot).'</th>';
		
		
		print '<td ></td>';
		print '<th align="right" >'.price($pmpUse).'</th>';
		print '<td ></td>';
		print '<th align="right" >'.price($mntUse).'</th>';
		print '<th align="right" >'.price($mntUse-$pmpUse).'</th>';
		print '</tr>';
	}
	print '</table>';
}
print '<br><br>';

if (GETPOST("idproduct") > 0 && $action != "sendtoproduct")
{
	print_fiche_titre($langs->trans("AddMovement"),'','');
	print '<form action="'.DOL_URL_ROOT.'/factory/project/productinproject.php" method="post">';
	print '<input type="hidden" name="action" value="sendtoproduct">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="withproject" value="'.$withproject.'">';
	print '<table class="border" >';
	print '<tr class="liste_titre">';
	print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Product").'</td>';
	print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Warehouse").'</td>';
	print '<td class="liste_titre" width=100px align="center">'.$langs->trans("QtyLefted").'</td>';
	print '</tr >';
	print '<tr >';
	$productstatic->fetch(GETPOST("idproduct"));
	
	print '<td align="left">'.$productstatic->getNomUrl(1).'</td>';
	print '<td align="left">';
	print '<input type=hidden name="idproduct" value="'. GETPOST("idproduct") .'">';
	select_entrepot($search_entrepot, "entrepotid", 1, 1, GETPOST("idproduct"));
	print '</td>';
	print '<td align="right">';
	print '<input type=text size=7 name=qtylefted value="'.GETPOST("qtylefted").'">';
	print '</td>';
	print '</tr >';
	print '</table>';

	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Transfert").'">';
	print ' &nbsp; &nbsp; <input type="submit" name="cancel" class="button" value="'.$langs->trans("Cancel").'">';
	print '</center>';
	print '</form>';
	
}


$sql = "SELECT * ";
$sql.= " FROM ".MAIN_DB_PREFIX."projet_stock as ps";
$sql.= " WHERE ps.fk_project = ".$id;
$sql.= " ORDER BY ps.date_creation ";
$res = $db->query($sql);

if ($res)
{
	$nump = $db->num_rows($res);
	if ($nump)
	{
		print "<br>";
		
		// liste des mouvements de stock effectu� sur le projet
		print_fiche_titre($langs->trans("ProductsMovedInProject"),'','');
		
		print '<table class="border" >';
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Product").'</td>';
		print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Warehouse").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("Quantity").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("DateMvt").'</td>';
		if ($user->rights->factory->showprice )
		{
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
		}
		print '</tr>';
	
		$i = 0;
		while ($i < $nump)
		{
			$obj = $db->fetch_object($res);
			print '<tr >';
			$productstatic->fetch($obj->fk_product);
			print "<td>".$productstatic->getnomurl(1)."</td>";
			$entrepot->fetch($obj->fk_entrepot);
			print "<td>".$entrepot->getnomurl(1)."</td>";
			print "<td align=right>".$obj->qty_from_stock."</td>";
			print "<td align=right>".dol_print_date($obj->date_creation,'day')."</td>";
			if ($user->rights->factory->showprice )
			{
				print "<td align=right>".price($obj->pmp)."</td>";
				print "<td align=right>".price($obj->pmp * $obj->qty_from_stock)."</td>";
				$totpmp += ($obj->pmp * $obj->qty_from_stock);
				print "<td align=right>".price($obj->price)."</td>";
				print "<td align=right>".price($obj->price * $obj->qty_from_stock)."</td>";
				$totprice+=($obj->price * $obj->qty_from_stock);
				print "<td align=right>".price(($obj->price - $obj->pmp) * $obj->qty_from_stock)."</td>";
			}
			print '</tr >';
			$i++;	
		}
		if ($user->rights->factory->showprice )
		{
			print "<td colspan=5 align=right>".$langs->trans("Total")."</td>";
			print "<td align=right>".price($totpmp)."</td>";
			print "<td align=right></td>";
			print "<td align=right>".price($totprice)."</td>";
			
			print "<td align=right>".price($totprice-$totpmp)."</td>";
		}

		print '</table>';
	}
	
}


// End of page
llxFooter();
$db->close();
?>