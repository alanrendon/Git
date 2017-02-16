<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (c) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012      Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2013      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2015      Jean-François Ferry  <jfefe@aternatik.fr>
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
 *  \file       htdocs/compta/facture/stats/index.php
 *  \ingroup    facture
 *  \brief      Page des stats factures
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facturestats.class.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/lib/pacientes.lib.php';
require_once DOL_DOCUMENT_ROOT.'/cclinico/class/pacientes.class.php';
global $conf;
dol_include_once('/cclinico/class/consultas.class.php');


$WIDTH=DolGraph::getDefaultGraphSizeForStats('width');
$HEIGHT=DolGraph::getDefaultGraphSizeForStats('height');

$fk_user_med=GETPOST("fk_user_med","int");
$action=GETPOST("action");
$fk_user_pacientes=GETPOST("fk_user_pacientes","int");
$Type_consultation=GETPOST("Type_consultation","int");
$search_statut=GETPOST("search_statut","int");

$mode=GETPOST("mode")?GETPOST("mode"):'customer';
$userid=GETPOST('userid','int');
$socid=GETPOST('socid','int');
// Security check
if ($user->societe_id > 0)
{
    $action = '';
    $socid = $user->societe_id;
}

$nowyear=strftime("%Y", dol_now());
$year = GETPOST('year')>0?GETPOST('year'):$nowyear;
//$startyear=$year-2;
$startyear=$year-1;
$endyear=$year;

/*
 * View
 */

$langs->load('bills');
$langs->load('companies');
$langs->load('other');

$form=new Form($db);
$con= new Consultas($db);
llxHeader();

