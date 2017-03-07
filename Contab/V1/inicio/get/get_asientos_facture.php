<?php
$url[0] = "../";
require "../conex/conexion.php";
require "../class/tipo_poliza.class.php";
require "../class/cuentas_poliza.class.php";
require "../class/cat_cuentas.class.php";
//require $url[0]."class/fact_prov_pendientes.class.php";
require ($url[0]."class/cuentas_rel.class.php");
require "../class/poliza_facture.class.php";
require_once $url[0]."class/fact_clie_pendientes.class.php";


if (isset($_POST['idFacture']) && isset($_POST['tipo'])) {

	if($_POST['tipo']=='1'){	
		$fact =new FacPolizaPendiente();
		$vfac =$fact->get_info_facture_obj_id((int)$_POST['idFacture']);
		if ($vfac) {
			($vfac->fk_statut == 1) ? get_partidas('CxP',$vfac): get_partidas('CP',$vfac);
		}
	}
}

function get_partidas($s_code_counts = 'default', $vfac){
	$rel              = new Rel_Cuenta();
	$cuenta           = new Cuenta();
	$tipo_polizas     = new Tipo_Poliza();
	$cta              = new Cuentas_Poliza();
	$poliza_facture   = new Poliza_Facture();
	$facpen           =new FacPolizaPendiente();
	$ctas_registradas = $cuenta->get_cuentas();
	$count            = 0;
	$aux              = $rel->get_cuentasRelFact(1,$vfac->fk_soc);
	$M_row_credit     = false;

    $poliza_facture->id_facture = $vfac->rowid;
    $poliza_facture->type       = '11';


    $a_poliza_facture =  $poliza_facture->get_poliza_facture_id();

    $fac            =$facpen->fetch_facture($vfac->rowid);
    $a_payment_term = $facpen->getCondiciones_de_Pago();
    $name           = (is_object($fac)) ? 'cond_pago_'.$fac->fk_cond_reglement : null ;
    $cp             =(isset($a_payment_term[$name])) ? $a_payment_term[$name] : 0 ;  

    switch ($cp) {
        case 1:
				$tipo                 = 'Contado';
				$poliza_facture->type = 1;
				$s_code_counts        = 'CP';
             	break;
        case 2:
             if ( $vfac->paye==1) {
				$tipo                 = 'Crédito-Pagado';
				$poliza_facture->type = 111;
				$s_code_counts        = 'CPP';
				$ctas = $cta->get_cuentas_poliza('CxP');

				foreach ($ctas as $key => $value) {
					if ($key == 2) {
						$M_row_credit =  $cta->get_cuentas_agrupacion_obj($value->codagr);
					}
				}

             }else{
				$s_code_counts        = 'CxP';
				$poliza_facture->type = 11;
             }
             break;

        default:
			$poliza_facture->type =1;
			$s_code_counts        = 'CP';
            break;
     }


	if ($s_code_counts != 'default') {
		$ctas = $cta->get_cuentas_poliza($s_code_counts);
		  foreach($ctas as $key => $value){
		  		$M_row =  $cta->get_cuentas_agrupacion_obj($value->codagr);
		  		$count++;
		  		if ($M_row) {
		  				if ($count == 2 && $poliza_facture->type == 111) {
		  					insert_select_asiento($M_row_credit);
		  				}
						print ('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">');
							print ('<div class="form-group">');
								print ('<div class="col-sm-3">');
										print ('<select class="select2_single form-control slc_cuenta" >');
											foreach ($ctas_registradas as $key => $value){
												if ( $poliza_facture->type == 11 && count($aux)>0 &&  $aux['codagr'] == $key && $count==1) {
													$M_row->codagr = $key;
													print ($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : ''; 
												}elseif ($poliza_facture->type == 111 && count($aux)>0 &&  $aux['codagr'] == $key && $count==2) {
													$M_row->codagr = $key;
													print ($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : ''; 
												}
												elseif($key == $M_row->codagr){
													print ($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : ''; 
												}
												else{
													print($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" >'.$value.'</option>' : ''; 
												}
												
											}									
										print ('</select>');										
										print('<input type="hidden" value="'.$M_row->codagr.'" name="txt_cuenta[]" class="form-control col-md-7 col-xs-12" placeholder="Cuenta">');
		
								print ('</div>');
								print ('<div class="col-sm-4">');
									print ('<input type="text" placeholder="Descripión" name="descripcion[]"  class="form-control col-md-7 col-xs-12 txt_debe">');
								print ('</div>');

							if ($count==1 ) {								
									if( $poliza_facture->type == 11  ){
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"  id="cliente_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Haber" name="txt_haber[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');
									}elseif ($poliza_facture->type == 1 || $poliza_facture->type == 111) {
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"  id="bancos_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Haber" name="txt_haber[]"  value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');	
									}
														
									
							}else if($count==2){
									
									if( $poliza_facture->type == 11  || $poliza_facture->type == 1 ){
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"   value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');	
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="venta_haber"  value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');	
									}elseif ($poliza_facture->type == 111) {
										print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Debe" name="txt_debe[]"   value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');	
										print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="cliente_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');	
									}
								
							}else if($count==3 ){
									if( $poliza_facture->type == 11  || $poliza_facture->type == 1  ){
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"  value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');
										print ('<div class="col-sm-2">');
											print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="iva_haber"  value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');
									}elseif ($poliza_facture->type == 111) {
										print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Debe" name="txt_debe[]"   value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										print ('</div>');	
										print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="iva_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										print ('</div>');	
									}
								
							}else{
									print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Debe" name="txt_debe[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
									print ('</div>');
									print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
									print ('</div>');
							}
							
									print ('<ul class="nav left panel_toolbox">');
									print ('<li>');
									print ('<a class="a_borrarAsiento" title="Borrar">');
									print ('<i class="fa fa-trash" style="color: red"></i>');
									print ('</a>');
									print ('</li>');
									print ('</ul>');
							print ('</div>');
						print ('</div>');
		  		}

		  }
                                         
	}
}

function insert_select_asiento( $M_row_credit){
    $rel              = new Rel_Cuenta();
	$cuenta           = new Cuenta();
	$tipo_polizas     = new Tipo_Poliza();
	$cta              = new Cuentas_Poliza();
	$poliza_facture   = new Poliza_Facture();
	$facpen           =new FacPolizaPendiente();
	$ctas_registradas = $cuenta->get_cuentas();
	$count            = 0;



	if ($M_row_credit) {
			print ('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">');
				print ('<div class="form-group">');
						print ('<div class="col-sm-3">');
							print ('<select class="select2_single form-control slc_cuenta" >');
									foreach ($ctas_registradas as $key => $value){
										if($key == $M_row_credit->codagr){
											print ($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : ''; 
										}
										else{
											print($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" >'.$value.'</option>' : ''; 
										}
														
									}									
							print ('</select>');										
							print('<input type="hidden" value="'.$M_row_credit->codagr.'" name="txt_cuenta[]" class="form-control col-md-7 col-xs-12" placeholder="Cuenta">');
						print ('</div>');
						print ('<div class="col-sm-4">');
							print ('<input type="text" placeholder="Descripión" name="descripcion[]"  class="form-control col-md-7 col-xs-12 txt_debe">');
						print ('</div>');
						print ('<div class="col-sm-2">');
								print ('<input type="text" placeholder="Debe" name="txt_debe[]"  id="iva_debe_pendiente" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
						print ('</div>');
						print ('<div class="col-sm-2">');
									print ('<input type="text" placeholder="Haber" name="txt_haber[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
						print ('</div>');
		
						print ('<ul class="nav left panel_toolbox">');
						print ('<li>');
						print ('<a class="a_borrarAsiento" title="Borrar">');
						print ('<i class="fa fa-trash" style="color: red"></i>');
						print ('</a>');
						print ('</li>');
						print ('</ul>');
				print ('</div>');
			print ('</div>');
		}

}

?>
