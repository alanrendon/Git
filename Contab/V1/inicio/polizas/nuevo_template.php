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
require_once "../class/method_payment_sat.class.php";
$met_payment  =new Payment_Sat();
$met_payment  = $met_payment->get_list_payment();

$tipo_polizas = new Tipo_Poliza();
$tipo         = $tipo_polizas->get_tipo_poliza_con_ctas();

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
        <input type="hidden" id="tmoneda" value="<?=$moneda?>">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Pólizas Plantillas</h3>
                </div>
                <ul class="nav right panel_toolbox" >
                    <li>
                        <a class="btn btn-default buttons-csv buttons-html5 btn-sm add-conf" title="Nueva" tabindex="0" href="template.php">
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
                            <form id="frm_AltaPoliza" data-parsley-validate class="form-horizontal form-label-left">
                                <div class="form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label><span class="required">*</span> Tipo de Póliza</label>
                                        <select class="select2_single form-control" name="slc_tipoPoliza" id="slc_tipoPoliza" tabindex="-1">
                                            <option value="I">01 - Ingreso</option>
                                            <option value="E">02 - Egreso</option>
                                            <option value="D">03 - Diario</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label><span class="required">*</span>Concepto</label>
                                        <input type="text" required="required" placeholder="Concepto" name="txt_concepto" id="txt_concepto" class="form-control col-md-7 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label>Método de pago</label>
                                    <select class="select2_single form-control" name="met_payment" id="met_payment" tabindex="-1">
                                         <?php foreach ($met_payment as $key => $value): ?>
                                              <option value="<?php echo $value->key_sat ?>"> <?php echo $value->name  ?></option>
                                          <?php endforeach ?>
                                    </select> 
                                </div>              
                                <div class="form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-12 cheques" style="display:none">
                                        <label>Cheque a nombre de</label>
                                        <input id="txt_nombreCheque" name="txt_nombreCheque" class="form-control col-md-7 col-xs-12" type="text" placeholder="Nombre">
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 cheques" style="display:none">
                                        <label>Cheque número</label>
                                        <input id="txt_noCheque" placeholder="Número" name="txt_noCheque" class="form-control col-md-7 col-xs-12" type="text">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label for="message">Comentario</label>
                                        <textarea class="form-control" rows="3" name="txt_comentario" placeholder="Comentario"></textarea>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="row">
                                    <div class="form-group">
                                        <?php if (is_array($tipo) && count($tipo)>0):?>
                                        <div class="col-md-6">
                                            <div class="col-md-9">
                                                <select class="select2_single form-control" name="slc_grupo_ctas_registrados" id="slc_grupo_ctas_registrados" tabindex="-1">
                                                    <option></option>
                                                    <?php foreach($tipo as $t): ?>
                                                    <option value="<?php echo $t->abr; ?>"><?php echo $t->nombre; ?></option>
                                                    <?php endforeach ?>

                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-warning btn-cargar">Cargar cuentas</button>
                                            </div>
                                        </div>
                                        <?php endif?>
                                        <div class="col-md-6">
                                            <div class="col-md-9">
                                                <a class="btn btn-default a_agregar" title="Agregar">
                                                    <span style="color:#7A8196 !important;">Agregar cuenta</span> 
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="marging-top: 0px !important;display:none;" id="cabecera_ctas" >
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
                                </div>
                                <div class="row" id="cuentas_precargadas">

                                </div>
                                <div  class="row" id="dv_Asientos">             
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                        <div class="form-group">
                                            <br />
                                            <div class="col-sm-3">
                                                <ul class="nav left panel_toolbox">
                                                    <li>
                                                        <div class="mensaje">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-sm-4">
                                            </div>
                                            <div class="col-sm-2 txt_debeTotal">
                                            </div>
                                            <input class="txt_debeTotalI" type="hidden">
                                            <div class="col-sm-2 txt_haberTotal">
                                            </div>
                                            <input class="txt_haberTotalI" type="hidden">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group pull-right">
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <button type="submit"  class="btn btn-success a_guardar_ctas" style="display: none;">Registrar póliza</button>
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
<script src="<?php echo $url[0].'js/get_list_docto_relacionados.js'?>"></script>
<script src="<?php echo $url[0].'js/put_poliza_template.js'?>"></script>

<script>
// Select
$(document).ready(iniciaSelect);

$(document).ready(function() {
  $("input[name = 'txt_doctoRelacionado']").change(cambiarDoctRelacionado);
  $("#frm_AltaPoliza").submit(put_poliza);
  $(".regresar").click(regresar);
  $("#met_payment").change(mostrarCheque);
  $(".btn-cargar").click(cargar_ctas);
  $(".a_agregar").click(AgregarAsiento);
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

function regresar() {
  location.replace('template.php');
}


function mostrarCheque() {
  var tipoPliza = $("#met_payment").val();
  if (tipoPliza == 7) {
    $(".cheques").show('fast');
  } else {
    $(".cheques").hide('fast');
  }
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

function inputSumatxt_debe() {
  $form = $(this).parent().parent().parent().parent().parent();
  $.ajax({
    url: "../get/get_suma_debe.php",
    type: 'POST',
    data: $form.serialize(),
    dataType: 'html',
    success: function(data) {
      $div = $form.find('.txt_debeTotal:first');
      $input_debe = $form.find('.txt_debeTotalI:first');
      $input_haber = $form.find('.txt_haberTotalI:first');
      $mensaje = $form.find('.mensaje:first');
      $button = $('.a_guardar_ctas');

      $div.html($('#tmoneda').val() + " <label>" + data + "</label>");
      $input_debe.val(data);

      comparar_totales($input_haber, $input_debe, $mensaje, $button);
    }
  });
  return false;
}

function inputSumatxt_haber() {
  $form = $(this).parent().parent().parent().parent().parent();
  $.ajax({
    url: "../get/get_suma_haber.php",
    type: 'POST',
    data: $form.serialize(),
    dataType: 'html',
    success: function(data) {
      $div = $form.find('.txt_haberTotal:first');
      $input_haber = $form.find('.txt_haberTotalI:first');
      $input_debe = $form.find('.txt_debeTotalI:first');
      $mensaje = $form.find('.mensaje:first');
      $button = $('.a_guardar_ctas');

      $div.html($('#tmoneda').val() + " <label>" + data + "</label>");
      $input_haber.val(data);

      comparar_totales($input_haber, $input_debe, $mensaje, $button);
    }
  });
  return false;
}

function comparar_totales($input_haber, $input_debe, $mensaje, $button) {
  $.ajax({
    url: "../get/get_total_debe_haber.php",
    type: 'POST',
    data: {
      "haber": $input_haber.val(),
      "debe": $input_debe.val()
    },
    dataType: 'html',
    success: function(data) {
      var numItems = $('.txt_haber').length;

      if ((parseFloat($input_haber.val()) == parseFloat($input_debe.val())) || numItems == 1) {
        $button.show();
        $("#cabecera_ctas").show();
      } else {

        $button.hide();
        $("#cabecera_ctas").show();
      }
      $mensaje.html(data);
    }
  });
}

function AgregarAsiento() {
  var concepto = $('#txt_concepto').val();
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
  return false;
}

function AgregarCuentaInput() {
  $padre = $(this).parent().parent();
  $hijo = $padre.find("input").filter(":first");
  $hijo.val($(this).val());
  //$("#txt_haber").val("0.0");
}
</script>
<!-- /Select2 -->

</body>
</html>
