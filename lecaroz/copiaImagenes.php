<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';

$db = new DBclass("pgsql://root:pobgnj@192.168.1.250:5432/scans", "autocommit=yes");

/*$doc[0]['num_cia'] = 5;
$doc[0]['tipo_doc'] = 22;
$doc[0]['cia'] = 1;

$doc[1]['num_cia'] = 6;
$doc[1]['tipo_doc'] = 22;
$doc[1]['cia'] = 4;

$doc[2]['num_cia'] = 6;
$doc[2]['tipo_doc'] = 4;
$doc[2]['cia'] = 4;

$doc[3]['num_cia'] = 10;
$doc[3]['tipo_doc'] = 4;
$doc[3]['cia'] = 9;

$doc[4]['num_cia'] = 10;
$doc[4]['tipo_doc'] = 5;
$doc[4]['cia'] = 9;

$doc[5]['num_cia'] = 10;
$doc[5]['tipo_doc'] = 22;
$doc[5]['cia'] = 9;

$doc[6]['num_cia'] = 23;
$doc[6]['tipo_doc'] = 4;
$doc[6]['cia'] = 22;

$doc[7]['num_cia'] = 23;
$doc[7]['tipo_doc'] = 5;
$doc[7]['cia'] = 22;

$doc[8]['num_cia'] = 23;
$doc[8]['tipo_doc'] = 22;
$doc[8]['cia'] = 22;

$doc[9]['num_cia'] = 41;
$doc[9]['tipo_doc'] = 4;
$doc[9]['cia'] = 40;

$doc[10]['num_cia'] = 41;
$doc[10]['tipo_doc'] = 5;
$doc[10]['cia'] = 40;

$doc[11]['num_cia'] = 41;
$doc[11]['tipo_doc'] = 22;
$doc[11]['cia'] = 40;

$doc[12]['num_cia'] = 45;
$doc[12]['tipo_doc'] = 4;
$doc[12]['cia'] = 28;

$doc[13]['num_cia'] = 45;
$doc[13]['tipo_doc'] = 5;
$doc[13]['cia'] = 28;

$doc[14]['num_cia'] = 45;
$doc[14]['tipo_doc'] = 22;
$doc[14]['cia'] = 28;

$doc[15]['num_cia'] = 51;
$doc[15]['tipo_doc'] = 4;
$doc[15]['cia'] = 43;

$doc[16]['num_cia'] = 51;
$doc[16]['tipo_doc'] = 5;
$doc[16]['cia'] = 43;

$doc[17]['num_cia'] = 51;
$doc[17]['tipo_doc'] = 22;
$doc[17]['cia'] = 43;

$doc[18]['num_cia'] = 56;
$doc[18]['tipo_doc'] = 4;
$doc[18]['cia'] = 27;

$doc[19]['num_cia'] = 56;
$doc[19]['tipo_doc'] = 5;
$doc[19]['cia'] = 27;

$doc[20]['num_cia'] = 56;
$doc[20]['tipo_doc'] = 22;
$doc[20]['cia'] = 27;

$doc[21]['num_cia'] = 62;
$doc[21]['tipo_doc'] = 4;
$doc[21]['cia'] = 60;

$doc[22]['num_cia'] = 63;
$doc[22]['tipo_doc'] = 4;
$doc[22]['cia'] = 44;

$doc[23]['num_cia'] = 63;
$doc[23]['tipo_doc'] = 5;
$doc[23]['cia'] = 44;

$doc[24]['num_cia'] = 63;
$doc[24]['tipo_doc'] = 22;
$doc[24]['cia'] = 44;

$doc[25]['num_cia'] = 65;
$doc[25]['tipo_doc'] = 4;
$doc[25]['cia'] = 7;

$doc[26]['num_cia'] = 65;
$doc[26]['tipo_doc'] = 5;
$doc[26]['cia'] = 7;

$doc[27]['num_cia'] = 65;
$doc[27]['tipo_doc'] = 22;
$doc[27]['cia'] = 7;

$doc[28]['num_cia'] = 69;
$doc[28]['tipo_doc'] = 22;
$doc[28]['cia'] = 49;

$doc[29]['num_cia'] = 70;
$doc[29]['tipo_doc'] = 22;
$doc[29]['cia'] = 27;*/

$doc[30]['num_cia'] = 902;
$doc[30]['tipo_doc'] = 22;
$doc[30]['cia'] = 992;

$doc[31]['num_cia'] = 902;
$doc[31]['tipo_doc'] = 22;
$doc[31]['cia'] = 994;

