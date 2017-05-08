<?php
$url[0] = "../";
require_once ($url[0]."class/cuentas_rel.class.php");
require_once ($url[0]."class/cat_cuentas.class.php");
require_once ($url[0]."class/facturedet.class.php");

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['Societev']) && isset($_POST['tipo'])) {
	$rel             = new Rel_Cuenta();
	$not_rel         = new Cuenta();
	$facturedet      = new FactureDet();

	$object_clie_prov = addslashes(trim($_POST['Societev']));
  	$string          = explode("-",$object_clie_prov);
	$tipo            = (int)$_POST['tipo'];
	echo " cuentas rel ".$object_clie_prov. " ".$tipo ;

  	$rel_cuentas    = false;

  	$object[]       = array ( 'rowid' => $string[0], 'societe' => $tipo);


  foreach($object as $obj){
		if ($tipo == 1 ) {
			$obj['societe'] = $string[2]!=2 ? 1 : 20;
		}
      $aux  = $rel->get_cuentasRelFact($obj['societe'],$obj['rowid']);
      if($aux){
          $rel_cuentas[$aux['codagr']]    = $aux['codagr'].' - '. ($aux['descripcion']);
      }

  }

	$not_rel_cuentas = $not_rel->get_cuentas();

	if ( $tipo == 2 ) {
		$debe = 'id="txt_haberClienteCta"';
		$haber = 'value="0.0"';
	}
	else {
		$haber = 'id="txt_haberClienteCta" ';
		$debe = 'value="0.0"';
	}
    $count=0;
	if (is_array($rel_cuentas)) {
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
	else {
		print('<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">');
		print('<div class="form-group">');
		print('<div class="col-sm-3">');
		print('<select class="select2_single form-control slc_cuenta" >');
		print('<option value=""></option>');
			foreach ($not_rel_cuentas as $key => $value){
				print ($not_rel->existe($key) <= 0) ? '<option value="'.$key.'" >'. ($value).'</option>' : '';
			}
		print('</select>');
		print('<input type="hidden" name="txt_cuenta[]" class="form-control col-md-7 col-xs-12" placeholder="Cuenta">');
		print('</div>');
         print('<div class="col-sm-4">');
        print('<input type="text" placeholder="Descripión" name="descripcion[]" value="" class="form-control col-md-7 col-xs-12 txt_debe">');
        print('</div>');
		print('<div class="col-sm-2">');

		print('<input type="text" placeholder="Debe" name="txt_debe[]" '.$debe.' class="form-control col-md-7 col-xs-12 txt_debe">');
		print('</div>');
		print('<div class="col-sm-2">');

		print('<input type="text" placeholder="Haber" name="txt_haber[]" '.$haber.'  class="form-control col-md-7 col-xs-12 txt_haber">');
		print('</div>');

		print('<ul class="nav left panel_toolbox"><li><a class="a_borrarAsiento" title="Borrar" idasiento="75"><i class="fa fa-trash" style="color: red"></i></a></li></ul>');

		print('</div>');
		print('</div>');
	}
}
?>
