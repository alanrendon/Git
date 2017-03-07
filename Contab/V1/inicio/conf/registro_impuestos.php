<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/impuestos_registrados.class.php";
$impuesto = new impuestos();
$impuestos = $impuesto->get_impuestos();

?>
<!DOCTYPE html>
<html lang="es">
    <link href="<?php echo $url[0].'../vendors/select2/dist/css/select2.min.css'?>" rel="stylesheet">
    <link href="<?php echo $url[0].'../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css'?>" rel="stylesheet">
    <link href="<?php echo $url[0].'../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css'?>" rel="stylesheet">
    <link href="<?php echo $url[0].'../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css'?>" rel="stylesheet">
    <link href="<?php echo $url[0].'../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css'?>" rel="stylesheet">
    <link href="<?php echo $url[0].'../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css'?>" rel="stylesheet">
    <?php include_once($url[0]."base/head.php"); ?>
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Impuestos</h3>
                </div>
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
                            <div id="mensaje" style="padding:10px" align="center"></div>
                            <form id="form_impuestos" data-parsley-validate class="form-horizontal form-label-left">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label class="">Nombre del impuesto * </label>
                                        <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="impuesto">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="">Valor del impuesto* </label>
                                        <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="valor">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="">Tipo</label>
                                        <select class="select2_single form-control" name="tipo" id="tipo" tabindex="-1">
                                            <option value="0" >Compra</option>
                                            <option value="1" >Venta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group pull-right">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <button type="submit" class="btn btn-success" id="registrar_impuesto">Registrar impuesto</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Impuestos agregados</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php if(count($impuestos)>0): ?>

                            <table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Impuesto</th>
                                        <th>Valor % </th>
                                        <th>Tipo</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($impuestos as $value): ?>
                                    <tr>
                                        <td><?php echo $value->nombre; ?> </td>
                                        <td><?php echo $value->impuesto; ?> </td>
                                        <td><?=  $value->tipo == 0 ? 'Compra':'Venta'; ?> </td>
                                        <td>
                                            <ul class="nav panel_toolbox" style="min-width: 10px !important;">
                                                <li>
                                                    <a id="<?php echo $value->rowid; ?>" class="delete-link" title="Borrar">
                                                        <i class="fa fa-trash" style="color: red"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>

                            <?php endif ?>
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
<!-- Select2 -->
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
<script src="<?php echo $url[0].'../vendors/jszip/dist/jszip.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/pdfmake.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/vfs_fonts.js'?>"></script>

<script src="<?php echo $url[0].'js/impuestos.js'?>"></script>

<script>

    $( document ).on( "click", ".delete-link", function() {
    var id = $(this).attr('id');
    var tr = $(this).parent().parent().parent().parent();
    if ( confirm("¿Está seguro de eliminar este impuesto?") ) {
    $.ajax({
    type: "POST",
    url: '../edit/eliminar_impuesto.php',
    data: "id="+id,
    success: function(data) {
    tr.remove();
    }
    });
    }
    });

    $(".select2_single").select2({
    placeholder: "Seleccione una opción",
    });


</script>

</body>
</html>
