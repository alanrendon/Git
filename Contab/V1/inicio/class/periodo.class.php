<?php
require_once $url[0]."conex/conexion.php";

class Periodo extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function validate_Periodo($anio,$mes) {
        

        $fechaUltimo=$this->get_ultimo_periodo_abierto();
        
        if($fechaUltimo->mes == 13 ){
        
            return array();
        }
        
		$rows = array();
		$sql ="SELECT
					rowid,
					estado
				FROM
				".PREFIX."contab_periodos
				WHERE
					anio='".$anio."'
				AND
					mes='".$mes."'
				AND
					estado IN(1,2);";
		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_assoc();
		}
		return false;
	}
    
    public function get_primer_periodo(){
        $sql ="SELECT
					min(anio) as anio
				FROM
				".PREFIX."contab_periodos";
		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_object();
		}
		return false;
    }
    
    public function get_ultimo_periodo_abierto(){
        $sql ="SELECT
					max(anio) as anio,
                    max(mes) as mes,
                    (
					CASE max(mes)
						WHEN '1' THEN
							'Enero'
						WHEN '2' THEN
							'Febrero'
						WHEN '3' THEN
							'Marzo'
						WHEN '4' THEN
							'Abril'
						WHEN '5' THEN
							'Mayo'
						WHEN '6' THEN
							'Junio'
						WHEN '7' THEN
							'Julio'
						WHEN '8' THEN
							'Agosto'
						WHEN '9' THEN
							'Septiembre'
						WHEN '10' THEN
							'Octubre'
						WHEN '11' THEN
							'Noviembre'
						WHEN '12' THEN
							'Dicembre'
                        WHEN '13' THEN
							'Ajuste'
						END
					) AS mes_name
				FROM
				".PREFIX."contab_periodos
                WHERE
					estado IN(1,2)
                ";
		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_object();
		}
		return false;
    }
    
     public function get_ultimo_periodo_cerrado(){
        $sql ="SELECT
					anio,
                    mes
				FROM
				".PREFIX."contab_periodos
                WHERE
					estado =0
                 ORDER BY rowid DESC
                    LIMIT 1
                ";

		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_object();
		}
		return false;
    }
    
     public function get_periodo_id_cerrado($id){
        $sql ="SELECT
					anio,
                    mes,
                    estado
				FROM
				".PREFIX."contab_periodos
                WHERE
					estado =0
                AND 
                    rowid = ".$id."
                 ORDER BY rowid DESC
                    LIMIT 1
                ";

		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_object();
		}
		return false;
    }
    public function get_periodo_id($id){
        $sql ="SELECT
					anio,
                    mes,
                    estado
				FROM
				".PREFIX."contab_periodos
                WHERE 
                    rowid = ".$id."
                 ORDER BY rowid DESC
                    LIMIT 1
                ";

		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_object();
		}
		return false;
    }

	public function validar_existe_Periodo($anio,$mes) {
		$rows = false;
		$sql ="SELECT
					rowid,
					estado
				FROM
				".PREFIX."contab_periodos
				WHERE
					anio='".$anio."'
				AND
					mes='".$mes."'
					";
		$query= $this->db->query($sql);
		if ($query) {
			return $query->fetch_assoc();
		}
		return false;
	}
    
    public function validar_nuevo_periodo() {
		$rows = false;
		$sql ="SELECT
					rowid,
					estado
				FROM
				".PREFIX."contab_periodos
				WHERE
					estado IN(1,2)
					";
		$query= $this->db->query($sql);
		if ($query) {
			return $query->num_rows;
		}
		return false;
	}

	public function get_AnioPeriodo() {
		$rows = false;
		$sql ="SELECT
					anio
				FROM
				".PREFIX."contab_periodos
				GROUP BY
				anio";
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

	public function put_Periodo($anio,$mes) {
		$sql ="INSERT INTO ".PREFIX."contab_periodos (anio, mes, estado) VALUES ('".$anio."', '".$mes."','2')";
		$query= $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
	}


	public function get_Periodos($anio) {
		$anio=(int)$anio;
		$rows = false;
		$sql ="SELECT
					periodo.anio,
					(
					CASE periodo.mes
						WHEN '1' THEN
							'Enero'
						WHEN '2' THEN
							'Febrero'
						WHEN '3' THEN
							'Marzo'
						WHEN '4' THEN
							'Abril'
						WHEN '5' THEN
							'Mayo'
						WHEN '6' THEN
							'Junio'
						WHEN '7' THEN
							'Julio'
						WHEN '8' THEN
							'Agosto'
						WHEN '9' THEN
							'Septiembre'
						WHEN '10' THEN
							'Octubre'
						WHEN '11' THEN
							'Noviembre'
						WHEN '12' THEN
							'Dicembre'
                        WHEN '13' THEN
							'Ajuste'
						END
					) AS mes,
                    periodo.mes AS m,
					periodo.estado,
					periodo.validado_bg,
					periodo.validado_bc,
					periodo.validado_er,
					periodo.validado_ld,
					periodo.validado_lm,
					periodo.rowid
				FROM
					".PREFIX."contab_periodos AS periodo";
				if (is_numeric($anio) && $anio!=0) {
		$sql .=" WHERE
					periodo.anio = '".$anio."'";
				}

		$sql .=" ORDER BY
					periodo.anio DESc,
					m ASC,
                    periodo.estado DESC
				;";
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
    
    public function get_all_Periodos_anio() {
		$rows =array ();
		$sql ="SELECT
					periodo.anio,
                    periodo.mes AS m,
					periodo.estado,
					periodo.rowid
				FROM
					".PREFIX."contab_periodos AS periodo";
		$sql .=" GROUP BY
	               periodo.anio
                ORDER BY
					periodo.anio DESC
				;";
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_object())
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}
    
    
	public function get_all_Periodos() {
		$rows = false;
		$sql ="SELECT
					periodo.anio,
					(
					CASE periodo.mes
						WHEN '1' THEN
							'Enero'
						WHEN '2' THEN
							'Febrero'
						WHEN '3' THEN
							'Marzo'
						WHEN '4' THEN
							'Abril'
						WHEN '5' THEN
							'Mayo'
						WHEN '6' THEN
							'Junio'
						WHEN '7' THEN
							'Julio'
						WHEN '8' THEN
							'Agosto'
						WHEN '9' THEN
							'Septiembre'
						WHEN '10' THEN
							'Octubre'
						WHEN '11' THEN
							'Noviembre'
						WHEN '12' THEN
							'Dicembre'
						END
					) AS mes,
                    periodo.mes AS m,
					periodo.estado,
					periodo.validado_bg,
					periodo.validado_bc,
					periodo.validado_er,
					periodo.validado_ld,
					periodo.validado_lm,
					periodo.rowid
				FROM
					".PREFIX."contab_periodos AS periodo";

		$sql .=" ORDER BY
					periodo.anio DESc,
					m ASC,
                    periodo.estado DESC
				;";
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

    public function cambiar_estado_reporte($id,$col){
        $sql = ('UPDATE '.PREFIX.'contab_periodos SET '.$col.'= IF('.$col.'=1, 0, 1) WHERE (rowid='.$id.') LIMIT 1;');
        $query= $this->db->query($sql);
		if ($query) {

                if( $this->total_estados_validados($id)>4){
                    $this->cambiar_estado_periodo($id,1);
                }else{
                     $this->cambiar_estado_periodo($id,2);
                }
			return true;
		}
		return false;

    }

    public function total_estados_validados ($id){
        $hola = $id;
        $sql="
            SELECT
                (
                    SUM(periodo.validado_bg)+
                    SUM(periodo.validado_bc)+
                    SUM(periodo.validado_er)+
                    SUM(periodo.validado_ld)+
                    SUM(periodo.validado_lm)
                ) AS total
            FROM
                ".PREFIX."contab_periodos AS periodo
            WHERE
                periodo.rowid='".$hola."';";

        $query= $this->db->query($sql);

		if ($query) {
            $row = $query->fetch_assoc();

			return (int)$row['total'];
		}
		return false;
    }

     public function cambiar_estado_periodo($id,$estado){
        $sql = ('UPDATE '.PREFIX.'contab_periodos SET estado='.$estado.' WHERE (rowid='.$id.') LIMIT 1');

        $query= $this->db->query($sql);

		if ($query) {
			return true;
		}
		return false;

    }

    public function cambiar_todoslosestados_reporte($id){
        $sql = "UPDATE `".PREFIX."contab_periodos`
                    SET `validado_bg` = '1',
                     `validado_bc` = '1',
                     `validado_er` = '1',
                     `validado_ld` = '1',
                     `validado_lm` = '1',
                     `estado` = '1'
                    WHERE
                        (`rowid` = '".$id."')
                    LIMIT 1";
         $query= $this->db->query($sql);

		if ($query) {
			return true;
		}
		return false;
    }

    public function cambiar_estado_periodo_inverso($id){
        $sql = ('UPDATE '.PREFIX.'contab_periodos SET estado= IF(estado=1, 0, 1) WHERE (rowid='.$id.') LIMIT 1;');
        $query= $this->db->query($sql);
		if ($query) {
			return true;
		}
		return false;
    }

}
