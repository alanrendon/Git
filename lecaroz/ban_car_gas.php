<?php
// CARTA DE GASOLINA
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

function dia_semana($dia,$mes,$anio) {
	$date = date("w",mktime(0,0,0,$mes,$dia,$anio));
	switch ($date) {
		case 0: $string = "Domingo"; break;
		case 1: $string = "Lunes"; break;
		case 2: $string = "Martes"; break;
		case 3: $string = "Mi&eacute;rcoles"; break;
		case 4: $string = "Jueves"; break;
		case 5: $string = "Viernes"; break;
		case 6: $string = "S&aacute;bado"; break;
		default : $string = ""; break;
	}
	
	return $string;
}

// --------------------------------- Delaracion de variables -------------------------------------------------
$numfilas = 70;	// Número de filas en la captura

if (isset($_POST['anio'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ban/carta_gasolina.tpl" );
	$tpl->prepare();
	
	$tpl->assign("dia",(int)date("d"));
	$tpl->assign("mes",mes_escrito(date("n")));
	$tpl->assign("anio",date("Y"));
	$tpl->assign("mes_vales",mes_escrito($_POST['mes']));
	$tpl->assign("anio_vales",$_POST['anio']);
	
	$tpl->assign("dia_semana",dia_semana($_POST['dia'],$_POST['mes'],$_POST['anio']));
	$tpl->assign("dia_entrega",$_POST['dia']);
	$tpl->assign("mes_entrega",mes_escrito($_POST['mes']));
	$tpl->assign("anio_entrega",$_POST['anio']);
	
	$numfilas_x_hoja = 100;
	$count = $numfilas_x_hoja;
	for ($i=0; $i<$numfilas; $i++) {
		if ($count >= $numfilas_x_hoja) {
			$tpl->newBlock("listado");
			
			$count = 0;
		}
		if ($_POST['num_cia'.$i] > 0 && $_POST['importe'.$i] > 0) {
			$tpl->newBlock("fila");
			$tpl->assign("cod_gasolina",$_POST['cod_gasolina'.$i]);
			$tpl->assign("nombre_cia",$_POST['nombre_cia'.$i]);
			$tpl->assign("importe_vales",number_format($_POST['importe'.$i],2,".",","));
			$importe_neto = $_POST['importe'.$i] / 1.16 + $_POST['importe'.$i] * 0.032;
			$iva = $importe_neto * 0.16;
			$cheque = $importe_neto + $iva;
			$tpl->assign("importe_neto",number_format($importe_neto,2,".",","));
			$tpl->assign("iva",number_format($iva,2,".",","));
			$tpl->assign("cheque",number_format($cheque,2,".",","));
			$count++;
			
			if ($id = ejecutar_script("SELECT id FROM imp_gasolina WHERE num_cia = ".$_POST['num_cia'.$i],$dsn))
				$sql = "UPDATE imp_gasolina SET importe = ".$_POST['importe'.$i]." WHERE id = ".$id[0]['id'];
			else
				$sql = "INSERT INTO imp_gasolina (num_cia,importe) VALUES (".$_POST['num_cia'.$i].",".$_POST['importe'.$i].")";
			ejecutar_script($sql,$dsn);
		}
	}
	$tpl->printToScreen();
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_gas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("dia",date("j"));
$tpl->assign(date("n"),"selected");
$tpl->assign("anio",date("Y"));

$cia = ejecutar_script("SELECT num_cia,nombre_corto,cod_gasolina,importe FROM catalogo_companias LEFT JOIN imp_gasolina USING(num_cia) ORDER BY num_cia",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	$tpl->assign("cod_gasolina",$cia[$i]['cod_gasolina']);
	$tpl->assign("importe",number_format($cia[$i]['importe'],2,".",""));
}

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",$i > 0 ? $i-1 : $numfilas-1);
	$tpl->assign("next",$i < $numfilas-1 ? $i+1 : 0);
}

$tpl->printToScreen();
?>