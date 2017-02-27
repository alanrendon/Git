<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 * 					JPFarber - jfarber55@hotmail.com
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
 * 
 * code pour créer le module 106, 117, 97, 110, b, 112, 97, 98, 108, 11, b, 102, 97, 114, 98, 101, 114
 */

/**
 *   	\file       dev/skeletons/skeleton_page.php
 * 		\ingroup    mymodule othermodule1 othermodule2
 * 		\brief      This file is an example of a php page
 * 					Put here some comments
 */
//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)
// Change this following line to use the correct relative path (../, ../../, etc)
/* $res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
    $res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
    $res = @include '../../../main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../main.inc.php"))
    $res = @include '../../../../main.inc.php';   // Used on dev env only
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/module/class/skeleton_class.class.php'); */

// Load traductions files requiredby by page
global $db;
if (file_exists(DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php')) {
	require_once DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT . '/custom/contab/class/contabsatctas.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

if (isset($_GET["change"]))
{
	$varr="CODE_ACTIVE_".$_GET["change"];
	if ($conf->global->$varr!=1) {
		$val=1;
	}
	if ($conf->global->$varr==1) {
		$val=2;
	}

	dolibarr_set_const($db, "CODE_ACTIVE_".$_GET["change"],$val,'chaine',0,'',$conf->entity);
   
}


if ($mod==3) {
	$html.="
	<table class='noborder' >
		<thead>
			<tr class='liste_titre'>
			  <td ><b>Cuenta</b></td>
			  <td ><b>Descripción</b></td>
			  <td align='center'><b>Acción</b></td>
			</tr>
		</thead>
		<tbody>";
		$num_2=0;

		$sql="
		SELECT
			c.codagr,c.descripcion,c.rowid
		FROM
			llx_contab_polizasdet AS a
		INNER JOIN llx_contab_polizas AS b ON b.rowid = a.fk_poliza
		INNER JOIN llx_contab_sat_ctas as c on c.codagr=a.cuenta
		
		GROUP BY c.codagr

		";

		$resql=$db->query($sql);

		if ($resql) {
			$num=$db->num_rows($resql);
			if ($num>0) {
				while ($obj=$db->fetch_object($resql)) {
					$satc= new Contabsatctas($db);
					$satc->fetch_by_CodAgr($obj->codagr);

					if($satc->natur=='A'){
					

						$sql='
							SELECT
							b.mes,SUM(a.haber) as suma
						FROM
							llx_contab_polizasdet AS a
						INNER JOIN llx_contab_polizas as b on b.rowid=a.fk_poliza
						WHERE
							a.cuenta = "'.$obj->codagr.'" 
						GROUP BY b.mes
						';
						

						$re=$db->query($sql);
						$meses=array();
						if ($re) {
							$suma=0;
							while ( $obj_2=$db->fetch_object($re)  ) {
								$meses[$obj_2->mes]=$obj_2->suma;
								$suma+=$obj_2->suma;
							}
							$num_2++;
							$html.="
								<tr>
									<td align='left' >".$obj->codagr."</td>
									<td align='left' >".$obj->descripcion."</td>
									<td align='center'>";
									$varr="CODE_ACTIVE_".$obj->rowid;
									
									if ( $conf->global->$varr!=1) {
										$html.= '<a href="'.$_SERVER['PHP_SELF'].'?mod='.$mod.'&change='.$obj->rowid.'">';
					                    
					                    
					                    $html.= img_picto($langs->trans("Activated"),'switch_on');
					                    $html.= '</a>';
									}
									if ($conf->global->$varr==1) {
										$html.= '<a href="'.$_SERVER['PHP_SELF'].'?mod='.$mod.'&change='.$obj->rowid.'">';
					                   
					                    $html.= img_picto($langs->trans("Disabled"),'switch_off');
					                    $html.= '</a>';
									}

									
							$html.="</td>
								</tr>
							";
						}
					}
				}
			}
		}
		$html.="
		</tbody>
	</table>";
	print $html;
}elseif ($mod==4) {
	$html.="
	<table class='noborder' >
		<thead>
			<tr class='liste_titre'>
			  <td ><b>Cuenta</b></td>
			  <td ><b>Descripción</b></td>
			  <td align='center'><b>Acción</b></td>
			</tr>
		</thead>
		<tbody>";
		$num_2=0;

		$sql="
		SELECT
			c.codagr,c.descripcion,c.rowid
		FROM
			llx_contab_polizasdet AS a
		INNER JOIN llx_contab_polizas AS b ON b.rowid = a.fk_poliza
		INNER JOIN llx_contab_sat_ctas as c on c.codagr=a.cuenta
		
		GROUP BY c.codagr

		";

		$resql=$db->query($sql);

		if ($resql) {
			$num=$db->num_rows($resql);
			if ($num>0) {
				while ($obj=$db->fetch_object($resql)) {
					$satc= new Contabsatctas($db);
					$satc->fetch_by_CodAgr($obj->codagr);

					if($satc->natur!='A'){
					

						$sql='
							SELECT
							b.mes,SUM(a.haber) as suma
						FROM
							llx_contab_polizasdet AS a
						INNER JOIN llx_contab_polizas as b on b.rowid=a.fk_poliza
						WHERE
							a.cuenta = "'.$obj->codagr.'" 
						GROUP BY b.mes
						';
						

						$re=$db->query($sql);
						$meses=array();
						if ($re) {
							$suma=0;
							while ( $obj_2=$db->fetch_object($re)  ) {
								$meses[$obj_2->mes]=$obj_2->suma;
								$suma+=$obj_2->suma;
							}
							$num_2++;
							$html.="
								<tr>
									<td align='left' >".$obj->codagr."</td>
									<td align='left' >".$obj->descripcion."</td>
									<td align='center'>";
									$varr="CODE_ACTIVE_".$obj->rowid;
									
									if ( $conf->global->$varr!=1) {
										$html.= '<a href="'.$_SERVER['PHP_SELF'].'?mod='.$mod.'&change='.$obj->rowid.'">';
					                    
					                    
					                    $html.= img_picto($langs->trans("Activated"),'switch_on');
					                    $html.= '</a>';
									}
									if ($conf->global->$varr==1) {
										$html.= '<a href="'.$_SERVER['PHP_SELF'].'?mod='.$mod.'&change='.$obj->rowid.'">';
					                   
					                    $html.= img_picto($langs->trans("Disabled"),'switch_off');
					                    $html.= '</a>';
									}

									
							$html.="</td>
								</tr>
							";
						}
					}
				}
			}
		}
		$html.="
		</tbody>
	</table>";
	print $html;
}



?>
