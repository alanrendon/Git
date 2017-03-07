<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/tipo_poliza.class.php";
$tipo_polizas = new Tipo_Poliza();
$tipo         = $tipo_polizas->get_tipo_poliza();
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
					<h3>Grupos</h3>
		  		</div>
		  		<ul class="nav right panel_toolbox" >
					<li>
						<a class="btn btn-default " href="agrupacion_ctas.php">
							<i class="fa fa-arrow-left"> </i> <span style="color:#7A8196 !important;">Regresar</span>
						</a>
					</li>
                </ul>
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
							
							<br />
						  	<form id="frm_altaGrupo" data-parsley-validate class="form-horizontal form-label-left">
								<div class="form-group">
									<div class="col-md-3 col-sm-3 col-xs-12">
										<label>Nombre</label>
										<input id="txt_nombre" name="txt_nombre" class="form-control col-md-7 col-xs-12" required="required" type="text" placeholder="Nombre">
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<label>Abreviatura</label>
										<input id="txt_abr" name="txt_abr" class="form-control col-md-7 col-xs-12" required="required" type="text" placeholder="Abreviatura" maxlength="3">
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<br />
											<input type="hidden" name="reporte" value="1" />
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
							<h2>Grupos activos</h2>
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
				  		<?php if(count($tipo)>0): ?>

				  			<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Grupo</th>
                                        <th width="15%">Abreviatura</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php foreach($tipo as $t): ?>
                                     <tr>
                                      <td><?php echo $t->nombre; ?> </td>
                                      <td><?php echo $t->abr; ?></td>
                                      <td>
                                          <ul class="nav panel_toolbox" style="min-width: 10px !important;">
                                              <li>
                                                  <a id="<?php echo $t->id; ?>" class="delete-link" title="Borrar">
                                                      <i class="fa fa-trash" style="color: red">

                                                      </i>
                                                  </a>
                                                </li>
                                          </ul>
                                      </td>
                                      </tr>
                                   <?php endforeach ?>
                                </tbody>
                            </table>
                          
                            <?php endif ?>
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
	<script src="<?php echo $url[0].'js/app_select.js'?>"></script>

	<script type="text/javascript">

		$(document).ready(function(){
			iniciaSelect();
			$("#frm_altaGrupo").submit(put_apartado);
			$(".delete-link").submit(put_apartado);
			
		});

		function put_apartado(value){
			$.ajax({
				url:"../put/put_grupo_cuentas_poliza.php",
				type:"POST",
				dataType: 'json',
				data:$("#frm_altaGrupo").serialize(),
				success: function(data) {
					alert(data.mensaje);
					location.reload();
				}
			});
			return false;
		}
        
        $( document ).on( "click", ".delete-link", function() {
		var id = $(this).attr('id');
		var tr = $(this).parent().parent().parent().parent();
		if ( confirm("¿Está seguro de eliminar este grupo? Se borrán todas las cuentas asociadas") ) {
			$.ajax({
				type: "POST",
				url: '../edit/eliminar_grupo_cuenta.php',
				data: "id="+id,
				success: function(data) {
					tr.remove();
				}
			});
		}
	});

	</script>

  </body>
</html>
