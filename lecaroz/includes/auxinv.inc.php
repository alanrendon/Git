<?php
include 'includes/class.auxinv.inc.php';

function ActualizarInventario($num_cia, $anio, $mes, $codmp = NULL, $real = TRUE, $virtual = FALSE, $his = TRUE, $fin = FALSE) {
	$sql = '';

	/*
	@
	@@ Actualizar Inventario Real
	@
	*/

	if ($his || $real) {
		$aux = new AuxInvClass($num_cia, $anio, $mes, $codmp, 'real');

		if ($real)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= 'UPDATE inventario_real SET existencia = ' . round($mp['existencia'], 2) . ', precio_unidad = ' . $mp['precio'] . ' WHERE num_cia = ' . $num_cia . ' AND codmp = ' . $cod . '' . ";\n";
			}

		if ($his)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= 'UPDATE historico_inventario SET existencia = ' . round($mp['existencia'], 2) . ', precio_unidad = ' . $mp['precio'] . ' WHERE num_cia = ' . $num_cia . ' AND codmp = ' . $cod . ' AND fecha = \'' . $aux->fecha2 . '\'' . ";\n";
			}

		if ($fin)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= 'UPDATE inventario_fin_mes SET existencia = ' . round($mp['existencia'], 2) . ', diferencia = ' . $mp['existencia'] . ' - inventario, precio_unidad = ' . $mp['precio'] . ' WHERE num_cia = ' . $num_cia . ' AND codmp = ' . $cod . ' AND fecha = \'' . $aux->fecha2 . '\'' . ";\n";
			}
	}

	/*
	@
	@@ Actualizar Inventario Virtual
	@
	*/

	if ($virtual) {
		$aux = new AuxInvClass($num_cia, $anio, $mes, $codmp, 'virtual');

		foreach ($aux->mps as $cod => $mp) {
			$sql .= 'UPDATE inventario_virtual SET existencia = ' . round($mp['existencia'], 2) . ', precio_unidad = ' . $mp['precio'] . ' WHERE num_cia = ' . $num_cia . ' AND codmp = ' . $cod . '' . ";\n";
		}
	}

	return $sql;
}

?>
