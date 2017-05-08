<?php 
$url[0] = "../";
require_once "../conex/conexion.php";

class muestra_polizas extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function consulta($tipo,$anio,$mes,$por_factura) { 
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
					poliza.cons,
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
					factProve.ref
				FROM
					".PREFIX."contab_polizas  AS poliza
				INNER JOIN ".PREFIX."facture_fourn AS factProve ON factProve.rowid=poliza.fk_facture
				INNER JOIN ".PREFIX."facture AS factCliente ON factCliente.rowid=poliza.fk_facture";

				if ( $tipo !== '' || $anio !== '' ||$mes !== '' || $por_factura !== '' ) {
					$sql .= " WHERE ";
				}
				if ( $tipo !== '' && $tipo !== 'T' ) {
					$sql .= " tipo_pol = '".$tipo."' AND ";
				}
				if ( $anio !== '' ) {
					$sql .= " anio = '".$anio."' AND ";
				}
				if ( $mes !== '' ) {
					$sql .= " mes = '".$mes."' AND ";
				}

				if ( $por_factura == 1 ) {
					$sql .= " fk_facture > 0 ";
				}

			$sql .=	"ORDER BY 
					poliza.fechahora DESC ";

		$query = $this->db->query($sql); 
		if ( $query ) {
			$rows = array();
			while($row = $query->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
	}
}

$muestra_polizas = new muestra_polizas(); 

$tipo = $_POST['tipo'];
$anio = $_POST['anio'];
$mes = $_POST['mes'];
$por_factura = $_POST['por_factura'];

$arreglo = $muestra_polizas->consulta($tipo,$anio,$mes,$por_factura);

foreach ( $arreglo as $key ){
	if ( $key['societe_type'] == 1 ) {
		$tipoDoctorelacionado =  $key['facnumber'];
	}
	else if( $key['societe_type'] == 2 ) {
		$tipoDoctorelacionado = $key['ref'];
	}
	else {
		$tipoDoctorelacionado = "No hay docto.";
	}

	$res .= '<div class="col-md-12 col-xs-12 col-lg-12">
						<div class=" x_panel">
							<div class="x_title">';
	$res .= '<h2>'.$key['tipo_pol'].' : '.$key['cons'].'</h2>';
	$res .= '</div></div></div>';
}

$res = '<div class="col-md-12 col-xs-12 col-lg-12">hola</div>';

$return["json"] = json_encode($res);
echo json_encode($return);

