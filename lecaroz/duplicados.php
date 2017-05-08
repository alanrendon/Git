<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

$sql="SELECT num_emp, nombre_completo,num_cia from catalogo_trabajadores order by num_emp";
$empleados=ejecutar_script($sql,$dsn);
$aux=0;
for($i=0;$i<count($empleados);$i++){
	if($empleados[$i]['num_emp']==$aux)
		echo "numero empleado ".$empleados[$i]['num_emp']."<br>";
	$aux=$empleados[$i]['num_emp'];
}





?>