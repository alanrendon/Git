<?php 

ob_start();

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$url[0] = "../";

require_once("../class/admin.class.php");
require_once "../class/balance_comprobacion.class.php";
require_once $url[0]."class/admin.class.php";
require "../class/html2pdf/html2pdf.class.php";
require_once "../class/logo.class.php";
$balance = new BalanceComp();
if($_GET['inicio']){
    $periodo[0] = $_GET['inicio'];
    $periodo[1] = $_GET['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}


$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
//print_r($periodo);
$ctas=$balance->get_balance_comp2($periodo);
$logo      = new Logo();
//header("Content-type: application/ms-excel");
//header("Content-disposition: attachment; filename=balanza_comprobacion_".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".xls");
?>
	<style type="text/css">
	table{
		font-size:10px;	
	}

    table.table_border{
        background-color: #F9F9F9; 
        border-top: solid 0.1mm #949fa9; 
        padding: 2mm;
        font-size: 10px;
        width: 100%; 
        color: #73879C;
    }
    table.table_border_asientos{
       background-color: #405467; 
       border-top: solid 0.1mm #949fa9; 
       padding: 2mm;
       font-size: 10px;
       width: 100%; 
       color: #FFFFFF;
   }
    table.table_blank{
        background-color: #FFFFFF; 
        border-top: solid 0.1mm #949fa9;
        border-bottom: solid 0.1mm #949fa9; 
        padding: 2mm;
        font-size: 10px;
        width: 100%; 

    }
    table.table_blank_space{
        background-color: #FFFFFF; 
        border-top: solid 0.1mm #949fa9;
        border-bottom: solid 0.1mm #949fa9; 
        padding: 2mm;
        font-size: 9;
        width: 100%; 
        color: #666666;

    }

    table.page_header, table.page_footer {
        width: 100%; 
        border: none; 
        padding: 1mm;
        font-size: 10px;
        color:#2A3F54;
        font-weight: bold;
                                   
    }

</style>

	<page backtop="10mm" backbottom="50mm" backleft="7mm" backright="10mm">  
		<page_header> 
    	<table class="page_header">
            <tr>
                <td style="width: 100%; text-align: right; ">
                    <?php if ($logo = $logo->get_logo()): ?>
                        <img src=" <?php echo $logo ;?>" >                           
                    <?php endif ?>                                      
                </td>
            </tr>
            <tr>
                <td style="width: 100%; text-align: center">
                    <h4 ><?php  isset($valores['MAIN_INFO_SOCIETE_NOM']) ? print $valores['MAIN_INFO_SOCIETE_NOM']: print 'Sin nombre'; ?></h4>
                </td>
            </tr>

        </table>
        </page_header> 
        <page_footer> 
            <table class="page_footer">
               <tr>
                   <td style="width: 100%; text-align: right">
                       Página [[page_cu]]/[[page_nb]]
                   </td>
               </tr>
            </table>
        </page_footer> 
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h4>Anexos del Catálogo</h4>
				</div>
			</div>			
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h4>Correspondiente <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h4>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<table id="datatable" class="table" width="100%" cellspacing="0" style="font-size: 12px;">
								<thead>
									<tr style="background-color:#7A8196; color:#FFFFFF">
										<th width="15%">
											Cuenta
										</th>
										<th width="25%">
											Nombre
										</th>
										<th style="text-align:right" width="15%">
											Saldo Inicial <br>
											Deudor/Acreedor
										</th>
										<th style="text-align:right" width="15%">
											Cargos
										</th>
										<th style="text-align:right" width="15%">
											Abonos
										</th>
										<th style="text-align:right" width="15%">
											Saldo Actual <br>
											Deudor/Acreedor
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$totini   =0;
									$totdebe  =0;
									$tothaber =0;
									$totact   =0;

									foreach ($ctas as $cta){
										if($cta['nivel']==1){

											@$ini    =$balance->get_saldo_ini_padre($periodo,$cta['cta']);
											@$idebe  =$ini->debe;
											@$ihaber =$ini->haber;
											@$saldi  =$balance->get_balance_comp_debhab_padre($periodo,$cta['cta']);

											if($cta['natur']=='D'){
												$inicial =$idebe-$ihaber;
												$sact    =$inicial+$saldi->debe-$saldi->haber;
											}else{
												$inicial =$ihaber-$idebe;
												$sact    =$inicial+$saldi->haber-$saldi->debe;
											}
										
											if($saldi->haber!=0 || $saldi->debe!=0 || $inicial!=0){
											?>
											<tr style="background-color:#F9F9F9; color:#000000">

												<td><strong><?=$cta['cta']?></strong></td>
												<td><strong><?=utf8_decode($cta['descta'])?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($inicial,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($saldi->debe,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($saldi->haber,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($sact,2)?></strong></td>
											</tr>
											<?php
											}
										}else
										{
                                            
											@$ini    =$balance->get_saldo_ini($periodo,$cta['cta']);
											@$idebe  =$ini->debe;
											@$ihaber =$ini->haber;

	                                        if( @$cta['nivel']==2 && @$cta['afectacion']==1){
	                                            @$saldi=$balance->get_balance_comp_debhab_padre($periodo,$cta['cta']);
	                                        }else{
	                                            @$saldi=$balance->get_balance_comp_debhab($periodo,$cta['cta']);
	                                        }
	                                            
											if($cta['natur']=='D'){
												@$inicial =$idebe-$ihaber;
												@$sact    =$inicial+$saldi->debe-$saldi->haber;
												@$totini  +=$inicial;
												@$totact  +=$sact;
	                                             
											}else {
												@$inicial =$ihaber-$idebe;
												@$sact    =$inicial+$saldi->haber-$saldi->debe;
												@$totini  -=$inicial;
												@$totact  -=$sact;
											}
											
											@$totdebe  +=$saldi->debe;
											@$tothaber +=$saldi->haber;
									?>
										<?php if (@$saldi->haber!=0 || @$saldi->debe!=0 || @$inicial!=0): ?>
											<tr >
												<td><?=$cta['cta']?></td>
												<td style="word-break: break-all; width:15%" ><?=utf8_decode($cta['descta'])?></td>
												<td align="right"><?=$moneda." ".number_format($inicial,2)?></td>
												<td align="right"><?=$moneda." ".number_format(@$saldi->debe,2)?></td>
												<td align="right"><?=$moneda." ".number_format(@$saldi->haber,2)?></td>
												<td align="right"><?=$moneda." ".number_format($sact,2)?></td>
											</tr>
											<tr>
												<td colspan="6">
													<br>
												</td>
											</tr>
										<?php endif ?>
									<?php
									
										}
									}
									?>
									<tr style="background-color:#000000; color:#FFFFFF">
										<td colspan="2"><strong>Total:</strong></td>
										<td align="right"><strong><?=$moneda." ".number_format($totini,2)?></strong></td>
										<td align="right"><strong><?=$moneda." ".number_format($totdebe,2)?></strong></td>
										<td align="right"><strong><?=$moneda." ".number_format($tothaber,2)?></strong></td>
										<td align="right"><strong><?=$moneda." ".number_format($totact,2)?></strong></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</page>

<?php
	$filename="balanza_comprobacion_".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".pdf";
	$html2pdf        = new HTML2PDF('P', 'A4', 'es');
	$html     = ob_get_contents();
    ob_end_clean();
    $html2pdf->writeHTML($html);
    $html2pdf->Output($filename);

?>                