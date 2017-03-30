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
 *   	\file       root/factoryproccess_card.php
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
dol_include_once('/factory/class/factorytools.class.php');

// Load traductions files requiredby by page
$langs->load("root");
$langs->load("other");

// Get parameters
$id			= GETPOST('idProp','int');
$action		= GETPOST('action','alpha');
$action2		= GETPOST('action2','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$comment=GETPOST('comment','alpha');


$search_fk_propal=GETPOST('search_fk_propal','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_operator=GETPOST('search_fk_operator','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Factoryproccess($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('factoryproccess'));
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
	

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
		$object->fk_propal=GETPOST('fk_propal','int');
		$object->fk_product=GETPOST('fk_product','int');
		$object->fk_operator=GETPOST('fk_operator','int');

			  

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	if($action=='confirm_cerrar'){
		$idPro= GETPOST('idProp');
		$idProd= GETPOST('idProd');
		if(GETPOST('dateEnd') &&  GETPOST('horas')  && GETPOST('minutos')){
			$fk_operator=GETPOST("fk_operator","int");

			if ($fk_operator>0) {
				$horas= GETPOST('horas');
				$minutos=  GETPOST('minutos');
				$datEnd=GETPOST('dateEnd');
				$fecha = trim($datEnd);
				$aux=str_replace('/','-',$fecha);
				$datE=date('Y-m-d',strtotime($aux));


				$string ='  UPDATE llx_factory_proccess as a
							INNER JOIN llx_factory_proccess_treatment as b ON b.fk_factory_proccess=a.rowid
							SET b.dateEnd = "'.$datE.'",
							a.STATUS = 6,
							b.hours='.$horas.',
							b.minutes='.$minutos;
				$string .= ',b.fk_operator='.$fk_operator;
				$string .='
							WHERE
								a.fk_propal ='.$idPro.'
							AND a.fk_product ='.$idProd;

				//die($string);		
				$query=$db->query($string);

				//print '<script>window.location.href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view"</script>';
				header("Location: ".$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view');
				exit;
			}else{
				$action='cerrar';
				setEventMessages('Introdusca el campo "Operador"', null, 'errors');
			}
			
		}else{
			setEventMessages('Introduzca todos los datos', null, 'errors');
			$action='cerrar';
		}
	}

	if($action=='confirm_cerrarTrat'){
		//print_r($_POST);
		$idPro= GETPOST('idProp');
		$idProd= GETPOST('idProd');
		
		$string ='UPDATE llx_factory_proccess					
					set  STATUS = 7
					WHERE
						fk_propal ='.$idPro.'
					AND fk_product ='.$idProd;
		$query=$db->query($string);

		print '<script>location.href="factoryTreatment_list.php";</script>';
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$idPro= GETPOST('idProp');
		$idProd= GETPOST('idProd');
		$result=$object->delete($idPro, $idProd);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/factory/factoryTreatment_list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	if ($action == 'confirm_asignar')
	{
		$idPro= GETPOST('idProp');
		$idProd= GETPOST('idProd');
		$procceso=GETPOST("idproccess");
		$dateStart=GETPOST("dateStart");
		$dateStarthour=GETPOST("dateStarthour");
		$dateStartmin=GETPOST("dateStartmin");
		$dateStartday=GETPOST("dateStartday");
		$dateStartmonth=GETPOST("dateStartmonth");
		$dateStartyear=GETPOST("dateStartyear");
		
		$fecha=dol_mktime($dateStarthour,$dateStartmin,0,$dateStartmonth,$dateStartday,$dateStartyear);
		
		$dateStart= dol_print_date($fecha,"%d/%m/%Y %H:%M");
	
		if ($procceso>0) {

			$operator= new Factoryproccess($db);
			$operator->addProcesos_treatment($procceso,0,$dateStart);
			print '<script>location.href="factoryTreatment_list.php";</script>';
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

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	
	jQuery("#fk_propal").change(function() {
		
		datos={funcion:"productsPropal", idPropal:$("#fk_propal").val()};

		$.ajax({
			async:true,
			type: "POST",
			dataType: "html",
			contentType: "application/x-www-form-urlencoded",
			url:"treatment_propal.php",
			data:datos,
			success:function (data){				
				$("#products_propal").html(data);							
			},
			error:function (data){
				alert("error "+data);
			}
		});	
	});
});
</script>';
print '<div style="vertical-align: middle">
		<div class="inline-block floatleft"></div>
		<div class="inline-block floatleft valignmiddle refid refidpadding"></div>
		<div class="pagination">
			<ul>
				<li class="pagination"></li>
				<li class="pagination">								
					Volver al tratamiento<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="factoryTreatment_list.php">&lt;</a>
				</li>
			</ul>
		</div>
	</div>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Nueva asignación"));

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
		print '<tr>';
			print '<td class="fieldrequired">'.$langs->trans("Presupuesto").'</td><td>';
				print '<select name="fk_propal" id="fk_propal">';
					print '<option>...Seleccione</option>';
					$list=$object->get_propals_treatment_pen();
					foreach ($list as $data) {
						print '<option value="'.$data->rowid.'">'.$data->ref.'</option>';
					}
				print '<select>';
			print '</td>';
		print '</tr>';
		print '<tr>';	
	
	print '</table>'."\n";	
	print '<br/>';
	print '<div name="products_propal" id="products_propal"></div>';
	dol_fiche_end();	
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_propal").'</td><td><input class="flat" type="text" name="fk_propal" value="'.$object->fk_propal.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_operator").'</td><td><input class="flat" type="text" name="fk_operator" value="'.$object->fk_operator.'"></td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ( $action == 'view' || $action == 'delete' || $action == 'cerrar' || $action == 'cerrarTrat' || $action == 'asignar')
{
	print load_fiche_titre($langs->trans("Detalle del Tratamiento"));
    
	dol_fiche_head();

	$idPro= GETPOST('idProp');
	$idProd= GETPOST('idProd');

	$string='
	SELECT
		p.ref AS refProd,
		p.label,
		pr.ref AS refPro,
		o.ref,
		o. NAME,
		o.lastname,
		t.rowid,
		t.fk_propal,
		t.fk_product,
		r.fk_operator,
		r.dateStart,
		r.dateEnd,
		r.hours,
		r.minutes,
		t. STATUS
	FROM
		llx_factory_proccess AS t
	LEFT JOIN llx_factory_proccess_treatment as r on r.fk_factory_proccess=t.rowid
	INNER JOIN llx_product AS p ON t.fk_product = p.rowid
	INNER JOIN llx_propal AS pr ON t.fk_propal = pr.rowid
	LEFT JOIN llx_factory_operator AS o ON r.fk_operator = o.rowid
	WHERE fk_propal='.$idPro.'
	and fk_product='.$idProd.';';
			//echo $string;
	$query=$db->query($string);
	$dat=$db->fetch_object($query);

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?idProp=' . $idPro.'&idProd='.$idProd, $langs->trans('Eliminar asignación de proceso'), $langs->trans('Desea eliminar esta asignación'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	if ($action == 'cerrar') {
		
		$html.= '<select name="fk_operator" id="fk_operator" class="fk_operator">';
			$operator= new Factorytools($db);
			$list=$operator->get_operators();
			$html.=  '<option value="-1">..Seleccione</option>';
			foreach ($list as $dat2) {
				$html.=  '<option value="'.$dat2->rowid.'">'.$dat2->name.'</option>';	
			}
		$html.=  '</select>';

		$formquestion = array(
	     array('type' => 'date', 'name' => 'dateEnd', 'id'=>'dateEnd', 'label' =>'Fecha de finalización' ),
	     array('type' => 'text', 'name' => 'horas', 'id'=>'horas', 'label' =>'Horas  ' ),
	     array('type' => 'text', 'name' => 'minutos', 'id'=>'minutosn', 'label' =>'Minutos  ' ),
	     array('type' => 'other', "name"=>"fk_operator" ,"id"=>"fk_operator",'label' =>'Operador','value' => $html )
	    );

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?idProp=' . $idPro.'&idProd='.$idProd, $langs->trans('Finalizar proceso'), '<b>Inicializado: '.dol_print_date($dat2->dateStart,"dayhour").'</b>' , 'confirm_cerrar',  $formquestion, 0, 1,250);
		print $formconfirm;
	}

	if ($action == 'cerrarTrat') {
		
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?idProp=' . $idPro.'&idProd='.$idProd, $langs->trans('Enviar a tratamiento'), $langs->trans('Desea enviar el procceso a tratamiento '), 'confirm_cerrarTrat',  $formquestion, 0, 1);
		print $formconfirm;
	}
	if ($action == 'asignar') {
		$procceso=GETPOST("idproccess");
		date_default_timezone_set('America/Mexico_City');
		$dateop=strtotime( date('Y-m-d h:i'));
		$pre= ($form->select_date($dateop,'dateStart',1, 1, 0, "", 1, 0, 1, 0, '', '', ''));
		$formquestion = array(
	     array('type' => 'other', 'name' => 'dateStart', 'id'=>'dateStart', 'label' =>'Fecha Inicialización', 'value' => $pre ),
	     array('type' => 'other', 'name' => 'dateStarthour', 'id'=>'dateStarthour'),
	     array('type' => 'other', 'name' => 'dateStartmin', 'id'=>'dateStartmin'),
	     array('type' => 'other', 'name' => 'dateStartmonth', 'id'=>'dateStartmonth'),
	     array('type' => 'other', 'name' => 'dateStartyear', 'id'=>'dateStartyear'),
	     array('type' => 'other', 'name' => 'dateStartday', 'id'=>'dateStartday')
	    );
		
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?idProp=' . $idPro.'&idProd='.$idProd.'&idproccess='.$procceso, $langs->trans('Nueva asignación'), $langs->trans('Desea iniciar el tratamiento?'), 'confirm_asignar',  $formquestion, 0, 1);
		print $formconfirm;
	}

	
	
	print '<table class="border centpercent">'."\n";
	
		print '<tr><td class="fieldrequired"  width="25%" >'.$langs->trans("Presupuesto").'</td><td>'.$dat->refPro.'</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Producto").'</td><td>'.$dat->refProd.'</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Operador").'</td><td>'.$dat->ref.' '.$dat->NAME.' '.$dat->lastname.'</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fecha inicio").'</td><td>'.$dat->dateStart.'</td></tr>';

		if ($dat->STATUS==6)
		{
			print '<tr><td class="fieldrequired">'.$langs->trans("Fecha fin").'</td><td>'.$dat->dateEnd.'</td></tr>';
			print '<tr><td class="fieldrequired">'.$langs->trans("Horas").'</td><td>'.$dat->hours.'</td></tr>';
			print '<tr><td class="fieldrequired">'.$langs->trans("Minutos").'</td><td>'.$dat->minutes.'</td></tr>';
		}
	print '</table>';
	
	print '<br/>';

		if ($user->rights->factory->maqui && $dat->STATUS==4)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&idproccess='.$dat->rowid.'&action=asignar">Asignar Tratamiento</a></div>'."\n";
		}
	
		if ($user->rights->factory->maqui && $dat->STATUS==5)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=cerrar">'.$langs->trans('Finalizar').'</a></div>'."\n";
		}

		if ($user->rights->factory->maqui && $dat->STATUS==5)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view&action2=Interrumpir">'.$langs->trans('Interrumpir').'</a></div>'."\n";
		}

		if ($user->rights->factory->maqui && $dat->STATUS==5)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
		
		/*if ($user->rights->factory->maqui && $dat->STATUS==2)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=cerrarTrat">'.$langs->trans('Enviar a tratamiento').'</a></div>'."\n";
		}*/

		if ($user->rights->factory->maqui && $dat->STATUS==7)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view&action2=Reanudar"">'.$langs->trans('Reanudar').'</a></div>'."\n";
		}
	
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

	$act='view2';

}

