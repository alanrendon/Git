<?php
// put full path to Smarty.class.php
require('/usr/local/lib/php/Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->template_dir = 'smarty/templates';
$smarty->compile_dir = 'smarty/templates_c';
$smarty->cache_dir = 'smarty/cache';
$smarty->config_dir = 'smarty/configs';

$smarty->assign('name', 'Ned');
$smarty->display('prueba_smarty.tpl');

?>