<?php
require_once $url[0]."conex/conexion.php";
/**
* Used to get de list the type of operations can have an societe
*/
class Tipo_Operacion extends conexion
{
	
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_list_typeoperation($fk_type_societe){
		$rows = array();
		$sql= '		SELECT
						s.`key`,
						s.`name`,
						s.rowid
					FROM
						llx_contab_operation_societe AS os
					INNER JOIN llx_contab_type_societe AS s ON os.key_type_societe = s.rowid
					WHERE
						os.key_type_operation='.$fk_type_societe;

		$query = $this->db->query($sql);

    	if ($query) 
    		while($row = $query->fetch_object())
    			$rows[] = $row;
 
    	return $rows;
	}

	public function get_id_operacion_proveedor($key_type_operation,$fk_operation_societe){
		$rows = array();
		$sql= '		SELECT
						os.key_type_operation,
						os.key_type_societe,
						os.rowid
					FROM
						llx_contab_operation_societe AS os
					INNER JOIN llx_contab_type_societe AS s ON os.key_type_societe = s.rowid
					WHERE
						os.key_type_operation='.$key_type_operation.'
					AND
						os.key_type_societe='.$fk_operation_societe;
		$query = $this->db->query($sql);

    	if ($query) 
    		while($row = $query->fetch_object())
    			return $row;
    	return false;
	}

	public function get_list_typesociete(){
		$rows = array();
		$sql= '		SELECT
						*
					FROM
						`llx_contab_type_operation` AS o
					ORDER BY
						o.`key`';

		$query = $this->db->query($sql);
    	if ($query) 
    		while($row = $query->fetch_object())
    			$rows[] = $row;
    	return $rows;
	}

	function get_info_proveedor($poliza){
		$sql = '(
					SELECT
						f.rowid AS facture,
						polif_fac.type,
						s.siren, 
						s.rowid AS id_societe
					FROM
						llx_facture AS f
					INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
					INNER JOIN llx_contab_poliza_facture AS polif_fac ON polif_fac.id_facture = f.rowid
					AND polif_fac.type = 1
					WHERE
						fk_statut != 0
					AND polif_fac.id_poliza = '.$poliza.'
				)
				UNION DISTINCT
				(
					SELECT
						f.rowid AS facture,
						polif_fac.type,
						s.siren,
						s.rowid AS id_societe
					FROM
							llx_facture_fourn AS f
					INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
					INNER JOIN llx_contab_poliza_facture AS polif_fac ON polif_fac.id_facture = f.rowid AND polif_fac.type = 2
					WHERE
						fk_statut != 0
					AND polif_fac.id_poliza = '.$poliza.'
				)';

		$query = $this->db->query($sql);
		if ($query) 
    		while($row = $query->fetch_object())
    			return $row;
 		return array();
	}

	

	function get_operaciones_registradas_proveedor($rfc){
		$rows = array();
		$sql ='SELECT
					odocto.fk_operation_societe AS operation,
					os.key_type_operation,
					os.key_type_societe
				FROM
					llx_contab_polizas_docto AS odocto
				INNER JOIN llx_contab_operation_societe AS os ON odocto.fk_operation_societe = os.rowid
				WHERE
					rfc ="'.$rfc.'"
				ORDER BY
					 odocto.rowid DESC
				LIMIT 1';

		$query = $this->db->query($sql);
		if ($query) 
    		while($row = $query->fetch_object())
    			return $row;
    	return $rows;

	}

}
?>