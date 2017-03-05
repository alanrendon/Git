<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       contab/contabsociete_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-28 00:33
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
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/contab/class/contabsociete.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$id         = GETPOST("id");

$proveedor=GETPOST("proveedor");
$month=GETPOST("month");
$year=GETPOST('year');
$type_diot=GETPOST('type_diot','int');
if (empty($type_diot)) {
	$type_diot=1;
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/






/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Proveedores','');

$form=new Form($db);
$formcompany = new FormCompany($db);
$formother=new FormOther($db);
function total_debe_haber($id)
{
	global $db;
	$sql="
	SELECT
		sum(asiento.debe) as debe,
		sum(asiento.haber) as haber
	FROM
		llx_contab_polizasdet AS asiento
	WHERE
		asiento.fk_poliza =".$id;

	$res=$db->query($sql);

	$array=array();
	if ($res) {
		$camp=$db->fetch_object($res);
		$array[0]=$camp->debe;
		$array[1]=$camp->haber;
	}
	return $array;
}
$sql="
	SELECT
	poliza.rowid AS 'id',
	poliza.entity,
	(
		CASE poliza.tipo_pol
		WHEN 'D' THEN
			'Diario'
		WHEN 'E' THEN
			'Egreso'
		WHEN 'I' THEN
			'Ingreso'
		WHEN 'C' THEN
			'Cheque'
		END
	) AS tipo_pol,
	CONCAT(
		DATE_FORMAT(poliza.fecha, '%y%c'),
		'-',
		poliza.tipo_pol,
		'-',
		poliza.cons
	) AS cons,
	poliza.anio,
	poliza.mes,
	poliza.fecha,
	poliza.tipo_pol AS tipol_l,
	poliza.cons npol,
	poliza.concepto,
	poliza.comentario,
	poliza.anombrede,
	poliza.numcheque,
	factProve.rowid,
	factCliente.rowid,
	poliza.societe_type,
	factCliente.facnumber,
	factProve.ref,
	IF (p.estado IS NULL, 1, p.estado) AS estado
	FROM
		llx_contab_polizas AS poliza
	LEFT JOIN llx_facture_fourn AS factProve ON factProve.rowid = poliza.fk_facture
	LEFT JOIN llx_facture AS factCliente ON factCliente.rowid = poliza.fk_facture
	LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = poliza.mes
";

	$sql.="
	WHERE
		poliza.fecha != '0000-00-00' ";

	if ($id>0) {
		$sql.=" AND poliza.rowid= ".$id;
	}
	if ($proveedor>0) {
		$sql.=" AND poliza.fk_proveedor= ".$proveedor;
	}
	if ($month>0) {
		$sql.=" AND poliza.mes= ".$month;
	}
	if ($year>0) {
		$sql.=" AND poliza.anio= ".$year;
	}

	

$sql.="

	GROUP BY
		poliza.rowid
	ORDER BY
		poliza.tipo_pol ASC,
		poliza.cons ASC,
		poliza.anio ASC,
		poliza.mes ASC
	LIMIT 10
";






// Part to show record
if ( (empty($action) || $action == 'view')  )
{
	print '<script type="text/javascript" language="javascript">
	jQuery(document).ready(function() {
	    $("#type_diot").change(function() {
	    	document.formsoc.submit();
	    });
	});
	</script>';
	print load_fiche_titre("Pólizas - Consulta y Registro");
	
	dol_fiche_head();
    $res=$db->query("SELECT a.nom,a.rowid FROM llx_contab_societe as a ");
    if ($res) {
		if ($db->num_rows($res)>0) {
			print '
		<form method="POST" name="formsoc" action="diot.php" >
			<table>
				<tr>
					<td>
						<b>Proveedor</b>
					</td>
					<td style="width:100px;">
						<select name="proveedor" class="flat">';
							print '<option value>&nbsp;</option>';
							while ($obj=$db->fetch_object($res)) {
								if ($proveedor==$obj->rowid) {
									print '<option value="'.$obj->rowid.'" selected>'.$obj->nom.'</option>';
								}else{
									print '<option value="'.$obj->rowid.'">'.$obj->nom.'</option>';
								}
							}
						print '
						</select>
					</td>
					<td style="width:50px;">
						<b> Fecha </b>
					</td>
					<td>
			';
				print $formother->select_month($month,'month',1);
			print ' </td>
					<td>
			';

			print $formother->select_year($year,'year',1, 20, 5);
			print ' </td>';

			print '
			<td>
				<input type="submit" class="button" name="add" value="Buscar">
			</td>';
			print '
			<td style="width:30px;">
				
			</td>';
			if ( $month>0 && $year>0) {
				print '
				<td>
					<select id="type_diot" name="type_diot" class="flat">';
							if ($type_diot==1) {
								print '<option value="1" selected>XML</option>';
							}else{
								print '<option value="1">XML</option>';
							}
							if ($type_diot==2) {
								print '<option value="2" selected>Monto Original</option>';
							}else{
								print '<option value="2">Monto Original</option>';
							}
						
						print '
					</select>
				</td>';
				print '
				<td>
					<a class="button" href="get_diot_txt.php?proveedor='.$proveedor.'&mes='.$month.'&anio='.$year.'&diot='.$type_diot.'">Generar BATCH</a>
					
				</td>';
			}
			

			
			print '
				</tr>
			</table>
		</form>';
			
		}
	}


	
		
	print '</div>'."\n";

	print '<table class="liste ">';
		print '<tr class="liste_titre">';
			print_liste_field_titre("Tipo");
			print_liste_field_titre("Número");
			print_liste_field_titre("Concepto");
			print_liste_field_titre("Fecha");
			print_liste_field_titre("Total Debe");
			print_liste_field_titre("Total Haber");
			print_liste_field_titre("Ver Detalle");
		print '</tr">';
		$res=$db->query($sql);
		if ($res) {
			if ($db->num_rows($res)>0) {
				while ($obj=$db->fetch_object($res)) {
					$montos=total_debe_haber($obj->id);
					print '<tr >';
						print '<td>'.$obj->tipo_pol.'</td>';
						print '<td>'.$obj->cons.'</td>';
						print '<td>'.$obj->concepto.'</td>';
						print '<td>'.dol_print_date($obj->fecha,"%d/%m/%Y").'</td>';
						print '<td>'.price(round($montos[0],2)).'</td>';
						print '<td>'.price(round($montos[1],2)).'</td>';
						print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'">Ver</a></td>';
					print '</tr">';
				}
				
			}else{
				print '<tr >';
					print '<td colspan=7>Sin Resultados</td>';
				print '</tr">';
			}
		}else{
			print '<tr >';
				print '<td colspan=7>Sin Resultados</td>';
			print '</tr">';
		}
		

	print '</table >';
	if ($id>0) {
		$sql="
		SELECT
			asiento.asiento,
			asiento.cuenta,
			b.descta as des,
			asiento.debe,
			asiento.haber,
			asiento.rowid,
			asiento.descripcion as de
		FROM
			llx_contab_polizasdet AS asiento
		LEFT JOIN llx_contab_cat_ctas as b on b.cta=asiento.cuenta
		WHERE
			asiento.fk_poliza =".$id;

		$res=$db->query($sql);
		if ($res) {
			print '
			<br>
			<table class="liste ">
				<tr class="liste_titre">
					<td>
						No.
					</td>
					<td>
						Cuenta
					</td>
					<td>
						Descripción
					</td>
					<td>
						Debe
					</td>
					<td>
						Haber
					</td>
				</tr>';


				if ($db->num_rows($res)>0) {
					$debe=0;
					$haber=0;
					while ($obj=$db->fetch_object()) {
						print '
							<tr >
								<td>
									'.$obj->asiento.'
								</td>
								<td>
									'.$obj->cuenta.' - '.$obj->des.'
								</td>
								<td>
									'.$obj->de.'
								</td>
								<td>
									'.price(round($obj->debe,2)).'
								</td>
								<td>
									'.price(round($obj->haber,2)).'
								</td>
							</tr>
						';

						$debe+=$obj->debe;
						$haber+=$obj->haber;
					}
					print '
						<tr class="pair">
							<td>
								<b>Total</b>
							</td>
							<td colspan=2>
							</td>
		
							<td>
								'.price(round($debe,2)).'
							</td>
							<td>
								'.price(round($haber,2)).'
							</td>
						</tr>
					';
				}else{
					print '
						<tr >
							<td colspan=5>
								Sin Elementos
							</td>
	
						</tr>
					';
				}
				



			print '
			</table>
			';
		}
		
	}

	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}





// End of page
llxFooter();
$db->close();
