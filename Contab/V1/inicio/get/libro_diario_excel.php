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

$diario = new LibroDiario();
if($_REQUEST['inicio']){
    $periodo[0] = $_REQUEST['inicio'];
    $periodo[1] = $_REQUEST['fin'];

}else{
    $periodo[0] = date('Y/m/01');
    $periodo[1] = date('Y/m/t');
}
require_once $url[0]."class/admin.class.php";
$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
$pol=$diario->get_pol_diario($periodo);

header("Content-type: application/ms-excel");
header("Content-disposition: attachment; filename=libro_diario_".$periodo[0]."_a_".$periodo[1]."_".date('Ymd').".xls");

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
					<h3>Libro diario correspondiente de: <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
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
											$tipoDoctorelacionado=$doc->ref;
										}
										else if($pl['societe_type']==1){
											$doc=$diario->get_fac_clt($pl['fk_facture']);
											$tipoDoctorelacionado=$doc->facnumber;
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
														<th colspan="3">Cuenta</th>
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
															<td colspan="3"><?=$asci['cuenta']." - ".$descripcion; ?></td>
															<td width="10%" align="right"><?=$moneda." ".number_format($asci['debe'],2)?></td>
															<td width="10%" align="right"><?=$moneda." ".number_format($asci['haber'],2)?></td>
														</tr>
														<?php
													}
													?>
														<tr>
															<td align="right" colspan="4"><strong>Total:</strong></td>
															<td width="10%" align="right"><strong><?=$moneda." ".number_format($totdeb,2)?></strong></td>
															<td width="10%" align="right"><strong><?=$moneda." ".number_format($tothab,2)?></strong></td>
														</tr>
														<tr>
														<td colspan="6">&nbsp;</td>
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
