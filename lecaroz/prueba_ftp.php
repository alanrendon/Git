<?php
$servidor_ftp = '192.168.1.251';
$nombre_usuario_ftp = 'lecaroz';
$contrasenya_ftp = 'pobgnj';


// establecer una conexion basica
$id_con = ftp_connect($servidor_ftp);

if (!$id_con) die('No me pude conectar');

// inicio de sesion con nombre de usuario y contrasenya
$resultado_login = ftp_login($id_con, $nombre_usuario_ftp, $contrasenya_ftp);

// chequear la conexion
if ((!$id_con) || (!$resultado_login)) {
        echo "&iexcl;La conexi&oacute;n FTP ha fallado!";
        echo "Se ha intentado la conexion con $servidor_ftp para el " .
             "usuario $nombre_usuario_ftp";
        exit;
    } else {
        echo "Conectado con $servidor_ftp, para el usuario $nombre_usuario_ftp";
    }

echo '<br>' . ftp_pwd($id_con);
echo '<pre>';
//ftp_chdir();
print_r(ftp_nlist($id_con, '/opt'));
echo '</pre>';

ftp_get($id_con, 'facturas/prueba.xml', '/opt/archivos/378-F579.xml', FTP_BINARY);

// cargar el archivo
//$carga = ftp_put($id_con, $archivo_destino, $archivo_fuente, FTP_BINARY);

// chequear el status de la carga
/*if (!$carga) {
        echo "&iexcl;La carga FTP ha fallado!";
    } else {
        echo "Se ha cargado $archivo_fuente a $servidor_ftp como $archivo_destino";
    }*/

// cierra la secuencia FTP
ftp_close($id_con);

?>