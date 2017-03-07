<?php
require_once $url[0]."conex/conexion.php";
require_once "asiento.class.php";

class FacPolizaPendiente extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function getFacPolizasPend(){
		$rows=array();
		$sql='SELECT
					f.rowid,
					f.paye,
					s.nom,
					f.facnumber,
					f.datef,
					b.dateo,
					pf.amount,
					f.total_ttc,
					pa. CODE,
					pa.libelle,
					pf.rowid AS paimid ,@a := f.rowid ,@b := pf.amount,
					f.fk_cond_reglement,
					f.type,
					polif_fac.type AS "polif_fac_type",
					f.total_ttc
				FROM
					llx_facture AS f
				LEFT JOIN llx_paiement_facture AS pf ON f.rowid = pf.fk_facture
				INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
				LEFT JOIN llx_paiement AS pai ON pf.fk_paiement = pai.rowid
				LEFT JOIN llx_bank AS b ON pai.fk_bank = b.rowid
				LEFT JOIN llx_c_paiement pa ON pai.fk_paiement = pa.id
				LEFT JOIN llx_contab_poliza_facture AS polif_fac ON polif_fac.id_facture = f.rowid
				AND polif_fac.type NOT IN (2, 21, 211)
				LEFT JOIN llx_contab_facture_ignore AS fac_ignore ON f.rowid = fac_ignore.fk_facture
				AND fac_ignore.type = 1
				WHERE
					fac_ignore.rowid IS NULL
				AND f.fk_statut <> 0
				ORDER BY
					f.rowid DESC';
				
