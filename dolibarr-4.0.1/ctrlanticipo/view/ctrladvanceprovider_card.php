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
 *   	\file       ctrlanticipo/ctrladvanceprovider_card.php
 * 		\ingroup    ctrlanticipo
 * 		\brief      This file is an example of a php page
 * 					Initialy built by build_class_from_table on 2016-12-08 18:32
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
//if (! defined('ssNOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOsssssLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)
// Change this following line to use the correct relative path (../, ../../, etc)
$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
    $res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (!$res)
    die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';

// Load traductions files requiredby by page
$langs->load('ctrlanticipo@ctrlanticipo');
$langs->load('bills');
$langs->load('companies');
$langs->load('compta');
$langs->load('products');
$langs->load('banks');
$langs->load('main');
$langs->load('other');

// Get parameters
$id             = GETPOST('id', 'int');
$urlfile        = GETPOST('urlfile');
$confirm        = GETPOST('confirm');
$idprovider     = GETPOST('idprovider', 'int');
$action         = GETPOST('action', 'alpha');
$backtopage     = GETPOST('backtopage');
$myparam        = GETPOST('myparam', 'alpha');
$user_applicant = GETPOST('user_applicant', 'int');


// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}

if($action=='create' || $action == 'edit'){
    if (!$user->rights->ctrlanticipo->ctrlanticipo1->createmodify) accessforbidden();
}else if($action == 'authorize'){
    if (!$user->rights->ctrlanticipo->ctrlanticipo5->authorizationadvance) accessforbidden();
}else if($action == 'emit_payment'){
    if (!$user->rights->ctrlanticipo->ctrlanticipo6->emitpayment) accessforbidden();
}else if($action == 'delete'){
    if (!$user->rights->ctrlanticipo->ctrlanticipo7->deletepayment) accessforbidden();
}else if($action == 'view'){
    if (!$user->rights->ctrlanticipo->ctrlanticipo2->read && !$user->rights->ctrlanticipo->ctrlanticipo3->readothers) accessforbidden();
}




if (empty($action) && empty($id) && empty($ref))
    $action = 'list';

// Load object if id or ref is provided as parameter
$object = new Ctrladvanceprovider($db);

if ($id > 0  && $action != 'add') {
    $result = $object->fetch($id);
}
$upload_dir=$conf->admin->dir_output."/".$object->ref;
if ($idprovider > 0 && $action == 'create') {
    $object->ref               = advance_mask($conf,1,$object);
    $object->fk_soc            = $idprovider;
    $object->fk_user_applicant = $user->id;
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array

$extrafields = new ExtraFields($db);



/* * *****************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 * ****************************************************************** */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action);    // Note that $action and $object may have been modified by some hooks

include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';

