<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005      Brice Davoleau       <brice.davoleau@gmail.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2006-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007      Patrick Raguin  		<patrick.raguin@gmail.com>
 * Copyright (C) 2010      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2015      Marcos García        <marcosgdf@gmail.com>
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
 *  \file       htdocs/societe/agenda.php
 *  \ingroup    societe
 *  \brief      Page of third party events
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
dol_include_once('/cclinico/class/pacientes.class.php');

$langs->load("companies");

// Security check
$socid = GETPOST('socid','int');
$id = GETPOST('id','int');
if ($user->societe_id) $socid=$user->societe_id;
//$result = restrictedArea($user, 'societe', $socid, '&societe');

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
//$hookmanager->initHooks(array('agendathirdparty'));


/*
 *	Actions
 */

$parameters=array('id'=>$socid);



/*
 *	View
 */

$object = new Pacientes($db);

if ($id)
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$langs->load("companies");


	//$object = new Societe($db);
	$result = $object->fetch($id);

	$title=$langs->trans("Agenda");
	
	llxHeader('','Agenda');
	$head = pacientes_prepare_head($object);

    dol_fiche_head($head, 'agenda', 'Agenda', 0, 'contact');

    $elemento=$object->element;
    $object->element="contact";
    dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', '');
    $object->element=$elemento;

	if (! empty($conf->notification->enabled)) $langs->load("mails");
	dol_fiche_end();

    $objthirdparty=$object;
    $objcon=new stdClass();
	
    $out='';
    $permok=$user->rights->agenda->myactions->create;
    if ((! empty($objthirdparty->id) || ! empty($objcon->id)) && $permok)
    {
        //$out.='<a href="'.DOL_URL_ROOT.'/comm/action/card.php?action=create';
        if (get_class($objthirdparty) == 'Societe') $out.='&amp;socid='.$objthirdparty->id;
        $out.=(! empty($objcon->id)?'&amp;contactid='.$objcon->id:'').'&amp;backtopage=1&amp;percentage=-1';
    	//$out.=$langs->trans("AddAnAction").' ';
    	//$out.=img_picto($langs->trans("AddAnAction"),'filenew');
    	//$out.="</a>";
	}

	print '<div class="tabsAction">';

    if (! empty($conf->agenda->enabled))
    {
    	if (! empty($user->rights->agenda->myactions->create) || ! empty($user->rights->agenda->allactions->create))
    	{
        	print '<a class="butAction" href="'.DOL_URL_ROOT.'/cclinico/agenda_card.php?aid='.$id.'&action=create'.$out.'">'.$langs->trans("AddAction").'</a>';
    	}
    	else
    	{
        	print '<a class="butActionRefused" href="#">'.$langs->trans("AddAction").'</a>';
    	}
    }
    print '</div>';

    print load_fiche_titre('Eventos respecto al paciente','','');

    // List of todo actions
    print $object->show_actions_todo($conf,$langs,$db,$object,null,0,1);

    // List of done actions
    print $object->show_actions_done($conf,$langs,$db,$object);
}


llxFooter();

$db->close();
