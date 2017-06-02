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
if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/Contabsociete.class.php')) {
	require_once DOL_DOCUMENT_ROOT.'/contab/class/Contabsociete.class.php';
} 
// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$limit = 25;
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$id         = GETPOST("id");
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;



$proveedor=GETPOST("proveedor");
$format=GETPOST("format");
if (empty($format)) {
	$format=1;
}

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
		poliza.societe_type,

	IF (p.estado IS NULL, 1, p.estado) AS estado
	FROM
		llx_contab_polizas AS poliza
	LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio AND p.mes = poliza.mes

	
";

$sql.="
WHERE
	(poliza.contabilizar_pol=1 OR poliza.tipo_pol='E')
	AND poliza.fecha != '0000-00-00' ";

	if ($id>0) {
		$sql.=" AND poliza.rowid= ".$id;

	}

	if ($month>0) {
		$sql.=" AND poliza.mes= ".$month;
	}
	if ($year>0) {
		$sql.=" AND poliza.anio= ".$year;
	}

	if ( $id>0 || $proveedor>0 || $month>0 || $year>0) {
		//$sql.=" AND b.fk_proveedor>0 ";
	}


$sql.="

	GROUP BY
		poliza.rowid	
	ORDER BY
		poliza.fecha desc,
		poliza.tipo_pol ASC,
		poliza.cons ASC
		
";

//$sql.= $db->plimit($limit+1, $offset);
//echo $sql;

