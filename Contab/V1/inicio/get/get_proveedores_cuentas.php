<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class proveedores_cuentas extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_cuentas() {
    $cuenta = array();
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cuentas_rel WHERE fk_type = 2");
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$cuenta[$row['rowid']] = $row['fk_object'].'*|*'.$row['fk_cuenta'];
		}
		return $cuenta;
	}

	public function get_cliente($rowid) {
		$result = $this->db->query("SELECT nom FROM ".PREFIX."societe WHERE rowid = ".$rowid);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['nom'];
	}

	public function get_cuenta($rowid) {
		$result = $this->db->query("SELECT * FROM ".PREFIX."contab_cat_ctas WHERE rowid = ".$rowid);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['codagr'].' - '.$row['descripcion'];
	}
}


$proveedores_cuentas = new proveedores_cuentas();
$arreglo = $proveedores_cuentas->get_cuentas();

    $res = '<table id="datatable" class="table table-striped jambo_table bulk_action" width="100%" cellspacing="0">
							<thead>
				          <tr>
				              <th>No.</th>
				              <th>Proveedor</th>
				              <th>Cuenta</th>
				              <th class="column-title no-link last"></th>
				          </tr>
				      </thead>
				      <tbody>';
    $contador = 1;
    foreach ( $arreglo as $key => $value ) {
        $value = explode('*|*', $value);
        $cliente = $proveedores_cuentas->get_cliente($value[0]);
        $cuenta = $proveedores_cuentas->get_cuenta($value[1]);
        $res .= '<tr>';
        $res .= '<td>'.$contador.'</td>';
        $res .= '<td>'. ($cliente).'</td>';
        $res .= '<td>'. ($cuenta).'</td>';
        $res .= '<td align="center">';
        $res .= '<ul class="nav panel_toolbox" style="min-width: 10px !important;"><li><a id="'.$key.'" class="delete-link" title="Borrar"><i class="fa fa-trash" style="color: red"></i></a></li></ul>';
        $res .= '</td>';
        $res .= '</tr>';
        $contador++;
    }

    $res .= '</tbody>
            </table>';

    print $res;