if($action2=="Reanudar"){
	
	print '<br/>';
	print '<fieldset>';
		print '<br/>';
		print load_fiche_titre($langs->trans("Reanudación del proceso"));	
		print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view">';			
		print '<table class="border centpercent"  width="100%">';						
			print '<tr>';
				print '<td width=20% align="left"><b>'.$langs->trans("Fecha de reanudación").'</b></td>';
				print '</td>';
				print '<td width=50% >';	   
					if($dateop==''){				
						date_default_timezone_set('America/Mexico_City');
						$dateop=strtotime( date('Y-m-d h:i'));
					}		
						print ($form->select_date($dateop,'dateReanudar' ,1, 1, 0, "", 1, 0, 1, 0, '', '', ''));										
				print '</td>';					
			print '</tr>';										
		print '</table>';
		//print '<a href="factorytools_card.php?id='.$id.'&action=view&action2=updateComment&idP='.$idP.'" class="button">Guardar</a>';		
		print '<div  align="center">';
			print '<br/><input type="submit" class="button" value="Guardar">';
		print '</div>';
		print '</form>';			
	print '</fieldset>';
	print '<br/>';	

	$act='view2';
}

if(GETPOST('dateReanudar')){	
	
	$date=GETPOST('dateReanudar','alpha');
	$hrs=GETPOST('dateReanudarhour','alpha');
	$min=GETPOST('dateReanudarmin','alpha');
	
	$dateStart=$date.' '.$hrs.':'.$min;

	if (isset($dateStart) && strcmp($dateStart, '')!=0) {
		$fecha = trim($dateStart);
		$aux=str_replace('/','-',$fecha);
		$dat=date('Y-m-d H:i',strtotime($aux));
		
		$sql='SELECT rowid from llx_factory_proccess where fk_propal='.$idPro.' and fk_product='.$idProd.';';		
		$query=$db->query($sql);
		$data=$db->fetch_object($query);
		$id=$data->rowid;

		

		$maxId='SELECT MAX(rowid) as id FROM '.MAIN_DB_PREFIX.'factory_proccess_interruption where fk_proccess='.$id.';';
		$resmaxId=$db->query($maxId);
		$datmasId=$db->fetch_object($resmaxId);

		$sql='UPDATE llx_factory_proccess_interruption set dateEnd="'.$dat.'" where rowid='.$datmasId->id.';';	
		$query=$db->query($sql);	

		$string ='UPDATE llx_factory_proccess						
						set  STATUS = 5
						WHERE
							rowid ='.$id;
			$query=$db->query($string);
	

		print '<script>window.location.href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view"</script>';
		
	}else{
		print "<script>alert('Seleccione la fecha de reanudación')</script>";	
	}

	$act='view2';
	
}

