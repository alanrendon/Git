<?php
$birthday = mktime(15, 15, 0, 9, 1, 1979);
$today = time();
$next_birthday = mktime(15, 15, 0, 9, 1, 2007);

$transcurred_seconds = $today - $birthday;
$avaliable_seconds = $next_birthday - $today;

$days_transcurred = intval($transcurred_seconds / 86400);
$rest_days = intval($avaliable_seconds / 86400);

echo "$days_transcurred | $rest_days";
?>