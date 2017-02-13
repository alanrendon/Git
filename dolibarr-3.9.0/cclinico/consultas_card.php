<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
dol_include_once('/cclinico/class/pacientes.class.php');
dol_include_once('/cclinico/class/consultas.class.php');
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("cclinico");

$mesg=''; $error=0; $errors=array();

$action     = (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm    = GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id         = GETPOST('id','int');
$aid         = GETPOST('aid','int');
$tmpcode='';
$facture_con=0;
if ($user->societe_id) $socid=$user->societe_id;

$pacientes = new Pacientes($db);
$consultas = new Consultas($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($consultas->table_element);

//


if ($action == 'edit' && ( $cid>0 || $aid>0) ) {
    if ($cid>0) {
        $consultas->fetch($cid);
    }elseif ($aid>0){
        $consultas->fetch($aid);
    }
    
    $consultas->Ref=mascara_referencia($conf,1);
    
    $consultas->update($user);
    $consultas->cambiar_statut(0,$user);
}

// agregar consulta
if ($action == 'add'){
    $tmpcode=mascara_referencia($conf,0);
    $consultas->Ref                    = $tmpcode;
    $consultas->fk_user_pacientes      = GETPOST("fk_user_pacientes");
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

    if (! $_POST["fk_user_pacientes"] || $_POST["fk_user_pacientes"]== -1 )
    {
        $error++;
        $errors[]="Inserte el paciente asignado a esta consulta";
        $action = 'create';
    }
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
    $extrafields->setOptionalsFromPost($extralabels,$consultas);
    if (! $error)
    {
        $id2 =  $consultas->create($user);
        if ($id2 > 0) {
            $consultas->rowid=$id2;
            $consultas->cambiar_statut(1,$user);
        }
        if ($id2 <= 0)
        {
            $error++; $errors=array_merge($errors,($consultas->error?array($consultas->error):$consultas->errors));
            $action = 'create';
        } else {
            $db->commit();
            $url='consultas_card.php?id='.$consultas->fk_user_pacientes.'&aid='.$id2;
            header("Location: ".$url);
            exit;
        }
    }
}
if ($action == 'add_borrador'){
    $tmpcode=mascara_referencia($conf,1);
    $consultas->Ref                    = $tmpcode;
    $consultas->fk_user_pacientes      = GETPOST("fk_user_pacientes");
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
    

    if (! $_POST["fk_user_pacientes"] || $_POST["fk_user_pacientes"]== -1 )
    {
        $error++;
        $errors[]="Inserte el paciente asignado a esta consulta";
        $action = 'create';
    }
    $extrafields->setOptionalsFromPost($extralabels,$consultas);
    if (! $error)
    {
        $id2 =  $consultas->create($user);
        if ($id2 > 0) {
            $consultas->rowid=$id2;
            $consultas->cambiar_statut(0,$user);
        }
        if ($id2 <= 0)
        {
            $error++; $errors=array_merge($errors,($consultas->error?array($consultas->error):$consultas->errors));
            $action = 'create';
        } else {
            $db->commit();
            $url='consultas_card.php?id='.$consultas->fk_user_pacientes.'&aid='.$id2;
            header("Location: ".$url);
            exit;
        }
    }
}
//guardar borrador
if ($action=='create_update' && !empty($aid)) {
    
    $consultas->fetch($aid);

    if (!isset($_POST["actualizar"])) {
        //$tmpcode=mascara_referencia($conf,1);
        //$consultas->Ref                    = $tmpcode;
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
    $extrafields->setOptionalsFromPost($extralabels,$consultas);
    if ($consultas->statut!=0) {
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
    }
    if (!$error) {
        $result=$consultas->update($user);
        if ($result>0 ) {
           $action='';
        }else{

            $action='edit';
        }
    }else{
        $action='edit';
    }
    
}

if (!empty($_POST["doc"]) && !empty($aid)) {
    $dir_paciente="files/Paciente-".$id;
    if (file_exists (($dir_paciente."/Consulta-".$aid."/".GETPOST("doc") ) )) {
        unlink ($dir_paciente."/Consulta-".$aid."/".GETPOST("doc"));
    }
}
if (!empty($_POST["doc2"]) && !empty($aid)) {
    $dir_paciente="files/Paciente-".$id;
    $f=$dir_paciente."/Consulta-".$aid."/".GETPOST("doc2");
    $fname=GETPOST("doc2");
    if (file_exists (($dir_paciente."/Consulta-".$aid."/".GETPOST("doc2") ) )) {


        header ("Content-Disposition: attachment; filename=".$fname." "); 
        header ("Content-Type: application/octet-stream");
        header ("Content-Length: ".filesize($f));
        readfile($f);
    }

}
if ($action == 'confirm_delete' && $confirm == 'yes' ){
    $consultas->fetch($aid);
    $result = $consultas->delete($user);
    $ret = $extrafields->setOptionalsFromPost($extralabels,$consultas);
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

if ($action == 'facture' && !empty($aid) ){
    $resql1=$db->query("SELECT a.fk_factura FROM llx_facturas_consulta as a WHERE a.statut=1 and a.fk_consulta=".$aid);
    if ($resql1){
        $num2 = $db->num_rows($resql1);
        if ($num2<1)
        {
            $url='factura.php?aid='.$aid."&action=create";
            header("Location: ".$url);
            exit;
        }
    }
}
if ($action == 'confirm_facture' && !empty($aid) ){
    if ($_POST["confirm"]=="yes") {
        $url='factura.php?aid='.$aid."&action=create";
        header("Location: ".$url);
        exit;
    }elseif($_POST["confirm"]=="no"){
        echo "cambio status";
        $action="";
    }
}




llxHeader('', 'Consultas Médicas', '');

$form = new Form($db);
$formcompany = new FormCompany($db);


$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
if ($aid>0) {
    $consultas->fetch($aid);
    $id=$consultas->fk_user_pacientes;
}
$head=array();
if ($id > 0)
{
    // Si edition contact deja existant
    $res=$pacientes->fetch($id, $user);
    if ($res < 0) { dol_print_error($db,$pacientes->error); exit; }
    
    // Show tabs
    $head = pacientes_prepare_head($pacientes);

    $title = "Pacientes";
}
if (!empty($_POST["statut"]) && !empty($aid)) {

    $status=GETPOST("statut");

    if ($status== 1) {
        $res=$consultas->cambiar_statut(3,$user);
        if ($res>0) {
            $action='';
        }
    }
    if ($status== 2) {
        $res=$consultas->cambiar_statut(1,$user);
        if ($res>0) {
            $action='';
        }
    }
}
if (!empty($_POST["fact"]) && $consultas->statut>0) {
    $band=$consultas->cambiar_vinculo(GETPOST("fact"),$user);
    if ($band<0) {
        $error++;
        $errors[] = "Error al borrar el vinculo con la factura";
        setEventMessages($object->error,$object->errors,'errors');
    }
}
if (!empty($_POST["fact2"]) && $consultas->statut>0) {
    $consultas->vincular_consulta_factura(GETPOST("fact2"),$user);
}
$dir_paciente="files/Paciente-".$id;
//crear borrador vacío
if ($action=='create') {
    $tmpcode=mascara_referencia($conf,1);
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
        $dir_consulta=$dir_paciente."/Consulta-".$id2."/";
        dol_mkdir($dir_consulta);
    }
}
//subir archivo al borrador
if ($action=='create_upfile') {
    $tmpcode=mascara_referencia($conf,0);
    //$consultas->Ref                    = $tmpcode;
    if (!isset($_POST["reabrir"])) {
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
    }
    foreach ($_FILES["archivos"]["error"] as $key => $error) {
        if (!empty($_FILES['archivos']['name'][$key])) {
            if ($error == UPLOAD_ERR_OK) {
                $newfile=$dir_paciente."/Consulta-".$aid."/".dol_sanitizeFileName($_FILES['archivos']['name'][$key]);
                if (!is_dir($dir_paciente."/Consulta-".$aid)) {
                    dol_mkdir($dir_paciente."/Consulta-".$aid);
                }
                error_reporting(E_ERROR);
                $result = dol_move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $newfile, 1);
                if ($result == -3) {
                    $error++;
                    $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' contiene caracteres invalidos";
                    setEventMessages($consultas->error,$consultas->errors,'errors');
                    break;
                }elseif (! $result > 0)
                {
                    $error++;
                    $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' es demaciado grande";
                    setEventMessages($consultas->error,$consultas->errors,'errors');
                    break;
                }
            }else{
                $error++;
                $errors[] = "El archivo '".$_FILES['archivos']['name'][$key]."' es demaciado grande";
                setEventMessages($consultas->error,$consultas->errors,'errors');
                break;
            }
        }
    }

    if (isset($_POST["actualizar"]) || isset($_POST["borrador"])) {
        $action='edit';
    }else{
        $action='';
    }
}
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
    $extrafields->setOptionalsFromPost($extralabels,$consultas);
    if (! $error)
    {
        $result = $consultas->update($user);($user);
        if ($result > 0) {
            $consultas->cambiar_statut(1,$user);
            $action = '';
        }
        if ($result <= 0)
        {
            $consultas->Ref=$ref_ant;
            $error++; $errors=array_merge($errors,($consultas->error?array($consultas->error):$consultas->errors));
            $action = 'edit';
        }
    }else{
        $consultas->Ref=$ref_ant;
        $action = 'edit';
    }
}
if ($action == 'facture' && !empty($aid) ){
    $resql1=$db->query("SELECT a.fk_factura FROM llx_facturas_consulta as a WHERE a.fk_consulta=".$aid);
    if ($resql1){
        $num2 = $db->num_rows($resql1);
        if ($num2>0)
        {
            //print $form->formconfirm($_SERVER['PHP_SELF'].'?aid='.$aid,$langs->trans('Code49_8'),$tex,'facture','',$preselectedchoice);
            print $form->formconfirm($_SERVER["PHP_SELF"]."?aid=".$aid,$langs->trans('Code49_8'),$langs->trans('Code49_7'),"confirm_facture",'',0,1);
        }
    }
    $action="";
    /*if (GETPOST("confirm")=="yes") {
        $url='factura.php?aid='.$aid."&action=create";
        header("Location: ".$url);
        exit;
    }elseif(GETPOST("confirm")=="no"){
        echo "cambio status";
    }else{
        $resql1=$db->query("SELECT a.fk_factura FROM llx_facturas_consulta as a WHERE a.fk_consulta=".$aid);
        if ($resql1){
            $num2 = $db->num_rows($resql1);
            if ($num2>0)
            {
                //print $form->formconfirm($_SERVER['PHP_SELF'].'?aid='.$aid,$langs->trans('Code49_8'),$tex,'facture','',$preselectedchoice);
                print $form->formconfirm($_SERVER["PHP_SELF"]."?aid=".$aid,$langs->trans('Code49_8'),$langs->trans('Code49_7'),"confirm_delete",'',0,1);

            }
        }
        if ($facture_con==0 ) {
            $url='factura.php?aid='.$aid."&action=create";
            header("Location: ".$url);
            exit;
        }
    }*/
    
}


//borrar consulta 
if ($action == 'delete')
{
    print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$id."&aid=".$aid,"Eliminar Antecedente","¿Está seguro de querer eliminar este antecedente y toda su información inherente?","confirm_delete",'',0,1);
}

