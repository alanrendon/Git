<?php
    if(isset($_POST['txt_debe'])){
        $total = 0 ;
        foreach ($_POST['txt_debe'] as $debe){
               if($debe<0){
                    $debe = -1* floatval(preg_replace('/[^\d.]/', '', $debe));
                }else{
                     $debe = floatval(preg_replace('/[^\d.]/', '', $debe));
                }
				$total+=$debe;
        }	
        echo number_format($total,2);
    }
?>