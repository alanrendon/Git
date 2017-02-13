<?php
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2013      Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2013-2015 Alexandre Spangaro 	<aspangaro.dolibarr@gmail.com>
 * Copyright (C) 2014      Juanjo Menent	 	<jmenent@2byte.es>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *       \file       htdocs/contact/card.php
 *       \ingroup    societe
 *       \brief      Card of a contact
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT. '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/cclinico/class/antecedentes.class.php');
dol_include_once('/cclinico/class/pacientes.class.php');
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$aid         = GETPOST('aid','int');
//$socid		= GETPOST('socid','int');
$object = new Antecedentes($db);
$form = new Form($db);

$head=array();
if ($id > 0)
{
    // Si edition contact deja existant
    $object2 = new Pacientes($db);
    $res=$object2->fetch($id, $user);
    if ($res < 0) { dol_print_error($db,$object2->error); exit; }
    $res=$object->fetch_optionals($object2->id,$extralabels);
    // Show tabs
    $head = pacientes_prepare_head($object2);
}




// Add antecedentes
if ($action == 'add' && !empty($id))
{
    $db->begin();
    $object->titulo          = GETPOST("titulo");
    $object->descripcion     = GETPOST("descripcion");
    $object->fk_pacientes    = $id;
    if (! $_POST["titulo"])
    {
        $error++;
        $errors[]="El campo 'Título del Antecedente' es obligatorio";
        $action = 'create';
    }

    if (! $error)
    {

        $id2 =  $object->create($user);
        if ($id2 <= 0)
        {
            $error++; $errors=array_merge($errors,($object->error?array($object->error):$object->errors));
            $action = 'create';
		} else {
            $action = '';
			// Categories association
			//$contcats = GETPOST( 'contcats', 'array' );
			//$object->setCategories($contcats);
		}
        if (! $error && $id > 0)
        {
            $db->commit();
            if (! empty($backtopage)) $url=$backtopage;
            else $url='perso.php?id='.$id;
            header("Location: ".$url);
            exit;
        }
        else
        {
            $db->rollback();
        }
    }
}
if ($action == 'edit' && !empty($aid))
{
    $object->fetch($aid);
}
if ($action == 'update' && !empty($aid) && !empty($id))
{
    $db->begin();
    $object->titulo          = GETPOST("titulo");
    $object->descripcion     = GETPOST("descripcion");
    $object->fk_pacientes    = $id;
    $object->id              = $aid;
    if (! $_POST["titulo"])
    {
        $error++;
        $errors[]="El campo 'Título del Antecedente' es obligatorio";
        $action = 'edit';
    }

    if (! $error)
    {

        $id2 =  $object->update($user);
        if ($id2 <= 0)
        {
            $error++; $errors=array_merge($errors,($object->error?array($object->error):$object->errors));
            $action = 'edit';
        } else {
            $action = '';
            // Categories association
            //$contcats = GETPOST( 'contcats', 'array' );
            //$object->setCategories($contcats);
        }
        if (! $error && $id > 0)
        {
            $db->commit();
            if (! empty($backtopage)) $url=$backtopage;
            else $url='perso.php?id='.$id;
            header("Location: ".$url);
            exit;
        }
        else
        {
            $db->rollback();
        }
    }
}





/*
*	View
*/


llxHeader('', 'Antecedentes', '');

if (! empty($id) && $action == 'create')
{

    dol_htmloutput_errors("",$errors);
    dol_fiche_head($head, 'perso', 'Paciente', 0, 'contact');

    print '<form method="post" name="formsoc" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<table class="border" width="100%">';
    print '<tr>
        <td colspan="2" class="maxwidthonsmartphone" >
            <label for="descripcion">&nbsp;Título del Antecedente</label>
        </td>
    </tr>';
    print '<tr><td>
    <input name="titulo" id="titulo" type="text" size="75" maxlength="90" value="'.GETPOST("titulo").'">
    </td></tr>';
    print '<tr>
        <td colspan="2" class="maxwidthonsmartphone" ><textarea class="flat" name="descripcion" id="descripcion" cols="100" rows="15">'.GETPOST("descripcion").'</textarea></td>
    </tr>';
    print '<tr><td>
    <input type="submit" value="Guardar" class="button">
    </td></tr>';
    echo "</table>";

    print "</form>";
            
    print dol_fiche_end();
}elseif (! empty($id) && $action == 'edit') {

    dol_htmloutput_errors("",$errors);
    dol_fiche_head($head, 'perso', 'Paciente', 0, 'contact');

    print '<form method="post" name="formsoc" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    print '<input type="hidden" name="aid" value="'.$aid.'">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<table class="border" width="100%">';
    print '<tr>
        <td colspan="2" class="maxwidthonsmartphone" >
            <label for="descripcion">&nbsp;Título del Antecedente</label>
        </td>
    </tr>';
    print '<tr><td>
    <input name="titulo" id="titulo" type="text" size="75" maxlength="90" value="'.(isset($_POST["titulo"])?$_POST["titulo"]:$object->titulo).'">
    </td></tr>';
    print '<tr>
        <td colspan="2" class="maxwidthonsmartphone" ><textarea class="flat" name="descripcion" id="descripcion" cols="100" rows="15">'.(isset($_POST["descripcion"])?$_POST["descripcion"]:$object->descripcion).'</textarea></td>
    </tr>';
    print '<tr><td>
    <input type="submit" value="Actualizar" class="button">
    </td></tr>';
    echo "</table>";
    print "</form>";
    print dol_fiche_end();
}





llxFooter();

$db->close();
