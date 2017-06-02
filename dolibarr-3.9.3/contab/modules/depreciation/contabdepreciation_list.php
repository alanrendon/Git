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
 *   	\file       contab/contabdepreciation_list.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-17 16:31
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
if (!$res && file_exists("../main.inc.php"))
	$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
	$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
	$res = @include '../../../main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../main.inc.php"))
	$res = @include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once('../../class/contabdepreciation.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_clave=GETPOST('search_clave','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_lifetime=GETPOST('search_lifetime','int');
$search_market_value=GETPOST('search_market_value','alpha');
$search_type_active=GETPOST('search_type_active','alpha');
$search_localitation=GETPOST('search_localitation','alpha');
$search_department=GETPOST('search_department','alpha');
$search_serial_number=GETPOST('search_serial_number','int');
$search_depreciation_rate=GETPOST('search_depreciation_rate','alpha');
$search_depreciation_accumulated=GETPOST('search_depreciation_accumulated','alpha');



$search_date_purchase=dol_mktime(12, 0 , 0, GETPOST('search_date_purchasemonth'), GETPOST('search_date_purchaseday'), GETPOST('search_date_purchaseyear'));



$search_date_init_purchase=dol_mktime(12, 0 , 0, GETPOST('search_date_init_purchasemonth'), GETPOST('search_date_init_purchaseday'), GETPOST('search_date_init_purchaseyear'));






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

if ($user->rights->contab->ldepres !=1)
{
	accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('contabdepreciationlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Contabdepreciation($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.clave'=>array('label'=>$langs->trans("Clave"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Monto original"), 'checked'=>1),
't.lifetime'=>array('label'=>$langs->trans("% Vida útil"), 'checked'=>1),
't.market_value'=>array('label'=>$langs->trans("Valor de mercado"), 'checked'=>1),
't.type_active'=>array('label'=>$langs->trans("Tipo de activo"), 'checked'=>0),
't.localitation'=>array('label'=>$langs->trans("Localización"), 'checked'=>1),
't.department'=>array('label'=>$langs->trans("Departamento"), 'checked'=>1),
't.serial_number'=>array('label'=>$langs->trans("No serie"), 'checked'=>1),
't.depreciation_rate'=>array('label'=>$langs->trans("Tasa de depreciación"), 'checked'=>1),
't.depreciation_accumulated'=>array('label'=>$langs->trans("Depreciación acumulada"), 'checked'=>1),
't.date_purchase'=>array('label'=>$langs->trans("Fecha de adquisición"), 'checked'=>0, 'position'=>500),
't.date_init_purchase'=>array('label'=>$langs->trans("Fecha inicio depreciación"), 'checked'=>0, 'position'=>500),
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
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{
	
$search_clave='';
$search_amount='';
$search_lifetime='';
$search_market_value='';
$search_type_active='';
$search_localitation='';
$search_department='';
$search_serial_number='';
$search_depreciation_rate='';
$search_depreciation_accumulated='';	
$search_date_purchase='';
$search_date_init_purchase='';
$search_array_options=array();
}


if (empty($reshook))
{
	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/contab/list.php',1));
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

llxHeader('','Depreciación','');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('Lista de depreciaciones');

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

		$sql .= " t.clave,";
		$sql .= " t.amount,";
		$sql .= " t.lifetime,";
		$sql .= " t.market_value,";
		$sql .= " r.descripcion as type_active,";
		$sql .= " t.localitation,";
		$sql .= " t.department,";
		$sql .= " t.serial_number,";
		$sql .= " t.depreciation_rate,";
		$sql .= " t.depreciation_accumulated,";
		$sql .= " t.date_purchase,";
		$sql .= " t.date_init_purchase";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_depreciation as t";
$sql.= " left join ".MAIN_DB_PREFIX."contab_sat_ctas as r on r.rowid=t.type_active ";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_depreciation_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_clave) $sql.= natural_search("clave",$search_clave);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_lifetime) $sql.= natural_search("lifetime",$search_lifetime);
if ($search_market_value) $sql.= natural_search("market_value",$search_market_value);
if ($search_type_active){
	$sql.= " AND r.descripcion like '%".$search_type_active."%' ";	
} 
if ($search_localitation) $sql.= natural_search("localitation",$search_localitation);
if ($search_department) $sql.= natural_search("department",$search_department);
if ($search_serial_number) $sql.= natural_search("serial_number",$search_serial_number);
if ($search_depreciation_rate) $sql.= natural_search("depreciation_rate",$search_depreciation_rate);
if ($search_depreciation_accumulated) $sql.= natural_search("depreciation_accumulated",$search_depreciation_accumulated);

if (!empty($search_date_init_purchase)){
	$sql.= natural_search("date_init_purchase",dol_print_date($search_date_init_purchase,"%Y-%m-%d"));
} 
if (!empty($search_date_purchase)){
	$sql.= natural_search("date_purchase",dol_print_date($search_date_purchase,"%Y-%m-%d"));
} 
if ($search_depreciation_accumulated){
	$sql.= natural_search("depreciation_accumulated",$search_depreciation_accumulated);
} 


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
//echo $sql;
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
	
if ($search_clave != '') $params.= '&amp;search_clave='.urlencode($search_clave);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_lifetime != '') $params.= '&amp;search_lifetime='.urlencode($search_lifetime);
if ($search_market_value != '') $params.= '&amp;search_market_value='.urlencode($search_market_value);
if ($search_type_active != '') $params.= '&amp;search_type_active='.urlencode($search_type_active);
if ($search_localitation != '') $params.= '&amp;search_localitation='.urlencode($search_localitation);
if ($search_department != '') $params.= '&amp;search_department='.urlencode($search_department);
if ($search_serial_number != '') $params.= '&amp;search_serial_number='.urlencode($search_serial_number);
if ($search_depreciation_rate != '') $params.= '&amp;search_depreciation_rate='.urlencode($search_depreciation_rate);
if ($search_depreciation_accumulated != '') $params.= '&amp;search_depreciation_accumulated='.urlencode($search_depreciation_accumulated);

if ($search_date_purchase != '') $params.= '&amp;search_date_purchase='.urlencode($search_date_purchase);
if ($search_date_init_purchase != '') $params.= '&amp;search_date_init_purchase='.urlencode($search_date_init_purchase);

if ($search_depreciation_accumulated != '') $params.= '&amp;search_depreciation_accumulated='.urlencode($search_depreciation_accumulated);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 
    
    print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');
  

    print "
    	<form method='GET' id='search' action='contabdepreciation_report.php' target='_blank'>
	    	<div class='liste_titre liste_titre_bydiv centpercent'>
	    		<div class='divsearchfield'>
	    		Generar Reporte 
	    		".($form->select_date(
			        GETPOST("date_advance"), 'date_advance', 0, 0, 1, "", 1, 1, 1, 0, '', '', ''
			    ))."  
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo de activo
	    		".($object->select_dol_active(GETPOST('type_active'),"type_active",1))."
			    <input type='submit' class='button' name='xls' value='Generar Reporte XLS'>
			    <input type='submit' class='button' name='pdf' value='Generar Reporte PDF'>
	    		</div>
		    </div>
	    </form>
    ";

	print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
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
    
if (! empty($arrayfields['t.clave']['checked'])) print_liste_field_titre($arrayfields['t.clave']['label'],$_SERVER['PHP_SELF'],'t.clave','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.serial_number']['checked'])) print_liste_field_titre($arrayfields['t.serial_number']['label'],$_SERVER['PHP_SELF'],'t.serial_number','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lifetime']['checked'])) print_liste_field_titre($arrayfields['t.lifetime']['label'],$_SERVER['PHP_SELF'],'t.lifetime','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.market_value']['checked'])) print_liste_field_titre($arrayfields['t.market_value']['label'],$_SERVER['PHP_SELF'],'t.market_value','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_active']['checked'])) print_liste_field_titre($arrayfields['t.type_active']['label'],$_SERVER['PHP_SELF'],'t.type_active','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localitation']['checked'])) print_liste_field_titre($arrayfields['t.localitation']['label'],$_SERVER['PHP_SELF'],'t.localitation','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.department']['checked'])) print_liste_field_titre($arrayfields['t.department']['label'],$_SERVER['PHP_SELF'],'t.department','',$param,'',$sortfield,$sortorder);

