<?php

function get_node($id_cred){
    global $conf,$user,$langs,$db;

    $sql="SELECT a.rowid FROM llx_ctrl_advance_credit as a WHERE a.fk_parent=".$id_cred;

    $resql = $db->query($sql);
    if ($resql) {
        $numrows = $db->num_rows($resql);
        if ($numrows>0) {

            $obj = $db->fetch_object($resql);
            $array[0]=$obj->rowid;
            $obj = $db->fetch_object($resql);
            $array[1]=$obj->rowid;
            return $array;
        }
    }
    return NULL;
}



function advance_mask($conf,$type=0)
{
    $dirsociete =array('core/modules/');
    $res        =false;
    $module     =(!empty($conf->global->SOCIETE_CODECTRLANTICIPO_ADDON)?$conf->global->SOCIETE_CODECTRLANTICIPO_ADDON:'mod_codeanticipos_monkey');

    if (substr($module, 0, 15) == 'mod_codeanticipos_' && substr($module, -3) == 'php')
        $module = substr($module, 0, dol_strlen($module)-4);

    foreach ($dirsociete as $dirroot){
        $res =dol_include_once("ctrlanticipo/".$dirroot.$module.'.php');
        if ($res) break;
    }

    $modCodeClient = new $module;
    $tmpcode       = $modCodeClient->getNextValue($object,$type);

    return $tmpcode;
}

function multidivisa($value='')
{
    
    global $conf,$user,$langs,$db;
    $sql2="SELECT code,label,unicode FROM llx_multidivisa_divisas WHERE entity=".$conf->entity;
    $resulset2=$db->query($sql2);
    print "<select name='moddivisa'>";
    while($res2=$db->fetch_object($resulset2)){
        print "<option value='".$res2->code."'>".$res2->code."</option>";
    }
    print "</select>";
    
}


