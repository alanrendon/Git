<?php

$url[0] = "../";
$url[1] = "../configuracion/";
$url[2] = "../asignacion/";
$url[3] = "../cuentas/";
$url[4] = "../polizas/";
$url[5] = "../conf/";
$url[6] = "../periodos/";
$url[7] = "../reportes/";

require_once "../class/poliza.class.php";
require_once "../class/asiento.class.php";
require_once "../class/cat_cuentas.class.php";
require_once "../class/periodo.class.php";


$polizas = new Poliza();
$asiento = new Asiento();
$cuenta = new Cuenta();
$periodo = new Periodo();
$tipo = '';
$anio = 0;
$mes = 0;
$por_factura = 0;
$por_recurrente = 0;

if  ( (isset($_GET['tipo']) && !empty($_GET['tipo']) ) || (isset($_GET['anio']) && !empty($_GET['anio'])) || (isset($_GET['mes']) && !empty($_GET['mes'])) || (isset($_GET['por_factura']) && !empty($_GET['por_factura'])) || (isset($_GET['por_recurrente']) && !empty($_GET['por_recurrente']))) {
    $tipo            = (isset($_GET['tipo'])) ? $_GET['tipo'] : '0';
    $anio            = (isset($_GET['anio'])) ? $_GET['anio'] : '0';
    $mes             = (isset($_GET['mes'])) ? $_GET['mes'] : '0';
    $por_factura     = (isset($_GET['por_factura'])) ? $_GET['por_factura'] : '0';
    $por_recurrente  = (isset($_GET['por_recurrente'])) ? $_GET['por_recurrente'] : '0';
    $arreglo_Polizas = $polizas->getPolizasFiltros($anio, $mes, $tipo, $por_factura, $por_recurrente);

} else if (isset($_GET['id'])) {
    $id = ($_GET['id']) ? $_GET['id'] : '';
    if ($id > 0) 
        $arreglo_Polizas = $polizas->getPolizaId($id);
    
} else {
    $arreglo_Polizas = $polizas->getPolizas();
}

