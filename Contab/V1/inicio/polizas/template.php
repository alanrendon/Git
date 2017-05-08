<?php

$url[0] = "../";
require_once "../class/poliza.class.php";
require_once "../class/periodo.class.php";
require_once "../class/asiento.class.php";
require_once "../class/cat_cuentas.class.php";

$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

$polizas        = new Poliza();
$asiento        = new Asiento();
$cuenta         = new Cuenta();
$periodo         = new Periodo();
$tipo           = 0;
$anio           = 0;
$mes            = 0;
$por_factura    = 0;
$por_recurrente = 0;

if(isset($_GET['tipo']) || isset($_GET['anio']) || isset($_GET['mes']) || isset($_GET['por_factura']) ) {
$tipo            = (isset($_GET['tipo']))?$_GET['tipo']:'0';
$anio            = (isset($_GET['anio']))?$_GET['anio']:'0';
$mes             = (isset($_GET['mes']))?$_GET['mes']:'0';
$por_factura     = (isset($_GET['por_factura']))?$_GET['por_factura']:'0';

$arreglo_Polizas = $polizas->getPolizasFiltros_recurrentes($anio,$mes,$tipo,$por_factura);
}
else if( isset($_GET['id']) ) {
$id = ($_GET['id'])?$_GET['id']:'';
if ( $id > 0 ) {
$arreglo_Polizas = $polizas->getPolizaId($id);
}
}
else {
$arreglo_Polizas = $polizas->getPolizas_recurrentes();
}

