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
 * \file    contab/contabsociete.class.php
 * \ingroup contab
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Contabsociete
 *
 * Put here description of your class
 * @see CommonObject
 */
class Contabsociete extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'contabsociete';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_societe';

	/**
	 * @var ContabsocieteLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $nom;
	public $entity;
	public $statut;
	public $tms = '';
	public $datec = '';
	public $status;
	public $address;
	public $zip;
	public $town;
	public $fk_departement;
	public $fk_pays;
	public $phone;
	public $fax;
	public $url;
	public $email;
	public $note_private;
	public $note_public;
	public $fk_user_creat;
	public $fk_user_modif;

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
	public function create(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		
		if (isset($this->nom)) {
			 $this->nom = trim($this->nom);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		if (isset($this->address)) {
			 $this->address = trim($this->address);
		}
		if (isset($this->zip)) {
			 $this->zip = trim($this->zip);
		}
		if (isset($this->town)) {
			 $this->town = trim($this->town);
		}
		if (isset($this->fk_departement)) {
			 $this->fk_departement = trim($this->fk_departement);
		}
		if (isset($this->fk_pays)) {
			 $this->fk_pays = trim($this->fk_pays);
		}
		if (isset($this->phone)) {
			 $this->phone = trim($this->phone);
		}
		if (isset($this->fax)) {
			 $this->fax = trim($this->fax);
		}
		if (isset($this->url)) {
			 $this->url = trim($this->url);
		}
		if (isset($this->email)) {
			 $this->email = trim($this->email);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'nom,';
		$sql.= 'tip_prov,';
		$sql.= 'rfc,';
		$sql.= 'id_fiscal,';
		$sql.= 'tip_op,';

		$sql.= 'entity,';
		$sql.= 'statut,';
		$sql.= 'datec,';
		$sql.= 'address,';
		$sql.= 'zip,';
		$sql.= 'town,';
		$sql.= 'fk_departement,';
		$sql.= 'fk_pays,';
		$sql.= 'phone,';
		$sql.= 'fax,';
		$sql.= 'url,';
		$sql.= 'email,';
		$sql.= 'note_private,';
		$sql.= 'note_public,';
		$sql.= 'fk_user_creat,';
		$sql.= 'fk_user_modif';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->nom)?'NULL':"'".$this->db->escape($this->nom)."'").',';
		$sql .= ' '.(! isset($this->tip_prov)?'NULL':"'".$this->db->escape($this->tip_prov)."'").',';
		
		$sql .= ' '.(! isset($this->rfc)?'NULL':"'".$this->db->escape($this->rfc)."'").',';
		$sql .= ' '.(! isset($this->id_fiscal)?'NULL':"'".$this->db->escape($this->id_fiscal)."'").',';
		$sql .= ' '.(! isset($this->tip_op)?'NULL':"'".$this->db->escape($this->tip_op)."'").',';



		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut).',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->address)?'NULL':"'".$this->db->escape($this->address)."'").',';
		$sql .= ' '.(! isset($this->zip)?'NULL':"'".$this->db->escape($this->zip)."'").',';
		$sql .= ' '.(! isset($this->town)?'NULL':"'".$this->db->escape($this->town)."'").',';
		$sql .= ' '.(! isset($this->fk_departement)?'NULL':$this->fk_departement).',';
		$sql .= ' '.(! isset($this->fk_pays)?'NULL':$this->fk_pays).',';
		$sql .= ' '.(! isset($this->phone)?'NULL':"'".$this->db->escape($this->phone)."'").',';
		$sql .= ' '.(! isset($this->fax)?'NULL':"'".$this->db->escape($this->fax)."'").',';
		$sql .= ' '.(! isset($this->url)?'NULL':"'".$this->db->escape($this->url)."'").',';
		$sql .= ' '.(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").',';
		$sql .= ' NULL,';
		$sql .= ' NULL,';
		$sql .= ' '.$user->id.',';
		$sql .= ' NULL';

		
		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}

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
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		$sql .= ' t.tip_prov,';	
		$sql .= ' t.rfc,';	
		$sql .= ' t.id_fiscal,';
		$sql .= ' t.tip_op,';	
		

		$sql .= " t.nom,";
		$sql .= " t.entity,";
		$sql .= " t.statut,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.address,";
		$sql .= " t.zip,";
		$sql .= " t.town,";
		$sql .= " t.fk_departement,";
		$sql .= " t.fk_pays,";
		$sql .= " t.phone,";
		$sql .= " t.fax,";
		$sql .= " t.url,";
		$sql .= " t.email,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				$this->rowid = $obj->rowid;

				$this->tip_prov=$obj->tip_prov;	
				$this->rfc=$obj->rfc;	
				$this->id_fiscal=$obj->id_fiscal;
				$this->tip_op=$obj->tip_op;	



				$this->nom = $obj->nom;
				$this->entity = $obj->entity;
				$this->statut = $obj->statut;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datec = $this->db->jdate($obj->datec);
				$this->address = $obj->address;
				$this->zip = $obj->zip;
				$this->town = $obj->town;
				$this->fk_departement = $obj->fk_departement;
				$this->fk_pays = $obj->fk_pays;
				$this->phone = $obj->phone;
				$this->fax = $obj->fax;
				$this->url = $obj->url;
				$this->email = $obj->email;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;

				
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
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
		
		$sql .= " t.nom,";
		$sql .= " t.entity,";
		$sql .= " t.statut,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.address,";
		$sql .= " t.zip,";
		$sql .= " t.town,";
		$sql .= " t.fk_departement,";
		$sql .= " t.fk_pays,";
		$sql .= " t.phone,";
		$sql .= " t.fax,";
		$sql .= " t.url,";
		$sql .= " t.email,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif";

		
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
				$line = new ContabsocieteLine();

				$line->id = $obj->rowid;
				
				$line->nom = $obj->nom;
				$line->entity = $obj->entity;
				$line->statut = $obj->statut;
				$line->tms = $this->db->jdate($obj->tms);
				$line->datec = $this->db->jdate($obj->datec);
				$line->address = $obj->address;
				$line->zip = $obj->zip;
				$line->town = $obj->town;
				$line->fk_departement = $obj->fk_departement;
				$line->fk_pays = $obj->fk_pays;
				$line->phone = $obj->phone;
				$line->fax = $obj->fax;
				$line->url = $obj->url;
				$line->email = $obj->email;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;

				

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
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters
		
		if (isset($this->nom)) {
			 $this->nom = trim($this->nom);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		if (isset($this->address)) {
			 $this->address = trim($this->address);
		}
		if (isset($this->zip)) {
			 $this->zip = trim($this->zip);
		}
		if (isset($this->town)) {
			 $this->town = trim($this->town);
		}
		if (isset($this->fk_departement)) {
			 $this->fk_departement = trim($this->fk_departement);
		}
		if (isset($this->fk_pays)) {
			 $this->fk_pays = trim($this->fk_pays);
		}
		if (isset($this->phone)) {
			 $this->phone = trim($this->phone);
		}
		if (isset($this->fax)) {
			 $this->fax = trim($this->fax);
		}
		if (isset($this->url)) {
			 $this->url = trim($this->url);
		}
		if (isset($this->email)) {
			 $this->email = trim($this->email);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' nom = '.(isset($this->nom)?"'".$this->db->escape($this->nom)."'":"null").',';


		$sql .= ' tip_prov = '.(isset($this->tip_prov)?"'".$this->db->escape($this->tip_prov)."'":"null").',';
		$sql .= ' rfc = '.(isset($this->rfc)?"'".$this->db->escape($this->rfc)."'":"null").',';
		$sql .= ' id_fiscal = '.(isset($this->id_fiscal)?"'".$this->db->escape($this->id_fiscal)."'":"null").',';
		$sql .= ' tip_op = '.(isset($this->tip_op)?"'".$this->db->escape($this->tip_op)."'":"null").',';




		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' address = '.(isset($this->address)?"'".$this->db->escape($this->address)."'":"null").',';
		$sql .= ' zip = '.(isset($this->zip)?"'".$this->db->escape($this->zip)."'":"null").',';
		$sql .= ' town = '.(isset($this->town)?"'".$this->db->escape($this->town)."'":"null").',';
		$sql .= ' fk_departement = '.(isset($this->fk_departement)?$this->fk_departement:"null").',';
		$sql .= ' fk_pays = '.(isset($this->fk_pays)?$this->fk_pays:"null").',';
		$sql .= ' phone = '.(isset($this->phone)?"'".$this->db->escape($this->phone)."'":"null").',';
		$sql .= ' fax = '.(isset($this->fax)?"'".$this->db->escape($this->fax)."'":"null").',';
		$sql .= ' url = '.(isset($this->url)?"'".$this->db->escape($this->url)."'":"null").',';
		$sql .= ' email = '.(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").',';
		$sql .= ' note_private = '.(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").',';
		$sql .= ' note_public = '.(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").',';
		$sql .= ' fk_user_creat = '.(isset($this->fk_user_creat)?$this->fk_user_creat:"null").',';
		$sql .= ' fk_user_modif = '.(isset($this->fk_user_modif)?$this->fk_user_modif:"null");

        
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
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
	public function delete(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (!$error) {
			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_DELETE',$user);
				//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				//// End call triggers
			}
		}

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
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
		$object = new Contabsociete($this->db);

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
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $langs, $conf, $db;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;


        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("Contab") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>Nombre:</b> ' . $this->nom;

        $link = '<a href="'.DOL_URL_ROOT.'/contab/polizas/contabsociete_card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->nom . $linkend;
		return $result;
	}
	
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
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
		
		$this->nom = '';
		$this->entity = '';
		$this->statut = '';
		$this->tms = '';
		$this->datec = '';
		$this->status = '';
		$this->address = '';
		$this->zip = '';
		$this->town = '';
		$this->fk_departement = '';
		$this->fk_pays = '';
		$this->phone = '';
		$this->fax = '';
		$this->url = '';
		$this->email = '';
		$this->note_private = '';
		$this->note_public = '';
		$this->fk_user_creat = '';
		$this->fk_user_modif = '';

		
	}

}

/**
 * Class ContabsocieteLine
 */
class ContabsocieteLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $nom;
	public $entity;
	public $statut;
	public $tms = '';
	public $datec = '';
	public $status;
	public $address;
	public $zip;
	public $town;
	public $fk_departement;
	public $fk_pays;
	public $phone;
	public $fax;
	public $url;
	public $email;
	public $note_private;
	public $note_public;
	public $fk_user_creat;
	public $fk_user_modif;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
