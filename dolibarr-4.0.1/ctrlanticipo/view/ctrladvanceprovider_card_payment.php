<?php
$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
    $res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php');
include_once(DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceprovider.class.php');
dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';
require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/libs/advance.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';
// Load traductions files requiredby by page
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
$langs->load('ctrlanticipo@ctrlanticipo');
$langs->load('bills');
$langs->load('companies');
$langs->load('compta');
$langs->load('products');
$langs->load('banks');
$langs->load('main');
$langs->load('other');




// Get parameters
$id          = GETPOST('id', 'int');
$action      = GETPOST('action', 'alpha');
$backtopage  = GETPOST('backtopage');
$confirm     = GETPOST('confirm');
$extrafields = new ExtraFields($db);
$accountid   = GETPOST('accountid');
$selectfk_paymen= GETPOST('fk_paymen');

if ($id>0) {
    $advance = new Ctrladvanceprovider($db);
    $result  =$advance->fetch($id);
    $form    =new Form($db);
    $paymentnum=$_POST['num_paiement'];
    if ($action == 'confirm_paiement' && $confirm == 'yes'){
        $error = 0;

        //$datepaye = dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
        $paiement_id = 0;
        $totalpayment = 0;
        $atleastonepaymentnotnull = 0;
        $tmpinvoice=new FactureFournisseur($db);

        foreach ($_POST as $key => $value)
        {
            if (substr($key,0,7) == 'amount_')
            {
                $cursorfacid = substr($key,7);
                $amounts[$cursorfacid] = price2num(trim(GETPOST($key)));
                $totalpayment = $totalpayment + $amounts[$cursorfacid];
                if (! empty($amounts[$cursorfacid])) $atleastonepaymentnotnull++;
                //$result=$tmpinvoice->fetch($cursorfacid);
                if ($result <= 0) dol_print_error($db);
                //$amountsresttopay[$cursorfacid]=price2num($tmpinvoice->total_ttc - $tmpinvoice->getSommePaiement());

                if ($amounts[$cursorfacid])
                {
                    // Check amount
                    if ($amounts[$cursorfacid] && (abs($amounts[$cursorfacid]) > abs($amountsresttopay[$cursorfacid])))
                    {
                        $addwarning=1;
                        $formquestion['text'] = img_warning($langs->trans("PaymentHigherThanReminderToPaySupplier")).' '.$langs->trans("HelpPaymentHigherThanReminderToPaySupplier");
                    }
                }
                $formquestion[$i++]=array('type' => 'hidden','name' => $key,  'value' => $_POST[$key]);
            }
        }
        $mont=GETPOST('remonth');
        $day=GETPOST('reday');
        $year=GETPOST('reyear');

        $datepaye = dol_mktime(0, 0, 0, $mont, $day, $year);

        $db->begin();

        // Creation of payment line 
        $paiement                        = new PaiementAdvance($db);
        $paiement->datepaye              = $datepaye;
        $paiement->amounts               = $amounts;   // Array with all payments dispatching
        //$paiement->multicurrency_amounts = $multicurrency_amounts;   // Array with all payments dispatching
        $paiement->paiementid            = dol_getIdFromCode($db,$selectfk_paymen,'c_paiement');
        $paiement->num_paiement          = $_POST['num_paiement'];
        $paiement->note                  = $_POST['comment'];
        $paiement->bank_line             = $accountid;


        if (empty($mont) || empty($day) || empty($year))
        {
            $errors[]=$langs->trans("ctrl_error_payment_date");
            $error++; 
            $action = 'create';
        }

        if (empty($selectfk_paymen))
        {
            $errors[]=$langs->trans("ctrl_error_payment_type");
            $error++; 
            $action = 'create';
        }
        if ($accountid<0)
        {
            $errors[]=$langs->trans("ctrl_error_payment_bank");
            $error++; 
            $action = 'create';
        }
        if ($totalpayment==0)
        {
            $errors[]=$langs->trans("ctrl_error_payment_amount");
            $error++; 
            $action = 'create';
        }
        if (! $error)
        {
            $paiement_id = $paiement->create_payment($user, (GETPOST('closepaidinvoices')=='on'?1:0));
            


            if ($paiement_id < 0)
            {
                $error=$langs->trans($paiement->error);
                $errors=$paiement->errors;
            }else{
                foreach ($paiement->amounts as $key => $amount)
                {
                    $facid = $key;
                    if (is_numeric($amount) && $amount <> 0)
                    {
                        $amount = price2num($amount);
                    }
                    else
                    {
                        dol_syslog('PaiementFourn::Create Montant non numerique',LOG_ERR);
                    }
                }
                $paiement->fetch($paiement_id);
                $label='ctrl_customer_payment';

                //if (GETPOST('type') == 2) $label='(CustomerInvoicePaymentBack)';
                $result=$paiement->addPaymentToBank($user,'payment_supplier',$label,GETPOST('accountid'),GETPOST('chqemetteur'),GETPOST('chqbank'));

                if ($result < 0)
                {
                    setEventMessages($paiement->error, $paiement->errors, 'errors');
                    $error++;
                }

                $loc = DOL_URL_ROOT.'/ctrlanticipo/view/ctrlpaiementfourn_card.php?id='.$paiement_id;
                header('Location: '.$loc);
                exit;  
            }
        }


        if ($error){
            $db->rollback();
        }
    }
}


llxHeader('', $langs->trans('ctrlanticipo'));
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
    function init_myfunc()
    {
        jQuery("#myid").removeAttr(\'disabled\');
        jQuery("#myid").attr(\'disabled\',\'disabled\');
    }
    init_myfunc();
    jQuery(".AutoFillAmout").click(function() {
        var ob=$(this).attr("data-rowname");
        var money=$(this).attr("data-value");
        $("input[name="+ob+"]").val(money);
    });
});
</script>';


if ($action == 'create' || $action == 'confirm_paiement' || $action == 'add_paiement')
{


    $title=$langs->trans("ctrl_titre_advance_payment");
    print load_fiche_titre($title);

     // Affiche les erreurs
    dol_htmloutput_errors(is_numeric($error)?'':$error,$errors);

    print '<form id="payment_form" name="add_paiement" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add_paiement">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    dol_fiche_head();

    print '<table class="border" width="100%">';

    // Third party
    $prov=new Societe($db);
    $prov->fetch($advance->fk_soc);

    print '<tr><td><span class="fieldrequired">'.$langs->trans('Company').'</span></td><td colspan="2">'.$prov->getNomUrl(0)."</td></tr>\n";

    // Date payment
    print '<tr><td><span class="fieldrequired">'.$langs->trans('Date').'</span></td><td>';
    $form->select_date($advance->date_advance,'','','',0,"date_advance",1,1,0,0,'','');
    print '</td>';
    print '<td>'.$langs->trans('Comments').'</td></tr>';

    $rowspan=5;

    // Payment mode
    print '<tr><td><span class="fieldrequired">'.$langs->trans('PaymentMode').'</span></td><td>';
    $form->select_types_paiements($selectfk_paymen,'fk_paymen','',2);
    print "</td>\n";
    print '<td rowspan="'.$rowspan.'" valign="top">';
    print '<textarea name="comment" wrap="soft" cols="60" rows="'.ROWS_4.'">'.(empty($_POST['comment'])?'':$_POST['comment']).'</textarea></td>';
    print '</tr>';

    // Bank account
    print '<tr>';
    if (! empty($conf->banque->enabled))
    {
        if ($advance->type != 2) print '<td><span class="fieldrequired">'.$langs->trans('AccountToCredit').'</span></td>';
        if ($advance->type == 2) print '<td><span class="fieldrequired">'.$langs->trans('AccountToDebit').'</span></td>';
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
        $form->select_comptes($accountid,'accountid',0,$filter,2);
        print '</td>';
    }
    else
    {
        print '<td colspan="2">&nbsp;</td>';
    }
    print "</tr>\n";

    // Cheque number
    print '<tr><td>'.$langs->trans('Numero');
    print ' <em>('.$langs->trans("ChequeOrTransferNumber").')</em>';
    print '</td>';
    print '<td><input name="num_paiement" type="text" value="'.$paymentnum.'"></td></tr>';

    // Check transmitter
    print '<tr><td class="'.(GETPOST('paiementcode')=='CHQ'?'fieldrequired ':'').'fieldrequireddyn">'.$langs->trans('CheckTransmitter');
    print ' <em>('.$langs->trans("ChequeMaker").')</em>';
    print '</td>';
    print '<td><input id="fieldchqemetteur" name="chqemetteur" size="30" type="text" value="'.GETPOST('chqemetteur').'"></td></tr>';

    // Bank name
    print '<tr><td>'.$langs->trans('Bank');
    print ' <em>('.$langs->trans("ChequeBank").')</em>';
    print '</td>';
    print '<td><input name="chqbank" size="30" type="text" value="'.GETPOST('chqbank').'"></td></tr>';

    print '</table>';

    dol_fiche_end();

    /*
     * List of unpaid invoices
     */
    $sql = 'SELECT
        a.rowid AS facid,
        a.total_import AS total_ttc,
        a.IMPORT as importt,
        IFNULL(a.fk_tva,"0") as fk_tva,
        a.statut as type,
        a.date_advance AS df,
        c.amount';
    if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
        $sql.= ' ,b.label';
    }

    $sql.= ' FROM llx_ctrl_advance_provider AS a';

    if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
        $sql.= ' 
             INNER JOIN llx_multidivisa_divisas as b on a.fk_mcurrency=b.rowid
        ';
    }
    $sql.= '
    LEFT JOIN  llx_ctrl_paiementfourn_facturefourn as c on c.fk_facturefourn=a.rowid
    WHERE
        (a.statut=2 OR a.statut=5) AND
    ';
    if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {

        $ant_prov =new Ctrladvanceprovider($db);

        $ant_prov->fetch($id);
        if ($ant_prov->fk_mcurrency>0) {
            $sql.= ' a.fk_mcurrency='.$ant_prov->fk_mcurrency.' AND ';
        }
    }
    $sql.='
        a.fk_soc ='.$advance->fk_soc;
    $sql.=' GROUP BY a.rowid';
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        if ($num > 0)
        {
            $sign=1;
            if ($advance->type == 2) $sign=-1;

            $arraytitle=$langs->trans('ctrl_advance');
            $i = 0;
            //print '<tr><td colspan="3">';
            print '<br>';
            print '<table class="noborder" width="100%">';
            print '<tr class="liste_titre">';
            print '<td>'.$arraytitle.'</td>';
            print '<td align="center">'.$langs->trans('Date').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_import').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_fk_tva').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_total_import').'</td>';
            print '<td align="right">'.$langs->trans('ctrl_rest_pay_tot').'</td>';
            if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                print '<td align="right">'.$langs->trans('ctrl_multi_cash').'</td>';
            }
            print '<td align="right">'.$langs->trans('ctrl_total_import').'</td>';
            print '<td align="right">&nbsp;</td>';
            print "</tr>\n";

            while ($i < $num)
            {
                $objp = $db->fetch_object($resql);

                print '<tr '.(($id==$objp->facid)?'class="pair"':'class="impair"').' >';
                $ant_prov =new Ctrladvanceprovider($db);

                $ant_prov->fetch($objp->facid);
                print '<td>';
                print $ant_prov->getNomUrl(1);
                print "</td>\n";

                // Date
                print '<td align="center">'.dol_print_date($db->jdate($objp->df),'day')."</td>\n";

                print '<td align="right">'.price($objp->importt).'</td>';

                print '<td align="right">'.price($objp->fk_tva).'</td>';
                // Price
                print '<td align="right">'.price($objp->total_ttc).'</td>';

                print '<td align="right">'.price($objp->total_ttc-$objp->amount).'</td>';
                
                // Remain to take or to pay back
                if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {
                    print '<td align="right">'.$objp->label.'</td>';
                }

                print '<td align="right">';
                    $namef = 'amount_'.$objp->facid;
                    $nameRemain = 'remain_'.$objp->facid;
                    if ($action != 'add_paiement')
                    {
                        if(!empty($conf->global->INVOICE_AUTO_FILLJS))
                            print img_picto("Auto fill",'rightarrow', "class='AutoFillAmout'   data-rowname='".$namef."' data-value='".price($objp->total_ttc-$objp->amount)."'");
                        print '<input type=hidden class="remain" name="'.$nameRemain.'" value="'.$remaintopay.'">';
                        print '<input type="text" size="8" class="amount" name="'.$namef.'" value="'.$_POST[$namef].'">';
                    }
                    else
                    {
                        print '<input type="text" size="8" name="'.$namef.'_disabled" value="'.$_POST[$namef].'" disabled>';
                        print '<input type="hidden" name="'.$namef.'" value="'.$_POST[$namef].'">';
                    }
                print "</td>";
                print "</tr>\n";
                $i++;
            }
            print "</table>";
        }
        $db->free($resql);
    }
    else
    {
        dol_print_error($db);
    }


    // Bouton Enregistrer
    if ($action != 'add_paiement')
    {
        $buttontitle =$langs->trans('ToMakePayment');
        print '<div class="center">';
        print '<input type="submit" class="button" value="'.dol_escape_htmltag($buttontitle).'"><br><br>';
        print '</div>';
    }

    if ($action == 'add_paiement')
    {
        foreach ($_POST as $key => $value)
        {
            if (substr($key,0,7) == 'amount_')
            {
                $cursorfacid = substr($key,7);
                $amounts[$cursorfacid] = price2num(trim(GETPOST($key)));
                $totalpayment = $totalpayment + $amounts[$cursorfacid];
            }
        }
        $preselectedchoice='no';

        print '<br>';
        print $form->formconfirm(
            $_SERVER['PHP_SELF'].'?facid='.$advance->rowid.'&socid='.$advance->socid.'&type='.$advance->type,
            $langs->trans('ctrl_quest_req'),
            $langs->trans('ctrl_confirm_payment').' '.price($totalpayment).' '.(($conf->global->MAIN_MODULE_MULTIDIVISA==1)?$objp->label:"")."?",
            'confirm_paiement',
            '',
            $preselectedchoice
        );
    }
    print "</form>\n";
    
}

?>