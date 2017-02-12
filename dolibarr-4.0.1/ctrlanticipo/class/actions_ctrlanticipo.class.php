<?php
class Actionsctrlanticipo
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

    function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $user,$langs;

        $langs->load('ctrlanticipo@ctrlanticipo');
            
        $societe= new Societe($object->db);

        $societe->fetch($object->id);
        if ($societe->fournisseur==1) {
            if ($user->rights->ctrlanticipo->ctrlanticipo1->createmodify) {
                print '<div class="inline-block divButAction"><a class="butAction" href="../ctrlanticipo/view/ctrladvanceprovider_card.php?idprovider=' . $object->id . '&amp;action=create">' . $langs->trans("ctrl_new_advance_button") . '</a></div>' . "\n";
            }
        }
        return 0;
    } 


    
}
?>