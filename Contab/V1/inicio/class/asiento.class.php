<?php
require_once $url[0]."conex/conexion.php";

class Asiento extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_asientoPoliza($id_poliza) { 
		$rows = array();
		$sql ="SELECT
					asiento.asiento,
					asiento.cuenta,
					asiento.debe,
					asiento.haber,
					asiento.rowid,
					asiento.descripcion
				FROM
					".PREFIX."contab_polizasdet AS asiento
				WHERE
					asiento.fk_poliza = '".$id_poliza."'";
		
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

	public function get_asiento($id_asiento) { 
		$rows = array();
		$sql ="SELECT
					asiento.asiento,
					asiento.cuenta,
					asiento.debe,
					asiento.haber,
					asiento.rowid,
					asiento.descripcion
				FROM
					".PREFIX."contab_polizasdet AS asiento
				WHERE
					asiento.rowid = '".$id_asiento."'";
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
    
    public function get_asiento_obj_id($id_asiento) { 
		$rows =  array();
		$sql ="SELECT
					asiento.asiento,
					asiento.cuenta,
					asiento.debe,
					asiento.haber,
					asiento.rowid,
					asiento.descripcion
				FROM
					".PREFIX."contab_polizasdet AS asiento
				WHERE
					asiento.rowid = '".$id_asiento."'";
		$query= $this->db->query($sql); 
		if ($query) {
			$rows = array();
			while($row = $query->fetch_object())
			{
				return $row;
			}		
		}
		return false;
	} 

	public function get_asientobyCta_Natu($cta,$natu,$periodo) { 
        $date = new DateTime($periodo[0]);
		$periodo[0] =$date->format('Y-m-d');
        
        $date = new DateTime($periodo[1]);
		$periodo[1] =$date->format('Y-m-d');
        
		$sql ="SELECT
					asiento.asiento,
					asiento.cuenta,
					asiento.debe,
					asiento.haber,
					asiento.rowid,
					asiento.descripcion
                    
				FROM
					".PREFIX."contab_polizasdet AS asiento
                INNER JOIN ".PREFIX."contab_polizas AS poliza ON poliza.rowid=asiento.fk_poliza
                WHERE
                   asiento.cuenta = '".$cta."' 
                AND
                    poliza.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."' 
                  ORDER BY
                asiento.cuenta ASC
             ";
		$query= $this->db->query($sql); 
		if ($query) {

			while($row = $query->fetch_assoc())
			{
				if($natu=='D'){
					return $row['debe']-$row['haber'];
				}else if($natu=='A'){
					return $row['haber']-$row['debe'];
				}
			}		
		}
		return 0;
	} 
	
	public function get_sumasientobyCta_Natu($cta,$natu,$periodo) {
        $sum=0;
        $sql ='SELECT
                    *
                FROM
                    llx_contab_cat_ctas AS cta

                WHERE
                    cta.codagr LIKE "%'.$cta.'%"
                AND
                    cta.nivel !=1
                AND 
                    cta.afectacion !=0
                ORDER BY
                    cta.codagr ASC;';
        $query= $this->db->query($sql);
        $suma= array();
        $res= array();
        $suma['debe'] = 0;
        $suma['haber'] = 0;

		if ($query) {
	
			while($row = $query->fetch_object())
			{
				$res=$this->get_suma_asientos($row->codagr,$row->natur,$periodo);
			    $suma['debe']  += $res['debe'];
                $suma['haber'] += $res['haber'];
            }
		}
        
        
		return $suma;
	
	}
    
    public function get_suma_asientos($cta,$natu,$periodo){
        $porciones = explode("/", $periodo[0]);
        
        if($porciones [0]<13 ){
            $date = new DateTime($periodo[0]);
            $periodo[0] =$date->format('Y-m-d');

            $date = new DateTime($periodo[1]);
            $periodo[1] =$date->format('Y-m-d');
        }else{
                $periodo[0] = str_replace("/","-",$periodo[0]);
                $periodo[1] = str_replace("/","-",$periodo[1]);

                $periodo[1] = str_replace("13","12",$periodo[1]);
                $periodo[0] = str_replace("13","12",$periodo[0]);
                $periodo[0] = $porciones[0];
                $periodo[1] = $porciones[0];
                $ajuste = true;
        }
        $sql ="SELECT
					asiento.asiento,
					asiento.cuenta,
					sum(asiento.debe) as debe,
					sum(asiento.haber) as haber,
					asiento.rowid,
					asiento.descripcion
				FROM
					".PREFIX."contab_polizasdet AS asiento
                INNER JOIN ".PREFIX."contab_polizas AS poliza ON poliza.rowid=asiento.fk_poliza
                WHERE
                   asiento.cuenta = '".$cta."'";
                if(isset($ajuste) && $ajuste){
                     $sql .=" AND
                                poliza.anio BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
                            AND
                                poliza.ajuste = 1
                            GROUP BY
                                asiento.rowid
                            ORDER BY
                                asiento.cuenta ASC;";
                }else{
                     $sql .=" AND
                                poliza.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'
                           GROUP BY
                                asiento.rowid
                            ORDER BY
                                asiento.cuenta ASC;
                     ";
                }
       $query= $this->db->query($sql);
        
       $suma= array();
       $suma['debe'] = 0;
       $suma['haber'] = 0;
       if ($query) {
	
			while($row = $query->fetch_assoc())
			{
                $row['haber']*=-1;
                
                if($row['debe'] <> 0 && $natu == 'A'){
                    $row['debe']*=-1;
                }else if($row['haber'] <> 0 && $natu == 'D'){
                    $row['haber']*=-1;
                }
				$suma['nat']   = $natu;
				$suma['cta']   = $row['cuenta'];
				$suma['debe']  += $row['debe'];
				$suma['haber'] += $row['haber'];
			}
		}
        return $suma;
    }

	public function put_asiento($fk_poliza,$asiento,$cuenta,$debe,$habe,$descripcion){
		 if($habe<0){
            $habe = -1* floatval(preg_replace('/[^\d.]/', '', $habe));
        }else{
            $habe = floatval(preg_replace('/[^\d.]/', '', $habe));
        }
    
        if($debe<0){
            $debe = -1* floatval(preg_replace('/[^\d.]/', '', $debe));
        }else{
            $debe = floatval(preg_replace('/[^\d.]/', '', $debe));
        }
		$sql = "INSERT INTO ".PREFIX."contab_polizasdet (
						fk_poliza,
						asiento,
						cuenta,
						debe,
						haber,
						descripcion
					)
					VALUES
						('".$fk_poliza."','".$asiento."','".$cuenta."','".$debe."','".$habe."','".$descripcion."')";
		$query= $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}

