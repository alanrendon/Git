<?php
/* Copyright (C) 2014		charles-Fr Benke	<charles.fr@benke.fr>
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
 * 	\file       htdocs/customlink/class/actions_customlink.class.php
 * 	\ingroup    customlink
 * 	\brief      Fichier de la classe des actions/hooks des customlink
 */
 
class ActionsFactory // extends CommonObject 
{ 
 
		/** Overloading the doActions function : replacing the parent's function with the one below 
	 *  @param      parameters  meta datas of the hook (context, etc...) 
	 *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
	 *  @param      action             current action (if set). Generally create or edit or null 
	 *  @return       void 
	 */ 
	function printSearchForm($parameters, $object, $action) 
	{ 
		if(DOL_VERSION<'3.9'){
		global $conf,$langs;
		
		$langs->load("factory@factory");
		$title = img_object('','factory@factory').' '.$langs->trans("Factory");
		$ret='';
		$ret.='<div class="menu_titre">';
		$ret.='<a class="vsmenu" href="'.dol_buildpath('/factory/list.php',1).'">';
		$ret.=$title.'</a><br>';
		$ret.='</div>';
		$ret.='<form action="'.dol_buildpath('/factory/list.php',1).'" method="post">';
		$ret.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$ret.='<input type="text" class="flat" ';
		if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $ret.=' placeholder="'.$langs->trans("SearchOf").''.strip_tags($title).'"';
		else $ret.=' title="'.$langs->trans("SearchOf").''.strip_tags($title).'"';
		$ret.=' name="tag" size="10" />&nbsp;';
		$ret.='<input type="submit" class="button" value="'.$langs->trans("Go").'">';
		$ret.="</form>\n";
		$this->resprints=$ret;
		return 0;
		}
	}

	function addMoreActionsButtons($parameters, $object, $action) 
	{
		if (in_array('propalcard', explode(':', $parameters['context'])))
		{
			if ($object->statut==0) {
				print '<div class="inline-block divButAction"><a style="color:rgb(0,0,120) !important;" class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&saction=create">Previsualización</a></div>';
			}
			if ($object->statut==2) {
				print '<div class="inline-block divButAction"><a style="color:rgb(0,0,120) !important;" class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">Crear Orden de Producción</a></div>';
			}
		}
	}
	function formAddObjectLine($parameters, $object, $action) 
	{
		global $db;
		
		$saction=GETPOST("saction");
		if ($saction=="create") {
			print '<tr>';
			print '<td><br>';
			print '</td></tr>';
			print '<tr style="border:1px solid #E0E0E0;">';
			print '<td valign="top" style="border-top:1px solid #E0E0E0;">Inserte la clave especifica del proyecto</td>';
			print '<td colspan=7 valign="top" style="border-top:1px solid #E0E0E0;">';
			$sql='SELECT
				SUBSTR(a.ref, 1, 8) as clave
			FROM
				llx_product AS a
			WHERE SUBSTR(a.ref, 1, 5) REGEXP "^[0-9]+$"
			AND SUBSTR(a.ref, 6, 1) = "_"
			AND SUBSTR(a.ref, 7, 2) REGEXP "^[A-Z]"
			GROUP BY SUBSTR(a.ref, 1, 8)';
			$query=$db->query($sql);
			print '
			<select class="flat" id="select_clave" name="type">';
				while ($obj = $db->fetch_object($query)) {
					print '<option value="'.$obj->clave.'">'.$obj->clave.'</option>';
				}
			print '
			</select>';
			print '</td>
			<td style="border-top:1px solid #E0E0E0;">
				<input type="submit" class="button" value="Cargar" name="cargar_clave" id="cargar_clave">
			</td>
			</tr>';
		}

	}
	function doActions($parameters, $object, $action) 
	{
		echo "<pre>";
			print_r($_POST);
		echo "</pre>";
	}
}
?>