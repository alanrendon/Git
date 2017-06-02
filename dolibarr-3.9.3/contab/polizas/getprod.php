<?php 
    require '../../main.inc.php';


    $action = GETPOST('action', 'alpha');

    if (! empty($action) && $action == 'selectProd'){
        $searchkey = (GETPOST('idprod') ? GETPOST('idprod') : (GETPOST('htmlname') ? GETPOST('htmlname') : ''));
        
        global $db;
        //key, value, label, qty "'.DOL_URL_ROOT.'/recepcionavanzada/lib/getprod.php?action=selectProd"
        $sql = "SELECT DISTINCT u.rowid as 'key', u.cta AS cta, u.descta AS descta";
        $sql.= " FROM llx_contab_cat_ctas as u";
        $sql.= " LEFT JOIN llx_contab_cat_iva as v ON u.rowid=v.id_cuenta";
        $sql.= " WHERE ";
        $sql.= "(u.cta LIKE '%".$db->escape($searchkey)."%' OR u.descta LIKE '%".$db->escape($searchkey)."%'";
        $sql.= ")";
        $sql.= " AND v.porcentaje IS NULL ";
        $sql.= " ORDER BY u.descta ASC";



        $resql=$db->query($sql);
        $dataset= array();
        if ($resql){
            while($rsl=$db->fetch_object($resql)){
                $rsl->label = $rsl->cta.' '.$rsl->descta;
            	$dataset[] = $rsl;
            }
        }
        echo json_encode($dataset);
    }