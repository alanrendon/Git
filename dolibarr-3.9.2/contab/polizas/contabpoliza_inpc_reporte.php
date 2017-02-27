<?php
//ini_set('memory_limit', '2048M');
//ini_set('max_execution_time', 300);
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
 *   	\file       contab/contabdepreciation_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-17 16:31
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

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../main.inc.php")) $res=@include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
//include_once('../../class/contabdepreciation.class.php');
if (file_exists(DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php')) {
	require_once DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT . '/custom/contab/class/contabsatctas.class.php';
}

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$anio	= GETPOST('anio','alpha');
	$html="
		<html>
		<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		</head>
		<body>";

if ($anio>0) {



	$html.="
		<table style='margin-left:50px; margin-top:65px; border-collapse: collapse;width: 70%;font-size:6px' align='left' >
			<tr>
				<td style='background-color:#3366ff; color:#ffffff;' align='center'><b>".$conf->global->MAIN_INFO_SOCIETE_NOM."</b></td>
				<td style='width:40%;' ></td>
				<td style='width:50px; border:0.5px solid #ffc07f; border-right-color:black; background-color:#ffc07f;' align='right' >Ejercicio: </td>
				<td style='width:50px; border:0.5px solid; background-color: #ffd9b2;' align='right'>".$anio."</td>
			</tr>
			<tr>
				<td colspan=4 style='font-size:7px;' ><b>Determinación del Ajuste Anual por Inflación</b></td>
			</tr>
		</table>

		<table  style='margin-top:15px; border-collapse: collapse;width: 90%;font-size:6px' align='center' >
			<thead>
				<tr>
				  <td style='background-color:#c0c0c0;' align='center' rowspan=2><b>Cuenta</b></td>
				  <td style='background-color:#c0c0c0; width:90px;' align='center'><b>Concepto/Partida</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Ene</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Feb</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Mar</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Abr</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>May</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Jun</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Jul</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Ago</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Sep</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Oct</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Nov</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>Dic</b></td>
				  <td style='background-color:#c0c0c0; width:35px;' rowspan=4 align='center'><b>Saldo Prom Anual</b></td>
				  <td></td>
				</tr>
				<tr>
				  <td style='background-color:#c0c0c0;' align='right'><b>Periodo</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>1</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>2</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>3</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>4</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>5</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>6</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>7</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>8</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>9</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>10</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>11</b></td>
				  <td style='background-color:#c0c0c0;' align='center'><b>12</b></td>
				  <td></td>
				</tr>
				<tr>
					<td colspan=13>&nbsp;</td>
				</tr>
				<tr>
					<td align='center'></td>
					<td align='center' ><u><i><b style='background-color:#a5c7ff;'>Saldo mensual de Créditos o Activos:</b></i></u></td>
					<td colspan=13></td>
				</tr>
			</thead>
			<tbody>";
				$num_2=0;

				$sql=$sql="
				SELECT
					c.cta as codagr,c.descta as descripcion,c.rowid
				FROM
					".MAIN_DB_PREFIX."contab_polizasdet AS a
				INNER JOIN ".MAIN_DB_PREFIX."contab_polizas AS b ON b.rowid = a.fk_poliza
				INNER JOIN ".MAIN_DB_PREFIX."contab_cat_ctas as c on c.cta=a.cuenta
				WHERE
					b.anio = ".$anio."
				GROUP BY c.cta

				";

				$resql=$db->query($sql);

				if ($resql) {
					$num=$db->num_rows($resql);
					if ($num>0) {
						while ($obj=$db->fetch_object($resql)) {
							$satc= new Contabsatctas($db);
 							$satc->fetch_by_CodAgr($obj->codagr);
 							$varr="CODE_ACTIVE_".$obj->rowid;
 							if($satc->natur=='A' &&  $conf->global->$varr!=1){
 							

 								$sql='
 									SELECT
										b.mes,SUM(a.haber) as suma
									FROM
										'.MAIN_DB_PREFIX.'contab_polizasdet AS a
									INNER JOIN '.MAIN_DB_PREFIX.'contab_polizas as b on b.rowid=a.fk_poliza
									WHERE
										a.cuenta = "'.$obj->codagr.'" AND b.anio='.$anio.'
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
											<td style='border:0.5px solid; background-color: #ffffcc;' align='center' >".$obj->codagr."</td>
											<td style='border-left:0.5px solid; width:100px;' align='center' >".$obj->descripcion."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[1],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[2],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[3],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[4],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[5],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[6],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[7],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[8],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[9],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[10],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[11],2))."</td>
											<td align='center' style='background-color:#b8cce4;' >".price(round($meses[12],2))."</td>
											<td align='center' >".price(round($suma/12),2)."</td>
											<td align='center' >".price(round($suma),2)."</td>

											
										</tr>
									";
									$enero+=$meses[1];
									$febrero+=$meses[2];
									$marzo+=$meses[3];
									$abril+=$meses[4];
									$mayo+=$meses[5];
									$junio+=$meses[6];
									$julio+=$meses[7];
									$agosto+=$meses[8];
									$septiembre+=$meses[9];
									$octubre+=$meses[10];
									$noviembre+=$meses[11];
									$diciembre+=$meses[12];
									$suma_tot+=$suma/12;

								}
 							}
						}
					}
				}

				for ($i=$num_2; $i < 13; $i++) { 
					$html.="
						<tr>
							<td style='border:0.5px solid; background-color: #ffffcc;' align='center' >&nbsp;</td>
							<td style='border-left:0.5px solid;' align='center' >&nbsp;</td>
							<td align='center' colspan=12 style='background-color:#b8cce4;' ></td>
							<td></td>
							<td></td>
						</tr>

					";
				}
				$html.="

						<tr><td colspan=16>&nbsp;</td></tr>

						<tr>
							<td></td>
							<td align='center' ><u><i><b style='background-color:#a5c7ff;'>Total Créditos o Activos</b></i></u></td>
							<td style='border:0.2px solid;' align='center'>".price(round($enero,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($febrero,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($marzo,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($abril,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($mayo,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($junio,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($julio,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($agosto,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($septiembre,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($octubre,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($noviembre,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($diciembre,2))."</td>
							<td style='border:0.2px solid;' align='center'>".price(round($suma_tot,2))."</td>
							<td style='border-left:0.2px solid;' align='center'></td>
						</tr>
						<tr><td colspan=16>&nbsp;<br><br></td></tr>
						</tbody>
						</table>
						<div style='page-break-before: always;' ></div>
						<table  style='margin-top:15px; border-collapse: collapse;width: 90%;font-size:6px' align='center' >
						<tr>
							<td align='center'></td>
							<td align='left' ><u><i><b style='background-color:#a5c7ff;'>Saldo mensual Deudas:</b></i></u></td>
							<td colspan=13></td>
						</tr>


						";
						$num_2=0;

						$sql=$sql="
				SELECT
					c.cta as codagr,c.descta as descripcion,c.rowid
				FROM
					".MAIN_DB_PREFIX."contab_polizasdet AS a
				INNER JOIN ".MAIN_DB_PREFIX."contab_polizas AS b ON b.rowid = a.fk_poliza
				INNER JOIN ".MAIN_DB_PREFIX."contab_cat_ctas as c on c.cta=a.cuenta
				WHERE
					b.anio = ".$anio."
				GROUP BY c.cta
						
				";
						$sum_exp=$suma_tot;
						$enero=0;
						$febrero=0;
						$marzo=0;
						$abril=0;
						$mayo=0;
						$junio=0;
						$julio=0;
						$agosto=0;
						$septiembre=0;
						$octubre=0;
						$noviembre=0;
						$diciembre=0;
						$suma_tot=0;
						$resql=$db->query($sql);

						if ($resql) {
							$num=$db->num_rows($resql);
							if ($num>0) {
								while ($obj=$db->fetch_object($resql)) {
									$satc= new Contabsatctas($db);
		 							$satc->fetch_by_CodAgr($obj->codagr);
		 							$varr="CODE_ACTIVE_".$obj->rowid;
		 							if($satc->natur!='A' && $conf->global->$varr!=1){
		 							

		 								$sql='
		 									SELECT
												b.mes,SUM(a.haber) as suma
											FROM
												'.MAIN_DB_PREFIX.'contab_polizasdet AS a
											INNER JOIN '.MAIN_DB_PREFIX.'contab_polizas as b on b.rowid=a.fk_poliza
											WHERE
												a.cuenta = "'.$obj->codagr.'" AND b.anio='.$anio.'
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
													<td style='border:0.5px solid; background-color: #ffffcc;' align='center' >".$obj->codagr."</td>
													<td style='border-left:0.5px solid; width:100px;' align='center ' >".$obj->descripcion."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[1],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[2],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[3],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[4],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[5],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[6],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[7],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[8],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[9],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[10],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[11],2))."</td>
													<td align='center' style='background-color:#b8cce4;' >".price(round($meses[12],2))."</td>
													<td align='center' >".price(round($suma/12),2)."</td>
													<td align='center' >".price(round($suma),2)."</td>

													
												</tr>
											";
											$enero+=$meses[1];
											$febrero+=$meses[2];
											$marzo+=$meses[3];
											$abril+=$meses[4];
											$mayo+=$meses[5];
											$junio+=$meses[6];
											$julio+=$meses[7];
											$agosto+=$meses[8];
											$septiembre+=$meses[9];
											$octubre+=$meses[10];
											$noviembre+=$meses[11];
											$diciembre+=$meses[12];
											$suma_tot+=$suma/12;

										}
		 							}
								}
							}
						}

						for ($i=$num_2; $i < 13; $i++) { 
							$html.="
								<tr>
									<td style='border:0.5px solid; background-color: #ffffcc;' align='center' >&nbsp;</td>
									<td style='border-left:0.5px solid;' align='center' >&nbsp;</td>
									<td align='center' colspan=12 style='background-color:#b8cce4;' ></td>
									<td></td>
									<td></td>
								</tr>
							";
						}
						$html.="
								<tr><td colspan=16>&nbsp;</td></tr>
								<tr>
									<td></td>
									<td align='center' ><u><i><b style='background-color:#a5c7ff;'>Total Créditos o Activos</b></i></u></td>
									<td style='border:0.2px solid;' align='center'>".price(round($enero,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($febrero,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($marzo,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($abril,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($mayo,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($junio,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($julio,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($agosto,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($septiembre,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($octubre,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($noviembre,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($diciembre,2))."</td>
									<td style='border:0.2px solid;' align='center'>".price(round($suma_tot,2))."</td>
									<td style='border-left:0.2px solid;' align='center'></td>
								</tr>
							";
					
				
	$sql="SELECT a.noviembre,a.diciembre FROM ".MAIN_DB_PREFIX."contab_inpc as a WHERE a.year=".$anio;
	$res=$db->query($sql);
	$mes1=0;
	$mes2=0;
	if ($res) {
		$obj=$db->fetch_object($res);
		$mes1=$obj->diciembre;
		$mes2=$obj->noviembre;
	}
	@$tasa=$mes1/$mes2;
	$tasa2=$tasa-1;


	$dif_deu_cred=$sum_exp-$suma_tot;
	$dif_cred_deu=$suma_tot-$sum_exp;

	if ($dif_deu_cred<0) {
		$dif_deu_cred=0;
	}

	if ($dif_cred_deu<0) {
		$dif_cred_deu=0;
	}
	$html.="
			</tbody>
		</table>
		<br><br>
		<table  style='margin-left:40px; border-collapse: collapse;width: 30%;font-size:6px' align='left' >
			<tr>
				<td colspan=2 style='border:0.5px solid; background-color:#c0c0c0;' align='left'><b>Saldo Promedio Anual:</b></td>
				
			</tr>
			<tr>
				<td style='border:0.5px solid; background-color:#99ccff;' align='center'><b>Créditos</b></td>
				<td style='border:0.5px solid;' align='right'><b>".price(round($sum_exp,2))."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid; background-color:#99ccff;' align='center'><b>Deudas</b></td>
				<td style='border:0.5px solid;' align='right'><b>".price(round($suma_tot,2))."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='left'><b>Diferencia Créditos - Deudas</b></td>
				<td style='border:0.5px solid;' align='right'><b>".price(round($dif_deu_cred,2))."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='left'><b>Diferencia Deudas - Créditos</b></td>
				<td style='border:0.5px solid;' align='right'><b>".price(round($dif_cred_deu,2))."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='left'><b>Tasa de inflación del ejercicio</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round($tasa2,4)."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid; background-color:#c0c0c0;' align='left'><b>Ajuste Anual por Inflación: </b></td>
				<td style='border:0.5px solid;' align='left'><b></b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid; background-color:#666699;' align='left'><b>Deducible</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round(($dif_deu_cred)*$tasa2,0)."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid; background-color:#666699;' align='left'><b>Acumulable</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round(($dif_cred_deu)*$tasa2,0)."</b></td>
			</tr>
		</table>

		<br><br><br>
		<table  style='margin-left:40px; border-collapse: collapse;width: 30%;font-size:6px' align='left' >
			<tr>
				<td colspan=3 style='border:0.5px solid;' align='center'><b>CALCULO DE LA TASA DE INFLACION ".$anio."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='left'><b></b></td>
				<td style='border:0.5px solid;' align='center'><b>INPC del último mes del ejercicio del cálculo</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round($mes1,4)."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='right'><b>Entre:</b></td>
				<td style='border:0.5px solid;' align='center'><b>INPC del último mes del ejercicio inmediato anterior</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round($mes2,4)."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='right'><b>Igual:</b></td>
				<td style='border:0.5px solid;' align='center'><b>Factor de ajuste anual</b></td>
				<td style='border:0.5px solid;' align='right'><b>".round($tasa,4)."</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='right'><b>Menos:</b></td>
				<td style='border:0.5px solid;' align='center'><b>Unidad</b></td>
				<td style='border:0.5px solid;' align='right'><b>1</b></td>
			</tr>
			<tr>
				<td style='border:0.5px solid;' align='right'><b>Igual:</b></td>
				<td style='border:0.5px solid;' align='center'><b>Factor de ajuste anual </b></td>
				<td style='border:0.5px solid;' align='right'><b>".round($tasa2,4)."</b></td>
			</tr>
		</table>

				  


	";



	require_once '../class/dompdf/dompdf_config.inc.php';
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper("letter","landscape");
	$dompdf->render();
	$dompdf->stream("Calculo_Ajuste_Anual_por_Inflacion.pdf",array('Attachment'=>0));

}




