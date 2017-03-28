<?php
/* Copyright (C) 2015		Charles-Fr BENKE	<charles.fr@benke.fr>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *  \file       htdocs/factory/admin/categories.php
 *  \ingroup    factory
 *  \brief      Administration du calcul de prix vente automatique
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once("../core/lib/factory.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/categories.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");

$langs->load("admin");
$langs->load("other");
$langs->load("factory@factory");

// Security check
//if (! $user->admin || $user->design) accessforbidden();

$action = GETPOST('action','alpha');
$type = GETPOST('type','alpha');
$id = GETPOST('id');

$object = new Categorie($db);

$InitPrice=array();
$ComputeValue=array();
$ComputeMode=array();
$MultiplyBy=array();


if ($id > 0)
{
	$result = $object->fetch($id);

	$elementtype = 'product';
	$objecttype = 'produit|service&categorie';
	$objectid = isset($id)?$id:(isset($ref)?$ref:'');
	$dbtablename = 'product';
	$fieldid = isset($ref)?'ref':'rowid';

	$upload_dir = $conf->categorie->multidir_output[$object->entity];
}

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user,$objecttype,$objectid,$dbtablename,'','',$fieldid);


/*
 * Actions
 */

if ($action == 'setvalue')
{
	if ($conf->global->PRODUIT_MULTIPRICES)
	{
		for ($i=1;$i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT;$i++)
		{
			// récupération des variables
			$InitPrice[$i]=GETPOST("InitPrice".$i);
			$ComputeValue[$i]=GETPOST("ComputeValue".$i);
			$ComputeMode[$i]=GETPOST("ComputeMode".$i);
			$MultiplyBy[$i]=GETPOST("MultiplyBy".$i);
	
			// on commence par supprimer le level price
			$sql="DELETE FROM ".MAIN_DB_PREFIX."factory_categ_changeprice";
			$sql.=" WHERE fk_categories=".$id;
			$sql.=" AND price_level=".$i;
			$result = $db->query($sql);

			// et on ajoute les nouveaux
			$sql="INSERT INTO ".MAIN_DB_PREFIX."factory_categ_changeprice (fk_categories, price_level, init_price, computemode, computevalue, multiplyby)";
			$sql.=" VALUES ( ".$id.", ".$i.", '".$InitPrice[$i]."', '".$ComputeMode[$i]."', ".price2num($ComputeValue[$i]).", '".$MultiplyBy[$i]."')";
			$result = $db->query($sql);
			//print $sql.'<br>';
		}
	}
	if ($conf->global->PRODUCT_PRICE_UNIQ)
	{
		// on commence par supprimer le level price
		$sql="DELETE FROM ".MAIN_DB_PREFIX."factory_categ_changeprice";
		$sql.=" WHERE fk_categories=".$id;
		$sql.=" AND price_level=1";
		$result = $db->query($sql);
		
		// et on ajoute le nouveau
		$sql="INSERT INTO ".MAIN_DB_PREFIX."factory_categ_changeprice (fk_categories, price_level, init_price, computemode, computevalue, multiplyby)";
		$sql.=" VALUES ( ".$id.", 1, '".GETPOST("InitPrice")."', '".GETPOST("ComputeMode")."', ".price2num(GETPOST("ComputeValue")).", '".GETPOST("MultiplyBy")."')";
		$result = $db->query($sql);
			
	}
	
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
}

/*
 * View
 */


$form = new Form($db);

$page_name = $langs->trans("ChangePriceSetting");
llxHeader('', $page_name);

$title=$langs->trans("ProductsCategoryShort");

if ($conf->global->PRODUIT_MULTIPRICES)
{
	// on récupère les valeurs, si mono prix la valeur est dans le 0
	for ($i=1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++)
	{
		$sql="SELECT * FROM ".MAIN_DB_PREFIX."factory_categ_changeprice";
		$sql.=" WHERE fk_categories=".$id;
		$sql.=" AND price_level=".$i;
		$resqlcateg=$db->query($sql);
		//print $sql.'<br>';
		if ($resqlcateg)
		{
			// specifique dauvet
			$numcomp = $db->num_rows($resqlcateg);

			$objp = $db->fetch_object($resqlcateg);

			// get the setting
			$InitPrice[$i]    = $objp->init_price;
			$ComputeValue[$i] = $objp->computevalue;
			$ComputeMode[$i]  = $objp->computemode;
			$MultiplyBy[$i]   = $objp->multiplyby;
		}
	}
}

//var_dump($MultiplyBy);

if ($conf->global->PRODUCT_PRICE_UNIQ)
{
	$sql="SELECT * FROM ".MAIN_DB_PREFIX."factory_categ_changeprice";
	$sql.=" WHERE fk_categories=".$id;
	$sql.=" AND price_level=1";
	
	$resqlcateg=$db->query($sql);
	
	if ($resqlcateg)
	{
		// specifique dauvet
		$numcomp = $db->num_rows($resqlcateg);
		// on boucle sur les matières premières
		$objp = $db->fetch_object($resqlcateg);
		// get the setting
		$InitPrice    = $objp->init_price;
		$ComputeValue = $objp->computevalue;
		$ComputeMode  = $objp->computemode;
		$MultiplyBy   = $objp->multiplyby;
	}

}
$head = categories_prepare_head($object, $type);

dol_fiche_head($head, 'factory', $langs->trans("ProductsCategoryShort"), 0, "category");

