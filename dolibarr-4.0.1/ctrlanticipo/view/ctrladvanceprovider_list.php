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
 *   	\file       ctrlanticipo/ctrladvanceprovider_list.php
 *		\ingroup    ctrlanticipo
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-08 18:32
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
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');
dol_include_once('/multiCurrency/class/MultiCurrency.class.php');

// Load traductions files requiredby by page
$langs->load("ctrlanticipo@ctrlanticipo");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_ref               =GETPOST('search_ref','alpha');
$search_concept_advance   =GETPOST('search_concept_advance','alpha');
$search_import            =GETPOST('search_import','alpha');
$search_total_import      =GETPOST('search_total_import','alpha');
$search_note_public       =GETPOST('search_note_public','alpha');
$search_note_private      =GETPOST('search_note_private','alpha');
$search_statut            =GETPOST('search_statut');
$search_fk_user_author    =GETPOST('search_fk_user_author','int');
$search_fk_user_modif     =GETPOST('search_fk_user_modif','int');
$search_fk_user_valid     =GETPOST('search_fk_user_valid','int');
$search_fk_soc            =GETPOST('search_fk_soc','alpha');
$search_fk_user_applicant =GETPOST('search_fk_user_applicant','alpha');
$search_fk_paymen         =GETPOST('search_fk_paymen','int');
$search_fk_project        =GETPOST('search_fk_project','int');
$search_fk_tva            =GETPOST('search_fk_tva','int');
$search_fk_mcurrency      =GETPOST('search_fk_mcurrency','int');
$search_type_advance      =GETPOST('search_type_advance','int');

$search_date_advance      =dol_mktime(0,0,0,GETPOST("search_date_advancemonth"),GETPOST("search_date_advanceday"),GETPOST("search_date_advanceyear"));

$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit     = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page      = GETPOST('page','int');

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_ref               ="";
	$search_concept_advance   ="";
	$search_import            ="";
	$search_total_import      ="";
	$search_note_public       ="";
	$search_note_private      ="";
	$search_statut            ="";
	$search_fk_user_author    ="";
	$search_fk_user_modif     ="";
	$search_fk_user_valid     ="";
	$search_fk_soc            ="";
	$search_fk_user_applicant ="";
	$search_fk_paymen         ="";
	$search_fk_project        ="";
	$search_fk_tva            ="";
	$search_fk_mcurrency      ="";
	$search_type_advance      ="";
	$search_date_advance      ="";

}




if ($page == -1) { 
	$page = 0; 
}

$offset   = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
    $socid = $user->societe_id;
	accessforbidden();
}

if ($user->rights->ctrlanticipo->ctrlanticipo2->read <>1)
	accessforbidden();


// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('ctrladvanceproviderlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('ctrlanticipo');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Ctrladvanceprovider($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.ref'               =>array('label'=>$langs->trans("ctrl_ref"), 'checked'=>1),
't.date_advance'      =>array('label'=>$langs->trans("ctrl_date_advance"), 'checked'=>0, 'position'=>500),
't.fk_soc'            =>array('label'=>$langs->trans("ctrl_fk_soc"), 'checked'=>1),
't.fk_user_applicant' =>array('label'=>$langs->trans("ctrl_fk_user_applicant"), 'checked'=>1),
't.concept_advance'   =>array('label'=>$langs->trans("ctrl_concept_advance"), 'checked'=>1),
't.total_import'      =>array('label'=>$langs->trans("ctrl_total_import"), 'checked'=>0),
't.type_advance'      =>array('label'=>$langs->trans("ctrl_type_advance"), 'checked'=>0),
't.statut'            =>array('label'=>$langs->trans("ctrl_statut"), 'checked'=>0)

);
if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
	$array                         =array('label'=>$langs->trans("ctrl_multi_cash"), 'checked'=>1);
	$arrayfields['t.fk_mcurrency'] =$array;
}



// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
   foreach($extrafields->attribute_label as $key => $val) 
   {
       $arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
   }
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($_POST['cancel']) { $action='list'; $massaction=''; }

$parameters =array();
$reshook    =$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';




