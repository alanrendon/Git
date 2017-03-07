<?php
require_once $url[0]."conex/conexion.php";

class Commande_Fournisseur extends conexion {
  
    public $refPed;
    public $refProv;
    public $modPag;
    public $pagBase;
    public $iva;
    public $pagTotal;
    public $id;
        
    public function __construct() {
        parent::__construct();       
    }
    
    
    public function get_data_order(){
        $rows = array( );
        $sql='SELECT
                a.ref,
                a.fk_soc,
                a.ref_supplier,
                a.fk_mode_reglement,
                a.total_ht,
                a.tva,
                a.total_ttc,
                b.nom,
                a.rowid,
                b.name_alias
            FROM
            '.PREFIX.'commande_fournisseur AS a
            INNER JOIN '.PREFIX.'societe AS b ON a.fk_soc = b.rowid
            WHERE
                a.fk_statut = 5';

        $query =$this->db->query($sql);
        if ($query) {
           while ( $data=$query->fetch_object()) {
                switch ($data->fk_mode_reglement) {
                    case 2:
                            $data->fk_mode_reglement = 'Transferencia bancaria';
                        break;
                    case 3:
                            $data->fk_mode_reglement = 'Domiciliaci&oacute;n';
                        break;
                    case 4:
                            $data->fk_mode_reglement = 'Efectivo';
                        break;
                    case 6:
                            $data->fk_mode_reglement = 'Tarjeta de cr&eacute;dito';
                        break;
                    case 7:
                            $data->fk_mode_reglement = 'Cheque';
                        break;
                    default:
                            $data->fk_mode_reglement = 'N/A';
                        break;
                }
            $rows[] = $data;
            }          
        
        }
        return $rows;
        
    }

     public function get_data_order_id(){
        $row = array( );
        $sql='SELECT
                a.ref,
                a.fk_soc,
                a.ref_supplier,
                a.fk_mode_reglement,
                a.date_commande,
                a.total_ht,
                a.tva,
                a.total_ttc,
                b.nom,
                a.rowid,
                b.name_alias
            FROM
            '.PREFIX.'commande_fournisseur AS a
            INNER JOIN '.PREFIX.'societe AS b ON a.fk_soc = b.rowid
            WHERE
                a.fk_statut = 5
            AND
                a.rowid = '.$this->id;
        $query =$this->db->query($sql);
        if ($query) {
            $data=$query->fetch_object();
             switch ($data->fk_mode_reglement) {
                    case 2:
                            $data->fk_mode_reglement = 'Transferencia bancaria';
                        break;
                    case 3:
                            $data->fk_mode_reglement = 'Domiciliaci&oacute;n';
                        break;
                    case 4:
                            $data->fk_mode_reglement = 'Efectivo';
                        break;
                    case 6:
                            $data->fk_mode_reglement = 'Tarjeta de cr&eacute;dito';
                        break;
                    case 7:
                            $data->fk_mode_reglement = 'Cheque';
                        break;
                    default:
                            $data->fk_mode_reglement = 'N/A';
                        break;
                }
            $row=  $data;
        }
        return $row;
        
    }
    
}

?>