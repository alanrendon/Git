<?php
require_once $url[0]."conex/conexion.php";

class Pais extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_paises() { 
        
		$rows = array();
		$sql ='SELECT * FROM `'.PREFIX.'c_country` ';

		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}		
		}
		return $rows;
	} 

	

}