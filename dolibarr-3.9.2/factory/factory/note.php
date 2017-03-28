<?php
/* Copyright (C) 2014		Charles-Fr BENKE	<charles.fr@benke.fr>
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
 *	\file       htdocs/factory/note.php
 *	\ingroup    equipement
 *	\brief      Fiche d'information sur un OF
 */


$res=@include("../main.inc.php");                    // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");        // For "custom" directory

require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";
require_once DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php";

dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');

$langs->load('companies');
$langs->load("factory@factory");

$id = GETPOST('id','int');
$ref = GETPOST('ref', 'alpha');
$action=GETPOST('action','alpha');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'factory');

$object = new Factory($db);
$object->fetch($id, $ref);

/*
 * Actions
 */
if ($action == 'setnote_public' && $user->rights->factory->creer)
{
	$result=$object->update_note_public(dol_html_entity_decode(GETPOST('note_public'), ENT_QUOTES), '_public');
	if ($result < 0) dol_print_error($db,$object->error);
}

else if ($action == 'setnote_private' && $user->rights->factory->creer)
{
	$result=$object->update_note(dol_html_entity_decode(GETPOST('note_private'), ENT_QUOTES), '_private');
	if ($result < 0) dol_print_error($db,$object->error);
}


/*
 * View
 */
llxHeader();

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{
	dol_htmloutput_mesg($mesg);

	$societe = new Societe($db);
	$societe->fetch($object->fk_soc_client);

	$head=factory_prepare_head($object, $user);
	dol_fiche_head($head, 'notes', $langs->trans('Factory'), 0, 'factory@factory');

	print '<table class="border" width="100%">';
	print '<tr><td width="25%">'.$langs->trans('Ref').'</td><td colspan="3">';
	print $form->showrefnav($object,'ref','',1,'ref','ref');
	print '</td></tr>';

	$prod=new Product($db);
	$prod->fetch($object->fk_product);
	print '<tr><td class="fieldrequired">'.$langs->trans("Product").'</td><td>'.$prod->getNomUrl(1)." : ".$prod->label.'</td></tr>';

	if ($factory->fk_entrepot > 0)
	{
		$entrepotStatic=new Entrepot($db);
		$entrepotStatic->fetch($object->fk_entrepot);
		print $entrepotStatic->getNomUrl(1)." - ".$entrepotStatic->lieu." (".$entrepotStatic->zip.")" ;
	}

	print "</table>";
	print '<br>';

	include(DOL_DOCUMENT_ROOT.'/core/tpl/notes.tpl.php');
	dol_fiche_end();
}

llxFooter();
$db->close();
?>