// Part to show record
if ( (empty($action) || $action == 'view')  )
{
	print '<script type="text/javascript" language="javascript">
	jQuery(document).ready(function() {
	    $("#type_diot,#format").change(function() {
	    	document.formsoc.submit();
	    });
	    
	});
	</script>';

	//print load_fiche_titre("Pólizas - Consulta y Registro",$page, $_SERVER["PHP_SELF"]);
	print_barre_liste("Pólizas - Consulta y Registro", $page, $_SERVER["PHP_SELF"]);    
	dol_fiche_head();
    $res2=$db->query("SELECT a.nom,a.rowid FROM llx_contab_societe as a ");

 	$res=$db->query($sql);

 	
	print '<div class="fichecenter">
		<form method="POST" name="formsoc" action="diot.php" >';
		print '<table class="nobordernopadding" width="100%" >';
			print '<tbody>';
				print '<tr>';
						print '
						<td align="left" class="nowrap borderright">
							<table>
								<tbody>
									<tr>
										<td>
											<b>Proveedor</b>
										</td>
										<td align="left">
											<div class="formleftzone">
												<select name="proveedor" class="flat">';
													print '<option value="-1">&nbsp;</option>';
													while ($obj=$db->fetch_object($res2)) {
														if ($proveedor==$obj->rowid) {
															print '<option value="'.$obj->rowid.'" selected>'.$obj->nom.'</option>';
														}else{
															print '<option value="'.$obj->rowid.'">'.$obj->nom.'</option>';
														}
													}
												print '
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td style="width:50px;">
											<b> Fecha </b>
										</td>
										<td>';
											print $formother->select_month($month,'month',1);
											print $formother->select_year($year,'year',1, 20, 5);
										print '
										</td>
									</tr>
									<tr>
										<td colspan=2>
											<input type="submit" class="button" name="add" value="Buscar">
										</td>
									</tr>
								</tbody>
							</table>
						</td>';
						if ( $month>0 && $year>0 && $db->num_rows($res)>0 ) {
							print '
							<td align="left" class="nowrap">
								<table>
									<tbody>
										
										<tr>
											<td>
												<b>Tipo</b>
											</td>
											<td>
												<select id="type_diot" name="type_diot" class="flat">';
													if ($type_diot==3) {
														print '<option value="3" selected>Monto Original/XML</option>';
													}else{
														print '<option value="3">Monto Original/XML</option>';
													}
													if ($type_diot==2) {
														print '<option value="2" selected>Monto Original</option>';
													}else{
														print '<option value="2">Monto Original</option>';
													}
													if ($type_diot==1) {
														print '<option value="1" selected>XML</option>';
													}else{
														print '<option value="1">XML</option>';
													}
													
													if ($format==1) {
														$format1= "selected";
													}else{
														$format2= "selected";
													}
												print '
											</select>
											</td>
										</tr>
										<tr>
											<td>
												<b>Formato</b>
											</td>
											<td>
												<select name="format" id="format" class="flat">
													<option value=1 '.$format1.'>
														TXT
													</option>
													<option value=2 '.$format2.'>
														Excel
													</option>
												</select>
											</td>
										</tr>
										<tr>
											<td >
												<a class="button" href="get_diot_txt.php?proveedor='.$proveedor.'&mes='.$month.'&anio='.$year.'&diot='.$type_diot.'&format='.$format.'">Generar Archivo</a>
											</td>
										</tr>
									</tbody>
								</table>
							</td>';
						}
				print '</tr>';
			print '</tbody>';
		print '</table>
		</form>';
	print '</div>';

	
		
	print '</div>'."\n";
	//print_barre_liste("", $page, $_SERVER["PHP_SELF"]);


    function get_info_docto_xml($idpol) {
        
        global $db;

        $rows = array();
        $sql="SELECT * FROM llx_contab_polizas as a WHERE a.rowid=".$idpol;


        $query=$db->query($sql);
        $obj=$db->fetch_object($query);



        $an=substr($obj->anio, -2, 2); 			
		$m = ((int)$obj->mes<10) ? "0".$obj->mes : $obj->mes ;
		$folio = $an.$m."-".$obj->tipo_pol."-".$obj->cons; 

        $string= "SELECT rowid, url FROM ".MAIN_DB_PREFIX."contab_doc WHERE folio='".$folio."'";


		$que=$db->query($string);
        
        if ($que) {
            $num= $db->num_rows($string);
            if ($num>0) {
            	return 1;
            }else{
            	return 0;
            }
        }


        return 0;
    }


    /*$res2=$db->query("SELECT fk_poliza,fk_proveedor FROM llx_contab_polizasdet WHERE fk_proveedor>0 ORDER BY fk_proveedor ASC ");
    while ($obj2=$db->fetch_object($res2)) {

		echo "<pre>";
			print_r($obj2);
		echo "<pre>";
	}*/



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
		
		if ($res) {

			if ($db->num_rows($res)>0) {
				$i=0;
				while ($obj=$db->fetch_object($res)) {
					$sql_prov="
					SELECT
						b.fk_proveedor
					FROM
						llx_contab_polizasdet AS b
					WHERE
						b.fk_poliza = ".$obj->id."
					AND b.fk_proveedor > 0
					LIMIT 1";
					$obj->fk_proveedor=0;
					$res_prov=$db->query($sql_prov);
					if ($res_prov) {
						$res_prov=$db->fetch_object($res_prov);
						$obj->fk_proveedor=$res_prov->fk_proveedor;
					}
					if ($obj->fk_proveedor==$proveedor || empty($proveedor) || $proveedor==-1) {
						


						$doc=get_info_docto_xml($obj->id);
						//echo "[".$obj->fk_proveedor."]   [".$doc."]<br>";

						if (  ( $obj->fk_proveedor>0 AND  $doc==1) or ( is_null($obj->fk_proveedor) AND  $doc==1) or ( $obj->fk_proveedor>0 AND  $doc==0)  ) {
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
							$i++;
						}
					}
					if ($i==$limit+1) {
						break;
					}
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
			asiento.descripcion as de,
			asiento.fk_proveedor
		FROM
			llx_contab_polizasdet AS asiento
		LEFT JOIN llx_contab_cat_ctas as b on b.cta=asiento.cuenta
		WHERE
			asiento.fk_proveedor!=-1 AND
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
						Proveedor
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
					$societe=new Contabsociete($db);
					while ($obj=$db->fetch_object($res)) {
						print '
							<tr >
								<td>
									'.$obj->asiento.'
								</td>
								<td>
									'.$obj->cuenta.' - '.$obj->des.'
								</td>';
								
								if ($obj->fk_proveedor>0) {
									echo "<td>";
										$societe->fetch($obj->fk_proveedor);
										print $societe->getNomUrl();
									echo "</td>";
								}else{
									echo "<td>";
										print "N/A";
									echo "</td>";
								}

						print '
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
							<td colspan=3>
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
							<td colspan=6>
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
