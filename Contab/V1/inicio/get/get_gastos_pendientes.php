<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once ($url[0]."class/gastos.class.php");
require_once $url[0]."class/admin.class.php";

$trans=new Gastos();
$total_ht=0;
$total_tv=0;
$total_ttc=0;

$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);

if(isset($_POST['transid'])){

    $nombre="Ref: ";
    $totalFacturas = count($_POST['transid']);
    if($totalFacturas>1){
        foreach($_POST['transid'] as $factid){
            $vfac=$trans->get_gastos_id($factid);
            $vfac=$vfac[0];
            $nombre.=" ".$vfac->ref;
            switch($vfac->fk_statut){
                case 0:
                    $statut = 'Borrador';
                    break;
                case 2:
                    $statut = 'Validado';
                    break;
                case 4:
                    $statut = 'Cancelado';
                    break;
                case 5:
                    $statut = 'Aprobado';
                    break;
                case 6:
                    $statut = 'Pagado';
                    break;
            }
            print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Referencia</td>";
                            print "<td>".$vfac->ref."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Periodo</td>";
                            print "<td>"."De " .$vfac->date_debut. " a " . $vfac->date_fin."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Estatus</td>";
                            print "<td>".$statut."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha aprobación</td>";
                            print "<td>".$vfac->date_approve."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Subtotal</td>";
                            print "<td>".$moneda . " " . number_format($vfac->total_ht, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>IVA</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_tva, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Total</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ttc, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
            
            print "<input type='hidden' id='idSociete' name='idSociete[]' value='4-".$vfac->idgasto."'>";
           
            print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->idgasto."'>";
            
                $total_ht +=$vfac->total_ht;
                $total_tv +=$vfac->total_tva;
                $total_ttc +=$vfac->total_ttc;
        }
         print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y")."'>";
    }elseif ($totalFacturas==1){
         $nombre="Ref: ";
        $idFact=(int)$_POST['transid'][0];
        $vfac=$trans->get_gastos_id($idFact);
        $vfac=$vfac[0];
 
        $nombre.=" ".$vfac->ref;
        switch($vfac->fk_statut){
                case 0:
                    $statut = 'Borrador';
                    break;
                case 2:
                    $statut = 'Validado';
                    break;
                case 4:
                    $statut = 'Cancelado';
                    break;
                case 5:
                    $statut = 'Aprobado';
                    break;
                case 6:
                    $statut = 'Pagado';
                    break;
            }
         print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Referencia</td>";
                            print "<td>".$vfac->ref."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Periodo</td>";
                            print "<td>"."De " .$vfac->date_debut. " a " . $vfac->date_fin."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Estatus</td>";
                            print "<td>".$statut."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha aprobación</td>";
                            print "<td>".$vfac->date_approve."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >Subtotal</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ht, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >IVA</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_tva, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td >Total</td>";
                            print "<td >".$moneda . " " . number_format($vfac->total_ttc, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
        $total_ht +=$vfac->total_ht;
        $total_tv +=$vfac->total_tva;
        $total_ttc +=$vfac->total_ttc;
        print "<input type='hidden' id='idSociete' name='idSociete[]' value='4-".$vfac->idgasto."'>";
        print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y", strtotime($vfac->date_fin))."'>";
        print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->idgasto."'>";

    }
    
    
    print "<input type='hidden' id='td_facnumber' value='".$nombre."'>";
    print "<input type='hidden' id='table_total_ht' value='".$total_ht."'>";
    print "<input type='hidden' id='table_total_tva' value='".$total_tv."'>";
    print "<input type='hidden' id='table_total_ttc' value='".$total_ttc."'>";
   
}else{
    print("<h1>No ha seleccionado Factura/s </h1>");
}

