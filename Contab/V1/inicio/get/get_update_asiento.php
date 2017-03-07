<?php
$url[0] = "../";
require_once "../class/asiento.class.php";
require_once "../class/cat_cuentas.class.php";

$cuenta          = new Cuenta();
$asiento         = new Asiento();
$arreglo_cuentas = $cuenta->get_cuentas();

if(isset($_POST['asiento'])){
    
    $id_asiento     =(int)$_POST['asiento'];
    $asiento        =$asiento->get_asiento_obj_id($id_asiento);
    
    print ('<div class="form-group">');
        print ('<div class="col-sm-12">');
            print ('<label class="">Cuenta <span class="required">*</span></label>');
                print ('<select class="select2_single select2_asiento form-control" name="txt_cuenta"  id="txt_cuenta" size="90" style="width:300px">');
                   foreach ($arreglo_cuentas as $key => $value){
                       if($key==$asiento->cuenta){
                            print ('<option value="'.$key.'" selected="selected">'.$value.'</option>');
                       }else{
                            print ('<option value="'.$key.'">'.$value.'</option>');
                       }
                   }
                print ('</select>');
        print ('</div>');
        print ('<div class="col-md-12 col-sm-12 col-xs-12">');
            print ('<label class="">Descripción <span class="required">*</span></label>');
                print ('<input type="text" placeholder="Descripción" name="txt_descripcion" class="form-control col-md-7 col-xs-12" value="'.$asiento->descripcion.'">');
        print ('</div>');
        print ('<div class="col-md-6 col-sm-6 col-xs-6">');
            print ('<label class="">Debe <span class="required">*</span></label>');
                print ('<input type="text" placeholder="Debe" name="txt_debe" id="txt_debe"  class="form-control col-md-7 col-xs-12" value="'.number_format($asiento->debe, 2, '.', ',').'">');
        print ('</div>');
        print ('<div class="col-md-6 col-sm-6 col-xs-6">');
            print ('<label class="">Haber <span class="required">*</span></label>');
            print ('<input type="text" placeholder="Haber" name="txt_haber"  id="txt_haber" class="form-control col-md-7 col-xs-12" value="'.number_format($asiento->haber, 2, '.', ',').'">');
        print ('</div>');
    print ('</div>');
           
}else{
    print('No ha seleccionado asiento');
}

?>