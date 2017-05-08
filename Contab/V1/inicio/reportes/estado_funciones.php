<?php

function get_estado_resultados($periodo){
    $apartados = new Apartados();
    
    $estado    =2;
    $operativa =0;
    $bruta     =0;
    
    $totalVentas = 0;
    $totalGastos = 0;
    $totalCostos = 0;
    

    $totalVentas =  get_total_grupo($apartados->get_apartados_obj_ventas($estado),$periodo);
    $totalGastos =  get_total_grupo($apartados->get_apartados_obj_costo_ventas($estado),$periodo);
    $totalCostos =  get_total_grupo($apartados->get_apartados_obj_gastos($estado),$periodo);

    $bruta= $totalVentas - $totalCostos;
    $operativa = $bruta - $totalGastos;
    
    return $operativa;
}


function get_total_grupo($apartado,$periodo){
    $balance   = new Balance();
    $totalFinal =0;
    $estado    =2;
    foreach ($apartado as $apartado){
        $datos = $balance->get_balance($apartado->rowid, $estado);
        if (count($datos) > 0){
            $total = 0;
            foreach ($datos as $value){
                $total+= $suma = $balance->get_cta_inicial($value['fk_codagr_ini'], $value['fk_codagr_fin'], $periodo);
            }
           $totalFinal+=$total;
        }
    }
    
    return $totalFinal;
}

?>

