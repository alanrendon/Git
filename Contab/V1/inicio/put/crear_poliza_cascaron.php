<?php
	$url[0] = "../";
	require_once "../class/periodo.class.php";
	require_once "../class/poliza.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$txt_fecha            = addslashes(trim($_POST['txt_fecha']));                      
		$idPoliza              =(int)$_POST['id'];
        $poliza                =new Poliza();
		
		if (!empty($txt_fecha)) {
			if (validarPeriodo($txt_fecha)) {
                    $poliza_cas =$poliza->getPolizaId($idPoliza);
                    $poliza_cas =(object)$poliza_cas[0];
			        $cons       =$poliza->get_ConsPoliza($poliza_cas->tp);

                    if ($idPolizaClonada =$poliza->crear_cascaron_poliza($idPoliza,$cons,$txt_fecha)) {
                        $asiento = new Asiento();
                        $asientos = $asiento->get_asientoPoliza($idPoliza);
                        foreach($asientos as $as){
                            $asiento->clonar_asiento($as['rowid'],$idPolizaClonada);
                        }
                       exit(json_encode( array('agregado' =>$idPolizaClonada)));
                    }else{
                        exit(json_encode(array('mensaje' =>"Error, no se pudo convertir la póliza.")));
                    }
			}
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	function validarPeriodo($periodo) {
		$anio = date("Y", strtotime($periodo));
		$mes  = date("m", strtotime($periodo));
		$periodo = new Periodo();
		$periodo = $periodo->validate_Periodo($anio,$mes);
		if ( is_array($periodo) ) {
			return true;
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, el periodo no está abierto o no está registrado.")));
		}
	}

	function validarDoctoRelacionado($txt_doctoRelacionado,$slc_facture) {
		if ( ($txt_doctoRelacionado==1 || $txt_doctoRelacionado==2) && empty($slc_facture) ) {
			exit(json_encode( array('mensaje' =>"Error, seleccione un documento relacionado.")));
		}
		return true;
	}
?>