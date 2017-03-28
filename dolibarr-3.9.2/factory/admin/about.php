<?php
/* Copyright (C) 2014	  Charles-Fr BENKE	 <charles.fr@benke.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		htdocs/factory/admin/about.php
 * 	\ingroup	factory
 * 	\brief		about page
 */

// Dolibarr environment
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory


// Libraries
require_once("../core/lib/factory.lib.php");


// Translations
$langs->load("factory@factory");

// Access control
if (!$user->admin)
	accessforbidden();

/*
 * View
 */
$page_name = $langs->trans("About");
llxHeader('', $page_name);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("FactorySetup"),$linkback,'setup');

// Configuration header
$head = factory_admin_prepare_head();
dol_fiche_head($head, 'about', $langs->trans("FactorySetup"), 0, "factory@factory");

// About page goes here
print '<br>';
print $langs->trans("PatasMonkeyPresent").'<br><br>';

$url='http://www.patas-monkey.com';
print '<a href="'.$url.'" target="_blank"><img border="0" width="180" src="'.DOL_URL_ROOT.'/factory/img/patas-monkey_logo.png"></a>';


print '<br><br>';
print $langs->trans("MoreModulesLink").'<br>';
$url='http://www.dolistore.com/search.php?search_query=benke';
print '<a href="'.$url.'" target="_blank"><img border="0" width="180" src="'.DOL_URL_ROOT.'/theme/dolistore_logo.png"></a><br><br><br>';

print '<br><br>';
print_titre($langs->trans("Changelog"));
print '<br>';
print  nl2br (file_get_contents('../changelog.txt'));

llxFooter();
$db->close();
?>
