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

require_once "../class/libro_diario.class.php";
require_once ("../class/periodo.class.php");

$periodo_fecha    = new Periodo();
$diario           = new LibroDiario();


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
$pol=$diario->get_pol_diario($periodo);
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
					<h3>Libro diario</h3>
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
                                <!-- <a href="../get/libro_diario_excel.php?inicio=<?//=$periodo[0].'&fin='.$periodo[1]; ?>" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" target="_blank">
                                <span>Descargar XLS</span>
                                </a> -->


                                <div class="x_title">
	                               
	                                <ul class="nav navbar-right panel_toolbox">
	                                    <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>
	                               
	                                        <li><a class="download-link" title="XLS"  target="_blank" href="../get/libro_diario_excel.php?inicio=<?=$periodo[0].'&fin='.$periodo[1]; ?>" > <i class="fa fa-file-excel-o " style="color: black"></i></a></li>
	                                       <!--  <li><a class="download-link" title="PDF" target="_blank"  href="../get/libro_diario_pdf.php?inicio=<?//=$periodo[0].'&fin='.$periodo[1]; ?>" > <i class="fa fa-file-pdf-o " style="color: black"></i></a></li>      -->             
	                               
	                                       
	                               
	                                    
	                                </ul>
	                                <div class="clearfix"></div>
	                            </div>

                            </div>
                        </div>
						<div class="x_content">
							<?php
							foreach ($pol as $pl){
								$contasientos=$diario->get_count_pol_det($pl['rowid']);
								//print $contasientos->cant;
								if($contasientos->cant>0){
									if($pl['tipo_pol']=='I'){$tipo='Ingreso';}
									if($pl['tipo_pol']=='E'){$tipo='Egreso';}
									if($pl['tipo_pol']=='D'){$tipo='Diario';}
									if($pl['tipo_pol']=='C'){$tipo='Cheque';}
									if($pl['fk_facture']>0){
										if($pl['societe_type']==2){
											$doc=$diario->get_fac_prov($pl['fk_facture']);
                                            if(is_object($doc)){
                                                 $tipoDoctorelacionado=$doc->ref;
                                            }else{
                                                 $tipoDoctorelacionado='No se encontró relación';
                                            }
											    
										}
										else if($pl['societe_type']==1){
											$doc=$diario->get_fac_clt($pl['fk_facture']);
											
                                            if(is_object($doc)){
                                                $tipoDoctorelacionado=$doc->facnumber;
                                            }else{
                                                 $tipoDoctorelacionado='No se encontró relación';
                                            }
										}
									}else{
                                        $tipoDoctorelacionado='No aplica';
                                    }
								?>
								<table class="table table-striped">
									<tbody>
										<tr style="background-color:#7A8196; color:#FFFFFF">
											<th width="30%">Concepto: <?php echo  ($pl['concepto']); ?></th>
											<th width="20%">Fecha: <?php echo $pl['fecha']; ?></th>
											<th width="20%"> Póliza: <?php echo $tipo.": ".$pl['cons'];?></th>
											<td ></td>
											<th width="20%">Documento Relacionado: <?php echo $tipoDoctorelacionado; ?></th>
											<td></td>
										</tr>
										<?php if ($pl['tipo_pol']=='C') {  ?>

										<tr>
											<th scope="row"  width="50" colspan="2">Cheque a Nombre: <?php echo( $pl['anombrede']); ?></th>
											<th scope="row"  width="50" colspan="2">Num. Cheque: <?php echo $pl['numcheque']; ?></th>
											<td width="90"></td>
											<td width="90"></td>
										</tr>
										<?php } ?>
									</tbody>
									<tr>
										<td colspan='6'>
											<table class="table table-striped table-bordered" width="100%" cellspacing="0">
												<thead>
													<tr>
														<th width="5%">No.</th>
														<th>Cuenta</th>
														<th width="10%" style="tetext-align:right" >Debe</th>
														<th width="10%" style="tetext-align:right">Haber</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$asc=$diario->get_pol_det($pl['rowid']);
													$totdeb=0;
													$tothab=0;
													foreach ($asc as $asci){
														$nomac=$diario->get_nom_cta($asci['cuenta']);
														$totdeb+=$asci['debe'];
														$tothab+=$asci['haber'];
                                                         if(!is_object($nomac)){
                                                             $descripcion=$nomac;
                                                         }else{
                                                              $descripcion=$nomac->descripcion;
                                                         }
														?>
														<tr>
															<td width="5%"><?=$asci['asiento']?></td>
															<td><?=$asci['cuenta']." - ".$descripcion; ?></td>
															<td width="10%" align="right"><?=$moneda." ".number_format($asci['debe'],2)?></td>
															<td width="10%" align="right"><?=$moneda." ".number_format($asci['haber'],2)?></td>
														</tr>
														<?php
													}
													?>
														<tr>
															<td align="right" colspan="2"><strong>Total:</strong></td>
															<td width="10%" align="right"><strong><?=$moneda." ".number_format($totdeb,2)?></strong></td>
															<td width="10%" align="right"><strong><?=$moneda." ".number_format($tothab,2)?></strong></td>
														</tr>
													<?php
												?>
												</tbody>
											</table>
										</td>
									</tr>
								</table>
								<?php
								}
							}
							?>
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