if($action2=="Interrumpir"){
	
	print '<br/>';
	print '<fieldset>';
		print '<br/>';
		print load_fiche_titre($langs->trans("Interrupción del tratamiento"));	
		print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view">';			
		print '<table class="border centpercent"  width="100%">';						
			print '<tr>';
				print '<td width=20% align="left"><b>'.$langs->trans("Fecha de la interrupcion").'</b></td>';
				print '</td>';
				print '<td width=50% >';	   
					if($dateop==''){				
						date_default_timezone_set('America/Mexico_City');
						$dateop=strtotime( date('Y-m-d h:i'));
					}		
						print ($form->select_date($dateop,'dateStart' ,1, 1, 0, "", 1, 0, 1, 0, '', '', ''));										
				print '</td>';					
			print '</tr>';	
			print '<tr>';
				print '<td width=20% align="left"><b>'.$langs->trans("Motivo de la interrupcion").'</b></td>';
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
	print '<br/>';
	$act='view2';
}

if($comment){	
	
	$date=GETPOST('dateStart','alpha');
	$hrs=GETPOST('dateStarthour','alpha');
	$min=GETPOST('dateStartmin','alpha');
	
	$dateStart=$date.' '.$hrs.':'.$min;

	if (isset($dateStart) && strcmp($dateStart, '')!=0) {
		$fecha = trim($dateStart);
		$aux=str_replace('/','-',$fecha);
		$dat=date('Y-m-d H:i',strtotime($aux));
		$sql='SELECT rowid from llx_factory_proccess where fk_propal='.$idPro.' and fk_product='.$idProd.';';		
		$query=$db->query($sql);
		$data=$db->fetch_object($query);

		$sql='insert into llx_factory_proccess_interruption(fk_proccess, dateIni, comment) values('.$data->rowid.',"'.$dat.'","'.$comment.'");';	
		$query=$db->query($sql);	

		$string ='UPDATE llx_factory_proccess						
						set  STATUS = 7
						WHERE
							rowid ='.$data->rowid;
			$query=$db->query($string);
	

		print '<script>window.location.href="'.$_SERVER["PHP_SELF"].'?idProp='.$idPro.'&idProd='.$idProd.'&action=view"</script>';
		
	}else{
		print "<script>alert('Seleccione la fecha de interrupcion')</script>";	
	}

	$act='view2';
	
}

