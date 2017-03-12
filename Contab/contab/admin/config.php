<?php
require("../../main.inc.php");
llxHeader('',"Configuracion");
$h=0;
$head[$h][0] = "admin.php";
$head[$h][1] = "Configuracion";
$head[$h][2] = "uno";
$h++;
$head[$h][0] = "config.php";
$head[$h][1] = "Configuracion2";
$head[$h][2] = "dos";
$h++;
global $langs;
$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title,$linkback,'setup');
dol_fiche_head($head, '1', "Configuracion", 0, 'generic');