<?php
require_once $url[0]."conex/conexion.php";


class CuentasMayor extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	}

	public function get_mayor($periodo){
        
        $rows = array();
        $ajuste = 0;
        
        $porciones = explode("/", $periodo[0]);
        
        $periodo[1] = str_replace("/13","/12",$periodo[1]);
        $periodo[0] = str_replace("/13","/12",$periodo[0]);
        $periodo[0] = str_replace("/","-",$periodo[0]);
        $periodo[1] = str_replace("/","-",$periodo[1]);
        
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo[0])) {

           $periodo[0] = str_replace("-","/",$periodo[0]);
            $periodo[1] = str_replace("-","/",$periodo[1]);
            $date = new DateTime($periodo[0]);
            $periodo[0] =$date->format('Y-m-d');

            $date = new DateTime($periodo[1]);
            $periodo[1] =$date->format('Y-m-d');
        }
        
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
    
        
		$sql = "SELECT 
                    a.codagr as cta,
                    a.descripcion as descta,
                    sum(debe) as debe, 
                    sum(haber) as haber,
                    a.natur
				FROM 
                    ".PREFIX."contab_cat_ctas a, 
                    ".PREFIX."contab_polizasdet b, 
                    ".PREFIX."contab_polizas c
				WHERE 
                    a.codagr=b.cuenta 
                AND
                    b.fk_poliza=c.rowid 
				AND 
                    c.entity=".ENTITY." 
                AND 
                    c.ajuste=".$ajuste." 
                GROUP BY a.codagr";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
                $row['descta']= ($row['descta']);
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
	
	public function get_saldo_ini($periodo,$cta){
       $rows = array();
        $ajuste = 0;
        
        $porciones = explode("/", $periodo[0]);
        
         $periodo[1] = str_replace("/13","/12",$periodo[1]);
        $periodo[0] = str_replace("/13","/12",$periodo[0]);
        $periodo[0] = str_replace("/","-",$periodo[0]);
        $periodo[1] = str_replace("/","-",$periodo[1]);
        
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo[0])) {
            $periodo[0] = str_replace("-","/",$periodo[0]);
            $periodo[1] = str_replace("-","/",$periodo[1]);
            $date = new DateTime($periodo[0]);
            $periodo[0] =$date->format('Y-m-d');

            $date = new DateTime($periodo[1]);
            $periodo[1] =$date->format('Y-m-d');
        }
        
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
    
        
		$sql = "SELECT
                        a.codagr AS cta,
                        a.descripcion AS descta,
                        sum(debe) AS debe,
                        sum(haber) AS haber
                    FROM
                        ".PREFIX."contab_cat_ctas a,
                        ".PREFIX."contab_polizasdet b,
                        ".PREFIX."contab_polizas c
                    WHERE
                        a.codagr = b.cuenta
                    AND b.fk_poliza = c.rowid
                    AND c.fecha < '".$periodo[0]."'
                    AND a.codagr = '".$cta."'
                    AND c.entity = ".ENTITY." ";
         $sql.=" GROUP BY a.codagr";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
                
				return $row;
			}
	
	
		}
		return $rows;
	}
	
	public function get_pol_mayor($periodo,$cuenta){
        $rows = array();
        $ajuste = 0;
        
        $porciones = explode("/", $periodo[0]);
        
        $periodo[1] = str_replace("/13","/12",$periodo[1]);
        $periodo[0] = str_replace("/13","/12",$periodo[0]);
        $periodo[0] = str_replace("/","-",$periodo[0]);
        $periodo[1] = str_replace("/","-",$periodo[1]);
        
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo[0])) {

           $periodo[0] = str_replace("-","/",$periodo[0]);
            $periodo[1] = str_replace("-","/",$periodo[1]);
            $date = new DateTime($periodo[0]);
            $periodo[0] =$date->format('Y-m-d');

            $date = new DateTime($periodo[1]);
            $periodo[1] =$date->format('Y-m-d');
        }
        
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
    
		$sql = "SELECT
                a.debe,
                a.haber,
                b.tipo_pol,
                b.cons,
                b.fecha
            FROM
                llx_contab_polizasdet a,
                llx_contab_polizas b
            WHERE
                a.cuenta = '".$cuenta."'
            AND a.fk_poliza = b.rowid
            AND b.entity = ".ENTITY."
            AND b.fecha BETWEEN '".$periodo[0]."'
            AND '".$periodo[1]."'
            AND (a.debe != 0 OR a.haber != 0)";

		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
}