<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class carga_cuenta extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_total($cod) {
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_cat_ctas WHERE codagr='".$cod."'");
		$row = mysqli_fetch_assoc($result);
		return $row['no'];
	}

	public function insert($nivel, $cod, $desc, $natur,$codsat,$afectada) {
		$result = $this->db->query("INSERT INTO ".PREFIX."contab_cat_ctas (nivel, codagr, descripcion, natur,codsat,afectacion)
						VALUES(".$nivel.",'".$cod."','".$desc."','".$natur."','".$codsat."','".$afectada."')");
		return ($result) ? 1: 0;
	}
}
$contador=0;
$carga_cuenta = new carga_cuenta();

$cod = addslashes(trim($_POST['codigo_cuenta']));
$nivel = addslashes(trim($_POST['nivel_cuenta']));
$desc = addslashes(trim(($_POST['nombre_cuenta'])));
$natur = addslashes(trim($_POST['naturaleza_cuenta']));
$codsat = addslashes(trim($_POST['codigo_sat']));
$afectada = addslashes(trim($_POST['afectada']));

$total = $carga_cuenta->get_total($cod);

if( empty($cod) ||empty($nivel) ||empty($desc) || empty($natur) ){
	  $error = "Datos incompletos";
    $return["json"] = json_encode($error);
    exit (json_encode($return));
}

if(empty($codsat)){
    $codsat = 0;
}
if( $total == 0 ) {
	$resultado = $carga_cuenta->insert($nivel, $cod, $desc, $natur,$codsat,$afectada);
	if($resultado == 0) {
		$contador++;
		$error = "Error: Problema al registrar la cuenta en la base de datos<br />";
	}
}
else {
	$contador++;
	$error = "No se ha registrado la cuenta con codigo ".$cod.", ya existe en el catalogo de cuentas.<br />";
}

$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode($error);
echo json_encode($return);
