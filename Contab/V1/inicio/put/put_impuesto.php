<?php
$url[0] = "../";
require_once "../conex/conexion.php";

class impuesto extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_total($impuesto) {
		$result = $this->db->query("SELECT count(*) as no FROM ".PREFIX."contab_impuestos WHERE nombre='".$impuesto."'");

		$row = mysqli_fetch_assoc($result);
		return $row['no'];
	}

	public function insert($impuesto,$valor,$tipo) {
		$result = $this->db->query("INSERT INTO ".PREFIX."contab_impuestos (nombre,impuesto,tipo)
						VALUES('".$impuesto."','".$valor."','".$tipo."')");

		return ($result) ? 1: 0;
	}
}

$contador = 0;
$registro_impuesto = new impuesto();

$impuesto = addslashes(trim($_POST['impuesto']));
$valor    = addslashes(trim($_POST['valor']));
$tipo     = (int)$_POST['tipo'];

if( empty($impuesto) || empty($valor)) {
	$error = "Datos incompletos";
    $return["json"] = json_encode($error);
    exit (json_encode($return));
}
else {
	$total = $registro_impuesto->get_total($impuesto);
    $valor= floatval($valor);
    
    if($valor == 0 || !$valor)
    {
        $error = "Ingrese un valor de impuesto correcto.<br />";
    }else if( $total == 0 ) {
		$resultado = $registro_impuesto->insert($impuesto,$valor,$tipo);
		if( $resultado == 0 ) {
			$contador++;
			$error = "Error: Problema al registrar la cuenta en la base de datos.<br />";
		}
	}
	else {
		$contador++;
		$error = "El impuesto ya se encuentra registrado.<br />";
	}

	$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode($error);
	echo json_encode($return);
}