if ($action == 'create')
{
    
    dol_htmloutput_errors("",$errors);

    $consultas->date_consultation=(empty($consultas->date_consultation)?dol_now():$consultas->date_consultation);
    dol_fiche_head($head, 'perso', $langs->trans("Code13"), 0, 'contact');

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
                           $("#action").val("add");
                           $("#form").submit();
                        });
                        jQuery("#create2").click(function (){
                           $("#action").val("add_borrador");
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
                        jQuery("#eliminar").click(function (){
                           $("#action").val("delete");
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
    print '<table class="border" style="width:1024px !important;">';
    print '
    <tr>
        <td  >
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code14").'</b></label>
        </td>
        <td  colspan="4" >';
            print $consultas->select_dolpacientes($consultas->fk_user_pacientes, 'fk_user_pacientes', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '
        </td>';
        print '<td  colspan="2" align="right" >  <b>'.$langs->trans("Code22").':</b>
        </td><td>';

        print '<b>'.(empty($consultas->Ref)?dol_escape_htmltag(str_replace("C","",$tmpcode)):str_replace("C","",$consultas->Ref) ).'</b>';

    print '</td>
    </tr>';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;Fecha de Consulta:</label>
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
            <label for="descripcion">&nbsp;'.$langs->trans("Code18").'</label>
        </td>
        <td  >
            <input name="blood_pressure" id="blood_pressure" type="text" size="2" maxlength="20" value="'.$consultas->blood_pressure.'">
        </td>
    </tr>
    ';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;<b>'.$langs->trans("Code20").'</b></label>
        </td>
        <td  >';
            print $consultas->select_dol($consultas->reason,'c_motivo_consulta' ,'reason', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '
        </td>
        <td  > <b>'.$langs->trans("Code21").'</b>
        </td>
        <td  colspan="2" >';
         print $consultas->select_dolusers((empty($consultas->fk_user_med)?$pacientes->fk_user:$consultas->fk_user_med), 'fk_user_med', 1, '', $disable2, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
         print '
        </td>
        <td></td>
        <td  >
            <label for="descripcion">&nbsp;Temperatura</label>
        </td>
        <td >
            <input name="temperature" id="temperature" type="text" size="2" maxlength="10" value="'.$consultas->temperature.'">
        </td>
    </tr>

    ';
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
    if (! empty($extrafields->attribute_label))
    {
        $param=array("colspan"=>"8");
        print $consultas->showOptionals($extrafields,'edit',$param);
    }
    print '
    <tr>
        <td colspan="3" >
            
        </td>
        <td colspan="5" align="right" >';
            
                print '
                <input  class="button" value="'.$langs->trans("Code27").'" name="create2" id="create2" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >';

            if ($user->rights->cclinico->validar) {
                print '
                <input class="button" value="'.$langs->trans("Code28").'" name="create" id="create" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >';
            }
                print '
                <a class="button" href="'.DOL_URL_ROOT.'/cclinico/Index.php?action=create" name="cancel" type="submit" style="text-decoration:none; border-radius: 0px; font-size:11px !important;">'.$langs->trans("Code29").'</a>
            ';
        print '
        </td>
    </tr>';

    print "</table>";

    print "</form>";
            
    print dol_fiche_end();
}

//crear nueva consulta siempre y cuando exista un paciente
if ($action == 'edit' && ! empty($aid))
{
    
    dol_htmloutput_errors("",$errors);

    $consultas->date_consultation=(empty($consultas->date_consultation)?dol_now():$consultas->date_consultation);
    dol_fiche_head($head, 'perso', $langs->trans("Code13"), 0, 'contact');

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
                        jQuery(".down_consultas").click(function (){
                           $("#doc2").val($(this).attr("doc"));
                           $("#action").val("create_upfile");
                           $("#form").submit();
                        });
                        jQuery("#borrador").click(function (){
                           $("#action").val("create_update");
                           $("#form").submit();
                        });
                        jQuery("#eliminar").click(function (){
                           $("#action").val("delete");
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
    print '<table class="border" style="width:1024px !important;">';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;<b>'.$langs->trans("Code14").'</b></label>
        </td>
        <td  colspan="4"  >';
            print $pacientes->getNomUrl(1);
    print '
        </td>';

    print '<td  colspan="2" align="right" >  <b>'.$langs->trans("Code22").':</b>
        </td><td>';

        print '<b>'.(empty($consultas->Ref)?dol_escape_htmltag(str_replace("C","",$tmpcode)):str_replace("C","",$consultas->Ref) ).'</b>';

    print '</td>
    </tr>';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;Fecha de Consulta:</label>
        </td>
        <td  >';
            $form->select_date($consultas->date_consultation,'date_consultation',1,1,1,"form_date",1,1,0,0,'fulldaystart');
    print '
        </td>
        <td  >
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code16").'</b></label>
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
            <label for="descripcion">&nbsp;'.$langs->trans("Code18").'</label>
        </td>
        <td  >
            <input name="blood_pressure" id="blood_pressure" type="text" size="2" maxlength="20" value="'.$consultas->blood_pressure.'">
        </td>
    </tr>';
    print '
    <tr>
        <td  >
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code20").'</b></label>
        </td>
        <td  >';
            print $consultas->select_dol($consultas->reason,'c_motivo_consulta' ,'reason', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '
        </td>
        <td  > <b>'.$langs->trans("Code21").'</b>
        </td>
        <td  colspan="2" >';
         print $consultas->select_dolusers((empty($consultas->fk_user_med)?$pacientes->fk_user:$consultas->fk_user_med), 'fk_user_med', 1, '', $disable2, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '
        </td>
        <td></td>
        <td  >
            <label for="descripcion">&nbsp;Temperatura</label>
        </td>
        <td colspan=7 >
            <input name="temperature" id="temperature" type="text" size="2" maxlength="10" value="'.$consultas->temperature.'">
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
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code24").'</b></label>
        </td>
        <td  colspan="5" >
            <textarea class="flat" name="treatments" data-limit-rows="true" id="treatments" style="width:482px !important;" rows="9">'.$consultas->treatments.'</textarea>
        </td>
    </tr>';
     print '
    <tr>
        <td  >
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code25").'</b></label>
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
            <label for="descripcion">&nbsp;Otros comentarios:</label>
        </td>
        <td  colspan="5" >
            <textarea class="flat" data-limit-rows="true" name="comments" id="comments" style="width:482px !important;" rows="9">'.$consultas->comments.'</textarea>
        </td>
    </tr>';
    if (! empty($extrafields->attribute_label))
    {
        $param=array("colspan"=>"8");
        print $consultas->showOptionals($extrafields,'edit',$param);

    }
    print '
    <tr><br>
        <td   colspan="2" >
            <label for="descripcion">&nbsp;'.$langs->trans("Code49_2").':</label>
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
        <td colspan="5" align="right" >';
            if ($consultas->statut==0) {
                print '
                    <input class="button" value="'.$langs->trans("Code27").'" name="borrador" id="borrador" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >';

                if ($user->rights->cclinico->crear) {
                    print '
                    <input class="button" value="'.$langs->trans("Code28").'" name="create" id="create" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >';
                }
                if ($user->rights->cclinico->eliminar) {
                    print '
                        <a class="button" href="'.DOL_URL_ROOT.'/cclinico/consultas_card.php?id='.$id.'&aid='.$aid.'" name="cancel" type="submit" style="text-decoration:none; border-radius: 0px; font-size:11px !important;">'.$langs->trans("Code29").'</a>
                    ';
                }
            }else{
                print '
                    <input class="button" value="Actualizar" name="actualizar" id="borrador" style="text-decoration:none; border-radius: 0px; font-size:11px !important;" >
                    <a class="button" href="'.DOL_URL_ROOT.'/cclinico/consultas_card.php?id='.$id.'&aid='.$aid.'" name="cancel" type="submit" style="text-decoration:none; border-radius: 0px; font-size:11px !important;">'.$langs->trans("Code29").'</a>
                ';
            }
        print '
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
                                print "
                                    <td>
                                        <a href='".$dir_paciente."/Consulta-".$aid."/".$archivo."' download='".$archivo."'>
                                            <img src='img/file.png' doc='".$archivo."' ></img>
                                        </a>
                                    </td>";
                                 if ($user->rights->cclinico->crear){
                                    print "<td><img src='img/delete.png' doc='".$archivo."' class='delete_consultas'></img></td>";
                                 }else{
                                    print "<td></td>";
                                 }
                                

                            print '</tr>';
                        }
                    }
                }else{
                    print '<tr><td colspan="4">Ningun archivo vinculado</td></tr>';
                }
            print '
            </table><br>
        </td>
        ';
        
    print '
    </tr>';

    print "</table>";

    print "</form>";
            
    print dol_fiche_end();
}
elseif ($action != 'edit' && $action != 'create' && ! empty($id) && ! empty($aid)){
    dol_htmloutput_errors("",$errors);

    $consultas->date_consultation=(empty($consultas->date_consultation)?dol_now():$consultas->date_consultation);
    dol_fiche_head($head, 'perso', $langs->trans("Code13"), 0, 'contact');

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
                        jQuery("#eliminar").click(function (){
                           $("#action").val("delete");
                           $("#form").submit();
                        });
                        jQuery("#create").click(function (){
                           $("#action").val("create_consulta");
                           $("#form").submit();
                        });
                        jQuery("#gratuita").click(function (){
                           $("#statut").val("1");
                           $("#form").submit();
                        });
                        jQuery("#factura").click(function (){
                           $("#action").val("facture");
                           $("#form").submit();
                        });
                        jQuery("#reabrir").click(function (){
                           $("#statut").val("2");
                           $("#form").submit();
                        });
                        jQuery(".down_consultas").click(function (){
                           $("#doc2").val($(this).attr("doc"));
                           $("#action").val("create_upfile");
                           $("#form").submit();
                        });
                        jQuery("#borrador_modificar").click(function (){
                           $("#action").val("edit");
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
                        jQuery(".delete_relacion").click(function (){
                            var id=$(this).attr("var");
                            $("#fact").val(id);
                            $("#form").submit();
                        });
                        jQuery("#c_fact").click(function (){
                            var id=$("#fac_replacement").val();
                            $("#fact2").val(id);
                            $("#form").submit();
                        });
                        
                    })'."\n";
            print '</script>'."\n";

    print '<form style="font-size:11px !important;" method="post" enctype="multipart/form-data" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" id="action" name="action" value="'.$action.'">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    print '<input type="hidden" name="aid" value="'.$aid.'">';
    print '<input type="hidden" name="statut" id="statut" value="">';
    print '<input type="hidden" name="fact" id="fact" value="">';
    print '<input type="hidden" id="doc" name="doc" value="">';
    print '<input type="hidden" id="doc2" name="doc2" value="">';
    print '<input type="hidden" id="fact2" name="fact2" value="">';
    print '<table class="border" style="width:1024px !important;">';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code14").'</label>
        </td>
        <td  >';
            print $pacientes->getNomUrl(1);
    print '
        </td>';
        print '
        <td  colspan="2" align="right" >  <b>'.$langs->trans("Code22").'</b></td>
            <td>';
        print (empty($consultas->Ref)?dol_escape_htmltag(str_replace("C","",$tmpcode)):str_replace("C","",$consultas->Ref ));

    print '</td>';
        if ($consultas->statut==0) {
            print '
            <td colspan=3 align="center" >Estatus:&nbsp;&nbsp; <img src="../theme/eldy/img/statut0.png" border="0" alt="" title="Borrador (a validar)">&nbsp;&nbsp; Borrador</td>
            ';
        }
        if ($consultas->statut==1) {
            print '
            <td colspan=3 align="center" >Estatus:&nbsp;&nbsp; <img src="../theme/eldy/img/statut4.png" border="0" alt="" title="Activado" >&nbsp;&nbsp; Validado</td>
            ';
        }
        if ($consultas->statut==2) {
            print '
            <td colspan=3 align="center" >Estatus:&nbsp;&nbsp; <img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Facturada" >&nbsp;&nbsp; (cerrado) Facturada</td>
            ';
        }
        if ($consultas->statut==3) {
            print '
            <td colspan=3 align="center" >Estatus:&nbsp;&nbsp; <img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Activado" >&nbsp;&nbsp; cerrada(consulta gratuita)</td>
            ';
        }
    print '
    </tr>
    <tr>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code15").'</label>
        </td>
        <td  >';
           print date("d/m/Y H:s",strtotime($consultas->date_consultation));
    print '
        </td>
        <td  >
            <label for="descripcion">&nbsp;<b>'.$langs->trans("Code16").'</b></label>
        </td>
        <td  >';
            print $consultas->select_dol($consultas->Type_consultation, 'c_tipo_consulta' ,'Type_consultation', 1, '', 1, '', 0, $conf->entity, 0, 1, '', 0, '', 'maxwidth300');
    print '
        </td>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code17").'</label>
        </td>
        <td  >
            <input disabled name="weight" id="weight" type="text" size="2" maxlength="20" value="'.$consultas->weight.'">
        </td>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code18").'</label>
        </td>
        <td  >
            <input disabled name="blood_pressure" id="blood_pressure" type="text" size="2" maxlength="20" value="'.$consultas->blood_pressure.'">
        </td>
    </tr>';
    print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;<b>'.$langs->trans("Code20").'</b></label>
        </td>
        <td  >';
            print $consultas->select_dol($consultas->reason,'c_motivo_consulta' ,'reason', 1, '', 1, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '
        </td>
        <td  > <b>'.$langs->trans("Code21").'</b>
        </td>
        <td  colspan="3" >';

         if (!empty($consultas->fk_user_med)) {
            $medico_prv= new User($db);

            $medico_prv->fetch($consultas->fk_user_med); 
            print $medico_prv->getNomUrl(1);
         }else{
            print 'N/A';
         }
         
    print '
        </td>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code19").'</label>
        </td>
        <td colspan=7 >
            <input disabled name="temperature" id="temperature" type="text" size="2" maxlength="10" value="'.$consultas->temperature.'">
        </td>
    </tr>';
     print '
    <tr>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code23").'</label>
        </td>
        <td  >
            <textarea disabled class="flat" name="reason_detail" id="reason_detail" style="width:327px !important;"  rows="9">'.$consultas->reason_detail.'</textarea>
        </td>
        <td  >
            <label for="descripcion">&nbsp;<b>'.$langs->trans("Code24").'</b></label>
        </td>
        <td  colspan="5" >
            <textarea disabled class="flat" name="treatments" data-limit-rows="true" id="treatments" style="width:482px !important;" rows="9">'.$consultas->treatments.'</textarea>
        </td>

    </tr>
    <tr>
        <td  >
            <label for="descripcion"><b>&nbsp;'.$langs->trans("Code25").'</b></label>
        </td>
        <td  colspan="8" >';
            print $consultas->select_dol($consultas->diagnostics,'c_tipo_diagnostico' ,'diagnostics', 1, '', 1, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
    print '
        </td>
    </tr>
    <tr>
        <td  >
            <label for="descripcion">&nbsp;'.$langs->trans("Code26").'</label>
        </td>
        <td  >
            <textarea disabled class="flat" name="diagnostics_detail" id="diagnostics_detail" style="width:327px !important;" rows="9">'.$consultas->diagnostics_detail.'</textarea>
        </td>
        <td  >
            <label for="descripcion">&nbsp;Otros comentarios:</label>
        </td>
        <td  colspan="5" >
            <textarea disabled class="flat" data-limit-rows="true" name="comments" id="comments" style="width:482px !important;" rows="9">'.$consultas->comments.'</textarea>
        </td>
    </tr>';
    if (! empty($extrafields->attribute_label))
    {
        $param=array("style"=>"class='trans'","colspan"=>"8");
        print $consultas->showOptionals($extrafields,'edit',$param);
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
        <td colspan="2" >
            <input type="file" multiple="multiple" name="archivos[]" />
            <input class="button" value="'.$langs->trans("Code49_3").'" id="files" style="text-decoration:none; border-radius: 0px; font-size:11px !important;">
        </td>
        <td colspan="6" align="right" >';
            if ($consultas->statut==0) {
                if ($user->rights->cclinico->crear) {
                    print '
                    <input class="button" value="Modificar Borrador" id="borrador_modificar" style=" border-radius: 0px; font-size:11px !important;" >';
                }
                if ($user->rights->cclinico->eliminar) {
                    print '<input class="button" value="Eliminar" id="eliminar" style=" border-radius: 0px; color:red; font-size:11px !important;" >';
                }
            }
            if ($consultas->statut==1) {
                if ($user->rights->cclinico->crear) {
                    print '
                    <input size="8" class="button" value="Modificar" id="borrador_modificar" style=" border-radius: 0px; font-size:11px !important;" >';
                    
                }

                if ($user->rights->cclinico->validar) {
                print '
                    <input size="13" class="button" value="Consulta Gratuita" id="gratuita" name="gratuita" style=" border-radius: 0px; font-size:11px !important;" >
                    <input size="13" class="button" value="'.$langs->trans("Code49_6").'" id="factura" name="factura" style=" border-radius: 0px; font-size:11px !important;" >';
                }
                

                if ($user->rights->cclinico->eliminar) {
                    print '
                    <input size="8"  class="button" value="Eliminar" id="eliminar" style=" border-radius: 0px; color:red; font-size:11px !important;" > ';
                }
            }
            if ($consultas->statut==2) {
                print '
                <input size="13" class="button" value="Reabrir" name="reabrir" id="reabrir" style=" border-radius: 0px; font-size:11px !important;">
                ';
                if ($user->rights->cclinico->crear) {
                    print '<a size="13" class="button" style="margin-left:10px; margin-right:10px;  text-decoration:none; border-radius: 0px; font-size:11px !important;" target="_blank"  href="pdf_formatos.php?id='.$aid.'"  >'.$langs->trans("Code49_5").'</a>';
                }
                if ($user->rights->cclinico->eliminar) {
                    print '<input size="8" class="button" style=" border-radius: 0px; color:red; font-size:11px !important;" value="Eliminar" name="eliminar" id="eliminar" >';
                }
            }
            if ($consultas->statut==3) {
                print '
                <input size="13" class="button" value="Reabrir" name="reabrir" id="reabrir" style=" border-radius: 0px; font-size:11px !important;">
                ';
                if ($user->rights->cclinico->crear) {
                    print '<a size="13" class="button" style="margin-left:10px; margin-right:10px;  text-decoration:none; border-radius: 0px; font-size:11px !important;" target="_blank"  href="pdf_formatos.php?id='.$aid.'"  >Generar Receta</a>';
                }
                if ($user->rights->cclinico->eliminar) {
                    print '<input size="8" class="button" style=" border-radius: 0px; color:red; font-size:11px !important;" value="Eliminar" name="eliminar" id="eliminar" >';
                }
            }
            
        print '
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
                                </a>
                                </td>";
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
        </td>';
        if ($facture_con==1)
        {
            $preselectedchoice='yes';

            print '<td colspan=6>';
            $tex=$langs->trans('Code49_7');
            print $form->formconfirm($_SERVER['PHP_SELF'].'?aid='.$aid,$langs->trans('Code49_8'),$tex,'facture','',$preselectedchoice);
            print '</td>';
        }
    print '
    </tr>';
   
        print '
        <tr>
            <td   colspan="2" >
                <label for="descripcion">&nbsp;'.$langs->trans("Code29_1").'</label>
            </td>
        </tr>';
        $facturestatic = new Facture($db);
        $facids = $consultas->list_replacable_invoices(); 
        foreach ($facids as $facparam)
        {
            $options .= '<option value="' . $facparam ['id'] . '"';
            if ($facparam ['id'] == $_POST['fac_replacement'])
                $options .= ' selected';
            $options .= '>' . $facparam ['ref'];
            $options .= ' (' .$facturestatic->LibStatut(0, $facparam ['status']). ')';
            $options .= '</option>';
        }
        require_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
        print ajax_combobox('fac_replacement');
        print '
        <tr>
            <td colspan="2">';
            $text .= '<select class="flat" name="fac_replacement" id="fac_replacement"';
            if (! $options)
                $text .= ' disabled';
            $text .= '>';
            if ($options) {
                $text .= '<option value="-1">&nbsp;</option>';
                $text .= $options;
            } else {
                $text .= '<option value="-1">' . $langs->trans("NoReplacableInvoice") . '</option>';
            }
            $text .= '</select> <input style="font-size:11px !important;" class="button" value="'.$langs->trans("Code29_2").'" id="c_fact">';
            print $text;
        print ' 
            </td>
        </tr>';


        print '
    <tr>
        <td colspan="2" ><br>
            <table class="border" width="100%">
                <tr class="liste_titre">
                    <td  >
                        '.$langs->trans("Code29_3").'
                    </td>
                    <td  >
                        '.$langs->trans("Code29_4").'
                    </td>
                    <td  >
                        '.$langs->trans("Code29_5").'
                    </td>
                    <td align="center" >
                        '.$langs->trans("Code8").'
                    </td>
                    <td  >
                    </td>
                </tr>';
                $temp=$consultas->listar_facturas();
                if (count($temp) >0) {
                    $total=0;
                    foreach ($temp as $key) {
                        $facturestatic=new Facture($db);
                        $facturestatic->fetch($key->rowid);
                        print '
                        <tr>
                            <td >
                                ';
                                print $facturestatic->getNomUrl(1,'',200,0);
                            print '
                            </td>
                            <td >
                                '.$key->datef.'
                            </td>
                            <td >
                                '.sprintf("%.2f",$key->total_ht).'
                            </td>';
                            print '<td align="center" >';
                                if ($key->fk_statut==1) {
                                    print '<img src="../theme/eldy/img/statut1.png" border="0" alt="" title="Pendiente de pago">';
                                }
                                if ($key->fk_statut==2) {
                                    print '<img src="../theme/eldy/img/statut6.png" border="0" alt="" title="Pagada">';
                                }
                                if ($key->fk_statut==0) {
                                    print '<img src="../theme/eldy/img/statut0.png" border="0" alt="" title="Borrador (a validar)">';
                                }
                            print "
                            </td>
                            <td class='delete_relacion' var=".$key->id." align='center' style='cursor:pointer;' title='Borrar Relación' ><img src='img/delete.png' doc='' >
                            </td>
                            ";
                        print '
                        </tr>';
                        $total=$total+$key->total_ht;
                    }
                    print '<tr>';
                        print '<td colspan=2>Base Imponible</td>';
                        print '<td colspan=4>'.sprintf("%.2f",$total).'</td>';
                    print '</tr>';
                }else{
                    print '<tr>';
                        print '<td colspan=6>Ninguna Factura Vinculada</td>';
                    print '</tr>';
                }
            print '</table>';
        print '</td></tr>';

    

    print "</table>";
    

    print "</form>";
            
    print dol_fiche_end();
}

    



llxFooter();

$db->close();
