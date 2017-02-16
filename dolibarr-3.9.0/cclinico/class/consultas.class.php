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
 * \file    cclinico/consultas.class.php
 * \ingroup cclinico
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Consultas
 *
 * Put here description of your class
 * @see CommonObject
 */
class Consultas extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'consultas';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'consultas';

	/**
	 * @var ConsultasLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $Ref;
	public $rowid;
	public $date_consultation = '';
	public $date_creation = '';
	public $fk_user_creation;
	public $date_validation = '';
	public $fk_user_validation;
	public $date_clos = '';
	public $fk_user_close;
	public $Type_consultation;
	public $weight;
	public $blood_pressure;
	public $fk_user_med;
	public $reason;
	public $reason_detail;
	public $diagnostics;
	public $diagnostics_detail;
	public $treatments;
	public $comments;
	public $statut;
	public $fk_user_pacientes;
	public $fk_evento;
	public $temperature;

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
	function select_dolusers($selected='', $htmlname='userid', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
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
			//var_dump($includeUsers);exit;
			//var_dump($user->users);exit;
		}

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
        /*if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
        {
            $sql.= ", e.label";
        }*/
        $sql.= " FROM ".MAIN_DB_PREFIX ."user as u";
        $sql.=" LEFT JOIN llx_user_extrafields AS b ON u.rowid = b.fk_object ";
        /*
        if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
        {
            $sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";
            if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
            else $sql.= " WHERE u.entity IS NOT NULL";
        }
        else
       {
        	if (! empty($conf->multicompany->transverse_mode))
        	{
        		$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
        		$sql.= " WHERE ug.fk_user = u.rowid";
        		$sql.= " AND ug.entity = ".$conf->entity;
        	}
        	else
        	{
        		$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
        	}
        }*/
        $sql.="
        WHERE
         u.statut=1 AND u.entity=".$conf->entity." AND b.med001=1 ";
        if (! empty($user->societe_id)) $sql.= " AND u.fk_soc = ".$user->societe_id;
        if (is_array($exclude) && $excludeUsers) $sql.= " AND u.rowid NOT IN ('".$excludeUsers."')";
        if (is_array($include) && $includeUsers) $sql.= " AND u.rowid IN ('".$includeUsers."')";
        if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX) || $noactive) $sql.= " AND u.statut <> 0";
        if (! empty($morefilter)) $sql.=" ".$morefilter;
        $sql.= " ORDER BY u.lastname ASC";
        //echo $sql;
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

                    $out.= $userstatic->getFullName($langs, 0, -1, $maxlength);
                    // Complete name with more info
                    $moreinfo=0;
                    if (! empty($conf->global->MAIN_SHOW_LOGIN))
                    {
                    	$out.= ($moreinfo?' - ':' (').$obj->login;
                    	$moreinfo++;
                    }
                    if ($showstatus >= 0)
                    {
                    	if ($obj->statut == 1 && $showstatus == 1)
                    	{
                    		$out.=($moreinfo?' - ':' (').$langs->trans('Enabled');
                    		$moreinfo++;
                    	}
						if ($obj->statut == 0)
						{
							$out.=($moreinfo?' - ':' (').$langs->trans('Disabled');
							$moreinfo++;
						}
					}
                    if (! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode) && $conf->entity == 1 && $user->admin && ! $user->entity)
                    {
                        if ($obj->admin && ! $obj->entity)
                        {
                        	$out.=($moreinfo?' - ':' (').$langs->trans("AllEntities");
                        	$moreinfo++;
                        }
                        else
                     {
                        	$out.=($moreinfo?' - ':' (').($obj->label?$obj->label:$langs->trans("EntityNameNotDefined"));
                        	$moreinfo++;
                     	}
                    }
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

    public function listar_antecedentes($id)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);
		$sql = 'SELECT
			a.*,b.lastname,b.firstname
		FROM
			llx_antecedentes AS a
		INNER JOIN llx_pacientes AS b ON a.fk_pacientes=b.rowid
		WHERE
		a.entity='.$conf->entity.' AND
		b.entity='.$conf->entity.' AND
			a.status=1 AND a.fk_pacientes= '.trim($id).'
		ORDER BY a.fecha_creacion desc';
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($obj)
					{
					     $array[$i]=$obj;
					}
					$i++;
				}
			}
			return $array;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}

	public function listar_facturas()
	{
		global $conf;
		$sql = '
		SELECT
			a.rowid AS id,
			b.total_ttc as total_ht,
			b.*
		FROM
			llx_facturas_consulta AS a
			INNER JOIN llx_facture AS b ON a.fk_factura = b.rowid
		WHERE
			a.entity='.$conf->entity.' AND
			b.entity='.$conf->entity.' AND
			a.statut = 1
		AND a.fk_consulta = '.trim($this->rowid).' 
		ORDER BY date_creation DESC';

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($obj)
					{
					    $array[$i]=$obj;
					}
					$i++;
				}
			}
			return $array;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}
	public function listar_evento($evento)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);
		$sql = '
		SELECT
			a.fk_consulta,a.fk_paciente,a.rowid
		FROM
			llx_eventos_consultas AS a
		WHERE 
		b.entity='.$conf->entity.' AND
		a.fk_evento='.trim($evento);
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				return $obj;
			}else{
				return array();
			}
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}


	public function create(User $user, $notrigger = false)
	{

		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		if (isset($this->weight)) {
			 $this->weight = trim($this->weight);
		}
		if (isset($this->Type_consultation)) {
			 $this->Type_consultation = trim($this->Type_consultation);
		}
		if (isset($this->blood_pressure)) {
			 $this->blood_pressure = trim($this->blood_pressure);
		}
		if (isset($this->reason)) {
			 $this->reason = trim($this->reason);
		}
		if (isset($this->reason_detail)) {
			 $this->reason_detail = trim($this->reason_detail);
		}
		if (isset($this->treatments)) {
			 $this->treatments = trim($this->treatments);
		}
		if (isset($this->diagnostics)) {
			 $this->diagnostics = trim($this->diagnostics);
		}
		if (isset($this->diagnostics_detail)) {
			 $this->diagnostics_detail = trim($this->diagnostics_detail);
		}
		if (isset($this->comments)) {
			 $this->comments = trim($this->comments);
		}
		if (isset($this->fk_user_med)) {
			 $this->fk_user_med = trim($this->fk_user_med);
		}
		if (empty($this->date_consultation)) {
			 $this->date_consultation = $this->db->idate(dol_now());
		}
		
		if (isset($this->fk_user_pacientes)) {
			 $this->fk_user_pacientes = trim($this->fk_user_pacientes);
		}
		if (isset($this->Ref)) {
			 $this->Ref = ((!empty($this->Ref))?trim($this->Ref):"NA");
		}
		

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		$sql.= 'entity,';
		$sql.= 'weight,';
		$sql.= 'Ref,';
		$sql.= 'fk_user_pacientes,';
		$sql.= 'Type_consultation,';
		$sql.= 'blood_pressure,';
		$sql.= 'reason,';
		$sql.= 'reason_detail,';
		$sql.= 'treatments,';
		$sql.= 'diagnostics,';
		$sql.= 'diagnostics_detail,';
		$sql.= 'comments,';
		$sql.= 'fk_user_med,';
		$sql.= 'date_consultation,';
		$sql.= 'statut,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_creation';
		$sql .= ') VALUES (';
		$sql .= ' '.$conf->entity.',';
		$sql .= ' '.(empty($this->weight)?'NULL':"'".$this->weight."'").',';
		$sql .= ' '.(empty($this->Ref)?'NULL':"'".$this->db->escape($this->Ref)."'").',';
		$sql .= $this->fk_user_pacientes.",";
		$sql .= ' '.(empty($this->Type_consultation)?'NULL':$this->Type_consultation).',';
		$sql .= ' '.(empty($this->blood_pressure)?'NULL':"'".$this->db->escape($this->blood_pressure)."'").',';
		$sql .= ' '.(empty($this->reason)?'NULL':$this->reason).',';
		$sql .= ' '.(empty($this->reason_detail)?'NULL':"'".$this->db->escape($this->reason_detail)."'").',';
		$sql .= ' '.(empty($this->treatments)?'NULL':"'".$this->db->escape($this->treatments)."'").',';
		$sql .= ' '.(empty($this->diagnostics)?'NULL':$this->diagnostics).',';
		$sql .= ' '.(empty($this->diagnostics_detail)?'NULL':"'".$this->db->escape($this->diagnostics_detail)."'").',';
		$sql .= ' '.(empty($this->comments)?'NULL':"'".$this->db->escape($this->comments)."'").',';
		$sql .= ' '.(empty($this->fk_user_med)?'NULL':$this->db->escape($this->fk_user_med)).',';
		$sql .= ' '.$this->db->idate($this->date_consultation).',0,';
		$sql .= ' '.$this->db->idate(dol_now()).',';
		$sql .= ' '.$user->id;
		$sql .= ')';

		$resql = $this->db->query($sql);

		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}


		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);
			if ($resql) {
	    		$result=$this->insertExtraFields();
	    		if ($result < 0)
	    		{
	    			$error++;
	    		}
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

	public function obtener_tercero(){
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$sql = 'SELECT a.fk_soc FROM llx_pacientes AS a
		INNER JOIN llx_consultas AS b ON a.rowid = b.fk_user_pacientes
		WHERE
			b.entity ='.$conf->entity.' AND 
			a.entity ='.$conf->entity.' AND 
			b.rowid='.$this->rowid;
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num == 1)
			{
				$obj = $this->db->fetch_object($resql);
				return $obj->fk_soc;
			}
			return -1;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}

	public function actualizar_tercero($tercero){
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$sql = 'UPDATE llx_pacientes AS a
		INNER JOIN llx_consultas AS b ON a.rowid = b.fk_user_pacientes
		SET a.fk_soc ='.$tercero;
		$sql .= ' WHERE b.rowid='.$this->rowid;
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
			return 1;
		}
	}

	public function vincular_consulta_factura($consulta,User $user, $notrigger = false)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$sql = 'INSERT INTO llx_facturas_consulta(';
		$sql.= 'entity,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_created,';
		$sql.= 'fk_consulta,';
		$sql.= 'fk_factura,';
		$sql.= 'statut';
		
		$sql .= ') VALUES (';
		$sql .= ' '.$conf->entity.',';
		$sql .= ' "'.$this->db->idate(dol_now()).'",';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.$this->db->escape($this->rowid).",";
		$sql .= ' '.$this->db->escape($consulta).',1 ';
		
		$sql .= ')';

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->cambiar_statut(2,$user);
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);
		}
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}


	


	function select_dol($selected='',$table='', $htmlname='inputref', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
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
			//var_dump($includeUsers);exit;
			//var_dump($user->users);exit;
		}

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT u.rowid, u.code, u.description, u.active";

        $sql.= " FROM ".MAIN_DB_PREFIX .$table." as u";
       
        /*if (! empty($user->societe_id)) $sql.= " AND u.fk_soc = ".$user->societe_id;
        if (is_array($exclude) && $excludeUsers) $sql.= " AND u.rowid NOT IN ('".$excludeUsers."')";
        if (is_array($include) && $includeUsers) $sql.= " AND u.rowid IN ('".$includeUsers."')";
        if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX) || $noactive) $sql.= " AND u.statut <> 0";
        if (! empty($morefilter)) $sql.=" ".$morefilter;*/
        $sql.= " ORDER BY u.description ASC";
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
                    $userstatic->code=$obj->code;
                    $userstatic->description=$obj->description;
                    $userstatic->active=$obj->active;

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
    function select_dolpacientes($selected='', $htmlname='inputref', $show_empty=0, $exclude='', $disabled=0, $include='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0, $morefilter='', $show_every=0, $enableonlytext='', $morecss='', $noactive=0)
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
			//var_dump($includeUsers);exit;
			//var_dump($user->users);exit;
		}

        $out='';

        // On recherche les utilisateurs
        $sql = "SELECT DISTINCT u.rowid,  CONCAT(u.lastname,' ',u.firstname) as nombre, u.statut";
        $sql.= " FROM ".MAIN_DB_PREFIX ."pacientes as u where u.statut=1";
       
        /*if (! empty($user->societe_id)) $sql.= " AND u.fk_soc = ".$user->societe_id;
        if (is_array($exclude) && $excludeUsers) $sql.= " AND u.rowid NOT IN ('".$excludeUsers."')";
        if (is_array($include) && $includeUsers) $sql.= " AND u.rowid IN ('".$includeUsers."')";
        if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX) || $noactive) $sql.= " AND u.statut <> 0";
        if (! empty($morefilter)) $sql.=" ".$morefilter;*/
        $sql.= " ORDER BY nombre ASC";
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


    



	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function listar_consultas($id)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT
			a.*,b.lastname,b.firstname
		FROM
			llx_consultas AS a
		INNER JOIN llx_pacientes AS b ON a.fk_user_pacientes=b.rowid
		WHERE
			a.entity ='.$conf->entity.' AND 
			b.entity ='.$conf->entity.' AND 
			a.fk_user_pacientes= '.trim($id).'
		ORDER BY a.date_consultation desc';
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($obj)
					{
					     $array[$i]=$obj;
					}
					$i++;
				}
			}
			return $array;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}

	function list_replacable_invoices()
	{
		global $conf;

		$return = array();

		$sql = "SELECT f.rowid as rowid, f.facnumber, f.fk_statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."facture as f";
		$sql.= " WHERE ";
		$sql.=" 
		f.entity =".$conf->entity." AND 
		f.rowid NOT IN (SELECT a.fk_factura FROM llx_facturas_consulta as a WHERE a.fk_consulta=".$this->rowid.")";
		$sql.= " ORDER BY f.facnumber";
		//dol_syslog(get_class($this)."::list_replacable_invoices", LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj=$this->db->fetch_object($resql))
			{
				$return[$obj->rowid]=array(	'id' => $obj->rowid,
				'ref' => $obj->facnumber,
				'status' => $obj->fk_statut);
			}
			//print_r($return);
			return $return;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}


	public function listar_motivo_consulta($id)
	{

		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT a.description FROM llx_c_motivo_consulta as a WHERE a.active=1 AND a.rowid='.trim($id);
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$res= $this->db->fetch_object($resql);
				
				return $res->description;
			}
			return $array;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}

	public function listar_diagnosticos($id)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT a.description FROM llx_c_tipo_diagnostico as a WHERE
		 a.entity ='.$conf->entity.' AND 
		 a.active=1 AND a.rowid='.trim($id);
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$res= $this->db->fetch_object($resql);
				
				return $res->description;
			}
			return $array;
		}else{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return array();
		}
	}


	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		$sql .= " t.Ref,";
		$sql .= " fk_user_pacientes,";
		$sql .= " t.date_consultation,";
		$sql .= " t.date_creation,";
		$sql .= " t.fk_user_creation,";
		$sql .= " t.date_validation,";
		$sql .= " t.fk_user_validation,";
		$sql .= " t.date_clos,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.Type_consultation,";
		$sql .= " t.weight,";
		$sql .= " t.blood_pressure,";
		$sql .= " t.fk_user_med,";
		$sql .= " t.reason,";
		$sql .= " t.reason_detail,";
		$sql .= " t.diagnostics,";
		$sql .= " t.diagnostics_detail,";
		$sql .= " t.treatments,";
		$sql .= " t.comments,";
		$sql .= " t.fk_evento,";
		$sql .= " t.statut,";
		$sql .= " t.temperature";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' WHERE t.rowid = ' . $id;


		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->rowid = $obj->rowid;
				$this->id = $obj->rowid;
				
				$this->Ref = $obj->Ref;
				$this->date_consultation = $obj->date_consultation;
				$this->date_creation = $obj->date_creation;
				$this->fk_user_creation = $obj->fk_user_creation;
				$this->date_validation = $obj->date_validation;
				$this->fk_user_validation = $obj->fk_user_validation;
				$this->date_clos = $obj->date_clos;
				$this->fk_user_close = $obj->fk_user_close;
				$this->Type_consultation = $obj->Type_consultation;
				$this->weight = $obj->weight;
				$this->blood_pressure = $obj->blood_pressure;
				$this->fk_user_med = $obj->fk_user_med;
				$this->reason = $obj->reason;
				$this->reason_detail = $obj->reason_detail;
				$this->diagnostics = $obj->diagnostics;
				$this->diagnostics_detail = $obj->diagnostics_detail;
				$this->treatments = $obj->treatments;
				$this->comments = $obj->comments;
				$this->fk_user_pacientes= $obj->fk_user_pacientes;
				$this->statut = $obj->statut;
				$this->fk_evento=$obj->fk_evento;
				$this->temperature=$obj->temperature;

				require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
                $extrafields=new ExtraFields($this->db);
                $extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
               	$this->fetch_optionals($this->id,$extralabels);
				
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
		
		$sql .= " t.Ref,";
		$sql .= " fk_user_pacientes,";
		$sql .= " t.date_consultation,";
		$sql .= " t.date_creation,";
		$sql .= " t.fk_user_creation,";
		$sql .= " t.date_validation,";
		$sql .= " t.fk_user_validation,";
		$sql .= " t.date_clos,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.fk_user_pacientes,";
		$sql .= " t.Type_consultation,";
		$sql .= " t.weight,";
		$sql .= " t.blood_pressure,";
		$sql .= " t.fk_user_med,";
		$sql .= " t.reason,";
		$sql .= " t.reason_detail,";
		$sql .= " t.diagnostics,";
		$sql .= " t.diagnostics_detail,";
		$sql .= " t.treatments,";
		$sql .= " t.comments,";
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
				$line = new ConsultasLine();

				$line->id = $obj->rowid;
				
				$line->Ref = $obj->Ref;
				$line->fk_user_pacientes= $obj->fk_user_pacientes;
				$line->date_consultation = $obj->date_consultation;
				$line->date_creation = $obj->date_creation;
				$line->fk_user_creation = $obj->fk_user_creation;
				$line->date_validation = $obj->date_validation;
				$line->fk_user_validation = $obj->fk_user_validation;
				$line->date_clos = $obj->date_clos;
				$line->fk_user_close = $obj->fk_user_close;
				$line->Type_consultation = $obj->Type_consultation;
				$line->weight = $obj->weight;
				$line->blood_pressure = $obj->blood_pressure;
				$line->fk_user_med = $obj->fk_user_med;
				$line->reason = $obj->reason;
				$line->reason_detail = $obj->reason_detail;
				$line->diagnostics = $obj->diagnostics;
				$line->diagnostics_detail = $obj->diagnostics_detail;
				$line->treatments = $obj->treatments;
				$line->comments = $obj->comments;
				$line->statut = $obj->statut;
				$line->fk_user_pacientes = $obj->fk_user_pacientes;
				

				

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
		
		if (isset($this->weight)) {
			 $this->weight = trim($this->weight);
		}
		if (isset($this->Type_consultation)) {
			 $this->Type_consultation = trim($this->Type_consultation);
		}
		if (isset($this->blood_pressure)) {
			 $this->blood_pressure = trim($this->blood_pressure);
		}
		if (isset($this->reason)) {
			 $this->reason = trim($this->reason);
		}
		if (isset($this->reason_detail)) {
			 $this->reason_detail = trim($this->reason_detail);
		}
		if (isset($this->treatments)) {
			 $this->treatments = trim($this->treatments);
		}
		if (isset($this->diagnostics)) {
			 $this->diagnostics = trim($this->diagnostics);
		}
		if (isset($this->diagnostics_detail)) {
			 $this->diagnostics_detail = trim($this->diagnostics_detail);
		}
		if (isset($this->comments)) {
			 $this->comments = trim($this->comments);
		}
		if (isset($this->fk_user_med)) {
			 $this->fk_user_med = trim($this->fk_user_med);
		}
		if (isset($this->fk_user_pacientes)) {
			 $this->fk_user_pacientes = trim($this->fk_user_pacientes);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		$sql .= ' Ref = "'.(isset($this->Ref)?$this->Ref:"Invalido").'",';
		$sql .= ' date_consultation = '.(! isset($this->date_consultation) || dol_strlen($this->date_consultation) != 0 ? "'".$this->db->idate($this->date_consultation)."'" : 'null').',';
		$sql .= ' date_creation = '.(! isset($this->date_creation) || dol_strlen($this->date_creation) != 0 ? "'".$this->db->idate($this->date_creation)."'" : 'null').',';
		$sql .= ' date_validation = '.(! empty($this->date_validation) || dol_strlen($this->date_validation) != 0 ? "'".$this->db->idate($this->date_validation)."'" : 'null').',';
		$sql .= ' fk_user_validation = '.(isset($this->fk_user_validation)?$this->fk_user_validation:"null").',';
		$sql .= ' date_clos = '.(! empty($this->date_clos) || dol_strlen($this->date_clos) != 0 ? "'".$this->date_clos."'" : 'null').',';
		$sql .= ' fk_user_close = '.(isset($this->fk_user_close)?"'".$this->db->escape($this->fk_user_close)."'":"null").',';
		$sql .= ' Type_consultation = '.(isset($this->Type_consultation)?$this->Type_consultation:"null").',';
		$sql .= ' weight = '.(!empty($this->weight)?"'".$this->weight."'":"null").',';
		$sql .= ' blood_pressure = '.(!empty($this->blood_pressure)?"'".$this->db->escape($this->blood_pressure)."'":"null").',';
		$sql .= ' fk_user_med = '.(isset($this->fk_user_med)?"'".$this->db->escape($this->fk_user_med)."'":"null").',';
		$sql .= ' reason = '.(isset($this->reason)?$this->reason:"null").',';
		$sql .= ' reason_detail = '.(!empty($this->reason_detail)?"'".$this->db->escape($this->reason_detail)."'":"null").',';
		$sql .= ' diagnostics = '.(isset($this->diagnostics)?$this->diagnostics:"null").',';
		$sql .= ' temperature = '.(!empty($this->temperature)?$this->temperature:"null").',';
		$sql .= ' diagnostics_detail = '.(!empty($this->diagnostics_detail)?"'".$this->db->escape($this->diagnostics_detail)."'":"null").',';
		$sql .= ' treatments = '.(!empty($this->treatments)?"'".$this->db->escape($this->treatments)."'":"null").',';
		$sql .= ' comments = '.(!empty($this->comments)?"'".$this->db->escape($this->comments)."'":"null").'';
		$sql .= ' WHERE rowid=' . $this->rowid;

		$this->db->begin();
		$resql = $this->db->query($sql);
		
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}
		if ($resql) {
			$this->id=$this->rowid;
			
    		$result=$this->insertExtraFields();
    			
    		if ($result < 0)
    		{
    			$error++;
    		}
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
	public function update2(User $user, $notrigger = false){
		$error = 0;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$var=$this->update($user);

		if ($var==1) {
			$var=$this->cambiar_statut(1,$user);
			if ($var==1) {
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}


	public function cambiar_statut($status,User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);
		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' statut = '.$status.'';
		if ($status==1) {
			$sql.= ',fk_user_close="",date_clos=""';
		}
		if ($status==3 || $status==2 ) {
			$sql.= ',fk_user_close='.$user->id.",date_clos='".$this->db->idate(dol_now())."'";
		}
		$sql .= ' WHERE rowid=' . $this->rowid;

		$this->db->begin();
		$resql = $this->db->query($sql);

		if ($resql) {
			$this->fetch($this->rowid);
		}

		if ($resql && ($status==3 || $status==2 ) ) {
			require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
			$this->fetch($this->rowid);
			$evento= new ActionComm($this->db);
			$evento->datep=$this->date_consultation;
			$evento->type_id=50;
			$evento->type_code="AC_OTH";
			$evento->note="Consulta ".str_replace("!","",$this->Ref);
			$evento->userownerid=$this->fk_user_med;
			$evento->label="Consulta ".str_replace("!","",$this->Ref);
			$evento->percentage=-1;
			$evento->punctual=1;
			$evento->transparency=1;
			$id_evento=$evento->add($user);
			if ($id_evento>0) {
				$sql="SELECT a.rowid,fk_evento FROM llx_eventos_consultas as a WHERE a.fk_paciente=".$this->fk_user_pacientes." AND a.fk_consulta=".$this->rowid;
				$resql = $this->db->query($sql);
				if ($resql) {
					$num = $this->db->num_rows($resql);
					if ($num){
						$obj = $this->db->fetch_object($resql);
						$sql="DELETE FROM llx_actioncomm WHERE llx_actioncomm.id=".$obj->fk_evento;
						$this->db->query($sql);
						$sql="UPDATE llx_eventos_consultas as a SET a.fk_evento=".$id_evento." WHERE a.fk_paciente=".$this->fk_user_pacientes." AND a.fk_consulta=".$this->rowid;
						$resql = $this->db->query($sql);
					}else{
						$sql="insert into llx_eventos_consultas (fk_paciente,fk_evento,fk_consulta) values (".$this->fk_user_pacientes.",".$id_evento.",".$this->rowid.");";
						$resql = $this->db->query($sql);
					}
				}
			}
		}


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


	public function cambiar_vinculo($aid,User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);
		// Update request
		$sql = 'UPDATE llx_facturas_consulta SET';
		
		$sql .= ' date_remove = "'.$this->db->idate(dol_now()).'",';
		$sql .= ' fk_user_remove = '.$user->id.', statut=0';
		$sql .= ' WHERE rowid=' . $aid;

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
			$this->fetch($this->rowid);
			$sql2=' 
				DELETE llx_actioncomm.*
				FROM
					llx_actioncomm
				INNER JOIN llx_consultas ON llx_actioncomm.id = llx_consultas.fk_evento
				WHERE llx_consultas.rowid='.$this->rowid;
			$this->db->query($sql2);

			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->rowid;
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
		$object = new Consultas($this->db);

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

        $label = '<u>Control Clinico</u>';
        $label.= '<div width="100%">';
        $label.= '<b>Referencia:</b> ' . str_replace("!", "", $this->Ref);
        $label.= '<br><b>Fecha de Consulta:</b> '.$this->date_consultation."<br>";
        $link = '<a href="'.DOL_URL_ROOT.'/cclinico/consultas_card.php?aid='.$this->rowid.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link.str_replace("!", "", $this->Ref).$linkend;
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
		
		$this->Ref = '';
		$this->fk_user_pacientes = '';
		$this->date_consultation = '';
		$this->date_creation = '';
		$this->fk_user_creation = '';
		$this->date_validation = '';
		$this->fk_user_validation = '';
		$this->date_clos = '';
		$this->fk_user_close = '';
		$this->Type_consultation = '';
		$this->weight = '';
		$this->blood_pressure = '';
		$this->fk_user_med = '';
		$this->reason = '';
		$this->reason_detail = '';
		$this->diagnostics = '';
		$this->diagnostics_detail = '';
		$this->treatments = '';
		$this->comments = '';
		$this->statut = '';

		
	}

}

/**
 * Class ConsultasLine
 */
class ConsultasLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $Ref;
	public $date_consultation = '';
	public $date_creation = '';
	public $fk_user_creation;
	public $date_validation = '';
	public $fk_user_validation;
	public $date_clos = '';
	public $fk_user_close;
	public $Type_consultation;
	public $weight;
	public $blood_pressure;
	public $fk_user_med;
	public $reason;
	public $reason_detail;
	public $diagnostics;
	public $diagnostics_detail;
	public $treatments;
	public $comments;
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
