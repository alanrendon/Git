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

$result = restrictedArea($user,'produit|service',$fieldvalue,'product&product','','',$fieldtype,$objcanvas);

$mesg = '';

$object = new Product($db);
$factory = new Factory($db);
$productid = 0;
if ($id || $ref) {
	$result = $object->fetch($id,$ref);
	$productid=$object->id;
	$id=$object->id;
	$factory->id =$id;
}

$productstatic = new Product($db);
$form = new Form($db);

llxHeader("","",$langs->trans("CardProduct".$product->type));

dol_htmloutput_mesg($mesg);

$head = product_prepare_head($object, $user);
$titre = $langs->trans("CardProduct".$object->type);
$picto = ('product');
dol_fiche_head($head, 'factory', $titre, 0, $picto);

if ($id || $ref) {
	if ($result) {
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
		if ($conf->global->PRODUIT_MULTIPRICES) {
			if ($socid) {
				$soc = new Societe($db);
				$soc->id = $socid;
				$soc->fetch($socid);

				print '<tr><td>'.$langs->trans("SellingPrice").'</td>';

				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC') {
					print '<td>'.price($object->multiprices_ttc["$soc->price_level"]);
				}
				else {
					print '<td>'.price($object->multiprices["$soc->price_level"]);
				}

				if ($object->multiprices_base_type["$soc->price_level"]) {
					print ' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else {
					print ' '.$langs->trans($object->price_base_type);
				}
				print '</td></tr>';

				// Prix mini
				print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
				if ($object->multiprices_base_type["$soc->price_level"] == 'TTC') {
					print price($object->multiprices_min_ttc["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				else {
					print price($object->multiprices_min["$soc->price_level"]).' '.$langs->trans($object->multiprices_base_type["$soc->price_level"]);
				}
				print '</td></tr>';

				// TVA
				print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx["$soc->price_level"],true).'</td></tr>';
			}
			else {
				for ($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {
					// TVA
					if ($i == 1) { // We show only price for level 1 
					     print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->multiprices_tva_tx[1],true).'</td></tr>';
					}
					
					print '<tr><td>'.$langs->trans("SellingPrice").' '.$i.'</td>';
		
					if ($object->multiprices_base_type["$i"] == 'TTC') {
						print '<td>'.price($object->multiprices_ttc["$i"]);
					}
					else {
						print '<td>'.price($object->multiprices["$i"]);
					}
		
					if ($object->multiprices_base_type["$i"]) {
						print ' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else {
						print ' '.$langs->trans($object->price_base_type);
					}
					print '</td></tr>';
		
					// Prix mini
					print '<tr><td>'.$langs->trans("MinPrice").' '.$i.'</td><td>';
					if ($object->multiprices_base_type["$i"] == 'TTC') {
						print price($object->multiprices_min_ttc["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					else {
						print price($object->multiprices_min["$i"]).' '.$langs->trans($object->multiprices_base_type["$i"]);
					}
					print '</td></tr>';
				}
			}
		}
		else {
			// TVA
			print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($object->tva_tx.($object->tva_npr?'*':''),true).'</td></tr>';
			
			// Price
			print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
			if ($object->price_base_type == 'TTC') {
				print price($object->price_ttc).' '.$langs->trans($object->price_base_type);
				$sale="";
			}
			else {
				print price($object->price).' '.$langs->trans($object->price_base_type);
				$sale=$object->price;
			}
			print '</td></tr>';
		
			// Price minimum
			print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
			if ($object->price_base_type == 'TTC') {
				print price($object->price_min_ttc).' '.$langs->trans($object->price_base_type);
			}
			else {
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
		
		$head = factory_product_prepare_head($object, $user);
		$titre = $langs->trans("Factory");
		$picto = "factory@factory";
		dol_fiche_head($head, 'pendienteproduct', $titre, 0, $picto);
		
		$sql = "SELECT f.rowid as idorden, fd.fk_product as idprod, label, SUM(stock) stock, SUM(fd.qty_planned) cant FROM ".MAIN_DB_PREFIX."factory f ";
		$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."factorydet fd ON fd.fk_factory = f.rowid ";
		$sql .= "LEFT JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = fd.fk_product WHERE stock <= 0 GROUP BY idprod LIMIT 15";
		$res = $db->query($sql);
		$num = $db->num_rows($res);
		$contador = 1;
		
		if( $num > 0 ) {

			print '<div width="100%" align="right" style="padding:10px">
						<a href="pdf/resumen.php?id='.$id.'" class="butAction" target="_blank">Ver PDF</a>
						<a href="pendientes.php?id='.$id.'" id="ver_detalle" class="butAction">Ver detalle</a>
					</div>';
			
			print '<div id="div_productos_pendientes">';
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td>Producto</td>';
			print '<td>Etiqueta</td>';
			print '<td align="center">Stock</td>';
			print '<td align="center">Cantidad requerida</td>';
			print '</tr>';
			while( $obj = $db->fetch_object($res) ) {
				print '<tr class="';
				print ( $contador%2 == 0 ) ? 'pair': 'impair';
				print '">';
				print '<td align="left">'.$factory->getNomUrlFactory($obj->idprod, 1,'fiche');
				print $factory->PopupProduct($obj->idprod);
				print '</td>';
				print '<td>'.$obj->label.'</td>';
				print '<td align="center" style="color:#FF0000">'.$obj->stock.'</td>';
				print '<td align="center">'.$obj->cant.'</td>';
				print '</tr>';
				$contador++;
			}
			print '</table>';
			print '</div>';
		}
		else {
			print '<div><strong>No se encontraron productos pendientes.</strong></div>';
		}
	}
}
llxFooter();
$db->close();