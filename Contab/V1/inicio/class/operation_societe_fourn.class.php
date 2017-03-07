<?php 
require_once $url[0]."conex/conexion.php";

class Operation_Societe_Fourn extends conexion
{
	public $rowid;
	public $id_societe;
	public $id_operation_societe;

	public function __construct() { 
		parent::__construct(); 
	} 

	public function put_operation_societe_fourn(){
		$sql ='INSERT INTO 
					`llx_contab_operation_societe_fourn` 
					(
						`id_societe`, 
						`id_operation_societe`
					) 
				VALUES ("'.$this->id_societe.'","'.$this->id_operation_societe.'")';

		$query= $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}

	public function delete_operation_societe_fourn(){
		$sql ='DELETE FROM `llx_contab_operation_societe_fourn` WHERE (`id_societe`="'.$this->id_societe.'")';
		$query= $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}

	public function get_operations_societe(){
		$rows =  array();
		$sql= '	SELECT
					`to`.`key` AS tokey,
					`to`.`name` AS toname,
					ts.`key` AS tskey,
					ts.`name` AS tsname,
					s.rowid,
					s.siren,
					s.nom
				FROM
					llx_contab_operation_societe_fourn AS osf
				INNER JOIN llx_societe AS s ON osf.id_societe = s.rowid
				INNER JOIN llx_contab_operation_societe AS os ON osf.id_operation_societe = os.rowid
				INNER JOIN llx_contab_type_operation AS `to` ON os.key_type_operation = `to`.rowid
				INNER JOIN llx_contab_type_societe AS ts ON os.key_type_societe = ts.rowid';
		
		$query= $this->db->query($sql); 
		if ($query) {
			while($row = $query->fetch_object())
			{
				$rows[]=$row;
			}		
		}
		return $rows;
	}


}
?>