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

if (!$res && file_exists("../main.inc.php"))
	$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
	$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
	$res = @include '../../../main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../main.inc.php"))
	$res = @include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once('../../class/contabdepreciation.class.php');


// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$xls	= GETPOST('xls','alpha');

if (isset($_GET["xls"])) {
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=Reporte INPC.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
}





// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';
function dif_date($date1,$date2)
{




	$date1 = $date1;
	$date2 = $date2;

	$ts1 = strtotime($date1);
	$ts2 = strtotime($date2);

	$year1 = date('Y', $ts1);
	$year2 = date('Y', $ts2);

	$month1 = date('m', $ts1);
	$month2 = date('m', $ts2);

	$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
	return $diff;
}

function inpc($mes,$anio)
{
	global $db;
	$meses = array('enero','febrero','marzo','abril','mayo','junio','julio',
               'agosto','septiembre','octubre','noviembre','diciembre');

	$sql="SELECT a.".$meses[$mes-1]." as valor FROM llx_contab_inpc as a WHERE a.year=".$anio;
	$res=$db->query($sql);

	$inpc_adq=0;
	if ($res) {
		$obje = $db->fetch_object($res);
		return $obje->valor;
	}
	return 0;
}
function truncateFloat($number, $digitos)
{
    $raiz = 10;
    $multiplicador = pow ($raiz,$digitos);
    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
    return number_format($resultado, $digitos);
 
}



// Load object if id or ref is provided as parameter





