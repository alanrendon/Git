<?php
$ts1 = mktime(0, 0, 0, 6, 24, 1988);
$ts2 = mktime(0, 0, 0, 9, 1, 1979);

$dif = $ts1 - $ts2;

$days = $dif / 86400;
echo $days;
?>