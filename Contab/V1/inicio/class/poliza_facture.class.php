<?php
require_once $url[0]."conex/conexion.php";
class Poliza_Facture extends conexion {
  public $id_facture;
  public $id_poliza;
  public $type;

	public function __construct() {
		parent::__construct();
	}

  function put_facture_poliza(){
    $sql = 'INSERT INTO `llx_contab_poliza_facture` (`id_facture`, `id_poliza`, type) VALUES ("'.$this->id_facture.'", "'.$this->id_poliza.'", "'.$this->type.'")';
    $query = $this->db->query($sql);
    return $query;
  }

  function get_facture_poliza_by_facture($type){
    $rows = array();
    $sql = '  SELECT
              	*
              FROM
              	`llx_contab_poliza_facture`
              WHERE
              	`llx_contab_poliza_facture`.id_facture = "'.$this->id_facture.'"
              AND
                type ='.$this->type.'
              ';
    $query = $this->db->query($sql);
    if ($query) {
        while( $row = $query->fetch_object() ){
            $rows[] = $row;
        }
    }
    return $rows;
  }


  public function get_poliza_facture_id()
  {
      $rows = array();
      $sql = 'SELECT
                pol_fact.id_facture,
                pol_fact.id_poliza,
                pol_fact.type,
                pol_fact.rowid
              FROM
                llx_contab_poliza_facture AS pol_fact
              WHERE
                pol_fact.id_facture = "'.$this->id_facture.'"
              AND 
                pol_fact.type = "'.$this->type.'"';
      $query = $this->db->query($sql);
      if ($query) {
          while( $row = $query->fetch_object() ){
              $rows[] = $row;
          }
      }
      return $rows;
  }



  function get_facture_poliza_by_fpoliza(){
    $rows = array();
    $sql = '  SELECT
              	*
              FROM
              	`llx_contab_poliza_facture`
              WHERE
              	`llx_contab_poliza_facture`.id_poliza = "'.$this->id_poliza.'"
              ';
    $query = $this->db->query($sql);
    if ($query) {
        while( $row = $query->fetch_object() ){
            $rows[] = $row;
        }
    }
    return $rows;
  }


}
