<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once $url[0]."class/admin.class.php";
require_once $url[0]."class/fact_clie_pendientes.class.php";

$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);

if(isset($_POST['factid'])){
    $fact=new FacPolizaPendiente();
    $valordebeBanco=0;
    $tva=0;
    $total=0;
    $nombre="Ref: ";
    $totalFacturas = count($_POST['factid']);
    if($totalFacturas>1){
        foreach($_POST['factid'] as $factid){

            $vfac               =$fact->get_info_facture($factid);
            $valordebeBanco     +=($vfac['tva']+$vfac['total']);
            $tva                +=$vfac['tva'];
            $total              +=$vfac['total'];
            $nombre             =" ".$vfac['nom'];
            $banco_rel          =$fact->cuenta_banco_rel($vfac['rowid']);

            print "<input type='hidden' id='idSociete' name='idSociete[]' value='".$vfac['fk_soc']."-".$vfac['rowid']."-".$vfac['type']."'>";
            print "<label>".$vfac['nom']."</label>";
            print "<table class='table table-striped'>";
                print "<tbody>";
                    print "<tr>";
                        print "<td>Ref.</td>";
                        print "<td>".$vfac['facnumber']."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Fecha de factura</td>";
                        print "<td id='FechaFactura'>".date("m/d/Y", strtotime($vfac['datef']))."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Base imponible</td>";
                        print "<td>".$moneda." ".number_format($vfac['total'],2)."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Importe IVA</td>";
                        print "<td>".$moneda." ".number_format($vfac['tva'],2)."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Importe total</td>";
                        print "<td>".$moneda." ".number_format($vfac['total_ttc'],2)."</td>";
                    print "</tr>";
                print "</tbody>";
            print "</table>";

            print "<input type='hidden' name='factidRecuperado[]' value='".$factid."'>";
        }
    }elseif ($totalFacturas==1){
      $idFact=(int)$_POST['factid'][0];
      if($idFact == 0){
        exit("<h1>No ha seleccionado Factura/s </h1>");
      }

        $vfac=$fact->get_info_facture($idFact);
        $valordebeBanco+=($vfac['tva']+$vfac['total']);

        $tva+=$vfac['tva'];
        $total+=$vfac['total'];
        $nombre.=" ".$vfac['nom'];
        $fact=new FacPolizaPendiente();

        $banco_rel= $fact->cuenta_banco_rel($vfac['rowid']);
        print "<input type='hidden' id='idSociete' name='idSociete[]' value='".$vfac['fk_soc']."-".$vfac['rowid']."-".$vfac['type']."'>";
        print "<table class='table table-striped'>";
            print "<tbody>";
                print "<tr>";
                    print "<td>Ref.</td>";
                    print "<td>".$vfac['facnumber']."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Empresa</td>";
                    print "<td>".$vfac['nom']."</td>";
                print "</tr>";
                print "<tr>";
                    if($vfac['type']==0){
                        $tipo="Estándar";
                    }else{
                        if($vfac['type']==1){
                            $tipo="Rectificativa";
                        }else{
                            if($vfac['type']==2){
                                $tipo="Nota de Crédito";
                            }else{
                                if($vfac['type']==3){
                                    $tipo="De anticipo";
                                }else{
                                    $tipo="";
                                }
                            }
                        }
                    }
                    print "<td>Tipo</td>";
                    print "<td>".$tipo."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Fecha de factura</td>";
                    print "<td id='FechaFactura'>".date("m/d/Y", strtotime($vfac['datef']))."</td>";
                print "</tr>";
                print "<tr>";
                    $condpag=$fact->get_cond_reglement($vfac['fk_cond_reglement']);
                    $pagcondicion=$fact->convierte_condiciones($condpag['libelle']);
                    print "<td>Condiciones de pago</td>";
                    print "<td>".$pagcondicion."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Fecha limite de pago</td>";
                    print "<td>".$vfac['date_lim_reglement']."</td>";
                print "</tr>";
                print "<tr>";
                    $formp=$fact->get_paimenet($vfac['fk_mode_reglement']);
                    $pagoform=$fact->convierte_pagos($formp['libelle']);
                    print "<td>Forma de pago</td>";
                    print "<td>".$pagoform."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Base imponible</td>";
                    print "<td>".$moneda." ".number_format($vfac['total'],2)."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Importe IVA</td>";
                    print "<td >".$moneda." ".number_format($vfac['tva'],2)."</td>";
                print "</tr>";
                print "<tr>";
                    print "<td>Importe total</td>";
                    print "<td>".$moneda." ".number_format($vfac['total_ttc'],2)."</td>";
                print "</tr>";
                print "<tr>";
                    if($vfac['fk_statut']==0){
                        $statut="Borrador";
                    }else{
                        if($vfac['fk_statut']==1){
                            $statut="Validado";
                        }else{
                            if($vfac['fk_statut']==2){
                                $statut="Pagado";
                            }else{
                                if($vfac['fk_statut']==3){
                                    $statut="Abandonado";
                                }else{
                                    $statut="";
                                }
                            }
                        }
                    }
                    print "<td>Estado</td>";
                    print "<td>".$statut."</td>";
                print "</tr>";
            print "</tbody>";
        print "</table>";

        $facpago=$fact->get_pagos_facture($idFact);
        //print_r($facpago);
        print "<table class='table table-striped'>";
        print "<thead>";
        print "<tr>";
        print "<td colspan='3'>Pagos de la Factura</td>";
        print "</tr>";
        print "<tr>";
        print "<td>Fecha de Pago</td>";
        print "<td>Modo de Pago</td>";
        print "<td>Pagado</td>";
        print "</tr>";
        print "</thead>";
        print "<tbody>";
        $cont=count($facpago);
        if($cont==0){
            print "<tr>";
            print "<td colspan='3'>No se han realizado pagos</td>";
            print "</tr>";
        }else{
            $m=0;
            while($m<$cont){
                print "<tr>";
                print "<td>".$facpago[$m]['datep']."</td>";
                $pagoform2=$fact->convierte_pagos($facpago[$m]['libelle']);
                print "<td>".$pagoform2."</td>";
                print "<td>".$moneda." ".number_format($facpago[$m]['amount'],2)."</td>";
                print "</tr>";
                $m++;
            }
        }
        print "</tbody>";
        print "</table>";
        print "<input type='hidden' name='factidRecuperado[]' value='".$idFact."'>";


    }
    print "<input type='hidden' id='td_facnumber' value='".$nombre."'>";
    print "<input type='hidden' id='ttc' value='".number_format($valordebeBanco,2)."'>"; //ttc
    print "<input type='hidden' id='tva' value='".number_format($tva,2)."'>";
    print "<input type='hidden' id='ht' value='".number_format($total,2)."'>";
    if(is_object($banco_rel)){
        print "<input type='hidden' id='cuenta_banco_rel_fact' value='".$banco_rel->codagr."'>";
    }else{
        print "<input type='hidden' id='cuenta_banco_rel_fact' value='0'>";
    }

}else{
  print("<h1>No ha seleccionado Factura/s </h1>");
}
