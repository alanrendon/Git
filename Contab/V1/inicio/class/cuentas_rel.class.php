<?php
require_once $url[0]."conex/conexion.php";

class Rel_Cuenta extends conexion {     
  public function __construct() { 
    parent::__construct(); 
  } 

  public function get_cuentasRelFact($tipo,$cuentaObj) { 

    $row = array();
        
    $sql ="SELECT
                    cuenta_rel.fk_object,
                    cuenta_rel.fk_type,
                    cuentas.descripcion,
                    cuentas.codagr
        FROM
          ".PREFIX."contab_cuentas_rel AS cuenta_rel 
        INNER JOIN ".PREFIX."contab_cat_ctas AS cuentas ON cuentas.rowid=cuenta_rel.fk_cuenta
        WHERE
                    cuenta_rel.fk_type = ".$tipo."
                AND
          cuenta_rel.fk_object =".$cuentaObj."          
                LIMIT 1
        ";

    $query= $this->db->query($sql); 
    if ($query) {
      $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    }
     return  $row;
    
  }

  public function get_cuentasNotRelFact($tipo,$cuentaObj) { 
    $rows = false;
    $sql ="SELECT
        cuenta_rel.fk_object,
        cuenta_rel.fk_type,
        cuentas.descripcion,
        cuentas.codagr
        FROM
          ".PREFIX."contab_cuentas_rel AS cuenta_rel 
        INNER JOIN ".PREFIX."contab_cat_ctas AS cuentas ON cuentas.rowid=cuenta_rel.fk_cuenta
        WHERE
          cuenta_rel.fk_type = ".$tipo."
        AND 
          cuenta_rel.fk_object=".$cuentaObj."
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

  public function get_cuentasRelBank($tipo,$cuentaObj) { 
    $rows = false;

    $sql="
        SELECT
          cuenta_rel.fk_object,
          cuenta_rel.fk_type,
          cuentas.descripcion,
          cuentas.codagr,
          c.ref
        FROM
          ".PREFIX."contab_cuentas_rel AS cuenta_rel
        INNER JOIN ".PREFIX."contab_cat_ctas AS cuentas ON cuenta_rel.fk_cuenta=cuentas.rowid
        INNER JOIN ".PREFIX."bank_account as c on c.rowid=cuenta_rel.fk_object
        WHERE
          cuenta_rel.fk_type = ".$tipo."
        AND cuentas.codagr = '".$cuentaObj."'
        LIMIT 1
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
    
    public function get_cuenta_iva(){
        $sql = 'SELECT
                        rel.rowid,
                        rel.fk_type,
                        rel.fk_object,
                        sat.descripcion,
                        sat.codagr
                    FROM
                        '.PREFIX.'contab_cuentas_rel AS rel
                    INNER JOIN '.PREFIX.'contab_cat_ctas AS sat ON rel.fk_cuenta = sat.rowid
                    WHERE
                            rel.fk_type = 10';
        $query= $this->db->query($sql); 
         $row = $query->fetch_assoc();
    if (count($row)>0) {
           
            $row['descripcion']= ($row['descripcion']);
            return $row;
    }
    return false;
    }
    
    
      public function get_cuenta_impuestos(){
           $cuenta = array();
        $sql = 'SELECT
                  rel.rowid,
                  rel.fk_type,
                  rel.fk_object,
                  cat.descripcion,
                  cat.codagr
                FROM
                  '.PREFIX.'contab_cuentas_rel AS rel
                INNER JOIN
                  '.PREFIX.'contab_cat_ctas AS cat ON rel.fk_cuenta = cat.rowid
                INNER JOIN
                  '.PREFIX.'contab_impuestos AS imp ON imp.rowid = rel.fk_object
                 AND 
                    imp.nombre LIKE "%IVA%"
                WHERE
                  rel.fk_type = 10';
        $query= $this->db->query($sql); 
        if ($query) {
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
          $cuenta[$row['codagr']] = $row['codagr'].' - '. ($row['descripcion']);
      }
           
    }
       
    return $cuenta;
    } 
    
    public function get_cuenta_bancos(){
        $cuenta = array();
        $sql = 'SELECT
                        rel.rowid,
                        rel.fk_type,
                        rel.fk_object,
                        sat.descripcion,
                        sat.codagr
                    FROM
                        '.PREFIX.'contab_cuentas_rel AS rel
                    INNER JOIN '.PREFIX.'contab_cat_ctas AS sat ON rel.fk_cuenta = sat.rowid
                    WHERE
                            rel.fk_type = 5';
        $query= $this->db->query($sql); 

    
    if ($query) {
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
          $cuenta[$row['codagr']] = $row['codagr'].' - '. ($row['descripcion']);
      }
           
    }
       
    return $cuenta;
    }
 
    
    public function delete_not_rel_ctas(){
        $sql = 'TRUNCATE '.PREFIX.'contab_cat_ctas';
    if ($this->db->query($sql)) {
           $sql = 'TRUNCATE '.PREFIX.'contab_cuentas_rel';
            if($this->db->query($sql)){
                 $sql = 'TRUNCATE '.PREFIX.'contab_grupos';
                 if($this->db->query($sql)){
                        $sql = 'TRUNCATE '.PREFIX.'contab_polizas';
                        if($this->db->query($sql)){
                            $sql = 'TRUNCATE '.PREFIX.'contab_polizasdet';
                             if($this->db->query($sql)){
                                 $sql = 'TRUNCATE '.PREFIX.'contab_polizas_docto';
                                 if($this->db->query($sql)){
                                      $sql = 'TRUNCATE '.PREFIX.'contab_cuentas_poliza';
                                         if($this->db->query($sql)){
                                             
                                         }
                                 }
                             }
                        }
                 }
            }
            return true;    
    }
    return false;
    }
    
    public function insert($fk_producto, $fk_cuenta,$tipo) { 
      $result = $this->db->query("INSERT INTO ".PREFIX."contab_cuentas_rel ( entity, fk_type, fk_object, fk_cuenta ) 
            VALUES(".ENTITY.",".$tipo.",".$fk_producto.",".$fk_cuenta.")");
    return ( $result ) ? 1: 0;
   }

    function update_cuenta_rel($id,$cuenta){
        $sql = 'UPDATE '.PREFIX.'contab_cuentas_rel SET `fk_cuenta`='.$cuenta.' WHERE (`rowid`='.$id.')';
        $query= $this->db->query($sql); 

    if ($query) {
          
            return true;
    }
    return false;
    }

  
}