if($act=='view2'){
	
	print '<br/>';
	print '<fieldset>';		
	print load_fiche_titre($langs->trans("Detalle interrupciones"));

	$sql='SELECT rowid from llx_factory_proccess where fk_propal='.$idPro.' and fk_product='.$idProd.';';		
	$query=$db->query($sql);
	$data=$db->fetch_object($query);

	$string='SELECT
					*
				FROM
					llx_factory_proccess_interruption
				where fk_proccess='.$data->rowid.';';
				//echo $string;
		$query=$db->query($string);
		
	
	print '<form name="productsFac"  method="post">';
		print '<table class="border"  width="100%">';		
			print '<tr class="liste_titre">';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Num.").'</td>';
				print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Fecha interrupción").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Fecha reanudación").'</td>';
				print '<td class="liste_titre" width=50px align="left">'.$langs->trans("Motivo").'</td>'; 	 						
			print '</tr>';	
					
			$cont=1;
			while ($dat=$db->fetch_object($query)) {
				print '<tr ">';
					print '<td align="left">'.$cont.'</td>';
					print '<td align="left">'.$dat->dateIni.'</td>';
					print '<td align="left">'.$dat->dateEnd.'</td>';
					print '<td align="left">'.$dat->comment.'</td>';													
				print '</tr>';	
				$cont++;
			}	
		print '</table>';		
	print '<form>';
	print '</fieldset>';		
		print '<br/>';
}

dol_fiche_end();
// End of page
llxFooter();
$db->close();
