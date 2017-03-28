<?php
/* Copyright (C) 2001-2007	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005		Eric Seigne				<eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2006		Andre Cianfarani		<acianfa@free.fr>
 * Copyright (C) 2011		Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2013-2015	Charles-Fr BENKE		<charles.fr@benke.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *  \file       htdocs/factory/report.php
 *  \ingroup    factory
 *  \brief      Page des Ordres de fabrication sur la fiche produit
 */

$res=@include("../main.inc.php");                    // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");        // For "custom" directory

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

if (! empty($conf->global->FACTORY_ADDON) && is_readable(dol_buildpath("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php")))
{
	dol_include_once("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php");
}

$langs->load("bills");
$langs->load("products");
$langs->load("stocks");
$langs->load("factory@factory");

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$cancel=GETPOST('cancel','alpha');
$key=GETPOST('key');
$parent=GETPOST('parent');

// Security check
if (! empty($user->societe_id)) $socid=$user->societe_id;
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result = restrictedArea($user, 'factory');

$mesg = '';

$product = new Product($db);
$factory = new Factory($db);
$form = new Form($db);

$productid=0;
if ($id || $ref)
{
	// l'of et le produit associ�
	$result = $factory->fetch($id, $ref);
	$result = $product->fetch($factory->fk_product);
}

if ($action == 'closeof')
{
	$factory->qty_made=GETPOST("qtymade");
	$factory->date_end_made=dol_mktime(GETPOST('madeendhour','int'),GETPOST('madeendmin','int'),0,GETPOST('madeendmonth','int'),GETPOST('madeendday','int'),GETPOST('madeendyear','int'));	
	$factory->duration_made=GETPOST("duration_madehour")*3600+GETPOST("duration_mademin")*60;
	$factory->description = GETPOST("description");
	$factory->fk_statut = 2;
	
	//on m�morise les infos de l'OF
	$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
	$sql.= " SET date_end_made = ".($factory->date_end_made ? $db->idate($factory->date_end_made) :'null');
	$sql.= " , duration_made = ".($factory->duration_made ? $factory->duration_made :'null');
	$sql.= " , qty_made = ".($factory->qty_made ? $factory->qty_made :'null');
	$sql.= " , description = '".$factory->description."'" ;
	$sql.= " , fk_statut =2";
	$sql.= " WHERE rowid = ".$id;
	if ($db->query($sql))
	{

		// on boucle sur les lignes de l'OF
		$prods_arbo =$factory->getChildsOF($id); 

		require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
		$mouvP = new MouvementStock($db);

		if (count($prods_arbo) > 0)
		{
			$totprixfabrication =0;
			foreach($prods_arbo as $value)
			{
				// on met � jour les infos des lignes de l'OF
				$sql = "UPDATE ".MAIN_DB_PREFIX."factorydet ";
				$sql.= " SET qty_used = ".(GETPOST("qtyused_".$value['id']) ? GETPOST("qtyused_".$value['id']) : 0);
				$sql.= " , qty_deleted = ".(GETPOST("qtydeleted_".$value['id']) ? GETPOST("qtydeleted_".$value['id']) : 0);
				//$sql.= " , qty_backwarhoused = ".(GETPOST("qtybackwarhoused_".$value['id']) ? GETPOST("qtybackwarhoused_".$value['id']) : 0);
				$sql.= " WHERE fk_factory = ".$id;
				$sql.= " AND fk_product = ".$value['id'];
				if ($db->query($sql))
				{
					// si les valeurs ne sont pas parfaite on ajoute des mouvements de stock 
					if ($value['qtyplanned'] != GETPOST("qtyused_".$value['id']))
					{
						// si il y a du d�truit
						if (GETPOST("qtydeleted_".$value['id']) > 0)
							$idmv=$mouvP->livraison($user, $value['id'], $factory->fk_entrepot, 
										GETPOST("qtydeleted_".$value['id']), 0, // le prix est � 0 pour ne pas impacter le pmp
										$langs->trans("DeletedFactory", $factory->ref), $factory->date_end_made);

						// on calcul si il y a du retour en stock (dans un sens ou l'autre
						$retourstock = ($value['qtyplanned'] - GETPOST("qtydeleted_".$value['id']) - GETPOST("qtyused_".$value['id']));

						if ( $retourstock  != 0 ) // on renvoie au stock (attention au sens du mouvement)
							$idmv=$mouvP->livraison($user, $value['id'], $factory->fk_entrepot, (-1*$retourstock), 0, // le prix est � 0 pour ne pas impacter le pmp
									$langs->trans("ReturnedFactory", $factory->ref), $factory->date_end_made);
						elseif ( $retourstock > 0 ) // on a utilis� moins que l'on avait, on rend au stock
							$idmv=$mouvP->reception($user, $value['id'], $factory->fk_entrepot, $retourstock, $value['price'], 
									$langs->trans("NeedMoreFactory", $factory->ref), $factory->date_end_made);

					}
					// on totalise le prix d'achat des composants utilis� pour d�terminer un prix de fabrication et mettre � jour le pmp du produit fabriqu�
					// attention on prend les quantit�s utilis� et d�truite
					//print "used=".GETPOST("qtyused_".$value['id'])."+delete=".GETPOST("qtydeleted_".$value['id'])."* pmp =".$value['pmp']."<br>";
					$totprixfabrication+=GETPOST("qtyused_".$value['id']) * $value['pmp'];
					$totprixfabrication+=GETPOST("qtydeleted_".$value['id']) * $value['pmp'];
					
				}
			}
		}
		//print "totprixfabrication=".$totprixfabrication."<br>";
		// on ajoute un mouvement de stock d'entr�e de produit
		$idmv=$mouvP->reception($user, $factory->fk_product, $factory->fk_entrepot, 
									$factory->qty_made, $totprixfabrication, 
									$langs->trans("BuildedFactory"), $factory->date_end_made);

	}
	$action="";
}
if ($action == 'reopenof')
{
	$factory->fk_statut = 1;
	$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
	$sql.= " SET fk_statut =1";
	$sql.= " WHERE rowid = ".$id;
	if ($db->query($sql))
	{
		// on supprime les mouvements de stock quand le mouvement sera stock�
	}
	$action="";
}
/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("","",$langs->trans("CardFactory"));

