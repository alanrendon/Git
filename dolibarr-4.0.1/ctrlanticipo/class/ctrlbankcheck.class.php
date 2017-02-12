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
 * \file    ctrlanticipo/ctrlbankcheck.class.php
 * \ingroup ctrlanticipo
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Ctrlbankcheck
 *
 * Put here description of your class
 * @see CommonObject
 */
class Ctrlbankcheck extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'ctrlbankcheck';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ctrl_bank_check';

	/**
	 * @var CtrlbankcheckLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_paiment;
	public $date_asign = '';
	public $receptor;
	public $concept;
	public $account_number;
	public $number_check;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_modify;
	public $date_modify = '';
	public $mode_print;
	public $statut;

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
		
		if (isset($this->fk_paiment)) {
			 $this->fk_paiment = trim($this->fk_paiment);
		}
		if (isset($this->receptor)) {
			 $this->receptor = trim($this->receptor);
		}
		if (isset($this->concept)) {
			 $this->concept = trim($this->concept);
		}
		if (isset($this->account_number)) {
			 $this->account_number = trim($this->account_number);
		}
		if (isset($this->number_check)) {
			 $this->number_check = trim($this->number_check);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_modify)) {
			 $this->fk_user_modify = trim($this->fk_user_modify);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO llx_ctrl_bank_check(';
		$sql.= 'ref,';
		$sql.= 'fk_paiment,';
		$sql.= 'date_asign,';
		$sql.= 'receptor,';
		$sql.= 'concept,';
		$sql.= 'account_number,';
		$sql.= 'number_check,';
		$sql.= 'fk_user_create,';
		$sql.= 'date_create,';
		$sql.= 'mode_print,';
		$sql.= 'statut';
		$sql .= ') VALUES (';
		
		$sql .= ' "'.$this->getNextNumRef().'",';
		$sql .= ' '.(! isset($this->fk_paiment)?'NULL':$this->fk_paiment).',';
		$sql .= ' '.(! isset($this->date_asign) || dol_strlen($this->date_asign)==0?'NULL':"'".$this->db->idate($this->date_asign)."'").',';
		$sql .= ' '.(! isset($this->receptor)?'NULL':"'".$this->db->escape($this->receptor)."'").',';
		$sql .= ' '.(! isset($this->concept)?'NULL':"'".$this->db->escape($this->concept)."'").',';
		$sql .= ' '.(! isset($this->account_number)?'NULL':"'".$this->account_number)."'".',';
		$sql .= ' '.(! isset($this->number_check)?'NULL':"'".$this->number_check)."'".',';
		$sql .= ' '.$user->id.',';
		$sql .= ' "'.$this->db->idate(dol_now()).'",';
		$sql .= ' '.(! isset($this->mode_print)?'NULL':$this->mode_print).',';
		$sql .= ' 0';
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
		$sql .= ' t.ref,';	
		$sql .= " t.fk_paiment,";
		$sql .= " t.date_asign,";
		$sql .= " t.receptor,";
		$sql .= " t.concept,";
		$sql .= " t.account_number,";
		$sql .= " t.number_check,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_modify,";
		$sql .= " t.date_modify,";
		$sql .= " t.mode_print,";
		
		$sql .= " t.statut";

		
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
				$this->ref = $obj->ref;
				$this->fk_paiment = $obj->fk_paiment;
				$this->date_asign = $this->db->jdate($obj->date_asign);
				$this->receptor = $obj->receptor;
				$this->concept = $obj->concept;
				$this->account_number = $obj->account_number;
				$this->number_check = $obj->number_check;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_modify = $obj->fk_user_modify;
				$this->mode_print = $obj->mode_print;
				$this->date_modify = $this->db->jdate($obj->date_modify);
				$this->statut = $obj->statut;
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
		
		$sql .= " t.fk_paiment,";
		$sql .= " t.date_asign,";
		$sql .= " t.receptor,";
		$sql .= " t.concept,";
		$sql .= " t.account_number,";
		$sql .= " t.number_check,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_modify,";
		$sql .= " t.date_modify,";
		$sql .= " t.mode_print,";
		
		$sql .= " t.statut";

		
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
				$line = new CtrlbankcheckLine();

				$line->id = $obj->rowid;
				
				$line->fk_paiment = $obj->fk_paiment;
				$line->date_asign = $this->db->jdate($obj->date_asign);
				$line->receptor = $obj->receptor;
				$line->concept = $obj->concept;
				$line->account_number = $obj->account_number;
				$line->number_check = $obj->number_check;
				$line->fk_user_create = $obj->fk_user_create;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_modify = $obj->fk_user_modify;
				$line->mode_print = $obj->mode_print;
				$line->date_modify = $this->db->jdate($obj->date_modify);
				$line->statut = $obj->statut;

				

				$this->lines[$line->id] = $line;
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
		
		if (isset($this->fk_paiment)) {
			 $this->fk_paiment = trim($this->fk_paiment);
		}
		if (isset($this->receptor)) {
			 $this->receptor = trim($this->receptor);
		}
		if (isset($this->concept)) {
			 $this->concept = trim($this->concept);
		}
		if (isset($this->account_number)) {
			 $this->account_number = trim($this->account_number);
		}
		if (isset($this->number_check)) {
			 $this->number_check = trim($this->number_check);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_modify)) {
			 $this->fk_user_modify = trim($this->fk_user_modify);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' rowid = '.(isset($this->rowid)?$this->rowid:"null").',';
		$sql .= ' fk_paiment = '.(isset($this->fk_paiment)?$this->fk_paiment:"null").',';
		$sql .= ' date_asign = '.(! isset($this->date_asign) || dol_strlen($this->date_asign) != 0 ? "'".$this->db->idate($this->date_asign)."'" : 'null').',';
		$sql .= ' receptor = '.(isset($this->receptor)?"'".$this->db->escape($this->receptor)."'":"null").',';
		$sql .= ' concept = '.(isset($this->concept)?"'".$this->db->escape($this->concept)."'":"null").',';
		$sql .= ' account_number = '.(isset($this->account_number)?$this->account_number:"null").',';
		$sql .= ' number_check = '.(isset($this->number_check)?$this->number_check:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' fk_user_modify = '.(isset($this->fk_user_modify)?$this->fk_user_modify:"null").',';
		$sql .= ' mode_print = '.(isset($this->mode_print)?$this->mode_print:"null").',';
		$sql .= ' date_modify = '.(! isset($this->date_modify) || dol_strlen($this->date_modify) != 0 ? "'".$this->db->idate($this->date_modify)."'" : 'null').',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null");

        
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

	public function getNextNumRef($mode='next')
	{
		global $conf, $db, $langs;
		$langs->load("bills");
		// Clean parameters (if not defined or using deprecated value)


		$mod_ref='mod_codecheck_bronan';
		$mybool=false;

		$file = $mod_ref.".php";
		$classname = $mod_ref;

		// Include file with class
		$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);




		foreach ($dirmodels as $reldir) {

			$dir = dol_buildpath($reldir."ctrlanticipo/core/modules/");

			// Load file with numbering class (if found)
			if (is_file($dir.$file) && is_readable($dir.$file))
			{
				$mybool |= include_once $dir . $file;
			}
		}
		$obj = new $classname();
		$numref = "";
		$numref = $obj->getNextValue($this);

		/**
		 * $numref can be empty in case we ask for the last value because if there is no invoice created with the
		 * set up mask.
		 */
		if ($mode != 'last' && !$numref) {
			//dol_print_error($db,"SupplierPayment::getNextNumRef ".$obj->error);
			return "";
		}

		return $numref;

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
		$object = new Ctrlbankcheck($this->db);

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

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->ref . $linkend;
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
		
		$this->fk_paiment = '';
		$this->date_asign = '';
		$this->receptor = '';
		$this->concept = '';
		$this->account_number = '';
		$this->number_check = '';
		$this->fk_user_create = '';
		$this->date_create = '';
		$this->fk_user_modify = '';
		$this->date_modify = '';
		$this->statut = '';

		
	}

}

/**
 * Class CtrlbankcheckLine
 */
class CtrlbankcheckLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_paiment;
	public $date_asign = '';
	public $receptor;
	public $concept;
	public $account_number;
	public $number_check;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_modify;
	public $date_modify = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
