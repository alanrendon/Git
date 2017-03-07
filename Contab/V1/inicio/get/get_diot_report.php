<?php

    $url[0] = "../";
    $url[1] = "../configuracion/";
    $url[2] = "../asignacion/";
    $url[3] = "../cuentas/";
    $url[4] = "../polizas/";
    $url[5] = "../conf/";
    $url[6] = "../periodos/";
    $url[7] = "../reportes/";

    require_once '../class/poliza.class.php';
    require_once '../class/periodo.class.php';
    require_once '../class/fact_prov_pendientes.class.php';
    require '../get/get_data_xml.php';


$polizas       =new Poliza();
$periodo       =new Periodo();
$facture_fourn =new FacPolizaPendiente();
$fecha         =$periodo->get_ultimo_periodo_abierto();
$anio          =0;
$mes           =0;
$j             =0;
$i             =0;
$xml_array     =array(); 

if (isset($_GET['anio']) && isset($_GET['mes'])) {
    $anio = (int)$_GET['anio'];
    $mes  = (int)$_GET['mes'];
}
if($anio <=0 || $mes <= 0 ){
    $anio  = $fecha->anio;
    $mes   = $fecha->mes;
}
//header('Content-type: text/plain');
//header('Content-Disposition: attachment; filename=DIOT_POLIZAS_'.$anio.'-'.$mes.'.txt');
$polizas_diot= $polizas->getPolizas_diot($anio, $mes);

foreach ($polizas_diot as $poli_key => $poli_value) {
    if( ($xml_rows = $polizas->get_info_docto_xml((int)$poli_value['id'])) && sizeof($xml_rows)>0 ){
        foreach ($xml_rows as $xml_key => $xml_value) {
                
                $url_xml = $url[4].'archivos/'.ENTITY.'/POL'.$poli_value['id'].'-'.$poli_value['tipol_l'].$poli_value['npol'].'/'.$xml_value->archivo;

                if(empty($xml_value->archivo) && ($fac_rows = $polizas->get_info_factures_xml_without_operation_societe_fourn((int)$poli_value['id'])) && sizeof($fac_rows)>0 ){
                
                    foreach ($fac_rows as $fac_key => $fac_value) {
                        $xml_array[$i]['rfc']               =(string)$xml_value->rfc;
                        $xml_array[$i]['key_to']            =(string)$xml_value->key_to;
                        $xml_array[$i]['key_ts']            =(string)$xml_value->key_ts;
                        
                        $xml_array[$i]['id_fiscal']         =(string)$xml_value->id_fiscal;
                        $xml_array[$i]['nombre_extranjero'] =(string)$xml_value->nombre_extranjero;
                        $xml_array[$i]['compIppSubTot']     =0;
                        $xml_array[$i]['compIppTot']        =round(floatval($fac_value->total),0);
                        $i++;
                    
                    }
                }
                else if (file_exists($url_xml) ) {
        
                    get_data_xml($url_xml);
                    $xml_array[$i]['rfc']               =(string)$emisorRfc;
                    $xml_array[$i]['key_to']            =(string)$xml_value->key_to;
                    $xml_array[$i]['key_ts']            =(string)$xml_value->key_ts;
                    $xml_array[$i]['id_fiscal']         ='';
                    $xml_array[$i]['nombre_extranjero'] ='';
                    $xml_array[$i]['compIppSubTot']     =round(floatval($compIppSubTot),0);
                    $xml_array[$i]['compIppTot']        = 0;
                    
                    $i++;
                }

        }
    
    }else if( ($fac_rows = $polizas->get_info_factures_xml((int)$poli_value['id'])) && sizeof($fac_rows)>0 ){
        foreach ($fac_rows as $fac_key => $fac_value) {
            $xml_array[$i]['rfc']               =(string)$fac_value->rfc;
            $xml_array[$i]['key_to']            =(string)$fac_value->key_to;
            $xml_array[$i]['key_ts']            =(string)$fac_value->key_ts;
            $xml_array[$i]['id_fiscal']         ='';
            $xml_array[$i]['nombre_extranjero'] ='';
            $xml_array[$i]['compIppSubTot']     =round(floatval($fac_value->subtotal),0);
            $xml_array[$i]['compIppTot']        = 0;

            $i++;
        }
    }
}

