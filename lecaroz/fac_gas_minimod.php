<?php
// CONSULTA DE GASTOS
// Tabla 'Rosticerías'
// Menu 'Rosticerías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
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

// --------------------------------- Generar pantalla --------------------------------------------------------

$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/fac/fac_gas_minimod.tpl");
$tpl->prepare();


if (isset($_GET['parser'])) {
	if($_GET['idmovimiento_gastos'] < 0){
		$sql="UPDATE pasivo_proveedores set codgastos=".$_GET['codgastos']." where id=".$_GET['idpasivo'];
		$sql1="UPDATE facturas set codgastos=".$_GET['codgastos']." where id=".$_GET['idfactura'];
		ejecutar_script($sql,$dsn);
		ejecutar_script($sql1,$dsn);
	}
	
	else{
	//MODIFICA LOS MOVIMIENTOS A LOS GASTOS
		$sql ="UPDATE movimiento_gastos SET codgastos=".$_GET['codgastos']." WHERE idmovimiento_gastos=".$_GET['idmovimiento_gastos'];
//echo "$sql <br>";
		ejecutar_script($sql,$dsn);
	//MODIFICA LAS FACTURAS PAGADAS
		$sql="UPDATE facturas_pagadas set codgastos=".$_GET['codgastos']." where id=".$_GET['idpagada'];
//echo "$sql <br>";		
		ejecutar_script($sql,$dsn);

		$sql="UPDATE facturas set codgastos=".$_GET['codgastos']." where id=".$_GET['idfactura'];
//echo "$sql <br>";		
		ejecutar_script($sql,$dsn);
		
		$sql="UPDATE cheques set codgastos=".$_GET['codgastos']." where id=".$_GET['idcheque'];
//echo "$sql <br>";		
		ejecutar_script($sql,$dsn);
	}
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Incluir el cuerpo del documento
$tpl->newBlock("modificar");
$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("num_proveedor",$_GET['num_proveedor']);
$tpl->assign("num_fact",$_GET['num_fact']);
$tpl->assign("num_cheque",$_GET['cheque']);

//SI LE MANDO EL ID MAYOR A CERO QUIERE DECIR QUE LA FACURA YA ESTA PAGADA Y PUEDO BUSCAR EL GASTO DIRECTAMENTE EN LA TABLA
//DE MOVIMIENTO DE GASTOS, TAMBIEN SE BUSCARÁN TODAS LAS AFECTACIONES POR EL PAGO DE LA FACTURA:
//MOVIMIENTO GASTOS, CHEQUES, FACTURAS PAGADAS Y FACTURAS
if($_GET['id'] > 0){
	$tpl->assign("parser",1);
//BUSCA EL GASTO EN MOVIMIENTO DE GASTOS
	$datos = ejecutar_script("SELECT * FROM movimiento_gastos WHERE idmovimiento_gastos = $_GET[id]",$dsn);
	$tpl->assign("codgastos",$datos[0]['codgastos']);
	$tpl->assign("fecha",$datos[0]['fecha']);
	$tpl->assign("concepto",$datos[0]['concepto']);
	$tpl->assign("importe",number_format($datos[0]['importe'],2,".",","));
	$tpl->assign("importe1",number_format($datos[0]['importe'],2,".",""));
//BUSCA LA FACTURA PAGADA	
	$facturas_pagadas=ejecutar_script("SELECT * FROM facturas_pagadas WHERE num_cia=$_GET[num_cia] and num_proveedor=$_GET[num_proveedor] and num_fact=$_GET[num_fact]",$dsn);
	$tpl->assign("idpagada",$facturas_pagadas[0]['id']);
//BUSCA EL CHEQUE
	$cheque=ejecutar_script("SELECT * FROM cheques WHERE num_cia=$_GET[num_cia] and num_proveedor=$_GET[num_proveedor] and folio=$_GET[cheque]",$dsn);
	$tpl->assign("idcheque",$cheque[0]['id']);
//FACTURAS
	$factura = ejecutar_script("SELECT * FROM facturas WHERE num_cia=$_GET[num_cia] and num_proveedor=$_GET[num_proveedor] and num_fact=$_GET[num_fact]",$dsn);
	$tpl->assign("idfactura",$factura[0]['id']);
}

//EN ESTE CASO LE MANDO UN ID NEGATIVO CON LO QUE ME INFORMA QUE LA FACTURA NO SE HA PAGADO Y NADA MAS SE TENDRIAN QUE AFECTAR
//LAS SIGUIENTES TABLAS:
//FACTURAS Y PASIVO PROVEEDORES
else{
	$tpl->assign("parser",0);
	$factura = ejecutar_script("SELECT * FROM facturas WHERE num_cia=$_GET[num_cia] and num_proveedor=$_GET[num_proveedor] and num_fact=$_GET[num_fact]",$dsn);
	$tpl->assign("codgastos",$factura[0]['codgastos']);
	$tpl->assign("fecha",$factura[0]['fecha_mov']);
	$tpl->assign("concepto",$factura[0]['concepto']);
	$tpl->assign("idfactura",$factura[0]['id']);
	$tpl->assign("importe",number_format($factura[0]['importe_total'],2,".",","));
	$tpl->assign("importe1",number_format($factura[0]['importe_total'],2,".",""));

	$pasivo = ejecutar_script("SELECT * FROM pasivo_proveedores WHERE num_cia=$_GET[num_cia] and num_proveedor=$_GET[num_proveedor] and num_fact=$_GET[num_fact]",$dsn);
	$tpl->assign("idpasivo",$pasivo[0]['id']);
}

$tpl->printToScreen();
?>