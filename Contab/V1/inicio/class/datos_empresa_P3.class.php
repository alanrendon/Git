<?php
require_once $url[0]."conex/conexion.php";

class Datos_Empresa_P3 extends conexion {
  
    public $rowid;
    public $empresa;
    public $pais;
    public $ciudad;
    public $cp;
    public $tel;
    public $direccion;
    public $fk_datos_contacto_paso2;
        
	public function __construct() {
		parent::__construct();
	}
    
    
    public function registro(){
        $sql='INSERT INTO 
                `'.PREFIX.'contab_datos_empresa_registro` 
                    (
                    `empresa`,
                    `pais`,
                    `ciudad`,
                    `cp`,
                    `tel`,
                    `direccion`,
                    `fk_datos_contacto_paso2`
                    )
                VALUES
                    (
                        "'.$this->empresa.'", 
                        "'.$this->pais.'", 
                        "'.$this->ciudad.'", 
                        "'.$this->cp.'", 
                        "'.$this->tel.'",
                        "'.$this->direccion.'",
                        "'.$this->fk_datos_contacto_paso2.'"
                    )';
        
        $query =$this->db->query($sql);
        
		if($this->db->affected_rows>0){
            $sql    = 'SELECT max(rowid) as id FROM `'.PREFIX.'contab_datos_empresa_registro` ';
            $query  = $this->db->query($sql);
            $row    = $query->fetch_object();
            return $row->id;
        }
        
        return 0;
    }
	
}

?>