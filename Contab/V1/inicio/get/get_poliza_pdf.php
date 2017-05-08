<?php
    ob_start();
	$url[0] = "../";
	require_once "../class/poliza.class.php";
	require_once "../class/asiento.class.php";
    require_once("../class/admin.class.php");
    require_once "../class/cat_cuentas.class.php";
    require_once "../class/logo.class.php";
    require "../class/html2pdf/html2pdf.class.php";

    if (isset($_GET['descarga'])) {
                $idPoliza        = (int)$_GET['descarga'];
                $polizas         = new Poliza();
                $asiento         = new Asiento();
                $cuenta          = new Cuenta();
                $logo            = new Logo();
                $eDolibarr       = new admin();
                $html2pdf        = new HTML2PDF('P', 'A4', 'es');
                $valores         = $eDolibarr->get_user();
                $moneda          = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);
                $arreglo_Polizas = $polizas->getPolizaId($idPoliza);

			 if ($arreglo_Polizas>0) {
		
                $arrasiento = $asiento->get_asientoPoliza($idPoliza);
                $filename   = "poliza_".$idPoliza."_" . date('Ymd') . ".pdf";
                foreach($arreglo_Polizas as $poliza){
                    $object = (object) $poliza;
                ?>
                
                    <style type="text/css">
                            table.table_border{
                                background-color: #F9F9F9; 
                                border-top: solid 0.1mm #949fa9; 
                                padding: 2mm;
                                font-size: 10px;
                                width: 100%; 
                                color: #73879C;
                            }
                             table.table_border_asientos{
                                background-color: #405467; 
                                border-top: solid 0.1mm #949fa9; 
                                padding: 2mm;
                                font-size: 10px;
                                width: 100%; 
                                color: #FFFFFF;
                            }
                            table.table_blank{
                                background-color: #FFFFFF; 
                                border-top: solid 0.1mm #949fa9;
                                border-bottom: solid 0.1mm #949fa9; 
                                padding: 2mm;
                                font-size: 10px;
                                width: 100%; 

                            }
                            table.table_blank_space{
                                background-color: #FFFFFF; 
                                border-top: solid 0.1mm #949fa9;
                                border-bottom: solid 0.1mm #949fa9; 
                                padding: 2mm;
                                font-size: 9;
                                width: 100%; 
                                color: #666666;

                            }

                            table.page_header, table.page_footer {
                                width: 100%; 
                                border: none; 
                                padding: 1mm;
                                font-size: 10px;
                                color:#2A3F54;
                                font-weight: bold;
                                   
                            }

                    </style>
 
                    <page backtop="10mm" backbottom="50mm" backleft="7mm" backright="10mm">  
                        <page_header > 
                            <table class="page_header">
                                <tr>
                                    <td style="width: 100%; text-align: right; ">
                                        <?php if ($logo = $logo->get_logo()): ?>
                                            <img src=" <?php echo $logo ;?>" >                           
                                        <?php endif ?>                                      
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="width: 100%; text-align: center">
                                        <h4 ><?php  isset($valores['MAIN_INFO_SOCIETE_NOM']) ? print $valores['MAIN_INFO_SOCIETE_NOM']: print 'Sin nombre'; ?></h4>
                                    </td>
                                </tr>

                            </table>
                        </page_header> 
                        <page_footer> 
                            <table class="page_footer">
                                <tr>
                                    <td style="width: 100%; text-align: right">
                                        Página [[page_cu]]/[[page_nb]]
                                    </td>
                                </tr>
                            </table>
                        </page_footer> 
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
 
                        <table class="table_blank">
                                <tbody>
                                    <tr>
                                        <th style="width: 100%;" align="center">Póliza de <?php echo $object->tipo_pol; ?>: <?php echo $object->cons; ?> </th>
                                    </tr>
                                </tbody>
                        </table>
                        <table class="table_border">
                            <tbody>
                                <tr>
                                    <th style="width: 50%;">Concepto: <?php echo $object->concepto ?></th>
                                    <th style="width: 15%; ">
                                         Fecha: <?php echo $object->fecha ?>
                                    </th>
                                    <th></th>
                                    <th  style="width: 15%; ">Tipo de pago: <?php echo  $object->nombre_pago; ?></th>
                                    <th  style="width: 20%;">
                                        Documento Relacionado: <br>  
                                            <?php if ($relacion_fact = $polizas->get_info_factures($object->id)): ?>
                                                <?php foreach ($relacion_fact as $llave): ?>
                                                    <?php echo $llave->ref.'<br>'; ?>
                                                <?php endforeach ?>
                                            <?php else: ?>
                                                    N/A
                                            <?php endif ?>                                                         
                                    </th>
                                    <th></th>
                                </tr>
                            </tbody>
                        </table>
                        <?php if ($object->comentario): ?>
                            <table class="table_blank">
                                <tbody>
                                    <tr>
                                        <th style="width: 100%;" align="justify">Comentario: <br>  <p> <?php echo $object->comentario; ?> </p></th>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif ?>

                          <?php if ( $object->nombre_pago == 'Cheque') { ?>
                            <table class="table_border">
                                <tbody>
                                    <tr>
                                        <th style="width: 50%;" >
                                            Cheque a Nombre:  <br>
                                            <?php echo(  $object->anombrede); ?>
                                        </th>
                                        <td style="width: 50%;">
                                            Num. Cheque:  <br>
                                            <?php echo  $object->numcheque; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php } ?>
                       <br>
                       <table class="table_border_asientos">
                            <tbody >
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: 25%;">Cuenta</th>
                                    <th style="width: 40%;">Descripción</th>
                                    <th style="width: 15%;" align="right">Debe</th>
                                    <th style="width: 15%;" align="right">Haber</th>
                                    
                                </tr>
                            </tbody>
                        </table>
                       <table  class="table_blank_space">
                            <tbody>
                                <?php
                                $arrasiento = $asiento->get_asientoPoliza($object->id);
                                if ($arrasiento) {
                                    $total_debe = 0;
                                    $total_habe = 0;
                                    foreach ($arrasiento as $asiento_row):
                                        $object_asiento = (object) $asiento_row;
                                        $total_debe     += $object_asiento->debe;
                                        $total_habe     += $object_asiento->haber;
                                        ?>
                                        <tr>
                                            <td style="width: 5%;" align="center"><?php echo  $object_asiento->asiento; ?></td>
                                            <td style="width: 25%;">
                                                <?php
                                                $nom_cuenta = $cuenta->get_nom_cuenta( $object_asiento->cuenta);
                                                echo  $object_asiento->cuenta. ' - ' . $nom_cuenta;
                                                ?>
                                            </td>
                                            <td style="width: 40%;">
                                                <?php

                                                echo  $object_asiento->descripcion;
                                                ?>
                                            </td>
                                            <td style="width: 15%;" align="right" ><?= $moneda ?> <?php echo number_format( $object_asiento->debe, 2, '.', ','); ?></td>
                                            <td style="width: 15%;" align="right"><?= $moneda ?> <?php echo number_format( $object_asiento->haber, 2, '.', ','); ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <tr>
                                        <td colspan="2" style="color: #000;"><strong>Total</strong></td>
                                        <td></td>
                                        <td align="right" style="color: #000;"><strong><?= $moneda ?> <?php print number_format($total_debe, 2, '.', ','); ?></strong></td>
                                        <td align="right" style="color: #000;"><strong><?= $moneda ?> <?php print number_format($total_habe, 2, '.', ','); ?></strong></td>
                                        <td></td>
                                    </tr>
                                    <?php if (number_format($total_debe, 2, '.', ',') != number_format($total_habe, 2, '.', ',')) { ?>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="4"><span style="color:#7f0000">Los totales no coinciden, favor de verificar.</span></td>
                                    </tr>
                                    <?php } 
                                }?>
                            </tbody>
                        </table>
                    </page>  
               <?php }
                $html     = ob_get_contents();
                ob_end_clean();
                $html2pdf->writeHTML($html);
                $html2pdf->Output($filename);
                exit();
		}
    }
	
?>