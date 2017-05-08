<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$tabla = $_GET['tabla'];

switch ($tabla) {
	case "mov_expendios":
		// Insertar datos en la base
		$db = new DBclass($dsn,$tabla,$_POST);
		$db->xinsertar();
		
		// Insertar o actualizar
		if (existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia'],$_POST['fecha']),$dsn))
			$sql = "UPDATE total_panaderias SET abono=abono+$_POST[abono],efectivo=efectivo+$_POST[abono],exp='TRUE' WHERE num_cia = $_POST[num_cia] AND fecha = '$_POST[fecha]'";
		else
			$sql = "INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) VALUES ($_POST[num_cia],'$_POST[fecha]',0,0,0,$_POST[abono],0,0,0,0,$_POST[abono],'FALSE','TRUE','FALSE','FALSE','FALSE')";
		ejecutar_script($sql,$dsn);
		
		header("location: ./pan_exp_cap.php");
	break;
	case "produccion":
		unset($_SESSION['pro']);
		
		// Insertar producción
		$db_control = new DBclass($dsn,$tabla,$_POST);
		$db_control->xinsertar();
		// Insertar totales
		$db_total = new DBclass($dsn,"total_produccion",$_POST);
		$db_total->xinsertar();
		
		// Insertar o actualizar efectivo
		if (existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia0'],$_POST['fecha0']),$dsn))
			$sql = "UPDATE total_panaderias SET raya_pagada=$_POST[raya_pagada],efectivo=efectivo-$_POST[raya_pagada],pro='TRUE' WHERE num_cia = $_POST[num_cia0] AND fecha = '$_POST[fecha0]'";
		else
			$sql = "INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) VALUES ($_POST[num_cia0],'$_POST[fecha0]',0,0,0,0,0,$_POST[raya_pagada],0,0,-$_POST[raya_pagada],'FALSE','FALSE','FALSE','TRUE','FALSE')";
		ejecutar_script($sql,$dsn);
		
		header("location: ./pan_pro_cap.php");
	break;
}
?>