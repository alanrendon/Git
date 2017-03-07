<?php

ob_start();
$url[0] = "../";

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

require_once("../class/admin.class.php");
require "../class/html2pdf/html2pdf.class.php";
require_once "../class/cuentas_mayor.class.php";
require_once "../class/logo.class.php";
require_once $url[0]."class/admin.class.php";
$mayor = new CuentasMayor();
if(isset($_GET['inicio'])){
    $periodo[0] = $_GET['inicio'];
    $periodo[1] = $_GET['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}


$eDolibarr = new admin();
$valores   = $eDolibarr->get_user();
$moneda    = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
$ctas      =$mayor->get_mayor($periodo);
$logo      = new Logo();
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
	<div class="title_left">
		<h4>Libro Mayor</h4>
	</div>
		
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h4>Correspondiente <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h4>
					<div class="clearfix"></div>
				</div>
				<div>
					
					<table  width="100%" cellspacing="1" border="0">
						<tr style="background-color:#7A8196; color:#FFFFFF">
								<th width="10%" >Cuenta</th>
								<th width="15%" >Nombre</th>
								<th style="text-align:right" width="12.5%"></th>
								<th style="text-align:right" width="12.5%"></th>
								<th style="text-align:right" width="12.5%">Saldo Inicial</th>
								<th style="text-align:center" width="12.5%" colspan="2">Acumulados</th>
							
								
							</tr>
							<tr style="background-color:#e8e9ed ; color:#000000">
								<th width="10%" >Fecha</th>
								<th width="15%" >Periodo</th>
								<th style="text-align:right"  width="12.5%">Cargos</th>
								<th style="text-align:right"  width="12.5%">Abonos</th>
								<th style="text-align:right"  width="12.5%">Saldo</th>
								<th style="text-align:right"  width="12.5%">Cargos</th>
								<th style="text-align:right"  width="12.5%">Abonos</th>
							</tr>
						<tbody>
							<?php
							foreach ($ctas as $cta){
								$ini      =$mayor->get_saldo_ini($periodo,$cta['cta']);
								@$idebe   =$ini->debe;
								@$ihaber  =$ini->haber;
								$totini   =0;
								$totdebe  =0;
								$totdebe_acumulado  =0;
								$tothaber_acumulado  =0;
								$tothaber =0;
								$totact   =0;
								if($cta['natur']=='D'){
									$inicial =$idebe-$ihaber;
									$sact    =$inicial+$cta['debe']-$cta['haber'];
								}else{
									$inicial =$ihaber-$idebe;
									$sact    =$inicial+$cta['haber']-$cta['debe'];
								}

								if($cta['debe']!=0 || $cta['haber']!=0 || $inicial !=0){
									$totini   +=$inicial;
									$totdebe  +=$cta['debe'];
									$tothaber +=$cta['haber'];
									$totact   +=$sact;
								?>
								<tr>
									<td width="10%"><strong><?=$cta['cta']?></strong></td>
									<td width="15%"><strong><?=$cta['descta']?></strong></td>
									<td align="right" width="12.5%"></td>
									<td align="right" width="12.5%"></td>
									<td align="right" width="12.5%"><strong><?=$moneda." ".number_format($inicial,2)?></strong></td>
									<td align="right" width="12.5%"></td>
									<th style="text-align:right" width="12.5%"></th>
								</tr>
								<?php
									$pol        =$mayor->get_pol_mayor($periodo, $cta['cta']);
									
									$totalhaber =0;
									$totaldebe  =0;

                                    if($pol>0){
                                        foreach ($pol as $pl){
											$tipo       = ($pl['tipo_pol']=='I') ? 'Ingreso': 'N/A' ;
											$tipo       = ($pl['tipo_pol']=='E') ? 'Egreso': 'N/A' ;
											$tipo       = ($pl['tipo_pol']=='D') ? 'Diario': 'N/A' ;
											$totaldebe  +=$pl['debe'];
											$totalhaber +=$pl['haber'];
											$now        = new DateTime($pl['fecha']);
										}
										$totdebe_acumulado  +=$totaldebe;
										$tothaber_acumulado +=$totalhaber;
										$tothaber           +=$totalhaber;
										$inicial            =$inicial+$totaldebe-$totalhaber;
									?>
										<tr>
											<td width="10%">
												<?php $date_fin = date('t/m/Y',strtotime($pl['fecha'])); ?>
												<?php echo $date_fin; ?>	
											</td>
											<td width="15%"><?=@$arrayMeses[((int)$now->format('m'))-1]?></td>
												<?php if ($pl['debe']>0): ?>
													<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totaldebe,2)?></td>
												<?php else: ?>
													<td width="12.5%" style="text-align:right">$ 0.00</td>
												<?php endif ?>
												<?php if ($pl['haber']>0): ?>
													<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totalhaber,2)?></td>
												<?php else: ?>
													<td width="12.5%" style="text-align:right">$ 0.00</td>
												<?php endif ?>
											<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($inicial,2)?></td>
											<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($totdebe,2)?></td>
											<td width="12.5%" style="text-align:right"><?=$moneda." ".number_format($tothaber,2)?></td>
										</tr>		
                                    <?php 
                                	}
									?> 
									
									<tr >
										<td style="text-align:right">
											<br>
										</td>
										<td width="15%" style="text-align:right"></td>
										<td width="12.5%" style="text-align:right"></td>
										<td width="12.5%" style="text-align:right"></td>
										<td width="12.5%" style="text-align:right"></td>
										<td width="12.5%" style="text-align:right"></td>
										<td width="12.5%" style="text-align:right"></td>
									</tr>
									<?php
									}
							}
							?>
						</tbody>
					</table>	
				</div>
			</div>
		</div>
	</div>
</page>		
<?php
	$filename="libro_mayor".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".pdf";
	$html2pdf        = new HTML2PDF('P', 'A4', 'es');
	$html     = ob_get_contents();
    ob_end_clean();
    $html2pdf->writeHTML($html);
    $html2pdf->Output($filename);

?> 