dol_htmloutput_mesg($mesg);

$head=factory_prepare_head($factory, $user);
$titre=$langs->trans("Factory");
$picto="factory@factory";
dol_fiche_head($head, 'factoryreport', $titre, 0, $picto);


	print '<form name="closeof" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="closeof">';
	print '<table class="border" width="100%">';
	print "<tr>";

	//$bproduit = ($product->isproduct()); 

	// Reference
	print '<td width="15%">'.$langs->trans("Ref").'</td><td colspan=3>';
	print $form->showrefnav($factory,'ref','',1,'ref');
	print '</td></tr>';

	

	// Lieu de stockage
	print '<tr><td>'.$langs->trans("Warehouse").'</td><td colspan=3>';
	if ($factory->fk_entrepot>0)
	{
		$entrepotStatic=new Entrepot($db);
		$entrepotStatic->fetch($factory->fk_entrepot);
		print $entrepotStatic->getNomUrl(1)." - ".$entrepotStatic->lieu." (".$entrepotStatic->zip.")" ;
	}

	print '</td></tr>';
	
	// Date start planned
	print '<tr><td width=20%>'.$langs->trans("DateStartPlanned").'</td><td width=30%>';
	print dol_print_date($factory->date_start_planned,'day');
	print '</td><td width=20%>'.$langs->trans("DateStartMade").'</td><td width=30%>';
	print dol_print_date($factory->date_start_made,'day');
	print '</td></tr>';

	// Date end planned
	print '<tr><td>'.$langs->trans("DateEndPlanned").'</td><td>';
	print dol_print_date($factory->date_end_planned,'day');
	print '</td><td>'.$langs->trans("DateEndMade").'</td><td>';
	if($factory->fk_statut == 1)
		print $form->select_date(($factory->date_end_made ? $factory->date_end_made : $factory->date_end_planned),'madeend',0,0,'',"madeend");
	else
		print dol_print_date($factory->date_end_made,'day');
	print '</td></tr>';
	
	// quantity
	print '<tr><td>'.$langs->trans("QuantityPlanned").'</td><td>';
	print $factory->qty_planned;
	print '</td><td>'.$langs->trans("QuantityMade").'</td><td>';
	if($factory->fk_statut == 1)
		print '<input type="text" name="qtymade" size=6 value="'.($factory->qty_made ? $factory->qty_made : $factory->qty_planned).'">';
	else
		print $factory->qty_made;
	print '</td></tr>';
	
	// duration
	print '<tr><td>'.$langs->trans("DurationPlanned").'</td><td>';
	print convertSecondToTime($factory->duration_planned,'allhourmin');
	print '</td><td>'.$langs->trans("DurationMade").'</td><td>';
	
	if($factory->fk_statut == 1)
		print $form->select_duration('duration_made', ($factory->duration_made ? $factory->duration_made : $factory->duration_planned) , 0, 'text');
	else
		print convertSecondToTime($factory->duration_made,'allhourmin');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Status').'</td><td colspan=3>'.$factory->getLibStatut(4).'</td></tr>';
	print '<tr><td valign=top>'.$langs->trans('Description').'</td><td colspan=3>';
	if($factory->fk_statut == 1)
		print '<textarea name="description" wrap="soft" cols="120" rows="'.ROWS_4.'">'.$factory->description.'</textarea>';
	else
		print str_replace(array("\r\n","\n"),"<br>",$factory->description);
	print '</td></tr>';
	print '</table>';
	print '<br>';
	
	
	
	// tableau de description du produit
	print '<table width=100% ><tr><td valign=top width=40%>';
	print_fiche_titre($langs->trans("ProducttoBuild"),'','');
	
	print '<table class="border" width="100%">';
	
	//$bproduit = ($object->isproduct()); 
	print '<tr><td width=30% class="fieldrequired">'.$langs->trans("Product").'</td><td>'.$product->getNomUrl(1)." : ".$product->label.'</td></tr>';

	// TVA
	print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($product->tva_tx.($product->tva_npr?'*':''),true).'</td></tr>';
	
	// Price
	print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
	if ($product->price_base_type == 'TTC')
	{
		print price($product->price_ttc).' '.$langs->trans($product->price_base_type);
		$sale="";
	}
	else
	{
		print price($product->price).' '.$langs->trans($product->price_base_type);
		$sale=$product->price;
	}
	print '</td></tr>';

	// Price minimum
	print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
	if ($product->price_base_type == 'TTC')
		print price($product->price_min_ttc).' '.$langs->trans($product->price_base_type);
	else
		print price($product->price_min).' '.$langs->trans($product->price_base_type);
	print '</td></tr>';



	// Status (to sell)
	print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
	print $product->getLibStatut(2,0);
	print '</td></tr>';

	// Status (to buy)
	print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
	print $product->getLibStatut(2,1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("PhysicalStock").'</td>';
	$product->load_stock();
	print '<td>'.$product->stock_reel.'</td></tr>';
	
	print '</table>';

	print '</td>';
	
	// tableau de description de la composition du produit
	print '<td  valign=top>';
	
	// indique si on a d�j� une composition de pr�sente ou pas
	$compositionpresente=0;
	
//	$factory->id =$product->id;
//	$factory->get_sousproduits_arbo();
//	// Number of subproducts
//	$prods_arbo = $factory->get_arbo_each_prod();
//	// somthing wrong in recurs, change id of object
//	$factory->id = $product->id;
	
	$prods_arbo =$factory->getChildsOF($id); 
	print_fiche_titre($langs->trans("FactorisedProductsNumber").' : '.count($prods_arbo),'','');
	
	// List of subproducts
	if (count($prods_arbo) > 0)
	{
		$compositionpresente=1;
		//print '<b>'.$langs->trans("FactoryTableInfo").'</b><BR>';
		print '<table class="border" >';
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
		print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyUnitNeed").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyFactoryNeed").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyConsummed").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyLosed").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyUsed").'</td>';
		print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyRestocked").'</td>';


		print '</tr>';
		$mntTot=0;
		$pmpTot=0;

		foreach($prods_arbo as $value)
		{

			// verify if product have child then display it after the product name
			$tmpChildArbo=$factory->getChildsArbo($value['id']);
			$nbChildArbo="";
			if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";

			print '<tr>';
			print '<td align="left">'.$factory->getNomUrlFactory($value['id'], 1,'fiche').$nbChildArbo.'</td>';
			print '<td align="left" title="'.$value['description'].'">';
			print $value['label'].'</td>';
			print '<td align="center">'.$value['nb'];
			if ($value['globalqty'] == 1)
				print "&nbsp;G";
			print '</td>';
			print '<td align="center">'.($value['qtyplanned']).'</td>';

			if ($factory->fk_statut == 1)
			{
				// si c'est la premi�re saisie on alimente avec les valeurs par d�faut
				if ($value['qtyused'])
				{
					print '<td align="center"><input type=text size=4 name="qtyused_'.$value['id'].'"  value="'.($value['qtyused']).'"></td>';
					print '<td align="center"><input type=text size=4 name="qtydeleted_'.$value['id'].'"  value="'.($value['qtydeleted']).'"></td>';
					print '<td align="right">'.($value['qtyused']+$value['qtydeleted']).'</td>';
					print '<td align="right">'.($value['qtyplanned']-($value['qtyused']+$value['qtydeleted'])).'</td>'; 
				}
				else
				{
					print '<td align="center"><input type=text size=4 name="qtyused_'.$value['id'].'"  value="'.($value['qtyplanned']).'"></td>';
					print '<td align="center"><input type=text size=4 name="qtydeleted_'.$value['id'].'"  value="0"></td>';
					print '<td ></td>';
					print '<td ></td>';
				}

			}
			else
			{
				print '<td align="right">'.$value['qtyused'].'</td>'; 
				print '<td align="right">'.$value['qtydeleted'].'</td>'; 
				print '<td align="right">'.($value['qtyused']+$value['qtydeleted']).'</td>'; 
				print '<td align="right">'.($value['qtyplanned']-($value['qtyused']+$value['qtydeleted'])).'</td>'; 
			}
			print '</tr>';

		}
		print '</table>';
	}
	print '</td>';
	print '</tr></table>';
	

/* Barre d'action				*/


if ($action == '' )
{
	print '<div class="tabsAction">';
	
	//print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/fiche.php?action=validate&id='.$id.'">'.$langs->trans("LaunchOF").'</a>';
	if ($user->rights->factory->creer && $factory->fk_statut == 1)
		print '<input type=submit class="butAction" value="'.$langs->trans("CloseFactory").'">';

//	if ($user->rights->factory->creer && $factory->fk_statut == 2)
//		print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/report.php?action=reopenof&id='.$id.'">'.$langs->trans("ReopenFactory").'</a>';

	print '</div>';

}

print '</form>';

llxFooter();
$db->close();


?>