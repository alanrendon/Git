<?php
require_once $url[0]."conex/conexion.php";

class Movstock extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_movimientos_stock() { 
		$sql="SELECT a.rowid as idmov,a.datem as fechamov, b.ref, b. label,c.label as almacen, 
				a.inventorycode,a.label as etiqmov, a.value as unidades,b.price,b.price_ttc
			FROM ".PREFIX."stock_mouvement a, ".PREFIX."product b, ".PREFIX."entrepot c
			WHERE a.fk_product=b.rowid AND b.entity=".ENTITY." AND a.fk_entrepot=c.rowid 
					AND a.rowid NOT IN 	(SELECT id_facture FROM ".PREFIX."contab_poliza_facture
								WHERE type=4)";
		//print $sql;
		$result = $this->db->query($sql); 
		$query= $this->db->query($sql);
		$rows = array();
        while($row = $query->fetch_assoc())
            $rows[] = $row;

		return $rows;
	}
    
    public function get_movimientos_stock_id($id) { 
		$sql="    SELECT 
                        a.rowid as idmov,
                        a.datem as fechamov,
                        b.ref,
                        b.label,
                        c.label as almacen, 
                        a.inventorycode,
                        a.label as etiqmov,
                        a.value as unidades,
                        b.price,
                        b.price_ttc,
                        c.rowid AS alm
			     FROM 
                        ".PREFIX."stock_mouvement a, 
                        ".PREFIX."product b, 
                        ".PREFIX."entrepot c
			     WHERE 
                    a.fk_product=b.rowid 
                
                 AND 
                    a.rowid = ".$id."
                 AND 
                    b.entity=".ENTITY." AND a.fk_entrepot=c.rowid 
				 AND 
                    a.rowid NOT IN (
                                        SELECT 
                                            fk_facture 
                                        FROM 
                                            ".PREFIX."contab_polizas 
								        WHERE 
                                            entity=".ENTITY." 
                                        AND 
                                            societe_type=4)";
		$result = $this->db->query($sql); 
		$query= $this->db->query($sql);
		$rows = array();
        while($row = $query->fetch_object())
				$rows[] = $row;
		return $rows;
	}

        
    public function cuenta_alm_rel($alm){
        $sql = 'SELECT
                    cta.codagr
                FROM
                    '.PREFIX.'contab_cuentas_rel AS cr  
                    INNER JOIN '.PREFIX.'contab_cat_ctas AS cta ON cta.rowid = cr.fk_cuenta
                WHERE
                    cr.fk_object = '.$alm.'
                AND 
                    cr.fk_type = 6';

        $result = $this->db->query($sql);
        
        return $result->fetch_object();
	
    }
    
    public function get_cuentasRelStock($condicion) {
    
    	$rows = false;
    
    	$sql ="SELECT
                    cuenta_rel.fk_object,
                    cuenta_rel.fk_type,
                    cuentas.descripcion,
                    cuentas.codagr
				FROM
					".PREFIX."contab_cuentas_rel AS cuenta_rel
				INNER JOIN ".PREFIX."contab_cat_ctas AS cuentas ON cuentas.rowid=cuenta_rel.fk_cuenta
				WHERE
                   (".$condicion.")
                
				";
    	 //print $sql;
    	$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($row = $query->fetch_array())
			{//print_r($row);
				$rows[] = $row;
			}		
		}
		return $rows;
    
    }
	
	
}