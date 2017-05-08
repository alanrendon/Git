<?php
    $url[0] = "../";
    require_once "../class/tipo_poliza.class.php";
    require_once "../class/cuentas_poliza.class.php";
    require_once "../class/cat_cuentas.class.php";


    if(isset($_POST['tipo']) && !empty($_POST['tipo'])){
        $tipo_polizas = new Tipo_Poliza();
        $cta          = new Cuentas_Poliza();
        $concepto       = '';
        if(isset($_POST['concepto'])){
            $concepto= explode(":",addslashes(trim($_POST['concepto'])));
	
            if(strtoupper ( $concepto[0] )== strtoupper ('Concepto')){
                $concepto= $concepto[1];
            }else{
                $concepto = addslashes(trim($_POST['concepto']));
            }
        }
        $ctas = $cta->get_cuentas_poliza(addslashes(trim($_POST['tipo'])));
        if(isset($_POST['total'])){
            print('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="marging-top: 0px !important;">');
                print('<div class="col-sm-3">');
                    print('<label class="">Cuenta <span class="required">*</span></label>');
                print('</div>');
               print('<div class="col-sm-4">');
                     print('<label class="">Descripcion</label>');
                print('</div>');
                print('<div class="col-sm-2">');
                    print('<label class="">Debe <span class="required">*</span></label>');
                print('</div>');
                print('<div class="col-sm-2">');
                    print('<label class="">Haber <span class="required">*</span></label>');
                print('</div>');
              
            print('</div>');
          }
        foreach($ctas as $value){
            $row=$cta->get_cuentas_agrupacion($value->codagr);
            print('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="padding-top: 0px !important;">');
            print('<div class="form-group">');
            print('<div class="col-sm-3">');
            print('<input type="text" readonly name="txt_cuenta[]" class="form-control col-md-7 col-xs-12" value="'.$row[0].'">');
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
            
        }
        if(isset($_POST['total'])){
            
        }
    }
	
?>
	                                		
											
												
													
													
												
												
													
													
												
												
													
													
												
												
													
													
												
											    
										