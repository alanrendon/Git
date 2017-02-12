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


class InterfaceModInversionistas extends DolibarrTriggers
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
		    case 'COMPANY_CREATE':
		    	require_once DOL_DOCUMENT_ROOT. '/core/lib/functions.lib.php';
		    	require_once DOL_DOCUMENT_ROOT. '/compta/bank/class/account.class.php';
		    	require_once DOL_DOCUMENT_ROOT. '/societe/class/societe.class.php';
		    	require_once DOL_DOCUMENT_ROOT. '/compta/facture/class/facture.class.php';
		    	require_once DOL_DOCUMENT_ROOT. '/fourn/class/fournisseur.facture.class.php';
		    	$now=dol_now();
		    	$cuenta_nueva=new Account($this->db);
		    	$tercero=new Societe($this->db);
		    	$tercero->fetch($object->id);
		    	if ($tercero->array_options["options_inv001"]==1) {
		    		
		    		$cuenta_nueva->ref="Inv-".$tercero->id;
			    	$cuenta_nueva->label=$tercero->name;
			    	$cuenta_nueva->currency_code="MXN";
			    	$cuenta_nueva->courant=0;
			    	$cuenta_nueva->rappro=1;
			    	$cuenta_nueva->country_id=154;
			    	$cuenta_nueva->solde=0;
			    	$cuenta_nueva->date_solde=$now;
			    	$id_cuenta=$cuenta_nueva->create($user);

			    	if ($id_cuenta>0) {
			    		$sql='
			    		INSERT INTO llx_terceros_cuenta (fk_tercero,fk_cuenta,date_creation) 
			    		VALUES ('.$tercero->id.','.$id_cuenta.', NOW() );';
			    		$this->db->query($sql);
			    		$sql='DELETE FROM llx_bank WHERE fk_account='.$id_cuenta;
			    		$this->db->query($sql);
			    		$sql="
			        	INSERT INTO llx_union_bt 
			        	(fk_tercero,fk_cuenta,date_c,fk_user_create) 
			        	VALUES (".$tercero->id.",".$id_cuenta.",now(),".$user->id.");";
						$this->db->query($sql);
			    	}
		    	}
		   		break;
	    }

        return 0;
	}

}
