<?php 
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/cat_cuentas.class.php";
require_once "../class/conf_apartados.class.php";

$cuenta = new Cuenta();
$arreglo_cuentas = $cuenta->get_cuentas_agrupacion();

$apartados = new Apartados();
$arreglo_apartados = $apartados->get_apartados(2);


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
	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Agrupación </h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Estado de resultados</h2>
							<div class="clearfix"></div>
						</div>
						<ul class="nav right panel_toolbox" >
							<li>
								<a href="#" data-toggle="tooltip" data-placement="top" title="La cada uno de los grupos creados serán visualizados en el reporte, si la agrupación no es la adecueda el reporte podría generar inconsistencias.">
									<i class="fa fa-question-circle fa-lg"> </i>
								</a>
							</li>
							<li>
								<a class="btn btn-default buttons-csv buttons-html5 btn-sm add-conf" title="Configuración" tabindex="0" aria-controls="datatable-buttons">
									<i class="fa fa-list"> </i> <span style="color:#7A8196 !important;">Configuración</span> 
								</a>
							</li>
							<li>
								<a class="btn btn-default buttons-csv buttons-html5 btn-sm add-link" title="Agregar" tabindex="0" aria-controls="datatable-buttons" idpoliza="<?php echo $key['id']; ?>">
									<i class="fa fa-plus"> </i> <span style="color:#7A8196 !important;">Nuevo grupo</span> 
								</a>
							</li>
						</ul>
						<div class="x_content" id="asignacion_resultado">
						</div>
						<div id="add_modalGrupo" class="modal fade" role="dialog">
							<div class="modal-dialog" style="width: 60% !important;">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Nuevo grupo</h4>
									</div>
									<form id="frm_putGrupo" accept-charset="utf-8" data-parsley-validate class="form-horizontal form-label-left">
										<div class="modal-body">
											<div class="row">
												<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
													<input type="hidden" name="idgrupo" id="idgrupo" value="" />
													<div class="form-group">
														<div class="col-sm-6">
															<label class="">Grupo Padre <span class="required">*</span></label>
															<select class="select2_single form-control" name="grupo_padre" id="grupo_padre" size="90" style="width:180px">
																<option value=""></option>
																<?php foreach ( $arreglo_apartados as $key => $value ): ?>
																    <option value="<?php print $value['rowid']; ?>">
																    <?php print  ($value['apartado']); ?></option>
															    <?php endforeach ?>
															</select>
														</div>
														<div class="col-sm-6">
															<label class="">Nombre del Grupo <span class="required">*</span></label>
															<input type="text" placeholder="Nombre del grupo" name="grupo" id="grupo"  class="form-control col-md-7 col-xs-12" value="">
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-6">
															<label class="">Cuenta inicial <span class="required">*</span></label>
															<select class="select2_single form-control" name="cuenta_inicial" id="cuenta_inicial" size="90" style="width:300px">
															<?php foreach ( $arreglo_cuentas as $key => $value ): ?>
																<option value="<?php print $key; ?>">
																<?php print  ($value); ?></option>
															<?php endforeach ?>
															</select>
														</div>
														<div class="col-sm-6">
															<label class="">Cuenta final <span class="required">*</span></label>
															<select class="select2_single form-control" name="cuenta_final" id="cuenta_final" size="90" style="width:300px">
															<?php foreach ( $arreglo_cuentas as $key => $value ): ?>
																<option value="<?php print $key; ?>">
																<?php print  ($value); ?></option>
															<?php endforeach ?>
															</select>
														</div>
													</div>    
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<input type="hidden" name="tipo" id="tipo" value="2" />
											<button type="submit" class="btn btn-success">Registrar grupo</button>
											<button type="button" class="btn btn-green" data-dismiss="modal">Cerrar</button>
										</div>
									</form>
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


<script type="text/javascript" src="<?php echo $url[0].'js/app_select.js'?>"></script>
<script type="text/javascript" src="<?php echo $url[0].'/js/get_agrupacion_resultado.js'?>"></script>


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
<script type="text/javascript">
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip(); 
		
		$( ".add-link" ).on( "click", function(e) { 
			abrirModalGrupo(); 
		});

		$( ".edit-grupo" ).on( "click", function(e) { 
			var idGrupo = $(this).attr('id');
			abrirModalUpdate(idGrupo); 
		});

		$( ".add-conf" ).on( "click", function(e) {
			window.location.href = 'configuracion_apartados_estado.php';
		});
	});

	function abrirModalGrupo(){
		$('#add_modalGrupo').modal();  
		$("#frm_putGrupo").submit(putGrupo); 
		iniciaSelect();
	}

	function abrirModalUpdate(){
		$("#idgrupo").val(idGrupo);
		//consultaGrupo(idGrupo);
		$('#add_modalGrupo').modal();  
		/*$("#frm_putAsiento").submit(updateAsiento); */
		iniciaSelect();
	}
</script>

</body>
</html>