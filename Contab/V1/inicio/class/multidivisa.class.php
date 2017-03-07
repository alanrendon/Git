<?php
/**
* Clase para determinar la Divisa Actual
*/
require_once $url[0]."conex/conexion.php";
class Divisa extends conexion
{
	public $id_facture;
	public $fk_document;
	public $type_document;

	public function __construct() {
		parent::__construct();
	}

	public function check_if_active(){
		$active =  false;
		$sql    =  'SELECT
						const.rowid
					FROM
						llx_const AS const
					WHERE
						const. NAME LIKE "MAIN_MODULE_MULTIDIVISA"
					AND
						const.entity = '.ENTITY;
		$result = $this->db->query($sql);

		if ($result->num_rows>0) 
			$active =true;
		return $active;
	}

	public function check_monnaie(){
		$row = array();
		$sql =  'SELECT
					const.`value`,
					const.entity
					FROM
						llx_const AS const
					WHERE
					const.`name` LIKE "MAIN_MONNAIE" 
					AND
						const.entity = '.ENTITY;

		$query = $this->db->query($sql);
		if ($query) 
			$row = $query->fetch_object();
		return $row;
	}

	public function fechtMultidivisa($value=0)
	{
		# code...
	}


	public function divisa_facture_fourn(){
		$row = array();
		$sql = 'SELECT
					fk_object,
					divisa,
					entity
				FROM
					llx_multidivisa_facture_fourn AS mff
				WHERE
					mff.fk_object = "'.$this->fk_document.'"
				GROUP BY
					fk_object
				ORDER BY
					fk_object DESC';
		$query = $this->db->query($sql);
		if ($query) 
			$row = $query->fetch_object();
		return $row;
	}

	public function divisa_facture(){

		$sql = 'SELECT
					fk_object,
					divisa,
					entity
				FROM
					llx_multidivisa_facture AS mff
				WHERE
					mff.fk_object =  "'.$this->fk_document.'"
				GROUP BY
					fk_object
				ORDER BY
					fk_object DESC';

		$query = $this->db->query($sql);
		if ($query) 
			$row = $query->fetch_object();
		
		return $row;
	}

	public function get_only_type($fk_document,$type_document){
		$divisa              ='';
		$row                 =array();
		$this->fk_document   =$fk_document;
		$this->type_document =$type_document;
		$row                 =$this->divisa_facture();

		if(count($row)>0){
			$divisa =$row->target_divisa;
		}else{
			$row    = $this->check_monnaie();
			$divisa = $row->value;
		}

		return $divisa;
	}


	public function get_list_factures_affected(){
		$rows = array();
		$sql = 'SELECT
					a.rowid,
					a.source_divisa,
					a.target_divisa,
					a.fecha,
					a.hora,
					a.fk_document,
					a.type_document,
					a.entity,
					a.tipo_cambio
				FROM
					llx_multidivisa AS a
				WHERE
					a.rowid IN (
						(
							SELECT
								MAX(b.rowid)
							FROM
								llx_multidivisa AS b
							WHERE
								a.fk_document = b.fk_document
							AND a.type_document LIKE b.type_document
						)
					)
				AND a.entity = '.ENTITY.'
				ORDER BY
					a.type_document ASC,
					a.rowid DESC';

		$query = $this->db->query($sql);
		if ($query) 
			while ($row = $query->fetch_object()) 
				$rows[]=$row;
		return $rows;
	}


	public function get_Facture($rowid)
	{
		$sql = 'SELECT
					f.fk_cond_reglement,
					f.type,
					f.paye,
					p.`code` AS mode_reglement_code,
					p.libelle AS mode_reglement_libelle,
					c.`code` AS cond_reglement_code,
					c.libelle AS cond_reglement_libelle,
					c.libelle_facture AS cond_reglement_libelle_doc,
					s.nom,
					f.facnumber AS ref,
					f.total_ttc
				FROM
					llx_facture AS f
				LEFT JOIN llx_c_payment_term AS c ON f.fk_cond_reglement = c.rowid
				LEFT JOIN llx_c_paiement AS p ON f.fk_mode_reglement = p.id
				LEFT JOIN llx_c_incoterms AS i ON f.fk_incoterms = i.rowid
				INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
				WHERE
					f.rowid='.$rowid.'
				AND
					f.entity = '.ENTITY.'
				';

		$query= $this->db->query($sql);
		if($query)
		 	return $query->fetch_object();
	}

	public function get_Facture_Fourn($rowid)
	{
		$sql = 'SELECT
					f.fk_cond_reglement,
					f.type,
					f.paye,
					p.`code` AS mode_reglement_code,
					p.libelle AS mode_reglement_libelle,
					c.`code` AS cond_reglement_code,
					c.libelle AS cond_reglement_libelle,
					c.libelle_facture AS cond_reglement_libelle_doc,
					s.nom,
					f.ref,
					f.total_ttc
				FROM
					llx_facture_fourn AS f
				LEFT JOIN llx_c_payment_term AS c ON f.fk_cond_reglement = c.rowid
				LEFT JOIN llx_c_paiement AS p ON f.fk_mode_reglement = p.id
				LEFT JOIN llx_c_incoterms AS i ON f.fk_incoterms = i.rowid
				INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
				WHERE
					f.rowid='.$rowid.'
				AND
					f.entity = '.ENTITY.'
				';

		$query= $this->db->query($sql);
		if($query)
		 	return $query->fetch_object();
	}


}
?>