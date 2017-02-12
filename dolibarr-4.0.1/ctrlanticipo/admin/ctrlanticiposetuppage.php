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
 *  \file       htdocs/societe/admin/societe.php
 *  \ingroup    company
 *  \brief      Third party module setup page
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';

$langs->load("admin");
$langs->load('other');

$action=GETPOST('action','alpha');
$value=GETPOST('value','alpha');

if (!$user->admin) accessforbidden();



if ($action == 'setcodeclient')
{
    if (dolibarr_set_const($db, "SOCIETE_CODECTRLANTICIPO_ADDON",$value,'chaine',0,'',$conf->entity) > 0)
    {
        header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

if ($action == 'setcodeformat')
{
    if (dolibarr_set_const($db, "CTRLANTICIPO_FORMAT",$value,'chaine',0,'',$conf->entity) > 0)
    {
        setEventMessages($langs->trans("ctrl_format_asign_succes"), null, 'mesgs');
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
 *  View
 */

clearstatcache();

$form=new Form($db);


llxHeader('',$langs->trans("ctrl_header"),$langs->trans("ctrl_header"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans("ctrl_conf_titre_prin"),$linkback,'title_setup');


$head=array(
    array($_SERVER["PHP_SELF"], $langs->trans("ctrl_ref_admin_advance"), 'advance_ref' ),
     array(DOL_URL_ROOT.'/ctrlanticipo/admin/ctrlanticiposetuppage_refund.php', $langs->trans("ctrl_ref_admin_refund"), 'refund_ref' ),
     array(DOL_URL_ROOT.'/ctrlanticipo/admin/ctrlanticiposetuppage_payment.php', $langs->trans("ctrl_ref_admin_payment_ref"), 'payment_ref' )
);

dol_fiche_head($head,'advance_ref', $langs->trans("ctrl_menu_list"), 0, 'service');

$dirsociete=array_merge(array('/ctrlanticipo/core/modules/'),$conf->modules_parts['societe']);

// Module to manage customer/supplier code

print load_fiche_titre($langs->trans("ctrl_conf_titre"),'','');

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
        $i=0;
        while (($file = readdir($handle))!==false )
        {
            if (substr($file, 0, 18) == 'mod_codeanticipos_' && substr($file, -3) == 'php' && strpos($file, '_bronan') === false)
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

                if ($conf->global->SOCIETE_CODECTRLANTICIPO_ADDON == "$file")
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



dol_fiche_end();

$dirsociete=array_merge(array('/ctrlanticipo/view/models/'),$conf->modules_parts['societe']);

dol_fiche_head();
print load_fiche_titre($langs->trans("ctrl_format_titre_default"),'','');
print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';

    print '<input type="hidden" name="action" value="setcodeformat">';

    print '<table class="noborder" width="100%">'."\n";
        print '<tr class="liste_titre">'."\n";
            print '<td>'.$langs->trans("ctrl_settings_titre").'</td>';
            print '<td align="center" width="80">'.$langs->trans("ctrl_models").'</td>';
            print '<td align="center" width="60"></td>';
        print "</tr>";
        print '<tr>';
            print '<td>'.$langs->trans("ctrl_desc_models").'</td>';
            print '<td align="center" width="80">';
                print '<select name="value">';
                foreach ($dirsociete as $dirroot){
                    $dir = dol_buildpath($dirroot,0);
                    $handle = @opendir($dir);
                    if (is_resource($handle))
                    {
                        while (($file = readdir($handle))!==false )
                        {
                            
                            if (substr($file, 0, 12) == 'ctrl_format_' && substr($file, -3) == 'php'){
                                $file = substr($file, 0, dol_strlen($file)-4);
                                $file_label=substr($file, 12);
                                if ($conf->global->CTRLANTICIPO_FORMAT==$file) {
                                    print '<option value="'.$file.'" selected>'.$file_label .'</option>';
                                }else{
                                    print '<option value="'.$file.'">'.$file_label .'</option>';
                                }
                                
                            }
                        }
                    }
                }
                print '</select>';
            print '</td>';
        print '  
        <td align="center" width="60">
            <input type="submit" class="button" value="Modificar">
        </td>';
        print "</tr>\n";

    print '</table>';
print '</form>';

dol_fiche_end();

llxFooter();

$db->close();
