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
 *   	\file       cclinico/consultas_list.php
 *		\ingroup    cclinico
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-15 06:53
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
dol_include_once('/cclinico/class/consultas.class.php');
dol_include_once('/cclinico/class/pacientes.class.php');
global $conf;
// Load traductions files requiredby by page
$langs->load("cclinico");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_Ref=GETPOST('search_Ref','alpha');
$search_code_client=GETPOST('search_code_client','alpha');
$search_fk_user_pacientes=GETPOST('search_fk_user_pacientes','alpha');


if (empty($_POST['search_date_consultation'])) {
	$date="";
}else{

	$date=dol_mktime(0,0,0,GETPOST("search_date_consultationmonth"),GETPOST("search_date_consultationday"),GETPOST("search_date_consultationyear"));
}

$search_date_consultation=$date;



$search_Type_consultation=GETPOST('search_Type_consultation','alpha');


$search_weight=GETPOST('search_weight','alpha');
$search_blood_pressure=GETPOST('search_blood_pressure','alpha');
$search_fk_user_med=GETPOST('search_fk_user_med','alpha');


$search_reason=GETPOST('search_reason','alpha');

$search_diagnostics=GETPOST('search_diagnostics','alpha');


$search_treatments=GETPOST('search_treatments','alpha');
$search_comments=GETPOST('search_comments','alpha');
$search_statut=GETPOST('search_statut','int');


$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
    $socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('consultaslist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('cclinico');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Consultas($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.Ref'=>array('label'=>"Ref", 'checked'=>1),
't.fk_user_pacientes'=>array('label'=>"Paciente", 'checked'=>1),
't.date_consultation'=>array('label'=>"Fecha Consulta", 'checked'=>1),
't.fk_user_med'=>array('label'=>"Médico", 'checked'=>1),




//'t.fk_user_creation'=>array('label'=>$langs->trans("Fieldfk_user_creation"), 'checked'=>1),
//'t.fk_user_validation'=>array('label'=>$langs->trans("Fieldfk_user_validation"), 'checked'=>1),
//'t.fk_user_close'=>array('label'=>$langs->trans("Fieldfk_user_close"), 'checked'=>1),
't.Type_consultation'=>array('label'=>"Tipo Consulta", 'checked'=>1),
//'t.weight'=>array('label'=>$langs->trans("Fieldweight"), 'checked'=>1),
//'t.blood_pressure'=>array('label'=>$langs->trans("Fieldblood_pressure"), 'checked'=>1),

't.reason'=>array('label'=>"Motivo Consulta", 'checked'=>1),
//'t.reason_detail'=>array('label'=>$langs->trans("Fieldreason_detail"), 'checked'=>1),
't.diagnostics'=>array('label'=>"Diagnostico", 'checked'=>1),
//'t.diagnostics_detail'=>array('label'=>$langs->trans("Fielddiagnostics_detail"), 'checked'=>1),
//'t.treatments'=>array('label'=>$langs->trans("Fieldtreatments"), 'checked'=>1),
//'t.comments'=>array('label'=>$langs->trans("Fieldcomments"), 'checked'=>1),
//'t.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),

    
    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
    //'t.datec'=>array('label'=>$langs->trans("DateCreation"), 'checked'=>0, 'position'=>500),
    //'t.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
't.statut'=>array('label'=>"Status", 'checked'=>1, 'position'=>1000),
);
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

$parameters=array();


include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{

	$search_Ref='';
	$search_code_client='';
	$search_fk_user_pacientes='';
	$search_date_consultation="";
	$search_fk_user_creation='';
	$search_fk_user_validation='';
	$search_fk_user_close='';
	$search_Type_consultation='';
	$search_weight='';
	$search_blood_pressure='';
	$search_fk_user_med='';
	$search_reason='';
	$search_diagnostics='';
	$search_diagnostics_detail='';
	$search_treatments='';
	$search_comments='';
	$search_statut='';
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}







