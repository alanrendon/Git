<?php
	$url[0] = "../";
	require_once "../class/periodo.class.php";
	require_once "../class/poliza.class.php";
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$txt_fecha            =addslashes(trim($_POST['txt_fecha']));                      
		$slc_tipoPoliza       =addslashes(trim($_POST['slc_tipoPoliza']));            
		$txt_concepto         =addslashes(trim($_POST['txt_concepto']));                
		$txt_doctoRelacionado =0;
		$txt_nombreCheque     =addslashes(trim($_POST['txt_nombreCheque']));        
		$txt_noCheque         =addslashes(trim($_POST['txt_noCheque']));                
		$txt_comentario       ="";            
		$slc_facture          ="";
		
		if (isset($_POST['slc_facture'])) {
			$slc_facture          =addslashes(trim($_POST['slc_facture']));
		} 
		if (isset($_POST['txt_comentario'])) {
			$txt_comentario       =addslashes(trim($_POST['txt_comentario']));   
		}  

		if (isset($_POST['txt_doctoRelacionado'])) {
			$txt_doctoRelacionado       =addslashes(trim($_POST['txt_doctoRelacionado']));   
		}         
		if (!empty($txt_fecha) && !empty($slc_tipoPoliza) && !empty($txt_concepto)
			&& !empty($txt_nombreCheque) && !empty($txt_noCheque) ) {
			if (validarPeriodo($txt_fecha)) {
				if (validarDoctoRelacionado($txt_doctoRelacionado,$slc_facture)) {
					$poliza = new Poliza();
					$cons   =$poliza->get_ConsPoliza($slc_tipoPoliza);
					$resultado = $poliza->put_Polizas($cons,$txt_fecha,$slc_tipoPoliza,$txt_concepto,$txt_doctoRelacionado,$txt_nombreCheque,$txt_noCheque,$txt_comentario,$slc_facture);
					if (!$resultado) {
						exit(json_encode( array('mensaje' =>"Error, no se ha podido agregar la póliza.")));
					}else{
						exit(json_encode( array('agregado' =>"true")));
					}
				}
			}
		}else{
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	function validarPeriodo($periodo){
		$anio =date("Y", strtotime($periodo));
		$mes  =date("m", strtotime($periodo));
		$periodo = new Periodo();
		$periodo = $periodo->validate_Periodo($anio,$mes);
		if (is_array($periodo)) {
			return true;
		}else{
			exit(json_encode( array('mensaje' =>"Error, el periodo no está abierto o no está registrado.")));
		}
	}

	function validarDoctoRelacionado($txt_doctoRelacionado,$slc_facture){
		if ( ($txt_doctoRelacionado==1 || $txt_doctoRelacionado==2) && empty($slc_facture)){
			exit(json_encode( array('mensaje' =>"Error, seleccione un documento relacionado.")));
		}
		return true;
	}
?>