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
 *   	\file       root/factorytools_card.php
 *		\ingroup    root
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-03 20:32
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

require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/factory/class/factorytools.class.php');
dol_include_once('/factory/class/factoryoperator.class.php');

// Load traductions files requiredby by page
$langs->load("root");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$comment=GETPOST('comment','alpha');
$dateFin=GETPOST('dateEnd','alpha');


$idP=GETPOST('idP','int');
$action2= GETPOST('action2','alpha');


$search_fk_operator=GETPOST('search_fk_operator','int');
$search_dateCreation=GETPOST('search_dateCreation','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_type=GETPOST('search_type','int');
$search_qty=GETPOST('search_qty','int');
$search_status=GETPOST('search_status','int');
$search_comment=GETPOST('search_comment','alpha');

$product = new Product($db);
$fact= NEW Factory($db);

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Factorytools($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('factorytools'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/factory/factoryoperator_list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_operator=GETPOST('fk_operator','int');
	$object->fk_product=GETPOST('fk_product','int');
	$object->type=GETPOST('type','int');
	$object->status=GETPOST('status','int');
	$object->comment=GETPOST('comment','alpha');
	$object->qty=GETPOST('qty');
	$object->dateCreation=GETPOST('dateCreation');
		

		if (empty($object->fk_operator))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Operador")), null, 'errors');
		}
		if (empty($object->fk_product))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Herramienta")), null, 'errors');
		}
		if (empty($object->type))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Tipo")), null, 'errors');
		}
		if (empty($object->qty))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Cantidad")), null, 'errors');
		}
		

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
					
				$object->entrepot();						
				$ref=$object->get_ref_operator($object->fk_operator);	

				
		        $id_tw=$object->fk_entrepot;
		        $qty=$object->qty;        
		        $stlabel='Herramienta asignada a  el operador '.$ref;       

		        $result=$product->fetch($object->fk_product);
		    
		        $product->load_stock();    // Load array product->stock_warehouse
		    
		        // Define value of products moved
		        $pricesrc=0;
		        if (! empty($product->pmp)) $pricesrc=$product->pmp;
		        $pricedest=$pricesrc;    
		        
		            // Add stock
		        $result=$product->correct_stock(
		                $user,
		                $id_tw,
		                $qty,
		                1,
		                $stlabel,
		                $pricedest
		                
		        );	        									

				// Creation OK				
				$urltogo=$backtopage?$backtopage:dol_buildpath('/factory/factoryoperator_list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Producción','');

$form=new Form($db);

print '<div style="vertical-align: middle">
					<div class="inline-block floatleft"></div>
					<div class="inline-block floatleft valignmiddle refid refidpadding"></div>
					<div class="pagination">
						<ul>
							<li class="pagination"></li>
							<li class="pagination">								
								Volver a operadores<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="factoryoperator_list.php">&lt;</a>
							</li>
						</ul>
					</div>
				</div>';

// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Asignación de herramientas"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Operador").'</td>';
	//print '<td><input class="flat" type="text" name="fk_operator" value="'.GETPOST('fk_operator').'"></td>';
	print '<td>';
		print '<select name=fk_operator>';
			$list=array();
			$list=$object->get_operators();
			foreach ($list as $dat) {
				print '<option value="'.$dat->rowid.'">'.$dat->name.'</option>';	
			}
		print '</select>';
	print '</td>';


print '</tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Producto").'</td>';print '<td>';
		print '<select name=fk_product>';
			$list=array();
			$list=$object->get_tools();
			foreach ($list as $dat) {
				print '<option value="'.$dat->rowid.'">'.$dat->label.'</option>';	
			}			

		print '</select>';
	print '</td>';
print '</tr>';

print '<tr><td class="fieldrequired">'.$langs->trans("Cantidad").'</td>';print '<td>';
		print '<input name="qty" type="text"/>';
	print '</td>';
print '</tr>';

print '<tr><td class="fieldrequired">'.$langs->trans("Fecha").'</td>';print '<td>';
		print ($form->select_date('','dateCreation' ,0, 0, 0, "", 1, 0, 1, 0, '', '', ''));
	print '</td>';
print '</tr>';

print '<tr><td class="fieldrequired">'.$langs->trans("Tipo").'</td>';
print '<td>';
		print '<select name=type>';
			print '<option value=1>Consumible</option>';
			print '<option value=2>Operable</option>';
		print '</select>';
	print '</td>';
print '</tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcomment").'</td><td><input class="flat" type="text" name="comment" value="'.GETPOST('comment').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

