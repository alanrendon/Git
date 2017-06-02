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
 *   	\file       contab/contabinpc_list.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-19 22:07
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
include_once('../../class/contabinpc.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');



$search_year=GETPOST('search_year','int');
$search_enero=GETPOST('search_enero','alpha');
$search_febrero=GETPOST('search_febrero','alpha');
$search_marzo=GETPOST('search_marzo','alpha');
$search_abril=GETPOST('search_abril','alpha');
$search_mayo=GETPOST('search_mayo','alpha');
$search_junio=GETPOST('search_junio','alpha');
$search_julio=GETPOST('search_julio','alpha');
$search_agosto=GETPOST('search_agosto','alpha');
$search_septiembre=GETPOST('search_septiembre','alpha');
$search_octubre=GETPOST('search_octubre','alpha');
$search_noviembre=GETPOST('search_noviembre','alpha');
$search_diciembre=GETPOST('search_diciembre','alpha');


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

if ($user->rights->contab->linpc!=1)
{
	accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('contabinpclist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Contabinpc($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.year'=>array('label'=>$langs->trans("Año"), 'checked'=>1),
't.enero'=>array('label'=>$langs->trans("Enero"), 'checked'=>1),
't.febrero'=>array('label'=>$langs->trans("Febrero"), 'checked'=>1),
't.marzo'=>array('label'=>$langs->trans("Marzo"), 'checked'=>1),
't.abril'=>array('label'=>$langs->trans("Abril"), 'checked'=>1),
't.mayo'=>array('label'=>$langs->trans("Mayo"), 'checked'=>1),
't.junio'=>array('label'=>$langs->trans("Junio"), 'checked'=>1),
't.julio'=>array('label'=>$langs->trans("Julio"), 'checked'=>0),
't.agosto'=>array('label'=>$langs->trans("Agosto"), 'checked'=>0),
't.septiembre'=>array('label'=>$langs->trans("Septiembre"), 'checked'=>0),
't.octubre'=>array('label'=>$langs->trans("Octubre"), 'checked'=>0),
't.noviembre'=>array('label'=>$langs->trans("Noviembre"), 'checked'=>0),
't.diciembre'=>array('label'=>$langs->trans("Diciembre"), 'checked'=>0),

    
    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
    't.datec'=>array('label'=>$langs->trans("DateCreation"), 'checked'=>0, 'position'=>500),
    't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
    //'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
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
	
$search_year='';
$search_enero='';
$search_febrero='';
$search_marzo='';
$search_abril='';
$search_mayo='';
$search_junio='';
$search_julio='';
$search_agosto='';
$search_septiembre='';
$search_octubre='';
$search_noviembre='';
$search_diciembre='';

	
	$search_date_creation='';
	$search_date_update='';
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

llxHeader('','Listado INPC','');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('Listado de valores INPC');

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

		$sql .= " t.year,";
		$sql .= " t.enero,";
		$sql .= " t.febrero,";
		$sql .= " t.marzo,";
		$sql .= " t.abril,";
		$sql .= " t.mayo,";
		$sql .= " t.junio,";
		$sql .= " t.julio,";
		$sql .= " t.agosto,";
		$sql .= " t.septiembre,";
		$sql .= " t.octubre,";
		$sql .= " t.noviembre,";
		$sql .= " t.diciembre";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_inpc as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_inpc_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1 and t.entity=".$conf->entity." ";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";


if ($search_year) $sql.= natural_search("year",$search_year);
if ($search_enero) $sql.= natural_search("enero",$search_enero);
if ($search_febrero) $sql.= natural_search("febrero",$search_febrero);
if ($search_marzo) $sql.= natural_search("marzo",$search_marzo);
if ($search_abril) $sql.= natural_search("abril",$search_abril);
if ($search_mayo) $sql.= natural_search("mayo",$search_mayo);
if ($search_junio) $sql.= natural_search("junio",$search_junio);
if ($search_julio) $sql.= natural_search("julio",$search_julio);
if ($search_agosto) $sql.= natural_search("agosto",$search_agosto);
if ($search_septiembre) $sql.= natural_search("septiembre",$search_septiembre);
if ($search_octubre) $sql.= natural_search("octubre",$search_octubre);
if ($search_noviembre) $sql.= natural_search("noviembre",$search_noviembre);
if ($search_diciembre) $sql.= natural_search("diciembre",$search_diciembre);


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
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
	

if ($search_year != '') $params.= '&amp;search_year='.urlencode($search_year);
if ($search_enero != '') $params.= '&amp;search_enero='.urlencode($search_enero);
if ($search_febrero != '') $params.= '&amp;search_febrero='.urlencode($search_febrero);
if ($search_marzo != '') $params.= '&amp;search_marzo='.urlencode($search_marzo);
if ($search_abril != '') $params.= '&amp;search_abril='.urlencode($search_abril);
if ($search_mayo != '') $params.= '&amp;search_mayo='.urlencode($search_mayo);
if ($search_junio != '') $params.= '&amp;search_junio='.urlencode($search_junio);
if ($search_julio != '') $params.= '&amp;search_julio='.urlencode($search_julio);
if ($search_agosto != '') $params.= '&amp;search_agosto='.urlencode($search_agosto);
if ($search_septiembre != '') $params.= '&amp;search_septiembre='.urlencode($search_septiembre);
if ($search_octubre != '') $params.= '&amp;search_octubre='.urlencode($search_octubre);
if ($search_noviembre != '') $params.= '&amp;search_noviembre='.urlencode($search_noviembre);
if ($search_diciembre != '') $params.= '&amp;search_diciembre='.urlencode($search_diciembre);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 
    
    print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');
    

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
    

if (! empty($arrayfields['t.year']['checked'])) print_liste_field_titre("Año",$_SERVER['PHP_SELF'],'t.year','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.enero']['checked'])) print_liste_field_titre("Enero",$_SERVER['PHP_SELF'],'t.enero','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.febrero']['checked'])) print_liste_field_titre("Febrero",$_SERVER['PHP_SELF'],'t.febrero','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.marzo']['checked'])) print_liste_field_titre("Marzo",$_SERVER['PHP_SELF'],'t.marzo','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.abril']['checked'])) print_liste_field_titre("Abril",$_SERVER['PHP_SELF'],'t.abril','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.mayo']['checked'])) print_liste_field_titre("Mayo",$_SERVER['PHP_SELF'],'t.mayo','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.junio']['checked'])) print_liste_field_titre("Junio",$_SERVER['PHP_SELF'],'t.junio','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.julio']['checked'])) print_liste_field_titre("Julio",$_SERVER['PHP_SELF'],'t.julio','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.agosto']['checked'])) print_liste_field_titre("Agosto",$_SERVER['PHP_SELF'],'t.agosto','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.septiembre']['checked'])) print_liste_field_titre("Septiembre",$_SERVER['PHP_SELF'],'t.septiembre','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.octubre']['checked'])) print_liste_field_titre("Octubre",$_SERVER['PHP_SELF'],'t.octubre','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.noviembre']['checked'])) print_liste_field_titre("Noviembre",$_SERVER['PHP_SELF'],'t.noviembre','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.diciembre']['checked'])) print_liste_field_titre("Diciembre",$_SERVER['PHP_SELF'],'t.diciembre','',$param,'',$sortfield,$sortorder);

    
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
	

