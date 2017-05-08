<?php
class AuxInvClass {
	var $version = '1.0.0';

	var $num_cia;
	var $anio;
	var $mes;

	var $codmp;
	var $inv;
	var $cont;
	var $tipo;
	var $ni;

	var $fecha_his;
	var $fecha1;
	var $fecha2;

	var $mps = array();
	var $movs = array();
	var $consumos = array();
	var $compra_directa = 0;
	var $diferencias = array();

	function AuxInvClass($num_cia, $anio, $mes, $codmp = NULL, $inv = 'real', $cont = '', $tipo = '', $ni = NULL, $dif = TRUE, $dia = NULL) {
		$this->num_cia = $num_cia;
		$this->anio = $anio;
		$this->mes = $mes;
		$this->dia = $dia;

		$this->codmp = $codmp;				// >0 = buscar solo un producto
		$this->inv = $inv;					// 'real' = usar inventario real, 'virtual' = usar inventario virtual
		$this->cont = $cont;				// '' = todos, 'TRUE' = solo controlados, 'FALSE' = solo no controlados
		$this->tipo = $tipo;				// '' = todos, 1 = materia prima, 2 = material de empaque
		$this->ni = $ni;					// Arreglo con productos que no se incluiran
		$this->dif = $dif;					// 'TRUE' = Calcular con diferencias de inventario

		$this->fecha_his = date('d/m/Y', mktime(0, 0, 0, $this->mes, 0, $this->anio));
		$this->fecha1 = date('d/m/Y', mktime(0, 0, 0, $this->mes, 1, $this->anio));
		if ($this->dia > 0)
		{
			$this->fecha2 = date('d/m/Y', mktime(0, 0, 0, $this->mes, $this->dia, $this->anio));
		}
		else
		{
			$this->fecha2 = date('d/m/Y', mktime(0, 0, 0, $this->mes + 1, 0, $this->anio));
		}

		$this->inventarioInicial();
		$this->movimientos();
		$this->calcularCostos();
	}

	function inventarioInicial() {
		/*
		@ Obtener historico de inicio de mes y reordenarlo en un arreglo de códigos de materia prima
		*/
		$sql = '
			SELECT
				codmp,
				nombre,
				existencia,
				precio_unidad,
				CASE
					WHEN controlada = \'TRUE\' THEN
						\'t\'
					ELSE
						\'f\'
				END
					AS
						controlado,
				tipo
			FROM
					historico_inventario
				LEFT JOIN
					catalogo_mat_primas
						USING
							(
								codmp
							)
			WHERE
					num_cia = ' . $this->num_cia . '
				AND
					fecha = \'' . $this->fecha_his . '\'
		';
		if ($this->codmp > 0)
			$sql .= '
				AND
					codmp = ' . $this->codmp . '
			';
		if ($this->cont != NULL)
			$sql .= '
				AND
					controlada = \'' . $this->cont . '\'
			';
		if ($this->cont != NULL && $this->tipo > 0)
			$sql .= '
				AND
					tipo = \'' . $this->tipo . '\'

			';
		if ($this->ni != NULL)
			$sql .= '
				AND
					codmp
						NOT IN
							(
								' . implode(', ', $this->ni) . '
							)
			';
		$sql .= '
			ORDER BY
				controlada
					DESC,
				tipo,
				codmp
		';
		$mps = $GLOBALS['db']->query($sql);

		/*
		@ Reordenar datos
		*/
		if ($mps)
			foreach ($mps as $mp) {
				$this->mps[$mp['codmp']] = array(
					'nombre' => $mp['nombre'],
					'controlado' => $mp['controlado'],
					'tipo' => $mp['tipo'],
					'existencia_ini' => floatval($mp['existencia']),
					'precio_ini' => floatval($mp['precio_unidad']),
					'costo_ini' => floatval($mp['existencia']) * floatval($mp['precio_unidad']),
					'entradas' => 0,
					'compras' => 0,
					'entradas_mercancias' => 0,
					'mercancias' => 0,
					'salidas' => 0,
					'consumos' => 0,
					'existencia' => floatval($mp['existencia']),
					'precio' => floatval($mp['precio_unidad']),
					'costo' => floatval($mp['existencia']) * floatval($mp['precio_unidad'])
				);

				$this->consumos[$mp['codmp']] = array(
					0  => 0,	// Sin turno
					1  => 0,	// Frances de día
					2  => 0,	// Frances de noche
					3  => 0,	// Bizcochero
					4  => 0,	// Repostero
					8  => 0,	// Piconero
					9  => 0,	// Gelatinero
					10 => 0,	// Despacho
				);
			}
	}

