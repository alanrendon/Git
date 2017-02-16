<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
dol_include_once('/cclinico/class/pacientes.class.php');
dol_include_once('/cclinico/class/antecedentes.class.php');
dol_include_once('/cclinico/class/consultas.class.php');
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("cclinico");
$now=dol_now();
$mesg=''; $error=0; $errors=array();

$action     = (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm    = GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id         = GETPOST('id','int');
$aid        = GETPOST('aid','int');
$socid      = GETPOST('socid','int');
$doc        = GETPOST("doc");
$doc2       = GETPOST("doc2");
if ($user->societe_id) $socid=$user->societe_id;
$dir_paciente="files/Paciente-".$id;
$pacientes = new Pacientes($db);
$antecedentes = new Antecedentes($db);
$extrafields = new ExtraFields($db);
$extrafields2 = new ExtraFields($db);
$consultas = new Consultas($db);



// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($pacientes->table_element);
$extralabels2=$extrafields2->fetch_name_optionals_label($consultas->table_element);
/*
 *  Actions
*/


//crear borrador vacío
if ($action=='create') {
    
    if (!is_dir($dir_paciente)) {
        dol_mkdir($dir_paciente);
    }
    $consultas->fk_user_med            = $pacientes->fk_user;
    $consultas->Ref                    = $tmpcode;
    $consultas->fk_user_pacientes      = $id;
    $id2 =  $consultas->create($user);

    if ($id2 > 0){
        $consultas->fetch($id2);
        $aid=$id2;

        $tmpcode=mascara_referencia($conf,1,$consultas);
        $consultas->Ref                    = $tmpcode;
        $consultas->update($user);

        $dir_consulta=$dir_paciente."/Consulta-".$id2."/";
        dol_mkdir($dir_consulta);
    }
}

//confirmar borrar antecedentes
if ($action == 'confirm_delete_1' && $confirm == 'yes' )
{
    $aid         = GETPOST('aid','int');
    $result=$antecedentes->fetch($aid);
    $result = $antecedentes->setstatus(0);
    if ($result > 0)
    {
        if ($backtopage)
        {
            header("Location: ".$backtopage);
            echo $backtopage;
            exit;
        }
        else
        {
            header("Location: ".DOL_URL_ROOT.'/cclinico/perso.php?id='.$id);
            exit;
        }
    }
    else
    {
        setEventMessages($pacientes->error,$pacientes->errors,'errors');
    }
}
//guardar borrador
if ($action=='create_update' ) {
    
    $consultas->fetch($aid);
    $tmpcode=mascara_referencia($conf,1,$consultas);
    if (empty($consultas->Ref)) {
        $consultas->Ref                    = $tmpcode;
    }
    
    $consultas->weight                 = GETPOST("weight");
    $consultas->Type_consultation      = GETPOST("Type_consultation");
    $consultas->blood_pressure         = GETPOST("blood_pressure");
    $consultas->reason                 = GETPOST("reason");
    $consultas->reason_detail          = GETPOST("reason_detail");
    $consultas->treatments             = GETPOST("treatments");
    $consultas->diagnostics            = GETPOST("diagnostics");
    $consultas->diagnostics_detail     = GETPOST("diagnostics_detail");
    $consultas->comments               = GETPOST("comments");
    $consultas->fk_user_med            = GETPOST("fk_user_med");
    $consultas->temperature            = GETPOST("temperature");
    $consultas->date_consultation      = dol_mktime(GETPOST("date_consultationhour"),GETPOST("date_consultationmin"),0,GETPOST("date_consultationmonth"),GETPOST("date_consultationday"),GETPOST("date_consultationyear"));
    $extrafields2->setOptionalsFromPost($extralabels2,$consultas);
    $result=$consultas->update($user);
    if ($result>0) {
       if ($backtopage) {
           header("Location: ".$backtopage);
       }else{
           header("Location: consultas_list.php");
       }
    }
}

//quitar archivos de consulta
if (!empty($doc) && $action=="del-down") {
    if (file_exists (($doc ) )) {
        unlink ($doc);
    }
}

//quitar archivos de consulta
if (!empty($doc) && !empty($aid)) {
    if (file_exists (($dir_paciente."/Consulta-".$aid."/".$doc ) )) {
        unlink ($dir_paciente."/Consulta-".$aid."/".$doc);
    }
}


//subir archivos a la consulta
if (!empty($doc2) && !empty($aid)) {
    $f=$dir_paciente."/Consulta-".$aid."/".$doc2;
    $fname=$doc2;
    if (file_exists (($dir_paciente."/Consulta-".$aid."/".$doc2 ) )) {
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$fname\"\n");
        $fp=fopen("$f", "r");
        fpassthru($fp);
    }
}

