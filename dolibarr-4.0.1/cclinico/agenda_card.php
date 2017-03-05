<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon TOSSER         <simon@kornog-computing.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2013 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2013      Florian Henry        <florian.henry@open-concept.pro>
 * Copyright (C) 2014      Cedric GROSS         <c.gross@kreiz-it.fr>
 * Copyright (C) 2015	   Alexandre Spangaro   <aspangaro.dolibarr@gmail.com>
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
 *       \file       htdocs/comm/action/card.php
 *       \ingroup    agenda
 *       \brief      Page for event card
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/agenda.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/cactioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
if (! empty($conf->projet->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
dol_include_once('/cclinico/class/pacientes.class.php');
$langs->load("companies");
$langs->load("commercial");
$langs->load("other");
$langs->load("bills");
$langs->load("orders");
$langs->load("agenda");

$action=GETPOST('action','alpha');
$cancel=GETPOST('cancel','alpha');
$backtopage=GETPOST('backtopage','alpha');
$contactid=GETPOST('contactid','int');
$origin=GETPOST('origin','alpha');
$originid=GETPOST('originid','int');
$confirm = GETPOST('confirm', 'alpha');

$fulldayevent=GETPOST('fullday');
$datep=dol_mktime($fulldayevent?'00':GETPOST("aphour"), $fulldayevent?'00':GETPOST("apmin"), 0, GETPOST("apmonth"), GETPOST("apday"), GETPOST("apyear"));
$datef=dol_mktime($fulldayevent?'23':GETPOST("p2hour"), $fulldayevent?'59':GETPOST("p2min"), $fulldayevent?'59':'0', GETPOST("p2month"), GETPOST("p2day"), GETPOST("p2year"));

// Security check
$socid = GETPOST('socid','int');
$id = GETPOST('id','int');
$aid = GETPOST('aid','int');
if ($user->societe_id) $socid=$user->societe_id;

$error=GETPOST("error");
$donotclearsession=GETPOST('donotclearsession')?GETPOST('donotclearsession'):0;

$cactioncomm = new CActionComm($db);
$object = new ActionComm($db);
$contact = new Contact($db);
$extrafields = new ExtraFields($db);
$formfile = new FormFile($db);

$form = new Form($db);
$formfile = new FormFile($db);
$formactions = new FormActions($db);
$pacientes= new Pacientes($db);


if ($aid>0) {
	$pacientes->fetch($aid);
}


// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);



/*
 * Actions
 */
$listUserAssignedUpdated = false;
// Remove user to assigned list



// Add event
if ($action == 'add')
{
	$error=0;

    if (empty($backtopage))
    {
        if ($socid > 0) $backtopage = DOL_URL_ROOT.'/societe/agenda.php?socid='.$socid;
        else $backtopage=DOL_URL_ROOT.'/comm/action/index.php';
    }

    if ($contactid)
	{
		$result=$contact->fetch($contactid);
	}

	if ($cancel)
	{
		header("Location: ".$backtopage);
		exit;
	}

    $percentage=in_array(GETPOST('status'),array(-1,100))?GETPOST('status'):(in_array(GETPOST('complete'),array(-1,100))?GETPOST('complete'):GETPOST("percentage"));	// If status is -1 or 100, percentage is not defined and we must use status

    // Clean parameters
	$datep=dol_mktime($fulldayevent?'00':GETPOST("aphour"), $fulldayevent?'00':GETPOST("apmin"), 0, GETPOST("apmonth"), GETPOST("apday"), GETPOST("apyear"));
	$datef=dol_mktime($fulldayevent?'23':GETPOST("p2hour"), $fulldayevent?'59':GETPOST("p2min"), $fulldayevent?'59':'0', GETPOST("p2month"), GETPOST("p2day"), GETPOST("p2year"));

	// Check parameters
	if (! $datef && $percentage == 100)
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("DateEnd")), null, 'errors');
	}

	if (empty($conf->global->AGENDA_USE_EVENT_TYPE) && ! GETPOST('label'))
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Title")), null, 'errors');
	}

	// Initialisation objet cactioncomm
	if (! GETPOST('actioncode') > 0)	// actioncode is id
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Type")), null, 'errors');
	}
	else
	{
		$object->type_code = GETPOST('actioncode');
	}

	if (! $error)
	{
		// Initialisation objet actioncomm
		$object->priority = GETPOST("priority")?GETPOST("priority"):0;
		$object->fulldayevent = (! empty($fulldayevent)?1:0);
		$object->location = GETPOST("location");
		$object->label = trim(GETPOST('label'));
		$object->fk_element = GETPOST("fk_element");
		$object->elementtype = GETPOST("elementtype");
		if (! GETPOST('label'))
		{
			if (GETPOST('actioncode') == 'AC_RDV' && $contact->getFullName($langs))
			{
				$object->label = $langs->transnoentitiesnoconv("TaskRDVWith",$contact->getFullName($langs));
			}
			else
			{
				if ($langs->trans("Action".$object->type_code) != "Action".$object->type_code)
				{
					$object->label = $langs->transnoentitiesnoconv("Action".$object->type_code)."\n";
				}
				else $object->label = $cactioncomm->libelle;
			}
		}
		$object->fk_project = isset($_POST["projectid"])?$_POST["projectid"]:0;
		$object->datep = $datep;
		$object->datef = $datef;
		$object->percentage = $percentage;
		$object->duree=((float) (GETPOST('dureehour') * 60) + (float) GETPOST('dureemin')) * 60;

		$transparency=(GETPOST("transparency")=='on'?1:0);

		$listofuserid=array();
		if (! empty($_SESSION['assignedtouser'])) $listofuserid=json_decode($_SESSION['assignedtouser'], true);
		$i=0;
		foreach($listofuserid as $key => $value)
		{
			if ($i == 0)	// First entry
			{
				if ($value['id'] > 0) $object->userownerid=$value['id'];
				$object->transparency = $transparency;
			}

			$object->userassigned[$value['id']]=array('id'=>$value['id'], 'transparency'=>$transparency);

			$i++;
		}
	}

	if (! $error && ! empty($conf->global->AGENDA_ENABLE_DONEBY))
	{
		if (GETPOST("doneby") > 0) $object->userdoneid = GETPOST("doneby","int");
	}

	$object->note = trim($_POST["note"]);

	if (isset($_POST["contactid"])) $object->contact = $contact;

	if (GETPOST('socid','int') > 0)
	{
		$object->socid=GETPOST('socid','int');
		$object->fetch_thirdparty();

		$object->societe = $object->thirdparty;	// For backward compatibility
	}

	// Special for module webcal and phenix
	// TODO external modules
	if (! empty($conf->webcalendar->enabled) && GETPOST('add_webcal') == 'on') $object->use_webcal=1;
	if (! empty($conf->phenix->enabled) && GETPOST('add_phenix') == 'on') $object->use_phenix=1;

	// Check parameters
	if (empty($object->userownerid) && empty($_SESSION['assignedtouser']))
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ActionsOwnedBy")), null, 'errors');
	}
	if ($object->type_code == 'AC_RDV' && ($datep == '' || ($datef == '' && empty($fulldayevent))))
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("DateEnd")), null, 'errors');
	}

	if (! GETPOST('apyear') && ! GETPOST('adyear'))
	{
		$error++; $donotclearsession=1;
		$action = 'create';
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), null, 'errors');
	}

	// Fill array 'array_options' with data from add form
	$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
	if ($ret < 0) $error++;

	if (! $error)
	{
		$db->begin();

		// On cree l'action
		$idaction=$object->add($user);

		if ($idaction > 0)
		{
			if (! $object->error)
			{
				$pacientes->vincular_paciente_evento($idaction,GETPOST('consulta'));
				unset($_SESSION['assignedtouser']);

				$moreparam='';
				if ($user->id != $object->userownerid) $moreparam="usertodo=-1";	// We force to remove filter so created record is visible when going back to per user view.

				$db->commit();
				if (! empty($backtopage))
				{
					dol_syslog("Back to ".$backtopage.($moreparam?(preg_match('/\?/',$backtopage)?'&'.$moreparam:'?'.$moreparam):''));
					header("Location: ".$backtopage.($moreparam?(preg_match('/\?/',$backtopage)?'&'.$moreparam:'?'.$moreparam):''));
				}
				elseif($idaction)
				{
					header("Location: ".DOL_URL_ROOT.'/comm/action/card.php?id='.$idaction.($moreparam?'&'.$moreparam:''));
				}
				else
				{
					header("Location: ".DOL_URL_ROOT.'/comm/action/index.php'.($moreparam?'?'.$moreparam:''));
				}
				exit;
			}
			else
			{
				// If error
				$db->rollback();
				$langs->load("errors");
				$error=$langs->trans($object->error);
				setEventMessages($error, null, 'errors');
				$action = 'create'; $donotclearsession=1;
			}
		}
		else
		{
			$db->rollback();
			setEventMessages($object->error, $object->errors, 'errors');
			$action = 'create'; $donotclearsession=1;
		}
	}
}




