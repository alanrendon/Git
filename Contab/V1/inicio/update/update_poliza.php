<?php
	$url[0] = "../";
	require_once "../class/periodo.class.php";
	require_once "../class/poliza.class.php";
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$txt_fecha        =addslashes(trim($_POST['txt_fecha']));                      
		$slc_tipoPoliza   =addslashes(trim($_POST['slc_tipoPoliza']));            
		$txt_concepto     =addslashes(trim($_POST['txt_concepto']));                
		$met_payment      =0;
		$txt_nombreCheque =addslashes(trim($_POST['txt_nombreCheque']));        
		$txt_noCheque     =addslashes(trim($_POST['txt_noCheque']));                
		$txt_comentario   ="";            
		$slc_facture      ="";
		
		if (isset($_POST['slc_facture'])) {
			$slc_facture          =addslashes(trim($_POST['slc_facture']));
		} 
		if (isset($_POST['txt_comentario'])) {
			$txt_comentario       =addslashes(trim($_POST['txt_comentario']));   
		}  

		if (isset($_POST['met_payment'])) {
			$met_payment       =addslashes(trim($_POST['met_payment']));   
		}      
        
        if($slc_tipoPoliza == 'C'  &&  (empty($txt_nombreCheque) || !empty($txt_noCheque) )){
           exit(json_encode( array('msg' =>"Error, faltan datos.")));
        }
		if (!empty($txt_fecha) && !empty($slc_tipoPoliza) && !empty($txt_concepto) && isset($_POST['id']) && !empty($met_payment)) {
            
            $poliza = new Poliza();

			if ( validarPeriodo($txt_fecha) ) {
				
					
				$cons   =$poliza->get_ConsPoliza($slc_tipoPoliza);
				$resultado = $poliza->update_poliza($cons,$txt_fecha,$slc_tipoPoliza,$txt_concepto,0,$txt_nombreCheque,$txt_noCheque,$txt_comentario,$slc_facture,(int)$_POST['id'],$met_payment);
				if (!$resultado) {
					exit(json_encode( array('msg' =>"Error, no se ha podido hacer el cambio en la póliza.")));
				}else{
					exit(json_encode( array('agregado' =>"true")));
				}

			}
		}else{
			exit(json_encode( array('msg' =>"Error, faltan datos.")));
		}
	}

	function validarPeriodo($periodo){

		$date = DateTime::createFromFormat('m/d/Y', $periodo);
        $periodo = $date->format('Y-m-d');

		$anio =date("Y", strtotime($periodo));
		$mes  =date("m", strtotime($periodo));

		$periodo = new Periodo();
		$periodo = $periodo->validate_Periodo($anio,$mes);

		if (is_array($periodo)) {
			return true;
		}else{
			exit(json_encode( array('msg' =>"Error, el periodo no está abierto o no está registrado.")));
		}
	}

?>