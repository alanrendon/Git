<?php
require_once $url[0]."conex/conexion.php";

class Contacto_P2 extends conexion {
  
    public $rowid;
	public $nombres;
    public $apelidos;
    public $email;
    public $tel;
    public $fk_dominio_Paso1;
        
	public function __construct() {
		parent::__construct();
	}
    
    
    public function registro(){
        $sql='INSERT INTO 
                `'.PREFIX.'contab_datos_contacto_registro` 
                    (
                    `nombres`,
                    `apelidos`,
                    `email`,
                    `tel`,
                    `fk_dominio_Paso1`
                    )
                VALUES
                    (
                        "'.$this->nombres.'", 
                        "'.$this->apelidos.'", 
                        "'.$this->email.'", 
                        "'.$this->tel.'", 
                        "'.$this->fk_dominio_Paso1.'"
                    )';
        
        $query =$this->db->query($sql);

		if($this->db->affected_rows>0){
            $sql    = 'SELECT max(rowid) as id FROM `'.PREFIX.'contab_datos_contacto_registro` ';
            $query  = $this->db->query($sql);
            $row    = $query->fetch_object();
            return $row->id;
        }
        
        return 0;
    }
	
}

?>