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
 *  \file       htdocs/factory/product/fiche.php
 *  \ingroup    product
 *  \brief      Page de création des Ordres de fabrication sur la fiche produit
 */

$res=@include("../../main.inc.php");                    // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");        // For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php";
require_once DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php";

dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');

if (! empty($conf->global->FACTORY_ADDON) && is_readable(dol_buildpath("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php"))) {
	if( $conf->global->FACTORY_ADDON == '._mod_babouin' ) {
		dol_include_once("../core/modules/factory/mod_babouin.php");
	}
	else if( $conf->global->FACTORY_ADDON == '._mod_mandrill' ) {
		dol_include_once("../core/modules/factory/mod_mandrill.php");
	}
	else {
		dol_include_once("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php");
	}
}
else {
	$conf->global->FACTORY_ADDON = '._mod_mandrill';
}

$langs->load("bills");
$langs->load("products");
$langs->load("factory@factory");

$factoryid = GETPOST('factoryid','int');
$id = GETPOST('id','int');
$ref = GETPOST('ref','alpha');
$action = GETPOST('action','alpha');
$confirm = GETPOST('confirm','alpha');
$cancel = GETPOST('cancel','alpha');
$key = GETPOST('key');
$parent = GETPOST('parent');

// Security check
if (! empty($user->societe_id)) $socid=$user->societe_id;
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result=restrictedArea($user,'produit|service',$fieldvalue,'product&product','','',$fieldtype,$objcanvas);

$mesg = '';

$object = new Product($db);
$factory = new Factory($db);
$productid=0;
if ($id || $ref) {
	$result = $object->fetch($id,$ref);
	$productid=$object->id;
	$id=$object->id;
	$factory->id =$id;
}

/*
 * Actions
 */
if ($cancel == $langs->trans("Cancel")) {
	$action = '';
}

// build product on each store
if ($action == 'createof' && GETPOST("createofrun")) {
	
	// on récupère les valeurs saisies
	$factory->fk_entrepot = GETPOST("entrepotid");
	$factory->qty_planned = GETPOST("nbToBuild");
	$factory->date_start_planned=dol_mktime(GETPOST('plannedstarthour','int'),GETPOST('plannedstartmin','int'),0,GETPOST('plannedstartmonth','int'),GETPOST('plannedstartday','int'),GETPOST('plannedstartyear','int'));	
	$factory->date_end_planned=dol_mktime(GETPOST('plannedendhour','int'),GETPOST('plannedendmin','int'),0,GETPOST('plannedendmonth','int'),GETPOST('plannedendday','int'),GETPOST('plannedendyear','int'));	
	$factory->duration_planned=GETPOST("workloadhour")*3600+GETPOST("workloadmin")*60;
	$factory->description=GETPOST("description");
	$factory->series=GETPOST("no_serie");
	//print $factory->series; exit();
	
	$newref = $factory->createof();
	
	// Little message to inform of the number of builded product
	$mesg = '<div class="ok">'.$newref.' '.$langs->trans("FactoryOrderSaved").'</div>';
	$action = "";
	$object->newref = $newref;
	$object->fk_id = $id;

	include_once(DOL_DOCUMENT_ROOT."/core/class/interfaces.class.php");
    $interface = new Interfaces($db);
    $result = $interface->run_triggers('CREATE_PDF_PRODUCTION', $object, $user, $langs, $conf);
    if ($result < 0) { $error++; $this->errors = $interface->errors; }
	
	// on affiche la liste des of en cours pour le produit 
	//Header("Location: list.php?fk_status=1&id=".$id);
}

/*
 * View
 */

$productstatic = new Product($db);
$form = new Form($db);

$morejs = array("/factory/js/funciones.js");
llxHeader('',$langs->trans("CardProduct".$product->type),'','','','',$morejs,'',0,0);
	
$head=product_prepare_head($object, $user);
$titre=$langs->trans("CardProduct".$object->type);
$picto=('product');
dol_fiche_head($head, 'factory', $titre, 0, $picto);