function select_multidivisa($selected='', $htmlname='ctrl_mcurreny', $show_empty=1, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=-1, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0,$only=0)
{
    global $conf,$user,$langs,$db;

    if ($conf->global->MAIN_MODULE_MULTIDIVISA==1) {

        // If no preselected user defined, we take current user
        if ((is_numeric($selected) && ($selected < -2 || empty($selected))) && empty($conf->global->SOCIETE_DISABLE_DEFAULT_SALESREPRESENTATIVE)) $selected=$user->id;

        $excludeUsers=null;
        $includeUsers=null;

        // Permettre l'exclusion d'utilisateurs
        if (is_array($exclude)) $excludeUsers = implode("','",$exclude);
        // Permettre l'inclusion d'utilisateurs
        if (is_array($include)) $includeUsers = implode("','",$include);
        else if ($include == 'hierarchy')
        {
            // Build list includeUsers to have only hierarchy
            $userid=$user->id;
            $include=array();
            if (empty($user->users) || ! is_array($user->users)) $user->get_full_tree();
            foreach($user->users as $key => $val)
            {
                if (preg_match('/_'.$userid.'/',$val['fullpath'])) $include[]=$val['id'];
            }
            $includeUsers = implode("','",$include);
            //var_dump($includeUsers);exit;
            //var_dump($user->users);exit;
        }

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT rowid,code,code as lastname,unicode";
        $sql.= " FROM llx_multidivisa_divisas as u WHERE entity=".$conf->entity;
        $sql.= " ORDER BY label ASC";

        $resql=$db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                // Enhance with select2
                $nodatarole='';
                if ($conf->use_javascript_ajax)
                {
                    include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
                    $comboenhancement = ajax_combobox($htmlname);
                    $out.=$comboenhancement;
                    $nodatarole=($comboenhancement?' data-role="none"':'');
                }
                if ($only==0) {
                    $out.= '<tr><td class="fieldrequired" >' . $langs->trans("ctrl_multi_cash") . '</td><td>';
                }
                

                $out.= '<select class="flat minwidth200'.($morecss?' '.$morecss:'').'" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
                if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
                if ($show_every) $out.= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

                $userstatic=new User($db);

                while ($i < $num)
                {
                    $obj = $db->fetch_object($resql);

                    $userstatic->id=$obj->rowid;
                    $userstatic->lastname=$obj->lastname;
                    $userstatic->firstname=$obj->firstname;

                    $disableline='';
                    if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=($enableonlytext?$enableonlytext:'1');

                    if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
                    {
                        $out.= '<option value="'.$obj->rowid.'"';
                        if ($disableline) $out.= ' disabled';
                        $out.= ' selected>';
                    }
                    else
                    {
                        $out.= '<option value="'.$obj->rowid.'"';
                        if ($disableline) $out.= ' disabled';
                        $out.= '>';
                    }

                    $out.= $userstatic->getFullName($langs, 0, -1, $maxlength);
                    // Complete name with more info
                    $moreinfo=0;
                    if (! empty($conf->global->MAIN_SHOW_LOGIN))
                    {
                        $out.= ($moreinfo?' - ':' (').$obj->login;
                        $moreinfo++;
                    }
                    if ($showstatus >= 0)
                    {
                        if ($obj->statut == 1 && $showstatus == 1)
                        {
                            $out.=($moreinfo?' - ':' (').$langs->trans('Enabled');
                            $moreinfo++;
                        }
                        if ($obj->statut == 0)
                        {
                            $out.=($moreinfo?' - ':' (').$langs->trans('Disabled');
                            $moreinfo++;
                        }
                    }
                    if (! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode) && $conf->entity == 1 && $user->admin && ! $user->entity)
                    {
                        if ($obj->admin && ! $obj->entity)
                        {
                            $out.=($moreinfo?' - ':' (').$langs->trans("AllEntities");
                            $moreinfo++;
                        }
                        else
                     {
                            $out.=($moreinfo?' - ':' (').($obj->label?$obj->label:$langs->trans("EntityNameNotDefined"));
                            $moreinfo++;
                        }
                    }
                    $out.=($moreinfo?')':'');
                    if ($disableline && $disableline != '1')
                    {
                        $out.=' - '.$disableline;   // This is text from $enableonlytext parameter
                    }
                    $out.= '</option>';

                    $i++;
                }
            }
            else
            {
                $out.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" disabled>';
                $out.= '<option value="">'.$langs->trans("None").'</option>';
            }
            $out.= '</select>';
            if ($only==0) {
                $out.= '</td></tr>';
            }
        }
        else
        {
            dol_print_error($db);
        }
    }
    return $out;
}
function advance_statut($search_statut=0,$htmlname)
{
    global $langs;
    print '
    <select class="flat minwidth200 maxwidth300" id="'.$htmlname.'" name="'.$htmlname.'" data-role="none">';
        print '
        <option value="" selected>&nbsp;</option>
        ';
        if ($search_statut==0 && strlen($search_statut )>0) {
            print '
            <option value="0" selected>&nbsp;'.$langs->trans('ctrl_action_statut0').'</option>
            ';
        }else{
             print '
            <option value="0" >&nbsp;'.$langs->trans('ctrl_action_statut0').'</option>
            ';
        }
        if ($search_statut==1) {
            print '
            <option value="1" selected>&nbsp;'.$langs->trans('ctrl_action_statut1').'</option>
            ';
        }else{
             print '
            <option value="1" >&nbsp;'.$langs->trans('ctrl_action_statut1').'</option>
            ';
        }
        if ($search_statut==2) {
            print '
            <option value="2" selected>&nbsp;'.$langs->trans('ctrl_action_statut2').'</option>
            ';
        }else{
             print '
            <option value="2" >&nbsp;'.$langs->trans('ctrl_action_statut2').'</option>
            ';
        }
        if ($search_statut==3) {
            print '
            <option value="3" selected>&nbsp;'.$langs->trans('ctrl_action_statut3').'</option>
            ';
        }else{
             print '
            <option value="3" >&nbsp;'.$langs->trans('ctrl_action_statut3').'</option>
            ';
        }
        if ($search_statut==5) {
            print '
            <option value="5" selected>&nbsp;'.$langs->trans('ctrl_action_statut5').'</option>
            ';
        }else{
             print '
            <option value="5" >&nbsp;'.$langs->trans('ctrl_action_statut5').'</option>
            ';
        }
        if ($search_statut==4) {
            print '
            <option value="4" selected>&nbsp;'.$langs->trans('ctrl_action_statut4').'</option>
            ';
        }else{
             print '
            <option value="4" >&nbsp;'.$langs->trans('ctrl_action_statut4').'</option>
            ';
        }

        

    print '</select>';
}