if ($action == 'delete' ){
    $consultas->fetch($aid);
    $result = $consultas->delete($user);
    $ret = $extrafields2->setOptionalsFromPost($extralabels,$consultas);
    if ($result > 0)
    {
        $dirtemp=$dir_paciente."/Consulta-".$aid;
        foreach(glob($dirtemp . "/*") as $archivos_carpeta)
        {
     
            if (is_dir($archivos_carpeta))
            {
                eliminarDir($archivos_carpeta);
            }
            else
            {
                unlink($archivos_carpeta);
            }
        }
        if (is_dir($dirtemp)) {
            rmdir($dirtemp);
        }
        
        header("Location: ".DOL_URL_ROOT.'/cclinico/perso.php?id='.$id);
        exit;
    }
    else
    {
        setEventMessages($consultas->error,$consultas->errors,'errors');
    }
}

llxHeader('', 'Crear Pacientes', '');

$form = new Form($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

if ($id > 0)
{
    // Si edition contact deja existant
    $res=$pacientes->fetch($id, $user);
    if ($res < 0) { dol_print_error($db,$pacientes->error); exit; }
    
    // Show tabs
    $head = pacientes_prepare_head($pacientes);

    $title = "Pacientes";
}
if ($aid>0) {
    $consultas->fetch($aid);
}
if ($socid > 0)
{
    $objsoc = new Societe($db);
    $objsoc->fetch($socid);
}


//subir archivo al borrador
if ($action=='create_upfile') {
    $tmpcode=mascara_referencia($conf,1,$consultas);
    //$consultas->Ref                    = $tmpcode;
    $consultas->weight                 = GETPOST("weight");
    $consultas->Type_consultation      = GETPOST("Type_consultation");
    $consultas->blood_pressure         = GETPOST("blood_pressure");
    $consultas->reason                 = GETPOST("reason");
    $consultas->reason_detail          = GETPOST("reason_detail");
    $consultas->treatments             = GETPOST("treatments");
    $consultas->diagnostics            = GETPOST("diagnostics");
    $consultas->diagnostics_detail     = GETPOST("diagnostics_detail");
    $consultas->comments               = GETPOST("comments");
    $consultas->fk_user_med            = GETPOST("fk_user_med");
    $consultas->temperature            = GETPOST("temperature");
    $consultas->date_consultation      = dol_mktime(GETPOST("date_consultationhour"),GETPOST("date_consultationmin"),0,GETPOST("date_consultationmonth"),GETPOST("date_consultationday"),GETPOST("date_consultationyear"));
    foreach ($_FILES["archivos"]["error"] as $key => $error) {
        if (!empty($_FILES['archivos']['name'][$key])) {
            if ($error == UPLOAD_ERR_OK) {
                $newfile=$dir_paciente."/Consulta-".$aid."/".dol_sanitizeFileName($_FILES['archivos']['name'][$key]);
                error_reporting(E_ERROR);
                $result = dol_move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $newfile, 1);
                if ($result == -3) {
                    $error++;
                    $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' contiene caracteres invalidos";
                    setEventMessages($object->error,$object->errors,'errors');
                    break;
                }elseif (! $result > 0)
                {
                    $error++;
                    $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' es demaciado grande";
                    setEventMessages($object->error,$object->errors,'errors');
                    break;
                }
            }else{
                $error++;
                $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' es demaciado grande";
                setEventMessages($object->error,$object->errors,'errors');
                break;
            }
        }
        
    }
    $action='create';
}

//crear consulta valida con folio no provicional
if ($action=='create_consulta') {
    $tmpcode=mascara_referencia($conf,0);
    $ref_ant=$consultas->Ref;
    $consultas->Ref                    = $tmpcode;
    $consultas->weight                 = GETPOST("weight");
    $consultas->Type_consultation      = GETPOST("Type_consultation");
    $consultas->blood_pressure         = GETPOST("blood_pressure");
    $consultas->reason                 = GETPOST("reason");
    $consultas->reason_detail          = GETPOST("reason_detail");
    $consultas->treatments             = GETPOST("treatments");
    $consultas->diagnostics            = GETPOST("diagnostics");
    $consultas->diagnostics_detail     = GETPOST("diagnostics_detail");
    $consultas->comments               = GETPOST("comments");
    $consultas->fk_user_med            = GETPOST("fk_user_med");
    $consultas->temperature            = GETPOST("temperature");
    $consultas->date_consultation      = dol_mktime(GETPOST("date_consultationhour"),GETPOST("date_consultationmin"),0,GETPOST("date_consultationmonth"),GETPOST("date_consultationday"),GETPOST("date_consultationyear"));
    
    if (! $_POST["date_consultation"] || ! $_POST["date_consultationhour"] || ! $_POST["date_consultationmin"] )
    {
        $error++;
        $errors[]="Llene los campos 'Fecha y Hora de Consulta' ";
        $action = 'create';
    }
    if (! $_POST["Type_consultation"] || $_POST["Type_consultation"]== -1 )
    {
        $error++;
        $errors[]="Inserte el tipo de consulta";
        $action = 'create';
    }
    if (! $_POST["reason"] || $_POST["reason"]== -1 )
    {
        $error++;
        $errors[]="Inserte el motivos consulta";
        $action = 'create';
    }
    if (! $_POST["fk_user_med"] || $_POST["fk_user_med"]== -1 )
    {
        $error++;
        $errors[]="Inserte el médico asignado a esta consulta";
        $action = 'create';
    }
    if (! $_POST["treatments"] || empty($_POST["treatments"]) )
    {
        $error++;
        $errors[]="Inserte detalle del tratamiento";
        $action = 'create';
    }
    if (! $_POST["diagnostics"] || $_POST["diagnostics"]== -1 )
    {
        $error++;
        $errors[]="Inserte el tipo diagnostico";
        $action = 'create';
    }
    $extrafields2->setOptionalsFromPost($extralabels2,$consultas);
    if (! $error)
    {
        $result = $consultas->update($user);
        if ($result > 0) {
            $consultas->cambiar_statut(1,$user);
            $action='';
            setEventMessages($langs->trans("Code29_6")." ".str_replace('!', '',$consultas->Ref), null, 'mesgs');
        }
        if ($result <= 0)
        {
            $consultas->Ref=$ref_ant;
            $error++; $errors=array_merge($errors,($consultas->error?array($consultas->error):$consultas->errors));
            $action='create';
        }
    }else{
        $consultas->Ref=$ref_ant;
        $action = 'create';
    }
    
}


