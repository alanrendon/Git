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
	<style>
	    td, tr, th {
			text-align: center !important;
			vertical-align: top !important;
		}
		tr{
			column-width: 10px !important;
		}
		.left_periodo {
			text-align: left !important;
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
					<h3>Cuentas</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Registradas</h2>
							<div class="clearfix"></div>
							
						</div>
						
						<ul class="nav right panel_toolbox">
							<li>
								<a idpoliza="51" aria-controls="datatable-buttons" tabindex="0" title="Registrar" class="btn btn-default buttons-csv buttons-html5 btn-sm registrar">
								<i style="color: blue" class="fa fa-plus fa-lg"> </i> <span style="color:#7A8196 !important;">Registrar cuenta</span>
								</a>
							</li>
							 <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <a href="../get/get_cuentas_csv.php" class="btn btn-default buttons-csv buttons-html5 btn-sm" target="_blank">
                                <span>Descargar CSV</span>
                                </a>
                            </div>
						</ul>
						<div class="x_content" id="cuentas_registradas">
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

<script type="text/javascript" src="<?php echo $url[0].'/js/get_list_cuentas_registradas.js'?>"></script>
<script type="text/javascript" src="<?php echo $url[0].'js/app_select.js'?>"></script>
<script>
$(document).ready(function(){
	$( ".registrar" ).on( "click", function(e) {
		window.location.href = 'registrar.php';
	});
});
</script>
</body>
</html>
