<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);


$sql="UPDATE catalogo_proveedores set 
nombre='".$_POST['nombre']."', 
direccion='".$_POST['direccion']."', 
rfc='".$_POST['rfc']."', 
telefono1='".$_POST['telefono1']."', 
telefono2='".$_POST['telefono2']."', 
fax='".$_POST['fax']."', 
email='".$_POST['email']."', 
restacompras='".$_POST['restacompras']."', 
idtipoproveedor=".$_POST['idtipoproveedor'].", 
tiempoentrega=".$_POST['tiempoentrega'].", 
tipopersona='".$_POST['tipopersona']."', 
idbancos=".$_POST['idbancos'].", 
clabe_banco='".$_POST['clabe_banco']."', 
clabe_plaza='".$_POST['clabe_plaza']."', 
clabe_cuenta='".$_POST['clabe_cuenta']."', 
clabe_identificador='".$_POST['clabe_identificador']."', 
pago_via_interbancaria='".$_POST['pago_via_interbancaria']."', 
prioridad='".$_POST['prioridad']."', 
diascredito=".$_POST['diascredito'].",
para_abono='".$_POST['abono_cuenta']."' 
where num_proveedor=".$_POST['num_proveedor'];
/*
print_r($_POST);
echo "<br>";
echo $sql;
*/
ejecutar_script($sql,$dsn);
header("location: ./fac_prov_mod.php");

?>