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

$polizas        = new Poliza();
$periodo       = new Periodo();


$fechaUltimo =$periodo->get_ultimo_periodo_abierto();
if(!$fechaUltimo->anio){
    echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('No hay periodos abiertos, no puede crear Póliza');
    location.replace(document.referrer);
    </SCRIPT>");
}

$poliza_arr = $polizas->getPolizaId( (int)$_GET['id']);

$poliza_arr= $poliza_arr[0];
$poliza_arr['fecha'] = date("d/m/Y", strtotime($poliza_arr['fecha']));

$fechaUltimo =$periodo->get_ultimo_periodo_abierto();

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
				<h3>Pólizas</h3>
			  </div>
			  	<ul class="nav right panel_toolbox" >
	                <li>
	                    <a class="btn btn-default  btn-sm add-conf" title="Nueva" tabindex="0" href="template.php">
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
						<h2>Crear póliza</h2>
						<div class="clearfix"></div>
					  </div>
					  
					  <div class="x_content">
						<br />
						  <form id="frm_EditarPoliza" data-parsley-validate class="form-horizontal form-label-left">
						      <input type="hidden" value="<?php echo $poliza_arr['id'] ?>" name='id'>
								<div class="form-group">
									<div class="col-md-6 col-sm-6 col-xs-12">
									  <label>Fecha de facturación <span class="required">*</span></label>
									  <input placeholder="Fecha" id="txt_fecha" name="txt_fecha" class="date-picker form-control col-md-7 col-xs-12" required="required" type="text">
									</div>
								</div>
								<div class="ln_solid"></div>
								<div class="form-group pull-right">
								  <div class="col-md-6 col-sm-6 col-xs-12 ">
									<!-- <button type="reset" class="btn btn-primary">Limpiar</button> -->
									<button type="submit" id="Guardar" class="btn btn-success">Crear</button>
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

	
	<script>
	// Fecha
	$(document).ready(function() {
		$('#txt_fecha').daterangepicker({
			   "singleDatePicker": true,
                "startDate": "<?php echo $inicioFecha; ?>",
                "endDate":  "<?php echo $finFecha; ?>",
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
        $('#frm_EditarPoliza').submit(Actualizar_Poliza);
    });
    function Actualizar_Poliza(){
        $form = $(this);
        $.ajax({
          method: "POST",
          url: "../put/crear_poliza_cascaron.php",
          data: $form.serialize(),
          dataType:'json'
        })
        .done(function( data ) {
            if(data.agregado){
                alert('Se ha creado la póliza' );
            }else{
                 alert( data.mensaje );
            }
                
          });
    return false;
    }

	</script>

  </body>
</html>