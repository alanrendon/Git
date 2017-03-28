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

llxHeader("","","");

$titre='Almacen Virtual Produccion';
$picto=('product');
dol_fiche_head("", 'factory', $titre, 0, $picto);

$sql="SELECT b.fk_product,c.ref,sum(b.qty_planned) as planeado,sum(IFNULL(qty_used,0)) as usado,c.price,c.label
		FROM ".MAIN_DB_PREFIX."factory a,".MAIN_DB_PREFIX."factorydet b,".MAIN_DB_PREFIX."product c
		WHERE a.entity=".$conf->entity." AND a.rowid=b.fk_factory AND b.fk_product=c.rowid 
		AND c.entity=".$conf->entity." GROUP BY b.fk_product ORDER BY c.ref";
$rqs=$db->query($sql);
$totalunidad=0;
$totalunven=0;
$totalventa=0;
print "<table width='100%' class='liste' >";
print "<tr class='liste_titre'>";
	print "<td>Producto</td>";
	print "<td>Etiqueta</td>";
	print "<td align='right'>Unidades</td>";
	print "<td align='right'>Prrecio de Venta<br>Unitario</td>";
	print "<td align='right'>Valor Venta</td>";
print "</tr>";
while($rs=$db->fetch_object($rqs)){
	$unidad=$rs->planeado-$rs->usado;
	$pric=$rs->price*$unidad;
	if($unidad>0){
		$totalunidad+=$unidad;
		$totalunven+=$rs->price;
		$totalventa+=$pric;
		print "<tr>";
			print "<td ><a href='".DOL_URL_ROOT."/product/card.php?id=".$rs->fk_product."'><img src='".DOL_URL_ROOT."/theme/eldy/img/object_product.png' alt='' class='classfortooltip' border='0'> ".$rs->ref."</a></td>";
			print "<td>".$rs->label."</td>";
			print "<td align='right'>".$unidad."</td>";
			print "<td align='right'>".number_format($rs->price,2)."</td>";
			print "<td align='right'>".number_format($pric,2)."</td>";
		print "</tr>";
	}
}
print "<tr>";
print "<td></td>";
print "<td align='right'><strong>Total:</strong></td>";
print "<td align='right'><strong>".$totalunidad."</strong></td>";
print "<td align='right'><strong>".number_format($totalunven,2)."</strong></td>";
print "<td align='right'><strong>".number_format($totalventa,2)."</strong></td>";
print "</tr>";
print "</table>";

llxFooter();
$db->close();
?>