$doc[32]['num_cia'] = 902;
$doc[32]['tipo_doc'] = 22;
$doc[32]['cia'] = 996;

$doc[33]['num_cia'] = 902;
$doc[33]['tipo_doc'] = 27;
$doc[33]['cia'] = 992;

$doc[34]['num_cia'] = 902;
$doc[34]['tipo_doc'] = 27;
$doc[34]['cia'] = 994;

$doc[35]['num_cia'] = 902;
$doc[35]['tipo_doc'] = 27;
$doc[35]['cia'] = 996;

$doc[36]['num_cia'] = 902;
$doc[36]['tipo_doc'] = 20;
$doc[36]['cia'] = 992;

$doc[37]['num_cia'] = 902;
$doc[37]['tipo_doc'] = 20;
$doc[37]['cia'] = 994;

$doc[38]['num_cia'] = 902;
$doc[38]['tipo_doc'] = 20;
$doc[38]['cia'] = 996;

$doc[39]['num_cia'] = 902;
$doc[39]['tipo_doc'] = 28;
$doc[39]['cia'] = 992;

$doc[40]['num_cia'] = 902;
$doc[40]['tipo_doc'] = 28;
$doc[40]['cia'] = 994;

$doc[41]['num_cia'] = 902;
$doc[41]['tipo_doc'] = 28;
$doc[41]['cia'] = 996;

$doc[42]['num_cia'] = 902;
$doc[42]['tipo_doc'] = 117;
$doc[42]['cia'] = 992;

$doc[43]['num_cia'] = 902;
$doc[43]['tipo_doc'] = 117;
$doc[43]['cia'] = 994;

$doc[44]['num_cia'] = 902;
$doc[44]['tipo_doc'] = 117;
$doc[44]['cia'] = 996;

$doc[45]['num_cia'] = 903;
$doc[45]['tipo_doc'] = 51;
$doc[45]['cia'] = 905;

$doc[46]['num_cia'] = 903;
$doc[46]['tipo_doc'] = 51;
$doc[46]['cia'] = 907;

$doc[47]['num_cia'] = 903;
$doc[47]['tipo_doc'] = 27;
$doc[47]['cia'] = 905;

$doc[48]['num_cia'] = 903;
$doc[48]['tipo_doc'] = 27;
$doc[48]['cia'] = 907;

$doc[49]['num_cia'] = 903;
$doc[49]['tipo_doc'] = 20;
$doc[49]['cia'] = 905;

$doc[50]['num_cia'] = 903;
$doc[50]['tipo_doc'] = 20;
$doc[50]['cia'] = 907;

$doc[51]['num_cia'] = 903;
$doc[51]['tipo_doc'] = 28;
$doc[51]['cia'] = 905;

$doc[52]['num_cia'] = 903;
$doc[52]['tipo_doc'] = 28;
$doc[52]['cia'] = 907;

$doc[53]['num_cia'] = 903;
$doc[53]['tipo_doc'] = 117;
$doc[53]['cia'] = 905;

$doc[54]['num_cia'] = 903;
$doc[54]['tipo_doc'] = 117;
$doc[54]['cia'] = 907;

$doc[55]['num_cia'] = 908;
$doc[55]['tipo_doc'] = 22;
$doc[55]['cia'] = 909;

$doc[56]['num_cia'] = 908;
$doc[56]['tipo_doc'] = 22;
$doc[56]['cia'] = 911;

$doc[57]['num_cia'] = 908;
$doc[57]['tipo_doc'] = 22;
$doc[57]['cia'] = 919;

$doc[58]['num_cia'] = 908;
$doc[58]['tipo_doc'] = 20;
$doc[58]['cia'] = 909;

$doc[59]['num_cia'] = 908;
$doc[59]['tipo_doc'] = 20;
$doc[59]['cia'] = 911;

$doc[60]['num_cia'] = 908;
$doc[60]['tipo_doc'] = 20;
$doc[60]['cia'] = 919;

$doc[61]['num_cia'] = 908;
$doc[61]['tipo_doc'] = 28;
$doc[61]['cia'] = 909;

$doc[62]['num_cia'] = 908;
$doc[62]['tipo_doc'] = 28;
$doc[62]['cia'] = 911;

$doc[63]['num_cia'] = 908;
$doc[63]['tipo_doc'] = 28;
$doc[63]['cia'] = 919;

$doc[64]['num_cia'] = 908;
$doc[64]['tipo_doc'] = 117;
$doc[64]['cia'] = 909;

$doc[65]['num_cia'] = 908;
$doc[65]['tipo_doc'] = 117;
$doc[65]['cia'] = 911;

