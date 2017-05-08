<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(18))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$partial_date = date('/m/Y');

$sql = "SELECT num_cia, nombre_corto, CASE WHEN extract(month from now()::date) = 2 AND dia_ven_luz > 28 THEN 28 ELSE dia_ven_luz END AS dia_ven_luz FROM catalogo_companias WHERE num_cia <= 300 AND periodo_pago_luz = 1 AND dia_ven_luz IS NOT NULL AND dia_ven_luz > 0 AND cast((CASE WHEN extract(month from now()::date) = 2 AND dia_ven_luz > 28 THEN 28 ELSE dia_ven_luz END) || '$partial_date' as date) BETWEEN CURRENT_DATE - interval '20 days' AND CURRENT_DATE + interval '5 days'
UNION
SELECT num_cia, nombre_corto, dia_ven_luz FROM catalogo_companias WHERE num_cia <= 300 AND periodo_pago_luz = 2 AND bim_par_imp_luz = cast(extract(month from CURRENT_DATE) as integer) % 2 AND dia_ven_luz IS NOT NULL AND dia_ven_luz > 0 AND cast((CASE WHEN extract(month from now()::date) = 2 AND dia_ven_luz > 28 THEN 28 ELSE dia_ven_luz END) || '$partial_date' as date) BETWEEN CURRENT_DATE - interval '20 days' AND CURRENT_DATE + interval '5 days' ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verPenLuz.tpl" );
$tpl->prepare();

$data = '';
foreach ($result as $reg) {
	if (!$db->query("SELECT id FROM cheques WHERE num_cia = $reg[num_cia] AND codgastos = 12 AND fecha BETWEEN cast('$reg[dia_ven_luz]$partial_date' as date) - interval '15 days' AND cast('$reg[dia_ven_luz]$partial_date' as date) + interval '5 days' AND fecha_cancelacion IS NULL")) {
		$data  .= "$reg[num_cia] $reg[nombre_corto]\n";
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('fecha', $reg['dia_ven_luz'] . $partial_date);
	}
}

if ($data != '') die($tpl->getOutputContent());
?>