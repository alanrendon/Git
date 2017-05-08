<?php 
if(!isset($_GET['id']) || empty($_GET['id']) ){
  header("Location: consulta.php");
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
require_once ("../class/poliza.class.php");
require_once ("../class/periodo.class.php");

$polizas     = new Poliza();
$periodo     = new Periodo();
$fechaUltimo =$periodo->get_ultimo_periodo_abierto();

if(!$fechaUltimo->anio){
    echo ("<SCRIPT LANGUAGE='JavaScript'>
     window.alert('No hay periodos abiertos, no puede editar Póliza');
    location.replace(document.referrer);
    </SCRIPT>");
}
$poliza_arr = $polizas->getPolizaId( (int)$_GET['id']);

$poliza_arr           = $poliza_arr[0];
$poliza_arr['fecha2'] = $poliza_arr['fecha'];
$poliza_arr['fecha']  = date("m/d/Y", strtotime($poliza_arr['fecha']));



// First day of the month.
 $inicioFecha = date($fechaUltimo->mes.'/01/'.$fechaUltimo->anio);

// Last day of the month.
$finFecha = date('t',strtotime($inicioFecha));
$finFecha = $fechaUltimo->mes.'/'.$finFecha.'/'.$fechaUltimo->anio;

?>


<!DOCTYPE html>
<html lang="es">
	 <!-- Select2 -->
	 <link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
	  <?php
		   include_once($url[0]."base/head.php");
	  ?>
		<!-- page content -->
		<div class="right_col" role="main">
		  <div class="">
			<div class="page-title">
			  <div class="title_left">
			   <?php if($poliza_arr['fecha2']=='0000-00-00'):?>
			       <h3>Pólizas Plantillas</h3>
			   <?php else: ?>
			        <h3>Pólizas</h3>
			   <?php endif ?>
				
			  </div>
			  <?php if ( isset($_GET['id']) && $_GET['id'] != '' ) {  ?>
				 <ul class="nav right panel_toolbox" >
                        <li>
                            <a class="btn btn-default buttons-csv buttons-html5 btn-sm back-edit" title="Nueva" tabindex="0">
                                <i class="fa fa-arrow-left"> </i> <span style="color:#7A8196 !important;">Regresar</span>
                            </a>
                        </li>			
                      </ul>
                <?php } ?>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
					  <div class="x_title">
						<h2>Edición</h2>
						<div class="clearfix"></div>
					  </div>
					  <div class="x_content">

						<br />
						  <form id="frm_EditarPoliza" data-parsley-validate class="form-horizontal form-label-left">
						      <input type="hidden" value="<?php echo $poliza_arr['id'] ?>" name='id'>
								<div class="form-group">
									<div class="col-md-6 col-sm-6 col-xs-12">
									  <label>Fecha de facturación <span class="required">*</span></label>
									  <input value='<?php echo $poliza_arr['fecha'] ?>' placeholder="Fecha" id="txt_fecha" name="txt_fecha" class="date-picker form-control col-md-7 col-xs-12" required="required" type="text">
									</div>
									 <div class="col-md-6 col-sm-6 col-xs-12">
									  <label >Tipo de Póliza <span class="required">*</span></label>
									  <select class="select2_single form-control" name="slc_tipoPoliza" id="slc_tipoPoliza" tabindex="-1">
										  <option value="I" <?= $poliza_arr['tipol_l']=='I' ?  'selected' : ''?> >01 - Ingreso</option>
										  <option value="E" <?= $poliza_arr['tipol_l']=='E' ?  'selected' : ''?> >02 - Egreso</option>
										  <option value="D" <?= $poliza_arr['tipol_l']=='D' ?  'selected' : ''?> >03 - Diario</option>
									  </select>
									</div> 
									<div class="col-md-6 col-sm-6 col-xs-12">
									  <label class="">Concepto <span class="required">*</span></label>
									  <input value='<?php echo  ($poliza_arr['concepto']) ?>' type="text" required="required" placeholder="Concepto" name="txt_concepto" class="form-control col-md-7 col-xs-12">
									</div>
									
									<div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Método de pago</label>
                                        <select class="select2_single form-control" name="met_payment" id="met_payment" tabindex="-1">
                                            <option value="4" <?= $poliza_arr['fk_paiement']=='4' ?  'selected' : ''?> >Efectivo</option>
                                            <option value="7" <?= $poliza_arr['fk_paiement']=='7' ?  'selected' : ''?> >Cheque</option>
                                            <option value="6" <?= $poliza_arr['fk_paiement']=='6' ?  'selected' : ''?> >Tarjeta</option>
                                            <option value="2" <?= $poliza_arr['fk_paiement']=='2' ?  'selected' : ''?> >Transferencia bancaria</option>
                                            <option value="3" <?= $poliza_arr['fk_paiement']=='3' ?  'selected' : ''?> >Domiciliación</option>
                                        </select> 
                                    </div>
									<div class="col-md-3 col-sm-3 col-xs-12 cheques" style="display:none">
									  <label>Cheque a nombre de</label>
									  <input  value='<?php echo $poliza_arr['anombrede'] ?>' id="txt_nombreCheque" name="txt_nombreCheque" class="form-control col-md-7 col-xs-12" type="text" placeholder="Nombre">
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12 cheques" style="display:none">
									  <label>Cheque número</label>
									  <input  value='<?php echo $poliza_arr['numcheque'] ?>' id="txt_noCheque" placeholder="Número" name="txt_noCheque" class="form-control col-md-7 col-xs-12" type="text">
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
									   <label for="message">Comentario</label>
									  <textarea class="form-control" rows="3" name="txt_comentario" placeholder="Comentario"><?php echo  ($poliza_arr['comentario']) ?> </textarea>
									</div>   
								</div>
								<div class="ln_solid"></div>
								<div class="form-group pull-right">
								  <div class="col-md-6 col-sm-6 col-xs-12 ">
									<!-- <button type="reset" class="btn btn-primary">Limpiar</button> -->
									<button type="submit" id="Guardar" class="btn btn-success">Guardar</button>
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
		<footer>
		  <div class="pull-right">
			Contab PRO 1.0 | Dolibarr ERP by <a href="http://www.auriboxconsulting.com/">Auribox Consulting</a>
		  </div>
		  <div class="clearfix"></div>
		</footer>
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
	<script src="<?php echo $url[0].'js/get_list_docto_relacionados.js'?>"></script>
	<script src="<?php echo $url[0].'js/put_poliza.js'?>"></script>
	
	<script>
	// Fecha
	$(document).ready(function() {
	$('#txt_fecha').daterangepicker({
		"singleDatePicker": true,
		"startDate": "<?php echo $inicioFecha; ?>",
		"endDate": "<?php echo $finFecha; ?>",
		"minDate": "<?php echo $inicioFecha; ?>",
		"maxDate": "<?php echo $finFecha; ?>",
		"format": 'MM/DD/YYYY',
		"calender_style": "picker_4",
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
	$("#met_payment").change(mostrarCheque);
	mostrarCheque();
	$("#frm_EditarPoliza").submit(Actualizar_Poliza)
	$(".back-edit").click(regresar);
});

function regresar() {
	location.replace('template.php');
}


// Select
$(document).ready(iniciaSelect);

$(document).ready(function() {
	$("input[name = 'txt_doctoRelacionado']").change(cambiarDoctRelacionado);
});

function cambiarDoctRelacionado(value) {
	var txt_doctoRelacionado = $('input[name="txt_doctoRelacionado"]:checked').val();
	var dibuja = "";
	if (txt_doctoRelacionado != '0') {
		listaDoctosRelacionados(txt_doctoRelacionado, "dv_dibujaRelacionado");
	} else {
		dibuja += '<label class="">Seleccione</label>';
		dibuja += '<select class="select2_single form-control" name="sls_Select" tabindex="-1" disabled>';
		dibuja += '</select>';
		$("#dv_dibujaRelacionado").html(dibuja);
		iniciaSelect();
	}
}

function Actualizar_Poliza() {
	$form = $(this);
	$.ajax({
			method: "POST",
			url: "../update/update_poliza.php",
			data: $form.serialize(),
			dataType: 'json'
		})
		.done(function(data) {
			if (data.agregado) {
				location.replace(document.referrer);
			} else {
				alert(data.msg);
			}

		});
	return false;
}
</script>

  </body>
</html>