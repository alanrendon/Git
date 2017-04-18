<?php

$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once ($url[0] . "class/cat_cuentas.class.php");
require_once ($url[0] . "class/cat_cuentas.class.php");
require_once ($url[0] . "class/commande_fournisseur.class.php");
require_once ($url[0]."class/cuentas_rel.class.php");
require_once "../class/tipo_poliza.class.php";
require_once ("../class/periodo.class.php");

$tipo_polizas    = new Tipo_Poliza();
$not_rel         = new Cuenta();
$rel             = new Rel_Cuenta();
$periodo         = new Periodo();
$pedido          = new Commande_Fournisseur();

$tipo_grupo_poliza = $tipo_polizas->get_tipo_poliza_con_ctas();
$not_rel_cuentas   = $not_rel->get_cuentas();
$rel_bancos        = $rel->get_cuenta_bancos();
$rel               = $rel->get_cuenta_iva();
$fechaUltimo       = $periodo->get_ultimo_periodo_abierto();
$inicioFecha       = date($fechaUltimo->mes . '/01/' . $fechaUltimo->anio);
$finFecha          = date('t',strtotime($inicioFecha));
$finFecha          = $fechaUltimo->mes.'/'.$finFecha.'/'.$fechaUltimo->anio;
$pedidos           = $pedido->get_data_order();