/*
 * View
 */

$help_url='EN:Module_Agenda_En|FR:Module_Agenda|ES:M&omodulodulo_Agenda';
llxHeader('',$langs->trans("Agenda"),$help_url);

if ($action == 'create' && !empty($aid))
{
	$contact = new Contact($db);

	if (GETPOST("contactid"))
	{
		$result=$contact->fetch(GETPOST("contactid"));
		if ($result < 0) dol_print_error($db,$contact->error);
	}

	dol_set_focus("#label");

    if (! empty($conf->use_javascript_ajax))
    {
        print "\n".'<script type="text/javascript">';
        print '$(document).ready(function () {
        			function setdatefields()
	            	{
	            		if ($("#fullday:checked").val() == null) {
	            			$(".fulldaystarthour").removeAttr("disabled");
	            			$(".fulldaystartmin").removeAttr("disabled");
	            			$(".fulldayendhour").removeAttr("disabled");
	            			$(".fulldayendmin").removeAttr("disabled");
	            			$("#p2").removeAttr("disabled");
	            		} else {
							$(".fulldaystarthour").prop("disabled", true).val("00");
							$(".fulldaystartmin").prop("disabled", true).val("00");
							$(".fulldayendhour").prop("disabled", true).val("23");
							$(".fulldayendmin").prop("disabled", true).val("59");
							$("#p2").removeAttr("disabled");
	            		}
	            	}
                    setdatefields();
                    $("#fullday").change(function() {
                        setdatefields();
                    });
                    $("#selectcomplete").change(function() {
                        if ($("#selectcomplete").val() == 100)
                        {
                            if ($("#doneby").val() <= 0) $("#doneby").val(\''.$user->id.'\');
                        }
                        if ($("#selectcomplete").val() == 0)
                        {
                            $("#doneby").val(-1);
                        }
                   });
                   $("#actioncode").change(function() {
                        if ($("#actioncode").val() == \'AC_RDV\') $("#dateend").addClass("fieldrequired");
                        else $("#dateend").removeClass("fieldrequired");
                   });
               })';
        print '</script>'."\n";
    }

	print '<form name="formaction" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="aid" value="'.$aid.'">';
	print '<input type="hidden" name="donotclearsession" value="1">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.($backtopage != '1' ? $backtopage : $_SERVER["HTTP_REFERER"]).'">';
	if (empty($conf->global->AGENDA_USE_EVENT_TYPE)) print '<input type="hidden" name="actioncode" value="'.dol_getIdFromCode($db, 'AC_OTH', 'c_actioncomm').'">';

	if (GETPOST("actioncode") == 'AC_RDV') print load_fiche_titre($langs->trans("AddActionRendezVous"));
	else print load_fiche_titre($langs->trans("AddAnAction"));

	dol_fiche_head();

	print '<table class="border" width="100%">';

	// Type of event
	if (! empty($conf->global->AGENDA_USE_EVENT_TYPE))
	{
		print '<tr><td  width="30%"><span class="fieldrequired">'.$langs->trans("Type").'</span></b></td><td>';
		$default=(empty($conf->global->AGENDA_USE_EVENT_TYPE_DEFAULT)?'':$conf->global->AGENDA_USE_EVENT_TYPE_DEFAULT);
		$formactions->select_type_actions(GETPOST("actioncode")?GETPOST("actioncode"):($object->type_code?$object->type_code:$default), "actioncode", "systemauto", 0, -1);
		print '</td></tr>';
	}
	print '
	<tr>
		<td  '.(empty($conf->global->AGENDA_USE_EVENT_TYPE)?' class="fieldrequired"':'').' >Paciente</td>
		<td>';
			print $pacientes->getNomUrl(0);
		print '
		</td>
	</tr>';
	// Title
	print '<tr><td '.(empty($conf->global->AGENDA_USE_EVENT_TYPE)?' class="fieldrequired"':'').'>'.$langs->trans("Title").'</td><td><input type="text" id="label" name="label" size="60" value="'.GETPOST('label').'"></td></tr>';

    // Full day
    print '<tr><td>'.$langs->trans("EventOnFullDay").'</td><td><input type="checkbox" id="fullday" name="fullday" '.(GETPOST('fullday')?' checked':'').'></td></tr>';

	// Date start
	$datep=($datep?$datep:$object->datep);
	if (GETPOST('datep','int',1)) $datep=dol_stringtotime(GETPOST('datep','int',1),0);
	print '<tr><td width="30%" class="nowrap"><span class="fieldrequired">'.$langs->trans("DateActionStart").'</span></td><td>';
	if (GETPOST("afaire") == 1) $form->select_date($datep,'ap',1,1,0,"action",1,1,0,0,'fulldayend');
	else if (GETPOST("afaire") == 2) $form->select_date($datep,'ap',1,1,1,"action",1,1,0,0,'fulldayend');
	else $form->select_date($datep,'ap',1,1,1,"action",1,1,0,0,'fulldaystart');
	print '</td></tr>';

	// Date end
	$datef=($datef?$datef:$object->datef);
    if (GETPOST('datef','int',1)) $datef=dol_stringtotime(GETPOST('datef','int',1),0);
	if (empty($datef) && ! empty($datep) && ! empty($conf->global->AGENDA_AUTOSET_END_DATE_WITH_DELTA_HOURS))
	{
		$datef=dol_time_plus_duree($datep, $conf->global->AGENDA_AUTOSET_END_DATE_WITH_DELTA_HOURS, 'h');
	}
	print '<tr><td><span id="dateend"'.(GETPOST("actioncode") == 'AC_RDV'?' class="fieldrequired"':'').'>'.$langs->trans("DateActionEnd").'</span></td><td>';
	if (GETPOST("afaire") == 1) $form->select_date($datef,'p2',1,1,1,"action",1,1,0,0,'fulldayend');
	else if (GETPOST("afaire") == 2) $form->select_date($datef,'p2',1,1,1,"action",1,1,0,0,'fulldayend');
	else $form->select_date($datef,'p2',1,1,1,"action",1,1,0,0,'fulldayend');
	print '</td></tr>';

	// Status
	print '<tr><td width="10%">'.$langs->trans("Status").' / '.$langs->trans("Percentage").'</td>';
	print '<td>';
	$percent=-1;
	if (isset($_GET['status']) || isset($_POST['status'])) $percent=GETPOST('status');
	else if (isset($_GET['percentage']) || isset($_POST['percentage'])) $percent=GETPOST('percentage');
	else
	{
		if (GETPOST('complete') == '0' || GETPOST("afaire") == 1) $percent='0';
		else if (GETPOST('complete') == 100 || GETPOST("afaire") == 2) $percent=100;
	}
	$formactions->form_select_status_action('formaction',$percent,1,'complete');
	print '</td></tr>';

    // Location
    if (empty($conf->global->AGENDA_DISABLE_LOCATION))
    {
		print '<tr><td>'.$langs->trans("Location").'</td><td colspan="3"><input type="text" name="location" size="50" value="'.(GETPOST('location')?GETPOST('location'):$object->location).'"></td></tr>';
    }

	// Assigned to
	print '<tr><td class="tdtop nowrap">'.$langs->trans("ActionAffectedTo").'</td><td>';
	$listofuserid=array();
	if (empty($donotclearsession))
	{
		$assignedtouser=GETPOST("assignedtouser")?GETPOST("assignedtouser"):(! empty($object->userownerid) && $object->userownerid > 0 ? $object->userownerid : $user->id);
		if ($assignedtouser) $listofuserid[$assignedtouser]=array('id'=>$assignedtouser,'mandatory'=>0,'transparency'=>$object->transparency);	// Owner first
		$_SESSION['assignedtouser']=json_encode($listofuserid);
	}
	else
	{
		if (!empty($_SESSION['assignedtouser']))
		{
			$listofuserid=json_decode($_SESSION['assignedtouser'], true);
		}
	}
	print '<div class="assignedtouser">';
	print $form->select_dolusers_forevent(($action=='create'?'add':'update'), 'assignedtouser', 1, '', 0, '', '', 0, 0, 0, 'AND u.statut != 0 AND u.entity='.$conf->entity); 
	print '</div>';

	if (in_array($user->id,array_keys($listofuserid))) 
	{
		print '<div class="myavailability">';
		print $langs->trans("MyAvailability").': <input id="transparency" type="checkbox" name="transparency"'.(((! isset($_GET['transparency']) && ! isset($_POST['transparency'])) || GETPOST('transparency'))?' checked':'').'> '.$langs->trans("Busy");
		print '</div>';
	}
	print '</td></tr>';

	// Realised by
	if (! empty($conf->global->AGENDA_ENABLE_DONEBY))
	{
		print '<tr><td class="nowrap">'.$langs->trans("ActionDoneBy").'</td><td>';
		print $form->select_dolusers(GETPOST("doneby")?GETPOST("doneby"):(! empty($object->userdoneid) && $percent==100?$object->userdoneid:0),'doneby',1);
		print '</td></tr>';
	}

	print '</table>';
	print '<br><br>';
	print '<table class="border" width="100%">';

	//paciente consultas
	print '<tr><td class="nowrap">Consulta</td><td>';
	print $pacientes->select_dolconsultas(GETPOST('consulta','int'), 'consulta', 1, 0, '', '', 0, 'minwidth200');
	print '</td></tr>';
	// Societe, contact
	print '<tr><td width="30%" class="nowrap">'.$langs->trans("ActionOnCompany").'</td><td>';
	if (GETPOST('socid','int') > 0)
	{
		$societe = new Societe($db);
		$societe->fetch(GETPOST('socid','int'));
		print $societe->getNomUrl(1);
		print '<input type="hidden" id="socid" name="socid" value="'.GETPOST('socid','int').'">';
	}
	else
	{

		$events=array();
		$events[]=array('method' => 'getContacts', 'url' => dol_buildpath('/core/ajax/contacts.php?showempty=1',1), 'htmlname' => 'contactid', 'params' => array('add-customer-contact' => 'disabled'));
		//For external user force the company to user company
		if (!empty($user->societe_id)) {
			print $form->select_thirdparty_list($user->societe_id,'socid','',1,1,0,$events);
		} else {
			print $form->select_thirdparty_list('','socid','',1,1,0,$events);
		}

	}
	print '</td></tr>';

	print '<tr><td class="nowrap">'.$langs->trans("ActionOnContact").'</td><td>';
	$form->select_contacts(GETPOST('socid','int'), GETPOST('contactid'), 'contactid', 1, '', '', 0, 'minwidth200');
	print '</td></tr>';



	// Project
	if (! empty($conf->projet->enabled))
	{
		$formproject=new FormProjets($db);

		// Projet associe
		$langs->load("projects");

		print '<tr><td>'.$langs->trans("Project").'</td><td>';

		$numproject=$formproject->select_projects((! empty($societe->id)?$societe->id:0),GETPOST("projectid")?GETPOST("projectid"):'','projectid');
		if ($numproject==0)
		{
			print ' &nbsp; <a href="'.DOL_URL_ROOT.'/projet/card.php?socid='.$societe->id.'&action=create">'.$langs->trans("AddProject").'</a>';
		}
		print '</td></tr>';
	}
	if(!empty($origin) && !empty($originid))
	{
		include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
		print '<tr><td>'.$langs->trans("LinkedObject").'</td>';
		print '<td colspan="3">'.dolGetElementUrl($originid,$origin,1).'</td></tr>';
		print '<input type="hidden" name="fk_element" size="10" value="'.GETPOST('originid').'">';
		print '<input type="hidden" name="elementtype" size="10" value="'.GETPOST('origin').'">';
		print '<input type="hidden" name="originid" size="10" value="'.GETPOST('originid').'">';
		print '<input type="hidden" name="origin" size="10" value="'.GETPOST('origin').'">';
	}

	if (GETPOST("datep") && preg_match('/^([0-9][0-9][0-9][0-9])([0-9][0-9])([0-9][0-9])$/',GETPOST("datep"),$reg))
	{
		$object->datep=dol_mktime(0,0,0,$reg[2],$reg[3],$reg[1]);
	}

	// Priority
	print '<tr><td class="nowrap">'.$langs->trans("Priority").'</td><td colspan="3">';
	print '<input type="text" name="priority" value="'.(GETPOST('priority')?GETPOST('priority'):($object->priority?$object->priority:'')).'" size="5">';
	print '</td></tr>';

    // Description
    print '<tr><td class="tdtop">'.$langs->trans("Description").'</td><td>';
    require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
    $doleditor=new DolEditor('note',(GETPOST('note')?GETPOST('note'):$object->note),'',180,'dolibarr_notes','In',true,true,$conf->fckeditor->enabled,ROWS_6,90);
    $doleditor->Create();
    print '</td></tr>';


    // Other attributes
    $parameters=array('id'=>$object->id);
    $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook


	if (empty($reshook) && ! empty($extrafields->attribute_label))
	{
		print $object->showOptionals($extrafields,'edit');
	}

	print '</table>';

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print "</form>";
}


llxFooter();

$db->close();
