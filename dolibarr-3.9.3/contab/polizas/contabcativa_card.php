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
 *   	\file       contab/contabcativa_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-05-10 00:37
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
dol_include_once('/contab/class/contabcativa.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_id_cuenta=GETPOST('search_id_cuenta','int');
$search_porcentaje=GETPOST('search_porcentaje','alpha');
$search_id_user_create=GETPOST('search_id_user_create','int');
$search_id_user_update=GETPOST('search_id_user_update','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';
$values= null;
// Load object if id or ref is provided as parameter
$object=new Contabcativa($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
	$values=$object->getAccount($object->id_cuenta);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabcativa'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}


		$error=0;

		/* object_prop_getpost_prop */
		
		$object->id_cuenta=GETPOST('id_cuenta','int');
		$object->porcentaje=GETPOST('porcentaje','alpha');

		

		if (empty($object->id_cuenta))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired","Cuenta"), null, 'errors');
		}
		if (empty($object->porcentaje))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired","% de IVA"), null, 'errors');
		}

		if ($object->id_cuenta>0) {
			$values=$object->getAccount($object->id_cuenta);
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/polizas/contabcativa_list.php',1);
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

		
		$object->id_cuenta=GETPOST('id_cuenta','int');
		$object->porcentaje=GETPOST('porcentaje','alpha');

			

		if (empty($object->id_cuenta))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired","Cuenta"), null, 'errors');
		}
		if (empty($object->porcentaje))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired","% de IVA"), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$object->fetch($result);
				$values=$object->getAccount($object->id_cuenta);
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
			header("Location: ".dol_buildpath('/contab/polizas/contabcativa_list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}



function formSearchProd($label = 'Productos/Servicios', $htmlname = 'idprod', $idOrRef = true,$value_acount = null){

	$id_val=1;
	$descta="";
	if ($value_acount!=null) {
		$id_val=$value_acount->rowid;
		$descta=$value_acount->descta;
	}
    print '
    <span class="prod_entry_mode_predef">
    <label for="prod_entry_mode_predef"> 
        '.$label.'
    </label> 
    <input type="hidden" name="'.$htmlname.'" id="'.$htmlname.'" value="'.$id_val.'">
    <!-- Javascript code for autocomplete of field idprod -->
    <script type="text/javascript">
    $(document).ready(function() {
        var autoselect = 0;
        var options = [];
        var get_parameter = "";

        /* Remove product id before select another product use keyup instead of change to avoid loosing the product id. This is needed only for select of predefined product */
        /* TODO Check if we can remove this */
        $("input#search_'.$htmlname.'").keydown(function() {
            $("#'.$htmlname.'").val("");
        });

        /* I disable this. A call to trigger is already done later into the select action of the autocomplete code
            $("input#search_'.$htmlname.'").change(function() {
            console.log("Call the change trigger on input idprod because of a change on search_'.$htmlname.' was triggered");
            $("#'.$htmlname.'").trigger("change");
        });*/

        // Check options for secondary actions when keyup
        $("input#search_'.$htmlname.'").keyup(function() {
                if ($(this).val().length == 0)
                {
                    $("#search_'.$htmlname.'").val("");
                    $("#'.$htmlname.'").val("").trigger("change");
                    if (options.option_disabled) {
                        $("#" + options.option_disabled).removeAttr("disabled");
                    }
                    if (options.disabled) {
                        $.each(options.disabled, function(key, value) {
                            $("#" + value).removeAttr("disabled");
                        });
                    }
                    if (options.update) {
                        $.each(options.update, function(key, value) {
                            $("#" + key).val("").trigger("change");
                        });
                    }
                    if (options.show) {
                        $.each(options.show, function(key, value) {
                            $("#" + value).hide().trigger("hide");
                        });
                    }
                }
        });
        $("input#search_'.$htmlname.'").autocomplete({
            source: function( request, response ) {
                $.get("'.DOL_URL_ROOT.'/contab/polizas/getprod.php?action=selectProd", { idprod: request.term }, function(data){

                    if (data != null)
                    {
                        response($.map( data, function(item) {
                            if (autoselect == 1 && data.length == 1) {
                                $("#search_'.$htmlname.'").val(item.value);
                                '.( $idOrRef  ? '$("#'.$htmlname.'").val(item.key).trigger("change");' : '$("#'.$htmlname.'").val(item.value).trigger("change");' ).'
                            }
                            var label = item.label.toString();
                            var update = {};
                            if (options.update) {
                                $.each(options.update, function(key, value) {
                                    update[key] = item[value];
                                });
                            }
                            return { label: label, value: item.value, id: item.key, update: update, disabled: item.disabled }
                        }));
                    }
                    else console.error("Error: Ajax url /dolibarr-4.0.4/htdocs/product/ajax/products.php?htmlname=idprod&outjson=1&price_level=0&type=&mode=1&status=1&finished=2 has returned an empty page. Should be an empty json array.");
                }, "json");
            },
            dataType: "json",
            minLength: 1,
            select: function( event, ui ) {		// Function ran once new value has been selected into javascript combo
                console.log("Call change on input idprod because of select definition of autocomplete select call on input#search_'.$htmlname.'");
                console.log("Selected id = "+ui.item.id+" - If this value is null, it means you select a record with key that is null so selection is not effective");
                '.( $idOrRef  ? '$("#'.$htmlname.'").val(ui.item.id).trigger("change");' : '$("#'.$htmlname.'").val(ui.item.value).trigger("change");' ).' // Select new value
                // Disable an element
                if (options.option_disabled) {
                    if (ui.item.disabled) {
                        $("#" + options.option_disabled).prop("disabled", true);
                        if (options.error) {
                            $.jnotify(options.error, "error", true);		// Output with jnotify the error message
                        }
                        if (options.warning) {
                            $.jnotify(options.warning, "warning", false);		// Output with jnotify the warning message
                        }
                } else {
                        $("#" + options.option_disabled).removeAttr("disabled");
                    }
                }
                if (options.disabled) {
                    $.each(options.disabled, function(key, value) {
                        $("#" + value).prop("disabled", true);
                    });
                }
                if (options.show) {
                    $.each(options.show, function(key, value) {
                        $("#" + value).show().trigger("show");
                    });
                }
                // Update an input
                if (ui.item.update) {
                    // loop on each "update" fields
                    $.each(ui.item.update, function(key, value) {
                        $("#" + key).val(value).trigger("change");
                    });
                }
                console.log("ajax_autocompleter new value selected, we trigger change on original component so field #search_'.$htmlname.'");
                $("#search_'.$htmlname.'").trigger("change");	// We have changed value of the combo select, we must be sure to trigger all js hook binded on this event. This is required to trigger other javascript change method binded on original field by other code.
            }
            ,delay: 200
        }).data("ui-autocomplete")._renderItem = function( ul, item ) {
            return $("<li>")
            .data( "ui-autocomplete-item", item ) // jQuery UI > 1.10.0
            .append( "<a><span class=\'tag\'>" + item.label + "</span></a>" )
            .appendTo(ul);
        };
    });
    </script>
    <input type="text" size="20" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$descta.'" class="ui-autocomplete-input" autocomplete="off">
    </span>
    ';
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Asignación de IVA','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code



// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Asignacion de % de IVA") );

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
	<tr>
		<td class="fieldrequired">Cuenta</td>';
		print '<td>';
			print formSearchProd(" ","id_cuenta",true,$values);
			print "<script type='text/javascript'> $(document).ready(function () { $('#s2id_prod').width('80%'); }); </script>";
		print '</td>';
	print '
	</tr>';

	print '
	<tr>
		<td class="fieldrequired">% de IVA</td>
		<td><input class="flat" type="text" name="porcentaje" value="'.GETPOST('porcentaje').'"></td>
	</tr>';



	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

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
	print '
	<tr>
		<td class="fieldrequired">Cuenta</td>
		<td>';
		print formSearchProd(" ","id_cuenta",true,$values);
	print '
		</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">% de IVA</td>
		<td><input class="flat" type="text" name="porcentaje" value="'.$object->porcentaje.'"></td>
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
	print load_fiche_titre($langs->trans("Asignacion de % de IVA"));
    

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Borrar cuenta de IVA'), $langs->trans('Desea borrar el vinculo?'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}



	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '
	<tr>
		<td class="fieldrequired">Cuenta</td>
		<td>'.$values->descta.'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">% de IVA</td>
		<td>'.$object->porcentaje.'</td>
	</tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

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