		$query= $this->db->query($sql);
		if ($query) {

			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}

		}

		foreach ($rows as $key => $row) {

			if ($row['polif_fac_type']==11 &&  $row['paye']==0) {
				unset($rows[$key]);
			}
			else if ($row['polif_fac_type']==1) {
				unset($rows[$key]);
			}
			if (isset($rows[$key+1])) {
				if ($row['rowid'] == $rows[$key+1]['rowid']) {
					unset($rows[$key]);
					unset($rows[$key+1]);
				}
			}
			
		}
		return $rows;
	}

	public function getCondiciones_de_Pago() {
		$a     =array();
		$sql   = "Select * FROM ".PREFIX."contab_payment_term Where entity = ".ENTITY;
		$query = $this->db->query($sql);
		if($query){
			$rows = array();
			while($row = $query->fetch_array())
			{
				$val = $row["cond_pago"];
    			$a["cond_pago_".$row["fk_payment_term"]] = $val;
			}
		}
		return $a;
	}

	function fetch_facture($rowid)
	{
		$rows=array();
		/*$sql = 'SELECT f.rowid,f.facnumber,f.ref_client,f.ref_ext,f.ref_int,f.type,f.fk_soc,f.amount,f.tva, f.localtax1, f.localtax2, f.total, f.total_ttc, f.revenuestamp';
		$sql.= ', f.remise_percent, f.remise_absolue, f.remise';
		$sql.= ', f.datef, ';
		$sql.= ', f.date_lim_reglement as dlr';
		$sql.= ', f.datec as datec';
		$sql.= ', f.type as tipoFact';
		$sql.= ', f.date_valid as datev';
		$sql.= ', f.tms as datem,f.fk_statut as statut';
		$sql.= ', f.note_private, f.note_public, f.fk_statut, f.paye, f.close_code, f.close_note, f.fk_user_author, f.fk_user_valid, f.model_pdf';
		$sql.= ', f.fk_facture_source';
		$sql.= ', f.fk_mode_reglement, f.fk_cond_reglement, f.fk_projet, f.extraparams';
		$sql.= ', f.situation_cycle_ref, f.situation_counter, f.situation_final';
		$sql.= ', f.fk_account';
		$sql.= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql.= ', c.code as cond_reglement_code, c.libelle as cond_reglement_libelle, c.libelle_facture as cond_reglement_libelle_doc';
		$sql.= ', f.fk_incoterms, f.location_incoterms';
		$sql.= ", i.libelle as libelle_incoterms";
		$sql.= ' FROM llx_facture as f';
		$sql.= ' LEFT JOIN llx_c_payment_term as c ON f.fk_cond_reglement = c.rowid';
		$sql.= ' LEFT JOIN llx_c_paiement as p ON f.fk_mode_reglement = p.id';
		$sql.= ' LEFT JOIN llx_c_incoterms as i ON f.fk_incoterms = i.rowid';
		$sql.= ' WHERE f.entity = '.ENTITY;
		$sql.= " AND f.rowid=".$rowid;
		$query= $this->db->query($sql);
		if($query){
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		}*/

		$rows = array();
		$sql = 'SELECT
					f.fk_cond_reglement,
					f.type,
					f.paye,
					p. CODE AS mode_reglement_code,
					p.libelle AS mode_reglement_libelle,
					c. CODE AS cond_reglement_code,
					c.libelle AS cond_reglement_libelle,
					c.libelle_facture AS cond_reglement_libelle_doc
				FROM
					llx_facture AS f
				LEFT JOIN llx_c_payment_term AS c ON f.fk_cond_reglement = c.rowid
				LEFT JOIN llx_c_paiement AS p ON f.fk_mode_reglement = p.id
				LEFT JOIN llx_c_incoterms AS i ON f.fk_incoterms = i.rowid
				WHERE
					f.rowid='.$rowid.'
				AND
					f.entity = '.ENTITY.'
				';

		$query= $this->db->query($sql);
		if($query){
		 	return $query->fetch_object();
		}
		return $rows;
	}

	function fetch_facture_lines($rowid)
	{
		$sql = 'SELECT * ';
		$sql.= ' FROM llx_facturedet as f';
		$sql.= ' WHERE ';
		$sql.= " AND fk_facture =".$rowid;
		$query= $this->db->query($sql);
		if($query){
			$rows = array();
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}

	function pol_relacionadas($id){
		$rows=array();
		$sql="SELECT count(*) as numf
						FROM ".PREFIX."contab_polizas
						WHERE entity=".ENTITY." AND fk_facture=".$id." AND societe_type=1 ";
		$query= $this->db->query($sql);
		if($query){
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}
	function get_fac_pagos($id){
		$sql="SELECT sum(amount) as total
							FROM ".PREFIX."paiement_facture
							WHERE fk_facture=".$id;
        $sql.=" GROUP BY fk_facture";
		$query= $this->db->query($sql);
		if($query){
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}

	function contabilizar_pol($id,$cp,$totalttc,$totalamount){
		$sqlr="SELECT fk_facture, tva_tx
							FROM ".PREFIX."facturedet
							WHERE fk_facture=".$id." GROUP BY tva_tx";
		//print $sqlr;
		$query=$this->db->query($sqlr);
		if ($query) {
			$nrr=mysqli_num_rows($query);
			$sqlv2="SELECT sum(amount) as total
								FROM ".PREFIX."paiement_facture
								WHERE fk_facture=".$id;
			$sqlv2 .=" GROUP BY fk_facture";
			$rv2   =$this->db->query($sqlv2);
			$rsv2  =$rv2->fetch_object();
			if($nrr>1 && $rsv2->total<$totalttc){
				$na=3;
			}else{
				if(($cp==1 || $cp==3) && $totalamount==0){
					$na=1;
				}else{
					if($cp==1 && $totalamount<$totalttc){
						$sqlv="SELECT sum(amount) as total
									FROM ".PREFIX."paiement_facture
									WHERE fk_facture=".$id;
                        $sql.=" GROUP BY fk_facture";
						$rv=$this->db->query($sqlv);
						@$rsv=$rv->fetch_object($rv);
						if(@$rsv->total<$totalttc){
							$na=2;
						}else{
							$na=0;
						}
					}else{
						$na=0;
					}
				}
			}
			return $na;
		}
		return false;
	}

    public function get_info_facture($id){
		$sql="		SELECT
						facnumber,
						datef,
						tva,
						total,
						total_ttc,
						a.type,
						fk_statut,
						fk_cond_reglement,
						fk_mode_reglement,
						date_lim_reglement,
						fk_soc,
						nom,
						a.rowid,
						a.paye
					FROM
						".PREFIX."facture a,
						".PREFIX."societe b
					WHERE
						a.rowid = ".$id."
					AND a.entity =".ENTITY."
					AND fk_soc = b.rowid";
		$result = $this->db->query($sql);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}

	public function get_info_facture_obj_id($id){
		$row = false;
		$sql="		SELECT
						facnumber,
						datef,
						tva,
						total,
						total_ttc,
						a.type,
						fk_statut,
						fk_cond_reglement,
						fk_mode_reglement,
						date_lim_reglement,
						fk_soc,
						nom,
						a.rowid,
						a.paye
					FROM
						".PREFIX."facture a,
						".PREFIX."societe b
					WHERE
						a.rowid = ".$id."
					AND a.entity =".ENTITY."
					AND fk_soc = b.rowid";
					

		$query =$this->db->query($sql);
        if ($query) {
            $row=  $query->fetch_object();
        }
		return $row;
	}
	public function get_paimenet($id){
		$row = array();
		$sql="SELECT code,libelle
			FROM ".PREFIX."c_paiement
			WHERE id=".$id;
		$result = $this->db->query($sql);
		if($result)
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}
	public function get_cond_reglement($id){
				$row = array();
		$sql="SELECT code,libelle
			FROM ".PREFIX."c_payment_term
			WHERE rowid=".$id;
		$result = $this->db->query($sql);
			if($result)
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}
	public function convierte_pagos($pago){
		$pago = str_replace("Carte Bancaire","Tarjeta",$pago);
		$pago = str_replace("Chèque","Cheque",$pago);
		$pago = str_replace("Espèces","Efectivo",$pago);
		$pago = str_replace("Prélèvement","Domiciliación bancaria",$pago);
		$pago = str_replace("TIP","Interbank Payment",$pago);
		$pago = str_replace("Paiement en ligne","Pago On Line",$pago);
		$pago = str_replace("Virement","Transferencia bancaria",$pago);
		return  ($pago);
	}
	public function convierte_condiciones($pago){
		$pago = str_replace("A réception de facture","	A la recepción",$pago);
		$pago = str_replace("30 jours","30 días",$pago);
		$pago = str_replace("30 jours fin de mois","30 días fin de mes",$pago);
		$pago = str_replace("60 jours","60 días",$pago);
		$pago = str_replace("60 jours fin de mois","60 días a fin de mes",$pago);
		$pago = str_replace("A réception de commande","Pedido",$pago);
		$pago = str_replace("Livraison","A la entrega",$pago);
		$pago = str_replace("50 et 50","	50/50",$pago);
		return  ($pago);
	}
	public function get_pagos_facture($id){
		$pag = array();
		$sql="SELECT a.amount,b.datep,code,libelle
		FROM ".PREFIX."paiement_facture a,".PREFIX."paiement b LEFT JOIN ".PREFIX."c_paiement c ON b.fk_paiement=c.id
		WHERE a.fk_facture=".$id." AND a.fk_paiement=b.rowid ";
		$result = $this->db->query($sql);
		//print $sql;

		if ($result) {

			while($row = $result->fetch_assoc())
			{
				$pag[] = $row;
			}

		} else{
			$pag='no';
		}
		return $pag;
	}

    public function cuenta_banco_rel($facture){
        $sql = 'SELECT
                    cta.codagr
                FROM
                    llx_paiement AS p
                    INNER JOIN llx_paiement_facture AS pf ON pf.fk_paiement = p.rowid
                    INNER JOIN llx_bank AS bank ON p.fk_bank = bank.rowid
                    INNER JOIN llx_bank_account AS account ON account.rowid = bank.fk_account
                    INNER JOIN llx_contab_cuentas_rel AS cr ON cr.fk_object = account.rowid AND cr.fk_type = 5
                    INNER JOIN llx_contab_cat_ctas AS cta ON cta.rowid = cr.fk_cuenta
                WHERE
                pf.fk_facture = '.$facture;

        $result = $this->db->query($sql);
				if ($result) {
					  return $result->fetch_object();
				}
        return array();

    }
}

