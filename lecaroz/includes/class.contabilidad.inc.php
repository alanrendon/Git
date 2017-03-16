<?php

class Contabilidad
{

	function __construct() {
	}

	public function registrar_factura($num_pro, $num_fact, $oficina)
	{
		$datos = $this->obtener_datos($num_pro, $num_fact, $oficina);

		$sql = $this->generar_scripts_insercion($datos);

		$this->registrar_datos($sql);
	}

	private function obtener_datos($num_pro, $num_fact, $oficina)
	{
		global $db;
		
		// Query para oficinas mollendo
		if ($oficina == 1)
		{
			$sql = "
				SELECT
					num_cia,
					num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					cp.rfc
						AS rfc_pro,
					f.num_fact,
					f.fecha,
					cp.calle,
					cp.no_exterior,
					cp.no_interior,
					cp.colonia,
					cp.localidad,
					cp.referencia,
					cp.municipio,
					cp.estado,
					cp.pais,
					cp.codigo_postal,
					cp.email1
						AS email,
					COALESCE(cmp.nombre, f.concepto)
						AS descripcion,
					COALESCE(d.precio, f.importe)
						AS precio,
					COALESCE(d.cantidad, 1)
						AS cantidad,
					COALESCE(d.piva, f.piva)
						AS iva,
					COALESCE(d.desc1, 0) + COALESCE(d.desc2, 0) + COALESCE(d.desc3, 0)
						AS descuento,
					CASE
						WHEN d.codmp IS NOT NULL THEN
							'C'
						WHEN f.codgastos IS NOT NULL THEN
							'G'
					END
						AS tipo,
					COALESCE(d.codmp, f.codgastos)
						AS codigo,
					COALESCE(d.ieps)
						AS ieps
				FROM
					pasivo_proveedores pp
					LEFT JOIN facturas f
						USING (num_cia, num_proveedor, num_fact)
					LEFT JOIN entrada_mp d
						USING (num_cia, num_proveedor, num_fact)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
				WHERE
					pp.num_proveedor = {$num_pro}
					AND pp.num_fact = '{$num_fact}'
				ORDER BY
					pp.id
			";

			$datos = $db->query($sql);

		}
		else if ($oficina == 2)
		{

		}

		if ($datos)
		{
			return $datos;
		}
		else
		{
			return NULL;
		}
	}

	private function generar_scripts_insercion($datos)
	{
		$sql = '';

		foreach ($datos as $i => $d)
		{
			if ($d['ieps'] > 0)
			{
				$ieps += $d['ieps'];
			}

			$sql .= "
				INSERT INTO
					i_invoice (
						i_invoice_id,
						ad_client_id,
						ad_org_id,
						createdby,
						updatedby,
						c_currency_id,
						bpartnervalue,
						name,
						postal,
						regionname,
						c_region_id,
						c_country_id,
						email,
						contactname,
						phone,
						doctypename,
						documentno,
						productvalue,
						linedescription,
						priceactual,
						dateinvoiced,
						qtyinvoiced,
						calle,
						noexterior,
						nointerior,
						colonia,
						localidad,
						referencia,
						municipio,
						bpartner_rfc,
						description,
						paymentrule,
						numctapago,
						paymenttermvalue,
						aduana,
						pedimento,
						fecha_pedimento,
						facturaefilename,
						isready,
						uomname,
						issotrx,
						poreference
					) VALUES (
						COALESCE((
							SELECT
								MAX(i_invoice_id)
							FROM
								i_invoice
						), 0) + 1,
						1000000,
						{$d['num_cia']},
						{$_SESSION['iduser']},
						{$_SESSION['iduser']},
						130,
						'{$d['rfc_pro']}',
						'{$d['nombre_pro']}',
						'{$d['codigo_postal']}',
						'{$d['estado']}',
						NULL,
						247,
						'{$d['email']}',
						'{$d['nombre_pro']}',
						NULL,
						'{$d['tipo']}-{$d['num_cia']}',
						'{$d['num_fact']}',
						'{$d['tipo']}-{$d['codigo']}',
						'{$d['descripcion']}',
						{$d['precio']},
						'{$d['fecha']}',
						'{$d['cantidad']}',
						'{$d['calle']}',
						'{$d['no_exterior']}',
						'{$d['no_interior']}',
						'{$d['colonia']}',
						'{$d['localidad']}',
						'{$d['referencia']}',
						'{$d['municipio']}',
						'{$d['rfc_pro']}',
						NULL,
						'4',
						NULL,
						0,
						NULL,
						NULL,
						NULL,
						NULL,
						" . ($i == count($datos) - 1 && $ieps == 0 ? "'Y'" : "'N'") . ",
						NULL,
						'N',
						'{$d['num_fact']}'
					)
			;\n";
			
			if ($d['ieps'] > 0)
			{
				$sql .= "
					INSERT INTO
						i_invoice (
							i_invoice_id,
							ad_client_id,
							ad_org_id,
							createdby,
							updatedby,
							c_currency_id,
							bpartnervalue,
							name,
							postal,
							regionname,
							c_region_id,
							c_country_id,
							email,
							contactname,
							phone,
							doctypename,
							documentno,
							productvalue,
							linedescription,
							priceactual,
							dateinvoiced,
							qtyinvoiced,
							calle,
							noexterior,
							nointerior,
							colonia,
							localidad,
							referencia,
							municipio,
							bpartner_rfc,
							description,
							paymentrule,
							numctapago,
							paymenttermvalue,
							aduana,
							pedimento,
							fecha_pedimento,
							facturaefilename,
							isready,
							uomname,
							issotrx,
							poreference
						) VALUES (
							COALESCE((
								SELECT
									MAX(i_invoice_id)
								FROM
									i_invoice
							), 0) + 1,
							1000000,
							{$d['num_cia']},
							{$_SESSION['iduser']},
							{$_SESSION['iduser']},
							130,
							'{$d['rfc_pro']}',
							'{$d['nombre_pro']}',
							'{$d['codigo_postal']}',
							'{$d['estado']}',
							NULL,
							247,
							'{$d['email']}',
							'{$d['nombre_pro']}',
							NULL,
							'{$d['tipo']}-{$d['num_cia']}',
							'{$d['num_fact']}',
							'IEPSC',
							'I.E.P.S.',
							{$ieps},
							'{$d['fecha']}',
							'1',
							'{$d['calle']}',
							'{$d['no_exterior']}',
							'{$d['no_interior']}',
							'{$d['colonia']}',
							'{$d['localidad']}',
							'{$d['referencia']}',
							'{$d['municipio']}',
							'{$d['rfc_pro']}',
							NULL,
							'4',
							NULL,
							0,
							NULL,
							NULL,
							NULL,
							NULL,
							'Y',
							NULL,
							'N',
							'{$d['num_fact']}'
						)
				;\n";
			}
		}


		return $sql;
	}

	private function registrar_datos($sql)
	{
		global $dbf;

		if ($sql != '')
		{
			$dbf->query($sql);
		}
	}

}