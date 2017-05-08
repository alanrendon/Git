<?php
    $url[0] = "../";
    require_once ("../class/cat_cuentas.class.php");
    $not_rel         = new Cuenta();
    $not_rel_cuentas = $not_rel->get_cuentas();
    $concepto        = '';

    if(isset($_POST['concepto'])){
        $concepto= explode(":",addslashes(trim($_POST['concepto'])));
	
        if(strtoupper ( $concepto[0] )== strtoupper ('Concepto')){
            $concepto= $concepto[1];
        }else{
            $concepto = addslashes(trim($_POST['concepto']));
        }
    }
    
    print('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">');
	print('<div class="form-group">');
	echo('<div class="col-sm-3">');
    print('<select class="select2_single form-control slc_cuenta" >');
		print('<option value=""></option>');
			foreach ($not_rel_cuentas as $key => $value){
				print ($not_rel->existe($key) <= 0) ? '<option value="'.$key.'" >'.$value.'</option>' : ''; 
			}									
		print('</select>');
	print('<input type="hidden" name="txt_cuenta[]" class="form-control col-md-7 col-xs-12">');
	print('</div>');
    print('<div class="col-sm-4">');
	print('<input type="text" placeholder="Descripión" name="descripcion[]" value="'.$concepto.'" class="form-control col-md-7 col-xs-12 txt_debe">');
	print('</div>');
	print('<div class="col-sm-2">');
	print('<input type="text" placeholder="Debe" name="txt_debe[]" id="txt_debe"  value="0.0" class="form-control col-md-7 col-xs-12 txt_debe">');
	print('</div>');
	print('<div class="col-sm-2">');
	print('<input type="text" placeholder="Haber" name="txt_haber[]"  id="txt_haber" value="0.0" class="form-control col-md-7 col-xs-12 txt_haber">');
    print('</div>');
	print('<ul class="nav left panel_toolbox">');
		print('<li>');
			print('<a class="a_borrarAsiento" title="Borrar" idasiento="75">');
				print('<i class="fa fa-trash" style="color: red"></i>');
			print('</a>');
		print('</li>');
	print('</ul>');
	print('</div>');
	print('</div>');
?>
	                                		
											
												
													
													
												
												
													
													
												
												
													
													
												
												
													
													
												
											    
										