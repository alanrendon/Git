<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";


require_once "../class/cuentas_mayor.class.php";
require_once ("../class/periodo.class.php");

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$monthNames = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
  "Julio", "Agosto", "Septiembre", "Octubre", "Nviembre", "Diciembre"
);


$periodo_fecha  = new Periodo();
$mayor          = new CuentasMayor();

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

$ctas=$mayor->get_mayor($periodo);


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
					<h3>Cuentas de Mayor</h3>
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
                               <!--  <a href="../get/cuentas_mayor_excel.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" target="_blank">
                                <span>Descargar XLS</span>
                                </a> -->

                                <div class="x_title">
	                               
	                                <ul class="nav navbar-right panel_toolbox">
	                                    <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>	                               
	                                    <li><a class="download-link" title="XLS"  target="_blank" href="../get/cuentas_mayor_excel.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>" > <i class="fa fa-file-excel-o " style="color: black"></i></a></li>
	                                    <li><a class="download-link" title="PDF" target="_blank"  href="../get/cuentas_mayor_pdf.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>" > <i class="fa fa-file-pdf-o " style="color: black"></i></a></li>             	                               	                                   
	                                </ul>
	                                <div class="clearfix"></div>
	                            </div>

                            </div>
                        </div>
						<div class="x_content">
							<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
								<thead>
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
								</thead>
		
							</table>
							<table class="table table-striped table-bordered" width="100%" cellspacing="1" border="1">
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
													<td width="15%"><?=$arrayMeses[((int)$now->format('m'))-1]?></td>
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
											<!--<tr>
												<td width="10%" style="text-align:right"><strong>Total:</strong></td>
												<td width="15%" style="text-align:right"><strong><?php //$moneda." ".number_format($totaldebe,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong>Total:</strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
												<td width="12.5%" style="text-align:right"><strong><?php //$moneda." ".number_format($totalhaber,2)?></strong></td>
											</tr>-->
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
		</div>
	</div>

<footer>
	<div class="pull-right">
		Contab PRO 1.0 | Dolibarr ERP by <a href="http://www.auriboxconsulting.com/">Auribox Consulting</a>
	</div>
	<div class="clearfix"></div>
</footer>


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