if (empty($reshook))
{
    // Mass actions. Controls on number of lines checked
    $maxformassaction=1000;
    if (! empty($massaction) && count($toselect) < 1)
    {
        $error++;
        setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
    }
    if (! $error && count($toselect) > $maxformassaction)
    {
        setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
        $error++;
    }
    
	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/ctrlanticipo/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now      =dol_now();

$form     =new Form($db);

$help_url ='';
$title    = $langs->trans('ctrl_titre_list');
llxHeader('', $title, $help_url);

// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


$sql = "SELECT";
$sql.= " t.rowid,";
		$sql .= " t.tms,";
		$sql .= " t.ref,";
		$sql .= " t.concept_advance,";
		$sql .= " t.import,";
		$sql .= " t.total_import,";
		$sql .= " t.note_public,";
		$sql .= " t.note_private,";
		$sql .= " t.statut,";
		$sql .= " t.date_advance,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_modif,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_user_applicant,";
		$sql .= " t.fk_paymen,";
		$sql .= " t.fk_project,";
		$sql .= " t.fk_tva,";
		$sql .= " t.type_advance,";
		$sql .= " t.fk_mcurrency";

		


// Add fields for extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks

$parameters =array();
$reshook    =$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql        .=$hookmanager->resPrint;
$sql        .=" FROM llx_ctrl_advance_provider as t";
$sql        .=" left join ".MAIN_DB_PREFIX."societe as r on r.rowid=t.fk_soc";
$sql        .=" left join ".MAIN_DB_PREFIX."user as n on n.rowid=t.fk_user_applicant";

if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) 
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."ctrl_advance_provider_extrafields as ef on (u.rowid = ef.fk_object)";

if ($user->rights->ctrlanticipo->ctrlanticipo3->readothers <>1)
	$sql .=  ' WHERE t.fk_user_applicant = '.$user->id;
else
	$sql .=  ' WHERE 1= 1';
$sql.=' and t.statut!=6 ';



if ($search_ref) $sql             .= natural_search("ref",$search_ref);
if ($search_concept_advance) $sql .= natural_search("concept_advance",$search_concept_advance);
if ($search_date_advance) $sql    .= natural_search("date_advance",dol_print_date($search_date_advance,'%Y-%m-%d'));


if ($search_concept_advance) $sql     .= natural_search("concept_advance",$search_concept_advance);
if ($search_import) $sql              .= natural_search("import",$search_import);
if ($search_total_import) $sql        .= natural_search("total_import",$search_total_import);
if ($search_note_public) $sql         .= natural_search("note_public",$search_note_public);
if ($search_note_private) $sql        .= natural_search("note_private",$search_note_private);



if (strlen($search_statut)>0 && $search_statut!=-1  ){ 
	$sql.=' and t.statut='.$search_statut;
}



if ($search_fk_user_author>0) $sql    .= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_modif>0) $sql     .= natural_search("fk_user_modif",$search_fk_user_modif);
if ($search_fk_user_valid>0) $sql     .= natural_search("fk_user_valid",$search_fk_user_valid);
if ($search_fk_soc){
	$sql             .= " and r.nom like '%".$search_fk_soc."%' ";
} 
if ($search_fk_user_applicant){
	$sql             .= " and concat(n.firstname,' ',n.lastname) like '%".$search_fk_user_applicant."%' ";
} 
if ($search_fk_paymen>0) $sql         .= natural_search("fk_paymen",$search_fk_paymen);
if ($search_fk_project>0) $sql        .= natural_search("fk_project",$search_fk_project);
if ($search_fk_tva>0) $sql            .= natural_search("fk_tva",$search_fk_tva);
if ($search_type_advance>0) $sql      .= natural_search("type_advance",$search_type_advance);
if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
	if ($search_fk_mcurrency>0) $sql.= natural_search("fk_mcurrency",$search_fk_mcurrency);
}

//echo $sql;
if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);
// Add where from extra fields
foreach ($search_array_options as $key => $val)
{
    $crit=$val;
    $tmpkey=preg_replace('/search_options_/','',$key);
    $typ=$extrafields->attribute_type[$tmpkey];
    $mode=0;
    if (in_array($typ, array('int','double'))) $mode=1;    // Search on a numeric
    if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit))) 
    {
        $sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
    }
}
// Add where from hooks
$parameters =array();
$reshook    =$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql        .=$hookmanager->resPrint;
$sql        .=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}	

$sql.= $db->plimit($limit+1, $offset);


dol_syslog($script_file, LOG_DEBUG);