$doc[66]['num_cia'] = 908;
$doc[66]['tipo_doc'] = 117;
$doc[66]['cia'] = 919;

$doc[67]['num_cia'] = 910;
$doc[67]['tipo_doc'] = 22;
$doc[67]['cia'] = 918;

$doc[68]['num_cia'] = 910;
$doc[68]['tipo_doc'] = 20;
$doc[68]['cia'] = 918;

$doc[69]['num_cia'] = 910;
$doc[69]['tipo_doc'] = 27;
$doc[69]['cia'] = 918;

$doc[70]['num_cia'] = 912;
$doc[70]['tipo_doc'] = 22;
$doc[70]['cia'] = 915;

$doc[71]['num_cia'] = 912;
$doc[71]['tipo_doc'] = 22;
$doc[71]['cia'] = 920;

$doc[72]['num_cia'] = 912;
$doc[72]['tipo_doc'] = 20;
$doc[72]['cia'] = 915;

$doc[73]['num_cia'] = 912;
$doc[73]['tipo_doc'] = 20;
$doc[73]['cia'] = 920;

$doc[74]['num_cia'] = 912;
$doc[74]['tipo_doc'] = 27;
$doc[74]['cia'] = 915;

$doc[75]['num_cia'] = 912;
$doc[75]['tipo_doc'] = 27;
$doc[75]['cia'] = 920;

$doc[76]['num_cia'] = 912;
$doc[76]['tipo_doc'] = 117;
$doc[76]['cia'] = 915;

$doc[77]['num_cia'] = 912;
$doc[77]['tipo_doc'] = 117;
$doc[77]['cia'] = 920;

$doc[78]['num_cia'] = 913;
$doc[78]['tipo_doc'] = 22;
$doc[78]['cia'] = 914;

$doc[79]['num_cia'] = 913;
$doc[79]['tipo_doc'] = 22;
$doc[79]['cia'] = 917;

$doc[80]['num_cia'] = 913;
$doc[80]['tipo_doc'] = 117;
$doc[80]['cia'] = 914;

$doc[81]['num_cia'] = 913;
$doc[81]['tipo_doc'] = 117;
$doc[81]['cia'] = 917;

$doc[82]['num_cia'] = 922;
$doc[82]['tipo_doc'] = 22;
$doc[82]['cia'] = 923;

$doc[83]['num_cia'] = 922;
$doc[83]['tipo_doc'] = 22;
$doc[83]['cia'] = 934;

$doc[84]['num_cia'] = 924;
$doc[84]['tipo_doc'] = 22;
$doc[84]['cia'] = 926;

$doc[85]['num_cia'] = 927;
$doc[85]['tipo_doc'] = 22;
$doc[85]['cia'] = 928;

$doc[86]['num_cia'] = 929;
$doc[86]['tipo_doc'] = 22;
$doc[86]['cia'] = 930;

$doc[87]['num_cia'] = 929;
$doc[87]['tipo_doc'] = 22;
$doc[87]['cia'] = 931;

$doc[88]['num_cia'] = 932;
$doc[88]['tipo_doc'] = 22;
$doc[88]['cia'] = 933;


$db->query("DELETE FROM imagenes_temp");

for ($i = 30; $i <= 88; $i++) {
	echo $doc[$i]['num_cia'] . " - " . $doc[$i]['tipo_doc'] . " " . $doc[$i]['cia'] . "<br>";
	
	// Obtener documentos e imagenes
	$db->query("INSERT INTO imagenes_temp (imagen) SELECT imagen FROM imagenes WHERE id_doc = (SELECT id_doc FROM documentos WHERE num_cia = {$doc[$i]['cia']} AND tipo_doc = {$doc[$i]['tipo_doc']}) ORDER BY id_img");
	
	// Resetear ultimo indice
	$sql = "SELECT setval('imagenes_indice_seq', 1, false);\n";
	// Copiar documento
	$sql .= "INSERT INTO documentos (num_cia,fecha,descripcion,tipo_doc) SELECT {$doc[$i]['num_cia']},CURRENT_DATE,descripcion,tipo_doc FROM documentos WHERE num_cia = {$doc[$i]['cia']} AND tipo_doc = {$doc[$i]['tipo_doc']};\n";
	// Mover todas las imagenes que se encuentran en temporal a la tabla de imagenes
	$sql .= "INSERT INTO imagenes (id_doc,imagen) SELECT (SELECT last_value FROM documentos_id_doc_seq),imagen FROM imagenes_temp;\n";
	// Borrar imagenes de la tabla temporal
	$sql .= "DELETE FROM imagenes_temp";
	
	$db->query($sql);
}
?>