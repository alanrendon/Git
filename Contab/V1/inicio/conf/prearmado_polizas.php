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
$ctas_registradas = $cuenta->get_cuentas();


?>
<!DOCTYPE html>

<html lang="es">
	<!-- Select2 -->
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
	<link href="<?php echo $url[0].'css/pricing.css'?>" rel="stylesheet">

	<?php
	include_once($url[0]."base/head.php");
	?>
	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Condiciones de pago</h3>
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
								<a href="../get/files_helps/conf_armado_polizas.pdf" data-toggle="tooltip" data-placement="top" title="Descargar manual de armado." target="_blank">
									<i class="fa fa-download fa-lg"> </i>
								</a>
							</li>
							<li>
								<a href="#" data-toggle="tooltip" data-placement="top" title="Debe configurar las cuentas de estos grupos para poder utilizar el prearmado de pólizas.">
									<i class="fa fa-question-circle fa-lg"> </i>
								</a>
							</li>
							<li>
								<a href="condiciones_pago.php" class="btn btn-default btn-sm add-conf" title="Configuración" tabindex="0" aria-controls="datatable-buttons">
									<i class="fa fa-list"> </i> <span style="color:#7A8196 !important;">Condiciones de pago</span>
								</a>
							</li>
						</ul>
						<div class="x_content">
						    <div class="row">
						    	<?php $tipo = $tipo_polizas->get_tipo_poliza_prearmado_clientes();  ?>
						    		<div class="col-md-4 col-sm-12">
								         <?php foreach($tipo as $t): ?>
								          		 <div class=" col-sm-12 col-xs-12 float-shadow">        
								                    <div class="price_table_container">
								                        <div class="price_table_heading primary-bg"> <?php echo $t->nombre ?></div>
								                        <div class="price_table_body">
								                        	<div class="price_table_row" style="text-align: center;">
								                        		<a class="delete-link title="Borrar" id="<?php echo $t->id; ?>"><i class="fa fa-trash" style="color: black"></i></a>
								                        	</div>
								                         	<?php $ctas = $cta->get_cuentas_poliza($t->abr); ?>
								           					<?php foreach($ctas as $value): ?>                                  
		                                                        	<?php $row =  ($cta->get_cuentas_agrupacion($value->codagr));?>
		                                                            <?php if(is_array($row) && count($row)>0):?>
		                                                            	 	<div class="price_table_row">        
		                                                              			<?php echo $row[0]; ?>
		                                                              		</div>
		                                                            <?php endif?>
		                                               		<?php endforeach ?>                                     
								                       
								                        	
								                        	<div class="price_table_row">       
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
			                                                            <button class="btn btn-success pull-right" type="submit"> Guardar</button>
			                                                    </form>
								                        	</div>
								                         </div>
								                    </div>
								                </div>
									    <?php endforeach ?>
							       </div>
							       <?php $tipo = $tipo_polizas->get_tipo_poliza_prearmado_proveedores();  ?>
							       <div class="col-md-4 col-sm-12">
								         <?php foreach($tipo as $t): ?>
								          		 <div class=" col-sm-12 float-shadow">        
								                    <div class="price_table_container">
								                        <div class="price_table_heading info-bg"> <?php echo $t->nombre ?></div>
								                        <div class="price_table_body">
								                        	<div class="price_table_row" style="text-align: center;">
								                        		<a class="delete-link title="Borrar" id="<?php echo $t->id; ?>"><i class="fa fa-trash" style="color: black"></i></a>
								                        	</div>
								                         	<?php $ctas = $cta->get_cuentas_poliza($t->abr); ?>
								           					<?php foreach($ctas as $value): ?>                                  
		                                                        	<?php $row =  ($cta->get_cuentas_agrupacion($value->codagr));?>
		                                                            <?php if(is_array($row) && count($row)>0):?>
		                                                            	 	<div class="price_table_row">        
		                                                              			<?php echo $row[0]; ?>
		                                                              		</div>
		                                                            <?php endif?>
		                                               			<?php endforeach ?>                                     
								                       
									                        <div class="price_table_row">       
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
			                                                            <button class="btn btn-success pull-right" type="submit"> Guardar</button>
			                                                    </form>
									                        </div>
								                         </div>
								                    </div>
								                </div>
									    <?php endforeach ?>
									</div>
									<?php $tipo = $tipo_polizas->get_tipo_poliza_prearmado_stock();  ?>
							       <div class="col-md-4 col-sm-12">
								         <?php foreach($tipo as $t): ?>
								          		 <div class=" col-sm-12 float-shadow">        
								                    <div class="price_table_container">
								                        <div class="price_table_heading primary-bg"> <?php echo $t->nombre ?></div>
								                        <div class="price_table_body">
								                        	<div class="price_table_row" style="text-align: center;">
								                        		<a class="delete-link title="Borrar" id="<?php echo $t->id; ?>"><i class="fa fa-trash" style="color: black"></i></a>
								                        	</div>
								                         	<?php $ctas = $cta->get_cuentas_poliza($t->abr); ?>
								           					<?php foreach($ctas as $value): ?>                                  
		                                                        	<?php $row =  ($cta->get_cuentas_agrupacion($value->codagr));?>
		                                                            <?php if(is_array($row) && count($row)>0):?>
		                                                            	 	<div class="price_table_row">        
		                                                              			<?php echo $row[0]; ?>
		                                                              		</div>
		                                                            <?php endif?>
		                                              		 <?php endforeach ?>                                     
								                       
									                        <div class="price_table_row">       
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
			                                                            <button class="btn btn-success pull-right" type="submit"> Guardar</button>
			                                                    </form>
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