if ($action=="mes") {
    
    $buscar_fk_user_med="";
    if (!empty($fk_user_med) && $fk_user_med!=-1) {
       $buscar_fk_user_med=" AND a.fk_user_med=".$fk_user_med;
    }
    $buscar_Type_consultation="";
    if (!empty($Type_consultation) && $Type_consultation!=-1) {
       $buscar_Type_consultation=" AND a.Type_consultation=".$Type_consultation;
    }
    $buscar_fk_user_pacientes="";
    if (!empty($fk_user_pacientes)  && $fk_user_pacientes!=-1) {
       $buscar_fk_user_pacientes=" AND a.fk_user_pacientes=".$fk_user_pacientes;
    }

    print load_fiche_titre('Estadísticas', $mesg, 'title_generic.png');



    $stats = new FactureStats($db, $socid, $mode, ($userid>0?$userid:0));
    $object2 = new Pacientes($db);

    // Build graphic number of object
    // $data = array(array('Lib',val1,val2,val3),...)
    $data = $stats->getNbByMonthWithPrevYear($endyear,$startyear);
    //var_dump($data);
    foreach ($data as $key => $val ) {
        $sql='
        SELECT
            count(*) as total
        FROM
            llx_consultas AS a
        ';

        if ($search_statut==2) {
            $sql.='
            inner JOIN llx_facturas_consulta AS b ON a.rowid = b.fk_consulta
            inner JOIN llx_facture as c on c.rowid=b.fk_factura';
        }


        $sql.='
        WHERE
             a.entity='.$conf->entity.'  AND 
             YEAR(a.date_creation)='.$nowyear.' AND MONTH(a.date_creation)='.($key+1)." ".$buscar_fk_user_med.' '.$buscar_Type_consultation." ".$buscar_fk_user_pacientes;
        if ($search_statut>0) {
            $sql.=' AND a.statut='.$search_statut;
        }
        if ($search_statut==2) {
            $sql.=" AND c.paye=1 AND
                b.entity=".$conf->entity."  AND 
                c.entity=".$conf->entity."   
            ";
        }

        
        $sql2='
        SELECT
            count(*) as total
        FROM
            llx_consultas AS a';


        if ($search_statut==2) {
            $sql2.='
            inner JOIN llx_facturas_consulta AS b ON a.rowid = b.fk_consulta
            inner JOIN llx_facture as c on c.rowid=b.fk_factura';
        }

        $sql2.='
        WHERE
            a.entity='.$conf->entity.'  AND 
             YEAR(a.date_creation)='.($nowyear-1).' AND MONTH(a.date_creation)='.($key+1)." ".$buscar_fk_user_med.' '.$buscar_Type_consultation." ".$buscar_fk_user_pacientes;
        if ($search_statut>0) {
            $sql2.=' AND a.statut='.$search_statut;
        }
        if ($search_statut==2) {
            $sql2.=" AND c.paye=1 AND
            b.entity=".$conf->entity."  AND 
            c.entity=".$conf->entity."   
            ";
        }

        $resql=$db->query($sql);
        $num = $db->num_rows($resql);
        if ($num>0)
        {
            $obj = $db->fetch_object($resql);
            $data[$key][2]=$obj->total;

        }else{
            $data[$key][2]=0;
        }

        $resql2=$db->query($sql2);
        $num2 = $db->num_rows($resql2);
        if ($num2>0 && $buscar_fk_user_med!="")
        {
            $obj2 = $db->fetch_object($resql2);
            $data[$key][1]=$obj2->total;

        }else{
            $data[$key][1]=0;
        }
    }



    $filenamenb = $dir."/invoicesnbinyear-".$year.".png";
    if ($mode == 'customer') $fileurlnb = DOL_URL_ROOT.'/viewimage.php?modulepart=billstats&amp;file=invoicesnbinyear-'.$year.'.png';
    if ($mode == 'supplier') $fileurlnb = DOL_URL_ROOT.'/viewimage.php?modulepart=billstatssupplier&amp;file=invoicesnbinyear-'.$year.'.png';

    $px1 = new DolGraph();
    $mesg = $px1->isGraphKo();
    if (! $mesg)
    {
        $px1->SetData($data);
        $px1->SetPrecisionY(0);
        $i=$startyear;$legend=array();
        while ($i <= $endyear)
        {
            $legend[]=$i;
            $i++;
        }
        $px1->SetLegend($legend);
        $px1->SetMaxValue($px1->GetCeilMaxValue());
        $px1->SetWidth($WIDTH);
        $px1->SetHeight($HEIGHT);
        $px1->SetYLabel("Numero de consultas");
        $px1->SetShading(3);
        $px1->SetHorizTickIncrement(1);
        $px1->SetPrecisionY(0);
        $px1->mode='depth';
        $px1->SetTitle("Numero de consultas por mes");

        $px1->draw($filenamenb,$fileurlnb);
    }

    // Build graphic amount of object
    $data = $stats->getAmountByMonthWithPrevYear($endyear,$startyear);
    //var_dump($data);
    // $data = array(array('Lib',val1,val2,val3),...)


    foreach ($data as $key => $val ) {
        $sql='
        SELECT
            SUM(c.total_ttc) as total
        FROM
            llx_consultas AS a';

        if ($search_statut==2) {
            $sql.='
            inner JOIN llx_facturas_consulta AS b ON a.rowid = b.fk_consulta
            inner JOIN llx_facture as c on c.rowid=b.fk_factura';
        }

        $sql.='
        WHERE
            a.entity='.$conf->entity.'  AND 
            YEAR(a.date_creation)='.$nowyear.' AND MONTH(a.date_creation)='.($key+1)." ".$buscar_fk_user_med.' '.$buscar_Type_consultation." ".$buscar_fk_user_pacientes;
        if ($search_statut>0) {
            $sql.=' AND a.statut='.$search_statut;
        }
        if ($search_statut==2) {
            $sql.=" AND c.paye=1 AND
            b.entity=".$conf->entity."  AND 
            c.entity=".$conf->entity."   ";
        }
        
        $sql2='
        SELECT
            SUM(c.total_ttc) as total
        FROM
            llx_consultas AS a';
        if ($search_statut==2) {
            $sql2.='
            inner JOIN llx_facturas_consulta AS b ON a.rowid = b.fk_consulta
            inner JOIN llx_facture as c on c.rowid=b.fk_factura';
        }
        $sql2.='
        WHERE
            YEAR(a.date_creation)='.($nowyear-1).' AND MONTH(a.date_creation)='.($key+1)." ".$buscar_fk_user_med.' '.$buscar_Type_consultation." ".$buscar_fk_user_pacientes;
        if ($search_statut>0) {
            $sql2.=' AND a.statut='.$search_statut;
        }
        if ($search_statut==2) {
            $sql2.=" AND c.paye=1 AND
            b.entity=".$conf->entity."  AND 
            c.entity=".$conf->entity."   ";
        }


        $resql=$db->query($sql);
        $num = $db->num_rows($resql);
        if ($num>0 )
        {
            $obj = $db->fetch_object($resql);
            $data[$key][2]=$obj->total;

        }else{
            $data[$key][2]=0;
        }

        $resql2=$db->query($sql2);
        $num2 = $db->num_rows($resql2);
        if ($num2>0 && $buscar_fk_user_med!="")
        {
            $obj2 = $db->fetch_object($resql2);
            $data[$key][1]=round($obj2->total,2);

        }else{
            $data[$key][1]=0;
        }
    }




    $filenameamount = $dir."/invoicesamountinyear-".$year.".png";
    if ($mode == 'customer') $fileurlamount = DOL_URL_ROOT.'/viewimage.php?modulepart=billstats&amp;file=invoicesamountinyear-'.$year.'.png';
    if ($mode == 'supplier') $fileurlamount = DOL_URL_ROOT.'/viewimage.php?modulepart=billstatssupplier&amp;file=invoicesamountinyear-'.$year.'.png';

    $px2 = new DolGraph();
    $mesg = $px2->isGraphKo();
    if (! $mesg)
    {
        $px2->SetData($data);
        $i=$startyear;$legend=array();
        while ($i <= $endyear)
        {
            $legend[]=$i;
            $i++;
        }
        $px2->SetLegend($legend);
        $px2->SetMaxValue($px2->GetCeilMaxValue());
        $px2->SetMinValue(min(0,$px2->GetFloorMinValue()));
        $px2->SetWidth($WIDTH);
        $px2->SetHeight($HEIGHT);
        $px2->SetYLabel("Monto por mes");
        $px2->SetShading(3);
        $px2->SetHorizTickIncrement(1);
        $px2->SetPrecisionY(0);
        $px2->mode='depth';
        $px2->SetTitle("Monto por mes");

        $px2->draw($filenameamount,$fileurlamount);
    }



    // Show array
    $data = $stats->getAllByYear();
    $arrayyears=array();
    foreach($data as $val) {
        $arrayyears[$val['year']]=$val['year'];
    }
    if (! count($arrayyears)) $arrayyears[$nowyear]=$nowyear;


    $h=0;
    $head = pacientes_prepare_head2($object2);

    dol_fiche_head($head,'mes',$langs->trans("Statistics"),0,"accounting");

    $tmp_companies = $form->select_thirdparty_list($socid,'socid',$filter,1, 0, 0, array(), '', 1);
    //Array passed as an argument to Form::selectarray to build a proper select input
    print '<div class="fichecenter"><div class="fichethirdleft">';


    //if (empty($socid))
    //{
        // Show filter box
        print '<form name="stats" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
        print '<input type="hidden" name="action" value="'.$action.'">';
        print '<table class="noborder" width="100%">';
        print '<tr class="liste_titre"><td class="liste_titre" colspan="2">'.$langs->trans("Filter").'</td></tr>';
        // Company
        print '<tr><td>Médico</td><td>';

        print $con->select_dolusers($fk_user_med, 'fk_user_med', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');

        print '</td></tr>';
        // User
        print '<tr><td>Paciente</td><td>';
        print $con->select_dolpacientes($fk_user_pacientes, 'fk_user_pacientes', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '</td></tr>';

        print '<tr><td>Tipo de Consulta</td><td>';
        print $con->select_dol($Type_consultation, 'c_tipo_consulta' ,'Type_consultation', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
        print '</td></tr>';


        print '<tr><td>Estatus</td><td>';

        print '<select class="flat minwidth200 maxwidth300" id="search_statut" name="search_statut" data-role="none">';
        print '
        
        ';
        if ($search_statut==-1) {
            print '
            <option value="-1" selected>&nbsp;</option>
            ';
        }else{
             print '
            <option value="-1" >&nbsp;</option>
            ';
        }
        if ($search_statut==0) {
            print '
            <option value="0" selected>&nbsp;Borrador</option>
            ';
        }else{
             print '
            <option value="0" >&nbsp;Borrador</option>
            ';
        }
        if ($search_statut==1) {
            print '
            <option value="1" selected>&nbsp;Validado</option>
            ';
        }else{
             print '
            <option value="1" >&nbsp;Validado</option>
            ';
        }
        if ($search_statut==2) {
            print '
            <option value="2" selected>&nbsp;(cerrado) Facturada</option>
            ';
        }else{
             print '
            <option value="2" >&nbsp;(cerrado) Facturada</option>
            ';
        }
        if ($search_statut==3) {
            print '
            <option value="3" selected>&nbsp;cerrada(consulta gratuita)</option>
            ';
        }else{
             print '
            <option value="3" >&nbsp;cerrada(consulta gratuita)</option>
            ';
        }

        print '</select>';
        print '</td></tr>';

        print '<tr><td align="center" colspan="2"><input type="submit" name="submit" class="button" value="Actualizar"></td></tr>';
        print '</table>';
        print '</form>';
        print '<br><br>';
    //}

    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre" height="24">';
    print '<td align="center">Año</td>';
    print '<td align="center">Numero</td>';
    print '<td align="center">Importe total</td>';
    print '<td align="center">Importe promedio</td>';
    print '</tr>';

    $oldyear=0;
    $var=true;



    $sql='
    SELECT
        COUNT(*) as numero,SUM(c.total_ttc) as total,SUM(c.tva) as promedio
    FROM
        llx_consultas AS a
    inner JOIN llx_facturas_consulta AS b ON a.rowid = b.fk_consulta
    inner JOIN llx_facture as c on c.rowid=b.fk_factura
    WHERE
        a.entity='.$conf->entity.'  AND 
        b.entity='.$conf->entity.'  AND 
        c.entity='.$conf->entity.'  AND
        c.paye=1 AND YEAR(a.date_creation)='.$nowyear.' '.$buscar_fk_user_med.' '.$buscar_Type_consultation." ".$buscar_fk_user_pacientes;

    $resql=$db->query($sql);


    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num>0 )
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    print '<tr  height="24">';
                        print '<td align="center">'.$nowyear.'</td>';
                        print '<td align="right">'.$obj->numero.'</td>';
                        print '<td align="right">'.round(($obj->total==0)?0:$obj->total,2).'</td>';
                        print '<td align="right">'.round(($obj->promedio==0)?0:$obj->promedio,2).'</td>';
                    print '</tr>';
                }
                $i++;
            }
        }else{
            print '<tr  height="24">';
                print '<td colspan=4>Sin Resultados</td>';
            print '</tr>';
        }
    }






    print '</table>';


    print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


    // Show graphs
    print '<table class="border" width="100%"><tr valign="top"><td align="center">';
    if ($mesg) { print $mesg; }
    else {
        print $px1->show();
        print "<br>\n";
        print $px2->show();
        print "<br>\n";
        //print $px3->show();
    }
    print '</td></tr></table>';


    print '</div></div></div>';
    print '<div style="clear:both"></div>';

}
if ($action=="pacientes") {
    
    print load_fiche_titre('Estadísticas', $mesg, 'title_generic.png');



    $stats = new FactureStats($db, $socid, $mode, ($userid>0?$userid:0));
    $object2 = new Pacientes($db);

    // Build graphic number of object
    // $data = array(array('Lib',val1,val2,val3),...)
    $data = $stats->getNbByMonthWithPrevYear($endyear,$startyear);
    //var_dump($data);
    foreach ($data as $key => $val ) {
        $sql='
        SELECT COUNT(*) as total FROM llx_pacientes as a WHERE a.entity='.$conf->entity.'  AND  YEAR(a.datec)='.$nowyear.' AND MONTH(a.datec)='.($key+1);
        $sql2='
        SELECT COUNT(*) as total FROM llx_pacientes as a WHERE a.entity='.$conf->entity.'  AND  YEAR(a.datec)='.($nowyear-1).' AND MONTH(a.datec)='.($key+1);

        $resql=$db->query($sql);
        $num = $db->num_rows($resql);
        if ($num>0 )
        {
            $obj = $db->fetch_object($resql);
            $data[$key][2]=$obj->total;

        }else{
            $data[$key][2]=0;
        }

        $resql2=$db->query($sql2);
        $num2 = $db->num_rows($resql2);
        if ($num2>0 )
        {
            $obj2 = $db->fetch_object($resql2);
            $data[$key][1]=$obj2->total;

        }else{
            $data[$key][1]=0;
        }
    }



    $filenamenb = $dir."/invoicesnbinyear-".$year.".png";
    if ($mode == 'customer') $fileurlnb = DOL_URL_ROOT.'/viewimage.php?modulepart=billstats&amp;file=invoicesnbinyear-'.$year.'.png';
    if ($mode == 'supplier') $fileurlnb = DOL_URL_ROOT.'/viewimage.php?modulepart=billstatssupplier&amp;file=invoicesnbinyear-'.$year.'.png';

    $px1 = new DolGraph();
    $mesg = $px1->isGraphKo();
    if (! $mesg)
    {
        $px1->SetData($data);
        $px1->SetPrecisionY(0);
        $i=$startyear;$legend=array();
        while ($i <= $endyear)
        {
            $legend[]=$i;
            $i++;
        }
        $px1->SetLegend($legend);
        $px1->SetMaxValue($px1->GetCeilMaxValue());
        $px1->SetWidth($WIDTH);
        $px1->SetHeight($HEIGHT);
        $px1->SetYLabel("Numero de pacientes");
        $px1->SetShading(3);
        $px1->SetHorizTickIncrement(1);
        $px1->SetPrecisionY(0);
        $px1->mode='depth';
        $px1->SetTitle("Numero de pacientes por mes");

        $px1->draw($filenamenb,$fileurlnb);
    }




    // Show array
    $data = $stats->getAllByYear();
    $arrayyears=array();
    foreach($data as $val) {
        $arrayyears[$val['year']]=$val['year'];
    }
    if (! count($arrayyears)) $arrayyears[$nowyear]=$nowyear;


    $h=0;
    $head = pacientes_prepare_head2($object2);

    dol_fiche_head($head,'pacientes',$langs->trans("Statistics"),0,"accounting");

    $tmp_companies = $form->select_thirdparty_list($socid,'socid',$filter,1, 0, 0, array(), '', 1);
    //Array passed as an argument to Form::selectarray to build a proper select input
    print '<div class="fichecenter"><div class="fichethirdleft">';


    print '<table class="border" width="100%"><tr valign="top"><td align="center">';
    if ($mesg) { print $mesg; }
    else {
        print $px1->show();
        print "<br>\n";
    }
    print '</td></tr></table>';


    print '</div>';
    print '<div style="clear:both"></div>';

}
dol_fiche_end();


llxFooter();

$db->close();
