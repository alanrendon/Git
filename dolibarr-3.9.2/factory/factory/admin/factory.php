<?php
/* Copyright (C) 2014	  Charles-Fr BENKE	 <charles.fr@benke.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	  \file	   htdocs/factory/admin/factory.php
 *		\ingroup	factory
 *		\brief	  Page to setup factory module
 */

require "../../main.inc.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

require_once DOL_DOCUMENT_ROOT.'/factory/class/factory.class.php';
require_once DOL_DOCUMENT_ROOT.'/factory/core/lib/factory.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("factory@factory");
$langs->load("admin");
$langs->load("errors");

if (! $user->admin) accessforbidden();

$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');


/*
 * Actions
 */

if ($action == 'updateMask')
{
	$maskconst=GETPOST('maskconst','alpha');
	$maskvalue=GETPOST('maskvalue','alpha');
	if ($maskconst) $res = dolibarr_set_const($db,$maskconst,$maskvalue,'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;

 	if (! $error)
	{
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	}
	else
	{
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
}

if ($action == 'setmod')
{
	// TODO Verifier si module numerotation choisi peut etre active
	// par appel methode canBeActivated

	dolibarr_set_const($db, "FACTORY_ADDON",$value,'chaine',0,'',$conf->entity);
}

if ($action == 'setdefaultother')
{
	// save the setting
	$res = dolibarr_set_const($db, "FACTORY_CATEGORIE_ROOT",GETPOST('root_categ','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db, "FACTORY_CATEGORIE_CATALOG",GETPOST('catalog_categ','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db, "FACTORY_CATEGORIE_VARIANT",GETPOST('variant_categ','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
 	if (! $error)
	{
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	}
	else
	{
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
	
}

if ($action == 'set')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$type='factory';
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
	$sql.= " VALUES ('".$db->escape($value)."','".$type."',".$conf->entity.", ";
	$sql.= ($label?"'".$db->escape($label)."'":'null').", ";
	$sql.= (! empty($scandir)?"'".$db->escape($scandir)."'":"null");
	$sql.= ")";
	if ($db->query($sql))
	{

	}
}

if ($action == 'del')
{
	$type='factory';
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql.= " WHERE nom = '".$db->escape($value)."'";
	$sql.= " AND type = '".$type."'";
	$sql.= " AND entity = ".$conf->entity;

	if ($db->query($sql))
	{
		if ($conf->global->FACTORY_ADDON_PDF == "$value") dolibarr_del_const($db, 'FACTORY_ADDON_PDF',$conf->entity);
	}
}

if ($action == 'setdoc')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$db->begin();

	if (dolibarr_set_const($db, "FACTORY_ADDON_PDF",$value,'chaine',0,'',$conf->entity))
	{
		$conf->global->FACTORY_ADDON_PDF = $value;
	}

	// On active le modele
	$type='factory';

	$sql_del = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql_del.= " WHERE nom = '".$db->escape($value)."'";
	$sql_del.= " AND type = '".$type."'";
	$sql_del.= " AND entity = ".$conf->entity;
	dol_syslog("admin/factory.php ".$sql_del);
	$result1=$db->query($sql_del);

	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
	$sql.= " VALUES ('".$value."', '".$type."', ".$conf->entity.", ";
	$sql.= ($label?"'".$db->escape($label)."'":'null').", ";
	$sql.= (! empty($scandir)?"'".$scandir."'":"null");
	$sql.= ")";
	dol_syslog("admin/factory.php ".$sql);
	$result2=$db->query($sql);
	if ($result1 && $result2)
	{
		$db->commit();
	}
	else
	{
		dol_syslog("admin/factory.php ".$db->lasterror(), LOG_ERR);
		$db->rollback();
	}
}

if ($action == 'ChangePriceSetting')
{
	dolibarr_set_const($db, "ChangePriceSetting", GETPOST('value'),'chaine',0,'',$conf->entity);
	// selon l'activation / désactivation on met  en place des tabs sur les catégorie de produits
	if (GETPOST('value') == 1)
	{
		// on ajoute l'onglet de paramétrage des prix multiple
		$onglet = 'categories_0:+factory:changeprice:@factory:/factory/changeprice/categories.php?type=0&id=__ID__';
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."const (";
		$sql.= "name";
		$sql.= ", type";
		$sql.= ", value";
		$sql.= ", note";
		$sql.= ", visible";
		$sql.= ", entity";
		$sql.= ")";
		$sql.= " VALUES (";
		$sql.= " 'MAIN_MODULE_FACTORY_TABS_CATEG'";
		$sql.= ", 'chaine'";
		$sql.= ", ".$db->encrypt($onglet,1);
		$sql.= ", null";
		$sql.= ", '1'";
		$sql.= ", ".$conf->entity;
		$sql.= ")";

		$resql=$db->query($sql);
		
		// on ajoute le menu
		require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
		$menu = new Menubase($db);
		$menu->menu_handler='all';
		$menu->module='factory';
		$menu->type='left';
		$menu->fk_menu=-1;
		$menu->fk_mainmenu="products";
		$menu->fk_leftmenu="product";
		$menu->titre=$langs->trans("Changeprice");
		$menu->url='/factory/changeprice/changeprice.php';
		$menu->langs="factory@factory";
		$menu->position=110;
		$menu->perms=1;
		$menu->target="";
		$menu->user=2;
		$menu->enabled=1;
		$result=$menu->create($user);
	}
	else
	{
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."const";
        $sql.= " WHERE name = 'MAIN_MODULE_FACTORY_TABS_CATEG'";
        $sql.= " AND entity = ".$conf->entity;

        $resql=$db->query($sql);
        
        $sql="delete from ".MAIN_DB_PREFIX."menu where url='/factory/changeprice/changeprice.php'";
        $resql=$db->query($sql);
	}

	
}


$ChangePriceSetting=$conf->global->ChangePriceSetting;



/*
 * View
 */

$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);

$title = $langs->trans('FactorySetup');
$tab = $langs->trans("FactorySetup");


llxHeader("",$langs->trans("FactorySetup"),'EN:Factory_Configuration|FR:Configuration_module_Factory|ES:Configuracion_Factory');

$form=new Form($db);
$htmlother=new FormOther($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("FactorySetup"),$linkback,'setup');

$head = factory_admin_prepare_head();
dol_fiche_head($head, 'setup', $tab, 0, 'factory@factory');


/*
 *  percent of order
 */


print_titre($langs->trans("FactoryNumberingModule"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap>'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print '</tr>'."\n";

clearstatcache();

foreach ($dirmodels as $reldir)
{
	$dir = dol_buildpath($reldir."factory/core/modules/factory/");
	if (is_dir($dir))
	{
		$handle = opendir($dir);
		if (is_resource($handle))
		{
			$var=true;

			while (($file = readdir($handle))!==false)
			{
				if (! is_dir($dir.$file) || (substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS'))
				{
					$filebis = $file;
					$classname = preg_replace('/\.php$/','',$file);
					// For compatibility
					if (! is_file($dir.$filebis))
					{
						$filebis = $file."/".$file.".modules.php";
						$classname = "mod_factory_".$file;
					}
					if (! class_exists($classname) && is_readable($dir.$filebis) && (preg_match('/mod_/',$filebis) || preg_match('/mod_/',$classname)) && substr($filebis, dol_strlen($filebis)-3, 3) == 'php')
					{
						// Chargement de la classe de numerotation
						//print "Aqui::".$dir.":-".$filebis."<br>";
						//require_once($dir.$filebis);
						if($filebis=='._mod_babouin.php'){
							require_once("../core/modules/factory/mod_babouin.php");	
							$module = new mod_babouin($db);
						}else{
							if($filebis=='._mod_mandrill.php'){
								require_once("../core/modules/factory/mod_mandrill.php");
								$module = new mod_mandrill($db);
							}else{
								require_once($dir.$filebis);
		
								$module = new $classname($db);
							}
						}
						// Show modules according to features level
						if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
						if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

						if ($module->isEnabled())
						{
							$var = !$var;
							print '<tr '.$bc[$var].'><td width="100">';
							echo preg_replace('/mod_factory_/','',preg_replace('/\.php$/','',$file));
							print "</td><td>\n";

							print $module->info();

							print '</td>';

							// Show example of numbering module
							print '<td nowrap="nowrap">';
							$tmp=$module->getExample();
							if (preg_match('/^Error/',$tmp)) { $langs->load("errors"); print '<div class="error">'.$langs->trans($tmp).'</div>'; }
							elseif ($tmp=='NotConfigured') print $langs->trans($tmp);
							else print $tmp;
							print '</td>'."\n";

							print '<td align="center">';
							//print "> ".$conf->global->FACTORY_ADDON." - ".$file;
							if ($conf->global->FACTORY_ADDON == $file || $conf->global->FACTORY_ADDON.'.php' == $file)
							{
								print img_picto($langs->trans("Activated"),'switch_on');
							}
							else
							{
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmod&value='.preg_replace('/\.php$/','',$file).'&scandir='.$module->scandir.'&label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
							}
							print '</td>';

							$factory=new Factory($db);
							$factory->initAsSpecimen();

							// Example for standard invoice
							$htmltooltip='';
							$htmltooltip.=''.$langs->trans("Version").': <b>'.$module->getVersion().'</b><br>';
							$nextval=$module->getNextValue($mysoc,'');
							if ("$nextval" != $langs->trans("NotAvailable"))	// Keep " on nextval
							{
								$htmltooltip.=$langs->trans("NextValueForFactory").': ';
								if ($nextval)
								{
									$htmltooltip.=$nextval.'<br>';
								}
								else
								{
									$htmltooltip.=$langs->trans($module->error).'<br>';
								}
							}

							print '<td align="center">';
							print $form->textwithpicto('',$htmltooltip,1,0);

							if ($conf->global->FACTORY_ADDON.'.php' == $file)  // If module is the one used, we show existing errors
							{
								if (! empty($module->error)) dol_htmloutput_mesg($module->error,'','error',1);
							}

							print '</td>';

							print "</tr>\n";

						}
					}
				}
			}
			closedir($handle);
		}
	}
}
print '</table>';


print '<br>';
print_titre($langs->trans("FactoryDeclinationFeature"));
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setdefaultother">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td width="200px">'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap>'.$langs->trans("Value").'</td>';
print '</tr>'."\n";
$var = true;
if (! empty($conf->categorie->enabled))
{
	
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("RootCategorie").'</td>';
	print '<td>'.$langs->trans("InfoRootCategorie").'</td>';
	print '<td nowrap>'.$htmlother->select_categories(0,$conf->global->FACTORY_CATEGORIE_ROOT,'root_categ',1).'</td>';
	print '</tr>'."\n";
	
	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("VariantCategorie").'</td>';
	print '<td>'.$langs->trans("InfoVariantCategorie").'</td>';
	print '<td nowrap>'.$htmlother->select_categories(0,$conf->global->FACTORY_CATEGORIE_VARIANT,'variant_categ',1).'</td>';
	print '</tr>'."\n";

	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("CatalogCategorie").'</td>';
	print '<td>'.$langs->trans("InfoCatalogCategorie").'</td>';
	print '<td nowrap>'.$htmlother->select_categories(0,$conf->global->FACTORY_CATEGORIE_CATALOG,'catalog_categ',1).'</td>';
	print '</tr>'."\n";
	
	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td colspan=3 align= center><input type=submit value='.$langs->trans("Save").'></td>';
	print '</tr>'."\n";
}
else
{
	print '<tr '.$bc[$var].'>';
	print '<td colspan=3>'.$langs->trans("ThisFeatureNeedToActivateCategorieModule").'</td>';
	print '</tr>'."\n";
}
print '</table>';
print '</form>';

print '<br>';
print_titre($langs->trans("FactoryPriceChangeFeature"));
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setdefaultPrice">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap>'.$langs->trans("Status").'</td>';
print '</tr>'."\n";
$var = true;
if (! empty($conf->categorie->enabled))
{
	print '<tr>';
	print '<td>'.$langs->trans("ChangePriceSetting").'</td>';
	print '<td>';
	if ( $ChangePriceSetting ==1)
	{	
		print '<a href="'.$_SERVER["PHP_SELF"].'?action=ChangePriceSetting&value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
	}	
	else
	{	
		print '<a href="'.$_SERVER["PHP_SELF"].'?action=ChangePriceSetting&value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
	}	
	print '</td></tr>';
}
else
{
	print '<tr '.$bc[$var].'>';
	print '<td colspan=3>'.$langs->trans("ThisFeatureNeedToActivateCategorieModule").'</td>';
	print '</tr>'."\n";
}
print '</table>';
print '</form>';



/*
 *  Document templates generators
 */
print '<br>';
print_titre($langs->trans("FactoryPDFModules"));

// Load array def with activated templates
$type='factory';
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = '".$type."'";
$sql.= " AND entity = ".$conf->entity;
$resql=$db->query($sql);
if ($resql)
{
	$i = 0;
	$num_rows=$db->num_rows($resql);
	while ($i < $num_rows)
	{
		$array = $db->fetch_array($resql);
		array_push($def, $array[0]);
		$i++;
	}
}
else
{
	dol_print_error($db);
}

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="60">'.$langs->trans("Default").'</td>';
print '<td align="center" width="32" colspan="2">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();

$var=true;
foreach ($dirmodels as $reldir)
{
	foreach (array('','/doc') as $valdir)
	{
		$dir = dol_buildpath($reldir."factory/core/modules/factory".$valdir);

		if (is_dir($dir))
		{
			$handle=opendir($dir);
			if (is_resource($handle))
			{
				while (($file = readdir($handle))!==false)
				{
					$filelist[]=$file;
				}
				closedir($handle);
				arsort($filelist);

				foreach($filelist as $file)
				{
					if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
					{
						if (file_exists($dir.'/'.$file))
						{
							$name = substr($file, 4, dol_strlen($file) -16);
							$classname = substr($file, 0, dol_strlen($file) -12);

							require_once($dir.'/'.$file);
							$module = new $classname($db);

							$modulequalified=1;
							if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
							if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

							if ($modulequalified)
							{
								$var = !$var;
								print '<tr '.$bc[$var].'><td width="100">';
								print (empty($module->name)?$name:$module->name);
								print "</td><td>\n";
								if (method_exists($module,'info')) print $module->info($langs);
								else print $module->description;
								print '</td>';

								// Active
								if (in_array($name, $def))
								{
									print '<td align="center">'."\n";
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&value='.$name.'">';
									print img_picto($langs->trans("Enabled"),'switch_on');
									print '</a>';
									print '</td>';
								}
								else
								{
									print "<td align=\"center\">\n";
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&value='.$name.'&scandir='.$module->scandir.'&label='.urlencode($module->name).'">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
									print "</td>";
								}

								// Defaut
								print "<td align=\"center\">";
								if ($conf->global->FACTORY_ADDON_PDF == "$name")
								{
									print img_picto($langs->trans("Default"),'on');
								}
								else
								{
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&value='.$name.'&scandir='.$module->scandir.'&label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
								}
								print '</td>';

								// Info
								$htmltooltip =	''.$langs->trans("Name").': '.$module->name;
								$htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
								if ($module->type == 'pdf')
								{
									$htmltooltip.='<br>'.$langs->trans("Width").'/'.$langs->trans("Height").': '.$module->page_largeur.'/'.$module->page_hauteur;
								}
								print '<td align="center">';
								print $form->textwithpicto('',$htmltooltip,1,0);
								print '</td>';

								// Preview
								print '<td align="center">';
								if ($module->type == 'pdf')
								{
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
								}
								else
								{
									print img_object($langs->trans("PreviewNotAvailable"),'generic');
								}
								print '</td>';

								print "</tr>\n";
							}
						}
					}
				}
			}
		}
	}
}
print '</table>';

dol_htmloutput_mesg($mesg);

$db->close();
llxFooter();
?>
