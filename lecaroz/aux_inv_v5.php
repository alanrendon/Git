<?php
class AuxInvClass {
	var $version = '0.0.1';
	
	function AuxInvClass($num_cia, $anio, $mes, $codmp = NULL) {
		$this->num_cia = $num_cia;
		$this->anio = $anio;
		$this->mes = $mes;
		$this->codmp = $codmp;
		
		$this->fecha_his = date('d/m/Y', mktime(0, 0, 0, $this->mes, 0, $this->anio));
		$this->fecha1 = date('d/m/Y', mktime(0, 0, 0, $this->mes, 1, $this->anio));
		$this->fecha2 = date('d/m/Y', mktime(0, 0, 0, $this->mes + 1, 0, $this->anio));
		
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
				existencia,
				precio_unidad
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
		$sql .= '
			ORDER BY
				controlada
					DESC,
				tipo,
				codmp
		';
		$mps = $db->query($sql);
		
		/*
		@ Reordenar datos
		*/
		$this->mps = array();
		if ($mps)
			foreach ($mps as $mp)
				$this->mps[$mp['codmp']] = array(
					'existencia_ini' => $mp['existencia'],
					'precio_ini' => $mp['precio_unidad'],
					'costo_ini' => $mp['existencia'] * $mp['precio_unidad'],
					'compras' => 0,
					'consumos' => 0,
					'existencia' => $mp['existencia'],
					'precio' => $mp['precio_unidad'],
					'costo' => $mp['existencia'] * $mp['precio_unidad']
				);
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
				cantidad,
				precio_unidad,
				total_mov,
				CASE
					WHEN descripcion = \'DIFERENCIA INVENTARIO\' THEN
						2
					ELSE
						1
				END
					AS
						tipo
			FROM
				mov_inv_real
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
		$sql .= '
			ORDER BY
				codmp,
				tipo,
				fecha,
				tipo_mov,
				cantidad
					DESC
		';
		$movs = $db->query($sql);
		
		/*
		@ Reordenar datos
		*/
		$this->movs = array();
		if ($movs)
			foreach ($movs as $mov)
				$this->movs[$mov['codmp']][] = array(
					'tipo' => $mov['tipo'],
					'fecha' => $mov['fecha'],
					'tipo_mov' => $mov['tipo_mov'],
					'cantidad' => $mov['cantidad'],
					'total' => $mov['total_mov'],
					'precio' => $mov['precio_unidad'],
					'existencia' => 0,
					'costo' => 0,
					'precio_pro' => $mov['precio_unidad']
				);
	}
	
	function calcularCostos() {
		foreach ($this->mps as $cod => $mp) {
			/*
			@ Si no hay movimientos para el producto saltar el proceso de calculo de costos
			*/
			if (!isset($movs[$cod]))
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
						$this->mps[$cod]['precio'] = ($mp['costo_ini'] + $mov['total']) / ($mp['existencia_ini'] + $mov['cantidad']);
						$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];
						$this->mps[$cod]['compras'] += $mov['total'];
						
						$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
						$this->movs[$cod][$i]['costo'] = $this->mps[$cod]['costo'];
						$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
					}
					/*
					@ Proceso para 'Salidas'
					*/
					else if ($mov['tipo_mov'] == 't') {
						$this->mps[$cod]['existencia'] -= $mov['cantidad'];
						if ($this->mps[$cod]['existencia'] >= 0)
							$this->mps[$cod]['costo'] = $this->mps[$cod]['existencia'] * $this->mps[$cod]['precio'];
						else
							$this->mps[$cod]['costo'] = 0;
						
						$this->mps[$cod]['consumos'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
						
						$this->movs[$cod][$i]['total'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['precio'] = $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
						$this->movs[$cod][$i]['costo'] = $this->mps[$cod]['costo'];
						$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
					}
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
						$this->mps[$cod]['costo'] += $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->mps[$cod]['compras'] += $mov['cantidad'] * $this->mps[$cod]['precio'];
						
						$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
						$this->movs[$cod][$i]['costo'] = $this->mps[$cod]['costo'];
						$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
					}
					/*
					@ Proceso de diferencias para 'Salidas'
					*/
					else if ($mov['tipo_mov'] == 't') {
						$this->mps[$cod]['existencia'] -= $mov['cantidad'];
						$this->mps[$cod]['consumos'] = $this->mps[$cod]['cantidad'] * $this->mps[$cod]['precio'];
						
						$this->movs[$cod][$i]['total'] = $mov['cantidad'] * $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['precio'] = $this->mps[$cod]['precio'];
						$this->movs[$cod][$i]['existencia'] = $this->mps[$cod]['existencia'];
						$this->movs[$cod][$i]['precio_pro'] = $this->mps[$cod]['precio'];
					}
				}
			}
		}
	}
}
?>