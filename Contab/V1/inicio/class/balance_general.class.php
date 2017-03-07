<?php
require_once $url[0]."conex/conexion.php";
require_once "cat_cuentas.class.php";
require_once "asiento.class.php";


class Balance extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_balance($tipo,$estado){
		$sql = "SELECT * FROM
				".PREFIX."contab_grupos
				WHERE tipo_edo_financiero = ".$estado." AND fk_grupo = ".$tipo;

		$query = $this->db->query($sql); 

		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
                $row['grupo']= ($row['grupo']);
				$rows[] = $row;
			}
					
		}
		return $rows;
	}

	public function get_cta_inicial($cta_in,$cta_fin,$periodo)
	{

		$cuenta  = new Cuenta();
		$asiento = new Asiento();
		$cta_in  = $cuenta->get_codagr_cuenta($cta_in);
		$cta_fin = $cuenta->get_codagr_cuenta($cta_fin);
		$ctas    = $cuenta->get_cuentas_agrupacion_in_fin_padres($cta_in,$cta_fin);
        $suma= array();
        $sum=0;
		foreach ($ctas as $cta) {
            $cta_codagr =$cta['codagr'];
            $cta_codagr =str_replace(".", "-",$cta_codagr);
		    $cta_codagr =explode("-",$cta_codagr);
			$suma=$asiento->get_sumasientobyCta_Natu($cta_codagr[0],$cta['natur'],$periodo);
		    $sum += $suma['debe']-$suma['haber'];
    
        }
        
		return $sum;

	}
    
    public function get_descripcion_ctas($cta_in,$cta_fin,$periodo)
	{

		$cuenta  = new Cuenta();
		$asiento = new Asiento();
		$cta_in  = $cuenta->get_codagr_cuenta($cta_in);
		$cta_fin = $cuenta->get_codagr_cuenta($cta_fin);
		$ctas    = $cuenta->get_cuentas_agrupacion_in_fin_padres($cta_in,$cta_fin);
		$sum     = 0;

        foreach ($ctas as $key => $cta) {
            $cta_codagr          =$cta['codagr'];
            $cta_codagr          =str_replace(".", "-",$cta_codagr);
		    $cta_codagr          =explode("-",$cta_codagr);
			$suma                =$asiento->get_sumasientobyCta_Natu($cta_codagr[0],$cta['natur'],$periodo);
		    $ctas[$key]['sum']   =number_format($suma['debe']-$suma['haber'],2) ;
            
            if(!($suma['debe']-$suma['haber'])<>0)
                unset($ctas[$key]);

        }
		return $ctas;

	}

	
}