?>
<!DOCTYPE html>
<html lang="es">
    <!-- Select2 -->
    <link href="<?php echo $url[0] . '../vendors/select2/dist/css/select2.min.css' ?>" rel="stylesheet">

    <!-- Datatables -->
    <link href="<?php echo $url[0] . '../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0] . '../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0] . '../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0] . '../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0] . '../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css' ?>" rel="stylesheet">
    <?php
    include_once($url[0] . "base/head.php");
    ?>
    <input type="hidden" id="tmoneda" value="<?= $moneda ?>">
    <div class="right_col" role="main">
        <div class="x_title">
            <h2>Pedidos a proveedor</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" >
            <br />
            <div id="dv_tabla">
                <form id="frm_AsignarPoliza" data-parsley-validate class="form-horizontal form-label-left">
                    <div class="form-group pull-right">
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                            <button type="submit" class="btn btn-success btn-sm">Contabilizar</button>
                        </div>
                    </div>
                    <table class="table table-striped jambo_table bulk_action" id="datatable">
                        <thead>
	                        <th>Ref.</th>
	                        <th>Proveedor</th>
	                        <th>Forma de pago</th>
	                        <th>Base imposible</th>
	                        <th>Importe IVA</th>
	                        <th>Importe total</th>
	                        <th><i class="fa fa-calculator" ></i></th>
	                    </thead>
                        <tbody>
                        	<?php foreach ($pedidos as $key => $ped): ?>
                                <tr>
                                    <td><?php echo $ped->ref; ?></td>
                                    <td><?php echo $ped->ref_supplier.' '.$ped->nom; ?></td>
                                    <td><?php echo $ped->fk_mode_reglement; ?></td>
                                    <td><?php echo $moneda." ".number_format($ped->total_ht); ?></td>
                                    <td><?php echo $moneda." ".number_format($ped->tva); ?></td>
                                    <td><?php echo $moneda." ".number_format($ped->total_ttc); ?></td>
                                    <td>
                                        <input name='pedidoid[]' type='checkbox' value='<?php echo $ped->rowid; ?>'>
                                    </td>
                                </tr>
                        	<?php endforeach ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id="dv_datos" style="display: none;" >
                <div class="row">
                    <div class=" col-md-12 col-lg-12 ">
                        <button type="button" class="btn btn-success" id="btn_back">Volver</button>
                         <button type="button" class="btn btn-warning" id="btn_Factura">Ver información</button>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12" style="display: none;" id="dv_panelFactura">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Información </h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up" style="color: black"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="infoFactura"></div>
                        </div>
                    </div>
                    <div class="col-sm-12"  id="dv_panelPoliza">
                    <?php if ($fechaUltimo->anio): ?>
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Póliza Contable</h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <form id="frm_AltaPoliza" data-parsley-validate class="form-horizontal form-label-left">
                                        <input type="hidden" value="1" name="txt_doctoRelacionado">
                                        <div id="facturasid">
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label>Fecha de facturación <span class="required">*</span></label>
                                                <input placeholder="Fecha" id="txt_fecha" name="txt_fecha" class="date-picker form-control col-md-7 col-xs-12" required="required" type="text">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label >Tipo de Póliza <span class="required">*</span></label>
                                                <select class="select2_single form-control" name="slc_tipoPoliza"  id="slc_tipoPoliza">
                                                    <option value="I" selected="selected">01 - Ingreso</option>
                                                    <option value="E">02 - Egreso</option>
                                                    <option value="D">03 - Diario</option>
                                                </select>
                                            </div> 
                                            <div class="col-md-6 col-sm-6 col-xs-6" id="dv_concepto">
                                                <label class="">Concepto <span class="required">*</span></label>
                                                <input type="text" required="required" placeholder="Concepto" name="txt_concepto" id="txt_concepto" class="form-control col-md-7 col-xs-12">
                                            </div>
                                             <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label>Método de pago</label>
                                                <select class="select2_single form-control" name="met_payment" id="met_payment" tabindex="-1">
                                                    <option value="01">Efectivo</option>
                                                    <option value="02">Cheque</option>
                                                    <option value="03">Transferencia electrónica de fondos</option>
                                                    <option value="04">Tarjeta de crédito</option>
                                                    <option value="05">Monedero electrónico</option>
                                                    <option value="06">Dinero electrónico</option>
                                                    <option value="08">Vales de despensa</option>
                                                    <option value="28">Tarjeta de débito</option>
                                                    <option value="29">Tarjeta de servicio</option>
                                                    <option value="99">Otros</option>
                                                </select> 
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12 cheques" style="display: none;" >
                                                <label>Cheque a nombre de</label>
                                                <input   id="txt_nombreCheque" name="txt_nombreCheque" class="form-control col-md-7 col-xs-12" type="text" placeholder="Nombre">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12 cheques" style="display: none;" >
                                                <label>Cheque número</label>
                                                <input  id="txt_noCheque" placeholder="Número" name="txt_noCheque" class="form-control col-md-7 col-xs-12" type="text">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <label for="message">Comentario</label>
                                                <textarea class="form-control" rows="3" name="txt_comentario" placeholder="Comentario"></textarea>
                                            </div>   
                                        </div>
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-sm-12" id="dv_cuentas">
                                                <ul class="nav left panel_toolbox">
                                                    <li>
                                                      <a class="btn btn-default a_agregar" title="Agregar">
                                                            <i class="fa fa-plus fa-lg" style="color: blue"> </i> <span style="color:#7A8196 !important;">Agregar cuenta</span> 
                                                        </a>
                                                    </li>
                                                    <?php if(count($tipo_grupo_poliza)>0 ): ?>
                                                        <li>
                                                            <div class="col-md-12">
                                                               <select class="select2_single form-control" name="slc_grupo_ctas_registrados" id="slc_grupo_ctas_registrados" tabindex="-1">
                                                                  <option></option>
                                                                  <?php foreach($tipo_grupo_poliza as $t): ?>
                                                                       <option value="<?php echo $t->abr; ?>"><?php echo $t->nombre; ?></option>
                                                                   <?php endforeach ?>

                                                              </select>
                                                          </div>
                                                        </li>
                                                        <li>
                                                             <div class="col-md-1">
                                                                <button type="button" class="btn btn-warning btn-cargar">Cargar cuentas</button>
                                                              </div>
                                                        </li>
                                                    <?php endif ?>
                                                </ul>
                                            </div> 
                                            <div class="col-md-12 col-lg-12 col-sm-12" style="marging-top: 0px !important;">
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="marging-top: 0px !important;">
                                                    <div class="col-sm-3">
                                                        <label class="">Cuenta <span class="required">*</span></label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="">Descripción </label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="">Debe <span class="required">*</span></label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="">Haber <span class="required">*</span></label>
                                                    </div>

                                                </div>
                                                <div id="cuentas_precargadas">
								    
								                </div>
                                                <div id="dv_AsientosCuentaCliente">

                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                                    <div class="form-group">
                                                        <div class="col-sm-3">
                                                          <select class="select2_single form-control"  name='txt_cuenta[]'>
                                                                <option value=""></option>
                                                                    <?php foreach ($not_rel_cuentas as $key => $value): ?>
                                                                        <?php print ($not_rel->existe($key) <= 0) ? '<option value="' . $key . '" >' . $value . '</option>' : ''; ?>
                                                                    <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" placeholder="Descripión" name="descripcion[]"  class="form-control col-md-7 col-xs-12 txt_debe">
                                                        </div>
                                                        <div class="col-sm-2">													
                                                            <input type="text" name="txt_debe[]" class="txt_debe form-control col-md-7 col-xs-12"  value="0.0" id='total_ht'>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="text"  name="txt_haber[]"  class="txt_haber form-control col-md-7 col-xs-12" value="0.0">
                                                        </div>
                                                        <ul class="nav left panel_toolbox">
                                                             <li>
                                                                <a class="a_borrarAsiento" title="Borrar" idasiento="75">
                                                                    <i class="fa fa-trash" style="color: red"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                                    <div class="form-group">
                                                        <div class="col-sm-3">
                                                          <select class="select2_single form-control"  name='txt_cuenta[]'>
                                                                <option value=""></option>
                                                                    <?php foreach ($not_rel_cuentas as $key => $value): ?>
                                                                        <?php print ($not_rel->existe($key) <= 0) ? '<option value="' . $key . '" >' . $value . '</option>' : ''; ?>
                                                                    <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" placeholder="Descripión" name="descripcion[]"  class="form-control col-md-7 col-xs-12 txt_debe">
                                                        </div>
                                                        <div class="col-sm-2">													
                                                            <input type="text" name="txt_debe[]" class="txt_debe form-control col-md-7 col-xs-12"  value="0.0" id='total_tva'>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="text"  name="txt_haber[]"  class="txt_haber form-control col-md-7 col-xs-12" value="0.0">
                                                        </div>
                                                       
                                                            <ul class="nav left panel_toolbox">
                                                                <li>
                                                                    <a class="a_borrarAsiento" title="Borrar" idasiento="75">
                                                                        <i class="fa fa-trash" style="color: red"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                                    <div class="form-group">
                                                        <div class="col-sm-3">
                                                            <select class="select2_single form-control slc_cuenta" name='txt_cuenta[]'>
                                                                <option value=""></option>
                                                                <?php foreach ($not_rel_cuentas as $key => $value): ?>
                                                                        <?php print ($not_rel->existe($key) <= 0) ? '<option value="' . $key . '" >' . $value . '</option>' : ''; ?>
                                                                <?php endforeach ?>
                        
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" placeholder="Descripión" name="descripcion[]"  class="form-control col-md-7 col-xs-12 txt_debe">
                                                        </div>
                                                        <div class="col-sm-2" >
                                                            <input type="text" name="txt_debe[]" class="txt_debe form-control col-md-7 col-xs-12 " value="0.0">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="text"  name="txt_haber[]" class="txt_haber form-control col-md-7 col-xs-12" value="0.0" id='total_ttc'>
                                                        </div>
                                                       
                                                            <ul class="nav left panel_toolbox">
                                                                <li>
                                                                    <a class="a_borrarAsiento" title="Borrar" idasiento="75">
                                                                        <i class="fa fa-trash" style="color: red"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                       
                                                    </div>
                                                </div>	 

                                                <div id="dv_Asientos">

                                                </div>  
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                                    <div class="form-group">
                                                        <div class="col-sm-3">
                                                             <ul class="nav left panel_toolbox">
                                                                <li>
                                                                    <div id="mensaje">
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-4">

                                                        </div>
                                                        <div class="col-sm-2" id="txt_debeTotal">

                                                        </div>
                                                        <input id="txt_debeTotalI" type="hidden">
                                                        <div class="col-sm-2" id="txt_haberTotal">

                                                        </div>
                                                        <input id="txt_haberTotalI" type="hidden">
                                                       
                                                    </div>
                                                </div>	 
                                            </div>
                                        </div>
                                        <div class="ln_solid"></div>
                                        <div class="form-group pull-right">
                                            <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                <button type="submit" class="btn btn-success">Registrar póliza</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
