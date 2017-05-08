<?php
// CAPTURA DE BLOCKS	
// Tabla 'BLOC'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
$tope = $_POST['enviados'];
$f1 = $_POST['folio_inicio'];
$f2 = $_POST['folio_final'];
$f_bloc = $_POST['folios_por_block'];
$letra='x';
if($_POST['num_cia'] != "")
{
		for ($i=0;$i<$tope;$i++) 
		{
			$f2=($f1 + $f_bloc) - 1;
			$datos['idcia'.$i] = $_POST['num_cia'];
			$datos['folio_inicio'.$i] = $f1;
			$datos['folio_final'.$i] = $f2;
			$datos['num_folios'.$i] = $_POST['folios_por_block'];
			$datos['fecha'.$i] = $_POST['fecha'];
			if($_POST['letra_folio']=="")
				$letra = 'x';
			else 
				$letra = strtoupper($_POST['letra_folio']);
			$datos['let_folio'.$i] = $letra;
			$datos['folios_usados'.$i] = $_POST['folios_usados'];
			$datos['estado'.$i] = $_POST['estado'];
			$f1=$f2 + 1;
		}
}
else
{
	header("location: ./pan_bloc_cap.php?codigo_error=1");
	die;
}
$db = new DBclass($dsn, $tabla, $datos);
$db->xinsertar();
header("location: ./pan_bloc_cap.php");
?>