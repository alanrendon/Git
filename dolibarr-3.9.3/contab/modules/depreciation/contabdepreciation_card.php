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
 *   	\file       contab/contabdepreciation_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-17 16:31
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
include_once('../../class/contabdepreciation.class.php');


// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_clave=GETPOST('search_clave','alpha');
$search_entity=GETPOST('search_entity','int');
$search_descripcion=GETPOST('search_descripcion','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_lifetime=GETPOST('search_lifetime','int');
$search_market_value=GETPOST('search_market_value','alpha');
$search_type_active=GETPOST('search_type_active','alpha');
$search_localitation=GETPOST('search_localitation','alpha');
$search_department=GETPOST('search_department','alpha');
$search_serial_number=GETPOST('search_serial_number','int');
$search_depreciation_rate=GETPOST('search_depreciation_rate','alpha');
$search_depreciation_accumulated=GETPOST('search_depreciation_accumulated','alpha');



// Protection if external user
if ($user->rights->contab->ndepres!=1)
{
	accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter

$object=new Contabdepreciation($db);


if (($id > 0 || ! empty($ref)) && $action != 'add')
{

	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabdepreciation'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/modules/depreciation/contabdepreciation_list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		if (!empty($_POST["date_purchase"])) {
			$object->date_purchase=dol_mktime(12, 0 , 0, $_POST['date_purchasemonth'], $_POST['date_purchaseday'], $_POST['date_purchaseyear']);
		}else{
			$object->date_purchase="";
		}

		if (!empty($_POST["date_init_purchase"])) {
			$object->date_init_purchase=dol_mktime(12, 0 , 0, $_POST['date_init_purchasemonth'], $_POST['date_init_purchaseday'], $_POST['date_init_purchaseyear']);
		}else{
			$object->date_init_purchase="";
		}
		

		

		$object->clave=GETPOST('clave','alpha');
		$object->entity=GETPOST('entity','int');
		$object->descripcion=GETPOST('descripcion','alpha');
		$object->amount=GETPOST('amount','alpha');
		$object->lifetime=GETPOST('lifetime','int');
		$object->market_value=GETPOST('market_value','alpha');
		$object->type_active=GETPOST('type_active','alpha');
		$object->localitation=GETPOST('localitation','alpha');
		$object->department=GETPOST('department','alpha');
		$object->serial_number=GETPOST('serial_number','int');
		$object->depreciation_rate=GETPOST('depreciation_rate','alpha');
		$object->depreciation_accumulated=GETPOST('depreciation_accumulated','alpha');

		

		if (empty($object->clave))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Clave")), null, 'errors');
		}
		if (empty($object->serial_number))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("No serie")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages("Depreciación creada: ".$object->clave, null);
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/modules/depreciation/contabdepreciation_list.php',1);
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

		
		if (!empty($_POST["date_purchase"])) {
			$object->date_purchase=dol_mktime(12, 0 , 0, $_POST['date_purchasemonth'], $_POST['date_purchaseday'], $_POST['date_purchaseyear']);
		}else{
			$object->date_purchase="";
		}

		if (!empty($_POST["date_init_purchase"])) {
			$object->date_init_purchase=dol_mktime(12, 0 , 0, $_POST['date_init_purchasemonth'], $_POST['date_init_purchaseday'], $_POST['date_init_purchaseyear']);
		}else{
			$object->date_init_purchase="";
		}
		

		

		$object->clave=GETPOST('clave','alpha');
		$object->entity=GETPOST('entity','int');
		$object->descripcion=GETPOST('descripcion','alpha');
		$object->amount=GETPOST('amount','alpha');
		$object->lifetime=GETPOST('lifetime','int');
		$object->market_value=GETPOST('market_value','alpha');
		$object->type_active=GETPOST('type_active','alpha');
		$object->localitation=GETPOST('localitation','alpha');
		$object->department=GETPOST('department','alpha');
		$object->serial_number=GETPOST('serial_number','int');
		$object->depreciation_rate=GETPOST('depreciation_rate','alpha');
		$object->depreciation_accumulated=GETPOST('depreciation_accumulated','alpha');

		

		if (empty($object->clave))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Clave")), null, 'errors');
		}

		if (empty($object->serial_number))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("No serie")), null, 'errors');
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
			header("Location: ".dol_buildpath('/contab/modules/depreciation/contabdepreciation_list.php',1));
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

