<?php

require_once $url[0]."conex/conexion.php";

class Cuentas_Poliza extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function put_cuenta_poiliza($abr,$cod){
		$sql = "  INSERT INTO `".PREFIX."contab_cuentas_poliza` (`fk_tipo`, `codagr`)
                    VALUES
						(
                        '".$abr."',
                        '".$cod."'
                        )";
		$query = $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
	}


	public function get_cuentas_poliza($fk_tipo) {
		$rows = array();
		$sql ="SELECT
					fk_tipo,
				    codagr
				FROM
					".PREFIX."contab_cuentas_poliza
                WHERE
                   fk_tipo LIKE '".$fk_tipo."' 
               ORDER BY id ASC";

		$query= $this->db->query($sql);
       
		if ($query) {
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}
    
    public function get_cuentas_agrupacion($agr) { 
		$cuenta = array();
        $sql = 'SELECT 
                            * 
                        FROM 
                        '.PREFIX.'contab_cat_ctas 
                    WHERE 
                        afectacion=1 
                    AND 
                        codagr LIKE "'.$agr.'"
                ';
		$result = $this->db->query($sql); 
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			 	$cuenta[] = $row['codagr'].' - '. ($row['descripcion']);
			}
		}
		
		return $cuenta;
	}

	 public function get_cuentas_agrupacion_obj($agr) { 
		$row = false;
		$sql = 'SELECT 
                            * 
                        FROM 
                        '.PREFIX.'contab_cat_ctas 
                    WHERE 
                        afectacion=1 
                    AND 
                        codagr LIKE "'.$agr.'"
                     LIMIT 1
                ';

		$result = $this->db->query($sql); 
		$query  =$this->db->query($sql);
        
        if ($query) {
           while ( $data=$query->fetch_object()) { 
            	$row = $data;
            }          
        }
		
		return $row;
	}
    
   function existe($agr,$tipo){
       
       $sql ='SELECT
                p.fk_tipo,
                p.codagr
            FROM
                llx_contab_cuentas_poliza AS p
            WHERE
                p.fk_tipo LIKE "'.$tipo.'"
             AND 
                p.codagr LIKE "'.$agr.'"
            ';

       $result = $this->db->query($sql); 
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			 return true;
			}
		}
		
		return false;
   }
    
    function eliminar_todas($abr){
        $sql = 'DELETE FROM `llx_contab_cuentas_poliza` WHERE (`fk_tipo`="'.$abr.'")';
        $result = $this->db->query($sql); 
		if ($result) {
			return true;
		}
		
		return false;
    
    }
    

}

?>