function advance_payment_prepare_head(PaiementAdvance $object) {
    
    global $langs, $conf;
    
    $h = 0;
    $head = array();

    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrlpaiementfourn_card.php?id='.$object->id;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'payment';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);                                                   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'payment');

    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrlpaiementfourn_info.php?id='.$object->id;
    $head[$h][1] = $langs->trans("Info");
    $head[$h][2] = 'info';
    $h++;

    complete_head_from_modules($conf,$langs,$object,$head,$h,'payment', 'remove');

    return $head;
}

function advance_prepare_head(Ctrladvanceprovider $object) {
    
    global $langs, $conf;
    
    $h = 0;
    $head = array();

    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$object->id.'&action=view';
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'payment';
    $h++;

    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvancecredit_card.php?id='.$object->id;
    $head[$h][1] = $langs->trans("ctrl_view_manage_credit");
    $head[$h][2] = 'ad_cred';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);                                                   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'payment');
    // Notes
    if (empty($conf->global->MAIN_DISABLE_NOTES_TAB)) {
        $nbNote = (empty($object->note_private)?0:1)+(empty($object->note_public)?0:1);
        $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card_actions.php?id='.$object->id.'&action=note';
        $head[$h][1] = $langs->trans("Note");
        if($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
        $head[$h][2] = 'note';
        $h++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    $upload_dir = DOL_DOCUMENT_ROOT."/documents/ctrlanticipo/".$object->id;
    $nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card_actions.php?id='.$object->id.'&saction=files';
    $head[$h][1] = $langs->trans("Documents");
    if($nbFiles > 0) $head[$h][1].= ' <span class="badge">'.$nbFiles.'</span>';
    $head[$h][2] = 'documents';
    $h++;


    $head[$h][0] = DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card_actions.php?id='.$object->id.'&action=log';
    $head[$h][1] = $langs->trans("Info");
    $head[$h][2] = 'info';
    $h++;

    complete_head_from_modules($conf,$langs,$object,$head,$h,'payment', 'remove');

    return $head;
}








function numtoletras($xcifra,$divisa=" MN")
{ 
    global $db;
    $xarray = array(0 => "Cero",
    1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", 
    "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", 
    "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 
    100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
    //
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false))
        {
        if ($xpos_punto == 0)
            {
            $xcifra = "0".$xcifra;
            $xpos_punto = strpos($xcifra, ".");
            }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }
     
    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)

    if (strpos($divisa,"MN")!==false ) {
        $moneda="PESO";
    }
    if (strpos($divisa,"USD")!==false  ) {
        $moneda="DOLAR";
    }
    if (strpos($divisa,"EUR")!==false ) {
        $moneda="EURO";
    }else{
       $sql='SELECT a.label FROM llx_c_currencies as a WHERE a.code_iso="'.$divisa.'"';

        $resql = $db->query($sql);
        if ($resql) {
            $numrows = $db->num_rows($resql);
            if ($numrows>0) {
                $var=1;
                $obj = $db->fetch_object($resql);
                $moneda=$obj->label;
            }
        } 
    }
    



    $xcadena = "";
    for($xz = 0; $xz < 3; $xz++)
        {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While 
        while ($xexit)
            {
            if ($xi == $xlimite) // si ya lleg� al l�mite m�ximo de enteros
                {
                break; // termina el ciclo
                }
     
            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
            for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
                {
                switch ($xy) 
                    {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            {
                            }
                        else
                            {
                            $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                            if ($xseek)
                                {
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100) 
                                    $xcadena = " ".$xcadena." CIEN ".$xsub;
                                else
                                    $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                            else // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                {
                                $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " ".$xcadena." ".$xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma l�gica que las centenas)
                        if (substr($xaux, 1, 2) < 10)
                            {
                            }
                        else
                            {
                            $xseek = $xarray[substr($xaux, 1, 2)];
                            if ($xseek)
                                {
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " ".$xcadena." VEINTE ".$xsub;
                                else
                                    $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                                $xy = 3;
                                }
                            else
                                {
                                $xseek = $xarray[substr($xaux, 1, 1) * 10];
                                if (substr($xaux, 1, 1) * 10 == 20)
                                    $xcadena = " ".$xcadena." ".$xseek;
                                else    
                                    $xcadena = " ".$xcadena." ".$xseek." Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                            {
                            }
                        else
                            {
                            $xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO
     
            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";
     
            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";
     
            // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
            if (trim($xaux) != "")
                {
                switch ($xz)
                    {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1 )
                            {
                                if ($var!=1) {
                                    if ($divisa=="MN") {
                                        $moneda="PESOS";
                                    }elseif ($divisa=="USD") {
                                        $moneda="DOLARES";
                                    }elseif ($divisa=="EUR") {
                                        $moneda="EUROS";
                                    }
                                }
                            $xcadena = "CERO ".$moneda." $xdecimales/100".$divisa;
                            }
                        if ($xcifra >= 1 && $xcifra < 2)
                            {
                            $xcadena = "UN ".$moneda." $xdecimales/100".$divisa;
                            }
                        if ($xcifra >= 2)
                            {
                                if ($var!=1) {
                                    if (strpos($divisa," MN")!==false  ) {
                                        $moneda="PESOS";
                                    }elseif (strpos($divisa,"USD")!==false ) {
                                        $moneda="DOLARES";
                                    }elseif (strpos($divisa,"EUR")!==false   ) {
                                        $moneda="EUROS";
                                    }
                                }
                            $xcadena.= " ".$moneda." $xdecimales/100".$divisa; // 
                            }
                        break;
                    } // endswitch ($xz)
                } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
}  // END FUNCTION
 
 
function subfijo($xx)
{ // esta funci�n regresa un subfijo para la cifra
$xx = trim($xx);
$xstrlen = strlen($xx);
if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
    $xsub = "";
//  
if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
    $xsub = "MIL";
//
return $xsub;
}

function format_chec($id,$model='')
{
    if (empty($model)) {
        if (isset($conf->global->CTRLANTICIPO_FORMAT) && !empty($conf->global->CTRLANTICIPO_FORMAT)) {
            include DOL_DOCUMENT_ROOT . "/ctrlanticipo/view/models/".$conf->global->CTRLANTICIPO_FORMAT.".php";
        }else{
            include DOL_DOCUMENT_ROOT . "/ctrlanticipo/view/models/ctrl_format_sol_cheque.php";
        }
    }else{
        
        include DOL_DOCUMENT_ROOT . "/ctrlanticipo/view/models/".$model.".php";
    }
    
    
}


function list_of_documents($filearray,$object,$modulepart,$param='',$forcedownload=0,$relativepath='',$permtodelete=1,$useinecm=0,$textifempty='',$maxlength=0,$title='',$url='', $showrelpart=0){

    global $user, $conf, $langs, $hookmanager;
    global $bc;
    global $sortfield, $sortorder, $maxheightmini;

    $hookmanager->initHooks(array('formfile'));

    $parameters=array(
            'filearray' => $filearray,
            'modulepart'=> $modulepart,
            'param' => $param,
            'forcedownload' => $forcedownload,
            'relativepath' => $relativepath,
            'permtodelete' => $permtodelete,
            'useinecm' => $useinecm,
            'textifempty' => $textifempty,
            'maxlength' => $maxlength,
            'title' => $title,
            'url' => $url
    );
    $reshook=$hookmanager->executeHooks('showFilesList', $parameters, $object);

    if (isset($reshook) && $reshook != '') // null or '' for bypass
    {
        return $reshook;
    }
    else
    {
        $param = (isset($object->id)?'&id='.$object->id:'').$param;

        // Show list of existing files
        if (empty($useinecm)) print load_fiche_titre($title?$title:$langs->trans("AttachedFiles"));
        if (empty($url)) $url=$_SERVER["PHP_SELF"];
        print '<!-- html.formfile::list_of_documents -->'."\n";
        print '<table width="100%" class="'.($useinecm?'nobordernopadding':'liste').'">';
        print '<tr class="liste_titre">';
            print '<td align="center" colspan=5>';
                print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
                    print '<input type="hidden" name="id" value="'.$object->id.'">';
                    print '<input type="hidden" name="action" value="rewrite_model">';
                    print 'Modelo&nbsp;&nbsp;&nbsp;<select name="format">';
                    $dirsociete=array_merge(array('/ctrlanticipo/view/models/'),$conf->modules_parts['societe']);
                    foreach ($dirsociete as $dirroot){
                        $dir = dol_buildpath($dirroot,0);
                        $handle = @opendir($dir);
                        if (is_resource($handle))
                        {
                            while (($file = readdir($handle))!==false )
                            {
                                
                                if (substr($file, 0, 12) == 'ctrl_format_' && substr($file, -3) == 'php'){
                                    $file = substr($file, 0, dol_strlen($file)-4);
                                    $file_label=substr($file, 12);
                                    if ($conf->global->CTRLANTICIPO_FORMAT==$file) {
                                        print '<option value="'.$file.'" selected>'.$file_label .'</option>';
                                    }else{
                                        print '<option value="'.$file.'">'.$file_label .'</option>';
                                    }
                                    
                                }
                            }
                        }
                    }
                    print '</select>
                    &nbsp;&nbsp;&nbsp;
                    <input type="submit" class="button" value="Generar">';
                print '</form>';
                print '</td>';
        print "</tr>\n";

        $nboffiles=count($filearray);

        if ($nboffiles > 0) include_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';

        $var=true;
        foreach($filearray as $key => $file)      // filearray must be only files here
        {
            if ($file['name'] != '.'
                    && $file['name'] != '..'
                    && ! preg_match('/\.meta$/i',$file['name']))
            {
                // Define relative path used to store the file
                if (empty($relativepath))
                {
                    $relativepath=(! empty($object->ref)?dol_sanitizeFileName($object->ref):'').'/';
                    if ($object->element == 'invoice_supplier') $relativepath=get_exdir($object->id,2,0,0,$object,'invoice_supplier').$relativepath;    // TODO Call using a defined value for $relativepath
                    if ($object->element == 'member') $relativepath=get_exdir($object->id,2,0,0,$object,'member').$relativepath;                // TODO Call using a defined value for $relativepath
                    if ($object->element == 'project_task') $relativepath='Call_not_supported_._Call_function_using_a_defined_relative_path_.';
                }
                // For backward compatiblity, we detect file is stored into an old path
                if (! empty($conf->global->PRODUCT_USE_OLD_PATH_FOR_PHOTO) && $file['level1name'] == 'photos')
                {
                    $relativepath=preg_replace('/^.*\/produit\//','',$file['path']).'/';
                }
                $var=!$var;
                print '<tr '.$bc[$var].'>';
                print '<td class="tdoverflow">';
                //print "XX".$file['name']; //$file['name'] must be utf8
                print '<a data-ajax="false" href="'.DOL_URL_ROOT.'/document.php?modulepart='.$modulepart;
                if ($forcedownload) print '&attachment=1';
                if (! empty($object->entity)) print '&entity='.$object->entity;
                $filepath=$relativepath.$file['name'];
                /* Restore old code: When file is at level 2+, full relative path (and not only level1) must be into url
                if ($file['level1name'] <> $object->id)
                    $filepath=$object->id.'/'.$file['level1name'].'/'.$file['name'];
                else
                    $filepath=$object->id.'/'.$file['name'];
                */
                print '&file='.urlencode($filepath);
                print '">';

                print img_mime($file['name'],$file['name'].' ('.dol_print_size($file['size'],0,0).')').' ';
                if ($showrelpart == 1) print $relativepath;
                //print dol_trunc($file['name'],$maxlength,'middle');
                print $file['name'];
                print '</a>';
                print "</td>\n";
                print '<td align="right" width="80px">'.dol_print_size($file['size'],1,1).'</td>';
                print '<td align="center" width="130px">'.dol_print_date($file['date'],"dayhour","tzuser").'</td>';
                // Preview
                if (empty($useinecm))
                {
                    $fileinfo = pathinfo($file['name']);
                    print '<td align="center">';
                    if (image_format_supported($file['name']) > 0)
                    {
                        $minifile=getImageFileNameForSize($file['name'], '_mini'); // For new thumbs using same ext (in lower case howerver) than original
                        if (! dol_is_file($file['path'].'/'.$minifile)) $minifile=getImageFileNameForSize($file['name'], '_mini', '.png'); // For backward compatibility of old thumbs that were created with filename in lower case and with .png extension
                        //print $file['path'].'/'.$minifile.'<br>';
                        print '<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&file='.urlencode($relativepath.$fileinfo['filename'].'.'.strtolower($fileinfo['extension'])).'" class="aphoto" target="_blank">';
                        print '<img border="0" height="'.$maxheightmini.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&file='.urlencode($relativepath.$minifile).'" title="">';
                        print '</a>';
                    }
                    else print '&nbsp;';
                    print '</td>';
                }
                // Delete or view link
                // ($param must start with &)
                print '<td class="valignmiddle right" width="50px">';
                if ($useinecm)     print '<a href="'.DOL_URL_ROOT.'/ecm/docfile.php?urlfile='.urlencode($file['name']).$param.'" class="editfilelink" rel="'.urlencode($file['name']).'">'.img_view().'</a> &nbsp; ';
                else
                {
                    if (image_format_supported($file['name']) > 0)
                    {
                        $permtoedit=0;
                        $newmodulepart=$modulepart;
                        if ($modulepart == 'product' || $modulepart == 'produit' || $modulepart == 'service')
                        {
                            if ($user->rights->produit->creer && $object->type == Product::TYPE_PRODUCT) $permtoedit=1;
                            if ($user->rights->service->creer && $object->type == Product::TYPE_SERVICE) $permtoedit=1;
                            $newmodulepart='produit|service';
                        }
                        /* TODO Not yet working
                        if ($modulepart == 'holiday')
                        {
                            if ($user->rights->holiday->write_all) $permtoedit=1;
                        }
                        */

                        if (empty($conf->global->MAIN_UPLOAD_DOC)) $permtoedit=0;

                        if ($permtoedit)
                        {
                            // Link to resize
                            print '<a href="'.DOL_URL_ROOT.'/core/photos_resize.php?modulepart='.urlencode($newmodulepart).'&id='.$object->id.'&file='.urlencode($relativepath.$fileinfo['filename'].'.'.strtolower($fileinfo['extension'])).'" title="'.dol_escape_htmltag($langs->trans("Resize")).'">'.img_picto($langs->trans("Resize"),DOL_URL_ROOT.'/theme/common/transform-crop-and-resize','',1).'</a> &nbsp; ';
                        }
                    }
                }
                if ($permtodelete)
                {
                    /*
                    if ($file['level1name'] <> $object->id)
                        $filepath=$file['level1name'].'/'.$file['name'];
                    else
                        $filepath=$file['name'];
                    */
                    $useajax=1;
                    if (! empty($conf->dol_use_jmobile)) $useajax=0;
                    if (empty($conf->use_javascript_ajax)) $useajax=0;
                    if (! empty($conf->global->MAIN_ECM_DISABLE_JS)) $useajax=0;

                    print '<a href="'.(($useinecm && $useajax)?'#':$url.'?action=delete&urlfile='.urlencode($filepath).$param).'" class="deletefilelink" rel="'.$filepath.'">'.img_delete().'</a>';
                }
                else print '&nbsp;';
                print "</td>";
                print "</tr>\n";
            }
        }
        if ($nboffiles == 0)
        {
            print '<tr '.$bc[false].'><td colspan="'.(empty($useinecm)?'5':'4').'" class="opacitymedium">';
            if (empty($textifempty)) print $langs->trans("NoFileFound");
            else print $textifempty;
            print '</td></tr>';
        }
        print "</table>";
        
        return $nboffiles;
    }
}
