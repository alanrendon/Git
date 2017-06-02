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
 *   	\file       contab/contabsociete_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-28 00:33
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
dol_include_once('/contab/class/contabsociete.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_nom=GETPOST('search_nom','alpha');
$search_entity=GETPOST('search_entity','int');
$search_statut=GETPOST('search_statut','int');
$search_status=GETPOST('search_status','int');
$search_address=GETPOST('search_address','alpha');
$search_zip=GETPOST('search_zip','alpha');
$search_town=GETPOST('search_town','alpha');
$search_fk_departement=GETPOST('search_fk_departement','int');
$search_fk_pays=GETPOST('search_fk_pays','int');
$search_phone=GETPOST('search_phone','alpha');
$search_fax=GETPOST('search_fax','alpha');
$search_url=GETPOST('search_url','alpha');
$search_email=GETPOST('search_email','alpha');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');



// Protection if external user
if ($user->rights->contab->cprove!=1)
{
	accessforbidden();
}


if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Contabsociete($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabsociete'));
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
	if ($action=='edit2') {
		$object->nom=GETPOST('nom','alpha');
		$object->address=GETPOST('address','alpha');
		$object->zip=GETPOST('zipcode','alpha');
		$object->town=GETPOST('town','alpha');
		$object->fk_departement=GETPOST('country_id','int');
		$object->fk_pays=GETPOST('state_id','int');
		$object->phone=GETPOST('phone','alpha');
		$object->fax=GETPOST('fax','alpha');
		$object->url=GETPOST('url','alpha');
		$object->email=GETPOST('email','alpha');
		$object->tip_prov=GETPOST('tip_prov');
		$object->rfc=GETPOST('rfc');
		$object->id_fiscal=GETPOST('id_fiscal');
		$object->tip_op=GETPOST('tip_op');
		$action='edit';
	}
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/polizas/contabsociete_list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		

		$object->nom=GETPOST('nom','alpha');
		$object->entity=$conf->entity;
		$object->address=GETPOST('address','alpha');
		$object->zip=GETPOST('zipcode','alpha');
		$object->town=GETPOST('town','alpha');
		$object->fk_departement=GETPOST('country_id','int');
		$object->fk_pays=GETPOST('state_id','int');
		$object->phone=GETPOST('phone','alpha');
		$object->fax=GETPOST('fax','alpha');
		$object->url=GETPOST('url','alpha');
		$object->email=GETPOST('email','alpha');
		$object->tip_prov=GETPOST('tip_prov');
		$object->rfc=GETPOST('rfc');
		$object->id_fiscal=GETPOST('id_fiscal');
		$object->tip_op=GETPOST('tip_op');

		


		if ($object->tip_prov==2) {
			if (empty($object->rfc))
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","RFC"), null, 'errors');
			}
			if (empty($object->id_fiscal))
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","No. ID fiscal"), null, 'errors');
			}
		}

		if ($object->valid(1)==-1)
		{
			$error++;
			setEventMessages("El RFC del Proveedor ya existe", null, 'errors');
		}

		
		if (empty($object->nom))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","Nombre del Proveedor"), null, 'errors');
		}

		if ($object->valid()==-1)
		{
			$error++;
			setEventMessages("El Nombre del Proveedor ya existe", null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/polizas/contabsociete_list.php',1);
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

		
		$object->nom=GETPOST('nom','alpha');
		$object->entity=$conf->entity;
		$object->address=GETPOST('address','alpha');
		$object->zip=GETPOST('zipcode','alpha');
		$object->town=GETPOST('town','alpha');
		$object->fk_departement=GETPOST('country_id','int');
		$object->fk_pays=GETPOST('state_id','int');
		$object->phone=GETPOST('phone','alpha');
		$object->fax=GETPOST('fax','alpha');
		$object->url=GETPOST('url','alpha');
		$object->email=GETPOST('email','alpha');
		$object->tip_prov=GETPOST('tip_prov');
		$object->rfc=GETPOST('rfc');
		$object->id_fiscal=GETPOST('id_fiscal');
		$object->tip_op=GETPOST('tip_op');
		if ($object->tip_prov==2) {
			if (empty($object->rfc))
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","RFC"), null, 'errors');
			}
			if (empty($object->id_fiscal))
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","No. ID fiscal"), null, 'errors');
			}
		}

		
		if (empty($object->nom))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired","Nombre del Proveedor"), null, 'errors');
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
			header("Location: ".dol_buildpath('/contab/polizas/contabsociete_list.php',1));
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

