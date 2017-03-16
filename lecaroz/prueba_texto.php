<?php


//echo hash('md5', 'ADMINISTRADOR');

$str = "SELECT codmp, nombre AS nombre_mp, tuc.descripcion AS unidad, CASE WHEN tipo = '1' THEN 'MATERIA PRIMA' WHEN tipo = '2' THEN 'MATERIAL DE EMPAQUE' END AS categoria, CASE WHEN controlada = 'TRUE' THEN TRUE ELSE FALSE END AS controlada, procpedautomat AS pedido, COALESCE(no_exi, FALSE) AS sin_existencia FROM catalogo_mat_primas cmp LEFT JOIN tipo_unidad_consumo tuc ON (tuc.idunidad = cmp.unidadconsumo) WHERE cmp.controlada IN ('TRUE', 'FALSE') AND cmp.tipo_cia IN (TRUE, FALSE) ORDER BY codmp LIMIT 30 OFFSET 0";

echo (stristr($str, 'SET', TRUE))[1];

?>