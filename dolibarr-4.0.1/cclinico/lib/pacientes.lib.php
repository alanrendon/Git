<?php
/* Copyright (C) 2006-2010  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012  Regis Houssin       <regis.houssin@capnetworks.com>
 * Copyright (C) 2015       Frederic France     <frederic.france@free.fr>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
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
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/contact.lib.php
 *		\brief      Ensemble de fonctions de base pour les contacts
 */

/**
 * Prepare array with list of tabs
 *
 * @param   Contact	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function pacientes_prepare_head(Pacientes $object)
{
	global $langs, $conf, $user;

	$tab = 0;
	$head = array();

	$head[$tab][0] = DOL_URL_ROOT.'/cclinico/pacientes_card.php?id='.$object->id;
	$head[$tab][1] = $langs->trans("Card");
	$head[$tab][2] = 'card';
	$tab++;

	if (! empty($conf->ldap->enabled) && ! empty($conf->global->LDAP_CONTACT_ACTIVE))
	{
		$langs->load("ldap");

		$head[$tab][0] = DOL_URL_ROOT.'/contact/ldap.php?id='.$object->id;
		$head[$tab][1] = $langs->trans("LDAPCard");
		$head[$tab][2] = 'ldap';
		$tab++;
	}
    $head[$tab][0] = DOL_URL_ROOT.'/cclinico/agenda.php?id='.$object->id;
    $head[$tab][1] = "Agenda";
    $head[$tab][2] = 'agenda';
    $tab++;
	
	$head[$tab][0] = DOL_URL_ROOT.'/cclinico/perso.php?id='.$object->id;
	$head[$tab][1] = "Expediente/Consultas";
	$head[$tab][2] = 'perso';
	$tab++;

    

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$tab,'contact');

    // Notes

    //$nbNote = (empty($object->note_private)?0:1)+(empty($object->note_public)?0:1);

    // Notes
    if (empty($conf->global->MAIN_DISABLE_NOTES_TAB)) {
        $nbNote = (empty($object->note_private)?0:1)+(empty($object->note_public)?0:1);
        $head[$tab][0] = DOL_URL_ROOT.'/cclinico/note.php?id='.$object->id;
        $head[$tab][1] = $langs->trans("Note");
        if($nbNote > 0) $head[$tab][1].= ' <span class="badge">'.$nbNote.'</span>';
        $head[$tab][2] = 'note';
        $tab++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    $modulepart='cclinico';
    $upload_dir = $conf->$modulepart->dir_output."/files/Paciente-".$object->id.'/documents';
    $nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
    $head[$tab][0] = DOL_URL_ROOT.'/cclinico/document.php?id='.$object->id;
    $head[$tab][1] = $langs->trans("Documents");
    if($nbFiles > 0) $head[$tab][1].= ' <span class="badge">'.$nbFiles.'</span>';
    $head[$tab][2] = 'documents';
    $tab++;



    /*$head[$tab][0] = DOL_URL_ROOT.'/cclinico/consultas.php?id='.$object->id;
    $head[$tab][1] = "Consultas";
    if($nbNote > 0) $head[$tab][1].= ' <span class="badge">Consultas</span>';
    $head[$tab][2] = 'consultas';
    $tab++;*/
    
    /*
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    $upload_dir = $conf->societe->dir_output . "/contact/" . dol_sanitizeFileName($object->ref);
    $nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
    $head[$tab][0] = DOL_URL_ROOT.'/contact/document.php?id='.$object->id;
    $head[$tab][1] = $langs->trans("Documents");
    if($nbFiles > 0) $head[$tab][1].= ' <span class="badge">'.$nbFiles.'</span>';
    $head[$tab][2] = 'documents';
    $tab++;*/
    
    // Info
    $head[$tab][0] = DOL_URL_ROOT.'/cclinico/info.php?id='.$object->id;
	$head[$tab][1] = $langs->trans("Info");
	$head[$tab][2] = 'info';
	
	$tab++;
	
	complete_head_from_modules($conf,$langs,$object,$head,$tab,'contact','remove');

	return $head;
}

