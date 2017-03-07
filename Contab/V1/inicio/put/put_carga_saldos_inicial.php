<?php 
$url[0] = '../';
require_once '../conex/conexion.php';
require_once ('../class/carga_cvs.class.php');
require_once ('../class/cat_cuentas.class.php');
require_once ('../class/poliza.class.php');
require_once ('../class/asiento.class.php');

$contador   =0;
$cuenta     = new Cuenta(); 

$tipo       = $_FILES['file']['type'];
$tamanio    = $_FILES['file']['size'];
$archivotmp = $_FILES['file']['name'];
$ruta       = ''.$_FILES['file']['name'];
$fk_poliza  = @move_uploaded_file($_FILES['file']['tmp_name'], $ruta);
$error      ='';
$i          =0;

if(isset($_POST['fecha']) && !empty($_POST['fecha'])){
    $txt_fecha =$_POST['fecha'];
}else{
  $txt_fecha =date('m/d/Y');
}
$asiento = new Asiento();

if (($fp = fopen($archivotmp, 'r')) == true) {
	while (( $data = fgetcsv ( $fp , 100, ',')) != false ) { 
        
		$cta      =$data[0];
		$debe     =$data[1];
		$habe     =$data[2];

    	if( $cuenta->existe_cta($cta) > 0 ) {
            if($i==0){
                 $poliza = new Poliza();
                 $cons   =$poliza->get_ConsPoliza('D');
                 $fk_poliza = $poliza->put_Polizas($cons,$txt_fecha,'D','Saldo inicial',0,'','','Póliza de saldo inicial',0);
                 if (!$fk_poliza) 
                    exit(json_encode( array('mensaje' =>"Error, no se ha podido agregar la póliza.")));
   
             }
    		  $fk_poliza       =(int)$fk_poliza;
              if ($debe>0  && $habe>0 ) {
                   print "Ingrese debe o haber, no los dos. Cta: ".$cta.'<br/>';
              }
              else if (isset($debe) && $debe!=0 && $debe!='') {
                   $habe = "";
              }   
              else {
                  $debe = "";
              }
            
              if ( !empty($fk_poliza)  && !empty($cta) ) {
                   
                    $no_asiento = $asiento->get_lastAsiento($fk_poliza);
                    if ( !$asiento->put_asiento($fk_poliza,$no_asiento,$cta,$debe,$habe,"" ) ) {
                         $error.="Error no se pudo registrar la cuenta: ".$cta.'<br/>';
                    }
              }
              else{
                  print "Error faltan datos en la cuenta: ".$cta.'<br/>';
             }

			 if($fk_poliza != 0){
				  $i++;
			 }
		}

	}
	fclose ( $fp );
  unlink($archivotmp);

	

}
if( $i != 0  && is_int($fk_poliza)) {
  $poliza = new Poliza();
  $poliza = $poliza->getPolizaId($fk_poliza);
  print 'Se cargaron todos los registros exitosamente. Póliza: '.$poliza[0]['cons'];
}
else {
	print 'Problemas al abrir el archivo: '.$error;
}