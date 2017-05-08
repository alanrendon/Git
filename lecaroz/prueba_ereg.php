<?php
$expresion = '^(\+|-)?([0-9]{0,})(\.)?([0-9]{0,})?$';

ereg($expresion, '+1231.0', $tmp);
print_r($tmp);

echo round(12.45) . '<br>';
echo round(12.45, 1) . '<br>';
echo round(12.45, -1) . '<br>';
echo number_format(12.45, -1) . '<br>';
?>