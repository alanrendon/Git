<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";
?>
<!DOCTYPE html>
<html lang="es">
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css'?>" rel="stylesheet">

	<?php include_once($url[0]."base/head.php");?>
		<!-- page content -->
		<div class="right_col" role="main">
			<div class="page-title">
		  		<div class="title_left">
					<h3>Grupos Padre</h3>
		  		</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
				  		<div class="x_title">
							<h2>Registro</h2>
							<div class="clearfix"></div>
				  		</div>
				  		<div class="x_content">
							<ul class="nav right panel_toolbox" >
								<li>
									<a class="btn btn-default buttons-csv buttons-html5 btn-sm regresar" title="Configuración" tabindex="0" aria-controls="datatable-buttons">
										<i class="fa fa-arrow-left"> </i> <span style="color:#7A8196 !important;">Regresar</span>
									</a>
								</li>
							</ul>
							<br />
						  	<form id="frm_altaApartado" data-parsley-validate class="form-horizontal form-label-left">
								<div class="form-group">
								   	<div class="col-md-3 col-sm-3 col-xs-12">
										<label>Tipo</label>
										<select class="select2_single form-control" name="txt_tipo" id="txt_tipo" size="90">
											<option value="1">Ventas</option>
											<option value="2">Costos</option>
											<option value="3">Gastos</option>
										</select>
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<label>Descripción</label>
										<input id="txt_descrpcion" name="txt_descrpcion" class="form-control col-md-7 col-xs-12" required="required" type="text" placeholder="Descripción">
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<br />
											<input type="hidden" name="reporte" value="2" />
											<button type="submit" class="btn btn-success">Registrar</button>
										</div>
									</div>
								</div>
						  	</form>
				  		</div>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
				  		<div class="x_title">
							<h2>Grupos padre activos</h2>
							<ul class="nav left panel_toolbox" >
								<li>
									<a href="#" data-toggle="tooltip" data-placement="top" title="Los grupos padres registrados se visualizarán en el reporte una vez que tenga asignado al menos un grupo de cuentas contables.">
									<i class="fa fa-question-circle fa-lg"> </i>
									</a>
								</li>
							</ul>
							<div class="clearfix"></div>
				  		</div>
				  		<div class="x_content">
				  			<div id="asignacion_grupos_padre_resultados"></div>
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

	 <!-- jQuery -->
	<script src="<?php echo $url[0].'../vendors/jquery/dist/jquery.min.js'?>"></script>
	<!-- Bootstrap -->
	<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
	<!-- FastClick -->
	<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>

	<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>

	 <!-- bootstrap-daterangepicker -->
	<script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
	<script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>

	<!-- Select2 -->
	<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>

	<!-- Custom Theme Scripts -->
	<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>
	 <!-- Dropzone.js -->
	<script src="<?php echo $url[0].'../vendors/dropzone/dist/min/dropzone.min.js'?>"></script>



	<script src="<?php echo $url[0].'js/app_select.js'?>"></script>
	<script src="<?php echo $url[0].'js/get_grupos_resultados.js'?>"></script>


	<script type="text/javascript">

		$(document).ready(function(){
			iniciaSelect();
			$("#frm_altaApartado").submit(put_apartado);
			$( ".regresar" ).on( "click", function(e) {
				window.location.href = 'agrupacion_resultados.php';
			});
		});

		function put_apartado(value){
			$.ajax({
				url:"../put/put_apartado.php",
				type:"POST",
				dataType: 'json',
				data:$("#frm_altaApartado").serialize(),
				success: function(data) {
					alert(data.mensaje);
					window.location.href = 'configuracion_apartados_estado.php';
				}
			});
			return false;
		}

	</script>

  </body>
</html>
