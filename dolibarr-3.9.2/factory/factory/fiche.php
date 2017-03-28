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
 *  \file       htdocs/factory/fiche.php
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
if (! empty($conf->global->FACTORY_ADDON) /* && is_readable(dol_buildpath("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php")) */)
{
	//dol_include_once("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php");
	if($conf->global->FACTORY_ADDON=='._mod_babouin'){
		dol_include_once("../core/modules/factory/mod_babouin.php");
	}else{
		if($conf->global->FACTORY_ADDON=='._mod_mandrill'){
			dol_include_once("../core/modules/factory/mod_mandrill.php");
		}else{
			dol_include_once("/factory/core/modules/factory/".$conf->global->FACTORY_ADDON.".php");
		}
	}
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
//if (! empty($user->societe_id)) $socid=$user->societe_id;
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result = restrictedArea($user, 'factory');

$mesg = '';

$product = new Product($db);
$factory = new Factory($db);
$extrafields = new ExtraFields($db);

$form = new Form($db);

$productid=0;
if ($id || $ref)
{
	// l'of et le produit associé
	$result = $factory->fetch($id, $ref);
	if (!$id) $id = $factory->id;
	$result = $product->fetch($factory->fk_product);
	//var_dump($factory);
}

// mise à jour des composants
if ( $action == 'add_prod' 
	&& $cancel <> $langs->trans("Cancel") 
	&& $factory->fk_statut == 0		// seulement si le module est à l'état brouillon
    && $user->rights->factory->creer )
{
	$error=0;
	for($i=0;$i < $_POST["max_prod"];$i++)
	{
		//print "<br> ".$i.": ".$_POST["prod_id_chk".$i];
		if($_POST["prod_id_chk".$i] != "")
		{
			if($factory->add_componentOF($id, $_POST["prod_id_".$i], $_POST["prod_qty_".$i], 0, 0, $_POST["prod_id_globalchk".$i], $_POST["descComposant".$i]) > 0)
			{
				$action = 'edit';
			}
			else
			{
				$error++;
				$action = 're-edit';
				if ($factory->error == "isFatherOfThis") 
					$mesg = '<div class="error">'.$langs->trans("ErrorAssociationIsFatherOfThis").'</div>';
				else 
					$mesg=$factory->error;
			}
		}
		else
		{
			if ($factory->del_componentOF($id, $_POST["prod_id_".$i]) > 0)
			{
				$action = 'edit';
			}
			else
			{
				$error++;
				$action = 're-edit';
				$mesg=$product->error;
			}
		}
	}
	if (! $error)
	{
		//header("Location: ".$_SERVER["PHP_SELF"].'?id='.$object->id);
		//exit;
	}
}
elseif (substr($action,0,7) == 'setExFi' && $user->rights->factory->creer)
{
	$keyExFi= substr($action,7);
	$res=$factory->fetch_optionals($factory->id, $extralabels);
	$factory->array_options["options_".$keyExFi]=$_POST["options_".$keyExFi];
	$factory->insertExtraFields();
}
elseif ($action == 'seteditdatestartmade')
{
	$datestartmade=dol_mktime('23','59','59', $_POST["datestartmademonth"], $_POST["datestartmadeday"], $_POST["datestartmadeyear"]);
	
	//$factory->fetch($id);
	$result=$factory->set_datestartmade($user,$datestartmade);
	if ($result < 0) dol_print_error($db,$factory->error);
	$action = "";
}
elseif ($action == 'seteditdatestartplanned')
{
	$datestartplanned=dol_mktime('23','59','59', $_POST["datestartplannedmonth"], $_POST["datestartplannedday"], $_POST["datestartplannedyear"]);
	
	//$factory->fetch($id);
	$result=$factory->set_datestartplanned($user,$datestartplanned);
	if ($result < 0) dol_print_error($db,$factory->error);
	$action = "";
}
elseif ($action == 'seteditdateendplanned')
{
	$dateendplanned=dol_mktime('23','59','59', $_POST["dateendplannedmonth"], $_POST["dateendplannedday"], $_POST["dateendplannedyear"]);
	
	//$factory->fetch($id);
	$result=$factory->set_dateendplanned($user,$dateendplanned);
	if ($result < 0) dol_print_error($db,$factory->error);
	$action = "";
}
elseif ($action == 'seteditdurationplanned')
{
	$dateendplanned=GETPOST("duration_plannedhour")*3600+GETPOST("duration_plannedmin")*60;;
	
	//$factory->fetch($id);
	$result=$factory->set_durationplanned($user,$dateendplanned);
	if ($result < 0) dol_print_error($db,$factory->error);
	$action = "";
}
elseif ($action == 'setdescription')
{
	//$factory->fetch($id);
	$result=$factory->set_description($user,$_POST["description"]);
	if ($result < 0) dol_print_error($db,$factory->error);
	$action = "";
}
else if ($action == 'builddoc')	// In get or post
{
	/*
	 * Generate order document
	 * define into /core/modules/commande/modules_commande.php
	 */

	// Save last template used to generate document
	if (GETPOST('model')) $factory->setDocModel($user, GETPOST('model','alpha'));

	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$factory->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	require_once DOL_DOCUMENT_ROOT."/factory/core/modules/factory/modules_factory.php";
	//$ni= new ModeleFactory();
	$result=factory_create($db, $factory, $factory->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	$action = "";
}
else if ($action == 'cancelof')
{
	$factory->fk_statut = 3;
	$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
	$sql.= " SET fk_statut =3";
	$sql.= " WHERE rowid = ".$id;
	if ($db->query($sql))
	{
		// on supprime les mouvements de stock
	}
	$action="";
}
// Remove file in doc form
else if ($action == 'remove_file')
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	$langs->load("other");
	$upload_dir = $conf->factory->dir_output;
	$file = $upload_dir . '/' . GETPOST('file');
	$ret=dol_delete_file($file,0,0,0,$object);
	if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	$action="";
}
else if ($action=='clonar'){
	$sqlv="SELECT fk_product, fk_entrepot, qty_planned, date_start_planned, date_end_planned, 
			      duration_planned, description
			FROM ".MAIN_DB_PREFIX."factory
			WHERE rowid=".$id;
	//print $sqlv;
	$rq=$db->query($sqlv);
	$rs=$db->fetch_object($rq);
	$factory2 = new Factory($db);
	$factory2->id =$rs->fk_product;
	$factory2->fk_entrepot=$rs->fk_entrepot;
	$factory2->qty_planned=$rs->qty_planned;
	$factory2->date_start_planned=$rs->date_start_planned;
	$factory2->date_end_planned=$rs->date_end_planned;
	$factory2->duration_planned=$rs->duration_planned;
	$factory2->description=$rs->description;
	$idclon=$factory2->createof();
	//$idclon=$factory2->id;
	$sqlc="INSERT INTO ".MAIN_DB_PREFIX."factory_object_object (fk_order_origen,fk_order_clone)
			VALUES (".$id.",".$idclon.")";
	$rq=$db->query($sqlc); 
	$sqlb="DELETE FROM ".MAIN_DB_PREFIX."factorydet WHERE fk_factory=".$idclon;
	$rq=$db->query($sqlb);
	print "<script>window.location.href='fiche.php?id=".$idclon."&action=addprod'</script>";
}
if($action=='addclprod'){
	$sqlv="SELECT fk_order_origen FROM ".MAIN_DB_PREFIX."factory_object_object
			WHERE fk_order_clone=".$id;
	$rq=$db->query($sqlv);
	$rs=$db->fetch_object($rq);
	$sqlv="SELECT fk_product,a.qty_unit,a.qty_planned,a.pmp,a.price,a.description,a.globalqty,ref,label
		FROM ".MAIN_DB_PREFIX."factorydet a,".MAIN_DB_PREFIX."product b
		WHERE fk_factory=".$rs->fk_order_origen." AND fk_product=b.rowid";
	$rq1=$db->query($sqlv);
	$mn=1;
	$msgv="";
	while($rs1=$db->fetch_object($rq1)){
		//name='prod-".$rs1->fk_product."'
		//name='cantp-".$rs1->fk_product."'
		if(GETPOST('prod-'.$rs1->fk_product)){
			//print GETPOST('cantp-'.$rs1->fk_product)."<br>";
			$sqlb="SELECT sum(qty_planned) as tot
					FROM ".MAIN_DB_PREFIX."factory_object_object, ".MAIN_DB_PREFIX."factorydet
					WHERE fk_order_origen=".$rs->fk_order_origen." AND 
						  fk_order_clone=fk_factory AND fk_product=".$rs1->fk_product;
			//print $sqlb."<br>";
			$rv=$db->query($sqlb);
			$nmr=$db->num_rows($rv);
			$mm=1;
			if($nmr==0){
				$mm=1;
			}else{
				$vrs=$db->fetch_object($rv);
				//print $vrs->tot.":2:".GETPOST('cantp-'.$rs1->fk_product)."<br>";
				$maxv=($vrs->tot+GETPOST('cantp-'.$rs1->fk_product));
				//print $maxv."::".$rs1->qty_planned."<br>";
				if($maxv>$rs1->qty_planned){
					$mm=2;
				}else{
					$mm=1;
				}
			}
			if($mm==2){
				$mn=2;
				$msgv.='La cantidad indicada del producto '.$rs1->label." es mayor a la de la Orden de produccion origen.<br>";
			}
		}
	}
	if($mn==1){
		$sqlv="SELECT fk_order_origen FROM ".MAIN_DB_PREFIX."factory_object_object
			WHERE fk_order_clone=".$id;
		$rq=$db->query($sqlv);
		$rs=$db->fetch_object($rq);
		$sqlv="SELECT fk_product,a.qty_unit,a.qty_planned,a.pmp,a.price,a.description,a.globalqty,ref,label
			FROM ".MAIN_DB_PREFIX."factorydet a,".MAIN_DB_PREFIX."product b
			WHERE fk_factory=".$rs->fk_order_origen." AND fk_product=b.rowid";
		$rq1=$db->query($sqlv);
		while($rs1=$db->fetch_object($rq1)){
			//name='prod-".$rs1->fk_product."'
			//name='cantp-".$rs1->fk_product."'
			if(GETPOST('prod-'.$rs1->fk_product)){
				$description=$rs1->description;
				$qtyglobal=$rs1->globalqty;
				//print GETPOST('cantp-'.$rs1->fk_product)."<br>";
				$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'factorydet(fk_factory, fk_product,';
				$sql .= 'qty_unit, qty_planned, pmp, price, globalqty, description)';
				$sql .= ' VALUES ('.$id.', '.$rs1->fk_product.', '.price2num($rs1->qty_unit);
				$sql .= ', '.price2num(GETPOST('cantp-'.$rs1->fk_product));
				$sql .= ', '.price2num($rs1->pmp).', '.price2num($rs1->price);
				$sql .= ', '.($qtyglobal?$qtyglobal:'0').', "'.$db->escape($description).'"';
				$sql .= ' )';
				$rq2=$db->query($sql);
			}
		}
		print "<script>window.location.href='fiche.php?id=".$id."'</script>";
	}
}
$sqlm="SELECT count(*) as con1 FROM ".MAIN_DB_PREFIX."factorydet WHERE fk_factory=".$id;
$rq1=$db->query($sqlm);
$rs1=$db->fetch_object($rq1);
if($rs1->con1==0){
	$action='addprod';
}
/*
 * View
 */
$extralabels=$extrafields->fetch_name_optionals_label('factory');

$res=$factory->fetch_optionals($factory->id,$extralabels);

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("","",$langs->trans("CardFactory"));
if($msgv!=''){
	dol_htmloutput_errors($msgv);
}
dol_htmloutput_mesg($mesg);

$head=factory_prepare_head($factory, $user);
$titre=$langs->trans("Factory");
$picto="factory@factory";
dol_fiche_head($head, 'factoryorder', $titre, 0, $picto);

print '<table class="border" width="100%">';
print "<tr>";

//$bproduit = ($product->isproduct()); 

// Reference
print '<td width="20%">'.$langs->trans("Ref").'</td><td colspan=3>';
print $form->showrefnav($factory,'ref','',1,'ref');
print '</td></tr>';



// Lieu de stockage
print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Warehouse").'</td>';
if ($action != 'editstock' && $factory->fk_statut == 0) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editstock&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td><td colspan="3">';
if ($action == 'editstock')
{
	print '<form name="editstock" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="setentrepot">';
	select_entrepot($factory->fk_entrepot, 'fk_entrepot',1,1);
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
}
else
{
	if ($factory->fk_entrepot>0)
	{
		$entrepotStatic=new Entrepot($db);
		$entrepotStatic->fetch($factory->fk_entrepot);
		print $entrepotStatic->getNomUrl(1)." - ".$entrepotStatic->lieu." (".$entrepotStatic->zip.")" ;
	}
}
print '</td></tr>';

// Date start planned
print '<tr><td width=20%><table class="nobordernopadding" width="100%"><tr><td align=left>'.$langs->trans("DateStartPlanned");
if ($action != 'editdatestartplanned' && $factory->fk_statut < 2) print '<td valign=top align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdatestartplanned&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td ><td width=20% valign=top>';
if ($action == 'editdatestartplanned')
{
	print '<form name="editdatestartplanned" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="seteditdatestartplanned">';
	print $form->select_date($factory->date_start_planned,'datestartplanned',0,0,'',"datestartplanned");
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
	
}
else
	print dol_print_date($factory->date_start_planned,'day');
print '</td>';



// Date start made
print '<td valign=top  width=20%><table class="nobordernopadding" width="100%"><tr><td align=left><b>'.$langs->trans("DateStartMade").'</b><br></td>';

// c'est la saisie de cette date qui conditionne la validation ou pas de l'OF
if ($action != 'editdatestartmade' && $factory->fk_statut < 2) print '<td valign=top align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdatestartmade&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td ><td colspan="3" valign=top>';
if ($action == 'editdatestartmade')
{
	print '<form name="editdatestartmade" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="seteditdatestartmade">';
	print $form->select_date($factory->date_start_made,'datestartmade',0,0,'',"datestartmade");
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
	
}
else
	print dol_print_date($factory->date_start_made,'day');
// pour gérer la mise en forme
if ($factory->date_start_made)	
	print '<br>';
else
	print "<b><font color=red>".$langs->trans("DateStartMadeInfo")."</font></b>";
print '</td></tr>';


// Date end planned
print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("DateEndPlanned").'</td>';
if ($action != 'editdateendplanned' && $factory->fk_statut == 0) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdateendplanned&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td><td colspan="3">';
if ($action == 'editdateendplanned')
{
	print '<form name="editdateendplanned" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="seteditdateendplanned">';
	print $form->select_date($factory->date_end_planned,'dateendplanned',0,0,'',"dateendplanned");
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
}
else
{
	print dol_print_date($factory->date_end_planned,'day');
}
print '</td></tr>';

print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("QuantityPlanned").'</td>';
if ($action != 'editquantity' && $factory->fk_statut == 0) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editquantity&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td><td colspan="3">';
if ($action == 'editquantity')
{
	print '<form name="editquantity" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="setquantity">';
	print '<input type="text" name="quantity" value="'.$factory->qty_planned.'">';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
}
else
{
	print $factory->qty_planned;
}
print '</td></tr>';

// Planned workload
print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("DurationPlanned").'</td>';
if ($action != 'editdurationplanned' && $factory->fk_statut == 0) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdurationplanned&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td><td colspan="3">';
if ($action == 'editdurationplanned')
{
	print '<form name="editdurationplanned" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="seteditdurationplanned">';
	print $form->select_duration('duration_planned', $factory->duration_planned, 0, 'text');
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
}
else
	print convertSecondToTime($factory->duration_planned,'allhourmin');
print '</td></tr>';

print '<tr><td>'.$langs->trans('Status').'</td><td colspan=3>'.$factory->getLibStatut(4).'</td></tr>';

// Extrafields
if (!empty($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key=>$label)
	{
		$value=(isset($_POST["options_".$key])?$_POST["options_".$key]:$factory->array_options["options_".$key]);

		print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$label.'</td>';
		if ($action != 'ExFi'.$key && $factory->statut == 0) 
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=ExFi'.$key.'&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
		print '</tr></table></td><td colspan="3">';
		if ($action == 'ExFi'.$key)
		{
			print '<form name="ExFi'.$key.'" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="setExFi'.$key.'">';
			print $extrafields->showInputField($key,$value);
			print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
			print '</form>';
		}
		else
		{
			print $extrafields->showOutputField($key,$value);
		}

		print '</td></tr>'."\n";
	}
}

// Description
print '<tr><td valign=top><table class="nobordernopadding" width="100%"><tr><td valign=top>'.$langs->trans("Description").'</td>';
if ($action != 'editdescription' && $factory->fk_statut == 0) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdescription&amp;id='.$factory->id.'">'.img_edit($langs->trans('Modify'),1).'</a></td>';
print '</tr></table></td><td colspan="3">';
if ($action == 'editdescription')
{
	print '<form name="editdescription" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="setdescription">';
	print '<textarea name="description" wrap="soft" cols="120" rows="'.ROWS_4.'">'.$factory->description.'</textarea>';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
}
else
	print str_replace(array("\r\n","\n"),"<br>",$factory->description);
print '</td></tr>';

$sqm="SELECT fk_order_origen,ref
	FROM ".MAIN_DB_PREFIX."factory_object_object a, ".MAIN_DB_PREFIX."factory b
	WHERE fk_order_clone=".$id." AND fk_order_origen=b.rowid";
$mrs=$db->query($sqm);
$rmq=$db->num_rows($mrs);
if($rmq>0){
	$mqs=$db->fetch_object($mrs);
	print '<tr><td valign=top><table class="nobordernopadding" width="100%"><tr><td valign=top>'.$langs->trans("Orden de Origen").'</td>';
	if ($action != 'editdescription' && $factory->fk_statut == 0) print '<td align="right"></td>';
	print '</tr></table></td><td colspan="3">';
		print "<a href='fiche.php?id=".$mqs->fk_order_origen."'><strong>".$mqs->ref."</strong></a>";
	print '</td></tr>';
}
$sqm="SELECT fk_order_clone,ref
	FROM ".MAIN_DB_PREFIX."factory_object_object a, ".MAIN_DB_PREFIX."factory b
	WHERE fk_order_origen=".$id." AND fk_order_clone=b.rowid";
$mrs=$db->query($sqm);
$rmq=$db->num_rows($mrs);
if($rmq>0){
	
	print '<tr><td valign=top><table class="nobordernopadding" width="100%"><tr><td valign=top>'.$langs->trans("Subordenes relacionadas").'</td>';
	if ($action != 'editdescription' && $factory->fk_statut == 0) print '<td align="right"></td>';
	print '</tr></table></td><td colspan="3">';
	while($mqs=$db->fetch_object($mrs)){
		print "<a href='fiche.php?id=".$mqs->fk_order_clone."'><strong>".$mqs->ref."</strong></a><br>";
	}
	print '</td></tr>';
}
print '</table>';
print '<br>';

// tableau de description du produit
print '<table width=100% ><tr><td valign=top width=35%>';
print_fiche_titre($langs->trans("ProducttoBuild"),'','');

print '<table class="border" width="100%">';

//$bproduit = ($object->isproduct()); 
print '<tr><td >'.$langs->trans("Product").'</td><td>'.$product->getNomUrl(1)." : ".$product->label.'</td></tr>';

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

// indique si on a déjà une composition de présente ou pas
$compositionpresente=0;

$prods_arbo =$factory->getChildsOF($id); 

// on travaille avec les valeurs conservées
if (false)
{	
	$factory->id =$product->id;
	$factory->get_sousproduits_arbo();
	// Number of subproducts
	$prods_arbo = $factory->get_arbo_each_prod();
	// somthing wrong in recurs, change id of object
	$factory->id = $product->id;
}
print_fiche_titre($langs->trans("FactorisedProductsNumber").' : '.count($prods_arbo),'','');

// List of subproducts
if (count($prods_arbo) > 0)
{
	$compositionpresente=1;
	print '<table class="border" >';
	print '<tr class="liste_titre">';
	print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
	print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyNeedOF").'</td>';
	// on affiche la colonne stock même si cette fonction n'est pas active
	print '<td class="liste_titre" width=50px align="center">'.$langs->trans("Stock").'</td>'; 
	if ($user->rights->factory->showprice )
	{
		if ($conf->stock->enabled)
		{ 	// we display vwap titles
			print '<td class="liste_titre" width=100px align="center">'.$langs->trans("UnitPmp").'</td>';
			print '<td class="liste_titre" width=100px align="center">'.$langs->trans("CostPmpHT").'</td>';
		}
		else
		{ 	// we display price as latest purchasing unit price title
			print '<td class="liste_titre" width=100px align="center">'.$langs->trans("UnitHA").'</td>';
			print '<td class="liste_titre" width=100px align="center">'.$langs->trans("CostHA").'</td>';
		}
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitProfitAmount").'</td>';
		print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
	}

	print '</tr>';
	$mntTot=0;
	$pmpTot=0;
	$btopChild = false;
	foreach($prods_arbo as $value)
	{
		// verify if product have child then display it after the product name
		$tmpChildArbo=$factory->getChildsArbo($value['id']);
		$nbChildArbo="";
		if (count($tmpChildArbo) > 0) 
		{
			$nbChildArbo=" (".count($tmpChildArbo).")";
			$btopChild = true;
		}	

		print '<tr>';
		print '<td align="left">'.$factory->getNomUrlFactory($value['id'], 1,'fiche').$nbChildArbo;
		print $factory->PopupProduct($value['id']);
		print '</td>';
		print '<td align="left" title="'.$value['description'].'">';
		print $value['label'].'</td>';
		print '<td align="center">'.$value['qtyplanned'];
		if ($value['globalqty'] == 1)
				print "&nbsp;G";
		print '</td>';

		if ($conf->stock->enabled)
		{	
			if ($value['fk_product_type']==0)
			{ 	// if product
				$product->fetch($value['id']);
				$product->load_stock();
				print '<td align=center>'.$factory->getUrlStock($value['id'], 1, $product->stock_reel).'</td>';
			}
			else // no stock management for services
				print '<td></td>';
		}
		if ($user->rights->factory->showprice )
		{
			print '<td align="right">'.price($value['pmp'],0,'',1,2,2).'</td>'; // display else vwap or else latest purchasing price
			print '<td align="right">'.price($value['pmp']*$value['nb']*($value['globalqty']==1? 1 :$factory->qty_planned) ,0,'',1,2,2).'</td>'; // display total line
			print '<td align="right">'.price($value['price'],0,'',1,2,2).'</td>';
			print '<td align="right">'.price($value['price']*$value['nb']*($value['globalqty']==1? 1 :$factory->qty_planned),0,'',1,2,2).'</td>';
			print '<td align="right">'.price(($value['price']-$value['pmp'])*$value['nb'],0,'',1,2,2).'</td>'; 
			print '<td align="right">'.price(($value['price']-$value['pmp'])*$value['nb']*($value['globalqty']==1? 1 :$factory->qty_planned),0,'',1,2,2).'</td>'; 
			$mntTot=$mntTot+$value['price']*$value['nb']*($value['globalqty']==1? 1 :$factory->qty_planned);
			$pmpTot=$pmpTot+$value['pmp']*$value['nb']*($value['globalqty']==1? 1 :$factory->qty_planned); // sub total calculation
		}			
		print '</tr>';

	}
	if ($user->rights->factory->showprice )
	{
		print '<tr class="liste_total">';
		print '<td colspan=4 align=right >'.$langs->trans("Total").'</b></td>';
		print '<td align="right" ><b>'.price($pmpTot/($factory->qty_planned),0,'',1,2,2).'</b></td>';
		print '<td align="right" ><b>'.price($pmpTot,0,'',1,2,2).'</b></td>';
		print '<td align="right" ><b>'.price($mntTot/($factory->qty_planned),0,'',1,2,2).'</b></td>';
		print '<td align="right" ><b>'.price($mntTot,0,'',1,2,2).'</b></td>';
		print '<td align="right" ><b>'.price(($mntTot-$pmpTot)/($factory->qty_planned),0,'',1,2,2).'</b></td>';
		print '<td align="right" ><b>'.price(($mntTot-$pmpTot),0,'',1,2,2).'</b></td>';
		print '</tr>';
	}
	print '</table>';
}
if ($btopChild)	print '<b>'.$langs->trans("FactoryTableInfo").'</b><BR>';
print '</td>';
print '</tr></table>';


/* Gestion de la composition à chaud */
if ($action == 'search')
{
	
	$sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.price, p.fk_product_type as type,  p.pmp';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
	$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON p.rowid = cp.fk_product';
	$sql.= ' WHERE p.entity IN ('.getEntity("product", 1).')';
	$sql.= " AND p.rowid <> ".$factory->fk_product;		 // pour ne pas afficher le produit lui-même
	if ($key != "")
	{
		$sql.= " AND (p.ref LIKE '%".$key."%'";
		$sql.= " OR p.label LIKE '%".$key."%')";
	}
	if ($conf->categorie->enabled && $parent != -1 and $parent)
	{
		$sql.= " AND cp.fk_categorie ='".$db->escape($parent)."'";
	}
	$sql.= " ORDER BY p.ref ASC";
//print $sql;
	$resql = $db->query($sql);
	
	$productstatic = new Product($db);
	$form = new Form($db);
}
	$rowspan=1;
	if ($conf->categorie->enabled) $rowspan++;
	if ($action == 'edit' || $action == 'search' || $action == 're-edit' )
	{
		print '<br>';
		print_fiche_titre($langs->trans("ProductToAddSearch"),'','');
		print '<form action="'.DOL_URL_ROOT.'/factory/fiche.php?id='.$id.'" method="post">';
		print '<table class="border" width="50%"><tr><td>';
		print '<table class="nobordernopadding" width="100%">';

		print '<tr><td>';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print $langs->trans("KeywordFilter").' &nbsp; ';
		print '</td>';
		print '<td><input type="text" name="key" value="'.$key.'">';
		print '<input type="hidden" name="action" value="search">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '</td>';
		print '<td rowspan="'.$rowspan.'"  valign="bottom">';
		print '<input type="submit" class="button" value="'.$langs->trans("Search").'">';
		print '</td></tr>';
		if ($conf->categorie->enabled)
		{
			print '<tr><td>'.$langs->trans("CategoryFilter").' &nbsp; </td>';
			print '<td>'.$form->select_all_categories(0,$parent).'</td></tr>';
		}

		print '</table>';
		print '</td></tr></table>';
		print '</form>';

		if ($action == 'search')
		{
			print '<br>';
			print '<form action="'.DOL_URL_ROOT.'/factory/fiche.php?id='.$id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="add_prod">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<table class="nobordernopadding" width="100%">';
			print '<tr class="liste_titre">';
			print '<th class="liste_titre">'.$langs->trans("Ref").'</th>';
			print '<th class="liste_titre">'.$langs->trans("Label").'</th>';
			print '<th class="liste_titre" align="right">'.$langs->trans("BuyPrice").'</th>';
			print '<th class="liste_titre" align="right">'.$langs->trans("SellPrice").'</th>';
			print '<th class="liste_titre" align="center">'.$langs->trans("AddDel").'</th>';
			print '<th class="liste_titre" align="right">'.$langs->trans("Quantity").'</th>';
			print '<th class="liste_titre" align="right">'.$langs->trans("Global").'</th>';
			print '</tr>';
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i=0;
				$var=true;

				if($num == 0) print '<tr><td colspan="4">'.$langs->trans("NoMatchFound").'</td></tr>';

				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);

					$var=!$var;
					print "\n<tr ".$bc[$var].">";
					$productstatic->id=$objp->rowid;
					$productstatic->ref=$objp->ref;
					$productstatic->libelle=$objp->label;
					$productstatic->type=$objp->type;

					print '<td>'.$factory->getNomUrlFactory($objp->rowid, 1,'index',24);
					print $factory->PopupProduct($objp->rowid,$i);
					print '</td>';
					$labeltoshow=$objp->label;
					//if ($conf->global->MAIN_MULTILANGS && $objp->labelm) $labeltoshow=$objp->labelm;

					print '<td>';
					print "<a href=# onclick=\"$('.detailligne".$i."').toggle();\" >".img_picto("","edit_add")."</a>&nbsp;";
					print $labeltoshow.'</td>';
					if($factory->is_sousproduitOF($id, $objp->rowid))
					{
						$addchecked = ' checked="checked"';
						$qty=$factory->is_sousproduit_qty;
						if ($factory->is_sousproduit_qtyglobal)
							$globalchecked = ' checked="checked"';
						$descComposant=$factory->is_sousproduit_description;
					}
					else
					{
						$addchecked = '';
						$globalchecked = '';
						$descComposant = '';
						$qty="1";
					}
					print '<td align="right">'.price($objp->pmp).'</td>';
					print '<td align="right">'.price($objp->price).'</td>';

					//print '<td align="left"><input type="text" size="5" name="prod_pmp_'.$i.'" value="'.price2num($objp->pmp).'">';
					//print '<td align="left"><input type="text" size="5" name="prod_price_'.$i.'" value="'.price2num($objp->price).'">';
					print '<td align="center"><input type="hidden" name="prod_id_'.$i.'" value="'.$objp->rowid.'">';
					print '<input type="checkbox" '.$addchecked.'name="prod_id_chk'.$i.'" value="'.$objp->rowid.'"></td>';
					print '<td align="right"><input type="text" size="3" name="prod_qty_'.$i.'" value="'.$qty.'">';
					print '<input type="checkbox" '.$globalchecked.'name="prod_id_globalchk'.$i.'" value="1">';
					print '</td></tr>';
					if ($bc[$var]=='class="pair"')
						print "<tr style='display:none' class='pair detailligne".$i."'>";
					else
						print "<tr style='display:none' class='impair detailligne".$i."'>";
					print '<td></td><td colspan=5>';
					print '<textarea name="descComposant'.$i.'" wrap="soft" cols="80" rows="'.ROWS_2.'">'.$descComposant.'</textarea>';
					print '</td>';
					print '</tr>';
					$i++;
				}
			}
			else
			{
				dol_print_error($db);
			}
			print '</table>';
			print '<input type="hidden" name="max_prod" value="'.$i.'">';

			if($num > 0)
			{
				print '<br><center><input type="submit" class="button" value="'.$langs->trans("Add").'/'.$langs->trans("Update").'">';
				print ' &nbsp; &nbsp; <input type="submit" name="cancel" class="button" value="'.$langs->trans("Cancel").'">';
				print '</center>';
			}
			print '</form>';
		}
	}



