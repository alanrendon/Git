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
if(isset($_POST['periodo'])){
    $periodo = explode("-", $_POST['periodo']);
    $periodo[0]=trim($periodo[0]);
    $periodo[1]=trim($periodo[1]);

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}

if(isset($_POST['crini']) && isset($_POST['crfin'])){
	$rango = array();
	$rango[0]=$_POST['crini'];
	$rango[1]=$_POST['crfin'];

}else{
	$rango[0] = '';
	$rango[1] = '';
}
if($rango[0]=='' && $rango[1]==''){
$ctas=$balance->get_balance_comp($periodo);
}else{
	$ctas=$balance->get_balance_comp($periodo,$rango);
}

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
	<?php
	include_once($url[0]."base/head.php");
	?>

	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Auxiliar de Cuentas</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Correspondiente <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h2>
							<div class="clearfix"></div>
						</div>
						 <div class="row">
                            <div class="col-md-8">
                                <form method="post" id="frm_periodo" action="#">
                                   <fieldset>
                                      <div class="control-group">
                                        <div class="controls">
                                          <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input type="text" style="width: 200px" name="periodo" id="periodo" class="form-control" value="<?php print $periodo[0]; ?> - <?php echo $periodo[1];?>" />
                                          </div>
                                          <input type="hidden" name="crini" id="crini" value="<?=$rango[0]?>">
                                          <input type="hidden" name="crfin" id="crfin" value="<?=$rango[1]?>">
                                        </div>
                                      </div>
                                    </fieldset>
				                </form>
				                <form method="post" action="#">
                                     Filtrar por Rango de cuentas de 
                                     <input type="text" name="crini" id="crini" placeholder="100.000.000" value="<?=$rango[0]?>">
                                     a <input type="text" name="crfin" id="crfin" placeholder="200.000.000" value="<?=$rango[1]?>">
                                       <input type="hidden" name="periodo" id="periodo" class="form-control" value="<?php print $periodo[0]; ?> - <?php echo $periodo[1];?>" />
                                     <input type="submit" value="Filtrar">
                                </form>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <a href="../get/auxiliar_excel.php?inicio=<?=$periodo[0].'&fin='.$periodo[1].'&rango1='.$rango[0].'&rango2='.$rango[1]; ?>" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" target="_blank">
                                <span>Descargar XLS</span>
                                </a>
                            </div>
                        </div>
						<div class="x_content">
							<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
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
										<th width="40%">Cuenta</th>
										<th style="text-align:right" width="15%">Saldo Inicial</th>
										<th style="text-align:right" width="15%">Debe</th>
										<th style="text-align:right" width="15%">Haber</th>
										<th style="text-align:right" width="15%">Saldo Actual</th>
									</tr>
										<tr style="background-color:#7A8196; color:#FFFFFF">
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

<!-- footer content -->
<footer>
	<div class="pull-right">
		Contab PRO 1.0 | Dolibarr ERP by <a href="http://www.auriboxconsulting.com/">Auribox Consulting</a>
	</div>
	<div class="clearfix"></div>
</footer>
<!-- /footer content -->

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
<script>
    
     $(document).ready(function() {
         $('#periodo').daterangepicker(
              {
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                format: 'YYYY/MM/DD',
                showDropdowns: true,
                locale: {
                        applyLabel: 'Aplicar',
                        cancelLabel: 'Limpiar',
                        fromLabel: 'de',
                        toLabel: 'a',
                        daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        firstDay: 1
              }
            },function(ev, picke){
                 $("#frm_periodo").submit(); 
            }                             
         );
          $('#periodo').on('apply.daterangepicker', function(ev, picker) {
            $("#frm_periodo").submit(); 
        });
      });
</script>
</body>
</html>