if ( $id || $ref ) {
	if ( $result ) {
		print '<table class="border" width="100%">';
		print "<tr>";

		$bproduit = ($object->isproduct()); 

		// Reference
		print '<td width="25%">'.$langs->trans("Ref").'</td><td>';
		print $form->showrefnav($object,'ref','',1,'ref');
		print '</td></tr>';

		// Libelle
		print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->libelle.'</td>';
		print '</tr>';

		// MultiPrix
		if ( $conf->global->PRODUIT_MULTIPRICES ) {
			if ( $socid ) {
				$soc = new Societe($db);
				$soc->id = $socid;
				$soc->fetch($socid);

				print '<tr><td>'.$langs->trans("SellingPrice").'</td>';

				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC') {
					print '<td>'.price($object->multiprices_ttc["$soc->price_level"]);
				}
				else {
					print '<td>'.price($object->multiprices["$soc->price_level"]);
				}

				if ($object->multiprices_base_type["$soc->price_level"]) {
					print ' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else {
					print ' '.$langs->trans($object->price_base_type);
				}
				print '</td></tr>';

				// Prix mini
				print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC') {
					print price($object->multiprices_min_ttc["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else {
					print price($object->multiprices_min["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				print '</td></tr>';

				// TVA
				print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx["$soc->price_level"],true).'</td></tr>';
			}
			else {
				for ($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {
					// TVA
					if ($i == 1) { // We show only price for level 1 
					     print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx[1],true).'</td></tr>';
					}
					
					print '<tr><td>'.$langs->trans("SellingPrice").' '.$i.'</td>';
		
					if ($object->multiprices_base_type["$i"] == 'TTC') {
						print '<td>'.price($object->multiprices_ttc["$i"]);
					}
					else {
						print '<td>'.price($object->multiprices["$i"]);
					}
		
					if ($object->multiprices_base_type["$i"]) {
						print ' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else {
						print ' '.$langs->trans($object->price_base_type);
					}
					print '</td></tr>';
		
					// Prix mini
					print '<tr><td>'.$langs->trans("MinPrice").' '.$i.'</td><td>';
					if ($object->multiprices_base_type["$i"] == 'TTC') {
						print price($object->multiprices_min_ttc["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else {
						print price($object->multiprices_min["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					print '</td></tr>';
				}
			}
		}
		else {
			// TVA
			print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->tva_tx.($object->tva_npr?'*':''),true).'</td></tr>';
			
			// Price
			print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
			if ($object->price_base_type == 'TTC') {
				print price($object->price_ttc).' '.$langs->trans($object->price_base_type);
				$sale="";
			}
			else {
				print price($object->price).' '.$langs->trans($object->price_base_type);
				$sale=$object->price;
			}
			print '</td></tr>';
		
			// Price minimum
			print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
			if ($object->price_base_type == 'TTC') {
				print price($object->price_min_ttc).' '.$langs->trans($object->price_base_type);
			}
			else {
				print price($object->price_min).' '.$langs->trans($object->price_base_type);
			}
			print '</td></tr>';
		}

		// Status (to sell)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
		print $object->getLibStatut(2,0);
		print '</td></tr>';

		// Status (to buy)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
		print $object->getLibStatut(2,1);
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("PhysicalStock").'</td>';
		print '<td>'.$object->stock_reel.'</td></tr>';
		
		print '</table>';
		
		dol_fiche_end();

		// indique si on a déjà une composition de présente ou pas
		$compositionpresente=0;
		
		$head=factory_product_prepare_head($object, $user);
		$titre=$langs->trans("Factory");
		$picto="factory@factory";
		dol_fiche_head($head, 'neworderbuild', $titre, 0, $picto);

		$prodsfather = $factory->getFather(); //Parent Products
		$factory->get_sousproduits_arbo();
		// Number of subproducts
		$prods_arbo = $factory->get_arbo_each_prod();
		// somthing wrong in recurs, change id of object
		$factory->id = $id;
		print_fiche_titre($langs->trans("FactorisedProductsNumber").' : '.count($prods_arbo),'','');
		
		// List of subproducts
		if (count($prods_arbo) > 0) {
			$compositionpresente=1;
			print '<b>'.$langs->trans("FactoryTableInfo").'</b><BR>';
			print '<table class="border" >';
			print '<tr class="liste_titre">';
			print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
			print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
			print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyNeed").'</td>';
			// on affiche la colonne stock même si cette fonction n'est pas active
			print '<td class="liste_titre" width=50px align="center">'.$langs->trans("Stock").'</td>'; 
			if ($conf->stock->enabled) { 	// we display vwap titles
				print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
				print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
			}
			else { 	// we display price as latest purchasing unit price title
				print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitHA").'</td>';
				print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostHA").'</td>';
			}
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
			print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';

			print '</tr>';
			$mntTot=0;
			$pmpTot=0;

			foreach($prods_arbo as $value) {

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

				if ($conf->stock->enabled) {	
					if ($value['fk_product_type']==0) { 	// if product
						$productstatic->fetch($value['id']);
						$productstatic->load_stock();
						print '<td align=center>'.$factory->getUrlStock($value['id'], 1, $productstatic->stock_reel).'</td>';
					}
					else // no stock management for services
						print '<td></td>';
				}

				print '<td align="right">'.price($value['pmp']).'</td>'; // display else vwap or else latest purchasing price
				print '<td align="right">'.price($value['pmp']*$value['nb']).'</td>'; // display total line
				print '<td align="right">'.price($value['price']).'</td>';
				print '<td align="right">'.price($value['price']*$value['nb']).'</td>';
				print '<td align="right">'.price(($value['price']-$value['pmp'])*$value['nb']).'</td>'; 
				
				$mntTot=$mntTot+$value['price']*$value['nb'];
				$pmpTot=$pmpTot+$value['pmp']*$value['nb']; // sub total calculation
				
				print '</tr>';

			}
			print '<tr class="liste_total">';
			print '<td colspan=5 align=right >'.$langs->trans("Total").'</td>';
			print '<td align="right" >'.price($pmpTot).'</td>';
			print '<td ></td>';
			print '<td align="right" >'.price($mntTot).'</td>';
			print '<td align="right" >'.price($mntTot-$pmpTot).'</td>';
			print '</tr>';
			print '</table>';
		}

		if(GETPOST('cambiaprod') && GETPOST('action2')=='cambioprod') {
			$prodactual=GETPOST('prodactual');
			$prodcantidad=GETPOST('prodcantidad');
			$prsustituto=GETPOST('prsustituto');
		
			$entrepotid=GETPOST("entrepotid2");
			$plannedstart=GETPOST('plannedstart2');
			$plannedstarthour=GETPOST('plannedstarthour2');
			$plannedstartmin=GETPOST('plannedstartmin2');
			$plannedend=GETPOST('plannedend2');
			$plannedendhour=GETPOST('plannedendhour2');
			$plannedendmin=GETPOST('plannedendmin2');
			$workloadhour=GETPOST("workloadhour2");
			$workloadmin=GETPOST("workloadmin2");
			$description=GETPOST("description2");
			
			$plannedstartmonth=GETPOST('plannedstartmonth2');
			$plannedstartday=GETPOST('plannedstartday2');
			$plannedstartyear=GETPOST('plannedstartyear2');
			$plannedendmonth=GETPOST('plannedendmonth2');
			$plannedendday=GETPOST('plannedendday2');
			$plannedendyear=GETPOST('plannedendyear2');
			$producto2= new Product($db);
			$producto2->fetch($prsustituto);
			$pmp2=$producto2->pmp;
			$price2=$producto2->price;
			$verifyof=GETPOST('verifyof');
			$nbToBuild=GETPOST('nbToBuild2');
			$sqlv="UPDATE ".MAIN_DB_PREFIX."product_factory
									SET fk_product_children=".$prsustituto.",pmp=".$pmp2.", price=".$price2."
									WHERE fk_product_father=".$id." AND fk_product_children=".$prodactual;//." AND qty=".$prodcantidad;
			//print $sqlv;
			$vrq=$db->query($sqlv);
			print "<script>window.location.href='fiche.php?id=".$id."&action=".$action."&entrepotid=".$entrepotid."&plannedstart=".$plannedstart."&plannedstarthour=".$plannedstarthour."&plannedstartmin=".$plannedstartmin."&plannedend=".$plannedend."&plannedendhour=".$plannedendhour."&plannedendmin=".$plannedendmin."&workloadhour=".$workloadhour."&workloadmin=".$workloadmin."&description=".$description."&plannedstartmonth=".$plannedstartmonth."&plannedstartday=".$plannedstartday."&plannedstartyear=".$plannedstartyear."&plannedendmonth=".$plannedendmonth."&plannedendday=".$plannedendday."&plannedendyear=".$plannedendyear."&verifyof=".$verifyof."&nbToBuild=".$nbToBuild."'</script>";
		}
		if ($action == 'build' || $action == 'createof') {
			//print $action."::";
			// Display the list of store with buildable product 
			print '<br>';
			print_fiche_titre($langs->trans("CreateOF"),'','');
			
			print '<form name="createof" action="'.DOL_URL_ROOT.'/factory/product/fiche.php?id='.$id.'" method="post">';
			print '<input type="hidden" name="action" value="createof">';
			print '<table class="nobordernopadding"><tr><td width=50% valign=top>';
			print '<table class="border">';
			print '<tr><td width=250px>'.$langs->trans("EntrepotStock").'</td><td width=250px>';
			select_entrepot(GETPOST("entrepotid"),"entrepotid",0,1);
			print '</td></tr>';
			print '<tr><td>'.$langs->trans("QtyToBuild").'</td>';
			print '<td  ><input style="text-align:right;" type="text" name="nbToBuild" size=5 value="'.GETPOST("nbToBuild").'">';
			print '</td></tr>';
			
			print '<tr><td>'.$langs->trans("DateStartPlanned").'</td>';
			print '<td >';
			$plannedstart=dol_mktime(GETPOST('plannedstarthour','int'),GETPOST('plannedstartmin','int'),0,GETPOST('plannedstartmonth','int'),GETPOST('plannedstartday','int'),GETPOST('plannedstartyear','int'));
			print $form->select_date((GETPOST("plannedstart")? $plannedstart:-1),'plannedstart',1,1,'',"plannedstart");
			print '</td></tr>';

			print '<tr><td>'.$langs->trans("DateEndBuildPlanned").'</td>';
			print '<td >';
			$plannedend=dol_mktime(GETPOST('plannedendhour','int'),GETPOST('plannedendmin','int'),0,GETPOST('plannedendmonth','int'),GETPOST('plannedendday','int'),GETPOST('plannedendyear','int'));
			print $form->select_date((GETPOST("plannedend")? $plannedend:-1),'plannedend',1,1,'',"plannedend");
			print '</td></tr>';
			
			print '<tr><td>'.$langs->trans("DurationPlanned").'</td>';
			print '<td>';
			print $form->select_duration('workload',GETPOST("workloadhour")*3600+GETPOST("workloadmin")*60,0,'text');
			print '</td></tr>';
			
			print '<tr><td colspan=2 valign="top">'.$langs->trans("Description").'</td></tr>';
			print '<td colspan=2 align=center>';
			print '<textarea name="description" wrap="soft" cols="80" rows="'.ROWS_3.'">'.GETPOST("description").'</textarea>';
			print '</td></tr>';

			print '<tr><td colspan=2 valign="top">Números de serie <br /> Define número inicial: <input type="text" name="numero_inicial_consecutivo" size="2" /> <input type="button" class="button" name="generar_series" value=" Generar números de serie" /></td></tr>';
			print '<td colspan=2 align=center>';
			print '<textarea name="no_serie" wrap="soft" cols="80" rows="'.ROWS_3.'">'.GETPOST("no_serie").'</textarea>';
			print '</td></tr>';

			print '<tr><td colspan=2 valign="top"><div id="div_alerta" align="center" style="color:red; padding:5px">Números de serie sin definir </div></td></tr>';
			
			
			print '</table>';
			print '</td>';
			print '<td valign=top width=50%>';
			if (GETPOST("verifyof")) {
				// on vérifie que la quantité à fabriqué a bien été saisie (valeur obligatoire)
				if (GETPOST("nbToBuild")) {
					// List of subproducts
					if (count($prods_arbo) > 0) {
						//print_r($_POST);
						$nbtobuild=GETPOST("nbToBuild");
						print '<table class="border" >';
						print '<tr class="liste_titre">';
						print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
						print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
						print '<td class="liste_titre" width=100px align="center">'.$langs->trans("QtyNeedOF").'</td>';
						print '<td class="liste_titre" width=100px align="center">'.$langs->trans("QtyPresent").'</td>'; 
						print '<td class="liste_titre" width=100px>'.$langs->trans("QtyAlert").'</td>';
						print '</tr>';
			
						foreach($prods_arbo as $value) {
							//var_dump($value);
							$productstatic->id=$value['id'];
							$productstatic->fetch($value['id']);
							$productstatic->type=$value['type'];
							// verify if product have child then display it after the product name
							$tmpChildArbo=$productstatic->getChildsArbo($value['id']);
							$nbChildArbo="";
							if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";
			
							print '<tr>';
							print '<td align="left">'.$factory->getNomUrlFactory($value['id'], 1,'fiche').$nbChildArbo.'</td>';
							print '<td align="left">'.$productstatic->label.'</td>';
							if ($value['globalqty'] == 0)
								print '<td align="right">'.$value['nb']*$nbtobuild.'</td>';
							else
								print '<td align="right">'.$value['nb'].'</td>';
							// uniquement pour les produits pas pour les services
							if ($value['type']!=1) { 	// if product
								$productstatic->load_stock();
								print '<td align=right>'.$productstatic->stock_warehouse[GETPOST("entrepotid")]->real.'</td>';
								print '<td align=right>';
								// pour gérer les niveaux d'alertes
								$qterestante=$productstatic->stock_warehouse[GETPOST("entrepotid")]->real - $value['nb']*$nbtobuild;
								if ($qterestante < 0)
									print '<font color="red"><b>'.$qterestante.'</b></font>';
								else // là on est OK
									print '<font color="green"><b>'."OK".'</b></font>';
								print '</td>';
							}
							else {  // no stock management for services, all is OK
								print '<td align=center>'.$langs->trans("Service").'</td>';
								print '<td align=right><font color="green"><b>'."OK".'</b></font></td>';
							}
							print '</tr>';
							
							$sqlv="SELECT fk_sustituto, ref, label
									FROM ".MAIN_DB_PREFIX."factory_producto_sustituto a, ".MAIN_DB_PREFIX."product b
									WHERE fk_product=".$value['id']." AND fk_sustituto=b.rowid";
							
							$vrq = $db->query($sqlv);
							$vnr = $db->num_rows($vrq);
							if( $vnr > 0 ) {
								print "<tr>";
								print "<td colspan='5'>";
								print "<form method='POST' action='fiche.php?id=".$id."'>";
								print "<input type='hidden' name='action' value='".$action."'>";
								print "<input type='hidden' name='entrepotid2' value='".GETPOST("entrepotid")."'>";
								print "<input type='hidden' name='plannedstart2' value='".GETPOST("plannedstart")."'>";
								print "<input type='hidden' name='plannedstarthour2' value='".GETPOST("plannedstarthour")."'>";
								print "<input type='hidden' name='plannedstartmin2' value='".GETPOST("plannedstartmin")."'>";
								print "<input type='hidden' name='plannedend2' value='".GETPOST("plannedend")."'>";
								print "<input type='hidden' name='plannedendhour2' value='".GETPOST("plannedendhour")."'>";
								print "<input type='hidden' name='plannedendmin2' value='".GETPOST("plannedendmin")."'>";
								print "<input type='hidden' name='workloadhour2' value='".GETPOST("workloadhour")."'>";
								print "<input type='hidden' name='workloadmin2' value='".GETPOST("workloadmin")."'>";
								print "<input type='hidden' name='description2' value='".GETPOST("description")."'>";
								print "<input type='hidden' name='plannedstartmonth2' value='".GETPOST("plannedstartmonth")."'>";
								print "<input type='hidden' name='plannedstartday2' value='".GETPOST("plannedstartday")."'>";
								print "<input type='hidden' name='plannedstartyear2' value='".GETPOST("plannedstartyear")."'>";
								print "<input type='hidden' name='plannedendmonth2' value='".GETPOST("plannedendmonth")."'>";
								print "<input type='hidden' name='plannedendday2' value='".GETPOST("plannedendday")."'>";
								print "<input type='hidden' name='plannedendyear2' value='".GETPOST("plannedendyear")."'>";
								
								print "<input type='hidden' name='action2' value='cambioprod'>";
								print "<input type='hidden' name='prodactual' value='".$value['id']."'>";
								print "<input type='hidden' name='prodcantidad' value='".$value['nb']."'>";
								print "<input type='hidden' name='verifyof' value='".GETPOST("verifyof")."'>";
								print "<input type='hidden' name='nbToBuild2' value='".GETPOST("nbToBuild")."'>"; 
								print "Cambiar por sustituto: &nbsp;";
								print "<select name='prsustituto'>";
								while( $vrs=$db->fetch_object($vrq) ) {
									print "<option value='".$vrs->fk_sustituto."'>".$vrs->ref." - ".$vrs->label."</option>";
								}
								print "</select>";
								//print "&nbsp; SI:<input type='radio' name='checkprod' id='checkprod' value='".$value['id']."'>";
								print "&nbsp;&nbsp;<input type='submit' class='button' name='cambiaprod' value='Cambiar'></form>";
								print "</td>";
								print "</tr>";
							}
							print "<tr><td colspan='5' style='text-align: center;'><hr></td></tr>";
						}
						print '</table>';
					}
				}
				else
					$mesg='<div class="error">'.$langs->trans("QuantityToBuildNotNull").'</div>';
			}
			print '</td></tr>';
			print '<tr>';
			print '<td align="center"><br /><input type="submit" class="button" name="verifyof" value="'.$langs->trans("VerifyQty").'"></td>';
			if ( $action=='createof' && GETPOST("nbToBuild") > 0 )
				print '<td align="center"><br /><input type="submit" class="button" name="createofrun" value="'.$langs->trans("LaunchOF").'"></td>';
			
			print '</tr>';
			print '</table>';
			print '</form>';
		}
	}
}

dol_htmloutput_mesg($mesg);

/* Barre d'action				*/
print '<div class="tabsAction">';
$object->fetch($id,$ref);
if ($action == '' && $bproduit) {
	if ($user->rights->factory->creer ) {
		//Le stock doit être actif et le produit ne doit pas être à l'achat
		if ($conf->stock->enabled && $object->status_buy ==0) {
			if ($compositionpresente) {
				print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/product/fiche.php?action=build&amp;id='.$productid.'">'.$langs->trans("LaunchCreateOF").'</a>';
			}
		}
		else
			print $langs->trans("NeedNotBuyProductAndStockEnabled");
	}
}

print '</div>';
llxFooter();
$db->close();

?>