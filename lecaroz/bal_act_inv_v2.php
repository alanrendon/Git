<?php
// ACTUALIZACION DE INVENTARIOS (VER. 2)
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// Conectarse a la base de datos
$db = new DBclass($dsn);

// Actualizar inventarios
if (isset($_GET['terminar'])) {
	$fecha_ini = date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")));
	$fecha = date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")));
	
	// Actualizar nuevamente existencias y diferencias, por si hubo algun cambio
	$sql = "UPDATE \"inventario_fin_mes\" SET \"existencia\" = \"inventario_real\".\"existencia\" WHERE \"fecha\" = '$fecha' AND \"num_cia\" = \"inventario_real\".\"num_cia\" AND \"codmp\" = \"inventario_real\".\"codmp\";\n";
	$sql .= "UPDATE \"inventario_fin_mes\" SET \"diferencia\" = \"existencia\" - \"inventario\" WHERE \"fecha\" = '$fecha';\n";
	
	// Generar diferencias en contra
	$sql .= "INSERT INTO \"diferencias_inventario\" (\"num_cia\",\"codmp\",\"fecha\",\"cod_turno\",\"tipo_mov\",\"cantidad\",\"existencia\",\"precio\",\"total_mov\",\"precio_unidad\",\"descripcion\") SELECT \"num_cia\",\"codmp\",'$fecha' AS \"fecha\",NULL AS \"cod_turno\",'TRUE' AS \"tipo_mov\",ABS(\"diferencia\") AS \"cantidad\",0 AS \"existencia\",\"precio_unidad\" AS \"precio\",ABS(\"precio_unidad\" * \"diferencia\") AS \"total_mov\",\"precio_unidad\",'DIFERENCIA INVENTARIO' AS \"descripcion\" FROM \"inventario_fin_mes\" WHERE \"diferencia\" > 0 AND \"fecha\" = '$fecha';\n";
	// Generar diferencias a favor
	$sql .= "INSERT INTO \"diferencias_inventario\" (\"num_cia\",\"codmp\",\"fecha\",\"cod_turno\",\"tipo_mov\",\"cantidad\",\"existencia\",\"precio\",\"total_mov\",\"precio_unidad\",\"descripcion\") SELECT \"num_cia\",\"codmp\",'$fecha' AS \"fecha\",NULL AS \"cod_turno\",'FALSE' AS \"tipo_mov\",ABS(\"diferencia\") AS \"cantidad\",0 AS \"existencia\",\"precio_unidad\" AS \"precio\",ABS(\"precio_unidad\" * \"diferencia\") AS \"total_mov\",\"precio_unidad\",'DIFERENCIA INVENTARIO' AS \"descripcion\" FROM \"inventario_fin_mes\" WHERE \"diferencia\" < 0 AND \"fecha\" = '$fecha';\n";
	// Generar movimientos
	$sql .= "INSERT INTO \"mov_inv_real\" (\"num_cia\",\"codmp\",\"fecha\",\"cod_turno\",\"tipo_mov\",\"cantidad\",\"existencia\",\"precio\",\"total_mov\",\"precio_unidad\",\"descripcion\") SELECT \"num_cia\",\"codmp\",\"fecha\",\"cod_turno\",\"tipo_mov\",\"cantidad\",\"existencia\",\"precio\",\"total_mov\",\"precio_unidad\",\"descripcion\" FROM \"diferencias_inventario\" WHERE \"fecha\" = '$fecha';\n";
	// Generar gastos
	$sql .= "INSERT INTO \"movimiento_gastos\" (\"codgastos\",\"num_cia\",\"fecha\",\"importe\",\"concepto\") SELECT 90 AS \"codgastos\",\"num_cia\",\"fecha\",\"total_mov\" AS \"importe\",\"descripcion\" AS \"concepto\" FROM \"mov_inv_real\" WHERE \"codmp\" = 90 AND \"tipo_mov\" = 'TRUE' AND \"fecha\" = '$fecha';\n";
	// Actualizar inventarios (sumar cantidades a favor)
	$sql .= "UPDATE \"inventario_real\" SET \"existencia\" = \"existencia\" + \"diferencias_inventario\".\"cantidad\" WHERE \"num_cia\" = \"diferencias_inventario\".\"num_cia\" AND \"codmp\" = \"diferencias_inventario\".\"codmp\" AND \"diferencias_inventario\".\"tipo_mov\" = 'FALSE' AND \"diferencias_inventario\".\"fecha\" = '$fecha';\n";
	// Actualizar inventarios (restar cantidades en contra)
	$sql .= "UPDATE \"inventario_real\" SET \"existencia\" = \"existencia\" - \"diferencias_inventario\".\"cantidad\" WHERE \"num_cia\" = \"diferencias_inventario\".\"num_cia\" AND \"codmp\" = \"diferencias_inventario\".\"codmp\" AND \"diferencias_inventario\".\"tipo_mov\" = 'TRUE' AND \"diferencias_inventario\".\"fecha\" = '$fecha';\n";
	// Copiar inventario real al virtual
	$sql .= "DELETE FROM \"inventario_virtual\";\n";
	$sql .= "INSERT INTO \"inventario_virtual\" (\"num_cia\",\"codmp\",\"existencia\",\"precio_unidad\") SELECT \"num_cia\",\"codmp\",\"existencia\",\"precio_unidad\" FROM \"inventario_real\";\n";
	// Actualizar historico de inventario
	$sql .= "INSERT INTO \"historico_inventario\" (\"num_cia\",\"codmp\",\"fecha\",\"existencia\",\"precio_unidad\") SELECT \"num_cia\",\"codmp\",'$fecha' AS \"fecha\",\"existencia\",\"precio_unidad\" FROM \"inventario_real\";";
	// Borrar todos los gastos y volver a insertarlos
	$sql .= "DELETE FROM movimiento_gastos WHERE fecha BETWEEN '$fecha_ini' AND '$fecha' AND captura = 'TRUE';\n";
	$sql .= "INSERT INTO movimiento_gastos (codgastos,num_cia,fecha,importe,concepto,captura,folio) SELECT codgastos,num_cia,fecha,importe,concepto,'TRUE',folio FROM cheques WHERE fecha BETWEEN '$fecha_ini' AND '$fecha' AND fecha_cancelacion IS NULL;\n";
	// Pasar todos los movimientos del mes a una tabla temporal
	$sql .= "TRUNCATE TABLE mov_inv_real_temp;\n";
	$sql .= "INSERT INTO mov_inv_real_temp (num_cia, codmp, fecha, cod_turno, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion)";
	$sql .= " SELECT num_cia, codmp, fecha, cod_turno, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion FROM mov_inv_real WHERE fecha BETWEEN '$fecha_ini' AND '$fecha' AND num_cia < 100;\n";
	// Poner bandera en tareas cron para actualizar costos en historico
	$sql .= "UPDATE flags SET actualizar_historico = TRUE;\n";
	
	// Ejecutar scripts
	$db->comenzar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	
	$db->desconectar();
	header("location: ./bal_act_inv_v2.php?alerta=1");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_inv_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['cancelar']))
	unset($_SESSION['act_inv']);