/* Barre d'action				*/


if ($action == '' )
{
	print '<div class="tabsAction">';
	if ($user->rights->factory->creer && $factory->fk_statut==0)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/fiche.php?action=clonar&id='.$id.'">'.$langs->trans("Generar Suborden").'</a>';
	//print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/fiche.php?action=validate&id='.$id.'">'.$langs->trans("LaunchOF").'</a>';
	if ($user->rights->factory->creer && $factory->fk_statut==0)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/fiche.php?action=cancelof&id='.$id.'">'.$langs->trans("CancelFactory").'</a>';

	if ($user->rights->factory->creer && $factory->fk_statut==0)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/factory/fiche.php?action=edit&id='.$id.'">'.$langs->trans("ChangeGlobalQtyFactory").'</a>';
	
	if ($user->rights->factory->send)
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&amp;action=presend&amp;mode=init">'.$langs->trans('SendByMail').'</a></div>';
	}
	else print '<div class="inline-block divButAction"><a class="butActionRefused" href="#">'.$langs->trans('SendByMail').'</a></div>';
	
	print '</div>';

	print '<div class="fichecenter"><div class="fichehalfleft">';
	print '<br><br>';
	/*
	 * Documents generes
	*/
	$comref = dol_sanitizeFileName($factory->ref);
	$file = $conf->factory->dir_output . '/' . $comref . '/' . $comref . '.pdf';
	$relativepath = $comref.'/'.$comref.'.pdf';
	$filedir = $conf->factory->dir_output . '/' . $comref;
	$urlsource=$_SERVER["PHP_SELF"]."?id=".$factory->id;
	$genallowed=$user->rights->factory->creer;
	$delallowed=$user->rights->factory->delete;
	$somethingshown=$formfile->show_documents('factory',$comref,$filedir,$urlsource,$genallowed,$delallowed,$factory->modelpdf,1,0,0,28,0,'','','','');

	/*
	 * Linked object block
	*/
	$somethingshown=$factory->showLinkedObjectBlock();

	print '</div><div class="fichehalfright"><div class="ficheaddleft">';

	// List of actions on element
	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
	$formactions=new FormActions($db);
	$somethingshown=$formactions->showactions($factory,'factory',$socid);


	print '</div></div></div>';		
}

