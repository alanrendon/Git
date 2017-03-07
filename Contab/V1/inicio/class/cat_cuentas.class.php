<?php
require_once $url[0]."conex/conexion.php";

class Cuenta extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_cuentas() {
        $sql ="SELECT * FROM ".PREFIX."contab_cat_ctas WHERE afectacion=1 ORDER BY  codagr ASC";

		$cuenta = false;
		$result = $this->db->query($sql);
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    $cuenta[$row['codagr']] = $row['codagr'].' - '. ($row['descripcion']);
			}
		}
		return $cuenta;
	}
    
    public function get_not_grup_cuentas() {
        $sql ="SELECT
              ".PREFIX."contab_cat_ctas.codagr,
              ".PREFIX."contab_cat_ctas.descripcion,
              ".PREFIX."contab_cuentas_poliza.codagr AS c
            FROM
              ".PREFIX."contab_cat_ctas
            LEFT JOIN
              ".PREFIX."contab_cuentas_poliza ON ".PREFIX."contab_cuentas_poliza.codagr = ".PREFIX."contab_cat_ctas.codagr
            WHERE
              afectacion = 1 AND ".PREFIX."contab_cuentas_poliza.codagr IS NULL
            ORDER BY
              codagr ASC";

		$cuenta = false;
        echo $sql;
		$result = $this->db->query($sql);
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    $cuenta[$row['codagr']] = $row['codagr'].' - '. ($row['descripcion']);
			}
		}
		return $cuenta;
	}

    public function get_cuentas_csv() {
		$cuenta =  array();
		$sql    = "SELECT 
                    nivel, 
                    codagr, 
                    descripcion, 
                    natur, 
                    afectacion, 
                    codsat
                    FROM ".PREFIX."contab_cat_ctas 
                ORDER BY 
                codagr ASC";
		$result = $this->db->query($sql);
        
        if($result){
            while ($row = $result->fetch_assoc()) {
                $cuenta[]=$row;
            }
        }
		
		return $cuenta;
	}

	public function get_cuentas_agrupacion() {
		$cuenta = false;
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE afectacion=0  AND nivel=1 ORDER BY codagr ASC");
		if ($result) {
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$cuenta[$row['rowid']] = $row['codagr'].' - '. ($row['descripcion']);
			}
		}

		return $cuenta;
	}

	public function existe($rowid) {
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cuentas_rel WHERE fk_cuenta=".$rowid."AND afectacion=1 ORDER BY codagr ASC");
		if ($result) {
			$row = mysqli_fetch_array($result);
			return $row['no'];
		}
		return false;

	}

    public function existe_cta($cta) {
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cat_ctas WHERE codagr='".$cta."' AND afectacion=1");
        if ($result) {
			$row = mysqli_fetch_array($result);

			return $row['no'];
		}
		return false;

	}

	public function get_nom_cuenta($cuenta) {
        
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE codagr='".$cuenta."' ORDER BY codagr ASC");
  
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		return  ($row['descripcion']);
	}

	public function get_codagr_cuenta($id) {
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE rowid='".$id."' ORDER BY codagr ASC");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['codagr'];
	}

	public function get_cuentas_agrupacion_in_fin($cta_in,$cta_fin){
		$row = false;
		if($cta_in>=$cta_fin){
			$aux     = $cta_fin;
			$cta_fin = $cta_in;
			$cta_in  = $aux;
		}

		$sql = "SELECT
					sat.natur,
					sat.codagr,
                    sat.descripcion,
                    sat.nivel,
                    sat.afectacion,
                    (
					CASE sat.natur
						WHEN 'D' THEN
							'Deudora'
						WHEN 'A' THEN
							'Acredora'
						END
					) AS naturaleza
					FROM
					".PREFIX."contab_cat_ctas AS sat
					WHERE
						 sat.codagr BETWEEN '".$cta_in."' AND '".$cta_fin."'
					ORDER BY
						sat.codagr ASC
		";
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{

				$rows[] = $row;
			}
		}
		return $rows;
	}

    public function get_cuentas_agrupacion_in_fin_padres($cta_in,$cta_fin){
		$row = array();
		if($cta_in>=$cta_fin){
			$aux     = $cta_fin;
			$cta_fin = $cta_in;
			$cta_in  = $aux;
		}

		$sql = "SELECT
					sat.natur,
					sat.codagr,
                    sat.descripcion,
                    sat.nivel,
                    sat.afectacion,
                    (
					CASE sat.natur
						WHEN 'D' THEN
							'Deudora'
						WHEN 'A' THEN
							'Acredora'
						END
					) AS naturaleza
					FROM
					".PREFIX."contab_cat_ctas AS sat
					WHERE
                        sat.nivel=1
                    AND
                        sat.afectacion=0
                    AND
						 sat.codagr BETWEEN '".$cta_in."' AND '".$cta_fin."'
					ORDER BY
						sat.codagr ASC
		";

		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{

				$rows[] = $row;
			}
		}
		return $rows;
	}
   function update_cuenta($codigo_cuenta,$nombre_cuenta,$nivel_cuenta,$naturaleza_cuenta,$rowid,$codigo_sat,$afectada){
       $row = false;
        $sql = 'UPDATE `'.PREFIX.'contab_cat_ctas`
                SET
                    `nivel` = "'.$nivel_cuenta.'",
                    `codagr` = "'.$codigo_cuenta.'",
                    `descripcion` = "'.$nombre_cuenta.'",
                    `natur` = "'.$naturaleza_cuenta.'",
                    `codsat` = "'.$codigo_sat.'",
                    `afectacion` = "'.$afectada.'"
                WHERE
                    (`rowid` = '.$rowid.')';

       $query= $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
   }

    public function eliminar_cuenta($rowid) {
        $sql = " SELECT
                    codagr
                  FROM
                    ".PREFIX."contab_cat_ctas
                  WHERE
                     rowid=".$rowid;
        $req=$this->db->query($sql);
        if($req){
            $res=$req->fetch_object();

                $sql =" SELECT
                              count( a.rowid ) AS cta
                        FROM
                             ".PREFIX."contab_polizasdet a,
                             ".PREFIX."contab_polizas b
                        WHERE
                              a.cuenta = '".$res->codagr."'
                        AND
                              a.fk_poliza = b.rowid
                        AND
                               b.entity =".ENTITY;
                $req=$this->db->query($sql);
                $res=$req->fetch_object();
                if($res->cta==0){
                    $this->db->query("DELETE FROM ".PREFIX."contab_cat_ctas WHERE rowid=".$rowid);
                    
                }

             return true;
        }


        return false;

	}





}
