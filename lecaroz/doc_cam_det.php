<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

function color($placa) {
	$ultimo_digito = NULL;
	
	if ($placa == "")
		return 0;
	
	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};
	
	switch ($ultimo_digito) {
		case 5:
		case 6: $color = "FFFF00"; break;
		case 7:
		case 8: $color = "FF99CC"; break;
		case 3:
		case 4: $color = "FF0000"; break;
		case 1:
		case 2: $color = "669900"; break;
		case 9:
		case 0: $color = "0000FF"; break;
		default: $color = "";
	}
	
	return $color;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_det.tpl");
$tpl->prepare();

$sql = "SELECT modelo,anio,placas,num_cia,nombre_corto,propietario,usuario,num_serie,num_motor,tipo_combustible,entidad,estatus,fecha_venta,clave_vehicular,num_poliza,inciso,plan,localizacion_fac,vencimiento,cambio_motor,tipo_unidad, fecha_tarjeta_circulacion FROM catalogo_camionetas LEFT JOIN catalogo_companias USING (num_cia) WHERE idcamioneta = $_GET[id]";
$result = $db->query($sql);

// Datos generales
$tpl->assign("id", $_GET['id']);
$tpl->assign("modelo", $result[0]['modelo'] != "" ? $result[0]['modelo'] : "&nbsp;");
$tpl->assign("anio", $result[0]['anio'] > 0 ? $result[0]['anio'] : "&nbsp;");
$tpl->assign("color", color($result[0]['placas']));
$tpl->assign("placas", $result[0]['placas'] != "" ? $result[0]['placas'] : "&nbsp;");
$tpl->assign("num_cia", $result[0]['num_cia'] > 0 ? $result[0]['num_cia'] : "&nbsp;");
$tpl->assign("nombre_cia", $result[0]['nombre_corto']);
$tpl->assign("propietario", $result[0]['propietario'] != "" ? $result[0]['propietario'] : "&nbsp;");
$tpl->assign("usuario", $result[0]['usuario'] != 0 ? $result[0]['usuario'] : "&nbsp;");
$tpl->assign("num_serie", $result[0]['num_serie'] != "" ? $result[0]['num_serie'] : "&nbsp;");
$tpl->assign("num_motor", $result[0]['num_motor'] != "" ? $result[0]['num_motor'] : "&nbsp;");
$tpl->assign("tipo_combustible", $result[0]['tipo_combustible'] == "t" ? "GASOLINA" : "GAS");
$tpl->assign("entidad", $result[0]['entidad'] == 1 ? "DISTRITO FEDERAL" : ($result[0]['entidad'] == 2 ? "ESTADO" : ($result[0]['entidad'] == 3 ? "MORELOS" : "OTRO")));
$tpl->assign("estatus", $result[0]['estatus'] == 1 ? "EN USO" : ($result[0]['estatus'] == 2 ? "VENDIDA" : "ROBADA"));
$tpl->assign("fecha_venta", $result[0]['fecha_venta'] != "" ? $result[0]['fecha_venta'] : "&nbsp;");
$tpl->assign("fecha_tarjeta_circulacion", $result[0]['fecha_tarjeta_circulacion'] != "" ? $result[0]['fecha_tarjeta_circulacion'] : "&nbsp;");
$tpl->assign("clave_vehicular", $result[0]['clave_vehicular'] != "" ? $result[0]['clave_vehicular'] : "&nbsp;");
$tpl->assign("num_poliza", $result[0]['num_poliza'] != "" ? $result[0]['num_poliza'] : "&nbsp;");
$tpl->assign("inciso", $result[0]['inciso'] != "" ? $result[0]['inciso'] : "&nbsp;");
$tpl->assign("plan", $result[0]['plan'] == 1 ? "TERCEROS" : ($result[0]['plan'] == 2 ? "TODO RIESGO" : ""));
$tpl->assign("localizacion_fac", $result[0]['localizacion_fac'] != "" ? $result[0]['localizacion_fac'] : "&nbsp;");
$tpl->assign("vencimiento", $result[0]['vencimiento'] != "" ? $result[0]['vencimiento'] : "&nbsp;");
$tpl->assign("cambio_motor", $result[0]['cambio_motor'] == "t" ? "SI" : "NO");
$tpl->assign('tipo_unidad', $result[0]['tipo_unidad'] == 1 ? 'CARGA' : ($result[0]['tipo_unidad'] == 2 ? 'PERSONAL' : 'PARTICULAR'));

// Scans
$sql = "SELECT id_doc, tipo_doc, id_img, indice FROM doc_camionetas LEFT JOIN img_camionetas USING (id_doc) WHERE idcamioneta = $_GET[id] ORDER BY id_img, indice";
$doc = $db_scans->query($sql);

if ($doc) {
	$id_doc = NULL;
	for ($i = 0; $i < count($doc); $i++) {
		if ($id_doc != $doc[$i]['id_doc']) {
			$id_doc = $doc[$i]['id_doc'];
			
			$tpl->newBlock("doc");
			$tipo_doc = $db->query("SELECT descripcion FROM catalogo_doc_camionetas WHERE tipo_doc = {$doc[$i]['tipo_doc']}");
			$tpl->assign("tipo_doc", $tipo_doc[0]['descripcion']);
			
			$cols = 0;
		}
		$tpl->newBlock("img");
		$tpl->assign("id_img", $doc[$i]['id_img']);
		$tpl->assign("indice", $doc[$i]['indice']);
		$cols++;
		$tpl->assign("doc.colspan", $cols);
	}
}

$tpl->printToScreen();
$db->desconectar();
// $db_scans->desconectar();
?>