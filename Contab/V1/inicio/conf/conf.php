<?php

if ( is_session_started_conf() === FALSE ) @session_start();

if(isset($_SESSION['ENTITY'])){
    $ENTITY= (int)$_SESSION['ENTITY'];
}else{
    $ENTITY=1;
}


define('DB_HOST','localhost');
define('DB_USER','pruebascemsa'); //usuario de la base de datos
define('DB_PASS','PRu3vA$C3M');  //contraseña de la base de datos
define('DB_NAME','pruebascemsa');  //nombre de la base de datos
define('DB_CHARSET','utf-8');
define('IMG_ROOT','http://cloud.dolibarrmexico.com:88/dolicemsa/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file=thumbs%2FLOGO-CEMSA_mini.jpg');
define('PREFIX','llx_'); 
#Ejemplo de URL, favor de omitir el último /
define('DOL_URL','http://cloud.dolibarrmexico.com:88/dolicemsa');
define('ENTITY',$ENTITY);
define('HIDOLI',0); //Ocultar divs de relación de facturas cliente/proveedor

function is_session_started_conf()
{
            if ( php_sapi_name() !== 'cli' ) {
                if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                    return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
                } else {
                    return session_id() === '' ? FALSE : TRUE;
                }
            }
}

?>
