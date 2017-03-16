<?php
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$codmp[0] = 600;
$codmp[1] = 160;
$codmp[2] = 700;
$codmp[3] = 352;
$codmp[4] = 297;
$codmp[5] = 363;
$codmp[6] = 303;
$codmp[7] = 302;
$codmp[8] = 301;
$codmp[9] = 300;
$codmp[10] = 451;
$codmp[11] = 452;
$codmp[12] = 450;
$codmp[13] = 359;
$codmp[14] = 304;
$codmp[15] = 192;
$codmp[16] = 315;
$codmp[17] = 355;
$codmp[18] = 358;
$codmp[19] = 401;
$codmp[20] = 601;

$db = DB::connect($dsn);
if (DB::isError($db))
	die($db->getUserInfo());

$sql = "SELECT * FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200";
$result = $db->query($sql);
if (DB::isError($result))
	die($result->getUserInfo());

while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	foreach($codmp as $key=>$value) {
		$new_result = $db->query("SELECT * FROM fact_rosticeria WHERE num_cia=".$row->num_cia." AND codmp=".$value);
		if (DB::isError($new_result))
			die($new_result->getUserInfo());
		if ($new_result->numRows()) {
			// Actualizar inventario real
			$sql =  "UPDATE inventario_real SET existencia=";
			$sql .= "(";
			$sql .= "(SELECT sum(cantidad) FROM fact_rosticeria WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " AND codmp=";
			$sql .= $value;
			$sql .= ")";
			$sql .= " + ";
			$sql .= "(SELECT existencia FROM inventario_real WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " AND codmp=";
			$sql .= $value;
			$sql .= ")";
			$sql .= ")";
			$sql .= "WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " and codmp=";
			$sql .= $value;
			echo $sql."<br>";
			$ok = $db->query($sql);
			if (DB::isError($ok))
				die($ok->getUserInfo());
			
			// Actualizar inventario virtual
			$sql =  "UPDATE inventario_virtual SET existencia=";
			$sql .= "(";
			$sql .= "(SELECT sum(cantidad) FROM fact_rosticeria WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " AND codmp=";
			$sql .= $value;
			$sql .= ")";
			$sql .= " + ";
			$sql .= "(SELECT existencia FROM inventario_virtual WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " AND codmp=";
			$sql .= $value;
			$sql .= ")";
			$sql .= ")";
			$sql .= "WHERE num_cia=";
			$sql .= $row->num_cia;
			$sql .= " and codmp=";
			$sql .= $value;
			echo $sql."<br>";
			$ok = $db->query($sql);
			if (DB::isError($ok))
				die($ok->getUserInfo());
		}
	}
}
$db->disconnect();
?>