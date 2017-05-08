<?php
class carga_cvs extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_total($cod) { 
       
              $sql = "SELECT count(*) as no FROM ".PREFIX."contab_cat_ctas WHERE codagr='".$cod."'";
            $result = $this->db->query($sql);
            $row = mysqli_fetch_assoc($result);

            return $row['no'];
      
        return 0;

	} 

	public function insert($nivel, $cod, $desc, $natur,$afectada,$codsat) { 
         if(empty($codsat) || $codsat=='' || !isset($codsat) || $codsat==null){
             $codsat  = 0;
         }
        $sql ="INSERT INTO ".PREFIX."contab_cat_ctas (nivel, codagr, descripcion, natur, afectacion, entity,codsat ) 
						VALUES(".$nivel.",'".$cod."','".$desc."','".$natur."','".$afectada."', '".ENTITY."','".$codsat."')";
		$result = $this->db->query($sql);
        
		return ($result) ? 1: 0;
	}
    
    public function update($nivel, $cod, $desc, $natur,$afectada,$codsat){
         if(empty($codsat) || $codsat=='' || !isset($codsat) || $codsat==null){
             $codsat  = 0;
         }
        $sql = 'UPDATE `llx_contab_cat_ctas`
                SET 
                    `nivel`         = "'.$nivel.'",
                    `descripcion`   = "'.$desc.'",
                    `natur`         = "'.$natur.'",
                    `afectacion`    = "'.$afectada.'",
                    `codsat`        = "'.$codsat.'"
                WHERE
                    `codagr` = "'.$cod.'"';
        $result = $this->db->query($sql);
        return ($result) ? 1: 0;
    }
}
?>