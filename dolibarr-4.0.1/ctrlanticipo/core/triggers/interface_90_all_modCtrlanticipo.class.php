<?php
/* Copyright (C) 2005-2014 Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2014 Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2015      Bahfir Abbes        <bafbes@gmail.com>
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
 *  \file       htdocs/core/triggers/interface_90_all_Demo.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


class InterfaceModCtrlanticipo extends DolibarrTriggers
{

	public $family = 'demo';
	public $picto = 'technic';
	public $description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
	public $version = self::VERSION_DOLIBARR;

	/**
     * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
     *
     * @param string		$action		Event action code
     * @param Object		$object     Object concerned. Some context information may also be provided into array property object->context.
     * @param User		    $user       Object user
     * @param Translate 	$langs      Object langs
     * @param conf		    $conf       Object conf
     * @return int         				<0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
		// Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action
	    
	    switch ($action) {
		    case 'BILL_SUPPLIER_DELETE':
		    	require_once DOL_DOCUMENT_ROOT.'/ctrlanticipo/class/ctrladvancecredit.class.php';
		    	$sql='SELECT
					a.rowid,a.fk_source
				FROM
					llx_element_element AS a
				INNER JOIN llx_paiementfourn_facturefourn as b on b.rowid=a.fk_target
				INNER JOIN llx_facture_fourn AS c on c.rowid=b.fk_facturefourn
				WHERE
					a.targettype = "paiementfourn_facturefourn"
				AND b.fk_facturefourn='.$object->id;
				$result=$object->db->query($sql);
		        if ($result)
		        {
		            if ($object->db->num_rows($result))
		            {
		                $obj = $object->db->fetch_object($result);

		                $credit=new Ctrladvancecredit($object->db);
					    $res=$credit->fetch($obj->fk_source);
					    if ($res) {
					    	$result=$credit->delete_link($user,$obj->rowid,$object->id);

					    	if ($result) {
					    		$credit->statut=1;
								$credit->update($user);
					    	}
					    }
					    $credit->change_value_advance($user);
		            }
		        }

		   		
		   	break;
	    }

        return 0;
	}

}