if (empty($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$fecha = date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")));
	
	if (empty($_SESSION['act_inv'])) {
		// Obtener primera compañía con diferencias
		$sql = "SELECT \"num_cia\" FROM \"inventario_fin_mes\" WHERE \"fecha\" >= '$fecha' ORDER BY \"num_cia\" LIMIT 1";
		$next_cia = $db->query($sql);
		if ($next_cia)
			$tpl->assign("num_cia",$next_cia[0]['num_cia']);
		else
			$tpl->assign("num_cia",1);
	}
	else
		$tpl->assign("num_cia",$_SESSION['act_inv']['num_cia']);
	
	if (isset($_GET['alerta'])) {
		$tpl->newBlock("alerta");
	}
	
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
	$db->desconectar();
	die;
}

$fecha = date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")));
$fecha2 = date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")));
$mes = date("n",mktime(0,0,0,date("m")-1,1,date("Y")));
$anio = date("Y",mktime(0,0,0,date("m")-1,1,date("Y")));

// Actualizar existencia en inventario_fin_mes
$db->comenzar_transaccion();
$sql = "UPDATE \"inventario_fin_mes\" SET \"existencia\" = \"inventario_real\".\"existencia\",\"diferencia\" = \"inventario_real\".\"existencia\" - \"inventario\",\"precio_unidad\" = \"inventario_real\".\"precio_unidad\" WHERE";
$sql .= " \"num_cia\" = $_GET[num_cia] AND \"fecha\" >= '$fecha' AND \"num_cia\" = \"inventario_real\".\"num_cia\" AND \"codmp\" = \"inventario_real\".\"codmp\"";
$db->query($sql);
$db->terminar_transaccion();

// Obtener diferencias
$sql = "SELECT \"id\",\"codmp\",\"nombre\",\"existencia\",\"inventario\",\"diferencia\",\"precio_unidad\", \"controlada\" FROM \"inventario_fin_mes\" JOIN \"catalogo_mat_primas\" USING (\"codmp\") WHERE";
$sql .= " \"num_cia\" = $_GET[num_cia] AND \"fecha\" >= '$fecha'";
if ($_GET['tipo'] != "todas")
	$sql .= " AND \"controlada\" = '".($_GET['tipo'] == "controladas" ? "TRUE" : "FALSE")."'";
$sql .= " ORDER BY \"num_cia\",\"controlada\" DESC,\"codmp\" ASC";
$result = $db->query($sql);

// Obtener todas las diferencias para obtener el total (MODIFICADO 02/OCT/2005)
$sql = "SELECT \"id\",\"codmp\",\"nombre\",\"existencia\",\"inventario\",\"diferencia\",\"precio_unidad\" FROM \"inventario_fin_mes\" JOIN \"catalogo_mat_primas\" USING (\"codmp\") WHERE";
$sql .= " \"num_cia\" = $_GET[num_cia] AND \"fecha\" >= '$fecha'";
$sql .= " ORDER BY \"num_cia\",\"controlada\",\"codmp\" ASC";
$result2 = $db->query($sql);

// Total de produccion (MODIFICADO 02/OCT/2005)
$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total BETWEEN '$fecha' AND '$fecha2'";
$produccion = $db->query($sql);

$tpl->newBlock("listado");
$tpl->assign("tipo",$_GET['tipo']);
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
$tpl->assign("mes",mes_escrito($mes,TRUE));
$tpl->assign("anio",$anio);

// Obtener siguiente compañía en el listado de diferencias
$sql = "SELECT \"num_cia\",\"nombre_corto\" FROM \"inventario_fin_mes\" JOIN \"catalogo_companias\" USING (\"num_cia\") WHERE \"num_cia\" > $_GET[num_cia] AND \"fecha\" >= '$fecha' ORDER BY \"num_cia\" LIMIT 1";
$next_cia = $db->query($sql);
if ($next_cia) {
	$tpl->assign("num_cia_next",$next_cia[0]['num_cia']);
	$tpl->assign("nombre_cia_next",$next_cia[0]['nombre_corto']);
	$_SESSION['act_inv']['num_cia'] = $next_cia[0]['num_cia'];
}
else {
	// Obtener primera compañía con diferencias
	$sql = "SELECT \"num_cia\",\"nombre_corto\" FROM \"inventario_fin_mes\" JOIN \"catalogo_companias\" USING (\"num_cia\") WHERE \"fecha\" >= '$fecha' ORDER BY \"num_cia\" LIMIT 1";
	$next_cia = $db->query($sql);
	if ($next_cia) {
		$tpl->assign("num_cia_next",$next_cia[0]['num_cia']);
		$tpl->assign("nombre_cia_next",$next_cia[0]['nombre_corto']);
		$_SESSION['act_inv']['num_cia'] = $next_cia[0]['num_cia'];
	}
}

// Listado de compañías
$sql = "SELECT \"num_cia\",\"nombre_corto\" FROM \"inventario_fin_mes\" LEFT JOIN \"catalogo_companias\" USING (\"num_cia\") WHERE \"fecha\" >= '$fecha' GROUP BY \"num_cia\",\"nombre_corto\" ORDER BY \"num_cia\"";
$cias = $db->query($sql);
for ($i=0; $i<count($cias); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
}

$cia = NULL;
$total = 0;
$favor = 0;
$contra = 0;

for ($i=0; $i<count($result); $i++) {
	if (round($result[$i]['inventario'] - $result[$i]['existencia'],2) != 0) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("mes",date("n",mktime(0,0,0,date("n"),0,date("Y"))));
		$tpl->assign("anio",date("Y",mktime(0,0,0,date("n"),0,date("Y"))));
		$tpl->assign("codmp",$result[$i]['codmp']);
		$tpl->assign("nombre_mp",$result[$i]['nombre']);
		$tpl->assign("color_mp", $result[$i]['controlada'] == "TRUE" ? "0000CC" : "993300");
		$tpl->assign("existencia",round($result[$i]['existencia'],2) != 0 ? number_format($result[$i]['existencia'],2,".",",") : "&nbsp;");
		$tpl->assign("inventario",round($result[$i]['inventario'],2) != 0 ? number_format($result[$i]['inventario'],2,".",",") : "&nbsp;");
		$diferencia = $result[$i]['inventario'] - $result[$i]['existencia'];
		$tpl->assign("falta",$diferencia < 0 ? number_format(abs($diferencia),2,".",",") : "&nbsp;");
		$tpl->assign("sobra",$diferencia > 0 ? number_format(abs($diferencia),2,".",",") : "&nbsp;");
		$tpl->assign("costo_unitario",number_format($result[$i]['precio_unidad'],4,".",","));
		$tpl->assign("costo_total","<font color=\"#".($diferencia < 0 ? "FF0000" : "0000FF")."\">".number_format(abs($diferencia * $result[$i]['precio_unidad']),2,".",",")."</font>");
		
		$total += $diferencia * round($result[$i]['precio_unidad'],4);
		if ($diferencia < 0)
			$contra += $diferencia * round($result[$i]['precio_unidad'],4);
		else
			$favor += $diferencia * round($result[$i]['precio_unidad'],4);
	}
}
$tpl->assign("listado.contra",$contra != 0 ? number_format(abs($contra),2,".",",") : "&nbsp;");
$tpl->assign("listado.favor",$favor != 0 ? number_format($favor,2,".",",") : "&nbsp;");
$tpl->assign("listado.total","<font color=\"#".($total > 0 ? "0000FF" : "FF0000")."\" size=\"+1\">".number_format(abs($total),2,".",",")."</font>");

$gran_total = 0;
$total_favor = 0;
$total_contra = 0;
for ($i=0; $i<count($result2); $i++) {
	if (round($result2[$i]['inventario'] - $result2[$i]['existencia'],2) != 0) {
		$diferencia = $result2[$i]['inventario'] - $result2[$i]['existencia'];
		
		$gran_total += $diferencia * round($result2[$i]['precio_unidad'],4);
		if ($diferencia < 0)
			$total_contra += $diferencia * round($result2[$i]['precio_unidad'],4);
		else
			$total_favor += $diferencia * round($result2[$i]['precio_unidad'],4);
	}
}
$tpl->assign("listado.total_contra",$total_contra != 0 ? number_format(abs($total_contra),2,".",",") : "&nbsp;");
$tpl->assign("listado.total_favor",$total_favor != 0 ? number_format($total_favor,2,".",",") : "&nbsp;");
$tpl->assign("listado.gran_total","<font color=\"#".($gran_total > 0 ? "0000FF" : "FF0000")."\" size=\"+1\">".number_format(abs($gran_total),2,".",",")."</font>");

$tpl->printToScreen();
$db->desconectar();
?>