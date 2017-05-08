<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class proveedor extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_proveedores() {
		$proveedor = array();
		$result = $this->db->query("SELECT * FROM ".PREFIX."societe WHERE fournisseur = 1");
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$proveedor[$row['rowid']] = $row['nom'];
		}
		return $proveedor;
	}

	public function existe($rowid) {
		$sql = 'SELECT
					count(*) AS no
					FROM
					llx_contab_operation_societe_fourn AS osf
					WHERE
					osf.id_societe = "'.$rowid.'"';
		
		$result = $this->db->query($sql);
		$row = mysqli_fetch_array($result);
		return $row['no'];
	}
}

$proveedores = new proveedor();
$arreglo = $proveedores->get_proveedores();

print '<label>Proveedor</label>
<select class="select2_multiple form-control select2-hidden-accessible" name="datos[]" multiple>
		<option></option>';
foreach ($arreglo as $key => $value) {
	print ($proveedores->existe($key) <= 0) ? '<option value="'.$key.'" >'. ($value).'</option>' : '';
	
}
print '</select>';