	public function update_asiento($cuenta,$debe,$habe,$rowid,$descripcion){
       
        if($habe<0){
            $habe = -1* floatval(preg_replace('/[^\d.]/', '', $habe));
        }else{
             $habe = floatval(preg_replace('/[^\d.]/', '', $habe));
        }
    
        if($debe<0){
            $debe = -1* floatval(preg_replace('/[^\d.]/', '', $debe));
        }else{
             $debe = floatval(preg_replace('/[^\d.]/', '', $debe));
        }
        
		$sql = "UPDATE ".PREFIX."contab_polizasdet
				SET cuenta = '".$cuenta."',
				 debe = '".$debe."',
				 haber = '".$habe."',
				 descripcion = '".$descripcion."'
				WHERE
					(rowid = '".$rowid."')
				LIMIT 1";
		
		$query = $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}

	public function  delete_asiento($rowid){
		$sql="	DELETE
				FROM
					 ".PREFIX."contab_polizasdet
				WHERE
					(rowid = '".$rowid."')
				LIMIT 1";
		$query= $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}


	public function delete_allAsientos($fk_poliza){
		$sql="	DELETE
				FROM
					 ".PREFIX."contab_polizasdet
				WHERE
					(fk_poliza = '".$fk_poliza."')
				LIMIT 1";
		$query= $this->db->query($sql); 
		if ($query) {
			return true;	
		}
		return false;
	}

	function get_lastAsiento($fk_poloza){
		$sql="SELECT
					Max(asiento.asiento) as ultimo
				FROM
					".PREFIX."contab_polizasdet AS asiento
				WHERE
					asiento.fk_poliza = '".$fk_poloza."'";
		$query= $this->db->query($sql); 
		if ($query) {
			$row=$query->fetch_assoc();
			return $row['ultimo']+1;	
		}
		return 1;
	}
    
    public function clonar_asiento($id,$poliza){
        $sql = "INSERT INTO ".PREFIX."contab_polizasdet SELECT
                    0,
                   ".$poliza.",
                    p.asiento,
                    p.cuenta,
                    p.debe,
                    p.haber,
                    p.descripcion
                FROM
                     ".PREFIX."contab_polizasdet AS p
                WHERE
                    p.rowid = ".$id.";
        ";
     
        $query= $this->db->query($sql); 
		if ($query) {
            $sql = "(SELECT MAX(rowid) AS id FROM ".PREFIX."contab_polizasdet)";
			$query= $this->db->query($sql); 
            $row = $query->fetch_assoc();
            return $row['id'];
		}
		return false;
    }

}