<?php
$res=@include("../../main.inc.php");                    // For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
    $res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");        // For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/lib/product.lib.php";
require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
require_once DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php";
require_once DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php";

dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');

$langs->load("bills");
$langs->load("products");

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$cancel=GETPOST('cancel','alpha');
$key=GETPOST('key');
$parent=GETPOST('parent');

// Security check
if (! empty($user->societe_id)) $socid=$user->societe_id;
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result=restrictedArea($user,'produit|service',$fieldvalue,'product&product','','',$fieldtype,$objcanvas);

$mesg = '';

$object = new Product($db);
$factory = new Factory($db);
$productid=0;
if ($id || $ref)
{
	$result = $object->fetch($id,$ref);
	$productid=$object->id;
	$id=$object->id;
	$factory->id =$id;
}


/*
 * Actions
 */


/*
 * View
 */


$productstatic = new Product($db);
$form = new Form($db);

llxHeader("","",$langs->trans("CardProduct".$product->type));

dol_htmloutput_mesg($mesg);


$head=product_prepare_head($object, $user);
$titre=$langs->trans("CardProduct".$object->type);
$picto=('product');
dol_fiche_head($head, 'factory', $titre, 0, $picto);

if ($id || $ref)
{
	if ($result)
	{
		print '<table class="border" width="100%">';
		print "<tr>";

		$bproduit = ($object->isproduct()); 

		// Reference
		print '<td width="25%">'.$langs->trans("Ref").'</td><td>';
		print $form->showrefnav($object,'ref','',1,'ref');
		print '</td></tr>';

		// Libelle
		print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->libelle.'</td>';
		print '</tr>';

		// MultiPrix
		if ($conf->global->PRODUIT_MULTIPRICES)
		{
			if ($socid)
			{
				$soc = new Societe($db);
				$soc->id = $socid;
				$soc->fetch($socid);

				print '<tr><td>'.$langs->trans("SellingPrice").'</td>';

				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC')
				{
					print '<td>'.price($object->multiprices_ttc["$soc->price_level"]);
				}
				else
				{
					print '<td>'.price($object->multiprices["$soc->price_level"]);
				}

				if ($object->multiprices_base_type["$soc->price_level"])
				{
					print ' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else
				{
					print ' '.$langs->trans($object->price_base_type);
				}
				print '</td></tr>';

				// Prix mini
				print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC')
				{
					print price($object->multiprices_min_ttc["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else
				{
					print price($object->multiprices_min["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				print '</td></tr>';

				// TVA
				print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx["$soc->price_level"],true).'</td></tr>';
			}
			else
			{
				for ($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++)
				{
					// TVA
					if ($i == 1) // We show only price for level 1
					{
					     print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx[1],true).'</td></tr>';
					}
					
					print '<tr><td>'.$langs->trans("SellingPrice").' '.$i.'</td>';
		
					if ($object->multiprices_base_type["$i"] == 'TTC')
					{
						print '<td>'.price($object->multiprices_ttc["$i"]);
					}
					else
					{
						print '<td>'.price($object->multiprices["$i"]);
					}
		
					if ($object->multiprices_base_type["$i"])
					{
						print ' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else
					{
						print ' '.$langs->trans($object->price_base_type);
					}
					print '</td></tr>';
		
					// Prix mini
					print '<tr><td>'.$langs->trans("MinPrice").' '.$i.'</td><td>';
					if ($object->multiprices_base_type["$i"] == 'TTC')
					{
						print price($object->multiprices_min_ttc["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else
					{
						print price($object->multiprices_min["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					print '</td></tr>';
				}
			}
		}
		else
		{
			// TVA
			print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->tva_tx.($object->tva_npr?'*':''),true).'</td></tr>';
			
			// Price
			print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
			if ($object->price_base_type == 'TTC')
			{
				print price($object->price_ttc).' '.$langs->trans($object->price_base_type);
				$sale="";
			}
			else
			{
				print price($object->price).' '.$langs->trans($object->price_base_type);
				$sale=$object->price;
			}
			print '</td></tr>';
		
			// Price minimum
			print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
			if ($object->price_base_type == 'TTC')
			{
				print price($object->price_min_ttc).' '.$langs->trans($object->price_base_type);
			}
			else
			{
				print price($object->price_min).' '.$langs->trans($object->price_base_type);
			}
			print '</td></tr>';
		}

		// Status (to sell)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
		print $object->getLibStatut(2,0);
		print '</td></tr>';

		// Status (to buy)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
		print $object->getLibStatut(2,1);
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("PhysicalStock").'</td>';
		print '<td>'.$object->stock_reel.'</td></tr>';
		
		print '</table>';
		
		dol_fiche_end();

		// indique si on a déjà une composition de présente ou pas
		$compositionpresente=0;
		
		$head=factory_product_prepare_head($object, $user);
		$titre=$langs->trans("Factory");
		$picto="factory@factory";
		dol_fiche_head($head, 'sutitutoproduct', $titre, 0, $picto);
		//$object->id
		//$object->status_buy;
		if($object->type==0 && $object->status_buy==1){
			?>
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
			    $( "#prodsustituto" ).combobox();
			    $( "#toggle" ).click(function() {
			      $( "#prodsustituto" ).toggle();
			    });
			  });
			  </script>
			<?php
			if($action=='addsustituto'){
				$prodsustituto=GETPOST('prodsustituto');
				$sqlv="INSERT INTO ".MAIN_DB_PREFIX."factory_producto_sustituto (fk_product,fk_sustituto,entity)
						VALUES (".$object->id.",".$prodsustituto.",".$conf->entity.")";
				$resv=$db->query($sqlv);
				print "<script>window.location.href='sustituto.php?id=".$object->id."'</script>";
			}
			if($action=='delsustituto'){
				$prodsustituto=GETPOST('idsus');
				$sqlv="DELETE FROM ".MAIN_DB_PREFIX."factory_producto_sustituto
						WHERE fk_product=".$object->id." AND fk_sustituto=".$prodsustituto." AND entity=".$conf->entity;
				$resv=$db->query($sqlv);
				print "<script>window.location.href='sustituto.php?id=".$object->id."'</script>";
			}
			
			print "<table class='noborder' width='100%'>";
				print "<tr class='liste_titre'>";
					print "<td colspan='3'>";
						print "Agregar producto sustituto";
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td width='15%'><form method='POST' action='sustituto.php?id=".$object->id."&action=addsustituto'>";
						print "Producto";
					print "</td>";
					print "<td>";
						
						$sqlc="SELECT rowid,ref,label FROM ".MAIN_DB_PREFIX."product
						WHERE fk_product_type=0 AND entity=".$conf->entity." AND rowid!=".$object->id." 
      					AND tobuy=1 AND rowid NOT IN(SELECT fk_sustituto FROM ".MAIN_DB_PREFIX."factory_producto_sustituto 
						WHERE entity=".$conf->entity." AND fk_product=".$object->id.")";
						$resc=$db->query($sqlc);
						print '<select name="prodsustituto" id="prodsustituto" >';
						print '<option value=""></option>';
							while($rqc=$db->fetch_object($resc)){
								print "<option value='".$rqc->rowid."' >".$rqc->ref." - ".$rqc->label."</option>";
							}
					  	print '</select>'; 
					print "</td>";
					print "<td>";
						print "<input type='submit' value='Guardar'>";
					print "</td>";
				print "</tr>";
			print "</table>";
			
			print "<br>";
			
			print "<table class='noborder' width='100%'>";
				print "<tr class='liste_titre'>";
					print "<td colspan='3'>";
						print "Productos sustituto";
					print "</td>";
				print "</tr>";
				print "<tr class='liste_titre'>";
					print "<td>";
						print "Ref.";
					print "</td>";
					print "<td>";
						print "Etiqueta";
					print "</td>";
					print "<td>";
						print "&nbsp;";
					print "</td>";
				print "</tr>";
				$sqlm="SELECT ref,label,fk_sustituto FROM ".MAIN_DB_PREFIX."factory_producto_sustituto a, ".MAIN_DB_PREFIX."product b
						WHERE a.entity=".$conf->entity." AND fk_product=".$object->id." AND a.fk_sustituto=.b.rowid";
				//print $sqlm;
				$resc2=$db->query($sqlm);
				$m=0;
				while($rqc=$db->fetch_object($resc2)){
					if($m==1){
					 $mm=" class='pair'";
					 $m=0;
					}else{
					 $mm=" class='impair'";
					 $m=1;
					}
					print "<tr ".$mm.">";
						print "<td>";
							print $rqc->ref;
						print "</td>";
						print "<td>";
							print $rqc->label;
						print "</td>";
						print "<td>";
							print "<a href='sustituto.php?id=".$object->id."&idsus=".$rqc->fk_sustituto."&action=delsustituto'>".img_delete()."</a>";
						print "</td>";
					print "</tr>";
				}
			print "</table>";
		}else{
			if($object->type==0 && $object->status_buy==0){
				print "<strong>Los productos en estado Fuera de compra no pueden tener sustituto</strong>";
			}
		}
		
	}
}


llxFooter();
$db->close();


?>