$arreglo_cuentas = $cuenta->get_cuentas();
$periodos = $periodo->get_all_Periodos_anio();

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

    <!-- JPaginate -->
    <link href="<?php echo $url[0].'../vendors/jpaginate/css/style.css'?>" rel="stylesheet">

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
            </div>
            <ul class="nav right panel_toolbox" >
                <li>
                    <a class="btn btn-default buttons-csv buttons-html5 btn-sm add-conf" title="Nueva" tabindex="0" href="nuevo_template.php">
                        <i class="fa fa-puzzle-piece"> </i> <span style="color:#7A8196 !important;">Nueva</span>
                    </a>
                </li>			
            </ul>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Consulta y registro</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php if ( !isset($id) || $id == '' ) {  ?>
                            <div class="form-group">
                                <form action="template.php" method="get">
                                    <div class="col-md-3 col-sm-2 col-xs-12">
                                        <label>Tipo</label>
                                        <select class="select2_single form-control" tabindex="-1" name="tipo">
                                            <option></option>
                                            <option value="T" <?php print ($tipo=='T')?'selected':''; ?>>Todas</option>
                                            <option value="D" <?php print ($tipo=='D')?'selected':''; ?>>Diario</option>
                                            <option value="E" <?php print ($tipo=='E')?'selected':''; ?>>Egreso</option>
                                            <option value="C" <?php print ($tipo=='C')?'selected':''; ?>>Cheque</option>
                                            <option value="I" <?php print ($tipo=='I')?'selected':''; ?>>Ingreso</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 col-sm-12 col-xs-12">
                                        <label>&nbsp;</label><br />
                                        <button class="btn btn-success pull-left" type="submit"> Consultar</button>
                                    </div>
                                </form>
                            </div>
                            <br />
                            <?php } ?>
                            <div id="dv_dibujarPolizas" class="demo">
                                <?php if ( $arreglo_Polizas ){ $count=0; ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="paginas"></div>
                                    </div>
                                </div>
                                <?php
                                $count2=0; foreach ( $arreglo_Polizas as $key ): $count2++;

                                if ( $key['societe_type'] == 1 ) {
                                $tipoDoctorelacionado =  $key['facnumber'];
                                }
                                else if( $key['societe_type'] == 2 ) {
                                $tipoDoctorelacionado = $key['ref'];
                                }
                                else {
                                $tipoDoctorelacionado = "No hay docto.";
                                }

                                if($count2==1 ): $count++;
                                ?>
                                <div id='p<?php echo $count;?>' style="display:none;">
                                    <?php endif?>
                                    <div class="col-md-12 col-xs-12 col-lg-12" >
                                        <div class=" x_panel" style="border: 1px solid #c2c2c2 !important;">
                                            <div class="x_title">
                                                <h2>Póliza de <?php echo $key['tipo_pol'].': '.($key['concepto']);?></h2>
                                                <ul class="nav navbar-right panel_toolbox">
                                                    <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>                                                    
                                                    <li><a class="cascaron-link" title="Crear póliza" href="cascaron.php?id=<?php echo $key['id']; ?>"><i class="fa fa-filter" style="color: black"></i></a></li>
                                                    <li><a class="delete-link" title="Borrar" idpoliza="<?php echo $key['id']; ?>"><i class="fa fa-trash" style="color: black"></i></a></li>
                                                </ul>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th width="30%">Documento Relacionado:</th>
                                                            <th width="20%"><?php echo $tipoDoctorelacionado; ?></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th width="20%"></th>
                                                            <th></th>
                                                        </tr>
                                                         <?php if(!empty ($key['comentario'])) : ?>
                                                            <tr>
                                                                <th colspan="6">Comentario: <br>  <p><?php echo  ($key['comentario']); ?> </p></th>
                                                            </tr>
                                                        <?php endif ?>
                                                        <?php if ( $key['tipo_pol'] == 'Cheque' ) {  ?>
                                                        <tr>
                                                            <th scope="row"  width="50" >Cheque a Nombre: </th>
                                                            <td><?php echo( $key['anombrede']); ?></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row"  width="50" >Num. Cheque: </th>
                                                            <td><?php echo $key['numcheque']; ?></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                            <td width="90"></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <div class="ln_solid"></div>
                                                <?php
                                                $arrasiento = $asiento->get_asientoPoliza($key['id']);
                                                if ( $arrasiento ) {
                                                $total_debe = 0;
                                                $total_habe = 0;
                                                ?>
                                                <table class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No.</th>
                                                            <th>Cuenta</th>
                                                            <th>Descripción</th>
                                                            <th width="10%" align="right">Debe</th>
                                                            <th width="10%" align="right">Haber</th>
                                                            <th width="5%" class="column-title no-link last"><span class="nobr"></span></th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ( $arrasiento as $key2 ):
                                                        $total_debe += $key2["debe"];
                                                        $total_habe += $key2["haber"];
                                                        ?>
                                                        <tr>
                                                            <td align="center"><?php echo $key2["asiento"];?></td>
                                                            <td>
                                                                <?php
                                                                $nom_cuenta = $cuenta->get_nom_cuenta($key2["cuenta"]);
                                                                echo $key2["cuenta"].' - '.$nom_cuenta;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                
                                                                echo $key2["descripcion"];
                                                                ?>
                                                            </td>
                                                            
                                                            <td align="right"><?=$moneda?> <?php echo number_format($key2["debe"],2,'.',','); ?></td>
                                                            <td align="right"><?=$moneda?> <?php echo number_format($key2["haber"],2,'.',','); ?></td>
                                                            <td class="last">
                                                                <ul class="nav left panel_toolbox" >
                                                                    <li><a class="editAsiento-link" title="Editar" idasiento="<?php echo $key2["rowid"];?>" ><i class="fa fa-edit" style="color: black"></i></a></li>
                                                                    <li><a class="deleteAsiento-link" title="Borrar" idasiento="<?php echo $key2["rowid"];?>"><i class="fa fa-trash" style="color: black"></i></a></li>
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach ?>
                                                        <tr>
                                                            <td colspan="2"><strong>Total</strong></td>
                                                             <td></td>

                                                            <td align="right"><strong><?=$moneda?> <?php print number_format($total_debe,2,'.',','); ?></strong></td>
                                                            <td align="right"><strong><?=$moneda?> <?php print number_format($total_habe,2,'.',','); ?></strong></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php if ( (int)$total_debe !== (int)$total_habe ) { ?>
                                                        <tr>
                                                            <td colspan="2"></td>
                                                            <td colspan="3"><span style="color:red">Los totales no coinciden, favor de verificar.</span></td>
                                                        </tr>
                                                        <?php  } ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                                }
                                                ?>
                                                <div>
                                                    <ul class="nav right panel_toolbox" >
                                                        <li>
                                                            <a class="btn btn-default buttons-csv buttons-html5 btn-sm a_agregar" title="Agregar" tabindex="0" aria-controls="datatable-buttons" idpoliza="<?php echo $key['id']; ?>">
                                                                <i class="fa fa-plus fa-lg" style="color: blue"> </i> <span style="color:#7A8196 !important;">Agregar cuenta</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <form>
                                                        <input type="hidden" value="<?php echo $key['id']; ?>" name="rowid">
                                                        <div class="row">
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">
                                                                <div class="form-group">
                                                                    <br />
                                                                    <div class='col-sm-3'>
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
                                                        <div class="row" >
                                                            <br />
                                                            <div class="col-sm-12">
                                                                <a class="btn btn-success a_guardar_ctas" style="display: none;">
                                                                    Guardar
                                                                </a>
                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php if($count2==3): ?>
                                    <?php  $count2=0; ?>
                                </div>
                                <?php endif?>
                                <?php  endforeach ?>

                                <?php } else { print 'No se encontraron resultados.'; } ?>
                                <input type="hidden" value ='<?php echo $count;?>' id="cantidad">

                            </div>
                            <div id="add_modalAsiento" class="modal fade" role="dialog">
                                <div class="modal-dialog" style="width: 60% !important;">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Registro de cuenta</h4>
                                        </div>
                                        <form id="frm_putAsiento" accept-charset="utf-8" data-parsley-validate class="form-horizontal form-label-left">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                        <input type="hidden" name="id_poliza" id="id_poliza">
                                                        <div class="form-group">
                                                            <div class="col-sm-5">
                                                                <label class="">Cuenta <span class="required">*</span></label>
                                                                <select class="select2_single form-control" name="txt_cuenta"  id="txt_cuenta" size="90" style="width:300px">
                                                                    <?php foreach ( $arreglo_cuentas as $key => $value ): ?>
                                                                    <option value="<?php print $key; ?>">
                                                                        <?php print $value; ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-3 col-sm-3 col-xs-3">
                                                                <label class="">Debe <span class="required">*</span></label>
                                                                <input type="text" placeholder="Debe" name="txt_debe" id="txt_debe"  class="form-control col-md-7 col-xs-12" value="0">
                                                            </div>
                                                            <div class="col-md-3 col-sm-3 col-xs-3">
                                                                <label class="">Haber <span class="required">*</span></label>
                                                                <input type="text" placeholder="Haber" name="txt_haber"  id="txt_haber" class="form-control col-md-7 col-xs-12" value="0">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Registrar cuenta</button>
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

<script src="<?php echo $url[0].'../vendors/jquery/dist/jquery.min.js'?>"></script>
<!-- Bootstrap -->
<script src="<?php echo $url[0].'../vendors/bootstrap/dist/js/bootstrap.min.js'?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0].'../vendors/fastclick/lib/fastclick.js'?>"></script>

<script src="<?php echo $url[0].'../vendors/iCheck/icheck.min.js'?>"></script>

<!-- Select2 -->
<script src="<?php echo $url[0].'../vendors/select2/dist/js/select2.full.min.js'?>"></script>
<script src="<?php echo $url[0].'js/app_select.js'?>"></script>
<script src="<?php echo $url[0].'js/asientos.js'?>"></script>
<script src="<?php echo $url[0].'js/polizas_tools.js'?>"></script>

<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>

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


<!-- JPaginate -->
<script src="<?php echo $url[0].'../vendors/jpaginate/jquery.paginate.js'?>"></script>

<script type="text/javascript">

    iniciaSelect();
    $(".delete-link").click(eliminarPoliza);
    $(".recycle-link").click(recuerentePoliza);
    $(".clonar-link").click(clonarPoliza);
    $(".remove-recurrente-link").click(removerRecurrente);

    $(document).ready(function () {
        $(".a_agregar").click(AgregarAsiento);
        $(".deleteAsiento-link").click(eliminarAsiento);
        $(".editAsiento-link").click(abrirModalUpdate);

    });

    $('.a_guardar_ctas').on("click", function () {
        $padre = $(this).parent().parent().parent().parent().parent();
        $form = $padre.find('form:first');
        if (confirm("¿Está seguro de agregar la póliza?")) {
            $.ajax({
                url: "../put/put_asientos.php",
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.mensaje) {
                        alert(data.mensaje);
                    } else {
                        $(location).attr("href", "consulta.php");
                    }
                }
            });
        }
        return false;
    });

    function AgregarAsiento() {
        $padre = $(this).parent().parent().parent();
        var cuenta = 1;
        if (cuenta != '') {
            $.ajax({
                url: "../get/div_asientos.php",
                type: 'POST',
                data: {"cuenta": cuenta},
                dataType: 'html',
                success: function (data) {
                    $dv_Asientos = $padre.find('div:first');
                    $a_last = $padre.find('a:last');

                    $dv_Asientos.append(data);
                    $(".slc_cuenta").change(AgregarCuentaInput);
                    iniciaSelect();
                    $(".txt_debe").change(inputSumatxt_debe);
                    $(".txt_haber").change(inputSumatxt_haber);
                    $a_last.show();
                    $(".a_borrarAsiento").click(function () {
                        $padre = $(this).parent().parent().parent().parent();
                        $padre.remove();
                    });
                }
            });
        }
        return false;
    }

    function inputSumatxt_debe() {
        $form = $(this).parent().parent().parent().parent().parent();
        $.ajax({
            url: "../get/get_suma_debe.php",
            type: 'POST',
            data: $form.serialize(),
            dataType: 'html',
            success: function (data) {
                $div = $form.find('.txt_debeTotal:first');
                $input_debe = $form.find('.txt_debeTotalI:first');
                $input_haber = $form.find('.txt_haberTotalI:first');
                $mensaje = $form.find('.mensaje:first');
                $button = $form.find('.a_guardar_ctas:first');

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
            success: function (data) {
                $div = $form.find('.txt_haberTotal:first');
                $input_haber = $form.find('.txt_haberTotalI:first');
                $input_debe = $form.find('.txt_debeTotalI:first');
                $mensaje = $form.find('.mensaje:first');
                $button = $form.find('.a_guardar_ctas:first');

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
            data: {"haber": $input_haber.val(), "debe": $input_debe.val()},
            dataType: 'html',
            success: function (data) {

                if (parseFloat($input_haber.val()) == parseFloat($input_debe.val())) {
                    $button.show();
                } else {
                    $button.hide();
                }
                $mensaje.html(data);
            }
        });
    }
    function AgregarCuentaInput() {
        $padre = $(this).parent().parent();
        $hijo = $padre.find("input").filter(":first");
        $hijo.val($(this).val());
        $("#txt_haber").val("0.0");
    }

    function abrirModalPut() {
        $('#txt_descripcion').val(" ");
        $('#txt_debe').val(" ");
        $('#txt_haber').val(" ");
        var idPoliza = $(this).attr('idpoliza');
        $("#id_poliza").val(idPoliza);
        $('#add_modalAsiento').modal();
        $("#frm_putAsiento").submit(put_Asiento);
        iniciaSelect();
    }

    function abrirModalUpdate() {
        var idPoliza = $(this).attr('idasiento');
        $("#id_poliza").val(idPoliza);
        consultaAsiento(idPoliza);
        $('#add_modalAsiento').modal();
        $("#frm_putAsiento").submit(updateAsiento);
        iniciaSelect();
    }

    $("#paginas").paginate({
        count: $('#cantidad').val(),
        start: 1,
        display: 10,
        border: true,
        text_color: 'rgb(42, 63, 84)',
        background_color: 'nonnw',
        text_hover_color: '#2573AF',
        background_hover_color: 'none',
        images: false,
        mouse: 'press',
        onChange: function (page) {
            $('._current', '#dv_dibujarPolizas').removeClass('_current').hide();
            $('#p' + page).addClass('_current').show();
        }
    });

    $(document).ready(function () {
        $('#p1').addClass('_current').show();

    });

</script>
<!-- /Select2 -->

</body>
</html>
