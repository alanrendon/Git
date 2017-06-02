<?php
class Actionsmembercomplements
{ 
    /**
     * Overloading the doActions function : replacing the parent's function with the one below
     *
     * @param   array()         $parameters     Hook metadatas (context, etc...)
     * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param   string          &$action        Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
    */

    /*
    function doActions($parameters, &$object, &$action, $hookmanager)
    {

        $error = 0; // Error counter
        $myvalue = 'test'; // A result value
        require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
        require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
        dol_include_once('/ctrlanticipo/class/ctrladvancecredit.class.php');
        if (in_array('invoicesuppliercard', explode(':', $parameters['context'])))
        {
            $id=GETPOST("facid");
            $buscar=stripos($_SERVER['PHP_SELF'], 'ctrlanticipo');
            if ( $buscar === false ) {
                $url_actual = $_SERVER["REQUEST_URI"];
                $parametros=explode("?", $url_actual);
                header('Location: ../../ctrlanticipo/view/card.php?'.$parametros[1]);
            }

        }
        if (in_array('addMoreActionsButtons', explode(':', $parameters['context'])))
        {
          $this->addMoreActionsButtons($parameters, $object, $action, $hookmanager);
        }else if(in_array('printFieldListTitle', explode(':', $parameters['context']))){
             $this->printFieldListTitle($parameters, $object, $action, $hookmanager);
        }
        else{
            $rights =GETPOST('rights', 'int');
            $id     =GETPOST('id', 'int');
            $module =GETPOST('module', 'alpha');

            if ($action=='addrights') {
                $edituser = new User($object->db);
                $edituser->fetch($id);
                if ($rights=='985004' || $rights=='985005') 
                    $edituser->addrights('985002', $module);
                else if($rights=='985002')
                    $edituser->addrights('985001', $module);
            }

            if ($action=='delrights') {
                $edituser = new User($object->db);
                $edituser->fetch($id);
                if ($rights=='985004' || $rights=='985005') 
                    $edituser->delrights('985002', $module);
                else if($rights=='985001')
                    $edituser->delrights('985002', $module);
            }

            return 0;
        }

        if (! $error)
        {
            $this->results = array('myreturn' => $myvalue);
            $this->resprints = 'A text to show';
            return 0; // or return 1 to replace standard code
        }
        else
        {
            $this->errors[] = 'Error message';
            return -1;
        }

       
    }
    */

