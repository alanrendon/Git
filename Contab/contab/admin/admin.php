<?php
require("../../main.inc.php");


$date_init=dol_mktime(0,0,0,GETPOST("date_initmonth"),GETPOST("date_initday"),GETPOST("date_inityear"));
$date_final=dol_mktime(0,0,0,GETPOST("date_finalmonth"),GETPOST("date_finalday"),GETPOST("date_finalyear"));



llxHeader('',"Configuracion");
/* $head[$h][0] = "config.php";
$head[$h][1] = "Configuracion2";
$head[$h][2] = "dos";
$h++; */
$head=array(
    array("admin.php", "Cliente", "cliente" ),
    array("admin_prov.php", "Proveedor", "prov" ),
);


global $langs,$db;
$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title,$linkback,'setup');
dol_fiche_head($head, 'cliente', "Configuracion", 0, 'generic');


/*
	if(GETPOST('action')=='actualiza'){
		$sql='SELECT url
			FROM '.MAIN_DB_PREFIX.'contab_url
			WHERE entity='.$conf->entity;
		$rqs=$db->query($sql);
		$nrw=$db->num_rows($rqs);
		if($nrw>0){
			$sql="UPDATE ".MAIN_DB_PREFIX."contab_url SET url='".GETPOST('urlc')."' WHERE entity=".$conf->entity;
		}else{
			$sql="INSERT INTO ".MAIN_DB_PREFIX."contab_url (entity,url) VALUES(".$conf->entity.",'".GETPOST('urlc')."')";
		}
		//print $sql;
		$rqs=$db->query($sql);
		print "<script>window.location.href='admin.php'</script>";
	}
	$sql='SELECT url
			FROM '.MAIN_DB_PREFIX.'contab_url
			WHERE entity='.$conf->entity;
	$rqs=$db->query($sql);
	$nrw=$db->num_rows($rqs);
	if($nrw>0){
		$rs=$db->fetch_object($rqs);
		$url=$rs->url;
	}else{
		$url='';
	}


	print "<form method='POST' action='?action=actualiza'>";
	print "<table width='100%'>";
	print "<tr class='liste_titre'>";
	print "<td colspan='2'>URL conexion modulo</td>";
	print "</tr>";
	print "<tr>";
	print "<td>URL</td>";
	print "<td><input type='text' id='urlc' name='urlc' value='".$url."'></td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='2'><input type='submit' value='Guardar'></td>";
	print "</tr>";
	print "</table>";
	print "</form>";
*/
$form = new Form($db);
print load_fiche_titre("Facturas a Clientes",'','');
print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
	print '<table class="noborder" width="100%">'."\n";
		print '<tr class="liste_titre">'."\n";
		print '  <td>Ref</td>';
		print '  <td>Cliente</td>';
		print '  <td>Poliza Ref</td>';
		print '  <td>Fecha</td>';
		print '  <td >Fecha Poliza</td>';
		print '  <td style="width:200px !important;">';
			print ($form->select_date(
		        $date_init, 'date_init'
		    ));
		print '</td>';
		print '<td style="width:200px !important;">';
			print ($form->select_date(
		        $date_final, 'date_final'
		    ));
		print '</td>';
		print '<td>';
			print img_search("Buscar",'search');

		print '</td>';
		print "</tr>\n";
	print "</table>";
print "</form>";

