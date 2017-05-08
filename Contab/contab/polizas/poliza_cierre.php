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
 *   	\file       dev/Contabpolizass/Contabpolizas_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-02-26 02:24
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

date_default_timezone_set("America/Mexico_City");

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../main.inc.php")) $res=@include '../../../../main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once '../class/poliza_cierre.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';

if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/contabpolizas.class.php')) {
	require_once DOL_DOCUMENT_ROOT.'/contab/class/contabpolizas.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/class/contabpolizas.class.php';
}

if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/contabpolizasdet.class.php')) {
	require_once DOL_DOCUMENT_ROOT.'/contab/class/contabpolizasdet.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/class/contabpolizasdet.class.php';
}

if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/contabcatctas.class.php')) {
	require_once DOL_DOCUMENT_ROOT.'/contab/class/contabcatctas.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/class/contabcatctas.class.php';
}

if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/contabperiodos.class.php')) {
	require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodos.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/class/contabperiodos.class.php';
}

if (file_exists(DOL_DOCUMENT_ROOT.'/contab/core/lib/contab.lib.php')){
	require_once DOL_DOCUMENT_ROOT.'/contab/core/lib/contab.lib.php';
} else {
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/core/lib/contab.lib.php';
}

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
if (file_exists(DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php')) {
	require_once DOL_DOCUMENT_ROOT . '/contab/class/contabsatctas.class.php';
} else {
	require_once DOL_DOCUMENT_ROOT . '/custom/contab/class/contabsatctas.class.php';
}
if (! $user->rights->contab->cont) {
	accessforbidden();
}
// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("bills");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');
$asiento 	= GETPOST('asiento');
$ref 		= GETPOST('ref');
$esfaccte	= GETPOST('fc');
$esfacprov	= GETPOST('fp');
$facid 		= GETPOST('facid','int');
$idpd 		= GETPOST('idpd', 'int');
$soc_type	= GETPOST("soc_type");
$socid 		= GETPOST("socid","int");



llxHeader('','','','','','','','',0,0);
$head = contab_prepare_head($object, $user);
dol_fiche_head($head, "Polizas", 'Contabilidad', 0, '');

$action		= GETPOST('action','alpha');
$act		= GETPOST('act','alpha');

if ($act == "generar") {	

	$ini=GETPOST('ini');
	$fin=GETPOST('fin');
	$anio=GETPOST('anio');
	$cresult=GETPOST('result');

	$pol = new Contabpolizas($db);						
	$pol->fetch_last_by_tipo_pol2('D',$anio, 12);
	$pol->tipo_pol='D';
	$pol->anio=$anio;
	$pol->mes=12;
	$pol->fecha=$anio.'-12-31';				
	$pol->cons=$pol->cons + 1;
	$pol->concepto='Poliza de cierre';
	$pol->pol_ajuste=1;
	$pol->comentario ='';
	$pol->fk_facture = '';
	$pol->ant_ctes = "";
	$pol->societe_type ='';
	$res=$pol->create($user->id);	

	$pol= new poliza_cierre($db);				
	$polizas=$pol->generar_poliza($ini, $fin, $anio, $res,$cresult);	
	if($polizas==1){
		print "<script>window.location.href='poliza.php?id=".$res."'</script>";	
	}else{
		setEventMessages('Error al crear la póliza','', 'errors');	
	}

	
}


$per = new Contabperiodos($db);
$aanios = array();
$aanios = $per->get_anios_array();
if (! $anio_selected > 0) {
	$anio_selected = date("Y");
}	
			
?>

<form method="post" action="?action=prearmar" id="createpolclose">
	<h2>Póliza de cierre</h2>
	<table>
		<tr>
			<td>Periodo contable:
				<select name="anio" >
					<option value="0">--Seleccione--</option>
			<?php 
					foreach ($aanios as $i => $anio) {
						?><option value="<?=$anio;?>" <?=($anio == $anio_selected) ? 'selected="selected"' : '' ;?>><?=$anio;?></option><?php
					}
			?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><b>Rango de cuentas</b></td>			
		</tr>
		<tr>
			<td>Inicio:</td><!-- <input type="text" name="ini" required> -->
			<td>
			<script>
  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        //this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " " )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( "#ini" ).combobox();
    $( "#fin" ).combobox();
    $( "#result" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#ini" ).toggle();
      $( "#fin" ).toggle();
      $( "#result" ).toggle();
    });
  });
  </script>
			<?php 
			$sqlc="SELECT cta,descta
				FROM ".MAIN_DB_PREFIX."contab_cat_ctas
				WHERE entity=".$conf->entity;
			$resc=$db->query($sqlc);
			?>
			<select name="ini" id="ini" >
				<option value=""></option>
				<?php 
				while($rqc=$db->fetch_object($resc)){
					$ac='';
					if($cuenta==$rqc->cta){
						$ac=' SELECTED';
					}
					print "<option value='".$rqc->cta."' ".$ac.">".$rqc->cta." - ".$rqc->descta."</option>";
				}
				?>
		  </select></td></tr>
		  <tr>		
			<td>Fin: </td><!-- <input type="text" name="fin" required> -->
				<td>
				<?php 
				$sqlc="SELECT cta,descta
					FROM ".MAIN_DB_PREFIX."contab_cat_ctas
					WHERE entity=".$conf->entity;
				$resc=$db->query($sqlc);
				?>
				<select name="fin" id="fin" >
					<option value=""></option>
					<?php 
					while($rqc=$db->fetch_object($resc)){
						$ac='';
						if($cuenta2==$rqc->cta){
							$ac=' SELECTED';
						}
						print "<option value='".$rqc->cta."' ".$ac.">".$rqc->cta." - ".$rqc->descta."</option>";
					}
					?>
			  </select>
			</td>			
		</tr>
		<tr>		
			<td>Cuenta de Resultados del ejercicio: </td><!-- <input type="text" name="fin" required> -->
				<td>
				<?php 
				$sqlc="SELECT cta,descta
					FROM ".MAIN_DB_PREFIX."contab_cat_ctas
					WHERE entity=".$conf->entity;
				$resc=$db->query($sqlc);
				?>
				<select name="result" id="result" >
					<option value=""></option>
					<?php 
					while($rqc=$db->fetch_object($resc)){
						$ac='';
						if($cuenta2==$rqc->cta){
							$ac=' SELECTED';
						}
						print "<option value='".$rqc->cta."' ".$ac.">".$rqc->cta." - ".$rqc->descta."</option>";
					}
					?>
			  </select>
			</td>			
		</tr>
		<tr>
			<td align="center" colspan="8">
				<br/>
				<input type="submit" name="Prearmar" class="button" value="Prearmar" >				
			</td>
		</tr>
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
	</table>	
