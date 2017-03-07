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
require_once "../class/cuentas_poliza.class.php";
require_once "../class/cat_cuentas.class.php";

$cuenta       = new Cuenta();
$tipo_polizas = new Tipo_Poliza();
$cta          = new Cuentas_Poliza();
$tipo         = $tipo_polizas->get_tipo_poliza();
$ctas_registradas = $cuenta->get_cuentas();


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
					<h3>Agrupación cuentas </h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Tipos de Pólizas</h2>
							<div class="clearfix"></div>
						</div>
						<ul class="nav right panel_toolbox" >
							<li>
								<a href="#" data-toggle="tooltip" data-placement="top" title="Aquí puede asignar las cuentas a los tipos de póliza.">
									<i class="fa fa-question-circle fa-lg"> </i>
								</a>
							</li>
							<li>
								<a class="btn btn-default add-conf" href="conf_grupos_cuentas.php">
									<i class="fa fa-list"> </i> <span style="color:#7A8196 !important;">Configuración</span>
								</a>
							</li>
						</ul>
						<div class="x_content">
						    <div class="row">
						          <?php foreach($tipo as $t): ?>
                        <div class="col-sm-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2><i class="fa fa-bars"></i> <?php echo $t->nombre.'-'.$t->abr; ?></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                            <?php $ctas = $cta->get_cuentas_poliza($t->abr); ?>
                                              <div class='row' align='center'>
                                                  <h5>Cuentas asignadas:</h5><a class="delete-link" title="Borrar" id="<?php echo $t->id; ?>"><i class="fa fa-trash" style="color: black"></i></a>
                                              </div>
                                                <?php foreach($ctas as $value): ?>
                                                        <div class='row' >                                                        
                                                        <?php $row =  ($cta->get_cuentas_agrupacion($value->codagr));?>
                                                            <?php if(is_array($row) && count($row)>0):?>
                                                              <?php echo $row[0]; ?>
                                                            <?php endif?>
                                                        </div>
                                               <?php endforeach ?>
                                               <br>
                                               <div class="ln_solid"></div>
                                                <div class="row">
                                                    <form class="guardar">
                                                       <input type="hidden" value="<?php echo $t->abr; ?>" name="tipo">
                                                        <label>Agrear nuevas</label>
                                                            <select class="select2_multiple form-control" multiple="multiple" name="cuentas[]">
                                                                   <?php foreach($ctas_registradas as $key => $value): ?>
                                                                        <?php if(!$cta->existe($key,$t->abr)): ?>
                                                                                <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                                                                       <?php endif ?>
                                                                   <?php endforeach ?>
                                                             </select>
                                                            <label>&nbsp;</label><br />
                                                            <button class="btn btn-success pull-left" type="submit"> Guardar</button>
                                                    </form>
                                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
							       <?php endforeach ?>
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


<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>

<script type="text/javascript" src="<?php echo $url[0].'js/app_select.js'?>"></script>


<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>


<script type="text/javascript">


    $(document).ready(function(){
        iniciaSelect();
        $('.guardar').submit(guardar_ctas_polizas);

        $( document ).on( "click", ".delete-link", function() {
          var id = $(this).attr('id');          
          eliminar_cuentas(id);
        });

    });


    function guardar_ctas_polizas(){
        $form = $(this);
        if(confirm('¿Está seguro de guardar las cuentas?')){
           var request = $.ajax({
              url: "../put/put_cuentas_poliza.php",
              method: "POST",
              data: $form.serialize(),
              dataType: "JSON",
              success: function(data){
                 //  alert(data.mensaje);
                   location.reload();
               }
            });

        }
        return false;
    }

   

    function eliminar_cuentas(id){
       // alert(id);
        if(confirm('¿Está seguro de eliminar las cuentas relacionadas?')){
           var request = $.ajax({
              url: "../edit/eliminar_grupo_cuenta.php",
              method: "POST",
              data: {function:'Eliminar cuentas', id:id},
              dataType: "html",
              success: function(data){                   
                   location.reload();
               }
            });

        }
        return false;
    }
</script>

</body>
</html>