if ($reshook < 0)
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook)) {
    // Action to add record
    if ($action == 'add') {
        if ($_POST['cancel']) {
            $urltogo = $backtopage ? $backtopage : dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_list.php', 1);
            header("Location: " . $urltogo);
            exit;
        }

        $error = 0;
        $ban=1;

        if (empty($object->fk_soc ) && empty($object->ref) && empty($object->fk_user_applicant)) {
            $object->fk_soc            = GETPOST('provider', 'int');
            $object->id                = $id;
        }
		
		$object->date_advance      = dol_mktime(0,0,0,GETPOST("date_advancemonth"),GETPOST("date_advanceday"),GETPOST("date_advanceyear"));
		$object->fk_user_applicant = GETPOST('user_applicant', 'int');
		$object->fk_paymen         = GETPOST('payment', 'int');
		$object->fk_project        = GETPOST('project', 'int');
		$object->concept_advance   = GETPOST('concept', 'alpha');
		$object->import            = floatval(GETPOST('import_advance'));
		$object->fk_tva            = GETPOST('tva', 'int');
		$object->note_public       = GETPOST('note_public', 'alpha');
		$object->note_private      = GETPOST('note_private', 'alpha');
        $object->type_advance      = GETPOST('type_advance', 'int');
        
		$object->total_import      = $object->import+($object->import*($object->fk_tva/100));
		

		$object->fk_user_author    = $user->id;
		$object->fk_mcurrency      = GETPOST('ctrl_mcurreny', 'int');

        if ($_POST['draft']) {
           $object->ref               = advance_mask($conf,1,$object);
           $object->statut            = 0;
        }else{
            $object->ref               = advance_mask($conf,0,$object);
            $object->statut            = 1;

            if (empty($object->type_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_type_advance")), null, 'errors');
            }
            if (empty($object->fk_soc) || $object->fk_soc<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_fk_soc")), null, 'errors');
            }

            if (empty($object->date_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_date_advance")), null, 'errors');
            }
            if (empty($object->fk_user_applicant) || $object->fk_user_applicant<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_fk_soc")), null, 'errors');
            }
            if (empty($object->concept_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_concept_advance")), null, 'errors');
            }
            
            if (empty($object->import) || $object->import<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_import")), null, 'errors');
            }

            if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                if (empty($object->fk_mcurrency) || $object->fk_mcurrency<0) {
                    $error++;
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_multi_cash")), null, 'errors');
                }
            }
        }
        if (!$error) {
            
            $result = $object->create($user);
            
            if ($result > 0) {
                // Creation OK
                $urltogo = $backtopage ? $backtopage : dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$object->id.(!empty($idprovider)?'&idprovider='.$idprovider:'' ).'&action=view', 1);
                header("Location: " . $urltogo);
                exit;
            } {
                // Creation KO
                if (!empty($object->errors))
                    setEventMessages(null, $object->errors, 'errors');
                else
                    setEventMessages($object->error, null, 'errors');
                $action = 'create';
            }
        }
        else {
            $object->ref               = advance_mask($conf,1);
            $object->statut            = 0;
            $action = 'create';
        }
    }

    // Cancel
    if ($action == 'update' && $_POST['cancel'])
        $action = 'view';

    // Action to update record
    if ($action == 'update' && !$_POST['cancel']) {
        $error = 0;


        $object->date_advance      = dol_mktime(0,0,0,GETPOST("date_advancemonth"),GETPOST("date_advanceday"),GETPOST("date_advanceyear"));
		$object->concept_advance   = GETPOST('concept_advance', 'alpha');
		$object->import            = floatval(GETPOST('import_advance'));
		
		$object->note_public       = GETPOST('note_public', 'alpha');
		$object->note_private      = GETPOST('note_private', 'alpha');
		$object->fk_user_modif     = $user->id;

        if(!empty($_POST['provider']) && !$object->prov_registered)
            $object->fk_soc            = GETPOST('provider', 'int');
 
		$object->fk_user_applicant = GETPOST('user_applicant', 'int');
		$object->fk_paymen         = GETPOST('payment', 'int');
		$object->fk_project        = GETPOST('project', 'int');
		$object->fk_tva            = GETPOST('tva', 'int');
		$object->fk_mcurrency      = GETPOST('ctrl_mcurreny', 'int');
        $object->total_import      = $object->import+($object->import*($object->fk_tva/100));
        $object->type_advance      = GETPOST('type_advance', 'int');

        if ($object->statut==2) {
            $object->statut=1;
            $object->date_valid="";
            $object->fk_user_valid="";
        }


        if ($object->statut>0 || $_POST['save_aut_draw'] ) {

            if (empty($object->type_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_type_advance")), null, 'errors');
            }
            if (empty($object->fk_soc) || $object->fk_soc<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_fk_soc")), null, 'errors');
            }
            if (empty($object->date_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_date_advance")), null, 'errors');
            }
            if (empty($object->fk_user_applicant) || $object->fk_user_applicant<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_fk_soc")), null, 'errors');
            }
            if (empty($object->concept_advance)) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_concept_advance")), null, 'errors');
            }
            
            if (empty($object->import) || $object->import<0) {
                $error++;
                setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_import")), null, 'errors');
            }

            if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                if (empty($object->fk_mcurrency) || $object->fk_mcurrency<0) {
                    $error++;
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->trans("ctrl_multi_cash")), null, 'errors');
                }
            }
        }


        if (!$error) {

            if ($_POST['save_aut_draw']) {
                $object->ref           =advance_mask($conf,0);
                $object->statut        =1;
                $object->fk_user_valid =$user->id;
                $object->date_valid    =dol_now();
            }

            $result = $object->update($user);
     
            if ($result > 0) {
                $action = 'view';
                (!empty($object->fk_soc) && $object->fk_soc>0) ? $object->prov_registered= true: $object->prov_registered= false;
            } else {
                // Creation KO
                if (!empty($object->errors))
                    setEventMessages(null, $object->errors, 'errors');
                else
                    setEventMessages($object->error, null, 'errors');
                $action = 'edit';
            }
        }
        else {
            $action = 'edit';
        }
    }

    if ($action == 'confirm_authorize') {

        $result = -1;

        if ($object->statut==1) {
            $object->statut=2;
            $object->fk_user_valid=$user->id;
            $result = $object->update($user);
        }

        if ($result > 0) {
            // Delete OK
            $action = 'view';
        } else {
            setEventMessages($langs->trans('ctrl_authorize_fail'), null, 'errors');
        }

    }

 
    // Action to delete
    if ($action == 'confirm_delete') {
        $result = $object->delete($user);
        if ($result > 0) {
            // Delete OK
            setEventMessages("RecordDeleted", null, 'mesgs');
            header("Location: " . dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_list.php', 1));
            exit;
        } else {
            if (!empty($object->errors))
                setEventMessages(null, $object->errors, 'errors');
            else
                setEventMessages($object->error, null, 'errors');
        }
    }

    if ($action=='rewrite_model') {
    	
        setEventMessages($langs->trans('ctrl_model_rewrited'), null);
        format_chec($id,GETPOST('format'));
        //format_chec(,GETPOST('format'))
        $action='';
    }
}




