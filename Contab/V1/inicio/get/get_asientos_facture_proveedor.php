<?php
$url[0] = "../";
require "../conex/conexion.php";
require "../class/tipo_poliza.class.php";
require "../class/cuentas_poliza.class.php";
require "../class/cat_cuentas.class.php";
require $url[0]."class/fact_prov_pendientes.class.php";
require ($url[0]."class/cuentas_rel.class.php");
require "../class/poliza_facture.class.php";


if (isset($_POST['idFacture']) && isset($_POST['tipo'])) {

	if($_POST['tipo']=='2'){
		
		$fact =new FacPolizaPendiente();
		$vfac =$fact->get_info_facture_obj_id((int)$_POST['idFacture']);
		if ($vfac) {
			get_partidas($vfac);
		}
	}
}

function get_partidas($vfac){
	$rel              = new Rel_Cuenta();
	$cuenta           = new Cuenta();
	$tipo_polizas     = new Tipo_Poliza();
	$cta              = new Cuentas_Poliza();
	$poliza_facture   = new Poliza_Facture();
	$ctas_registradas = $cuenta->get_cuentas();
	$facpen           =new FacPolizaPendiente();
	$count            = 0;
	$aux              = $rel->get_cuentasRelFact(2,$vfac->fk_soc);


    $poliza_facture->id_facture = $vfac->rowid;
    $poliza_facture->type       = '21';


    $a_poliza_facture =  $poliza_facture->get_poliza_facture_id();


  	$fac            =$facpen->fetch_facture($vfac->rowid);
    $a_payment_term = $facpen->getCondiciones_de_Pago();
    $name           = (is_object($fac)) ? 'cond_pago_'.$fac->fk_cond_reglement : null ;
    $cp             =(isset($a_payment_term[$name])) ? $a_payment_term[$name] : 0 ;  
    switch ($cp) {
        case 1:
             	$tipo = 'Contado';
             	$poliza_facture->type = '2';
				$s_code_counts        = 'PP';
             	break;
        case 2:
             if ( $vfac->paye==1) {
				$tipo                 = 'Crédito-Pagado';
				$poliza_facture->type = 211;
				$s_code_counts        = 'PPP';
				$ctas = $cta->get_cuentas_poliza('PxP');

				foreach ($ctas as $key => $value) {
					if ($key == 1) {
						$M_row_credit =  $cta->get_cuentas_agrupacion_obj($value->codagr);
					}
				}

					
             }else{
                 $s_code_counts= 'PxP';
                 $poliza_facture->type = 21;
             }
             break;

        default:
            $poliza_facture->type = '2';
			$s_code_counts        = 'PP';
             break;
     }


	if ($s_code_counts != 'default') {
		$ctas = $cta->get_cuentas_poliza($s_code_counts);

		  foreach($ctas as $key => $value){
		  		$M_row =  $cta->get_cuentas_agrupacion_obj($value->codagr);
		  		$count++;
		  	if ($M_row) {
		  			
						print ('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">');
							print ('<div class="form-group">');
								print ('<div class="col-sm-3">');
										print ('<select class="select2_single form-control slc_cuenta" >');
											foreach ($ctas_registradas as $key => $value){
												if ( $poliza_facture->type == 21 && count($aux)>0 &&  $aux['codagr'] == $key && $count==3) {
													$M_row->codagr = $key;
													print ($cuenta->existe($key) <= 0) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : ''; 
												}elseif ($poliza_facture->type == 211 && count($aux)>0 &&  $aux['codagr'] == $key && $count==1) {
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

							if ($count==1) {
									print ('<div class="col-sm-2">');
										if ($poliza_facture->type == 21 || $poliza_facture->type == 2) {
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" id="compra_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}elseif ($poliza_facture->type == 211) {
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" id="proveedor_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}else{
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}
									print ('</div>');

									print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
									print ('</div>');
							}else if($count==2){
									print ('<div class="col-sm-2">');
										if ($poliza_facture->type == 21 || $poliza_facture->type == 2) {
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" id="iva_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}
										elseif ($poliza_facture->type == 211) {
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"  id="iva_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
											
										}else{
											print ('<input type="text" placeholder="Debe" name="txt_debe[]"  value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}
									print ('</div>');
									print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Haber" name="txt_haber[]"  value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										
									print ('</div>');
							}else if($count==3  ){
									print ('<div class="col-sm-2">');
										print ('<input type="text" placeholder="Debe" name="txt_debe[]"  value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
									print ('</div>');
									print ('<div class="col-sm-2">');
									
									if ($poliza_facture->type == 21 || $poliza_facture->type == 2) {
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="proveedor_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
									}
									else if ($poliza_facture->type == 211) {
										print ('<input type="text" placeholder="Haber" name="txt_haber[]"  id="bancos_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
									}
									else{
										print ('<input type="text" placeholder="Haber" name="txt_haber[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');

									}
									print ('</div>');
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
						if ($count == 3 && $poliza_facture->type == 211) {
		  					insert_select_asiento($M_row_credit);
		  			}
		  			
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
								print ('<input type="text" placeholder="Debe" name="txt_debe[]"   value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
						print ('</div>');
						print ('<div class="col-sm-2">');
									print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="iva_haber_pendiente"  value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
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