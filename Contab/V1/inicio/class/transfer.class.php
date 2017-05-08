<?php
require_once $url[0]."conex/conexion.php";

class Transfert extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_transfert() { 
		$sql="SELECT b.rowid as idtransfer,b.dateo,b.amount,b.label, b.fk_type,c.label as cuenta,
						c.ref as refcuenta
				FROM ".PREFIX."bank b, ".PREFIX."bank_account c
				WHERE b.label NOT LIKE '(%' AND b.label NOT LIKE '%)' AND b.fk_account=c.rowid 
						AND c.entity=".ENTITY." AND 
						b.rowid NOT IN (SELECT fk_facture FROM ".PREFIX."contab_polizas 
								WHERE entity=".ENTITY." AND societe_type=3)";

		$query= $this->db->query($sql);
		$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		
		return $rows;
	}
    
    public function get_transfert_id($id) { 
        $rows = array();
		$sql="SELECT 
                    b.rowid as idtransfer,
                    b.dateo,
                    b.amount,
                    b.label,
                    b.fk_type,
                    c.label as cuenta,
                    c.ref as refcuenta,
                    b.fk_account AS bank
				FROM ".PREFIX."bank b, ".PREFIX."bank_account c
				WHERE 
                        b.label NOT LIKE '(%' AND b.label NOT LIKE '%)' 
                AND 
                    b.fk_account=c.rowid 
				AND 
                    c.entity=".ENTITY." 
                AND 
                     b.rowid=".$id."
                AND 
				    b.rowid NOT IN (
                                    SELECT 
                                        fk_facture 
                                    FROM ".PREFIX."contab_polizas 
								        WHERE entity=".ENTITY." 
                                        AND 
                                        societe_type=3 
                                    )";
        

		$query= $this->db->query($sql);
		$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		
		return $rows;
	}
	
	
	public function get_paimenet_cod($cod){
		$sql="SELECT code,libelle
			FROM ".PREFIX."c_paiement
			WHERE code='".$cod."'";
		$result = $this->db->query($sql);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}
	
	public function convierte_pagos($pago){
		//print $pago."<<<br>";
		$pago= ($pago);
		$pago = str_replace("Prélèvement","Domiciliacion bancaria",$pago);
		$pago = str_replace("Carte Bancaire","Tarjeta",$pago);
		$pago = str_replace("Chèque","Cheque",$pago);
		$pago = str_replace("Espèces","Efectivo",$pago);
		$pago = str_replace("TIP","Interbank Payment",$pago);
		$pago = str_replace("Paiement en ligne","Pago On Line",$pago);
		$pago = str_replace("Virement","Transferencia bancaria",$pago);
		return  ($pago);
	}
    
    public function cuenta_bank_rel($bank){
        $sql = 'SELECT
                    cta.codagr
                FROM
                    '.PREFIX.'contab_cuentas_rel AS cr  
                    INNER JOIN '.PREFIX.'contab_cat_ctas AS cta ON cta.rowid = cr.fk_cuenta
                WHERE
                    cr.fk_object = '.$bank.'
                AND 
                    cr.fk_type = 5';

        $result = $this->db->query($sql);

        return $result->fetch_object();
	
    }
	
}