function pacientes_prepare_head2(Pacientes $object)
{
    global $langs, $conf, $user;

    $tab = 0;
    $head = array();

    $head[$tab][0] = DOL_URL_ROOT.'/cclinico/estadisticas.php?action=mes';
    $head[$tab][1] = "Mes";
    $head[$tab][2] = 'mes';
    $tab++;
    $head[$tab][0] = DOL_URL_ROOT.'/cclinico/estadisticas.php?action=pacientes';
    $head[$tab][1] = "Pacientes";
    $head[$tab][2] = 'pacientes';
    $tab++;


    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);                                                   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$tab,'contact');

    // Notes    
    $tab++;
    
    complete_head_from_modules($conf,$langs,$object,$head,$tab,'contact','remove');

    return $head;
}

function mascara_referencia($conf,$type=0,$object="")
{
   $module=(! empty($conf->global->SOCIETE_CODECONSULTA_ADDON)?$conf->global->SOCIETE_CODECONSULTA_ADDON:'mod_codeconsultas_monkey');
    if ($type==1) {
        $module="mod_codeconsultas_monkey";
    }
    if (substr($module, 0, 15) == 'mod_codeconsultas_' && substr($module, -3) == 'php')
    {
        $module = substr($module, 0, dol_strlen($module)-4);
    }
    $dirsociete=array_merge(array('./core/modules/'),$conf->modules_parts['societe']);
    foreach ($dirsociete as $dirroot)
    {
        $res=dol_include_once("cclinico/".$dirroot.$module.'.php');
        if ($res) break;
    }
    //echo $module;
    $modCodeClient = new $module;
    // Load object modCodeFournisseur
    $module=(! empty($conf->global->SOCIETE_CODECONSULTA_ADDON)?$conf->global->SOCIETE_CODECONSULTA_ADDON:'mod_codeclient_leopard');
    if (substr($module, 0, 15) == 'mod_codeconsultas_' && substr($module, -3) == 'php')
    {
        $module = substr($module, 0, dol_strlen($module)-4);
    }
    $tmpcode=$modCodeClient->getNextValue($object,$type);

    return $tmpcode;
}

