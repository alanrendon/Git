<?php
$ch = curl_init("http://192.168.1.250/lecaroz/prueba_datos.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "data[]=0&data[]=1&data[]=2&data[]=3");
curl_exec($ch);
if (curl_errno($ch)) {
           print curl_error($ch);
       } else {
           curl_close($ch);
       }

?>