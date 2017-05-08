<?php

require_once $url[0]."conex/conexion.php";

class impuestos extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_impuestos() {
		$rows = array();

		$sql = "SELECT rowid, nombre,impuesto,tipo FROM ".PREFIX."contab_impuestos ORDER BY rowid DESC";
		$query = $this->db->query($sql);

		if ( $query ) 
			while($row = $query->fetch_object())
				$rows[] = $row;
		return $rows;
	}
    
    public function existe($rowid) { 
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cuentas_rel WHERE fk_type = 10 AND fk_object=".$rowid);
		$row = mysqli_fetch_array($result);

        return $row['no'];
	}

    function eliminar_impuesto($rowid){
        $sql = "DELETE FROM ".PREFIX."contab_impuestos WHERE rowid=".$rowid;
        $result = $this->db->query($sql);
		if ( $result ) {
			return true;
		}

		return false;
    }


}

?>
