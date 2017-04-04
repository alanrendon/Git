<?php
require_once $url[0]."conex/conexion.php";

class tipos_pagos extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

  

  public function get_tiposPagos() { 
    $rows = array();

    $string="SELECT
          a.rowid, a.libelle
        FROM
          llx_c_payment_term AS a
        LEFT JOIN llx_contab_payment_term AS b ON a.rowid = fk_payment_term
        WHERE
          b.rowid IS NULL;";
      
    $data = $this->db->query($string);
    
    if ($data) {    
       while($row = $data->fetch_object())
      {
        
        $rows[] = $row;
      }   
    }
    return $rows;
  }

  public function insert_paiement($fk_payment_term,$cond_pago)
  {
    $sql = 'INSERT INTO `llx_contab_payment_term` (
                  `entity`, 
                  `fk_payment_term`, 
                  `cond_pago`) 
                  VALUES (
                  '.ENTITY.',
                  '.$fk_payment_term.', 
                   '.$cond_pago.'
                   )';
    
    $data = $this->db->query($sql);
    return $data;
    
  }


  public function delete_paiement($id)
  {
    $sql = 'DELETE FROM `llx_contab_payment_term` WHERE (`rowid`='.$id.')';
    return $this->db->query($sql);
  }

  public function get_condiciones_pagos_asignadas() { 
    $rows = array();

    $string="SELECT
              a.rowid,
              a.fk_payment_term,
              a.cond_pago,
              b.libelle
            FROM
              llx_contab_payment_term AS a
            INNER JOIN llx_c_payment_term AS b ON a.fk_payment_term = b.rowid;";
      
    $data = $this->db->query($string);
    
    if ($data) {      
      while($row = $data->fetch_object())
      {
      
        switch ($row->cond_pago) {
          case 1:
            $row->cond_pago='Contado';
            break;
          case 2:
            $row->cond_pago='Crédito';
            break;
          case 3:
            $row->cond_pago='Anticipo';
            break;
          case 4:
            $row->cond_pago='50/50';
            break;            
          default:
            $row->cond_pago='N/A';
            break;
        }
               
        $rows[] = $row;
      }   
    }
    return $rows;

  }


}