if (! empty($arrayfields['t.depreciation_rate']['checked'])) print_liste_field_titre($arrayfields['t.depreciation_rate']['label'],$_SERVER['PHP_SELF'],'t.depreciation_rate','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.depreciation_accumulated']['checked'])) print_liste_field_titre($arrayfields['t.depreciation_accumulated']['label'],$_SERVER['PHP_SELF'],'t.depreciation_accumulated','',$param,'',$sortfield,$sortorder);


if (! empty($arrayfields['t.date_purchase']['checked'])) print_liste_field_titre($arrayfields['t.date_purchase']['label'],$_SERVER['PHP_SELF'],'t.date_purchase','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_init_purchase']['checked'])) print_liste_field_titre($arrayfields['t.date_init_purchase']['label'],$_SERVER['PHP_SELF'],'t.date_init_purchase','',$param,'',$sortfield,$sortorder);




    
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
	
if (! empty($arrayfields['t.clave']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_clave" value="'.$search_clave.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.lifetime']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lifetime" value="'.$search_lifetime.'" size="10"></td>';
if (! empty($arrayfields['t.market_value']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_market_value" value="'.$search_market_value.'" size="10"></td>';
if (! empty($arrayfields['t.type_active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_active" value="'.$search_type_active.'" size="10"></td>';
if (! empty($arrayfields['t.localitation']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localitation" value="'.$search_localitation.'" size="10"></td>';
if (! empty($arrayfields['t.department']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_department" value="'.$search_department.'" size="10"></td>';
if (! empty($arrayfields['t.serial_number']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_serial_number" value="'.$search_serial_number.'" size="10"></td>';


if (! empty($arrayfields['t.depreciation_rate']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_depreciation_rate" value="'.$search_depreciation_rate.'" size="10"></td>';


if (! empty($arrayfields['t.depreciation_accumulated']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_depreciation_accumulated" value="'.$search_depreciation_accumulated.'" size="10"></td>';


if (! empty($arrayfields['t.date_purchase']['checked'])){
	print '
	<td class="liste_titre">';
	print $form->select_date(
				$search_date_purchase, 'search_date_purchase', 0, 0, 1, "", 1, 1, 1, 0, '', '', ''
		    );
	print ' 
	</td>';
} 

if (! empty($arrayfields['t.date_init_purchase']['checked'])){
	print '
	<td class="liste_titre">';
		print $form->select_date(
				$search_date_init_purchase, 'search_date_init_purchase', 0, 0, 1, "", 1, 1, 1, 0, '', '', ''
		    );
	print '
	</td>';
} 



	
	// Extra fields
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
    /*if (! empty($arrayfields['u.statut']['checked']))
    {
        // Status
        print '<td class="liste_titre" align="center">';
        print $form->selectarray('search_statut', array('-1'=>'','0'=>$langs->trans('Disabled'),'1'=>$langs->trans('Enabled')),$search_statut);
        print '</td>';
    }*/
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
        if ($obj)
        {
            // You can use here results
            print '<tr>';
            
			if (! empty($arrayfields['t.clave']['checked'])){
				$object_new=new Contabdepreciation($db);
				$var=$object_new->fetch($obj->rowid);
				if ($var) {
					print '<td>'.$object_new->getNomUrl().'</td>';
				}else{
					print '<td>'.$obj->clave.'</td>';
				}
				
			} 
			if (! empty($arrayfields['t.amount']['checked'])) print '<td>'.$obj->amount.'</td>';
			if (! empty($arrayfields['t.lifetime']['checked'])) print '<td>'.$obj->lifetime.'</td>';
			if (! empty($arrayfields['t.market_value']['checked'])) print '<td>'.$obj->market_value.'</td>';
			if (! empty($arrayfields['t.type_active']['checked'])) print '<td>'.$obj->type_active.'</td>';
			if (! empty($arrayfields['t.localitation']['checked'])) print '<td>'.$obj->localitation.'</td>';
			if (! empty($arrayfields['t.department']['checked'])) print '<td>'.$obj->department.'</td>';
			if (! empty($arrayfields['t.serial_number']['checked'])) print '<td>'.$obj->serial_number.'</td>';


			if (! empty($arrayfields['t.depreciation_rate']['checked'])) print '<td>'.$obj->depreciation_rate.'</td>';
			if (! empty($arrayfields['t.depreciation_accumulated']['checked'])) print '<td>'.$obj->depreciation_accumulated.'</td>';

			if (! empty($arrayfields['t.date_purchase']['checked'])){
				print '<td>'.dol_print_date($obj->date_purchase,"%d/%m/%Y").'</td>';
			} 

			if (! empty($arrayfields['t.date_init_purchase']['checked'])){
				print '<td>'.dol_print_date($obj->date_init_purchase,"%d/%m/%Y").'</td>';
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
            // Status
            /*
            if (! empty($arrayfields['u.statut']['checked']))
            {
    		  $userstatic->statut=$obj->statut;
              print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
            }*/
            // Action column
            print '<td></td>';
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