	function movimientos() {
		/*
		@ Obtener movimientos  y reordenarlo en un arreglo de códigos de materia prima y movimientos
		*/
		$sql = '
			SELECT
				codmp,
				fecha,
				tipo_mov,
				cod_turno,
				ct.nombre_corto
					AS
						turno,
				cantidad,
				precio_unidad,
				total_mov,
				movs.descripcion
					AS
						concepto,
				num_proveedor
					AS
						num_pro,
				' . ($this->inv == 'virtual' ? 'NULL AS num_fact' : 'num_fact') . ',
				CASE
					WHEN movs.descripcion = \'DIFERENCIA INVENTARIO\' THEN
						2
					ELSE
						1
				END
					AS
						tipo
			FROM
					mov_inv_' . $this->inv . '
						AS
							movs
				LEFT JOIN
					catalogo_mat_primas
						cmp
							USING
								(
									codmp
								)
				LEFT JOIN
					catalogo_turnos
						ct
							USING
								(
									cod_turno
								)
			WHERE
					num_cia = ' . $this->num_cia . '
				AND
					fecha
						BETWEEN
								\'' . $this->fecha1 . '\'
							AND
								\'' . $this->fecha2 . '\'
		';
		if ($this->codmp > 0)
			$sql .= '
				AND
					codmp = ' . $this->codmp . '
			';
		if ($this->cont != NULL)
			$sql .= '
				AND
					controlada = \'' . $this->cont . '\'
			';
		if ($this->cont != NULL && $this->tipo > 0)
			$sql .= '
				AND
					tipo = \'' . $this->tipo . '\'

			';
		if ($this->ni != NULL)
			$sql .= '
				AND
					codmp
						NOT IN
							(
								' . implode(', ', $this->ni) . '
							)
			';
		if (!$this->dif)
			$sql .= '
				AND
					movs.descripcion != \'DIFERENCIA INVENTARIO\'
			';
		$sql .= '
			ORDER BY
				codmp,
				tipo,
				fecha,
				tipo_mov,
				cantidad
					DESC
		';
		$movs = $GLOBALS['db']->query($sql);

		/*
		@ Reordenar datos
		*/
		if ($movs)
			foreach ($movs as $mov)
				$this->movs[$mov['codmp']][] = array(
					'tipo' => $mov['tipo'],
					'fecha' => $mov['fecha'],
					'concepto' => $mov['concepto'],
					'num_pro' => $mov['num_pro'],
					'num_fact' => $mov['num_fact'],
					'tipo_mov' => $mov['tipo_mov'],
					'cod_turno' => $mov['cod_turno'],
					'turno' => $mov['turno'],
					'cantidad' => floatval($mov['cantidad']),
					'total' => floatval($mov['total_mov']),
					'precio' => floatval($mov['precio_unidad']),
					'existencia' => 0,
					'costo' => 0,
					'precio_pro' => floatval($mov['precio_unidad'])
				);
	}