if (! empty($arrayfields['t.year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_year" value="'.$search_year.'" size="10"></td>';
if (! empty($arrayfields['t.enero']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_enero" value="'.$search_enero.'" size="10"></td>';
if (! empty($arrayfields['t.febrero']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_febrero" value="'.$search_febrero.'" size="10"></td>';
if (! empty($arrayfields['t.marzo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_marzo" value="'.$search_marzo.'" size="10"></td>';
if (! empty($arrayfields['t.abril']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_abril" value="'.$search_abril.'" size="10"></td>';
if (! empty($arrayfields['t.mayo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_mayo" value="'.$search_mayo.'" size="10"></td>';
if (! empty($arrayfields['t.junio']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_junio" value="'.$search_junio.'" size="10"></td>';
if (! empty($arrayfields['t.julio']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_julio" value="'.$search_julio.'" size="10"></td>';
if (! empty($arrayfields['t.agosto']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_agosto" value="'.$search_agosto.'" size="10"></td>';
if (! empty($arrayfields['t.septiembre']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_septiembre" value="'.$search_septiembre.'" size="10"></td>';
if (! empty($arrayfields['t.octubre']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_octubre" value="'.$search_octubre.'" size="10"></td>';
if (! empty($arrayfields['t.noviembre']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_noviembre" value="'.$search_noviembre.'" size="10"></td>';
if (! empty($arrayfields['t.diciembre']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_diciembre" value="'.$search_diciembre.'" size="10"></td>';

	
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
            $object_nef=new Contabinpc($db);
            $object_nef->fetch($obj->rowid);
if (! empty($arrayfields['t.year']['checked'])) print '<td>'.$object_nef->getNomUrl().'</td>';
if (! empty($arrayfields['t.enero']['checked'])) print '<td>'.$obj->enero.'</td>';
if (! empty($arrayfields['t.febrero']['checked'])) print '<td>'.$obj->febrero.'</td>';
if (! empty($arrayfields['t.marzo']['checked'])) print '<td>'.$obj->marzo.'</td>';
if (! empty($arrayfields['t.abril']['checked'])) print '<td>'.$obj->abril.'</td>';
if (! empty($arrayfields['t.mayo']['checked'])) print '<td>'.$obj->mayo.'</td>';
if (! empty($arrayfields['t.junio']['checked'])) print '<td>'.$obj->junio.'</td>';
if (! empty($arrayfields['t.julio']['checked'])) print '<td>'.$obj->julio.'</td>';
if (! empty($arrayfields['t.agosto']['checked'])) print '<td>'.$obj->agosto.'</td>';
if (! empty($arrayfields['t.septiembre']['checked'])) print '<td>'.$obj->septiembre.'</td>';
if (! empty($arrayfields['t.octubre']['checked'])) print '<td>'.$obj->octubre.'</td>';
if (! empty($arrayfields['t.noviembre']['checked'])) print '<td>'.$obj->noviembre.'</td>';
if (! empty($arrayfields['t.diciembre']['checked'])) print '<td>'.$obj->diciembre.'</td>';

            
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
