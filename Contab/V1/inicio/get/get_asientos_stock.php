<?php
$url[0] = "../";


require "../conex/conexion.php";
require "../class/tipo_poliza.class.php";
require "../class/cuentas_poliza.class.php";
require "../class/cat_cuentas.class.php";
require ($url[0]."class/cuentas_rel.class.php");
require_once ($url[0]."class/stock.class.php");

if (isset($_POST['idMovStock']) ) {
	$fact =new Movstock();
	$vfac =$fact->get_movimientos_stock_id((int)$_POST['idMovStock']);
	if ($vfac) {
		get_partidas($vfac[0]);
	}
}

function get_partidas($vfac){
	$rel              = new Rel_Cuenta();
	$cuenta           = new Cuenta();
	$tipo_polizas     = new Tipo_Poliza();
	$cta              = new Cuentas_Poliza();
	$ctas_registradas = $cuenta->get_cuentas();
	$count            = 0;

    if ($vfac->price*$vfac->unidades>0) {
		$s_code_counts        = 'PRS';
    }else{
		$s_code_counts        = 'PSS';
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
												if($key == $M_row->codagr){
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
										if ($s_code_counts == 'PRS') {
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" id="prs_debe" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}else{
											print ('<input type="text" placeholder="Debe" name="txt_debe[]" value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
										}

									print ('</div>');

									print ('<div class="col-sm-2">');
										if ($s_code_counts == 'PSS') {
											print ('<input type="text" placeholder="Haber" name="txt_haber[]" id="pss_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
										}else{
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
		  		}

		  }
                                         
	}

    }
?>