llxHeader('','Proveedores','');

$form=new Form($db);
$formcompany = new FormCompany($db);

// Put here content of your page

// Example : Adding jquery code



// Part to create
if ($action == 'create')
{
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
		$("#selectcountry_id").change(function() {
	    	document.formsoc.action.value="create";
	    	document.formsoc.submit();
	    });
	    $("#tip_prov").change(function() {
	    	document.formsoc.action.value="create";
	    	document.formsoc.submit();
	    });
	});
	</script>';
	print load_fiche_titre($langs->trans("Crear Proveedor"));



	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="formsoc">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Nombre del Proveedor").'
		</td>
		<td colspan="3">
			<input type="text" size="60" maxlength="128" name="nom" id="nom" value="'.GETPOST('nom').'" autofocus="autofocus">
		</td>
	</tr>';
	$tip_prov=GETPOST("tip_prov");
	if (empty($tip_prov)) {
		$tip_prov=1;
	}
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Tipo de proveedor").'
		</td>
		<td colspan="3">
			<select name="tip_prov" id="tip_prov" >';

				if ($tip_prov==1) {
					print '<option value=1 selected>Nacional</option>';
				}else{
					print '<option value=1>Nacional</option>';
				}
				if ($tip_prov==2) {
					print '<option value=2 selected>Extranjero</option>';
				}else{
					print '<option value=2>Extranjero</option>';
				}
				if ($tip_prov==3) {
					print '<option value=3 selected>Global</option>';
				}else{
					print '<option value=3>Global</option>';
				}

				
			print '
			</select>
		</td>
	</tr>';
	
	if ($tip_prov==2) {
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				<input type="text" name="rfc" id="rfc" size="32" value="'.GETPOST('rfc').'">
			</td>
		</tr>
		<tr>
			<td class="fieldrequired">'.$langs->trans("No. ID fiscal").'
			</td>
			<td colspan="3">
				<input type="text" name="id_fiscal" id="id_fiscal" size="32" value="'.GETPOST('id_fiscal').'">
			</td>
		</tr>';
	}
	if ($tip_prov==1) {
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				<input type="text" name="rfc" id="rfc" size="32" value="'.GETPOST('rfc').'">
			</td>
		</tr>';
	}


	$tip_op=GETPOST("tip_op");
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Tipo de operación").'</td>
		<td colspan="3">
			<select name="tip_op" id="tip_op" >';

				if ($tip_op==1) {
					print '<option value=1 selected> Prestación de Servicios Profesionales</option>';
				}else{
					print '<option value=1> Prestación de Servicios Profesionales</option>';
				}
				if ($tip_op==2) {
					print '<option value=2 selected> Arrendamiento de Inmuebles</option>';
				}else{
					print '<option value=2> Arrendamiento de Inmuebles</option>';
				}
				if ($tip_op==3) {
					print '<option value=3 selected> Otros </option>';
				}else{
					print '<option value=3> Otros </option>';
				}

				
			print '
			</select>
		</td>
	</tr>';


	print '
	<tr>
		<td >'.$langs->trans("Dirección").'
		</td>
		<td colspan="3">
			<textarea name="address" id="address" cols="80" rows="_ROWS_2" wrap="soft">'.GETPOST('address').'</textarea>
		</td>
	</tr>';

 	print '
 	<tr>
 		<td>'.fieldLabel('Zip','zipcode').'</td>
 		<td>';
    		print $formcompany->select_ziptown(GETPOST('zipcode'),'zipcode',array('town','selectcountry_id','state_id'),6);
    print '
    	</td>
    	<td>'.fieldLabel('Town','town').'</td>
    	<td>';
    		print $formcompany->select_ziptown(GETPOST('town'),'town',array('zipcode','selectcountry_id','state_id'));
    print '
    	</td>
    </tr>';

    // Country
    print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
    print $form->select_country(GETPOST('country_id'));
    if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
    print '</td></tr>';
    $countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
    // State
    if (empty($conf->global->SOCIETE_DISABLE_STATE))
    {
        print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
        if (GETPOST('country_id')) print $formcompany->select_state(GETPOST('state_id'),GETPOST('country_id'));
        else print $countrynotdefined;
        print '</td></tr>';
    }
    print '
	<tr>
		<td >'.fieldLabel('EMail','email').'
		</td>
		<td colspan=3>
			<input type="text" name="email" id="email" size="32" value="'.GETPOST('email').'">
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Web','url').'
		</td>
		<td colspan=3>
			<input type="text" name="url" id="url" size="32" value="'.GETPOST('url').'">
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Phone','phone').'
		</td>
		<td>
			<input type="text" name="phone" id="phone" value="'.GETPOST('phone').'">
		</td>
		<td>'.fieldLabel('Fax','fax').'
		</td>
		<td>
			<input class="flat" type="text" name="fax" value="'.GETPOST('fax').'">
		</td>
	</tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit' )
{
	print load_fiche_titre($langs->trans("Editar Proveedor"));
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
		$("#selectcountry_id").change(function() {
	    	document.formsoc.action.value="edit2";
	    	document.formsoc.submit();
	    });
	    $("#tip_prov").change(function() {
	    	document.formsoc.action.value="edit2";
	    	document.formsoc.submit();
	    });
	});
	</script>';
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="formsoc">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Nombre del Proveedor").'
		</td>
		<td colspan="3">
			<input type="text" size="60" maxlength="128" name="nom" id="nom" value="'.$object->nom.'" autofocus="autofocus">
		</td>
	</tr>';


	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Tipo de proveedor").'
		</td>
		<td colspan="3">
			<select name="tip_prov" id="tip_prov" >';

				if ($object->tip_prov==1) {
					print '<option value=1 selected>Nacional</option>';
				}else{
					print '<option value=1>Nacional</option>';
				}
				if ($object->tip_prov==2) {
					print '<option value=2 selected>Extranjero</option>';
				}else{
					print '<option value=2>Extranjero</option>';
				}
				if ($object->tip_prov==3) {
					print '<option value=3 selected>Global</option>';
				}else{
					print '<option value=3>Global</option>';
				}

				
			print '
			</select>
		</td>
	</tr>';
	
	if ($object->tip_prov==2) {
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				<input type="text" name="rfc" id="rfc" size="32" value="'.$object->rfc.'">
			</td>
		</tr>';
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("No. ID fiscal").'
			</td>
			<td colspan="3">
				<input type="text" name="id_fiscal" id="id_fiscal" size="32" value="'.$object->id_fiscal.'">
			</td>
		</tr>';
	}
	if ($object->tip_prov==1) {
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				<input type="text" name="rfc" id="rfc" size="32" value="'.$object->rfc.'">
			</td>
		</tr>';
	}


	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Tipo de operación").'</td>
		<td colspan="3">
			<select name="tip_op" id="tip_op" >';

				if ($object->tip_op==1) {
					print '<option value=1 selected> Prestación de Servicios Profesionales</option>';
				}else{
					print '<option value=1> Prestación de Servicios Profesionales</option>';
				}
				if ($object->tip_op==2) {
					print '<option value=2 selected> Arrendamiento de Inmuebles</option>';
				}else{
					print '<option value=2> Arrendamiento de Inmuebles</option>';
				}
				if ($object->tip_op==3) {
					print '<option value=3 selected> Otros </option>';
				}else{
					print '<option value=3> Otros </option>';
				}

				
			print '
			</select>
		</td>
	</tr>';




	print '
	<tr>
		<td >'.$langs->trans("Dirección").'
		</td>
		<td colspan="3">
			<textarea name="address" id="address" cols="80" rows="_ROWS_2" wrap="soft">'.$object->address.'</textarea>
		</td>
	</tr>';

 	print '
 	<tr>
 		<td>'.fieldLabel('Zip','zipcode').'</td>
 		<td>';
    		print $formcompany->select_ziptown($object->zip,'zipcode',array('town','selectcountry_id','state_id'),6);
    print '
    	</td>
    	<td>'.fieldLabel('Town','town').'</td>
    	<td>';
    		print $formcompany->select_ziptown($object->town,'town',array('zipcode','selectcountry_id','state_id'));
    print '
    	</td>
    </tr>';

    // Country
    print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
    print $form->select_country($object->fk_departement);
    print '</td></tr>';
    $countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
    // State
    if (empty($conf->global->SOCIETE_DISABLE_STATE))
    {
        print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
        	print $formcompany->select_state($object->fk_pays,$object->fk_departement);
        print '</td></tr>';
    }
    print '
	<tr>
		<td >'.fieldLabel('EMail','email').'
		</td>
		<td colspan=3>
			<input type="text" name="email" id="email" size="32" value="'.$object->email.'">
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Web','url').'
		</td>
		<td colspan=3>
			<input type="text" name="url" id="url" size="32" value="'.$object->url.'">
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Phone','phone').'
		</td>
		<td>
			<input type="text" name="phone" id="phone" value="'.$object->phone.'">
		</td>
		<td>'.fieldLabel('Fax','fax').'
		</td>
		<td>
			<input class="flat" type="text" name="fax" value="'.$object->fax.'">
		</td>
	</tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="Guardar"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("Proveedor"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Borrar Proveedor'), $langs->trans('Esta seguro de borrar al proveedor?'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '
	<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//






	print '
	<tr>
		<td >'.$langs->trans("Nombre del Proveedor").'
		</td>
		<td colspan="3">'.$object->nom.'</td>
	</tr>';


	print '
	<tr>
		<td>'.$langs->trans("Tipo de proveedor").'
		</td>
		<td colspan="3">';

				if ($object->tip_prov==1) {
					print 'Nacional';
				}
				if ($object->tip_prov==2) {
					print 'Extranjero';
				}
				if ($object->tip_prov==3) {
					print 'Global';
				}

				
			print '
		</td>
	</tr>';
	

	if ($object->tip_prov==1) {
		print '
		<tr>
			<td>'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				'.$object->rfc.'
			</td>
		</tr>';
	}
	if ($object->tip_prov==2) {
		print '
		<tr>
			<td>'.$langs->trans("RFC").'
			</td>
			<td colspan="3">
				'.$object->rfc.'
			</td>
		</tr>';
		print '
		<tr>
			<td>'.$langs->trans("No. ID fiscal").'
			</td>
			<td colspan="3">
				'.$object->id_fiscal.'
			</td>
		</tr>';
	}

	

	print '
	<tr>
		<td >'.$langs->trans("Tipo de operación").'</td>
		<td colspan="3">';

				if ($object->tip_op==1) {
					print 'Prestación de Servicios Profesionales';
				}
				if ($object->tip_op==2) {
					print 'Arrendamiento de Inmuebles';
				}
				if ($object->tip_op==3) {
					print 'Otros';
				}

				
			print '
		</td>
	</tr>';



	print '
	<tr>
		<td >'.$langs->trans("Dirección").'
		</td>
		<td colspan="3">
			<textarea disabled name="address" id="address" cols="80" rows="_ROWS_2" wrap="soft">'.$object->address.'</textarea>
		</td>
	</tr>';

 	print '
 	<tr>
 		<td>'.fieldLabel('Zip','zipcode').'</td>
 		<td>'.$object->zip.'
    	</td>
    	<td>'.fieldLabel('Town','town').'</td>
    	<td>'.$object->town.'
    	</td>
    </tr>';

    // Country
    print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
    print getCountry($object->fk_departement);
    print '</td></tr>';
 
   
    print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
    if (!empty($object->fk_pays)) {
    	print getState($object->fk_pays);
    }
    

    print '</td></tr>';
    
    print '
	<tr>
		<td >'.fieldLabel('EMail','email').'
		</td>
		<td colspan=3>
			'.$object->email.'
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Web','url').'
		</td>
		<td colspan=3>
			'.$object->url.'
		</td>
	</tr>';


	print '
	<tr>
		<td>'.fieldLabel('Phone','phone').'
		</td>
		<td>
			'.$object->phone.'
		</td>
		<td>'.fieldLabel('Fax','fax').'
		</td>
		<td>
			'.$object->fax.'
		</td>
	</tr>';

	print '</table>'."\n";
	
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
