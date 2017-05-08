<?php
// ACTUALIZACION DE INVENTARIOS
// Tablas 'folios_cheque'
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
$descripcion_error[1] = "Ya esta actualizado el inventario";
$descripcion_error[2] = "";

// ---------------------------------- Insertar datos en tablas -----------------------------------------------
if (isset($_GET['accion'])) {
	/*if (existe_registro("diferencias_inventario",array("fecha"),array(date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))),$dsn)) {
		header("location: ./bal_act_inv.php?codigo_error=1");
		die;
	}*/
	
	if ($_POST['rango'] == "pan")
		$rango = "num_cia < 100";
	else if ($_POST['rango'] == "ros")
		$rango = "(num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 799)";
		//$rango = "num_cia = 702";
	//$rango = "(num_cia < 200 OR num_cia BETWEEN 702 AND 799)";
	
	// Generar diferencias en contra
	$sql = "INSERT INTO diferencias_inventario (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov,precio_unidad,descripcion) SELECT num_cia,codmp,'".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."' AS fecha,NULL AS cod_turno,'TRUE' AS tipo_mov,ABS(diferencia) AS cantidad,0 AS existencia,precio_unidad AS precio,ABS(precio_unidad*diferencia) AS total_mov,precio_unidad,'DIFERENCIA INVENTARIO' AS descripcion FROM inventario_fin_mes WHERE $rango AND diferencia>0 AND fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	// Generar diferencias a favor
	$sql = "INSERT INTO diferencias_inventario (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov,precio_unidad,descripcion) SELECT num_cia,codmp,'".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."' AS fecha,NULL AS cod_turno,'FALSE' AS tipo_mov,ABS(diferencia) AS cantidad,0 AS existencia,precio_unidad AS precio,ABS(precio_unidad*diferencia) AS total_mov,precio_unidad,'DIFERENCIA INVENTARIO' AS descripcion FROM inventario_fin_mes WHERE $rango AND diferencia<0 AND fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	// Generar movimientos
	$sql = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov,precio_unidad,descripcion) SELECT num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov,precio_unidad,descripcion FROM diferencias_inventario WHERE $rango AND fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	// Generar gastos
	$sql = "INSERT INTO movimiento_gastos (codgastos,num_cia,fecha,importe,concepto) SELECT 90 AS codgastos,num_cia,fecha,total_mov as importe,descripcion AS concepto FROM mov_inv_real WHERE $rango AND codmp=90 and tipo_mov='TRUE' AND fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	
	// Actualizar inventarios (sumar cantidades a favor)
	$sql = "UPDATE inventario_real SET existencia=existencia+diferencias_inventario.cantidad WHERE $rango AND num_cia=diferencias_inventario.num_cia AND codmp=diferencias_inventario.codmp AND diferencias_inventario.tipo_mov='FALSE' AND diferencias_inventario.fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	// Actualizar inventarios (restar cantidades en contra)
	$sql = "UPDATE inventario_real SET existencia=existencia-diferencias_inventario.cantidad WHERE $rango AND num_cia=diferencias_inventario.num_cia AND codmp=diferencias_inventario.codmp AND diferencias_inventario.tipo_mov='TRUE' AND diferencias_inventario.fecha='".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."'";
	ejecutar_script($sql,$dsn);
	// Copiar inventario real al virtual
	$sql = "DELETE FROM inventario_virtual WHERE $rango;
	INSERT INTO inventario_virtual (num_cia,codmp,existencia,precio_unidad) SELECT num_cia,codmp,existencia,precio_unidad FROM inventario_real WHERE $rango";
	ejecutar_script($sql, $dsn);
	// Actualizar historico de inventario
	$sql = "INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) SELECT num_cia,codmp,'".date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")))."' AS fecha,existencia,precio_unidad FROM inventario_real WHERE $rango";
	ejecutar_script($sql,$dsn);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_inv.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die;
}
else if (isset($_GET['tipo'])) {
	if (date("d") > 10) {
		header("location: ./bal_act_inv.php?codigo_error=2");
		die;
	}
	
	// Actualizar existencia en inventario_fin_mes
	if ($_GET['rango'] == "pan") {
		$sql = "UPDATE inventario_fin_mes SET existencia=inventario_real.existencia,diferencia=inventario_real.existencia-inventario,precio_unidad=inventario_real.precio_unidad WHERE";
		if ($_GET['num_cia'] > 0)
			$sql .= " num_cia = $_GET[num_cia]";
		else
			$sql .= " num_cia < 100";
		$sql .= " AND fecha >= '".date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")))."' AND num_cia=inventario_real.num_cia AND codmp=inventario_real.codmp";
		ejecutar_script($sql,$dsn);
	}
	
	$sql =  "SELECT id,num_cia,codmp,nombre,existencia,inventario,diferencia,precio_unidad FROM inventario_fin_mes JOIN catalogo_mat_primas USING(codmp) ";
	$sql .= "WHERE ";
	if ($_GET['num_cia'] > 0)
		$sql .= " num_cia = $_GET[num_cia]";
	else if ($_GET['rango'] == "pan")
		$sql .= " num_cia < 100";
	else if ($_GET['rango'] == "ros")
		$sql .= " (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 799)";
		//$sql .= " num_cia = 702";
	$sql .= " AND fecha >= '".date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")))."'";
	if ($_GET['tipo'] == "controlada")
		$sql .= " AND controlada = 'TRUE'";
	else if ($_GET['tipo'] == "no_controlada")
		$sql .= " AND controlada = 'FALSE'";
	$sql .= " ORDER BY num_cia,controlada,codmp ASC";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./bal_act_inv.php?codigo_error=3");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("rango",($_GET['rango'] == "pan")?"pan":"ros");
	switch (date("n")-1) {
		case 0: $mes = "DICIEMBRE"; break;
		case 1: $mes = "ENERO"; break;
		case 2: $mes = "FEBRERO"; break;
		case 3: $mes = "MARZO"; break;
		case 4: $mes = "ABRIL"; break;
		case 5: $mes = "MAYO"; break;
		case 6: $mes = "JUNIO"; break;
		case 7: $mes = "JULIO"; break;
		case 8: $mes = "AGOSTO"; break;
		case 9: $mes = "SEPTIEMBRE"; break;
		case 10: $mes = "OCTUBRE"; break;
		case 11: $mes = "NOVIEMBRE"; break;
		case 12: $mes = "DICIEMBRE"; break;
	}
	$tpl->assign("mes",$mes);
	
	$cia = NULL;
	$total = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($cia == NULL || $cia != $result[$i]['num_cia']) {
			if ($total != 0) {
				$tpl->assign("cia.favor",number_format($favor,2,".",","));
				$tpl->assign("cia.contra",number_format($contra,2,".",","));
				$tpl->assign("cia.color_total",$total > 0 ? "0000FF" : "FF0000");
				$tpl->assign("cia.total",number_format($total,2,".",","));
			}
			
			$cia = $result[$i]['num_cia'];
			$nombre = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$cia",$dsn);
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$cia);
			$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
			
			$favor = 0;
			$contra = 0;
			$total = 0;
		}
		if ($result[$i]['inventario']-$result[$i]['existencia'] != 0) {
			$tpl->newBlock("fila");
			
			$tpl->assign("tipo",$_GET['tipo']);
			
			$tpl->assign("num_cia",$result[$i]['num_cia']);
			$tpl->assign("mes",date("n")-1);
			$tpl->assign("anio",date("Y",mktime(0,0,0,date("n")-1,1,date("Y"))));
			$tpl->assign("id",$result[$i]['id']);
			$tpl->assign("codmp",$result[$i]['codmp']);
			$tpl->assign("mp",$result[$i]['nombre']);
			$tpl->assign("existencia",number_format($result[$i]['existencia'],2,".",","));
			$tpl->assign("inventario",number_format($result[$i]['inventario'],2,".",","));
			$tpl->assign("falta",($result[$i]['inventario']-$result[$i]['existencia'] < 0)?"<font color='#FF0000'>".number_format(abs($result[$i]['inventario']-$result[$i]['existencia']))."</font>":"&nbsp;");
			$tpl->assign("sobra",($result[$i]['inventario']-$result[$i]['existencia'] > 0)?"<font color='#0000FF'>".number_format(abs($result[$i]['inventario']-$result[$i]['existencia']))."</font>":"&nbsp;");
			$tpl->assign("costo_unitario",number_format($result[$i]['precio_unidad'],2,".",","));
			$tpl->assign("costo_total",($result[$i]['inventario']-$result[$i]['existencia'] < 0)?"<font color='#FF0000'>".number_format($result[$i]['precio_unidad']*($result[$i]['inventario']-$result[$i]['existencia']),2,".",",")."</font>":"<font color='#0000FF'>".number_format($result[$i]['precio_unidad']*($result[$i]['inventario']-$result[$i]['existencia']),2,".",",")."</font>");
			
			$temp = $result[$i]['precio_unidad']*($result[$i]['inventario']-$result[$i]['existencia']);
			
			$favor += $temp > 0 ? $temp : 0;
			$contra += $temp < 0 ? $temp : 0;
			$total += $result[$i]['precio_unidad']*($result[$i]['inventario']-$result[$i]['existencia']);
		}
	}
	if ($total != 0) {
		$tpl->assign("cia.favor",number_format($favor,2,".",","));
		$tpl->assign("cia.contra",number_format($contra,2,".",","));
		$tpl->assign("cia.color_total",$total > 0 ? "0000FF" : "FF0000");
		$tpl->assign("cia.total",number_format($total,2,".",","));
	}
	$tpl->printToScreen();
	die;
}
else {
	header("location: ./bal_act_inv.php?codigo_error=1");
	die;
}
?>