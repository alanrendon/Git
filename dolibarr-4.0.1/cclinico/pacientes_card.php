<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/cclinico/class/pacientes.class.php');
dol_include_once('/cclinico/class/consultas.class.php');
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$socid		= GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;

$object = new Pacientes($db);
$consultas = new Consultas($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
$now=dol_now();
/*
 *	Actions
 */
    // Cancel
    if (GETPOST("cancel") && ! empty($backtopage))
    {
        header("Location: ".$backtopage);
        exit;
    }

    // Confirmation desactivation
    if ($action == 'disable')
    {
    	$object->fetch($id);
    	if ($object->setstatus(0)<0)
    	{
    	    setEventMessages($object->error, $object->errors, 'errors');
    	}
    	else
    	{
        	header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
        	exit;
    	}
    }

    // Confirmation activation
    if ($action == 'enable')
    {
    	$object->fetch($id);
        	if ($object->setstatus(1)<0)
    	{
    	    setEventMessages($object->error, $object->errors, 'errors');
    	}
    	else
    	{
        	header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
        	exit;
    	}
    }

    // Add contact
    if ($action == 'add')
    {
        $db->begin();

        if ($canvas) $object->canvas=$canvas;

        $object->socid			= GETPOST("socid",'int');
        $object->lastname		= GETPOST("lastname");
        $object->firstname		= GETPOST("firstname");
        $object->civility_id	= GETPOST("civility_id",'alpha');
        $object->poste			= GETPOST("poste");
        $object->address		= GETPOST("address");
        $object->zip			= GETPOST("zipcode");
        $object->town			= GETPOST("town");
        $object->fk_pays		= GETPOST("country_id",'int');
        $object->fk_departement = GETPOST("state_id",'int');
        $object->skype			= GETPOST("skype");
        $object->email			= GETPOST("email",'alpha');
        $object->phone   		= GETPOST("phone_pro");
        $object->phone_perso	= GETPOST("phone_perso");
        $object->phone_mobile	= GETPOST("phone_mobile");
        $object->fax			= GETPOST("fax");
        $object->jabberid		= GETPOST("jabberid",'alpha');
		$object->no_email		= GETPOST("no_email",'int');
        $object->priv			= GETPOST("priv",'int');
        $object->note_public	= GETPOST("note_public");
        $object->note_private	= GETPOST("note_private");
        $object->sexo			= GETPOST("sexo");
        $object->edad			= GETPOST("edad");
        $object->estatura		= GETPOST("estatura");
        $object->fk_user		= GETPOST("fk_user");

        $object->statut			= 1; //Defult status to Actif

        // Note: Correct date should be completed with location to have exact GM time of birth.
        $object->birthday = dol_mktime(0,0,0,GETPOST("birthdaymonth",'int'),GETPOST("birthdayday",'int'),GETPOST("birthdayyear",'int'));
        $object->birthday_alert = GETPOST("birthday_alert",'alpha');

        // Fill array 'array_options' with data from add form

		$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
        
		if ($ret < 0)
		{
			$error++;
			$action = 'create';
		}

        if (! GETPOST("lastname"))
        {
            $error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Lastname").' / '.$langs->transnoentities("Label"));
            $action = 'create';
        }
        if (! GETPOST("firstname"))
        {
            $error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Firstname"));
            $action = 'create';
        }

        if (! $error)
        {

            $id =  $object->create($user);
            
            if ($id <= 0)
            {
                $error++; $errors=array_merge($errors,($object->error?array($object->error):$object->errors));
                $action = 'create';
			}
        }

        if (! $error && $id > 0)
        {
            
            $db->commit();
            if (! empty($backtopage)) $url=$backtopage;
            else $url='pacientes_card.php?id='.$id;
            header("Location: ".$url);
            exit;
        }
        else
        {
            $db->rollback();
        }
    }

    if ($action == 'confirm_delete' && $confirm == 'yes' )
    {
        $result=$object->fetch($id);

        $object->old_lastname      = GETPOST("old_lastname");
        $object->old_firstname = GETPOST("old_firstname");

        $result = $object->delete();
        if ($result > 0)
        {
        	if ($backtopage)
        	{
        		header("Location: ".$backtopage);
        		exit;
        	}
        	else
        	{
        		header("Location: ".DOL_URL_ROOT.'/cclinico/pacientes_list.php');
        		exit;
        	}
        }
        else
        {
            setEventMessages($object->error,$object->errors,'errors');
        }
    }

    if ($action == 'update' && ! $_POST["cancel"] )
    {
        if (empty($_POST["lastname"]))
        {
            $error++; $errors=array($langs->trans("ErrorFieldRequired",$langs->transnoentities("Name").' / '.$langs->transnoentities("Label")));
            $action = 'edit';
        }
        if (! GETPOST("firstname"))
        {
            $error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Firstname"));
            $action = 'edit';
        }


        if (! $error)
        {
        	$contactid=GETPOST("contactid",'int');

            $object->fetch($contactid);

			$object->oldcopy = clone $object;

            $object->old_lastname	= GETPOST("old_lastname");
            $object->old_firstname	= GETPOST("old_firstname");

            $object->socid			= GETPOST("socid",'int');
            $object->lastname		= GETPOST("lastname");
            $object->firstname		= GETPOST("firstname");
            $object->civility_id	= GETPOST("civility_id",'alpha');
            $object->poste			= GETPOST("poste");

            $object->address		= GETPOST("address");
            $object->zip			= GETPOST("zipcode");
            $object->town			= GETPOST("town");
            $object->state_id   	= GETPOST("state_id",'int');
            $object->fk_departement	= GETPOST("state_id",'int');	// For backward compatibility
            $object->fk_pays		= GETPOST("country_id",'int');

            $object->email			= GETPOST("email",'alpha');
            $object->skype			= GETPOST("skype",'alpha');
            $object->phone  		= GETPOST("phone_pro");
            $object->phone_perso	= GETPOST("phone_perso");
            $object->phone_mobile	= GETPOST("phone_mobile");
            $object->fax			= GETPOST("fax");
            $object->jabberid		= GETPOST("jabberid",'alpha');
			$object->no_email		= GETPOST("no_email",'int');
            $object->edad           = GETPOST("edad");
            $object->estatura       = GETPOST("estatura");
            $object->sexo           = GETPOST("sexo");
            $object->fk_user        = GETPOST("fk_user");
            $object->birthday       = dol_mktime(0,0,0,GETPOST("birthdaymonth"),GETPOST("birthdayday"),GETPOST("birthdayyear"));



            // Fill array 'array_options' with data from add form
			// Fill array 'array_options' with data from add form
            $ret = $extrafields->setOptionalsFromPost($extralabels,$object);

            if ($ret < 0)
            {
                $error++;
                $action = 'create';
            }

            $result = $object->update($contactid, $user);
			if ($result > 0) {
				$result = $object->update_perso($contactid, $user);
                $action = 'view';

            }
            else
            {
                setEventMessages($object->error, $object->errors, 'errors');
                $action = 'edit';
            }
        }
    }



llxHeader('', 'Crear Pacientes', '');

$form = new Form($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

if ($socid > 0)
{
    $objsoc = new Societe($db);
    $objsoc->fetch($socid);
}   
if ($action == 'delete')
{
    print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$id.($backtopage?'&backtopage='.$backtopage:''),$langs->trans("DeleteContact"),$langs->trans("ConfirmDeleteContact"),"confirm_delete",'',0,1);
}


$head=array();
if ($id > 0)
{
    // Si edition contact deja existant
    $object = new Pacientes($db);
    $res=$object->fetch($id, $user);
    if ($res < 0) { dol_print_error($db,$object->error); exit; }
    $res=$object->fetch_optionals($object->id,$extralabels);

    // Show tabs
    $head = pacientes_prepare_head($object);

    $title = "Pacientes";
}


if ($action == 'create')
{
    $object->canvas=$canvas;

    $object->state_id = $_POST["state_id"];

    // We set country_id, country_code and label for the selected country
    $object->country_id=$_POST["country_id"]?$_POST["country_id"]:(empty($objsoc->country_id)?$mysoc->country_id:$objsoc->country_id);
    if ($object->country_id)
    {
    	$tmparray=getCountry($object->country_id,'all');
        $object->country_code = $tmparray['code'];
        $object->country      = $tmparray['label'];
    }

    $linkback='';
    print load_fiche_titre("Crear Paciente",$linkback,'title_project.png');

    // Affiche les erreurs
    dol_htmloutput_errors(is_numeric($error)?'':$error,$errors);

    if ($conf->use_javascript_ajax)
    {
		print "\n".'<script type="text/javascript" language="javascript">'."\n";
		print 'jQuery(document).ready(function () {
					jQuery("#selectcountry_id").change(function() {
						document.formsoc.action.value="create";
						document.formsoc.submit();
					});
					$("#edadButton").click(function() {
					    
						var starts = $("#birthday").val();
						var match = /(\d+)\/(\d+)\/(\d+)/.exec(starts)
						var birth = new Date(match[3], match[2], match[1]);
					    var curr  = new Date();
					    var diff = curr.getTime() - birth.getTime();
					   	var edad = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
					   	if (edad > 0) {
					   		$("#edad").val(edad);
					   	}else{
					   		$("#edad").val("");
					   	}
					    
					});
					$("#copyaddressfromsoc").click(function() {
						$(\'textarea[name="address"]\').val("'.dol_escape_js($objsoc->address).'");
						$(\'input[name="zipcode"]\').val("'.dol_escape_js($objsoc->zip).'");
						$(\'input[name="town"]\').val("'.dol_escape_js($objsoc->town).'");
						console.log("Set state_id to '.dol_escape_js($objsoc->state_id).'");
						$(\'select[name="state_id"]\').val("'.dol_escape_js($objsoc->state_id).'").trigger("change");
						/* set country at end because it will trigger page refresh */
						console.log("Set country id to '.dol_escape_js($objsoc->country_id).'");
						$(\'select[name="country_id"]\').val("'.dol_escape_js($objsoc->country_id).'").trigger("change");   /* trigger required to update select2 components */
                    });
				})'."\n";
		print '</script>'."\n";
    }

    print '<form method="post" name="formsoc" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    dol_fiche_head($head, 'card', '', 0, '');

    print '<table class="border" width="100%">';


    // Name
    print '<tr><td width="20%" class="fieldrequired"><label for="lastname">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</label></td>';
    print '<td width="30%"><input name="lastname" id="lastname" type="text" size="30" maxlength="80" value="'.dol_escape_htmltag(GETPOST("lastname")?GETPOST("lastname"):$object->lastname).'" autofocus="autofocus"></td>';
    print '<td width="20%"><label for="firstname">'.$langs->trans("Firstname").'</label></td>';
    print '<td width="30%"><input name="firstname" id="firstname"type="text" size="30" maxlength="80" value="'.dol_escape_htmltag(GETPOST("firstname")?GETPOST("firstname"):$object->firstname).'"></td></tr>';

    // Company
    if (empty($conf->global->SOCIETE_DISABLE_CONTACTS))
    {
        if ($socid > 0)
        {
            print '<tr><td><label for="socid">'.$langs->trans("ThirdParty").'</label></td>';
            print '<td colspan="3" class="maxwidthonsmartphone">';
            print $objsoc->getNomUrl(1);
            print '</td>';
            print '<input type="hidden" name="socid" id="socid" value="'.$objsoc->id.'">';
            print '</td></tr>';
        }
        else {
            print '<tr><td><label for="socid">'.$langs->trans("ThirdParty").'</label></td><td colspan="3" class="maxwidthonsmartphone">';
            print $form->select_company($socid,'socid','',1);
            print '</td></tr>';
        }
    }


    // Civility
    print '<tr><td width="15%"><label for="civility_id">'.$langs->trans("UserTitle").'</label></td><td colspan="3">';
    print $formcompany->select_civility(GETPOST("civility_id",'alpha')?GETPOST("civility_id",'alpha'):$object->civility_id);
    print '</td></tr>';

    print '<tr><td><label for="title">'.$langs->trans("PostOrFunction").'</label></td>';
    print '<td colspan="3"><input name="poste" id="title" type="text" size="50" maxlength="80" value="'.dol_escape_htmltag(GETPOST("poste",'alpha')?GETPOST("poste",'alpha'):$object->poste).'"></td>';

    $colspan=3;
    if ($conf->use_javascript_ajax && $socid > 0) $colspan=2;

    // Address
    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->address)) == 0) $object->address = $objsoc->address;	// Predefined with third party
    print '<tr><td><label for="address">'.$langs->trans("Address").'</label></td>';
    print '<td colspan="'.$colspan.'"><textarea class="flat" name="address" id="address" cols="70">'.(GETPOST("address",'alpha')?GETPOST("address",'alpha'):$object->address).'</textarea></td>';

    if ($conf->use_javascript_ajax && $socid > 0)
    {
        $rowspan=3;
		if (empty($conf->global->SOCIETE_DISABLE_STATE)) $rowspan++;

        print '<td valign="middle" align="center" rowspan="'.$rowspan.'">';
        print '<a href="#" id="copyaddressfromsoc">'.$langs->trans('CopyAddressFromSoc').'</a>';
        print '</td>';
    }
    print '</tr>';

    // Zip / Town
    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->zip)) == 0) $object->zip = $objsoc->zip;			// Predefined with third party
    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->town)) == 0) $object->town = $objsoc->town;	// Predefined with third party
    print '<tr><td><label for="zipcode">'.$langs->trans("Zip").'</label> / <label for="town">'.$langs->trans("Town").'</label></td><td colspan="'.$colspan.'" class="maxwidthonsmartphone">';
    print $formcompany->select_ziptown((GETPOST("zipcode")?GETPOST("zipcode"):$object->zip),'zipcode',array('town','selectcountry_id','state_id'),6).'&nbsp;';
    print $formcompany->select_ziptown((GETPOST("town")?GETPOST("town"):$object->town),'town',array('zipcode','selectcountry_id','state_id'));
    print '</td></tr>';

    // Country
    print '<tr><td><label for="selectcountry_id">'.$langs->trans("Country").'</label></td><td colspan="'.$colspan.'" class="maxwidthonsmartphone">';
    print $form->select_country((GETPOST("country_id",'alpha')?GETPOST("country_id",'alpha'):$object->country_id),'country_id');
    if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
    print '</td></tr>';

    // State
    if (empty($conf->global->SOCIETE_DISABLE_STATE))
    {
        print '<tr><td><label for="state_id">'.$langs->trans('State').'</label></td><td colspan="'.$colspan.'" class="maxwidthonsmartphone">';
        if ($object->country_id)
        {
            print $formcompany->select_state(GETPOST("state_id",'alpha')?GETPOST("state_id",'alpha'):$object->state_id,$object->country_code,'state_id');
        }
        else
      {
            print $countrynotdefined;
        }
        print '</td></tr>';
    }

    // Phone / Fax
    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->phone_pro)) == 0) $object->phone_pro = $objsoc->phone;	// Predefined with third party
    print '<tr><td><label for="phone_pro">'.$langs->trans("PhonePro").'</label></td>';
    print '<td><input name="phone_pro" id="phone_pro" type="text" size="18" maxlength="80" value="'.dol_escape_htmltag(GETPOST("phone_pro")?GETPOST("phone_pro"):$object->phone_pro).'"></td>';
    print '<td><label for="phone_perso">'.$langs->trans("PhonePerso").'</label></td>';
    print '<td><input name="phone_perso" id="phone_perso" type="text" size="18" maxlength="80" value="'.dol_escape_htmltag(GETPOST("phone_perso")?GETPOST("phone_perso"):$object->phone_perso).'"></td></tr>';

    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->fax)) == 0) $object->fax = $objsoc->fax;	// Predefined with third party
    print '<tr><td><label for="phone_mobile">'.$langs->trans("PhoneMobile").'</label></td>';
    print '<td><input name="phone_mobile" id="phone_mobile" type="text" size="18" maxlength="80" value="'.dol_escape_htmltag(GETPOST("phone_mobile")?GETPOST("phone_mobile"):$object->phone_mobile).'"></td>';
    print '<td><label for="fax">'.$langs->trans("Fax").'</label></td>';
    print '<td><input name="fax" id="fax" type="text" size="18" maxlength="80" value="'.dol_escape_htmltag(GETPOST("fax",'alpha')?GETPOST("fax",'alpha'):$object->fax).'"></td></tr>';

    // EMail
    if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->email)) == 0) $object->email = $objsoc->email;	// Predefined with third party
    print '<tr><td><label for="email">'.$langs->trans("Email").'</label></td>';
    print '<td><input name="email" id="email" type="text" size="50" maxlength="80" value="'.(GETPOST("email",'alpha')?GETPOST("email",'alpha'):$object->email).'"></td>';
    if (! empty($conf->mailing->enabled))
    {
    	print '<td><label for="no_email">'.$langs->trans("No_Email").'</label></td>';
        print '<td>'.$form->selectyesno('no_email',(GETPOST("no_email",'alpha')?GETPOST("no_email",'alpha'):$object->no_email), 1).'</td>';
    }
    else{
  		print '<td colspan="2">&nbsp;</td>';
    }
    print '</tr>';

    // Instant message and no email
    print '<tr><td><label for="jabberid">'.$langs->trans("IM").'</label></td>';
    print '<td colspan="3"><input name="jabberid" id="jabberid" type="text" size="50" maxlength="80" value="'.(GETPOST("jabberid",'alpha')?GETPOST("jabberid",'alpha'):$object->jabberid).'"></td></tr>';

    // Skype
    if (! empty($conf->skype->enabled))
    {
        print '<tr><td><label for="skype">'.$langs->trans("Skype").'</label></td>';
        print '<td colspan="3"><input name="skype" id="skype" type="text" size="50" maxlength="80" value="'.(GETPOST("skype",'alpha')?GETPOST("skype",'alpha'):$object->skype).'"></td></tr>';
    }

    // Visibility
    print '<tr><td><label for="priv">'.$langs->trans("ContactVisibility").'</label></td><td colspan="3">';
    $selectarray=array('0'=>$langs->trans("ContactPublic"),'1'=>$langs->trans("ContactPrivate"));
    print $form->selectarray('priv',$selectarray,(GETPOST("priv",'alpha')?GETPOST("priv",'alpha'):$object->priv),0);
    print '</td></tr>';

    
	


    // Other attributes
    $parameters=array('colspan' => ' colspan="3"');
    $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

    if (empty($reshook) && ! empty($extrafields->attribute_label))
    {
    	print $object->showOptionals($extrafields,'edit');
    }


    print "</table><br>";


    // Add personnal information
    print load_fiche_titre('<div class="comboperso">'.$langs->trans("PersonalInformations").'</div>','','');

    print '<table class="border" width="100%">';

    // Date To Birth
    print '<tr><td width="20%"><label for="birthday">'.$langs->trans("DateToBirth").'</label></td><td width="30%">';
    $form=new Form($db);
    if ($object->birthday)
    {
        print $form->select_date($object->birthday,'birthday',0,0,0,"perso", 1, 0, 1);
    }
    else
    {
        print $form->select_date('','birthday',0,0,1,"perso", 1, 0, 1);
    }
    print '</td>';
    print '<td ><label for="edad">Edad</label></td>';
    print '<td >
    <input name="edad" id="edad" type="text" size="5" maxlength="80" value="'.GETPOST("edad",'int').'">
    </td>';
    print '<tr>
    <td>
    	<label for="estatura">'.$langs->trans("Code30").'</label>
    </td>
    <td>
    	<input name="estatura" id="estatura" type="text" size="9" maxlength="80" value="'.GETPOST("estatura").'">
    </td>
    <td><label for="sexo">'.$langs->trans("Code31").'</label></td>';
    print '<td colspan="3">';
    	print '<select class="flat" name="sexo" id="sexo">';
    		//if (GETPOST("sexo",'int')) {
    			if (GETPOST("sexo",'int') == 0 or $object->sexo == 0) {
    				print '<option value="0" selected>Masculino</option>';
    			}else{
    				print '<option value="0">Masculino</option>';
    			}
    			if (GETPOST("sexo",'int') == 1 or $object->sexo == 1) {
    				print '<option value="1" selected>Femenino</option>';
    			}else{
    				print '<option value="1">Femenino</option>';
    			}
    		//}
    	print '</select>';
    // Hierarchy
    print '<tr><td>Médico Asignado</td>';
    print '<td>';

    //$db->begin();
	$resql=$db->query("
		SELECT
			a.rowid as id
		FROM
			llx_user AS a
		LEFT JOIN llx_user_extrafields as b ON a.rowid = b.fk_object
		WHERE
			b.rowid IS NULL AND a.statut=1
	");
	$arra = array();
	if ($resql)
	{
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num){
                $obj = $db->fetch_object($resql);
                if ($obj){
                    array_push ( $arra,$obj->id);
                }
                $i++;
            }
        }
	}

    print $consultas->select_dolusers($object->fk_user, 'fk_user', 1, $arra, 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '</td><td colspan="3"></td>';
    print "</tr>\n";

    
   



    print "</table>";

    print dol_fiche_end();

    print '<div class="center">';
    print '<input type="submit" class="button" name="add" value="'.$langs->trans("Add").'">';
    if (! empty($backtopage))
    {
        print ' &nbsp; &nbsp; ';
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    }
    print '</div>';

    print "</form>";
}
elseif ($action == 'edit' && ! empty($id))
{

    if (isset($_POST["country_id"]) || $object->country_id)
    {
        $tmparray=getCountry($object->country_id,'all');
        $object->country_code =	$tmparray['code'];
        $object->country      =	$tmparray['label'];
    }

	$objsoc = new Societe($db);
	$objsoc->fetch($object->socid);

    // Affiche les erreurs
    dol_htmloutput_errors($error,$errors);

    if ($conf->use_javascript_ajax)
    {
		print "\n".'<script type="text/javascript" language="javascript">'."\n";
		print 'jQuery(document).ready(function () {
					jQuery("#selectcountry_id").change(function() {
						document.formsoc.action.value="edit";
						document.formsoc.submit();
					});

					$("#copyaddressfromsoc").click(function() {
						$(\'textarea[name="address"]\').val("'.dol_escape_js($objsoc->address).'");
						$(\'input[name="zipcode"]\').val("'.dol_escape_js($objsoc->zip).'");
						$(\'input[name="town"]\').val("'.dol_escape_js($objsoc->town).'");
						console.log("Set state_id to '.dol_escape_js($objsoc->state_id).'");
						$(\'select[name="state_id"]\').val("'.dol_escape_js($objsoc->state_id).'").trigger("change");
						/* set country at end because it will trigger page refresh */
						console.log("Set country id to '.dol_escape_js($objsoc->country_id).'");
						$(\'select[name="country_id"]\').val("'.dol_escape_js($objsoc->country_id).'").trigger("change");   /* trigger required to update select2 components */
    				});
				})'."\n";
		print '</script>'."\n";
    }

    print '<form enctype="multipart/form-data" method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" name="formsoc">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="contactid" value="'.$object->id.'">';
    print '<input type="hidden" name="old_lastname" value="'.$object->lastname.'">';
    print '<input type="hidden" name="old_firstname" value="'.$object->firstname.'">';
    if (! empty($backtopage)) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    dol_fiche_head($head, 'card', $title, 0, 'contact');

    print '<table class="border" width="100%">';

    // Ref/ID
    if (! empty($conf->global->MAIN_SHOW_TECHNICAL_ID))
   	{
        print '<tr><td>'.$langs->trans("ID").'</td><td colspan="3">';
        print $object->ref;
        print '</td></tr>';
   	}
   	
    // Lastname
    print '<tr><td width="20%" class="fieldrequired"><label for="lastname">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</label></td>';
    print '<td width="30%"><input name="lastname" id="lastname" type="text" size="20" maxlength="80" value="'.(isset($_POST["lastname"])?$_POST["lastname"]:$object->lastname).'" autofocus="autofocus"></td>';
    print '<td width="20%"><label for="firstname">'.$langs->trans("Firstname").'</label></td>';
    print '<td width="30%"><input name="firstname" id="firstname" type="text" size="20" maxlength="80" value="'.(isset($_POST["firstname"])?$_POST["firstname"]:$object->firstname).'"></td></tr>';

    // Company
    if (empty($conf->global->SOCIETE_DISABLE_CONTACTS))
    {
        print '<tr><td><label for="socid">'.$langs->trans("ThirdParty").'</label></td>';
        print '<td colspan="3" class="maxwidthonsmartphone">';
        print $form->select_company(GETPOST('socid','int')?GETPOST('socid','int'):($object->socid?$object->socid:-1),'socid','',1);
        print '</td>';
        print '</tr>';
    }

    // Civility
    print '<tr><td><label for="civility_id">'.$langs->trans("UserTitle").'</label></td><td colspan="3">';
    print $formcompany->select_civility(isset($_POST["civility_id"])?$_POST["civility_id"]:$object->civility_id);
    print '</td></tr>';

    print '<tr><td><label for="title">'.$langs->trans("PostOrFunction").'</label></td>';
    print '<td colspan="3"><input name="poste" id="title" type="text" size="50" maxlength="80" value="'.(isset($_POST["poste"])?$_POST["poste"]:$object->poste).'"></td></tr>';

    // Address
    print '<tr><td><label for="address">'.$langs->trans("Address").'</label></td>';
    print '<td colspan="3"><textarea class="flat" name="address" id="address" cols="70">'.(isset($_POST["address"])?$_POST["address"]:$object->address).'</textarea></td>';

    $rowspan=3;
	if (empty($conf->global->SOCIETE_DISABLE_STATE)) $rowspan++;

    print '<tr><td><label for="zipcode">'.$langs->trans("Zip").'</label> / <label for="town">'.$langs->trans("Town").'</label></td><td colspan="3" class="maxwidthonsmartphone">';
    print $formcompany->select_ziptown((isset($_POST["zipcode"])?$_POST["zipcode"]:$object->zip),'zipcode',array('town','selectcountry_id','state_id'),6).'&nbsp;';
    print $formcompany->select_ziptown((isset($_POST["town"])?$_POST["town"]:$object->town),'town',array('zipcode','selectcountry_id','state_id'));
    print '</td></tr>';

    // Country
    print '<tr><td><label for="selectcountry_id">'.$langs->trans("Country").'</label></td><td colspan="3" class="maxwidthonsmartphone">';
    print $form->select_country(isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id,'country_id');
    if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
    print '</td></tr>';

    // State
    if (empty($conf->global->SOCIETE_DISABLE_STATE))
    {
        print '<tr><td><label for="state_id">'.$langs->trans('State').'</label></td><td colspan="3" class="maxwidthonsmartphone">';
        print $formcompany->select_state($object->state_id,isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id,'state_id');
        print '</td></tr>';
    }

    // Phone
    print '<tr><td><label for="phone_pro">'.$langs->trans("PhonePro").'</label></td>';
    print '<td><input name="phone_pro" id="phone_pro" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone_pro"])?$_POST["phone_pro"]:$object->phone_pro).'"></td>';
    print '<td><label for="phone_perso">'.$langs->trans("PhonePerso").'</label></td>';
    print '<td><input name="phone_perso" id="phone_perso" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone_perso"])?$_POST["phone_perso"]:$object->phone_perso).'"></td></tr>';

    print '<tr><td><label for="phone_mobile">'.$langs->trans("PhoneMobile").'</label></td>';
    print '<td><input name="phone_mobile" id="phone_mobile" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone_mobile"])?$_POST["phone_mobile"]:$object->phone_mobile).'"></td>';
    print '<td><label for="fax">'.$langs->trans("Fax").'</label></td>';
    print '<td><input name="fax" id="fax" type="text" size="18" maxlength="80" value="'.(isset($_POST["fax"])?$_POST["fax"]:$object->fax).'"></td></tr>';

    // EMail
    print '<tr><td><label for="email">'.$langs->trans("EMail").'</label></td>';
    print '<td><input name="email" id="email" type="text" size="40" maxlength="80" value="'.(isset($_POST["email"])?$_POST["email"]:$object->email).'"></td>';
    if (! empty($conf->mailing->enabled))
    {
        $langs->load("mails");
        print '<td class="nowrap">'.$langs->trans("NbOfEMailingsSend").'</td>';
        print '<td>'.$object->getNbOfEMailings().'</td>';
    }
    else
	{
		print '<td colspan="2">&nbsp;</td>';
    }
    print '</tr>';

    // Jabberid
    print '<tr><td><label for="jabberid">'.$langs->trans("IM").'</label></td>';
    print '<td><input name="jabberid" id="jabberid" type="text" size="40" maxlength="80" value="'.(isset($_POST["jabberid"])?$_POST["jabberid"]:$object->jabberid).'"></td>';
    if (! empty($conf->mailing->enabled))
    {
    	print '<td><label for="no_email">'.$langs->trans("No_Email").'</label></td>';
        print '<td>'.$form->selectyesno('no_email',(isset($_POST["no_email"])?$_POST["no_email"]:$object->no_email), 1).'</td>';
    }
    else
	{
		print '<td colspan="2">&nbsp;</td>';
	}
    print '</tr>';

    // Skype
    if (! empty($conf->skype->enabled))
    {
        print '<tr><td><label for="skype">'.$langs->trans("Skype").'</label></td>';
        print '<td><input name="skype" id="skype" type="text" size="40" maxlength="80" value="'.(isset($_POST["skype"])?$_POST["skype"]:$object->skype).'"></td></tr>';
    }

    // Visibility
    print '<tr><td><label for="priv">'.$langs->trans("ContactVisibility").'</label></td><td colspan="3">';
    $selectarray=array('0'=>$langs->trans("ContactPublic"),'1'=>$langs->trans("ContactPrivate"));
    print $form->selectarray('priv',$selectarray,$object->priv,0);
    print '</td></tr>';

    print '<tr><td>'.$langs->trans("Status").'</td>';
    print '<td>';
    print $object->getLibStatut(4);
    print '</td></tr>';


    // Other attributes
    $parameters=array('colspan' => ' colspan="3"');
    $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    if (empty($reshook) && ! empty($extrafields->attribute_label))
    {
    	print $object->showOptionals($extrafields,'edit');
    }
    print '
    <tbody>
        <tr>
            <td class="nobordernopadding" valign="middle" colspan=4>
                <div class="titre"><div class="comboperso">Información personal</div>
                </div>
            </td>
        </tr>
    </tbody>';

    // Date To Birth
    print '<tr><td>'.$langs->trans("DateToBirth").'</td><td>';
    $form=new Form($db);
    print $form->select_date($object->birthday,'birthday',0,0,1,"perso", 1,0,1);
    print '</td>';

    print '<td  width="1%">Edad Ingresada</td>';
    print '
    <td >
        <input name="edad" id="edad" type="text" size="5" maxlength="80" value="'.$object->edad.'">
    </td>';
    print '</tr>';
    print '<tr>
    <td>'.$langs->trans("Code30").'</td>
    <td >
        <input name="estatura" id="estatura" type="text" size="5" maxlength="80" value="'.$object->estatura.'">';
    print '</td><td>'.$langs->trans("Code31").'</td>';
    print '<td>';
        print '<select class="flat" name="sexo" id="sexo">';
            if ($object->sexo == 0) {
                print '<option value="0" selected>Masculino</option>';
            }else{
                print '<option value="0">Masculino</option>';
            }
            if ($object->sexo == 1) {
                print '<option value="1" selected>Femenino</option>';
            }else{
                print '<option value="1">Femenino</option>';
            }       
        print '</select>';
    print '</td>';

    print '</tr>';
    //medico fk_user
    print '<tr><td>'.$langs->trans("Code32").'</td>';
    print '<td>';

    //$db->begin();
    $resql=$db->query("
        SELECT
            a.rowid as id
        FROM
            llx_user AS a
        LEFT JOIN llx_user_extrafields as b ON a.rowid = b.fk_object
        WHERE
            b.rowid IS NULL AND a.statut=1
    ");
    $arra = array();
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num){
                $obj = $db->fetch_object($resql);
                if ($obj){
                    array_push ( $arra,$obj->id);
                }
                $i++;
            }
        }
    }

    print $consultas->select_dolusers($object->fk_user, 'fk_user', 1, $arra, 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '</td><td colspan="3"></td>';
    print "</tr>";
    $object->load_ref_elements();

   

    print '</table>';

    print dol_fiche_end();

    print '<div class="center">';
    print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
    print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    print '</div>';

    print "</form>";

}

if (! empty($id) && $action != 'edit' && $action != 'create')
{
    $objsoc = new Societe($db);


    dol_htmloutput_errors($error,$errors);
    dol_fiche_head($head, 'card', 'Paciente', 0, 'contact');

    $linkback = '<a href="'.DOL_URL_ROOT.'/cclinico/pacientes_list.php">'.$langs->trans("BackToList").'</a>';
    
   	$elemento=$object->element;
    $object->element="contact";

    dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', '');
    $object->element=$elemento;
    
    print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
    
    print '<div class="underbanner clearboth"></div>';
    print '<table class="border tableforfield" width="100%">';

    // Company
    if (empty($conf->global->SOCIETE_DISABLE_CONTACTS))
    {
        print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
        if ($object->socid > 0)
        {
            $objsoc->fetch($object->socid);
            print $objsoc->getNomUrl(1);
        }
        else
        {
            print "Paciente no vinculado a un tercero";
        }
        print '</td>';
    }

    print '</tr>';

    // Civility
    print '<tr><td>'.$langs->trans("UserTitle").'</td><td>';
    print $object->getCivilityLabel();
    print '</td></tr>';

    // Role
    print '<tr><td>'.$langs->trans("PostOrFunction").'</td><td>'.$object->poste.'</td></tr>';

    // Email
    if (! empty($conf->mailing->enabled))
    {
        $langs->load("mails");
        print '<tr><td>'.$langs->trans("NbOfEMailingsSend").'</td>';
        print '<td><a href="'.DOL_URL_ROOT.'/comm/mailing/list.php?filteremail='.urlencode($object->email).'">'.$object->getNbOfEMailings().'</a></td></tr>';
    }

    // Instant message and no email
    print '<tr><td>'.$langs->trans("IM").'</td><td>'.$object->jabberid.'</td></tr>';
    if (!empty($conf->mailing->enabled))
    {
    	print '<tr><td>'.$langs->trans("No_Email").'</td><td>'.yn($object->no_email).'</td></tr>';
    }

    print '<tr><td>'.$langs->trans("ContactVisibility").'</td><td>';
    print $object->LibPubPriv($object->priv);
    print '</td></tr>';
    // Date To Birth
    print '<tr>';
    if (! empty($object->birthday))
    {
        include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

        print '<td>'.$langs->trans("DateToBirth").'</td><td colspan="3">'.dol_print_date($object->birthday,"day");

        print ' &nbsp; ';
        //var_dump($birthdatearray);

        $ageyear=convertSecondToTime($now-$object->birthday,'year')-1970;
        $agemonth=convertSecondToTime($now-$object->birthday,'month')-1;
        if ($ageyear >= 2) print '('.$ageyear.' '.$langs->trans("DurationYears").')';
        else if ($agemonth >= 2) print '('.$agemonth.' '.$langs->trans("DurationMonths").')';
        else print '('.$agemonth.' '.$langs->trans("DurationMonth").')';
        print '</td>';
    }
    else
    {
        print '<td>'.$langs->trans("DateToBirth").'</td><td colspan="3">'.$langs->trans("Unknown")."</td>";
    }
    print "</tr>";
    print '<tr><td class="edad">Edad Ingresada</td><td colspan="3">';
    print $object->edad;
    print '</td></tr>';
    print '<tr><td class="estatura">'.$langs->trans("Code30").'</td><td colspan="3">';
    print $object->estatura;
    print '</td></tr>';
    print '<tr><td class="sexo">'.$langs->trans("Code31").'</td><td colspan="3">';
    print (($object->sexo==1)?"Femenino":"Masculino");
    print '</td></tr>';
    // medico asignado
   
    if ($object->fk_user > 0)
    {
        $objsoc2 = new User($db);
        $objsoc2->fetch($object->fk_user);

        print '<tr><td>'.$langs->trans("Code32").'</td><td colspan="3">'.$objsoc2->getNomUrl(1).'</td></tr>';
    }

    else
    {
        print '<tr><td>'.$langs->trans("Code34").'</td><td colspan="3">';
        print "Ningun médico da seguimiento a este paciente";
        print '</td></tr>';
    }
    

    print '</table>';
    
    print '</div>';
    print '<div class="fichehalfright"><div class="ficheaddleft">';
   
    print '<div class="underbanner clearboth"></div>';
    print '<table class="border tableforfield" width="100%">';


  
    print $object->showOptionals($extrafields);
    


    

    print "</table>";
    
    print '</div></div></div>';
    print '<div style="clear:both"></div>';
            
    print dol_fiche_end();

    // Barre d'actions
    print '<div class="tabsAction">';

    print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=edit">'.$langs->trans('Modify').'</a>';
    
    // Activer
    if ($object->statut == 0)
    {
        print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=enable">'.$langs->trans("Reactivate").'</a>';
    }
    // Desactiver
    if ($object->statut == 1 )
    {
        print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=disable&id='.$object->id.'">'.$langs->trans("DisableUser").'</a>';
    }

    // Delete
    print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete'.($backtopage?'&backtopage='.urlencode($backtopage):'').'">'.$langs->trans('Delete').'</a>';
    print "</div>";
    print "<br>";
}
    



llxFooter();

$db->close();