if($action=='view'){
	$objectOp=new Factoryoperator($db);
	$result=$objectOp->fetch($id);
	print '<br/>';
	print '<fieldset>';
		print '<br/>';
		print load_fiche_titre($langs->trans("Operador"));		
		print '<table class="border centpercent"  width="100%">';			
			print '<tr>';
				print '<td width=20% align="left"><b>'.$langs->trans("Ref").'</b></td>';
				print '</td>';
				print '<td>';	   
					print $objectOp->getNomUrl(1,$objectOp->ref);				
				print '</td>';					
			print '</tr>';		
			print '<tr>';
				print '<td  width=100px align="left"><b>'.$langs->trans("Nombre").'</b></td>';
				print '</td>';
				print '<td>';	
					print $objectOp->name;				
				print '</td>';				
			print '</tr>';	
			print '<tr>';
				print '<td  width=100px align="left"><b>'.$langs->trans("Apellido").'</b></td>';
				print '</td>';
				print '<td>';				
					print $objectOp->lastname;
				print '</td>';				
			print '</tr>';				
		print '</table>';
	print '</fieldset>';
	print '<br/>';

	print '<fieldset>';
		print '<br/>';
		print load_fiche_titre($langs->trans("Herramientas activas "));		
		print '<form name="productsFac"  method="post">';
		print '<table class="border"  width="100%">';		
			print '<tr class="liste_titre">';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Label").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Qty").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Fecha recepción").'</td>'; 	
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Type").'</td>'; 	
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Operación").'</td>'; 						
			print '</tr>';	
					
			$list=array();
			$list=$object->get_tools_asigned($id);
			foreach ($list as $dat) {
				print '<tr ">';
					print '<td align="left">'.$fact->getNomUrlFactory($dat->rowid, 1,'index').'</td>';
					print '<td align="left">'.$dat->label.'</td>';
					print '<td align="left">'.$dat->qty.'</td>';
					print '<td align="left">'.dol_print_date($db->jdate($dat->dateCreation), 'day').'</td>';					
					if($dat->type==2){
						print '<td align="left">Operable</td>';							
						print '<td align="center"><a href="factorytools_card.php?id='.$id.'&idP='.$dat->fila.'&action=confirmDelete" class="button">Devolver</a></td>';					
						
					}else{
						print '<td align="left">Consumible</td>';	
						print '<td align="center"><a href="factorytools_card.php?id='.$id.'&idP='.$dat->fila.'&action=confirmFin" class="button">Devolver</a></td>';			
					}					
				print '</tr>';	
			}	
		print '</table>';		
		print '<form>';


	print '</fieldset>';

	$act='view2';
}

if($action=='confirmDelete'){
	$form = new Form($db);
	$id=GETPOST('id','int');
	$idP=GETPOST('idP','int');

	$formquestion = array(                            
                     array('type' => 'date', 'name' => 'dateEnd', 'id'=>'dateEnd', 'label' =>'Fecha de entrega' ),                    
                    );
	print $form->formconfirm("factorytools_card.php?id=".$id."&action=view&idP=".$idP."","Comfirmar","La herramienta esta completa?","delete",$formquestion);
}

if($action=='confirmFin'){
	$form = new Form($db);
	$id=GETPOST('id','int');
	$idP=GETPOST('idP','int');
	$formquestion = array(                            
        array('type' => 'date', 'name' => 'dateEnd', 'id'=>'dateEnd', 'label' =>'Fecha de entrega' ),            
        );

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&action=view&idP='.$idP.'', $langs->trans('Herramienta agotada'), $langs->trans('El material se termino?'), 'fin',  $formquestion);
		print $formconfirm;
}

if($_REQUEST['confirm']=='yes' && $_REQUEST['action']=='fin'){

	if($dateFin){		

	    $dateFin = trim($dateFin);
		$aux=str_replace('/','-',$dateFin);
		$dat=date('Y-m-d',strtotime($aux));
	    $sql='UPDATE llx_factory_tools set status=2, dateDeliver="'.$dat.'" where rowid='.$idP;
		$query=$db->query($sql);	
		//echo $sql;
	  
	    print "<script>window.location.href='factorytools_card.php?id=".$id."&action=view'</script>";

	}else{
		print '<script>alert("Selecione fecha de entrega")</script>';
	}
    $act='view2';
}

if($_REQUEST['confirm']=='yes' && $_REQUEST['action']=='fin'){
    $act='view2';
}


