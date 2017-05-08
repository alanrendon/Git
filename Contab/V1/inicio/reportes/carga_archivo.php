<?php
$url = array();
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require "../put/put_file_poliza.php";
require_once "../class/poliza.class.php";

if (isset($_REQUEST['idp'])) {
    $poliza    = new Poliza();
    $documents = $poliza->get_info_docto_list((int)$_REQUEST['idp']);
}else{
    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Seleccione una póliza');
        window.location.href='../index.php';
        </SCRIPT>");
    exit();
}

$urld = $url[4] . "archivos/" . ENTITY . "/POL" . $_REQUEST['idp'] . "-" . $_REQUEST['tipo_pol'] . $_REQUEST['cons'] . "/";
?>
<!DOCTYPE html>
<html lang="es">
    <!-- Select2 -->
    <link href="<?php echo $url[0] . '../vendors/select2/dist/css/select2.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0].'css/pricing.css'?>" rel="stylesheet">
    

    <?php
    include_once($url[0] . "base/head.php");
    if (isset($_GET['ntipo_pol']) && isset($_GET['cons'])) {
        $_SESSION['ntipo_pol'] = addslashes(trim($_GET['ntipo_pol']));
        $_SESSION['cons']      = addslashes(trim($_GET['cons']));
    }
    ?>
    <div class="right_col" role="main">
        <ul class="nav right panel_toolbox" >
            <li>
                <a class="btn btn-default  btn-sm add-conf" title="Nueva" tabindex="0" href="consulta_diot.php">
                    <i class="fa fa-arrow-left"> </i> <span style="color:#7A8196 !important;">Regresar</span>
                </a>
            </li>
        </ul>
        <div class="row">
            <div class="x_panel" style="border: 1px solid #c2c2c2 !important;">
                <div class="x_title">
                    <h2>Carga de archivos Póliza <?= $_SESSION['ntipo_pol'] ?> <?= $_SESSION['cons'] ?></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                                <div id="mensaje"  class="col-sm-12">
                                    
                                </div>
                        </div>
                        <form enctype="multipart/form-data" method="post" id="formulario">
                            <input type="hidden" id="action" value="carga">
                            <input type="hidden" id="idp" value="<?= isset($_REQUEST['idp']) ? $_REQUEST['idp']: '' ?>">
                            <input type="hidden" id="tipo_pol" value="<?= isset($_REQUEST['tipo_pol']) ? $_REQUEST['tipo_pol']: '' ?>">
                            <input type="hidden" id="cons" value="<?= isset($_REQUEST['cons']) ? $_REQUEST['cons']: '' ?>">
                            <input type="hidden" id="tipo" value="<?= isset($_REQUEST['tipo']) ? $_REQUEST['tipo']: '' ?>">
                            <input type="hidden" id="anio" value="<?= isset($_REQUEST['anio']) ? $_REQUEST['anio']: '' ?>">
                            <input type="hidden" id="mes" value="<?=isset($_REQUEST['mes']) ? $_REQUEST['mes']: '' ?>">
                            <input type="hidden" id="por_factura" value="<?= isset($_REQUEST['por_factura']) ? $_REQUEST['por_factura']: '' ?>">
                            <input type="hidden" id="por_recurrente" value="<?= isset($_REQUEST['por_recurrente']) ? $_REQUEST['por_recurrente']: '' ?>">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label for="tipo_doc">Tipo Documento</label>
                                        <select id="tipo_doc" id="tipo_doc" class="select2_single">
                                            <option value="cfdixml">CFDI XML</option>
                                            <option value="cfdipdf">CFDI PDF</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="tipodocto_doc">Cargue el documento</label>
                                         <input name="file" id="file" type="file" multiple class="form-control"  />
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div_dibuja">
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <input class="btn btn-success pull-right" type="submit" value="Cargar">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="x_panel" style="border: 1px solid #c2c2c2 !important;">
                <div class="x_title">
                    <h2>Documentos cargados Póliza <?= $_SESSION['ntipo_pol'] ?> <?= $_SESSION['cons'] ?></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <label for="">Dar clic para descargar.</label>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php foreach ($documents as $key => $document): ?>
                            <div class="col-sm-12 col-md-3">
                                        <div class=" col-sm-12 col-xs-12 float-shadow">        
                                            <div class="price_table_container">
                                                <div class="price_table_heading primary-bg"> <?php echo strtoupper($document->tipo) ?></div>
                                                <div class="price_table_body">
                                                    <?php if (empty($document->archivo)): ?>
                                                        <div class="price_table_row">
                                                            RFC:
                                                            <?php echo $document->rfc ?>
                                                        </div>
                                                        <div class="price_table_row">
                                                            Nombre Extranjero:
                                                            <?php echo $document->nombre_extranjero ?>
                                                        </div>
                                                        <div class="price_table_row">
                                                            ID fiscal:
                                                            <?php echo $document->id_fiscal ?>
                                                        </div>
                                                    <?php else: ?>
                                                       
                                                          <?php if ($document->tipo == 'cfdixml'): ?>
                                                                <div class="price_table_row">
                                                                    RFC:
                                                                    <?php echo $document->rfc ?>
                                                                </div>
                                                                <div class="price_table_row">
                                                                    UUID:
                                                                    <?php echo $document->uuid ?>
                                                                </div>
                                                                <div class="price_table_row">
                                                                    <a href="<?php echo $urld.$document->archivo ?>" class="btn btn-success pull-center" target="_blank">Descargar</a>
                                                                </div>
                                                            <?php else: ?>
                                                               <div class="price_table_row" style=" word-wrap: break-word;">
                                                                    Archivo:
                                                                    <?php echo $document->archivo ?>
                                                                </div>
                                                                <div class="price_table_row">
                                                                    <a href="<?php echo $urld.$document->archivo ?>" class="btn btn-success pull-center" target="_blank">Descargar</a>
                                                                </div>
                                                          <?php endif ?>
                                                        
                                                    <?php endif ?>
                                                    
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
<script src="<?php echo $url[0] . 'js/app_select.js' ?>"></script>


