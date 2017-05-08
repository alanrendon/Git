<?php
require_once $url[0]."conex/conexion.php";

class Gastos extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_gastos() { 
		$sql="SELECT a.rowid as idgasto, a.ref, a.total_ht, a.total_tva, a.total_ttc, a.date_debut, 
				a.date_fin, date_approve, a.fk_statut
			FROM ".PREFIX."expensereport a
			WHERE a.entity=".ENTITY." AND a.rowid NOT IN (SELECT fk_facture 
				FROM ".PREFIX."contab_polizas WHERE entity=".ENTITY." AND societe_type=5)";
		//print $sql;
		$query= $this->db->query($sql);
		$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		
		return $rows;
	}
    
    public function get_gastos_id($id) { 
		$sql="SELECT 
                    a.rowid as idgasto, 
                    a.ref, a.total_ht, 
                    a.total_tva, 
                    a.total_ttc, 
                    a.date_debut, 
				    a.date_fin, 
                    date_approve, 
                    a.fk_statut
			  FROM ".PREFIX."expensereport a
			  WHERE 
                    a.entity=".ENTITY." 
              AND 
                a.rowid=".$id." 
              AND
                a.rowid NOT IN (SELECT fk_facture 
				    FROM ".PREFIX."contab_polizas WHERE entity=".ENTITY." AND societe_type=5)";
		//print $sql;
		$query= $this->db->query($sql);
		$rows = array();
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		
		return $rows;
	}
	
	
}