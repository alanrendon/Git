<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$url[0] = "../";
require_once "../conex/conexion.php";

class cuentas_registradas extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_cuentas() {
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas ORDER BY codagr ASC");
        $cuenta =  array();
        if($result){
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                switch($row['natur']){
                    case 'D':
                        $row['natur'] ='Deudora';
                        break;
                    case 'A':
                        $row['natur'] ='Acreedora';
                        break;
                }
                $cuenta[$row['rowid']] = $row['descripcion'].'*|*'.$row['codagr'].'*|*'.$row['natur'].'*|*'.$row['codsat'].'*|*'.$row['afectacion'].'*|*'.$row['nivel'];
            }
        }
		
		return $cuenta;
	}
}

$cuentas_registradas = new cuentas_registradas();
$arreglo = $cuentas_registradas->get_cuentas();

$res = '<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>No.</th>
					<th>Cuenta</th>
					<th>Descripción</th>
					<th>Código <br />SAT</th>
					<th>Naturaleza</th>
					<th>Afectada <br />por pólizas</th>
					<th class="column-title no-link last"></th>
				</tr>
			</thead>
			<tbody>';

$contador = 1;


foreach ( $arreglo as $key => $value ) {

	$value = explode('*|*', $value);
  $a = (int)$value[4];
	$a = ($a==0) ? 'No':'Sí';
	$res .= '<tr>';
	$res .= '<td>'.$contador.'</td>';
	$res .= '<td class="left_periodo">'.$value[1].'</td>';
	$res .= '<td class="left_periodo">'. ($value[0]).'</td>';
	$res .= '<td>'.$value[3].'</td>';
	$res .= '<td align="center">'.$value[2].'</td>';
	$res .= '<td align="center">'.$a.'</td>';
	$res .= '<td>';
	$res .= '<ul class="nav panel_toolbox" style="min-width: 50px !important;"><li><a id="'.$key.'" class="edit-link" title="Editar"><i class="fa fa-file-text" style="color: #FFC300"></i></a></li><li><a id="'.$key.'" class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a></li></ul>';
	$res .= '</td>';
	$res .= '</tr>';
	$contador++;
}

$res .= '</tbody>
		</table>';

print $res;
