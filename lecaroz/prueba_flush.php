<?php

ob_start();

for ($i = 0; $i < 10; $i++) {
	echo '<img src="imagenes/cubo.GIF" width="16" height="32">';
	
	ob_flush();
	flush();
	
	sleep(1);
}

?>