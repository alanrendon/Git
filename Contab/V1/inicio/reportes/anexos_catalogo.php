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
require_once ("../class/periodo.class.php");

$periodo_fecha    = new Periodo();
$balance          = new BalanceComp();


if(isset($_POST['periodo'])){
    $periodo = explode("-", $_POST['periodo']);
    $periodo[0]=trim($periodo[0]);
    $periodo[1]=trim($periodo[1]);

}else{
    $fechaUltimo =$periodo_fecha->get_ultimo_periodo_abierto();
    if(!$fechaUltimo->anio){
        $periodo[0] = date('m/01/Y');
        $periodo[1] = date('m/t/Y');
    }else{
        $periodo[0] = date($fechaUltimo->mes.'/01/'.$fechaUltimo->anio);
        $finFecha = date('t',strtotime($periodo[0]));
        $periodo[1] = $fechaUltimo->mes.'/'.$finFecha.'/'.$fechaUltimo->anio;

    }
}


$ctas=$balance->get_balance_comp2($periodo);

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
					<h3>Anexos del Catálogo</h3>
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
                            <div class="col-md-4">
                                <form method="post" id="frm_periodo" action="#">
                                   <fieldset>
                                      <div class="control-group">
                                        <div class="controls">
                                          <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input type="text" style="width: 200px" name="periodo" id="periodo" class="form-control" value="<?php print $periodo[0]; ?> - <?php echo $periodo[1];?>" />
                                          </div>
                                        </div>
                                      </div>
                                    </fieldset>
				                </form>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <!-- <a href="../get/anexos_catalogo_excel.php?inicio=<?//=$periodo[0].'&fin='.$periodo[1]; ?>" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" target="_blank">
                                <span>Descargar XLS</span>
                                </a> -->

                                <div class="x_title">
	                               
	                                <ul class="nav navbar-right panel_toolbox">
	                                    <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>
	                               
	                                        <li><a class="download-link" title="XLS"  target="_blank" href="../get/anexos_catalogo_excel.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>" target="_blank"> <i class="fa fa-file-excel-o " style="color: black"></i></a></li>
	                                        <li><a class="download-link" title="PDF" target="_blank"  href="../get/anexos_catalogo_pdf.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>"> <i class="fa fa-file-pdf-o " style="color: black"></i></a></li>
	                                </ul>
	                                <div class="clearfix"></div>
	                            </div>
                            </div>
                        </div>
						<div class="x_content">
							<table id="datatable" class="table" width="100%" cellspacing="0">
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
												<td><?=utf8_decode($cta['descta'])?></td>
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
                 "format": 'MM/DD/YYYY',
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
