<?php
require_once $url[0]."conex/conexion.php";

class Grupo extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function put_grupo($grupo,$codigo_agrupador,$cuenta_inicial,$cuenta_final,$grupo_padre,$tipo){
		$sql = "INSERT INTO ".PREFIX."contab_grupos (
						grupo,
						fk_codagr_ini,
						fk_codagr_fin,
						tipo_edo_financiero,
						fk_grupo
					)
					VALUES
						('".$grupo."',".$cuenta_inicial.",".$cuenta_final.",".$tipo.",".$grupo_padre.")";
		
		$query = $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
	}

	public function update_grupo($grupo_padre,$grupo,$codigo_agrupador,$cuenta_inicial,$cuenta_final,$rowid) {
		$sql = "UPDATE ".PREFIX."contab_grupos
				SET 
                    grupo = '".$grupo."',
				    fk_codagr_ini = '".$cuenta_inicial."',
				    fk_codagr_fin = '".$cuenta_final."',
				    fk_grupo = '".$grupo_padre."'
				WHERE
					(rowid = '".$rowid."')
				LIMIT 1";
		$query = $this->db->query($sql);
		if ($query) {
			return $sql;
		}
		return false;
	}

	public function get_grupo($idgrupo) {
		$rows = false;
		$sql ="SELECT
					g.rowid,
					g.grupo,
					g.fk_codagr_rel,
					g.fk_codagr_ini,
					g.fk_codagr_fin,
					g.tipo_edo_financiero,
					g.fk_grupo
				FROM
					".PREFIX."contab_grupos g
				WHERE
					g.rowid = '".$idgrupo."'";
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
                $row['grupo'] =  ($row['grupo']);
				$rows[] = $row;
			}
		}
		return $rows;
	}
    

}
