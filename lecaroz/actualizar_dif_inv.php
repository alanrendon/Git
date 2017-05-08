<?php
include './includes/class.db.inc.php';

$user = "root";
$pass = "IVcaMA";
$host = "192.168.1.250";
$db_name   = "lecaroz";
$dsn1 = "pgsql://$user:$pass@$host:5432/$db_name";

$user = "root";
$pass = "IVcaMA";
$host = "192.168.1.250";
$db_name   = "prueba";
$dsn2 = "pgsql://$user:$pass@$host:5432/$db_name";

$db_lecaroz = new DBclass($dsn1);
$db_prueba = new DBclass($dsn2);


$sql = "SELECT * FROM inventario_real WHERE num_cia < 100;\n";
$inv = $db_prueba->query($sql);
$sql = "SELECT * FROM mov_inv_real WHERE num_cia < 100 AND fecha = '2005/04/30' AND descripcion LIKE '%DIFERENCIA%';\n";
$mov = $db_prueba->query($sql);
$sql = "SELECT * FROM inventario_fin_mes WHERE num_cia < 100 AND fecha = '2005/04/30';\n";
$fin = $db_prueba->query($sql);
$sql = "SELECT * FROM diferencias_inventario WHERE num_cia < 100 AND fecha = '2005/04/30';\n";
$dif = $db_prueba->query($sql);
$sql = "SELECT * FROM movimiento_gastos WHERE num_cia < 100 AND fecha = '2005/04/30' AND codgastos = 90;\n";
$gas = $db_prueba->query($sql);
$sql = "SELECT * FROM historico_inventario WHERE num_cia < 100 AND fecha = '2005/04/30'";
$his = $db_prueba->query($sql);

$sql = "DELETE FROM inventario_real WHERE num_cia < 100;\n";
$sql .= "DELETE FROM mov_inv_real WHERE num_cia < 100 AND fecha = '2005/04/30' AND descripcion LIKE '%DIFERENCIA%';\n";
$sql .= "DELETE FROM inventario_fin_mes WHERE num_cia < 100 AND fecha = '2005/04/30';\n";
$sql .= "DELETE FROM diferencias_inventario WHERE num_cia < 100 AND fecha = '2005/04/30';\n";
$sql .= "DELETE FROM movimiento_gastos WHERE num_cia < 100 AND fecha = '205/04/30' AND codgastos = 90;\n";
$sql .= "DELETE FROM historico_inventario WHERE num_cia < 100 AND fecha = '2005/04/30';\n";

$sql .= $db_lecaroz->multiple_insert("inventario_real",$inv);
$sql .= $db_lecaroz->multiple_insert("mov_inv_real",$mov);
$sql .= $db_lecaroz->multiple_insert("inventario_fin_mes",$fin);
$sql .= $db_lecaroz->multiple_insert("diferencias_inventario",$dif);
$sql .= $db_lecaroz->multiple_insert("movimiento_gastos",$gas);
$sql .= $db_lecaroz->multiple_insert("historico_inventario",$his);

$db_lecaroz->comenzar_transaccion();
$db_lecaroz->query($sql);
$db_lecaroz->terminar_transaccion();

$db_prueba->desconectar();
$db_lecaroz->desconectar();

?>