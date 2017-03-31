<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Charles-Fr BENKE		<charles.fr@benke.fr>
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
 *	    \file       htdocs/factory/product/list.php
 *      \ingroup    factory
 *		\brief      Page to list all factory process
 */

$res=@include("../main.inc.php");                    // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");        // For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

$langs->load('companies');
$langs->load('propal');
$langs->load('compta');
$langs->load('bills');
$langs->load('orders');
$langs->load('products');

$socid=GETPOST('socid','int');


$search_ref=GETPOST('sf_ref')?GETPOST('sf_ref','alpha'):GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');

$viewstatut=GETPOST('viewstatut');
$optioncss = GETPOST('optioncss','alpha');
$object_statut=GETPOST('propal_statut');

// Security check
$module='propal';
$dbtable='';
$objectid='';
if (! empty($user->societe_id))	$socid=$user->societe_id;
if (! empty($socid))
{
	$objectid=$socid;
	$module='societe';
	$dbtable='&societe';
}
$result = restrictedArea($user, $module, $objectid, $dbtable);

if (GETPOST("button_removefilter") || GETPOST("button_removefilter_x"))	// Both tests are required to be compatible with all browsers
{    
    $search_ref='';    
    $search_label='';      
}

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('propallist'));

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
    'c.ref'=>'Ref',
    'c.label'=>'label',    
);

/*
 * Actions
 */

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
/*
 * View
 */

llxHeader("","",$langs->trans("FactoryListPen".$product->type));
dol_htmloutput_mesg($mesg);
print_fiche_titre($langs->trans("FactoryListPen"));

$objectstatic=new Propal($db);
$companystatic=new Societe($db);
$fact= NEW Factory($db);
$product = new Product($db);

$now=dol_now();

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (! $sortorder) $sortorder='DESC';
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;


$sql = 'SELECT
			d.fk_product_father,
			d.fk_product_children,
			b.qty as qtydet,
			d.qty,	';
		//	SUM(d.qty*b.qty) AS suma,
$sql .= '	b.fk_propal,
			b.fk_product,
			c.label,
			c.ref,
			c.rowid,
			c.cost_price,
			a.fk_status_factory
		FROM
			llx_propal AS a
		INNER JOIN llx_propaldet AS b ON a.rowid = b.fk_propal
		INNER JOIN llx_product_factory AS d ON b.fk_product = d.fk_product_father
		INNER JOIN llx_product AS c ON d.fk_product_children = c.rowid
		WHERE
			a.fk_status_factory = 0
		AND a.fk_statut = 2
		AND c.fk_product_type = 0';		

if ($search_ref) {
	$sql .= natural_search('c.ref', $search_ref);
}

if ($search_label) {
	$sql .= natural_search('c.label', $search_label);
}

if ($sall) {
    $sql .= natural_search(array_keys($fieldstosearchall), $sall);
}

$sql.=' ORDER BY c.rowid';
//$sql.=' GROUP BY d.fk_product_father , d.fk_product_children ORDER BY c.rowid ';


$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}
//print $sql;

$sql.= $db->plimit($limit + 1,$offset);
$result=$db->query($sql);

