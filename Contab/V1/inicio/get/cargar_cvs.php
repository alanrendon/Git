<?php 
$url[0] = '../';
require_once '../conex/conexion.php';
require_once ('../class/carga_cvs.class.php');
require_once ('../class/cuentas_rel.class.php');
require_once ('../class/grupo.class.php');

$contador   =0;
$carga_cvs  = new carga_cvs(); 
$not_rel    = new Rel_Cuenta();
$grupo      = new Grupo();
$tipo       = $_FILES['file']['type'];
$tamanio    = $_FILES['file']['size'];
$archivotmp = $_FILES['file']['name'];
$ruta       = ''.$_FILES['file']['name'];

$resultado = @move_uploaded_file($_FILES['file']['tmp_name'], $ruta);

$error='';
$error_cod='';

/*
if( $tipo != 'text/csv' || $tipo != ' application/vnd.ms-excel' ){
    unlink($archivotmp);
    exit('Error, el archivo solo puede ser CSV ');
}*/
$i=0;
if($_POST['eliminar_ctas']==1) $not_rel->delete_not_rel_ctas();


if (($fp = fopen($archivotmp, 'r')) == true) {
	while (( $data = fgetcsv ( $fp , null, ',')) != false ) { 
        if (isset($data) && count($data)>=4) {

        	$nivel    = isset($data[0]) ? $data[0]:'';
			$cod      = isset($data[1]) ? $data[1]:'';
			$desc     = isset($data[2]) ? $data[2]:'';
			$natur    = isset($data[3]) ? $data[3]:'';
			$afectada = isset($data[4]) ? $data[4]:1;
			$codsat   = isset($data[5]) ? $data[5]:'';
	    	$total    = $carga_cvs->get_total($cod);

	    	if( $total == 0 ) {
	    		$resultado = $carga_cvs->insert($nivel, $cod, $desc, $natur,$afectada,$codsat);
				if($resultado == 0){
					$contador++;
					$error .= 'Error: problema al insertar registro en base de datos<br>';
				}
			}
			else{
				$resultado =  $carga_cvs->update($nivel, $cod, $desc, $natur,$afectada,$codsat); 
			}
        }
		
	}
	fclose ( $fp );
    unlink($archivotmp);
	if( $contador == 0 ) 
		print 'Se cargaron todos los registros exitosamente.';
	else 
		print 'Hubo problemas al cargar los registros.';
}
else {
	print 'Problemas al abrir el archivo: '.$error;
}