	function calcularCostos() {
		if (count($this->mps) == 0)
			return FALSE;

		foreach ($this->mps as $cod => $mp) {
			/*
			@ Si no hay movimientos para el producto saltar el proceso de calculo de costos
			*/
			if (!isset($this->movs[$cod]))
				continue;

			$uni_in = 0;
			$uni_out = 0;

			$costo_in = 0;
			$costo_out = 0;

			/*
			@ Recorrer movimientos
			*/
			foreach ($this->movs[$cod] as $i => $mov) {
				/*
				@ Tipo 1: Movimiento normal de entrada/salida
				*/
				if ($mov['tipo'] == 1) {
					/*
					@ Proceso para 'Entradas'
					*/
					if ($mov['tipo_mov'] == 'f') {
						$this->mps[$cod]['existencia'] += $mov['cantidad'];
						$this->mps[$cod]['entradas'] += $mov['cantidad'];
						$this->mps[$cod]['compras'] += $mov['cantidad'] >= 0 ? $mov['total'] : $mov['cantidad'] * $this->mps[$cod]['precio'];
						if ($mov['cantidad'] >= 0)
							$this->mps[$cod]['precio'] = $mp['existencia_ini'] + $this->mps[$cod]['entradas'] != 0 ? ($mp['costo_ini'] + $this->mps[$cod]['compras']) / ($mp['existencia_ini'] + $this->mps[$cod]['entradas']) : 0;
						else {
							$this->movs[$cod][$i]['total'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
							$this->movs[$cod][$i]['precio'] = $this->mps[$cod]['precio'];
						}
						$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];

						if (strpos($mov['concepto'], 'COMPRA DIRECTA') !== FALSE) {
							$this->compra_directa += $mov['total'];

							$this->mps[$cod]['entradas_mercancias'] += $mov['cantidad'];
							$this->mps[$cod]['mercancias'] += $mov['cantidad'] >= 0 ? $mov['total'] : $mov['cantidad'] * $this->mps[$cod]['precio'];
						}
					}
					/*
					@ Proceso para 'Salidas'
					*/
					else if ($mov['tipo_mov'] == 't') {
						$this->mps[$cod]['existencia'] -= $mov['cantidad'];
						$this->mps[$cod]['salidas'] += $mov['cantidad'];
						$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];

						$this->movs[$cod][$i]['total'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['precio'] = $this->mps[$cod]['precio'];

						/*
						@ Sumar consumo por turno
						*/
						if ($mov['cod_turno'] > 0 && $mov['cod_turno'] < 11)
							$this->consumos[$cod][$mov['cod_turno']] += $mov['cantidad'];
						/*
						@ Sumar consumo sin turno
						*/
						else
							$this->consumos[$cod][0] += $mov['cantidad'];
					}

					$this->mps[$cod]['consumos'] = $this->mps[$cod]['salidas'] * $this->mps[$cod]['precio'];

					$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
					$this->movs[$cod][$i]['costo'] = $this->mps[$cod]['costo'];
					$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
				}
				/*
				@ Tipo 2: Diferencia de inventario
				*/
				else if ($mov['tipo'] == 2) {
					/*
					@ Proceso de diferencias para 'Entradas'
					@ NOTA: Se incluye este apartado por razones de compatibilidad con movimientos anteriores donde existian entradas como diferencias
					*/
					if ($mov['tipo_mov'] == 'f') {
						$this->mps[$cod]['existencia'] += $mov['cantidad'];
						$this->mps[$cod]['compras'] += $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];
					}
					/*
					@ Proceso de diferencias para 'Salidas'
					*/
					else if ($mov['tipo_mov'] == 't') {
						$this->mps[$cod]['existencia'] -= $mov['cantidad'];
						$this->mps[$cod]['salidas'] += $mov['cantidad'];
						$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];

						$this->movs[$cod][$i]['total'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['precio'] = $this->mps[$cod]['precio'];

						/*
						@ Sumar consumo sin turno
						*/
						$this->consumos[$cod][0] += $mov['cantidad'];
					}

					$this->mps[$cod]['consumos'] = $this->mps[$cod]['salidas'] * $this->mps[$cod]['precio'];

					$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
					$this->movs[$cod][$i]['costo'] = $this->mps[$cod]['costo'];
					$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
				}
			}
		}
	}
}
?>
