<?php
// RELACION DE SALDOS DE PROVEEDORES
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

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
$tpl->assignInclude("body","./plantillas/ban/ban_sal_pro.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------

if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha",date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

// Construir script
$sql = "SELECT num_cia,num_proveedor,nombre,fecha,num_fact,total,fecha FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE fecha <= '$_GET[fecha_corte]'";
if ($_GET['tipo'] == "cia")
	$sql .= " AND num_cia = $_GET[num_cia]";
$sql .= " ORDER BY num_cia,";
if ($_GET['orden'] == "num")
	$sql .= "num_proveedor,";
else if ($_GET['orden'] == "alf")
	$sql .= "nombre,";
$sql .= "fecha";

$result = ejecutar_script($sql,$dsn);

if ($result) {
	$num_cia = NULL;
	$num_proveedor = NULL;
	$gran_total = 0;
	
	$tpl->newBlock("listado");
	
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia'] || $num_proveedor != $result[$i]['num_proveedor']) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL)
					$tpl->assign("cia.gran_total",number_format($gran_total,2,".",","));
				
				$num_cia = $result[$i]['num_cia'];
				
				$tpl->newBlock("cia");
				$tpl->assign("num_cia",$num_cia);
				$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
				$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
				
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_GET['fecha_corte'],$fecha);
				
				$tpl->assign("dia",$fecha[1]);
				$tpl->assign("anio",$fecha[3]);
				switch ($fecha[2]) {
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
			}
			
			if ($num_proveedor != NULL) {
				$tpl->assign("title.rowspan",$rowspan);
				$tpl->assign("proveedor.total",number_format($total,2,".",","));
			}
			
			$total = 0;
			$rowspan = 0;
			
			$num_proveedor = $result[$i]['num_proveedor'];
			
			$tpl->newBlock("proveedor");
			$tpl->newBlock("fila");
			$tpl->assign("fecha_mov",$result[$i]['fecha_mov']);
			$tpl->assign("num_fact",$result[$i]['num_fact']);
			$tpl->assign("importe",number_format($result[$i]['total'],2,".",","));
			$tpl->assign("fecha_pago",$result[$i]['fecha']);
			
			$tpl->newBlock("title");
			$tpl->assign("num_pro",$result[$i]['num_proveedor']);
			$tpl->assign("nombre_pro",$result[$i]['nombre']);
			
			$total += $result[$i]['total'];
			$gran_total += $result[$i]['total'];
			$rowspan++;
		}
		else {
			$tpl->newBlock("fila");
			$tpl->assign("fecha",$result[$i]['fecha']);
			$tpl->assign("num_fact",$result[$i]['num_fact']);
			$tpl->assign("importe",number_format($result[$i]['total'],2,".",","));
			$tpl->assign("fecha",$result[$i]['fecha']);
			
			$total += $result[$i]['total'];
			$gran_total += $result[$i]['total'];
			$rowspan++;
		}
	}
	if ($num_proveedor != NULL) {
		$tpl->assign("title.rowspan",$rowspan);
		$tpl->assign("proveedor.total",number_format($total,2,".",","));
	}
	if ($num_cia != NULL)
		$tpl->assign("cia.gran_total",number_format($gran_total,2,".",","));
	
	$tpl->printToScreen();
}
else {
	header("location: ./ban_sal_pro.php?codigo_error=1");
	die;
}
?>