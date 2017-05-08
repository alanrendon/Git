<?php
// REPORTES DE NOMINA
// Tabla 'nominas'
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
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_nom_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// GENERAR LISTADOS
if (isset($_GET['tipo'])) {
	$anio = $_GET['anio'];
	$semana_corte = $_GET['semana'];
	$numsemanas_x_fila = 28;

	// NOMINAS RECIBIDAS
	if ($_GET['tipo'] == "recibidas") {
		$numfilas_x_hoja = 32;

		$sql = "SELECT num_cia, nombre_corto, semana FROM nominas LEFT JOIN catalogo_companias USING(num_cia) WHERE semana <= $semana_corte AND anio = $anio ORDER BY num_cia,semana";
		$result = ejecutar_script($sql,$dsn);

		if (!$result) {
			header("location: ./fac_nom_con.php?mensaje=No+hay+resultados");
			die;
		}

		$tpl->newBlock("listado_recibidos");

		$tpl->assign('timestamp', date('d/m/Y H:i:s'));

		$tpl->assign("anio",$anio);
		$tpl->assign("semana",$semana_corte);

		$cia_ant = NULL;
		$numfilas = 0;
		$tpl->newBlock("bloque_rec");
		for ($i=0; $i<count($result); $i++) {
			if ($result[$i]['num_cia'] != $cia_ant) {
				$cia_ant = $result[$i]['num_cia'];

				if ($numfilas >= $numfilas_x_hoja) {
					$tpl->newBlock("bloque_rec");
					$numfilas = 0;
					$numfilas_x_hoja = 35;
				}

				$tpl->newBlock("fila_rec");
				$tpl->assign("num_cia",$result[$i]['num_cia']);
				$tpl->assign("nombre_cia",$result[$i]['nombre_corto']);
				$cadena = "";
				$numsemanas = 0;
				$numfilas++;
			}
			$cadena .= $result[$i]['semana'];
			if ($numsemanas < $numsemanas_x_fila)
				$cadena .= "&nbsp;&nbsp;&nbsp;";
			else {
				$cadena .= "<br>";
				$numsemanas = 0;
			}
			$numsemanas++;
			$tpl->assign("semanas",$cadena);
		}
		$tpl->printToScreen();
	}
	// NOMINAS PENDIENTES POR COMPAÑÍA
	else if ($_GET['tipo'] == "pendientes_cia") {
		// Obtener compañías
		$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (318, 329, 335, 339, 350, 700, 702, 704, 800) ORDER BY num_cia";
		$cia = ejecutar_script($sql,$dsn);

		$result = array();

		// Recorrer compañías
		$count = 0;
		for ($i=0; $i<count($cia); $i++) {
			$num_cia = $cia[$i]['num_cia'];
			$nombre_cia = $cia[$i]['nombre_corto'];
			// Consultar nominas ya recibidas
			$sql = "SELECT * FROM nominas WHERE num_cia = $num_cia AND semana <= $semana_corte AND anio = $anio";
			$rec = ejecutar_script($sql,$dsn);

			$corte = $num_cia == 700 || $num_cia == 800 ? floor($semana_corte / 2) : $semana_corte;

			if ($rec && count($rec) < $corte) {
				// Pasar todos los registros de recibidos a una variable temporal
				$temp = array();
				for ($j=1; $j<=count($rec); $j++)
					$temp[$j] = $rec[$j-1]['semana'];

				for ($j=1; $j<=$corte; $j++) {
					if (($key = array_search($j,$temp)) == FALSE) {
						$result[$count]['num_cia'] = $num_cia;
						$result[$count]['nombre_corto'] = $nombre_cia;
						$result[$count]['semana'] = $j;
						$count++;
					}
				}
			}
			else if (!$rec) {
				$result[$count]['num_cia'] = $num_cia;
				$result[$count]['nombre_corto'] = $nombre_cia;
				$result[$count]['semana'] = 0;
				$count++;
			}
		}

		if (count($result) > 0) {
			$numfilas_x_hoja = 60;

			$tpl->newBlock("listado_pendientes");

			$tpl->assign('timestamp', date('d/m/Y H:i:s'));

			$tpl->assign("anio",$anio);
			$tpl->assign("semana",$semana_corte);

			$cia_ant = NULL;
			$numfilas = 0;
			$tpl->newBlock("bloque_pen");
			for ($i=0; $i<count($result); $i++) {
				if ($result[$i]['num_cia'] != $cia_ant) {
					$cia_ant = $result[$i]['num_cia'];

					if ($numfilas >= $numfilas_x_hoja) {
						$tpl->newBlock("bloque_pen");
						$numfilas = 0;
						$numfilas_x_hoja = 65;
					}

					$tpl->newBlock("fila_pen");
					$tpl->assign("num_cia",$result[$i]['num_cia']);
					$tpl->assign("nombre_cia",$result[$i]['nombre_corto']);
					$cadena = "";
					$numsemanas = 0;
					$numfilas++;
				}
				$cadena .= $result[$i]['semana'] > 0 ? $result[$i]['semana'] : "NO SE HA RECIBIDO NINGUNA";
				if ($numsemanas < $numsemanas_x_fila)
					$cadena .= "&nbsp;&nbsp;&nbsp;";
				else {
					$cadena .= "<br>";
					$numsemanas = 0;
				}
				$numsemanas++;
				$tpl->assign("semanas",$cadena);
			}
			$tpl->printToScreen();
		}
		else {
			header("location: ./fac_nom_con.php?mensaje=No+hay+resultados");
			die;
		}
	}
	// NOMINAS PENDIENTES POR SUPERVISOR
	else if ($_GET['tipo'] == "pendientes_sup") {
		// Obtener compañías
		$sql = "SELECT num_cia, nombre_corto, idadministrador, nombre_administrador FROM catalogo_companias LEFT JOIN catalogo_administradores USING (idadministrador) WHERE num_cia <= 300 OR num_cia IN (303, 318, 329, 335, 339, 350, 700, 702, 704, 800) ORDER BY idadministrador, num_cia";
		$cia = ejecutar_script($sql,$dsn);

		$result = array();

		// Recorrer compañías
		$count = 0;
		for ($i=0; $i<count($cia); $i++) {
			$num_cia = $cia[$i]['num_cia'];
			$nombre_cia = $cia[$i]['nombre_corto'];
			$idadministrador = $cia[$i]['idadministrador'];
			$admin = $cia[$i]['nombre_administrador'];
			// Consultar nominas ya recibidas
			$sql = "SELECT * FROM nominas WHERE num_cia = $num_cia AND semana <= $semana_corte AND anio = $anio";
			$rec = ejecutar_script($sql,$dsn);

			$corte = $num_cia == 700 || $num_cia == 800 ? floor($semana_corte / 2) : $semana_corte;

			if ($rec && count($rec) < $corte) {
				// Pasar todos los registros de recibidos a una variable temporal
				$temp = array();
				for ($j=1; $j<=count($rec); $j++)
					$temp[$j] = $rec[$j-1]['semana'];

				for ($j=1; $j<=$corte; $j++) {
					if (($key = array_search($j,$temp)) == FALSE) {
						$result[$count]['idadministrador'] = $idadministrador;
						$result[$count]['admin'] = $admin;
						$result[$count]['num_cia'] = $num_cia;
						$result[$count]['nombre_corto'] = $nombre_cia;
						$result[$count]['semana'] = $j;
						$count++;
					}
				}
			}
			else if (!$rec) {
				$result[$count]['idadministrador'] = $idadministrador;
				$result[$count]['admin'] = $admin;
				$result[$count]['num_cia'] = $num_cia;
				$result[$count]['nombre_corto'] = $nombre_cia;
				$result[$count]['semana'] = 0;
				$count++;
			}
		}

		if (count($result) > 0) {
			$numfilas_x_hoja = 30;

			$cia_ant = NULL;
			$admon_ant = NULL;

			$numfilas = 0;
			$tpl->newBlock("bloque_pen");
			for ($i=0; $i<count($result); $i++) {
				if ($result[$i]['num_cia'] != $cia_ant) {
					$cia_ant = $result[$i]['num_cia'];

					if ($admon_ant != $result[$i]['idadministrador']) {
						$admon_ant = $result[$i]['idadministrador'];
						$tpl->newBlock("listado_pen_sup");

						$tpl->assign('timestamp', date('d/m/Y H:i:s'));

						$tpl->assign("anio",$anio);
						$tpl->assign("semana",$semana_corte);
						$tpl->assign('admin', '<br>' . $result[$i]['admin']);
					}

					$tpl->newBlock("fila_pen_sup");
					$tpl->assign("num_cia",$result[$i]['num_cia']);
					$tpl->assign("nombre_cia",$result[$i]['nombre_corto']);
					$cadena = "";
					$numsemanas = 0;
					$numfilas++;
				}
				$cadena .= $result[$i]['semana'] > 0 ? $result[$i]['semana'] : "NO SE HA RECIBIDO NINGUNA";
				if ($numsemanas < $numsemanas_x_fila)
					$cadena .= "&nbsp;&nbsp;&nbsp;";
				else {
					$cadena .= "<br>";
					$numsemanas = 0;
				}
				$numsemanas++;
				$tpl->assign("semanas",$cadena);
			}
			$tpl->printToScreen();
		}
		else {
			header("location: ./fac_nom_con.php?mensaje=No+hay+resultados");
			die;
		}
	}
	die;
}


// PANTALLA DE DATOS
$tpl->newBlock("datos");
$tpl->assign("anio",date("Y"));

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
?>
