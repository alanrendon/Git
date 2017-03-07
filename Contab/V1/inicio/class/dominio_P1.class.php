<?php
require_once $url[0]."conex/conexion.php";

class Dominio_P1 extends conexion {
    
    public $rowid;
    public $dominio;
    public $cupon;
    
	public function __construct() {
		parent::__construct();
	}
    
    
    public function registro(){
        $sql='INSERT INTO 
                `'.PREFIX.'contab_dominio_registro` 
                    (
                    `dominio`, 
                    `cupon`
                    )
                VALUES
                    (
                        "'.$this->dominio.'", 
                        "'.$this->cupon.'"
                    )';

        $query =$this->db->query($sql);
        
		if($this->db->affected_rows>0){
            $sql    = 'SELECT max(rowid) as id FROM `'.PREFIX.'contab_dominio_registro` ';
            $query  = $this->db->query($sql);
            $row    = $query->fetch_object();
           
            return $row->id;
        }
        
        return 0;
    }
	
}

?>