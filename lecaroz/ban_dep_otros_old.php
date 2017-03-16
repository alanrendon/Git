<?php
// CAPTURA DE OTROS DEPOSITOS
// Tabla 'estado_cuenta'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_otros.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------

if (isset($_POST['numfilas'])) {
	// Almacenar registros temporalmente
	$_SESSION['car']['mes'] = $_POST['mes'];
	$_SESSION['car']['anio'] = $_POST['anio'];
	$_SESSION['car']['numfilas'] = $_POST['numfilas'];
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$_SESSION['car']['num_cia'.$i] = $_POST['num_cia'.$i];
		$_SESSION['car']['nombre_cia'.$i] = $_POST['nombre'.$i];
		$_SESSION['car']['dia'.$i] = $_POST['dia'.$i];
		$_SESSION['car']['importe'.$i] = $_POST['importe'.$i];
	}
	
	$count = 0;
	$fecha_cap = date("d/m/Y");
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['num_cia'.$i] > 0 && $_POST['dia'.$i] > 0 && $_POST['importe'.$i] != "") {
			$otros['num_cia'.$count] = $_POST['num_cia'.$i];
			$otros['fecha'.$count] = $_POST['dia'.$i]."/".$_POST['mes']."/".$_POST['anio'];
			$otros['importe'.$count] = $_POST['importe'.$i];
			$otros['fecha_cap'.$count] = $fecha_cap;
			$otros['acumulado'.$count]="true";
			$count++;
		}
	}
//SE INSERTO EL 
	$diasxmes[1]=31;
	$diasxmes[2]=29;
	$diasxmes[3]= 31;
	$diasxmes[4]= 30;
	$diasxmes[5]= 31;
	$diasxmes[6]= 30;
	$diasxmes[7]= 31;
	$diasxmes[8]= 31;
	$diasxmes[9]= 30;
	$diasxmes[10]= 31;
	$diasxmes[11]= 30;
	$diasxmes[12]= 31;
	
	if($_POST['acumulado']==0)
	{
		$sql="UPDATE otros_depositos SET acumulado = 'FALSE' WHERE fecha BETWEEN '1/".$_POST['mes']."/".$_POST['anio']."' AND '".$diasxmes[$_POST['mes']]."/".$_POST['mes']."/".$_POST['anio']."'";
		ejecutar_script($sql,$dsn);
	}
	/*if($_POST['todos']==1)
	{
		$sql="UPDATE otros_depositos SET acumulado = 'TRUE' WHERE fecha BETWEEN '1/$_POST[mes]/$_POST[anio]' AND '".$diasxmes[$_POST['mes']]."/$_POST[mes]/$_POST[anio]'";
		ejecutar_script($sql,$dsn);
	}*/
	
	$db = new DBclass($dsn,"otros_depositos",$otros);
	$db->xinsertar();
//----------------------------------------------------------------GENERA LISTADO DESPUES DE LA INSERCION	

//if(isset($count))
	if ($count > 0) {
		$tpl->newBlock("listado");
		
		$tpl->assign("dia",date("d"));
		$tpl->assign("anio",date("Y"));
		switch (date("m")) {
			case 1: $tpl->assign("mes","Enero"); break;
			case 2: $tpl->assign("mes","Febrero"); break;
			case 3: $tpl->assign("mes","Marzo"); break;
			case 4: $tpl->assign("mes","Abril"); break;
			case 5: $tpl->assign("mes","Mayo"); break;
			case 6: $tpl->assign("mes","Junio"); break;
			case 7: $tpl->assign("mes","Julio"); break;
			case 8: $tpl->assign("mes","Agosto"); break;
			case 9: $tpl->assign("mes","Septiembre"); break;
			case 10: $tpl->assign("mes","Octubre"); break;
			case 11: $tpl->assign("mes","Noviembre"); break;
			case 12: $tpl->assign("mes","Diciembre"); break;
		}
		
		// Generar listado de depositos capturados
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE (num_cia < 100 OR num_cia > 200) ORDER BY num_cia";
		$cia = ejecutar_script($sql,$dsn);
		
		if ($cia) {
			$gran_total = 0;
			for ($i=0; $i<count($cia); $i++) {
				// Consultar si la panaderia tiene rosticerias
				$dep = ejecutar_script("SELECT * FROM dependencia_cia WHERE cia_primaria = ".$cia[$i]['num_cia']." ORDER BY cia_secundaria",$dsn);
				
				// Consultar depositos de la compañía primaria
				$sql = "SELECT * FROM otros_depositos WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha_cap = '$fecha_cap' and acumulado='t'"; //AQUI SE INSERTO LA CONDICION DE ACUMULADO
				$pan = ejecutar_script($sql,$dsn);
				
				// Consultar depositos de la compañia secundaria
				if ($dep) {
					$sql = "SELECT * FROM otros_depositos WHERE num_cia IN (";
					for ($j=0; $j<count($dep); $j++) {
						$sql .= $dep[$j]['cia_secundaria'];
						if ($j < count($dep)-1) $sql .= ",";
					}
					$sql .= ") AND fecha_cap = '$fecha_cap' AND acumulado='t' ORDER BY num_cia";//AQUI SE INSERTO LA CONDICION DE ACUMULADO
					$ros = ejecutar_script($sql,$dsn);
				}
				else
					$ros = FALSE;
				
				if ($pan || $ros) {
					$tpl->newBlock("grupo");
					$rows = 0;
					$total = 0;
					if ($pan) {
						for ($c=0; $c<count($pan); $c++) {
							$tpl->newBlock("fila_lis");
							$tpl->assign("num_cia",$cia[$i]['num_cia']);
							$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
							$tpl->assign("fecha",$pan[$c]['fecha']);
							$tpl->assign("deposito",number_format($pan[$c]['importe'],2,".",","));
							$total += $pan[$c]['importe'];
							$gran_total += $pan[$c]['importe'];
							$rows++;
						}
					}
					if ($ros) {
						for ($r=0; $r<count($ros); $r++) {
							$tpl->newBlock("fila_lis");
							$temp = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$ros[$r]['num_cia'],$dsn);
							$tpl->assign("num_cia",$ros[$r]['num_cia']);
							$tpl->assign("nombre_cia",$temp[0]['nombre_corto']);
							$tpl->assign("fecha",$ros[$r]['fecha']);
							$tpl->assign("deposito",number_format($ros[$r]['importe'],2,".",","));
							$total += $ros[$r]['importe'];
							$gran_total += $ros[$r]['importe'];
							$rows++;
						}
					}
					if ($rows > 1) {
						$tpl->newBlock("total");
						$tpl->assign("total",number_format($total,2,".",","));
					}
				}
			}
		}
		$tpl->assign("listado.gran_total",number_format($gran_total,2,".",","));
		$total_mes = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE fecha >= '1/$_POST[mes]/$_POST[anio]' AND fecha <= '".date("d/m/Y",mktime(0,0,0,$_POST['mes']+1,0,$_POST['anio']))."'",$dsn);
		$tpl->assign("listado.total_mes",number_format($total_mes[0]['sum'],2,'.',','));
		
		
		$tpl->printToScreen();
		die;
		unset($_SESSION['car']);
	}
	unset($_SESSION['car']);
}

