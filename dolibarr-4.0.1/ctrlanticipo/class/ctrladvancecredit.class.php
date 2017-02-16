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
 * \file    ctrlanticipo/ctrladvancecredit.class.php
 * \ingroup ctrlanticipo
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Ctrladvancecredit
 *
 * Put here description of your class
 * @see CommonObject
 */
class Ctrladvancecredit extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'ctrladvancecredit';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ctrl_advance_credit';

	/**
	 * @var CtrladvancecreditLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_advance;
	public $date_c = '';
	public $import;
	public $fk_tva;
	public $statut;
	public $total_import;
	public $fk_user_agree;
	public $fk_soc;
	

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
		
		if (isset($this->fk_advance)) {
			 $this->fk_advance = trim($this->fk_advance);
		}
		if (isset($this->import)) {
			 $this->import = trim($this->import);
		}
		if (isset($this->fk_tva)) {
			 $this->fk_tva = trim($this->fk_tva);
		}
		if (isset($this->total_import)) {
			 $this->total_import = trim($this->total_import);
		}
		if (isset($this->fk_user_agree)) {
			 $this->fk_user_agree = trim($this->fk_user_agree);
		}

		$sql = 'INSERT INTO llx_ctrl_advance_credit (';
		$sql.= 'fk_advance,';
		$sql.= 'fk_soc,';
		$sql.= 'date_c,';
		$sql.= 'import,';
		$sql.= 'fk_tva,';
		$sql.= 'total_import,';
		$sql.= 'fk_brother_credit,';
		$sql.= 'fk_parent,';
		$sql.= 'statut,';
		$sql.= 'fk_user_agree';
		$sql .= ') VALUES (';
		$sql .= ' '.(! isset($this->fk_advance)?'NULL':$this->fk_advance).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.$this->db->idate(dol_now()).',';
		$sql .= ' '.(! isset($this->import)?'NULL':"'".$this->import."'").',';
		$sql .= ' '.(! isset($this->fk_tva)?'NULL':$this->fk_tva).',';
		$sql .= ' '.(! isset($this->total_import)?'NULL':"'".$this->total_import."'").',';
		$sql .= ' '.(! isset($this->fk_brother_credit)?'NULL':"'".$this->fk_brother_credit."'").',';
		$sql .= ' '.(! isset($this->fk_parent)?'0':$this->fk_parent).',';
		$sql .= ' 1,';
		$sql .= ' '.$user->id;
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
		$sql.= 	" fk_soc,";
		$sql .= " t.fk_advance,";
		$sql .= " t.date_c,";
		$sql .= " t.import,";
		$sql .= " t.fk_tva,";
		$sql .= " t.total_import,";
		$sql .= " t.statut,";
		$sql .= " t.fk_user_agree,";
		$sql .= " t.fk_parent,";
		$sql .= " t.date_asign,";
		$sql .= " t.fk_user_asign,";
		$sql .= " t.date_split";

		

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
				$this->fk_soc = $obj->fk_soc;
				$this->fk_advance = $obj->fk_advance;
				$this->fk_parent = $obj->fk_parent;
				$this->date_asign = $obj->date_asign;
				$this->fk_user_asign = $obj->fk_user_asign;
				
				$this->date_c = $this->db->jdate($obj->date_c);
				$this->date_split = $this->db->jdate($obj->date_split);
				$this->import = $obj->import;
				$this->fk_tva = $obj->fk_tva;
				$this->statut = $obj->statut;
				$this->fk_brother_credit = $obj->fk_brother_credit;
				$this->total_import = $obj->total_import;
				$this->fk_user_agree = $obj->fk_user_agree;
				
				
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
		
		$sql .= " t.fk_advance,";
		$sql .= " t.date_c,";
		$sql .= " t.import,";
		$sql .= " t.fk_tva,";
		$sql .= " t.total_import,";
		$sql .= " t.fk_user_agree";

		
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
				$line = new CtrladvancecreditLine();

				$line->id = $obj->rowid;
				
				$line->fk_advance = $obj->fk_advance;
				$line->date_c = $this->db->jdate($obj->date_c);
				$line->import = $obj->import;
				$line->fk_tva = $obj->fk_tva;
				$line->total_import = $obj->total_import;
				$line->fk_user_agree = $obj->fk_user_agree;

				

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



	public function set_split_credit($notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'UPDATE llx_ctrl_advance_credit SET';
		$sql .= ' date_split = "'.$this->db->idate(dol_now()).'",';
		$sql .= ' fk_brother_credit = '.$this->fk_brother_credit;
        
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

	public function set_reasigne($notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'UPDATE llx_ctrl_advance_credit SET';
		$sql .= ' fk_soc = "'.$this->fk_soc.'",';
		$sql .= ' date_asign = "'.$this->db->idate(dol_now()).'",';
		$sql .= ' fk_user_asign = '.$this->fk_user_asign;
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
		
		if (isset($this->fk_advance)) {
			 $this->fk_advance = trim($this->fk_advance);
		}
		if (isset($this->import)) {
			 $this->import = price2num($this->import);
		}
		if (isset($this->fk_tva)) {
			 $this->fk_tva = trim($this->fk_tva);
		}
		if (isset($this->total_import)) {
			 $this->total_import = price2num($this->total_import);
		}
		if (isset($this->fk_user_agree)) {
			 $this->fk_user_agree = trim($this->fk_user_agree);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' fk_advance = '.(isset($this->fk_advance)?$this->fk_advance:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' date_split = '.(! isset($this->date_split) || dol_strlen($this->date_split) != 0 ? "'".$this->db->idate($this->date_split)."'" : 'null').',';
		$sql .= ' date_c = '.(! isset($this->date_c) || dol_strlen($this->date_c) != 0 ? "'".$this->db->idate($this->date_c)."'" : 'null').',';
		$sql .= ' import = '.(isset($this->import)?$this->import:"null").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' fk_tva = '.(isset($this->fk_tva)?$this->fk_tva:"null").',';
		$sql .= ' total_import = '.(isset($this->total_import)?$this->total_import:"null").',';
		$sql .= ' fk_user_agree = '.$user->id;

        
		$sql .= ' WHERE rowid=' . $this->id;
		$this->db->begin();
		//echo $sql;
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

	//borrar vinculo 
	public function delete_link(User $user,$id_element,$id_fact)
	{
		$error = 0;

		$sql='SELECT a.fk_target FROM '.MAIN_DB_PREFIX.'element_element as a WHERE a.rowid='.$id_element;
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj=$this->db->fetch_object($resql);
			$sql='DELETE FROM '.MAIN_DB_PREFIX.'paiementfourn_facturefourn WHERE rowid='.$obj->fk_target;
			$resql = $this->db->query($sql);
			if ($resql) {
				$sql='DELETE FROM '.MAIN_DB_PREFIX.'element_element WHERE rowid='.$id_element;
				$resql = $this->db->query($sql);
				if ($resql) {
					$this->db->commit();
					return 1;
				}else{
					$this->db->rollback();
					return 1;
				}
			}else{
				$this->db->rollback();
				return -1;
			}
		}else{
			$this->db->rollback();
			return -2;
		}

	}

	public function add_fact($id_fact,$user,$type=0)
	{
		$error = 0;

		$sql='
			INSERT INTO '.MAIN_DB_PREFIX.'paiementfourn_facturefourn (
				fk_facturefourn,
				amount,
				multicurrency_amount
			)
			VALUES
			(
				'.$id_fact.',
				'.$this->total_import.',
				'.$this->total_import.'
			);
		';
		
		
		$this->db->begin();
		$resql = $this->db->query($sql);
		if ($resql) {
			$id = $this->db->last_insert_id(MAIN_DB_PREFIX.'paiementfourn_facturefourn');
			if ($id>0) {
				$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'element_element (';
				$sql.= 'fk_source,';
				$sql.= 'sourcetype,';
				$sql.= 'fk_target,';
				$sql.= 'targettype';
				$sql .= ') VALUES (';
				$sql .= ' '.$this->id.',';
				$sql .= ' "ctrladvancecredit",';
				$sql .= ' '.$id.',';
				$sql .= ' "paiementfourn_facturefourn" ';
				$sql .= ')';
				$resql = $this->db->query($sql);
				if ($resql) {
					$id = $this->db->last_insert_id(MAIN_DB_PREFIX.'element_element');
					$this->db->commit();

					return $id;
				}else{
					$this->db->rollback();
					return -1;
				}
			}

			

		}else{
			$this->db->rollback();
			return -2;
		}
	}

	public function change_value_advance($user)
	{
		$sql="
		SELECT
			a.rowid,a.fk_advance
		FROM
			llx_ctrl_advance_credit AS a
		WHERE
			a.rowid =".$this->id;

		$resql = $this->db->query($sql);
		if ($resql) {

			$num = $this->db->num_rows($resql);
			if ($num>0) {
				$obj=$this->db->fetch_object($resql);

				$sql_prov="SELECT a.rowid FROM llx_ctrl_advance_credit as a WHERE a.statut=1 AND a.fk_advance=".$obj->fk_advance;
				$resql_prov = $this->db->query($sql_prov);
				if ($resql_prov) {

					$num_prov = $this->db->num_rows($resql_prov);
					if ($num_prov==0) {
						
						$sql="UPDATE llx_ctrl_advance_provider SET statut=4 WHERE rowid=".$obj->fk_advance;
						$this->db->query($sql);
						//echo $sql;
					}else{
						$sql="UPDATE llx_ctrl_advance_provider SET statut=3 WHERE rowid=".$obj->fk_advance;
						$this->db->query($sql);
					}
				}
				
			}
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
		$object = new Ctrladvancecredit($this->db);

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
		
		$this->fk_advance = '';
		$this->date_c = '';
		$this->import = '';
		$this->fk_tva = '';
		$this->total_import = '';
		$this->fk_user_agree = '';
	}

}

/**
 * Class CtrladvancecreditLine
 */
class CtrladvancecreditLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_advance;
	public $date_c = '';
	public $import;
	public $fk_tva;
	public $total_import;
	public $fk_user_agree;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