if($action=='addprod'){
	$sqlv="SELECT fk_order_origen FROM ".MAIN_DB_PREFIX."factory_object_object
			WHERE fk_order_clone=".$id;
	$rq=$db->query($sqlv);
	$rs=$db->fetch_object($rq);
	$sqlv="SELECT fk_product,a.qty_unit,a.qty_planned,a.pmp,a.price,a.description,a.globalqty,ref,label
		FROM ".MAIN_DB_PREFIX."factorydet a,".MAIN_DB_PREFIX."product b
		WHERE fk_factory=".$rs->fk_order_origen." AND fk_product=b.rowid";
	$rq1=$db->query($sqlv);
	//$factory->add_componentOF($id, $fk_product, $_POST["prod_qty_".$i], 0, 0, $_POST["prod_id_globalchk".$i], $_POST["descComposant".$i]) 
	print "<form method='POST' action='fiche.php?id=".$id."&action=addclprod'>";
	print "<table class='noborder' width='100%'>";
		print "<tr class='liste_titre'>";
			print "<td colspan='5'>";
			print "Agregar productos";
			print "</td>";
		print "</tr>";
		print "<tr class='liste_titre'>";
			print "<td>";
				print " ";
			print "</td>";
			print "<td>";
				print "Ref.";
			print "</td>";
			print "<td>";
				print "Etiqueta.";
			print "</td>";
			print "<td>";
				print "Cant.";
			print "</td>";
			print "<td>";
				print "Cant necesaria";
			print "</td>";
		print "</tr>";
		
		while($rs1=$db->fetch_object($rq1)){
			$sqlb="SELECT sum(qty_planned) as tot
					FROM ".MAIN_DB_PREFIX."factory_object_object, ".MAIN_DB_PREFIX."factorydet
					WHERE fk_order_origen=".$rs->fk_order_origen." AND fk_order_clone=fk_factory AND fk_product=".$rs1->fk_product;
			//print $sqlb;
			$rv=$db->query($sqlb);
			$nmr=$db->num_rows($rv);
			$mm=1;
			if($nmr==0){
				$mm=1;
			}else{
				$vrs=$db->fetch_object($rv);
				if($vrs->tot>=$rs1->qty_planned){
					$mm=2;
				}else{
					$mm=1;
				}
			}
			if($mm==1){
			print "<tr>";
				print "<td>";
					print "<input type='checkbox' name='prod-".$rs1->fk_product."' value='1' checked>";
				print "</td>";
				print "<td>";
					print $rs1->ref;
				print "</td>";
				print "<td>";
					print $rs1->label;
				print "</td>";
				print "<td>";
					print $rs1->qty_unit;
				print "</td>";
				print "<td>";
					print "<input type='text' name='cantp-".$rs1->fk_product."' value='".$rs1->qty_planned."'>";
				print "</td>";
			print "</tr>";
			}
		}
		print "<tr>";
		print "<td colspan='5' align='center'>";
		print "<input type='submit' value='Agregar'>";
		print "</td>";
		print "</tr>";
	print "</table>";
	print "</form>";
}