<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0] . '../build/js/custom.min.js' ?>"></script>


<script>

    $(".select2_single").select2({
        placeholder: "Seleccione una opción"
    });
    $(".descargar").click(function () {
        window.open($(this).attr('href'), '_blank');
    });

    $.ajax({
        url: '../get/get_selects_operation_societe.php',
        dataType: 'html',
        type: 'post',
        data: {type:'get_list_typesociete', 'idp':$('#idp').val()},
        success: function (data) {
            $('#div_dibuja').html(data);
            $(".select2_single").select2({
                placeholder: "Seleccione una opción"
            });
            $.ajax({
                url: '../get/get_selects_operation_societe.php',
                dataType: 'html',
                data: {type:'type_societe', 'fk_type_societe': $('#id_typeoperation').val(), 'idp':$('#idp').val(),'primera':'1'},
                type: 'post',
                success: function (data) {
                    $('#get_list_typeoperation').html(data);
                    $(".select2_single").select2({
                        placeholder: "Seleccione una opción"
                    });
                    }
                });
        }

    });

    $('#tipo_doc').on('change',function (e){
        $('#div_dibuja').html('');
        if($('#tipo_doc').val() == 'cfdixml'){
          
        }
    });

    function get_list_typeoperation(){
        $('#get_list_typeoperation').html('');
                        
        $.ajax({
            url: '../get/get_selects_operation_societe.php',
            dataType: 'html',
            data: {type:'type_societe', 'fk_type_societe': $('#id_typeoperation').val(), 'idp':$('#idp').val()},
            type: 'post',
            success: function (data) {
                 $('#get_list_typeoperation').html(data);
                $(".select2_single").select2({
                    placeholder: "Seleccione una opción"
                });
            }
        });
    }

    $("#formulario").on("submit", function (e) {
     
        var uui_replace = 0;
        e.preventDefault();

        if(confirm('¿Está seguro de cargar el archivo?'))
        {
            if (file) {
                ajax_file(uui_replace);
            } else {
                $("#mensaje").html('<label>Debe seleccionar un archivo</label>');
            }
        }
 
    });

    function ajax_file(uui_replace){
        var file = $('#file').prop('files')[0];
        var form = new FormData();
        form.append('docto', file);
        form.append('uui_replace', uui_replace);
        form.append('idp', $('#idp').val() );
        form.append('tipo_pol', $('#tipo_pol').val() );
        form.append('cons', $('#cons').val() );
        form.append('tipo', $('#tipo').val() );
        form.append('anio', $('#anio').val() );
        form.append('mes', $('#mes').val() );
        form.append('por_factura', $('#por_factura').val() );
        form.append('por_recurrente', $('#por_recurrente').val() );
        form.append('tipo_doc', $('#tipo_doc').val() );
        form.append('action', $('#action').val());
        form.append('id_typeoperation', $('#id_typeoperation').val());
        form.append('operation_societe', $('#operation_societe').val());
        form.append('rfc_ext', $('#rfc_ext').val());
        form.append('id_ext', $('#id_ext').val());
        form.append('nombre_ext', $('#nombre_ext').val());

        $.ajax({
            url: '../put/put_file_poliza.php',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form,
            type: 'POST',
            success: function (data) {
                if(data.mensaje){
                    $("#mensaje").html('<label">' + data.mensaje + '</label>');
                    setTimeout('document.location.reload()',2000);
                }else if(data.repetido){
                    var string ='';
                    $.each( data.pol, function( key, value ) {
                        string += '\n'+value;
                    });
                    if (confirm(data.repetido+string)) {
                        ajax_file(1);
                    }
                }
                
            }
        });
        return true;
    }

</script>
</body>
</html>
