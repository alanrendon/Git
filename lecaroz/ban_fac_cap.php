<?php
// CAPTURA DE FACTURAS DE CLIENTES
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ---------------------------------------------------------------<strong>-</strong>
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por imprimir";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
if (isset($_GET['num_cia'])) {
	$num_cia = $_GET['num_cia'];
	$idcliente = $_GET['idcliente'];
	$fecha = $_GET['fecha'];
	$folio = $_GET['folio'];
	
	$numfilas = 4;
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
	
	// Seleccionar plantilla
	$plantilla = ($_GET['tamano'] == "carta")?"factura_carta.tpl":"factura_oficio.tpl";
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ban/$plantilla" );
	$tpl->prepare();
	
	// Obtener datos de la compañía
	$sql = "SELECT aplica_iva FROM catalogo_companias WHERE num_cia = $num_cia";
	$temp = ejecutar_script($sql,$dsn);
	if (!$temp) {
		$tpl->newBlock("cerrar");
		$tpl->assign("mensaje","No existe la compañía");
		$tpl->assign("otros","window.opener.document.form.num_cia.select();");
		$tpl->printToScreen();
		die;
	}
	
	$aplica_iva = ($temp && $temp[0]['aplica_iva'] == "t")?0.15:0;
	
	// Obtener datos del cliente
	$sql = "SELECT * FROM catalogo_clientes WHERE id = $idcliente";
	$cliente = ejecutar_script($sql,$dsn);
	if (!$cliente) {
		$tpl->newBlock("cerrar");
		$tpl->assign("mensaje","No existe el cliente");
		$tpl->assign("otros","window.opener.document.form.idcliente.select();");
		$tpl->printToScreen();
		die;
	}
	
	$tpl->newBlock("factura");
	
	$tpl->assign("dia1",$fecha[1]);
	$tpl->assign("mes",mes_escrito($fecha[2]));
	$tpl->assign("anio",$fecha[3]);
	
	$tpl->assign("nombre",strtoupper($cliente[0]['nombre']));
	$tpl->assign("direccion",strtoupper($cliente[0]['direccion']));
	$tpl->assign("rfc",strtoupper($cliente[0]['rfc']));
	
	$subtotal = 0;
	for ($i=0; $i<$numfilas; $i++) {
		if ($_GET['cantidad'.$i] > 0 && /*$_GET['descripcion'.$i] != "" &&*/ $_GET['importe'.$i] > 0) {
			// Desglozar por porcentajes
			$tpl->assign("cantidad".($i+1),number_format($_GET['cantidad'.$i]));
			$tpl->assign("descripcion".($i+1),strtoupper($_GET['descripcion'.$i]));
			$tpl->assign("pu".($i+1),($_GET['precio_unidad'.$i] != "")?number_format($_GET['precio_unidad'.$i],2,".",","):"&nbsp;");
			$tpl->assign("importe".($i+1),($_GET['precio_unidad'.$i] > 0)?number_format($_GET['cantidad'.$i]*$_GET['precio_unidad'.$i],2,".",","):number_format($_GET['importe'.$i],2,".",","));
			$subtotal += ($_GET['precio_unidad'.$i] > 0)?round($_GET['cantidad'.$i]*$_GET['precio_unidad'.$i],2):$_GET['importe'.$i];
			
			// Guardar registro
			$sql = "INSERT INTO facturas_clientes (num_cia,idcliente,importe_total,impuestos,descripcion,fecha,cantidad,precio_unidad,subtotal,folio) VALUES ($num_cia,$idcliente,".($_GET['importe'.$i]*(1+$aplica_iva)).",".($aplica_iva > 0?$_GET['importe'.$i]*$aplica_iva:0).",'".$_GET['descripcion'.$i]."','$fecha[0]',".$_GET['cantidad'.$i].",".($_GET['precio_unidad'.$i] > 0?$_GET['precio_unidad'.$i]:"NULL").",".$_GET['importe'.$i].",$folio)";
			ejecutar_script($sql,$dsn);
		}
	}
	$iva = ($temp && $temp[0]['aplica_iva'] == "t")?$subtotal*0.15:0;
	$total = $subtotal + $iva;
	
	$tpl->assign("total_escrito",num2string($total));
	$tpl->assign("subtotal",number_format($subtotal,2,".",","));
	$tpl->assign("iva",/*($iva != 0)?*/number_format($iva,2,".",",")/*:"&nbsp;"*/);
	$tpl->assign("total",number_format($total,2,".",","));
	
	$js = "function actualiza() {
		window.opener.document.location = './ban_fac_cap.php';
		var cadena = 'Instrucciones:\\nColoque las facturas en la en la impresora y presione CTRL+P. Ir a Propiedades de la Impresora\\ny configurar el tamaño de la hoja a carta u oficio según sea el caso.';
		alert(cadena);
	}
	
	window.onload = actualiza();";
	$tpl->assign("otros",$js);
	
	$tpl->printToScreen();
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fac_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock("captura");
$tpl->assign("fecha",date("d/m/Y"));

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

$sql = "SELECT id,nombre FROM catalogo_clientes ORDER BY id";
$cliente = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($cliente); $i++) {
	$tpl->newBlock("cliente");
	$tpl->assign("idcliente",$cliente[$i]['id']);
	$tpl->assign("cliente",$cliente[$i]['nombre']);
}

$numfilas = 4;
for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",($i > 0)?$i-1:$numfilas-1);
	$tpl->assign("next",($i < $numfilas-1)?$i+1:0);
}

$tpl->printToScreen();
?>