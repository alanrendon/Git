<?php

$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once ($url[0]."class/fact_clie_pendientes.class.php");
require_once ($url[0]."class/cat_cuentas.class.php");
require_once ($url[0]."class/cuentas_rel.class.php");
require_once ($url[0]."class/multidivisa.class.php");


$divisa = new Divisa();

$multidivisa_factures = $divisa->get_list_factures_affected();


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
            <h2>Facturas de cliente pendientes de contabilizar</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" >
            <br />
            <div id="dv_tabla">
                <form id="frm_AsignarPoliza" data-parsley-validate class="form-horizontal form-label-left">
                    <div class="row">
                        <div class="col-md-12 pull-right">
                            <button type="submit" class=" pull-right btn btn-success btn-sm">Siguiente</button>
                        </div>
                    </div>
                    <table class="table table-striped jambo_table bulk_action" id="datatable">
                        <thead>
	                        <th>Referencia</th>
	                        <th>Tipo documento</th>
	                        <th>Divisa Origen</th>
	                        <th>Divisa Destino</th>
	                        <th>Fecha Conversión</th>
	                        <th>Importe Pagado</th>
	                        <th>Tipo de cambio</th>
	                        <th><i class="fa fa-calculator" aria-hidden="true"></i></th>
                        </thead>
                        <tbody>
                            <?php foreach ($multidivisa_factures as $key => $value): ?>
                            		<?php if (strcmp($value->type_document,'facture') ): ?>
                            			<?php $facture = $divisa->get_Facture($value->fk_document); ?>
                            			<?php $value->type_document = 'Factura Cliente' ?>
                            		<?php else: ?>
                            			<?php $facture = $divisa->get_Facture_Fourn($value->fk_document);	 ?>
                            			<?php $value->type_document = 'Factura Proveedor' ?>
                            		<?php endif ?>
                            		
                            		<?php if (is_object($facture)): ?>
                            			<tr>
                            				<td>
                            			 		<?php echo $facture->ref ?>
                            			 		<a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $value->type_document.' - '.$facture->nom ?>">
                                               	 <i class="fa fa-question-circle fa-lg"> </i></a>
                                            	</a>
	                            			 </td>
	                            			 <td>
	                            			 	<?php echo  $value->type_document ?>
	                            			 </td>
	                            			 <td>
	                            			 	<?php echo $value->source_divisa ?>
	                            			 </td>
	                            			 
	                            			 <td>
	                            			 	<?php echo $value->target_divisa ?>
	                            			 </td>
	                            			 <td>
	                            			 	<?php echo $value->fecha.'  '.$value->hora?>
	                            			 </td>
	                            			 <td>
	                            			 	<?php echo number_format($facture->total_ttc,2) ?>
	                            			 </td>
	                            			 <td>
	                            			 	<?php echo $value->tipo_cambio ?>
	                            			 </td>
	                            			 <td>
	                            			 	<input type="checkbox" class="radio" value="<?php echo $value->rowid ?>" name="divisa_id[]" /></label>
	                            			 </td>
                            			</tr>
                            		<?php endif ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </form>
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
<script src="<?php echo $url[0].'js/app_select.js'?>"></script>
<script src="<?php echo $url[0].'js/asientos.js'?>"></script>
<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0].'../build/js/custom.min.js'?>"></script>


<!-- bootstrap-daterangepicker -->
<script src="<?php echo $url[0].'js/moment/moment.min.js'?>"></script>
<script src="<?php echo $url[0].'js/datepicker/daterangepicker.js'?>"></script>

<!-- Datatables -->
<script src="<?php echo $url[0].'../vendors/datatables.net/js/jquery.dataTables.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-responsive/js/dataTables.responsive.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/datatables.net-scroller/js/dataTables.scroller.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/jszip/dist/jszip.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/pdfmake.min.js'?>"></script>
<script src="<?php echo $url[0].'../vendors/pdfmake/build/vfs_fonts.js'?>"></script>

<script type="text/javascript">

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

// the selector will match all input controls of type :checkbox
// and attach a click event handler 
$("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});

</script>

</body>
</html>
