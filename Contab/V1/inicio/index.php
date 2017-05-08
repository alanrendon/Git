<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
	<?php include_once("base/head.php"); ?>
	<!-- page content -->
	<div class="right_col" role="main">
		<!-- top tiles -->
		<div class="row tile_count"></div>
		<!-- /top tiles -->
		<div class="row">
			<div class="jumbotron">
				<h1>Bienvenido a Contab PRO 3.0 el Módulo de Contabilidad</h1>
				<blockquote>
					<p>
						<h2><i class="fa fa-tag"></i> Actualización:</h2>
						<ul>
							<li>Registro de cuentas masiva(CSV)</li>
							<li>Integración de diferentes niveles de cuentas</li>
							<li>Configuración personlizada para agrupaciones </li>
							<li>Mejoras para la gestión de Pólizas</li>
							<li>Mejoras en los reportes</li>
						</ul>
					</p>
				</blockquote>
				<br />
				<br />
				<br />
				<div align="right">
					<span>Contab PRO 3.0 | Para más información consulte el <a href="#"> Manual de usuario</a> </span>
				</div>
			</div>
		</div>
		<br />
	</div>
	<!-- /page content -->

	<!-- footer content -->
	<footer>
		<div class="pull-right">
			Módulo de Contabilidad | Dolibarr ERP by <a href="http://www.auriboxconsulting.com/">Auribox Consulting</a>
		</div>
		<div class="clearfix"></div>
	</footer>
	<!-- /footer content -->
</div>
</div>

	<!-- jQuery -->
	<script src="../vendors/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
	<!-- FastClick -->
	<script src="../vendors/fastclick/lib/fastclick.js"></script>
	<!-- NProgress -->
	<script src="../vendors/nprogress/nprogress.js"></script>
	<!-- Chart.js -->
	<script src="../vendors/Chart.js/dist/Chart.min.js"></script>
	<!-- gauge.js -->
	<script src="../vendors/bernii/gauge.js/dist/gauge.min.js"></script>
	<!-- bootstrap-progressbar -->
	<script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
	<!-- iCheck -->
	<script src="../vendors/iCheck/icheck.min.js"></script>
	<!-- Skycons -->
	<script src="../vendors/skycons/skycons.js"></script>
	<!-- Flot -->
	<script src="../vendors/Flot/jquery.flot.js"></script>
	<script src="../vendors/Flot/jquery.flot.pie.js"></script>
	<script src="../vendors/Flot/jquery.flot.time.js"></script>
	<script src="../vendors/Flot/jquery.flot.stack.js"></script>
	<script src="../vendors/Flot/jquery.flot.resize.js"></script>
	<!-- Flot plugins -->
	<script src="js/flot/jquery.flot.orderBars.js"></script>
	<script src="js/flot/date.js"></script>
	<script src="js/flot/jquery.flot.spline.js"></script>
	<script src="js/flot/curvedLines.js"></script>
	<!-- jVectorMap -->
	<script src="js/maps/jquery-jvectormap-2.0.3.min.js"></script>
	<!-- bootstrap-daterangepicker -->
	<script src="js/moment/moment.min.js"></script>
	<script src="js/datepicker/daterangepicker.js"></script>

	<!-- Custom Theme Scripts -->
	<script src="../build/js/custom.min.js"></script>

	<!-- jVectorMap -->
	<script src="js/maps/jquery-jvectormap-world-mill-en.js"></script>
	<script src="js/maps/jquery-jvectormap-us-aea-en.js"></script>
	<script src="js/maps/gdp-data.js"></script>
	<script src="js/app.js"></script>
</body>
</html>

<?php ob_end_flush(); ?>
