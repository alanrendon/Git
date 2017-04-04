<?php 

require_once $url[0]."conex/conexion.php";

class Tipo_Poliza extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function put_Tipopoliza($tipo,$abr){
		$sql = "INSERT INTO `".PREFIX."contab_tipo_grupo_poliza` (`nombre`, `abr`) VALUES
						(
                        '".$tipo."',
                        '".$abr."'
                        )";
            $query= $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
	}


	public function get_tipo_poliza() {
		$rows = array();
        
		$sql ="SELECT
					nombre,
					id,
					abr
				FROM
					llx_contab_tipo_grupo_poliza
				WHERE
					abr NOT IN ('CP','CxP','CPP','PP','PxP','PPP','PRS','PSS')
				ORDER BY
					abr";
		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function get_tipo_poliza_prearmado_clientes() {
		$rows = array();
        
		$sql ="SELECT
					nombre,
                    id,
                    abr
				FROM
					".PREFIX."contab_tipo_grupo_poliza
				WHERE
					abr IN('CP','CPP','CxP')
                ORDER BY
                abr";
		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function get_tipo_poliza_prearmado_proveedores() {
		$rows = array();
        
		$sql ="SELECT
					nombre,
                    id,
                    abr
				FROM
					".PREFIX."contab_tipo_grupo_poliza
				WHERE
					abr IN('PP','PXP','PPP')
                ORDER BY
                abr";
		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				$rows[] = $row;
			}
		}
		return $rows;
	}

	

	public function get_tipo_poliza_prearmado_stock() {
		$rows = array();
        
		$sql ="SELECT
					nombre,
                    id,
                    abr
				FROM
					".PREFIX."contab_tipo_grupo_poliza
				WHERE
					abr IN('PSS','PRS','AAP')
                ORDER BY
                abr";
		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				$rows[] = $row;
			}
		}
		return $rows;
	}
    
    public function get_tipo_poliza_con_ctas() {
		$rows = array();
        
		$sql ="SELECT
					gp.nombre,
                    gp.id,
                    gp.abr,
                    ctap.codagr
				FROM
					".PREFIX."contab_tipo_grupo_poliza AS gp
                INNER  JOIN ".PREFIX."contab_cuentas_poliza AS ctap ON ctap.fk_tipo = gp.abr
                WHERE
                	abr NOT IN ('CP','CxP','CPP','PP','PxP','PPP','PRS','PSS')
                GROUP BY
                    gp.id
                ORDER BY abr";

		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				$rows[] = $row;
			}
		}
		return $rows;
	}
    
    public function get_tipo_poliza_id($id) {
		$rows = array();
        
		$sql ="SELECT
					nombre,
                    id,
                    abr
				FROM
					".PREFIX."contab_tipo_grupo_poliza
                WHERE
                    id = ".$id."
                ORDER BY
                abr";
		$query= $this->db->query($sql);
        
		if ($query) {
			while($row = $query->fetch_object())
			{
    
				return $row;
			}
		}
		return $rows;
	}
    
    function eliminar($id){
        $sql = 'DELETE FROM `llx_contab_tipo_grupo_poliza` WHERE (`id`="'.$id.'")';
         $result = $this->db->query($sql); 
		if ($result) {
			return true;
		}
		
		return false;
    }
    

}

?>