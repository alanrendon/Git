 <?php
 	error_reporting (0);
 	require('../libs/html2pdf/html2pdf.class.php');
 	//require "../../../main.inc.php";

	require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
	require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
	require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/societe/class/companybankaccount.class.php';

	if (! empty($conf->banque->enabled)) require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
 	global $mysoc,$db,$langs,$conf;
 	$langs->load("ctrlanticipo");
	$langs->load("other");
	$langs->load("compta");
	$langs->load("bills");
	dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
	dol_include_once('/ctrlanticipo/libs/advance.lib.php');
 	ob_start();	
	$html2pdf = new HTML2PDF('P', 'A4', 'es');



	//$id	= GETPOST('id','int');
	$pago = new PaiementAdvance($db);
	$advance = new Ctrladvanceprovider($db);
	$societe= new Societe($db);
	$projectstatic=new Project($db);
	$solicitante= new User($db);
	$accountstatic=new Account($db);
	

	if ($id > 0 )
	{



		$advance->fetch($id);
		$societe->fetch($advance->fk_soc);
		$solicitante->fetch($advance->fk_user_applicant);
		$projectstatic->fetch($advance->fk_project);

		if ($result < 0) dol_print_error($db);
	}

	$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
	

	if (! empty($mysoc->logo_mini)) {
		if (file_exists($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini)) {
			$urllogo=$conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini;
			
		}
	} else {
		$urllogo=$_SERVER["HTTP_HOST"]."/".DOL_URL_ROOT.'/public/theme/common/nophoto.png';
	}

	

	$hsbc=DOL_DOCUMENT_ROOT.'/ctrlanticipo/img/1430.png';
 	print '

	<style>
        table{
        	width: 100%;
        	padding: 1mm;
            font-size: 10px;

        }
        table.cocepto{
        	font-style: italic;
        	width: 100%;
        }
        
		table.conta{
        	border-radius:8px;
			spacing: 0;
			padding: 0mm;
        	width: 200mm;
			border-collapse: collapse;
			border: 1px solid rgb(16,120,179);
        }
		
		tr.contatr{
			spacing: 0;
			padding: 0mm;
			border: 1px solid rgb(16,120,179);
		}
		
		td.contatd{
			spacing: 0;
			padding: 0mm;
			border: 1px solid rgb(16,120,179);
			height: 20px;
		}
		
		
		th.contath{
			text-align: center;
			spacing: 0;
			padding: 0mm;
			border: 1px solid rgb(16,120,179);
			height: 18px;
		}
		
		table.concepto-descripcion-title th {
        	height: 10px;
        	background-repeat: no-repeat;
        	background-position: center center;
        	border: 1px solid #ffffff;
        	background-color: #000000;
        	font-weight: normal;
        	color: #ffffff;
        	padding: 0.3mm;
        	vertical-align: middle;
        	border-radius: 5px;
        }
		table.concepto-sign  {
			margin-top:10px;
        }

        table.concepto-sign th {
        	height: 50px;
        	border: 1px solid #000000;
        	font-weight: normal;
        	padding: 0.3mm;
        	border-radius: 3px;
        	font-size: 9px;
        	vertical-align: top;
        }

        table.concepto-descripcion-title td {
        	height: 20px;
        	background-repeat: no-repeat;
        	background-color: #ECECEC;
        	font-weight: normal;
        	color: #000000;
        	vertical-align: middle;
        }

        table.concepto-descripcion-total td {
        	height: 20px;
        	background-repeat: no-repeat;
        	font-weight: normal;
        	vertical-align: middle;
        }

        table.cheque{
        	background-color: #D8F4CE;
			font-style: italic;
			width: 180mm;
			height: 76mm;
        }

        td.border-bottom{
        	border-bottom: 0.03mm solid black;
        }
        td.border-right{
        	border-right: 0.03mm solid black;
        }
        .td-10{
			width: 10%;
        }
        .td-20{
			width: 20%;
        }
        .td-50{
        	width: 50%;
        }
        .td-60{
        	width: 60%;
        }
         .td-100{
        	width: 100%;
        }
        .td-30{
        	width: 30%;
        }
        .text-8{
			font-size: 8px;
        }
        .text-7{
        	font-size: 7px;
        	font-style: oblique;
        	font-weight: bold;
        }

        .text-num{
        	font-size: 12px;
        	font-style: oblique;
        	font-weight: bold;
        }

        .text-right{
			text-align: right;
        }
        .text-center{
			text-align: center;
        }
        legend{
        	border: none;
        	background-color: #ffffff;
        }
        table.page_footer {
            width: 100%; 
            border: none; 
            padding: 1mm;
            font-size: 10px;
            color:#2A3F54;
            font-weight: bold;                 
        }
        .date_td {
        	border-radius:8px;
        	background-color:rgb(16,120,179);
            color:#fff;
            font-weight: bold;                 
        }
        .tot_td {
        	border-radius:8px !important;
        	background-color:rgb(194,229,243);
              
        }
        .div_titre{
        	width: 100%;
        	text-align: center;
        	color:black;
        }
        
		.div_firma{
        	color:black;
        }
		
		.div_titre2{
        	width: 100%;
        	border-radius:8px;
        	background-color:rgb(16,120,179);
        	color:#fff !important;
        	text-align: left;
        }
	</style>';

	$sql=' 
	SELECT
		CODE AS type_code,
		libelle AS paiement_type
	FROM
		llx_c_paiement
	WHERE id='.$advance->fk_paymen;

	$acc = new CompanyBankAccount($db);
	$labeltype="";
	$resqll=$db->query($sql);
	if ($resqll) {
		$obb=$db->fetch_object($resqll);
		
		$acc->fetch("",$advance->fk_soc);

		$labeltype=$langs->trans("PaymentType".$obb->type_code)!=("PaymentType".$obb->type_code)?$langs->trans("PaymentType".$obb->type_code):$obb->paiement_type;
	}
	
	$conDivisa = "SELECT code, unicode FROM llx_multidivisa_divisas WHERE rowid = $advance->fk_mcurrency";
	$resDivisa = $db->query($conDivisa);
	$objDivisa = $db->fetch_object($resDivisa);

	print '
	<table align="center">
		<tbody>
			<tr >
				<td class="td-20" align="left">
					<img src="'.$urllogo.'" style="width: 100%">
				</td>
				<td class="td-50" align="center">
					<table align="center">
						<tr><td><b>'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</b></td></tr>
						<tr>
							<td>'.(empty($conf->global->MAIN_INFO_SOCIETE_ADDRESS)?'':$conf->global->MAIN_INFO_SOCIETE_ADDRESS).', '.$conf->global->MAIN_INFO_SOCIETE_TOWN.'
							</td>
						</tr>
						<tr>
							<td>'.(empty($mysoc->country_code)?'':str_replace('MX - ', '',getCountry($mysoc->country_code,1))).', C.P '.$conf->global->MAIN_INFO_SOCIETE_ZIP.', R.F.C '.$conf->global->MAIN_INFO_SIREN.'
							</td>
						</tr>
						<tr><td>Tel. '.$conf->global->MAIN_INFO_SOCIETE_TEL.' '.$conf->global->MAIN_INFO_SOCIETE_WEB.'</td></tr>
					</table>
				</td>
				<td class="td-20">
					<table align="center">
						<tr><td align="center" class="date_td">CODIGO</td></tr>
						<tr><td align="center">RE-ADM-04</td></tr>
						<tr><td></td></tr>
						<tr><td align="center" class="date_td">FECHA REQUISICION</td></tr>
						<tr><td align="center">'.dol_print_date($advance->date_advance,'%d/%m/%Y').'	</td></tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>


	<div class="div_titre" >
	'.$langs->trans("ctrl_titre_label_format")."      ".$advance->ref.' 
	</div><br>
	<div style="padding-top:5px; padding-left:5px; width: 40%; border:1px solid rgb(16,120,179); border-radius:8px; display: inline; " >
		<b>'.$langs->trans("ctrl_format_labels").'</b><br>
		'.$societe->idprof1.'
		'.$societe->nom.'<br>
		'.$langs->trans("ctrl_fk_paymen").': '.$labeltype.'<br>
		'.$langs->trans("ctrl_bank").': '.$acc->bank.'<br>
		'.$langs->trans("ctrl_bank_num_account").': '.$acc->number.'<br>
		'.$langs->trans("ctrl_clabe").': '.$acc->iban.'  
	</div>
	<div >
	</div>
	<div style="width: 40%; border:1px solid rgb(16,120,179); border-radius:8px; position:absolute; top:110px; left:60%;  " >
		 <b>&nbsp;Solicita:</b><br> &nbsp;'.$solicitante->firstname.'
		 '.$solicitante->lastname.'<br> &nbsp;
		 '.$solicitante->email.'<br> &nbsp;'.$langs->trans("ctrl_titre_proyect").': <br> &nbsp;'.$projectstatic->title.'
		 <br><br>
	</div>
	<br><br><br><br>
	<div class="div_titre2" style="color:#fff;" >
	&nbsp;&nbsp;&nbsp;'.$langs->trans("ctrl_concept_mot").'
	</div>
	<div class="div_titre"  style="border:1px solid rgb(16,120,179); border-radius:8px; height:100px; word-wrap: break-word;" >
	'.$advance->concept_advance.'	
	</div>
	<br>
	<div class="div_titre"  style=" text-align:right;" >
		
	
	<label class="tot_td">TOTAL              '.price($advance->total_import).' '.$objDivisa->code.'</label><br>	
	</div>
	<br>
	<div class="div_titre"  style="text-align:left; padding-top:5px;  border:1px solid rgb(16,120,179); border-radius:8px; height:100px; word-wrap: break-word;" >&nbsp;&nbsp;Notas: 
	'.$advance->note_public.'	
	</div>

	<br><br>
	<table class="conta">
		<tr class="contatr">
			<th class="contath"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CUENTA CONTABLE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
			<th class="contath"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CARGO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
			<th class="contath"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ABONO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
		<tr class="contatr">
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
			<td class="contatd">&nbsp;</td>
		</tr>
	</table>
	
	<br><br><br><br><br><br>
	<table align="center" >
		<tr>
			<td  style="border-top:1px solid rgb(16,120,179); text-align:center; width: 180px;" >
				<p style="font-size:9pt;"> SOLICITO </p>
			</td>
			<td  class="td-10"  >
				
			</td>
			<td  style="border-top:1px solid rgb(16,120,179); text-align:center; width: 180px;" >
				<p style="font-size:9pt;"> REVISO </p>
			</td>
			<td  class="td-10"  >
				
			</td>
			<td style="border-top:1px solid rgb(16,120,179); text-align:center; width: 180px;" >
				<p style="font-size:9pt;"> AUTORIZO </p>
			</td>
		</tr>
	</table>

	';

	
	$upload_dir=$conf->user->dir_output."/".$advance->ref;
 	if (!is_dir($upload_dir)) {
 		dol_mkdir($upload_dir);
 	}
 	$html =ob_get_contents();
    ob_clean();
 	$html2pdf->writeHTML($html);
    $html2pdf->Output($upload_dir.'/'.$advance->ref.'.pdf','F');