if($_REQUEST['confirm']=='yes' && $_REQUEST['action']=='delete'){

	if($dateFin){

		$object->entrepot();						
		$ref=$object->get_ref_operator($object->fk_operator);	

		
	    $id_tw=$object->fk_entrepot;
	    $qty=0;  
	    $sql='SELECT qty, fk_product from llx_factory_tools where rowid='.$idP;
		$query=$db->query($sql);	
		if($query){
			$datQty=$db->fetch_object($query);
			$qty=$datQty->qty;  
		}
	    
	    $stlabel='Herramienta devuelta por el operador '.$ref;       

	    $result=$product->fetch($datQty->fk_product);

	    $product->load_stock();    // Load array product->stock_warehouse

	    // Define value of products moved
	    $pricesrc=0;
	    if (! empty($product->pmp)) $pricesrc=$product->pmp;
	    $pricedest=$pricesrc;        
	        // Add stock
	    $result=$product->correct_stock(
	            $user,
	            $id_tw,
	            $qty,
	            0,
	            $stlabel,
	            $pricedest
	            
	    );	 

	    $dateFin = trim($dateFin);
		$aux=str_replace('/','-',$dateFin);
		$dat=date('Y-m-d',strtotime($aux));
	    $sql='UPDATE llx_factory_tools set status=2, dateDeliver="'.$dat.'" where rowid='.$idP;
		$query=$db->query($sql);	
		//echo $sql;
	  
	    print "<script>window.location.href='factorytools_card.php?id=".$id."&action=view'</script>";

	}else{
		print '<script>alert("Selecione fecha de entrega")</script>';
	}
    $act='view2';
}


if($_REQUEST['confirm']=='no' && $_REQUEST['action']=='delete'){

	if($dateFin){

		print '<input type="hidden" name="dateEnd" value="'.$dateFin.'"/>';
		print '<br/>';
		print '<fieldset>';
			print '<br/>';
			print load_fiche_titre($langs->trans("Entrega insatisfactoria"));	
			print '<form method="post" action="/factorytools_card.php?id='.$id.'&action=updateComment&idP='.$idP.'">';			
			print '<table class="border centpercent"  width="100%">';						
				print '<tr>';
					print '<td width=20% align="left"><b>'.$langs->trans("Comentario de entrega").'</b></td>';
					print '</td>';
					print '<td width=50% >';	   
						print '<textarea name="comment" COLS=70 ROWS=3 required ></textarea>';												
					print '</td>';					
				print '</tr>';						
			print '</table>';
			//print '<a href="factorytools_card.php?id='.$id.'&action=view&action2=updateComment&idP='.$idP.'" class="button">Guardar</a>';		
			print '<div  align="center">';
				print '<br/><input type="submit" class="button" value="Guardar">';
			print '</div>';
			print '</form>';			
		print '</fieldset>';
	}else{
		print '<script>alert("Selecione fecha de entrega")</script>';
	}
	

	$act='view2';
}

if($comment){	
	if (isset($dateFin) && strcmp($dateFin, '')!=0) {
		$dateFin = trim($dateFin);
		$aux=str_replace('/','-',$dateFin);
		$dat=date('Y-m-d',strtotime($aux));

		$sql='UPDATE llx_factory_tools set status=3, comment="'.$comment.' " , dateDeliver="'.$dat.'" where rowid='.$idP;
		$query=$db->query($sql);	
		print "<script>window.location.href='factorytools_card.php?id=".$id."&action=view'</script>";
		
	}else{
		print "<script>alert('Seleccione la fecha de entrega')</script>";	
	}
	
}

if($act=='view2'){
		print '<br/>';
	print '<fieldset>';
		print '<br/>';		
		print load_fiche_titre($langs->trans("Historial de herramientas "));		
		print '<form name="productsFac"  method="post">';
		print '<table class="border"  width="100%">';		
			print '<tr class="liste_titre">';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Label").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Qty").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Fecha recepción").'</td>'; 	
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Fecha devolución").'</td>'; 	
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Type").'</td>'; 	
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Status").'</td>'; 						
			print '</tr>';	
					
			$list=array();
			$list=$object->get_tools_history($id);
			foreach ($list as $dat) {
				print '<tr ">';
					print '<td align="left">'.$fact->getNomUrlFactory($dat->rowid, 1,'index').'</td>';
					print '<td align="left">'.$dat->label.'</td>';
					print '<td align="left">'.$dat->qty.'</td>';
					print '<td align="left">'.dol_print_date($db->jdate($dat->dateCreation), 'day').'</td>';	
					print '<td align="left">'.dol_print_date($db->jdate($dat->dateDeliver), 'day').'</td>';					
					if($dat->type==2){
						print '<td align="left">Operable</td>';							
						if($dat->status==2){
							print '<td align="center">Devuelto</td>';
						}else{
							print '<td align="center">Comentario:'.$dat->comment.'</td>';
						}						
						
					}else{
						print '<td align="left">Consumible</td>';	
						if($dat->status==2){
							print '<td align="center">Agotada</td>';
						}else{
							print '<td align="center"></td>';
						}						
					}					
				print '</tr>';	
			}	
		print '</table>';		
		print '<form>';
	print '</fieldset>';
}




// End of page
llxFooter();
$db->close();
