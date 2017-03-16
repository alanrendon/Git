<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	/*
	* Insertar registro de locales que ya estan vacios solo como informativo
	*/
	INSERT INTO
		rentas_locales
		 (
			id_catalogo_arrendatarios,
			idarrendador,
			domicilio,
			tipo_local,
			superficie,
			iduser_baja,
			tsbaja
		 )
	
	SELECT
		id
			AS id_catalogo_arrendatarios,
		(
			SELECT
				idarrendador
			FROM
				rentas_arrendadores
			WHERE
				arrendador = arr.cod_arrendador
		)
			AS idarrendador,
		TRIM(regexp_replace(COALESCE(direccion_local, \'\'), \'\s+\', \' \', \'g\'))
			AS domicilio,
		tipo_local,
		COALESCE(metros_cuadrados, 0)
			AS superficie,
		1
			AS iduser_baja,
		NOW()
			AS tsbaja
	FROM
		catalogo_arrendatarios arr
	WHERE
		status = 0
	ORDER BY
		cod_arrendador,
		num_local;
	
	/*
	* Insertar locales activos
	*/
	INSERT INTO
		rentas_locales
		 (
			id_catalogo_arrendatarios,
			idarrendador,
			domicilio,
			tipo_local,
			superficie
		 )
	
	SELECT
		id
			AS id_catalogo_arrendatarios,
		(
			SELECT
				idarrendador
			FROM
				rentas_arrendadores
			WHERE
				arrendador = arr.cod_arrendador
		)
			AS idarrendador,
		TRIM(regexp_replace(COALESCE(direccion_local, \'\'), \'\s+\', \' \', \'g\'))
			AS domicilio,
		tipo_local,
		COALESCE(metros_cuadrados, 0)
			AS superficie
	FROM
		catalogo_arrendatarios arr
	WHERE
		status = 1
	ORDER BY
		cod_arrendador,
		num_local;
';

$db->query($sql);

$sql = '
	SELECT
		idlocal,
		idarrendador
	FROM
		rentas_locales
	WHERE
		tsbaja IS NULL
	ORDER BY
		idarrendador,
		idlocal
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$local = 1;
		}
		
		$sql .= 'UPDATE rentas_locales SET local = ' . $local . ', alias_local = \'LOCAL ' . $local . '\' WHERE idlocal = ' . $rec['idlocal'] . ";\n";
		
		$local++;
	}
	
	$db->query($sql);
}

$sql = '
	SELECT
		idlocal,
		idarrendador
	FROM
		rentas_locales
	WHERE
		tsbaja IS NOT NULL
	ORDER BY
		idarrendador,
		idlocal
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$local = 1;
		}
		
		$sql .= 'UPDATE rentas_locales SET local = ' . $local . ', alias_local = \'LOCAL ' . $local . '\' WHERE idlocal = ' . $rec['idlocal'] . ";\n";
		
		$local++;
	}
	
	$db->query($sql);
}