llxHeader('','Depreciación','');

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
	print load_fiche_titre($langs->trans("Nueva Depreciación"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="entity" value="'.$conf->entity.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr>
		<td class="fieldrequired" style="width:465px;">'.$langs->trans("Clave").'</td>
		<td>
			<input class="flat" type="text" name="clave" value="'.GETPOST('clave').'">
		</td>
	</tr>';
	print '<tr>
		<td class="fieldrequired">'.$langs->trans("No serie").'</td>
		<td>
			<input class="flat" type="text" name="serial_number" value="'.GETPOST('serial_number').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Fecha de adquisición").'</td>
		<td>';
			print $form->select_date(
		        (empty($object->date_purchase)?GETPOST('date_purchase'):$object->date_purchase), 'date_purchase', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
		    );
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Monto original").'</td>
		<td>
			<input class="flat" type="text" name="amount" value="'.GETPOST('amount').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("% Vida útil").'</td>
		<td>
			<input class="flat" type="text" name="lifetime" value="'.GETPOST('lifetime').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Valor de mercado").'</td>
		<td>
			<input class="flat" type="text" name="market_value" value="'.GETPOST('market_value').'">
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Localización").'</td>
		<td>
			<input class="flat" type="text" name="localitation" value="'.GETPOST('localitation').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Departamento").'</td>
		<td>
			<input class="flat" type="text" name="department" value="'.GETPOST('department').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Tipo de activo").'</td>
		<td>';
		print $object->select_dol_active(GETPOST('type_active'),"type_active");
	print ' 
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Descripción").'</td>
		<td>
			<textarea class="flat"  name="descripcion" style="margin: 0px; width: 383px; height: 82px;">'.GETPOST('descripcion').'</textarea>
		</td>
	</tr>';

	print '</table><br><br>';
	print '<table class="border centpercent">'."\n";
	
	print '<tr>
		<td style="width:465px;">'.$langs->trans("Fecha inicio depreciación").'</td>
		<td>';
			print $form->select_date(
				(empty($object->date_init_purchase)?GETPOST('date_init_purchase'):$object->date_init_purchase), 'date_init_purchase', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
		    );
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Tasa de depreciación").'</td>
		<td>
			<input class="flat" type="text" name="depreciation_rate" value="'.GETPOST('depreciation_rate').'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Depreciación acumulada último cierre").'</td>
		<td>
			<input class="flat" type="text" name="depreciation_accumulated" value="'.GETPOST('depreciation_accumulated').'">
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
	print load_fiche_titre($langs->trans("Modificar Depreciación"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="entity" value="'.$object->entity.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr>
		<td class="fieldrequired" style="width:465px;">'.$langs->trans("Clave").'</td>
		<td>
			<input class="flat" type="text" name="clave" value="'.$object->clave.'">
		</td>
	</tr>';
	print '<tr>
		<td class="fieldrequired">'.$langs->trans("No serie").'</td>
		<td>
			<input class="flat" type="text" name="serial_number" value="'.$object->serial_number.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Fecha de adquisición").'</td>
		<td>';
			print $form->select_date(
		        (empty($object->date_purchase)?$object->date_purchase:$object->date_purchase), 'date_purchase', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
		    );
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Monto original").'</td>
		<td>
			<input class="flat" type="text" name="amount" value="'.$object->amount.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("% Vida útil").'</td>
		<td>
			<input class="flat" type="text" name="lifetime" value="'.$object->lifetime.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Valor de mercado").'</td>
		<td>
			<input class="flat" type="text" name="market_value" value="'.$object->market_value.'">
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Localización").'</td>
		<td>
			<input class="flat" type="text" name="localitation" value="'.$object->localitation.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Departamento").'</td>
		<td>
			<input class="flat" type="text" name="department" value="'.$object->department.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Tipo de activo").'</td>
		<td>';
		print $object->select_dol_active($object->type_active,"type_active");
	print ' 
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Descripción").'</td>
		<td>
			<textarea class="flat"  name="descripcion" style="margin: 0px; width: 383px; height: 82px;">'.$object->descripcion.'</textarea>
		</td>
	</tr>';

	print '</table><br><br>';
	print '<table class="border centpercent">'."\n";
	
	print '<tr>
		<td style="width:465px;">'.$langs->trans("Fecha inicio depreciación").'</td>
		<td>';
			print $form->select_date(
				$object->date_init_purchase, 'date_init_purchase', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
		    );
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Tasa de depreciación").'</td>
		<td>
			<input class="flat" type="text" name="depreciation_rate" value="'.$object->depreciation_rate.'">
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Depreciación acumulada último cierre").'</td>
		<td>
			<input class="flat" type="text" name="depreciation_accumulated" value="'.$object->depreciation_accumulated.'">
		</td>
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
	print load_fiche_titre($langs->trans("Depreciación"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
	<tr>
		<td class="fieldrequired" style="width:465px;">'.$langs->trans("Clave").'</td>
		<td>
			'.$object->getNomUrl().'
		</td>
	</tr>';
	print '<tr>
		<td class="fieldrequired">'.$langs->trans("No serie").'</td>
		<td>
			'.$object->serial_number.'
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Fecha de adquisición").'</td>
		<td>';
			print dol_print_date($object->date_purchase,"%d/%m/%Y");
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Monto original").'</td>
		<td>
			'.$object->amount.'
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("% Vida útil").'</td>
		<td>
			'.$object->lifetime.' %
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Valor de mercado").'</td>
		<td>
			'.$object->market_value.'
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Localización").'</td>
		<td>
			'.$object->localitation.'
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Departamento").'</td>
		<td>
			'.$object->department.'
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Tipo de activo").'</td>
		<td>';
		print $object->select_dol_active($object->type_active,"type_active",0,0,1);
	print ' 
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Descripción").'</td>
		<td>
			<textarea disabled class="flat"  name="descripcion" style="margin: 0px; width: 383px; height: 82px;">'.$object->descripcion.'</textarea>
		</td>
	</tr>';

	print '</table><br><br>';
	print '<table class="border centpercent">'."\n";
	
	print '<tr>
		<td style="width:465px;">'.$langs->trans("Fecha inicio depreciación").'</td>
		<td>';
			print dol_print_date($object->date_init_purchase,"%d/%m/%Y");
	print '
		</td>
	</tr>';

	print '<tr>
		<td>'.$langs->trans("Tasa de depreciación").'</td>
		<td>
			'.$object->depreciation_rate.'
		</td>
	</tr>';
	print '<tr>
		<td>'.$langs->trans("Depreciación acumulada último cierre").'</td>
		<td>
			'.$object->depreciation_accumulated.'
		</td>
	</tr>';
	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";

	print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";

	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
