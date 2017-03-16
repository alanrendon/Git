<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[1] = "No se encontraron facturas";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn);
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/" . ($_SESSION['iduser'] < 28 ? "cometra_new.tpl" : "cometra_zap.tpl"));
$tpl->prepare();

if($_GET){
	$cias = array();
	foreach ($_GET['cia'] as $c)
		if ($c > 0)
			$cias[] = $c;
	
	$sql = "SELECT num_cia, nombre, cliente_cometra, direccion FROM catalogo_companias WHERE num_cia IN (";
	foreach ($cias as $i => $c)
		$sql .= $c . ($i < count($cias) - 1 ? ', ' : ')');
	$sql .= $_SESSION['iduser'] >= 28 ? " AND num_cia BETWEEN 900 AND 998" : "";
	$sql .= ' ORDER BY num_cia';
	$cias = $db->query($sql);
	
	if(!$cias){
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}

	
	
	$esp = false;
	foreach ($cias as $cia) {
		if(strlen($cia['direccion']) > 52)
			$direccion = substr($cia['direccion'], 0, 52);
		else
			$direccion = $cia['direccion'];
		
		for ($i=0; $i<$_GET['numero']; $i++){
			$tpl->newBlock("ficha");
			$tpl->assign("num_cia", $cia['num_cia']);
			$tpl->assign("nombre_cia", $cia['nombre']);
			$tpl->assign("cuenta", strlen(trim($cia['cliente_cometra'])) > 0 ? trim($cia['cliente_cometra']) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			$tpl->assign("direccion", $direccion);
			
			//if ($cia[0]['num_cia'] >= 900)
				//if (($i + 1) /*% 3*/ == /*0*/3)
					//$tpl->assign('venta', "\n      VENTA\n");
					//$tpl->assign("br", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;VENTA\n<br>");
					//$tpl->assign("br1", "<br><br>");
			
			/*if ($cia[0]['num_cia'] >= 900)
				$tpl->newBlock("zap");
			else
				$tpl->newBlock("pan");*/
		}
	}
	
}

if($_POST){
	for($i=0;$i<$_POST['contador'];$i++){
		if($_POST['importe'.$i] != ""){
			$sql="SELECT num_cia, nombre, cliente_cometra, direccion FROM catalogo_companias WHERE num_cia = {$_POST['num_cia'.$i]}";
			$cia=$db->query($sql);
			if(strlen($cia[0]['direccion']) > 54)
				$direccion=substr($cia[0]['direccion'],0,54);
			else
				$direccion=$cia[0]['direccion'];

			$tpl->newBlock("ficha");
			$tpl->assign("num_cia",$cia[0]['num_cia']);
			$tpl->assign("nombre_cia",$cia[0]['nombre']);
			$tpl->assign("cuenta",/*$cia[0]['clabe_cuenta']*/$cia[0]['cliente_cometra']);
			$tpl->assign("direccion",$direccion);
			$tpl->assign("importe1",number_format($_POST['importe'.$i],2,'.',','));
			$tpl->assign("importe2",number_format($_POST['importe'.$i],2,'.',','));
			$tpl->assign("importe3",number_format($_POST['importe'.$i],2,'.',','));
		}
	}
}




$tpl->printToScreen();

?>
