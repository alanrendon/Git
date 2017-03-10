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

	public function get_cuentas_bancos($fk_factura) {
		$rows = array();
		$sql ="
			SELECT
				r.codagr,w.fk_tipo
			FROM
				llx_c_paiement AS c
			INNER JOIN llx_paiement AS p ON p.fk_paiement = c.id
			INNER JOIN llx_paiement_facture AS pf ON pf.fk_paiement = p.rowid
			LEFT JOIN llx_bank AS b ON p.fk_bank = b.rowid
			LEFT JOIN llx_bank_account AS ba ON b.fk_account = ba.rowid
			LEFT JOIN llx_contab_cuentas_rel AS a ON a.fk_object = b.fk_account
			LEFT JOIN llx_contab_cat_ctas AS r ON r.rowid = a.fk_cuenta
			LEFT JOIN llx_contab_cuentas_poliza AS w ON w.codagr = r.codagr
			WHERE
				pf.fk_facture = ".$fk_factura."
			AND a.fk_type=5
			GROUP BY
				ba.rowid
			ORDER BY
				p.datep,
				p.tms
		";
		
		$query= $this->db->query($sql);
      
		if ($query) {
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function get_cuentas_prov($fk_prov) {
		$rows = array();
		$sql ="
			SELECT
			r.codagr,
			w.fk_tipo
		FROM
			llx_contab_cuentas_rel AS a 
		LEFT JOIN llx_contab_cat_ctas AS r ON r.rowid = a.fk_cuenta
		LEFT JOIN llx_contab_cuentas_poliza AS w ON w.codagr = r.codagr
		WHERE
			a.fk_object=".$fk_prov."
		AND a.fk_type = 2
		";
		
		$query= $this->db->query($sql);
      
		if ($query) {
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function get_cuentas_clien($fk_prov) {
		$rows = array();
		$sql ="
			SELECT
			r.codagr,
			w.fk_tipo
		FROM
			llx_contab_cuentas_rel AS a 
		LEFT JOIN llx_contab_cat_ctas AS r ON r.rowid = a.fk_cuenta
		LEFT JOIN llx_contab_cuentas_poliza AS w ON w.codagr = r.codagr
		WHERE
			a.fk_object=".$fk_prov."
		AND a.fk_type = 1
		";

		
		$query= $this->db->query($sql);
      
		if ($query) {
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
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
