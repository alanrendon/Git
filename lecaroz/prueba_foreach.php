<?php
$array = array(0,1,2,3,4,5,6,7,8,9);

foreach ($array as $value) {
	echo "$value<br>";
	if ($value == 6) break;
}

?>