<?php

require_once $url[0] . "conex/conexion.php";
require_once "asiento.class.php";
require_once ("periodo.class.php");

class Poliza extends conexion {

    public function __construct() {
        parent::__construct();
    }

    public function put_Polizas($cons, $txt_fecha, $slc_tipoPoliza, $txt_concepto, $txt_doctoRelacionado, $txt_nombreCheque, $txt_noCheque, $txt_comentario, $slc_facture, $txt_type_payment=0) {
        $periodo = new Periodo();
        $fechaUltimo = $periodo->get_ultimo_periodo_abierto();

        $date = new DateTime($txt_fecha);
        $txt_fecha = $date->format('Y-m-d');
        $anio = date("Y", strtotime($txt_fecha));
        $mes = date("m", strtotime($txt_fecha));

        if ($fechaUltimo->mes == 13) {
            $txt_ajuste = 1;
        } else {
            $txt_ajuste = 0;
        }

        $sql = "INSERT INTO " . PREFIX . "contab_polizas (
						tipo_pol,
						cons,
						anio,
						mes,
						fecha,
						concepto,
						comentario,
						fk_facture,
						anombrede,
						numcheque,
						societe_type,
                        entity,
                        ajuste,
                        fk_paiement
					)
					VALUES
					(
						'" . $slc_tipoPoliza . "',
						'" . $cons . "',
						'" . $anio . "',
						'" . $mes . "',
						'" . $txt_fecha . "',
						'" . $txt_concepto . "',
						'" . $txt_comentario . "',
						'" . $slc_facture . "',
						'" . $txt_nombreCheque . "',
						'" . $txt_noCheque . "',
						'" . $txt_doctoRelacionado . "',
						'" . ENTITY . "',
						'" . $txt_ajuste . "',
						'" . $txt_type_payment . "'
					)";
        $query = $this->db->query($sql);
        if ($query) {

            $sql = "SELECT MAX(rowid) as rowid FROM " . PREFIX . "contab_polizas";
            $query = $this->db->query($sql);
            if ($query) {
                $rows = array();
                $row = $query->fetch_assoc();
                return $row['rowid'];
            }
        }
        return false;
    }

    public function get_ConsPoliza($slc_tipoPoliza) {
        $last = $this->get_ultimo_periodo_abierto();
        $sql = "SELECT
                        MAX(cp.cons) AS last,
                        cp.tipo_pol
                    FROM
                        " . PREFIX . "contab_polizas AS cp
                    INNER JOIN " . PREFIX . "contab_periodos AS p ON p.mes=cp.mes AND p.anio=cp.anio
                    WHERE
                        tipo_pol = '" . $slc_tipoPoliza . "'
                    AND
                         cp.entity = " . ENTITY . "
                    AND
                        p.anio='" . $last->anio . "'
                    AND
                        p.mes='" . $last->mes . "'
                    AND
                        p.estado != 0
		      ";


        $query = $this->db->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();


            return $row['last'] + 1;
        }
        return 1;
    }

    public function get_ultimo_periodo_abierto() {
        $sql = "SELECT
					max(anio) as anio,
                    max(mes) as mes
				FROM
				" . PREFIX . "contab_periodos
                WHERE
					estado IN(1,2)
                ";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->fetch_object();
        }
        return false;
    }

    public function getPolizas() {
        $rows = array();
        $sql = "	SELECT
					poliza.rowid as 'id',
					poliza.entity,
					(
						CASE poliza.tipo_pol
							WHEN 'D' THEN 'Diario'
				      		WHEN 'E' THEN 'Egreso'
							WHEN 'I' THEN 'Ingreso'
					    	WHEN 'C' THEN 'Cheque'
						END
					) AS tipo_pol,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
				   poliza.tipo_pol as tipol_l,
					poliza.cons npol,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
					poliza.societe_type,
					factCliente.facnumber,
					factProve.ref,
                    poliza.recurente,
                   	if(p.estado is null , 1, p.estado) AS estado,
                    poliza.ajuste,
					poliza.fk_paiement,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment
				FROM
					" . PREFIX . "contab_polizas  AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid=poliza.fk_facture
                LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = poliza.mes
                LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
                WHERE
                    poliza.entity = " . ENTITY . "
                AND
                    poliza.fecha != '0000-00-00'
                GROUP BY
                    poliza.rowid
				ORDER BY 
					poliza.fecha DESC,
					poliza.tipo_pol ASC,
					poliza.cons ASC
				LIMIT 10
				";

		
        $query = $this->db->query($sql);
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizas_diot($anio, $mes) {
        $rows = array();

        $periodomes = 'poliza.mes';
        if ($mes == 13) {
            $mes = 12;
            $ajuste = 1;
            $periodomes = 13;
        }
        $sql = 'SELECT
					poliza.rowid AS "id",
					poliza.entity,
					(
						CASE poliza.tipo_pol
						WHEN "D" THEN
							"Diario"
						WHEN "E" THEN
							"Egreso"
						WHEN "C" THEN
							"Cheque"
						END
					) AS tipo_pol,
					poliza.tipo_pol AS tipol_l,
					poliza.cons npol,
					CONCAT(
						DATE_FORMAT(poliza.fecha, "%y%c"),
						"-",
						poliza.tipo_pol,
						"-",
						poliza.cons
					) AS cons,
					poliza.cons npol,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					poliza.societe_type,
					factProve.ref,
					poliza.recurente,
					IF (p.estado IS NULL, 0, p.estado) AS estado,
				 	poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment
				FROM
					llx_contab_polizas AS poliza
				INNER JOIN llx_contab_poliza_facture AS cpf ON cpf.id_poliza=poliza.rowid
				LEFT JOIN llx_facture_fourn AS factProve ON factProve.rowid = poliza.fk_facture
				LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = ' . $periodomes . '
				LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
				WHERE
					poliza.tipo_pol = "E"
				AND
					poliza.fecha 	!= "0000-00-00"
				AND 
					poliza.ajuste 	IN (1,-1, 0)
				AND 
					poliza.entity 	= ' . ENTITY . '
				AND 
					poliza.anio 	= "' . $anio . '"
				AND 
					poliza.mes 		= "' . $mes . '"
				AND 
					cpf.type IN (2,0,21,211)
				GROUP BY
					poliza.rowid
				ORDER BY
					poliza.fechahora ASC,
					poliza.societe_type ASC,
					factProve.ref ASC,
					poliza.cons ASC,
					poliza.tipo_pol DESC';
        $query = $this->db->query($sql);
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizas_recurrentes() {
        $rows = false;
        $sql = "	SELECT
					poliza.rowid as 'id',
					poliza.entity,
					(
						CASE poliza.tipo_pol
							WHEN 'D' THEN 'Diario'
				      		WHEN 'E' THEN 'Egreso'
							WHEN 'I' THEN 'Ingreso'
					    	WHEN 'C' THEN 'Cheque'
						END
					) AS tipo_pol,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
					poliza.societe_type,
					factCliente.facnumber,
					factProve.ref,
                    poliza.recurente,
                    poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment
				FROM
					" . PREFIX . "contab_polizas  AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid=poliza.fk_facture
				LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
                WHERE
                    poliza.entity = " . ENTITY . "
                AND poliza.fecha = '0000-00-00'
				ORDER BY 
					poliza.tipo_pol ASC,
					poliza.cons ASC,
                    poliza.anio ASC,
                    poliza.mes ASC
					
				LIMIT 10
				";
        $query = $this->db->query($sql);
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizasFiltros_recurrentes($anio, $mes, $tipo_pol, $factura) {
        $rows = false;
        if ($tipo_pol == 'T') {
            $tipo_pol = "'D','E','C','I'";
        } else {
            $tipo_pol = "'" . $tipo_pol . "'";
        }
        $sql = "	SELECT
					poliza.rowid as 'id',
					poliza.entity,
					(
						CASE poliza.tipo_pol
							WHEN 'D' THEN 'Diario'
				      		WHEN 'E' THEN 'Egreso'
							WHEN 'I' THEN 'Ingreso'
					    	WHEN 'C' THEN 'Cheque'
						END
					) AS tipo_pol,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
					poliza.societe_type,
					factCliente.facnumber,
					factProve.ref,
					poliza.recurente,
                    poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment

				FROM
					" . PREFIX . "contab_polizas AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid = poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid = poliza.fk_facture
				LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
				WHERE
					poliza.tipo_pol IN (" . $tipo_pol . ")
                AND
                    poliza.entity = " . ENTITY . "
                AND poliza.fecha = '0000-00-00";
        if (!empty($anio)) {
            $sql .= " AND poliza.anio = '" . $anio . "'";
        }
        if (!empty($mes)) {
            $sql .= " AND poliza.mes = '" . $mes . "'";
        }


        if (!empty($factura) && $factura == "1") {
            $sql .= " AND poliza.societe_type > 0
							ORDER BY
								poliza.fechahora ASC,
								poliza.societe_type ASC,
								factProve.ref ASC,
								factCliente.facnumber ASC ,
					           poliza.cons ASC,
                               poliza.tipo_pol DESC";
        } else {
            $sql .= " ORDER BY
								poliza.fechahora ASC,
					           poliza.cons ASC,
                               poliza.tipo_pol DESC";
        }

        $query = $this->db->query($sql);

        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizaId($rowid) {
        $rows = array();
        $sql = "	SELECT
					poliza.rowid as 'id',
					poliza.entity,
					(
						CASE poliza.tipo_pol
							WHEN 'D' THEN 'Diario'
				      		WHEN 'E' THEN 'Egreso'
							WHEN 'I' THEN 'Ingreso'
					    	WHEN 'C' THEN 'Cheque'
						END
					) AS tipo_pol,
                    tipo_pol AS tp,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.anio,
				    poliza.tipo_pol as tipol_l,
					poliza.cons npol,
					poliza.mes,
					poliza.fecha,
                    poliza.concepto concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
					poliza.societe_type,
					factCliente.facnumber,
					factProve.ref,
                    poliza.recurente,
					poliza.fk_paiement,
                    if(p.estado is null , 1, p.estado) AS estado,
                    poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment
				FROM
					" . PREFIX . "contab_polizas AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid=poliza.fk_facture
                LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = poliza.mes
                LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
				WHERE poliza.rowid = " . $rowid . "
                  GROUP BY
                    poliza.rowid
                ";

        $query = $this->db->query($sql);
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizasby() {
        $sql = "SELECT
					poliza.rowid,
					poliza.entity,
					poliza.tipo_pol,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
                     poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment
				FROM
					" . PREFIX . "contab_polizas  AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid=poliza.fk_facture
				LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement
                WHERE
                    poliza.entity = " . ENTITY . "
				ORDER BY 
					poliza.fechahora DESC 
				LIMIT 10
				";
        $query = $this->db->query($sql);
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getPolizasFiltros($anio, $mes, $tipo_pol, $factura, $recurrente) {
        $rows = array();
        if ($tipo_pol == 'T' || (empty($tipo_pol) || $tipo_pol == '')) {
            $tipo_pol = "'D','E','C','I'";
        } else {
            $tipo_pol = "'" . $tipo_pol . "'";
        }


        $ajuste = 0;

        $periodomes = 'poliza.mes';
        if ($mes == 13) {
            $mes = 12;
            $ajuste = 1;
            $periodomes = 13;
        }

        $sql = "SELECT
					poliza.rowid as 'id',
					poliza.entity,
					(
						CASE poliza.tipo_pol
							WHEN 'D' THEN 'Diario'
				      		WHEN 'E' THEN 'Egreso'
							WHEN 'I' THEN 'Ingreso'
					    	WHEN 'C' THEN 'Cheque'
						END
					) AS tipo_pol,
					poliza.tipo_pol as tipol_l,
					poliza.cons npol,
					CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons,
					poliza.cons npol,
					poliza.anio,
					poliza.mes,
					poliza.fecha,
					poliza.concepto,
					poliza.comentario,
					poliza.anombrede,
					poliza.numcheque,
					factProve.rowid,
					factCliente.rowid,
					poliza.societe_type,
					factCliente.facnumber,
					factProve.ref,
					poliza.recurente,
					poliza.fk_paiement,
                    if(p.estado is null , 0, p.estado) AS estado,
                    poliza.ajuste,
					msp.rowid AS paiment_id,
					msp.`name` AS paiment

				FROM
					" . PREFIX . "contab_polizas AS poliza
				LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid = poliza.fk_facture
				LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid = poliza.fk_facture
                LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = " . $periodomes . "
                LEFT JOIN llx_contab_method_sat_payment AS msp ON msp.rowid=poliza.fk_paiement

				WHERE
					poliza.tipo_pol IN (" . $tipo_pol . ")
                AND
                    poliza.fecha != '0000-00-00'
                AND
                    poliza.ajuste = " . $ajuste . "
                AND
                    poliza.entity = " . ENTITY . "";

        if (!empty($anio)) {
            $sql .= " AND poliza.anio = '" . $anio . "'";
        }
        if (!empty($mes)) {
            $sql .= " AND poliza.mes = '" . $mes . "'";
        }
        if ($recurrente == "1") {
            $sql .= " AND poliza.recurente = 1";
        }
        if (!empty($factura) && $factura == "1") {
            $sql .= " AND poliza.societe_type > 0
                    
                            GROUP BY
                                poliza.rowid
							ORDER BY
								poliza.fechahora ASC,
								poliza.societe_type ASC,
								factProve.ref ASC,
								factCliente.facnumber ASC ,
					           poliza.cons ASC,
                               poliza.tipo_pol DESC";
        } else {
            $sql .= " 
                            GROUP BY
                                    poliza.rowid
                                ORDER BY
                    
								poliza.fechahora ASC,
					           poliza.cons ASC,
                               poliza.tipo_pol DESC";
        }
        $query = $this->db->query($sql);

        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    function update_poliza($cons, $txt_fecha, $slc_tipoPoliza, $txt_concepto, $txt_doctoRelacionado=0, $txt_nombreCheque, $txt_noCheque, $txt_comentario, $slc_facture, $rowid, $fk_paiement=0) {

        $date = DateTime::createFromFormat('m/d/Y', $txt_fecha);
        $txt_fecha = $date->format('Y-m-d');

        $anio = date("Y", strtotime($txt_fecha));
        $mes = date("m", strtotime($txt_fecha));

        $poliza = $this->getPolizaId($rowid);

        $poliza = (object)$poliza[0];

        

        $sql = "	UPDATE " . PREFIX . "contab_polizas
				SET 
				    tipo_pol = '" . $slc_tipoPoliza . "',  
					anio = '" . $anio . "',
					mes = '" . $mes . "',
					fecha = '" . $txt_fecha . "',
					concepto = '" . $txt_concepto . "',
					comentario = '" . $txt_comentario . "',
					fk_facture = '" . $slc_facture . "',
					anombrede = '" . $txt_nombreCheque . "',
					numcheque = '" . $txt_noCheque . "',
					fk_paiement = '" . $fk_paiement . "'";
		if ( $poliza->tipol_l != $slc_tipoPoliza) {
        	 $sql .= ", cons= '".$cons."'" ;
        }
		$sql .= "WHERE
					(`rowid` = '" . $rowid . "')
				LIMIT 1
		";

        $query = $this->db->query($sql);
        if ($query) {
            return true;
        }
        return false;
    }

    public function delete_poliza($rowid) {
        $asientos = new Asiento();
        $asientos->delete_allAsientos($rowid);
        $sql = "DELETE FROM " . PREFIX . "contab_polizas
				WHERE
					(rowid = '" . $rowid . "')
				LIMIT 1";
        $query = $this->db->query($sql);

        if ($query) {
        	$sql ='DELETE FROM `llx_contab_poliza_facture` WHERE (`id_poliza`="'. $rowid .'")';
        	$query = $this->db->query($sql);
            return true;
        }
        return false;
    }

    public function recurente_poliza($rowid) {
        $sql = "UPDATE " . PREFIX . "contab_polizas SET recurente='1' 
				WHERE
					(rowid = '" . $rowid . "')
				LIMIT 1";
        $query = $this->db->query($sql);
        if ($query) {

            return true;
        }
        return false;
    }

    public function remover_recurente_poliza($rowid) {
        $sql = "UPDATE " . PREFIX . "contab_polizas SET recurente='0' 
				WHERE
					(rowid = '" . $rowid . "')
				LIMIT 1";
        $query = $this->db->query($sql);
        if ($query) {

            return true;
        }
        return false;
    }

    public function clonar_poliza($id, $constante) {
        $sql = "INSERT INTO " . PREFIX . "contab_polizas SELECT
                    0,
					p.entity,
					p.tipo_pol,
                    " . $constante . ",
                    p.anio,
					p.mes,
					p.fecha,
					p.concepto,
					p.comentario,
					p.fk_facture,
					p.anombrede,
					p.numcheque,
					p.ant_ctes,
					p.fechahora,
					p.societe_type,
					- 1,
					p.ajuste
                FROM
                   " . PREFIX . "contab_polizas AS p
                WHERE
                    p.rowid = " . $id . ";
        ";
        $query = $this->db->query($sql);
        if ($query) {
            $sql = "(SELECT MAX(rowid) AS id FROM " . PREFIX . "contab_polizas)";
            $query = $this->db->query($sql);
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return false;
    }

    public function crear_cascaron_poliza($id, $constante, $txt_fecha) {
        $date = new DateTime($txt_fecha);
        $txt_fecha = $date->format('Y-m-d');
        $anio = date("Y", strtotime($txt_fecha));
        $mes = date("m", strtotime($txt_fecha));

        $sql = "INSERT INTO " . PREFIX . "contab_polizas SELECT
                    0,
                    p.entity,
                    p.tipo_pol,
                    " . $constante . ",
                    '" . $anio . "',
                    '" . $mes . "',
                    '" . $txt_fecha . "',
                    p.concepto,
                    p.comentario,
                    p.fk_facture,
                    p.anombrede,
                    p.numcheque,
                    p.ant_ctes,
                    p.fechahora,
                    p.societe_type,
                    p.recurente,
                    p.ajuste
                FROM
                   " . PREFIX . "contab_polizas AS p
                WHERE
                    p.rowid = " . $id . ";
        ";
        $query = $this->db->query($sql);
        if ($query) {
            $sql = "(SELECT MAX(rowid) AS id FROM " . PREFIX . "contab_polizas)";
            $query = $this->db->query($sql);
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return false;
    }

    function getPolizasPeriodoCuenta($cta, $fecha1, $fecha2) {
        $sql = "	SELECT 
                    poliza.rowid as 'id', 
                    poliza.entity, 
    			 (  CASE poliza.tipo_pol 
                        WHEN 'D' THEN 'Diario' 
                        WHEN 'E' THEN 'Egreso' 
                        WHEN 'I' THEN 'Ingreso' 
                        WHEN 'C' THEN 'Cheque' 
                    END 
                  ) AS tipo_pol, 
    			    CONCAT(DATE_FORMAT(poliza.fecha,'%y%c'),'-',poliza.tipo_pol,'-',poliza.cons) AS cons, 
    			    poliza.anio, 
                    poliza.mes, 
                    poliza.fecha, 
                    oliza.concepto, 
                    poliza.comentario, 
                    poliza.anombrede, 
    			    poliza.numcheque, 
                    factProve.rowid, 
                    factCliente.rowid, 
                    poliza.societe_type, 
                    factCliente.facnumber, 
    			factProve.ref, poliza.recurente 
				FROM " . PREFIX . "contab_polizas AS poliza 
				     LEFT JOIN " . PREFIX . "facture_fourn AS factProve ON factProve.rowid = poliza.fk_facture 
				     LEFT JOIN " . PREFIX . "facture AS factCliente ON factCliente.rowid = poliza.fk_facture 
				     INNER JOIN " . PREFIX . "contab_polizasdet AS poldet ON poldet.fk_poliza = poliza.rowid 
				     		AND poldet.cuenta='" . $cta . "' 
				WHERE
                    poliza.entity='" . ENTITY . "'
                AND 
                    poliza.fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "' 
				ORDER BY 
                    poliza.fechahora DESC,
                    poliza.cons DESC ";
        //print $sql;

        $query = $this->db->query($sql);

        if ($query) {
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function insert_info_docto($idpol, $nomcarpeta, $archivo, $tipo, $timbreFDUuid, $fk_operation_societe = '', $rfc = '', $id_ext ='', $nombre_ext='') {
        $sql = "INSERT INTO 
                    " . PREFIX ."contab_polizas_docto 
                        (
                            entity,
                            fk_poliza,
    			            nom_carpeta,
                            archivo,
                            tipo,
                            uuid,
                            fk_operation_societe,
                            rfc,
                            id_fiscal,
                            nombre_extranjero
                        ) 
                        VALUES(
                            '" . ENTITY . "',
                            '" . $idpol . "',
    				        '" . $nomcarpeta . "',
                            '" . $archivo . "',
                            '" . $tipo . "',
                            '" . $timbreFDUuid . "',
                            '" . $fk_operation_societe . "',
                            '" . $rfc . "',
                            '" . $id_ext . "',
                            '" . $nombre_ext . "'
                        )";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function search_uuid_docto($idpol, $nomcarpeta, $archivo, $tipo, $timbreFDUuid) {
        $sql = "SELECT
					pd.uuid,
					p.fecha,
					p.concepto,
					p.rowid,
					CONCAT(
						DATE_FORMAT(p.fecha, '%y%c'),
						'-',
						p.tipo_pol,
						'-',
						p.cons
					) AS cons
					FROM
						llx_contab_polizas_docto AS pd
					LEFT JOIN llx_contab_polizas AS p ON pd.fk_poliza = p.rowid
					WHERE
                        uuid = '" . $timbreFDUuid . "'
                     GROUP BY
						p.rowid
                        ";

        $query = $this->db->query($sql);

        $string = '';
        if ($query) {
            $rows = array();
            while ($row = $query->fetch_object()) {
                $rows[] = 'UUID' . $row->uuid . ' Periodo: ' . $row->fecha . ' Concepto: ' . $row->concepto . ' No.: ' . $row->cons;
            }
            return $rows;
        }
        return false;
    }

    public function get_prov_by_rfc_prov($rfc, $idpol) {
        $sql = 'SELECT
					f.rowid AS facture,
					polif_fac.type,
					s.siren,
					s.rowid AS id_societe
				FROM
					llx_facture_fourn AS f
				INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
				INNER JOIN llx_contab_poliza_facture AS polif_fac ON polif_fac.id_facture = f.rowid
				AND polif_fac.type = 2
				WHERE
					fk_statut != 0
				AND 
					polif_fac.id_poliza = "' . $idpol . '"
				AND s.siren =  "' . $rfc . '"';

        $query = $this->db->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();
            return true;
        }
        return false;
    }

    public function get_prov_by_rfc_clie($rfc, $idpol) {
        $sql = 'SELECT
					f.rowid AS facture,
					polif_fac.type,
					s.siren,
					s.rowid AS id_societe
				FROM
					llx_facture AS f
				INNER JOIN llx_societe AS s ON f.fk_soc = s.rowid
				INNER JOIN llx_contab_poliza_facture AS polif_fac ON polif_fac.id_facture = f.rowid
				AND polif_fac.type = 1
				WHERE
					fk_statut != 0
				AND 
				polif_fac.id_poliza = "' . $idpol . '"
				AND s.siren =  "' . $rfc . '"';
        $query = $this->db->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();
            return true;
        }
        return false;
    }

    public function get_info_docto($idpol) {
        $sql = "SELECT
                   COUNT(rowid) as row
                FROM
                     " . PREFIX . "contab_polizas_docto 
                WHERE
                    fk_poliza =   '" . $idpol . "'
                    ";
        $query = $this->db->query($sql);
        return $query->fetch_object();
    }

    public function get_info_factures($id_po) {
        $rows = array();
        $rows2 = array();
        $sql = 'SELECT
						pf.rowid,
						pf.type,
						pf.id_facture

					FROM
						llx_contab_poliza_facture AS pf
					WHERE
					pf.id_poliza='.$id_po.'
				';
        $query = $this->db->query($sql);
        if ($query) {
            while ($row = $query->fetch_object()) {
                $rows[] = $row;
            }
        }
        foreach ($rows as $key => $row) {
        	if ($row->type == 1 ||$row->type == 11|| $row->type == 111 ) {
        		 $sql = 'SELECT
									llx_facture.facnumber AS ref
							FROM
								llx_facture
							WHERE
								llx_facture.rowid = '.$row->id_facture.'
							';
				$query  = $this->db->query($sql);
				$rows2[] =$query->fetch_object();
        	}elseif ($row->type == 2 ||$row->type == 21|| $row->type == 211 ){
        		 $sql = 'SELECT
							f.ref AS ref
							FROM
							llx_facture_fourn AS f
							WHERE
								f.rowid = '.$row->id_facture.'
							';
				$query  = $this->db->query($sql);
				$rows2[] =$query->fetch_object();
        	}
        }
        return $rows2;
    }

    public function get_info_factures_xml($id_po) {
        $rows = array();
        $sql = 'SELECT
						ff.ref,
						ff.rowid,
						pf.id_poliza,
						s.rowid AS prov,
						s.siren AS rfc,
						ts.`key` AS key_ts,
						`to`.`key` AS key_to,
						ff.total_ht AS subtotal,
						ff.total_ttc AS total
					FROM
						llx_contab_poliza_facture AS pf
					INNER JOIN llx_facture_fourn AS ff ON pf.id_facture = ff.rowid
					INNER JOIN llx_societe AS s ON s.rowid = ff.fk_soc
					INNER JOIN llx_contab_operation_societe_fourn AS osf ON osf.id_societe = s.rowid
					INNER JOIN llx_contab_operation_societe AS os ON osf.id_operation_societe = os.rowid
					INNER JOIN llx_contab_type_societe AS ts ON os.key_type_societe = ts.rowid
					INNER JOIN llx_contab_type_operation AS `to` ON os.key_type_operation = `to`.rowid
					WHERE 
						pf.id_poliza=' . $id_po . '
					ORDER BY
					s.siren
				';
        $query = $this->db->query($sql);
        if ($query) {
            while ($row = $query->fetch_object()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function get_info_factures_xml_without_operation_societe_fourn ($id_po) {
        $rows = array();
        $sql = 'SELECT
						ff.ref,
						ff.rowid,
						pf.id_poliza,
						s.rowid AS prov,
						ff.total_ht AS subtotal,
						ff.total_ttc AS total
					FROM
						llx_contab_poliza_facture AS pf
					INNER JOIN llx_facture_fourn AS ff ON pf.id_facture = ff.rowid
					INNER JOIN llx_societe AS s ON s.rowid = ff.fk_soc
					WHERE 
						pf.id_poliza=' . $id_po . '
					ORDER BY
					s.siren
				';

        $query = $this->db->query($sql);
        if ($query) {
            while ($row = $query->fetch_object()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function get_info_docto_xml($idpol, $tipo = 'cfdixml') {
        $rows = array();
        $sql = 'SELECT
					pd.rowid,
					pd.entity,
					pd.fk_poliza,
					pd.nom_carpeta,
					pd.archivo,
					pd.tipo,
					pd.uuid,
					pd.fk_operation_societe,
					pd.rfc,
					pd.id_fiscal,
					pd.nombre_extranjero,
					`to`.`key` AS key_to,
					ts.`key` AS key_ts
				FROM
					llx_contab_polizas_docto AS pd
				INNER JOIN llx_contab_operation_societe AS os ON pd.fk_operation_societe = os.rowid
				INNER JOIN llx_contab_type_operation AS `to` ON os.key_type_operation    =  `to`.rowid
				INNER JOIN llx_contab_type_societe AS ts ON os.key_type_societe          = ts.rowid
				WHERE
    	            fk_poliza =  ' . $idpol . '
    	        AND
    	        	tipo = "' . $tipo . '"
    	        ORDER BY 
    	        	pd.rfc';
        $query = $this->db->query($sql);
        if ($query) {
            while ($row = $query->fetch_object()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

     public function get_info_docto_list($idpol) {
        $rows = array();
        $sql = 'SELECT
					pd.rowid,
					pd.entity,
					pd.fk_poliza,
					pd.nom_carpeta,
					pd.archivo,
					pd.tipo,
					pd.uuid,
					pd.fk_operation_societe,
					pd.rfc,
					pd.id_fiscal,
					pd.nombre_extranjero,
					`to`.`key` AS key_to,
					ts.`key` AS key_ts
				FROM
					llx_contab_polizas_docto AS pd
				LEFT JOIN llx_contab_operation_societe AS os ON pd.fk_operation_societe = os.rowid
				LEFT JOIN llx_contab_type_operation AS `to` ON os.key_type_operation    =  `to`.rowid
				LEFT JOIN llx_contab_type_societe AS ts ON os.key_type_societe          = ts.rowid
				WHERE
    	            fk_poliza =  ' . $idpol . '
    	        ORDER BY 
    	        	pd.rfc';

        $query = $this->db->query($sql);
        if ($query) {
            while ($row = $query->fetch_object()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

}
