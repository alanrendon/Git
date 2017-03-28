<?php
/* Copyright (C) 2014 Charles-Fr BENKE  <charles.fr@benke.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	    \file       /extraprice/lib/extraprice.lib.php
 *		\brief      Ensemble de fonctions de base pour le module extraprice
 *      \ingroup    extraprice
 */

function factory_admin_prepare_head()
{
	global $langs, $conf;
	$langs->load('factory@factory');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/factory/admin/factory.php",1);
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'setup';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/admin/factory_extrafields.php",1);
	$head[$h][1] = $langs->trans("Extrafields");
	$head[$h][2] = 'attributes';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/admin/about.php",1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;
	
	/* $head[$h][0] = dol_buildpath("/factory/admin/grupotareas.php",1);
	$head[$h][1] = $langs->trans("Grupo de Tareas");
	$head[$h][2] = 'tareas';
	$h++; */

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'factory_admin');

	return $head;
}

function factory_product_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load('factory@factory');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/factory/product/index.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Composition");
	$head[$h][2] = 'composition';
	$h++;
	$head[$h][0] = dol_buildpath("/factory/product/direct.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("DirectBuild");
	$head[$h][2] = 'directbuild';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/product/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("OrderBuild");
	$head[$h][2] = 'neworderbuild';
	$h++;
	$head[$h][0] = dol_buildpath("/factory/product/list.php?fk_status=1&id=".$object->id,1);
	$head[$h][1] = $langs->trans("OrderBuildList");
	$head[$h][2] = 'orderbuildlist';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'factory_product');

	$head[$h][0] = dol_buildpath("/factory/product/list.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("OrderBuildHistory");
	$head[$h][2] = 'orderbuildhistory';
	$h++;
	
	$head[$h][0] = dol_buildpath("/factory/product/sustituto.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Producto Sustituto");
	$head[$h][2] = 'sutitutoproduct';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab


	return $head;
}

function factory_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load('factory@factory');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/factory/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("FactoryOrder");
	$head[$h][2] = 'factoryorder';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/report.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("FactoryReport");
	$head[$h][2] = 'factoryreport';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/documents.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Documents");
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/factory/note.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Notes");
	$head[$h][2] = 'notes';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'factory');

	$head[$h][0] = dol_buildpath("/factory/info.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Infos");
	$head[$h][2] = 'infos';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab


	return $head;
}


// TOSO : a	intégrer dans le core 
/**
 *	Return list of entrepot (for the stock
  *
 *	@param  string	$selected       Preselected type
 *	@param  string	$htmlname       Name of field in html form
 * 	@param	int		$showempty		Add an empty field
 * 	@param	int		$hidetext		Do not show label before combo box
 * 	@param	int		$idproduct		display the Qty of product id if 
 *  @return	void
 */
function select_entrepot($selected='', $htmlname='entrepotid', $showempty=0, $hidetext=0, $idproduct=0)
{
    global $db,$langs,$user,$conf;

	if (empty($hidetext)) print $langs->trans("EntrepotStock").': ';
	
	// boucle sur les entrepots 
	$sql = "SELECT rowid, label, zip";
	$sql.= " FROM ".MAIN_DB_PREFIX."entrepot";
	//$sql.= " WHERE statut = 1";
	$sql.= " ORDER BY zip ASC";
	
	dol_syslog("factory.lib::select_entrepot sql=".$sql);

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			print '<select class="flat" name="'.$htmlname.'">';
			if ($showempty)
			{
				print '<option value="-1"';
				if ($selected == -1) print ' selected="selected"';
				print '>&nbsp;</option>';
			}
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$qtereel=0;
				$sql="select ps.reel FROM ".MAIN_DB_PREFIX."product_stock as ps";
				$sql.= " WHERE ps.fk_product = ".$idproduct;
				$sql.= " AND ps.fk_entrepot = ".$obj->rowid;
				$resreel=$db->query($sql);
				if ($resreel)
				{
					$objreel = $db->fetch_object($resreel);
					$qtereel=($objreel->reel?$objreel->reel:0);
				}
				print '<option value="'.$obj->rowid.'"';
				if ($obj->rowid == $selected) print ' selected="selected"';
				print ">".$obj->label." (".$qtereel.")</option>";
				$i++;
			}
			print '</select>';
		}
		else
		{
			// si pas de liste, on positionne un hidden à vide
			print '<input type="hidden" name="'.$htmlname.'" value=-1>';
		}
	}
}

/**
 *	Return list of status of equipement
 *
 *	@param  string	$selected       Preselected type
 *	@param  string	$htmlname       Name of field in html form
 * 	@param	int		$showempty		Add an empty field
 * 	@param	int		$hidetext		Do not show label before combo box
 * 	@param	string	$forceall		Force to show products and services in combo list, whatever are activated modules
 *  @return	void
 */
function select_equipement_etat($selected='',$htmlname='fk_etatequipement',$showempty=0,$hidetext=0)
{
    global $db,$langs,$user,$conf;

	if (empty($hidetext)) print $langs->trans("EquipementState").': ';
	
	// boucle sur les entrepots
	$sql = "SELECT rowid, libelle";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_equipement_etat";
	$sql.= " WHERE active = 1";
	
	dol_syslog("Equipement.Lib::select_equipement_etat sql=".$sql);

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			print '<select class="flat" name="'.$htmlname.'">';
			if ($showempty)
			{
				print '<option value="-1"';
				if ($selected == -1) print ' selected="selected"';
				print '>&nbsp;</option>';
			}
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				print '<option value="'.$obj->rowid.'"';
				if ($obj->rowid == $selected) print ' selected="selected"';
				print ">".$langs->trans($obj->libelle)."</option>";
				$i++;
			}
			print '</select>';
		}
		else
		{
			// si pas de liste, on positionne un hidden à vide
			print '<input type="hidden" name="'.$htmlname.'" value=-1>';
		}
	}
}
?>