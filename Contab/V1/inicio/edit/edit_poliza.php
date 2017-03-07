<?php
	$url[0] = "../";
	require_once "../class/poliza.class.php";
	require_once "../class/asiento.class.php";
	require_once "../class/periodo.class.php";
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		if (isset($_POST['eliminar'])) {
			$idPoliza= (int)$_POST['eliminar'];
			$poliza    = new Poliza();
            $polizaRecurrenteArray =$poliza->getPolizaId($idPoliza);
            if ( $polizaRecurrenteArray[0]['fecha'] =='0000-00-00' || validarPeriodo($polizaRecurrenteArray[0]['fecha'])) {
				if ($poliza=$poliza->delete_Poliza($idPoliza)) {
					exit(json_encode(true));
                }else{
                    exit(json_encode(array('mensaje' =>"Error, no se pudo eliminar la póliza.")));
                }
			}
			
		}
		else if (isset($_POST['recurente'])) {
			$idPoliza= (int)$_POST['recurente'];
			$asiento    = new Poliza();
			if ($asiento=$asiento->recurente_poliza($idPoliza)) {
				exit(json_encode(true));
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo convertir la póliza.")));
			}
		}else if(isset($_POST['removerRecurente'])){
            $idPoliza   = (int)$_POST['removerRecurente'];
			$asiento    = new Poliza();
			if ($asiento=$asiento->remover_recurente_poliza($idPoliza)) {
				exit(json_encode(true));
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo convertir la póliza.")));
			}
        }
		else if (isset($_POST['clonar'])) {
			$idPoliza              =(int)$_POST['clonar'];
			$poliza                =new Poliza();
			$polizaRecurrenteArray =$poliza->getPolizaId($idPoliza);
			$cons                  =$poliza->get_ConsPoliza($polizaRecurrenteArray[0]['tp']);

			if ($idPolizaClonada =$poliza->clonar_poliza($idPoliza,$cons)) {
				$asiento = new Asiento();
				$asientos = $asiento->get_asientoPoliza($idPoliza);
				foreach($asientos as $as){
					$asiento->clonar_asiento($as['rowid'],$idPolizaClonada);
				}
				exit(json_encode(true));
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo convertir la póliza.")));
			}
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
			exit(json_encode( array('mensaje' =>"Error, el periodo no está abierto o no está registrado. a")));
		}
	}

	
?>