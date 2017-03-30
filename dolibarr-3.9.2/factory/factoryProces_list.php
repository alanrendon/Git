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
 *   	\file       root/factoryproccess_list.php
 *		\ingroup    root
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-16 05:10
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
dol_include_once('/factory/class/factoryproccess.class.php');
dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
dol_include_once('/factory/class/factoryoperator.class.php');

// Load traductions files requiredby by page
$langs->load("root");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$fact= NEW Factory($db);
$objectstatic=new Propal($db);
$objectOp=new Factoryoperator($db);

$search_fk_propal=GETPOST('search_fk_propal','alpha');
$search_fk_product=GETPOST('search_fk_product','alpha');
$search_fk_operator=GETPOST('search_fk_operator','alpha');
$search_dateStart=GETPOST('search_dateStart','alpha');
$search_dateEnd=GETPOST('search_dateEnd','alpha');
$search_status=GETPOST('search_status','int');


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
$hookmanager->initHooks(array('factoryproccesslist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('root');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Factoryproccess($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_propal'=>array('label'=>$langs->trans("Presupuesto"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Producto"), 'checked'=>1),
't.fk_operator'=>array('label'=>$langs->trans("Operador"), 'checked'=>1),
't.dateStart'=>array('label'=>$langs->trans("Fecha inicio"), 'checked'=>1),
't.dateEnd'=>array('label'=>$langs->trans("Ficha fin"), 'checked'=>1),
't.status'=>array('label'=>$langs->trans("Estado"), 'checked'=>1),

    
    
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
	
$search_fk_propal='';
$search_fk_product='';
$search_fk_operator='';
$search_dateStart='';
$search_dateEnd='';
	
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
			header("Location: ".dol_buildpath('/root/list.php',1));
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

llxHeader('','Produccion','');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('Listado de procesos');

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


$sql = "SELECT
		p.ref,
		p.label,
		pr.ref as refProp,
		o.ref as refOp,
		o. NAME,
		o.lastname,
		t.rowid,
		t.fk_propal,
		t.fk_product,
		t.fk_operator,
		t.dateStart,
		t.dateEnd,
		t. STATUS";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."factory_proccess as t
INNER JOIN llx_product AS p ON t.fk_product = p.rowid
INNER JOIN llx_propal AS pr ON t.fk_propal = pr.rowid
LEFT JOIN llx_factory_operator AS o ON t.fk_operator = o.rowid";

$sql.= " WHERE 1=1 and t.STATUS<4 ";

if ($search_fk_propal) $sql.= natural_search("pr.ref",$search_fk_propal);
if ($search_fk_product) $sql.= natural_search("p.ref",$search_fk_product);
if ($search_fk_operator) $sql.= natural_search("o.ref",$search_fk_operator);
if ($search_status) $sql.= natural_search("t.STATUS",$search_status);


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

//$sql.= $db->plimit($conf->liste_limit+1, $offset);
//echo $sql;

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
	
if ($search_fk_propal != '') $params.= '&amp;search_fk_propal='.urlencode($search_fk_propal);
if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_fk_operator != '') $params.= '&amp;search_fk_operator='.urlencode($search_fk_operator);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 
    
    print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');

    print '<div align="right">';		
		print '<a class="button" href="factoryproccess_card.php?action=create">Asignar proceso </a>';
	print '</div>';
	print '<br/>';
    

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
    

if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_propal']['checked'])) print_liste_field_titre($arrayfields['t.fk_propal']['label'],$_SERVER['PHP_SELF'],'t.fk_propal','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_operator']['checked'])) print_liste_field_titre($arrayfields['t.fk_operator']['label'],$_SERVER['PHP_SELF'],'t.fk_operator','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.dateStart']['checked'])) print_liste_field_titre($arrayfields['t.dateStart']['label'],$_SERVER['PHP_SELF'],'t.dateStart','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.dateEnd']['checked'])) print_liste_field_titre($arrayfields['t.dateEnd']['label'],$_SERVER['PHP_SELF'],'t.dateEnd','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$param,'',$sortfield,$sortorder);

    
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
	

if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_propal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_propal" value="'.$search_fk_propal.'" size="10"></td>';
if (! empty($arrayfields['t.fk_operator']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_operator" value="'.$search_fk_operator.'" size="10"></td>';
print '<td class="liste_titre"></td>';
print '<td class="liste_titre"></td>';
print '<td class="liste_titre" >';	
		print '<select class="flat" name="search_status">
			<option></option>	
			<option value=1>Inicializado</option>
			<option value=2>Finalizado</option>
			<option value=3>Interrumpido</option>
			<option value=4>Listo para tratamiento</option>';
		print '</select>';
	print '</td>';
	
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
			$objectstatic->id=$obj->fk_propal;	
			$objectstatic->ref=$obj->refProp;		

			$objectOp->ref=$obj->refOp;
    		$objectOp->name=$obj->NAME;
    		$objectOp->lastname=$obj->lastname;    		
    		$objectOp->rowid=$obj->fk_operator;	

            print '<tr>';
			if (! empty($arrayfields['t.fk_product']['checked'])) print '<td>'.$fact->getNomUrlFactory($obj->fk_product, 1,'index').'</td>';        
			if (! empty($arrayfields['t.fk_propal']['checked'])) print '<td>'.$objectstatic->getNomUrl(1).'</td>';
			if (! empty($arrayfields['t.fk_operator']['checked'])){
				print '<td>';
					if (!empty($obj->refOp)) {
						print $objectOp->getNomUrl(1,$obj->refOp).' '.$obj->NAME ;
					}else{
						print "Sin Asignar";
					}
				print '</td>';
			} 
			$date=strtotime($obj->dateStart);
			if (! empty($arrayfields['t.dateStart']['checked'])) print '<td>'.dol_print_date($date,"dayhour").'</td>';
			$date=strtotime($obj->dateEnd);
			if (! empty($arrayfields['t.dateEnd']['checked'])) print '<td>'.dol_print_date($date,"day").'</td>';
			if (! empty($arrayfields['t.status']['checked'])) print '<td>'.$object->LibStatut($obj->STATUS).'</td>';
            
        	
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
            print '<td><a href="factoryproccess_card.php?action=view&idProp='.$obj->fk_propal.'&idProd='.$obj->fk_product.'" class="button">Ver</a></td>';
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