print '<table class="border" width="100%">';

// Reference
print '<tr>';
print '<td width="15%">'.$langs->trans("Ref").'</td><td colspan="2">';
print $object->label;
print '</td>';
print '</tr>';
print '</table>';


print '<br>';
print_titre($langs->trans("FactoryChangePriceSetting"));
print '<br>';

print $langs->trans("FactoryChangePriceInfo");
print '<br><br>';
print '<form method="post" action="categories.php">';
print '<input type="hidden" name="action" value="setvalue">';
print '<input type="hidden" name="type" value="'.$type.'">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<td width=30% align=left>&nbsp;</td>';
print '<td  align=left>'.$langs->trans("BasedPrice").'</td>';
print '<td  align=left>'.$langs->trans("ComputationMode").'</td>';
print '<td  align=center>'.$langs->trans("Value").'</td>';

print '<td  align=left>'.$langs->trans("MultiplyMode").'</td>';
print '</tr>'."\n";
if ($conf->global->PRODUIT_MULTIPRICES)
{
	for ($i=1;$i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT;$i++)
	{
		print '<tr >';
		print '<td   align=left>'.$langs->trans("FactoryChangepriceFormulaMulti").' '.$i.'</td>';
		print '<td align=left><select name="InitPrice'.$i.'" >';
		print '<option value=sellprice '.($InitPrice[$i]=='sellprice'?' selected ':'').'>'.$langs->trans("SellPrice").'</option>';
		print '<option value=pmpprice '.($InitPrice[$i]=='pmpprice'?' selected ':'').'>'.$langs->trans("PmpPrice").'</option>';
		print '</select></td>';		
		print '<td align=left><select name="ComputeMode'.$i.'" >';
		print '<option value=add '.($ComputeMode[$i]=='add'?' selected ':'').'>'.$langs->trans("Add").'</option>';
		print '<option value=subtract '.($ComputeMode[$i]=='subtract'?' selected ':'').'>'.$langs->trans("Subtract").'</option>';
		print '<option value=multiply '.($ComputeMode[$i]=='multiply'?' selected ':'').'>'.$langs->trans("Multiply").'</option>';
		print '<option value=divide '.($ComputeMode[$i]=='divide'?' selected ':'').'>'.$langs->trans("Divide").'</option>';
		print '</select></td>';
		print '<td  align=left><input type =text size=10 name=ComputeValue'.$i.' value="'.price($ComputeValue[$i]?$ComputeValue[$i]:"0").'"></td>';
		print '<td align=left><select name="MultiplyBy'.$i.'" >';
		print '<option value=notused '.($MultiplyBy[$i]=='notused'?' selected ':'').'>'.$langs->trans("NotUsed").'</option>';
		print '<option value=nbproduct '.($MultiplyBy[$i]=='nbproduct'?' selected ':'').'>'.$langs->trans("ByNbProduct").'</option>';
		print '<option value=nbservice '.($MultiplyBy[$i]=='nbservice'?' selected ':'').'>'.$langs->trans("ByNbService").'</option>';
		print '</select></td>';
		print '</tr>'."\n";
	}
}
if ($conf->global->PRODUCT_PRICE_UNIQ)
{
	print '<tr >';
	print '<td width=20%  align=left>'.$langs->trans("FactoryChangepriceFormula").'</td>';
	print '<td align=left><select name="InitPrice" >';
	print '<option value=sellprice '.($InitPrice=='sellprice'?' selected ':'').'>'.$langs->trans("SellPrice").'</option>';
	print '<option value=pmpprice '.($InitPrice=='pmpprice'?' selected ':'').'>'.$langs->trans("PmpPrice").'</option>';
	print '</select></td>';	
	print '<td align=center><select name="ComputeMode" >';
	print '<option value=add '.($ComputeMode='add'?' selected ':'').'>'.$langs->trans("Add").'</option>';
	print '<option value=subtract '.($ComputeMode='subtract'?' selected ':'').'>'.$langs->trans("Minus").'</option>';
	print '<option value=multiply '.($ComputeMode='multiply'?' selected ':'').'>'.$langs->trans("Multiply").'</option>';
	print '<option value=divide '.($ComputeMode='divide'?' selected ':'').'>'.$langs->trans("Divide").'</option>';
	print '</select></td>';
	print '<td  align=left><input type =text size=10 name="ComputeValue" value="'.$ComputeValue.'"></td>';
	print '<td align=left><select name="MultiplyBy" >';
	print '<option value=notused '.($MultiplyBy=='notused'?' selected ':'').'>'.$langs->trans("NotUsed").'</option>';
	print '<option value=nbproduct '.($MultiplyBy=='nbproduct'?' selected ':'').'>'.$langs->trans("ByNbProduct").'</option>';
	print '<option value=nbservice '.($MultiplyBy=='nbservice'?' selected ':'').'>'.$langs->trans("ByNbService").'</option>';
	print '</select></td>';
	print '</tr>'."\n";
}

print '<tr ><td>';
// Boutons d'action
print '<div class="tabsAction">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</div>';
print '</td></tr>'."\n";
print '</table>';
print '</form>';
// Show errors
dol_htmloutput_errors($object->error,$object->errors);

// Show messages
dol_htmloutput_mesg($object->mesg,'','ok');

// Footer
llxFooter();
// Close database handler
$db->close();
?>