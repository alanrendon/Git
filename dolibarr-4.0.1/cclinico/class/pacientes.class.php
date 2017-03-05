<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * \file    Cclinico/pacientes.class.php
 * \ingroup Cclinico
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Pacientes
 *
 * Put here description of your class
 * @see CommonObject
 */
class Pacientes extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'pacientes';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'pacientes';

	/**
	 * @var PacientesLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $datec = '';
	public $tms = '';
	public $fk_soc;
	public $entity;
	public $ref_ext;
	public $civility;
	public $lastname;
	public $firstname;
	public $address;
	public $zip;
	public $town;
	public $fk_departement;
	public $fk_pays;
	public $birthday = '';
	public $poste;
	public $phone;
	public $phone_perso;
	public $phone_mobile;
	public $fax;
	public $email;
	public $jabberid;
	public $skype;
	public $photo;
	public $no_email;
	public $priv;
	public $fk_user_creat;
	public $fk_user_modif;
	public $note_private;
	public $note_public;
	public $default_lang;
	public $canvas;
	public $import_key;
	public $statut;
	public $sexo;
    public $edad;
    public $estatura;
    public $fk_user;


	/**
	 */
	

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
		return 1;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function setCategories($categories)
	{
		// Handle single category
		if (!is_array($categories)) {
			$categories = array($categories);
		}

		// Get current categories
		require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
		$c = new Categorie($this->db);
		$existing = $c->containing($this->id, Categorie::TYPE_CONTACT, 'id');

		// Diff
		if (is_array($existing)) {
			$to_del = array_diff($existing, $categories);
			$to_add = array_diff($categories, $existing);
		} else {
			$to_del = array(); // Nothing to delete
			$to_add = $categories;
		}

		// Process
		foreach ($to_del as $del) {
			if ($c->fetch($del) > 0) {
				$c->del_type($this, 'pacientes');
			}
		}
		foreach ($to_add as $add) {
			if ($c->fetch($add) > 0) {
				$c->add_type($this, 'pacientes');
			}
		}

		return;
	}
	function info($id)
	{
		$sql = "SELECT c.rowid, c.datec as datec, c.fk_user_creat,";
		$sql.= " c.tms as tms, c.fk_user_modif";
		$sql.= " FROM llx_pacientes as c";
		$sql.= " WHERE c.rowid = ".$this->db->escape($id);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id                = $obj->rowid;

				if ($obj->fk_user_creat) {
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_creat);
					$this->user_creation     = $cuser;
				}

				if ($obj->fk_user_modif) {
					$muser = new User($this->db);
					$muser->fetch($obj->fk_user_modif);
					$this->user_modification = $muser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->tms);
			}

			$this->db->free($resql);
		}
		else
		{
			print $this->db->error();
		}
	}

	public function create(User $user, $notrigger = false)
	{
		
		dol_syslog(__METHOD__, LOG_DEBUG);
		global $conf, $langs, $hookmanager;
		$error = 0;

		// Clean parameters
		
		$this->lastname=$this->lastname?trim($this->lastname):trim($this->name);
        $this->firstname=trim($this->firstname);
        if (! empty($conf->global->MAIN_FIRST_TO_UPPER)) $this->lastname=ucwords($this->lastname);
        if (! empty($conf->global->MAIN_FIRST_TO_UPPER)) $this->firstname=ucwords($this->firstname);
        if (empty($this->socid)) $this->socid = 0;
		if (empty($this->priv)) $this->priv = 0;
		if (empty($this->statut)) $this->statut = 0; // This is to convert '' into '0' to avoid bad sql request




		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		
			$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
			
			$sql.= 'datec,';
			$sql.= 'fk_soc,';
			$sql.= 'entity,';
			$sql.= 'ref_ext,';
			$sql.= 'civility,';
			$sql.= 'lastname,';
			$sql.= 'firstname,';
			$sql.= 'address,';
			$sql.= 'zip,';
			$sql.= 'town,';
			$sql.= 'fk_departement,';
			$sql.= 'fk_pays,';
			$sql.= 'birthday,';
			$sql.= 'poste,';
			$sql.= 'phone,';
			$sql.= 'phone_perso,';
			$sql.= 'phone_mobile,';
			$sql.= 'fax,';
			$sql.= 'email,';
			$sql.= 'jabberid,';
			$sql.= 'skype,';
			$sql.= 'photo,';
			//$sql.= 'no_email,';
			$sql.= 'priv,';
			$sql.= 'fk_user_creat,';
			$sql.= 'fk_user_modif,';
			$sql.= 'note_private,';
			$sql.= 'note_public,';
			$sql.= 'default_lang,';
			$sql.= 'canvas,';
			$sql.= 'import_key';
			$sql.= ', statut';
			$sql.= ", edad";
			$sql.= ", estatura";
			$sql.= ", sexo";
			$sql.= ", fk_user";

			
			$sql .= ') VALUES (';
			
			$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
			if ($this->socid > 0) $sql.= " ".$this->socid.",";
			else $sql.= "null,";
			$sql .= ' '.$conf->entity.',';
			$sql .= ' '.(! isset($this->ref_ext)?'NULL':"'".$this->db->escape($this->ref_ext)."'").',';
			$sql .= ' '.(! isset($this->civility_id)?'NULL':"'".$this->db->escape($this->civility_id)."'").',';
			$sql .= ' '.(! isset($this->lastname)?'NULL':"'".$this->db->escape($this->lastname)."'").',';
			$sql .= ' '.(! isset($this->firstname)?'NULL':"'".$this->db->escape($this->firstname)."'").',';
			$sql .= ' '.(! isset($this->address)?'NULL':"'".$this->db->escape($this->address)."'").',';
			$sql .= ' '.(! isset($this->zip)?'NULL':"'".$this->db->escape($this->zip)."'").',';
			$sql .= ' '.(! isset($this->town)?'NULL':"'".$this->db->escape($this->town)."'").',';
			$sql .= ' '.(! isset($this->fk_departement)?'NULL':$this->fk_departement).',';
			$sql .= ' '.(! isset($this->fk_pays)?'NULL':$this->fk_pays).',';
			$sql .= ' '.(! isset($this->birthday) || dol_strlen($this->birthday)==0?'NULL':"'".$this->db->idate($this->birthday)."'").',';
			$sql .= ' '.(! isset($this->poste)?'NULL':"'".$this->db->escape($this->poste)."'").',';
			$sql .= ' '.(! isset($this->phone)?'NULL':"'".$this->db->escape($this->phone)."'").',';
			$sql .= ' '.(! isset($this->phone_perso)?'NULL':"'".$this->db->escape($this->phone_perso)."'").',';
			$sql .= ' '.(! isset($this->phone_mobile)?'NULL':"'".$this->db->escape($this->phone_mobile)."'").',';
			$sql .= ' '.(! isset($this->fax)?'NULL':"'".$this->db->escape($this->fax)."'").',';
			$sql .= ' '.(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").',';
			$sql .= ' '.(! isset($this->jabberid)?'NULL':"'".$this->db->escape($this->jabberid)."'").',';
			$sql .= ' '.(! isset($this->skype)?'NULL':"'".$this->db->escape($this->skype)."'").',';
			$sql .= ' '.(! isset($this->photo)?'NULL':"'".$this->db->escape($this->photo)."'").',';
			//$sql .= ' '.(! isset($this->no_email)?'NULL':$this->no_email).',';
			$sql .= ' '.(! isset($this->priv)?'NULL':$this->priv).',';
			$sql .= ' '.$user->id.',';
			$sql .= ' '.(! isset($this->fk_user_modif)?'NULL':$this->fk_user_modif).',';
			$sql .= ' '.(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").',';
			$sql .= ' '.(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").',';
			$sql .= ' '.(! isset($this->default_lang)?'NULL':"'".$this->db->escape($this->default_lang)."'").',';
			$sql .= ' '.(! isset($this->canvas)?'NULL':"'".$this->db->escape($this->canvas)."'").',';
			$sql .= ' '.(! isset($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'").',';
			$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut);
			$sql.= ",".(! empty($this->edad)?$this->db->escape($this->edad):"NULL")." ";
	        $sql.= ",".(! empty($this->estatura)?$this->db->escape($this->estatura):"NULL")." ";
	        $sql.= ",".(! empty($this->sexo)?$this->db->escape($this->sexo):"0")." ";
	        $sql.= ",".(! empty($this->fk_user)?$this->fk_user:"NULL")." ";
			
			$sql .= ')';
		
		/*$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element." (";
		$sql.= " datec";
		$sql.= ", fk_soc";
        $sql.= ", lastname";
        $sql.= ", firstname";
        $sql.= ", fk_user_creat";
		$sql.= ", priv";
		$sql.= ", statut";
		$sql.= ", canvas";
		$sql.= ", entity";
		$sql.= ",ref_ext";
		$sql.= ", import_key";
		$sql.= ", edad";
		$sql.= ", estatura";
		$sql.= ", sexo";
		$sql.= ", fk_user";
		$sql.= ") VALUES (";
		$sql.= "'".$this->db->idate($now)."',";
		if ($this->socid > 0) $sql.= " ".$this->socid.",";
		else $sql.= "null,";
		$sql.= "'".$this->db->escape($this->lastname)."',";
        $sql.= "'".$this->db->escape($this->firstname)."',";
		$sql.= " ".($user->id > 0 ? "'".$user->id."'":"null").",";
		$sql.= " ".$this->priv.",";
		$sql.= " ".$this->statut.",";
        $sql.= " ".(! empty($this->canvas)?"'".$this->db->escape($this->canvas)."'":"null").",";
        $sql.= " ".$conf->entity.",";
        $sql.= "'".$this->db->escape($this->ref_ext)."',";
        $sql.= " ".(! empty($this->import_key)?"'".$this->import_key."'":"null");
        $sql.= ",".(! empty($this->edad)?$this->db->escape($this->edad):"0")." ";
        $sql.= ",".(! empty($this->estatura)?$this->db->escape($this->estatura):"0")." ";
        $sql.= ",".(! empty($this->sexo)?$this->db->escape($this->sexo):"0")." ";
        $sql.= ",".(! empty($this->fk_user)?$this->fk_user:"")." ";
		$sql.= ")";*/
		//print $sql;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (! $error)
			{
                $hookmanager->initHooks(array('pacientesdao'));
			    $parameters=array('socid'=>$this->id);
			    $reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
			    if (empty($reshook))
			    {
			    	if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			    	{
			    		$result=$this->insertExtraFields();
			    		if ($result < 0)
			    		{
			    			$error++;
			    		}
			    	}
			    }
			}
		}

		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		/*if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}*/

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	function getCivilityLabel()
	{
		global $langs;
		$langs->load("dict");

		$code=(! empty($this->civility_id)?$this->civility_id:(! empty($this->civilite_id)?$this->civilite_id:''));
		if (empty($code)) return '';
        return $langs->getLabelFromKey($this->db, "Civility".$code, "c_civility", "code", "label", $code);
	}
	function LibPubPriv($statut)
	{
		global $langs;
		if ($statut=='1') return $langs->trans('ContactPrivate');
		else return $langs->trans('ContactPublic');
	}
	function setstatus($statut)
	{
		global $conf,$langs,$user;

		$error=0;

		// Check parameters
		if ($this->statut == $statut) return 0;
		else $this->statut = $statut;

		$this->db->begin();

		// Desactive utilisateur
		$sql = "UPDATE ".MAIN_DB_PREFIX."pacientes";
		$sql.= " SET statut = ".$this->statut;
		$sql.= " WHERE rowid = ".$this->id;
		$result = $this->db->query($sql);

		dol_syslog(get_class($this)."::setstatus", LOG_DEBUG);
		if ($result)
		{
            // Call trigger
            $result=$this->call_trigger('CONTACT_ENABLEDISABLE',$user);
            if ($result < 0) { $error++; }
            // End call triggers
		}

		if ($error)
		{
			$this->db->rollback();
			return -$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}
	function update_perso($id, $user=null)
	{
	    $error=0;
	    $result=false;

		// Mis a jour contact
		$sql = "UPDATE ".MAIN_DB_PREFIX."pacientes SET";
		$sql.= " birthday=".($this->birthday ? "'".$this->db->idate($this->birthday)."'" : "null");
		if ($user) $sql .= ", fk_user_modif=".$user->id;
		$sql .=", edad=".(  (!empty($this->edad))? $this->edad:"null");
		$sql .=", estatura=".(! empty($this->estatura)?$this->estatura:"null");
		$sql .=", sexo=".(!empty($this->sexo)?$this->sexo:"0");
		$sql .=", fk_user=".$this->fk_user;
		$sql.= " WHERE rowid=".$this->db->escape($id);
		//print $sql;
		dol_syslog(get_class($this)."::update_perso this->birthday=".$this->birthday." -", LOG_DEBUG);

		$resql = $this->db->query($sql);
		if (! $resql)
		{
            $error++;
		    $this->error=$this->db->lasterror();
		}

		// Mis a jour alerte birthday
		if ($this->birthday_alert)
		{
			//check existing
			$sql_check = "SELECT * FROM ".MAIN_DB_PREFIX."user_alert WHERE type=1 AND fk_contact=".$this->db->escape($id)." AND fk_user=".$user->id;
			$result_check = $this->db->query($sql_check);
			if (! $result_check || ($this->db->num_rows($result_check)<1))
			{
				//insert
				$sql = "INSERT INTO ".MAIN_DB_PREFIX."user_alert(type,fk_contact,fk_user) ";
				$sql.= "VALUES (1,".$this->db->escape($id).",".$user->id.")";
				$result = $this->db->query($sql);
				if (! $result)
				{
                    $error++;
                    $this->error=$this->db->lasterror();
				}
			}
			else
			{
				$result = true;
			}
		}
		else
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."user_alert ";
			$sql.= "WHERE type=1 AND fk_contact=".$this->db->escape($id)." AND fk_user=".$user->id;
			$result = $this->db->query($sql);
			if (! $result)
			{
                $error++;
                $this->error=$this->db->lasterror();
			}
		}

		return $result;
	}
	function load_ref_elements()
	{
		// Compte les elements pour lesquels il est contact
		$sql ="SELECT tc.element, count(ec.rowid) as nb";
		$sql.=" FROM ".MAIN_DB_PREFIX."element_contact as ec, ".MAIN_DB_PREFIX."c_type_contact as tc";
		$sql.=" WHERE ec.fk_c_type_contact = tc.rowid";
		$sql.=" AND fk_socpeople = ". $this->id;
		$sql.=" GROUP BY tc.element";

		dol_syslog(get_class($this)."::load_ref_elements", LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			while($obj=$this->db->fetch_object($resql))
			{
				if ($obj->nb)
				{
					if ($obj->element=='facture')  $this->ref_facturation = $obj->nb;
					if ($obj->element=='contrat')  $this->ref_contrat = $obj->nb;
					if ($obj->element=='commande') $this->ref_commande = $obj->nb;
					if ($obj->element=='propal')   $this->ref_propal = $obj->nb;
				}
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error=$this->db->error()." - ".$sql;
			return -1;
		}
	}
	function fetch($id, $user=0, $ref_ext='')
	{
		global $langs;

		dol_syslog(get_class($this)."::fetch id=".$id, LOG_DEBUG);

		if (empty($id) && empty($ref_ext))
		{
			$this->error='BadParameter';
			return -1;
		}

		$langs->load("companies");

		$sql = "SELECT c.rowid, c.ref_ext, c.fk_soc, c.civility as civility_id, c.lastname, c.firstname,";
		$sql.= " c.address, c.statut, c.zip, c.town,";
		$sql.= " c.fk_pays as country_id,";
		$sql.= " c.fk_departement,";
		$sql.= " c.birthday,";
		$sql.= " c.edad,";
		$sql.= " c.sexo,";
		$sql.= " c.estatura,";
		$sql.= " c.fk_user,";
		$sql.= " c.poste, c.phone, c.phone_perso, c.phone_mobile, c.fax, c.email, c.jabberid, c.skype,";
        $sql.= " c.photo,";
		$sql.= " c.priv, c.note_private, c.note_public, c.default_lang, c.no_email, c.canvas,";
		$sql.= " c.import_key,";
		$sql.= " co.label as country, co.code as country_code,";
		$sql.= " d.nom as state, d.code_departement as state_code,";
		$sql.= " u.rowid as user_id, u.login as user_login,";
		$sql.= " s.nom as socname, s.address as socaddress, s.zip as soccp, s.town as soccity, s.default_lang as socdefault_lang";
		$sql.= " FROM ".MAIN_DB_PREFIX."pacientes as c";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as co ON c.fk_pays = co.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as d ON c.fk_departement = d.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON c.rowid = u.fk_socpeople";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON c.fk_soc = s.rowid";
		if ($id) $sql.= " WHERE c.rowid = ". $id;
		elseif ($ref_ext) $sql .= " WHERE c.ref_ext = '".$this->db->escape($ref_ext)."'";

		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{

				$obj = $this->db->fetch_object($resql);

				$this->id				= $obj->rowid;
				$this->ref				= $obj->rowid;
				$this->ref_ext			= $obj->ref_ext;
				$this->civility_id		= $obj->civility_id;
				$this->lastname			= $obj->lastname;
				$this->firstname		= $obj->firstname;
				$this->address			= $obj->address;
				$this->zip				= $obj->zip;
				$this->town				= $obj->town;

				$this->fk_departement	= $obj->fk_departement;    // deprecated
				$this->state_id			= $obj->fk_departement;
				$this->departement_code = $obj->state_code;	       // deprecated
				$this->state_code       = $obj->state_code;
				$this->departement		= $obj->state;	           // deprecated
				$this->state			= $obj->state;

				$this->country_id 		= $obj->country_id;
				$this->country_code		= $obj->country_id?$obj->country_code:'';
				$this->country			= $obj->country_id?($langs->trans('Country'.$obj->country_code)!='Country'.$obj->country_code?$langs->transnoentities('Country'.$obj->country_code):$obj->country):'';

				$this->socid			= $obj->fk_soc;
				$this->socname			= $obj->socname;
				$this->poste			= $obj->poste;
				$this->statut			= $obj->statut;

				$this->phone_pro		= trim($obj->phone);
				$this->fax				= trim($obj->fax);
				$this->phone_perso		= trim($obj->phone_perso);
				$this->phone_mobile		= trim($obj->phone_mobile);

				$this->email			= $obj->email;
				$this->jabberid			= $obj->jabberid;
        		$this->skype			= $obj->skype;
                $this->photo			= $obj->photo;
				$this->priv				= $obj->priv;
				$this->mail				= $obj->email;

				$this->birthday			= $this->db->jdate($obj->birthday);
				$this->note				= $obj->note_private;		// deprecated
				$this->note_private		= $obj->note_private;
				$this->note_public		= $obj->note_public;
				$this->default_lang		= $obj->default_lang;
				$this->no_email			= $obj->no_email;
				$this->user_id			= $obj->user_id;
				$this->user_login		= $obj->user_login;
				$this->canvas			= $obj->canvas;

				$this->import_key		= $obj->import_key;

				$this->edad				= $obj->edad;
				$this->estatura			= $obj->estatura;
				$this->sexo				= $obj->sexo;
				$this->fk_user			= $obj->fk_user;

				// Recherche le user Dolibarr lie a ce contact
				$sql = "SELECT u.rowid ";
				$sql .= " FROM ".MAIN_DB_PREFIX."user as u";
				$sql .= " WHERE u.fk_socpeople = ". $this->id;

				$resql=$this->db->query($sql);
				if ($resql)
				{
					if ($this->db->num_rows($resql))
					{
						$uobj = $this->db->fetch_object($resql);

						$this->user_id = $uobj->rowid;
					}
					$this->db->free($resql);
				}
				else
				{
					$this->error=$this->db->error();
					return -1;
				}

				// Charge alertes du user
				if ($user)
				{
					$sql = "SELECT fk_user";
					$sql .= " FROM ".MAIN_DB_PREFIX."user_alert";
					$sql .= " WHERE fk_user = ".$user->id." AND fk_contact = ".$this->db->escape($id);

					$resql=$this->db->query($sql);
					if ($resql)
					{
						if ($this->db->num_rows($resql))
						{
							$obj = $this->db->fetch_object($resql);

							$this->birthday_alert = 1;
						}
						$this->db->free($resql);
					}
					else
					{
						$this->error=$this->db->error();
						return -1;
					}
				}

				// Retreive all extrafield for contact
                // fetch optionals attributes and labels
                require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
                $extrafields=new ExtraFields($this->db);
                $extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
               	$this->fetch_optionals($this->id,$extralabels);

				return 1;
			}
			else
			{
				$this->error=$langs->trans("RecordNotFound");
				return 0;
			}
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_soc,";
		$sql .= " t.entity,";
		$sql .= " t.ref_ext,";
		$sql .= " t.civility,";
		$sql .= " t.lastname,";
		$sql .= " t.firstname,";
		$sql .= " t.address,";
		$sql .= " t.zip,";
		$sql .= " t.town,";
		$sql .= " t.fk_departement,";
		$sql .= " t.fk_pays,";
		$sql .= " t.birthday,";
		$sql .= " t.poste,";
		$sql .= " t.phone,";
		$sql .= " t.phone_perso,";
		$sql .= " t.phone_mobile,";
		$sql .= " t.fax,";
		$sql .= " t.email,";
		$sql .= " t.jabberid,";
		$sql .= " t.skype,";
		$sql .= " t.photo,";
		$sql .= " t.no_email,";
		$sql .= " t.priv,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.default_lang,";
		$sql .= " t.canvas,";
		$sql .= " t.import_key,";
		$sql .= " t.statut";
		$sql .= " t.edad";
		$sql .= " t.estatura";
		$sql .= " t.sexo";
		$sql .= " t.fk_user";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new PacientesLine();

				$line->id = $obj->rowid;
				
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_soc = $obj->fk_soc;
				$line->entity = $obj->entity;
				$line->ref_ext = $obj->ref_ext;
				$line->civility = $obj->civility;
				$line->lastname = $obj->lastname;
				$line->firstname = $obj->firstname;
				$line->address = $obj->address;
				$line->zip = $obj->zip;
				$line->town = $obj->town;
				$line->fk_departement = $obj->fk_departement;
				$line->fk_pays = $obj->fk_pays;
				$line->birthday = $this->db->jdate($obj->birthday);
				$line->poste = $obj->poste;
				$line->phone = $obj->phone;
				$line->phone_perso = $obj->phone_perso;
				$line->phone_mobile = $obj->phone_mobile;
				$line->fax = $obj->fax;
				$line->email = $obj->email;
				$line->jabberid = $obj->jabberid;
				$line->skype = $obj->skype;
				$line->photo = $obj->photo;
				$line->no_email = $obj->no_email;
				$line->priv = $obj->priv;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->default_lang = $obj->default_lang;
				$line->canvas = $obj->canvas;
				$line->import_key = $obj->import_key;
				$line->statut = $obj->statut;
				$line->edad = $obj->edad;
				$line->estatura = $obj->estatura;
				$line->sexo = $obj->sexo;
				$line->fk_user = $obj->fk_user;

				

				$this->lines[] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	function select_dolconsultas($selected='', $htmlname='inputref', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0){

	    global $conf,$user,$langs;

	    $out='';

	    // On recherche les utilisateurs
	    $sql = "SELECT u.rowid, u.Ref as nombre, u.statut";
	    $sql.= " FROM llx_consultas as u where u.fk_user_pacientes=".$this->id;
	   
	    /*if (! empty($user->societe_id)) $sql.= " AND u.fk_soc = ".$user->societe_id;
	    if (is_array($exclude) && $excludeUsers) $sql.= " AND u.rowid NOT IN ('".$excludeUsers."')";
	    if (is_array($include) && $includeUsers) $sql.= " AND u.rowid IN ('".$includeUsers."')";
	    if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX) || $noactive) $sql.= " AND u.statut <> 0";
	    if (! empty($morefilter)) $sql.=" ".$morefilter;*/
	    $sql.= " ORDER BY u.date_creation DESC";
	    dol_syslog(get_class($this)."::select_dolusers", LOG_DEBUG);
	    $resql=$this->db->query($sql);
	    if ($resql)
	    {
	        $num = $this->db->num_rows($resql);
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

	            $userstatic=new User($this->db);

	            while ($i < $num)
	            {
	                $obj = $this->db->fetch_object($resql);

	                $userstatic->id=$obj->rowid;
	                $userstatic->description=$obj->nombre;
	                $userstatic->active=$obj->statut;

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

	                $out.= $userstatic->description.'</option>';

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
	        dol_print_error($this->db);
	    }

	    return $out;
	}
	function update($id, $user=null, $notrigger=0, $action='update')
	{
		global $conf, $langs, $hookmanager;

		$error=0;

		$this->id = $id;

		// Clean parameters
		$this->lastname=trim($this->lastname)?trim($this->lastname):trim($this->lastname);
		$this->firstname=trim($this->firstname);
		$this->email=trim($this->email);
		$this->phone_pro=trim($this->phone_pro);
		$this->phone_perso=trim($this->phone_perso);
		$this->phone_mobile=trim($this->phone_mobile);
		$this->jabberid=trim($this->jabberid);
		$this->skype=trim($this->skype);
		$this->photo=trim($this->photo);
		$this->fax=trim($this->fax);
		$this->zip=(empty($this->zip)?'':$this->zip);
		$this->town=(empty($this->town)?'':$this->town);
		$this->country_id=($this->country_id > 0?$this->country_id:$this->country_id);
		$this->state_id=($this->state_id > 0?$this->state_id:$this->fk_departement);
		if (empty($this->statut)) $this->statut = 0;

		$this->db->begin();
		
		$sql = "UPDATE ".MAIN_DB_PREFIX."pacientes SET ";
		if ($this->socid > 0) $sql .= " fk_soc='".$this->db->escape($this->socid)."',";
		else if ($this->socid == -1) $sql .= " fk_soc=null,";
		$sql .= "  civility='".$this->db->escape($this->civility_id)."'";
		$sql .= ", lastname='".$this->db->escape($this->lastname)."'";
		$sql .= ", firstname='".$this->db->escape($this->firstname)."'";
		$sql .= ", address='".$this->db->escape($this->address)."'";
		$sql .= ", zip='".$this->db->escape($this->zip)."'";
		$sql .= ", town='".$this->db->escape($this->town)."'";
		$sql .= ", fk_pays=".($this->country_id>0?$this->country_id:'NULL');
		$sql .= ", fk_departement=".($this->state_id>0?$this->state_id:'NULL');
		$sql .= ", poste='".$this->db->escape($this->poste)."'";
		$sql .= ", fax='".$this->db->escape($this->fax)."'";
		$sql .= ", email='".$this->db->escape($this->email)."'";
		$sql .= ", skype='".$this->db->escape($this->skype)."'";
		$sql .= ", photo='".$this->db->escape($this->photo)."'";
		$sql .= ", note_private = ".(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null");
		$sql .= ", note_public = ".(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null");
		$sql .= ", phone = ".(isset($this->phone)?"'".$this->db->escape($this->phone)."'":"null");
		$sql .= ", phone_perso = ".(isset($this->phone_perso)?"'".$this->db->escape($this->phone_perso)."'":"null");
		$sql .= ", phone_mobile = ".(isset($this->phone_mobile)?"'".$this->db->escape($this->phone_mobile)."'":"null");
		$sql .= ", jabberid = ".(isset($this->jabberid)?"'".$this->db->escape($this->jabberid)."'":"null");
		$sql .= ", priv = '".$this->priv."'";
		$sql .= ", statut = ".$this->statut;
		$sql .= ", fk_user_modif=".($user->id > 0 ? "'".$user->id."'":"NULL");
		$sql .= ", default_lang=".($this->default_lang?"'".$this->default_lang."'":"NULL");
		$sql .= ", no_email=".($this->no_email?"'".$this->no_email."'":"0");
		$sql .= " WHERE rowid=".$this->db->escape($id);


		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		
		$result = $this->db->query($sql);
		if ($result)
		{
		    unset($this->country_code);
		    unset($this->country);
		    unset($this->state_code);
		    unset($this->state);

		    $action='update';

		    // Actions on extra fields (by external module or standard code)
		    $hookmanager->initHooks(array('pacientesdao'));
		    $parameters=array('socid'=>$this->id);
		    $reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
		    if (empty($reshook))
		    {
		    	if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
		    	{
		    		$result=$this->insertExtraFields();
		    		if ($result < 0)
		    		{
		    			$error++;
		    		}
		    	}
		    }
		    else if ($reshook < 0) $error++;

			if (! $error && ! $notrigger)
			{
                // Call trigger
                $result=$this->call_trigger('CONTACT_MODIFY',$user);
                if ($result < 0) { $error++; }
                // End call triggers
			}

			if (! $error)
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				dol_syslog(get_class($this)."::update Error ".$this->error,LOG_ERR);
				$this->db->rollback();
				return -$error;
			}
		}
		else
		{
			$this->error=$this->db->lasterror().' sql='.$sql;
            $this->db->rollback();
			return -1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function vincular_paciente_evento($evento,$consulta)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$sql = 'INSERT INTO llx_eventos_consultas(';
		$sql.= 'fk_paciente,';
		$sql.= 'fk_evento,';
		$sql.= 'fk_consulta';
		$sql.= ') VALUES (';
		$sql.= ' '.$this->id.',';
		$sql.= ' '.$evento.",";
		$sql.= ' '.(empty($consulta)?'NULL':$consulta);
		$sql .=')';
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}
	function delete($notrigger=0)
	{
		global $conf, $langs, $user;

		$error=0;

		$this->old_lastname       = $obj->lastname;
		$this->old_firstname      = $obj->firstname;

		$this->db->begin();

		/*if (! $error)
		{
			// Get all rowid of element_contact linked to a type that is link to llx_socpeople
			$sql = "SELECT ec.rowid";
			$sql.= " FROM ".MAIN_DB_PREFIX."element_contact ec,";
			$sql.= " ".MAIN_DB_PREFIX."c_type_contact tc";
			$sql.= " WHERE ec.fk_socpeople=".$this->id;
			$sql.= " AND ec.fk_c_type_contact=tc.rowid";
			$sql.= " AND tc.source='external'";
			dol_syslog(get_class($this)."::delete", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql)
			{
				$num=$this->db->num_rows($resql);

				$i=0;
				while ($i < $num && ! $error)
				{
					$obj = $this->db->fetch_object($resql);

					$sqldel = "DELETE FROM ".MAIN_DB_PREFIX."element_contact";
					$sqldel.=" WHERE rowid = ".$obj->rowid;
					dol_syslog(get_class($this)."::delete", LOG_DEBUG);
					$result = $this->db->query($sqldel);
					if (! $result)
					{
						$error++;
						$this->error=$this->db->error().' sql='.$sqldel;
					}

					$i++;
				}
			}
			else
			{
				$error++;
				$this->error=$this->db->error().' sql='.$sql;
			}
		}

		if (! $error)
		{
			// Remove category
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."categorie_contact WHERE fk_socpeople = ".$this->id;
			dol_syslog(get_class($this)."::delete", LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql)
			{
				$error++;
				$this->error .= $this->db->lasterror();
				$errorflag=-1;
			}
		}*/

		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."pacientes";
			$sql .= " WHERE rowid=".$this->id;
			dol_syslog(get_class($this)."::delete", LOG_DEBUG);
			//print $sql;
			$result = $this->db->query($sql);
			if (! $result)
			{
				$error++;
				$this->error=$this->db->error().' sql='.$sql;
			}
		}

		// Removed extrafields
		 if ((! $error) && (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED))) { // For avoid conflicts if trigger used
			$result=$this->deleteExtraFields($this);
			if ($result < 0) $error++;
		}

		if (! $error && ! $notrigger)
		{
            // Call trigger
            $result=$this->call_trigger('CONTACT_DELETE',$user);
            if ($result < 0) { $error++; }
            // End call triggers
		}

		if (! $error)
		{

			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			dol_syslog("Error ".$this->error,LOG_ERR);
			return -1;
		}
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Pacientes($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 *  Return a link to the user card (with optionaly the picto)
	 * 	Use this->id,this->lastname, this->firstname
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	integer	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='',$type=0)
	{
		global $langs, $conf, $db;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;
        $this->fetch($this->id);
        $result = '';
        $companylink = '';
        $label = '<u>Paciente</u>';
 
        $label.= '</br><b>Nombre:</b> ' . $this->lastname." ". $this->firstname;
        $link = '<a href="'.DOL_URL_ROOT.'/cclinico/pacientes_card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '> ';
        //$link.=$this->lastname;
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		if ($type==0) {
			$result.= $link . $this->lastname . $linkend;
		}else{
			$result.= $link . $this->lastname." ".$this->firstname . $linkend;
		}
		
		return $result;
	}
	function show_actions_todo($conf,$langs,$db,$object,$objcon='',$noprint=0)
	{
	    global $bc,$user,$conf;

	    // Check parameters
	    if (! is_object($object)) dol_print_error('','BadParameter');

	    $now=dol_now('tzuser');
	    $out='';

	    if (! empty($conf->agenda->enabled))
	    {
	        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	        $actionstatic=new ActionComm($db);
	        $userstatic=new User($db);
	        $contactstatic = new Contact($db);
	        $out.='<table width="100%" class="noborder">';
	        $out.='<tr class="liste_titre">';
			if($conf->global->AGENDA_USE_EVENT_TYPE) $out.='<td colspan="3">';
			else $out.='<td colspan="2">';
	        if (get_class($object) == 'Societe') $out.='<a href="'.DOL_URL_ROOT.'/comm/action/listactions.php?socid='.$object->id.'&amp;status=todo">';
	        $out.=$langs->trans("ActionsToDoShort");
	        if (get_class($object) == 'Societe') $out.='</a>';
	        $out.='</td>';
			
			if($conf->global->AGENDA_USE_EVENT_TYPE) {
				$out.='<td>';
				$out.=$langs->trans("Type");
				$out.='</td>';
				$out.='<td colspan="3" align="right">';
			} else {
				$out.='<td colspan="4" align="right">';
			}
	        
	        $out.='</td>';
	        $out.='</tr>';

	        $sql = "SELECT a.id, a.label,";
	        $sql.= " a.datep as dp,";
	        $sql.= " a.datep2 as dp2,";
	        $sql.= " a.percent,";
	        $sql.= " a.fk_user_author, a.fk_contact,";
	        $sql.= " a.fk_element, a.elementtype,";
	        $sql.= " c.code as acode, c.libelle,";
	        $sql.= " u.login, u.rowid";
	        $sql.= " FROM
						llx_user AS u
					INNER JOIN	llx_actioncomm AS a ON u.rowid = a.fk_user_author
					INNER JOIN llx_eventos_consultas AS b ON b.fk_evento=a.id
					inner JOIN llx_c_actioncomm AS c ON a.fk_action = c.id";
	        $sql.= " WHERE a.entity IN (".getEntity('agenda', 1).")";
	        $sql.= " AND b.fk_paciente=".$this->id;
	        if (! empty($objcon->id)) $sql.= " AND a.fk_contact = ".$objcon->id;
	        $sql.= " AND ((a.percent >= 0 AND a.percent < 100) OR (a.percent = -1 AND a.datep > '".$db->idate($now)."'))";
	        $sql.= " ORDER BY a.datep DESC, a.id DESC";

	        $result=$db->query($sql);
	        if ($result)
	        {
	            $i = 0 ;
	            $num = $db->num_rows($result);
	            $var=true;

	            if ($num)
	            {
	                while ($i < $num)
	                {
	                    $var = !$var;

	                    $obj = $db->fetch_object($result);
						$actionstatic->fetch($obj->id);
	                    $datep=$db->jdate($obj->dp);
	                    $datep2=$db->jdate($obj->dp2);

	                    $out.="<tr ".$bc[$var].">";

	                    // Date
	                    $out.='<td width="120" align="left" class="nowrap">';
	                    $out.=dol_print_date($datep,'dayhour');
	                    if ($datep2 && $datep2 != $datep)
		        		{
			        		$tmpa=dol_getdate($datep,true);
			        		$tmpb=dol_getdate($datep2,true);
			        		if ($tmpa['mday'] == $tmpb['mday'] && $tmpa['mon'] == $tmpb['mon'] && $tmpa['year'] == $tmpb['year']) $out.='-'.dol_print_date($datep2,'hour');
			        		else $out.='-'.dol_print_date($datep2,'dayhour');
		        		}
	                    $out.="</td>\n";

	                    // Picto warning
	                    $out.='<td width="16">';
	                    if ($obj->percent >= 0 && $datep && $datep < ($now - ($conf->global->MAIN_DELAY_ACTIONS_TODO *60*60*24)) ) $out.=' '.img_warning($langs->trans("Late"));
	                    else $out.='&nbsp;';
	                    $out.='</td>';

	                    $actionstatic->type_code=$obj->acode;
	                    $transcode=$langs->trans("Action".$obj->acode);
	                    $libelle=($transcode!="Action".$obj->acode?$transcode:$obj->libelle);
	                    //$actionstatic->libelle=$libelle;
	                    $actionstatic->libelle=$obj->label;
	                    $actionstatic->id=$obj->id;
	                    //$out.='<td width="140">'.$actionstatic->getNomUrl(1,16).'</td>';

	                    // Title of event
	                    //$out.='<td colspan="2">'.dol_trunc($obj->label,40).'</td>';
	                    $out.='<td>'.$actionstatic->getNomUrl(1,120).'</td>';
						
						if($conf->global->AGENDA_USE_EVENT_TYPE) {
							$out.= '<td>';
							$out.=$actionstatic->type;
							$out.='</td>';
						}
	                    // Contact pour cette action
	                    if (empty($objcon->id) && $obj->fk_contact > 0)
	                    {
	                        $contactstatic->lastname=$obj->lastname;
	                        $contactstatic->firstname=$obj->firstname;
	                        $contactstatic->id=$obj->fk_contact;
	                        $out.='<td width="120">'.$contactstatic->getNomUrl(1,'',10).'</td>';
	                    }
	                    else
	                    {
	                        $out.='<td>&nbsp;</td>';
	                    }


	                    // Statut
	                    $out.='<td class="nowrap" width="20">'.$actionstatic->LibStatut($obj->percent,3).'</td>';

	                    $out.="</tr>\n";
	                    $i++;
	                }
	            }
	            else
	            {
	                // Aucun action a faire

	            }
	            $db->free($result);
	        }
	        else
	        {
	            dol_print_error($db);
	        }
	        $out.="</table>\n";

	        $out.="<br>\n";
	    }

	    if ($noprint) return $out;
	    else print $out;
	}



	function show_actions_done($conf,$langs,$db,$object,$objcon='',$noprint=0)
	{
	    global $bc,$user,$conf;

	    // Check parameters
	    if (! is_object($object)) dol_print_error('','BadParameter');

	    $out='';
	    $histo=array();
	    $numaction = 0 ;
	    $now=dol_now('tzuser');

	    if (! empty($conf->agenda->enabled))
	    {
	        // Recherche histo sur actioncomm
	        $sql = "SELECT a.id, a.label,";
	        $sql.= " a.datep as dp,";
	        $sql.= " a.datep2 as dp2,";
	        $sql.= " a.note, a.percent,";
	        $sql.= " a.fk_element, a.elementtype,";
	        $sql.= " a.fk_user_author, a.fk_contact,";
	        $sql.= " c.code as acode, c.libelle,";
	        $sql.= " u.login, u.rowid as user_id";
	        $sql.= " FROM
						llx_user AS u
					INNER JOIN	llx_actioncomm AS a ON u.rowid = a.fk_user_author
					INNER JOIN llx_eventos_consultas AS b ON b.fk_evento=a.id
					inner JOIN llx_c_actioncomm AS c ON a.fk_action = c.id";
	        $sql.= " WHERE b.fk_paciente=".$this->id;
	        $sql.= " AND a.entity IN (".getEntity('agenda', 1).")";
	        $sql.= " AND (a.percent = 100 OR (a.percent = -1 AND a.datep <= '".$db->idate($now)."'))";
	        $sql.= " ORDER BY a.datep DESC, a.id DESC";

	        dol_syslog("company.lib::show_actions_done", LOG_DEBUG);
	        $resql=$db->query($sql);
	        if ($resql)
	        {
	            $i = 0 ;
	            $num = $db->num_rows($resql);
	            $var=true;
	            while ($i < $num)
	            {
	                $obj = $db->fetch_object($resql);
	                $histo[$numaction]=array(
	                		'type'=>'action',
	                		'id'=>$obj->id,
	                		'datestart'=>$db->jdate($obj->dp),
	                		'dateend'=>$db->jdate($obj->dp2),
	                		'note'=>$obj->label,
	                		'percent'=>$obj->percent,
	                		'acode'=>$obj->acode,
	                		'libelle'=>$obj->libelle,
	                		'userid'=>$obj->user_id,
	                		'login'=>$obj->login,
	                		'contact_id'=>$obj->fk_contact,
	                		'lastname'=>$obj->lastname,
	                		'firstname'=>$obj->firstname,
	                		'fk_element'=>$obj->fk_element,
	                		'elementtype'=>$obj->elementtype
	                );
	                $numaction++;
	                $i++;
	            }
	        }
	        else
	        {
	            dol_print_error($db);
	        }
	    }

	    if (! empty($conf->mailing->enabled) && ! empty($objcon->email))
	    {
	        $langs->load("mails");

	        // Recherche histo sur mailing
	        $sql = "SELECT m.rowid as id, mc.date_envoi as da, m.titre as note, '100' as percentage,";
	        $sql.= " 'AC_EMAILING' as acode,";
	        $sql.= " u.rowid as user_id, u.login";	// User that valid action
	        $sql.= " FROM ".MAIN_DB_PREFIX."mailing as m, ".MAIN_DB_PREFIX."mailing_cibles as mc, ".MAIN_DB_PREFIX."user as u";
	        $sql.= " WHERE mc.email = '".$db->escape($objcon->email)."'";	// Search is done on email.
	        $sql.= " AND mc.statut = 1";
	        $sql.= " AND u.rowid = m.fk_user_valid";
	        $sql.= " AND mc.fk_mailing=m.rowid";
	        $sql.= " ORDER BY mc.date_envoi DESC, m.rowid DESC";

	        dol_syslog("company.lib::show_actions_done", LOG_DEBUG);
	        $resql=$db->query($sql);
	        if ($resql)
	        {
	            $i = 0 ;
	            $num = $db->num_rows($resql);
	            $var=true;
	            while ($i < $num)
	            {
	                $obj = $db->fetch_object($resql);
	                $histo[$numaction]=array(
	                		'type'=>'mailing',
	                		'id'=>$obj->id,
	                		'datestart'=>$db->jdate($obj->da),
	                		'dateend'=>$db->jdate($obj->da),
	                		'note'=>$obj->note,
	                		'percent'=>$obj->percentage,
	                		'acode'=>$obj->acode,
	                		'userid'=>$obj->user_id,
	                		'login'=>$obj->login
					);
	                $numaction++;
	                $i++;
	            }
		        $db->free($resql);
	        }
	        else
	        {
	            dol_print_error($db);
	        }
	    }


	    if (! empty($conf->agenda->enabled) || (! empty($conf->mailing->enabled) && ! empty($objcon->email)))
	    {
	        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	        require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	        require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
	        require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
	        $actionstatic=new ActionComm($db);
	        
	        $contactstatic = new Contact($db);

	        // TODO uniformize
	        $propalstatic=new Propal($db);
	        $orderstatic=new Commande($db);
	        $facturestatic=new Facture($db);

	        $out.="\n";
	        $out.='<table class="noborder" width="100%">';
	        $out.='<tr class="liste_titre">';
			if($conf->global->AGENDA_USE_EVENT_TYPE) $out.='<td colspan="3">';
			else $out.='<td colspan="2">';
	        if (get_class($object) == 'Societe') $out.='<a href="'.DOL_URL_ROOT.'/comm/action/listactions.php?socid='.$object->id.'&amp;status=done">';
	        $out.=$langs->trans("ActionsDoneShort");
	        if (get_class($object) == 'Societe') $out.='</a>';
	        $out.='</td>';
			
			if($conf->global->AGENDA_USE_EVENT_TYPE) {
				$out.='<td>';
				$out.=$langs->trans("Type");
				$out.='</td>';
				$out.='<td colspan="4" align="right">';
			} else {
				$out.='<td colspan="5" align="right">';
			}
	        
	        $out.='</td>';
	        $out.='</tr>';

	        foreach ($histo as $key=>$value)
	        {
	            $var=!$var;
	            $out.="<tr ".$bc[$var].">";
				$actionstatic->fetch($histo[$key]['id']);
	            // Champ date
	            $out.='<td width="120" class="nowrap">';
	            $out.=dol_print_date($histo[$key]['datestart'],'dayhour');
	            if ($histo[$key]['dateend'] && $histo[$key]['dateend'] != $histo[$key]['datestart'])
	            {
	        		$tmpa=dol_getdate($histo[$key]['datestart'],true);
	        		$tmpb=dol_getdate($histo[$key]['dateend'],true);
	        		if ($tmpa['mday'] == $tmpb['mday'] && $tmpa['mon'] == $tmpb['mon'] && $tmpa['year'] == $tmpb['year']) $out.='-'.dol_print_date($histo[$key]['dateend'],'hour');
	        		else $out.='-'.dol_print_date($histo[$key]['dateend'],'dayhour');
	            }
	            $out.="</td>\n";

	            // Picto
	            $out.='<td width="16">&nbsp;</td>';

	            // Action
	            $out.='<td>';
	            if (isset($histo[$key]['type']) && $histo[$key]['type']=='action')
	            {
	                $actionstatic->type_code=$histo[$key]['acode'];
	                $transcode=$langs->trans("Action".$histo[$key]['acode']);
	                $libelle=($transcode!="Action".$histo[$key]['acode']?$transcode:$histo[$key]['libelle']);
	                //$actionstatic->libelle=$libelle;
	                $actionstatic->libelle=$histo[$key]['note'];
	                $actionstatic->id=$histo[$key]['id'];
	                $out.=$actionstatic->getNomUrl(1,120);
	            }
	            if (isset($histo[$key]['type']) && $histo[$key]['type']=='mailing')
	            {
	                $out.='<a href="'.DOL_URL_ROOT.'/comm/mailing/card.php?id='.$histo[$key]['id'].'">'.img_object($langs->trans("ShowEMailing"),"email").' ';
	                $transcode=$langs->trans("Action".$histo[$key]['acode']);
	                $libelle=($transcode!="Action".$histo[$key]['acode']?$transcode:'Send mass mailing');
	                $out.=dol_trunc($libelle,120);
	            }
	            $out.='</td>';
				
				if($conf->global->AGENDA_USE_EVENT_TYPE) {
					$out.='<td>';
					$out.=$actionstatic->type;
					$out.='</td>';
				}
	            // Title of event
	            //$out.='<td>'.dol_trunc($histo[$key]['note'], 40).'</td>';

	            // Objet lie
	            // TODO uniformize
	            $out.='<td>';
	            //var_dump($histo[$key]['elementtype']);
	            if (isset($histo[$key]['elementtype']))
	            {
	            	if ($histo[$key]['elementtype'] == 'propal' && ! empty($conf->propal->enabled))
	            	{
	            		//$propalstatic->ref=$langs->trans("ProposalShort");
	            		//$propalstatic->id=$histo[$key]['fk_element'];
	                    if ($propalstatic->fetch($histo[$key]['fk_element'])>0) {
	                        $propalstatic->type=$histo[$key]['ftype'];
	                        $out.=$propalstatic->getNomUrl(1);
	                    } else {
	                        $out.= $langs->trans("ProposalDeleted");
	                    }
	             	}
	            	elseif (($histo[$key]['elementtype'] == 'order' || $histo[$key]['elementtype'] == 'commande') && ! empty($conf->commande->enabled))
	            	{
	            		//$orderstatic->ref=$langs->trans("Order");
	            		//$orderstatic->id=$histo[$key]['fk_element'];
	                    if ($orderstatic->fetch($histo[$key]['fk_element'])>0) {
	                        $orderstatic->type=$histo[$key]['ftype'];
	                        $out.=$orderstatic->getNomUrl(1);
	                    } else {
	                        $out.= $langs->trans("OrderDeleted");
	                    }
	             	}
	            	elseif (($histo[$key]['elementtype'] == 'invoice' || $histo[$key]['elementtype'] == 'facture') && ! empty($conf->facture->enabled))
	            	{
	            		//$facturestatic->ref=$langs->trans("Invoice");
	            		//$facturestatic->id=$histo[$key]['fk_element'];
	                    if ($facturestatic->fetch($histo[$key]['fk_element'])>0) {
	                        $facturestatic->type=$histo[$key]['ftype'];
	                        $out.=$facturestatic->getNomUrl(1,'compta');
	                    } else {
	                        $out.= $langs->trans("InvoiceDeleted");
	                    }
	            	}
	            	else $out.='&nbsp;';
	            }
	            else $out.='&nbsp;';
	            $out.='</td>';

	            // Contact pour cette action
	            if (! empty($objcon->id) && isset($histo[$key]['contact_id']) && $histo[$key]['contact_id'] > 0)
	            {
	                $contactstatic->lastname=$histo[$key]['lastname'];
	                $contactstatic->firstname=$histo[$key]['firstname'];
	                $contactstatic->id=$histo[$key]['contact_id'];
	                $out.='<td width="120">'.$contactstatic->getNomUrl(1,'',10).'</td>';
	            }
	            else
	            {
	                $out.='<td>&nbsp;</td>';
	            }

	            // Auteur
	            $out.='<td class="nowrap" width="80">';
	            //$userstatic->id=$histo[$key]['userid'];
	            //$userstatic->login=$histo[$key]['login'];
	            //$out.=$userstatic->getLoginUrl(1);
	            $userstatic=new User($db);
	            $userstatic->fetch($histo[$key]['userid']);
	            $out.=$userstatic->getNomUrl(1);
	            $out.='</td>';

	            // Statut
	            $out.='<td class="nowrap" width="20">'.$actionstatic->LibStatut($histo[$key]['percent'],3).'</td>';

	            $out.="</tr>\n";
	            $i++;
	        }
	        $out.="</table>\n";
	        //$out.="<br>\n";
	    }

	    if ($noprint) return $out;
	    else print $out;
	}
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->statut,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}
	
	
	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;
		
		$this->datec = '';
		$this->tms = '';
		$this->fk_soc = '';
		$this->entity = '';
		$this->ref_ext = '';
		$this->civility = '';
		$this->lastname = '';
		$this->firstname = '';
		$this->address = '';
		$this->zip = '';
		$this->town = '';
		$this->fk_departement = '';
		$this->fk_pays = '';
		$this->birthday = '';
		$this->poste = '';
		$this->phone = '';
		$this->phone_perso = '';
		$this->phone_mobile = '';
		$this->fax = '';
		$this->email = '';
		$this->jabberid = '';
		$this->skype = '';
		$this->photo = '';
		$this->no_email = '';
		$this->priv = '';
		$this->fk_user_creat = '';
		$this->fk_user_modif = '';
		$this->note_private = '';
		$this->note_public = '';
		$this->default_lang = '';
		$this->canvas = '';
		$this->import_key = '';
		$this->statut = '';
		$this->edad = '';
		$this->estatura = '';
		$this->sexo = '';
		$this->fk_user = '';

		
	}

}

/**
 * Class PacientesLine
 */
class PacientesLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $datec = '';
	public $tms = '';
	public $fk_soc;
	public $entity;
	public $ref_ext;
	public $civility;
	public $lastname;
	public $firstname;
	public $address;
	public $zip;
	public $town;
	public $fk_departement;
	public $fk_pays;
	public $birthday = '';
	public $poste;
	public $phone;
	public $phone_perso;
	public $phone_mobile;
	public $fax;
	public $email;
	public $jabberid;
	public $skype;
	public $photo;
	public $no_email;
	public $priv;
	public $fk_user_creat;
	public $fk_user_modif;
	public $note_private;
	public $note_public;
	public $default_lang;
	public $canvas;
	public $import_key;
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