</form>
<?php

if ($action == "prearmar") {	
	if(GETPOST('anio')){
		if(is_numeric(GETPOST('ini')) && is_numeric(GETPOST('fin'))){
			$ini=GETPOST('ini');
			$fin=GETPOST('fin');
			$cresult=GETPOST('result');
			$anio=GETPOST('anio');

			$pol= new poliza_cierre($db);
			$polizas=$pol->prearmar_poliza($ini, $fin, $anio);			
			$pol = new Contabpolizas($db);						
			$pol->fetch_last_by_tipo_pol2('D',$anio, 12);
			$pol->tipo_pol='D';
			$pol->anio=$anio;
			$pol->mes=12;
			$pol->fecha=$anio.'-12-31';				
			$pol->cons=$pol->cons + 1;
			?>

				<table class="noborder" style="width:100%">
					<tr class="liste_titre">
						<td colspan="2">Encabezado de la Poliza</td>
						<td style="text-align: right;"></td>
						<td  style="text-align: right;"></td>
						<td style="text-align: right;"></td>
						<td style="text-align: right;"></td>
					</tr>	
					<tr>
						<td colspan = "2">
							Poliza:
							<strong> 
							<?php 							
								print $pol->Get_folio_poliza()." Cons: ".$pol->cons;
							?>
							</strong>
						</td>
						<td colspan = "2">Fecha: <?php print $pol->fecha;?></td>
						<td colspan = "2">
							Documento Relacionado:</a>
						</td>
						
					</tr>						
					<tr>
						<td colspan = "4">
							Concepto: <strong>Poliza de cierre</strong>
							&nbsp;
							Comentario: <strong></strong>
						</td>
						<td colspan = "3" >
							Archivos adjuntos:<br/>
						</td>
					</tr>
					<tr>
						<td colspan = "6">
							Cheque a Nombre: <strong></strong>
							&nbsp;
							Num. Cheque: <strong></strong>
						</td>
					</tr>
					<?php
					if($pol->pol_ajuste==1){ 
					?>
						<tr>
							<td colspan = "6">
								<strong>Poliza del periodo de ajuste</strong>
							</td>
						</tr>
					<?php 
					}	
					?>				
					<tr class="liste_titre">
						<td>Asiento</td>
						<td>Cuenta</td>
						<td>Concepto</td>
						<td>UUID</td>
						<td style="text-align: right; width: 12%;">Debe</td>
						<td style="text-align: right; width: 12%;">Haber</td>
					</tr>		
					<?php
					$i=0;	
					$totdebe=0;
					$tothaber=0;
					foreach ($polizas as $value) {
						$i++; 
						if($value->debe!=0 && $value->haber!=0){
							?>
							<tr>
								<td><?php print $i ?></td>
								<td><?php print $value->cuenta; 
									$ctas = new Contabcatctas($db);
									$ctas->fetch_by_Cta($value->cuenta, false);
									print " ".$ctas->descta;
							?>
								</td>
								<td></td>
								<td></td>
								<?php
									//Si hay saldo en debe se colocara en el haber al generar la poliza de cierre
									$tothaber=$tothaber+$value->debe;
									?>
									<td style=" text-align: right;"></td>
									<td style=" text-align: right;"><?=number_format($value->debe,2)?></td>
								</tr>
							<?php
							$i++;
							?>
								<tr>
									<td><?php print $i ?></td>
									<td><?php print $value->cuenta; 
										$ctas = new Contabcatctas($db);
										$ctas->fetch_by_Cta($value->cuenta, false);
										print " ".$ctas->descta;
								?>
									</td>
									<td></td>
									<td></td>
									<?php //Si hay saldo en el haber se colocara en el debeal generar la poliza de cierre
									  	$totdebe=$totdebe+$value->haber;
										?>
										<td style="text-align: right;"><?=number_format($value->haber,2)?></td>
										<td style=" text-align: right;"></td>
									  <?php 
									
									?>
									</tr>
								<?php
						}else{
						?>
						<tr>
							<td><?php print $i ?></td>
							<td><?php print $value->cuenta; 
							$ctas = new Contabcatctas($db);
							$ctas->fetch_by_Cta($value->cuenta, false);
							print " ".$ctas->descta;
								?>
									
							</td>
							<td></td>
							<td></td>
							<?php
								if($value->debe != 0){
									//Si hay saldo en debe se colocara en el haber al generar la poliza de cierre
									$tothaber=$tothaber+$value->debe;
							?>
									<td style=" text-align: right;"></td>
									<td style=" text-align: right;"><?=number_format($value->debe,2)?></td>
						  <?php }else{
						  			//Si hay saldo en el haber se colocara en el debeal generar la poliza de cierre
								  	$totdebe=$totdebe+$value->haber;
						  		?>
									<td style="text-align: right;"><?=number_format($value->haber,2)?></td>
									<td style=" text-align: right;"></td>
						  <?php 
								}
							?>
						</tr>
					<?php
						}
					}
					$i++;
					if($totdebe>$tothaber){
						$difer=$totdebe-$tothaber;
						$tothaber+=$difer;
						?>
						<tr>
							<td><?php print $i ?></td>
							<td><?php print $cresult;/* $value->cuenta */; 
							$ctas = new Contabcatctas($db);
							$ctas->fetch_by_Cta($cresult, false);
							print " ".$ctas->descta;
								?>			
							</td>
							<td></td>
							<td></td>	
							<td style=" text-align: right;"></td>
							<td style=" text-align: right;"><?=number_format($difer, 2)?></td>
							</tr>
						<?php
					}else{
						$difer=$tothaber-$totdebe;
						$totdebe+=$difer;
						?>
						<tr>
							<td><?php print $i ?></td>
							<td><?php print $cresult;/* $value->cuenta */; 
							$ctas = new Contabcatctas($db);
							$ctas->fetch_by_Cta($cresult, false);
							print " ".$ctas->descta;
								?>			
							</td>
							<td></td>
							<td></td>	
							<td style=" text-align: right;"><?=number_format($difer, 2)?></td>
							<td style=" text-align: right;"></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td colspan='4' align="right">
							<strong>Total</strong>
						</td>
						<td style="text-align: right;"><?=$langs->getCurrencySymbol($conf->currency).' '.number_format($totdebe, 2)?></td>
						<td style="text-align: right;"><?=$langs->getCurrencySymbol($conf->currency).' '.number_format($tothaber, 2)?></td>
						
					</tr>						
					<?php
					unset($pol);					
					?>
					</table>
					<br><hr><br>
					<?php print "<a align='center' class='button' href='poliza_cierre.php?action=prearmar&anio=".$anio."&ini=".$ini."&fin=".$fin."&result=".$cresult."&act=generar'>Generar</a>"?>					
				</form>
			<?php			
		}else{
			setEventMessages('Introduce solamente números','', 'errors');		
		}
	}else{
		setEventMessages('Seleccione un periodo contable','', 'errors');		
	}
	
}


llxFooter();
$db->close();
?>

