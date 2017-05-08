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

$balance = new BalanceComp();
if($_REQUEST['inicio']){
    $periodo[0] = $_REQUEST['inicio'];
    $periodo[1] = $_REQUEST['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}
if(isset($_REQUEST['rango1']) && isset($_REQUEST['rango2'])){
	$rango = array();
	$rango[0]=$_REQUEST['rango1'];
	$rango[1]=$_REQUEST['rango2'];

}else{
	$rango[0] = '';
	$rango[1] = '';
}
require_once $url[0]."class/admin.class.php";
$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
//print_r($periodo);
if($rango[0]=='' && $rango[1]==''){
$ctas=$balance->get_balance_comp($periodo);
}else{
	$ctas=$balance->get_balance_comp($periodo,$rango);
}

header("Content-type: application/ms-excel");
header("Content-disposition: attachment; filename=balanza_comprobacion_".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".xls");
?>
<!DOCTYPE html>
<html lang="es">
	<!-- Select2 -->
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
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
					<h3>Auxiliar de Cuentas correspondiente de: <?=$periodo[0]." a ".$periodo[1]?></h3>
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
									$totini=0;
									$totdebe=0;
									$tothaber=0;
									$totact=0;
									foreach ($ctas as $cta){
										$ini=$balance->get_saldo_ini($periodo,$cta['cta']);
										$idebe=$ini->debe;
										$ihaber=$ini->haber;

										if($cta['natur']=='D'){
											$inicial=$idebe-$ihaber;
											$sact=$inicial+$cta['debe']-$cta['haber'];
										}else{
											$inicial=$ihaber-$idebe;
											$sact=$inicial+$cta['haber']-$cta['debe'];
										}
										$totini+=$inicial;
										$totdebe+=$cta['debe'];
										$tothaber+=$cta['haber'];
										$totact+=$sact;
									?>
										<tr>
											<td><?=$cta['cta']." ".$cta['descta']?></td>
											<td align="right"><?=$moneda." ".number_format($inicial,2)?></td>
											<td align="right"><?=$moneda." ".number_format($cta['debe'],2)?></td>
											<td align="right"><?=$moneda." ".number_format($cta['haber'],2)?></td>
											<td align="right"><?=$moneda." ".number_format($sact,2)?></td>
										</tr>
										<tr>
											<td colspan="5">
												<table width="98%" align="center" class="table table-striped table-bordered">
												<thead>
													<th>Poliza</th>
													<th>Factura</th>
													<th>Concepto</th>
													<th>Fecha</th>
													<th>Debe</th>
													<th>Haber</th>
												</thead>
												<?php 
												$poliz=$balance->get_polizas_cuenta($periodo,$cta['cta']);
												$todebe=0;
												$tohaber=0;
												foreach ($poliz as $poli){
													if($poli['tipo_pol']=='I'){
														$tipo='Ingreso';
													}else{
														if($poli['tipo_pol']=='E'){
															$tipo='Egreso';
														}else{
															if($poli['tipo_pol']=='D'){
																$tipo='Diario';
															}else{
																$tipo='';
															}
														}
													}
													$todebe+=$poli['debe'];
													$tohaber+=$poli['haber'];
													if($poli['societe_type']==1 || $poli['societe_type']==2){
														$fact=$balance->get_facture($poli['fk_facture'], $poli['societe_type']);
														$facture=$fact->facnumber;
													}else{
														$facture='N/A';
													}
													?>
													<tr>
													<td>Poliza <?=$tipo." ".$poli['cons']?></td>
													<td><?=$facture?></td>
													<td><?= ($poli['concepto'])?></td>
													<td><?=$poli['fecha']?></td>
													<td align="right"><?=$moneda." ".number_format($poli['debe'],2)?></td>
													<td align="right"><?=$moneda." ".number_format($poli['haber'],2)?></td>
													</tr>
													<?php 
												}
												?>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td align="right"><strong>Total:</strong></td>
													<td align="right"><?=$moneda." ".number_format($todebe,2)?></td>
													<td align="right"><?=$moneda." ".number_format($tohaber,2)?></td>
													</tr>
												</table>
											</td>
										</tr>
									<?php
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
<!-- /page content -->
<!-- jQuery -->
<script src="<?php echo $url[0].'../vendors/jquery/dist/jquery.min.js'?>"></script>
<!-- Bootstrap -->
<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>
<!-- Select2 -->
<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>
<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>
<!-- Dropzone.js -->
<script src="<?php echo $url[0].'../vendors/dropzone/dist/min/dropzone.min.js'?>"></script>
<!-- Dropzone.js -->
 <!-- Datatables -->
<script src="<?php echo $url[0].'../vendors/datatables.net/js/jquery.dataTables.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-buttons/js/dataTables.buttons.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-buttons/js/buttons.flash.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-buttons/js/buttons.html5.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-buttons/js/buttons.print.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-responsive/js/dataTables.responsive.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/jszip/dist/jszip.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/pdfmake.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/vfs_fonts.js'?>"></script>

<script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
<script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>

</body>
</html>

