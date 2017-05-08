<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://root:pobgnj@127.0.0.1:5432/scans");
$session = new sessionclass($dsn);

if (isset($_GET['id'])) {
	$innerHTML = '';
	
	if ($_GET['accion'] == 'expandir') {
		// Datos completos del contacto
		$sql = 'SELECT "IdContacto", "Nombre", "Contacto", "Numero", "Puesto", "Email", "Observaciones" FROM "Directorio" WHERE "IdContacto" = ' . $_GET['id'];
		$reg = $db->query($sql);
		
		// Teléfonos asociados al contacto
		$sql = 'SELECT "Telefono", "TipoTel", "ObsTel" FROM "TelefonosDirectorio" WHERE "IdContacto" = ' . $_GET['id'] . ' AND "Status" = 1 ORDER BY "IdTelefono"';
		$tels = $db->query($sql);
		
		// Tipos del contacto
		$sql = 'SELECT "Tipo" FROM "TiposContacto" AS "TC" LEFT JOIN "CatalogoTiposContacto" USING ("IdTipo") WHERE "IdContacto" = ' . $_GET['id'] . ' AND "TC"."Status" = 1 ORDER BY "IdTipoContacto"';
		$tipos = $db->query($sql);
		
		$innerHTML .= '<div id="tarjeta">';
		if ($db_scans->query('SELECT "IdImgTarjeta" FROM "ImagenTarjeta" WHERE "IdContacto" = ' . $_GET['id']))
			$innerHTML .= '<a href="Tarjeta.php?id=' . $_GET['id'] . '" target="_blank"><img src="Tarjeta.php?width=240&id=' . $_GET['id'] . '" /></a>';
		else
			$innerHTML .= '<img src="imagenes/NoTarjeta.png" />';
		$innerHTML .= '</div>';
		$innerHTML .= '<div id="datos">';
		$innerHTML .= '<div style="float:right;font-weight:bold;">#' . $reg[0]['Numero'] . '</div>';
		$innerHTML .= '<div id="nombre">';
		$innerHTML .= $reg[0]['Nombre'] . '</div>';
		$innerHTML .= '<div id="contacto">';
		$innerHTML .= $reg[0]['Contacto'] != '' ? $reg[0]['Contacto'] : $reg[0]['Nombre'];
		$innerHTML .= $reg[0]['Puesto'] != '' ? ' <span style="color:#666666">[' . $reg[0]['Puesto'] . ']</span>' : '';
		$innerHTML .= '</div>';
		
		if ($tels) {
			$innerHTML .= '<!-- telefonos : ' . count($tels) . ' -->';
			$innerHTML .= '<div id="elemento"><ul>';
			foreach ($tels as $tel) {
				switch ($tel['TipoTel']) {
					case 1:  $innerHTML .= '<li class="celular">'; break;
					case 2:  $innerHTML .= '<li class="casa">';    break;
					case 3:  $innerHTML .= '<li class="trabajo">'; break;
					case 4:  $innerHTML .= '<li class="fax">';     break;
					case 5:  $innerHTML .= '<li class="otro">';    break;
					default: $innerHTML .= '<li class="otro">';
				}
				$innerHTML .= $tel['Telefono'] . (trim($tel['ObsTel']) != '' ? ' <span style="color:#666666">[' . trim($tel['ObsTel']) . ']</span>' : '') . '</li>';
			}
			
			if ($reg[0]['Email'] != '')
				$innerHTML .= '<li class="email">' . ($reg[0]['Email'] != '' ? '<a href="mailto:' . $reg[0]['Email'] . '">' . $reg[0]['Email'] . '</a>' : '<span style="font-size:9pt; color:#666666">Sin e-mail</span>') . '</li>';
			
			$innerHTML .= '</div></ul>';
		}
		
		if ($reg[0]['Observaciones'] != '')
			$innerHTML .= '<div id="elemento"><span class="titulo">Observaciones</span><br />' . $reg[0]['Observaciones'] . '</div>';
		
		if ($tipos) {
			$innerHTML .= '<div id="elemento"><span class="titulo">Tipos</span><br /><span class="tipos">';
			foreach ($tipos as $i => $t)
				$innerHTML .= $t['Tipo'] . ($i < count($tipos) - 1 ? ', ' : '');
			$innerHTML .= '</span></div>';
		}
		$innerHTML .= '</div>';
	}
	else if ($_GET['accion'] == 'contraer') {
		$sql = 'SELECT "IdContacto", "Nombre", "Contacto", "Numero", (SELECT "Telefono" FROM "TelefonosDirectorio" WHERE "IdContacto" = "Dir"."IdContacto" AND "Status" = 1 ORDER BY "IdTelefono" LIMIT 1) AS "Telefono", "Email" FROM "Directorio" "Dir" WHERE "IdContacto" = ' . $_GET['id'];
		$reg = $db->query($sql);
		
		$innerHTML .= '<div style="float:right;font-weight:bold;">#' . $reg[0]['Numero'] . '</div>';
		$innerHTML .= '<div id="nombre">' . $reg[0]['Nombre'] . '</div>';
		$innerHTML .= '<span style="font-weight:bold;">' . $reg[0]['Contacto'] . '</span><br />';
		$innerHTML .= $reg[0]['Telefono'] . '<br />';
		$innerHTML .= '<a href="mailto:' . $reg[0]['Email'] . '">' . $reg[0]['Email'] . '</a>';
	}
	else if ($_GET['accion'] == 'eliminar') {
		$sql = 'UPDATE "Directorio" SET "Status" = 0 WHERE "IdContacto" = ' . "$_GET[id];\n";
		$sql .= 'UPDATE "TelefonosDirectorio" SET "Status" = 0 WHERE "IdContacto" = ' . "$_GET[id];\n";
		$sql .= 'UPDATE "TiposContacto" SET "Status" = 0 WHERE "IdContacto" = ' . "$_GET[id];\n";
		$db->query($sql);
	}
	
	die($innerHTML);
}

