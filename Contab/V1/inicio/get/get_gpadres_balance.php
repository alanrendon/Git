<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class grupos_registrados extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_grupos() {
        $grupo = array();
		$result = $this->db->query("SELECT * FROM ".PREFIX."conf_apartados WHERE reporte = 1");
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$grupo[$row['rowid']] = $row['apartado'].'*|*'.$row['tipo'];
		}
		return $grupo;
	}
}

$grupos_registrados = new grupos_registrados();
$arreglo = $grupos_registrados->get_grupos();

$res = '<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>Grupo Padre</th>
					<th width="15%">Tipo</th>
					<th width="5%"></th>
				</tr>
			</thead>
			<tbody>';

$contador = 1;

foreach ( $arreglo as $key => $value ) {
	$value = explode('*|*', $value);
    
    switch ($value[1]){
        case 1:
            $value[1] ='Activo';
            break;
        case 2:
            $value[1] ='Pasivo';
            break;
        case 3:
            $value[1] ='Capital Social';
            break;
    }

	$res .= '<tr>';
	$res .= '<td>'. ($value[0]).'</td>';
	$res .= '<td>'. ($value[1]).'</td>';
	$res .= '<td>';
	$res .= '<ul class="nav panel_toolbox" style="min-width: 10px !important;"><li><a id="'.$key.'" class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a></li></ul>';
	$res .= '</td>';
	$res .= '</tr>';
	$contador++;
}

$res .= '</tbody>
		</table>';

print $res;
