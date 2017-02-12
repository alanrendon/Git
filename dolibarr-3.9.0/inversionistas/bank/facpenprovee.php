<?php
require('../../main.inc.php');

if(GETPOST('action')=='desexcel'){
	header("Content-type: application/ms-excel");
	header("Content-disposition: attachment; filename=deudas_proveedor.xls");
	print "<table border='1' width='80%' >";
		print "<tr class='liste_titre'>";
			print "<td>Proveedor</td>";
			print "<td >Deuda Pendiente</td>";
		print "</tr>";
	$sql="SELECT a.fk_soc, (sum(a.total_ttc) -IFNULL(sum(b.amount),0)) as restante, c.nom
	     FROM ".MAIN_DB_PREFIX."facture_fourn a
			LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn b ON a.rowid=b.fk_facturefourn,
			".MAIN_DB_PREFIX."societe c
		WHERE a.entity=".$conf->entity." AND a.fk_statut=1 AND a.fk_soc=c.rowid GROUP BY a.fk_soc";
	$rqs=$db->query($sql);
	while($rs=$db->fetch_object($rqs)){
		print "<tr>";
			print "<td>".$rs->nom."</td>";
			print "<td align='right'>".price($rs->restante)."</td>";
		print "</tr>";
	}
	print "</table>";
}
if(GETPOST('action')=='desexcelp'){
	header("Content-type: application/ms-excel");
	header("Content-disposition: attachment; filename=deudas_proveedor_detalle.xls");
	print "<table class='border' width='100%' >";
	print "<tr class='liste_titre'>";
	print "<td>Proveedor</td>";
	print "<td>Factura</td>";
	print "<td align='right'>Importe</td>";
	print "<td align='right'>Pagado</td>";
	print "<td>Fecha Limite de Pago</td>";
	print "</tr>";
	$sql="SELECT a.fk_soc, a.ref,a.total_ttc, IFNULL(b.amount,0) as pagado, c.nom, a.date_lim_reglement
		FROM ".MAIN_DB_PREFIX."facture_fourn a
			LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn b ON a.rowid=b.fk_facturefourn,
			".MAIN_DB_PREFIX."societe c
		WHERE a.entity=".$conf->entity." AND a.fk_soc=".GETPOST('idp')." AND a.fk_statut=1 AND a.fk_soc=c.rowid ";
	$rqs=$db->query($sql);
	while($rs=$db->fetch_object($rqs)){
		print "<tr>";
		print "<td>".$rs->nom."</td>";
		print "<td>".$rs->ref."</td>";
		print "<td align='right'>".price($rs->total_ttc)."</td>";
		print "<td align='right'>".price($rs->pagado)."</td>";
		print "<td>".$rs->date_lim_reglement."</td>";
		print "</tr>";
	}
}
if(GETPOST('action')==''){
	llxHeader("","Deudas Proveedor",'');
	print_fiche_titre('Deudas Proveedor','','setup');
	print "<div align='right'><a href='facpenprovee.php?action=desexcel' target='_blank' class='button'>Descargar Excel</a></div><br>";
	print "<table class='border' width='100%' >";
	print "<tr class='liste_titre'>";
		print "<td>Proveedor</td>";
		print "<td >Deuda Pendiente</td>";
		print "<td align='center'>Detalle</td>";
	print "</tr>";
	$sql="SELECT a.fk_soc, (sum(a.total_ttc) -IFNULL(sum(b.amount),0)) as restante, c.nom
	     FROM ".MAIN_DB_PREFIX."facture_fourn a 
			LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn b ON a.rowid=b.fk_facturefourn, 
			".MAIN_DB_PREFIX."societe c
		WHERE a.entity=".$conf->entity." AND a.fk_statut=1 AND a.fk_soc=c.rowid GROUP BY a.fk_soc";
	$rqs=$db->query($sql);
	while($rs=$db->fetch_object($rqs)){
		print "<tr>";
			print "<td><a href='".DOL_MAIN_URL_ROOT."/fourn/card.php?socid=".$rs->fk_soc."'>".$rs->nom."</a></td>";
			print "<td align='right'>".price($rs->restante)."</td>";
			print "<td align='center'><a href='facpenprovee.php?action=detalle&idp=".$rs->fk_soc."'>".img_search()."</a></td>";
		print "</tr>";
	}
	print "</table>";
}
if(GETPOST('action')=='detalle'){
	llxHeader("","Deudas Proveedor",'');
	print_fiche_titre('Deudas Proveedor','','setup');
	print "<div align='right'><a href='facpenprovee.php?action=desexcelp&idp=".GETPOST('idp')."' target='_blank' class='button'>Descargar Excel</a> 
			 <a href='facpenprovee.php'>Volver a listado</a></div><br>";
	print "<table class='border' width='100%' >";
		print "<tr class='liste_titre'>";
			print "<td>Proveedor</td>";
			print "<td>Factura</td>";
			print "<td align='right'>Importe</td>";
			print "<td align='right'>Pagado</td>";
			print "<td>Fecha Limite de Pago</td>";
		print "</tr>";
		$sql="SELECT a.rowid, a.fk_soc, a.ref,a.total_ttc, IFNULL(b.amount,0) as pagado, c.nom, a.date_lim_reglement
		FROM ".MAIN_DB_PREFIX."facture_fourn a 
			LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn b ON a.rowid=b.fk_facturefourn, 
			".MAIN_DB_PREFIX."societe c
		WHERE a.entity=".$conf->entity." AND a.fk_soc=".GETPOST('idp')." AND a.fk_statut=1 AND a.fk_soc=c.rowid ";
		$rqs=$db->query($sql);
		while($rs=$db->fetch_object($rqs)){
			print "<tr>";
			print "<td><a href='".DOL_MAIN_URL_ROOT."/fourn/card.php?socid=".$rs->fk_soc."'>".$rs->nom."</a></td>";
			print "<td><a href='".DOL_MAIN_URL_ROOT."/fourn/facture/card.php?facid=".$rs->rowid."'>".$rs->ref."</a></td>";
			print "<td align='right'>".price($rs->total_ttc)."</td>";
			print "<td align='right'>".price($rs->pagado)."</td>";
			print "<td>".$rs->date_lim_reglement."</td>";
			print "</tr>";
		}
}

