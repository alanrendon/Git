<?php
require_once $url[0]."conex/conexion.php";


class LibroDiario extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_pol_diario($periodo){

        $rows = array();
        $porciones = explode("/", $periodo[0]);
        
         $periodo[1] = str_replace("/13","/12",$periodo[1]);
        $periodo[0] = str_replace("/13","/12",$periodo[0]);
        $periodo[0] = str_replace("/","-",$periodo[0]);
        $periodo[1] = str_replace("/","-",$periodo[1]);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo[0])) {

          $periodo[0] = str_replace("-","/",$periodo[0]);

            $periodo[1] = str_replace("-","/",$periodo[1]);
            echo "string";
            print_r($periodo[0]);
            return null;
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
                        *
				FROM 
                    ".PREFIX."contab_polizas
				WHERE 
                    fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'";
        if($ajuste){
                $sql .=" AND 
                    ajuste=1";
        }
            $sql .=" AND 
                    entity=".ENTITY;
        
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
	
	public function get_fac_clt($facid){
		$sql = "SELECT facnumber
				FROM ".PREFIX."facture
				WHERE rowid='".$facid."'";
		$query = $this->db->query($sql);

		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
		}
		return $rows;
	}
	
	public function get_fac_prov($facid){
        $rows = false;
		$sql = "SELECT ref
				FROM ".PREFIX."facture
				WHERE rowid='".$facid."'";
		$query = $this->db->query($sql);

		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
		}
		return $rows;
	}
	
	public function get_pol_det($polid){
		$sql = "SELECT *
				FROM ".PREFIX."contab_polizasdet
				WHERE fk_poliza='".$polid."' ";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
	
		}
		return $rows;
	}
	
	public function get_count_pol_det($polid){
		$sql = "SELECT count(*) as cant
				FROM ".PREFIX."contab_polizasdet
				WHERE fk_poliza='".$polid."' ";
		$query = $this->db->query($sql);
		if ($query) {
			$rows = array();
			if($row = $query->fetch_object()){
				return $row;
			}
		}
		return $rows;
	}
	public function get_nom_cta($acid){
		$sql = "SELECT descripcion
				FROM ".PREFIX."contab_cat_ctas
				WHERE codagr='".$acid."' ";
		$query = $this->db->query($sql);

		if ($query) {
	
			if($row = $query->fetch_object()){
               
                $row->descripcion= ( $row->descripcion);
               
				return $row;
			}
		}
		 return 'No hay descripción';
	}
}