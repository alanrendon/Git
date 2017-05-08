<?php
if ( isset($_POST['txt_haber']) ){
        $total = 0 ;
        foreach ($_POST['txt_haber'] as $haber){
                if($haber<0){
                    $haber = -1* floatval(preg_replace('/[^\d.]/', '', $haber));
                }else{
                     $haber = floatval(preg_replace('/[^\d.]/', '', $haber));
                }
                
				$total+=$haber;
        }	
    
        echo number_format($total,2);
    }
?>