    function getLoginPageOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $langs;
        $langs->load("membercomplements@membercomplements");
        $link="<a href='public/members/new.php' style='font-size: 20px; font-style: oblique;' >".$langs->trans("mc_register_new")."</a>";
        $hookmanager->resArray['options']=array('js'=>$link);
    }

    function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {

        global $langs;
        if (in_array('membercard', explode(':', $parameters['context'])) && $action=="")
        {
            require_once DOL_DOCUMENT_ROOT.'/membercomplements/class/mccomplements.class.php';
            $complement=new Mccomplements($object->db);
            $complement->fetch("","",$object->id);
            print "<tr><td>".$langs->trans("Text Alert Phone 1")."</td><td>\n";

                if ($complement->q_phone1==1) {
                    print "Yes";
                }else{
                    print "No";
                }
            print "</td></tr>\n";
            print "<tr><td>".$langs->trans("Text Alert Phone 2")."</td><td>\n";

                if ($complement->q_phone2==1) {
                    print "Yes";
                }else{
                    print "No";
                }
            print "</td></tr>\n";
            print "<tr><td>".$langs->trans("Provider")."</td><td>\n";
                $thirdparty=new Societe($object->db);
                $thirdparty->fetch($object->fk_soc);
                print $thirdparty->code_client;
            print "</td></tr>\n";
            print "<tr><td>".$langs->trans("Fax")."</td><td>\n";
                $nouser=new User($object->db);
                $nouser->fetch($object->user_id);
                print $nouser->office_fax;
            print "</td></tr>\n";

            print "<tr><td>".$langs->trans("Primary Phone")."</td><td>\n";
                if ($complement->primary_phone==1) {
                    print 'Home/Office';
                }
                if ($complement->primary_phone==2) {
                    print 'Mobile Phone 1';
                }
                if ($complement->primary_phone==3) {
                    print 'Mobile Phone 2';
                }
            print "</td></tr>\n";

            print "<tr><td>".$langs->trans("Temperature/Humidity Alert")."</td><td>\n";
                if ($complement->temperature==0 || empty($complement->temperature)) {
                    print 'No';
                }

                if ($complement->temperature==1) {
                    print 'Yes';
                }
            print "</td></tr>\n";
        }

        if (in_array('membercard', explode(':', $parameters['context'])) && $action=="edit")
        {
            require_once DOL_DOCUMENT_ROOT.'/membercomplements/class/mccomplements.class.php';
            $complement=new Mccomplements($object->db);
            $complement->fetch("","",$object->id);

            print "<tr><td>".$langs->trans("Fax")."</td><td>\n";
                $fax=GETPOST('fax');
                if (empty($fax)) {
                    $nouser=new User($object->db);
                    $nouser->fetch($object->user_id);
                    $fax= $nouser->office_fax;
                }
                print '<input type="text" name="fax" size="15" value="'.dol_escape_htmltag($fax).'">';
            print "</td></tr>\n";

            print '
            <tr><td>Text Alert Phone 1</td><td>
                <select name="q_phone1">';
                 $q_phone1=GETPOST("q_phone1");
                    
                if (empty($q_phone1)) {
                    $q_phone1=$complement->q_phone1;
                }
                if ($q_phone1==1) {
                    print "
                        <option selected value=1>Yes</option>
                        <option value=0>No</option>
                    ";
                }else{
                    print "
                        <option value=1>Yes</option>
                        <option selected value=0>No</option>
                    ";
                } 
            print '
                </select>
            </td></tr>'."\n";

            print '
            <tr><td>Text Alert Phone 2</td><td>
                <select name="q_phone2">';
                 $q_phone2=GETPOST("q_phone2");
                    
                if (empty($q_phone2)) {
                    $q_phone2=$complement->q_phone2;
                }
                if ($q_phone2==1) {
                    print "
                        <option selected value=1>Yes</option>
                        <option value=0>No</option>
                    ";
                }else{
                    print "
                        <option value=1>Yes</option>
                        <option selected value=0>No</option>
                    ";
                } 
            print '
                </select>
            </td></tr>'."\n";

            print '
            <tr><td> Primary Phone </td><td>
                <select name="primary_phone">';
                    $primary_phone=GETPOST("primary_phone");
                    if (empty($primary_phone)) {
                        $primary_phone=$complement->primary_phone;
                    }
                    if ($primary_phone==1) {
                        print '<option selected value=1>Home/Office</option>';
                    }else{
                        print '<option value=1>Home/Office</option>';
                    }
                    if ($primary_phone==2) {
                        print '<option selected value=2>Mobile Phone 1</option>';
                    }else{
                        print '<option value=2>Mobile Phone 1</option>';
                    }
                    if ($primary_phone==3) {
                        print '<option selected value=3>Mobile Phone 2</option>';
                    }else{
                        print '<option value=3>Mobile Phone 2</option>';
                    }  
            print '
                </select>
            </td></tr>'."\n";

            print '
            <tr><td>Temperature/Humidity Alert</td><td>
                <select name="temperature">';
                    $temperature=GETPOST("temperature");
                    if (empty($temperature)) {
                        $temperature=$complement->temperature;
                    }
                    if ($temperature==0) {
                        print '<option selected value=0>No</option>';
                    }else{
                        print '<option value=0>No</option>';
                    }

                    if ($temperature==1) {
                        print '<option selected value=1>Yes</option>';
                    }else{
                        print '<option  value=1>Yes</option>';
                    }
            print '
                </select>
            </td></tr>'."\n";
            
        }
    }

    function insertExtraFields($parameters, &$object, &$action, $hookmanager)
    {
        global $user;
        if (in_array('memberdao', explode(':', $parameters['context'])) )
        {
            require_once DOL_DOCUMENT_ROOT.'/membercomplements/class/mccomplements.class.php';
            $complement=new Mccomplements($object->db);
            $complement->fetch("","",$object->id);

            $complement->q_phone1=GETPOST("q_phone1");
            $complement->q_phone2=GETPOST("q_phone2");
            $complement->primary_phone=GETPOST("primary_phone");
            $complement->temperature=GETPOST("temperature");
            $complement->update($user);



            //cambiar fax
            $nouser=new User($object->db);
            $nouser->fetch($object->user_id);
            $fax=GETPOST('fax');
            $nouser->office_fax=$fax;
            $nouser->update($user);

            

            


        }
    }

    

    


    
}
?>