$aux=array();

$xml_array=burbuja($xml_array,sizeof($xml_array));

for ($i=0; $i < sizeof($xml_array) ; $i++) { 
    if($i==0)
    {
        $aux[$j] = $xml_array[$i];
    }else if( $aux[$j]['rfc'] ==  $xml_array[$i]['rfc']  && ( $aux[$j]['key_to'] == $xml_array[$i]['key_to'] && $aux[$j]['key_ts'] == $xml_array[$i]['key_ts'])  ){
        $aux[$j]['compIppSubTot'] += $xml_array[$i]['compIppSubTot'];
        $aux[$j]['compIppTot']    += $xml_array[$i]['compIppTot'];
    }else{
        $j++;
        $aux[$j] = $xml_array[$i];
    }
}
$xml_array =$aux;
function burbuja($A,$n)
    {
        for($i=1;$i<$n;$i++)
        {
                for($j=0;$j<$n-$i;$j++)
                {
                    if($A[$j]>$A[$j+1])
                        {$k=$A[$j+1]; $A[$j+1]=$A[$j]; $A[$j]=$k;}
                }
        }

      return $A;
    }

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

    <!-- JPaginate -->
    <link href="<?php echo $url[0] . '../vendors/jpaginate/css/style.css' ?>" rel="stylesheet">

    <?php
    include_once($url[0] . "base/head.php");
    ?>

   <style>
       ul.jPag-pages{
           width: 100% !important;
       }
        .c{
         
            width: auto;
            height: auto;
            margin: 0;
            position:absolute;
            top:-13px;
            right:-8px;
            padding:5px;
            color: #F4A460 !important;
        }    
    </style>
    <!-- page content -->
    <div class="right_col" role="main">
        <input type="hidden" id="tmoneda" value="<?= $moneda ?>">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <?php if (isset($id) && $id != '' && $arreglo_Polizas[0]['fecha'] == '0000-00-00'): ?>
                        <h3>Pólizas Plantillas</h3>
                    <?php else: ?>
                        <h3>Pólizas</h3>
                    <?php endif ?>
                </div>
                    <?php if (isset($id) && $id != ''  && $arreglo_Polizas[0]['fecha'] == '0000-00-00') { ?>
                    <ul class="nav right panel_toolbox" >
                        <li>
                            <a class="btn btn-default buttons-csv buttons-html5 btn-sm back-edit" title="Nueva" tabindex="0">
                                <i class="fa fa-arrow-left"> </i> <span style="color:#7A8196 !important;">Regresar</span>
                            </a>
                        </li>			
                    </ul>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <?php if (isset($id) && $id != '' && $arreglo_Polizas[0]['fecha'] == '0000-00-00'): ?>
                                <h2>Asignar asientos</h2>
                            <?php else: ?>
                                <h2>Reporte</h2>
                            <?php endif ?>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="overflow-x: auto;">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr role="row">
                                	<th style='text-align: center;'  colspan="2">TIPO DE TERCERO</th>
                                	<th style='text-align: center;'  colspan="12"></th>
                                	<th style='text-align: center;'  colspan="3">TASA DE IVA FRONTERIZO</th>
                                	<th style='text-align: center;'  colspan="2">TASA DE IVA GENERAL</th>
                                	<th style='text-align: center;'  colspan="2">TASA DE IVA FRONTERIZO</th>
                                	<th style='text-align: center;'  >EXENTO</th>
                                	<th style='text-align: center;'  >TASA AL 0%</th>
                                	<th style='text-align: center;'  >EXENTO</th>
                                	<th style='text-align: center;'  colspan="4"></th>
                                </tr>
                                </thead>
                            <tbody>
                                	<tr role='row'>
	                                	<td style='text-align: center;'>NACIONAL</td>
	                                	<td style='text-align: center;'>EXTRANJERO</td>


	                                	<td style='text-align: center;'>TIPO DE OPERACIÓN</td>
	                                	<td style='text-align: center;'>CONCEPTO O BIEN DEL SERVICIO</td>
	                                	<td style='text-align: center;'>RFC DEL PROVEEDOR</td>
	                                	<td style='text-align: center;'>NÚMERO DE ID FISCAL</td>
	                                	<td style='text-align: center;'>NOMBRE DEL EXTRANJERO</td>
	                                	<td style='text-align: center;'>PAÍS DE RESIDENCIA</td>
	                                	<td style='text-align: center;'>NACIONALIDAD</td>
	                                	<td style='text-align: center;'>MONTO DE LA OPERACIÓN PAGADA A PROVEEDORES (INCLUYENDO IVA)</td>
	                                	<td style='text-align: center;'>MONTO DE LA OPERACIÓN DEDUCIBLE PARA ISR</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A PROVEEDORES A TASA DE IVA GENERAL</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A LA TASA DEL 15% DE IVA</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A LA TASA DEL 15%  DE IVA</td>
	                                	<td style='text-align: center;'>MONTO DEL IVA PAGADO NO ACREDITABLE A LA TASA DE IVA GENERAL (CORRESPONDIENTE EN LA PROPORCION DEDUCIBLE DE ISR)</td>



	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A PROVEEDORES A TASA DE IVA FRONTERIZO</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A LA TASA DEL 10% DE IVA</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A PROVEEDORES EN LA IMPORTACION DE BIENES Y SERVICIOS A LA TASA DE IVA GENERAL</td>




	                                	<td style='text-align: center;'>MONTO DEL IVA PAGADO NO ACREDITABLE POR LA IMPORTACION A LA TASA DE IVA GENERAL (CORRESPONDIENTE EN LA PROPORCION DEDUCIBLE DE ISR)</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A PROVEEDORES EN LA IMPORTACION DE BIENES Y SERVICIOS A LA TASA DE IVA FRONTERIZO</td>


	                                	<td style='text-align: center;'>MONTO DEL IVA PAGADO NO ACREDITABLE POR LA IMPORTACION A LA TASA DE IVA FRONTERIZO (CORRESPONDIENTE EN LA PROPORCION DEDUCIBLE DE ISR)</td>
	                                	<td style='text-align: center;'>VALOR DE LOS ACTOS O ACTIVIDADES PAGADOS A PROVEEDORES EN LA IMPORTACION DE BIENES Y SERVICIOS POR LOS QUE NO SE PAGARÁ EL IVA (EXENTOS)</td>

	                                	<td style='text-align: center;'  >VALOR DE LOS DEMAS ACTOS 0 ACTIVIDADES PAGADOS A PROVEEDORES A LA TASA DEL 0% DE IVA </td>
	                                	<td style='text-align: center;'  >VALOR DE LOS DEMAS ACTOS 0 ACTIVIDADES PAGADOS A PROVEEDORES POR LOS QUE NO PAGA EL IVA (EXENTOS)</td>
	                                	<td style='text-align: center;'  >IVA RETENIDO POR EL CONTRIBUYENTE</td>

	                                	<td style='text-align: center;'>TOTAL DE IVA TRASLADADO AL CONTRIBUYENTE (PAGADO A PROVEEDORES EXCEPTO IMPORTACIONES DE BIENES Y SERVICIOS) </td>
	                                	<td style='text-align: center;'>TOTAL DE IVA PAGADO EN LAS IMPORTACIONES DE BIENES Y SERVICIOS</td>
	                                	<td style='text-align: center;'>IVA CORRESPONDIENTE A LAS DEVOLUCIONES, DESCUENTOS Y BONIFICACIONES SOBRE COMPRAS</td>
	                                </tr>
                            

                        
                        <?php
                            foreach ($xml_array as $xml_key => $xml_value) {
                                /*echo<<<EOT
                                {$xml_value['key_to']}|{$xml_value['key_ts']}|{$xml_value['rfc']}|||||{$xml_value['compIppSubTot']}|||||||||||||||
                                \n\r
                                EOT;*/
                                print "
                                
	                                <tr role='row'>
	                                	<td style='text-align: center;'>".$xml_value['key_to']."</td>
	                                	<td style='text-align: center;'></td>


	                                	<td style='text-align: center;'>".$xml_value['key_ts']."</td>
	                                	<td style='text-align: center;'>".$xml_value['key_ts']."=Otros</td>
	                                	<td style='text-align: center;'>".$xml_value['rfc']."</td>
	                                	<td style='text-align: center;'>".$xml_value['id_fiscal']."</td>
	                                	<td style='text-align: center;'>".$xml_value['nombre_extranjero']."</td>
	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'>".number_format($xml_value['compIppSubTot'],2)."</td>
	                                	<td style='text-align: center;'>".number_format($xml_value['compIppSubTot'],2)."</td>
	                                	<td style='text-align: center;'>".number_format($xml_value['compIppSubTot'],2)."</td>
	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>



	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>




	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>


	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'></td>

	                                	<td style='text-align: center;'  ></td>
	                                	<td style='text-align: center;'  ></td>
	                                	<td style='text-align: center;'  ></td>

	                                	<td style='text-align: center;'></td>
	                                	<td style='text-align: center;'>".number_format($xml_value['compIppTot'],2)."</td>
	                                	<td style='text-align: center;'></td>
	                                </tr>
	                            
                                ";


                                $xml_value['compIppSubTot'] = $xml_value['compIppSubTot'] == 0 ?  '':$xml_value['compIppSubTot'];
                                $xml_value['compIppTot']    = $xml_value['compIppTot'] == 0 ?     '':$xml_value['compIppTot'];



                                /*echo $xml_value['key_to'].'|'.$xml_value['key_ts'].'|'.$xml_value['rfc'].'|'.$xml_value['id_fiscal'].'|'.$xml_value['nombre_extranjero'].'|||'.$xml_value['compIppSubTot'].'||||||||||||'.$xml_value['compIppTot'].'|||';
                                    if ($xml_key<(sizeof($xml_array)-1)) 
                                        echo PHP_EOL;*/
                            }
                            print "</tbody>";

                        ?>
                        </table>
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


<script src="<?php echo $url[0] . '../vendors/jquery/dist/jquery.min.js' ?>"></script>
<!-- Bootstrap -->
<script src="<?php echo $url[0] . '../vendors/bootstrap/dist/js/bootstrap.min.js' ?>"></script>
<!-- FastClick -->
<script src="<?php echo $url[0] . '../vendors/fastclick/lib/fastclick.js' ?>"></script>

<script src="<?php echo $url[0] . '../vendors/iCheck/icheck.min.js' ?>"></script>

<!-- Select2 -->
<script src="<?php echo $url[0] . '../vendors/select2/dist/js/select2.full.min.js' ?>"></script>
<script src="<?php echo $url[0] . 'js/app_select.js' ?>"></script>
<script src="<?php echo $url[0] . 'js/asientos.js' ?>"></script>
<script src="<?php echo $url[0] . 'js/polizas_tools.js' ?>"></script>

<!-- Custom Theme Scripts -->
<script src="<?php echo $url[0] . '../build/js/custom.min.js' ?>"></script>

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


<!-- JPaginate -->
<script src="<?php echo $url[0] . '../vendors/jpaginate/jquery.paginate.js' ?>"></script>


<!-- /Select2 -->

</body>
</html>
