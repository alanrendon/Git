<?php
require "../../main.inc.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

require_once DOL_DOCUMENT_ROOT.'/factory/class/factory.class.php';
require_once DOL_DOCUMENT_ROOT.'/factory/core/lib/factory.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("factory@factory");
$langs->load("admin");
$langs->load("errors");
$mesg='';
$action = GETPOST('action','alpha');


$title = $langs->trans('FactorySetup');
$tab = $langs->trans("FactorySetup");


llxHeader("",$langs->trans("Grupo de Tareas"),'EN:Factory_Configuration|FR:Configuration_module_Factory|ES:Configuracion_Factory');

$form=new Form($db);
$htmlother=new FormOther($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("Grupo de Tareas"),$linkback,'setup');

$head = factory_admin_prepare_head();
dol_fiche_head($head, 'tareas', $tab, 0, 'factory@factory');

if($action=='addgruop'){
	$name=GETPOST('gruponame');
	$sql="INSERT INTO ".MAIN_DB_PREFIX."factory_group (name,entity) VALUES ('".$db->escape($name)."',".$conf->entity.")";
	$rs=$db->query($sql);
	print "<script>window.location.href='grupotareas.php'</script>";
}

if($action=='delgroup'){
	$idr=GETPOST('idr');
	$sql="SELECT rowid, fk_groupt, etiqueta
			FROM ".MAIN_DB_PREFIX."factory_groupdet
			WHERE fk_groupt=".$idr;
	$rs=$db->query($sql);
	$nr=$db->num_rows($rs);
	if($nr=='0'){
		$sql="DELETE FROM ".MAIN_DB_PREFIX."factory_group WHERE rowid=".$idr;
		$rs=$db->query($sql);
		print "<script>window.location.href='grupotareas.php'</script>";
	}else{
		$mesg='No puede eliminar un grupo con tareas asignadas';
	}
}
if($action=='deltarea'){
	$idr=GETPOST('idr');
	$idt=GETPOST('idt');
	$sql="DELETE FROM ".MAIN_DB_PREFIX."factory_groupdet WHERE fk_groupt=".$idr." AND rowid=".$idt;
	$rs=$db->query($sql);
	print "<script>window.location.href='grupotareas.php'</script>";
}

if($action=='addtarealine'){
	$idr=GETPOST('idr');
	$tareaname=GETPOST('tareaname');
	$sql="INSERT INTO ".MAIN_DB_PREFIX."factory_groupdet (fk_groupt,etiqueta) VALUES (".$idr.",'".$db->escape($tareaname)."')";
	$rs=$db->query($sql);
	print "<script>window.location.href='grupotareas.php'</script>";
}

print "<table class='noborder' width='100%'>";
	print "<tr class='liste_titre'>";
		print "<td colspan='2'>";
			print "Nuevo Grupo";
		print "</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='2'><form action='grupotareas.php?action=addgruop' method='POST'>";
			print "Nombre grupo: ";
			print " <input type='text' name='gruponame' size='50' required>";
			print " <input type='submit' value='Guardar'>";
		print "</form></td>";
	print "</tr>";
print "</table>";

print "<br><br>";

$sql="SELECT rowid, name FROM ".MAIN_DB_PREFIX."factory_group WHERE entity=".$conf->entity;
$rs=$db->query($sql);
$nr=$db->num_rows($rs);
print "<table class='noborder' width='100%'>";
	print "<tr class='liste_titre'>";
		print "<td colspan='4'>";
			print "Lista de grupos";
		print "</td>";
	print "</tr>";
	if($nr>0){
		while($rq=$db->fetch_object($rs)){
			print "<tr class='pair'>";
				print "<td width='20%'>";
				print "Nombre Grupo:";
				print "</td>";
				print "<td>";
				print $rq->name;
				print "</td>";
				print "<td>";
				print " <a href='grupotareas.php?action=delgroup&idr=".$rq->rowid."'>".img_delete()."</a>";
				print "</td>";
				print "<td width='20%'>";
				print "<form method='POST' action='grupotareas.php?action=addtarea&idr=".$rq->rowid."'><input type='submit' value='Agregar Tarea'></form>";
				print "</td>";
			print "</tr>";
			$sql2="SELECT rowid,fk_groupt,etiqueta FROM ".MAIN_DB_PREFIX."factory_groupdet WHERE fk_groupt=".$rq->rowid;
			$rs2=$db->query($sql2);
			$nr2=$db->num_rows($rs2);
			if($nr2>0){
				while ($rq2=$db->fetch_object($rs2)){
					print "<tr>";
						print "<td width='20%'>";
							print "Tarea:";
						print "</td>";
						print "<td colspan='2'>";
							print $rq2->etiqueta;
						print "</td>";
						print "<td width='20%'>";
							print "<a href='grupotareas.php?action=deltarea&idr=".$rq->rowid."&idt=".$rq2->rowid."'>".img_delete()."</a>";
						print "</td>";
					print "</tr>";
				}
			}
			if($action=='addtarea' && GETPOST('idr')==$rq->rowid){
				print "<tr>";
					print "<td colspan='4' align='center'><form action='grupotareas.php?action=addtarealine&idr=".$rq->rowid."' method='POST'>";
						print "Etiqueta Tarea: ";
						print " <input type='text' name='tareaname' size='50' required>";
						print " <input type='submit' value='Guardar'>";
					print "</form></td>";
				print "</tr>";
			}
			print "<tr><td colspan='4' style='text-align: center;'><hr></td></tr>";
		}
	}
print "</table>";

if($mesg!=''){
	dol_htmloutput_errors($mesg);
}
$db->close();
llxFooter();
?>
