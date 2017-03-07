<?php
	$url[0] = "../";
	$url[1] = "../configuracion/";
	$url[2] = "../asignacion/";
	$url[3] = "../cuentas/";
	$url[4] = "../polizas/";
	$url[5] = "../conf/";
	$url[6] = "../periodos/";
	$url[7] = "../reportes/";

	require_once ("../class/periodo.class.php");

	$periodos     = new Periodo();
	$year_periodo = $periodos->get_AnioPeriodo();

	if(isset($_POST['txt_anioPeriodo'])){
		$periodos = $periodos->get_Periodos((int)$_POST['txt_anioPeriodo']);
		$anio = $_POST['txt_anioPeriodo'];

	}
	else {
		$periodos = $periodos->get_Periodos("T");
		$anio = "T";
	}
	
	
?>
<!DOCTYPE html>
<html lang="es">
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
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
	<?php
	include_once($url[0]."base/head.php");
	?>

	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Periodos Contables</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_content">
							<div class="row">
								<div class="col-sm-12">
									<div class="col-md-2 col-sm-2 col-xs-12">
										<form method="post" id="frm_anioPeriodo" action="#">
											<label>Seleccione el año</label>
											<select class="select2_single form-control" name="txt_anioPeriodo" id="txt_anioPeriodo">
												<option value="T">Todos</option>
												<?php foreach ($year_periodo as $key):
													if($anio ==$key['anio']) : ?>
														<option value="<?php echo $key['anio'] ?>" selected ><?php echo $key['anio'] ?></option>
													<?php else: ?>
														<option value="<?php echo $key['anio'] ?>"  ><?php echo $key['anio'] ?></option>
													<?php endif ?>
												<?php endforeach ?>
											 </select>
										</form>
									</div>

									<div id="dv_addPeriodo" style="display: none">
                                         
										 <form id="frm_AltaPeriodo" data-parsley-validate class="form-horizontal form-label-left">
											<div class="col-sm-3 ">
												<label>Fecha</label>
												<input id="txt_anio" name="txt_anio" class="form-control col-md-7 col-xs-12" required="required" type="month" placeholder="AAAA-MM">
											</div>
											<div class="col-sm-3">
												<label>
                                                    Periodo de Ajuste
												    <input name="ajuste" class="form-control"  type="checkbox" value="1">
												</label>
											</div>
											<br />
											<div class="col-sm-2">
												<button type="submit" class="btn btn-success" >Agregar</button>
											</div>
										 </form>
										   <label class="pull-right">Periodo de ajuste: ingrese año deseado y mes x, active casilla.</label>
									</div>

								</div>
								<div class="clearfix"></div>
								<br />
								<div class="col-sm-12">
									<?php if(is_array($periodos)):?>
									<table class="table table-striped jambo_table bulk_action">
										<thead>
											<tr>
												<th class="left_periodo">Mes</th>
												<th>Balcance <br />general</th>
												<th>Estado de <br />resultados</th>
												<th>Balanza de <br /> comprobación</th>
												<th>Libro diario</th>
												<th>Cuentas <br />de mayor</th>
												<th>Estado</th>
												<th>Periodo</th>
												<th colspan="4">Archivos XML</th>
											</tr>
									  	</thead>
									  	<tbody id="cuerpo_Periodos" >
										<?php foreach ($periodos as $key ):
                                            
                                            if($key['m']<13)
											     $ultimo = cal_days_in_month(CAL_GREGORIAN, $key['m'], $key['anio']);
										?>
											<tr>
											  	<?php if( !isset($_POST['txt_anioPeriodo']) || ( isset($_POST['txt_anioPeriodo']) && $_POST['txt_anioPeriodo']=="T")): ?>
												  <td scope="row" class="left_periodo"><?php echo $key['mes']."-".$key['anio'] ?></td>
											   	<?php else: ?>
												   <td scope="row" class="left_periodo"><?php echo $key['mes'] ?></td>
											   	<?php endif ?>
											
											  	<td>
													<?php if ($key['validado_bg']==1 ): ?>
														<a class="p_estado_r" idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_bg" title="Validado" style="cursor: pointer"><i class="fa fa-check-square-o fa-lg" style="color:green;"></i></a>
													<?php else: ?>
														<a class="p_estado_r" title="Por validar" columna="validado_bg" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer"><i class="fa fa-clock-o fa-lg" style="color:orange;"></i></a>
													<?php endif ?>
													<a class="validado_bg" title="Ver" style="cursor: pointer" href="../get/get_balance_general_excel.php?inicio=<?php echo date($key['anio'].'/'.$key['m'].'/01').'&fin='.date($key['anio'].'/'.$key['m'].'/'.$ultimo); ?>"  target="_blank"><i class="fa fa-download fa-lg" style="color:gray;"></i></a>
											  	</td>
											   	<td>
													<?php if ($key['validado_er']==1): ?>
														<a class="p_estado_r" idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_er" title="Validado" style="cursor: pointer"><i class="fa fa-check-square-o fa-lg" style="color:green;"></i></a>
													<?php else: ?>
														<a class="p_estado_r" title="Por validar" columna="validado_er" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer"><i class="fa fa-clock-o fa-lg" style="color:orange;"></i></a>
													<?php endif ?>
													<a class="validado_er" title="Ver" style="cursor: pointer" href="../get/get_estado_resultados_excel.php?inicio=<?php echo date($key['anio'].'/'.$key['m'].'/01').'&fin='.date($key['anio'].'/'.$key['m'].'/'.$ultimo); ?>"  target="_blank"><i class="fa fa-download fa-lg" style="color:gray;"></i></a>
											  	</td>
											  	<td>
													<?php if ($key['validado_bc']==1): ?>
														<a class="p_estado_r" idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_bc" title="Validado" style="cursor: pointer"><i class="fa fa-check-square-o fa-lg" style="color:green;"></i></a>
													<?php else: ?>
														<a class="p_estado_r" title="Por validar" columna="validado_bc" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer"><i class="fa fa-clock-o fa-lg" style="color:orange;"></i></a>
													<?php endif ?>
													<a class="validado_bc" title="Ver" style="cursor: pointer" href="../get/balance_comprobacion_excel.php?inicio=<?php echo date($key['anio'].'/'.$key['m'].'/01').'&fin='.date($key['anio'].'/'.$key['m'].'/'.$ultimo); ?>"  target="_blank"><i class="fa fa-download fa-lg" style="color:gray;"></i></a>
											  	</td>
											  	<td>
													<?php if ($key['validado_ld']==1): ?>
														<a class="p_estado_r" idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_ld" title="Validado" style="cursor: pointer"><i class="fa fa-check-square-o fa-lg" style="color:green;"></i></a>
													<?php else: ?>
														<a class="p_estado_r" title="Por validar" columna="validado_ld" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer"><i class="fa fa-clock-o fa-lg" style="color:orange;"></i></a>
													<?php endif ?>
													<a class="validado_ld" title="Ver" style="cursor: pointer" href="../get/libro_diario_excel.php?inicio=<?php echo date($key['anio'].'/'.$key['m'].'/01').'&fin='.date($key['anio'].'/'.$key['m'].'/'.$ultimo); ?>"  target="_blank"><i class="fa fa-download fa-lg" style="color:gray;"></i></a>
												</td>
											  	<td>
												<?php if ($key['validado_lm']==1): ?>
														<a class="p_estado_r"  idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_lm" title="Validado" style="cursor: pointer"><i class="fa fa-check-square-o fa-lg" style="color:green;"></i></a>
													<?php else: ?>
														<a class="p_estado_r"  idPeriodo="<?php echo $key['rowid']; ?>" columna="validado_lm" title="Por validar" style="cursor: pointer"><i class="fa fa-clock-o fa-lg" style="color:orange;"></i></a>
													<?php endif ?>
													<a class="validado_er" title="Ver" style="cursor: pointer" href="../get/cuentas_mayor_excel.php?inicio=<?php echo date($key['anio'].'/'.$key['m'].'/01').'&fin='.date($key['anio'].'/'.$key['m'].'/'.$ultimo); ?>"  target="_blank"><i class="fa fa-download fa-lg" style="color:gray;"></i></a>
											 	</td>
											 	<td>
													<?php if ($key['estado']==1 && $key['validado_bg']==1 && $key['validado_bc']==1 && $key['validado_ld']!==1 && $key['validado_er']==1): ?>
														<a class="p_abierto" title="Abierto" style="cursor: pointer"><i class="fa fa-unlock fa-lg" style="color:green;"></i></a>
													<?php elseif ($key['estado']==2): ?>
														<a class="p_validar" title="Por validar" style="cursor: pointer"><i class="fa fa-hourglass-start fa-lg" style="color:orange;"></i></a>
													<?php else: ?>
														<a class="p_cerrado" title="Cerrado" style="cursor: pointer"><i class="fa fa-lock fa-lg" style="color:red;"></i></a>
													<?php endif ?>
											  	</td>
											  	<td>
												  	<?php if($key['validado_bg']!=1 || $key['validado_bc']!=1 || $key['validado_ld']!=1 || $key['validado_er']!=1 || $key['validado_lm']!=1): ?>
													   <a class="p_validar_r"  idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer">Validar reportes</a>
												   	<?php elseif($key['estado'] ==1): ?>
														<a class="p_estado_p" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer">Cerrar</a>
												   	<?php elseif($key['estado'] !=2 ): ?>
													   <a class="p_estado_p" idPeriodo="<?php echo $key['rowid']; ?>" style="cursor: pointer">Re abrir</a>
												   	<?php endif ?>
											 	</td>
									
											 	<?php if ($key['estado']==1 && $key['validado_bg']==1 && $key['validado_bc']==1 && $key['validado_ld']!==1 && $key['validado_er']==1){ 
											 	?>
					                          	<td colspan="4">&nbsp;</td>    
						                          	<?php }
						                          		elseif ($key['estado']==2){ 
						                          	?>
					                          	<td colspan="4">&nbsp;</td>
					                          		<?php }else{ ?>
													<td>
														<a target="_blank" href="creaxml.php?crearxml=create_catalogo_xml&xanio=<?=$key['anio']?>&xmes=<?=$key['m']?>" title="Catálogo"><i class="fa fa-file-excel-o"></i></a>
													</td>
							                        <td>
							                          	<a target="_blank" href="creaxml.php?crearxml=create_balanza_xml&xanio=<?=$key['anio']?>&xmes=<?=$key['m']?>&tipoenv=N" title="Balanza Normal"> <i class="fa fa-file-excel-o"></i></a>
							                        </td>
							                        <td>
							                          	<a target="_blank" href="creaxml.php?crearxml=create_balanza_xml&xanio=<?=$key['anio']?>&xmes=<?=$key['m']?>&tipoenv=C" title="Balanza Complementaria"> <i class="fa fa-file-excel-o"></i></a>
							                        </td>
							                        <td>
							                        	<a target="_blank" href="creaxml.php?crearxml=create_xml_polizas&xanio=<?=$key['anio']?>&xmes=<?=$key['m']?>" title="Pólizas"> <i class="fa fa-file-excel-o"></i></a> 
							                        </td>
					                          	<?php }?>
											</tr>
										<?php endforeach ?>
									  </tbody>
									</table>
									<?php else: ?>
										<h4>No hay periodos registrados</h4>
									<?php endif ?>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<button type="button" class="btn btn-warning" id="btn_AddPeriodo">Agregar periodo</button>
									</div>
									<div class="col-sm-9">
									    <label class="pull-right">Solo se puede tener un periodo abierto.</label>
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

