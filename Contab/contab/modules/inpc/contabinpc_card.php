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
 *   	\file       contab/contabinpc_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-19 22:08
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
if (!$res && file_exists("../main.inc.php"))
	$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
	$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
	$res = @include '../../../main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../main.inc.php"))
	$res = @include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once('../../class/contabinpc.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_year=GETPOST('search_year','int');
$search_enero=GETPOST('search_enero','alpha');
$search_febrero=GETPOST('search_febrero','alpha');
$search_marzo=GETPOST('search_marzo','alpha');
$search_abril=GETPOST('search_abril','alpha');
$search_mayo=GETPOST('search_mayo','alpha');
$search_junio=GETPOST('search_junio','alpha');
$search_julio=GETPOST('search_julio','alpha');
$search_agosto=GETPOST('search_agosto','alpha');
$search_septiembre=GETPOST('search_septiembre','alpha');
$search_octubre=GETPOST('search_octubre','alpha');
$search_noviembre=GETPOST('search_noviembre','alpha');
$search_diciembre=GETPOST('search_diciembre','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Contabinpc($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabinpc'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/modules/inpc/contabinpc_list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
		$object->entity=GETPOST('entity','int');
		$object->year=GETPOST('year','int');
		$object->enero=GETPOST('enero','alpha');
		$object->febrero=GETPOST('febrero','alpha');
		$object->marzo=GETPOST('marzo','alpha');
		$object->abril=GETPOST('abril','alpha');
		$object->mayo=GETPOST('mayo','alpha');
		$object->junio=GETPOST('junio','alpha');
		$object->julio=GETPOST('julio','alpha');
		$object->agosto=GETPOST('agosto','alpha');
		$object->septiembre=GETPOST('septiembre','alpha');
		$object->octubre=GETPOST('octubre','alpha');
		$object->noviembre=GETPOST('noviembre','alpha');
		$object->diciembre=GETPOST('diciembre','alpha');

		

		if (empty($object->year))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Año")), null, 'errors');
		}

		if ($object->valid_year_inpc()==1) {
			$error++;
			setEventMessages("El año ingresado ya existe", null, 'errors');
		}


		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/modules/inpc/contabinpc_list.php',1);
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


		$object->year=GETPOST('year','int');
		$object->enero=GETPOST('enero','alpha');
		$object->febrero=GETPOST('febrero','alpha');
		$object->marzo=GETPOST('marzo','alpha');
		$object->abril=GETPOST('abril','alpha');
		$object->mayo=GETPOST('mayo','alpha');
		$object->junio=GETPOST('junio','alpha');
		$object->julio=GETPOST('julio','alpha');
		$object->agosto=GETPOST('agosto','alpha');
		$object->septiembre=GETPOST('septiembre','alpha');
		$object->octubre=GETPOST('octubre','alpha');
		$object->noviembre=GETPOST('noviembre','alpha');
		$object->diciembre=GETPOST('diciembre','alpha');

		

		if (empty($object->year))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Año")), null, 'errors');
		}

		if ($object->valid_year_inpc()==1) {
			$error++;
			setEventMessages("El año ingresado ya existe", null, 'errors');
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
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/contab/modules/inpc/contabinpc_list.php',1));
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

llxHeader('','INPC','');

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
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Crear nuevo Valor INPC"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="entity" value="'.$conf->entity.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("Año").'</td>
			<td><input class="flat" type="text" name="year" value="'.GETPOST('year').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Enero").'</td>
			<td><input class="flat" type="text" name="enero" value="'.GETPOST('enero').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Febrero").'</td>
			<td><input class="flat" type="text" name="febrero" value="'.GETPOST('febrero').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Marzo").'</td>
			<td><input class="flat" type="text" name="marzo" value="'.GETPOST('marzo').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Abril").'</td>
			<td><input class="flat" type="text" name="abril" value="'.GETPOST('abril').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Mayo").'</td>
			<td><input class="flat" type="text" name="mayo" value="'.GETPOST('mayo').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Junio").'</td>
			<td><input class="flat" type="text" name="junio" value="'.GETPOST('junio').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Julio").'</td>
			<td><input class="flat" type="text" name="julio" value="'.GETPOST('julio').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Agosto").'</td>
			<td><input class="flat" type="text" name="agosto" value="'.GETPOST('agosto').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Septiembre").'</td>
			<td><input class="flat" type="text" name="septiembre" value="'.GETPOST('septiembre').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Octubre").'</td>
			<td><input class="flat" type="text" name="octubre" value="'.GETPOST('octubre').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Noviembre").'</td>
			<td><input class="flat" type="text" name="noviembre" value="'.GETPOST('noviembre').'">
			</td>
		</tr>';
	print '
		<tr>
			<td>'.$langs->trans("Diciembre").'</td>
			<td><input class="flat" type="text" name="diciembre" value="'.GETPOST('diciembre').'">
			</td>
		</tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Editar valor INPC"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr>
				<td class="fieldrequired">'.$langs->trans("Año").'</td>
				<td><input class="flat" type="text" name="year" value="'.$object->year.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Enero").'</td>
				<td><input class="flat" type="text" name="enero" value="'.$object->enero.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Febrero").'</td>
				<td><input class="flat" type="text" name="febrero" value="'.$object->febrero.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Marzo").'</td>
				<td><input class="flat" type="text" name="marzo" value="'.$object->marzo.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Abril").'</td>
				<td><input class="flat" type="text" name="abril" value="'.$object->abril.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Mayo").'</td>
				<td><input class="flat" type="text" name="mayo" value="'.$object->mayo.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Junio").'</td>
				<td><input class="flat" type="text" name="junio" value="'.$object->junio.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Julio").'</td>
				<td><input class="flat" type="text" name="julio" value="'.$object->julio.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Agosto").'</td>
				<td><input class="flat" type="text" name="agosto" value="'.$object->agosto.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Septiembre").'</td>
				<td><input class="flat" type="text" name="septiembre" value="'.$object->septiembre.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Octubre").'</td>
				<td><input class="flat" type="text" name="octubre" value="'.$object->octubre.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Noviembre").'</td>
				<td><input class="flat" type="text" name="noviembre" value="'.$object->noviembre.'"></td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Diciembre").'</td>
				<td><input class="flat" type="text" name="diciembre" value="'.$object->diciembre.'"></td>
			</tr>';

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
	print load_fiche_titre($langs->trans("INPC"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr>
				<td class="fieldrequired">'.$langs->trans("Año").'</td>
				<td>'.$object->getNomUrl().'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Enero").'</td>
				<td>'.$object->enero.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Febrero").'</td>
				<td>'.$object->febrero.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Marzo").'</td>
				<td>'.$object->marzo.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Abril").'</td>
				<td>'.$object->abril.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Mayo").'</td>
				<td>'.$object->mayo.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Junio").'</td>
				<td>'.$object->junio.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Julio").'</td>
				<td>'.$object->julio.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Agosto").'</td>
				<td>'.$object->agosto.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Septiembre").'</td>
				<td>'.$object->septiembre.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Octubre").'</td>
				<td>'.$object->octubre.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Noviembre").'</td>
				<td>'.$object->noviembre.'</td>
			</tr>';
	print '<tr>
				<td>'.$langs->trans("Diciembre").'</td>
				<td>'.$object->diciembre.'</td>
			</tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{

			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";

			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();