/*
 * Add file in email form
*/
if (GETPOST('addfile'))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory TODO Use a dedicated directory for temp mails files
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	dol_add_file_process($upload_dir_tmp,0,0);
	$action ='presend';
}

/*
 * Remove file in email form
*/
if (GETPOST('removedfile'))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	// TODO Delete only files that was uploaded from email form
	dol_remove_file_process(GETPOST('removedfile'),0);
	$action ='presend';
}

/*
 * Send mail
*/
if ($action == 'send' && ! GETPOST('addfile') && ! GETPOST('removedfile') && ! GETPOST('cancel'))
{
	$langs->load('mails');

	if ($id > 0)
	{
		$ref = dol_sanitizeFileName($object->ref);
		$file = $conf->factory->dir_output . '/' . $ref . '/' . $ref . '.pdf';
		
		if (is_readable($file))
		{
			if (GETPOST('sendto'))
			{
				// Le destinataire a ete fourni via le champ libre
				$sendto = GETPOST('sendto');
				$sendtoid = 0;
			}
			elseif (GETPOST('receiver') != '-1')
			{
				// Recipient was provided from combo list
				if (GETPOST('receiver') == 'thirdparty') // Id of third party
				{
					$sendto = $factory->client->email;
					$sendtoid = 0;
				}
				else	// Id du contact
				{
					//$sendto = $factory->client->contact_get_property(GETPOST('receiver'),'email');
					$sendtoid = GETPOST('receiver');
				}
			}

			if (dol_strlen($sendto))
			{
				$langs->load("commercial");
	
				$from = GETPOST('fromname') . ' <' . GETPOST('frommail') .'>';
				$replyto = GETPOST('replytoname'). ' <' . GETPOST('replytomail').'>';
				$message = GETPOST('message');
				$sendtocc = GETPOST('sendtocc');
				$deliveryreceipt = GETPOST('deliveryreceipt');
	
				if ($action == 'send')
				{
					if (dol_strlen(GETPOST('subject'))) $subject=GETPOST('subject');
					else $subject = $langs->transnoentities('Order').' '.$factory->ref;
					$actiontypecode='AC_COM';
					$actionmsg = $langs->transnoentities('MailSentBy').' '.$from.' '.$langs->transnoentities('To').' '.$sendto.".\n";
					if ($message)
					{
						$actionmsg.=$langs->transnoentities('MailTopic').": ".$subject."\n";
						$actionmsg.=$langs->transnoentities('TextUsedInTheMessageBody').":\n";
						$actionmsg.=$message;
					}
					$actionmsg2=$langs->transnoentities('Action'.$actiontypecode);
				}
	
				// Create form object
				include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
				$formmail = new FormMail($db);
	
				$attachedfiles=$formmail->get_attached_files();
				$filepath = $attachedfiles['paths'];
				$filename = $attachedfiles['names'];
				$mimetype = $attachedfiles['mimes'];
	
				// Send mail
				require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
				$mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,'',$deliveryreceipt,-1);
				if ($mailfile->error)
				{
					$mesg='<div class="error">'.$mailfile->error.'</div>';
				}
				else
				{
					$result=$mailfile->sendfile();
					if ($result)
					{
						$mesg=$langs->trans('MailSuccessfulySent',$mailfile->getValidAddress($from,2),$mailfile->getValidAddress($sendto,2));	// Must not contains "
	
						$error=0;
	
						// Initialisation donnees
						$object->sendtoid		= $sendtoid;
						$object->actiontypecode	= $actiontypecode;
						$object->actionmsg		= $actionmsg;
						$object->actionmsg2		= $actionmsg2;
						$object->fk_element		= $factory->id;
						$object->elementtype	= $factory->element;
	
						// Appel des triggers
						include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
						$interface=new Interfaces($db);
						$result=$interface->run_triggers('FACTORY_SENTBYMAIL',$object,$user,$langs,$conf);
						if ($result < 0) {
							$error++; $this->errors=$interface->errors;
						}
						// Fin appel triggers
	
						if ($error)
						{
							dol_print_error($db);
						}
						else
						{
							// Redirect here
							// This avoid sending mail twice if going out and then back to page
							header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&mesg='.urlencode($mesg));
							exit;
						}
					}
					else
					{
						$langs->load("other");
						$mesg='<div class="error">';
						if ($mailfile->error)
						{
							$mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
							$mesg.='<br>'.$mailfile->error;
						}
						else
						{
							$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
						}
						$mesg.='</div>';
					}
				}
			}
			 else
			{
			$langs->load("other");
			$mesg='<div class="error">'.$langs->trans('ErrorMailRecipientIsEmpty').' !</div>';
			$action='presend';
			dol_syslog('Recipient email is empty');
			}
		}
		else
		{
			$langs->load("errors");
			$mesg='<div class="error">'.$langs->trans('ErrorCantReadFile',$file).'</div>';
			dol_syslog('Failed to read file: '.$file);
		}
	}
	else
	{
		$langs->load("other");
		$mesg='<div class="error">'.$langs->trans('ErrorFailedToReadEntity',$langs->trans("Order")).'</div>';
		dol_syslog($langs->trans('ErrorFailedToReadEntity', $langs->trans("Order")));
	}
	
	$action="";
}

