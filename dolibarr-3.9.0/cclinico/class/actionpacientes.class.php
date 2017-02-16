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
 * \file    cclinico/actionpacientes.class.php
 * \ingroup cclinico
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Actionpacientes
 *
 * Put here description of your class
 * @see CommonObject
 */
class Actionpacientes extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'actionpacientes';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'action_pacientes';

	/**
	 * @var ActionpacientesLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $ref_ext;
	public $entity;
	public $datep = '';
	public $datep2 = '';
	public $fk_action;
	public $code;
	public $datec = '';
	public $tms = '';
	public $fk_user_author;
	public $fk_user_mod;
	public $fk_project;
	public $fk_soc;
	public $fk_pacientes;
	public $fk_parent;
	public $fk_user_action;
	public $fk_user_done;
	public $transparency;
	public $priority;
	public $fulldayevent;
	public $punctual;
	public $percent;
	public $location;
	public $durationp;
	public $label;
	public $note;
	public $email_subject;
	public $email_msgid;
	public $email_from;
	public $email_sender;
	public $email_to;
	public $email_tocc;
	public $email_tobcc;
	public $errors_to;
	public $recurid;
	public $recurrule;
	public $recurdateend = '';
	public $fk_element;
	public $elementtype;

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
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->fk_action)) {
			 $this->fk_action = trim($this->fk_action);
		}
		if (isset($this->code)) {
			 $this->code = trim($this->code);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_project)) {
			 $this->fk_project = trim($this->fk_project);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_pacientes)) {
			 $this->fk_pacientes = trim($this->fk_pacientes);
		}
		if (isset($this->fk_parent)) {
			 $this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_user_action)) {
			 $this->fk_user_action = trim($this->fk_user_action);
		}
		if (isset($this->fk_user_done)) {
			 $this->fk_user_done = trim($this->fk_user_done);
		}
		if (isset($this->transparency)) {
			 $this->transparency = trim($this->transparency);
		}
		if (isset($this->priority)) {
			 $this->priority = trim($this->priority);
		}
		if (isset($this->fulldayevent)) {
			 $this->fulldayevent = trim($this->fulldayevent);
		}
		if (isset($this->punctual)) {
			 $this->punctual = trim($this->punctual);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}
		if (isset($this->location)) {
			 $this->location = trim($this->location);
		}
		if (isset($this->durationp)) {
			 $this->durationp = trim($this->durationp);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}
		if (isset($this->email_subject)) {
			 $this->email_subject = trim($this->email_subject);
		}
		if (isset($this->email_msgid)) {
			 $this->email_msgid = trim($this->email_msgid);
		}
		if (isset($this->email_from)) {
			 $this->email_from = trim($this->email_from);
		}
		if (isset($this->email_sender)) {
			 $this->email_sender = trim($this->email_sender);
		}
		if (isset($this->email_to)) {
			 $this->email_to = trim($this->email_to);
		}
		if (isset($this->email_tocc)) {
			 $this->email_tocc = trim($this->email_tocc);
		}
		if (isset($this->email_tobcc)) {
			 $this->email_tobcc = trim($this->email_tobcc);
		}
		if (isset($this->errors_to)) {
			 $this->errors_to = trim($this->errors_to);
		}
		if (isset($this->recurid)) {
			 $this->recurid = trim($this->recurid);
		}
		if (isset($this->recurrule)) {
			 $this->recurrule = trim($this->recurrule);
		}
		if (isset($this->fk_element)) {
			 $this->fk_element = trim($this->fk_element);
		}
		if (isset($this->elementtype)) {
			 $this->elementtype = trim($this->elementtype);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'ref_ext,';
		$sql.= 'entity,';
		$sql.= 'datep,';
		$sql.= 'datep2,';
		$sql.= 'fk_action,';
		$sql.= 'code,';
		$sql.= 'datec,';
		$sql.= 'fk_user_author,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_project,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_pacientes,';
		$sql.= 'fk_parent,';
		$sql.= 'fk_user_action,';
		$sql.= 'fk_user_done,';
		$sql.= 'transparency,';
		$sql.= 'priority,';
		$sql.= 'fulldayevent,';
		$sql.= 'punctual,';
		$sql.= 'percent,';
		$sql.= 'location,';
		$sql.= 'durationp,';
		$sql.= 'label,';
		$sql.= 'note,';
		$sql.= 'email_subject,';
		$sql.= 'email_msgid,';
		$sql.= 'email_from,';
		$sql.= 'email_sender,';
		$sql.= 'email_to,';
		$sql.= 'email_tocc,';
		$sql.= 'email_tobcc,';
		$sql.= 'errors_to,';
		$sql.= 'recurid,';
		$sql.= 'recurrule,';
		$sql.= 'recurdateend,';
		$sql.= 'fk_element';
		$sql.= 'elementtype';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->ref_ext)?'NULL':"'".$this->db->escape($this->ref_ext)."'").',';
		$sql .= ' '.$conf->entity.',';
		$sql .= ' '.(! isset($this->datep) || dol_strlen($this->datep)==0?'NULL':"'".$this->db->idate($this->datep)."'").',';
		$sql .= ' '.(! isset($this->datep2) || dol_strlen($this->datep2)==0?'NULL':"'".$this->db->idate($this->datep2)."'").',';
		$sql .= ' '.(! isset($this->fk_action)?'NULL':$this->fk_action).',';
		$sql .= ' '.(! isset($this->code)?'NULL':"'".$this->db->escape($this->code)."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_project)?'NULL':$this->fk_project).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_pacientes)?'NULL':$this->fk_pacientes).',';
		$sql .= ' '.(! isset($this->fk_parent)?'NULL':$this->fk_parent).',';
		$sql .= ' '.(! isset($this->fk_user_action)?'NULL':$this->fk_user_action).',';
		$sql .= ' '.(! isset($this->fk_user_done)?'NULL':$this->fk_user_done).',';
		$sql .= ' '.(! isset($this->transparency)?'NULL':$this->transparency).',';
		$sql .= ' '.(! isset($this->priority)?'NULL':$this->priority).',';
		$sql .= ' '.(! isset($this->fulldayevent)?'NULL':$this->fulldayevent).',';
		$sql .= ' '.(! isset($this->punctual)?'NULL':$this->punctual).',';
		$sql .= ' '.(! isset($this->percent)?'NULL':$this->percent).',';
		$sql .= ' '.(! isset($this->location)?'NULL':"'".$this->db->escape($this->location)."'").',';
		$sql .= ' '.(! isset($this->durationp)?'NULL':"'".$this->durationp."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->note)?'NULL':"'".$this->db->escape($this->note)."'").',';
		$sql .= ' '.(! isset($this->email_subject)?'NULL':"'".$this->db->escape($this->email_subject)."'").',';
		$sql .= ' '.(! isset($this->email_msgid)?'NULL':"'".$this->db->escape($this->email_msgid)."'").',';
		$sql .= ' '.(! isset($this->email_from)?'NULL':"'".$this->db->escape($this->email_from)."'").',';
		$sql .= ' '.(! isset($this->email_sender)?'NULL':"'".$this->db->escape($this->email_sender)."'").',';
		$sql .= ' '.(! isset($this->email_to)?'NULL':"'".$this->db->escape($this->email_to)."'").',';
		$sql .= ' '.(! isset($this->email_tocc)?'NULL':"'".$this->db->escape($this->email_tocc)."'").',';
		$sql .= ' '.(! isset($this->email_tobcc)?'NULL':"'".$this->db->escape($this->email_tobcc)."'").',';
		$sql .= ' '.(! isset($this->errors_to)?'NULL':"'".$this->db->escape($this->errors_to)."'").',';
		$sql .= ' '.(! isset($this->recurid)?'NULL':"'".$this->db->escape($this->recurid)."'").',';
		$sql .= ' '.(! isset($this->recurrule)?'NULL':"'".$this->db->escape($this->recurrule)."'").',';
		$sql .= ' '.(! isset($this->recurdateend) || dol_strlen($this->recurdateend)==0?'NULL':"'".$this->db->idate($this->recurdateend)."'").',';
		$sql .= ' '.(! isset($this->fk_element)?'NULL':$this->fk_element).',';
		$sql .= ' '.(! isset($this->elementtype)?'NULL':"'".$this->db->escape($this->elementtype)."'");

		
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
	function add($user,$notrigger=0)
    {
        global $langs,$conf,$hookmanager;

        $error=0;
        $now=dol_now();

        // Check parameters
        if (empty($this->userownerid))
        {
            dol_syslog("You tried to create an event but mandatory property ownerid was not defined", LOG_WARNING);
        	$this->errors[]='ErrorPropertyUserowneridNotDefined';
        	return -1;
        }

        // Clean parameters
        $this->label=dol_trunc(trim($this->label),128);
        $this->location=dol_trunc(trim($this->location),128);
        $this->note=dol_htmlcleanlastbr(trim($this->note));
        if (empty($this->percentage))   $this->percentage = 0;
        if (empty($this->priority) || ! is_numeric($this->priority)) $this->priority = 0;
        if (empty($this->fulldayevent)) $this->fulldayevent = 0;
        if (empty($this->punctual))     $this->punctual = 0;
        if (empty($this->transparency)) $this->transparency = 0;
        if ($this->percentage > 100) $this->percentage = 100;
        //if ($this->percentage == 100 && ! $this->dateend) $this->dateend = $this->date;
        if (! empty($this->datep) && ! empty($this->datef))   $this->durationp=($this->datef - $this->datep);		// deprecated
        //if (! empty($this->date)  && ! empty($this->dateend)) $this->durationa=($this->dateend - $this->date);
        if (! empty($this->datep) && ! empty($this->datef) && $this->datep > $this->datef) $this->datef=$this->datep;
        //if (! empty($this->date)  && ! empty($this->dateend) && $this->date > $this->dateend) $this->dateend=$this->date;
        if (! isset($this->fk_project) || $this->fk_project < 0) $this->fk_project = 0;
        if ($this->elementtype=='facture')  $this->elementtype='invoice';
        if ($this->elementtype=='commande') $this->elementtype='order';
        if ($this->elementtype=='contrat')  $this->elementtype='contract';

        if (! is_array($this->userassigned) && ! empty($this->userassigned))	// For backward compatibility
        {
        	$tmpid=$this->userassigned;
        	$this->userassigned=array();
        	$this->userassigned[$tmpid]=array('id'=>$tmpid);
        }

        if (is_object($this->contact) && isset($this->contact->id) && $this->contact->id > 0 && ! ($this->contactid > 0)) $this->contactid = $this->contact->id;		// For backward compatibility. Using this->contact->xx is deprecated


        $userownerid=$this->userownerid;
        $userdoneid=$this->userdoneid;

        // Be sure assigned user is defined as an array of array('id'=>,'mandatory'=>,...).
        if (empty($this->userassigned) || count($this->userassigned) == 0 || ! is_array($this->userassigned))
        	$this->userassigned = array($userownerid=>array('id'=>$userownerid));

        if (! $this->type_id || ! $this->type_code)
        {
        	$key=empty($this->type_id)?$this->type_code:$this->type_id;

            // Get id from code
            $cactioncomm=new CActionComm($this->db);
            $result=$cactioncomm->fetch($key);

            if ($result > 0)
            {
                $this->type_id=$cactioncomm->id;
                $this->type_code=$cactioncomm->code;
            }
            else if ($result == 0)
            {
                $this->error='Failed to get record with id '.$this->type_id.' code '.$this->type_code.' from dictionary "type of events"';
                return -1;
            }
            else
			{
                $this->error=$cactioncomm->error;
                return -1;
            }
        }

        // Check parameters
        if (! $this->type_id)
        {
            $this->error="ErrorWrongParameters";
            return -1;
        }

        $this->db->begin();

        $sql = "INSERT INTO llx_action_pacientes";
        $sql.= "(datec,";
        $sql.= "datep,";
        $sql.= "datep2,";
        $sql.= "durationp,";	// deprecated
        $sql.= "fk_action,";
        $sql.= "code,";
        $sql.= "fk_soc,";
        $sql.= "fk_project,";
        $sql.= "note,";
        $sql.= "fk_pacientes,";
        $sql.= "fk_user_author,";
        $sql.= "fk_user_action,";
        $sql.= "fk_user_done,";
        $sql.= "label,percent,priority,fulldayevent,location,punctual,";
        $sql.= "transparency,";
        $sql.= "fk_element,";
        $sql.= "elementtype,";
        $sql.= "entity";
        $sql.= ") VALUES (";
        $sql.= "'".$this->db->idate($now)."',";
        $sql.= (strval($this->datep)!=''?"'".$this->db->idate($this->datep)."'":"null").",";
        $sql.= (strval($this->datef)!=''?"'".$this->db->idate($this->datef)."'":"null").",";
        $sql.= ((isset($this->durationp) && $this->durationp >= 0 && $this->durationp != '')?"'".$this->durationp."'":"null").",";	// deprecated
        $sql.= (isset($this->type_id)?$this->type_id:"null").",";
        $sql.= (isset($this->type_code)?" '".$this->type_code."'":"null").",";
        $sql.= ((isset($this->socid) && $this->socid > 0)?" '".$this->socid."'":"null").",";
        $sql.= ((isset($this->fk_project) && $this->fk_project > 0)?" '".$this->fk_project."'":"null").",";
        $sql.= " '".$this->db->escape($this->note)."',";
        $sql.= ((isset($this->contactid) && $this->contactid > 0)?"'".$this->contactid."'":"null").",";
        $sql.= (isset($user->id) && $user->id > 0 ? "'".$user->id."'":"null").",";
        $sql.= ($userownerid>0?"'".$userownerid."'":"null").",";
        $sql.= ($userdoneid>0?"'".$userdoneid."'":"null").",";
        $sql.= "'".$this->db->escape($this->label)."','".$this->percentage."','".$this->priority."','".$this->fulldayevent."','".$this->db->escape($this->location)."','".$this->punctual."',";
        $sql.= "'".$this->transparency."',";
        $sql.= (! empty($this->fk_element)?$this->fk_element:"null").",";
        $sql.= (! empty($this->elementtype)?"'".$this->elementtype."'":"null").",";
        $sql.= $conf->entity;
        $sql.= ")";

        dol_syslog(get_class($this)."::add", LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."actioncomm","id");

            // Now insert assignedusers
			if (! $error)
			{
				foreach($this->userassigned as $key => $val)
				{
			        if (! is_array($val))	// For backward compatibility when val=id
			        {
			        	$val=array('id'=>$val);
			        }

					$sql ="INSERT INTO ".MAIN_DB_PREFIX."actioncomm_resources(fk_actioncomm, element_type, fk_element, mandatory, transparency, answer_status)";
					$sql.=" VALUES(".$this->id.", 'user', ".$val['id'].", ".(empty($val['mandatory'])?'0':$val['mandatory']).", ".(empty($val['transparency'])?'0':$val['transparency']).", ".(empty($val['answer_status'])?'0':$val['answer_status']).")";

					$resql = $this->db->query($sql);
					if (! $resql)
					{
						$error++;
		           		$this->errors[]=$this->db->lasterror();
					}
					//var_dump($sql);exit;
				}
			}

            if (! $error)
            {
            	$action='create';

	            // Actions on extra fields (by external module or standard code)
				// TODO le hook fait double emploi avec le trigger !!
            	$hookmanager->initHooks(array('actioncommdao'));
	            $parameters=array('actcomm'=>$this->id);
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
            }

            if (! $error && ! $notrigger)
            {
                // Call trigger
                $result=$this->call_trigger('ACTION_CREATE',$user);
                if ($result < 0) { $error++; }
                // End call triggers
            }

            if (! $error)
            {
            	$this->db->commit();
            	return $this->id;
            }
            else
           {
	           	$this->db->rollback();
	           	return -1;
            }
        }
        else
        {
            $this->db->rollback();
            $this->error=$this->db->lasterror();
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
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		//$sql .= ' t.rowid,';
		
		$sql .= " t.id,";
		$sql .= " t.ref_ext,";
		$sql .= " t.entity,";
		$sql .= " t.datep,";
		$sql .= " t.datep2,";
		$sql .= " t.fk_action,";
		$sql .= " t.code,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_project,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_pacientes,";
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_user_action,";
		$sql .= " t.fk_user_done,";
		$sql .= " t.transparency,";
		$sql .= " t.priority,";
		$sql .= " t.fulldayevent,";
		$sql .= " t.punctual,";
		$sql .= " t.percent,";
		$sql .= " t.location,";
		$sql .= " t.durationp,";
		$sql .= " t.label,";
		$sql .= " t.note,";
		$sql .= " t.email_subject,";
		$sql .= " t.email_msgid,";
		$sql .= " t.email_from,";
		$sql .= " t.email_sender,";
		$sql .= " t.email_to,";
		$sql .= " t.email_tocc,";
		$sql .= " t.email_tobcc,";
		$sql .= " t.errors_to,";
		$sql .= " t.recurid,";
		$sql .= " t.recurrule,";
		$sql .= " t.recurdateend,";
		$sql .= " t.fk_element,";
		$sql .= " t.elementtype";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.id = ' . $id;
		}
		$sql.= ' AND t.entity='.$conf->entity;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
				$this->ref_ext = $obj->ref_ext;
				$this->entity = $obj->entity;
				$this->datep = $this->db->jdate($obj->datep);
				$this->datep2 = $this->db->jdate($obj->datep2);
				$this->fk_action = $obj->fk_action;
				$this->code = $obj->code;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_user_author = $obj->fk_user_author;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_project = $obj->fk_project;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_pacientes = $obj->fk_pacientes;
				$this->fk_parent = $obj->fk_parent;
				$this->fk_user_action = $obj->fk_user_action;
				$this->fk_user_done = $obj->fk_user_done;
				$this->transparency = $obj->transparency;
				$this->priority = $obj->priority;
				$this->fulldayevent = $obj->fulldayevent;
				$this->punctual = $obj->punctual;
				$this->percent = $obj->percent;
				$this->location = $obj->location;
				$this->durationp = $obj->durationp;
				$this->label = $obj->label;
				$this->note = $obj->note;
				$this->email_subject = $obj->email_subject;
				$this->email_msgid = $obj->email_msgid;
				$this->email_from = $obj->email_from;
				$this->email_sender = $obj->email_sender;
				$this->email_to = $obj->email_to;
				$this->email_tocc = $obj->email_tocc;
				$this->email_tobcc = $obj->email_tobcc;
				$this->errors_to = $obj->errors_to;
				$this->recurid = $obj->recurid;
				$this->recurrule = $obj->recurrule;
				$this->recurdateend = $this->db->jdate($obj->recurdateend);
				$this->fk_element = $obj->fk_element;
				$this->elementtype = $obj->elementtype;

				
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
		
		$sql .= " t.id,";
		$sql .= " t.ref_ext,";
		$sql .= " t.entity,";
		$sql .= " t.datep,";
		$sql .= " t.datep2,";
		$sql .= " t.fk_action,";
		$sql .= " t.code,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_project,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_pacientes,";
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_user_action,";
		$sql .= " t.fk_user_done,";
		$sql .= " t.transparency,";
		$sql .= " t.priority,";
		$sql .= " t.fulldayevent,";
		$sql .= " t.punctual,";
		$sql .= " t.percent,";
		$sql .= " t.location,";
		$sql .= " t.durationp,";
		$sql .= " t.label,";
		$sql .= " t.note,";
		$sql .= " t.email_subject,";
		$sql .= " t.email_msgid,";
		$sql .= " t.email_from,";
		$sql .= " t.email_sender,";
		$sql .= " t.email_to,";
		$sql .= " t.email_tocc,";
		$sql .= " t.email_tobcc,";
		$sql .= " t.errors_to,";
		$sql .= " t.recurid,";
		$sql .= " t.recurrule,";
		$sql .= " t.recurdateend,";
		$sql .= " t.fk_element,";
		$sql .= " t.elementtype";

		
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
				$line = new ActionpacientesLine();

				$line->id = $obj->rowid;
				
				$line->ref_ext = $obj->ref_ext;
				$line->entity = $obj->entity;
				$line->datep = $this->db->jdate($obj->datep);
				$line->datep2 = $this->db->jdate($obj->datep2);
				$line->fk_action = $obj->fk_action;
				$line->code = $obj->code;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_project = $obj->fk_project;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_pacientes = $obj->fk_pacientes;
				$line->fk_parent = $obj->fk_parent;
				$line->fk_user_action = $obj->fk_user_action;
				$line->fk_user_done = $obj->fk_user_done;
				$line->transparency = $obj->transparency;
				$line->priority = $obj->priority;
				$line->fulldayevent = $obj->fulldayevent;
				$line->punctual = $obj->punctual;
				$line->percent = $obj->percent;
				$line->location = $obj->location;
				$line->durationp = $obj->durationp;
				$line->label = $obj->label;
				$line->note = $obj->note;
				$line->email_subject = $obj->email_subject;
				$line->email_msgid = $obj->email_msgid;
				$line->email_from = $obj->email_from;
				$line->email_sender = $obj->email_sender;
				$line->email_to = $obj->email_to;
				$line->email_tocc = $obj->email_tocc;
				$line->email_tobcc = $obj->email_tobcc;
				$line->errors_to = $obj->errors_to;
				$line->recurid = $obj->recurid;
				$line->recurrule = $obj->recurrule;
				$line->recurdateend = $this->db->jdate($obj->recurdateend);
				$line->fk_element = $obj->fk_element;
				$line->elementtype = $obj->elementtype;

				

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
		
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_action)) {
			 $this->fk_action = trim($this->fk_action);
		}
		if (isset($this->code)) {
			 $this->code = trim($this->code);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_project)) {
			 $this->fk_project = trim($this->fk_project);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_pacientes)) {
			 $this->fk_pacientes = trim($this->fk_pacientes);
		}
		if (isset($this->fk_parent)) {
			 $this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_user_action)) {
			 $this->fk_user_action = trim($this->fk_user_action);
		}
		if (isset($this->fk_user_done)) {
			 $this->fk_user_done = trim($this->fk_user_done);
		}
		if (isset($this->transparency)) {
			 $this->transparency = trim($this->transparency);
		}
		if (isset($this->priority)) {
			 $this->priority = trim($this->priority);
		}
		if (isset($this->fulldayevent)) {
			 $this->fulldayevent = trim($this->fulldayevent);
		}
		if (isset($this->punctual)) {
			 $this->punctual = trim($this->punctual);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}
		if (isset($this->location)) {
			 $this->location = trim($this->location);
		}
		if (isset($this->durationp)) {
			 $this->durationp = trim($this->durationp);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}
		if (isset($this->email_subject)) {
			 $this->email_subject = trim($this->email_subject);
		}
		if (isset($this->email_msgid)) {
			 $this->email_msgid = trim($this->email_msgid);
		}
		if (isset($this->email_from)) {
			 $this->email_from = trim($this->email_from);
		}
		if (isset($this->email_sender)) {
			 $this->email_sender = trim($this->email_sender);
		}
		if (isset($this->email_to)) {
			 $this->email_to = trim($this->email_to);
		}
		if (isset($this->email_tocc)) {
			 $this->email_tocc = trim($this->email_tocc);
		}
		if (isset($this->email_tobcc)) {
			 $this->email_tobcc = trim($this->email_tobcc);
		}
		if (isset($this->errors_to)) {
			 $this->errors_to = trim($this->errors_to);
		}
		if (isset($this->recurid)) {
			 $this->recurid = trim($this->recurid);
		}
		if (isset($this->recurrule)) {
			 $this->recurrule = trim($this->recurrule);
		}
		if (isset($this->fk_element)) {
			 $this->fk_element = trim($this->fk_element);
		}
		if (isset($this->elementtype)) {
			 $this->elementtype = trim($this->elementtype);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' ref_ext = '.(isset($this->ref_ext)?"'".$this->db->escape($this->ref_ext)."'":"null").',';
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' datep = '.(! isset($this->datep) || dol_strlen($this->datep) != 0 ? "'".$this->db->idate($this->datep)."'" : 'null').',';
		$sql .= ' datep2 = '.(! isset($this->datep2) || dol_strlen($this->datep2) != 0 ? "'".$this->db->idate($this->datep2)."'" : 'null').',';
		$sql .= ' fk_action = '.(isset($this->fk_action)?$this->fk_action:"null").',';
		$sql .= ' code = '.(isset($this->code)?"'".$this->db->escape($this->code)."'":"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_project = '.(isset($this->fk_project)?$this->fk_project:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_pacientes = '.(isset($this->fk_pacientes)?$this->fk_pacientes:"null").',';
		$sql .= ' fk_parent = '.(isset($this->fk_parent)?$this->fk_parent:"null").',';
		$sql .= ' fk_user_action = '.(isset($this->fk_user_action)?$this->fk_user_action:"null").',';
		$sql .= ' fk_user_done = '.(isset($this->fk_user_done)?$this->fk_user_done:"null").',';
		$sql .= ' transparency = '.(isset($this->transparency)?$this->transparency:"null").',';
		$sql .= ' priority = '.(isset($this->priority)?$this->priority:"null").',';
		$sql .= ' fulldayevent = '.(isset($this->fulldayevent)?$this->fulldayevent:"null").',';
		$sql .= ' punctual = '.(isset($this->punctual)?$this->punctual:"null").',';
		$sql .= ' percent = '.(isset($this->percent)?$this->percent:"null").',';
		$sql .= ' location = '.(isset($this->location)?"'".$this->db->escape($this->location)."'":"null").',';
		$sql .= ' durationp = '.(isset($this->durationp)?$this->durationp:"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' note = '.(isset($this->note)?"'".$this->db->escape($this->note)."'":"null").',';
		$sql .= ' email_subject = '.(isset($this->email_subject)?"'".$this->db->escape($this->email_subject)."'":"null").',';
		$sql .= ' email_msgid = '.(isset($this->email_msgid)?"'".$this->db->escape($this->email_msgid)."'":"null").',';
		$sql .= ' email_from = '.(isset($this->email_from)?"'".$this->db->escape($this->email_from)."'":"null").',';
		$sql .= ' email_sender = '.(isset($this->email_sender)?"'".$this->db->escape($this->email_sender)."'":"null").',';
		$sql .= ' email_to = '.(isset($this->email_to)?"'".$this->db->escape($this->email_to)."'":"null").',';
		$sql .= ' email_tocc = '.(isset($this->email_tocc)?"'".$this->db->escape($this->email_tocc)."'":"null").',';
		$sql .= ' email_tobcc = '.(isset($this->email_tobcc)?"'".$this->db->escape($this->email_tobcc)."'":"null").',';
		$sql .= ' errors_to = '.(isset($this->errors_to)?"'".$this->db->escape($this->errors_to)."'":"null").',';
		$sql .= ' recurid = '.(isset($this->recurid)?"'".$this->db->escape($this->recurid)."'":"null").',';
		$sql .= ' recurrule = '.(isset($this->recurrule)?"'".$this->db->escape($this->recurrule)."'":"null").',';
		$sql .= ' recurdateend = '.(! isset($this->recurdateend) || dol_strlen($this->recurdateend) != 0 ? "'".$this->db->idate($this->recurdateend)."'" : 'null').',';
		$sql .= ' fk_element = '.(isset($this->fk_element)?$this->fk_element:"null").',';
		$sql .= ' elementtype = '.(isset($this->elementtype)?"'".$this->db->escape($this->elementtype)."'":"null");

        
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
		$object = new Actionpacientes($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/cclinico/card.php?id='.$this->id.'"';
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
		
		$this->ref_ext = '';
		$this->entity = '';
		$this->datep = '';
		$this->datep2 = '';
		$this->fk_action = '';
		$this->code = '';
		$this->datec = '';
		$this->tms = '';
		$this->fk_user_author = '';
		$this->fk_user_mod = '';
		$this->fk_project = '';
		$this->fk_soc = '';
		$this->fk_pacientes = '';
		$this->fk_parent = '';
		$this->fk_user_action = '';
		$this->fk_user_done = '';
		$this->transparency = '';
		$this->priority = '';
		$this->fulldayevent = '';
		$this->punctual = '';
		$this->percent = '';
		$this->location = '';
		$this->durationp = '';
		$this->label = '';
		$this->note = '';
		$this->email_subject = '';
		$this->email_msgid = '';
		$this->email_from = '';
		$this->email_sender = '';
		$this->email_to = '';
		$this->email_tocc = '';
		$this->email_tobcc = '';
		$this->errors_to = '';
		$this->recurid = '';
		$this->recurrule = '';
		$this->recurdateend = '';
		$this->fk_element = '';
		$this->elementtype = '';

		
	}

}

/**
 * Class ActionpacientesLine
 */
class ActionpacientesLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $ref_ext;
	public $entity;
	public $datep = '';
	public $datep2 = '';
	public $fk_action;
	public $code;
	public $datec = '';
	public $tms = '';
	public $fk_user_author;
	public $fk_user_mod;
	public $fk_project;
	public $fk_soc;
	public $fk_pacientes;
	public $fk_parent;
	public $fk_user_action;
	public $fk_user_done;
	public $transparency;
	public $priority;
	public $fulldayevent;
	public $punctual;
	public $percent;
	public $location;
	public $durationp;
	public $label;
	public $note;
	public $email_subject;
	public $email_msgid;
	public $email_from;
	public $email_sender;
	public $email_to;
	public $email_tocc;
	public $email_tobcc;
	public $errors_to;
	public $recurid;
	public $recurrule;
	public $recurdateend = '';
	public $fk_element;
	public $elementtype;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
