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
 *   	\file       ctrlanticipo/ctrladvancecredit_card.php
 *		\ingroup    ctrlanticipo
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-03 18:11
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
dol_include_once('/ctrlanticipo/class/ctrladvancecredit.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');
dol_include_once('/ctrlanticipo/class/ctrlrefundcredit.class.php');
dol_include_once('/ctrlanticipo/libs/advance.lib.php');

// Load traductions files requiredby by page
$langs->load("ctrlanticipo");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$cid		= GETPOST('cid','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$confirm	= GETPOST('confirm','alpha');




// Load object if id or ref is provided as parameter
$anticipo=new Ctrladvanceprovider($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$anticipo->fetch($id);
	if ($result < 0) dol_print_error($db);
	
	if ($result) {

		$proveedor = new Societe($db);
    	$proveedor->fetch($anticipo->fk_soc);
	}
}

if ($cid>0) {
	$split=new Ctrladvancecredit($db);
	$split->fetch($cid);
}



// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('ctrladvancecredit'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/




$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$anticipo,$action);    // Note that $action and $anticipo may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add_credit' && $confirm=="yes")
	{

		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		$error=0;



		//inputs split
		$ammount_1=GETPOST("amount_1");
		$ammount_2=GETPOST("amount_2");
		$tot_ammount=$ammount_1+$ammount_2;


		$credit_new1=new Ctrladvancecredit($db);
		$credit_new1->fk_advance=$id;
		$credit_new1->import=100*$ammount_1/($split->fk_tva+100);
		$credit_new1->fk_tva=$split->fk_tva;
		$credit_new1->fk_parent=$split->id;
		$credit_new1->fk_soc=$split->fk_soc;
		$credit_new1->total_import=$ammount_1;
		

		$credit_new2=new Ctrladvancecredit($db);
		$credit_new2->fk_advance=$id;
		$credit_new2->import=100*$ammount_2/($split->fk_tva+100);
		$credit_new2->fk_tva=$split->fk_tva;
		$credit_new2->fk_soc=$split->fk_soc;
		$credit_new2->fk_parent=$split->id;
		$credit_new2->total_import=$ammount_2;		

		if ($ammount_1<=0)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_view_credit_amm_imp1_not"), null, 'errors');
		}
		if ($ammount_2<=0)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_view_credit_amm_imp2_not"), null, 'errors');
		}

		if ($tot_ammount>$split->total_import)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_view_credit_amm_over"), null, 'errors');
		}
		if ($tot_ammount<0)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_view_credit_amm_over"), null, 'errors');
		}
		if ($tot_ammount>0 && $tot_ammount<$split->total_import )
		{
			$error++;
			setEventMessages($langs->trans("ctrl_view_credit_amm_noteq"), null, 'errors');
		}

		if (! $error)
		{
			$result1=$credit_new1->create($user);
			$result2=$credit_new2->create($user);

			if ($result > 0 && $result2 > 0)
			{
				// Creation OK



				$credit_new1->fk_brother_credit=$result2;
				$credit_new2->fk_brother_credit=$result1;
				$credit_new1->set_split_credit();
				$credit_new2->set_split_credit();
				$split->statut=4;
				$split->update($user);

				if ($split->fk_soc>0) {
					$credit_new1->fk_soc=$split->fk_soc;
					$credit_new1->fk_user_asign=$user->id;
					$credit_new1->set_reasigne();
					$credit_new2->fk_soc=$split->fk_soc;
					$credit_new2->fk_user_asign=$user->id;
					$credit_new2->set_reasigne();
				}


				setEventMessages($langs->trans("ctrl_refund_split_credit_succ"), null, 'mesgs');
				$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($anticipo->errors)) setEventMessages(null, $anticipo->errors, 'errors');
				else  setEventMessages($anticipo->error, null, 'errors');
				$action='create';
			}
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=0;
		$error=0;
		$id_prov=$split->fk_parent;

		$cur=get_node($id_prov);
		while ($cur!=NULL) {

			$split_left=new Ctrladvancecredit($db);
			$split_right=new Ctrladvancecredit($db);
			$split_left->fetch($cur[0]);
			$split_right->fetch($cur[1]);
			$split_left->statut;
			$split_right->statut;
			if (($split_left->statut==2 || $split_left->statut==3 || $split_right->statut==2 || $split_right->statut==3 )  ) {
				$error++;
				setEventMessages($langs->trans("ctrl_refund_sure_del_credit"), null, 'errors');
				break;
			}

			$left=get_node($cur[0]);
			$right=get_node($cur[1]);

			if ($left==NULL) {
				$cur=$right;
			}else{
				$pre=$left;
				while (get_node($pre[1])!=NULL && get_node($pre[1])!=$cur) {
					$x=get_node($pre[1]);
					$pre=get_node($x[1]);
				}

				if (get_node($pre[1])==NULL){
					echo $cur[0];
					$cur=get_node($cur[0]);

				}else{
					echo $cur[1];
					$cur=get_node($cur[1]);
				}
			}
		}

		if (! $error)
		{
			$cur=get_node($id_prov);
			$split_pat=new Ctrladvancecredit($db);
			$split_pat->fetch($id_prov);

			$split_pat->statut=1;
			$result=$split_pat->update($user);
			while ($cur!=NULL) {
				$split_left=new Ctrladvancecredit($db);
				$split_right=new Ctrladvancecredit($db);
				$split_left->fetch($cur[0]);
				$split_right->fetch($cur[1]);
				if ($split_left->statut==2 || $split_left->statut==3 || $split_right->statut==2 || $split_right->statut==3 ) {
					$error++;
					setEventMessages($langs->trans("ctrl_refund_sure_del_credit"), null, 'errors');
					break;
				}
				$left=get_node($cur[0]);
				$right=get_node($cur[1]);

				if ($left==NULL) {
					$cur=$right;
				}else{
					$pre=$left;
					while (get_node($pre[1])!=NULL && get_node($pre[1])!=$cur) {
						$x=get_node($pre[1]);
						$pre=get_node($x[1]);
					}

					if (get_node($pre[1])==NULL){
						echo $cur[0];
						$cur=get_node($cur[0]);

					}else{
						echo $cur[1];
						$cur=get_node($cur[1]);
					}
				}

				$split_left->delete($user);
				$split_right->delete($user);
			}
			
			
			if ($result > 0)
			{
				// Delete OK
				setEventMessages($langs->trans("ctrl_refund_del_credit_succ"), null, 'mesgs');
				header("Location: ".dol_buildpath('/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$id,1));
				exit;
			}
			else
			{

				if (! empty($anticipo->errors)) setEventMessages(null, $anticipo->errors, 'errors');
				else setEventMessages($anticipo->error, null, 'errors');
			}
		}else{
			$action="";
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record


	if ($action == 'asign_def' )
	{
		$error=0;
		$action='';

		if (! $error)
		{
			$split->fk_soc=GETPOST("fk_provider");
			$split->fk_user_asign=$user->id;
			
			$result=$split->set_reasigne();

			if ($result > 0)
			{
				setEventMessages($langs->trans("ctrl_refund_asign_success"), null, 'mesgs');
			}
			else
			{
				// Creation KO
				if (! empty($anticipo->errors)) setEventMessages(null, $anticipo->errors, 'errors');
				else setEventMessages($anticipo->error, null, 'errors');
			}
		}
	}


	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$anticipo->fk_advance=GETPOST('fk_advance','int');
		$anticipo->import=GETPOST('import','alpha');
		$anticipo->fk_tva=GETPOST('fk_tva','int');
		$anticipo->total_import=GETPOST('total_import','alpha');
		$anticipo->fk_user_agree=GETPOST('fk_user_agree','int');

		if (empty($anticipo->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$anticipo->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($anticipo->errors)) setEventMessages(null, $anticipo->errors, 'errors');
				else setEventMessages($anticipo->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans("ctrl_view_tit_credit_view"),'');

$form=new Form($db);


// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete' || $action == 'split' || $action == 'add_credit' || $action=='asign' || $action=="asign_step2" ))
{
	//print load_fiche_titre($langs->trans("MyModule"));

    $linkback = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$id.'">'.$langs->trans("ctrl_view_credit_back").'</a>';
    $head = advance_prepare_head($anticipo);
    //print load_fiche_titre($langs->trans("ctrl_view_tit_credit_fiche"),'','');

	dol_fiche_head($head, 'ad_cred', $langs->trans("ctrl_advance_note"), 0, 'payment');
	
		dol_banner_tab($proveedor, 'id', $linkback, 0, 'rowid', 'nom');

		if ($action == 'delete') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id."&cid=".$cid, $langs->trans('ctrl_refund_delete_credit'), $langs->trans('ctrl_refund_conf_delete_credit'), 'confirm_delete', '', 0, 1);
			print $formconfirm;
		}
		
		print '<table class="border centpercent">';
		print '
		<tr>
			<td class="fieldrequired">'.$langs->trans("ctrl_view_credit_amount_tit").'</td>
			<td>'.$anticipo->getNomUrl(0).'</td>
			<td>'.$anticipo->import." ";
			if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
				$sql='SELECT a.label FROM llx_multidivisa_divisas as a WHERE a.rowid='.$anticipo->fk_mcurrency;
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
			print '</td>
			<td>
			'.$langs->trans("ctrl_view_statuts")." ".img_picto($langs->trans('ctrl_action_statut'.$anticipo->statut),'statut'.$anticipo->statut)."  ".$langs->trans('ctrl_action_statut'.$anticipo->statut).'
			</td>';
		print '
		</tr>';
		print '</table>';
	
	dol_fiche_end();

	//creditos SIN aplicar
	print load_fiche_titre($langs->trans("ctrl_view_credit_not_apply"));
	print '<table class="border centpercent">';
		print '<tr class="liste_titre">';
            print '<td align="center">'.$langs->trans('Date').'</td>';
            print '<td align="center">'.$langs->trans('ctrl_view_credit_soc_assigned').'</td>';
            print '<td align="right"></td>';
            print '<td align="right">'.$langs->trans('ctrl_view_credit_base').'</td>';

            print '<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$langs->trans('ctrl_view_credit_rate').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_view_credit_total_imp').'</td>';
            print '<td align="left" colspan=4>&nbsp;&nbsp;&nbsp;&nbsp;'.$langs->trans('ctrl_view_credit_agreedby').'</td>';
        print "</tr>";
        $sql="SELECT a.* FROM llx_ctrl_advance_credit as a WHERE a.statut=1 and a.fk_advance=".$id;
        //echo $sql;
        $resql=$db->query($sql);
		if ($resql) {
			$num = $db->num_rows($resql);
			if ($num>0) {

				while ($objp = $db->fetch_object($resql) ) {
						$prov_adv_cred=new Ctrladvanceprovider($db);
			            $prov_adv_cred->fetch($objp->fk_advance);
					
						if ($objp->rowid==$cid) {
							print '<tr class="pair" >';
						}else{
							print '<tr class="impair" >';
						}
						
			            if ($objp->fk_user_asign>0) {
			            	$proveedor2 = new Societe($db);
    						$proveedor2->fetch($objp->fk_soc);
    						print '<td align="center">'.dol_print_date($db->jdate($objp->date_asign),'%H:%M %p <br> %d/%m/%Y').'</td>';
			            	print '<td align="center">'.$proveedor2->getNomUrl(1);
			            }else{
			            	print '<td align="right">'.dol_print_date($db->jdate($objp->date_c),'%H:%M %p <br> %d/%m/%Y').'</td>';
			            	print '<td align="right">'.$proveedor->getNomUrl(1);
			            }
			            print '</td>';
			            print '<td align="center">';
			            print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="ctrladvancecredit_card.php?id='.$id.'&cid='.$objp->rowid.'&action=asign">'.img_picto('','edit').' '.$langs->trans('ctrl_view_credit_reasign').'</a>';
			            print '</td>';
			            print '<td align="right">'.price($objp->import).'</td>';
			            print '<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$objp->fk_tva.'%</td>';
			            print '<td align="right">'.price($objp->total_import).'</td>';
			            $user_prov=new User($db);
			            $user_prov->fetch($objp->fk_user_agree);
			            print '<td align="center">'.$user_prov->getNomUrl(1).'</td>';
			            print '<td align="center">';
			            print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&cid='.$objp->rowid.'&action=split">'.img_picto('','split').' '.$langs->trans('ctrl_view_credit_divide').'</a>';
			            print '</td>';
			            print '<td align="center">';


			            if ($objp->fk_soc!=$prov_adv_cred->fk_soc && $objp->fk_user_asign>0) {
			            	print img_picto('','uparrow').' <strike>'.$langs->trans('ctrl_view_credit_refund').'</strike>';
			            }else{
			            	
			            	print '<a href="ctrlrefundcredit_card.php?id='.$objp->rowid.'&cid='.$id.'&action=create">'.img_picto('','uparrow').' '.$langs->trans('ctrl_view_credit_refund').'</a>';
			            }


			            print '</td>';
			            print '<td align="center">';

			            if ($objp->fk_brother_credit>0) {
			            	$split_new=new Ctrladvancecredit($db);
							$split_new->fetch($objp->fk_brother_credit);
							if ($objp->fk_parent>0 ) {
								print '<a href="ctrladvancecredit_card.php?id='.$id.'&cid='.$objp->rowid.'&action=delete">'.img_picto('','delete').'</a>';
							}else{
								print img_picto('','delete','style="opacity: 0.5;"');
							}
			            }else{
			            	print img_picto('','delete','style="opacity: 0.5;"');
			            }
			            
			            print '</td>';
			        print "</tr>";
				}
			}
		}
	print '</table><br>';

	if (($action=="split" || ($action=="add_credit" && $confirm=="yes") ) && $cid>0) {
		$imp=$split->total_import/2;
		print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		    print '<input type="hidden" name="action" value="add_credit">';
		    print '<input type="hidden" name="id" value="'.$id.'">';
		    print '<input type="hidden" name="cid" value="'.$cid.'">';
		    print '<input type="hidden" name="fk_tva" value="'.$split->fk_tva.'">';
			print '
			<table width="100%" class="valid">
				<tbody>
					<tr class="validtitre">
						<td class="validtitre" colspan="3">
							'.img_picto('','recent').'
							'.$langs->trans('ctrl_view_credit_dpo').'
						</td>
					</tr>
					<tr class="valid">
						<td class="valid" colspan=3>
							'.$langs->trans('ctrl_view_credit_imp').'
						</td>
					</tr>
					<tr class="valid">
						<td class="valid">
							'.$langs->trans('ctrl_view_credit_timp').' 1
						</td>
						<td class="valid" align="left">
							<input type="text" size="8" name="amount_1" value="'.((empty($_POST['amount_1']) && !isset($_POST['amount_1']) ) ?$imp:GETPOST('amount_1')).'" >
						</td>
					</tr>
					<tr class="valid">
						<td class="valid">
							'.$langs->trans('ctrl_view_credit_timp').' 2
						</td>
						<td class="valid" align="left">
							<input type="text" size="8" name="amount_2" value="'.( (empty($_POST['amount_2']) && !isset($_POST['amount_2']) )?$imp:GETPOST('amount_2')).'" >
						</td>
					</tr>
					<tr class="valid">
						<td class="valid" colspan=2>
							'.$langs->trans('ctrl_view_credit_quest1').' '.price($split->total_import).' '.$label.'
							'.$langs->trans('ctrl_view_credit_quest2').'
						</td>
						<td class="valid" align="center">
							<select class="flat" id="confirm" name="confirm">
								<option value="yes" selected="">Sí</option>
								<option value="no">No</option>
							</select>
						</td>
						<td class="valid" align="center">
							<input class="button" type="submit" value="Validar">
						</td>
					</tr>
				</tbody>
			</table><br>';
		print '</form>';
	}

	if ($action=="asign"  && $cid>0) {
		$imp=$split->total_import/2;
		print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		    print '<input type="hidden" name="action" value="asign_def">';
		    print '<input type="hidden" name="id" value="'.$id.'">';
		    print '<input type="hidden" name="cid" value="'.$cid.'">';
			print '
			<table width="100%" class="valid">
				<tbody>
					<tr class="validtitre">
						<td class="validtitre" colspan="3">
							'.img_picto('','recent').'
							'.$langs->trans('ctrl_refund_asign_steps').'
						</td>
					</tr>

					<tr class="valid">
						<td class="valid">
							'.$langs->trans('ctrl_refund_asign_step1').' 
						</td>
						<td class="valid" align="right">
						'.($form->select_thirdparty_list('', 'fk_provider', 'fournisseur = 1', 0, '',1)).'
						</td>
						<td class="valid" align="right">
							<input class="button" type="submit" value="Aceptar">
						</td>
					</tr>
				</tbody>
			</table><br><br>';
		print '</form>';
	}
	

	
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

	//creditos YA aplicados
	print load_fiche_titre($langs->trans("ctrl_view_credit_apply"));
	print '<table class="border centpercent">';
		print '<tr class="liste_titre">';
            print '<td align="center">'.$langs->trans('Date').'</td>';
            print '<td align="center">'.$langs->trans('ctrl_view_credit_soc_assigned').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_view_credit_base').'</td>';
            print '<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$langs->trans('ctrl_view_credit_rate').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_view_credit_total_imp').'</td>';
            print '<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;'.$langs->trans('ctrl_view_credit_autor').'</td>';
            print '<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;'.$langs->trans('ctrl_view_statuts').'</td>';
        print "</tr>";
        $sql="SELECT a.* FROM llx_ctrl_advance_credit as a WHERE a.statut!=1 and a.statut!=4 and a.fk_advance=".$id;
        $resql=$db->query($sql);

		if ($resql) {
			$num = $db->num_rows($resql);
			if ($num>0) {
				while ($objp = $db->fetch_object($resql) ) {

						print '<tr class="impair" >';
					
					
			           if ($objp->fk_user_asign>0) {
			            	$proveedor2 = new Societe($db);
    						$proveedor2->fetch($objp->fk_soc);
    						print '<td align="center">'.dol_print_date($db->jdate($objp->date_asign),'%H:%M %p <br> %d/%m/%Y').'</td>';
			            	print '<td align="center">'.$proveedor2->getNomUrl(1);
			            }else{
			            	print '<td align="center">'.dol_print_date($db->jdate($objp->date_c),'%H:%M %p <br> %d/%m/%Y').'</td>';
			            	print '<td align="center">'.$proveedor->getNomUrl(1);
			            }
			            print '<td align="right">'.price($objp->import).'</td>';
			            print '<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$objp->fk_tva.'%</td>';
			            print '<td align="right">'.price($objp->total_import).'</td>';
			            $user_prov=new User($db);
			            $user_prov->fetch($objp->fk_user_agree);
			            print '<td align="center">'.$user_prov->getNomUrl(1).'</td>';
			            print '<td align="center">';
			            print $langs->trans('ctrl_refund_ststut_'.$objp->statut);
			            $sql="SELECT a.* FROM llx_ctrl_refund_credit as a WHERE a.fk_credit=".$objp->rowid;
				        $res=$db->query($sql);

						if ($res) {
							$nu = $db->num_rows($res);
							if ($nu>0) {
								$ob = $db->fetch_object($res);
								$rembolso=new Ctrlrefundcredit($db);
					            $rembolso->fetch($ob->rowid);
					            print "&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;".$rembolso->getNomUrl(1)."&nbsp;&nbsp;)";
							}
						}

						$sql='SELECT
							c.rowid as fk_target
						FROM
							llx_element_element AS a
						INNER JOIN llx_paiementfourn_facturefourn as b on b.rowid=a.fk_target
						INNER JOIN llx_facture_fourn AS c on c.rowid=b.fk_facturefourn
						WHERE
							a.targettype = "paiementfourn_facturefourn"
						AND a.fk_source = '.$objp->rowid;
				        $res=$db->query($sql);
						if ($res) {
							$nu = $db->num_rows($res);
						
							if ($nu>0) {
								$ob = $db->fetch_object($res);
								$rembolso=new FactureFournisseur($db);
					            $rembolso->fetch($ob->fk_target);
					            
					            print "&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;".$rembolso->getNomUrl(1)."&nbsp;&nbsp;)";
							}
						}
			            print '</td>';
			        print "</tr>";
				}
			}
		}
	print '</table><br>';



	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$anticipo,$action);    // Note that $action and $anticipo may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->ctrlanticipo->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$anticipo->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->ctrlanticipo->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$anticipo->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($anticipo);
	//$linktoelem = $form->showLinkToObjectBlock($anticipo);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
