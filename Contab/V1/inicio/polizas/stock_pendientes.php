<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$url[0] = "../";
require_once ($url[0]."class/stock.class.php");
require_once ($url[0]."class/cat_cuentas.class.php");
require_once ($url[0]."class/cuentas_rel.class.php");
require_once "../class/tipo_poliza.class.php";
require_once ("../class/periodo.class.php");


$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/method_payment_sat.class.php";
$met_payment  =new Payment_Sat();
$met_payment  = $met_payment->get_list_payment();

$movstock       = new Movstock();
$not_rel        = new Cuenta();
$rel            = new Rel_Cuenta();
$periodo        = new Periodo();
$tipo_polizas   = new Tipo_Poliza();

$movs              =$movstock->get_movimientos_stock();


$tipo_grupo_poliza  = $tipo_polizas->get_tipo_poliza_con_ctas();
$not_rel_cuentas    = $not_rel->get_cuentas();
$rel_bancos         = $rel->get_cuenta_bancos();
$rel                = $rel->get_cuenta_iva();

$fechaUltimo        = $periodo->get_ultimo_periodo_abierto();

// First day of the month.
$inicioFecha        = date($fechaUltimo->mes . '/01/' . $fechaUltimo->anio);

// Last day of the month.
$finFecha = date('t',strtotime($inicioFecha));
$finFecha = $fechaUltimo->mes.'/'.$finFecha.'/'.$fechaUltimo->anio;

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
    <input type="hidden" id="tmoneda" value="<?=$moneda?>">
    <div class="right_col" role="main">
        <div class="x_title">
            <h2>Movimientos de stock pendientes de contabilizarr</h2>
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
                    <table class="table table-striped" id="datatable">
                        <thead>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <!-- <th>Codigo Inv./Mov.</th> -->
                            <th>Etiqueta del movimiento</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                            <th>Total</th>
                            <th><i class="fa fa-calculator" aria-hidden="true"></i></th>
                        </thead>
                        <tbody>
                           <?php 
                           //print "<pre>";print_r($movs);print "</pre>";
                           foreach ($movs as $tr){
                            ?>
                            <tr>
                                <td><?=$tr['fechamov']?></td>
                                <td><?=$tr['ref']." - ".$tr['label']?></td>
                                <td><?=$tr['almacen']?></td>
                                <!--<td><?=($tr['inventorycode'])?></td>-->
                                <td><?=($tr['etiqmov'])?></td>
                                <td align="right"><?=$tr['unidades']?></td>
                                <td align="right"><?=$moneda."".number_format($tr['price'],2)?></td>
                                <td align="right"><?=$moneda."".number_format($tr['price']*$tr['unidades'],2)?></td>
                                <td><input name='transid[]' type='checkbox' value='<?php echo $tr['idmov']?>'></td>
                            </tr>
                            <?php 
                           }
                           ?>
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
                                                     <?php foreach ($met_payment as $key => $value): ?>
                                                        <option value="<?php echo $value->key_sat ?>"> <?php echo $value->name  ?></option>
                                                    <?php endforeach ?>
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
                                                <div id="predeterminados">
                                                
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
<script src="<?php echo $url[0].'js/app_select.js'?>"></script>

     <!-- bootstrap-daterangepicker -->
    <script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
    <script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>

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

    function getFacturaInfo() {
        $("#dv_tabla").toggle('fast');
        $("#dv_datos").toggle('fast');
        $.ajax({
            url: "../get/get_stock.php",
            type: 'POST',
            data: $("#frm_AsignarPoliza").serialize(),
            dataType: 'html',
            success: function(data) {
                $("#infoFactura").html(data);
                $("#txt_concepto").val('Póliza de egreso para  ' + $("#td_facnumber").val());
                $("#txt_fecha").val($("#FechaFactura").val());
                $("input[name='descripcion[]']").val($("#txt_concepto").val());

                var cuenta_alm_stock = $('#cuenta_alm_stock').val();

                $("#cuenta_stock option[value='']").prop('selected', true);
                $("#cuenta_stock option[value='" + cuenta_alm_stock + "']").prop('selected', true);

                var inps = document.getElementsByName('factidRecuperado[]');
                var inp ;
               
                for (var i = 0; i < inps.length; i++) {
                    inp=inps[i];
                    $("#facturasid").append('<input type="hidden" name="txt_idFactura[]" value="' + inp.value + '" id="txt_idFactura">');
                }
                iniciaSelect();
                f_get_partidas_facture(inp.value);
            }
        });
        return false;
    }

    function f_get_partidas_facture(facturasid){

            $.ajax({
                url: "../get/get_asientos_stock.php",
                type: 'POST',
                data: {idMovStock: facturasid},
                dataType: 'html',
                success: function(data) {
                    $('#predeterminados').html(data);

                    $("#txt_fecha").val($("#FechaFactura").val());
                    $(".slc_cuenta").change(AgregarCuentaInput);


                    //PRS
                    if ($("#prs_debe").length ) {
                        $("#prs_debe").val($("#total").val());
                    }
                    

                    //PSS

                    if ($("#pss_haber").length ) {
                        $("#pss_haber").val($("#total").val());
                    }

        
                    

                    //PSS

                    if ($("#pss2_haber").length ) {
                        $("#pss2_haber").val($("#total").val());
                    }


                    $(".txt_debe").change(inputSumatxt_debe);
                    $(".txt_haber").change(inputSumatxt_haber);
                    $("input[name='descripcion[]']").val($("#txt_concepto").val());

                    $(".a_borrarAsiento").click(function() {
                        $padre = $(this).parent().parent().parent().parent();
                        $padre.remove();
                    });

                    iniciaSelect();
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
                data: $("#frm_AltaPoliza").serialize() + '&tipoSociedad=4',
                dataType: 'json',
                success: function(data) {
                    if (data.mensaje) {
                        alert(data.mensaje);
                    } else {
                        $(location).attr("href", "consulta.php" + "?id=" + data.agregado);
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
    
    function mostrarCheque() {
        var tipoPliza = $("#met_payment").val();
        if (tipoPliza == 7) {
            $(".cheques").show('fast');
        } else {
            $(".cheques").hide('fast');
        }
    }

</script>


</body>
</html>