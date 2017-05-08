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

		case 'generar_formato_pdf':
			$condiciones = array();

			$condiciones[] = "c.codgastos IN (49, 52)";

			if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != '')
			{
				$condiciones[] = "c.fecha >= '{$_REQUEST['fecha']}'";
			}
			else
			{
				$condiciones[] = "c.fecha = COALESCE((SELECT MAX(fecha) FROM cheques WHERE num_cia < 900 AND codgastos IN (49, 52)), NOW()::DATE)";
			}

			$condiciones[] = "c.fecha_cancelacion IS NULL";

			$condiciones[] = "c.importe > 0";

			$sql = "
				SELECT
					c.num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.rfc
						AS rfc_cia,
					c.concepto,
					EXTRACT(MONTH FROM c.fecha)
						AS mes,
					EXTRACT(YEAR FROM c.fecha)
						AS anio,
					pc.importe,
					pc.iva,
					pc.ret_iva,
					pc.isr,
					pc.total,
					c.folio,
					c.cuenta,
					cp.nombre
						AS nombre_pro,
					cp.rfc
						AS rfc_pro,
					cp.curp
						AS curp_pro,
					rep.nombre
						AS nombre_rep,
					rep.rfc
						AS rfc_rep,
					rep.curp
						AS curp_rep
				FROM
					cheques c
					LEFT JOIN pre_cheques pc
						USING (num_cia, num_proveedor, codgastos)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = c.num_proveedor)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = c.num_cia)
					LEFT JOIN catalogo_proveedores rep
						ON (rep.num_proveedor = cc.num_proveedor)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					nombre_pro,
					c.num_cia
			";

			$result = $db->query($sql);

			if ($result)
			{
				require_once('includes/fpdf/fpdf.php');
				require_once('includes/fpdi/fpdi.php');

				$pdf = new FPDI();

				$pagecount = $pdf->setSourceFile('forma-37.pdf');

				$tplidx = $pdf->importPage(1, '/MediaBox');

				foreach ($result as $row)
				{
					$pdf->addPage();
					$pdf->useTemplate($tplidx);

					$pdf->SetFont('Arial', '', 10);
					$pdf->SetTextColor(0, 0, 0);

					$pdf->SetXY(157, 30);
					$pdf->Write(0, $row['mes']);

					$pdf->SetXY(171, 30);
					$pdf->Write(0, $row['mes']);

					$pdf->SetXY(186, 30);
					$pdf->Write(0, $row['anio']);

					$pdf->SetXY(50, 45);
					$pdf->Write(0, $row['rfc_pro']);

					$pdf->SetXY(50, 51);
					$pdf->Write(0, $row['curp_pro']);

					$pdf->SetXY(50, 57);
					$pdf->Write(0, $row['nombre_pro']);

					$pdf->SetXY(70, 115);
					$pdf->Write(0, 'B1');

					$pdf->SetXY(82, 138);
					$pdf->Write(0, $row['importe'] > 0 ? number_format($row['importe'], 2) : '');

					$pdf->SetXY(125, 138);
					$pdf->Write(0, $row['importe'] > 0 ? number_format($row['importe'], 2) : '');

					$pdf->SetXY(82, 152);
					$pdf->Write(0, $row['isr'] > 0 ? number_format($row['isr'], 2) : '');

					$pdf->SetXY(125, 152);
					$pdf->Write(0, $row['iva'] > 0 ? number_format($row['iva'], 2) : '');

					$pdf->SetXY(55, 165);
					$pdf->Write(0, $row['rfc_cia']);

					$pdf->SetXY(55, 170);
					$pdf->Write(0, $row['nombre_cia']);

					$pdf->SetXY(55, 175);
					$pdf->Write(0, $row['nombre_rep']);

					$pdf->SetXY(55, 181);
					$pdf->Write(0, $row['rfc_rep']);

					$pdf->SetXY(140, 181);
					$pdf->Write(0, $row['curp_rep']);
				}

				$pdf->Output('pdfs/constancias-pagos-retenciones.pdf', 'F');
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ConstanciaPagosRetenciones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
