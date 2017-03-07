<?php 
$url[0] = "../";
require_once "../conex/conexion.php";
require_once ($url[0]."class/sat_ctas.class.php");
$not_rel         = new Cuenta();
$not_rel_cuentas = $not_rel->get_cuentas();


class editar_cuenta extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 

	public function get_cuenta($rowid) { 
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE rowid =".$rowid); 
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$cuenta[$row['rowid']] = $row['codagr'].'*!*'.$row['descripcion'].'*!*'.$row['nivel'].'*!*'.$row['natur'].'*!*'.$row['codsat'].'*!*'.$row['afectacion'];
		}
		return $cuenta;
	} 
}

$editar_cuenta = new editar_cuenta(); 
$arreglo = $editar_cuenta->get_cuenta($_POST['id']);

foreach ( $arreglo as $key => $value ) {
	$value = explode('*!*', $value);
}

$s1 = ($value[2]==1) ? 'selected' : '';
$s2 = ($value[2]==2) ? 'selected' : '';
$s3 = ($value[2]==3) ? 'selected' : '';
$s4 = ($value[2]==4) ? 'selected' : '';
$s4 = ($value[2]==4) ? 'selected' : '';

$n1 = ($value[3]=='A') ? 'selected' : '';
$n2 = ($value[3]=='D') ? 'selected' : '';
$a = (int)$value[5];
$codsat= $value[4];

print '<div align="right" style="cursor:pointer; font-weight:bold;" class="regresar"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Regresar</div>
		<form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
			 <input  type="hidden" name="rowid" value="'.$_POST['id'].'">
            <div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="">Código contable</label>
					<input type="text" required="required" class="form-control col-md-7 col-xs-12" name="codigo_cuenta" value="'.$value[0].'">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="">Nombre cuenta</label>
					<input class="form-control col-md-7 col-xs-12" type="text" name="nombre_cuenta" value="'. ($value[1]).'">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label>Nivel</label>
					<select class="select2_single form-control" tabindex="-1" name="nivel_cuenta">
						<option></option>
						<option value="1" '.$s1.'>1</option>
						<option value="2" '.$s2.'>2</option>
						<option value="3" '.$s3.'>3</option>
						<option value="4" '.$s4.'>4</option>
					</select>
				</div> 
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label>Naturaleza</label>
					<select class="select2_single form-control" tabindex="-1" name="naturaleza_cuenta">
						<option></option>
						<option value="A" '.$n1.'>Acredora</option>
						<option value="D" '.$n2.'>Deudora</option>
					</select>
				</div> 
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label class="">Código SAT</label>
				        <select class="select2_single form-control" name="codigo_sat"  id="codigo_sat">';
                               
                           foreach ($not_rel_cuentas as $key => $value){
                                if($codsat== $key){
                                     print '<option value="'.$key.'" selected="selected">'.$value.'</option>';           
                                }else{
                                     print '<option value="'.$key.'" >'.$value.'</option>';       
                                }
                            }
            print '</select>									
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
                    <label class="">Afectada</label>
                        <select class="select2_single form-control" name="afectada">';
                              if($a == 0){
                                     print '<option selected="selected" value="0">No</option>';      
                                     print ' <option  value="1">Sí</option>';   
                                }else{
                                    print '<option value="0">No</option>';       
                                     print ' <option  value="1" selected="selected" >Sí</option>';       
                                }   
            print '</select>								
				</div>
			</div>
			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<button type="reset" class="btn btn-primary regresar">Limpiar</button>
					<button type="submit" class="btn btn-success " >Actualizar</button>
				</div>
			</div>
		</form>';