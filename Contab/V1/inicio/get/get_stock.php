<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once ($url[0]."class/stock.class.php");
require_once $url[0]."class/admin.class.php";
$trans=new Movstock();

$eDolibarr = new admin();
$valores = $eDolibarr->get_user();
$moneda = $eDolibarr->get_moneda($valores['MAIN_MONNAIE']);


if(isset($_POST['transid'])){

    $bandera1      =0;
    $bandera2      =0;
    $nombre        ="Ref: ";
    $totalFacturas = count($_POST['transid']);
    $total         = 0;
    if($totalFacturas>1){
        foreach($_POST['transid'] as $factid){


            $vfac   =$trans->get_movimientos_stock_id($factid);
            $vfac   =$vfac[0];
            $nombre .=" ".$vfac->ref."-".$vfac->label;
            $total  += $vfac->price*$vfac->unidades;
            $count  =$trans->cuenta_alm_rel($vfac->alm);
             print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Etiqueta del movimiento</td>";
                            print "<td>".$vfac->etiqmov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha</td>";
                            print "<td>".$vfac->fechamov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Producto</td>";
                            print "<td>".$vfac->fechamov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Almacén</td>";
                            print "<td>".$vfac->ref.'-'.$vfac->label."</td>";
                        print "</tr>";
                        print "<tr>";
                        if($vfac->unidades>0){
                        	$bandera1=1;
                        }
                        if($vfac->unidades<0){
                        	$bandera2=1;
                        }
                            print "<td>Cantidad</td>";
                            print "<td>".$vfac->unidades."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Precio</td>";
                            print "<td>".$moneda . " " . number_format($vfac->price, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Total</td>";
                            print "<td>".$moneda . " " . number_format($vfac->price*$vfac->unidades, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
            
            print "<input type='hidden' id='idSociete' name='idSociete[]' value='4-".$vfac->idmov."'>";
           
            print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->idmov."'>";
        }
         print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y")."'>";
    }elseif ($totalFacturas==1){
         $nombre ="Ref: ";
         $idFact =(int)$_POST['transid'][0];
         $vfac   =$trans->get_movimientos_stock_id($idFact);
         $vfac   =$vfac[0];
         $nombre .=" ".$vfac->ref."-".$vfac->label;
         $count  =$trans->cuenta_alm_rel($vfac->alm);
         $total  += $vfac->price*$vfac->unidades;
             print "<table class='table table-striped'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td>Etiqueta del movimiento</td>";
                            print "<td>".$vfac->etiqmov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Fecha</td>";
                            print "<td>".$vfac->fechamov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Producto</td>";
                            print "<td>".$vfac->fechamov."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Almacén</td>";
                            print "<td>".$vfac->ref.'-'.$vfac->label."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Cantidad</td>";
                            if($vfac->unidades>0){
                            	$bandera1=1;
                            }
                            if($vfac->unidades<0){
                            	$bandera2=1;
                            }
                            print "<td>".$vfac->unidades."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Precio</td>";
                            print "<td>".$moneda . " " . number_format($vfac->price, 2)."</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td>Total</td>";
                            print "<td>".$moneda . " " . number_format($vfac->price*$vfac->unidades, 2)."</td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br /><br />";
        print "<input type='hidden' id='idSociete' name='idSociete[]' value='4-".$vfac->idmov."'>";
        print "<input type='hidden' id='FechaFactura' value='".date("m/d/Y", strtotime($vfac->fechamov))."'>";
        print "<input type='hidden' name='factidRecuperado[]' value='".$vfac->idmov."'>";

    }
    print "<input type='hidden' id='td_facnumber' value='".$nombre."'>";
    if($bandera1==1 && $bandera2==1){
    	print "<input type='hidden' id='td_cantstocmv' value='3'>";
    }else{
    	if($bandera1==1 && $bandera2==0){
    		print "<input type='hidden' id='td_cantstocmv' value='1'>";
    	}else{
    		if($bandera1==0 && $bandera2==1){
    			print "<input type='hidden' id='td_cantstocmv' value='2'>";
    		}else{
    			print "<input type='hidden' id='td_cantstocmv' value='0'>";	 
    		}	 
    	}	
    }
    print "<input type='hidden' id='total' value='".$total."'>";
   
}else{
    print("<h1>No ha seleccionado Factura/s </h1>");
}