/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('', 'Listado de consultas', '');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('MyModuleListTitle');

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

		$sql .= " t.Ref,";
		$sql .= " t.date_consultation,";
		$sql .= " t.date_creation,";
		$sql .= " t.fk_user_pacientes,";
		$sql .= " t.fk_user_creation,";
		$sql .= " t.date_validation,";
		$sql .= " t.fk_user_validation,";
		$sql .= " t.date_clos,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.Type_consultation,";
		$sql .= " t.weight,";
		$sql .= " t.blood_pressure,";
		$sql .= " t.fk_user_med,";
		$sql .= " t.reason,";
		$sql .= " t.reason_detail,";
		$sql .= " t.diagnostics,";
		$sql .= " t.diagnostics_detail,";
		$sql .= " t.treatments,";
		$sql .= " t.comments,";
		$sql .= " t.statut";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."consultas as t inner join llx_pacientes as u on u.rowid=t.fk_user_pacientes";

if (!empty($search_fk_user_med)) {
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as fm on t.fk_user_med=fm.rowid";
}

if (!empty($search_Type_consultation)) {
	$sql.= " LEFT JOIN llx_c_tipo_consulta as tc on t.Type_consultation=tc.rowid";
}

if (!empty($search_reason)) {
	$sql.= " LEFT JOIN llx_c_motivo_consulta as mc on t.reason=mc.rowid";
}
if (!empty($search_reason)) {
	$sql.= " LEFT JOIN llx_c_tipo_diagnostico as td on t.diagnostics=td.rowid";
}





if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)){
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."consultas_extrafields as ef on (u.rowid = ef.fk_object)";
	$sql.= " WHERE 1 = 1  AND t.entity=".$conf->entity." AND u.entity=".$conf->entity." ";
} else{
	$sql.= " WHERE 1 = 1  AND t.entity=".$conf->entity." AND u.entity=".$conf->entity." ";
}

//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";



if ($search_Ref) $sql.= natural_search("Ref",$search_Ref);
//if ($search_code_client) $sql.= natural_search("code_client",$search_code_client);
if ($search_fk_user_pacientes) $sql.= natural_search("concat(u.lastname, ' ',u.firstname)",$search_fk_user_pacientes);
if ($search_date_consultation) $sql.= natural_search("date_consultation",dol_print_date($search_date_consultation,'%Y-%m-%d'));

//if ($search_fk_user_creation) $sql.= natural_search("fk_user_creation",$search_fk_user_creation);
//if ($search_fk_user_validation) $sql.= natural_search("fk_user_validation",$search_fk_user_validation);
//if ($search_fk_user_close) $sql.= natural_search("fk_user_close",$search_fk_user_close);
if ($search_Type_consultation){
	$sql.=" and tc.description like '%".$search_Type_consultation."%'";
}
//if ($search_weight) $sql.= natural_search("weight",$search_weight);
//if ($search_blood_pressure) $sql.= natural_search("blood_pressure",$search_blood_pressure);
if ($search_fk_user_med){
	$sql.=" AND CONCAT(fm.lastname,' ',fm.firstname) LIKE '%".$search_fk_user_med."%'";
} 
if ($search_reason){
	$sql.=" and mc.description LIKE '%".$search_reason."%'";
}
if ($search_diagnostics){
	$sql.=" and td.description LIKE '%".$search_diagnostics."%'";
}


if ($search_statut) $sql.= natural_search("t.statut",$search_statut);
if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);


// Add where from extra fields
foreach ($search_array_options as $key => $val)
{
    $crit=$val;
    $tmpkey=preg_replace('/search_options_/','',$key);
    $typ=$extrafields->attribute_type[$tmpkey];
    $mode=0;
    if (in_array($typ, array('int'))) $mode=1;    // Search on a numeric
    if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit))) 
    {
        $sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
    }
}
// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}	