/* * *************************************************
 * VIEW
 *
 * Put here all code to build page
 * ************************************************** */

llxHeader('', $langs->trans('ctrlanticipo'));

$form = new Form($db);


// Put here content of your page
// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		var $import= parseFloat($("#import_advance").val());
        var $tva= parseInt($("#tva").val());
        if ($import>0) {
            $("#total_import").html( CurrencyFormat( parseFloat( $import+($import*($tva/100))  ).toFixed(2) )    );
        }else{
            $("#total_import").html("");
        }
	}

    jQuery("#tva").change(init_myfunc);

    jQuery("#import_advance").keyup(init_myfunc);

    $("#import_advance").keydown(function (event) {
        if (event.shiftKey == true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) || 
            (event.keyCode >= 96 && event.keyCode <= 105) || 
            event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
            event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

        } else {
            event.preventDefault();
        }

        if($(this).val().indexOf(".") !== -1 && event.keyCode == 190)
            event.preventDefault(); 
        
    });



	function CurrencyFormat(number)
	{
	   var decimalplaces = 2;
	   var decimalcharacter = ".";
	   var thousandseparater = ",";
	   number = parseFloat(number);
	   var sign = number < 0 ? "-" : "";
	   var formatted = new String(number.toFixed(decimalplaces));
	   if( decimalcharacter.length && decimalcharacter != "." ) { formatted = formatted.replace(/\./,decimalcharacter); }
	   var integer = "";
	   var fraction = "";
	   var strnumber = new String(formatted);
	   var dotpos = decimalcharacter.length ? strnumber.indexOf(decimalcharacter) : -1;
	   if( dotpos > -1 )
	   {
	      if( dotpos ) { integer = strnumber.substr(0,dotpos); }
	      fraction = strnumber.substr(dotpos+1);
	   }
	   else { integer = strnumber; }
	   if( integer ) { integer = String(Math.abs(integer)); }
	   while( fraction.length < decimalplaces ) { fraction += "0"; }
	   temparray = new Array();
	   while( integer.length > 3 )
	   {
	      temparray.unshift(integer.substr(-3));
	      integer = integer.substr(0,integer.length-3);
	   }
	   temparray.unshift(integer);
	   integer = temparray.join(thousandseparater);
	   return sign + integer + decimalcharacter + fraction;
	}
});
</script>';