<?php else: ?>
                            No puede registrar la póliza, no hay un periodo abierto.
<?php endif ?>
                    </div>
                </div>
            </div>
            <br />
        </div>
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


<script src="<?php echo $url[0] . '../vendors/jquery/dist/jquery.min.js' ?>"></script>
<!-- Bootstrap -->
<script src="<?php echo $url[0] . '../vendors/bootstrap/dist/js/bootstrap.min.js' ?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0] . '../vendors/fastclick/lib/fastclick.js' ?>"></script>

<script src="<?php echo $url[0] . '../vendors/iCheck/icheck.min.js' ?>"></script>


<!-- Select2 -->
<script src="<?php echo $url[0] . '../vendors/select2/dist/js/select2.full.min.js' ?>"></script>
<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0] . '../build/js/custom.min.js' ?>"></script>
<script src="<?php echo $url[0] . 'js/app_select.js' ?>"></script>

<!-- bootstrap-daterangepicker -->
<script src="<?php echo $url[0] . 'js/moment/moment.min.js' ?>"></script>
<script src="<?php echo $url[0] . 'js/datepicker/daterangepicker.js' ?>"></script>

<!-- Datatables -->
<script src="<?php echo $url[0] . '../vendors/datatables.net/js/jquery.dataTables.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-buttons/js/dataTables.buttons.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-buttons/js/buttons.flash.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-buttons/js/buttons.html5.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-buttons/js/buttons.print.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-responsive/js/dataTables.responsive.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/datatables.net-scroller/js/dataTables.scroller.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/jszip/dist/jszip.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/pdfmake/build/pdfmake.min.js' ?>"></script>
<script src="<?php echo $url[0] . '../vendors/pdfmake/build/vfs_fonts.js' ?>"></script>

