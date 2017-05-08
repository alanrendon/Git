<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

require_once "../class/balance_comprobacion.class.php";
require_once $url[0]."class/admin.class.php";

$balance = new BalanceComp();
if($_REQUEST['inicio']){
    $periodo[0] = $_REQUEST['inicio'];
    $periodo[1] = $_REQUEST['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}


$eDolibarr = new admin();
$valores   = $eDolibarr->get_user();
$moneda    = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
//print_r($periodo);
$ctas=$balance->get_balance_comp2($periodo);

header("Content-type: application/ms-excel");
header("Content-disposition: attachment; filename=balanza_comprobacion_".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".xls");
?>
<!DOCTYPE html>
<html lang="es">
	<!-- Select2 -->
	<style>
        a.modal_a{
            color: #337ab7 !important;
            text-decoration: underline !important;
            cursor:help !important;
        }
    </style>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Balanza de Comprobaci&oacute;n correspondiente de: <?=$periodo[0]." a ".$periodo[1]?></h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_content">
							<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th width="40%">Cuenta</th>
										<th style="text-align:right" width="15%">Saldo Inicial</th>
										<th style="text-align:right" width="15%">Debe</th>
										<th style="text-align:right" width="15%">Haber</th>
										<th style="text-align:right" width="15%">Saldo Actual</th>
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
											$ini    =$balance->get_saldo_ini_padre($periodo,$cta['cta']);
											$idebe  =$ini->debe;
											$ihaber =$ini->haber;
											
											$saldi=$balance->get_balance_comp_debhab_padre($periodo,$cta['cta']);
											if($cta['natur']=='D'){
												$inicial =$idebe-$ihaber;
												$sact    =$inicial+$saldi->debe-$saldi->haber;
											}else{
												$inicial=$ihaber-$idebe;
												$sact=$inicial+$saldi->haber-$saldi->debe;
											}
										
											if($saldi->haber!=0 || $saldi->debe!=0 || $inicial!=0){
											?>
											<tr>
												<td><strong><?=$cta['cta']." ".utf8_decode($cta['descta'])?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($inicial,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($saldi->debe,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($saldi->haber,2)?></strong></td>
												<td align="right"><strong><?=$moneda." ".number_format($sact,2)?></strong></td>
											</tr>
											<?php
											}
										}else{
											$ini    =$balance->get_saldo_ini($periodo,$cta['cta']);
											$idebe  =$ini->debe;
											$ihaber =$ini->haber;

								        if(@$cta['nivel']==2 && @$cta['afectacion']==1){
                                                $saldi=$balance->get_balance_comp_debhab_padre($periodo,$cta['cta']);
                                            }else{
                                                $saldi=$balance->get_balance_comp_debhab($periodo,$cta['cta']);
                                            }
										if($cta['natur']=='D'){
											@$inicial =$idebe-$ihaber;
											@$sact    =$inicial+$saldi->debe-$saldi->haber;
											@$totini  +=$inicial;
											@$totact  +=$sact;
										}else{
											@$inicial =$ihaber-$idebe;
											@$sact    =$inicial+$saldi->haber-$saldi->debe;
											@$totini  -=$inicial;
											@$totact  -=$sact;
										}
										
										@$totdebe  +=$saldi->debe;
										@$tothaber +=$saldi->haber;
										
										if(@$saldi->haber!=0 || @$saldi->debe!=0 || @$inicial!=0){
									?>
										<tr>
											<td><?=$cta['cta']." ".utf8_decode($cta['descta'])?></td>
											<td align="right"><?=$moneda." ".number_format($inicial,2)?></td>
											<td align="right"><?=$moneda." ".number_format($saldi->debe,2)?></td>
											<td align="right"><?=$moneda." ".number_format($saldi->haber,2)?></td>
											<td align="right"><?=$moneda." ".number_format($sact,2)?></td>
										</tr>
									<?php
										}
										}
									}
									?>
									<tr>
										<td align="right"><strong>Total:</strong></td>
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
	</div>
</body>
</html>