$sql.= $db->plimit($conf->liste_limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);


if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
	
if ($search_Ref != '') $params.= '&amp;search_Ref='.urlencode($search_Ref);
//if ($search_code_client != '') $params.= '&amp;search_code_client='.urlencode($search_code_client);
if ($search_fk_user_pacientes != '') $params.= '&amp;search_fk_user_pacientes='.urlencode($search_fk_user_pacientes);
if ($search_date_consultation != '') $params.= '&amp;search_date_consultation='.urlencode($search_date_consultation);



//if ($search_fk_user_creation != '') $params.= '&amp;search_fk_user_creation='.urlencode($search_fk_user_creation);
//if ($search_fk_user_validation != '') $params.= '&amp;search_fk_user_validation='.urlencode($search_fk_user_validation);
//if ($search_fk_user_close != '') $params.= '&amp;search_fk_user_close='.urlencode($search_fk_user_close);
if ($search_Type_consultation != '') $params.= '&amp;search_Type_consultation='.urlencode($search_Type_consultation);
//if ($search_weight != '') $params.= '&amp;search_weight='.urlencode($search_weight);
//if ($search_blood_pressure != '') $params.= '&amp;search_blood_pressure='.urlencode($search_blood_pressure);
if ($search_fk_user_med != '') $params.= '&amp;search_fk_user_med='.urlencode($search_fk_user_med);
if ($search_reason != '') $params.= '&amp;search_reason='.urlencode($search_reason);
//if ($search_reason_detail != '') $params.= '&amp;search_reason_detail='.urlencode($search_reason_detail);
if ($search_diagnostics != '') $params.= '&amp;search_diagnostics='.urlencode($search_diagnostics);
//if ($search_diagnostics_detail != '') $params.= '&amp;search_diagnostics_detail='.urlencode($search_diagnostics_detail);
//if ($search_treatments != '') $params.= '&amp;search_treatments='.urlencode($search_treatments);
//if ($search_comments != '') $params.= '&amp;search_comments='.urlencode($search_comments);

if ($search_statut != '') $params.= '&amp;search_statut='.urlencode($search_statut);
	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields

    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 
    
    print_barre_liste('Listado de consultas', $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_generic');
    

	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    print '</div>';
	}

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
	
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    
if (! empty($arrayfields['t.Ref']['checked'])) print_liste_field_titre("No Consulta",$_SERVER['PHP_SELF'],'t.Ref','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.code_client']['checked'])) print_liste_field_titre($arrayfields['t.code_client']['label'],$_SERVER['PHP_SELF'],'t.code_client','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_pacientes']['checked'])) print_liste_field_titre("Paciente",$_SERVER['PHP_SELF'],'t.fk_user_pacientes','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_consultation']['checked'])) print_liste_field_titre("Fecha Consulta",$_SERVER['PHP_SELF'],'t.date_consultation','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.fk_user_creation']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_creation']['label'],$_SERVER['PHP_SELF'],'t.fk_user_creation','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.fk_user_validation']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_validation']['label'],$_SERVER['PHP_SELF'],'t.fk_user_validation','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.fk_user_close']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_close']['label'],$_SERVER['PHP_SELF'],'t.fk_user_close','',$param,'',$sortfield,$sortorder);

