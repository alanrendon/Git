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
	<?php include_once($url[0]."base/head.php"); ?>
	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Saldos iniciales </h3>
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
							<div align="right">
								<a href="ejemplo_saldos.csv" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons">
								<span>Descargar formato CSV de ejemplo </span>
								</a>
							</div>
						
							<form data-parsley-validate class="form-horizontal form-label-left">
                                <p id="mensaje_carga">Se creará una póliza de diario cargando los saldos de las cuentas contenidas en el CSV.</p>
                                 <div class="form-group">
                                    <br>
                                    <div class="col-md-4 col-sm-12">
                                          <input placeholder="Fecha" id="txt_fecha" name="txt_fecha" class="date-picker form-control col-md-7 col-xs-12" required="required" type="text">
                                    </div>
								</div>
  								<div class="form-group">
  									<div class="col-md-6 col-sm-6 col-xs-12">
										<input name="file" id="file" type="file" multiple /> <br />
										<button type="button" class="btn btn-success" id="cargar_saldos">Registrar saldos</button>
  									</div>
								</div>

							</form>
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
<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>

	 <!-- bootstrap-daterangepicker -->
	<script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
	<script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>

<script type="text/javascript">
	$(document).ready(function(){
        $('#txt_fecha').daterangepicker({
			   "singleDatePicker": true,
			calender_style: "picker_4",
                "locale": {
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                }
		  });

		$( "#cargar_saldos" ).on( "click", function(e) {
			e.preventDefault();
			$("#mensaje_carga").html('');

    		var file = $('#file').prop('files')[0];

			var form = new FormData();

			if( file ) {
				form.append('file', file);
                form.append('fecha', $('#txt_fecha').val());
				
				$.ajax({
					url: '../put/put_carga_saldos_inicial.php',
					dataType: 'text',
					cache: false,
					contentType: false,
					processData: false,
					data: form,
					type: 'post',
					success: function(data) {
						$("#mensaje_carga").html(data);
						setTimeout(function(){
							window.location.href = "carga_saldos.php";
						}, 4000);
					}
				});
			}
			else {
				$("#mensaje_carga").html('Debe agregar un archivo CVS');
			}
		});
	});
</script>

</body>
</html>
