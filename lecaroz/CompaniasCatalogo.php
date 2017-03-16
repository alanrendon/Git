<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/CompaniasCatalogoInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_pro':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'consultar':
			$condiciones = array();

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = "ctc.tipo_usuario = {$_SESSION['tipo_usuario']}";
			}

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'cc.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "
				SELECT
					cc.num_cia,
					cc.nombre
						AS nombre_cia,
					cc.nombre_corto,
					cc.razon_social,
					cc.rfc,
					ctc.descripcion
						AS tipo_cia
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_tipos_compania ctc
						USING (tipo_cia)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					cc.num_cia
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/CompaniasCatalogoConsulta.tpl');
			$tpl->prepare();

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('nombre_corto', utf8_encode($row['nombre_corto']));
					$tpl->assign('razon_social', utf8_encode($row['razon_social']));
					$tpl->assign('rfc', utf8_encode($row['rfc']));
					$tpl->assign('tipo_cia', utf8_encode(strtoupper($row['tipo_cia'])));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/fac/CompaniasCatalogoAlta.tpl');
			$tpl->prepare();

			$tipos = $db->query('
				SELECT
					tipo_cia
						AS value,
					UPPER(descripcion)
						AS text
				FROM
					catalogo_tipos_compania
				' . ($_SESSION['iduser'] != 1 ? "WHERE tipo_usuario = {$_SESSION['tipo_usuario']}" : '') . '
				ORDER BY
					value
			');

			if ($tipos)
			{
				foreach ($tipos as $tipo)
				{
					$tpl->newBlock('tipo_cia');
					$tpl->assign('value', $tipo['value']);
					$tpl->assign('text', utf8_encode($tipo['text']));
				}
			}

			$del_imss = $db->query('
				SELECT
					iddelimss
						AS value,
					UPPER(nombre_del_imss)
						AS text
				FROM
					catalogo_del_imss
				ORDER BY
					value
			');

			if ($del_imss)
			{
				foreach ($del_imss as $del)
				{
					$tpl->newBlock('iddelimss');
					$tpl->assign('value', $del['value']);
					$tpl->assign('text', utf8_encode($del['text']));
				}
			}

			$subdel_imss = $db->query('
				SELECT
					idsubdelimss
						AS value,
					UPPER(nombre_subdel_imss)
						AS text
				FROM
					catalogo_subdel_imss
				ORDER BY
					value
			');

			if ($subdel_imss)
			{
				foreach ($subdel_imss as $subdel)
				{
					$tpl->newBlock('idsubdelimss');
					$tpl->assign('value', $subdel['value']);
					$tpl->assign('text', utf8_encode($subdel['text']));
				}
			}

			foreach (range(1, 3) as $cortes)
			{
				$tpl->newBlock('cortes');
				$tpl->assign('cortes', $cortes);
			}

			$administradores = $db->query('
				SELECT
					idadministrador
						AS value,
					UPPER(nombre_administrador)
						AS text
				FROM
					catalogo_administradores
				ORDER BY
					value
			');

			if ($administradores)
			{
				foreach ($administradores as $admin)
				{
					$tpl->newBlock('idadministrador');
					$tpl->assign('value', $admin['value']);
					$tpl->assign('text', utf8_encode($admin['text']));
				}
			}

			$operadoras = $db->query('
				SELECT
					idoperadora
						AS value,
					UPPER(nombre_operadora)
						AS text
				FROM
					catalogo_operadoras
				ORDER BY
					value
			');

			if ($operadoras)
			{
				foreach ($operadoras as $operadora)
				{
					$tpl->newBlock('idoperadora');
					$tpl->assign('value', $operadora['value']);
					$tpl->assign('text', utf8_encode($operadora['text']));
				}
			}

			$contadores = $db->query('
				SELECT
					idcontador
						AS value,
					UPPER(nombre_contador)
						AS text
				FROM
					catalogo_contadores
				ORDER BY
					value
			');

			if ($contadores)
			{
				foreach ($contadores as $contador)
				{
					$tpl->newBlock('idcontador');
					$tpl->assign('value', $contador['value']);
					$tpl->assign('text', utf8_encode($contador['text']));
				}
			}

			$auditores = $db->query('
				SELECT
					idauditor
						AS value,
					UPPER(nombre_auditor)
						AS text
				FROM
					catalogo_auditores
				ORDER BY
					value
			');

			if ($auditores)
			{
				foreach ($auditores as $auditor)
				{
					$tpl->newBlock('idauditor');
					$tpl->assign('value', $auditor['value']);
					$tpl->assign('text', utf8_encode($auditor['text']));
				}
			}

			$aseguradoras = $db->query('
				SELECT
					idaseguradora
						AS value,
					UPPER(nombre_aseguradora)
						AS text
				FROM
					catalogo_aseguradoras
				ORDER BY
					value
			');

			if ($aseguradoras)
			{
				foreach ($aseguradoras as $aseguradora)
				{
					$tpl->newBlock('idaseguradora');
					$tpl->assign('value', $aseguradora['value']);
					$tpl->assign('text', utf8_encode($aseguradora['text']));
				}
			}

			$sindicatos = $db->query('
				SELECT
					idsindicato
						AS value,
					UPPER(nombre_sindicato)
						AS text
				FROM
					catalogo_sindicatos
				ORDER BY
					value
			');

			if ($sindicatos)
			{
				foreach ($sindicatos as $sindicato)
				{
					$tpl->newBlock('idsindicato');
					$tpl->assign('value', $sindicato['value']);
					$tpl->assign('text', utf8_encode($sindicato['text']));
				}
			}

			$estados = $db->query('
				SELECT
					UPPER(SP_ASCII("Entidad"))
						AS estado
				FROM
					catalogo_entidades
				ORDER BY
					"IdEntidad"
			');

			if ($estados)
			{
				foreach ($estados as $estado)
				{
					$tpl->newBlock('estado');
					$tpl->assign('estado', utf8_encode($estado['estado']));
				}
			}

			$paises = $db->query("
				SELECT
					UPPER(SP_ASCII(pais))
						AS pais,
					CASE
						WHEN UPPER(SP_ASCII(pais)) = 'MEXICO' THEN
							1
						ELSE
							2
					END
						AS orden
				FROM
					catalogo_paises
				ORDER BY
					orden,
					pais
			");

			if ($paises)
			{
				foreach ($paises as $pais)
				{
					$tpl->newBlock('pais');
					$tpl->assign('pais', utf8_encode($pais['pais']));
				}
			}

			foreach (range(1, 31) as $dia)
			{
				$tpl->newBlock('dia_ven_luz');
				$tpl->assign('dia', $dia);
			}

			$logos = $db->query("SELECT id, nombre_imagen, descripcion FROM catalogo_logos_cfd ORDER BY id");

			if ($logos)
			{
				foreach ($logos as $l)
				{
					$tpl->newBlock('logo');
					$tpl->assign('value', $l['id']);
					$tpl->assign('text', utf8_encode($l['descripcion']));
					$tpl->assign('imagen', utf8_encode($l['nombre_imagen']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			if ($db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}"))
			{
				header('Content-Type: application/json');

				echo json_encode(array(
					'status'	=> -1
				));

				return FALSE;
			}

			$domicilio_partes = array();

			if (isset($_REQUEST['calle']) && $_REQUEST['calle'] != '')
			{
				$domicilio_partes[] = $_REQUEST['calle'];
			}

			if (isset($_REQUEST['no_exterior']) && $_REQUEST['no_exterior'] != '')
			{
				$domicilio_partes[] = $_REQUEST['no_exterior'];
			}

			if (isset($_REQUEST['no_interior']) && $_REQUEST['no_interior'] != '')
			{
				$domicilio_partes[] = $_REQUEST['no_interior'];
			}

			if (isset($_REQUEST['colonia']) && $_REQUEST['colonia'] != '')
			{
				$domicilio_partes[] = $_REQUEST['colonia'];
			}

			if (isset($_REQUEST['localidad']) && $_REQUEST['localidad'] != '')
			{
				$domicilio_partes[] = $_REQUEST['localidad'];
			}

			if (isset($_REQUEST['referencia']) && $_REQUEST['referencia'] != '')
			{
				$domicilio_partes[] = $_REQUEST['referencia'];
			}

			if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '')
			{
				$domicilio_partes[] = $_REQUEST['municipio'];
			}

			if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != '')
			{
				$domicilio_partes[] = $_REQUEST['estado'];
			}

			if (isset($_REQUEST['pais']) && $_REQUEST['pais'] != '')
			{
				$domicilio_partes[] = $_REQUEST['pais'];
			}

			if (isset($_REQUEST['codigo_postal']) && $_REQUEST['codigo_postal'] != '')
			{
				$domicilio_partes[] = $_REQUEST['codigo_postal'];
			}

			$domicilio = implode(', ', $domicilio_partes);

			$sql = "
				INSERT INTO
					catalogo_companias (
						num_cia,
						nombre,
						direccion,
						rfc,
						no_imss,
						no_infonavit,
						telefono,
						sub_cuenta_deudores,
						no_cta_cia_luz,
						persona_fis_moral,
						nombre_corto,
						idadministrador,
						idaseguradora,
						idauditor,
						idcontador,
						iddelimss,
						idoperadora,
						idsindicato,
						idsubdelimss,
						cod_gasolina,
						clabe_banco,
						clabe_plaza,
						clabe_cuenta,
						clabe_identificador,
						email,
						status,
						num_cia_primaria,
						aplica_iva,
						num_proveedor,
						clabe_banco2,
						clabe_plaza2,
						clabe_cuenta2,
						clabe_identificador2,
						med_agua,
						cliente_cometra,
						luz_esp,
						cortes_caja,
						aviso_saldo,
						dia_ven_luz,
						periodo_pago_luz,
						bim_par_imp_luz,
						ref,
						cia_aguinaldos,
						calle,
						no_exterior,
						no_interior,
						colonia,
						localidad,
						referencia,
						municipio,
						estado,
						pais,
						codigo_postal,
						num_cia_saldos,
						razon_social,
						turno_cometra,
						regimen_fiscal,
						por_bg,
						por_efectivo,
						num_cia_ros,
						por_bg_1,
						por_efectivo_1,
						por_bg_2,
						por_efectivo_2,
						por_bg_3,
						por_efectivo_3,
						por_bg_4,
						por_efectivo_4,
						tipo_cia,
						cia_fiscal_matriz,
						logo_cfd
					)
					VALUES (
						{$_REQUEST['num_cia']},
						'" . utf8_decode($_REQUEST['nombre']) . "',
						'" . utf8_decode($domicilio) . "',
						'" . utf8_decode($_REQUEST['rfc']) . "',
						" . (isset($_REQUEST['no_imss']) && $_REQUEST['no_imss'] != '' ? "'" . utf8_decode($_REQUEST['no_imss']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['no_infonavit']) && $_REQUEST['no_infonavit'] != '' ? "'" . utf8_decode($_REQUEST['no_infonavit']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['telefono']) && $_REQUEST['telefono'] != '' ? "'" . utf8_decode($_REQUEST['telefono']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['sub_cuenta_deudores']) && $_REQUEST['sub_cuenta_deudores'] > 0 ? $_REQUEST['sub_cuenta_deudores'] : 'NULL') . ",
						" . (isset($_REQUEST['no_cta_cia_luz']) && $_REQUEST['no_cta_cia_luz'] != '' ? "'" . utf8_decode($_REQUEST['no_cta_cia_luz']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['persona_fis_moral']) ? $_REQUEST['persona_fis_moral'] : 'FALSE') . ",
						'" . utf8_decode($_REQUEST['nombre_corto']) . "',
						{$_REQUEST['idadministrador']},
						{$_REQUEST['idaseguradora']},
						{$_REQUEST['idauditor']},
						{$_REQUEST['idcontador']},
						{$_REQUEST['iddelimss']},
						{$_REQUEST['idoperadora']},
						{$_REQUEST['idsindicato']},
						{$_REQUEST['idsubdelimss']},
						" . (isset($_REQUEST['cod_gasolina']) && $_REQUEST['cod_gasolina'] > 0 ? $_REQUEST['cod_gasolina'] : 'NULL') . ",
						" . (isset($_REQUEST['clabe_banco']) && $_REQUEST['clabe_banco'] != '' ? "'" . utf8_decode($_REQUEST['clabe_banco']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_plaza']) && $_REQUEST['clabe_plaza'] != '' ? "'" . utf8_decode($_REQUEST['clabe_plaza']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_cuenta']) && $_REQUEST['clabe_cuenta'] != '' ? "'" . utf8_decode($_REQUEST['clabe_cuenta']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_identificador']) && $_REQUEST['clabe_identificador'] != '' ? "'" . utf8_decode($_REQUEST['clabe_identificador']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['email']) && $_REQUEST['email'] != '' ? "'" . utf8_decode($_REQUEST['email']) . "'" : 'NULL') . ",
						TRUE,
						" . (isset($_REQUEST['num_cia_primaria']) && $_REQUEST['num_cia_primaria'] > 0 ? $_REQUEST['num_cia_primaria'] : $_REQUEST['num_cia']) . ",
						" . (isset($_REQUEST['aplica_iva']) ? $_REQUEST['aplica_iva'] : 'FALSE') . ",
						" . (isset($_REQUEST['num_proveedor']) && $_REQUEST['num_proveedor'] > 0 ? $_REQUEST['num_proveedor'] : 'NULL') . ",
						" . (isset($_REQUEST['clabe_banco2']) && $_REQUEST['clabe_banco2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_banco2']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_plaza2']) && $_REQUEST['clabe_plaza2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_plaza2']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_cuenta2']) && $_REQUEST['clabe_cuenta2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_cuenta2']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['clabe_identificador2']) && $_REQUEST['clabe_identificador2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_identificador2']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['med_agua']) ? $_REQUEST['med_agua'] : 'FALSE') . ",
						" . (isset($_REQUEST['cliente_cometra']) && $_REQUEST['cliente_cometra'] > 0 ? $_REQUEST['cliente_cometra'] : 'NULL') . ",
						" . (isset($_REQUEST['luz_esp']) ? $_REQUEST['luz_esp'] : 'FALSE') . ",
						" . (isset($_REQUEST['cortes_caja']) && $_REQUEST['cortes_caja'] > 0 ? $_REQUEST['cortes_caja'] : 'NULL') . ",
						" . (isset($_REQUEST['aviso_saldo']) ? $_REQUEST['aviso_saldo'] : 'FALSE') . ",
						" . (isset($_REQUEST['dia_ven_luz']) && $_REQUEST['dia_ven_luz'] > 0 ? $_REQUEST['dia_ven_luz'] : 'NULL') . ",
						" . (isset($_REQUEST['periodo_pago_luz']) && $_REQUEST['periodo_pago_luz'] > 0 ? $_REQUEST['periodo_pago_luz'] : 'NULL') . ",
						" . (isset($_REQUEST['bim_par_imp_luz']) && $_REQUEST['bim_par_imp_luz'] >= 0 ? $_REQUEST['bim_par_imp_luz'] : 'NULL') . ",
						" . (isset($_REQUEST['ref']) && $_REQUEST['ref'] != '' ? "'" . utf8_decode($_REQUEST['ref']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['cia_aguinaldos']) && $_REQUEST['cia_aguinaldos'] > 0 ? $_REQUEST['cia_aguinaldos'] : $_REQUEST['num_cia']) . ",
						" . (isset($_REQUEST['calle']) && $_REQUEST['calle'] != '' ? "'" . utf8_decode($_REQUEST['calle']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['no_exterior']) && $_REQUEST['no_exterior'] != '' ? "'" . utf8_decode($_REQUEST['no_exterior']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['no_interior']) && $_REQUEST['no_interior'] != '' ? "'" . utf8_decode($_REQUEST['no_interior']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['colonia']) && $_REQUEST['colonia'] != '' ? "'" . utf8_decode($_REQUEST['colonia']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['localidad']) && $_REQUEST['localidad'] != '' ? "'" . utf8_decode($_REQUEST['localidad']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['referencia']) && $_REQUEST['referencia'] != '' ? "'" . utf8_decode($_REQUEST['referencia']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '' ? "'" . utf8_decode($_REQUEST['municipio']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['estado']) && $_REQUEST['estado'] != '' ? "'" . utf8_decode($_REQUEST['estado']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['pais']) && $_REQUEST['pais'] != '' ? "'" . utf8_decode($_REQUEST['pais']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['codigo_postal']) && $_REQUEST['codigo_postal'] != '' ? "'" . utf8_decode($_REQUEST['codigo_postal']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['num_cia_saldos']) && $_REQUEST['num_cia_saldos'] > 0 ? $_REQUEST['num_cia_saldos'] : $_REQUEST['num_cia']) . ",
						'" . utf8_decode($_REQUEST['razon_social']) . "',
						" . (isset($_REQUEST['turno_cometra']) && $_REQUEST['turno_cometra'] > 0 ? $_REQUEST['turno_cometra'] : 1) . ",
						" . (isset($_REQUEST['regimen_fiscal']) && $_REQUEST['regimen_fiscal'] != '' ? "'" . utf8_decode($_REQUEST['regimen_fiscal']) . "'" : 'NULL') . ",
						" . (isset($_REQUEST['por_bg']) && $_REQUEST['por_bg'] > 0 ? $_REQUEST['por_bg'] : '0') . ",
						" . (isset($_REQUEST['por_efectivo']) && $_REQUEST['por_efectivo'] > 0 ? $_REQUEST['por_efectivo'] : '0') . ",
						" . (isset($_REQUEST['num_cia_ros']) && $_REQUEST['num_cia_ros'] > 0 ? $_REQUEST['num_cia_ros'] : $_REQUEST['num_cia']) . ",
						" . (isset($_REQUEST['por_bg_1']) && $_REQUEST['por_bg_1'] > 0 ? $_REQUEST['por_bg_1'] : '0') . ",
						" . (isset($_REQUEST['por_efectivo_1']) && $_REQUEST['por_efectivo_1'] > 0 ? $_REQUEST['por_efectivo_1'] : '0') . ",
						" . (isset($_REQUEST['por_bg_2']) && $_REQUEST['por_bg_2'] > 0 ? $_REQUEST['por_bg_2'] : '0') . ",
						" . (isset($_REQUEST['por_efectivo_2']) && $_REQUEST['por_efectivo_2'] > 0 ? $_REQUEST['por_efectivo_2'] : '0') . ",
						" . (isset($_REQUEST['por_bg_3']) && $_REQUEST['por_bg_3'] > 0 ? $_REQUEST['por_bg_3'] : '0') . ",
						" . (isset($_REQUEST['por_efectivo_3']) && $_REQUEST['por_efectivo_3'] > 0 ? $_REQUEST['por_efectivo_3'] : '0') . ",
						" . (isset($_REQUEST['por_bg_4']) && $_REQUEST['por_bg_4'] > 0 ? $_REQUEST['por_bg_4'] : '0') . ",
						" . (isset($_REQUEST['por_efectivo_4']) && $_REQUEST['por_efectivo_4'] > 0 ? $_REQUEST['por_efectivo_4'] : '0') . ",
						" . (isset($_REQUEST['tipo_cia']) && $_REQUEST['tipo_cia'] > 0 ? $_REQUEST['tipo_cia'] : '1') . ",
						" . (isset($_REQUEST['cia_fiscal_matriz']) && $_REQUEST['cia_fiscal_matriz'] > 0 ? $_REQUEST['cia_fiscal_matriz'] : $_REQUEST['num_cia']) . ",
						" . (isset($_REQUEST['logo_cfd']) && $_REQUEST['logo_cfd'] > 0 ? $_REQUEST['logo_cfd'] : '1') . "
					);
			";

			$sql .= "
				INSERT INTO
					saldos (
						num_cia,
						saldo_libros,
						saldo_bancos,
						cuenta
					) VALUES (
						{$_REQUEST['num_cia']},
						0,
						0,
						1
					);
			";

			$sql .= "
				INSERT INTO
					saldos (
						num_cia,
						saldo_libros,
						saldo_bancos,
						cuenta
					) VALUES (
						{$_REQUEST['num_cia']},
						0,
						0,
						2
					);
			";

			$db->query($sql);

			header('Content-Type: application/json');

			echo json_encode(array(
				'status'	=> 1
			));

			return FALSE;

			break;

		case 'modificar':
			$sql = '
				SELECT
					num_cia,
					nombre,
					direccion,
					rfc,
					no_imss,
					no_infonavit,
					telefono,
					sub_cuenta_deudores,
					no_cta_cia_luz,
					persona_fis_moral,
					nombre_corto,
					idadministrador,
					idaseguradora,
					idauditor,
					idcontador,
					iddelimss,
					idoperadora,
					idsindicato,
					idsubdelimss,
					cod_gasolina,
					clabe_banco,
					clabe_plaza,
					clabe_cuenta,
					clabe_identificador,
					email,
					homo_clave,
					status,
					num_cia_primaria,
					aplica_iva,
					num_proveedor,
					emisora,
					clabe_banco2,
					clabe_plaza2,
					clabe_cuenta2,
					clabe_identificador2,
					med_agua,
					cliente_cometra,
					luz_esp,
					cortes_caja,
					aviso_saldo,
					dia_ven_luz,
					periodo_pago_luz,
					bim_par_imp_luz,
					ref,
					cia_aguinaldos,
					calle,
					no_exterior,
					no_interior,
					colonia,
					localidad,
					referencia,
					municipio,
					estado,
					pais,
					codigo_postal,
					num_cia_saldos,
					razon_social,
					turno_cometra,
					regimen_fiscal,
					por_bg,
					por_efectivo,
					num_cia_ros,
					por_bg_1,
					por_efectivo_1,
					por_bg_2,
					por_efectivo_2,
					por_bg_3,
					por_efectivo_3,
					por_bg_4,
					por_efectivo_4,
					tipo_cia,
					cia_fiscal_matriz,
					logo_cfd
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			$row = $result[0];

			$tpl = new TemplatePower('plantillas/fac/CompaniasCatalogoModificar.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', $row['num_cia']);
			$tpl->assign('nombre', utf8_encode($row['nombre']));
			$tpl->assign('nombre_corto', utf8_encode($row['nombre_corto']));
			$tpl->assign('num_proveedor', $row['num_proveedor']);

			$tpl->assign('num_cia_primaria', $row['num_cia_primaria']);
			$tpl->assign('num_cia_saldos', $row['num_cia_saldos']);
			$tpl->assign('cia_aguinaldos', $row['cia_aguinaldos']);
			$tpl->assign('num_cia_ros', $row['num_cia_ros']);
			$tpl->assign('cia_fiscal_matriz', $row['cia_fiscal_matriz']);

			$tpl->assign('razon_social', utf8_encode($row['razon_social']));
			$tpl->assign('rfc', utf8_encode($row['rfc']));
			$tpl->assign('persona_fis_moral_' . ($row['persona_fis_moral'] != '' ? $row['persona_fis_moral'] : 'f'), ' checked="checked"');
			$tpl->assign('regimen_fiscal', utf8_encode($row['regimen_fiscal']));
			$tpl->assign('aplica_iva_' . ($row['aplica_iva'] != '' ? $row['aplica_iva'] : 'f'), ' checked="checked"');

			$tpl->assign('calle', utf8_encode($row['calle']));
			$tpl->assign('no_exterior', utf8_encode($row['no_exterior']));
			$tpl->assign('no_interior', utf8_encode($row['no_interior']));
			$tpl->assign('colonia', utf8_encode($row['colonia']));
			$tpl->assign('localidad', utf8_encode($row['localidad']));
			$tpl->assign('referencia', utf8_encode($row['referencia']));
			$tpl->assign('municipio', utf8_encode($row['municipio']));
			$tpl->assign('codigo_postal', utf8_encode($row['codigo_postal']));

			$tpl->assign('telefono', utf8_encode($row['telefono']));
			$tpl->assign('email', utf8_encode($row['email']));
			$tpl->assign('no_imss', utf8_encode($row['no_imss']));
			$tpl->assign('no_infonavit', utf8_encode($row['no_infonavit']));
			$tpl->assign('med_agua_' . ($row['med_agua'] != '' ? $row['med_agua'] : 'f'), ' checked="checked"');
			$tpl->assign('cod_gasolina', $row['cod_gasolina'] > 0 ? $row['cod_gasolina'] : '');
			$tpl->assign('no_cta_cia_luz', utf8_encode($row['no_cta_cia_luz']));
			$tpl->assign('luz_esp', $row['luz_esp'] == 't' ? ' checked="checked"' : '');
			$tpl->assign('periodo_pago_luz_' . ($row['periodo_pago_luz'] > 0 ? $row['periodo_pago_luz'] : '2'), ' checked="checked"');
			$tpl->assign('bim_par_imp_luz_' . ($row['bim_par_imp_luz'] >= 0 ? $row['bim_par_imp_luz'] : '1'), ' checked="checked"');
			$tpl->assign('sub_cuenta_deudores', $row['sub_cuenta_deudores'] > 0 ? $row['sub_cuenta_deudores'] : '');

			$tpl->assign('clabe_banco', utf8_encode($row['clabe_banco']));
			$tpl->assign('clabe_plaza', utf8_encode($row['clabe_plaza']));
			$tpl->assign('clabe_cuenta', utf8_encode($row['clabe_cuenta']));
			$tpl->assign('clabe_identificador', utf8_encode($row['clabe_identificador']));
			$tpl->assign('clabe_banco2', utf8_encode($row['clabe_banco2']));
			$tpl->assign('clabe_plaza2', utf8_encode($row['clabe_plaza2']));
			$tpl->assign('clabe_cuenta2', utf8_encode($row['clabe_cuenta2']));
			$tpl->assign('clabe_identificador2', utf8_encode($row['clabe_identificador2']));
			$tpl->assign('aviso_saldo_' . ($row['aviso_saldo'] != '' ? $row['aviso_saldo'] : 't'), ' checked="checked"');
			$tpl->assign('cliente_cometra', $row['cliente_cometra'] > 0 ? $row['cliente_cometra'] : '');
			$tpl->assign('turno_cometra_' . ($row['turno_cometra'] > 0 ? $row['turno_cometra'] : '1'), ' checked="checked"');
			$tpl->assign('ref', utf8_encode($row['ref']));

			$tpl->assign('por_bg', $row['por_bg'] != 0 ? number_format($row['por_bg'], 2) : '');
			$tpl->assign('por_efectivo', $row['por_efectivo'] != 0 ? number_format($row['por_efectivo'], 2) : '');
			$tpl->assign('por_bg_1', $row['por_bg_1'] != 0 ? number_format($row['por_bg_1'], 2) : '');
			$tpl->assign('por_efectivo_1', $row['por_efectivo_1'] != 0 ? number_format($row['por_efectivo_1'], 2) : '');
			$tpl->assign('por_bg_2', $row['por_bg_2'] != 0 ? number_format($row['por_bg_2'], 2) : '');
			$tpl->assign('por_efectivo_2', $row['por_efectivo_2'] != 0 ? number_format($row['por_efectivo_2'], 2) : '');
			$tpl->assign('por_bg_3', $row['por_bg_3'] != 0 ? number_format($row['por_bg_3'], 2) : '');
			$tpl->assign('por_efectivo_3', $row['por_efectivo_3'] != 0 ? number_format($row['por_efectivo_3'], 2) : '');
			$tpl->assign('por_bg_4', $row['por_bg_4'] != 0 ? number_format($row['por_bg_4'], 2) : '');
			$tpl->assign('por_efectivo_4', $row['por_efectivo_4'] != 0 ? number_format($row['por_efectivo_4'], 2) : '');

			$tipos = $db->query('
				SELECT
					tipo_cia
						AS value,
					UPPER(descripcion)
						AS text
				FROM
					catalogo_tipos_compania
					' . ($_SESSION['iduser'] != 1 ? "WHERE tipo_usuario = {$_SESSION['tipo_usuario']}" : '') . '
				ORDER BY
					value
			');

			if ($tipos)
			{
				foreach ($tipos as $tipo)
				{
					$tpl->newBlock('tipo_cia');
					$tpl->assign('value', $tipo['value']);
					$tpl->assign('text', utf8_encode($tipo['text']));

					if ($tipo['value'] == $row['tipo_cia'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$del_imss = $db->query('
				SELECT
					iddelimss
						AS value,
					UPPER(nombre_del_imss)
						AS text
				FROM
					catalogo_del_imss
				ORDER BY
					value
			');

			if ($del_imss)
			{
				foreach ($del_imss as $del)
				{
					$tpl->newBlock('iddelimss');
					$tpl->assign('value', $del['value']);
					$tpl->assign('text', utf8_encode($del['text']));

					if ($del['value'] == $row['iddelimss'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$subdel_imss = $db->query('
				SELECT
					idsubdelimss
						AS value,
					UPPER(nombre_subdel_imss)
						AS text
				FROM
					catalogo_subdel_imss
				ORDER BY
					value
			');

			if ($subdel_imss)
			{
				foreach ($subdel_imss as $subdel)
				{
					$tpl->newBlock('idsubdelimss');
					$tpl->assign('value', $subdel['value']);
					$tpl->assign('text', utf8_encode($subdel['text']));

					if ($subdel['value'] == $row['idsubdelimss'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			foreach (range(1, 3) as $cortes)
			{
				$tpl->newBlock('cortes');
				$tpl->assign('cortes', $cortes);

				if ($cortes == $row['cortes_caja'])
				{
					$tpl->assign('selected', ' selected="selected"');
				}
			}

			$administradores = $db->query('
				SELECT
					idadministrador
						AS value,
					UPPER(nombre_administrador)
						AS text
				FROM
					catalogo_administradores
				ORDER BY
					value
			');

			if ($administradores)
			{
				foreach ($administradores as $admin)
				{
					$tpl->newBlock('idadministrador');
					$tpl->assign('value', $admin['value']);
					$tpl->assign('text', utf8_encode($admin['text']));

					if ($admin['value'] == $row['idadministrador'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$operadoras = $db->query('
				SELECT
					idoperadora
						AS value,
					UPPER(nombre_operadora)
						AS text
				FROM
					catalogo_operadoras
				ORDER BY
					value
			');

			if ($operadoras)
			{
				foreach ($operadoras as $operadora)
				{
					$tpl->newBlock('idoperadora');
					$tpl->assign('value', $operadora['value']);
					$tpl->assign('text', utf8_encode($operadora['text']));

					if ($operadora['value'] == $row['idoperadora'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$contadores = $db->query('
				SELECT
					idcontador
						AS value,
					UPPER(nombre_contador)
						AS text
				FROM
					catalogo_contadores
				ORDER BY
					value
			');

			if ($contadores)
			{
				foreach ($contadores as $contador)
				{
					$tpl->newBlock('idcontador');
					$tpl->assign('value', $contador['value']);
					$tpl->assign('text', utf8_encode($contador['text']));

					if ($contador['value'] == $row['idcontador'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$auditores = $db->query('
				SELECT
					idauditor
						AS value,
					UPPER(nombre_auditor)
						AS text
				FROM
					catalogo_auditores
				ORDER BY
					value
			');

			if ($auditores)
			{
				foreach ($auditores as $auditor)
				{
					$tpl->newBlock('idauditor');
					$tpl->assign('value', $auditor['value']);
					$tpl->assign('text', utf8_encode($auditor['text']));

					if ($auditor['value'] == $row['idauditor'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$aseguradoras = $db->query('
				SELECT
					idaseguradora
						AS value,
					UPPER(nombre_aseguradora)
						AS text
				FROM
					catalogo_aseguradoras
				ORDER BY
					value
			');

			if ($aseguradoras)
			{
				foreach ($aseguradoras as $aseguradora)
				{
					$tpl->newBlock('idaseguradora');
					$tpl->assign('value', $aseguradora['value']);
					$tpl->assign('text', utf8_encode($aseguradora['text']));

					if ($aseguradora['value'] == $row['idaseguradora'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$sindicatos = $db->query('
				SELECT
					idsindicato
						AS value,
					UPPER(nombre_sindicato)
						AS text
				FROM
					catalogo_sindicatos
				ORDER BY
					value
			');

			if ($sindicatos)
			{
				foreach ($sindicatos as $sindicato)
				{
					$tpl->newBlock('idsindicato');
					$tpl->assign('value', $sindicato['value']);
					$tpl->assign('text', utf8_encode($sindicato['text']));

					if ($sindicato['value'] == $row['idsindicato'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$estados = $db->query('
				SELECT
					UPPER(SP_ASCII("Entidad"))
						AS estado
				FROM
					catalogo_entidades
				ORDER BY
					"IdEntidad"
			');

			if ($estados)
			{
				foreach ($estados as $estado)
				{
					$tpl->newBlock('estado');
					$tpl->assign('estado', utf8_encode($estado['estado']));

					if ($estado['estado'] == $row['estado'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			$paises = $db->query("
				SELECT
					UPPER(SP_ASCII(pais))
						AS pais,
					CASE
						WHEN UPPER(SP_ASCII(pais)) = 'MEXICO' THEN
							1
						ELSE
							2
					END
						AS orden
				FROM
					catalogo_paises
				ORDER BY
					orden,
					pais
			");

			if ($paises)
			{
				foreach ($paises as $pais)
				{
					$tpl->newBlock('pais');
					$tpl->assign('pais', utf8_encode($pais['pais']));

					if ($pais['pais'] == $row['pais'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			foreach (range(1, 31) as $dia)
			{
				$tpl->newBlock('dia_ven_luz');
				$tpl->assign('dia', $dia);

				if ($dia == $row['dia_ven_luz'])
				{
					$tpl->assign('selected', ' selected="selected"');
				}
			}

			$logos = $db->query("SELECT id, nombre_imagen, descripcion FROM catalogo_logos_cfd ORDER BY id");

			if ($logos)
			{
				foreach ($logos as $l)
				{
					$tpl->newBlock('logo');
					$tpl->assign('value', $l['id']);
					$tpl->assign('text', utf8_encode($l['descripcion']));
					$tpl->assign('imagen', utf8_encode($l['nombre_imagen']));

					if ($l['id'] == $row['logo_cfd'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$domicilio_partes = array();

			if (isset($_REQUEST['calle']) && $_REQUEST['calle'] != '')
			{
				$domicilio_partes[] = $_REQUEST['calle'];
			}

			if (isset($_REQUEST['no_exterior']) && $_REQUEST['no_exterior'] != '')
			{
				$domicilio_partes[] = $_REQUEST['no_exterior'];
			}

			if (isset($_REQUEST['no_interior']) && $_REQUEST['no_interior'] != '')
			{
				$domicilio_partes[] = $_REQUEST['no_interior'];
			}

			if (isset($_REQUEST['colonia']) && $_REQUEST['colonia'] != '')
			{
				$domicilio_partes[] = $_REQUEST['colonia'];
			}

			if (isset($_REQUEST['localidad']) && $_REQUEST['localidad'] != '')
			{
				$domicilio_partes[] = $_REQUEST['localidad'];
			}

			if (isset($_REQUEST['referencia']) && $_REQUEST['referencia'] != '')
			{
				$domicilio_partes[] = $_REQUEST['referencia'];
			}

			if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '')
			{
				$domicilio_partes[] = $_REQUEST['municipio'];
			}

			if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != '')
			{
				$domicilio_partes[] = $_REQUEST['estado'];
			}

			if (isset($_REQUEST['pais']) && $_REQUEST['pais'] != '')
			{
				$domicilio_partes[] = $_REQUEST['pais'];
			}

			if (isset($_REQUEST['codigo_postal']) && $_REQUEST['codigo_postal'] != '')
			{
				$domicilio_partes[] = $_REQUEST['codigo_postal'];
			}

			$domicilio = implode(', ', $domicilio_partes);

			$sql = "
				UPDATE
					catalogo_companias
				SET
					nombre = '" . utf8_decode($_REQUEST['nombre']) . "',
					direccion = '" . utf8_decode($domicilio) . "',
					rfc = '" . utf8_decode($_REQUEST['rfc']) . "',
					no_imss = " . (isset($_REQUEST['no_imss']) && $_REQUEST['no_imss'] != '' ? "'" . utf8_decode($_REQUEST['no_imss']) . "'" : 'NULL') . ",
					no_infonavit = " . (isset($_REQUEST['no_infonavit']) && $_REQUEST['no_infonavit'] != '' ? "'" . utf8_decode($_REQUEST['no_infonavit']) . "'" : 'NULL') . ",
					telefono = " . (isset($_REQUEST['telefono']) && $_REQUEST['telefono'] != '' ? "'" . utf8_decode($_REQUEST['telefono']) . "'" : 'NULL') . ",
					sub_cuenta_deudores = " . (isset($_REQUEST['sub_cuenta_deudores']) && $_REQUEST['sub_cuenta_deudores'] > 0 ? $_REQUEST['sub_cuenta_deudores'] : 'NULL') . ",
					no_cta_cia_luz = " . (isset($_REQUEST['no_cta_cia_luz']) && $_REQUEST['no_cta_cia_luz'] != '' ? "'" . utf8_decode($_REQUEST['no_cta_cia_luz']) . "'" : 'NULL') . ",
					persona_fis_moral = " . (isset($_REQUEST['persona_fis_moral']) ? $_REQUEST['persona_fis_moral'] : 'FALSE') . ",
					nombre_corto = '" . utf8_decode($_REQUEST['nombre_corto']) . "',
					idadministrador = {$_REQUEST['idadministrador']},
					idaseguradora = {$_REQUEST['idaseguradora']},
					idauditor = {$_REQUEST['idauditor']},
					idcontador = {$_REQUEST['idcontador']},
					iddelimss = {$_REQUEST['iddelimss']},
					idoperadora = {$_REQUEST['idoperadora']},
					idsindicato = {$_REQUEST['idsindicato']},
					idsubdelimss = {$_REQUEST['idsubdelimss']},
					cod_gasolina = " . (isset($_REQUEST['cod_gasolina']) && $_REQUEST['cod_gasolina'] > 0 ? $_REQUEST['cod_gasolina'] : 'NULL') . ",
					clabe_banco = " . (isset($_REQUEST['clabe_banco']) && $_REQUEST['clabe_banco'] != '' ? "'" . utf8_decode($_REQUEST['clabe_banco']) . "'" : 'NULL') . ",
					clabe_plaza = " . (isset($_REQUEST['clabe_plaza']) && $_REQUEST['clabe_plaza'] != '' ? "'" . utf8_decode($_REQUEST['clabe_plaza']) . "'" : 'NULL') . ",
					clabe_cuenta = " . (isset($_REQUEST['clabe_cuenta']) && $_REQUEST['clabe_cuenta'] != '' ? "'" . utf8_decode($_REQUEST['clabe_cuenta']) . "'" : 'NULL') . ",
					clabe_identificador = " . (isset($_REQUEST['clabe_identificador']) && $_REQUEST['clabe_identificador'] != '' ? "'" . utf8_decode($_REQUEST['clabe_identificador']) . "'" : 'NULL') . ",
					email = " . (isset($_REQUEST['email']) && $_REQUEST['email'] != '' ? "'" . utf8_decode($_REQUEST['email']) . "'" : 'NULL') . ",
					num_cia_primaria = " . (isset($_REQUEST['num_cia_primaria']) && $_REQUEST['num_cia_primaria'] > 0 ? $_REQUEST['num_cia_primaria'] : $_REQUEST['num_cia']) . ",
					aplica_iva = " . (isset($_REQUEST['aplica_iva']) ? $_REQUEST['aplica_iva'] : 'FALSE') . ",
					num_proveedor = " . (isset($_REQUEST['num_proveedor']) && $_REQUEST['num_proveedor'] > 0 ? $_REQUEST['num_proveedor'] : 'NULL') . ",
					clabe_banco2 = " . (isset($_REQUEST['clabe_banco2']) && $_REQUEST['clabe_banco2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_banco2']) . "'" : 'NULL') . ",
					clabe_plaza2 = " . (isset($_REQUEST['clabe_plaza2']) && $_REQUEST['clabe_plaza2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_plaza2']) . "'" : 'NULL') . ",
					clabe_cuenta2 = " . (isset($_REQUEST['clabe_cuenta2']) && $_REQUEST['clabe_cuenta2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_cuenta2']) . "'" : 'NULL') . ",
					clabe_identificador2 = " . (isset($_REQUEST['clabe_identificador2']) && $_REQUEST['clabe_identificador2'] != '' ? "'" . utf8_decode($_REQUEST['clabe_identificador2']) . "'" : 'NULL') . ",
					med_agua = " . (isset($_REQUEST['med_agua']) ? $_REQUEST['med_agua'] : 'FALSE') . ",
					cliente_cometra = " . (isset($_REQUEST['cliente_cometra']) && $_REQUEST['cliente_cometra'] > 0 ? $_REQUEST['cliente_cometra'] : 'NULL') . ",
					luz_esp = " . (isset($_REQUEST['luz_esp']) ? $_REQUEST['luz_esp'] : 'FALSE') . ",
					cortes_caja = " . (isset($_REQUEST['cortes_caja']) && $_REQUEST['cortes_caja'] > 0 ? $_REQUEST['cortes_caja'] : 'NULL') . ",
					aviso_saldo = " . (isset($_REQUEST['aviso_saldo']) ? $_REQUEST['aviso_saldo'] : 'FALSE') . ",
					dia_ven_luz = " . (isset($_REQUEST['dia_ven_luz']) && $_REQUEST['dia_ven_luz'] > 0 ? $_REQUEST['dia_ven_luz'] : 'NULL') . ",
					periodo_pago_luz = " . (isset($_REQUEST['periodo_pago_luz']) && $_REQUEST['periodo_pago_luz'] > 0 ? $_REQUEST['periodo_pago_luz'] : 'NULL') . ",
					bim_par_imp_luz = " . (isset($_REQUEST['bim_par_imp_luz']) && $_REQUEST['bim_par_imp_luz'] >= 0 ? $_REQUEST['bim_par_imp_luz'] : 'NULL') . ",
					ref = " . (isset($_REQUEST['ref']) && $_REQUEST['ref'] != '' ? "'" . utf8_decode($_REQUEST['ref']) . "'" : 'NULL') . ",
					cia_aguinaldos = " . (isset($_REQUEST['cia_aguinaldos']) && $_REQUEST['cia_aguinaldos'] > 0 ? $_REQUEST['cia_aguinaldos'] : $_REQUEST['num_cia']) . ",
					calle = " . (isset($_REQUEST['calle']) && $_REQUEST['calle'] != '' ? "'" . utf8_decode($_REQUEST['calle']) . "'" : 'NULL') . ",
					no_exterior = " . (isset($_REQUEST['no_exterior']) && $_REQUEST['no_exterior'] != '' ? "'" . utf8_decode($_REQUEST['no_exterior']) . "'" : 'NULL') . ",
					no_interior = " . (isset($_REQUEST['no_interior']) && $_REQUEST['no_interior'] != '' ? "'" . utf8_decode($_REQUEST['no_interior']) . "'" : 'NULL') . ",
					colonia = " . (isset($_REQUEST['colonia']) && $_REQUEST['colonia'] != '' ? "'" . utf8_decode($_REQUEST['colonia']) . "'" : 'NULL') . ",
					localidad = " . (isset($_REQUEST['localidad']) && $_REQUEST['localidad'] != '' ? "'" . utf8_decode($_REQUEST['localidad']) . "'" : 'NULL') . ",
					referencia = " . (isset($_REQUEST['referencia']) && $_REQUEST['referencia'] != '' ? "'" . utf8_decode($_REQUEST['referencia']) . "'" : 'NULL') . ",
					municipio = " . (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '' ? "'" . utf8_decode($_REQUEST['municipio']) . "'" : 'NULL') . ",
					estado = " . (isset($_REQUEST['estado']) && $_REQUEST['estado'] != '' ? "'" . utf8_decode($_REQUEST['estado']) . "'" : 'NULL') . ",
					pais = " . (isset($_REQUEST['pais']) && $_REQUEST['pais'] != '' ? "'" . utf8_decode($_REQUEST['pais']) . "'" : 'NULL') . ",
					codigo_postal = " . (isset($_REQUEST['codigo_postal']) && $_REQUEST['codigo_postal'] != '' ? "'" . utf8_decode($_REQUEST['codigo_postal']) . "'" : 'NULL') . ",
					num_cia_saldos = " . (isset($_REQUEST['num_cia_saldos']) && $_REQUEST['num_cia_saldos'] > 0 ? $_REQUEST['num_cia_saldos'] : $_REQUEST['num_cia']) . ",
					razon_social = '" . utf8_decode($_REQUEST['razon_social']) . "',
					turno_cometra = " . (isset($_REQUEST['turno_cometra']) && $_REQUEST['turno_cometra'] > 0 ? $_REQUEST['turno_cometra'] : 1) . ",
					regimen_fiscal = " . (isset($_REQUEST['regimen_fiscal']) && $_REQUEST['regimen_fiscal'] != '' ? "'" . utf8_decode($_REQUEST['regimen_fiscal']) . "'" : 'NULL') . ",
					por_bg = " . (isset($_REQUEST['por_bg']) && $_REQUEST['por_bg'] > 0 ? $_REQUEST['por_bg'] : '0') . ",
					por_efectivo = " . (isset($_REQUEST['por_efectivo']) && $_REQUEST['por_efectivo'] > 0 ? $_REQUEST['por_efectivo'] : '0') . ",
					num_cia_ros = " . (isset($_REQUEST['num_cia_ros']) && $_REQUEST['num_cia_ros'] > 0 ? $_REQUEST['num_cia_ros'] : $_REQUEST['num_cia']) . ",
					por_bg_1 = " . (isset($_REQUEST['por_bg_1']) && $_REQUEST['por_bg_1'] > 0 ? $_REQUEST['por_bg_1'] : '0') . ",
					por_efectivo_1 = " . (isset($_REQUEST['por_efectivo_1']) && $_REQUEST['por_efectivo_1'] > 0 ? $_REQUEST['por_efectivo_1'] : '0') . ",
					por_bg_2 = " . (isset($_REQUEST['por_bg_2']) && $_REQUEST['por_bg_2'] > 0 ? $_REQUEST['por_bg_2'] : '0') . ",
					por_efectivo_2 = " . (isset($_REQUEST['por_efectivo_2']) && $_REQUEST['por_efectivo_2'] > 0 ? $_REQUEST['por_efectivo_2'] : '0') . ",
					por_bg_3 = " . (isset($_REQUEST['por_bg_3']) && $_REQUEST['por_bg_3'] > 0 ? $_REQUEST['por_bg_3'] : '0') . ",
					por_efectivo_3 = " . (isset($_REQUEST['por_efectivo_3']) && $_REQUEST['por_efectivo_3'] > 0 ? $_REQUEST['por_efectivo_3'] : '0') . ",
					por_bg_4 = " . (isset($_REQUEST['por_bg_4']) && $_REQUEST['por_bg_4'] > 0 ? $_REQUEST['por_bg_4'] : '0') . ",
					por_efectivo_4 = " . (isset($_REQUEST['por_efectivo_4']) && $_REQUEST['por_efectivo_4'] > 0 ? $_REQUEST['por_efectivo_4'] : '0') . ",
					tipo_cia = " . (isset($_REQUEST['tipo_cia']) && $_REQUEST['tipo_cia'] > 0 ? $_REQUEST['tipo_cia'] : '1') . ",
					cia_fiscal_matriz = " . (isset($_REQUEST['cia_fiscal_matriz']) && $_REQUEST['cia_fiscal_matriz'] > 0 ? $_REQUEST['cia_fiscal_matriz'] : $_REQUEST['num_cia']) . ",
					logo_cfd = " . (isset($_REQUEST['logo_cfd']) && $_REQUEST['logo_cfd'] > 0 ? $_REQUEST['logo_cfd'] : '1') . "
				WHERE
					num_cia = {$_REQUEST['num_cia']};
			";

			$db->query($sql);

			break;

		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_tanques
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/CompaniasCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