//if (! empty($arrayfields['t.weight']['checked'])) print_liste_field_titre($arrayfields['t.weight']['label'],$_SERVER['PHP_SELF'],'t.weight','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.blood_pressure']['checked'])) print_liste_field_titre($arrayfields['t.blood_pressure']['label'],$_SERVER['PHP_SELF'],'t.blood_pressure','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_med']['checked'])) print_liste_field_titre("Médico",$_SERVER['PHP_SELF'],'t.fk_user_med','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.Type_consultation']['checked'])) print_liste_field_titre("Tipo Consulta",$_SERVER['PHP_SELF'],'t.Type_consultation','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.reason']['checked'])) print_liste_field_titre($arrayfields['t.reason']['label'],$_SERVER['PHP_SELF'],'t.reason','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.reason_detail']['checked'])) print_liste_field_titre($arrayfields['t.reason_detail']['label'],$_SERVER['PHP_SELF'],'t.reason_detail','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.diagnostics']['checked'])) print_liste_field_titre("Diagnostico",$_SERVER['PHP_SELF'],'t.diagnostics','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.diagnostics_detail']['checked'])) print_liste_field_titre($arrayfields['t.diagnostics_detail']['label'],$_SERVER['PHP_SELF'],'t.diagnostics_detail','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.treatments']['checked'])) print_liste_field_titre($arrayfields['t.treatments']['label'],$_SERVER['PHP_SELF'],'t.treatments','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.comments']['checked'])) print_liste_field_titre($arrayfields['t.comments']['label'],$_SERVER['PHP_SELF'],'t.comments','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre("Status",$_SERVER['PHP_SELF'],'t.statut','',$param,'',$sortfield,$sortorder);

    
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
	$parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($langs->trans("DateCreationShort"),$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($langs->trans("DateModificationShort"),$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
	
if (! empty($arrayfields['t.Ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_Ref" value="'.$search_Ref.'" size="10"></td>';
//if (! empty($arrayfields['t.code_client']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_client" value="'.$search_code_client.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_pacientes']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_pacientes" value="'.$search_fk_user_pacientes.'" size="10"></td>';
if (! empty($arrayfields['t.date_consultation']['checked'])){
	
	print '<td class="liste_titre">';
	$form->select_date($search_date_consultation,'search_date_consultation',0,0,1,"form_date",1,1,0,0,'');
	print '</td>';
}
if (! empty($arrayfields['t.fk_user_med']['checked'])){
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_fk_user_med" value="'.$search_fk_user_med.'" size="10">';
	print '</td>';
} 
//if (! empty($arrayfields['t.fk_user_creation']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_creation" value="'.$search_fk_user_creation.'" size="10"></td>';
//if (! empty($arrayfields['t.fk_user_validation']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_validation" value="'.$search_fk_user_validation.'" size="10"></td>';
//if (! empty($arrayfields['t.fk_user_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_close" value="'.$search_fk_user_close.'" size="10"></td>';
if (! empty($arrayfields['t.Type_consultation']['checked'])){
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_Type_consultation" value="'.$search_Type_consultation.'" size="10">';
	
	print '</td>';
} 
//if (! empty($arrayfields['t.weight']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_weight" value="'.$search_weight.'" size="10"></td>';
//if (! empty($arrayfields['t.blood_pressure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_blood_pressure" value="'.$search_blood_pressure.'" size="10"></td>';

if (! empty($arrayfields['t.reason']['checked'])){
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_reason" value="'.$search_reason.'" size="10">';
	print '</td>';
} 


//if (! empty($arrayfields['t.reason_detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_reason_detail" value="'.$search_reason_detail.'" size="10"></td>';
if (! empty($arrayfields['t.diagnostics']['checked'])){
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_diagnostics" value="'.$search_diagnostics.'" size="10">';
	print '</td>';

	
}
//if (! empty($arrayfields['t.diagnostics_detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_diagnostics_detail" value="'.$search_diagnostics_detail.'" size="10"></td>';
//if (! empty($arrayfields['t.treatments']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_treatments" value="'.$search_treatments.'" size="10"></td>';
//if (! empty($arrayfields['t.comments']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_comments" value="'.$search_comments.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])){
	print '<td class="liste_titre">';
	print '
	<select class="flat minwidth200 maxwidth300" id="search_statut" name="search_statut" data-role="none">';
		print '
	    <option value="" selected>&nbsp;</option>
	    ';
		if ($search_statut==0) {
		    print '
		    <option value="0" selected>&nbsp;Borrador</option>
		    ';
		}else{
			 print '
		    <option value="0" >&nbsp;Borrador</option>
		    ';
		}
		if ($search_statut==1) {
		    print '
		    <option value="1" selected>&nbsp;Validado</option>
		    ';
		}else{
			 print '
		    <option value="1" >&nbsp;Validado</option>
		    ';
		}
		if ($search_statut==2) {
		    print '
		    <option value="2" selected>&nbsp;(cerrado) Facturada</option>
		    ';
		}else{
			 print '
		    <option value="2" >&nbsp;(cerrado) Facturada</option>
		    ';
		}
		if ($search_statut==3) {
		    print '
		    <option value="3" selected>&nbsp;cerrada(consulta gratuita)</option>
		    ';
		}else{
			 print '
		    <option value="3" >&nbsp;cerrada(consulta gratuita)</option>
		    ';
		}

	print '</select>';
	print '</td>';
}

	
	// Extra fields
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	{
	   foreach($extrafields->attribute_label as $key => $val) 
	   {
			if (! empty($arrayfields["ef.".$key]['checked'])) print '<td class="liste_titre"></td>';
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


	
    // Action column
	print '<td class="liste_titre" align="right">';
	print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
	print '</tr>'."\n";
        
    
    $i = 0;
    while ($i < $num)
    {
        $obj = $db->fetch_object($resql);
        $i++;
        if ($obj)
        {
	            // You can use here results
	        print '<tr>';
	            
			if (! empty($arrayfields['t.Ref']['checked'])){
				print '<td >';
				$objsoc2 = new Consultas($db);
				$objsoc2->fetch( $obj->rowid);
				print $objsoc2->getNomUrl(1);
				print '</td>';
			} 
			//if (! empty($arrayfields['t.code_client']['checked'])) print '<td>'.$obj->code_client.'</td>';
			if (! empty($arrayfields['t.fk_user_pacientes']['checked'])){
				print '<td>';
				$contactstatic=new Pacientes($db);
				$contactstatic->fetch( $obj->fk_user_pacientes);
				print $contactstatic->getNomUrl(1,'', 0, 24, '',1);
				print '</td>';
			}


			if (! empty($arrayfields['t.date_consultation']['checked'])) print '<td>'.dol_print_date($obj->date_consultation,"%d/%m/%Y").'</td>';
			if (! empty($arrayfields['t.fk_user_med']['checked'])){
				$objsoc2 = new User($db);
				if ($obj->fk_user_med<1) {
					print '<td>';
					print "N/A";
					print '</td>';
				}else{
					$objsoc2->fetch($obj->fk_user_med);
					print '<td>';
					print $objsoc2->getNomUrl(1);
					print '</td>';
				}
				
			}

			if (! empty($arrayfields['t.Type_consultation']['checked'])){
				print '<td>'; 
				$resql1=$db->query("SELECT * FROM llx_c_tipo_consulta as a WHERE a.entity=".$conf->entity." AND a.active=1 AND a.rowid=".$obj->Type_consultation);

			    if ($resql1)
			    {
			        $num2 = $db->num_rows($resql1);
			        if ($num2)
			        {
			            $obj2 = $db->fetch_object($resql1);

			            $label=$obj2->description;
			    		print '<a class="classfortooltip" style="text-decoration: none;" title="'.$label.'" >'.dol_trunc($obj2->description,10).'</a>';
			            
			        }
			    }
				print '</td>';
			}
			if (! empty($arrayfields['t.reason']['checked'])){
				print '<td>'; 
				$resql1=$db->query("SELECT * FROM llx_c_motivo_consulta as a WHERE a.entity=".$conf->entity." AND a.active=1 AND a.rowid=".$obj->reason);
			    if ($resql1)
			    {
			        $num2 = $db->num_rows($resql1);
			        if ($num2)
			        {
			            $obj3 = $db->fetch_object($resql1);

			            $label=$obj3->description;
			    		print '<a class="classfortooltip" style="text-decoration: none;" title="'.$label.'" >'.dol_trunc($obj3->description,10).'</a>';
			        }
			    }

				print '</td>';
			}
			if (! empty($arrayfields['t.diagnostics']['checked'])){
				print '<td>'; 
				$resql1=$db->query("SELECT * FROM llx_c_tipo_diagnostico as a WHERE a.entity=".$conf->entity." AND a.active=1 AND a.rowid=".$obj->diagnostics);
			    if ($resql1)
			    {
			        $num2 = $db->num_rows($resql1);
			        if ($num2)
			        {
			        	$obj4 = $db->fetch_object($resql1);
			            $label=$obj4->description;
			    		print '<a class="classfortooltip" style="text-decoration: none;" title="'.$label.'" >'.dol_trunc($obj4->description,10).'</a>';
			            
			        }
			    }






				print '</td>';
			}


	//if (! empty($arrayfields['t.fk_user_creation']['checked'])) print '<td>'.$obj->fk_user_creation.'</td>';
	//if (! empty($arrayfields['t.fk_user_validation']['checked'])) print '<td>'.$obj->fk_user_validation.'</td>';
	//if (! empty($arrayfields['t.fk_user_close']['checked'])) print '<td>'.$obj->fk_user_close.'</td>';
	//if (! empty($arrayfields['t.Type_consultation']['checked'])) print '<td>'.$obj->Type_consultation.'</td>';
	//if (! empty($arrayfields['t.weight']['checked'])) print '<td>'.$obj->weight.'</td>';
	//if (! empty($arrayfields['t.blood_pressure']['checked'])) print '<td>'.$obj->blood_pressure.'</td>';
	//if (! empty($arrayfields['t.fk_user_med']['checked'])) print '<td>'.$obj->fk_user_med.'</td>';

	//if (! empty($arrayfields['t.reason_detail']['checked'])) print '<td>'.$obj->reason_detail.'</td>';

	//if (! empty($arrayfields['t.diagnostics_detail']['checked'])) print '<td>'.$obj->diagnostics_detail.'</td>';
	//if (! empty($arrayfields['t.treatments']['checked'])) print '<td>'.$obj->treatments.'</td>';
	//if (! empty($arrayfields['t.comments']['checked'])) print '<td>'.$obj->comments.'</td>';
			if (! empty($arrayfields['t.statut']['checked'])){
				//print '<td>'.$obj->statut.'</td>';
					if ($obj->statut==0) {
			            print '
			            <td colspan=4 align="left" ><img src="../theme/eldy/img/statut0.png" border="0" alt="" title="Borrador (a validar)">&nbsp;&nbsp; Borrador</td>
			            ';
			        }
			        if ($obj->statut==1) {
			            print '
			            <td colspan=4 align="left" ><img src="../theme/eldy/img/statut4.png" border="0" alt="" title="Activado" >&nbsp;&nbsp; Validado</td>
			            ';
			        }
			        if ($obj->statut==2) {
			            print '
			            <td colspan=4 align="left" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Facturada" >&nbsp;&nbsp; (cerrado) Facturada</td>
			            ';
			        }
			        if ($obj->statut==3) {
			            print '
			            <td colspan=4 align="left" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Activado" >&nbsp;&nbsp; cerrada(consulta gratuita)</td>
			            ';
			        }
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
	            print dol_print_date($db->jdate($obj->date_creation), 'dayhour');
	            print '</td>';
	        }
	        // Date modification
	        if (! empty($arrayfields['t.tms']['checked']))
	        {
	            print '<td align="center">';
	            print dol_print_date($db->jdate($obj->date_update), 'dayhour');
	            print '</td>';
	        }

			print '</tr>';
	    }
    
}
    
    $db->free($resql);

	$parameters=array('sql' => $sql);

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
