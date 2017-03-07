<?php
$url[0] = "../";
require_once "../conex/conexion.php";
require_once "../class/conf_apartados.class.php";

class cuentas_registradas extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_grupos() {
        $grupo = array();
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_grupos WHERE tipo_edo_financiero = 1");

		if($result){
           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $grupo[$row['rowid']] = $row['grupo'].'*|*'.$row['fk_codagr_rel'].'*|*'.$row['fk_codagr_ini'].'*|*'.$row['fk_codagr_fin'].'*|*'.$row['fk_grupo'];
            }
        }

		return $grupo;
	}

	public function get_cuenta($rowid) {
        $cuenta = array();
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE rowid=".$rowid);
        if($result){
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $cuenta[$row['rowid']] = $row['codagr'].' '.$row['descripcion'];
            }
        }

		return $cuenta;
	}
}

$cuentas_registradas = new cuentas_registradas();
$arreglo = $cuentas_registradas->get_grupos();
$apartados = new Apartados();
$res = '<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>Grupo Padre</th>
					<th>Grupo</th>
					<th>Cuenta inicial</th>
					<th>Cuenta final</th>
					<th class="column-title no-link last"><span class="nobr"></span></th>
				</tr>
			</thead>
			<tbody>';

$contador = 1;

if(count($arreglo)<1){
    exit('No hay agrupaciones de cuentas');
}
foreach ( $arreglo as $key => $value ) {
	$value = explode('*|*', $value);
	$a = $cuentas_registradas->get_cuenta($value[1]);
	$b = $cuentas_registradas->get_cuenta($value[2]);
	$c = $cuentas_registradas->get_cuenta($value[3]);

     $padre = $apartados->get_apartado_id($value[4]);
     if(!is_object($padre)){
            $grupo_padre= 'Sin grupo padre';
     }else{
          $grupo_padre= $padre->apartado;
     }
    
    if(isset($grupo_padre) && isset($value[0])  && isset($b[$value[2]]) && isset($c[$value[3]])){
        
        $res .= '<tr>';
        $res .= '<td>'.($grupo_padre).'</td>';
        $res .= '<td>'.($value[0]).'</td>';
        $res .= '<td>'.($b[$value[2]]).'</td>';
        $res .= '<td>'.($c[$value[3]]).'</td>';
        $res .= '<td>';
        $res .= '<ul class="nav panel_toolbox" style="min-width: 50px !important;">';
        $res .= '<li><a href="edit.php?id='.$key.'&tipo=1"  title="Editar"><i class="fa fa-file-text" style="color: #FFC300"></i></a></li>';
        $res .= '<li><a id="'.$key.'" class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a></li></ul>';
        $res .= '</td>';
        $res .= '</tr>';
        $contador++;
    }
}

$res .= '</tbody>
		</table>';

print $res;
