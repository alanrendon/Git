<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

function mes_abr($mes) {
	switch ($mes) {
		case 1: $mes = 'Ene'; break;
		case 2: $mes = 'Feb'; break;
		case 3: $mes = 'Mar'; break;
		case 4: $mes = 'Abr'; break;
		case 5: $mes = 'May'; break;
		case 6: $mes = 'Jun'; break;
		case 7: $mes = 'Jul'; break;
		case 8: $mes = 'Ago'; break;
		case 9: $mes = 'Sep'; break;
		case 10: $mes = 'Oct'; break;
		case 11: $mes = 'Nov'; break;
		case 12: $mes = 'Dic'; break;
		default: $mes = '';
	}

	return $mes;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_cam.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['idadministrador'])) {
	if (!isset($_GET['prom'])) {
		$array_keys = array(
			"venta_puerta",
			"otros",
			"ventas_netas",
			"inv_ant",
			"compras",
			"mercancias",
			"inv_act",
			"mat_prima_utilizada",
			"gastos_fab",
			"costo_produccion",
			"utilidad_bruta",
			"gastos_generales",
			"gastos_caja",
			"reserva_aguinaldos",
			"gastos_otras_cias",
			"total_gastos",
			"ingresos_ext",
			"utilidad_neta",
			"efectivo",
			"mp_pro",
			"utilidad_bruta_pro",
			"utilidad_pro",
			"clientes",
			"utilidad_ventas",
			"utilidad_mat_prima",
			"mat_prima_ventas",
			"pollos",
			"pescuezos",
			"precio_pollo"
		);

		// Obtener cias
		if (array_search($_GET['campo'], $array_keys) !== FALSE) {
			$sql = "
				(
					SELECT
						num_cia,
						nombre_corto,
						idadministrador,
						tipo_cia
					FROM
						balances_pan
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						anio = $_GET[anio]
						AND mes BETWEEN 1 AND $_GET[mes]
			";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : ($_GET['rango'] == 0 || $_GET['rango'] == 1 ? " AND tipo_cia = 1" : " AND tipo_cia = 2");
			$sql .= "
					GROUP BY
						num_cia,
						nombre_corto,
						idadministrador,
						tipo_cia
				)

				UNION

				(
					SELECT
						num_cia,
						nombre_corto,
						idadministrador,
						tipo_cia
					FROM
						balances_ros
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						anio = $_GET[anio]
						AND mes BETWEEN 1 AND $_GET[mes]
			";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : ($_GET['rango'] == 0 || $_GET['rango'] == 2 ? " AND tipo_cia = 2" : " AND tipo_cia = 1");
			$sql .= "
					GROUP BY
						num_cia,
						nombre_corto,
						idadministrador,
						tipo_cia
				)

				ORDER BY ";
			$sql .= $_GET['idadministrador'] == -1 ? "idadministrador" : "num_cia";
		}
		else {
			$sql = "
				SELECT
					num_cia,
					nombre_corto,
					idadministrador,
					tipo_cia
				FROM
					balances_pan
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					anio = $_GET[anio]
					AND mes BETWEEN 1 AND $_GET[mes]
			";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia <= 300";
			$sql .= "
				GROUP BY
					num_cia,
					nombre_corto,
					idadministrador,
					tipo_cia
				ORDER BY ";
			$sql .= $_GET['idadministrador'] == -1 ? "idadministrador" : "num_cia";
		}

		$cia = $db->query($sql);

		if (!$cia) {
			header("location: ./bal_com_cam.php?codigo_error=1");
			die;
		}

		// Obtener cantodades descontables a la utilidad del mes
		$sql = "SELECT num_cia, cantidad FROM desc_utilidad_mes ORDER BY num_cia";
		$desc = $db->query($sql);

		function buscarDesc($num_cia) {
			global $desc;
			if (!$desc)
				return 0;

			foreach ($desc as $reg)
				if ($reg['num_cia'] == $num_cia)
					return $reg['cantidad'];

			return 0;
		}

		$title['venta_puerta'] = "Venta en Puerta";
		$title['bases'] = "Bases";
		$title['barredura'] = "Barredura";
		$title['pastillaje'] = "Pastillaje";
		$title['abono_emp'] = "Abono Empleados";
		$title['otros'] = "Otros";
		$title['total_otros'] = "Total Otros";
		$title['abono_reparto'] = "Abono Reparto";
		$title['errores'] = "Errores";
		$title['ventas_netas'] = "Ventas Netas";
		$title['inv_ant'] = "Inventario Anterior";
		$title['compras'] = "Compras";
		$title['mercancias'] = "Mercancias";
		$title['inv_act'] = "Inventario Actual";
		$title['mat_prima_utilizada'] = "Mat. Prima Utilizada";
		$title['mano_obra'] = "Mano de Obra";
		$title['panaderos'] = "Panaderos";
		$title['gastos_fab'] = "Gastos de Fabricaci&oacute;n";
		$title['costo_produccion'] = "Costo de Producci&oacute;n";
		$title['utitlidad_bruta'] = "Utilidad Bruta";
		$title['pan_comprado'] = "Pan Comprado";
		$title['gastos_generales'] = "Gastos Generales";
		$title['gastos_caja'] = "Gastos por Caja";
		$title['reserva_aguinaldos'] = "Reserva para Aguinaldos";
		$title['gastos_otras_cias'] = "Gastos Pagados por Otras Cias.";
		$title['total_gastos'] = "Total de Gastos";
		$title['ingresos_ext'] = "Ingresos Extraordinarios";
		$title['utilidad_neta'] = "Utilidad del Mes";
		$title['produccion_total'] = "Producci&oacute;n Total";
		$title['faltante_pan'] = "Faltante de Pan";
		$title['rezago_ini'] = "Rezago Inicial";
		$title['rezago_fin'] = "Rezago Final";
		$title['efectivo'] = "Efectivo";
		$title['utilidad_pro'] = "Utilidad Neta / Producci&oacute;n";
		$title['utilidad_bruta_pro'] = "Utilidad Bruta / Producci&oacute;n";
		$title['mp_pro'] = "Materia prima / Producci&oacute;n";
		$title['clientes'] = "Clientes";
		$title['utilidad_ventas'] = "Utilidad neta / Ventas";
		$title['utilidad_mat_prima'] = "Utilidad neta / Materia prima";
		$title['mat_prima_ventas'] = "Materia prima / Ventas";
		$title['pollos'] = "Pollos";
		$title['pescuezos'] = "Pescuezos";
		$title['precio_pollo'] = "Precio por kilo";


		function buscar($mes) {
			global $result, $campo;

			for ($i = 0; $i < count($result); $i++)
				if ($mes == $result[$i]['mes']) {
					if (strpos($campo, "utilidad_neta, ingresos_ext") !== FALSE)
					{
						$value = $result[$i]['utilidad_neta'] - (!isset($_GET['ing']) ? $result[$i]['ingresos_ext'] : 0);

						if ($result[$i]['num_cia'] > 300 && $_REQUEST['anio'] < 2016)
						{
							$fecha1 = date('d/m/Y', mktime(0, 0, 0, $result[$i]['mes'], 1, $result[$i]['anio']));
							$fecha2 = date('d/m/Y', mktime(0, 0, 0, $result[$i]['mes'] + 1, 0, $result[$i]['anio']));

							$ide = $GLOBALS['db']->query("SELECT
								ROUND(SUM(importe)::NUMERIC - 25000, 2) * 0.03 AS importe
							FROM
								estado_cuenta
							WHERE
								num_cia = {$result[$i]['num_cia']}
							AND fecha_con BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND cod_mov IN (1, 7, 13, 16, 79)");

							$value -= $ide[0]['importe'];
						}

						return $value;
					}
					else
					{
						return $result[$i][$campo];
					}
				}

			return FALSE;
		}

		$numfilas_x_hoja = 45;
		$numfilas = $numfilas_x_hoja;
		$idadmin = NULL;
		$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
		$gran_total = 0;
		$prom = 0;
		for ($i = 0; $i < count($cia); $i++) {
			if (($_GET['idadministrador'] == -1 && $idadmin != $cia[$i]['idadministrador']) || $numfilas >= $numfilas_x_hoja) {
				if ($_GET['idadministrador'] == -1 && $idadmin != NULL) {
					$tpl->newBlock("totales");

					for ($j = 1; $j <= $_GET['mes']; $j++) {
						$tpl->newBlock("total");
						$tpl->assign("total", number_format($total_mes[$j], (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));
					}

					//$tpl->assign("colspan", $_GET['mes'] + 1);
					$tpl->newBlock("total");
					$tpl->assign("total", number_format($gran_total, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));
					$tpl->newBlock("total");
					$tpl->assign("total", number_format($prom, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");

					$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
					$gran_total = 0;
					$prom = 0;
				}

				$idadmin = $cia[$i]['idadministrador'];

				$tpl->newBlock("listado");
				$tpl->assign("campo", $title[$_GET['campo']]);
				$tpl->assign("anio", $_GET['anio']);
				for ($j = 1; $j <= $_GET['mes']; $j++) {
					$tpl->newBlock("title_mes");
					$tpl->assign("mes", mes_abr($j));
				}
				$tpl->newBlock("title_mes");
				$tpl->assign("mes", "Total");
				$tpl->newBlock("title_mes");
				$tpl->assign("mes", "Prom");

				$numfilas = 0;
			}

			$tabla = $cia[$i]['tipo_cia'] == 1 ? "balances_pan" : "balances_ros";
			if ($_GET['campo'] == "venta_puerta")
				$campo = $cia[$i]['tipo_cia'] == 1 ? (isset($_GET['error']) ? "venta_puerta - errores AS venta_puerta" : "venta_puerta") : "venta";
			else if ($_GET['campo'] == "utilidad_neta")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "utilidad_neta + COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
						LEFT JOIN catalogo_companias ccec
							USING (num_cia)
					WHERE
						((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta, ingresos_ext" : "utilidad_neta - COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
					WHERE
						((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta, ingresos_ext";
			else if ($_GET['campo'] == "utilidad_pro")
				$campo = $cia[$i]['tipo_cia'] == 1 ? $_GET['campo'] : "0 AS utilidad_pro";
			else if ($_GET['campo'] == "utilidad_bruta_pro")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "utilidad_bruta / produccion_total AS utilidad_bruta_pro" : "0 AS utilidad_bruta_pro";
			else if ($_GET['campo'] == "mp_pro")
				$campo = $cia[$i]['tipo_cia'] == 1 ? $_GET['campo'] : "0 AS mp_pro";
			else if ($_GET['campo'] == "clientes")
				$campo = "COALESCE((SELECT clientes FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes), 0) AS clientes";
			else if ($_GET['campo'] == "utilidad_ventas")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS utilidad_ventas" : "CASE WHEN ventas_netas != 0 THEN utilidad_neta / ventas_netas ELSE 0 END AS utilidad_ventas";
			else if ($_GET['campo'] == "utilidad_mat_prima")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS utilidad_mat_prima" : "utilidad_neta / mat_prima_utilizada AS utilidad_mat_prima";
			else if ($_GET['campo'] == "mat_prima_ventas")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS mat_prima_ventas" : "CASE WHEN ventas_netas > 0 THEN mat_prima_utilizada / ventas_netas ELSE 0 END AS mat_prima_ventas";
			else if ($_GET['campo'] == "pollos")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS pollos" : "pollos_vendidos AS pollos";
			else if ($_GET['campo'] == "pescuezos")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS pescuezos" : "pescuezos";
			else if ($_GET['campo'] == "precio_pollo")
				$campo = $cia[$i]['tipo_cia'] == 1 ? "0 AS precio_pollo" : "(SELECT precio_pollo FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes) AS precio_pollo";
			else
				$campo = $_GET['campo'];

			$sql = "SELECT num_cia, $campo, mes, anio FROM $tabla AS bal LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia = {$cia[$i]['num_cia']} AND mes BETWEEN 1 AND $_GET[mes] AND anio = $_GET[anio]";
			$result = $db->query($sql);

			if (strpos($campo, "utilidad_neta, ingresos_ext") !== FALSE) $campo = 'utilidad_neta, ingresos_ext';
			if ($campo == 'venta_puerta - errores AS venta_puerta') $campo = 'venta_puerta';
			if ($campo == '0 AS utilidad_pro') $campo = 'utilidad_pro';
			if ($campo == 'utilidad_bruta / produccion_total AS utilidad_bruta_pro' || $campo == '0 AS utilidad_bruta_pro') $campo = 'utilidad_bruta_pro';
			if ($campo == '0 AS mp_pro') $campo = 'mp_pro';
			if ($campo == "COALESCE((SELECT clientes FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes), 0) AS clientes") $campo = 'clientes';
			if ($campo == 'CASE WHEN ventas_netas != 0 THEN utilidad_neta / ventas_netas ELSE 0 END AS utilidad_ventas' || $campo == '0 AS utilidad_ventas') $campo = 'utilidad_ventas';
			if ($campo == 'utilidad_neta / mat_prima_utilizada AS utilidad_mat_prima' || $campo == '0 AS utilidad_mat_prima') $campo = 'utilidad_mat_prima';
			if ($campo == 'CASE WHEN ventas_netas > 0 THEN mat_prima_utilizada / ventas_netas ELSE 0 END AS mat_prima_ventas' || $campo == '0 AS mat_prima_ventas') $campo = 'mat_prima_ventas';
			if ($campo == 'pollos_vendidos AS pollos' || $campo == '0 AS pollos') $campo = 'pollos';
			if ($campo == '0 AS pescuezos') $campo = 'pescuezos';
			if ($campo == "(SELECT precio_pollo FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes) AS precio_pollo" || $campo == '0 AS precio_pollo') $campo = 'precio_pollo';

			// [06-Dic-2006] Validar que el total del mes no sea 0
			$total = 0;
			if ($result)
			{
				foreach ($result as $reg)
				{
					$total += (strpos($campo, "utilidad_neta, ingresos_ext") !== FALSE ? $reg['utilidad_neta'] - (!isset($_GET['ing']) ? $reg['ingresos_ext'] : 0) : $reg[$campo]) + ($_GET['campo'] == "utilidad_neta" && isset($_GET['desc']) ? buscarDesc($cia[$i]['num_cia']) : 0);
				}
			}

			if ($total == 0) continue;

			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $cia[$i]['num_cia']);
			$tpl->assign("nombre_cia", str_replace('ROSTICERIA', 'ROST.', $cia[$i]['nombre_corto']));

			$total = 0;

			for ($j = 1; $j <= $_GET['mes']; $j++) {
				$tpl->newBlock("mes");
				$tpl->assign("dato", ($dato = buscar($j) + ($_GET['campo'] == "utilidad_neta" && isset($_GET['desc']) ? buscarDesc($cia[$i]['num_cia']) : 0)) != 0 ? number_format($dato, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ",") : "&nbsp;");
				$total_mes[$j] += $dato;
				$total += $dato;
			}
			$tpl->newBlock("mes");
			$tpl->assign("dato", "<strong>" . number_format($total, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ",") . "</strong>");
			$tpl->newBlock("mes");
			$tpl->assign("dato", "<strong>" . number_format($total / $_GET['mes'], (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ",") . "</strong>");
			$gran_total += $total;
			$prom += $total / $_GET['mes'];

			$numfilas++;
			if ($numfilas >= $numfilas_x_hoja)
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
		}
		$tpl->newBlock("totales");
		for ($i = 1; $i <= $_GET['mes']; $i++) {
			$tpl->newBlock("total");
			$tpl->assign("total", number_format($total_mes[$i], (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));
		}
		//$tpl->assign("colspan", $_GET['mes'] + 1);
		$tpl->newBlock("total");
		$tpl->assign("total", number_format($gran_total, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));
		$tpl->newBlock("total");
		$tpl->assign("total", number_format($prom, (in_array($_GET['campo'], array('utilidad_pro', 'mp_pro', 'utilidad_bruta_pro', 'utilidad_ventas', 'utilidad_mat_prima', 'mat_prima_ventas', 'precio_pollo')) ? 2 : 0), ".", ","));

		$tpl->printToScreen();
		die;
	}
	else {
		$anio_ant = $_GET['anio'] - 1;
		$anio_act = $_GET['anio'];
		$array_keys = array(
			"venta_puerta",
			"otros",
			"ventas_netas",
			"inv_ant",
			"compras",
			"mercancias",
			"inv_act",
			"mat_prima_utilizada",
			"gastos_fab",
			"costo_produccion",
			"utilidad_bruta",
			"gastos_generales",
			"gastos_caja",
			"reserva_aguinaldos",
			"gastos_otras_cias",
			"total_gastos",
			"ingresos_ext",
			"utilidad_neta",
			"efectivo",
			"mp_pro",
			"utilidad_bruta_pro",
			"utilidad_pro",
			"clientes",
			"utilidad_ventas",
			"utilidad_mat_prima",
			"mat_prima_ventas",
			"pollos",
			"pescuezos",
			"precio_pollo"
		);

		// Obtener cias
		if (array_search($_GET['campo'], $array_keys) !== FALSE) {
			$sql = "(SELECT num_cia, nombre_corto, idadministrador, tipo_cia FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE anio IN ($anio_ant, $anio_act) AND mes BETWEEN 1 AND $_GET[mes]";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : ($_GET['rango'] == 0 || $_GET['rango'] == 1 ? " AND num_cia <= 300" : " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704))");
			$sql .= " GROUP BY num_cia, nombre_corto, idadministrador, tipo_cia)";
			$sql .= " UNION ";
			$sql .= "(SELECT num_cia, nombre_corto, idadministrador, tipo_cia FROM balances_ros LEFT JOIN catalogo_companias USING (num_cia) WHERE anio IN ($anio_ant, $anio_act) AND mes BETWEEN 1 AND $_GET[mes]";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : ($_GET['rango'] == 0 || $_GET['rango'] == 2 ? " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704))" : " AND num_cia < 100");
			$sql .= " GROUP BY num_cia, nombre_corto, idadministrador, tipo_cia)";
			$sql .= " ORDER BY";
			$sql .= $_GET['idadministrador'] == -1 ? " idadministrador" : " num_cia";
		}
		else {
			$sql = "SELECT num_cia, nombre_corto, idadministrador, tipo_cia FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE anio IN ($anio_ant, $anio_act) AND mes BETWEEN 1 AND $_GET[mes]";
			$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
			$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia <= 300";
			$sql .= " GROUP BY num_cia, nombre_corto, idadministrador, tipo_cia ORDER BY";
			$sql .= $_GET['idadministrador'] == -1 ? " idadministrador" : " num_cia";
		}
		$cia = $db->query($sql);

		if (!$cia) {
			header("location: ./bal_com_cam.php?codigo_error=1");
			die;
		}

		// Obtener cantodades descontables a la utilidad del mes
		$sql = "SELECT num_cia, cantidad FROM desc_utilidad_mes ORDER BY num_cia";
		$desc = $db->query($sql);

		function buscarDesc($num_cia) {
			global $desc;
			if (!$desc)
				return 0;

			foreach ($desc as $reg)
				if ($reg['num_cia'] == $num_cia)
					return $reg['cantidad'];

			return 0;
		}

		$title['venta_puerta'] = "Venta en Puerta";
		$title['bases'] = "Bases";
		$title['barredura'] = "Barredura";
		$title['pastillaje'] = "Pastillaje";
		$title['abono_emp'] = "Abono Empleados";
		$title['otros'] = "Otros";
		$title['total_otros'] = "Total Otros";
		$title['abono_reparto'] = "Abono Reparto";
		$title['errores'] = "Errores";
		$title['ventas_netas'] = "Ventas Netas";
		$title['inv_ant'] = "Inventario Anterior";
		$title['compras'] = "Compras";
		$title['mercancias'] = "Mercancias";
		$title['inv_act'] = "Inventario Actual";
		$title['mat_prima_utilizada'] = "Mat. Prima Utilizada";
		$title['mano_obra'] = "Mano de Obra";
		$title['panaderos'] = "Panaderos";
		$title['gastos_fab'] = "Gastos de Fabricaci&oacute;n";
		$title['costo_produccion'] = "Costo de Producci&oacute;n";
		$title['utitlidad_bruta'] = "Utilidad Bruta";
		$title['pan_comprado'] = "Pan Comprado";
		$title['gastos_generales'] = "Gastos Generales";
		$title['gastos_caja'] = "Gastos por Caja";
		$title['reserva_aguinaldos'] = "Reserva para Aguinaldos";
		$title['gastos_otras_cias'] = "Gastos Pagados por Otras Cias.";
		$title['total_gastos'] = "Total de Gastos";
		$title['ingresos_ext'] = "Ingresos Extraordinarios";
		$title['utilidad_neta'] = "Utilidad del Mes";
		$title['produccion_total'] = "Producci&oacute;n Total";
		$title['faltante_pan'] = "Faltante de Pan";
		$title['rezago_ini'] = "Rezago Inicial";
		$title['rezago_fin'] = "Rezago Final";
		$title['efectivo'] = "Efectivo";
		$title['utilidad_pro'] = "Utilidad Neta / Producci&oacute;n";
		$title['utilidad_bruta_pro'] = "Utilidad Bruta / Producci&oacute;n";
		$title['mp_pro'] = "Materia prima / Producci&oacute;n";
		$title['clientes'] = "Clientes";
		$title['utilidad_ventas'] = "Utilidad neta / Ventas";
		$title['utilidad_mat_prima'] = "Utilidad neta / Materia prima";
		$title['mat_prima_ventas'] = "Materia prima / Ventas";
		$title['pollos'] = "Pollos";
		$title['pescuezos'] = "Pescuezos";
		$title['precio_kilo'] = "Precio por kilo";

		function buscar($anio) {
			global $result, $campo;

			for ($i = 0; $i < count($result); $i++)
				if ($anio == $result[$i]['anio']) {
					if ($campo == 'utilidad_mes' && $anio < 2016 && $result[$i]['num_cia'] > 300)
					{
						$value = $result[$i][$campo];

						$ide = $db->query("SELECT
							SUM(importe)
						FROM
							(
								SELECT
									mes,
									SUM(COALESCE((
										SELECT
											ROUND(SUM(importe)::NUMERIC - 25000, 2) * 0.03 AS importe
										FROM
											estado_cuenta
										WHERE
											num_cia = bal.num_cia
											AND fecha_con BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
											AND cod_mov IN (1, 7, 13, 16, 79)
									), 0)) AS importe
								FROM
									balances_ros AS bal
								WHERE
									num_cia = {$result[0]['num_cia']}
									AND anio = {$anio}
									AND mes <= {$_REQUEST['mes']}
								GROUP BY
									anio,
									mes
						) AS result");

						$value -= $ide[0]['importe'];

						return $value;
					}
					else
					{
						return $result[$i][$campo];
					}
				}

			return FALSE;
		}

		// Obtener datos
		$data1 = array();
		$data2 = array();
		for ($i = 0; $i < count($cia); $i++) {
			$tabla = $cia[$i]['tipo_cia'] == 1 ? "balances_pan" : "balances_ros";
			if ($_GET['campo'] == "venta_puerta") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? (isset($_GET['error']) ? "sum(venta_puerta - errores) / $_GET[mes] AS venta_puerta" : "sum(venta_puerta) / $_GET[mes] AS venta_puerta") : "sum(venta) / $_GET[mes] AS venta";
				$campo = $cia[$i]['tipo_cia'] == 1 ? "venta_puerta" : "venta";
			}
			else if ($_GET['campo'] == "utilidad_neta") {
				$campo_query = "sum(utilidad_neta" . (!isset($_GET['ing']) ? " - ingresos_ext" : "") . ") / $_GET[mes] AS utilidad_neta";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "utilidad_pro" || $_GET['campo'] == "mp_pro") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "sum($_GET[campo]) / $_GET[mes] AS $_GET[campo]" : "0 AS $_GET[campo]";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "utilidad_bruta_pro") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "sum(CASE WHEN produccion_total > 0 THEN (utilidad_bruta / produccion_total) ELSE 0 END) / $_GET[mes] AS $_GET[campo]" : "0 AS $_GET[campo]";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "mp_pro") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "SUM({$_GET['campo']}) / $_GET[mes] AS {$_GET['campo']}" : "0 AS mp_pro";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "clientes") {
				$campo_query = "SUM(COALESCE((SELECT clientes FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes), 0)) / $_GET[mes] AS clientes";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "utilidad_ventas") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS utilidad_ventas" : "SUM(CASE WHEN ventas_netas != 0 THEN utilidad_neta / ventas_netas ELSE 0 END) / $_GET[mes] AS utilidad_ventas";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "utilidad_mat_prima") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS utilidad_mat_prima" : "SUM(utilidad_neta / mat_prima_utilizada) / $_GET[mes] AS utilidad_mat_prima";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "mat_prima_ventas") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS mat_prima_ventas" : "SUM(CASE WHEN ventas_netas > 0 THEN mat_prima_utilizada / ventas_netas ELSE 0 END) / $_GET[mes] AS mat_prima_ventas";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "pollos") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS pollos" : "SUM(pollos_vendidos) / $_GET[mes] AS pollos";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "pescuezos") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS pescuezos" : "SUM(pescuezos) / $_GET[mes] AS pescuezos";
				$campo = $_GET['campo'];
			}
			else if ($_GET['campo'] == "precio_pollo") {
				$campo_query = $cia[$i]['tipo_cia'] == 1 ? "0 AS precio_pollo" : "SUM(SELECT precio_pollo FROM historico WHERE num_cia = $tabla.num_cia AND anio = $tabla.anio AND mes = $tabla.mes) / $_GET[mes] AS precio_pollo";
				$campo = $_GET['campo'];
			}
			else {
				$campo_query = "sum($_GET[campo]) / $_GET[mes] AS $_GET[campo]";
				$campo = $_GET['campo'];
			}

			$sql = "SELECT $campo_query, anio FROM $tabla WHERE num_cia = {$cia[$i]['num_cia']} AND mes BETWEEN 1 AND $_GET[mes] AND anio IN ($anio_ant, $anio_act) GROUP BY anio ORDER BY anio";
			$result = $db->query($sql);

			if ($cia[$i]['tipo_cia'] == 1) {
				$data1[$i]['num_cia'] = $cia[$i]['num_cia'];
				$data1[$i]['nombre_corto'] = $cia[$i]['nombre_corto'];
				$data1[$i]['idadministrador'] = $cia[$i]['idadministrador'];
				$data1[$i]['prom_ant'] = buscar($anio_ant);
				$data1[$i]['prom_act'] = buscar($anio_act);
				if ($_GET['campo'] == "utilidad_neta" && isset($_GET['desc']))
					$data1[$i]['prom_act'] += buscarDesc($cia[$i]['num_cia']);
				$data1[$i]['dif'] = $data1[$i]['prom_ant'] - $data1[$i]['prom_act'];
				@$data1[$i]['inc'] = $data1[$i]['prom_ant'] != 0 && $data1[$i]['prom_act'] != 0 ? -$data1[$i]['dif'] / abs($data1[$i]['prom_ant']) * 100 : NULL;
				$data1[$i]['tipo'] = 1;
			}
			else {
				$data2[$i]['num_cia'] = $cia[$i]['num_cia'];
				$data2[$i]['nombre_corto'] = $cia[$i]['nombre_corto'];
				$data2[$i]['idadministrador'] = $cia[$i]['idadministrador'];
				$data2[$i]['prom_ant'] = buscar($anio_ant);
				$data2[$i]['prom_act'] = buscar($anio_act);
				if ($_GET['campo'] == "utilidad_neta" && isset($_GET['desc']))
					$data2[$i]['prom_act'] += buscarDesc($cia[$i]['num_cia']);
				$data2[$i]['dif'] = $data2[$i]['prom_ant'] - $data2[$i]['prom_act'];
				@$data2[$i]['inc'] = $data2[$i]['prom_ant'] != 0 && $data2[$i]['prom_act'] != 0 ? -$data2[$i]['dif'] / abs($data2[$i]['prom_ant']) * 100 : NULL;
				$data2[$i]['tipo'] = 2;
			}
		}

		function cmp($a, $b) {
			if ($_GET['idadministrador'] == -1) {
				if ($a['idadministrador'] == $b['idadministrador']) {
					if ($a['tipo'] == $b['tipo']) {
						if ($a['inc'] == $b['inc'])
							return 0;
						else
							return $a['inc'] < $b['inc'] ? 1 : -1;
					}
					else
						return $a['tipo'] < $b['tipo'] ? -1 : 1;
				}
				else
					return $a['idadministrador'] < $b['idadministrador'] ? -1 : 1;
			}
			else {
				if ($a['tipo'] == $b['tipo']) {
					if ($a['inc'] == $b['inc'])
						return 0;
					else
						return $a['inc'] < $b['inc'] ? 1 : -1;
					}
				else
					return $a['tipo'] < $b['tipo'] ? -1 : 1;
			}
		}

		//usort($data1, "cmp");
		//usort($data2, "cmp");

		$data = array_merge($data1, $data2);
		usort($data, "cmp");

		$numfilas_x_hoja = 59;
		$numfilas = $numfilas_x_hoja;
		$idadmin = NULL;
		$total_ant = 0;
		$total_act = 0;
		$total_dif = 0;

		$sin_prom_cont = 0;
		for ($i = 0; $i < count($cia); $i++) {
			if ($data[$i]['dif'] != 0 && $data[$i]['inc'] == 0) {
				$sin_prom_cont++;
				continue;
			}

			if (($_GET['idadministrador'] == -1 && $idadmin != $data[$i]['idadministrador']) || $numfilas >= $numfilas_x_hoja) {
				if ($idadmin != NULL) {
					//$tpl->newBlock("totales_prom");
					//$tpl->assign("total_ant", number_format($total_ant, 2, ".", ","));
					//$tpl->assign("total_act", number_format($total_act, 2, ".", ","));
					//$tpl->assign("total_dif", number_format($total_dif, 2, ".", ","));
					$tpl->assign("promedios.salto", "<br style=\"page-break-after:always;\">");
				}

				$idadmin = $data[$i]['idadministrador'];

				$tpl->newBlock("promedios");
				$tpl->assign("campo", $title[$_GET['campo']]);
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				$tpl->assign("anio_ant", $anio_ant);

				//$total_ant = 0;
				//$total_act = 0;
				//$total_dif = 0;

				$numfilas = 0;
			}
			$color = $data[$i]['inc'] === NULL ? "0000CC" : ($data[$i]['inc'] > 0 ? "000000" : "CC0000");
			$tpl->newBlock("cia_prom");
			$tpl->assign("color", $color);
			$tpl->assign("num_cia", $data[$i]['num_cia']);
			$tpl->assign("nombre_cia", $data[$i]['nombre_corto']);
			$tpl->assign("prom_ant", $data[$i]['prom_ant'] != 0 ? number_format($data[$i]['prom_ant'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("prom_act", $data[$i]['prom_act'] != 0 ? number_format($data[$i]['prom_act'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("dif", $data[$i]['dif'] != 0 ? number_format($data[$i]['dif'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("inc", $data[$i]['inc'] != 0 ? number_format($data[$i]['inc'], 2, ".", ",") . "%" : "&nbsp;");

			$total_ant += $data[$i]['prom_ant'];
			$total_act += $data[$i]['prom_act'];
			$total_dif += $data[$i]['dif'];

			$numfilas++;
			if ($numfilas >= $numfilas_x_hoja)
				$tpl->assign("promedios.salto", "<br style=\"page-break-after:always;\">");
		}
		$tpl->newBlock("totales_prom");
		$tpl->assign("total_ant", number_format($total_ant, 2, ".", ","));
		$tpl->assign("total_act", number_format($total_act, 2, ".", ","));
		$tpl->assign("total_dif", number_format($total_dif, 2, ".", ","));

		// Código para diferencias mayores a 0 pero incrementos en 0%
		if ($sin_prom_cont > 0) {
			$tpl->assign("promedios.salto", "<br style=\"page-break-after:always;\">");

			$numfilas_x_hoja = 59;
			$numfilas = $numfilas_x_hoja;
			$idadmin = NULL;
			$total_ant = 0;
			$total_act = 0;
			$total_dif = 0;

			$sin_prom = FALSE;
			for ($i = 0; $i < count($cia); $i++) {
				if ($data[$i]['dif'] != 0 && $data[$i]['inc'] != 0) {
					continue;
				}

				if (($_GET['idadministrador'] == -1 && $idadmin != $data[$i]['idadministrador']) || $numfilas >= $numfilas_x_hoja) {
					if ($idadmin != NULL) {
						//$tpl->newBlock("totales_prom");
						//$tpl->assign("total_ant", number_format($total_ant, 2, ".", ","));
						//$tpl->assign("total_act", number_format($total_act, 2, ".", ","));
						//$tpl->assign("total_dif", number_format($total_dif, 2, ".", ","));
						$tpl->assign("promedios.salto", "<br style=\"page-break-after:always;\">");
					}

					$idadmin = $data[$i]['idadministrador'];

					$tpl->newBlock("promedios");
					$tpl->assign("campo", $title[$_GET['campo']]);
					$tpl->assign("mes", mes_escrito($_GET['mes']));
					$tpl->assign("anio", $_GET['anio']);
					$tpl->assign("anio_ant", $anio_ant);

					//$total_ant = 0;
					//$total_act = 0;
					//$total_dif = 0;

					$numfilas = 0;
				}
				$color = $data[$i]['inc'] === NULL ? "0000CC" : ($data[$i]['inc'] > 0 ? "000000" : "CC0000");
				$tpl->newBlock("cia_prom");
				$tpl->assign("color", $color);
				$tpl->assign("num_cia", $data[$i]['num_cia']);
				$tpl->assign("nombre_cia", $data[$i]['nombre_corto']);
				$tpl->assign("prom_ant", $data[$i]['prom_ant'] != 0 ? number_format($data[$i]['prom_ant'], 2, ".", ",") : "&nbsp;");
				$tpl->assign("prom_act", $data[$i]['prom_act'] != 0 ? number_format($data[$i]['prom_act'], 2, ".", ",") : "&nbsp;");
				$tpl->assign("dif", $data[$i]['dif'] != 0 ? number_format($data[$i]['dif'], 2, ".", ",") : "&nbsp;");
				$tpl->assign("inc", $data[$i]['inc'] != 0 ? number_format($data[$i]['inc'], 2, ".", ",") . "%" : "&nbsp;");

				$total_ant += $data[$i]['prom_ant'];
				$total_act += $data[$i]['prom_act'];
				$total_dif += $data[$i]['dif'];

				$numfilas++;
				if ($numfilas >= $numfilas_x_hoja)
					$tpl->assign("promedios.salto", "<br style=\"page-break-after:always;\">");
			}
			$tpl->newBlock("totales_prom");
			$tpl->assign("total_ant", number_format($total_ant, 2, ".", ","));
			$tpl->assign("total_act", number_format($total_act, 2, ".", ","));
			$tpl->assign("total_dif", number_format($total_dif, 2, ".", ","));
		}

		$tpl->printToScreen();
		die;
	}
}

$mes = date("m");
$anio = date("Y");

$tpl->newBlock("datos");
$tpl->assign(date("n", mktime(0, 0, 0, $mes, 0, $anio)), "selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, $mes, 0, $anio)));

$admin = $db->query("SELECT * FROM catalogo_administradores ORDER BY nombre_administrador");
for ($i = 0; $i < count($admin); $i++) {
	$tpl->newBlock("admin");
	$tpl->assign("id", $admin[$i]['idadministrador']);
	$tpl->assign("nombre", $admin[$i]['nombre_administrador']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