//borrar antecedentes 
if ($action == 'delete_1')
{
    $aid         = GETPOST('aid','int');
    print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$id."&aid=".$aid.($backtopage?'&backtopage='.$backtopage:''),"Eliminar Antecedente","¿Está seguro de querer eliminar este antecedente y toda su información inherente?","confirm_delete_1",'',0,1);
}




if (! empty($id) )
{
    $objsoc = new Societe($db);
    dol_htmloutput_errors('',$errors);
        dol_fiche_head($head, 'perso', 'Crear Consulta', 0, 'contact');
        $linkback = '<a href="'.DOL_URL_ROOT.'/cclinico/pacientes_list.php">'.$langs->trans("BackToList").'</a>';
        $elemento=$pacientes->element;
        $pacientes->element="contact";
        dol_banner_tab($pacientes, 'id', $linkback, 1, 'rowid', 'ref', '');
        $pacientes->element=$elemento;
        
        print '<div class="fichecenter">';

        //datos del paciente
        print '<div class="fichehalfleft" >';
        print '<div class="underbanner clearboth"></div>';
        print '<table  width="100%">';
        print '<tr>';
            print '<td style="vertical-align:top !important;"><table class="border tableforfield" width="100%">';
            // Date To Birth
            print '<tr>';
            if (! empty($pacientes->birthday))
            {
                include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

                print '<td>'.$langs->trans("DateToBirth").'</td><td colspan="3">'.dol_print_date($pacientes->birthday,"day");

                print ' &nbsp; ';
                //var_dump($birthdatearray);
                $ageyear=convertSecondToTime($now-$pacientes->birthday,'year')-1970;
                $agemonth=convertSecondToTime($now-$pacientes->birthday,'month')-1;
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
            print '<tr><td class="edad">'.$langs->trans("Code33").'</td><td colspan="3">';
            print $pacientes->edad;
            print '</td></tr>';
            print '<tr><td class="estatura">'.$langs->trans("Code30").'</td><td colspan="3">';
            print $pacientes->estatura;
            print '</td></tr>';
            print '<tr><td class="sexo">'.$langs->trans("Code31").'</td><td colspan="3">';
            print (($pacientes->sexo==1)?"Femenino":"Masculino");
            print '</td></tr>';
            // medico asignado
            
            if ($pacientes->fk_user > 0)
            {
                $objsoc2 = new User($db);
                $objsoc2->fetch($pacientes->fk_user);

                print '<tr><td>'.$langs->trans("Code34").'</td><td colspan="3">'.$objsoc2->getNomUrl(1).'</td></tr>';
            }

            else
            {
                print '<tr><td>'.$langs->trans("Code34").'</td><td colspan="3">';
                print "Ningun médico da seguimiento a este paciente";
                print '</td></tr>';
            }
            
            print '</table></td>';
            print '<td style="vertical-align:top !important;"><table class="border tableforfield" width="100%"  >';

                print '<tr><td>' . $langs->trans( "cl_function" ) . '</td>';
                print '<td colspan="3">';
                print $pacientes->poste;
                print '</td></tr>';
            
            print $pacientes->showOptionals($extrafields);

            print "</table></td>";
        print '</tr>';

        if ($action=="create") {
            print '<tr>';
                $rest=$consultas->listar_consultas($id);
                print '<br>';
                print "\n".'<script type="text/javascript" language="javascript">'."\n";
                print 'jQuery(document).ready(function () {
                            $( ".input_label" ).click(function() {
                                var id=$(this).attr("var");
                                var rot=$("#img2-"+id).attr("rot");
                                if (rot == 1) {
                                    $("#img2-"+id).attr("src","img/3down.png");
                                    $("#img2-"+id).attr("rot",0);
                                }else{
                                    $("#img2-"+id).attr("src","img/2down.png");
                                    $("#img2-"+id).attr("rot",1);
                                }
                                $("#fade2-"+id).fadeToggle( "slow", "linear" );
                            });
                            jQuery(".delete_consultas").click(function (){
                               $("#doc").val($(this).attr("doc"));
                               $("#form").submit();
                            });
                            jQuery(".down_consultas").click(function (){
                               $("#doc2").val($(this).attr("doc"));
                               $("#form").submit();
                            });
                        })'."\n";
                print '</script>'."\n";
                if ($rest) {
                    print '
                    <table class="border" width="100%" style="font-size: 10px !important;">
                        <tr class="liste_titre">
                            <td >
                                Num
                            </td>
                            <td>
                                Fecha
                            </td>
                            <td>
                                Doctor que atendío
                            </td>
                            <td>
                                Motivo de consulta
                            </td>
                            <td>
                                Diagnóstico/Enfermedad
                            </td>
                            <td>
                                Tratamiento
                            </td>
                            <td>
                            </td>
                        </tr>';
                    $i=count($rest);
                    if ($user->rights->cclinico->leer) {
                        foreach ($rest as $key) {
                            print '
                            <tr class="lum">
                                <td>';
                                if (!empty($key->Ref)){
                                    $objsoc2 = new Consultas($db);
                                    $objsoc2->fetch( $key->rowid);
                                    print $objsoc2->getNomUrl(1);
                                }
                                print '</td>
                                <td>'.date("d/m/Y",strtotime($key->date_consultation)).'</td>
                                <td>';
                                if ($key->fk_user_med > 0){
                                    $objsoc2 = new User($db);
                                    $objsoc2->fetch($key->fk_user_med);
                                    print $objsoc2->getNomUrl(1);
                                }
                                else
                                {
                                    print "N/A";
                                }
                                print '
                                </td>
                                <td>';
                                    if ($key->reason > 0){

                                        $reason=$consultas->listar_motivo_consulta($key->reason);
                                        if (strlen ( $reason)>15) {
                                            print substr($reason,0,15)."...";
                                        }else{
                                            print $reason;
                                        } 
                                    }
                                    else
                                    {
                                        print "N/A";
                                    }
                                print '
                                </td>
                                <td>';
                                    if ($key->diagnostics > 0){
                                        $diagnostic=$consultas->listar_diagnosticos($key->diagnostics);
                                        if (strlen ( $diagnostic)>15) {
                                            print substr($diagnostic,0,15)."...";
                                        }else{
                                            print $diagnostic;
                                        } 
                                    }
                                    else
                                    {
                                        print "N/A";
                                    }
                                print '
                                </td>
                                <td>';
                                    if (!empty($key->treatments)){
                                        if (strlen ( $key->treatments)>15) {
                                            print substr($key->treatments,0,15)."...";
                                        }else{
                                            print $key->treatments;
                                        }
                                        
                                    }
                                    else
                                    {
                                        print "N/A";
                                    }
                                print '
                                </td>
                                <td  align="center" class="input_label" style="cursor:pointer;" var='.$i.'>
                                    <img class="imgalign" id="img2-'.$i.'" src="img/2down.png" rot="1">
                                </td>';
                                print'
                            </tr>';
                            print '<tr id="fade2-'.$i.'" class="fader">';
                                print '<td colspan="8"><br>';
                                    print '<strong>Motivo de la Consulta: </strong>';
                                    if ($key->reason > 0){
                                        print $consultas->listar_motivo_consulta($key->reason);
                                    }
                                    else
                                    {
                                        print "Ningun motivo de consulta asignado a esta consulta";
                                    }
                                    print '<br><pre>'.$key->reason_detail.'</pre>';
                                    print '<br><br><strong>Diagnostico/Enfermedad: </strong>';
                                    if ($key->diagnostics > 0){

                                        print $consultas->listar_diagnosticos($key->diagnostics);
                                    }
                                    else
                                    {
                                        print "Ningun motivo de consulta asignado a esta consulta";
                                    }
                                    print '<br>'.$key->diagnostics_detail;
                                    print '<br><br><strong>Tratamiento:<br></strong>';
                                    if (!empty($key->treatments)){
                                        print '<pre>'.$key->treatments.'</pre>';
                                    }
                                    else
                                    {
                                        print "Ningun tratamiento asignado";
                                    }
                                print '<br><br></td>';
                            print '</tr>';
                            $i--;
                            if (count($rest)-3==$i) {
                                break;
                            }
                        }
                    }else{
                        print '<tr><td colspan=7>Necesita permisos para listar las consultas</td>';
                    }
                    
                    print '</table>';
                }
            print '</tr>';
        }
        

        print '</table>';
        print '</div>';

        //datos de expedientes
        print '<div class="fichehalfright"><div class="ficheaddleft">';
            print '<div class="underbanner clearboth"></div>';
            print '
            <div class="liste_titre" style="min-height: 20px !important;padding-top: 5px; padding-left:5px;">
                '.$langs->trans("Code35").'
            </div>';
            print '<div class="tabBar">';

            print "\n".'<script type="text/javascript" language="javascript">'."\n";
            print 'jQuery(document).ready(function () {
                        $( ".inputlabel" ).click(function() {
                            var id=$(this).attr("var");
                            var rot=$("#img-"+id).attr("rot");
                            if (rot == 1) {
                                $("#img-"+id).attr("src","img/3down.png");
                                $("#img-"+id).attr("rot",0);
                            }else{
                                $("#img-"+id).attr("src","img/2down.png");
                                $("#img-"+id).attr("rot",1);
                            }
                            $("#fade-"+id).fadeToggle( "slow", "linear" );
                        });
                    })'."\n";
            print '</script>'."\n";

            print '<div style="margin-bottom:5px;">';
            print '<a href="antecedentes.php?id='.$id.'&action=create" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" class="button" >'.$langs->trans("Code36").'</a></div>';

            $rest=$antecedentes->listar_antecedentes($id);
            if ($rest) {
                $i=count($rest);
                foreach ($rest as $key) {
                    print '
                    <div class="box-panel">
                        
                        <label class="inputlabel" var='.$i.'  >
                        <img class="imgalign" id="img-'.$i.'" src="img/2down.png" rot=1 />
                        n°'.$i.' - '.$key->fecha_creacion.' - '.$key->lastname.' '.$key->firstname.' : '.$key->titulo.'</label>
                        <div id="fade-'.$i.'" class="fader">
                            <div >
                                <a href="antecedentes.php?id='.$id.'&aid='.$key->id.'&action=edit"><img class="imgalign" src="img/edit.png"  /></a>
                                <a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&aid='.$key->id.'&action=delete_1"><img class="imgalign" src="img/disable.png"  /></a>
                                <br><br>
                                <div style="width: 500px !important; white-space: pre-wrap; word-wrap: break-word;">'.$key->descripcion.'</div>
                            </div>
                        </div>
                    </div>
                    ';
                    $i--;
                }
            }
        print '</div></div></div></div>';
        print '<div style="clear:both"></div>';
    print dol_fiche_end();

    $tmpcode=mascara_referencia($conf,0);

    if ($action!='create' && $user->rights->cclinico->leer) {
        //resumen de consultas
        //datos consultas
        print '<div class="tabBar">';
        if ($user->rights->cclinico->crear){
            $linkback = '<a href="'.DOL_URL_ROOT.'/cclinico/perso.php?id='.$id.'&action=create&backtopage=perso.php?id='.$id.'" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" class="button">'.$langs->trans("Code44").'</a>';
        }else{
            $linkback="";
        }
        print load_fiche_titre($langs->trans("Code37"),$linkback,'title_generic.png');
        dol_htmloutput_errors("",$errors);

        print "\n".'<script type="text/javascript" language="javascript">'."\n";
        print 'jQuery(document).ready(function () {
                    $( ".input_label" ).click(function() {
                        var id=$(this).attr("var");
                        var rot=$("#img2-"+id).attr("rot");
                        if (rot == 1) {
                            $("#img2-"+id).attr("src","img/3down.png");
                            $("#img2-"+id).attr("rot",0);
                        }else{
                            $("#img2-"+id).attr("src","img/2down.png");
                            $("#img2-"+id).attr("rot",1);
                        }
                        $("#fade2-"+id).fadeToggle( "slow", "linear" );
                    });
                    jQuery(".delete_consultas").click(function (){
                       $("#doc").val($(this).attr("doc"));
                       $("#form").submit();
                    });
                    jQuery(".down_consultas").click(function (){
                       $("#doc2").val($(this).attr("doc"));
                       $("#form").submit();
                    });
                })'."\n";
        print '</script>'."\n";
        $rest=$consultas->listar_consultas($id);

        print '<br>';
        if ($rest) {
            print '
            
            <table class="border" width="100%">
                <tr class="liste_titre">
                    <td >
                        '.$langs->trans("Code38").'
                    </td>
                    <td>
                        '.$langs->trans("Code39").'
                    </td>
                    <td>
                        '.$langs->trans("Code40").'
                    </td>
                    <td>
                        '.$langs->trans("Code41").'
                    </td>
                    <td>
                        '.$langs->trans("Code42").'
                    </td>
                    <td>
                        '.$langs->trans("Code43").'
                    </td>
                    <td>
                        
                    </td>
                    <td>
                    </td>
                </tr>';
            $i=count($rest);
            if ($user->rights->cclinico->leer) {
                foreach ($rest as $key) {
                    print '
                    <tr class="lum">
                        <td>';
                        if (!empty($key->Ref)){
                            $objsoc2 = new Consultas($db);
                            $objsoc2->fetch( $key->rowid);
                            print $objsoc2->getNomUrl(1);
                        }
                        print '</td>
                        <td>'.date("d/m/Y",strtotime($key->date_consultation)).'</td>
                        <td>';
                        if ($key->fk_user_med > 0){
                            $objsoc2 = new User($db);
                            $objsoc2->fetch($key->fk_user_med);
                            print $objsoc2->getNomUrl(1);
                        }
                        else
                        {
                            print "N/A";
                        }
                        print '
                        </td>
                        <td>';
                            if ($key->reason > 0){

                                $reason=$consultas->listar_motivo_consulta($key->reason);
                                if (strlen ( $reason)>22) {
                                    print substr($reason,0,22)."...";
                                }else{
                                    print $reason;
                                } 
                            }
                            else
                            {
                                print "N/A";
                            }
                        print '
                        </td>
                        <td>';
                            if ($key->diagnostics > 0){
                                $diagnostic=$consultas->listar_diagnosticos($key->diagnostics);
                                if (strlen ( $diagnostic)>22) {
                                    print substr($diagnostic,0,22)."...";
                                }else{
                                    print $diagnostic;
                                } 
                            }
                            else
                            {
                                print "N/A";
                            }
                        print '
                        </td>
                        <td>';
                            if (!empty($key->treatments)){
                                if (strlen ( $key->treatments)>22) {
                                    print substr($key->treatments,0,22)."...";
                                }else{
                                    print $key->treatments;
                                }
                                
                            }
                            else
                            {
                                print "N/A";
                            }
                        print '
                        </td>
                        <td  align="center" class="input_label" style="cursor:pointer;" var='.$i.'><img class="imgalign" id="img2-'.$i.'" src="img/2down.png" rot="1"></td>';
                            if ($key->statut==0) {
                                print '
                                <td colspan=4 align="left" ><img src="../theme/eldy/img/statut0.png" border="0" alt="" title="Borrador (a validar)"></td>
                                ';
                            }
                            if ($key->statut==1) {
                                print '
                                <td colspan=4 align="left" ><img src="../theme/eldy/img/statut4.png" border="0" alt="" title="Activado" ></td>
                                ';
                            }
                            if ($key->statut==2) {
                                print '
                                <td colspan=4 align="left" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Facturada" ></td>
                                ';
                            }
                            if ($key->statut==3) {
                                print '
                                <td colspan=4 align="left" ><img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Activado" ></td>
                                ';
                            }
                        print'
                    </tr>';
                    print '<tr id="fade2-'.$i.'" class="fader">';
                        print '<td colspan="8"><br>';
                            print '<strong>Motivo de la Consulta: </strong>';
                            if ($key->reason > 0){
                                print $consultas->listar_motivo_consulta($key->reason);
                            }
                            else
                            {
                                print "Ningun motivo de consulta asignado a esta consulta";
                            }
                            print '<br><pre>'.$key->reason_detail.'</pre>';
                            print '<br><br><strong>Diagnostico/Enfermedad: </strong>';
                            if ($key->diagnostics > 0){

                                print $consultas->listar_diagnosticos($key->diagnostics);
                            }
                            else
                            {
                                print "Ningun motivo de consulta asignado a esta consulta";
                            }
                            print '<br>'.$key->diagnostics_detail;
                            print '<br><br><strong>Tratamiento:<br></strong>';
                            if (!empty($key->treatments)){
                                print '<pre>'.$key->treatments.'</pre>';
                            }
                            else
                            {
                                print "Ningun tratamiento asignado";
                            }
                        print '<br><br></td>';
                    print '</tr>';
                    $i--;
                }
            }else{
                print '<tr><td colspan=7>Necesita permisos para listar las consultas</td>';
            }
            
            print '</table></div>';
        }

        if ($user->rights->cclinico->leer){
            
            
            print '<div class="tabBar">';
            print load_fiche_titre($langs->trans("Code45"),'','title_generic.png');
            print '
                <form style="font-size:11px !important;" method="post" enctype="multipart/form-data" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';
                print '<input type="hidden" name="id" value="'.$id.'">';
                print '<input type="hidden" id="action" name="action" value="del-down">';
                print '<input type="hidden" id="doc" name="doc" value="">';
                print '<input type="hidden" id="doc2" name="doc2" value="">';
                print '<br><br>
                <table class="border" width="70%">
                        <tr class="liste_titre">
                            <td  colspan="2" >
                                '.$langs->trans("Code46").'
                            </td>
                            <td align="right" colspan="3" >
                                <a class="button" style="text-decoration:none; font-size:11px !important;" target="_blank" href="pdf_formatos.php?aid='.$id.'">'.$langs->trans("Code48").'</a>
                            </td>
                           
                        </tr>
                        ';
                        $dir_paciente="files/Paciente-".$id."/";
                        if (is_dir($dir_paciente) && count(scandir($dir_paciente)) > 2 ) {
                            $directorio = opendir($dir_paciente);
                            $ban=0;

                            while ($archivo = readdir($directorio)) {
                                $resultado= stristr ($dir_paciente.$archivo,$dir_paciente);
                                if ($resultado) {
                                    $directorio2 = opendir($resultado);
                                    while ($archivo2 = readdir($directorio2) ){
                                        if (is_file($resultado."/".$archivo2)) {
                                           if (!is_dir($archivo2) && $archivo2!="." && $archivo2!=".."  ) {
                                                $ban=1;
                                                print '<tr class="lum">';
                                                print "<td>".$archivo2."</td>";
                                                print "<td>".filesize($resultado."/".$archivo2)." bytes</td>";
                                                print "<td>".strftime("%X, %d/%m/%Y", filectime($resultado."/".$archivo2))."</td>";
                                                
                                                print "
                                                <td>
                                                    <a href='".$resultado."/".$archivo2."' download='".$archivo2."'>
                                                        <img src='img/file.png' doc='".$archivo2."' ></img>
                                                    </a>
                                                </td>";
                                                if ($user->rights->cclinico->crear){
                                                    print "<td><img src='img/delete.png' doc='".$resultado."/".$archivo2."' download='".$archivo2."' class='delete_consultas'></img></td>";
                                                 }else{
                                                    print "<td></td>";
                                                 }
                                                print '</tr>';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($ban==0) {
                            print '<tr><td colspan="4">'.$langs->trans("Code47").'</td></tr>';
                        }
                    print '
                    </table><br>
                </form>
                ';
            print '</div>';
        }
    }
    if ($action == 'create' && ! empty($id) )
    {
        print '<div class="tabBar">';
        $consultas->date_consultation=(empty($consultas->date_consultation)?dol_now():$consultas->date_consultation);
        print "\n".'<script type="text/javascript" language="javascript">'."\n";
                print 'jQuery(document).ready(function () {
                            jQuery("#files").click(function (){
                               $("#action").val("create_upfile");
                               $("#form").submit();
                            });
                            jQuery(".delete_consultas").click(function (){
                               $("#doc").val($(this).attr("doc"));
                               $("#action").val("create_upfile");
                               $("#form").submit();
                            });
                            jQuery("#create").click(function (){
                               $("#action").val("create_consulta");
                               $("#form").submit();
                            });
                            jQuery("#eliminar").click(function (){
                               $("#action").val("delete");
                               $("#form").submit();
                            });
                            jQuery(".down_consultas").click(function (){
                               $("#doc2").val($(this).attr("doc"));
                               $("#action").val("create_upfile");
                               $("#form").submit();
                            });
                            jQuery("#borrador").click(function (){
                               $("#action").val("create_update");
                               $("#form").submit();
                            });
                            $("textarea[data-limit-rows=true]").on("keypress", function (event) {
                                var textarea = $(this),
                                    numberOfLines = (textarea.val().match(/\n/g) || []).length + 1,
                                    maxRows = parseInt(textarea.attr("rows"));
                                
                                if (event.which === 13 && numberOfLines === maxRows ) {
                                  return false;
                                }
                            });
                            
                        })'."\n";
                print '</script>'."\n";

        print '<form style="font-size:11px !important;" method="post" enctype="multipart/form-data" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" id="action" name="action" value="'.$action.'">';
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
        print '<input type="hidden" name="id" value="'.$id.'">';
        print '<input type="hidden" name="aid" value="'.$aid.'">';
        print '<input type="hidden" id="doc" name="doc" value="">';
        print '<input type="hidden" id="doc2" name="doc2" value="">';
        print '<table class="border"  style="width:1024px !important;">';
        print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;<b>'.$langs->trans("Code14").'</b></label>
            </td>
            <td  colspan="4" >';
                print $pacientes->getNomUrl(1);
        print '
            </td>';

            print '<td  colspan="2" align="right" >  Folio:
            </td><td>';

            print '<b>'.(empty($consultas->Ref)?dol_escape_htmltag($tmpcode):$consultas->Ref ).'</b>';
        print '</td>
        </tr>';
        print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code15").'</label>
            </td>
            <td  >';
                $form->select_date($consultas->date_consultation,'date_consultation',1,1,1,"form_date",1,1,0,0,'fulldaystart');
        print '
            </td>
            <td  >
                <label for="descripcion">&nbsp;<b>'.$langs->trans("Code16").'</b></label>
            </td>
            <td  >';
                print $consultas->select_dol($consultas->Type_consultation, 'c_tipo_consulta' ,'Type_consultation', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '
            </td>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code17").'</label>
            </td>
            <td  >
                <input name="weight" id="weight" type="text" size="2" maxlength="20" value="'.$consultas->weight.'">
            </td>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code18").'l</label>
            </td>
            <td  >
                <input name="blood_pressure" id="blood_pressure" type="text" size="2" maxlength="20" value="'.$consultas->blood_pressure.'">
            </td>
        </tr>';
        print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;<b>'.$langs->trans("Code20").'</b></label>
            </td>
            <td  >';
                print $consultas->select_dol($consultas->reason,'c_motivo_consulta' ,'reason', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '
            </td>
            <td  ><b>'.$langs->trans("Code21").'</b>
            </td>
            <td  colspan="2" >';
             print $consultas->select_dolusers($pacientes->fk_user, 'fk_user_med', 1, '', $disable2, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');

        print '
            </td>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code19").'</label>
            </td>
            <td colspan=7 >
                <input name="temperature" id="temperature" type="text" size="10" maxlength="10" value="'.$consultas->temperature.'">
            </td>
        </tr>';
         print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code23").'</label>
            </td>
            <td  >
                <textarea class="flat" name="reason_detail" id="reason_detail" style="width:327px !important;" rows="9">'.$consultas->reason_detail.'</textarea>
            </td>
            <td  >
                <label for="descripcion">&nbsp;<b>'.$langs->trans("Code24").'</b></label>
            </td>
            <td  colspan="5" >
                <textarea class="flat" name="treatments" data-limit-rows="true" id="treatments" style="width:482px !important;" rows="9">'.$consultas->treatments.'</textarea>
            </td>
        </tr>';
         print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;<b>'.$langs->trans("Code25").'</b></label>
            </td>
            <td  colspan="8" >';
                print $consultas->select_dol($consultas->diagnostics,'c_tipo_diagnostico' ,'diagnostics', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '
            </td>
        </tr>';
        print '
        <tr>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code26").'</label>
            </td>
            <td  >
                <textarea class="flat" name="diagnostics_detail" id="diagnostics_detail" style="width:327px !important;" rows="9">'.$consultas->diagnostics_detail.'</textarea>
            </td>
            <td  >
                <label for="descripcion">&nbsp;'.$langs->trans("Code49_1").'</label>
            </td>
            <td  colspan="5" >
                <textarea class="flat" data-limit-rows="true" name="comments" id="comments" style="width:482px !important;" rows="9">'.$consultas->comments.'</textarea>
            </td>
        </tr>';
        if (! empty($extrafields2->attribute_label))
        {
            $param=array("colspan"=>"8");
            print $consultas->showOptionals($extrafields2,'edit',$param);

        }
        print '
        <tr><br>
            <td   colspan="2" >
                <label for="descripcion">&nbsp;Adjuntar nuevo archivo:</label>
            </td>
            <td  colspan="8" >
            </td>
        </tr>';
        print '
        <tr>
            <td colspan="3" >
                <input type="file" multiple="multiple" name="archivos[]" />
                <input class="button" value="'.$langs->trans("Code49_3").'" id="files" style="text-decoration:none; border-radius: 0px; font-size:11px !important;">
            </td>
            <td colspan="5" >
                <input class="button" value="Guardar Borrador" name="borrador" id="borrador" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >
                <input class="button" value="'.$langs->trans("Code28").'" name="create" id="create" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >
                <input size="8" class="button" value="Cancelar" name="eliminar" id="eliminar" style="text-decoration:none; border-radius: 0px; color:red; font-size:11px !important;" >
            </td>
        </tr>';
        print '
        <tr>
            <td colspan="2" ><br>
                <table class="border" width="100%">
                    <tr class="liste_titre">
                        <td  colspan="5" >
                            '.$langs->trans("Code49_4").'
                        </td>
                    </tr>
                    ';
                    if (is_dir($dir_paciente."/Consulta-".$aid."/") && count(scandir($dir_paciente."/Consulta-".$aid."/")) > 2 ) {
                        $directorio = opendir($dir_paciente."/Consulta-".$aid."/");
                        while ($archivo = readdir($directorio)) 
                        {
                            if (!is_dir($archivo) && $archivo!="." && $archivo!="..")
                            {
                                print '<tr class="lum">';
                                    print "<td>".$archivo."</td>";
                                    print "<td>".filesize($dir_paciente."/Consulta-".$aid."/".$archivo)." bytes</td>";
                                    print "<td>".strftime("%X, %d/%m/%Y", filectime($dir_paciente."/Consulta-".$aid."/".$archivo))."</td>";
                                    print "<td>
                                            <a href='".$dir_paciente."/Consulta-".$aid."/".$archivo."' download='".$archivo."'>
                                        <img src='img/file.png' doc='".$archivo."' ></img>
                                        </a></td>";
                                     if ($user->rights->cclinico->crear){
                                        print "<td><img src='img/delete.png' doc='".$archivo."' class='delete_consultas'></img></td>";
                                     }else{
                                        print "<td></td>";
                                     }
                                    

                                print '</tr>';
                            }
                        }
                    }else{
                        print '<tr><td colspan="4">'.$langs->trans("Code47").'</td></tr>';
                    }
                print '
                </table><br>
            </td>
        </tr>';

        print "</table>";

        print "</form>";
                
        print dol_fiche_end();
    }
}
    



llxFooter();

$db->close();
