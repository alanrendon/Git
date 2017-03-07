<?php

if(!isset($_GET['id']) || empty($_GET['id']) ){
  header("Location: ../index.php");
  die();  
}

$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/grupo.class.php";
require_once "../class/conf_apartados.class.php";
require_once "../class/cat_cuentas.class.php";

$grupo = new Grupo();

$arreglo = $grupo->get_grupo($_GET['id']);
$apartados = new Apartados();

if($_GET['tipo']==1){
    $arreglo_apartados = $apartados->get_apartados(1);
}else{
    $arreglo_apartados = $apartados->get_apartados(2);
}


$cuenta = new Cuenta();
$arreglo_cuentas = $cuenta->get_cuentas_agrupacion();
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
							<h2>Edición de grupo</h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<form id="frm_updateGrupo" accept-charset="utf-8" data-parsley-validate class="form-horizontal form-label-left">
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
											<div class="form-group">
												<div class="col-sm-6">
													<label class="">Grupo Padre <span class="required">*</span></label>
													<select class="select2_single form-control" name="grupo_padre" id="grupo_padre" size="90">
														<option value=""></option>
														  <?php
															foreach ( $arreglo_apartados as $key => $value ):
																$select = ($arreglo[0]['fk_grupo'] == $value['rowid']) ? 'selected': '';
														  ?>
																<option value="<?php print $value['rowid']; ?>" <?php print $select; ?> >
																	<?php print  ($value['apartado']); ?>
																</option>
															<?php endforeach ?>
													</select>
												</div>
												<div class="col-md-6 col-sm-6 col-xs-6">
													<label class="">Nombre del Grupo <span class="required">*</span></label>
													<input type="text" placeholder="Nombre del grupo" name="grupo" id="grupo"  class="form-control col-md-7 col-xs-12" value="<?php print $arreglo[0]['grupo']; ?>">
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-6 col-sm-6 ">
													<label class="">Cuenta inicial <span class="required">*</span></label>
													<select class="select2_single form-control" name="cuenta_inicial" id="cuenta_inicial" size="90">
													  <?php
														foreach ( $arreglo_cuentas as $key => $value ):
															$select = ($arreglo[0]['fk_codagr_ini'] == $key) ? 'selected': '';
													   ?>
														<option value="<?php print $key; ?>" <?php print $select; ?>>
														<?php print  ($value); ?>
														</option>
													<?php endforeach ?>
													</select>
												</div>
												<div class="col-md-6 col-sm-6 ">
													<label class="">Cuenta final <span class="required">*</span></label>
													<select class="select2_single form-control" name="cuenta_final" id="cuenta_final" size="90">
													   <?php
														foreach ( $arreglo_cuentas as $key => $value ):
															$select = ($arreglo[0]['fk_codagr_fin'] == $key) ? 'selected': '';
													   ?>
														<option value="<?php print $key; ?>" <?php print $select; ?>> 
														<?php print  ($value); ?>
														</option>
													<?php endforeach ?>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<input type="hidden" name="rowid" id="rowid" value="<?php print $_GET['id'];?>" />
									<button type="button" class="btn btn-primary regresar">Regresar</button>
									<button type="submit" class="btn btn-success actualizar">Actualizar</button>
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
<!-- Bootstrap -->
<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>

<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>

<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>

<script type="text/javascript" src="<?php echo $url[0].'js/app_select.js'?>"></script>
<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>
<script type="text/javascript" src="<?php echo $url[0].'/js/get_agrupacion_balance.js'?>"></script>

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
iniciaSelect();
$(document).ready(function(){
	$( ".regresar" ).on( "click", function(e) {
		location.replace(document.referrer);
	});

	$( ".actualizar" ).on( "click", function(e) {
		$("#frm_putGrupo").submit(updateGrupo);
	});
    $("#frm_updateGrupo").submit(Actualizar_Agrupacion);
});
    
    
function Actualizar_Agrupacion(){
    $form = $(this);
    $.ajax({
      method: "POST",
      url: "../update/update_agrupacion.php",
      data: $form.serialize(),
      dataType:'json'
    })
    .done(function( data ) {
        alert( data.msg );
            location.replace(document.referrer);
      });
return false;
}
</script>

</body>
</html>
