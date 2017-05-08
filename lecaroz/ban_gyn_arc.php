<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = 'El archivo contiene más columnas de las permitidas';
$descripcion_error[2] = 'La compañía en la línea ' . (isset($_REQUEST['line']) ? $_REQUEST['line'] : '') . ' no tiene número de cuenta';

if (isset($_GET['pro'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[pro]";
	$result = $db->query($sql);

	die($result ? $result[0]['nombre'] : '');
}

if (isset($_GET['cod'])) {
	$sql = "SELECT descripcion AS desc FROM catalogo_gastos WHERE codgastos = $_GET[cod]";
	$result = $db->query($sql);

	die($result ? $result[0]['desc'] : '');
}

if (isset($_FILES['archivo'])) {
	$fp = fopen($_FILES['archivo']['tmp_name'], "r");

	$fecha = $_POST['fecha'];
	$codgastos = $_POST['codgastos'];
	$concepto = strtoupper(trim($_POST['concepto']));
	$cuenta = $_POST['cuenta'];

	$cont = 0;
	$sql = '';
	while (!feof($fp)) {
		$buffer = fgets($fp);

		if (trim($buffer) != '') {
			$data = split(',', $buffer);

			if (count($data) > 2)
				die(header('location: ./ban_gyn_arc.php?codigo_error=1'));

			if (get_val($data[0]) > 0) {
				$num_cia = get_val($data[0]);
				$importe = floatval(preg_replace('/[^\d\.]/', '', $data[1]));

				//if ($codgastos == 134) {
					$int_part = floor($importe);
					$dec_part = round($importe, 2) - $int_part;

					$pow = $dec_part * 100;

					$new_dec_part = $pow % 5 == 0 ? $pow / 100 : round($pow / 10) / 10;

					$importe = $int_part + $new_dec_part;
				//}

				if ($importe > 0) {
					$num_cuenta = $db->query("SELECT TRIM(" . ($cuenta == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . ") AS num_cuenta FROM catalogo_companias WHERE num_cia = {$num_cia}");

					if (strlen($num_cuenta[0]['num_cuenta']) != 11)
					{
						die(header('location: ./ban_gyn_arc.php?codigo_error=2&line=' . ($cont + 1)));
					}

					if (!isset($folio_cheque[$num_cia])) {
						$result = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = $cuenta ORDER BY folio DESC LIMIT 1");
						$folio_cheque[$num_cia] = $result ? $result[0]['folio'] + 1 : 51;
					}

					// Actualizar saldo en libros
					if ($id = $db->query("SELECT id FROM saldos WHERE num_cia = $num_cia AND cuenta = $cuenta"))
						$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe WHERE id = {$id[0]['id']};\n";

					if ($_POST['num_pro'] > 0) {
						$pro = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores WHERE num_proveedor = $_POST[num_pro]");
					}
					else {
						if ( ! ($pro = $db->query("SELECT num_proveedor AS num_pro, cp.nombre FROM catalogo_companias LEFT JOIN catalogo_proveedores cp USING (num_proveedor) WHERE num_cia = $num_cia"))) {
							$pro[0]['num_pro'] = 12;
						}
					}

					if (!($pro[0]['num_pro'] > 0))
						continue;

					$sql .= 'INSERT INTO cheques (cod_mov, codgastos, num_proveedor, num_cia, a_nombre, concepto, fecha, folio, importe, iduser, imp, cuenta, poliza, archivo) VALUES (';
					$sql .= "5, $codgastos, {$pro[0]['num_pro']}, $num_cia, '{$pro[0]['nombre']}', '$concepto', '$fecha', $folio_cheque[$num_cia], $importe, $_SESSION[iduser], 'FALSE', $cuenta, 'FALSE', 'TRUE');\n";

					$sql .= 'INSERT INTO estado_cuenta (num_cia, fecha, concepto, tipo_mov, importe, cod_mov, folio, cuenta, iduser) VALUES (';
					$sql .= "$num_cia, '$fecha', '$concepto', 'TRUE', $importe, 5, $folio_cheque[$num_cia], $cuenta, $_SESSION[iduser]);\n";

					$sql .= 'INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES (';
					$sql .= "$folio_cheque[$num_cia], $num_cia, 'FALSE', 'TRUE', '$fecha', $cuenta);\n";

					$sql .= 'INSERT INTO movimiento_gastos (num_cia, codgastos, fecha, importe, concepto, captura, folio, cuenta) VALUES (';
					$sql .= "$num_cia, $codgastos, '$fecha', $importe, '$concepto', 'TRUE', $folio_cheque[$num_cia], $cuenta);\n";

					$folio_cheque[$num_cia]++;
					$cont++;
				}
			}
		}
	}
	fclose($fp);

	if ($sql != '') $db->query($sql);

	die(header('location: ./ban_gyn_arc.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_gyn_arc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock('datos');
$tpl->assign('fecha', date('d/m/Y'));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
