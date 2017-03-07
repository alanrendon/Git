<?php
$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";
require_once ($url[0] . "class/sat_ctas.class.php");
$not_rel = new Cuenta();
$not_rel_cuentas = $not_rel->get_cuentas();
?>
<!DOCTYPE html>
<html lang="es">
    <link href="<?php echo $url[0] . '../vendors/select2/dist/css/select2.min.css' ?>" rel="stylesheet">
    <link href="<?php echo $url[0] . '../vendors/dropzone/dist/min/dropzone.min.css' ?>" rel="stylesheet">
    <?php include_once($url[0] . "base/head.php"); ?>
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Cuentas</h3>
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
                            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Registro</a>
                                    </li>
                                    <li role="presentation" class="">
                                        <a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Carga masiva (CSV)</a>
                                    </li>
                                </ul>
                                <div id="myTabContent" class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                                        <br />
                                        <div id="mensaje_cuenta" style="padding:10px" align="center"></div>
                                        <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label class="">*Código contable</label>
                                                    <input type="text" required="required" class="form-control col-md-7 col-xs-12" name="codigo_cuenta">
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label class="">*Nombre cuenta</label>
                                                    <input class="form-control col-md-7 col-xs-12" type="text" name="nombre_cuenta">
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label>*Nivel</label>
                                                    <select class="select2_single form-control" tabindex="-1" name="nivel_cuenta">
                                                        <option></option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label>*Naturaleza</label>
                                                    <select class="select2_single form-control" tabindex="-1" name="naturaleza_cuenta">
                                                        <option></option>
                                                        <option value="A">Acredora</option>
                                                        <option value="D">Deudora</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label class="">Código SAT</label>
                                                    <select class="select2_single form-control" name="codigo_sat"  id="codigo_sat">
                                                        <option value=""></option>
                                                        <?php foreach ($not_rel_cuentas as $key => $value): ?>
                                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label class="">*Afectada</label>
                                                    <select class="select2_single form-control" name="afectada">
                                                        <option selected="selected" value='0'>No</option>
                                                        <option  value='1'>Sí</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ln_solid"></div>
                                            <div class="form-group pull-right">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <button type="submit" class="btn btn-success" id="carga_cuenta">Registrar cuenta</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                                        <div id="mensaje_carga"></div>
                                        <div align="right">
                                            <a href="ejemplo_cuentas.csv" class="btn btn-default buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons">
                                                <span>Descargar formato CSV de ejemplo </span>
                                            </a>
                                        </div>
                                        <form action="" class="dropzone" id="myAwesomeDropzone">
                                            <span>Solo podrá subir archivos en formato CSV. Al eliminar las cuentas se borrará toda información relacionada a estas.</span>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="flat" checked="checked" id="eliminar_ctas"> Eliminar cuentas registradas
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <div class="fallback">
                                                        <input name="file" id="file" type="file" multiple />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12" style="margin-top: 40px;">
                                                    <button type="button" class="btn btn-success" id="carga_cvs">Cargar</button>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
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

<script type="text/javascript" src="<?php echo $url[0] . 'js/cuentas_cvs.js' ?>"></script>
<script>
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
    });

</script>


</body>
</html>