// Part to create
if ($action == 'create') {
    print load_fiche_titre($langs->trans("ctrl_new_advance"));

    print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
    print '<input type="hidden" name="idprovider" value="' . $idprovider . '">';

    dol_fiche_head();
    
    print '<table class="border centpercent">' . "\n";
    // print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
    // 
    print '<tbody>';
    print '<tr><td style="width: 20%;">' . $langs->trans("ctrl_ref") . '</td><td><label id="ref"> <b>Borrador</b></label></td></tr>';

    print '
    <tr>
        <td class="fieldrequired">' . $langs->trans("ctrl_type_advance") . '</td>
        <td>';
            if (!empty($object->type_advance)) {
                if ($object->type_advance==1) {
                    print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }else{
                    print '<input type="radio" name="type_advance" value="1">&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                if ($object->type_advance==2) {
                    print '<input type="radio" name="type_advance" value="2" checked> '.$langs->trans("ctrl_type_viat");
                }else{
                    print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
                }
            }else{
                print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
            }
            
            
    print '
        </td>
    </tr>';


    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_soc") . '</td>';
    print '<td>';
    
    if ($idprovider<1) {
    	print ($form->select_thirdparty(
                   $object->fk_soc, 'provider', 'fournisseur = 1', '', ''));
    }else{
    	print '<input type="hidden" name="provider" value="'.$idprovider.'">';
    	$prov=new Societe($db);
    	$prov->fetch($idprovider);
    	print $prov->getNomUrl(0);
    }
    
    print '</td></tr>';
    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_date_advance") . '</td>';
    print '<td>';
    print ($form->select_date(
        $object->date_advance, 'date_advance', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
    ));
    print '</td></tr>';
    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_user_applicant") . '</td>';
    print '<td>';
    print ($form->select_dolusers(
       (empty($object->fk_user_applicant)?$user->id:$object->fk_user_applicant), 'user_applicant', '', 0, '', '', 1,0,0,0," and u.statut=1 "
    ));
    print '</td></tr>';
    print '<tr><td >' . $langs->trans("ctrl_fk_paymen") . '</td>';
    print '<td>';
    print ($form->select_types_paiements(
       $object->fk_paymen, 'payment', '', 0, 0, 0, 0, 1
    ));
    print '</td></tr>';
    print '<tr><td >' . $langs->trans("ctrl_fk_project"). '</td>';
    print '<td>';
    print ( $object->form_project(
        '', -1, $object->fk_project, 'project', 1, 20, 0,0
    ));
    print '</td></tr>';

    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_concept_advance") . '</td><td><textarea class="flat"  name="concept" rows="2" cols="80"> ' . GETPOST('concept') . '</textarea></td></tr>';
    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_import") . '</td><td><input class="flat" type="text" name="import_advance" id="import_advance" value="' . GETPOST('import_advance') . '"  ></td></tr>';
    print '<tr><td >' . $langs->trans("ctrl_fk_tva") . '</td>';
    print '<td>';
    print $form->load_tva("tva", $object->fk_tva, $mysoc, '');
    print '</td></tr>';
    print '<tr><td >' . $langs->trans("ctrl_total_import") . '</td><td>$<label id="total_import"></label></td></tr>';    
    print select_multidivisa($object->fk_mcurrency);

    print '<tr><td >' . $langs->trans("ctrl_note_public") . '</td><td><textarea class="flat" name="note_public"  rows="2" cols="80">' . GETPOST('note_public') . '</textarea></td></tr>';
    print '<tr><td >' . $langs->trans("ctrl_note_private") . '</td><td><textarea class="flat" name="note_private" rows="2" cols="80">' . GETPOST('note_private') . '</textarea></td></tr>';
    print '</tbody>';
    print '</table>' . "\n";

    dol_fiche_end();

    print '
        <div class="center">
            <input type="submit" class="button"  style="border-radius:0px !important;" name="draft" value="' . $langs->trans("ctrl_button_draft") . '"> &nbsp; 
            <input type="submit" class="button"  style="border-radius:0px !important;" name="add" value="' . $langs->trans("ctrl_create_advance_row") . '"> &nbsp; 
            <input type="submit" class="button"  style="border-radius:0px !important;" name="cancel" value="' . $langs->trans("Cancel") . '">
        </div>';

    print '</form>';
}



