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




if(isset($_GET['periodo']) && isset($_GET['cta'])){
    $periodo = explode("-", $_GET['periodo']);
    $periodo[0]=trim($periodo[0]);
    $periodo[1]=trim($periodo[1]);
    $cta = addslashes(trim($_GET['cta'])) ;

}


$ctas=$balance->get_balance_comp_desc_cuenta($periodo,$cta);

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
					<h3>Descripción de cuenta <?php echo $cta; ?> </h3>
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
						<div class="x_content">
							<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th width="40%">Cuenta</th>
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
                                    
										if($cta['nivel']==1){
								
										}else{
                                            
										$ini=$balance->get_saldo_ini($periodo,$cta['cta']);
										@$idebe=$ini->debe;
										@$ihaber=$ini->haber;

										
                                        if($cta['nivel']==2 && $cta['afectacion']==1){
                                            $saldi=$balance->get_balance_comp_debhab_padre($periodo,$cta['cta']);
                                            continue;
                                           
                                        }else{
                                            $saldi=$balance->get_balance_comp_debhab($periodo,$cta['cta']);
                                          
                                        }
                                            
										if($cta['natur']=='D'){
											$inicial=$idebe-$ihaber;
											@$sact=$inicial+$saldi->debe-$saldi->haber;
                                            $totini+=$inicial;
                                            $totact+=$sact;
                                             
										}else {
											$inicial=$ihaber-$idebe;
											@$sact=$inicial+$saldi->haber-$saldi->debe;
                                            $totini-=$inicial;
                                            $totact-=$sact;
										}
										
										@$totdebe+=$saldi->debe;
										@$tothaber+=$saldi->haber;
										
										if(@$saldi->haber!=0 || @$saldi->debe!=0 || @$inicial!=0){
									?>
										<tr>
											<td><?=$cta['cta']." ".utf8_decode($cta['descta'])?></td>
										
											<td align="right"><?=$moneda." ".number_format($sact,2)?></td>
										</tr>
									<?php
										}
										}
									}
									?>
									<tr>
										<td align="right"><strong>Total:</strong></td>
									
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