$object=new Contabdepreciation($db);
$sql="SELECT * FROM llx_contab_depreciation as a";
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    if ($num>0) {
    	$hoy= dol_mktime(0,0,0,GETPOST("date_advancemonth"),GETPOST("date_advanceday"), GETPOST("date_advanceyear"));
		$hoy2= dol_mktime(0,0,0,GETPOST("date_advancemonth"),GETPOST("date_advanceday"), GETPOST("date_advanceyear")-1);
		$hoy3=$hoy;
    	$html.="


		<table  style=' border:0.5px solid; border-collapse: collapse;width: 95%;font-size:8px' align='center' >
			<thead>
				<tr>
					<td colspan=16 style=' border:0.5px solid;' ><b>".$conf->global->MAIN_INFO_SOCIETE_NOM."</b></td>
				</tr>
				<tr>
					<td style=' border:0.5px solid;'>".GETPOST("date_advance")."</td>
					<td colspan=15><b>Depreciaciones contables y fiscales del ejercicio ".$_REQUEST['date_advanceyear']."</b></td>
				</tr>
				<tr>
				  <td style=' border:0.5px solid;'><strong>CONCEPTO</strong></td>
				  <td style=' border:0.5px solid;'><strong>% Dep</strong></td>
				  <td style=' border:0.5px solid;'><strong>Fecha de Adquis.</strong></td>
				  <td style=' border:0.5px solid;'><strong>M.O.I.</strong></td>
				  <td style=' border:0.5px solid;'><strong>Fecha de inicio de Depre</strong></td>
				  <td style=' border:0.5px solid;'><strong>Meses Uso Complt.</strong></td>
				  <td style=' border:0.5px solid;'><strong>Dep. Acum. ".dol_print_date($hoy2,"daytext")."</strong></td>
				  <td style=' border:0.5px solid;'><strong>Dep'n del Ej. ".dol_print_date($hoy3,"%Y")."</strong></td>
				  <td style=' border:0.5px solid;'><strong>sdo x  redimir</strong></td>
				  <td style=' border:0.5px solid;'><strong>Dep'n Acum. al fin Ej. (Balance)</strong></td>
				  <td style=' border:0.5px solid;'><strong>Valor Neto en Libros</strong></td>
				  <td style=' border:0.5px solid;'><strong>INPC mes de Adq.</strong></td>
				  <td style=' border:0.5px solid;'><strong>INPC Ultima Mitad del Ej.</strong></td>
				  <td style=' border:0.5px solid;'><strong>F.Actz.</strong></td>
				  <td style=' border:0.5px solid;'><strong>dep. actualizada</strong></td>
				  <td style=' border:0.5px solid;'><strong>Dep'n Mensual del Ej.</strong></td>
				</tr>
			<thead>
			<tbody>
			";
    	$totalmes_uso_comp=0;
    	$totaldep_acumulada_final=0;
    	$totaldep_eje=0;
    	$totalsdo_redimir=0;
    	$totalacum_al_final=0;
    	$totalvalor_neto_lib=0;
    	$totalinpc_adq=0;
    	$totalinpc_ultima_mitad=0;
    	$totalactz=0;
    	$totaldep_actu_n=0;
    	$totaldep_mensual_del_eje=0;
		while ($obj = $db->fetch_object($resql)) {
			$dia=dol_print_date($obj->date_purchase,"%d");
			

			$lifetime=$obj->depreciation_rate/100;

			
			$fecha_inicio_depresiacion=(       ($dia<28 )? strtotime( " + ".(28-$dia)." days" , $db->jdate($obj->date_purchase) )    :  $obj->date_purchase );



			$dif=dif_date(dol_print_date($fecha_inicio_depresiacion,"%Y-%m-%d"),dol_print_date($hoy,"%Y-%m-%d"));


			

			$mes_uso_comp=round($dif,0);


			$mes_uso_comp_2=1/($lifetime/12);



			if ($mes_uso_comp>$mes_uso_comp_2) {
				$mes_uso_comp_def=$mes_uso_comp_2;
			}else{
				$mes_uso_comp_def=$mes_uso_comp;
			}


			$dep_acumulada=$mes_uso_comp-dol_print_date($hoy,"%m");




			$dep_acumulada_med1=round($dep_acumulada,0);
			//$dep_acumulada_med=$dep_acumulada_med1*($lifetime/12)*$obj->amount;
			$dep_acumulada_med=$dep_acumulada_med1;
			$dep_acumulada_final=0;



			if ($dep_acumulada_med1>$mes_uso_comp_def) {
				$dep_acumulada_final=$mes_uso_comp_def;
			}else{
				if ($dep_acumulada_med1<0) {
					$dep_acumulada_final=0;
				}else{
					$dep_acumulada_final=$dep_acumulada_med;
				}
			}
			$dep_acumulada_final=$dep_acumulada_final*($lifetime/12)*$obj->amount;


			$dep_eje1=$dep_acumulada_final+(dol_print_date($hoy,"%m")* ( $lifetime/12 )*$obj->amount);
			if ($dep_eje1>$obj->amount) {
				$dep_eje=$obj->amount-$dep_acumulada_final;
			}else{
				if (dol_print_date($fecha_inicio_depresiacion,"%Y") == dol_print_date($hoy,"%Y")) {
					$dep_eje=(dol_print_date($hoy,"%m")-dol_print_date($fecha_inicio_depresiacion,"%m"))*($lifetime/12)*$obj->amount;
				}else{
					$dep_eje=dol_print_date($hoy,"%m")*($lifetime/12)*$obj->amount;
				}
			}

			$sdo_redimir=$obj->amount-$dep_acumulada_final;

			$acum_al_final=$dep_acumulada_final+$dep_eje;

			$valor_neto_lib=$obj->amount-$acum_al_final;


			$anio=dol_print_date($fecha_inicio_depresiacion,"%Y");


			$mes=dol_print_date($fecha_inicio_depresiacion,"%m");


			$inpc_adq=inpc($mes,$anio);
			

			$anio_hoy=dol_print_date($db->idate($hoy),"%Y");
			$mes_hoy=dol_print_date($db->idate($hoy),"%m");

			$inpc_ultima_mitad=0;
			if ($anio<>$anio_hoy) {
				$inpc_ultima_mitad=inpc(floor($mes_hoy/2),$anio_hoy);
			}else{
				$inpc_ultima_mitad=inpc(floor(($mes_hoy-$mes)/2)+($mes-1),$anio);
			}


			$actz=truncateFloat($inpc_ultima_mitad/$inpc_adq,4);

			$dep_actu_n=$dep_eje*$actz;


			$fecha_depre=dol_print_date($date_purchase,"%Y-%m-%d");
			$fecha_hoy=dol_print_date($db->idate($hoy),"%Y-%m-%d");

			$dep_mensual_del_eje=0;
			if ($fecha_depre>$fecha_hoy) {
				$dep_mensual_del_eje=0;

			}else{
				$temp=0;
				if ( ($dep_acumulada_final+(($mes_hoy-1)*($lifetime/12)*$obj->amount) ) >$obj->amount ) {

					$temp=$obj->amount-$dep_acumulada_final;

				}else{
					if ($anio==$anio_hoy) {

						$temp= (($mes_hoy-1)-$mes)*($lifetime/12)*$obj->amount;
					}else{

						$temp= ($mes_hoy-1)*($lifetime/12)*$obj->amount;
					}
				}
				$dep_mensual_del_eje=$dep_eje-$temp;
			}



			$totalmes_uso_comp+=$mes_uso_comp;
			$totaldep_acumulada_final+=$dep_acumulada_final;
			$totaldep_eje+=$dep_eje;
			$totalsdo_redimir+=$sdo_redimir;
			$totalacum_al_final+=$acum_al_final;
			$totalvalor_neto_lib+=$valor_neto_lib;
			$totalinpc_adq+=$inpc_adq;
			$totalinpc_ultima_mitad+=$inpc_ultima_mitad;
			$totalactz+=$actz;
			$totaldep_actu_n+=$dep_actu_n;
			$totaldep_mensual_del_eje+=$dep_mensual_del_eje;
			$html.="
				<tr>
				  <td style=' border:0.5px solid;'>".$obj->clave."</td>
				  <td style=' border:0.5px solid;'>".$obj->depreciation_rate."</td>
				  <td style=' border:0.5px solid;'>".dol_print_date($obj->date_purchase,"%d/%m/%Y")."</td>
				  <td style=' border:0.5px solid;'>".$obj->amount."</td>
				  <td style=' border:0.5px solid;'>".dol_print_date($fecha_inicio_depresiacion,"%d/%m/%Y")."</td>
				  <td style=' border:0.5px solid;'>".price($mes_uso_comp_def)."</td>
				  <td style=' border:0.5px solid;'>".price(round($dep_acumulada_final,2))."</td>
				  <td style=' border:0.5px solid;'>".price(round($dep_eje,2))."</td>
				  <td style=' border:0.5px solid;'>".price(round($sdo_redimir,2))."</td>
				  <td style=' border:0.5px solid;'>".price(round($acum_al_final,2))."</td>
				  <td style=' border:0.5px solid;'>".price(round($valor_neto_lib))."</td>
				  <td style=' border:0.5px solid;'>".price($inpc_adq)."</td>
				  <td style=' border:0.5px solid;'>".price($inpc_ultima_mitad)."</td>
				  <td style=' border:0.5px solid;'>".$actz."</td>
				  <td style=' border:0.5px solid;'>".price(round($dep_actu_n,2))."</td>
				  <td style=' border:0.5px solid;'>".price(round($dep_mensual_del_eje,2))."</td>
				</tr>
			";
		}
		$html.="
				<tr>
				  <td style=' border:0.5px solid;'  colspan='6' align='right'><strong>Total:</strong></td>
				  <td style=' border:0.5px solid;' >".price(round($totaldep_acumulada_final,2))."</td>
				  <td style=' border:0.5px solid;' >".price(round($totaldep_eje,2))."</td>
				  <td style=' border:0.5px solid;' >".price(round($totalsdo_redimir,2))."</td>
				  <td style=' border:0.5px solid;' >".price(round($totalacum_al_final,2))."</td>
				  <td style=' border:0.5px solid;' >".price(round($totalvalor_neto_lib))."</td>
				  <td style=' border:0.5px solid;' >".price($totalinpc_adq)."</td>
				  <td style=' border:0.5px solid;' >".price($totalinpc_ultima_mitad)."</td>
				  <td style=' border:0.5px solid;' >".price($totalactz)."</td>
				  <td style=' border:0.5px solid;' >".price(round($totaldep_actu_n,2))."</td>
				  <td style=' border:0.5px solid;' >".price(round($totaldep_mensual_del_eje,2))."</td>
				</tr>
			";

		$html.="</tbody></table>";
    }
}
if (isset($_GET["xls"])) {
	print $html;
}


if (isset($_GET["pdf"])) {
	require_once '../../class/dompdf/dompdf_config.inc.php';
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper("letter","landscape");
	$dompdf->render();
	$dompdf->stream("balance_general.pdf",array('Attachment'=>0));
}
