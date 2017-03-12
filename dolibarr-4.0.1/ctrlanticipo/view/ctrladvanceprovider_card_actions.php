<?php
/* Copyright (C) 2001-2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011      Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012      Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010           Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2013           Florian Henry		 <florian.henry@open-concept.pro>
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
 *   \file       htdocs/contact/note.php
 *   \brief      Tab for notes on contact
 *   \ingroup    societe
 */

$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
    $res = @include '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');
include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php'; // Must be include, not includ_once
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
$id = GETPOST('id','int');
$saction = GETPOST('saction');
$action = GETPOST('action');
$confirm = GETPOST('confirm');

$langs->load("companies");
$langs->load("ctrlanticipo");
$param = '&saction=files';
// Security check

if ($user->societe_id) $id=$user->societe_id;
//$result = restrictedArea($user, 'contact', $id, 'socpeople&societe');

$object = new Ctrladvanceprovider($db);
if ($id > 0) $object->fetch($id);
$permission=$user->rights->ctrlanticipo->ctrlanticipo1->createmodify;  // Used by the include of actions_setnotes.inc.php
$permissionnote=$user->rights->ctrlanticipo->ctrlanticipo1->createmodify;

$upload_dir=$conf->user->dir_output."/".$object->ref;

include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php'; // Must be include, not includ_once

include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';
$now=dol_now();


$form = new Form($db);

llxHeader('',$langs->trans("Documents"), $help_url);

if ($id > 0)
{

    //notas
    if ($action=='note' || $action=='editnote_public' || $action=='setnote_public' || $action=='editnote_private' || $action=='setnote_private') {
        if (! empty($conf->notification->enabled)) $langs->load("mails");

        $head = advance_prepare_head($object);

        dol_fiche_head($head, 'note', $langs->trans("ctrl_advance_note"), 0, 'payment');


        print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

        
        $linkback = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_list.php">'.$langs->trans("BackToList").'</a>';
        $societe=new Societe($db);
        $societe->fetch($object->fk_soc);
        dol_banner_tab($societe, 'id', $linkback, 0, 'rowid', 'ref', '');
        
        print '<div class="fichecenter">';
        
        print '<div class="underbanner clearboth"></div>';
        print '<table class="border centpercent">';

        // Civility
        print '<tr><td class="titlefield">'.$langs->trans("ctrl_view_credit_amount_tit").'</td>';
        print '<td align="center" >'.$object->getNomUrl(0).'</td>';
        print '<td>'.$object->import." ";
            if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                $sql='SELECT a.label FROM llx_multidivisa_divisas as a WHERE a.rowid='.$object->fk_mcurrency;
                $resql=$db->query($sql);
                if ($resql) {
                    $num = $db->num_rows($resql);
                    if ($num>0) {
                        $objp = $db->fetch_object($resql);
                        $label=$objp->label;
                        print $objp->label." ".$langs->trans("ctrl_view_without_iva");
                    }
                }
            }
        print '</td>';
        print '</tr>';


        print "</table>";

        print '<div>';
        
        print '<br>';

        $cssclass='titlefield';
        include DOL_DOCUMENT_ROOT.'/core/tpl/notes.tpl.php';


        dol_fiche_end();
    }
    if ($action=='log') {

        $head = advance_prepare_head($object);

        dol_fiche_head($head, 'info', $langs->trans("ctrl_advance_note"), 0, 'payment');
        $linkback = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_list.php">'.$langs->trans("BackToList").'</a>';
        print '<table class="border" width="100%">';
        print '<tr><td class="titlefield">'.$langs->trans('Ref').'</td><td colspan="3">';
        print $form->showrefnav($object, 'ref', $linkback, 0, 'ref', 'ref', '');
        print '</td></tr>';

        print '</table>';

        print '<br>';

        print '<table width="100%"><tr><td>';

        dol_print_object_info($object);
        print '</td></tr></table>';

        print '</div>';

        llxFooter();
    }
    if ($saction=='files' || isset($_POST['sendit']) || (empty($saction) && empty($action))) {
        $head = advance_prepare_head($object);

        dol_fiche_head($head, 'documents', $langs->trans("ctrl_advance_note"), 0, 'payment');
        // Construit liste des fichiers
        $filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);


        $totalsize=0;
        foreach($filearray as $key => $file)
        {
            $totalsize+=$file['size'];
        }

        $linkback = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_list.php">'.$langs->trans("BackToList").'</a>';
        
        $societe=new Societe($db);
        $societe->fetch($object->fk_soc);
        dol_banner_tab($societe, 'id', $linkback, 0, 'rowid', 'ref', '');

        print '<div class="fichecenter">';
            print '<div class="underbanner clearboth"></div>';
                print '<table class="border centpercent">';
                    print '<tr><td class="titlefield">'.$langs->trans("ctrl_view_credit_amount_tit").'</td>';
                    print '<td align="left" >'.$object->getNomUrl(0).'</td>';
                    print '<td>'.$object->import." ";
                        if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                            $sql='SELECT a.label FROM llx_multidivisa_divisas as a WHERE a.rowid='.$object->fk_mcurrency;
                            $resql=$db->query($sql);
                            if ($resql) {
                                $num = $db->num_rows($resql);
                                if ($num>0) {
                                    $objp = $db->fetch_object($resql);
                                    $label=$objp->label;
                                    print $objp->label." ".$langs->trans("ctrl_view_without_iva");
                                }
                            }
                        }
                    print '</td>';
                    print '</tr>';

                    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';
                    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';
                print '</table>';
            print '</div>';
        print '</div>';
        
        $modulepart = 'userphoto';
        $permission = $user->rights->ctrlanticipo->ctrlanticipo1->createmodify;
        include DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
    }
    
}

llxFooter();
$db->close();