function print_actions_filter2($form, $canedit, $status, $year, $month, $day, $showbirthday, $filtera, $filtert, $filterd, $pid, $socid, $action, $showextcals=array(), $actioncode='', $usergroupid='', $excludetype='', $resourceid=0)
{
    global $conf, $user, $langs, $db, $hookmanager;
    global $begin_h, $end_h, $begin_d, $end_d;

    $langs->load("companies");

    // Filters
    print '<form name="listactionsfilter" class="listactionsfilter" action="' . $_SERVER["PHP_SELF"] . '" method="get">';
    print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
    print '<input type="hidden" name="year" value="' . $year . '">';
    print '<input type="hidden" name="month" value="' . $month . '">';
    print '<input type="hidden" name="day" value="' . $day . '">';
    print '<input type="hidden" name="action" value="' . $action . '">';
    print '<input type="hidden" name="showbirthday" value="' . $showbirthday . '">';

    print '<div class="fichecenter">';

    if (! empty($conf->browser->phone)) print '<div class="fichehalfleft">';
    else print '<table class="nobordernopadding" width="100%"><tr><td class="borderright">';

    print '<table class="nobordernopadding">';

    if ($canedit)
    {
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
        print $langs->trans("ActionsToDoBy").' &nbsp; ';
        print '</td><td class="maxwidthonsmartphone" style="padding-bottom: 2px; padding-right: 4px;">';
        print $form->select_dolusers($filtert, 'usertodo', 1, '', ! $canedit, '', '', 0, 0, 0, '', 0, '', 'maxwidth300');
        if (empty($conf->dol_optimize_smallscreen)) print ' &nbsp; '.$langs->trans("or") . ' '.$langs->trans("ToUserOfGroup").' &nbsp; ';
        print $form->select_dolgroups($usergroupid, 'usergroup', 1, '', ! $canedit);
        print '</td></tr>';

        if ($conf->resource->enabled)
        {
            include_once DOL_DOCUMENT_ROOT . '/resource/class/html.formresource.class.php';
            $formresource=new FormResource($db);
            
            // Resource
            print '<tr>';
            print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
            print $langs->trans("Resource");
            print ' &nbsp;</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px; padding-right: 4px;">';
            print $formresource->select_resource_list($resourceid, "resourceid", '', 1, 0, 0, null, '', 2);
            print '</td></tr>';
        }
        
        include_once DOL_DOCUMENT_ROOT . '/core/class/html.formactions.class.php';
        $formactions=new FormActions($db);

        // Type
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
        print $langs->trans("Type");
        print ' &nbsp;</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px; padding-right: 4px;">';
        $multiselect=0;
        if (! empty($conf->global->MAIN_ENABLE_MULTISELECT_TYPE))     // We use an option here because it adds bugs when used on agenda page "peruser" and "list"
        {
            $multiselect=(!empty($conf->global->AGENDA_USE_EVENT_TYPE));
        }
        print $formactions->select_type_actions($actioncode, "actioncode", $excludetype, (empty($conf->global->AGENDA_USE_EVENT_TYPE)?1:0), 0, $multiselect);
        print '</td></tr>';
    }

    if (! empty($conf->societe->enabled) && $user->rights->societe->lire)
    {
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
        print $langs->trans("ThirdParty").' &nbsp; ';
        print '</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px;">';
        print $form->select_company($socid, 'socid', '', 1);
        print '</td></tr>';
    }

    if (! empty($conf->projet->enabled) && $user->rights->projet->lire)
    {
        require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
        $formproject=new FormProjets($db);

        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px;">';
        print $langs->trans("Project").' &nbsp; ';
        print '</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px;">';
        $formproject->select_projects($socid?$socid:-1, $pid, 'projectid', 0);
        print '</td></tr>';
    }

    if ($canedit)
    {
        // Status
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
        print $langs->trans("Status");
        print ' &nbsp;</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px; padding-right: 4px;">';
        $formactions->form_select_status_action('formaction',$status,1,'status',1,2);
        print '</td></tr>';
    }

    if ($canedit && $action == 'show_peruser')
    {
        // Filter on hours
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">'.$langs->trans("VisibleTimeRange").'</td>';
        print "<td class='nowrap maxwidthonsmartphone'>";
        print '<div class="ui-grid-a"><div class="ui-block-a">';
        print '<input type="number" class="short" name="begin_h" value="'.$begin_h.'" min="0" max="23">';
        if (empty($conf->dol_use_jmobile)) print ' - ';
        else print '</div><div class="ui-block-b">';
        print '<input type="number" class="short" name="end_h" value="'.$end_h.'" min="1" max="24">';
        if (empty($conf->dol_use_jmobile)) print ' '.$langs->trans("H");
        print '</div></div>';
        print '</td></tr>';

        // Filter on days
        print '<tr>';
        print '<td class="nowrap">'.$langs->trans("VisibleDaysRange").'</td>';
        print "<td class='nowrap maxwidthonsmartphone'>";
        print '<div class="ui-grid-a"><div class="ui-block-a">';
        print '<input type="number" class="short" name="begin_d" value="'.$begin_d.'" min="1" max="7">';
        if (empty($conf->dol_use_jmobile)) print ' - ';
        else print '</div><div class="ui-block-b">';
        print '<input type="number" class="short" name="end_d" value="'.$end_d.'" min="1" max="7">';
        print '</div></div>';
        print '</td></tr>';
    }
    if (! empty($conf->cclinico->enabled) )
    {
        $consultas = new Consultas($db);
        print '<tr>';
        print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
        print $langs->trans("Code14").' &nbsp; ';
        print '</td><td class="nowrap maxwidthonsmartphone" style="padding-bottom: 2px;">';
        print $consultas->select_dolpacientes(GETPOST("paciente"), 'paciente', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '</td></tr>';
    }

    // Hooks
    $parameters = array('canedit'=>$canedit, 'pid'=>$pid, 'socid'=>$socid);
    $reshook = $hookmanager->executeHooks('searchAgendaFrom', $parameters, $object, $action); // Note that $action and $object may have been

    print '</table>';

    if (! empty($conf->browser->phone)) print '</div>';
    else print '</td>';

    if (! empty($conf->browser->phone)) print '<div class="fichehalfright">';
    else print '<td align="center" valign="middle" class="nowrap">';

    print '<table><tr><td align="center">';
    print '<div class="formleftzone">';
    print '<input type="submit" class="button" style="min-width:120px" name="refresh" value="' . $langs->trans("Refresh") . '">';
    print '</div>';
    print '</td></tr>';
    print '</table>';

    if (! empty($conf->browser->phone)) print '</div>';
    else print '</td></tr></table>';

    print '</div>'; // Close fichecenter
    print '<div style="clear:both"></div>';

    print '</form>';
}