// Part to edit record
if ($id  && $action == 'edit') {
    print load_fiche_titre($langs->trans("ctrl_modif_advance"));
    
    print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
    print '<input type="hidden" name="id" value="' . $object->id . '">';

    dol_fiche_head();
    print '<table class="border centpercent">' . "\n";
    // print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
    //
    print '<tbody>';
	    print '<tr><td style="width: 20%;">' . $langs->trans("ctrl_ref") . '</td><td><span ><label id="ref"> <b>'.$object->ref.'</b></label></span><span style="margin-left:70%;">Estatus: '.img_picto($langs->trans('ctrl_action_statut'.$object->statut),'statut'.(($object->statut==5)?7:$object->statut))."  ".$langs->trans('ctrl_action_statut'.$object->statut).'</span></td></tr>';
	   
        print '
        <tr>
            <td class="fieldrequired">' . $langs->trans("ctrl_type_advance") . '</td>
            <td>';
                if (!empty($object->type_advance)) {
                    if ($object->type_advance==1) {
                        print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }else{
                        print '<input type="radio" name="type_advance" value="1">&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                    if ($object->type_advance==2) {
                        print '<input type="radio" name="type_advance" value="2" checked> '.$langs->trans("ctrl_type_viat");
                    }else{
                        print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
                    }
                }else{
                    print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
                }
        print '
            </td>
        </tr>';

        print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_soc") . '</td>';
	    print '<td>';

        if (!$object->prov_registered) {
            print ($form->select_thirdparty(
                       $object->fk_soc, 'provider', 'fournisseur = 1', '', ''));
        }else{
            $prov=new Societe($db);
            $prov->fetch($object->fk_soc);
            print $prov->getNomUrl(0);
        }
        
	    print '</td></tr>';
	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_date_advance") . '</td>';
	    print '<td>';
	    print ($form->select_date(
	       $object->date_advance, 'date_advance', 0, 0, 0, "", 1, 1, 1, 0, '', '', ''
	    ));
	    print '</td></tr>';
	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_user_applicant") . '</td>';
	    print '<td>';
	    print ($form->select_dolusers(
	       (empty($object->fk_user_applicant)?$user->id:$object->fk_user_applicant), 'user_applicant', '', 0, '', '', 1,0,0,0," and u.statut=1 "
	    ));
	    print '</td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_paymen") . '</td>';
	    print '<td>';
	    print ($form->select_types_paiements(
	       $object->fk_paymen, 'payment', '', 0, 0, 0, 0, 1
	    ));
	    print '</td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_project"). '</td>';
	    print '<td>';
	    print ( $object->form_project(
	        '', -1, $object->fk_project, 'project', 1, 20, 0,0
	    ));
	    print '</td></tr>';


	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_concept_advance") . '</td><td><textarea class="flat"  name="concept_advance" rows="2" cols="80"> ' . $object->concept_advance . '</textarea></td></tr>';
	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_import") . '</td><td><input class="flat" type="text" name="import_advance" id="import_advance" value="' . $object->import. '"></td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_tva") . '</td>';
	    print '<td>';
	    print $form->load_tva("tva", $object->fk_tva, $mysoc, '');
	    print '</td></tr>';

	    print '<tr><td >' . $langs->trans("ctrl_total_import") . '</td><td>$<label id="total_import">'.$object->total_import.'</label></td></tr>';
        print select_multidivisa($object->fk_mcurrency);
	    print '<tr><td >' . $langs->trans("ctrl_note_public") . '</td><td><textarea class="flat" name="note_public"  rows="2" cols="80">' . $object->note_public . '</textarea></td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_note_private") . '</td><td><textarea class="flat" name="note_private" rows="2" cols="80">' . $object->note_private . '</textarea></td></tr>';
    print '</tbody>';
    print '</table>';
    dol_fiche_end(); 

    print '<div class="center"><input type="submit" class="button"  style="border-radius:0px !important;" name="save" value="' . $langs->trans("Save") . '">';
    if ($user->rights->ctrlanticipo->ctrlanticipo4->validateproviders && $object->statut==0) {
        print '&nbsp;<input type="submit" class="button"  style="border-radius:0px !important;" name="save_aut_draw" value="' . $langs->trans("ctrl_create_advance_row") . '">';
    }
    print ' &nbsp; <input type="submit" class="button"  style="border-radius:0px !important;" name="cancel" value="' . $langs->trans("Cancel") . '">';
    print '</div>';

    print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete' || $action == 'delete_ant' || $action=='authorize' || $action=='confirm_deletefile' )  ) {

    $head = advance_prepare_head($object);

    dol_fiche_head($head, 'payment', $langs->trans("ctrl_advance_note"), 0, 'payment');


    if ($action == 'delete_ant' && empty($urlfile)) {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ctrl_delete_title_advance'), $langs->trans('ctrl_delete_text_advance').' '.$object->ref, 'confirm_delete', '', 0, 1);
        print $formconfirm;
    }

    if ($action == 'authorize') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ctrl_authorize_title_advance'), $langs->trans('ctrl_authorize_text_advance').' '.$object->ref, 'confirm_authorize', '', 0, 1);
        print $formconfirm;
    }
    print'<fieldset disabled style="border:none !important;">';
    print '<table class="border centpercent">' . "\n";

    print '<tbody>';
	    print '
        <tr>
            <td style="width: 20%;">' . $langs->trans("ctrl_ref") . '</td>
            <td>
                
                
                ';
                if ($object->statut!=3 && $object->statut!=5 && $object->statut!=4) {
                  

                    $ref=$object->ref;
                    $object->ref=$object->id;
                    $object->next_prev_filter="te.statut!=6";
                    print $form->showrefnav(
                        $object, "id", 
                        'Estatus: '.img_picto($langs->trans('ctrl_action_statut'.$object->statut),'statut'.(($object->statut==5)?7:$object->statut))."  ".$langs->trans('ctrl_action_statut'.$object->statut).'&nbsp;&nbsp;'
                        , 1, 'rowid','none','','',0,
                        '
                        <span style="line-height: 30px;" >
                            <label id="ref"> 
                                <b>'.$ref.'</b>
                            </label>
                        </span>
                        
                        ');
                    $object->ref=$ref;


                }else{
                    print '<b>'.$object->ref.'</b>&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;<a href="./ctrladvancecredit_card.php?id='.$id.'">'.img_picto('','object_reduc').' '.$langs->trans('ctrl_view_manage_credit').'</a>&nbsp;&nbsp;&nbsp;)';
                }
            print '
            </td>';
            if ($object->statut==3 || $object->statut==5 || $object->statut==4) {
                print '
                <td style="width: 60%;">';
                $ref=$object->ref;
                $object->ref=$object->id;
                $object->next_prev_filter="te.statut!=6";
                print $form->showrefnav(
                    $object, "id", '', 1, 'rowid','none','','',0,
                    '<span style="line-height: 30px;">Estatus: '.img_picto($langs->trans('ctrl_action_statut'.$object->statut),'statut'.(($object->statut==5)?7:$object->statut))."  ".$langs->trans('ctrl_action_statut'.$object->statut).'</span>
                    ');
                print '</td>';
                $object->ref=$ref;
            }
        print '
        </tr>';


        print '
        <tr>
            <td class="fieldrequired">' . $langs->trans("ctrl_type_advance") . '</td>
            <td>';
                if (!empty($object->type_advance)) {
                    if ($object->type_advance==1) {
                        print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }else{
                        print '<input type="radio" name="type_advance" value="1">&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                    if ($object->type_advance==2) {
                        print '<input type="radio" name="type_advance" value="2" checked> '.$langs->trans("ctrl_type_viat");
                    }else{
                        print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
                    }
                }else{
                    print '<input type="radio" name="type_advance" value="1" checked>&nbsp;'.$langs->trans("ctrl_type_ext_prov").'
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    print '<input type="radio" name="type_advance" value="2"> '.$langs->trans("ctrl_type_viat");
                }
        print '
            </td>
        </tr>';

	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_soc") . '</td>';
	    print '<td>';

        if (!$object->prov_registered) {
          print ($form->select_thirdparty_list(  
                $object->fk_soc,'provider','fournisseur = 1',-1, 0, 0, '', '', 0, '', 'minwidth400', 'disabled'
            ));
        }else{
            $prov =new Societe($db);
            $prov->fetch($object->fk_soc);
            print $prov->getNomUrl(0);
        }
	    print '</td>';
        $num_paiments=0;
        if ($object->statut==3 || $object->statut==5 || $object->statut==4) {
            //tabla de pagos
            print '<td rowspan="12" style="vertical-align: top;" >';
            $sql = '
            SELECT
                a.*, b.datec,b.fk_bank_account as acc
            FROM
                llx_ctrl_paiementfourn_facturefourn AS a
            INNER JOIN llx_ctrl_paiementfourn AS b ON a.fk_paiementfourn = b.rowid
            LEFT JOIN llx_bank as c on c.rowid=b.fk_bank_account
            WHERE
                a.fk_facturefourn = '.$object->id." order by b.datec";

            $resql=$db->query($sql);
            $tot_ammot_rest=0;
            if ($resql){
                $num = $db->num_rows($resql);
                $i = 0;
                $total = 0;
                print '<table class="noborder" width="100%">';
                print '<tr class="liste_titre">';
                print '<td>'.$langs->trans('ctrl_view_pay').'</td>';
                print '<td>'.$langs->trans('ctrl_view_date').'</td>';
                print '<td align="center">'.$langs->trans('ctrl_view_type').'</td>';
                print '<td align="right">'.$langs->trans('ctrl_view_account').'</td>';
                print '<td align="right">'.$langs->trans('ctrl_view_import').'</td>';
                print "</tr>";

                if ($num > 0)
                {
                    $var=True;

                    while ($i < $num)
                    {
                        $objp = $db->fetch_object($resql);
                        $band=1;
                        $var=!$var;
                        print '<tr >';

                        $invoice=new PaiementAdvance($db);
                        $res=$invoice->fetch($objp->fk_paiementfourn);
                        print '<td>';
                        print $invoice->getNomUrl(1);
                        print "</td>\n";

                        // Third party
                        print '<td>';
                        print dol_print_date($db->jdate($objp->datec),'%d/%m/%Y');
                        print '</td>';

                        // type payment
                        $labeltype=$langs->trans("PaymentType".$invoice->type_code)!=("PaymentType".$invoice->type_code)?$langs->trans("PaymentType".$invoice->type_code):$invoice->type_libelle;
                        print '<td >'.$labeltype.'</td>';


                        // account
                        if (!empty($objp->acc)) {
                            print '<td align="right">';
                                $account= new Account($db);
                                $res=$account->fetch($objp->acc);
                                print $account->getNomUrl(1);
                            print '</td>';
                        }
                        
                        // Remain to pay
                        $tot_ammot_rest+=$invoice->montant;
                        print '<td align="right">'.price($invoice->montant).'</td>';
                        print "</tr>\n";
                        $i++;
                        $num_paiments++;
                    }
                }
                if ($tot_ammot_rest>0) {
                    print '<tr>';

                    print '<td colspan=4>';
                        print $langs->trans("ctrl_remains");
                    print '</td>';

                    print '<td align="right">';
                        print price($object->total_import-$tot_ammot_rest);
                    print '</td>';

                    print '</tr>';
                }
                $var=!$var;

                print "</table>\n";
                $db->free($resql);
            }
            print '</td>';
        }
        

        print '</tr>';

        

	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_date_advance") . '</td>';
	    print '<td>';
	    print ($form->select_date(
	                    $object->date_advance,  'date_advance', 0, 0, 0, "", 1, 1, 1, 1, '', '', ''
	    ));
	    print '</td></tr>';
	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_fk_user_applicant") . '</td>';
	    print '<td>';
	    print ($form->select_dolusers(
	                    (empty($object->fk_user_applicant)?$user->id:$object->fk_user_applicant), 1, '','', 1, '', 0,0,0,0," and u.statut=1 "
	    ));
	    print '</td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_paymen") . '</td>';
	    print '<td>';
	    print ($form->select_types_paiements(
	                $object->fk_paymen, 'payment', '', 0, 0, 0, 0, 1
	    ));
	    print '</td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_project"). '</td>';
	    print '<td>';
	    print ( $object->form_project(
	                    '', -1, $object->fk_project, 'project', 1, 20, 0,1
	    ));
	    print '</td></tr>';

	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_concept_advance") . '</td><td><textarea class="flat"  name="concept" rows="2" cols="80" > ' . $object->concept_advance . '</textarea></td></tr>';
	    print '<tr><td class="fieldrequired">' . $langs->trans("ctrl_import") . '</td><td><input class="flat" type="text" name="import_advance" value="' . price($object->import). '" ></td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_fk_tva") . '</td>';
	    print '<td>';
	    print $form->load_tva("tva", $object->fk_tva, $mysoc, '');
	    print '</td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_total_import") . '</td><td>$<label id="total_import">'.price($object->total_import).'</label></td></tr>';
        print select_multidivisa($object->fk_mcurrency,"fk_mcurrency",0,'',1);
	    print '<tr><td >' . $langs->trans("ctrl_note_public") . '</td><td><textarea class="flat" name="note_public"  rows="2" cols="80" >' . $object->note_public . '</textarea></td></tr>';
	    print '<tr><td >' . $langs->trans("ctrl_note_private") . '</td><td><textarea class="flat" name="note_private" rows="2" cols="80" >' . $object->note_private . '</textarea></td></tr>';
	print '</tbody>';
    print '</table>';
    print'</fieldset>';
    dol_fiche_end();

    // Buttons

    print '<div class="tabsAction">';


    if ($user->rights->ctrlanticipo->ctrlanticipo6->emitpayment && ($object->statut==2 || $object->statut==5) && $object->fk_paymen==7 ) {
        print '
        <div class="inline-block divButAction">
            <a class="button" style="border-radius:0px !important;" href="ctrlbankcheck_card.php?cid='.$object->id.'&amp;action=create">'.$langs->trans("ctrl_check_imp").'</a>
        </div>';
    }
    if ($user->rights->ctrlanticipo->ctrlanticipo1->createmodify) {
        if ($band!=1) {
            print '<div class="inline-block divButAction"><a class="button"  style="border-radius:0px !important;"href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=edit'.(!empty($idprovider)?'&idprovider='.$idprovider:'' ).'">' . $langs->trans("Modify") . '</a></div>' . "\n";
        }
        
    }
    
    if ($user->rights->ctrlanticipo->ctrlanticipo5->authorizationadvance && $object->statut==1) {
        print '<div class="inline-block divButAction"><a class="button"  style="border-radius:0px !important;"href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=authorize">' . $langs->trans("Authorize") . '</a></div>' . "\n";
    }

    if ($user->rights->ctrlanticipo->ctrlanticipo6->emitpayment && ($object->statut==2 || $object->statut==5 )) {
        print '<div class="inline-block divButAction"><a class="button" style="border-radius:0px !important;" href="ctrladvanceprovider_card_payment.php?id=' . $object->id . '&action=create">' . $langs->trans("ctrl_button_payment") . '</a></div>' . "\n";
    }
    if ($user->rights->ctrlanticipo->ctrlanticipo7->deletepayment) {
        if ($num_paiments==0) {
            print '<div class="inline-block divButAction"><a class="button" style="color: #800 !important; border-radius:0px !important;" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=delete_ant">' . $langs->trans('Delete') . '</a></div>' . "\n";
        }
        
    }

    

    print '</div>';
    //if ($object->statut==3) {
	    require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
	    $formfile = new FormFile($db);
	    $modulepart = 'systemtools';
	    $permission = $user->rights->ctrlanticipo->ctrlanticipo1->createmodify;




	    $carpeta=$conf->admin->dir_output."/".$object->ref;
	    $filearray=dol_dir_list($carpeta,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);



	    print '
        <div class="fichecenter">
            <div class="fichehalfleft">';
	    if ($action == 'delete')
	    {
	        $langs->load("companies");  // Need for string DeleteFile+ConfirmDeleteFiles
	        $ret = $form->form_confirm(
	            $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&urlfile=' . urlencode(GETPOST("urlfile")) . '&linkid=' . GETPOST('linkid', 'int') . (empty($param)?'':$param),
	            $langs->trans('DeleteFile'),
	            $langs->trans('ConfirmDeleteFile'),
	            'confirm_deletefile',
	            '',
	            0,
	            1
	        );
	    }
	    list_of_documents(
	        $filearray,
	        $object,
	        $modulepart,
	        '',
	        0,
	        '',        // relative path with no file. For example "moduledir/0/1"
	        $permission
	    );

        if ($object->fk_paymen==7) {
           $sql='SELECT a.* FROM llx_ctrl_bank_check as a WHERE a.fk_paiment='.$object->id;
            $resql=$db->query($sql);

            if ($resql)
            {

                print '
                </div>
                <div class="fichehalfright">
                    <div class="ficheaddleft">';
                        

                       
                            $num = $db->num_rows($resql);
                            $i=0;
                            if ($num>0) {
                                print load_fiche_titre($langs->trans("ctrl_check_view_tit_adm"),"","title_commercial");
                                print '<table class="liste formdoc noborder" style="width: 100% !important;" >';
                                    print '<tr class="liste_titre">';
                                    print '<td>'.$langs->trans('ctrl_ref').'</td>';
                                    print '<td align="center">'.$langs->trans('ctrl_date_advance').'</td>';
                                    print '<td align="right">'.$langs->trans('ctrl_check_format').'</td>';
                                    print '<td align="right">'.$langs->trans('ctrl_check_view_tit_view').'</td>';
                                    print '<td align="right">'.$langs->trans('ctrl_check_view_tit_pdf').'</td>';
                                    print "</tr>\n";
                                    while ($i < $num){
                                        $objp = $db->fetch_object($resql);
                                        print '<tr >';
                                            print '<td>'.$objp->ref.'</td>';
                                            print '<td align="center">'.dol_print_date($objp->date_asign,'%d/%m/%Y').'</td>';
                                            print '<td align="right">'.(($objp->mode_print==1)?"HSBC":"Santander").'</td>';
                                            print '<td align="right">
                                                <a href="ctrlbankcheck_card.php?id='.$objp->rowid.'">
                                                '.img_picto($langs->trans('ctrl_check_view_detail'),'view').'
                                                </a>
                                            </td>';
                                            print '<td align="right">
                                                <a href="cheque.php?id='.$objp->rowid.'" TARGET="_BLANK">
                                                '.img_picto($langs->trans('ctrl_check_view_pdf'),'pdf2').'
                                                </a>
                                            </td>';
                                        print "</tr>\n";
                                        $i++;
                                    }

                                print "</table>\n";
                            }
                       

                        print '
                        </div>
                    </div>
                </div>';
            }
        }
        
    //}
    
}


// End of page
llxFooter();
$db->close();
