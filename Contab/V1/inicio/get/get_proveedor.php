<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class proveedor extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_proveedores() {
		$result = $this->db->query("SELECT * FROM ".PREFIX."societe WHERE fournisseur = 1");
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$proveedor[$row['rowid']] = $row['nom'];
		}
		return $proveedor;
	}

	public function existe($rowid) {
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cuentas_rel WHERE fk_type = 2 AND fk_object=".$rowid);
		$row = mysqli_fetch_array($result);
		return $row['no'];
	}
}

$proveedores = new proveedor();
$arreglo = $proveedores->get_proveedores();

print '<input type="hidden" value="2" name="tipo"><label class="">Proveedor</label>
           <select class="select2_multiple form-control select2-hidden-accessible" name="datos[]" multiple>
		<option></option>';

foreach ($arreglo as $key => $value) {
	print ($proveedores->existe($key) <= 0) ? '<option value="'.$key.'" >'. ($value).'</option>' : '';
	//print '<option value="'.$key.'" >'.$value.'</option>';
}

print '</select>';
