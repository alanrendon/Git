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
 * \file    ctrlanticipo/ctrladvanceprovider.class.php
 * \ingroup ctrlanticipo
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Ctrladvanceprovider
 *
 * Put here description of your class
 * @see CommonObject
 */


//anticipos
class Ctrladvanceprovider extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'ctrladvanceprovider';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ctrl_advance_provider';

	/**
	 * @var CtrladvanceproviderLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $tms          = '';
	public $ref;
	public $concept_advance;
	public $import;
	public $total_import;
	public $note_public;
	public $note_private;
	public $statut;
	public $date_advance = '';
	public $date_valid   = '';
	public $date_modif   = '';
	public $date_create  = '';
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_user_valid;
	public $fk_soc;
	public $fk_user_applicant;
	public $fk_paymen;
	public $fk_project;
	public $fk_tva;
	public $fk_mcurrency;
	public $type_advance;

	/**
	 */
	

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */

	public function select_advance_list($selected=0)
	{
		$sql='SELECT a.* FROM llx_ctrl_advance_provider as a WHERE a.rowid!='.$this->id.' && a.fk_soc='.$this->fk_soc;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$html='<select id="fk_advance" class="flat"  name="fk_advance">';

				while ($i<$numrows) {
					$obj =$this->db->fetch_object($resql);

					if ($selected==$obj->rowid) {
						$html.='<option value="'.$obj->rowid.'" selected="">';
					}else{
						$html.='<option value="'.$obj->rowid.'" >';
					}
					$html.=$obj->ref;
					$html.='</option>';
					$i++;
				}
				$html.='</select>';
			}
		}
		return $html;
	}
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
		
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->concept_advance)) {
			 $this->concept_advance = trim($this->concept_advance);
		}
		if (isset($this->import)) {
			 $this->import = trim($this->import);
		}
		if (isset($this->total_import)) {
			 $this->total_import = trim($this->total_import);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_user_valid)) {
			 $this->fk_user_valid = trim($this->fk_user_valid);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_user_applicant)) {
			 $this->fk_user_applicant = trim($this->fk_user_applicant);
		}
		if (isset($this->fk_paymen)) {
			 $this->fk_paymen = trim($this->fk_paymen);
		}
		if (isset($this->fk_project)) {
			 $this->fk_project = trim($this->fk_project);
		}
		if (isset($this->fk_tva)) {
			 $this->fk_tva = trim($this->fk_tva);
		}
		if (isset($this->fk_mcurrency)) {
			 $this->fk_mcurrency = trim($this->fk_mcurrency);
		}
		if (isset($this->type_advance)) {
			$this->type_advance = trim($this->type_advance);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO llx_ctrl_advance_provider (';
		
		$sql.= 'ref,';
		$sql.= 'concept_advance,';
		$sql.= 'import,';
		$sql.= 'total_import,';
		$sql.= 'note_public,';
		$sql.= 'note_private,';
		$sql.= 'statut,';
		$sql.= 'date_advance,';
		$sql.= 'date_valid,';
		$sql.= 'date_modif,';
		$sql.= 'date_create,';
		$sql.= 'fk_user_author,';
		$sql.= 'fk_user_modif,';
		$sql.= 'fk_user_valid,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_user_applicant,';
		$sql.= 'fk_paymen,';
		$sql.= 'fk_project,';
		$sql.= 'fk_tva,';
		$sql.= 'type_advance,';
		$sql.= 'fk_mcurrency';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->concept_advance)?'NULL':"'".$this->db->escape($this->concept_advance)."'").',';
		$sql .= ' '.(! isset($this->import)?'NULL':"'".$this->import."'").',';
		$sql .= ' '.(! isset($this->total_import)?'NULL':"'".$this->total_import."'").',';
		$sql .= ' '.(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").',';
		$sql .= ' '.(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").',';
		$sql .= ' '.(! isset($this->statut)?'0':$this->statut).',';
		$sql .= ' '.(! isset($this->date_advance) || dol_strlen($this->date_advance)==0?'NULL':"'".$this->db->idate($this->date_advance)."'").',';
		$sql .= ' '.(! isset($this->date_valid) || dol_strlen($this->date_valid)==0?"'".$this->db->idate(dol_now())."'":'NULL').',';
		$sql .= ' '.(! isset($this->date_modif) || dol_strlen($this->date_modif)==0?"'".$this->db->idate(dol_now())."'":'NULL').',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?"'".$this->db->idate(dol_now())."'":'NULL').',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_user_modif)?'NULL':$this->fk_user_modif).',';
		$sql .= ' '.(! isset($this->fk_user_valid)?'NULL':$this->fk_user_valid).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.( empty($this->fk_user_applicant)?'NULL':$this->fk_user_applicant).',';
		$sql .= ' '.( empty($this->fk_paymen)?'NULL':$this->fk_paymen).',';
		$sql .= ' '.(! empty($this->fk_project)?$this->fk_project:'NULL').',';
		$sql .= ' '.(! isset($this->fk_tva)?'NULL':$this->fk_tva).',';
		$sql .= ' '.(! isset($this->type_advance)?'1':$this->type_advance).',';
		$sql .= ' '.( empty($this->fk_mcurrency)?'NULL':$this->fk_mcurrency);

		
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
			if (empty($this->ref)) {
				$this->ref="(PROV".$this->id.")";
				$this->update($user);
			}
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
		
		$sql .= " t.tms,";
		$sql .= " t.ref,";
		$sql .= " t.concept_advance,";
		$sql .= " t.import,";
		$sql .= " t.total_import,";
		$sql .= " t.note_public,";
		$sql .= " t.note_private,";
		$sql .= " t.statut,";
		$sql .= " t.date_advance,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_modif,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_user_applicant,";
		$sql .= " t.fk_paymen,";
		$sql .= " t.fk_project,";
		$sql .= " t.fk_tva,";
		$sql .= " t.type_advance,";
		$sql .= " t.fk_mcurrency";

		
		$sql .= ' FROM llx_ctrl_advance_provider as t';
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
				$this->tms = $this->db->jdate($obj->tms);
				$this->ref = $obj->ref;
				$this->concept_advance = $obj->concept_advance;
				$this->import = $obj->import;
				$this->total_import = $obj->total_import;
				$this->note_public = $obj->note_public;
				$this->note_private = $obj->note_private;
				$this->statut = $obj->statut;
				$this->date_advance = $this->db->jdate($obj->date_advance);
				$this->date_creation=$this->db->jdate($obj->date_advance);
				$this->date_valid = $this->db->jdate($obj->date_valid);
				$this->date_validation = $this->db->jdate($obj->date_valid);
				$this->date_modif = $this->db->jdate($obj->date_modif);
				$this->date_modification = $this->db->jdate($obj->date_modif);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_author = $obj->fk_user_author;
				$this->user_creation = $obj->fk_user_author;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->user_modification= $obj->fk_user_modif;
				$this->fk_user_valid = $obj->fk_user_valid;
				$this->user_validation = $obj->fk_user_valid;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_user_applicant = $obj->fk_user_applicant;
				$this->fk_paymen = $obj->fk_paymen;
				$this->fk_project = $obj->fk_project;
				$this->fk_tva = $obj->fk_tva;
				$this->type_advance = $obj->type_advance;
				
				$this->fk_mcurrency = $obj->fk_mcurrency;
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
		
		$sql .= " t.tms,";
		$sql .= " t.ref,";
		$sql .= " t.concept_advance,";
		$sql .= " t.import,";
		$sql .= " t.total_import,";
		$sql .= " t.note_public,";
		$sql .= " t.note_private,";
		$sql .= " t.statut,";
		$sql .= " t.date_advance,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_modif,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_user_applicant,";
		$sql .= " t.fk_paymen,";
		$sql .= " t.fk_project,";
		$sql .= " t.fk_tva,";
		$sql .= " t.fk_mcurrency";

		
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
				$line = new CtrladvanceproviderLine();
				
				$line->id = $obj->rowid;
				
				$line->tms = $this->db->jdate($obj->tms);
				$line->ref = $obj->ref;
				$line->concept_advance = $obj->concept_advance;
				$line->import = $obj->import;
				$line->total_import = $obj->total_import;
				$line->note_public = $obj->note_public;
				$line->note_private = $obj->note_private;
				$line->statut = $obj->statut;
				$line->date_advance = $this->db->jdate($obj->date_advance);
				$line->date_valid = $this->db->jdate($obj->date_valid);
				$line->date_modif = $this->db->jdate($obj->date_modif);
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->fk_user_valid = $obj->fk_user_valid;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_user_applicant = $obj->fk_user_applicant;
				$line->fk_paymen = $obj->fk_paymen;
				$line->fk_project = $obj->fk_project;
				$line->fk_tva = $obj->fk_tva;
				$line->fk_mcurrency = $obj->fk_mcurrency;
				
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
		
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->concept_advance)) {
			 $this->concept_advance = trim($this->concept_advance);
		}
		if (isset($this->import)) {
			 $this->import = trim($this->import);
		}
		if (isset($this->total_import)) {
			 $this->total_import = trim($this->total_import);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_user_applicant)) {
			 $this->fk_user_applicant = trim($this->fk_user_applicant);
		}
		if (isset($this->fk_paymen)) {
			 $this->fk_paymen = trim($this->fk_paymen);
		}
		if (isset($this->fk_project)) {
			 $this->fk_project = trim($this->fk_project);
		}
		if (isset($this->fk_tva)) {
			 $this->fk_tva = trim($this->fk_tva);
		}
		if (isset($this->fk_mcurrency)) {
			$this->fk_mcurrency = trim($this->fk_mcurrency);
		}
		if (isset($this->type_advance)) {
			$this->type_advance = trim($this->type_advance);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE llx_ctrl_advance_provider SET';
		
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' concept_advance = '.(isset($this->concept_advance)?"'".$this->db->escape($this->concept_advance)."'":"null").',';
		$sql .= ' import = '.(isset($this->import)?$this->import:"null").',';
		$sql .= ' total_import = '.(isset($this->total_import)?$this->total_import:"null").',';
		$sql .= ' note_public = '.(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").',';
		$sql .= ' note_private = '.(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' date_advance = '.(! isset($this->date_advance) || dol_strlen($this->date_advance) != 0 ? "'".$this->db->idate($this->date_advance)."'" : 'null').',';
		$sql .= ' date_valid = '.(! isset($this->date_valid) || dol_strlen($this->date_valid) != 0 ? "'".$this->db->idate($this->date_valid)."'" : 'null').',';
		$sql .= ' date_modif = '.(! isset($this->date_modif) || dol_strlen($this->date_modif) != 0 ? "'".$this->db->idate(dol_now())."'" : 'null').',';
		
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' fk_user_modif = '.(isset($this->fk_user_modif)?$this->fk_user_modif:"null").',';
		$sql .= ' fk_user_valid = '.(!empty($this->fk_user_valid)?$this->fk_user_valid:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_user_applicant = '.(isset($this->fk_user_applicant)?$this->fk_user_applicant:"null").',';
		$sql .= ' fk_paymen = '.(!empty($this->fk_paymen)?$this->fk_paymen:"null").',';
		$sql .= ' fk_project = '.(!empty($this->fk_project)?$this->fk_project:"null").',';
		$sql .= ' fk_tva = '.(!empty($this->fk_tva)?$this->fk_tva:"null").',';
		$sql .= ' type_advance = '.(!empty($this->type_advance)?$this->type_advance:"1").',';

		$sql .= ' fk_mcurrency = '.(!empty($this->fk_mcurrency)?$this->fk_mcurrency:"null");

        
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
			$sql = 'UPDATE llx_ctrl_advance_provider set statut=6 ';
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
		$object = new Ctrladvanceprovider($this->db);

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

        $label = '<u>' . $langs->trans("ctrlanticipo") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrladvanceprovider_card.php?id='.$this->id.'&action=view"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'service', ($notooltip?'':'class="classfortooltip"')).$linkend);
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
		
		$this->tms               = '';
		$this->ref               = '';
		$this->concept_advance   = '';
		$this->import            = '';
		$this->total_import      = '';
		$this->note_public       = '';
		$this->note_private      = '';
		$this->statut            = '';
		$this->date_advance      = '';
		$this->date_valid        = '';
		$this->date_modif        = '';
		$this->date_create       = '';
		$this->fk_user_author    = '';
		$this->fk_user_modif     = '';
		$this->fk_user_valid     = '';
		$this->fk_soc            = '';
		$this->fk_user_applicant = '';
		$this->fk_paymen         = '';
		$this->fk_project        = '';
		$this->fk_tva            = '';
		$this->fk_mcurrency      = '';

		
	}

	public function select_dol($selected='',$table='', $htmlname='inputref', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
    {
        global $conf,$user,$langs;

        // If no preselected user defined, we take current user
        if ((is_numeric($selected) && ($selected < -2 || empty($selected))) && empty($conf->global->SOCIETE_DISABLE_DEFAULT_SALESREPRESENTATIVE)) $selected=$user->id;
		
		$excludeUsers =null;
		$includeUsers =null;

		// Permettre l'exclusion d'utilisateurs
		if (is_array($exclude))	$excludeUsers = implode("','",$exclude);
		// Permettre l'inclusion d'utilisateurs
		if (is_array($include))	$includeUsers = implode("','",$include);
		else if ($include == 'hierarchy')
		{
			// Build list include Users to have only hierarchy
			$userid  =$user->id;
			$include =array();
			if (empty($user->users) || ! is_array($user->users)) $user->get_full_tree();
			foreach($user->users as $key => $val)
			{
				if (preg_match('/_'.$userid.'/',$val['fullpath'])) $include[]=$val['id'];
			}
			$includeUsers = implode("','",$include);
			//var_dump($includeUsers);exit;
			//var_dump($user->users);exit;
		}

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT u.rowid, u.code, u.description, u.active";
        if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
        {
            $sql.= ", e.label";
        }
        $sql.= " FROM ".MAIN_DB_PREFIX .$table." as u";
       
        /*if (! empty($user->societe_id)) $sql.= " AND u.fk_soc = ".$user->societe_id;
        if (is_array($exclude) && $excludeUsers) $sql.= " AND u.rowid NOT IN ('".$excludeUsers."')";
        if (is_array($include) && $includeUsers) $sql.= " AND u.rowid IN ('".$includeUsers."')";
        if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX) || $noactive) $sql.= " AND u.statut <> 0";
        if (! empty($morefilter)) $sql.=" ".$morefilter;*/
		$sql   .= " ORDER BY u.description ASC";
		dol_syslog(get_class($this)."::select_dolusers", LOG_DEBUG);
		$resql =$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i   = 0;
            if ($num)
            {
           		// Enhance with select2
           		$nodatarole='';
		        if ($conf->use_javascript_ajax)
		        {
		            include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
					$comboenhancement = ajax_combobox($htmlname);
					$out              .=$comboenhancement;
					$nodatarole       =($comboenhancement?' data-role="none"':'');
		        }

				$out                  .= '<select class="flat minwidth200'.($morecss?' '.$morecss:'').'" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
				if ($show_empty) $out .= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
				if ($show_every) $out .= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

                $userstatic=new User($this->db);

                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);

					$userstatic->id          =$obj->rowid;
					$userstatic->code        =$obj->code;
					$userstatic->description =$obj->description;
					$userstatic->active      =$obj->active;
					$disableline             ='';
                    
                    if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=($enableonlytext?$enableonlytext:'1');

                    if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
                    {
						$out                   .= '<option value="'.$obj->rowid.'"';
						if ($disableline) $out .= ' disabled';
						$out                   .= ' selected>';
                    }
                    else
                    {
						$out                   .= '<option value="'.$obj->rowid.'"';
						if ($disableline) $out .= ' disabled';
						$out                   .= '>';
                    }
					
					$out.= $userstatic->description.'</option>';

                    $i++;
                }
            }
            else
            {
				$out .= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" disabled>';
				$out .= '<option value="">'.$langs->trans("None").'</option>';
            }
            $out.= '</select>';
        }
        else
        {
            dol_print_error($this->db);
        }

        return $out;
    }

    function form_project($page, $socid, $selected='', $htmlname='projectid', $discard_closed=0, $maxlength=20, $forcefocus=0,$disabled=0)
    {
        global $langs;

        require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
        require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';

        $formproject=new FormProjets($this->db);

        $langs->load("project");
        if ($htmlname != "none")
        {
            print "\n";
            $formproject->select_projects($socid, $selected, $htmlname, $maxlength, 0, 1, $discard_closed, $forcefocus, $disabled, '','');
        }
        else
        {
            if ($selected)
            {
                $projet = new Project($this->db);
                $projet->fetch($selected);
                //print '<a href="'.DOL_URL_ROOT.'/projet/card.php?id='.$selected.'">'.$projet->title.'</a>';
                print $projet->getNomUrl(0,'',1);
            }
            else
            {
                print "&nbsp;";
            }
        }
    }

}

/**
 * Class CtrladvanceproviderLine
 */
class CtrladvanceproviderLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $tms = '';
	public $ref;
	public $concept_advance;
	public $import;
	public $total_import;
	public $note_public;
	public $note_private;
	public $statut;
	public $date_advance = '';
	public $date_valid = '';
	public $date_modif = '';
	public $date_create = '';
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_user_valid;
	public $fk_soc;
	public $fk_user_applicant;
	public $fk_paymen;
	public $fk_project;
	public $fk_tva;
	public $fk_mcurrency;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