/*
 * Action presend
*
*/
if ($action == 'presend')
{
	$ref = dol_sanitizeFileName($factory->ref);
	include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$fileparams = dol_most_recent_file($conf->factory->dir_output . '/' . $ref , preg_quote($ref,'/'));
	$file=$fileparams['fullname'];

	// Build document if it not exists
	if (! $file || ! is_readable($file))
	{
		// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
		//if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		
		$result=factory_create($db, $factory, $factory->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		//$result=factory_create($db, $object, GETPOST('model')?GETPOST('model'):$object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		$fileparams = dol_most_recent_file($conf->factory->dir_output . '/' . $ref, preg_quote($ref,'/'));
		$file=$fileparams['fullname'];
	}

	print '<br>';
	print_titre($langs->trans('SendFactoryByMail'));

	// Cree l'objet formulaire mail
	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
	$formmail = new FormMail($db);
	$formmail->fromtype = 'user';
	$formmail->fromid   = $user->id;
	$formmail->fromname = $user->getFullName($langs);
	$formmail->frommail = $user->email;
	$formmail->withfrom=1;
	$liste=array();
	foreach ($factory->contact_entrepot_email_array(1) as $key=>$value)	$liste[$key]=$value;
	$formmail->withto=GETPOST('sendto')?GETPOST('sendto'):$liste;
	$formmail->withtocc=$liste;
	$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
	$formmail->withtopic=$langs->trans('SendFactoryRef','__FACTORYREF__');
	$formmail->withfile=2;
	$formmail->withbody=1;
	$formmail->withdeliveryreceipt=1;
	$formmail->withcancel=1;
	// Tableau des substitutions
	$formmail->substit['__FACTORYREF__']=$factory->ref;
	$formmail->substit['__SIGNATURE__']=$user->signature;
	//$formmail->substit['__REFCLIENT__']=$object->ref_client;
	$formmail->substit['__PERSONALIZED__']='';
	//$formmail->substit['__CONTACTCIVNAME__']='';

	$custcontact='';
	$contactarr=array();
	$entrepotStatic=new Entrepot($db);
	$entrepotStatic->fetch($factory->fk_entrepot);
	$entrepotStatic->element='stock'; // bug dolibarr corrigé dans les prochaines versions
	$contactarr=$entrepotStatic->liste_contact(-1,'external');
	if (is_array($contactarr) && count($contactarr)>0) {
		foreach($contactarr as $contact) {
			if ($contact['libelle']==$langs->trans('TypeContact_entrepot_external')) {
				$contactstatic=new Contact($db);
				$contactstatic->fetch($contact['id']);
				$custcontact=$contactstatic->getFullName($langs,1);
			}
		}

		if (!empty($custcontact)) {
			$formmail->substit['__CONTACTCIVNAME__']=$custcontact;
		}
	}

	// Tableau des parametres complementaires
	$formmail->param['action']='send';
	$formmail->param['models']='factory_send';
	$formmail->param['orderid']=$id;
	$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$id;

	// Init list of files
	if (GETPOST("mode")=='init')
	{
		$formmail->clear_attached_files();
		$formmail->add_attached_files($file,basename($file),dol_mimetype($file));
	}

	// Show form
	$formmail->show_form();

	print '<br>';
}

llxFooter();
$db->close();

print '<script>$(function(){';
print '$(".tiptipimg").tipTip({maxWidth: "auto", edgeOffset: 10});';
print '});</script>';

?>