$arreglo_cuentas = $cuenta->get_cuentas();
$periodos        = $periodo->get_all_Periodos_anio();
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
                                <h2>Consulta y registro</h2>
                            <?php endif ?>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php if (!isset($id) || $id == '') { ?>
                                <div class="form-group">
                                    <form action="consulta.php" method="get" >
                                    <div class="row">
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                          Tipo
                                            <select class="select2_single form-control" tabindex="-1" name="tipo">
                                                <option></option>
                                                <option value="T" <?php print ($tipo == 'T') ? 'selected' : ''; ?>>Todas</option>
                                                <option value="D" <?php print ($tipo == 'D') ? 'selected' : ''; ?>>Diario</option>
                                                <option value="E" <?php print ($tipo == 'E') ? 'selected' : ''; ?>>Egreso</option>
                                                <option value="C" <?php print ($tipo == 'C') ? 'selected' : ''; ?>>Cheque</option>
                                                <option value="I" <?php print ($tipo == 'I') ? 'selected' : ''; ?>>Ingreso</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                          Año
                                            <select class="select2_single form-control" tabindex="-1" name="anio">
                                                <option></option>
                                                <?php foreach ($periodos as $p): ?>
                                                    <option value="<?php echo $p->anio; ?>" <?php print ($anio == $p->anio) ? 'selected' : ''; ?>><?php echo $p->anio; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                            Mes
                                            <select class="select2_single form-control" tabindex="-1" name="mes">
                                                <option></option>
                                                <option value="01" <?php print ($mes == '01') ? 'selected' : ''; ?>>Enero</option>
                                                <option value="02" <?php print ($mes == '02') ? 'selected' : ''; ?>>Febrero</option>
                                                <option value="03" <?php print ($mes == '03') ? 'selected' : ''; ?>>Marzo</option>
                                                <option value="04" <?php print ($mes == '04') ? 'selected' : ''; ?>>Abril</option>
                                                <option value="05" <?php print ($mes == '05') ? 'selected' : ''; ?>>Mayo</option>
                                                <option value="06" <?php print ($mes == '06') ? 'selected' : ''; ?>>Junio</option>
                                                <option value="07" <?php print ($mes == '07') ? 'selected' : ''; ?>>Julio</option>
                                                <option value="08" <?php print ($mes == '08') ? 'selected' : ''; ?>>Agosto</option>
                                                <option value="09" <?php print ($mes == '09') ? 'selected' : ''; ?>>Septiembre</option>
                                                <option value="10" <?php print ($mes == '10') ? 'selected' : ''; ?>>Octubre</option>
                                                <option value="11" <?php print ($mes == '11') ? 'selected' : ''; ?>>Noviembre</option>
                                                <option value="12" <?php print ($mes == '12') ? 'selected' : ''; ?>>Diciembre</option>
                                                <option value="13" <?php print ($mes == '13') ? 'selected' : ''; ?>>Ajuste</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                           Por factura
                                            <select class="select2_single form-control" tabindex="-1" name="por_factura">
                                                <option></option>
                                                <option value="2" <?php print ($por_factura == '2') ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php print ($por_factura == '1') ? 'selected' : ''; ?>>Si</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                           Recurrentes
                                            <select class="select2_single form-control" tabindex="-1" name="por_recurrente">
                                                <option></option>
                                                <option value="1" <?php print ($por_recurrente == '1') ? 'selected' : ''; ?>>Sí</option>
                                                <option value="2" <?php print ($por_recurrente == '2') ? 'selected' : ''; ?>>No</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>
                                                Modo Descriptivo
                                                <input name="tipo_vista" class="form-control pull-lef" <?= (isset($_GET['tipo_vista']) && $_GET['tipo_vista']==1) ?  'checked':  ''; ?>  type="checkbox" value="1" id="tipo_vista">
                                            </label>
								        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label>&nbsp;</label><br />
                                            <button class="btn btn-success pull-left" type="submit"> Consultar</button>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <br />
                            <?php } ?>
                            
                            <?php if(isset($_GET['tipo_vista']) && $_GET['tipo_vista']==1): ?>
                                
                                 <div id="dv_dibujarPolizas" class="row">
                                <?php if (count($arreglo_Polizas)>0) {
                                    $count = 0; ?>
                                         <div class="row">
                                            <div class="col-sm-12">
                                                <div class="paginas"></div>
                                            </div>
                                        </div>
                                    <?php
                                        $count2 = 0;
                                        foreach ($arreglo_Polizas as $key): $count2++;

                                            if ($key['societe_type'] == 1) {
                                                $tipoDoctorelacionado = $key['facnumber'];
                                            } else if ($key['societe_type'] == 2) {
                                                $tipoDoctorelacionado = $key['ref'];
                                            } else {
                                                $tipoDoctorelacionado = "No hay docto.";
                                            }
                                            $ajuste = $key['ajuste']==1 ?  'Ajuste ':'';
                                            $cantidad_docto = $polizas->get_info_docto($key['id']);

                                            if ($count2 == 1): $count++;
                                         ?>
                                            <div id='p<?php echo $count; ?>' style="display:none;">
                                            <?php endif ?>
                                                <div class="col-md-12 col-xs-12 col-lg-12" >
                                                    <div class=" x_panel" style="border: 1px solid #c2c2c2 !important;">
                                                        <div class="x_title">
                                                            <h2>Póliza de <?php echo $ajuste;  ?><?php echo $key['tipo_pol'] . ": " . $key['cons']; ?></h2>
                                                            <ul class="nav navbar-right panel_toolbox">
                                                                <li><a class="collapse-link" title="Minimizar"><i class="fa fa-chevron-up" style="color: black"></i></a></li>
                                                                <?php if ($key['fecha'] != '0000-00-00'): ?>
                                                                    <li><a class="download-link" title="Descargar" href="../get/get_poliza_xls.php?descarga=<?php echo $key['id']; ?>" target="_blank"> <i class="fa fa-file-excel-o " style="color: black"></i></a></li>
                                                                    <li><a class="download-link" title="Descargar" href="../get/get_poliza_pdf.php?descarga=<?php echo $key['id']; ?>" target="_blank"> <i class="fa fa-file-pdf-o " style="color: black"></i></a></li>
                                                                <?php endif ?>
                                                                <?php if ($key['recurente'] == 0 && $key['fecha'] != '0000-00-00') { ?>
                                                                    <li><a class="recycle-link" title="Recurrente" idpoliza="<?php echo $key['id']; ?>"> <i class="fa fa-copy" style="color: black"></i></a></li>
                                                                <?php } elseif ($key['recurente'] != -1 && $key['fecha'] != '0000-00-00') { ?>
                                                                    <li><a class="clonar-link" title="Clonar Póliza" idpoliza="<?php echo $key['id']; ?>"> <i class="fa fa-link" style="color: black"></i></a></li>
                                                                    <li><a class="remove-recurrente-link" title="Remover recurrente" idpoliza="<?php echo $key['id']; ?>"> <i class="fa fa-chain-broken" style="color: black"></i></a></li>
                                                                <?php } else if($key['fecha'] != '0000-00-00') { ?>
                                                                    <li><a class="ban-link" title="Póliza clonada" idpoliza="<?php echo $key['id']; ?>"> <i class="fa fa-ban" style="color: red"></i></a></li>
                                                                <?php } ?>
                                                                <?php if ($key['estado'] != 0): ?>
                                                                    <?php if ($key['fecha'] != '0000-00-00'): ?>
                                                                    <li><a class="edit-link" title="Editar" href="edit.php?id=<?php echo $key['id']; ?>"><i class="fa fa-edit" style="color: black"></i></a></li>
                                                                     <?php endif ?>
                                                                    <li><a class="delete-link" title="Borrar" idpoliza="<?php echo $key['id']; ?>"><i class="fa fa-trash" style="color: black"></i></a></li>
                                                                <?php endif ?>
                                                                <?php if($key['fecha'] != '0000-00-00') { ?>
                                                                         <li>

                                                                            <a title="Cargar Archivo" href="carga_archivo.php?idp=<?=$key['id']?>&ntipo_pol=<?=$key['tipo_pol']?>&tipo_pol=<?=$key['tipol_l']?>&cons=<?=$key['npol']?>&anio=<?=$anio?>&mes=<?=$mes?>&tipo=<?=$tipo?>&por_factura=<?=$por_factura?>&por_recurrente=<?=$por_recurrente?>">
                                                                                 <i class="fa fa-cloud-upload" style="color: black"></i>
                                                                             </a>
                                                                               <span class="c"><?php echo $cantidad_docto->row;?></span>
                                                                        </li>
                                                                 <?php } ?>
                                                            </ul>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content">
                                                            <table class="table table-striped">
                                                                <tbody>
                                                                    <tr>
                                                                        <th width="30%">Concepto: <?php echo  ($key['concepto']); ?></th>
                                                                        <th width="20%">
                                                                            <?php if ( $key['fecha'] == '0000-00-00'): ?>
                                                                                 Fecha: N/A
                                                                            <?php else: ?>
                                                                                Fecha: <?php echo $key['fecha']; ?>

                                                                            <?php endif ?>
                                                                        </th>
                                                                        <th></th>
                                                                        <th>Tipo de pago: <?php echo $key['paiment']; ?></th>
                                                                        <th width="20%">
                                                                            Documento(s) Relacionado: 
                                                                            <?php if ($relacion_fact = $polizas->get_info_factures($key['id'])): ?>
                                                                                   <?php foreach ($relacion_fact as $llave): ?>
                                                                                        <?php echo $llave->ref.'<br>'; ?>
                                                                                   <?php endforeach ?>
                                                                            <?php endif ?>
                                                                        </th>
                                                                        <th></th>
                                                                    </tr>
                                                                    <?php if(!empty ($key['comentario'])) : ?>
                                                                        <tr>
                                                                            <th colspan="6">Comentario: <br>  <p><?php echo  ($key['comentario']); ?> </p></th>

                                                                        </tr>
                                                                    <?php endif ?>
                                                                    <?php if ($key['paiment'] == 'Cheque') { ?>
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
                                                            if ($arrasiento) {
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
                                                                            foreach ($arrasiento as $key2):
                                                                                $total_debe += $key2["debe"];
                                                                                $total_habe += $key2["haber"];
                                                                                ?>
                                                                            <tr>
                                                                                <td align="center"><?php echo $key2["asiento"]; ?></td>
                                                                                <td>
                                                                                    <?php
                                                                                    $nom_cuenta = $cuenta->get_nom_cuenta($key2["cuenta"]);
                                                                                    echo $key2["cuenta"]. ' - ' . $nom_cuenta;
                                                                                    ?>
                                                                                </td>
                                                                                 <td>
                                                                                    <?php echo $key2["descripcion"]; ?>
                                                                                </td>
                                                                                <td align="right"><?= $moneda ?> <?php echo number_format($key2["debe"], 2, '.', ','); ?></td>
                                                                                <td align="right"><?= $moneda ?> <?php echo number_format($key2["haber"], 2, '.', ','); ?></td>
                                                                                <td class="last">
                                                                                    <ul class="nav left panel_toolbox" >
                                                                                        <?php if ($key['estado'] != 0): ?>
                                                                                            <li><a class="editAsiento-link" title="Editar" idasiento="<?php echo $key2["rowid"]; ?>" ><i class="fa fa-edit" style="color: black"></i></a></li>
                                                                                            <li><a class="deleteAsiento-link" title="Borrar" idasiento="<?php echo $key2["rowid"]; ?>"><i class="fa fa-trash" style="color: black"></i></a></li>
                                                                                        <?php endif ?>
                                                                                    </ul>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach ?>
                                                                        <tr>
                                                                            <td colspan="2"><strong>Total</strong></td>
                                                                             <td></td>
                                                                            <td align="right"><strong><?= $moneda ?> <?php print number_format($total_debe, 2, '.', ','); ?></strong></td>
                                                                            <td align="right"><strong><?= $moneda ?> <?php print number_format($total_habe, 2, '.', ','); ?></strong></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <?php if (number_format($total_debe, 2, '.', ',') != number_format($total_habe, 2, '.', ',')) { ?>
                                                                            <tr>
                                                                                <td colspan="2"></td>
                                                                                <td colspan="4"><span style="color:red">Los totales no coinciden, favor de verificar.</span></td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                                <?php if ($key['estado'] != 0 && $key['fecha'] != '0000-00-00'): ?>
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
                                                                <?php endif ?>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php if ($count2 == 3): ?>
                                                <?php $count2 = 0; ?>
                                                </div>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                               
                                    <?php } else {
                                        print 'No se encontraron resultados.';
                                    } ?>
                                    <input type="hidden" value ='<?php echo $count; ?>' id="cantidad">

                                </div>
                            <?php else: ?>
                                <table id="datatable" class="table table-striped jambo_table bulk_action">
                                  <thead>
                                    <tr>
                                      <th align="center">Tipo</th>
                                      <th align="center">Número</th>
                                      <th align="center">Concepto</th>
                                      <th align="center">Fecha</th>
                                      <th align="center">Total Debe</th>
                                      <th align="center">Total Haber</th>
                                      <th align="center">Ver Detallada</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach($arreglo_Polizas as $key): ?>
                                        <?php $ajuste = $key['ajuste']==1 ?  'Ajuste ':''; ?>
                                        <?php $arrasiento = $asiento->get_asientoPoliza($key['id']); ?>
                                        
                                        <?php $total_debe=0; ?>
                                        <?php $total_habe=0; ?>
                                        
                                        <?php foreach ($arrasiento as $key2): ?>
                                                <?php $total_debe += $key2["debe"]; ?>
                                                <?php $total_habe += $key2["haber"]; ?>
                                         <?php endforeach ?>
                                         <tr>
                                              <td  scope="row"  align="left"><?php echo $ajuste.' '.$key['tipo_pol']; ?></td>
                                              <td  scope="row"  align="left"><?php echo $key['cons']; ?></td>
                                              <td  scope="row"  align="left"><?php echo $key['concepto']; ?></td>
                                              <td  scope="row"  align="left">
                                                   <?php if ( $key['fecha'] == '0000-00-00'): ?>
                                                            No aplica.
                                                    <?php else: ?>
                                                            <?php echo $key['fecha']; ?>
                                                    <?php endif ?>
                                              </td>
                                               <td  scope="row"  align="center">
                                                   <strong>
                                                       <?= $moneda ?> <?php print number_format($total_debe, 2, '.', ','); ?>
                                                    </strong>
                                               </td>
                                               <td  scope="row"  align="center">
                                                   <strong>
                                                       <?= $moneda ?> <?php print number_format($total_habe, 2, '.', ','); ?>
                                                    </strong>
                                                </td>
                                              <td  scope="row"  align="center">
                                                  <a href="consulta.php?tipo_vista=1&id=<?= $key['id'] ?>" target="_blank">Ver</a>
                                              </td>
                                        </tr>
                                    <?php endforeach ?>
                                   
                                  </tbody>
                                </table>
                            <?php endif ?>
                  
                            <div id="add_modalAsiento" class="modal fade " role="dialog">
                                <div class="modal-dialog" style="width: 60% !important;">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Registro de cuenta</h4>
                                        </div>
                                        <form id="frm_putAsiento" accept-charset="utf-8" data-parsley-validate class="form-horizontal form-label-left">
                                             <input type="hidden" name="id_poliza" id="id_poliza">
                                            <div class="modal-body" id="div_get_update_asiento">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Actualizar</button>
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

<script type="text/javascript">
    iniciaSelect();
    $(document).ready(function () {
        $(".a_agregar").click(AgregarAsiento);
        $(".deleteAsiento-link").click(eliminarAsiento);
        $(".editAsiento-link").click(abrirModalUpdate);
        $(".back-edit").click(regresar);
        $("#frm_putAsiento").submit(updateAsiento);
        $("#tipo_vista").change(cambiar_vista);
        
        $(".delete-link").click(eliminarPoliza);
        $(".recycle-link").click(recuerentePoliza);
        $(".clonar-link").click(clonarPoliza);
        $(".remove-recurrente-link").click(removerRecurrente);
        $( '#datatable' ).DataTable({
			"aoColumnDefs": [{ 'bSortable': false, 'aTargets': [ 3] }],
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
    });


    function cambiar_vista(){
      $(this).parents("form:first").submit();
    }
    
    function regresar() {
        location.replace('template.php');
    }

    $('.a_guardar_ctas').on("click", function () {
        $padre = $(this).parent().parent().parent().parent().parent();
        $form = $padre.find('form:first');
        if (confirm("¿Está seguro de agregar los asientos a la póliza?")) {
            $.ajax({
                url: "../put/put_asientos.php",
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.mensaje) {
                        alert(data.mensaje);
                    } else {
                        alert('Se han agregado los asientos.');
                        location.reload(true);

                    }
                }
            });
        }
        return false;
    });

    function AgregarAsiento() {
        $padre = $(this).parent().parent().parent();
        $padre2 = $padre.parent();
        $concepto = $padre2.find('th:first');

        if ($concepto) {
            $.ajax({
                url: "../get/div_asientos.php",
                type: 'POST',
                data: {"concepto": $concepto.html()},
                dataType: 'html',
                success: function (data) {
                    $dv_Asientos = $padre.find('div:first');
                    //$a_last = $padre.find('a:last');

                    $dv_Asientos.append(data);
                    $(".slc_cuenta").change(AgregarCuentaInput);
                    iniciaSelect();
                    $(".txt_debe").change(inputSumatxt_debe);
                    $(".txt_haber").change(inputSumatxt_haber);
          
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
                var numItems = $('.txt_haber').length;

                if ( (parseFloat($input_haber.val()) == parseFloat($input_debe.val())) || numItems==1) {
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
       // $("#txt_haber").val("0.0");
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
        $("#div_get_update_asiento").html();
        
        $("#id_poliza").val(idPoliza);
        get_update_asiento();
        $('#add_modalAsiento').modal();
        iniciaSelect();
    }
    
    function get_update_asiento() {
        $.ajax({
            url: "../get/get_update_asiento.php",
            type: 'POST',
            data: {"asiento":  $("#id_poliza").val()},
            dataType: 'html',
            success: function (data) {
                $("#div_get_update_asiento").html(data);
                $(".select2_asiento").select2({
                  });
            }
        });
    }

    $(".paginas").paginate({
        count: $('#cantidad').val(),
        start: 1,
        display: 10,
        border: true,
        text_color: 'rgb(42, 63, 84)',
        background_color: 'nonnw',
        text_hover_color: '#2573AF',
        background_hover_color: 'none',
        images: true,
        mouse: 'press',
        onChange: function (page) {
            $('._current', '#dv_dibujarPolizas').removeClass('_current').hide();
            $('#p' + page).addClass('_current').show();
            $('html, body').animate({scrollTop:0}, 'slow');
        }
    });

    $(document).ready(function () {
        $('#p1').addClass('_current').show();

    });

</script>
<!-- /Select2 -->

</body>
</html>
