<?php
require_once $url[0]."conex/conexion.php";


class BalanceComp extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 
	
	public function get_balance_comp($periodo,$rango=""){
        $rows =array();
         $porciones = explode("/", $periodo[0]);
        
        if($porciones [0]<13 ){
            $date = new DateTime($periodo[0]);
            $periodo[0] =$date->format('Y-m-d');

            $date = new DateTime($periodo[1]);
            $periodo[1] =$date->format('Y-m-d');
            $ajuste = 0;
            
        }else{
              $periodo[0] = str_replace("/","-",$periodo[0]);
              $periodo[1] = str_replace("/","-",$periodo[1]);
              $periodo[1] = str_replace("13","12",$periodo[1]);
              $periodo[0] = str_replace("13","12",$periodo[0]);
              $periodo[0] = $porciones[0];
              $periodo[1] = $porciones[0];
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
                    b.fk_poliza=c.rowid ";
            if($ajuste){
                $sql .="AND
                            c.anio BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
                        AND
                            c.ajuste = 1;
                        AND 
                            c.entity=".ENTITY." ";
            }else{
                $sql .= "AND 
                    c.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'  
				AND 
                    c.entity=".ENTITY." ";
            }	
            if($rango!=""){
                 if($rango[0] != '' && $rango[1] != ''){
                     $sql.=" AND a.codagr BETWEEN '".$rango[0]."' AND '".$rango[1]."'";
                 }else if($rango[0] != '' && $rango[1] == ''){
                     $sql.=" AND a.codagr > '".$rango[0]."'";
                 }else if($rango[0] == '' && $rango[1] != ''){
                      $sql.=" AND a.codagr < '".$rango[1]."'";
                 }
                 
            }

		$sql.=" GROUP BY a.codagr";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
                $row['descta']=utf8_encode($row['descta']);
				$rows[] = $row;
			}
				
		}
		return $rows;
	}
	
	public function get_balance_comp2($periodo,$rango=""){
        
		$sql = "SELECT a.codagr as cta,a.descripcion as descta,a.natur,a.nivel
				FROM ".PREFIX."contab_cat_ctas a ";
		       
		$sql.=" GROUP BY a.codagr";
    
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$row['descta']=utf8_encode($row['descta']);
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
    
    public function get_balance_comp_desc_cuenta($periodo,$cta){
        $cta_codagr =str_replace(".", "-",$cta);
        $cta_codagr =explode("-",$cta_codagr);
        
		$sql = "  SELECT    
                            a.codagr as cta,
                            a.descripcion as descta,
                            a.natur,
                            a.nivel
				    FROM 
                    ".PREFIX."contab_cat_ctas a 
                    WHERE
                        a.codagr  LIKE '%".$cta_codagr[0]."%'
                    ";
		       
		$sql.=" GROUP BY a.codagr";

		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$row['descta']=utf8_encode($row['descta']);
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
    
	public function get_balance_comp_debhab($periodo,$cta){
        $rows = array();
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
        
        
        $ajuste = 0;
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
                        b.fk_poliza=c.rowid";
                    if($ajuste){
                        $sql .=" AND
                                    c.anio BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
                                AND
                                    c.ajuste = 1
                            AND 
                                c.entity=".ENTITY." ";
                    }else{
                        $sql .= " AND 
                            c.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'  
                            AND 
                                c.entity=".ENTITY." ";
                    }	
            $sql .= " AND 
                    a.codagr='".$cta."'";
		$sql.=" GROUP BY a.codagr;";

		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
				
	
		}
		return $rows;
	}
	
	public function get_balance_comp_debhab_padre($periodo,$cta){
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
        
        $ajuste = 0;
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
        
		$cta=str_replace(".", "-",$cta);
		$cta=explode("-",$cta);
        
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
                    c.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
				AND
                    c.entity=".ENTITY." 
                AND 
                    a.codagr LIKE '".$cta[0]."%'";
 
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
	
	
		}
		return $rows;
	}
	
	public function get_saldo_ini($periodo,$cta){
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
        $ajuste = 0;
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
                    c.fecha < '".$periodo[0]."'  
				AND 
                    a.codagr='".$cta."' 
                AND 
                c.entity=".ENTITY." ";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
				
	
		}
		return $rows;
	}
	
	public function get_saldo_ini_padre($periodo,$cta){
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
        
        $ajuste = 0;
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
        
		$cta=str_replace(".", "-",$cta);
		$cta=explode("-",$cta);
        
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
                        c.fecha < '".$periodo[0]."'
                    AND 
                        a.codagr LIKE '".$cta[0]."%' 
                    AND 
                        c.entity=".ENTITY." ";

		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
	
	
		}
		return $rows;
	}
	
	public function get_polizas_cuenta($periodo,$cuenta){
        $rows = array();
        
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
        $ajuste = 0;
        if($porciones [0]==13 ){
            $ajuste = 1;
        }
        
		$sql = "  SELECT 
                        a.debe, 
                        a.haber, 
                        b.tipo_pol, 
                        b.cons, 
                        b.concepto, 
                        b.fk_facture, 
                        b.societe_type,
                        b.fecha
			         FROM   
                        ".PREFIX."contab_polizasdet a, 
                        ".PREFIX."contab_polizas b
			         WHERE 
                            a.cuenta='".$cuenta."' 
                    AND 
                            b.entity=".ENTITY." 
                    AND 
                            a.fk_poliza=b.rowid";
                    if($ajuste){
                        $sql .=" AND
                                    b.anio BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
                                AND
                                    b.ajuste = 1
                           ";
                    }else{
                        $sql .= " AND 
                               b.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'  
                            ";
                    }	
                    $sql .= " ORDER BY 
                        b.tipo_pol,b.cons";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
	}
	
	public function get_facture($idfac,$tipo){
		if($tipo==1){
			$sql = "SELECT facnumber FROM ".PREFIX."facture WHERE rowid=".$idfac;
		}
		if($tipo==2){
			$sql = "SELECT ref as facnumber FROM ".PREFIX."facture_fourn WHERE rowid=".$idfac;
		}
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
	
	
		}
		return $rows;
	}
	
	public function get_cat_cta(){
		$sql = "SELECT * FROM ".PREFIX."contab_cat_ctas WHERE entity=".ENTITY." ORDER BY codagr ASC";
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