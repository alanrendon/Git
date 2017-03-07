<?php
$url[0] = "../";
require_once ($url[0]."class/cuentas_rel.class.php");
require_once ($url[0]."class/cat_cuentas.class.php");
require_once ($url[0]."class/stock.class.php");

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['tipomovimiento']) && isset($_POST['tipo'])) {
	$rel             = new Movstock();
	$not_rel         = new Cuenta();

	$object = trim($_POST['tipomovimiento']);

  	$rel_cuentas    = false;
  
	$condicion="";
	if($object==1){
		$condicion=" cuenta_rel.fk_type=20";
	}
	if($object==2){
		$condicion=" cuenta_rel.fk_type=21";
	}
	if($object==3){
		$condicion=" cuenta_rel.fk_type=20 OR cuenta_rel.fk_type=21";
	}
      $aux  = $rel->get_cuentasRelStock($condicion);
     if($aux){
     	$rel_cuentas=array();
     }
    for($i=0;$i<count($aux);$i++){
     // $arr=$aux;
        $rel_cuentas[$aux[$i]['codagr']]    = $aux[$i]['codagr'].' - '. ($aux[$i]['descripcion']);
    	//print_r($aux[1]['codagr']);print "<br>";
    }
    //print_r($rel_cuentas);print "<br>";
	
	$haber = 'value="0.0"';
	$debe = 'value="0.0"';
    $count=0;
	if (is_array($rel_cuentas) && count($aux)>0) {
		foreach ($rel_cuentas as $key => $value) {

			$descripcion =  explode( '-', $value ) ;
            if($count>0){
                $debe='value="0.0"';
                $haber='value="0.0"';
            }
			print('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">');
			print('<div class="form-group">');
			print('<div class="col-sm-3">');
			print('<input type="text" readonly="readonly" name="txt_cuenta[]" class="form-control col-md-7 col-xs-12" value="'.$value.'">');
			print('</div>');
             print('<div class="col-sm-4">');
            print('<input type="text" placeholder="Descripión" name="descripcion[]" value="" class="form-control col-md-7 col-xs-12 txt_debe">');
            print('</div>');
			print('<div class="col-sm-2">');
			print('<input type="text" placeholder="Debe"  name="txt_debe[]" '.$debe.'  class="form-control col-md-7 col-xs-12 txt_debe">');
			print('</div>');
			print('<div class="col-sm-2">');
			print('<input type="text" placeholder="Haber" name="txt_haber[]" '.$haber.' class="form-control col-md-7 col-xs-12 txt_haber">');
			print('</div>');

			print('<ul class="nav left panel_toolbox"><li><a class="a_borrarAsiento" title="Borrar" idasiento="75"><i class="fa fa-trash" style="color: red"></i></a></li></ul>');
			print('</div>');
			print('</div>');
      $count ++;
		}
	}
}
?>
