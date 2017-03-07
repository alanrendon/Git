<?php
/**
* 
*/
require_once $url[0]."conex/conexion.php";
class Facture_Ignore extends conexion
{
	public $id_facture;
	public $type;

	public function __construct() { 
		parent::__construct(); 
	} 

	public function insert(){
		$inserted = false;
		$sql = 'INSERT INTO `llx_contab_facture_ignore` 
						(
							`fk_facture`, 
							`type`
						) 
						VALUES 
						(
							"'.$this->id_facture.'", 
							"'.$this->type.'"
						)';
		$query= $this->db->query($sql); 
		if ($this->db->affected_rows>0)
    			$inserted = true;
    	return $inserted;
	}
}
?>