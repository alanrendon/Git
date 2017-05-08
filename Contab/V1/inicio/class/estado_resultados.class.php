<?php
require_once $url[0]."conex/conexion.php";

class Estado extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_estado($mes,$tipo){
		$sql = "SELECT * FROM
				".PREFIX."contab_grupos
				WHERE tipo_edo_financiero = 2 AND fk_grupo = ".$tipo;

		$query = $this->db->query($sql); 

		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
					
		}
		return $rows;
	}
	
}