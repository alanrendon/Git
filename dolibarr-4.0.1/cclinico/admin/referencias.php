<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011-2012 Juanjo Menent        <jmenent@2byte.es>
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
 *	\file       htdocs/societe/admin/societe.php
 *	\ingroup    company
 *	\brief      Third party module setup page
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

$langs->load("admin");
$langs->load('other');

$action=GETPOST('action','alpha');
$value=GETPOST('value','alpha');
$prod=GETPOST('prod','int');

if (!$user->admin) accessforbidden();



/*
 * Actions
 */

if ($action == 'add_prod')
{

	if (GETPOST("add")) {
		if (dolibarr_set_const($db, "CLINICO_PROD",$prod,'chaine',0,'',$conf->entity) > 0)
		{
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	if (GETPOST("redit")) {

		$prod="0";
		if (dolibarr_set_const($db, "CLINICO_PROD",$prod,'chaine',0,'',$conf->entity) > 0)
		{
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	
}

if ($action == 'setcodeclient')
{
	if (dolibarr_set_const($db, "SOCIETE_CODECONSULTA_ADDON",$value,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

if ($action == 'setcodecompta')
{
	if (dolibarr_set_const($db, "SOCIETE_CODECOMPTA_ADDON",$value,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

if ($action == 'updateoptions')
{
	if (GETPOST('COMPANY_USE_SEARCH_TO_SELECT'))
	{
		$companysearch = GETPOST('activate_COMPANY_USE_SEARCH_TO_SELECT','alpha');
		$res = dolibarr_set_const($db, "COMPANY_USE_SEARCH_TO_SELECT", $companysearch,'chaine',0,'',$conf->entity);
		if (! $res > 0) $error++;
		if (! $error)
	    {
		    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	    }
	    else
	    {
		    setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}

	if (GETPOST('CONTACT_USE_SEARCH_TO_SELECT'))
	{
		$contactsearch = GETPOST('activate_CONTACT_USE_SEARCH_TO_SELECT','alpha');
		$res = dolibarr_set_const($db, "CONTACT_USE_SEARCH_TO_SELECT", $contactsearch,'chaine',0,'',$conf->entity);
		if (! $res > 0) $error++;
		if (! $error)
		{
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else
		{
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}
}

// Define constants for submodules that contains parameters (forms with param1, param2, ... and value1, value2, ...)
if ($action == 'setModuleOptions')
{
	$post_size=count($_POST);

	$db->begin();

	for($i=0;$i < $post_size;$i++)
    {
    	if (array_key_exists('param'.$i,$_POST))
    	{
    		$param=GETPOST("param".$i,'alpha');
    		$value=GETPOST("value".$i,'alpha');
    		if ($param) $res = dolibarr_set_const($db,$param,$value,'chaine',0,'',$conf->entity);
	    	if (! $res > 0) $error++;
    	}
    }
	if (! $error)
    {
        $db->commit();
	    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        $db->rollback();
	    setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

// Activate a document generator module
if ($action == 'set')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$type='company';
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
	$sql.= " VALUES ('".$db->escape($value)."','".$type."',".$conf->entity.", ";
	$sql.= ($label?"'".$db->escape($label)."'":'null').", ";
	$sql.= (! empty($scandir)?"'".$db->escape($scandir)."'":"null");
	$sql.= ")";

	$resql=$db->query($sql);
	if (! $resql) dol_print_error($db);
}

// Disable a document generator module
if ($action== 'del')
{
	$type='company';
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql.= " WHERE nom='".$db->escape($value)."' AND type='".$type."' AND entity=".$conf->entity;
	$resql=$db->query($sql);
	if (! $resql) dol_print_error($db);
}

// Define default generator
if ($action == 'setdoc')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$db->begin();

	dolibarr_set_const($db, "COMPANY_ADDON_PDF",$value,'chaine',0,'',$conf->entity);

	// On active le modele
	$type='company';
	$sql_del = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql_del.= " WHERE nom = '".$db->escape(GETPOST('value','alpha'))."'";
	$sql_del.= " AND type = '".$type."'";
	$sql_del.= " AND entity = ".$conf->entity;
    dol_syslog("societe.php ".$sql);
	$result1=$db->query($sql_del);

	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
	$sql.= " VALUES ('".$db->escape($value)."', '".$type."', ".$conf->entity.", ";
	$sql.= ($label?"'".$db->escape($label)."'":'null').", ";
	$sql.= (! empty($scandir)?"'".$db->escape($scandir)."'":"null");
	$sql.= ")";
    dol_syslog("societe.php", LOG_DEBUG);
	$result2=$db->query($sql);
	if ($result1 && $result2)
	{
		$db->commit();
	}
	else
	{
	    $db->rollback();
	}
}

//Activate ProfId
if ($action == 'setprofid')
{
	$status = GETPOST('status','alpha');

	$idprof="SOCIETE_IDPROF".$value."_UNIQUE";
	if (dolibarr_set_const($db, $idprof,$status,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

//Activate Set ref in list
if ($action=="setaddrefinlist") {
	$setaddrefinlist = GETPOST('value','int');
	$res = dolibarr_set_const($db, "SOCIETE_ADD_REF_IN_LIST", $setaddrefinlist,'yesno',0,'',$conf->entity);
	if (! $res > 0) $error++;
	if (! $error)
	{
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	}
	else
	{
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}


//Activate ProfId mandatory
if ($action == 'setprofidmandatory')
{
	$status = GETPOST('status','alpha');

	$idprof="SOCIETE_IDPROF".$value."_MANDATORY";
	if (dolibarr_set_const($db, $idprof,$status,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

//Activate ProfId invoice mandatory
if ($action == 'setprofidinvoicemandatory')
{
	$status = GETPOST('status','alpha');

	$idprof="SOCIETE_IDPROF".$value."_INVOICE_MANDATORY";
	if (dolibarr_set_const($db, $idprof,$status,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

//Set hide closed customer into combox or select
if ($action == 'sethideinactivethirdparty')
{
	$status = GETPOST('status','alpha');

	if (dolibarr_set_const($db, "COMPANY_HIDE_INACTIVE_IN_COMBOBOX",$status,'chaine',0,'',$conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

/*
 * 	View
 */


function select_dol_products($selected='', $htmlname='prod', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
{
    global $conf,$user,$langs,$db;

    // If no preselected user defined, we take current user
    if ((is_numeric($selected) && ($selected < -2 || empty($selected))) && empty($conf->global->SOCIETE_DISABLE_DEFAULT_SALESREPRESENTATIVE)) $selected=$user->id;

    $excludeUsers=null;
    $includeUsers=null;

    // Permettre l'exclusion d'utilisateurs
    if (is_array($exclude))	$excludeUsers = implode("','",$exclude);
    // Permettre l'inclusion d'utilisateurs
    if (is_array($include))	$includeUsers = implode("','",$include);
	else if ($include == 'hierarchy')
	{
		// Build list includeUsers to have only hierarchy
		$userid=$user->id;
		$include=array();
		if (empty($user->users) || ! is_array($user->users)) $user->get_full_tree();
		foreach($user->users as $key => $val)
		{
			if (preg_match('/_'.$userid.'/',$val['fullpath'])) $include[]=$val['id'];
		}
		$includeUsers = implode("','",$include);
		//var_dump($includeUsers);exit;
		//var_dump($user->users);exit;
	}

    $out='';

    // On recherche les utilisateurs
    $sql = "SELECT DISTINCT u.rowid, u.ref, u.label,u.price_ttc,u.tva_tx,u.duration,u.fk_product_type";
    /*if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
    {
        $sql.= ", e.label";
    }*/
    $sql.= " FROM ".MAIN_DB_PREFIX ."product as u";



    $sql.= " ORDER BY u.ref ASC";

    $resql=$db->query($sql);

    if ($resql)
    {

        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {

       		// Enhance with select2
       		$nodatarole='';
	        if ($conf->use_javascript_ajax)
	        {
	            include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	            $comboenhancement = ajax_combobox($htmlname);
	            $out.=$comboenhancement;
	            $nodatarole=($comboenhancement?' data-role="none"':'');
	        }

            $out.= '<select class="flat minwidth200'.($morecss?' '.$morecss:'').'" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
            if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
			if ($show_every) $out.= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

            $userstatic=new User($db);

            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);

                $disableline='';
                if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=($enableonlytext?$enableonlytext:'1');

                if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
                {
                    $out.= '<option value="'.$obj->rowid.'"';
                    if ($disableline) $out.= ' disabled';
                    $out.= ' selected>';
                }
                else
                {
                    $out.= '<option value="'.$obj->rowid.'"';
                    if ($disableline) $out.= ' disabled';
                    $out.= '>';
                }

                $out.= $obj->ref." - ".$obj->label." - $".price($obj->price_ttc,0,'',0,0,0);

                if (empty($obj->duration)) {
                	if ($obj->tva_tx>0) {
	                	$out.=" ".$langs->trans("TTC");
	                }else{
	                	$out.=" ".$langs->trans("HT");
	                }
                }else{
                	$our_value=substr($obj->duration,0,dol_strlen($obj->duration)-1);
                	$outdurationunit=substr($obj->duration,-1);
                	
                	$da=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
		            if (isset($da[$outdurationunit]))
		            {
		                $out.= " - ".$our_value." ".$langs->trans($da[$outdurationunit]);
		            }
                }
                


                $out.= '</option>';

                $i++;
            }
        }
        else
        {
            $out.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" disabled>';
            $out.= '<option value="">'.$langs->trans("None").'</option>';
        }
        $out.= '</select>';
    }
    else
    {
        dol_print_error($db);
    }
    return $out;
}





clearstatcache();

$form=new Form($db);

$help_url='EN:Module Third Parties setup|FR:Paramétrage_du_module_Tiers|ES:Configuración_del_módulo_terceros';
llxHeader('','Configuración del módulo clínico','Configuración del módulo clínico');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre("Configuración del módulo de generación y control de los códigos",$linkback,'title_setup');


$head=array(
    array(DOL_URL_ROOT.'/cclinico/admin/cclinicsetuppage.php', 'Atributos adicionales (Pacientes)', 'pacientes' ),
    array(DOL_URL_ROOT.'/cclinico/admin/consultas_extra.php', 'Atributos adicionales (Consultas)', 'consultas' ),
    array(DOL_URL_ROOT.'/cclinico/admin/referencias.php', 'Miscelánea', 'referencias' )
);

dol_fiche_head($head,'referencias', 'Control Clínico', 0, 'globe');

$dirsociete=array_merge(array('/cclinico/core/modules/'),$conf->modules_parts['societe']);

// Module to manage customer/supplier code

print load_fiche_titre("Módulo de generación y control de los códigos de consultas (Validadas / Borradores)",'','');

print '<table class="noborder" width="100%">'."\n";
print '<tr class="liste_titre">'."\n";
print '  <td>'.$langs->trans("Name").'</td>';
print '  <td>'.$langs->trans("Description").'</td>';
print '  <td>'.$langs->trans("Example").'</td>';
print '  <td align="center" width="80">'.$langs->trans("Status").'</td>';
print '  <td align="center" width="60">'.$langs->trans("ShortInfo").'</td>';
print "</tr>\n";

$var = true;
foreach ($dirsociete as $dirroot)
{
	$dir = dol_buildpath($dirroot,0);
	//echo $dir;
    $handle = @opendir($dir);
    if (is_resource($handle))
    {
    	// Loop on each module find in opened directory
    	while (($file = readdir($handle))!==false)
    	{
    		if (substr($file, 0, 18) == 'mod_codeconsultas_' && substr($file, -3) == 'php')
    		{

    			$file = substr($file, 0, dol_strlen($file)-4);
    			
    			try {
        			dol_include_once($dirroot.$file.'.php');
    			}catch(Exception $e){
    			    dol_syslog($e->getMessage(), LOG_ERR);
    			}

    			$modCodeTiers = new $file;

    			// Show modules according to features level
    			if ($modCodeTiers->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
    			if ($modCodeTiers->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

    			$var = !$var;
    			print '<tr '.$bc[$var].'>'."\n";
    			print '<td width="140" >'.$modCodeTiers->name.'</td>'."\n";
    			print '<td>'.$modCodeTiers->info($langs).'</td>'."\n";
    			print '<td class="nowrap" align="center">'.$modCodeTiers->getExample($langs).'</td>'."\n";

    			if ($conf->global->SOCIETE_CODECONSULTA_ADDON == "$file")
    			{
    				print '<td align="center">'."\n";
    				print img_picto($langs->trans("Activated"),'switch_on');
    				print "</td>\n";
    			}
    			else
    			{
    				$disabled = false;
    				if (! empty($conf->multicompany->enabled) && (is_object($mc) && ! empty($mc->sharings['referent']) && $mc->sharings['referent'] == $conf->entity) ? false : true);
    				print '<td align="center">';
    				if (! $disabled) print '<a href="'.$_SERVER['PHP_SELF'].'?action=setcodeclient&value='.$file.'">';
    				print img_picto($langs->trans("Disabled"),'switch_off');
    				if (! $disabled) print '</a>';
    				print '</td>';
    			}

    			print '<td align="center">';
    			$s=$modCodeTiers->getToolTipp($langs,null,-1);
    			print $form->textwithpicto('',$s,1);
    			print '</td>';

    			print '</tr>';
    		}
    	}
    	closedir($handle);
    }
}


print '</table>';
print '<br>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>Otros Comentarios (PDF)</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";
print '</tr>';

$var=true;

// Mail required for members
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>Mostrar dentro del formato de recetas medicas en consultas a pacientes, el campo (Otros Comentarios)</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('CONSULTA_REQUIRED');
}

print '</td></tr>';

print '</table>';


print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
	print '<input type="hidden" name="action" value="add_prod">';
	print '<br>';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>Producto/Servicio vinculado a las facturas</td>';
	print '<td align="center" width="20">&nbsp;</td>';
	print '<td align="center" width="100">Asignar</td>'."\n";
	print '</tr>';

	$var=true;

	// Mail required for members
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	if ($conf->global->CLINICO_PROD>0) {
		print '<td>Productos/Servicio:  '.select_dol_products($conf->global->CLINICO_PROD, 'prod', 0, '',1).'</td>';
	}else{
		print '<td>Productos/Servicio:  '.select_dol_products($conf->global->CLINICO_PROD).'</td>';
	}

	print '<td align="center" width="20">';
	print '</td>';

	print '<td align="center" width="100">';

	if ($conf->global->CLINICO_PROD>0) {
		print ' <input type="submit" class="button"  style="border-radius:0px !important;" name="redit" value="Eliminar">';
	}else{
		print ' <input type="submit" class="button"  style="border-radius:0px !important;" name="add" value="Asignar">';
	}
	print '</td></tr>';

	print '</table>';
print '</form>';



print "<br>";


dol_fiche_end();


llxFooter();

$db->close();
