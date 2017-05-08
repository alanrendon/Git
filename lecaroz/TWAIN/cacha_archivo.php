<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?
$nombre_archivo = $_FILES['file']['name'][0];
$archivo_temp   = $_FILES['file']['tmp_name'][0];
if (move_uploaded_file($archivo_temp,"/var/www/html/lecaroz/TWAIN/images/" . $nombre_archivo))
	echo "<img src='$nombre_archivo'>";
else
	echo "No se pudo subir el archivo";
?>
</body>
</html>
