<?php
$url[0] = "../";
require_once ("../class/poliza.class.php");
require_once "../class/asiento.class.php";
require_once "../class/cat_cuentas.class.php";

$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";
$polizas        = new Poliza();
$asiento        = new Asiento();
$cuenta         = new Cuenta();
$cta=$_REQUEST['cta'];
$fecha1=$_REQUEST['fc1'];
$fecha2=$_REQUEST['fc2'];
$nomcu=$cuenta->get_nom_cuenta($cta);
$arreglo_Polizas = $polizas->getPolizasPeriodoCuenta($cta, $fecha1, $fecha2);
//print_r($arreglo_Polizas);
?>
<!DOCTYPE html>
<html lang="es">
	<!-- Select2 -->
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">

	<!-- Datatables -->
	<link href="<?php echo $url[0].'../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css'?>" rel="stylesheet">
	<?php
	include_once($url[0]."base/head.php");
	?>
<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Pólizas</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Consulta Correspondiente  <?php print $fecha1; ?> a <?php echo $fecha2;?> Cuenta: <?=$cta." - ".$nomcu?></h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div id="dv_dibujarPolizas">
								<?php if ( $arreglo_Polizas ){  ?>
									<?php foreach ( $arreglo_Polizas as $key ): ?>
										<?php if ( $key['societe_type'] == 1 )
												$tipoDoctorelacionado =  $key['facnumber'];
											else if( $key['societe_type'] == 2 )
												$tipoDoctorelacionado = $key['ref'];
											else
												$tipoDoctorelacionado = "No hay docto.";
											?>
											<div class="col-md-12 col-xs-12 col-lg-12">
												 <div class=" x_panel" style="border: 1px solid #c2c2c2 !important;">
													<div class="x_title">
														<h2>Póliza: <?php echo $key['tipo_pol'].": ".$key['cons'];?></h2>
														 <div class="clearfix"></div>
													</div>
													<div class="x_content">
														<table class="table table-striped">
															<tbody>
																<tr style="background-color:#7A8196; color:#FFFFFF">
																	<th width="30%">Concepto: <?php echo  ($key['concepto']); ?></th>
																	<th width="20%">Fecha: <?php echo $key['fecha']; ?></th>
																	<td></td>
																	<td ></td>
																	<th width="20%">Documento Relacionado: <?php echo $tipoDoctorelacionado; ?></th>
																	<td></td>
																</tr>
																<tr>
																	<td width="50">Tipo Póliza: <?php echo $key['tipo_pol']; ?></td>
																	<td></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																</tr>
																<?php if ( $key['tipo_pol'] == 'Cheque' ) {  ?>
																
																<tr>
																	<th scope="row"  width="50" >Cheque a Nombre: </th>
																	<td><?php echo( $key['anombrede']); ?></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																</tr>
																<tr>
																	<th scope="row"  width="50" >Num. Cheque: </th>
																	<td><?php echo $key['numcheque']; ?></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																	<td width="90"></td>
																</tr>
																<?php } ?>
															</tbody>
														</table>
														<div class="ln_solid"></div>
														
																												
															<?php
															$arrasiento = $asiento->get_asientoPoliza($key['id']);
															if ( $arrasiento ) {
																$total_debe = 0;
																$total_habe = 0;
															?>
															<table class="table table-striped table-bordered" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th width="5%">No.</th>
																		<th>Cuenta</th>
																		<th width="10%" align="right">Debe</th>
																		<th width="10%" align="right">Haber</th>
																	</tr>
																</thead>
																<tbody> 
																<?php 
																	foreach ( $arrasiento as $key ): 
																		$total_debe += $key["debe"];
																		$total_habe += $key["haber"];
																?>
																	<tr>
																		<td align="center"><?php echo $key["asiento"];?></td>
																		<td>
																			<?php 
																			$nom_cuenta = $cuenta->get_nom_cuenta($key["cuenta"]);
																			echo $key["cuenta"].' - '. ($nom_cuenta);

																			?>
																		</td>
																		<td align="right"><?=$moneda?> <?php echo number_format($key["debe"],2,'.',','); ?></td>
																		<td align="right"><?=$moneda?> <?php echo number_format($key["haber"],2,'.',','); ?></td>
																		
																	</tr>
																<?php endforeach ?>
																	<tr>
																		<td colspan="2"><strong>Total</strong></td>
																		<td align="right"><strong><?=$moneda?> <?php print number_format($total_debe,2,'.',','); ?></strong></td>
																		<td align="right"><strong><?=$moneda?> <?php print number_format($total_habe,2,'.',','); ?></strong></td>
																		
																	</tr>
																	<?php if ( (int)$total_debe !== (int)$total_habe ) { ?>
																	<tr>
																		<td colspan="2"></td>
																		<td colspan="2"><span style="color:red">Los totales no coinciden, favor de verificar.</span></td>								
																	</tr>
																	<?php  } ?>
																</tbody>
															</table>
															<?php
															}
															?>
															</div>
														</div>
													</div>
									<?php endforeach ?>
								<?php } else { print 'No se encontraron resultados.'; } ?>
							</div>
							<div id="add_modalAsiento" class="modal fade" role="dialog">
								<div class="modal-dialog" style="width: 60% !important;">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Registro de cuenta</h4>
										</div>
											<div class="modal-body">
												<div class="row">
													<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
														<input type="hidden" name="id_poliza" id="id_poliza">
														<div class="form-group">
															<div class="col-sm-3">
																<label class="">Cuenta <span class="required">*</span></label>
																<select class="select2_single form-control" name="txt_cuenta"  id="txt_cuenta" size="90">
																	<?php foreach ( $arreglo_cuentas as $key => $value ): ?>
																		<option value="<?php print $key; ?>">
																		<?php print $value; ?></option>
																	<?php endforeach ?>
																</select>
															</div>
															
															<div class="col-md-3 col-sm-3 col-xs-3">
																<label class="">Debe <span class="required">*</span></label>
																<input type="text" placeholder="Debe" name="txt_debe" id="txt_debe"  class="form-control col-md-7 col-xs-12" value="0">
															</div>
															<div class="col-md-3 col-sm-3 col-xs-3">
																<label class="">Haber <span class="required">*</span></label>
																<input type="text" placeholder="Haber" name="txt_haber"  id="txt_haber" class="form-control col-md-7 col-xs-12" value="0">
															</div>
															
														</div>    
													</div>
												</div>
											</div>
									</div>
								</div>
							</div>
						</div>
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
</div>
</div>

<script src="<?php echo $url[0].'../vendors/jquery/dist/jquery.min.js'?>"></script>
<!-- Bootstrap -->
<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>

<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>

<!-- Select2 -->
<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>
<script src="<?php echo $url[0].'js/app_select.js'?>"></script>
<script src="<?php echo $url[0].'js/asientos.js'?>"></script>
<script src="<?php echo $url[0].'js/polizas_tools.js'?>"></script>

<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>

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
<script src="<?php echo $url[0].'../vendors/datatables.net-scroller/js/dataTables.scroller.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/jszip/dist/jszip.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/pdfmake.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/vfs_fonts.js'?>"></script>
 
</html>