<!-- Select2 -->
<script>
	$(document).ready(function () {
		$(".select2_single").select2({
			placeholder: "Seleccione una opción",
			allowClear: true
		});
		$(".select2_group").select2({});
		$(".select2_multiple").select2({
			placeholder: "Seleccione uno o más",
			allowClear: true
		});

		$("#btn_AddPeriodo").click(function(){
			$("#dv_addPeriodo").toggle('fast');
		});
		$("#frm_AltaPeriodo").submit(AltaPeriodo);
		$("#txt_anioPeriodo").change(function(){
		   $("#frm_anioPeriodo").submit();
		});

		$(".p_estado_r").click(CambiarEstado_Reporte);
		$(".p_validar_r").click(Validar_Reportes);
		$(".p_estado_p").click(Cambiar_Estado_Periodo);
	});

	function AltaPeriodo(){
		var $form=$(this);
		 if(confirm("¿Está seguro de agregar el periodo?: "+$('#txt_anio').val())){
			$.ajax({
				url: "../put/put_periodo.php",
				type: 'POST',
				data:$("#frm_AltaPeriodo").serialize(),
				dataType: 'json',
				success: function (data) {
					if( data.mensaje ) {
						alert(data.mensaje);
					}
					else {
						alert('Se ha agredado el periodo');
						location.reload();
					}
				}
			});
		}
			return false;
	}

	function CambiarEstado_Reporte(){
		var $liga = $(this);

			$.ajax({
				url: "../edit/edit_estado_reporte_periodo.php",
				type: 'POST',
				data:{"col": $liga.attr('columna'),"id":$liga.attr('idPeriodo')},
				dataType: 'json',
				success: function (data) {
					if( data.mensaje ) {
						alert(data.mensaje);
					}
					else {
						location.reload();
					}
				}
			});

	}

	function Validar_Reportes(){
		  var $liga = $(this);

			$.ajax({
				url: "../edit/edit_estado_reporte_periodo.php",
				type: 'POST',
				data:{"validar":$liga.attr('idPeriodo')},
				dataType: 'json',
				success: function (data) {
					if( data.mensaje ) {
						alert(data.mensaje);
					}
					else {
						location.reload();
					}
				}
			});
	}

	function Cambiar_Estado_Periodo(){
		 var $liga = $(this);

			$.ajax({
				url: "../edit/edit_estado_reporte_periodo.php",
				type: 'POST',
				data:{"estado":$liga.attr('idPeriodo')},
				dataType: 'json',
				success: function (data) {
					if( data.mensaje ) {
						alert(data.mensaje);
					}
					else {
						location.reload();
					}
				}
			});
	}
</script>
<!-- /Select2 -->

</body>
</html>
