 <?php
 	require('../libs/html2pdf/html2pdf.class.php');
 	require '../../main.inc.php';
	require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
	require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
	require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
 	global $mysoc,$db;
 	dol_include_once('/ctrlanticipo/class/ctrlbankcheck.class.php');
	dol_include_once('/ctrlanticipo/class/ctrladvanceproviderpayment.class.php');
	dol_include_once('/ctrlanticipo/libs/advance.lib.php');
 	ob_start();
	$html2pdf = new HTML2PDF('P', 'A4', 'es');



	$id	= GETPOST('id','int');
	$object=new Ctrlbankcheck($db);
	$pago = new PaiementAdvance($db);
	$advance = new Ctrladvanceprovider($db);
	$societe= new Societe($db);
	$accountstatic=new Account($db);
	        
			
	if ($id > 0 )
	{
		$result=$object->fetch($id);
		$pago->fetch($object->fk_paiment);

		$advance->fetch($object->fk_paiment);
		$societe->fetch($advance->fk_soc);

		if ($result < 0) dol_print_error($db);
	}


	$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		
	if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
	{
		$urllogo=DOL_DOCUMENT_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_mini);
	}
	else
	{
		$urllogo=DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.png';
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
        	width: 100%;
        }

        table.concepto-descripcion-title th {
        	height: 10px;
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
        	font-weight: normal;
        	padding: 0.3mm;
        	border-radius: 3px;
        	font-size: 9px;
        	vertical-align: top;
        }

        table.concepto-descripcion-title td {
        	height: 20px;
        	font-weight: normal;
        	color: #000000;
        	vertical-align: middle;
        }

        table.concepto-descripcion-total td {
        	height: 20px;
        	font-weight: normal;
        	vertical-align: middle;
        }

        table.cheque{
			
			width: 180mm;
			height: 76mm;
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
        	font-size: 14px;
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
        }
        table.page_footer {
            width: 100%; 
            border: none; 
            padding: 1mm;
            font-size: 10px;
            color:#2A3F54;
            font-weight: bold;
                                   
        }

	</style>

	<page  backtop="-1mm" backbottom="7mm" backleft="10mm" backright="10mm">';

   
		print '
			<div style="position: absolute; left: 82%; top:5%; font-size: 14px;">
				<b>'.dol_print_date($object->date_asign,'daytext','tzuserrel').'</b>
			</div>
			<table class="cheque" align="center" style="margin-top:48px;">
				<tbody>
					<tr >
						<td class="td-20">
							
						</td>
						<td class="td-50 text-8">
							
						</td>
						<td class="td-30 text-right">
							<h5>&nbsp;</h5>
						</td>
					</tr>
					<tr >
						<td COLSPAN=2 style="font-size: 14px;"> <H5>
							'.$object->receptor.' </H5>
						</td>
						<td class="td-30 text-right" style="font-size: 14px;">
							<strong> '.price($advance->total_import).'</strong> 
						</td>
					</tr>
					<tr >
						<td class="td-70" COLSPAN=2 style="font-size: 14px;"><H5>';

						$sql="SELECT a.code FROM llx_multidivisa_divisas as a WHERE a.rowid=".$advance->fk_mcurrency;
						$divisa=" MN";
						$resql=$db->query($sql);
			            if ($resql){
			                $num = $db->num_rows($resql);
			                if ($num>0) {
		                        $objp = $db->fetch_object($resql);
		                        $divisa= " ".$objp->code;
		                        $divisa=str_replace("MXN"," MN",$divisa);
			                }
			            }
			            
			            print numtoletras($advance->total_import,$divisa);
					print '
						</H5>
						</td>

						<td class="td-30 text-right">
						</td>
					</tr>
					
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>


					<tr>
						
					
						<td class="border-bottom border-right" style="padding-top: 3px;">
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td><br></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td><br></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>';
	

	print '

		<br><br><br><br><br>
		<table class="cocepto">
			<tr>
				<td class="td-50">
					<h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h5>
				</td>
				<td class="td-50 text-right">
					<h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h5>
				</td>
			</tr>
		</table>

		
		   <legend >
		   		<strong></strong>
		   </legend>
			   <p align="left">
			   		<div style="word-wrap: break-word; width:90%; float: left;" ><b>
			   	 	'.$object->concept.'</b>
			   	 	</div>
			   </p>
		<br><br>

	</page>';

 	
 	$html =ob_get_contents();
    ob_clean();
 	$html2pdf->writeHTML($html);
    $html2pdf->Output('Cheque.pdf');
    exit();
 ?>

