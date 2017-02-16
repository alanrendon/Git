<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/cclinico/class/skeleton_class.class.php');
dol_include_once('/cclinico/class/pacientes.class.php');
dol_include_once('/cclinico/class/consultas.class.php');
// Load traductions files requiredby by page
$langs->load("cclinico");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_field1=GETPOST("search_field1");
$search_field2=GETPOST("search_field2");

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Skeleton_Class($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('skeleton'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/cclinico/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		$search_name=GETPOST("search_name");
		$search_ref=GETPOST("search_ref");
		$action='create';
		
	}

}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans("Code1"),'');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Control Clínico"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	/*dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// LIST_OF_TD_LABEL_FIELDS_CREATE
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';*/

	print '<div class="fichecenter"><div class="fichethirdleft">';
	// Search thirdparty


	
		print '<form method="post" action="'.DOL_URL_ROOT.'/core/search.php">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="act" value="buscar">';
		if (! empty($conf->societe->enabled) && $user->rights->societe->lire)
		{
			$listofsearchfields['search_nombre']=array('text'=>'Nombre');
		}
		// Search contact/address
		if (! empty($conf->societe->enabled) && $user->rights->societe->lire)
		{
			$listofsearchfields['search_referencia']=array('text'=>'Referencia');
		}

		print '
			<table class="noborder nohover centpercent">
				<tbody>
					<tr class="liste_titre">
						<td colspan="3">'.$langs->trans("Code2").'</td>
					</tr>
					<tr class="impair">
						<td class="nowrap">
							<label for="search_nombre">Nombre</label>:</td>
						<td>
							<input type="text" class="flat" name="search_name" id="search_name" size="18" value="'.$search_name.'">
						</td>
						<td rowspan="2"><input type="submit" value="'.$langs->trans("Code2").'" class="button">
						</td>
						</tr>
						<tr class="impair">
						<td class="nowrap">
							<label for="search_referencia">'.$langs->trans("Code3").'</label>:
						</td>
						<td>
							<input type="text" class="flat" name="search_ref" id="search_referencia" value="'.$search_ref.'" size="18">
						</td>
					</tr>
				</tbody>
			</table>
		';


		print '</form>';
		print '<br>';

		$buscar_name="";
		if (!empty($search_name)) {
			$buscar_name=" and CONCAT(a.lastname,' ',a.firstname) LIKE '%".$search_name."%' ";
		}
		$buscar_ref="";
		if (!empty($search_ref)) {
			$buscar_ref=" and a.Ref LIKE '%".$search_ref."%'  ";
		}

		$sql = "SELECT COUNT(*) con FROM llx_consultas WHERE entity=".$conf->entity.";";
        $result=$db->query($sql);
        $obj = $db->fetch_object($result);  
        $sql = "SELECT COUNT(*) con FROM llx_pacientes as a WHERE a.statut=1 and a.entity=".$conf->entity.";";
        $result2=$db->query($sql);
        $obj2 = $db->fetch_object($result2);    

		print '<table class="noborder" width="100%">
				<tbody>
					<tr class="liste_titre">
						<td colspan="2">'.$langs->trans("Code4").'</td>
					</tr>
					<tr class="pair">
						<td>
							<a href="pacientes_list.php">'.$langs->trans("Code5").'</a>
						</td>
						<td align="right">'.$obj2->con.'</td>
					</tr>
					<tr class="impair">
						<td>
							<a href="consultas_list.php">'.$langs->trans("Code6").'</a>
						</td>
						<td align="right">'.$obj->con.'</td>
					</tr>
				</tbody>
			   </table>';
	
	print '</div>';
	print '<div class="fichetwothirdright">
				<div class="ficheaddleft">';
	print '<!-- last thirdparties modified -->
	  <table class="noborder" width="100%">
		  <tbody>
		  	<tr class="liste_titre">
		  		<th colspan="2">'.$langs->trans("Code7").'</th>
		  		<th>&nbsp;</th>
		  		<th align="right">'.$langs->trans("Code8").'</th>
		  	</tr>';

		  	$sql = "SELECT
				rowid,datec,statut
			FROM
				llx_pacientes AS a
			WHERE
		    	a.entity=".$conf->entity."
			".$buscar_name."
			ORDER BY
				a.tms DESC
			LIMIT 3";
            $result=$db->query($sql);
            if ($result)
            {
                $num = $db->num_rows($result);
                if ($num)
                {
                	$i=0;
                    while ($i < $num)
                    {
                    	$obj = $db->fetch_object($result);
                    	print '
                    		<tr class="impair">
								<td class="nowrap">';

								$paciente= new Pacientes($db);
								$paciente->fetch($obj->rowid);
								print $paciente->getNomUrl(0,'', 0, 24, '',1);

							print '		
								</td>
								<td align="center">
									<a href="">Paciente
									</a>
								</td>
								<td align="right">'.$obj->datec.'</td><td align="right" class="nowrap">';
								if ($obj->statut==0) {
									print '<img src="../theme/eldy/img/statut0.png" border="0" alt="" title="No Activo">';
								}
								if ($obj->statut==1) {
									print '<img src="../theme/eldy/img/statut4.png" border="0" alt="" title="Activo">';
								}
								print '
								</td>
							</tr>
		
                    	';
                    	$i++;
                    }
                }
            }

	print '
	  	  </tbody>
	  </table>
	  <table class="noborder" width="100%">
		  <tbody>
		  	<tr class="liste_titre">
		  		<th >'.$langs->trans("Code9").'</th>
		  		<th align="center">'.$langs->trans("Code10").'</th>
		  		<th></th>
		  		<th align="right">'.$langs->trans("Code8").'</th>
		  	</tr>
			';

		  	$sql = "SELECT
				*
			FROM
				llx_consultas AS a
			WHERE
		    a.entity=".$conf->entity."
			".$buscar_ref."
			ORDER BY
				a.date_creation DESC
			LIMIT 15";
            $result=$db->query($sql);
            if ($result)
            {
                $num = $db->num_rows($result);
                if ($num)
                {
                	$i=0;
                    while ($i < $num)
                    {
                    	$obj = $db->fetch_object($result);
                    	print '
                    		<tr class="impair">
								<td class="nowrap">';

								$paciente= new Pacientes($db);
								$paciente->fetch($obj->fk_user_pacientes);
								print $paciente->getNomUrl(0,'', 0, 24, '',1);

							print '		
								</td>
								<td align="center">';

								$consultas= new Consultas($db);
								$consultas->fetch($obj->rowid);
								print $consultas->getNomUrl(0);

							print '	
								</td>
								<td align="right">';
									print $obj->date_consultation;
							print '	
								</td>';
								if ($obj->statut==0) {
						            print '
						            <td align="right" ><img src="../theme/eldy/img/statut0.png" border="0" alt="" title="Borrador (a validar)"></td>
						            ';
						        }
						        if ($obj->statut==1) {
						            print '
						            <td align="right" ><img src="../theme/eldy/img/statut4.png" border="0" alt="" title="Activado" ></td>
						            ';
						        }
						        if ($obj->statut==2) {
						            print '
						            <td align="right" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Facturada" ></td>
						            ';
						        }
						        if ($obj->statut==3) {
						            print '
						            <td align="right" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Activado" ></td>
						            ';
						        }
								print '
								
							</tr>';
                    	$i++;
                    }
                }
            }

	print '
	  	  </tbody>
	  </table>
	<!-- End last thirdparties modified -->
			    </div>
		   </div>';


	/*
		   <div class="fichetwothirdright">
		   		<div class="ficheaddleft">';
	print "\n<!-- last thirdparties modified -->\n";
			print '<table class="noborder" width="100%">';
				print '<tr class="liste_titre">
							<th colspan="2">Los 15 últimos pacientes modificados</th>';
    				 print '<th>&nbsp;</th>';
    				 print '<th>&nbsp;</th>';
    				 print '<th align="center">Estado</th>';
    			print '</tr>'."\n";
   			    print "<tr ".$bc[$var].">";
   			         print '<td align="left" class="nowrap">';
		            	 print "<a href=\"ligne.php?rowid=".$objp->rowid.'">'.img_object($langs->trans("asdasd").': '.$objp->rowid, 'payment', 'class="classfortooltip"')."</a> &nbsp; Luiz Hernandez";
		             print '</td>';
      				 print '<td align="center"><a style="cursor:pointer;">Paciente</a></td>';
    			     print '<td align="center">04/29/2016</td>';
    			     print '<td align="center"><img src="/dolibarr-3.9.0/htdocs/theme/eldy/img/statut4.png" border="0" alt="" title="Activo"></td>';
    			print "</tr>\n";
    		print "</table>\n";
	print "<!-- End last thirdparties modified -->\n";
	print '		</div>
			</div>';*/

	//print '</td></tr></table>';
	/*
	 * Last third parties modified
	 */
	/*$max=15;
	$sql = 'SELECT * FROM llx_bank_account as a';
	

	//print $sql;
	$result = $db->query($sql);

	echo "<pre>";
		print_r($result);
	echo "</pre>";
	
	/*
	while ($fila = $result->fetch_assoc()) {
        printf ("%s (%s)\n", $fila["rowid"], $fila["CountryCode"]);
    }*/





	/*if ($result)
	{
	    $num = $db->num_rows($result);

	    $i = 0;
	    //print_r( $result);

	    if ($num > 0)
	    {
	    	
	        $transRecordedType = $langs->trans("LastModifiedThirdParties",$max);

	        print "\n<!-- last thirdparties modified -->\n";
	        print '<table class="noborder" width="100%">';

	        print '<tr class="liste_titre"><th colspan="2">'.$transRecordedType.'</th>';
	        print '<th>&nbsp;</th>';
	        print '<th align="right">'.$langs->trans('Status').'</th>';
	        print '</tr>'."\n";

	        $var=True;

	        while ($i < $num)
	        {
	            $objp = $db->fetch_object($result);

	            $var=!$var;
	            print "<tr ".$bc[$var].">";
	            // Name
	            print '<td class="nowrap">';
	            $thirdparty_static->id=$objp->rowid;
	            $thirdparty_static->name=$objp->name;
	            $thirdparty_static->client=$objp->client;
	            $thirdparty_static->fournisseur=$objp->fournisseur;
	            $thirdparty_static->logo = $objp->logo;
	            $thirdparty_static->datem=$db->jdate($objp->datem);
	            $thirdparty_static->status=$objp->status;
	            $thirdparty_static->code_client = $objp->code_client;
	            $thirdparty_static->code_fournisseur = $objp->code_fournisseur;
	            $thirdparty_static->canvas=$objp->canvas;
	            print $thirdparty_static->getNomUrl(1);
	            print "</td>\n";
	            // Type
	            print '<td align="center">';
	            if ($thirdparty_static->client==1 || $thirdparty_static->client==3)
	            {
	            	$thirdparty_static->name=$langs->trans("Customer");
	            	print $thirdparty_static->getNomUrl(0,'customer',0,1);
	            }
	            if ($thirdparty_static->client == 3 && empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) print " / ";
	            if (($thirdparty_static->client==2 || $thirdparty_static->client==3) && empty($conf->global->SOCIETE_DISABLE_PROSPECTS))
	            {
	            	$thirdparty_static->name=$langs->trans("Prospect");
	            	print $thirdparty_static->getNomUrl(0,'prospect',0,1);
	            }
	            if (! empty($conf->fournisseur->enabled) && $thirdparty_static->fournisseur)
	            {
	                if ($thirdparty_static->client) print " / ";
	            	$thirdparty_static->name=$langs->trans("Supplier");
	            	print $thirdparty_static->getNomUrl(0,'supplier',0,1);
	            }
	            print '</td>';
	            // Last modified date
	            print '<td align="right">';
	            print dol_print_date($thirdparty_static->datem,'day');
	            print "</td>";
	            print '<td align="right" class="nowrap">';
	            print $thirdparty_static->getLibStatut(3);
	            print "</td>";
	            print "</tr>\n";
	            $i++;
	        }

	        $db->free();

	        print "</table>\n";
	        print "<!-- End last thirdparties modified -->\n";
	    }
	}
	else
	{
	    dol_print_error($db);
	}

	//print '</td></tr></table>';*/
	

	llxFooter();


}


// End of page
llxFooter();
$db->close();
