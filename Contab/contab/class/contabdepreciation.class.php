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
 * \file    contab/contabdepreciation.class.php
 * \ingroup contab
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Contabdepreciation
 *
 * Put here description of your class
 * @see CommonObject
 */
class Contabdepreciation extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'contabdepreciation';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_depreciation';

	/**
	 * @var ContabdepreciationLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $clave;
	public $entity;
	public $descripcion;
	public $date_purchase = '';
	public $amount;
	public $lifetime;
	public $market_value;
	public $type_active;
	public $localitation;
	public $department;
	public $serial_number;
	public $date_init_purchase = '';
	public $depreciation_rate;
	public $depreciation_accumulated;

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
		
		if (isset($this->clave)) {
			 $this->clave = trim($this->clave);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->descripcion)) {
			 $this->descripcion = trim($this->descripcion);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->lifetime)) {
			 $this->lifetime = trim($this->lifetime);
		}
		if (isset($this->market_value)) {
			 $this->market_value = trim($this->market_value);
		}
		if (isset($this->type_active)) {
			 $this->type_active = trim($this->type_active);
		}
		if (isset($this->localitation)) {
			 $this->localitation = trim($this->localitation);
		}
		if (isset($this->department)) {
			 $this->department = trim($this->department);
		}
		if (isset($this->serial_number)) {
			 $this->serial_number = trim($this->serial_number);
		}
		if (isset($this->depreciation_rate)) {
			 $this->depreciation_rate = trim($this->depreciation_rate);
		}
		if (isset($this->depreciation_accumulated)) {
			 $this->depreciation_accumulated = trim($this->depreciation_accumulated);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'clave,';
		$sql.= 'entity,';
		$sql.= 'descripcion,';
		$sql.= 'date_purchase,';
		$sql.= 'amount,';
		$sql.= 'lifetime,';
		$sql.= 'market_value,';
		$sql.= 'type_active,';
		$sql.= 'localitation,';
		$sql.= 'department,';
		$sql.= 'serial_number,';
		$sql.= 'date_init_purchase,';
		$sql.= 'depreciation_rate,';
		$sql.= 'depreciation_accumulated';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->clave)?'NULL':"'".$this->db->escape($this->clave)."'").',';
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->descripcion)?'NULL':"'".$this->db->escape($this->descripcion)."'").',';
		$sql .= ' '.(! isset($this->date_purchase) || dol_strlen($this->date_purchase)==0?'NULL':"'".$this->db->idate($this->date_purchase)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.( empty($this->lifetime)?'NULL':$this->lifetime).',';
		$sql .= ' '.(! isset($this->market_value)?'NULL':"'".$this->market_value."'").',';
		$sql .= ' '.(! isset($this->type_active)?'NULL':"'".$this->db->escape($this->type_active)."'").',';
		$sql .= ' '.(! isset($this->localitation)?'NULL':"'".$this->db->escape($this->localitation)."'").',';
		$sql .= ' '.(! isset($this->department)?'NULL':"'".$this->db->escape($this->department)."'").',';
		$sql .= ' '.(! isset($this->serial_number)?'NULL':$this->serial_number).',';
		$sql .= ' '.(! isset($this->date_init_purchase) || dol_strlen($this->date_init_purchase)==0?'NULL':"'".$this->db->idate($this->date_init_purchase)."'").',';
		$sql .= ' '.(! isset($this->depreciation_rate)?'NULL':"'".$this->depreciation_rate."'").',';
		$sql .= ' '.(! isset($this->depreciation_accumulated)?'NULL':"'".$this->depreciation_accumulated."'");


		$sql .= ')';
		echo $sql;
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



	function select_dol_active($selected='', $htmlname='type_active', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss="style='width:200px !important;'", $noactive=0)
    {
        global $conf,$user,$langs;

        // If no preselected user defined, we take current user
        if ((is_numeric($selected) && ($selected < -2 || empty($selected))) && empty($conf->global->SOCIETE_DISABLE_DEFAULT_SALESREPRESENTATIVE)) $selected=$user->id;

        $excludeUsers=null;
        $includeUsers=null;

        // Permettre l'exclusion d'utilisateurs
        if (is_array($exclude))	$excludeUsers = implode("','",$exclude);
        // Permettre l'inclusion d'utilisateurs
        if (is_array($include))	$includeUsers = implode("','",$include);
		else if ($include == 'hierarchy')
		{
			// Build list includeUsers to have only hierarchy
			$userid=$user->id;
			$include=array();
			if (empty($user->users) || ! is_array($user->users)) $user->get_full_tree();
			foreach($user->users as $key => $val)
			{
				if (preg_match('/_'.$userid.'/',$val['fullpath'])) $include[]=$val['id'];
			}
			$includeUsers = implode("','",$include);
		}

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT u.rowid, u.cta as codagr, u.descta as label";
        $sql.= " FROM llx_contab_cat_ctas as u";

        if (! empty($morefilter)) $sql.=" ".$morefilter;
        $sql.= " ORDER BY u.cta ASC ";

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

                $out.= '<select class="flat minwidth200"'.($morecss?' '.$morecss:'').' id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled':'').$nodatarole.'>';
                if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected':'').'>&nbsp;</option>'."\n";
				if ($show_every) $out.= '<option value="-2"'.(($selected==-2)?' selected':'').'>-- '.$langs->trans("Everybody").' --</option>'."\n";

                $userstatic=new User($this->db);

                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);

                    $userstatic->id=$obj->rowid;
                    $userstatic->lastname=$obj->lastname;
                    $userstatic->firstname=$obj->firstname;

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
                    $out.=$obj->label." (".$obj->codagr.") ";
                    $moreinfo=0;
					$out.=($moreinfo?')':'');
					if ($disableline && $disableline != '1')
					{
						$out.=' - '.$disableline;	// This is text from $enableonlytext parameter
					}

                    $out.= '</option>';
                  
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
		
		$sql .= " t.clave,";
		$sql .= " t.entity,";
		$sql .= " t.descripcion,";
		$sql .= " t.date_purchase,";
		$sql .= " t.amount,";
		$sql .= " t.lifetime,";
		$sql .= " t.market_value,";
		$sql .= " t.type_active,";
		$sql .= " t.localitation,";
		$sql .= " t.department,";
		$sql .= " t.serial_number,";
		$sql .= " t.date_init_purchase,";
		$sql .= " t.depreciation_rate,";
		$sql .= " t.depreciation_accumulated";

		
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
				
				$this->clave = $obj->clave;
				$this->entity = $obj->entity;
				$this->descripcion = $obj->descripcion;
				$this->date_purchase = $this->db->jdate($obj->date_purchase);
				$this->amount = $obj->amount;
				$this->lifetime = $obj->lifetime;
				$this->market_value = $obj->market_value;
				$this->type_active = $obj->type_active;
				$this->localitation = $obj->localitation;
				$this->department = $obj->department;
				$this->serial_number = $obj->serial_number;
				$this->date_init_purchase = $this->db->jdate($obj->date_init_purchase);
				$this->depreciation_rate = $obj->depreciation_rate;
				$this->depreciation_accumulated = $obj->depreciation_accumulated;

				
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
		
		$sql .= " t.clave,";
		$sql .= " t.entity,";
		$sql .= " t.descripcion,";
		$sql .= " t.date_purchase,";
		$sql .= " t.amount,";
		$sql .= " t.lifetime,";
		$sql .= " t.market_value,";
		$sql .= " t.type_active,";
		$sql .= " t.localitation,";
		$sql .= " t.department,";
		$sql .= " t.serial_number,";
		$sql .= " t.date_init_purchase,";
		$sql .= " t.depreciation_rate,";
		$sql .= " t.depreciation_accumulated";

		
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
				$line = new ContabdepreciationLine();

				$line->id = $obj->rowid;
				
				$line->clave = $obj->clave;
				$line->entity = $obj->entity;
				$line->descripcion = $obj->descripcion;
				$line->date_purchase = $this->db->jdate($obj->date_purchase);
				$line->amount = $obj->amount;
				$line->lifetime = $obj->lifetime;
				$line->market_value = $obj->market_value;
				$line->type_active = $obj->type_active;
				$line->localitation = $obj->localitation;
				$line->department = $obj->department;
				$line->serial_number = $obj->serial_number;
				$line->date_init_purchase = $this->db->jdate($obj->date_init_purchase);
				$line->depreciation_rate = $obj->depreciation_rate;
				$line->depreciation_accumulated = $obj->depreciation_accumulated;

				

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
		
		if (isset($this->clave)) {
			 $this->clave = trim($this->clave);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->descripcion)) {
			 $this->descripcion = trim($this->descripcion);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->lifetime)) {
			 $this->lifetime = trim($this->lifetime);
		}
		if (isset($this->market_value)) {
			 $this->market_value = trim($this->market_value);
		}
		if (isset($this->type_active)) {
			 $this->type_active = trim($this->type_active);
		}
		if (isset($this->localitation)) {
			 $this->localitation = trim($this->localitation);
		}
		if (isset($this->department)) {
			 $this->department = trim($this->department);
		}
		if (isset($this->serial_number)) {
			 $this->serial_number = trim($this->serial_number);
		}
		if (isset($this->depreciation_rate)) {
			 $this->depreciation_rate = trim($this->depreciation_rate);
		}
		if (isset($this->depreciation_accumulated)) {
			 $this->depreciation_accumulated = trim($this->depreciation_accumulated);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' clave = '.(isset($this->clave)?"'".$this->db->escape($this->clave)."'":"null").',';

		$sql .= ' descripcion = '.(isset($this->descripcion)?"'".$this->db->escape($this->descripcion)."'":"null").',';
		$sql .= ' date_purchase = '.(! isset($this->date_purchase) || dol_strlen($this->date_purchase) != 0 ? "'".$this->db->idate($this->date_purchase)."'" : 'null').',';
		$sql .= ' amount = '.(!empty($this->amount)?$this->amount:"null").',';
		$sql .= ' lifetime = '.(!empty($this->lifetime)?$this->lifetime:"null").',';
		$sql .= ' market_value = '.(!empty($this->market_value)?$this->market_value:"null").',';
		$sql .= ' type_active = '.(isset($this->type_active)?"'".$this->db->escape($this->type_active)."'":"null").',';
		$sql .= ' localitation = '.(!empty($this->localitation)?"'".$this->db->escape($this->localitation)."'":"null").',';
		$sql .= ' department = '.(!empty($this->department)?"'".$this->db->escape($this->department)."'":"null").',';
		$sql .= ' serial_number = '.(!empty($this->serial_number)?$this->serial_number:"null").',';
		$sql .= ' date_init_purchase = '.(! isset($this->date_init_purchase) || dol_strlen($this->date_init_purchase) != 0 ? "'".$this->db->idate($this->date_init_purchase)."'" : 'null').',';
		$sql .= ' depreciation_rate = '.(!empty($this->depreciation_rate)?$this->depreciation_rate:"null").',';
		$sql .= ' depreciation_accumulated = '.(!empty($this->depreciation_accumulated)?$this->depreciation_accumulated:"null");

        
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
		$object = new Contabdepreciation($this->db);

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
        $label.= '<b>' . $langs->trans('Clave') . ':</b> ' . $this->clave;

        $link = '<a href="'.DOL_URL_ROOT.'/contab/modules/depreciation/contabdepreciation_card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->clave . $linkend;
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
		
		$this->clave = '';
		$this->entity = '';
		$this->descripcion = '';
		$this->date_purchase = '';
		$this->amount = '';
		$this->lifetime = '';
		$this->market_value = '';
		$this->type_active = '';
		$this->localitation = '';
		$this->department = '';
		$this->serial_number = '';
		$this->date_init_purchase = '';
		$this->depreciation_rate = '';
		$this->depreciation_accumulated = '';

		
	}

}

/**
 * Class ContabdepreciationLine
 */
class ContabdepreciationLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $clave;
	public $entity;
	public $descripcion;
	public $date_purchase = '';
	public $amount;
	public $lifetime;
	public $market_value;
	public $type_active;
	public $localitation;
	public $department;
	public $serial_number;
	public $date_init_purchase = '';
	public $depreciation_rate;
	public $depreciation_accumulated;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
