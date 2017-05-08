<?php
require_once $url[0]."conex/conexion.php";

class admin extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_user() { 
		$user = false;
		$result = $this->db->query("SELECT name, value FROM ".PREFIX."const"); 
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$user[$row['name']] = $row['value'];
			} 
		}
		
		return $user;
	} 

	public function get_moneda($code_iso) {
		$result = $this->db->query("SELECT unicode FROM ".PREFIX."c_currencies WHERE code_iso='".$code_iso."'"); 
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$unicode = str_replace('[','',$row['unicode']);
		$unicode = str_replace(']','',$unicode);
		$a = mb_convert_encoding("&#{$unicode};", "UTF-8", 'HTML-ENTITIES');
		return $a;
	}

	public function get_rfc_empresa(){
		$sql = 'SELECT
						*
					FROM
						llx_const AS cons
					WHERE
						cons.`name` = "MAIN_INFO_SIREN"
					AND cons.entity ='.ENTITY;
		$query= $this->db->query($sql); 
		if ($query)
			while($row = $query->fetch_object())
    			return  $row->value;
    	return false;
	}
}