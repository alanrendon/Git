<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2015 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	    \file       htdocs/contact/info.php
 *      \ingroup    societe
 *		\brief      Onglet info d'un contact
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
dol_include_once('/cclinico/class/pacientes.class.php');
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';

$langs->load("companies");


// Security check
$id = GETPOST("id",'int');
if ($user->societe_id) $socid=$user->societe_id;
//$result = restrictedArea($user, 'contact', $id, 'socpeople&societe');

$object = new Pacientes($db);



/*
 * 	View
 */

$form=new Form($db);

$title = (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses"));

llxHeader('',$title,'EN:Module_Third_Parties|FR:Module_Tiers|ES:M&oacute;dulo_Empresas');

if ($id > 0)
{
	$result = $object->fetch($id, $user);

	$object->info($id);


	$head = pacientes_prepare_head($object);

    dol_fiche_head($head, 'info', 'Notas', 0, 'contact');

	$linkback = '<a href="'.DOL_URL_ROOT.'/cclinico/pacientes_list.php">'.$langs->trans("BackToList").'</a>';

	$elemento=$object->element;
    $object->element="contact";
    dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', '');
    $object->element=$elemento;


	print '<div class="fichecenter">';

	print '<div class="underbanner clearboth"></div>';

	print '<br>';
	
	dol_print_object_info($object);

	print '</div>';
	
	dol_fiche_end();
}

llxFooter();

$db->close();