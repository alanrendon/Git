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
 * \file    contab/contabinpc.class.php
 * \ingroup contab
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Contabinpc
 *
 * Put here description of your class
 * @see CommonObject
 */
class Contabinpc extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'contabinpc';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_inpc';

	/**
	 * @var ContabinpcLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $year;
	public $enero;
	public $febrero;
	public $marzo;
	public $abril;
	public $mayo;
	public $junio;
	public $julio;
	public $agosto;
	public $septiembre;
	public $octubre;
	public $noviembre;
	public $diciembre;

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
		
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->year)) {
			 $this->year = trim($this->year);
		}
		if (isset($this->enero)) {
			 $this->enero = trim($this->enero);
		}
		if (isset($this->febrero)) {
			 $this->febrero = trim($this->febrero);
		}
		if (isset($this->marzo)) {
			 $this->marzo = trim($this->marzo);
		}
		if (isset($this->abril)) {
			 $this->abril = trim($this->abril);
		}
		if (isset($this->mayo)) {
			 $this->mayo = trim($this->mayo);
		}
		if (isset($this->junio)) {
			 $this->junio = trim($this->junio);
		}
		if (isset($this->julio)) {
			 $this->julio = trim($this->julio);
		}
		if (isset($this->agosto)) {
			 $this->agosto = trim($this->agosto);
		}
		if (isset($this->septiembre)) {
			 $this->septiembre = trim($this->septiembre);
		}
		if (isset($this->octubre)) {
			 $this->octubre = trim($this->octubre);
		}
		if (isset($this->noviembre)) {
			 $this->noviembre = trim($this->noviembre);
		}
		if (isset($this->diciembre)) {
			 $this->diciembre = trim($this->diciembre);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'year,';
		$sql.= 'enero,';
		$sql.= 'febrero,';
		$sql.= 'marzo,';
		$sql.= 'abril,';
		$sql.= 'mayo,';
		$sql.= 'junio,';
		$sql.= 'julio,';
		$sql.= 'agosto,';
		$sql.= 'septiembre,';
		$sql.= 'octubre,';
		$sql.= 'noviembre,';
		$sql.= 'diciembre';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(empty($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(empty($this->year)?'NULL':$this->year).',';
		$sql .= ' '.(empty($this->enero)?'NULL':"'".$this->enero."'").',';
		$sql .= ' '.(empty($this->febrero)?'NULL':"'".$this->febrero."'").',';
		$sql .= ' '.(empty($this->marzo)?'NULL':"'".$this->marzo."'").',';
		$sql .= ' '.(empty($this->abril)?'NULL':"'".$this->abril."'").',';
		$sql .= ' '.(empty($this->mayo)?'NULL':"'".$this->mayo."'").',';
		$sql .= ' '.(empty($this->junio)?'NULL':"'".$this->junio."'").',';
		$sql .= ' '.(empty($this->julio)?'NULL':"'".$this->julio."'").',';
		$sql .= ' '.(empty($this->agosto)?'NULL':"'".$this->agosto."'").',';
		$sql .= ' '.(empty($this->septiembre)?'NULL':"'".$this->septiembre."'").',';
		$sql .= ' '.(empty($this->octubre)?'NULL':"'".$this->octubre."'").',';
		$sql .= ' '.(empty($this->noviembre)?'NULL':"'".$this->noviembre."'").',';
		$sql .= ' '.(empty($this->diciembre)?'NULL':"'".$this->diciembre."'");

		
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


	public function valid_year_inpc()
	{
		$sql = 'SELECT COUNT(*) as num FROM llx_contab_inpc as a WHERE a.year='.$this->year;
		if ($this->rowid>0) {
			$sql.=" AND a.rowid!=".$this->rowid;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj->num>0) {
				return 1;
			}else{
				return -2;
			}
		}else{
			return -1;
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
		
		$sql .= " t.entity,";
		$sql .= " t.year,";
		$sql .= " t.enero,";
		$sql .= " t.febrero,";
		$sql .= " t.marzo,";
		$sql .= " t.abril,";
		$sql .= " t.mayo,";
		$sql .= " t.junio,";
		$sql .= " t.julio,";
		$sql .= " t.agosto,";
		$sql .= " t.septiembre,";
		$sql .= " t.octubre,";
		$sql .= " t.noviembre,";
		$sql .= " t.diciembre";

		
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
				$this->entity = $obj->entity;
				$this->year = $obj->year;
				$this->enero = $obj->enero;
				$this->febrero = $obj->febrero;
				$this->marzo = $obj->marzo;
				$this->abril = $obj->abril;
				$this->mayo = $obj->mayo;
				$this->junio = $obj->junio;
				$this->julio = $obj->julio;
				$this->agosto = $obj->agosto;
				$this->septiembre = $obj->septiembre;
				$this->octubre = $obj->octubre;
				$this->noviembre = $obj->noviembre;
				$this->diciembre = $obj->diciembre;

				
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
		
		$sql .= " t.entity,";
		$sql .= " t.year,";
		$sql .= " t.enero,";
		$sql .= " t.febrero,";
		$sql .= " t.marzo,";
		$sql .= " t.abril,";
		$sql .= " t.mayo,";
		$sql .= " t.junio,";
		$sql .= " t.julio,";
		$sql .= " t.agosto,";
		$sql .= " t.septiembre,";
		$sql .= " t.octubre,";
		$sql .= " t.noviembre,";
		$sql .= " t.diciembre";

		
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
				$line = new ContabinpcLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->year = $obj->year;
				$line->enero = $obj->enero;
				$line->febrero = $obj->febrero;
				$line->marzo = $obj->marzo;
				$line->abril = $obj->abril;
				$line->mayo = $obj->mayo;
				$line->junio = $obj->junio;
				$line->julio = $obj->julio;
				$line->agosto = $obj->agosto;
				$line->septiembre = $obj->septiembre;
				$line->octubre = $obj->octubre;
				$line->noviembre = $obj->noviembre;
				$line->diciembre = $obj->diciembre;

				

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
		
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->year)) {
			 $this->year = trim($this->year);
		}
		if (isset($this->enero)) {
			 $this->enero = trim($this->enero);
		}
		if (isset($this->febrero)) {
			 $this->febrero = trim($this->febrero);
		}
		if (isset($this->marzo)) {
			 $this->marzo = trim($this->marzo);
		}
		if (isset($this->abril)) {
			 $this->abril = trim($this->abril);
		}
		if (isset($this->mayo)) {
			 $this->mayo = trim($this->mayo);
		}
		if (isset($this->junio)) {
			 $this->junio = trim($this->junio);
		}
		if (isset($this->julio)) {
			 $this->julio = trim($this->julio);
		}
		if (isset($this->agosto)) {
			 $this->agosto = trim($this->agosto);
		}
		if (isset($this->septiembre)) {
			 $this->septiembre = trim($this->septiembre);
		}
		if (isset($this->octubre)) {
			 $this->octubre = trim($this->octubre);
		}
		if (isset($this->noviembre)) {
			 $this->noviembre = trim($this->noviembre);
		}
		if (isset($this->diciembre)) {
			 $this->diciembre = trim($this->diciembre);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' year = '.(!empty($this->year)?$this->year:"null").',';
		$sql .= ' enero = '.(!empty($this->enero)?$this->enero:"null").',';
		$sql .= ' febrero = '.(!empty($this->febrero)?$this->febrero:"null").',';
		$sql .= ' marzo = '.(!empty($this->marzo)?$this->marzo:"null").',';
		$sql .= ' abril = '.(!empty($this->abril)?$this->abril:"null").',';
		$sql .= ' mayo = '.(!empty($this->mayo)?$this->mayo:"null").',';
		$sql .= ' junio = '.(!empty($this->junio)?$this->junio:"null").',';
		$sql .= ' julio = '.(!empty($this->julio)?$this->julio:"null").',';
		$sql .= ' agosto = '.(!empty($this->agosto)?$this->agosto:"null").',';
		$sql .= ' septiembre = '.(!empty($this->septiembre)?$this->septiembre:"null").',';
		$sql .= ' octubre = '.(!empty($this->octubre)?$this->octubre:"null").',';
		$sql .= ' noviembre = '.(!empty($this->noviembre)?$this->noviembre:"null").',';
		$sql .= ' diciembre = '.(!empty($this->diciembre)?$this->diciembre:"null");

        
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
		$object = new Contabinpc($this->db);

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
        $label.= '<b>' . $langs->trans('Año') . ':</b> ' . $this->year;

        $link = '<a href="'.DOL_URL_ROOT.'/contab/modules/inpc/contabinpc_card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->year . $linkend;
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
		
		$this->entity = '';
		$this->year = '';
		$this->enero = '';
		$this->febrero = '';
		$this->marzo = '';
		$this->abril = '';
		$this->mayo = '';
		$this->junio = '';
		$this->julio = '';
		$this->agosto = '';
		$this->septiembre = '';
		$this->octubre = '';
		$this->noviembre = '';
		$this->diciembre = '';

		
	}

}

/**
 * Class ContabinpcLine
 */
class ContabinpcLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $entity;
	public $year;
	public $enero;
	public $febrero;
	public $marzo;
	public $abril;
	public $mayo;
	public $junio;
	public $julio;
	public $agosto;
	public $septiembre;
	public $octubre;
	public $noviembre;
	public $diciembre;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
