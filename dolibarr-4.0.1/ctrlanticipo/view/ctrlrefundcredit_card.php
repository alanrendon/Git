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
 *   	\file       ctrlanticipo/ctrlrefundcredit_card.php
 *		\ingroup    ctrlanticipo
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-06 17:51
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


include_once(DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php');


dol_include_once('/ctrlanticipo/class/ctrlrefundcredit.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvancecredit.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');

// Load traductions files requiredby by page
$langs->load("ctrlanticipo");
$langs->load("other");
$langs->load('bills');
$langs->load('banks');
$langs->load('companies');

// Get parameters
$id			= GETPOST('id','int');
$cid	    = GETPOST('cid','int');
$cid		= GETPOST('cid','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$credit=new Ctrladvancecredit($db);
$refund= new Ctrlrefundcredit($db);
$advance=new Ctrladvanceprovider($db);
if ($id > 0 )
{
	$result=$credit->fetch($id);
	if ($result) {
		
		$advance->fetch($credit->fk_advance);
		$societe= new Societe($db);
		$societe->fetch($advance->fk_soc);
	}
	if ($result < 0) dol_print_error($db);
}
if ($cid > 0 )
{
	$result=$refund->fetch($cid);
	if ($result) {
		
		$credit->fetch($refund->fk_credit);
		$advance->fetch($credit->fk_advance);
		$societe= new Societe($db);

		$societe->fetch($refund->fk_soc);
	}
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('ctrlrefundcredit'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$credit,$action);    // Note that $action and $credit may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$advance->rowid,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		$result=0;
		/* object_prop_getpost_prop */
		$refund->fk_soc=$advance->fk_soc;
		$refund->fk_credit=$id;
		$refund->fk_paymen=GETPOST('fk_paymen','int');
		$refund->fk_bank_account=GETPOST('fk_bank_account','int');
		$refund->date_apply=GETPOST('date_apply');

		$refund->date_apply=dol_mktime(0, 0, 0, GETPOST('date_applymonth'), GETPOST('date_applyday'), GETPOST('date_applyyear'));

		
		$refund->num_paiment=GETPOST('num_paiment','alpha');
		$refund->transfer=GETPOST('transfer','alpha');
		$refund->bank=GETPOST('bank','alpha');
		$refund->note=GETPOST('note','alpha');


		if (empty($refund->date_apply))
		{
			$error++;
			setEventMessages($langs->trans("ctrl_error_payment_date",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($refund->fk_paymen==0)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_error_payment_type",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($refund->fk_bank_account<0)
		{
			$error++;
			setEventMessages($langs->trans("ctrl_error_refound_bank",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{ 
			$result=$refund->create($user);
			if ($result > 0)
			{
				
				// Creation OK
				$credit->statut=3;
				$credit->update($user);
				$credit->change_value_advance($user);
				$label='ctrl_customer_refund';
				$refund->fetch($result);
				$var= $refund->addPaymentToBank($user,$credit->total_import,'payment',$label,GETPOST('fk_bank_account'),GETPOST('transfer'),GETPOST('bank'));
				
				setEventMessages($langs->trans("ctrl_alert_refund_created"), null);
				$urltogo=$backtopage?$backtopage:dol_buildpath('/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$cid,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($credit->errors)) setEventMessages(null, $credit->errors, 'errors');
				else  setEventMessages($credit->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$credit->fk_soc=GETPOST('fk_soc','int');
		$credit->fk_paymen=GETPOST('fk_paymen','int');
		$credit->fk_bank_account=GETPOST('fk_bank_account','int');
		$credit->num_paiment=GETPOST('num_paiment','alpha');
		$credit->transfer=GETPOST('transfer','alpha');
		$credit->bank=GETPOST('bank','alpha');
		$credit->note=GETPOST('note','alpha');

		if (empty($credit->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$credit->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($credit->errors)) setEventMessages(null, $credit->errors, 'errors');
				else setEventMessages($credit->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$id_advance=$credit->fk_advance;
		$result=$refund->delete($user);
		if ($result > 0)
		{
			// Delete OK
			$credit->statut=1;
			$credit->update($user);
			$credit->change_value_advance($user);
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$id_advance,1));
			exit;
		}
		else
		{
			if (! empty($credit->errors)) setEventMessages(null, $credit->errors, 'errors');
			else setEventMessages($credit->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('ctrl_view_refund_credit_tit'),'');

$form=new Form($db);


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


// Part to create
if ($action == 'create' && $id > 0)
{
	print load_fiche_titre($langs->trans("ctrl_view_refund_credit"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="cid" value="'.$cid.'">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//

	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_fk_soc").'</td>
		<td colspan=2>'.$societe->getNomUrl(1).'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_date_advance").'</td>
		<td>';
			 print $form->select_date($refund->date_apply, 'date_apply', 0, 0, 0, "", 1, 1, 1, 0, '', '', '');
 print '</td>

		<td>'.$langs->trans("ctrl_comments").'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_fk_paymen").'</td>
		<td>';
			print $form->select_types_paiements($refund->fk_paymen, 'fk_paymen', '', 0, 0, 0, 0, 1);
 print '</td>
		
		<td colspan=2 rowspan=3><textarea name="note" class="flat" style="width:97%;" rows="6" cols="50" >'.$refund->note.'</textarea></td>
	</tr>';

	// Bank account
    print '<tr>';
    if (! empty($conf->banque->enabled))
    {
        print '<td><span class="fieldrequired">'.$langs->trans('AccountToDebit').'</span></td>';
        print '<td>';
        $filter="";
        if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
            $sql = "SELECT DISTINCT rowid,code,label as lastname,unicode";
            $sql.= " FROM llx_multidivisa_divisas as u WHERE rowid=".$advance->fk_mcurrency;
            $sql.= " ORDER BY label ASC";
            $resql=$db->query($sql);
            if ($resql)
            {
                $num = $db->num_rows($resql);
                if ($num>0) {
                    $obj = $db->fetch_object($resql);
                    $filter=" currency_code = '".$obj->code."' ";
                }
            }
            
        }

        $form->select_comptes($refund->fk_bank_account,'fk_bank_account',0,$filter,2);
        print '</td>';
    }
    else
    {
        print '<td colspan="2">&nbsp;</td>';
    }
    print "</tr>\n";
	print '
	<tr>
		<td>'.$langs->trans("ctrl_num_trans").'</td>
		<td><input class="flat" type="text" size=9 name="num_paiment" value="'.$refund->num_paiment.'"></td>
	</tr>';
	
	print '
	<tr>
		<td>'.$langs->trans("ctrl_transfer").'</td>
		<td><input class="flat" type="text" name="transfer" value="'.$refund->transfer.'"></td>
		<td colspan=2 rowspan=2>
			<table class="noborder" width="100%">
				<tr class="liste_titre">
					<td>'.$langs->trans('ctrl_note_advance').'</td>
					<td align="center">'.$langs->trans('ctrl_date_advance').'</td>
					<td align="center">'.$langs->trans('ctrl_view_refund_import').'</td>';
					if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
						print '<td align="right">'.$langs->trans('ctrl_multi_cash').'</td>';
					}
				print '
				</tr>
				<tr>
					<td>'.$advance->getNomUrl(1).'</td>
					<td align="center">'.dol_print_date($credit->date_c,'%H:%M %p <br> %d/%m/%Y').'</td>
					<td align="center">'.price($credit->total_import).'</td>';
					if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
						if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
							$sql='SELECT a.code FROM llx_multidivisa_divisas as a WHERE a.rowid='.$advance->fk_mcurrency;
							$resql=$db->query($sql);
							if ($resql) {
								$num = $db->num_rows($resql);
								if ($num>0) {
									$objp = $db->fetch_object($resql);
									$label=$objp->code;
									print '<td align="right">'.$label.'</td>';
								}
							}
							
						}
					}
				print '
				</tr>
			</table>
		</td>
	</tr>';
	print '
	<tr>
		<td>'.$langs->trans("ctrl_bank_pay").'</td>
		<td><input class="flat" type="text" name="bank" value="'.$refund->bank.'"></td>
	</tr>';


	print '</table>'."\n";

	dol_fiche_end();

	print '
	<div class="center">
		<input type="submit" class="button" name="add" value="'.$langs->trans("ctrl_view_refund").'"> &nbsp; 
	</div>';

	print '</form>';
}




// Part to show record
if ($cid && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("ctrl_view_refund_credit_view"));

	dol_fiche_head();
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?cid='.$cid, $langs->trans('ctrl_delete_credit_refund'), $langs->trans('ctrl_conf_delete_credit'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("Ref").'</td>
		<td colspan=2><b>'.$refund->ref.'</b></td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_fk_soc").'</td>
		<td colspan=2>'.$societe->getNomUrl(1).'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_date_advance").'</td>
		<td>';
			 print $form->select_date($refund->date_apply, 'date_apply', 0, 0, 0, "", 1, 1, 1, 1);
 print '</td>

		<td>'.$langs->trans("ctrl_comments").'</td>
	</tr>';
	print '
	<tr>
		<td class="fieldrequired">'.$langs->trans("ctrl_fk_paymen").'</td>
		<td>';
			print $form->select_types_paiements($refund->fk_paymen, 'fk_paymen', '', 0, 0, 0, 0, 1);
 print '</td>
		
		<td colspan=2 rowspan=3><textarea disabled name="note" class="flat" style="width:97%;" rows="6" cols="50" >'.$refund->note.'</textarea></td>
	</tr>';

	// Bank account
    print '<tr>';
    if (! empty($conf->banque->enabled))
    {
        print '<td><span class="fieldrequired">'.$langs->trans('AccountToDebit').'</span></td>';
        print '<td>';
        $filter="";
        if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
            $sql = "SELECT DISTINCT rowid,code,label as lastname,unicode";
            $sql.= " FROM llx_multidivisa_divisas as u WHERE rowid=".$advance->fk_mcurrency;
            $sql.= " ORDER BY label ASC";
            $resql=$db->query($sql);
            if ($resql)
            {
                $num = $db->num_rows($resql);
                if ($num>0) {
                    $obj = $db->fetch_object($resql);
                    $filter=" currency_code = '".$obj->code."' ";
                }
            }
            
        }
        $account= new Account($db);
        $account->fetch($refund->fk_bank_account);
        print $account->getNomUrl(1);
        //$form->select_comptes($refund->fk_bank_account,'fk_bank_account',0,$filter,2);
        print '</td>';
    }
    else
    {
        print '<td colspan="2">&nbsp;</td>';
    }
    print "</tr>\n";








	$bankline=new AccountLine($db);
	if ($refund->fk_bank_line>0) {
		$res_line_bank=$bankline->fetch($refund->fk_bank_line);
		if ($res_line_bank) {
			if ($bankline->rappro)
	        {
	            $disable_delete = 1;
	            $title_button = dol_escape_htmltag($langs->transnoentitiesnoconv("CantRemoveConciliatedPayment"));
	        }

	    	print '<tr>';
	    	print '<td>'.$langs->trans('BankTransactionLine').'</td>';
			print '<td colspan="3">';
			print $bankline->getNomUrl(1,0,'showconciliated');
	    	print '</td>';
	    	print '</tr>';

			if ($object->type_code == 'CHQ' && $bankline->fk_bordereau > 0) 
			{
				dol_include_once('/compta/paiement/cheque/class/remisecheque.class.php');
				$bordereau = new RemiseCheque($db);
				$bordereau->fetch($bankline->fk_bordereau);
				print '<tr>';
		    	print '<td>'.$langs->trans('CheckReceipt').'</td>';
				print '<td colspan="3">';
				print $bordereau->getNomUrl(1);
		    	print '</td>';
		    	print '</tr>';
			}
		}
	}





	print '
	<tr>
		<td>'.$langs->trans("ctrl_num_trans").'</td>
		<td>'.$refund->num_paiment.'</td>
	</tr>';
	
	print '
	<tr>
		<td>'.$langs->trans("ctrl_transfer").'</td>
		<td>'.$refund->transfer.'</td>
		<td colspan=2 rowspan=2>
			<table class="noborder" width="100%">
				<tr class="liste_titre">
					<td>'.$langs->trans('ctrl_note_advance').'</td>
					<td align="center">'.$langs->trans('ctrl_date_advance').'</td>
					<td align="center">'.$langs->trans('ctrl_view_refund_import').'</td>';
					if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
						print '<td align="right">'.$langs->trans('ctrl_multi_cash').'</td>';
					}
				print '
				</tr>
				<tr>
					<td>'.$advance->getNomUrl(1).'</td>
					<td align="center">'.dol_print_date($credit->date_c,'%H:%M %p <br> %d/%m/%Y').'</td>
					<td align="center">'.price($credit->total_import).'</td>';
					if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
						if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
							$sql='SELECT a.code FROM llx_multidivisa_divisas as a WHERE a.rowid='.$advance->fk_mcurrency;
							$resql=$db->query($sql);
							if ($resql) {
								$num = $db->num_rows($resql);
								if ($num>0) {
									$objp = $db->fetch_object($resql);
									$label=$objp->code;
									print '<td align="right">'.$label.'</td>';
								}
							}
							
						}
					}
				print '
				</tr>
			</table>
		</td>
	</tr>';
	print '
	<tr>
		<td>'.$langs->trans("ctrl_bank_pay").'</td>
		<td>'.$refund->bank.'</td>
	</tr>';


	print '</table>'."\n";
	
	dol_fiche_end();

	print '<div class="tabsAction">';
	if ($user->rights->ctrlanticipo->ctrlanticipo7->deletepayment) {
		print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?cid='.$cid.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
	}
	print '</div>';
}


// End of page
llxFooter();
$db->close();