$sql = '
	INSERT INTO
		rentas_arrendatarios
			(
				id_catalogo_arrendatarios,
				idarrendador,
				idlocal,
				bloque,
				alias_arrendatario,
				nombre_arrendatario,
				rfc,
				tipo_persona,
				calle,
				no_exterior,
				no_interior,
				colonia,
				municipio,
				estado,
				pais,
				codigo_postal,
				contacto,
				telefono1,
				telefono2,
				email,
				giro,
				representante,
				fianza,
				tipo_fianza,
				fecha_inicio,
				fecha_termino,
				deposito_garantia,
				recibo_mensual,
				incremento_anual,
				porcentaje_incremento,
				renta,
				mantenimiento,
				subtotal,
				iva,
				agua,
				retencion_iva,
				retencion_isr,
				total,
				iduser_baja,
				tsbaja
			)
	
	SELECT
		id
		 AS id_catalogo_arrendatarios,
		(
			SELECT
				idarrendador
			FROM
				rentas_arrendadores
			WHERE
				arrendador = arr.cod_arrendador
		)
			AS idarrendador,
		(
			SELECT
				idlocal
			FROM
				rentas_locales
			WHERE
				id_catalogo_arrendatarios = arr.id
		)
			AS idlocal,
		bloque,
		TRIM(regexp_replace(COALESCE(nombre_local, \'\'), \'\s+\', \' \', \'g\'))
			AS alias_arrendatario,
		TRIM(regexp_replace(COALESCE(nombre_arrendatario, \'\'), \'\s+\', \' \', \'g\'))
			AS nombre_arrendatario,
		TRIM(regexp_replace(COALESCE(rfc, \'\'), \'\s+\', \' \', \'g\'))
			AS rfc,
		tipo_persona,
		TRIM(regexp_replace(COALESCE(calle, \'\'), \'\s+\', \' \', \'g\'))
			AS calle,
		TRIM(regexp_replace(COALESCE(no_exterior, \'\'), \'\s+\', \' \', \'g\'))
			AS no_exterior,
		TRIM(regexp_replace(COALESCE(no_interior, \'\'), \'\s+\', \' \', \'g\'))
			AS no_interior,
		TRIM(regexp_replace(COALESCE(colonia, \'\'), \'\s+\', \' \', \'g\'))
			AS colonia,
		TRIM(regexp_replace(COALESCE(municipio, \'\'), \'\s+\', \' \', \'g\'))
			AS municipio,
		TRIM(regexp_replace(COALESCE(estado, \'\'), \'\s+\', \' \', \'g\'))
			AS estado,
		TRIM(regexp_replace(COALESCE(pais, \'\'), \'\s+\', \' \', \'g\'))
			AS pais,
		codigo_postal,
		TRIM(regexp_replace(COALESCE(contacto, \'\'), \'\s+\', \' \', \'g\'))
			AS contacto,
		TRIM(regexp_replace(COALESCE(telefono, \'\'), \'\s+\', \' \', \'g\'))
			AS telefono1,
		TRIM(regexp_replace(COALESCE(\'\', \'\'), \'\s+\', \' \', \'g\'))
			AS telefono2,
		LOWER(TRIM(regexp_replace(COALESCE(email, \'\'), \'\s+\', \' \', \'g\')))
			AS email,
		TRIM(regexp_replace(COALESCE(giro, \'\'), \'\s+\', \' \', \'g\'))
			AS giro,
		TRIM(regexp_replace(COALESCE(representante, \'\'), \'\s+\', \' \', \'g\'))
			AS representante,
		TRIM(regexp_replace(COALESCE(nombre_aval, \'\'), \'\s+\', \' \', \'g\'))
			AS fianza,
		TRIM(regexp_replace(COALESCE(bien_avaluo, \'\'), \'\s+\', \' \', \'g\'))
			AS tipo_fianza,
		COALESCE(fecha_inicio, NOW()::DATE)
			AS fecha_inicio,
		COALESCE(fecha_final, NOW()::DATE)
			AS fecha_termino,
		COALESCE(rentas_en_deposito, 0)
			AS deposito_garantia,
		recibo_mensual,
		incremento_anual,
		COALESCE(por_incremento, 0)
			AS porcentaje_incremento,
		COALESCE(renta_con_recibo, 0)
			AS renta,
		COALESCE(mantenimiento, 0)
			AS mantenimiento,
		COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)
			AS subtotal,
		ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::NUMERIC, 2)
			AS iva,
		COALESCE(agua, 0),
		CASE
			WHEN retencion_iva = TRUE THEN
				ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.1066666667)::NUMERIC, 2)
			ELSE
				0
		END
			AS retencion_iva,
		CASE
			WHEN retencion_isr = TRUE THEN
				ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10)::NUMERIC, 2)
			ELSE
				0
		END
			AS retencion_isr,
		(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0))
		+ ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::NUMERIC, 2)
		+ COALESCE(agua, 0)
		- (
			CASE
				WHEN retencion_iva = TRUE THEN
					ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.1066666667)::NUMERIC, 2)
				ELSE
					0
			END
		)
		- (
			CASE
				WHEN retencion_isr = TRUE THEN
					ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10)::NUMERIC, 2)
				ELSE
					0
			END
		)
			AS total,
		1
			AS iduser_baja,
		NOW()
			AS tsbaja
	FROM
		catalogo_arrendatarios arr
	WHERE
		status = 0
	ORDER BY
		cod_arrendador,
		num_local;
	
	INSERT INTO
		rentas_arrendatarios
			(
				id_catalogo_arrendatarios,
				idarrendador,
				idlocal,
				bloque,
				alias_arrendatario,
				nombre_arrendatario,
				rfc,
				tipo_persona,
				calle,
				no_exterior,
				no_interior,
				colonia,
				municipio,
				estado,
				pais,
				codigo_postal,
				contacto,
				telefono1,
				telefono2,
				email,
				giro,
				representante,
				fianza,
				tipo_fianza,
				fecha_inicio,
				fecha_termino,
				deposito_garantia,
				recibo_mensual,
				incremento_anual,
				porcentaje_incremento,
				renta,
				mantenimiento,
				subtotal,
				iva,
				agua,
				retencion_iva,
				retencion_isr,
				total
			)
	
	SELECT
		id
		 AS id_catalogo_arrendatarios,
		(
			SELECT
				idarrendador
			FROM
				rentas_arrendadores
			WHERE
				arrendador = arr.cod_arrendador
		)
			AS idarrendador,
		(
			SELECT
				idlocal
			FROM
				rentas_locales
			WHERE
				id_catalogo_arrendatarios = arr.id
		)
			AS idlocal,
		bloque,
		TRIM(regexp_replace(COALESCE(nombre_local, \'\'), \'\s+\', \' \', \'g\'))
			AS alias_arrendatario,
		TRIM(regexp_replace(COALESCE(nombre_arrendatario, \'\'), \'\s+\', \' \', \'g\'))
			AS nombre_arrendatario,
		TRIM(regexp_replace(COALESCE(rfc, \'\'), \'\s+\', \' \', \'g\'))
			AS rfc,
		tipo_persona,
		TRIM(regexp_replace(COALESCE(calle, \'\'), \'\s+\', \' \', \'g\'))
			AS calle,
		TRIM(regexp_replace(COALESCE(no_exterior, \'\'), \'\s+\', \' \', \'g\'))
			AS no_exterior,
		TRIM(regexp_replace(COALESCE(no_interior, \'\'), \'\s+\', \' \', \'g\'))
			AS no_interior,
		TRIM(regexp_replace(COALESCE(colonia, \'\'), \'\s+\', \' \', \'g\'))
			AS colonia,
		TRIM(regexp_replace(COALESCE(municipio, \'\'), \'\s+\', \' \', \'g\'))
			AS municipio,
		TRIM(regexp_replace(COALESCE(estado, \'\'), \'\s+\', \' \', \'g\'))
			AS estado,
		TRIM(regexp_replace(COALESCE(pais, \'\'), \'\s+\', \' \', \'g\'))
			AS pais,
		codigo_postal,
		TRIM(regexp_replace(COALESCE(contacto, \'\'), \'\s+\', \' \', \'g\'))
			AS contacto,
		TRIM(regexp_replace(COALESCE(telefono, \'\'), \'\s+\', \' \', \'g\'))
			AS telefono1,
		TRIM(regexp_replace(COALESCE(\'\', \'\'), \'\s+\', \' \', \'g\'))
			AS telefono2,
		LOWER(TRIM(regexp_replace(COALESCE(email, \'\'), \'\s+\', \' \', \'g\')))
			AS email,
		TRIM(regexp_replace(COALESCE(giro, \'\'), \'\s+\', \' \', \'g\'))
			AS giro,
		TRIM(regexp_replace(COALESCE(representante, \'\'), \'\s+\', \' \', \'g\'))
			AS representante,
		TRIM(regexp_replace(COALESCE(nombre_aval, \'\'), \'\s+\', \' \', \'g\'))
			AS fianza,
		TRIM(regexp_replace(COALESCE(bien_avaluo, \'\'), \'\s+\', \' \', \'g\'))
			AS tipo_fianza,
		COALESCE(fecha_inicio, NOW()::DATE)
			AS fecha_inicio,
		COALESCE(fecha_final, NOW()::DATE)
			AS fecha_termino,
		COALESCE(rentas_en_deposito, 0)
			AS deposito_garantia,
		recibo_mensual,
		incremento_anual,
		COALESCE(por_incremento, 0)
			AS porcentaje_incremento,
		COALESCE(renta_con_recibo, 0)
			AS renta,
		COALESCE(mantenimiento, 0)
			AS mantenimiento,
		COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)
			AS subtotal,
		ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::NUMERIC, 2)
			AS iva,
		COALESCE(agua, 0),
		CASE
			WHEN retencion_iva = TRUE THEN
				ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.1066666667)::NUMERIC, 2)
			ELSE
				0
		END
			AS retencion_iva,
		CASE
			WHEN retencion_isr = TRUE THEN
				ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10)::NUMERIC, 2)
			ELSE
				0
		END
			AS retencion_isr,
		(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0))
		+ ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::NUMERIC, 2)
		+ COALESCE(agua, 0)
		- (
			CASE
				WHEN retencion_iva = TRUE THEN
					ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.1066666667)::NUMERIC, 2)
				ELSE
					0
			END
		)
		- (
			CASE
				WHEN retencion_isr = TRUE THEN
					ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10)::NUMERIC, 2)
				ELSE
					0
			END
		)
			AS total
	FROM
		catalogo_arrendatarios arr
	WHERE
		status = 1
	ORDER BY
		cod_arrendador,
		num_local;
';

$db->query($sql);

$sql = '
	SELECT
		idarrendatario,
		idarrendador
	FROM
		rentas_arrendatarios
	WHERE
		tsbaja IS NULL
	ORDER BY
		idarrendador,
		idarrendatario
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$arrendatario = 1;
		}
		
		$sql .= 'UPDATE rentas_arrendatarios SET arrendatario = ' . $arrendatario . ' WHERE idarrendatario = ' . $rec['idarrendatario'] . ";\n";
		
		$arrendatario++;
	}
	
	$db->query($sql);
}

$sql = '
	SELECT
		idarrendatario,
		idarrendador
	FROM
		rentas_arrendatarios
	WHERE
		tsbaja IS NOT NULL
	ORDER BY
		idarrendador,
		idarrendatario
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$arrendatario = 1;
		}
		
		$sql .= 'UPDATE rentas_arrendatarios SET arrendatario = ' . $arrendatario . ' WHERE idarrendatario = ' . $rec['idarrendatario'] . ";\n";
		
		$arrendatario++;
	}
	
	$db->query($sql);
}

$sql = '
	INSERT INTO
		rentas_recibos
			(
				idarrendatario,
				idcfd,
				num_recibo,
				fecha,
				renta,
				mantenimiento,
				subtotal,
				iva,
				agua,
				retencion_iva,
				retencion_isr,
				total,
				tsalta,
				idalta,
				tsbaja,
				idbaja,
				idviejorecibo
			)
	
	SELECT
		(
			SELECT
				idarrendatario
			FROM
				rentas_arrendatarios
			WHERE
				id_catalogo_arrendatarios = rec.local
		)
			AS idarrendatario,
		(
			SELECT
				id
			FROM
				facturas_electronicas
			WHERE
				num_cia = inm.homoclave
				AND tipo_serie = 2
				AND consecutivo = rec.num_recibo::NUMERIC
		)
			AS idcfd,
		num_recibo::NUMERIC
			AS num_recibo,
		fecha,
		COALESCE(rec.renta, 0)
			AS renta,
		COALESCE(rec.mantenimiento, 0)
			AS mantenimiento,
		COALESCE(rec.renta, 0) + COALESCE(rec.mantenimiento, 0)
			AS subtotal,
		COALESCE(rec.iva, 0)
			AS iva,
		COALESCE(rec.agua, 0)
			AS agua,
		COALESCE(rec.iva_retenido, 0)
			AS retencion_iva,
		COALESCE(rec.isr_retenido, 0)
			AS retencion_isr,
		COALESCE(rec.neto, 0)
			AS total,
		COALESCE(tsins, fecha::TIMESTAMP)
			AS tsalta,
		COALESCE(iduser, 1)
			AS idalta,
		CASE
			WHEN rec.status = 0 THEN
				COALESCE(tsins, fecha::TIMESTAMP)
			ELSE
				NULL
		END
			AS tsbaja,
		CASE
			WHEN rec.status = 0 THEN
				COALESCE(iduser, 1)
			ELSE
				NULL
		END
			AS idbaja,
		rec.id
			AS idviejorecibo
	FROM
		recibos_rentas rec
		LEFT JOIN catalogo_arrendatarios arr
			ON (arr.id = rec.local)
		LEFT JOIN catalogo_arrendadores inm
			USING (cod_arrendador)
	WHERE
		rec.local NOT IN (183, 141)
	ORDER BY
		rec.id
';

$db->query($sql);

$sql = '
	UPDATE
		estado_cuenta
	SET
		idarrendatario = result.idarrendatario
	FROM (
		SELECT
			id_catalogo_arrendatarios
				AS idviejo,
			idarrendatario
		FROM
			rentas_arrendatarios
	)
		AS result
	WHERE
		estado_cuenta.local = result.idviejo;
	
	UPDATE
		estado_cuenta
	SET
		idreciborenta = result.idreciborenta
	FROM (
		SELECT
			idarrendatario,
			idreciborenta,
			fecha
		FROM
			rentas_recibos
	)
		AS result
	WHERE
		estado_cuenta.idarrendatario = result.idarrendatario
		AND estado_cuenta.fecha_renta = result.fecha;
';

$db->query($sql);

?>