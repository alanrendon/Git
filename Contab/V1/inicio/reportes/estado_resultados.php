<?php 
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

require_once "../class/balance_general.class.php";
require_once "../class/conf_apartados.class.php";
require_once ("../class/periodo.class.php");

$periodo_fecha      = new Periodo();
$apartados          = new Apartados();
$balance            = new Balance();

if(isset($_POST['periodo'])){
    $periodo = explode("-", $_POST['periodo']);

}else{
     $fechaUltimo =$periodo_fecha->get_ultimo_periodo_abierto();
    if(!$fechaUltimo->anio){
        $periodo[0] = date('m/01/Y');
        $periodo[1] = date('m/t/Y');
    }else{
        $periodo[0] = date($fechaUltimo->mes.'/01/'.$fechaUltimo->anio);
        $finFecha = date('t',strtotime($periodo[0]));
        $periodo[1] = $fechaUltimo->mes.'/'.$finFecha.'/'.$fechaUltimo->anio;

    }
}


$estado = 2;

$apartados_ventas = $apartados->get_apartados_obj_ventas($estado);
$apartados_costos = $apartados->get_apartados_obj_costo_ventas($estado);
$apartados_gastos = $apartados->get_apartados_obj_gastos($estado);


$totalVentas=0;
$totalGastos=0;
$totalCostos=0;

$cont=0;


