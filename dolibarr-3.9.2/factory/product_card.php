<?php
/* Copyright (C) 2001-2007  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2005       Eric Seigne             <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2015  Regis Houssin           <regis.houssin@capnetworks.com>
 * Copyright (C) 2006       Andre Cianfarani        <acianfa@free.fr>
 * Copyright (C) 2006       Auguria SARL            <info@auguria.org>
 * Copyright (C) 2010-2015  Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2013-2014  Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2012-2013  Cédric Salvador         <csalvador@gpcsolutions.fr>
 * Copyright (C) 2011-2015  Alexandre Spangaro      <aspangaro.dolibarr@gmail.com>
 * Copyright (C) 2014       Cédric Gross            <c.gross@kreiz-it.fr>
 * Copyright (C) 2014-2015  Ferran Marcet           <fmarcet@2byte.es>
 * Copyright (C) 2015       Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2015       Raphaël Doursenaud      <rdoursenaud@gpcsolutions.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/product/card.php
 *  \ingroup    product
 *  \brief      Page to show product
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT."/factory/class/factory.class.php";
if (! empty($conf->propal->enabled))   require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->facture->enabled))  require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
if (! empty($conf->commande->enabled)) require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

$langs->load("products");
$langs->load("other");
if (! empty($conf->stock->enabled)) $langs->load("stocks");
if (! empty($conf->facture->enabled)) $langs->load("bills");
if (! empty($conf->productbatch->enabled)) $langs->load("productbatch");

$mesg=''; $error=0; $errors=array();

$refalreadyexists=0;

$id=GETPOST('id', 'int');

$ref=GETPOST('ref', 'alpha');
$type=GETPOST('type','int');
$action=(GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm','alpha');
$socid=GETPOST('socid','int');
$duration_value = GETPOST('duration_value');
$duration_unit = GETPOST('duration_unit');
if (! empty($user->societe_id)) $socid=$user->societe_id;

$object = new Product($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

$object = new Product($db);
$factory = new Factory($db);
$productid=0;
if ($id || $ref)
{
    $result = $object->fetch($id,$ref);
    $sql="SELECT a.tratamiento FROM llx_product as a WHERE a.rowid=".$id;
    $res=$db->query($sql);
    if ($res) {
        $res=$db->fetch_object($res);
        $object->tratamiento=$res->tratamiento;
    }
    
    $productid=$object->id;
    $id=$object->id;
    $factory->id =$id;
}


// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$canvas = !empty($object->canvas)?$object->canvas:GETPOST("canvas");
$objcanvas=null;
if (! empty($canvas))
{
    require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
    $objcanvas = new Canvas($db,$action);
    $objcanvas->getCanvas('product','card',$canvas);
}

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result=restrictedArea($user,'produit|service',$fieldvalue,'product&product','','',$fieldtype,$objcanvas);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productcard','globalcard'));



/*
 * Actions
 */

if ($cancel) $action = '';

$createbarcode=empty($conf->barcode->enabled)?0:1;
if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->creer_advance)) $createbarcode=0;

