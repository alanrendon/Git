<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once ($url[0]."class/transfer.class.php");
require_once $url[0]."class/admin.class.php";

$trans=new Transfert();

$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);

 $total = 0;
if(isset($_POST['transid'])){

    $nombre="Ref: ";
    $totalFacturas = count($_POST['transid']);
    if($totalFacturas>1){
        foreach($_POST['transid'] as $factid){
            
            $vfac   =$trans->get_transfert_id($factid);
            $vfac   =$vfac[0];
            $nombre.=" ".$vfac['idtransfer'];
            $tpago2 =$trans->get_paimenet_cod($vfac['fk_type']);
            $tpago  =$trans->convierte_pagos($tpago2['libelle']);
            
            $count=$trans->cuenta_bank_rel($vfac['bank']);
            
             print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Fecha Operación.</td>";
                            print "<td>".$vfac['dateo']."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Tipo</td>";
                            print "<td>".$tpago."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Descripción</td>";
                            print "<td>".$vfac['label']."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Cuenta</td>";
                            print "<td>".$vfac['refcuenta']." - ".$vfac['cuenta'] ."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Importe total</td>";
                            print "<td>".$moneda . " " . number_format($vfac['amount'], 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
            print "<input type='hidden' id='idSociete' name='idSociete[]' value='3-".$vfac['idtransfer']."'>";
           
            print "<input type='hidden' name='factidRecuperado[]' value='".$vfac['idtransfer']."'>";
            
             $total += $vfac['amount'];
        }
         print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y")."'>";
    }elseif ($totalFacturas==1){
         $nombre="Ref: ";
         $idFact=(int)$_POST['transid'][0];
         $vfac=$trans->get_transfert_id($idFact);
         $vfac=$vfac[0];
         $tpago2 = $trans->get_paimenet_cod($vfac['fk_type']);
         $tpago = $trans->convierte_pagos($tpago2['libelle']);
        $count=$trans->cuenta_bank_rel($vfac['bank']);
        
        $total = $vfac['amount'];
         print "<table class='table table-striped'>";
                print "<tbody>";
                    print "<tr>";
                        print "<td>Fecha Operación.</td>";
                        print "<td>".$vfac['dateo']."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Tipo</td>";
                        print "<td>".$tpago."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Descripción</td>";
                        print "<td>".$vfac['label']."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Cuenta</td>";
                        print "<td>".$vfac['refcuenta']." - ".$vfac['cuenta'] ."</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td>Importe total</td>";
                        print "<td>".$moneda . " " . number_format($vfac['amount'], 2)."</td>";
                    print "</tr>";
                print "</tbody>";
            print "</table>";
            print "<br /><br />";
       
 
        $nombre.=" ".$vfac['idtransfer'];
        print "<input type='hidden' id='idSociete' name='idSociete[]' value='3-".$vfac['idtransfer']."'>";
        print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y", strtotime($vfac['dateo']))."'>";
        print "<input type='hidden' name='factidRecuperado[]' value='".$vfac['idtransfer']."'>";

    }

    print "<input type='hidden' id='td_facnumber' value='".$nombre."'>";
    print "<input type='hidden' id='cuenta_bak_rel' value='".$count->codagr."'>";
    print "<input type='hidden' id='importe_total_get' value='".number_format($vfac['amount'], 2)."'>";
   
}else{
    print("<h1>No ha seleccionado Factura/s </h1>");
}

