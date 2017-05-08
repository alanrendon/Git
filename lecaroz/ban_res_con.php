<?php
// RESULTADOS DE CONCILIACION
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_res_con.tpl");
$tpl->prepare();

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['tabla'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		// Poner fecha de conciliacion
		ejecutar_script("UPDATE estado_cuenta SET fecha_con='".$_POST['fecha_con'.$i]."' WHERE id=".$_POST['id'.$i],$dsn);
		// Almacenar en variables de sesión las id's para posteriormente imprimir un listado con los movimientos conciliados
		$_SESSION['con']['id_con'.$_SESSION['con']['id_con_count']++] = $_POST['id'.$i];
	}
	
	//if (existe_registro("saldos",array("num_cia"),array($_SESSION['con']['num_cia'.$_SESSION['con']['next']]),$dsn))
		ejecutar_script("UPDATE saldos SET saldo_bancos=$_POST[saldo_final] WHERE cuenta = 1 AND num_cia=".$_SESSION['con']['num_cia'.$_SESSION['con']['next']],$dsn);
	//else
		//ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos) VALUES (".$_SESSION['con']['num_cia'.$_SESSION['con']['next']].",$_POST[saldo_final],$_POST[saldo_final])",$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// -------------------------------- Generar pantalla de resultados -------------------------------------------
$tpl->newBlock("resultados");
$tpl->assign("tabla","estado_cuenta");

$tpl->assign("num_cia",   $_SESSION['con']['num_cia'.$_SESSION['con']['next']]);
$tpl->assign("nombre_cia",$_SESSION['con']['nombre_cia'.$_SESSION['con']['next']]);
$tpl->assign("cuenta",    $_SESSION['con']['cuenta'.$_SESSION['con']['next']]);
$tpl->assign("fecha_con", $_SESSION['con']['fecha_con']);

// Obtener saldo inicial
$result = ejecutar_script("SELECT * FROM saldos WHERE cuenta = 1 AND num_cia=".$_SESSION['con']['num_cia'.$_SESSION['con']['next']],$dsn);
$saldo_ini = $result?$result[0]['saldo_bancos']:0;

$saldo_fin = $saldo_ini;

$tpl->assign("fsaldo_inicial",number_format($saldo_ini,2,".",","));

$count = 0;
$numfilas = 0;

if (isset($_POST['num_che']))
	for ($i=0; $i<$_POST['num_che']; $i++)
		if (isset($_POST['che_con'.$i]))
			$numfilas++;

if (isset($_POST['num_dep']))
	for ($i=0; $i<$_POST['num_dep']; $i++)
		if (isset($_POST['dep_con'.$i]))
			$numfilas++;

// Generar cargos conciliados
if (isset($_POST['num_che'])) {
	$tpl->newBlock("cargos");
	$title1 = false;
	$count_cargos = 0;
	$total_cargos = 0;
	for ($i=0; $i<$_POST['num_che']; $i++) {
		if (isset($_POST['che_con'.$i])) {
			$tpl->newBlock("cargo");
			if (!$title1) {
				$tpl->newBlock("cargo_title");
				$tpl->gotoBlock("cargo");
				$title1 = true;
			}
			$tpl->assign("i",$count);
			
			if ($count < $numfilas-1)
				$tpl->assign("next",$count+1);
			else
				$tpl->assign("next",0);
			
			if ($count > 0)
				$tpl->assign("back",$count-1);
			else
				$tpl->assign("back",$numfilas-1);
			
			$tpl->assign("id",$_POST['che_con'.$i]);
			$tpl->assign("cargo",number_format($_POST['monto'.$i],2,".",","));
			$tpl->assign("fecha",$_SESSION['con']['fecha_con']);
			$count++;
			$count_cargos++;
			
			$total_cargos += $_POST['monto'.$i];
			$saldo_fin -= $_POST['monto'.$i];
		}
	}
	if ($count_cargos > 0)
		$tpl->assign("cargo_title.span",$count_cargos);
	$tpl->assign("cargos.total_cargos",number_format($total_cargos,2,".",","));
}

// Generar abonos conciliados
if (isset($_POST['num_dep'])) {
	$tpl->newBlock("abonos");
	$title2 = false;
	$count_abonos = 0;
	$total_abonos = 0;
	for ($i=0; $i<$_POST['num_dep']; $i++) {
		if (isset($_POST['dep_con'.$i])) {
			$tpl->newBlock("abono");
			if (!$title2) {
				$tpl->newBlock("abono_title");
				$tpl->gotoBlock("abono");
				$title2 = true;
			}
			$tpl->assign("i",$count);
			
			if ($count < $numfilas-1)
				$tpl->assign("next",$count+1);
			else
				$tpl->assign("next",0);
			
			if ($count > 0)
				$tpl->assign("back",$count-1);
			else
				$tpl->assign("back",$numfilas-1);
			
			$tpl->assign("id",$_POST['dep_con'.$i]);
			$tpl->assign("abono",number_format($_POST['importe'.$i],2,".",","));
			$tpl->assign("fecha",$_SESSION['con']['fecha_con']);
			$count++;
			$count_abonos++;
			
			$total_abonos += $_POST['importe'.$i];
			$saldo_fin += $_POST['importe'.$i];
		}
	}
	if ($count_abonos > 0)
		$tpl->assign("abono_title.span",$count_abonos);
	$tpl->assign("abonos.total_abonos",number_format($total_abonos,2,".",","));
}

$tpl->gotoBlock("resultados");
$tpl->assign("numfilas",$count);
$tpl->assign("saldo_final",$saldo_fin);
$tpl->assign("fsaldo_final",number_format($saldo_fin,2,".",","));

// Cerrar ventana si no hubo conciliacion
if ($count == 0)
	$tpl->newBlock("cerrar");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>