$parameters=array('id'=>$id, 'ref'=>$ref, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{

    if ($action == 'add' && ($user->rights->produit->creer || $user->rights->service->creer))
    {
        $error=0;
        $object->tratamiento           = GETPOST('tratamiento');

        if ($object->tratamiento=="on") {
            $object->tratamiento=1;
        }else{
            $object->tratamiento=0;
        }


        if (! GETPOST('label'))
        {
            setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Label')), null, 'errors');
            $action = "create";
            $error++;
        }
        if (empty($ref))
        {
            setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Ref')), null, 'errors');
            $action = "create";
            $error++;
        }
        
        if (! $error)
        {
            $units = GETPOST('units', 'int');

            $object->ref                   = $ref;
            $object->label                 = GETPOST('label');
            

            $object->status                = 1;
            $object->status_buy            = 0;

            // Set barcode_type_xxx from barcode_type id
            $stdobject=new GenericObject($db);
            $stdobject->element='product';
            $stdobject->barcode_type=GETPOST('fk_barcode_type');
            $result=$stdobject->fetch_barcode();
            if ($result < 0)
            {
                $error++;
                $mesg='Failed to get bar code type information ';
                setEventMessages($mesg.$stdobject->error, $mesg.$stdobject->errors, 'errors');
            }


            // Fill array 'array_options' with data from add form
            $ret = $extrafields->setOptionalsFromPost($extralabels,$object);
            if ($ret < 0) $error++;

            if (! $error)
            {
                $id = $object->create($user);
            }

            if ($id > 0)
            {
                // Category association

                $sql="UPDATE llx_product as a SET a.tratamiento=".$object->tratamiento." WHERE a.rowid=".$id;
                $db->query($sql);


                $categories = GETPOST('categories');
                $object->setCategories($categories);

                header("Location: ".$_SERVER['PHP_SELF']."?action=edit&id=".$id);
                exit;
            }
            else
            {
                if (count($object->errors)) setEventMessages($object->error, $object->errors, 'errors');
                else setEventMessages($langs->trans($object->error), null, 'errors');
                $action = "create";
            }
        }
    }



    // Update a product or service
    if ($action == 'update' )
    {
        if (GETPOST('cancel'))
        {
            $action = '';
        }
        else
        {
            if ($object->id > 0)
            {
                $object->oldcopy= clone $object;

                $object->ref                    = $ref;
                $object->label                  = GETPOST('label');

                $object->tratamiento           = GETPOST('tratamiento');
                if ($object->tratamiento=="on") {
                    $object->tratamiento=1;
                }else{
                    $object->tratamiento=0;
                }
                $object->description            = dol_htmlcleanlastbr(GETPOST('desc'));
                $object->url                    = GETPOST('url');
                $object->note                   = dol_htmlcleanlastbr(GETPOST('note'));
                $object->customcode             = GETPOST('customcode');
                $object->country_id             = GETPOST('country_id');
                $object->status                 = GETPOST('statut');
                $object->status_buy             = GETPOST('statut_buy');
                $object->status_batch           = GETPOST('status_batch');
                // removed from update view so GETPOST always empty
                /*
                $object->seuil_stock_alerte     = GETPOST('seuil_stock_alerte');
                $object->desiredstock           = GETPOST('desiredstock');
                */
                $object->duration_value         = GETPOST('duration_value');
                $object->duration_unit          = GETPOST('duration_unit');

                $object->canvas                 = GETPOST('canvas');
                $object->weight                 = GETPOST('weight');
                $object->weight_units           = GETPOST('weight_units');
                $object->length                 = GETPOST('size');
                $object->length_units           = GETPOST('size_units');
                $object->surface                = GETPOST('surface');
                $object->surface_units          = GETPOST('surface_units');
                $object->volume                 = GETPOST('volume');
                $object->volume_units           = GETPOST('volume_units');
                $object->finished               = GETPOST('finished');

                $units = GETPOST('units', 'int');

                if ($units > 0) {
                    $object->fk_unit = $units;
                } else {
                    $object->fk_unit = null;
                }

                $object->barcode_type           = GETPOST('fk_barcode_type');
                $object->barcode                = GETPOST('barcode');
                // Set barcode_type_xxx from barcode_type id
                $stdobject=new GenericObject($db);
                $stdobject->element='product';
                $stdobject->barcode_type=GETPOST('fk_barcode_type');
                $result=$stdobject->fetch_barcode();
                if ($result < 0)
                {
                    $error++;
                    $mesg='Failed to get bar code type information ';
                    setEventMessages($mesg.$stdobject->error, $mesg.$stdobject->errors, 'errors');
                }
                $object->barcode_type_code      = $stdobject->barcode_type_code;
                $object->barcode_type_coder     = $stdobject->barcode_type_coder;
                $object->barcode_type_label     = $stdobject->barcode_type_label;

                $object->accountancy_code_sell  = GETPOST('accountancy_code_sell');
                $object->accountancy_code_buy   = GETPOST('accountancy_code_buy');

                // Fill array 'array_options' with data from add form
                $ret = $extrafields->setOptionalsFromPost($extralabels,$object);
                if ($ret < 0) $error++;

                if (! $error && $object->check())
                {
                    if ($object->update($object->id, $user) > 0)
                    {
                        $sql="UPDATE llx_product as a SET a.tratamiento=".$object->tratamiento." WHERE a.rowid=".$object->id;
                        $db->query($sql);
                        // Category association
                        $categories = GETPOST('categories');
                        $object->setCategories($categories);
                        setEventMessages("Elemento Actualizado", "");
                        $action = 'edit';
                    }
                    else
                    {
                        if (count($object->errors)) setEventMessages($object->error, $object->errors, 'errors');
                        else setEventMessages($langs->trans($object->error), null, 'errors');
                        $action = 'edit';
                    }
                }
                else
                {
                    if (count($object->errors)) setEventMessages($object->error, $object->errors, 'errors');
                    else setEventMessages($langs->trans("ErrorProductBadRefOrLabel"), null, 'errors');
                    $action = 'edit';
                }
            }

        }
    }

    if ($action=='add_line') {

        $id_line=GETPOST("id_line");
        if ($_REQUEST["id_line2"]>0) {
            $id_line=GETPOST("id_line2");
        }


        $cantidad=1;

        if ($id_line>0) {
            if ($cantidad>0) {
                $producto2= new Product($db);
                $producto2->fetch($id_line);
                $pmp2=$producto2->pmp;
                $price2=$producto2->price;

                if($factory->add_component($id,$id_line , $cantidad, $pmp2, $price2, 0)){
                    

                    $factory->change_defaultPrice($id);
                    setEventMessages("Elemento cargado satisfactoriamente", "");
                    header("Location: ".$_SERVER["PHP_SELF"].'?action=edit&id='.$object->id);
                    exit;
                    
                }
                else
                {
                    setEventMessages($langs->trans("ErrorAssociationIsFatherOfThis"), "", 'errors');
                    $action = 'edit';
                }
            }else{
                setEventMessages("Ingrese una cantidad valida", "", 'errors');
                $action = 'edit';
            }
            
        }else{
            setEventMessages("Ingrese un elemento a la integración valido", "", 'errors');
            $action = 'edit';
        }
        
    }

    if ($action=='del_line') {

        $i=0;
        foreach ($_POST as $key => $value) {
            if ($_POST["prod_id_chk".$i]) {
                if ($factory->del_component($id, $_POST["prod_id_chk".$i]) > 0)
                {
                    $ban=1;
                    $factory->change_defaultPrice($id);
                    
                }
            }
            $i++;
        }
        if ($ban==1) {
            setEventMessages("Elemento eliminado satisfactoriamente","");
            header("Location: ".$_SERVER["PHP_SELF"].'?action=edit&id='.$object->id);
            exit;
        }else{
            $mesg=$product->error;
            setEventMessages($mesg, "", 'errors');
            $action = 'edit';
        }

        
    }

}

function select_dol_products($selected='', $htmlname='prod', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
{
    global $conf,$user,$langs,$db;



    $out='';

    // On recherche les utilisateurs
    $sql = "SELECT DISTINCT u.rowid, u.ref, u.label,u.price_ttc,u.tva_tx,u.duration,u.fk_product_type";
    /*if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
    {
        $sql.= ", e.label";
    }*/
    $sql.= " FROM ".MAIN_DB_PREFIX ."product as u  ";

    if (!empty($exclude)) {
        $sql.=$exclude;
    }

    $sql.= " ORDER BY u.ref ASC";

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

            $out.= '<select class="flat minwidth200'.($morecss?' '.$morecss:'').'" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
            if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
            if ($show_every) $out.= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

            $userstatic=new User($db);

            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);

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

                $out.= $obj->ref." - ".$obj->label." - $".price($obj->price_ttc,0,'',0,0,0);

                if (empty($obj->duration)) {
                    if ($obj->tva_tx>0) {
                        $out.=" ".$langs->trans("TTC");
                    }else{
                        $out.=" ".$langs->trans("HT");
                    }
                }else{
                    $our_value=substr($obj->duration,0,dol_strlen($obj->duration)-1);
                    $outdurationunit=substr($obj->duration,-1);
                    
                    $da=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
                    if (isset($da[$outdurationunit]))
                    {
                        $out.= " - ".$our_value." ".$langs->trans($da[$outdurationunit]);
                    }
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
    }
    else
    {
        dol_print_error($db);
    }
    return $out;
}



/*
 * View
 */

$helpurl='';
if (GETPOST("type") == '0' || ($object->type == Product::TYPE_PRODUCT)) $helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
if (GETPOST("type") == '1' || ($object->type == Product::TYPE_SERVICE)) $helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';

if (isset($_GET['type'])) $title = $langs->trans('CardProduct'.GETPOST('type'));
else $title = $langs->trans('ProductServiceCard');

llxHeader('', $title, $helpurl);

$form = new Form($db);
$formproduct = new FormProduct($db);
    $head=product_prepare_head($object);
    

    if ($action == 'view' && ($user->rights->produit->creer || $user->rights->service->creer)){
        $titre=$langs->trans("Edición Rápida");
        $picto=($object->type== Product::TYPE_SERVICE?'service':'product');

        print_fiche_titre($titre);
        dol_fiche_head();
            print '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">'."\n";
                 print '<input type="hidden" name="action" value="edit">';
                
                print '<b>Producto:</b>'.select_dol_products($id, 'id',1);
                print '<br><br><input type="submit" class="button" value="Cargar Producto">';
            print '</form>';    
        dol_fiche_end();
    }

    if ($action == 'create'  && ($user->rights->produit->creer || $user->rights->service->creer))
    {
        //WYSIWYG Editor
        print '<script type="text/javascript" language="javascript">
        jQuery(document).ready(function() {
            $("#ref").select();
            $("#ref").keydown (function(e) {
                if (e.which==40) {
                    $("#label").select();
                }
            });  
            $("#label").keydown (function(e) {
                if (e.which==38) {
                    $("#ref").select();
                }
                if (e.which==40) {
                    $("#options_anc").select();
                }
            }); 
            $("#options_anc").keydown (function(e) {
                if (e.which==38) {
                    $("#label").select();
                }
                if (e.which==40) {
                    $("#options_lar").select();
                }
            });
            $("#options_lar").keydown (function(e) {
                if (e.which==38) {
                    $("#options_anc").select();
                }
                if (e.which==40) {
                    $("#options_total_prod").select();
                }
            });
            $("#options_total_prod").keydown (function(e) {
                if (e.which==38) {
                    $("#options_lar").select();
                }
                if (e.which==40) {
                    $("#options_hmcnc").select();
                }
            }); 

            $("#options_hmcnc").keydown (function(e) {
                if (e.which==38) {
                    $("#options_total_prod").select();
                }
                if (e.which==40) {
                    $("#options_hecnc").select();
                }
            }); 
            $("#options_hecnc").keydown (function(e) {
                if (e.which==38) {
                    $("#options_hmcnc").select();
                }
                if (e.which==40) {
                    $("#options_hr").select();
                }
                
            }); 
            $("#options_hr").keydown (function(e) {
                if (e.which==38) {
                    $("#options_hecnc").select();
                }
                if (e.which==40) {
                    $("#crear").select();
                }
            }); 
            $("#crear").keydown (function(e) {
                if (e.which==38) {
                    $("#options_hr").select();
                }
            }); 
            
        });
        </script>';

        require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

        // Load object modCodeProduct
        $module=(! empty($conf->global->PRODUCT_CODEPRODUCT_ADDON)?$conf->global->PRODUCT_CODEPRODUCT_ADDON:'mod_codeproduct_leopard');
        if (substr($module, 0, 16) == 'mod_codeproduct_' && substr($module, -3) == 'php')
        {
            $module = substr($module, 0, dol_strlen($module)-4);
        }
        $result=dol_include_once('/core/modules/product/'.$module.'.php');
        if ($result > 0)
        {
            $modCodeProduct = new $module();
        }

        // Load object modBarCodeProduct
        if (! empty($conf->barcode->enabled) && ! empty($conf->global->BARCODE_PRODUCT_ADDON_NUM))
        {
            $module=strtolower($conf->global->BARCODE_PRODUCT_ADDON_NUM);
            $dirbarcode=array_merge(array('/core/modules/barcode/'),$conf->modules_parts['barcode']);
            foreach ($dirbarcode as $dirroot)
            {
                $res=dol_include_once($dirroot.$module.'.php');
                if ($res) break;
            }
            if ($res > 0)
            {
                $modBarCodeProduct =new $module();
            }
        }

        print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
        print '<input type="hidden" name="action" value="add">';
        if ($type==1) $title=$langs->trans("NewService");
        else $title=$langs->trans("NewProduct");
        $linkback="";
        print load_fiche_titre($title,$linkback,'title_products.png');

        dol_fiche_head('');

        print '<table class="border" width="100%">';
        print '<tr>';
        $tmpcode='';
        if (! empty($modCodeProduct->code_auto)) $tmpcode=$modCodeProduct->getNextValue($object,$type);
        print '<td class="fieldrequired" width="20%">'.$langs->trans("Ref").'</td><td colspan="3"><input id="ref" name="ref" size="32" maxlength="128" value="'.$object->ref.'">';
        if ($refalreadyexists)
        {
            print $langs->trans("RefAlreadyExists");
        }
        print '</td></tr>';

        // Label
        print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3">
                <input id="label" name="label" size="40" maxlength="255" value="'.$object->label.'">
            </td></tr>';

        // Other attributes
        $parameters=array('colspan' => 3);
        $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
        if (empty($reshook) && ! empty($extrafields->attribute_label))
        {
            //print $object->showOptionals($extrafields,'edit',$parameters);
            print '
                <tr>
                    <td>'.$extrafields->attribute_label["anc"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_anc" name="options_anc" size="6" value="'.number_format($object->array_options["options_anc"],2).'"> 
                    </td>
                </tr>
            ';
            print '
                <tr>
                    <td>'.$extrafields->attribute_label["lar"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_lar" name="options_lar" size="6" value="'.number_format($object->array_options["options_lar"],2).'"> 
                    </td>
                </tr>
            ';
            print '
               <tr>
                    <td>'.$extrafields->attribute_label["total_prod"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_total_prod" name="options_total_prod" size="10" maxlength="10" value="'.(empty($object->array_options["options_total_prod"])?0:$object->array_options["options_total_prod"]).'">
                    </td>
                </tr>
            ';
            print '
                <tr></tr>
               <tr>
                    <td>'.$extrafields->attribute_label["hmcnc"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_hmcnc" name="options_hmcnc" size="10" maxlength="10" value="'.(empty($object->array_options["options_hmcnc"])?0:$object->array_options["options_hmcnc"]).'">
                    </td>
                </tr>
            ';
            print '
               <tr>
                    <td>'.$extrafields->attribute_label["hecnc"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_hecnc" name="options_hecnc" size="10" maxlength="10" value="'.(empty($object->array_options["options_hecnc"])?0:$object->array_options["options_hecnc"]).'">
                    </td>
                </tr>
            ';
            print '
               <tr>
                    <td>'.$extrafields->attribute_label["hr"].'</td>
                    <td colspan="3">
                        <input type="text" class="flat" id="options_hr" name="options_hr" size="10" maxlength="10" value="'.(empty($object->array_options["options_hr"])?0:$object->array_options["options_hr"]).'">
                    </td>
                </tr>
            ';
        }

        $cad="";
        if ($object->tratamiento==1) {
            $cad="checked";
        }

        print '
           <tr>
                <td>Solo Tratamiento</td>
                <td colspan="3">
                    <input type="checkbox" name="tratamiento" '.$cad.'>
                </td>
            </tr>
        ';



        print '</table>';

        print '<br>';

        print '<div class="center"><input id="crear" type="submit" class="button" value="'.$langs->trans("Create").'"></div>';
        
        print '</form>';
        dol_fiche_end();


 
    }

    if ($object->id > 0)
    {
        // Fiche en mode edition
        if ( $action == 'edit'  && ($user->rights->produit->creer || $user->rights->service->creer))
        {
            print '
            <script type="text/javascript" language="javascript">
                jQuery(document).ready(function() {

                    $("#ref").select();
                    $("#ref").keydown (function(e) {
                        if (e.which==40) {
                            $("#label").select();
                        }
                    });  
                    $("#label").keydown (function(e) {
                        if (e.which==38) {
                            $("#ref").select();
                        }
                        if (e.which==40) {
                            $("#options_anc").select();
                        }
                    }); 
                    $("#options_anc").keydown (function(e) {
                        if (e.which==38) {
                            $("#label").select();
                        }
                        if (e.which==40) {
                            $("#options_lar").select();
                        }
                    });
                    $("#options_lar").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_anc").select();
                        }
                        if (e.which==40) {
                            $("#options_total_prod").select();
                        }
                    });
                    $("#options_total_prod").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_lar").select();
                        }
                        if (e.which==40) {
                            $("#options_hmcnc").select();
                        }
                    }); 

                    $("#options_hmcnc").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_total_prod").select();
                        }
                        if (e.which==40) {
                            $("#options_hecnc").select();
                        }
                    }); 
                    $("#options_hecnc").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_hmcnc").select();
                        }
                        if (e.which==40) {
                            $("#options_hr").select();
                        }
                        
                    }); 
                    $("#options_hr").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_hecnc").select();
                        }
                        if (e.which==40) {
                            $("#edit").select();
                        }
                    });
                    $("#edit").keydown (function(e) {
                        if (e.which==38) {
                            $("#options_hr").select();
                        }
                    });
                });
            </script>';
            require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
            $titre=$langs->trans("Edición Rápida");
            $picto=($object->type== Product::TYPE_SERVICE?'service':'product');
            dol_fiche_head($head, 'card', $titre, 0, $picto);
            $type = $langs->trans('Product');

            if ($object->isService()) $type = $langs->trans('Service');
            //print load_fiche_titre($langs->trans('Modify').' '.$type.' : '.(is_object($object->oldcopy)?$object->oldcopy->ref:$object->ref), "");

            // Main official, simple, and not duplicated code
            print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="POST">'."\n";
            print '<input type="hidden" name="action" value="update">';
            print '<input type="hidden" name="id" value="'.$object->id.'">';

            

            print '<table class="border allwidth">';

            if (! empty($modCodeProduct->code_auto)) $tmpcode=$modCodeProduct->getNextValue($object,$type);
            print '<td class="fieldrequired" width="20%">'.$langs->trans("Ref").'</td><td colspan="3"><input id="ref" name="ref" size="32" maxlength="128" value="'.$object->ref.'">';
            if ($refalreadyexists)
            {
                print $langs->trans("RefAlreadyExists");
            }
            print '</td></tr>';

            // Label
            print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3">
                    <input id="label" name="label" size="40" maxlength="255" value="'.$object->label.'">
                </td></tr>';
            // Other attributes
            $parameters=array('colspan' => 3);
            $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
            $option_anc=0;
            $options_lar=0;
            $options_total_prod=0;

            if (empty($reshook) && ! empty($extrafields->attribute_label))
            {
                //print $object->showOptionals($extrafields,'edit',$parameters);
                print '
                    <tr>
                        <td>'.$extrafields->attribute_label["anc"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_anc" name="options_anc" size="6" value="'.number_format($object->array_options["options_anc"],2).'"> 
                        </td>
                    </tr>
       
                    <tr>
                        <td>'.$extrafields->attribute_label["lar"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_lar" name="options_lar" size="6" value="'.number_format($object->array_options["options_lar"],2).'"> 
                        </td>
                    </tr>
        
                   <tr>
                        <td>'.$extrafields->attribute_label["total_prod"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_total_prod" name="options_total_prod" size="10" maxlength="10" value="'.(empty($object->array_options["options_total_prod"])?0:$object->array_options["options_total_prod"]).'">
                        </td>
                    </tr>
                ';
                print '
                <tr></tr>
                   <tr>
                        <td>'.$extrafields->attribute_label["hmcnc"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_hmcnc" name="options_hmcnc" size="10" maxlength="10" value="'.(empty($object->array_options["options_hmcnc"])?0:number_format($object->array_options["options_hmcnc"],2)).'">
                        </td>
                    </tr>
                ';
                print '
                   <tr>
                        <td>'.$extrafields->attribute_label["hecnc"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_hecnc" name="options_hecnc" size="10" maxlength="10" value="'.(empty($object->array_options["options_hecnc"])?0:number_format($object->array_options["options_hecnc"],2)).'">
                        </td>
                    </tr>
                ';
                print '
                   <tr>
                        <td>'.$extrafields->attribute_label["hr"].'</td>
                        <td colspan="3">
                            <input type="text" class="flat" id="options_hr" name="options_hr" size="10" maxlength="10" value="'.(empty($object->array_options["options_hr"])?0:number_format($object->array_options["options_hr"],2)).'">
                        </td>
                    </tr>
                ';
                $option_anc=$object->array_options["options_anc"];
                $options_lar=$object->array_options["options_lar"];
                $options_total_prod=$object->array_options["options_total_prod"];
            }
            $cad="";
            if ($object->tratamiento==1) {
                $cad="checked";
            }

            print '
               <tr>
                    <td>Solo Tratamiento</td>
                    <td colspan="3">
                        <input type="checkbox" name="tratamiento" '.$cad.'>
                    </td>
                </tr>
            ';
            print '</table>';
            
            //}
            print '<div class="center"><br>';
            print '<input type="submit" id="edit" class="button" value="'.$langs->trans("Save").'">';
            print '</div>';
            dol_fiche_end();
            print '</form>';
            if ($option_anc>0 && $options_lar>0 && $options_total_prod>0 && !empty($object->ref) && !empty($object->label)){

                $prodsfather = $factory->getFather(); //Parent Products

                // pour connaitre les produits composant le produits
                $factory->get_sousproduits_arbo();

       

                // Number of subproducts
                $prods_arbo = $factory->get_arbo_each_prod();
                $ban_trat=0;
                foreach ($prods_arbo as $key => $value) {
                    if ($value["type"]==0) {
                        $ban_trat=1;
                    }
                }

                if ($object->status_buy==0 && $action=="edit" && $object->tratamiento==0 ) {
                    
                    print_fiche_titre("Face 2 - Materia Prima",'','');
                    dol_fiche_head();

                        print '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
                            print '<input type="hidden" name="action" value="add_line">';
                            print '<input type="hidden" name="id" value="'.$id.'">';

                            print '<b>Producto:</b>'.select_dol_products(GETPOST("id_line"), 'id_line',1," WHERE fk_product_type<>1 and rowid<>".$object->id." ");
                            //print '<br><b>Cantidad: </b><input name="cantidad" size="10"  value="'.GETPOST("cantidad").'">';
                            print '<br><br><input type="submit" class="button" value="Cargar">';
                        print '</form>';    
                    dol_fiche_end();
                }

                if ($object->status_buy==0 &&  $action=="edit" && ($ban_trat==1 || $object->tratamiento==1) ) {
                    
                    print_fiche_titre("Face 3 - Tratamiento",'','');
                    dol_fiche_head();

                        print '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
                            print '<input type="hidden" name="action" value="add_line">';
                            print '<input type="hidden" name="id" value="'.$id.'">';
                            print '<b>Producto:</b>'.select_dol_products(GETPOST("id_line2"), 'id_line2',1," WHERE fk_product_type=1 and rowid<>".$object->id." ");
                            print '<br><br><input type="submit" class="button" value="Cargar">';
                        print '</form>';    
                    dol_fiche_end();
                }
            }

            print_fiche_titre("Integración",'','');
            print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
            print '<input type="hidden" name="action" value="del_line">';
            print '<input type="hidden" name="id" value="'.$id.'">';
            print '<table class="border" >';
                print '<tr class="liste_titre">';
                print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</td>';
                print '<td class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</td>';
                print '<td class="liste_titre" width=50px align="center">'.$langs->trans("QtyNeed").'</td>';
                // on affiche la colonne stock m�me si cette fonction n'est pas active
                print '<td class="liste_titre" width=50px align="center">'.$langs->trans("Stock").'</td>'; 
                if ($conf->stock->enabled)
                {   // we display vwap titles
                    print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPmp").'</td>';
                    print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostPmpHT").'</td>';
                }
                else
                {   // we display price as latest purchasing unit price title
                    print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitHA").'</td>';
                    print '<td class="liste_titre" width=100px align="right">'.$langs->trans("CostHA").'</td>';
                }
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("UnitPriceHT").'</td>';
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellingPriceHT").'</td>';
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("ProfitAmount").'</td>';
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("TheoreticalWeight").'</td>';
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellPriceUnit").'</td>';
                print '<td class="liste_titre" width=100px align="right">'.$langs->trans("SellPrice").'</td>';
                print '<td class="liste_titre" width=100px align="center">Retirar</td>';

                print '</tr>';
                if (count($prods_arbo) > 0)
                {
                    $compositionpresente=1;
                    //print '<b>'.$langs->trans("FactoryTableInfo").'</b><BR>';
                    
                        $mntTot=0;
                        $pmpTot=0;
                        $sumPriUnit=0;
                        $sumPriTot=0;
                        $i=0;
                        foreach($prods_arbo as $value)
                        {
                            //var_dump($value);
                            // verify if product have child then display it after the product name
                            $tmpChildArbo=$factory->getChildsArbo($value['id']);
                            $nbChildArbo="";
                            if (count($tmpChildArbo) > 0) $nbChildArbo=" (".count($tmpChildArbo).")";

                            print '<tr>';
                            print '<td align="left">'.$factory->getNomUrlFactory($value['id'], 1,'index').$nbChildArbo.'</td>';
                            print '<td align="left" title="'.$value['description'].'">';
                            print $value['label'].'</td>';
                            print '<td align="center">'.$value['nb'];
                            if ($value['globalqty'] == 1)
                                print "&nbsp;G";
                            print '</td>';

                            $productstatic = new Product($db);
                            $padre9= new factory($db);//soon
                            $padre9->priceFather($value['id']);
                            $price=$padre9->costo;      


                            //$price=$value['price'];
                            $pmp=$value['pmp'];
                            if ($conf->stock->enabled)
                            {   // we store vwap in variable pmp and display stock

                                $productstatic->fetch($value['id']);

                                if ($value['fk_product_type']==0)
                                {   // if product

                                    $productstatic->load_stock();

                                    print '<td align=center></td>';

                                }
                                else{
                                    print '<td></td>';
                                } 
                                    
                            }
                            else{
                                print '<td></td>';
                            }

                            $precioUnit=0;              
                            $factory->get_ref($value['id']);
                            $factory->dataIntegrationProduct($id);//father
                            $soon= new factory($db);//soon
                            $soon->dataIntegrationProduct($value['id']);
                            
                            $resp=substr($factory->refFather, 0, 3);                
                            if((strcmp($resp, 'TRA')==0 || strcmp($resp, 'tra')==0) || strcmp($ref, 'cxhcnc')==0 || strcmp($ref, 'cxhdm')==0 || strcmp($ref, 'cxhr') ==0 || $factory->type==1){
                                $pesoTeori=0;
                                $precioUnit=$value['price'];                
                            }else{
                                                
                                $pesoTeori=($soon->esp*$factory->anc*$factory->lar*$soon->fac/ 1000000);//peso teorico= espesor * ancho * largo * factor / 1000000
                            }       

                                
                            if((strcmp($resp, 'TRA')==0 || strcmp($resp, 'tra')==0) ){                  

                                /*//////////////calculo de Costo Unitario Tratamiento
                                Costo Unitario de trat.=costo tratamiento + UTILIDAD TRATAMIENTO    
                                costo tratamiento=FACTOR * AREA CUADRADA
                                AREA CUADRADA =ANCHO PULGADAS * LARGO PULGADAS
                                ANCHO PULGADAS = ancho milimetros / 25.4
                                LARGO PULGADAS = largo milimetros / 25.4
                                UTILIDAD TRATAMIENTO = costo tratamiento *  %utilidad %  ejemplo  =Y24*Z24%*/

                                $largo=($factory->lar/25.4);                
                                $ancho=($factory->anc/25.4);                
                                $area=$largo*$ancho;
                                $costoTrat=($soon->fac*$area);          

                                $utilidad=(($costoTrat*$soon->utilidad)/100);
                                $precioUnit=$costoTrat+$utilidad;                   

                            }else{
                                if($factory->type==0){

                                    /*///////////////////////////Calculo del costo unitario de material////////////
                                    costo unitario de material =costo materia prima * utilidad de materia prima                                 
                                    utilidad de materia prima = costo de materia prima  *  % %  ejemplo  =Q24*R24%      
                                    peso teorico= espesor * ancho * largo * factor / 1000000
                                    conversion MN = precio kg * tc  
                                    costo materia prima = conversion MN * peso teorico      */

                                    $valorTC=$factory->get_valorTC();
                                //  echo "  - ".$valorTC;
                                    $conversionMN=$price*$valorTC;/////////////////////////conversion MN/////////////////////       
                                //  echo " ".$conversionMN;
                                    $costMatPrima=($conversionMN*$pesoTeori);   ////////////Costo materia prima /////////////////////////           
                                //  echo " ".$costMatPrima;
                                    $utilidadMP=(($costMatPrima*$soon->utilidad)/100); ////////////////Utilidad de materia rima////////////////////
                                //  echo " ".$utilidadMP;
                                    $precioUnit=$costMatPrima+$utilidadMP;
                                //  echo " ".$precioUnit;


                                }
                            }       
                            
                            print '<td align="right">'.price($pmp).'</td>'; // display else vwap or else latest purchasing price
                            print '<td align="right">'.price($pmp*$value['nb']).'</td>'; // display total line
                            print '<td align="right">'.price($price).'</td>';
                            print '<td align="right">'.price($price*$value['nb']).'</td>';
                            print '<td align="right">'.price(($price-$pmp)*$value['nb']).'</td>'; 
                            print '<td align="right">'.number_format($pesoTeori,2).'</td>'; //peso teorico= espesor * ancho * largo * factor / 1000000
                            print '<td align="right">'.number_format($precioUnit,2).'</td>'; //precio unitario
                            print '<td align="right">'.number_format(($precioUnit*$value['nb']),2).'</td>'; //precio total
                            print '<td align="center"><input type="checkbox" name="prod_id_chk'.$i.'" value="'.$value['id'].'"></td>';
                            
                            $mntTot=$mntTot+$price*$value['nb'];
                            $pmpTot=$pmpTot+$pmp*$value['nb']; // sub total calculation
                            $sumPriUnit+=round($precioUnit,2);
                            $sumPriTot+=round(($precioUnit*$value['nb']),2);
                            
                            print '</tr>';

                            //var_dump($value);
                            //print '<pre>'.$productstatic->ref.'</pre>';
                            //print $productstatic->getNomUrl(1).'<br>';
                            //print $value[0];  // This contains a tr line.
                            $i++;
                        }
                        print '<tr class="liste_total">';
                        print '<td colspan=5 align=right >'.$langs->trans("Total").'</td>';
                        print '<td align="right" >'.price($pmpTot).'</td>';
                        print '<td ></td>';
                        print '<td align="right" >'.price($mntTot).'</td>';
                        print '<td align="right" >'.price($mntTot-$pmpTot).'</td>';
                        print '<td colspan=3 ></td>';
                        print '<td align="center" ><input type="submit" class="button" value="Retirar"></td>';
                        //print '<td >'.round($sumPriUnit,2).'</td>';
                        //print '<td >'.round($sumPriTot,2).'</td>';
                        
                }else{
                    print '<tr >';
                        print '<td colspan=13 >Sin Resultados</td>';
                    print '</tr>';
                }
            print '</table>';
            print '</form>';   
            
        }
        

    }




/*
 * All the "Add to" areas
 */


llxFooter();
$db->close();