$tpl = new TemplatePower('smarty/templates/ConsultaDirectorio.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = 'SELECT "IdContacto", "Nombre", "Contacto", "Numero", (SELECT "Telefono" FROM "TelefonosDirectorio" WHERE "IdContacto" = "Dir"."IdContacto" AND "Status" = 1 ORDER BY "IdTelefono" LIMIT 1) AS "Telefono", (SELECT count("Telefono") FROM "TelefonosDirectorio" WHERE "IdContacto" = "Dir"."IdContacto" AND "Status" = 1) AS "NumTels", "Email", (SELECT count("IdTipo") FROM "TiposContacto" WHERE "IdContacto" = "Dir"."IdContacto" AND "Status" = 1) AS "NumTipos", "Observaciones" FROM "Directorio" "Dir" WHERE "Status" = 1';
if (isset($_GET['letter'])) {
	if ($_GET['letter'] == '#')
		$sql .= ' AND "Nombre" ~ \'^\d\'';
	else
		$sql .= " AND \"Nombre\" LIKE '$_GET[letter]%'";
}
$sql .= ' ORDER BY "Nombre"';
$result = $db->query($sql);

if ($result)
	foreach ($result as $i => $reg) {
		$tpl->newBlock('contacto');
		$tpl->assign('color_row', ($i + 1) % 2 == 0 ? 'on' : 'off');
		
		$tpl->assign('id', $reg['IdContacto']);
		$tpl->assign('offset', $reg['NumTels'] * 29 + ($reg['Email'] != '' ? 29 : 0) + $reg['NumTipos'] * 37 + ($reg['Observaciones'] != '' ? 37 : 0));
		$tpl->assign('Nombre', $reg['Nombre']);
		$tpl->assign('Contacto', $reg['Contacto']);
		$tpl->assign('Telefono', $reg['Telefono']);
		$tpl->assign('Email', $reg['Email']);
		$tpl->assign('Numero', $reg['Numero']);
	}

$letters = array('#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
foreach ($letters as $letter) {
	$tpl->newBlock('letter');
	$tpl->assign('letter', $letter);
}

$tpl->printToScreen();
?>