$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

	if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
	if ($search_date_advance != '') $params.= '&amp;search_date_advance='.urlencode($search_ref);

	if ($search_concept_advance != '') $params.= '&amp;search_concept_advance='.urlencode($search_concept_advance);
	if ($search_import != '') $params.= '&amp;search_import='.urlencode($search_import);
	if ($search_total_import != '') $params.= '&amp;search_total_import='.urlencode($search_total_import);
	if ($search_note_public != '') $params.= '&amp;search_note_public='.urlencode($search_note_public);
	if ($search_note_private != '') $params.= '&amp;search_note_private='.urlencode($search_note_private);
	if ($search_statut != '') $params.= '&amp;search_statut='.urlencode($search_statut);
	if ($search_fk_user_author != '') $params.= '&amp;search_fk_user_author='.urlencode($search_fk_user_author);
	if ($search_fk_user_modif != '') $params.= '&amp;search_fk_user_modif='.urlencode($search_fk_user_modif);
	if ($search_fk_user_valid != '') $params.= '&amp;search_fk_user_valid='.urlencode($search_fk_user_valid);
	if ($search_fk_soc != '') $params.= '&amp;search_fk_soc='.urlencode($search_fk_soc);
	if ($search_fk_user_applicant != '') $params.= '&amp;search_fk_user_applicant='.urlencode($search_fk_user_applicant);
	if ($search_fk_paymen != '') $params.= '&amp;search_fk_paymen='.urlencode($search_fk_paymen);
	if ($search_fk_project != '') $params.= '&amp;search_fk_project='.urlencode($search_fk_project);
	if ($search_fk_tva != '') $params.= '&amp;search_fk_tva='.urlencode($search_fk_tva);
	if ($search_type_advance != '') $params.= '&amp;search_type_advance='.urlencode($search_type_advance);


	if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
		if ($search_fk_mcurrency != '') $params.= '&amp;search_fk_mcurrency='.urlencode($search_fk_mcurrency);
	}
	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {

        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 



	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	   print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
    $moreforfilter = '';

    
	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
		$parameters =array();
		$reshook    =$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    print '</div>';
	}

	$varpage        =empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
	$selectedfields =$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
	
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    // 
	if (! empty($arrayfields['t.ref']['checked'])) 
			print_liste_field_titre($langs->trans("ctrl_ref"),$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.date_advance']['checked']))
		 print_liste_field_titre($langs->trans("ctrl_date_advance"),$_SERVER['PHP_SELF'],'t.date_advance','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.fk_soc']['checked'])) 
		print_liste_field_titre($langs->trans("ctrl_fk_soc"),$_SERVER['PHP_SELF'],'t.fk_soc','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.fk_user_applicant']['checked']) && $user->rights->ctrlanticipo->ctrlanticipo3->readothers) 
		print_liste_field_titre($langs->trans("ctrl_fk_user_applicant"),$_SERVER['PHP_SELF'],'t.fk_user_applicant','',$params,'',$sortfield,$sortorder);
	
	if (! empty($arrayfields['t.concept_advance']['checked'])) 
		print_liste_field_titre($langs->trans("ctrl_concept_advance_titre"),$_SERVER['PHP_SELF'],'t.concept_advance','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.total_import']['checked'])) 
		print_liste_field_titre($langs->trans("ctrl_total_import"),$_SERVER['PHP_SELF'],'t.total_import','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.statut']['checked'])) 
		print_liste_field_titre($langs->trans("ctrl_statut"),$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.type_advance']['checked'])) 
		print_liste_field_titre($langs->trans("ctrl_type_advance"),$_SERVER['PHP_SELF'],'t.type_advance','',$params,'',$sortfield,$sortorder);
	

	if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
		if (! empty($arrayfields['t.fk_mcurrency']['checked'])) print_liste_field_titre($langs->trans("ctrl_multi_cash"),$_SERVER['PHP_SELF'],'t.fk_mcurrency','',$params,'',$sortfield,$sortorder);
	}


    //if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$params,'',$sortfield,$sortorder);
    //if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$params,'',$sortfield,$sortorder);
	// Extra fields
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	{
	   foreach($extrafields->attribute_label as $key => $val) 
	   {
           if (! empty($arrayfields["ef.".$key]['checked'])) 
           {
				$align=$extrafields->getAlignFlag($key);
				print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$param,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
           }
	   }
	}
    // Hook fields
	$parameters =array('arrayfields'=>$arrayfields);
	$reshook    =$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
	// 
	if (! empty($arrayfields['t.ref']['checked'])){
		print '
		<td class="liste_titre">
			<input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10">
		</td>';
	} 
	if (! empty($arrayfields['t.date_advance']['checked'])){
		print '<td class="liste_titre" style="width:130px !important;">';
			print ($form->select_date(
			    $search_date_advance, 'search_date_advance', 0, 0, 1, "", 1, 0, 0, 0, '', '', ''
			));
		print '</td>';
	}

	if (! empty($arrayfields['t.fk_soc']['checked'])){
		print '<td class="liste_titre">';
			print '<input type="text" class="flat" name="search_fk_soc" value="'.$search_fk_soc.'" size="10">';
			//print ($form- >select_thirdparty($search_fk_soc, 'search_fk_soc', 'fournisseur = 1', '', ''));
		print '</td>';
	}
	if (! empty($arrayfields['t.fk_user_applicant']['checked'])  && $user->rights->ctrlanticipo->ctrlanticipo3->readothers ){
		print '<td class="liste_titre">';
			print '<input type="text" class="flat" name="search_fk_user_applicant" value="'.$search_fk_user_applicant.'" size="10">';
		print '</td>';
	}
	if (! empty($arrayfields['t.concept_advance']['checked'])){
		print '<td class="liste_titre"><input type="text" class="flat" name="search_concept_advance" value="'.$search_concept_advance.'" size="10"></td>';
	}

	if (! empty($arrayfields['t.total_import']['checked'])) 
		print '<td class="liste_titre"><input type="text" class="flat" name="search_total_import" value="'.$search_total_import.'" size="10"></td>';

	if (! empty($arrayfields['t.statut']['checked'])){
		print '<td class="liste_titre">';

			advance_statut($search_statut,"search_statut");
		print '</td>';
	}
	if (! empty($arrayfields['t.type_advance']['checked'])){
		print '
		<td class="liste_titre">';
			print '<select  id="search_type_advance" name="search_type_advance" data-role="none">';
				if ($search_type_advance==-1 || empty($search_type_advance)) {
					print '<option value="-1" selected>&nbsp;</option>';
				}else{
					print '<option value="-1" >&nbsp;</option>';
				}
				if ($search_type_advance==1) {
					print '<option value="1" selected>'.$langs->trans("ctrl_type_ext_prov").'</option>';
				}else{
					print '<option value="1">'.$langs->trans("ctrl_type_ext_prov").'</option>';
				}
				if ($search_type_advance==2) {
					print '<option value="2" selected>'.$langs->trans("ctrl_type_viat").'</option>';
				}else{
					print '<option value="2">'.$langs->trans("ctrl_type_viat").'</option>';
				}

            
		print ' </select>
		</td>';
	}




	
	if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
		if (! empty($arrayfields['t.fk_mcurrency']['checked'])){
			print '<td class="liste_titre" >';
			print select_multidivisa($search_fk_mcurrency,'search_fk_mcurrency' , 1, '', 0, '', '', 0, 0, -1, '', 0, '', '', 0,1);
			print '</td>';
		}
	}


	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	{
        foreach($extrafields->attribute_label as $key => $val) 
        {
            if (! empty($arrayfields["ef.".$key]['checked']))
            {
                $align=$extrafields->getAlignFlag($key);
                $typeofextrafield=$extrafields->attribute_type[$key];
                print '<td class="liste_titre'.($align?' '.$align:'').'">';
            	if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')))
				{
				    $crit=$val;
    				$tmpkey=preg_replace('/search_options_/','',$key);
    				$searchclass='';
    				if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
    				if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
    				print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
				}
                print '</td>';
            }
        }
	}
    // Fields from hook
	$parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
    if (! empty($arrayfields['t.datec']['checked']))
    {
        // Date creation
        print '<td class="liste_titre">';
        print '</td>';
    }
    if (! empty($arrayfields['t.tms']['checked']))
    {
        // Date modification
        print '<td class="liste_titre">';
        print '</td>';
    }

	print '<td class="liste_titre" align="right">';
    $searchpitco=$form->showFilterAndCheckAddButtons(0);
    print $searchpitco;
    print '</td>';
	print '</tr>'."\n";
        
    //aqui
	$i=0;
	$var=true;
	$totalarray=array();
    while ($i < min($num, $limit))
    {
        $obj = $db->fetch_object($resql);
        if ($obj)
        {
            $var = !$var;
            
            // Show here line of result
            print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST
            if (! empty($arrayfields['t.ref']['checked'])){
            	$advance=new Ctrladvanceprovider($db);
            	$advance->fetch($obj->rowid);
            	print '<td>'.$advance->getNomUrl(0).'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.date_advance']['checked'])){

            	print '<td>'.(empty($obj->date_advance)?"S/F":dol_print_date($obj->date_advance,"%d/%m/%Y")).'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.fk_soc']['checked'])){
            	$soc=new Societe($db);
            	$soc->fetch($obj->fk_soc);
            	print '<td>'.$soc->getNomUrl(0).'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.fk_user_applicant']['checked']) && $user->rights->ctrlanticipo->ctrlanticipo3->readothers){
				$soc =new User($db);
            	$soc->fetch($obj->fk_user_applicant);
            	print '<td>'.$soc->getNomUrl(0).'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.concept_advance']['checked'])){
            	$label=$obj->concept_advance;
            	print '<td><a class="classfortooltip" style="text-decoration: none;" title="'.$label.'" >'.substr($obj->concept_advance,0,10).'</a></td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.total_import']['checked'])) {
            	$label=
				print '<td>'.substr(price($obj->total_import),0,10).'</a></td>';
    		    if (! $i) $totalarray['nbfield']++;
			}
            if (! empty($arrayfields['t.statut']['checked'])){

            	print '<td align="left">'.img_picto($langs->trans('ctrl_action_statut'.$obj->statut),'statut'.(($obj->statut==5)?7:$obj->statut))."  ".$langs->trans('ctrl_action_statut'.$obj->statut).'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.type_advance']['checked'])){

            	print '
            	<td align="left">';
            		if ($obj->type_advance==1) {
            			print $langs->trans("ctrl_type_ext_prov");
            		}else{
            			print $langs->trans("ctrl_type_viat");
            		}
            	print '
            	</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
            	if (! empty($arrayfields['t.fk_mcurrency']['checked'])){
            		$sql_mon = "SELECT DISTINCT rowid,code,label as lastname,unicode";
			        $sql_mon.= " FROM llx_multidivisa_divisas as u WHERE rowid=".$obj->fk_mcurrency;
			        $sql_mon.= " ORDER BY label ASC";
			        
			        $result_mon=$db->query($sql_mon);

			        if ($db->num_rows($result_mon)>0) {
			        	$obj_mon = $db->fetch_object($result_mon);
			        	print '<td>'.$obj_mon->lastname.'</td>';
			        }else{
			        	print '<td></td>';
			        }

	            	
	    		    if (! $i) $totalarray['nbfield']++;
	    		}
        	}
            

            
            
            if (! empty($arrayfields['t.field1']['checked'])) 
            {
                print '<td>'.$obj->field1.'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.field2']['checked'])) 
            {
                print '<td>'.$obj->field2.'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
        	// Extra fields
    		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
    		{
    		   foreach($extrafields->attribute_label as $key => $val) 
    		   {
    				if (! empty($arrayfields["ef.".$key]['checked'])) 
    				{
    					print '<td';
    					$align=$extrafields->getAlignFlag($key);
    					if ($align) print ' align="'.$align.'"';
    					print '>';
    					$tmpkey='options_'.$key;
    					print $extrafields->showOutputField($key, $obj->$tmpkey, '', 1);
    					print '</td>';
    		            if (! $i) $totalarray['nbfield']++;
    				}
    		   }
    		}
            // Fields from hook
    	    $parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
    		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
            print $hookmanager->resPrint;
        	// Date creation
            if (! empty($arrayfields['t.datec']['checked']))
            {
                print '<td align="center">';
                print dol_print_date($db->jdate($obj->date_advance), 'dayhour');
                print '</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            // Date modification
            if (! empty($arrayfields['t.tms']['checked']))
            {
                print '<td align="center">';
                print dol_print_date($db->jdate($obj->date_update), 'dayhour');
                print '</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            // Status
            /*
            if (! empty($arrayfields['u.statut']['checked']))
            {
    		  $userstatic->statut=$obj->statut;
              print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
            }*/

            // Action column
            print '<td></td>';
            if (! $i) $totalarray['nbfield']++;

            print '</tr>';
        }
        $i++;
    }
    
    $db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";
	
	$db->free($result);
}
else
{
    $error++;
    dol_print_error($db);
}


// End of page
llxFooter();
$db->close();
