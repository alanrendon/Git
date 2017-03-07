<?php
require_once $url[0]."conex/conexion.php";

class Usuario extends conexion { 
    
    public $usuario;
    public $contra;
    public $rowid;
    
	public function __construct() { 
		parent::__construct(); 
	} 
    
   
    
	function validar_UsuarioContra(){
        $sql =  "SELECT
                    u.rowid
                FROM
                    ".PREFIX."user AS u
                INNER JOIN  ".PREFIX."user_rights AS r ON r.fk_user = u.rowid
                INNER JOIN ".PREFIX."rights_def AS rd ON rd.id = r.fk_id
                WHERE
                    u.login = '".$this->usuario."'
                AND 
                    (
                            u.pass = '".$this->contra."'
                        OR
                            u.pass_crypted = '".md5($this->contra)."'
                    )
                AND u.statut = 1
                AND
                    rd.module LIKE 'Contab'
                LIMIT 1";
            $result = $this->db->query($sql); 
            if ($result) {
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                   $this->rowid = $row['rowid'];
                    return $row['rowid'];
                }
            }
            return false;
    }
    
    function comparar_token($key){
        $sql   ='SELECT 
                    u.rowid
                FROM 
                    `'.PREFIX.'contab_dol_login` AS dl
                 INNER JOIN  '.PREFIX.'user AS u ON u.login = dl.dol_login
                WHERE 
                    dl.login_key = "'.$key.'" LIMIT 1;';
                
            $result = $this->db->query($sql); 
            if ($result->num_rows>0) {
                while ($row = $result->fetch_object()) {
                    return $row->rowid;
                }
            }
            return false;
    }
    
    function multiempresa(){
        $rows = array();
        $sql ="show tables like '".PREFIX."entity'";
        
        $result = $this->db->query($sql); 
        if ($result && $result->num_rows>0) {
            $sql ="SELECT  * FROM ".PREFIX."entity";
            $result = $this->db->query($sql); 
      
            while ($row = $result->fetch_object()) {
                    $rows[]= $row;
            }
            return $rows;
        }
        return false;
        
    }
    
    function get_multiempresa($id){
        $rows = array();
        
	    $sql ="show tables like '".PREFIX."entity'";
        $result = $this->db->query($sql); 

        if ($result && $result->num_rows>0) {
              $sql ="SELECT * FROM ".PREFIX."entity where rowid=".$id;

	          $result = $this->db->query($sql); 
 	          if($result) 
                  return $result->fetch_object();
        }
        return false;
        
    }
}
