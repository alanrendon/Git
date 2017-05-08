<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function trim_array_value($value)
{
	$value = trim($value, "\r\n");
	$value = trim($value, "\n");

	return $value;
}

function split_string($string, $sizes)
{
	$pieces = array();

	$position = 0;

	foreach ($sizes as $size) {
		if ($size > 0) {
			$pieces[] = substr($string, $position, $size);
		} else {
			$pieces[] = substr($string, $position);
		}

		$position += $size;
	}

	return $pieces;
}

function obtener_valores($file_rows) {
	global $db;

	$datos = array();

	foreach ($file_rows as $file_row) {
		switch (substr($file_row, 0, 1)) {

			case 'H':

				break;

			case 'D':
				$pieces = split_string($file_row, array(1, 2, 5, 2, 10, 7, 8, 8, 13, 4, 1, 50, 3, 78));

				$num_cia = $db->query("SELECT num_cia FROM catalogo_companias WHERE clabe_cuenta LIKE '%{$pieces[4]}'");

				$datos[] = array(
					'num_cia'		=> $num_cia[0]['num_cia'],
					'cuenta'		=> str_pad($pieces[4], 11, '0', STR_PAD_LEFT),
					'banco'			=> 1,
					'folio'			=> intval($pieces[5], 10),
					'importe'		=> floatval(substr($pieces[8], 0, 11) . '.' . substr($pieces[8], -2, 2)),
					'beneficiario'	=> trim(preg_replace('/\s+/', ' ', $pieces[11]))
				);

				break;

		}
	}

	return $datos;
}

$db = new DBclass($dsn, 'autocommit=yes');

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'procesar_archivo':

			ini_set("auto_detect_line_endings", TRUE);

			$finfo = new finfo(FILEINFO_MIME_TYPE);

			/*
			@ Obtener el tipo de contenido del archivo
			*/

			$mime_type = $finfo->file($_FILES['archivo']['tmp_name']);

			/*
			@ Extraer el contenido del archivo y guardar las lineas en array
			*/

			echo "@ Determinando tipo de archivo y obteniendo contenido ({$mime_type}).<br />";

			if ($mime_type == 'application/x-gzip')
			{
				// Archivo de texto comprimido en formato GZIP
				$file_rows = array_map('trim_array_value', gzfile($_FILES['archivo']['tmp_name']));

				echo "@@ Archivo GZIP, " . count($file_rows) . " linea(s).<br />";

				echo '<pre>' . implode("\n", $file_rows) . '</pre>';
			}
			else if ($mime_type == 'text/plain')
			{
				// Archivo de texto plano
				$file_rows = file($_FILES['archivo']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

				echo "@@ Archivo de texto, " . count($file_rows) . " linea(s).<br />";

				echo '<pre>' . implode("\n", $file_rows) . '</pre>';
			}
			else
			{
				echo "@@@ No es posible extraer el contenido del archivo.<br />";

				die(-1);
			}

			echo "@ Validando cabecera.<br />";

			if (strlen($file_rows[0]) > 192 || substr($file_rows[0], 0, 1) != 'H')
			{
				echo "@@@ El archivo no es válido (Tamaño de cabecera:" . strlen($file_rows[0]) . ").<br />";

				die(-2);
			}

			echo "@ Obteniendo datos.<br />";

			$datos = obtener_valores($file_rows);

			$sql = '';

			foreach ($datos as $row)
			{
				$sql .= "UPDATE cheques SET archivo = TRUE WHERE cuenta = 1 AND num_cia = {$row['num_cia']} AND folio = {$row['folio']} AND fecha >= '2014-01-01';\n";
			}

			// echo '<pre>' . print_r($datos, TRUE) . '</pre>';
			echo "<pre>{$sql}</pre>";

			break;

	}

	die;
}

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Interpretar archivo de chequera de seguridad Banorte</title>
	</head>

	<body>
		<form action="InterpretarArchivoChequeraSeguridadBanorte.php" method="post" enctype="multipart/form-data" name="archivo-form" id="archivo-form">
			<input name="accion" type="hidden" id="accion" value="procesar_archivo">
			<p>
				Archivo: <input name="archivo" type="file" id="archivo" />
			</p>
			<p>
				<input type="submit" value="Procesar" />
			</p>
		</form>
	</body>
</html>

