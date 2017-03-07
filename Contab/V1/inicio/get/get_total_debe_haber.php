<?php
if ( isset($_POST['debe']) && isset($_POST['haber'])){

        $haber = $_POST['haber'];
        $debe = $_POST['debe'];
    
        if($haber<0){
            $haber = -1* floatval(preg_replace('/[^\d.]/', '', $haber));
        }else{
             $haber = floatval(preg_replace('/[^\d.]/', '', $haber));
        }
    
        if($debe<0){
            $debe = -1* floatval(preg_replace('/[^\d.]/', '', $debe));
        }else{
            $debe = floatval(preg_replace('/[^\d.]/', '', $debe));
        }

        if($haber<>$debe){
           print('<a class="a_mensaje" style="color:red" >');
			  print('No coinciden los totales');
		  print('</a>');
        }else{
            print('<a class="a_mensaje" style="color:blue" >');
			  print('Coinciden los totales');
		  print('</a>');
        }
    
        
    }
?>