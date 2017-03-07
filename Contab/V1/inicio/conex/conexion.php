<?php
require_once $url[0]."conf/conf.php";
class conexion { 
	protected $db; 

	public function __construct()  { 
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); 

		if ( $this->db->connect_errno ) { 
			echo "Fallo al conectar a MySQL: ". $this->db->connect_error; 
			return;     
		} 

		$this->db->set_charset(DB_CHARSET); 
       $this->db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

	} 
}

