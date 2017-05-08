<?php
/*
    CREATE TABLE `llx_conf_apartados` (
    `rowid`  int NOT NULL AUTO_INCREMENT ,
    `apartado`  varchar(255) NOT NULL ,
    `tipo`  int(2) NOT NULL ,
    `reporte`  int(2) NOT NULL ,
    PRIMARY KEY (`rowid`),
    UNIQUE INDEX `apartado_conf` (`apartado`) 
    )
    ;

*/

require_once $url[0]."conex/conexion.php";

class Apartados extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_apartados($reporte) { 
		$rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}		
		}
		return $rows;
	}
    
    public function get_apartado_id($rowid) { 
		$rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE rowid = ".$rowid." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
            $obj=$query->fetch_object();
            $obj->apartado =  ($obj->apartado);
			return $obj;
		}
		return false;
	} 
    
    function get_apartados_obj_pasivo($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 2
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }
    
     function get_apartados_obj_capital($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 3
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }
    
     function get_apartados_obj_ventas($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 1
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }
    
     function get_apartados_obj_costo_ventas($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 2
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }
    
    function get_apartados_obj_gastos($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 3
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }
    
     function get_apartados_obj_activo($reporte){
        $rows = false;
        $sql ="     SELECT
                        apartado.apartado,
                        apartado.rowid
                    FROM
                    ".PREFIX."conf_apartados AS apartado
                    WHERE tipo = 1
                    AND 
                    reporte = ".$reporte." 
                    ORDER BY
                        apartado.apartado ASC
            ";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($obj = $query->fetch_object())
			{
				$rows[] = $obj;
			}		
		}
		return $rows;
        
    }

    public function put_apartado($txt_descrpcion,$txt_tipo,$reporte) {
        $sql = "INSERT INTO ".PREFIX."conf_apartados (`apartado`,`tipo`,`reporte`) VALUES ('".$txt_descrpcion."',".$txt_tipo.",".$reporte.");";
		$query= $this->db->query($sql); 
		if ( $query ) {
			return true;	
		}
		return false;
    }
}