<script type="text/javascript">
$("#btn_Factura").click(function(event) {
    $("#dv_panelFactura").toggle('fast');

    if ($("#dv_panelPoliza").attr("class") == 'col-sm-6') {
        $("#dv_panelPoliza").removeClass();
        $("#dv_panelPoliza").addClass('col-sm-12');
    } else {
        $("#dv_panelPoliza").removeClass();
        $("#dv_panelPoliza").addClass('col-sm-6');
    }
});
$('#datatable').DataTable({
    "aoColumnDefs": [{
        'bSortable': false,
        'aTargets': [3]
    }],
    "language": {
        "lengthMenu": "Mostrar _MENU_ resultados",
        "zeroRecords": "No se encontraron registros",
        "info": "Página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay datos para mostrar",
        "infoFiltered": "(filtrados de _MAX_ resultados totales)",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "search": "Buscar "
    }
});
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
    $("#btn_back").click(function(event) {
        $("#dv_tabla").toggle('fast');
        $("#dv_datos").toggle('fast');
        $("#dv_Asientos").html(" ");
        $("#facturasid").html(" ");
    });
    iniciaSelect();
    $(".a_agregar").click(AgregarAsiento);
    $(".txt_debe").change(inputSumatxt_debe);
    $(".txt_haber").change(inputSumatxt_haber);
    $(".btn-cargar").click(cargar_ctas);
    $("#met_payment").change(mostrarCheque);

});
$(document).ready(function() {
    $("#frm_AsignarPoliza").submit(getFacturaInfo);
    $("#frm_AltaPoliza").submit(AgregarPoliza);
});

