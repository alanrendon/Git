<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       ctrlanticipo/ctrlbankcheck_card.php
 *		\ingroup    ctrlanticipo
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-10 23:11
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/ctrlanticipo/class/ctrlbankcheck.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');

dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
// Load traductions files requiredby by page
$langs->load("ctrlanticipo");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$cid		= GETPOST('cid','int');
$aid		= GETPOST('aid','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_paiment=GETPOST('search_fk_paiment','int');
$search_receptor=GETPOST('search_receptor','alpha');
$search_concept=GETPOST('search_concept','alpha');
$search_account_number=GETPOST('search_account_number','int');
$search_number_check=GETPOST('search_number_check','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_modify=GETPOST('search_fk_user_modify','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Ctrlbankcheck($db);
$pago = new PaiementAdvance($db);
$advance = new Ctrladvanceprovider($db);
$societe= new Societe($db);
if ($cid>0) {
	$advance->fetch($cid);
	$societe->fetch($advance->fk_soc);
}


if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id);

	$advance->fetch($object->fk_paiment);
	$societe->fetch($advance->fk_soc);
	
	if ($result < 0) dol_print_error($db);
}



// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('ctrlbankcheck'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$cid,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
		$object->fk_paiment=$cid;
		$object->date_asign=dol_mktime(0, 0, 0, GETPOST('date_asignmonth'), GETPOST('date_asignday'), GETPOST('date_asignyear'));
		$object->receptor=GETPOST('receptor','alpha');
		$object->concept=GETPOST('concept','alpha');
		$object->account_number=GETPOST('account_number','alpha');
		$object->number_check=GETPOST('number_check','alpha');
		$object->mode_print=GETPOST('mode_print');

		if (empty($object->receptor))
		{
			$error++;
			setEventMessages($langs->trans("ctrl_check_receptor_not_select"), null, 'errors');
		}
		if (empty($object->mode_print)  )
		{
			$error++;
			setEventMessages($langs->trans("ctrl_check_format_not_select"), null, 'errors');
		}
	

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages($langs->trans("ctrl_check_created_succ"), null);
				$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$cid,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
		$object->fk_paiment=GETPOST('fk_paiment','int');
		$object->receptor=GETPOST('receptor','alpha');
		$object->concept=GETPOST('concept','alpha');
		$object->account_number=GETPOST('account_number','int');
		$object->number_check=GETPOST('number_check','int');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_modify=GETPOST('fk_user_modify','int');
		$object->statut=GETPOST('statut','int');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$direct=$object->fk_paiment;
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages($langs->trans("ctrl_check_Deleted"), null);
			header("Location: ".dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$direct,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans("ctrl_check_format"),'');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create' && $cid>0)
{
	print load_fiche_titre($langs->trans("ctrl_check_format"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="cid" value="'.$cid.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	


	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_advance").'</td>
		<td>'.$advance->getNomUrl(1).'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_date_advance").'</td>
		<td>';
		print $form->select_date($object->date_asign, 'date_asign', 0, 0, 0, '', 1, 1, 1, 0, '', '', '');
	print '
		</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_check_receptor").'</td>
		<td><input class="flat" type="text" name="receptor" value="'.(empty($object->receptor)?$societe->name:$object->receptor).'"></td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_concept").'</td>
		<td>
			<textarea class="flat"  name="concept" rows="2" cols="80"> ' . (empty($object->concept)?$advance->concept_advance:$object->concept). '</textarea>
		</td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_account_number").'</td>
		<td><input class="flat" type="text" name="account_number" value="'.$object->account_number.'"></td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_number").'</td>
		<td><input class="flat" type="text" name="number_check" value="'.$object->number_check.'"></td>
	</tr>';

	print '
	<tr>
		<td class="fieldrequired" >'.$langs->trans("ctrl_check_print_formats").'</td>
		<td>
		<div style="height: 45px; margin: 10px; float: left; text-align:center;">
			'.img_picto("HSBC","hsbc.png@ctrlanticipo",'style="height:40px ;"').'<br>
			<input type="radio" name="mode_print" value="1">
		</div>
		<div style="height: 75px; margin: 10px; float: left; text-align:center;">
			'.img_picto("Santander","santander.png@ctrlanticipo",'style="height:40px ;"').'<br>
			<input type="radio" name="mode_print" value="2">
		</div>
		</td>
	</tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '
	<div class="center">
		<input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; 
		<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">
	</div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_paiment").'</td><td><input class="flat" type="text" name="fk_paiment" value="'.$object->fk_paiment.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldreceptor").'</td><td><input class="flat" type="text" name="receptor" value="'.$object->receptor.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconcept").'</td><td><input class="flat" type="text" name="concept" value="'.$object->concept.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccount_number").'</td><td><input class="flat" type="text" name="account_number" value="'.$object->account_number.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnumber_check").'</td><td><input class="flat" type="text" name="number_check" value="'.$object->number_check.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modify").'</td><td><input class="flat" type="text" name="fk_user_modify" value="'.$object->fk_user_modify.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("ctrl_check_format"));

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$object->id."&aid=".$pago->id, $langs->trans('ctrl_check_view_tit_delete'), $langs->trans('ctrl_check_ConfirmDelete'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="delete">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	

	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_ref").'</td>
		<td>'.$object->ref.'</td>
	</tr>';

	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_advance").'</td>
		<td>'.$advance->getNomUrl(1).'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_date_advance").'</td>
		<td>';
		print dol_print_date($object->date_asign,'%d/%m/%Y');
	print '
		</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_check_receptor").'</td>
		<td>'.($object->receptor).'</td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_concept").'</td>
		<td>
			<div style="word-wrap: break-word; width:140px; float: left; " >'.$object->concept. '</div>
		</td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_account_number").'</td>
		<td>'.$object->account_number.'</td>
	</tr>';
	print '
	<tr>
		<td >'.$langs->trans("ctrl_check_number").'</td>
		<td>'.$object->number_check.'</td>
	</tr>';

	print '
	<tr>
		<td class="fieldrequired" >'.$langs->trans("ctrl_check_print_formats").'</td>
		<td>';


		if ($object->mode_print==1) {
			print '
			<div style="height: 45px; margin: 10px; float: left; text-align:center;">
				'.img_picto("HSBC","hsbc.png@ctrlanticipo",'style="height:40px ;"').'<br>
			</div>';
		}else{
			print '
			<div style="height: 75px; margin: 10px; float: left; text-align:center;">
				'.img_picto("Santander","santander.png@ctrlanticipo",'style="height:40px ;"').'<br>
			</div>';
		}
		

		print '
		</td>
	</tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '
	<div class="center">

		<input type="submit" class="button" name="cancel" value="'.$langs->trans("ctrl_check_view_tit_delete").'">
	</div>';

	print '</form>';

}


// End of page
llxFooter();
$db->close();
