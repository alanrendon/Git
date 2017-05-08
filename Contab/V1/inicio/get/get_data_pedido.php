<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once ($url[0]."class/commande_fournisseur.class.php");
require_once $url[0]."class/admin.class.php";

$trans     = new Commande_Fournisseur();
$eDolibarr = new admin();
$total_ht  = 0;
$total_tv  = 0;
$total_ttc = 0;
$valores   = $eDolibarr->get_user();
$moneda    = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);


if(isset($_POST['pedidoid'])){

    $nombre="Ref: ";
    $totalFacturas = count($_POST['pedidoid']);
    if($totalFacturas>1){
        foreach($_POST['pedidoid'] as $factid){
            $trans->id =  $idFact;
            $vfac      =  $trans->get_data_order_id();
            $nombre    .= "  ".$vfac->ref;
             
        
            print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Referencia</td>";
                            print "<td>".$vfac->ref."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Modo de pago</td>";
                            print "<td>".$vfac->fk_mode_reglement."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha recepcición</td>";
                            print "<td>".$vfac->date_commande."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Subtotal</td>";
                            print "<td>".$moneda . " " . number_format($vfac->total_ht, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>IVA</td>";
                            print "<td >".$moneda . " " . number_format($vfac->tva, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Total</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ttc, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
            
            print "<input type='hidden' id='idSociete' name='idSociete[]' value='11-".$vfac->fk_soc."'>";
           
            print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->rowid."'>";
            
                $total_ht +=$vfac->total_ht;
                $total_tv +=$vfac->tva;
                $total_ttc +=$vfac->total_ttc;
        }
         print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y")."'>";
    }elseif ($totalFacturas==1){

        $nombre    ="Ref: ";
        $idFact    =(int)$_POST['pedidoid'][0];
        $trans->id = $idFact;
        $vfac      =$trans->get_data_order_id();
        $nombre    .=" ".$vfac->ref;

         print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Referencia</td>";
                            print "<td>".$vfac->ref."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Forma de pago</td>";
                            print "<td>".$vfac->fk_mode_reglement."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha aprobación</td>";
                            print "<td>".$vfac->date_commande."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >Subtotal</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ht, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >IVA</td>";
                            print "<td >".$moneda . " " . number_format($vfac->tva, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >Total</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ttc, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
        $total_ht +=$vfac->total_ht;
        $total_tv +=$vfac->tva;
        $total_ttc +=$vfac->total_ttc;
        print "<input type='hidden' id='idSociete' name='idSociete[]' value='11-".$vfac->fk_soc."'>";
        print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y", strtotime($vfac->date_commande))."'>";
        print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->rowid."'>";

    }
    
    
    print "<input type='hidden' id='td_facnumber' value='".$nombre."'>";
    print "<input type='hidden' id='table_total_ht' value='".$total_ht."'>";
    print "<input type='hidden' id='table_total_tva' value='".$total_tv."'>";
    print "<input type='hidden' id='table_total_ttc' value='".$total_ttc."'>";
   
}else{
    print("<h1>No ha seleccionado Factura/s </h1>");
}