if (!isset($_GET['numfilas'])) {
	if (isset($_SESSION['car']))
		unset($_SESSION['car']);
	
	$tpl->newBlock("datos");
	$tpl->assign("anio",date("Y"));
	$tpl->assign(date("m"),"selected");
	
	$tpl->printToScreen();
	die;
}
//----------------------------------------------------------------------------CAPTURA DE LOS DEPOSITOS
$tpl->newBlock("captura");
$tpl->assign("tabla","estado_cuenta");
$tpl->assign("mes",$_GET['mes']);
$tpl->assign("anio",$_GET['anio']);

$tpl->assign("numfilas",$_GET['numfilas']);
/*
if($_GET['listado']==1)
	$tpl->assign("todos",1);
else if($_GET['todos']==0)
	$tpl->assign("todos",0);
*/

if($_GET['tipo_con']==1)
	$tpl->assign("acumulado",1);
else if($_GET['tipo_con']==0)
	$tpl->assign("acumulado",0);

switch ($_GET['mes']) {
	case 1: $tpl->assign("nombre_mes","ENERO"); break;
	case 2: $tpl->assign("nombre_mes","FEBRERO"); break;
	case 3: $tpl->assign("nombre_mes","MARZO"); break;
	case 4: $tpl->assign("nombre_mes","ABRIL"); break;
	case 5: $tpl->assign("nombre_mes","MAYO"); break;
	case 6: $tpl->assign("nombre_mes","JUNIO"); break;
	case 7: $tpl->assign("nombre_mes","JULIO"); break;
	case 8: $tpl->assign("nombre_mes","AGOSTO"); break;
	case 9: $tpl->assign("nombre_mes","SEPTIEMBRE"); break;
	case 10: $tpl->assign("nombre_mes","OCTUBRE"); break;
	case 11: $tpl->assign("nombre_mes","NOVIEMBRE"); break;
	case 12: $tpl->assign("nombre_mes","DICIEMBRE"); break;
}
// Número máximo de días
switch ($_GET['mes']) {
	case 1: $maxdias = 31; break;
	case 2: $maxdias = 29; break;
	case 3: $maxdias = 31; break;
	case 4: $maxdias = 30; break;
	case 5: $maxdias = 31; break;
	case 6: $maxdias = 30; break;
	case 7: $maxdias = 31; break;
	case 8: $maxdias = 31; break;
	case 9: $maxdias = 30; break;
	case 10: $maxdias = 31; break;
	case 11: $maxdias = 30; break;
	case 12: $maxdias = 31; break;
}
$tpl->assign("maxdias",$maxdias);

$mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) cod_mov,descripcion FROM catalogo_mov_bancos WHERE tipo_mov='TRUE' ORDER BY cod_mov",$dsn);

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

for ($i=0; $i<$_GET['numfilas']; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$_GET['numfilas']-1);
	
	if ($i < $_GET['numfilas']-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
		
	$tpl->assign("num_cia",isset($_SESSION['car'])?$_SESSION['car']['num_cia'.$i]:"");
	$tpl->assign("nombre_cia",isset($_SESSION['car'])?$_SESSION['car']['nombre_cia'.$i]:"");
	$tpl->assign("dia",isset($_SESSION['car'])?$_SESSION['car']['concepto'.$i]:"");
	$tpl->assign("importe",isset($_SESSION['car'])?$_SESSION['car']['importe'.$i]:"");
}

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