?>
<!DOCTYPE html>
<html lang="es">
	<!-- Select2 -->
	<link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
	<style>
        a.modal_a{
            color: #337ab7 !important;
            text-decoration: underline !important;
            cursor:help !important;
        }
    </style>
	<?php
	include_once($url[0]."base/head.php");
	?>
	<input type="hidden" id="tmoneda" value="<?=$moneda?>">
	<!-- page content -->
	<div class="right_col" role="main">
		<div class="">
			<div class="page-title">
				<div class="title_left">
					<h3>Estado de Resultados</h3>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
				            <h2>Correspondiente  <?php print $periodo[0]; ?> a <?php echo $periodo[1];?></h2>
							<div class="clearfix"></div>
						</div>
						  <div class="row">
                            <div class="col-md-4">
                                <form method="post" id="frm_periodo" action="#">
                                  <fieldset>
                                      <div class="control-group">
                                        <div class="controls">
                                          <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input type="text" style="width: 200px" name="periodo" id="periodo" class="form-control" value="<?php print $periodo[0]; ?> - <?php echo $periodo[1];?>" />
                                          </div>
                                        </div>
                                      </div>
                                    </fieldset>
				                </form>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <!-- <a href="../get/get_estado_resultados_excel.php?inicio=<?php// echo $periodo[0].'&fin='.$periodo[1]; ?>" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" target="_blank">
                                <span>Descargar XLS</span>
                                </a> -->
                                
                                <div class="x_title">
                                   
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>
                                   
                                            <li><a class="download-link" title="XLS" href="../get/get_estado_resultados_excel.php?inicio=<?php echo $periodo[0].'&fin='.$periodo[1]; ?>" target="_blank"> <i class="fa fa-file-excel-o " style="color: black"></i></a></li>
                                           <!--  <li><a class="download-link" title="PDF" target="_blank"  href="../get/anexos_catalogo_pdf.php?inicio=<?//=$periodo[0].'&fin='.$periodo[1]; ?>"> <i class="fa fa-file-pdf-o " style="color: black"></i></a></li> -->
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>

                            </div>
                        </div>
						<div class="x_content">
						
						<div class="row">
								<div class="col-md-4 col-sm-12 col-xs-12">
								  <?php foreach($apartados_ventas as $ventas): ?>
                                        <?php $cont++; ?>
                                         <?php $datos= $balance->get_balance($ventas->rowid,$estado); ?>
                                           <?php if(count($datos)>0): ?>
                                             <div class="x_panel">
                                                <div class="x_title">
                                                    <h2><i class="fa fa-bars"></i> <?php echo $ventas->apartado; ?></h2>
                                                    <ul class="nav navbar-right panel_toolbox">
                                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                    </ul>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                                        <table width="100%">
                                                            <?php $total=0;?>
                                                            <?php foreach($datos as $value): ?>
                                                                <tr>
                                                                    <td>
                                                                        <a class="modal_a" nmgrupo="<?php echo $value['grupo'] ?> " periodo0="<?php echo$periodo[0]?>" periodo1="<?php echo $periodo[1] ?>" inicio="<?php echo$value["fk_codagr_ini"]?>" fin="<?php echo$value["fk_codagr_fin"] ?>">
                                                                            <?php echo $value['grupo']?>
                                                                        </a>
                                                                    </td>
                                                                    <?php $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo); ?>
                                                                    <td style="text-align: right !important;"> <?php echo $moneda.' '.number_format($suma,2); ?></td>
                                                                    <td style="text-align: right !important;"></td>
                                                                </tr>
                                                           <?php endforeach ?>
                                                            <tr>
                                                                <td colspan="3" style="border-bottom:1px solid #000"></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                 <td style="text-align: right !important;"><?php echo $moneda.' '.number_format($total,2); $totalVentas+=$total; ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                           <?php endif?>
                                          
							       <?php endforeach ?>
								</div>
								<div class="col-md-4 col-sm-12 col-xs-12">
							       <?php foreach($apartados_costos as $costos): ?>
                                        <?php $cont++;?>
                                         <?php $datos= $balance->get_balance($costos->rowid,$estado); ?>
                                           <?php if(count($datos)>0): ?>
                                             <div class="x_panel">
                                                <div class="x_title">
                                                    <h2><i class="fa fa-bars"></i> <?php echo $costos->apartado; ?></h2>
                                                    <ul class="nav navbar-right panel_toolbox">
                                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                    </ul>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                                        <table width="100%">
                                                            <?php $total=0;?>
                                                            <?php foreach($datos as $value): ?>
                                                                <tr>
                                                                    <td>
                                                                        <a class="modal_a" nmgrupo="<?php echo $value['grupo'] ?> " periodo0="<?php echo$periodo[0]?>" periodo1="<?php echo $periodo[1] ?>" inicio="<?php echo$value["fk_codagr_ini"]?>" fin="<?php echo$value["fk_codagr_fin"] ?>">
                                                                            <?php echo $value['grupo']?>
                                                                        </a>
                                                                    </td>
                                                                    <?php $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo); ?>
                                                                    <td style="text-align: right !important;"> <?php echo $moneda.' '.number_format($suma,2); ?></td>
                                                                    <td style="text-align: right !important;"></td>
                                                                </tr>
                                                           <?php endforeach ?>
                                                            <tr>
                                                                <td colspan="3" style="border-bottom:1px solid #000"></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                 <td style="text-align: right !important;"><?php echo $moneda.' '.number_format($total,2); $totalCostos+=$total; ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                           <?php endif?>
                                          
                                    <?php endforeach ?>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12">
							       <?php foreach($apartados_gastos as $gastos): ?>
                                        <?php $cont++;?>
                                         <?php $datos= $balance->get_balance($gastos->rowid,$estado); ?>
                                           <?php if(count($datos)>0): ?>
                                             <div class="x_panel">
                                                <div class="x_title">
                                                    <h2><i class="fa fa-bars"></i> <?php echo $gastos->apartado; ?></h2>
                                                    <ul class="nav navbar-right panel_toolbox">
                                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                    </ul>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                                        <table width="100%">
                                                            <?php $total=0;?>
                                                            <?php foreach($datos as $value): ?>
                                                                <tr>
                                                                    <td>
                                                                        <a class="modal_a" nmgrupo="<?php echo $value['grupo'] ?> " periodo0="<?php echo$periodo[0]?>" periodo1="<?php echo $periodo[1] ?>" inicio="<?php echo$value["fk_codagr_ini"]?>" fin="<?php echo$value["fk_codagr_fin"] ?>">
                                                                            <?php echo $value['grupo']?>
                                                                        </a>
                                                                    </td>
                                                                    <?php $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo); ?>
                                                                    <td style="text-align: right !important;"> <?php echo $moneda.' '.number_format($suma,2); ?></td>
                                                                    <td style="text-align: right !important;"></td>
                                                                </tr>
                                                           <?php endforeach ?>
                                                            <tr>
                                                                <td colspan="3" style="border-bottom:1px solid #000"></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                 <td style="text-align: right !important;"><?php echo $moneda.' '.number_format($total,2); $totalGastos+=$total; ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                           <?php endif?>
                                          
                                    <?php endforeach ?>
                                </div>
							</div>
							<div class="row">
							    <div class="col-md-12 col-sm-12 col-xs-12">
											<div class="x_panel">
												<div class="x_title">
													<table width="100%">
														<tr>
															<td><strong>Total Ventas</strong></td>
															<td></td>
															<td style="text-align: right !important;"><?php echo $moneda.' '.number_format($totalVentas,2); ?></td>
														</tr>
														<tr>
															<td><strong>Total Costos</strong></td>
															<td></td>
															<td style="text-align: right !important;"><?php echo $moneda.' '.number_format($totalCostos,2); ?></td>
														</tr>
														<tr>
															<td><strong>Total Gastos</strong></td>
															<td></td>
															<td style="text-align: right !important;"><?php echo $moneda.' '.number_format($totalGastos,2); ?></td>
														</tr>
														<tr>
															<td><strong>Utilidad Bruta</strong></td>
															<td></td>
															<td style="text-align: right !important;"><?php echo $moneda.' '.number_format($totalVentas-$totalCostos,2); ?></td>
														</tr>
														<tr>
															<td><strong>Utilidad Operativa</strong></td>
															<td></td>
															<td style="text-align: right !important;"><?php echo $moneda.' '.number_format(($totalVentas-$totalCostos)-$totalGastos,2); ?></td>
														</tr>
													</table>
												</div>
											</div>
										</div>
							</div>
							
							<div id="add_modalAsiento" class="modal fade" role="dialog">
								<div class="modal-dialog" style="width: 60% !important;">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Cuentas del grupo: <div id="nombre_grupo"></div></h4>
										</div>
										<form id="frm_putAsiento" accept-charset="utf-8" data-parsley-validate class="form-horizontal form-label-left">
											<div class="modal-body">
												<div class="row">
													<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
														<table class="table table-striped">
														    <thead>
                                                              <tr>
                                                                <th>No. Cuenta</th>
                                                                <th>Descripción</th>
                                                                <th>Naturalreza</th>
                                                                <th>Total</th>
                                                                <th>Polizas</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody id="contenido">
                                                              
                                                            </tbody>
														</table>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-green" data-dismiss="modal">Cerrar</button>
											</div>
										</form>
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
<script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
<script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>
<!-- Select2 -->
<script>
   $(document).ready(function() {
         $('#periodo').daterangepicker(
              {
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                "format": 'MM/DD/YYYY',
                showDropdowns: true,
                locale: {
                        applyLabel: 'Aplicar',
                        cancelLabel: 'Limpiar',
                        fromLabel: 'de',
                        toLabel: 'a',
                        daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        firstDay: 1
              }
            },function(ev, picke){
                 $("#frm_periodo").submit(); 
            }                             
         );
      });
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
         $(".modal_a").click(VerCuentasGrupo);
	});
    function VerCuentasGrupo(){
        $liga= $(this);
        $.ajax({
            url:"../get/get_descripcion_grupos.php", 
            type:"POST",
            dataType: 'json',
            data:{"inicio":$liga.attr('inicio') , "fin":$liga.attr('fin'),"periodo0":$liga.attr('periodo0'),"periodo1":$liga.attr('periodo1') },
            success: function(data){
                 var dibuja = '';
                $.each(data, function(key, value ) {
                    if(value['indentado']){
                         dibuja+="<tr>";
                          dibuja+="<td ><p  style='text-indent: 50px;'>"+value['codagr']+"</p></td>";
                          dibuja+="<td>"+value['descripcion']+"</td>";
                          dibuja+="<td>"+value['naturaleza']+"</td>";
                          dibuja+="<td>"+$('#tmoneda').val()+" "+value['sum']+"</td>";
                          dibuja+="<td><a target='_blank' href='polizas.php?cta="+value['codagr']+"&fc1="+$liga.attr('periodo0')+"&fc2="+$liga.attr('periodo1')+"' >Ver</a></td>";
                        dibuja+="</tr>";
                    }else{
                          dibuja+="<tr>";
                          dibuja+="<td>"+value['codagr']+"</td>";
                          dibuja+="<td>"+value['descripcion']+"</td>";
                          dibuja+="<td>"+value['naturaleza']+"</td>";
                          dibuja+="<td>"+$('#tmoneda').val()+" "+value['sum']+"</td>";
                          dibuja+="<td><a target='_blank' href='ver_cuenta.php?cta="+value['codagr']+"&periodo="+$liga.attr('periodo0')+"-"+$liga.attr('periodo1')+"' >Ver</a></td>";
                        dibuja+="</tr>";
                    }
                    
                });
                $("#contenido").html(dibuja);
            }
        });
        $("#nombre_grupo").html($liga.attr('nmgrupo'));
        $('#add_modalAsiento').modal();
    }
</script>
<!-- /Select2 -->

</body>
</html>