if ($result)
{
	$objectstatic=new Propal($db);
	$userstatic=new User($db);
	$num = $db->num_rows($result);

 	
    if ($search_ref)         $param.='&search_ref=' .$search_ref;    
    if ($search_label)     $param.='&search_label=' .$search_label;	
	
	if ($optioncss != '') $param.='&optioncss='.$optioncss;

	// Lignes des champs de filtre
	print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        //sort($fieldstosearchall);
        print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
    }
	
	$i = 0;


	print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";
	
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER["PHP_SELF"],'c.ref','',$param,'',$sortfield,$sortorder);	
	print_liste_field_titre($langs->trans('Label'),$_SERVER["PHP_SELF"],'c.Label','',$param,'',$sortfield,$sortorder);	
	print_liste_field_titre($langs->trans('QtyNeed'),$_SERVER["PHP_SELF"],'','',$param,'align="right"',$sortfield,$sortorder);	
	print_liste_field_titre($langs->trans('Stock'),$_SERVER["PHP_SELF"],'','',$param,'align="right"',$sortfield,$sortorder);	
	print_liste_field_titre($langs->trans('QtyPen'),$_SERVER["PHP_SELF"],'','',$param,'align="right"',$sortfield,$sortorder);	
	//print_liste_field_titre($langs->trans('UnitPmp'),$_SERVER["PHP_SELF"],'','',$param, 'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans('PriceCost'),$_SERVER["PHP_SELF"],'','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre('',$_SERVER["PHP_SELF"],"",'','','',$sortfield,$sortorder,'maxwidthsearch ');
	print "</tr>\n";

	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input class="flat" size="6" type="text" name="search_ref" value="'.$search_ref.'">';
	print '</td>';	
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" type="text" size="12" name="search_label" value="'.$search_label.'">';
	print '</td>';	
	print '<td class="liste_titre" colspan="1">&nbsp;</td>';	
	print '<td class="liste_titre" colspan="1">&nbsp;</td>';	
	print '<td class="liste_titre" colspan="1">&nbsp;</td>';	
	//print '<td class="liste_titre" colspan="1">&nbsp;</td>';	
	//print '<td class="liste_titre" colspan="1">&nbsp;</td>';	
	
	print '<td class="liste_titre" align="right">';
	print '<input type="image" name="button_search" class="liste_titre" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '<input type="image" name="button_removefilter" class="liste_titre" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
	print "</tr>\n";

	$var=true;
	$total=0;
	$subtotal=0;
	$list=array();

	$i=0;
	$pesoTeoriUnit=0;
	$cantNeed=0;
	$pend=0;

	while ($dat=$db->fetch_object($result))
	{		
		$list[]=$dat;
	}	

	foreach ($list as $dat) 
	{		
		

		if($list[$i]->rowid==$list[$i+1]->rowid){
			$cant=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);
			$cantTT=$cant*$dat->qtydet;
			$cantNeed+=$cantTT;
			
		
		//	echo '<br/>'.$i.' '.$cant.' '.$dat->qtydet.' '.$cantTT.' '.$cantNeed.'<br/> ';
	
		}else{
		
			$now = dol_now();		
			
			$cant=$fact->get_peso_teorico($dat->fk_product_father, $dat->fk_product_children);
			$cantTT=$cant*$dat->qtydet;
			$cantNeed+=$cantTT;
			$stoc= $fact->get_qty_stock($dat->rowid);			
			if($cantNeed>$stoc){
				$qtyStoc = ($stoc>0) ? $stoc : 0 ;		
				$pend=$cantNeed-$stoc;
				if ($stoc>= $cantNeed/1000) {
					$pend=0;
				}

				if ($pend/1000!=0) {
					print '<tr >';
						print '<td align="left">'.$fact->getNomUrlFactory($dat->rowid, 1,'index').'</td>';
						print '<td align="left">'.$dat->label.'</td>';
						print '<td align="right">';											
							print number_format($cantNeed/1000,0,'.',',');
						print '</td>';					
						print '<td align="right">';						
										
							print $fact->getUrlStock($dat->rowid, 1, $qtyStoc);			 
						print '</td>';
						print '<td align="right">';							
										
							print number_format($pend/1000,0,'.',',')." K";						
						print '</td>';	
						/*print '<td align="right">';											
							$price= $dat->cost_price;							
							print price($price);
						print '</td>';
						print '<td align="right">';
							$pricett= $price*$cantNeed;							
							print price($pricett);
						print '</td>';*/			

						print '<td><a href="detailsFactoryPen.php?id='.$dat->rowid.'" class="button">Ver</a></td>';
					print "</tr>\n";
				}
				
			}
			$cant=0;
			$cantNeed=0;
			
		}
		$i++;
	}	

	print '</table>';

	print '</form>';

	$db->free($result);
}
else
{
	dol_print_error($db);
}

// End of page
llxFooter();
$db->close();

?>