function mostrarCheque() {
    var tipoPliza = $("#met_payment").val();
    if (tipoPliza == 7) {
        $(".cheques").show('fast');
    } else {
        $(".cheques").hide('fast');
    }
}
function getFacturaInfo() {
    $("#dv_tabla").toggle('fast');
    $("#dv_datos").toggle('fast');
    $.ajax({
        url: "../get/get_data_pedido.php",
        type: 'POST',
        data: $("#frm_AsignarPoliza").serialize(),
        dataType: 'html',
        success: function(data) {
            $("#infoFactura").html(data);
            $("#txt_concepto").val('Póliza ' + $("#td_facnumber").val());
            $("#txt_fecha").val($("#FechaFactura").val());
            $("input[name='descripcion[]']").val($("#txt_concepto").val());



            $('#total_ht').val($('#table_total_ht').val());
            $('#total_tva').val($('#table_total_tva').val());
            $('#total_ttc').val($('#table_total_ttc').val());
            var inps = document.getElementsByName('factidRecuperado[]');

            for (var i = 0; i < inps.length; i++) {
                var inp = inps[i];
                $("#facturasid").append('<input type="hidden" name="txt_idFactura[]" value="' + inp.value + '" id="txt_idFactura">');
            }

            inputSumatxt_debe();
            inputSumatxt_haber();
        }
    });
    return false;
}

function AgregarAsiento() {
    var concepto = $('#txt_concepto').val();
    if (concepto != '') {
        $.ajax({
            url: "../get/div_asientos.php",
            type: 'POST',
            data: {
                "concepto": concepto
            },
            dataType: 'html',
            success: function(data) {
                $("#dv_Asientos").append(data);
                $(".slc_cuenta").change(AgregarCuentaInput);
                iniciaSelect();
                $(".txt_debe").change(inputSumatxt_debe);
                $(".txt_haber").change(inputSumatxt_haber);
                $(".a_borrarAsiento").click(function() {
                    $padre = $(this).parent().parent().parent().parent();
                    $padre.remove();
                });

            }
        });
    }
    return false;
}

function AgregarCuentaInput() {
    $padre = $(this).parent().parent();
    $hijo = $padre.find("input").filter(":first");
    $hijo.val($(this).val());
    $("#txt_haber").val("0.0");
}

function inputSumatxt_debe() {
    $.ajax({
        url: "../get/get_suma_debe.php",
        type: 'POST',
        data: $("#frm_AltaPoliza").serialize(),
        dataType: 'html',
        success: function(data) {
            $("#txt_debeTotal").html($('#tmoneda').val() + " <label>" + data + "</label>");
            $("#txt_debeTotalI").val(data);
            comparar_totales();
        }
    });
    return false;
}

function inputSumatxt_haber() {
    $.ajax({
        url: "../get/get_suma_haber.php",
        type: 'POST',
        data: $("#frm_AltaPoliza").serialize(),
        dataType: 'html',
        success: function(data) {
            $("#txt_haberTotal").html($('#tmoneda').val() + " <label>" + data + "</label>");
            $("#txt_haberTotalI").val(data);
            comparar_totales();
        }
    });
    return false;
}

function comparar_totales() {
    $.ajax({
        url: "../get/get_total_debe_haber.php",
        type: 'POST',
        data: {
            "haber": $("#txt_haberTotalI").val(),
            "debe": $("#txt_debeTotalI").val()
        },
        dataType: 'html',
        success: function(data) {
            $("#mensaje").html(data);
        }
    });
}

function AgregarPoliza() {
    var $form = $(this);
    if (confirm("¿Está seguro de agregar la póliza?")) {
        $.ajax({
            url: "../put/put_poliza_asientos.php",
            type: 'POST',
            data: $("#frm_AltaPoliza").serialize() + '&tipoSociedad=3',
            dataType: 'json',
            success: function(data) {
                if (data.mensaje) {
                    alert(data.mensaje);
                } else {
                    $(location).attr("href", "consulta.php");
                }
            }
        });
    }
    return false;
}

function cargar_ctas() {
    var request = $.ajax({
        url: "../get/div_cuentas_poliza.php",
        method: "POST",
        data: {
            tipo: $("#slc_grupo_ctas_registrados").val(),
            'concepto': $("#txt_concepto").val()
        },
        dataType: "HTML",
        success: function(data) {
            $("#cuentas_precargadas").html(data);

            $(".txt_debe").change(inputSumatxt_debe);
            $(".txt_haber").change(inputSumatxt_haber);
            $(".a_borrarAsiento").click(function() {
                $padre = $(this).parent().parent().parent().parent();
                $padre.remove();
            });

        }
    });

}
</script>


</body>
</html>