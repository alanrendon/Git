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
 * \file    ctrlanticipo/ctrlrefundcredit.class.php
 * \ingroup ctrlanticipo
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Ctrlrefundcredit
 *
 * Put here description of your class
 * @see CommonObject
 */
class Ctrlrefundcredit extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'ctrlrefundcredit';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ctrl_refund_credit';

	/**
	 * @var CtrlrefundcreditLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	public $id;
	public $fk_soc;
	public $ref;
	public $date_apply = '';
	public $fk_paymen;
	public $fk_bank_account;
	public $num_paiment;
	public $transfer;
	public $bank;
	public $note;
	public $date_c;

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
		
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_paymen)) {
			 $this->fk_paymen = trim($this->fk_paymen);
		}
		if (isset($this->fk_bank_account)) {
			 $this->fk_bank_account = trim($this->fk_bank_account);
		}
		if (isset($this->num_paiment)) {
			 $this->num_paiment = trim($this->num_paiment);
		}
		if (isset($this->transfer)) {
			 $this->transfer = trim($this->transfer);
		}
		if (isset($this->bank)) {
			 $this->bank = trim($this->bank);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}

		$sql = 'INSERT INTO llx_ctrl_refund_credit(';
		$sql.= 'ref,';
		$sql.= 'fk_credit,';
		$sql.= 'fk_soc,';
		$sql.= 'date_apply,';
		$sql.= 'fk_paymen,';
		$sql.= 'fk_bank_account,';
		$sql.= 'num_paiment,';
		$sql.= 'transfer,';
		$sql.= 'bank,';
		$sql.= 'note,';
		$sql.= 'date_c,';
		$sql.= 'fk_user';
		$sql .= ') VALUES (';
		
		$sql .= ' "'.$this->getNextNumRef().'",';
		$sql .= ' '.(! isset($this->fk_credit)?'NULL':$this->fk_credit).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->date_apply) || dol_strlen($this->date_apply)==0?'NULL':"'".$this->db->idate($this->date_apply)."'").',';
		$sql .= ' '.(! isset($this->fk_paymen)?'NULL':$this->fk_paymen).',';
		$sql .= ' '.(! isset($this->fk_bank_account)?'NULL':$this->fk_bank_account).',';
		$sql .= ' '.(! isset($this->num_paiment)?'NULL':"'".$this->db->escape($this->num_paiment)."'").',';
		$sql .= ' '.(! isset($this->transfer)?'NULL':"'".$this->db->escape($this->transfer)."'").',';
		$sql .= ' '.(! isset($this->bank)?'NULL':"'".$this->db->escape($this->bank)."'").',';
		$sql .= ' '.(! isset($this->note)?'NULL':"'".$this->db->escape($this->note)."'").',';
		$sql .= ' "'.$this->db->idate(dol_now()).'",';
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


	function addPaymentToBank($user,$totalamount,$mode,$label,$accountid,$emetteur_nom,$emetteur_banque,$notrigger=0)
    {
        global $conf,$langs,$user;

        $error=0;
        $bank_line_id=0;

        if (! empty($conf->banque->enabled))
        {

        	if ($accountid <= 0)
        	{
        		$this->error='Bad value for parameter accountid';
        		dol_syslog(get_class($this).'::addPaymentToBank '.$this->error, LOG_ERR);
        		return -1;
        	}

        	$this->db->begin();

        	$this->fk_account=$accountid;

        	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

            dol_syslog("$user->id,$mode,$label,$this->fk_account,$emetteur_nom,$emetteur_banque");

            $acc = new Account($this->db);
            $result=$acc->fetch($this->fk_bank_account);
			
			
            
            // if dolibarr currency != bank currency then we received an amount in customer currency (currently I don't manage the case : my currency is USD, the customer currency is EUR and he paid me in GBP. Seems no sense for me)
			
            // Insert payment into llx_bank


            $bank_line_id = $acc->addline(
                $this->date_apply,
                $this->fk_paymen,  // Payment mode id or code ("CHQ or VIR for example")
                $langs->trans($label),
                $totalamount,
                $this->num_paiement,
                '',
                $user,
                $emetteur_nom,
                $emetteur_banque
            );
 

            // Mise a jour fk_bank dans llx_paiement
            // vinculo 
            if (! $error && $label == 'ctrl_customer_refund')
			{
				$result=$acc->add_url_line(
					$bank_line_id,
					$this->id,
					DOL_URL_ROOT.'/ctrlanticipo/view/ctrlrefundcredit_card.php?action=view&cid=',
					$this->ref,
					'RefundCredit'
				);
			}
            if ($bank_line_id > 0)
            {
                $result=$this->update_fk_bank($bank_line_id);
                if ($result <= 0)
                {
                    $error++;
                    dol_print_error($this->db);
                }
            }
            else
			{
                $this->error=$acc->error;
                $error++;
            }

            if (! $error)
            {
            	$this->db->commit();
            }
            else
			{
            	$this->db->rollback();
            }
        }

        if (! $error)
        {
            return $bank_line_id;
        }
        else
        {
            return -1;
        }
    }

    function update_fk_bank($id_bank)
	{
		$sql = 'UPDATE llx_ctrl_refund_credit set fk_bank_line = '.$id_bank;
		$sql.= ' WHERE rowid = '.$this->id;

		dol_syslog(get_class($this).'::update_fk_bank', LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			return 1;
		}
		else
		{
            $this->error=$this->db->lasterror();
            dol_syslog(get_class($this).'::update_fk_bank '.$this->error);
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
		$sql .= ' t.ref,';
		$sql .= " t.fk_credit,";
		$sql .= " t.fk_soc,";
		$sql .= " t.date_apply,";
		$sql .= " t.fk_paymen,";
		$sql .= " t.fk_bank_account,";
		$sql .= " t.num_paiment,";
		$sql .= " t.transfer,";
		$sql .= " t.bank,";
		$sql .= " t.date_c,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_bank_line,";
		$sql .= " t.note";

		
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
				$this->fk_soc = $obj->fk_soc;
				$this->fk_credit = $obj->fk_credit;
				$this->fk_bank_line = $obj->fk_bank_line;
				$this->date_apply = $this->db->jdate($obj->date_apply);
				$this->fk_paymen = $obj->fk_paymen;
				$this->fk_bank_account = $obj->fk_bank_account;
				$this->num_paiment = $obj->num_paiment;
				$this->transfer = $obj->transfer;
				$this->bank = $obj->bank;
				$this->date_c = $obj->date_c;
				$this->fk_user = $obj->fk_user;
				$this->note = $obj->note;
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

	public function getNextNumRef($mode='next')
	{
		global $conf, $db, $langs;
		$langs->load("bills");
		// Clean parameters (if not defined or using deprecated value)


		$mod_ref=$conf->global->SOCIETE_CODECTRLREFUND_ADDON;
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
		
		$sql .= " t.fk_soc,";
		$sql .= " t.date_apply,";
		$sql .= " t.fk_paymen,";
		$sql .= " t.fk_bank_account,";
		$sql .= " t.num_paiment,";
		$sql .= " t.transfer,";
		$sql .= " t.bank,";
		$sql .= " t.note";

		
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
				$line = new CtrlrefundcreditLine();

				$line->id = $obj->rowid;
				
				$line->fk_soc = $obj->fk_soc;
				$line->date_apply = $this->db->jdate($obj->date_apply);
				$line->fk_paymen = $obj->fk_paymen;
				$line->fk_bank_account = $obj->fk_bank_account;
				$line->num_paiment = $obj->num_paiment;
				$line->transfer = $obj->transfer;
				$line->bank = $obj->bank;
				$line->note = $obj->note;

				

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
		
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_paymen)) {
			 $this->fk_paymen = trim($this->fk_paymen);
		}
		if (isset($this->fk_bank_account)) {
			 $this->fk_bank_account = trim($this->fk_bank_account);
		}
		if (isset($this->num_paiment)) {
			 $this->num_paiment = trim($this->num_paiment);
		}
		if (isset($this->transfer)) {
			 $this->transfer = trim($this->transfer);
		}
		if (isset($this->bank)) {
			 $this->bank = trim($this->bank);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' rowid = '.(isset($this->rowid)?$this->rowid:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' date_apply = '.(! isset($this->date_apply) || dol_strlen($this->date_apply) != 0 ? "'".$this->db->idate($this->date_apply)."'" : 'null').',';
		$sql .= ' fk_paymen = '.(isset($this->fk_paymen)?$this->fk_paymen:"null").',';
		$sql .= ' fk_bank_account = '.(isset($this->fk_bank_account)?$this->fk_bank_account:"null").',';
		$sql .= ' num_paiment = '.(isset($this->num_paiment)?"'".$this->db->escape($this->num_paiment)."'":"null").',';
		$sql .= ' transfer = '.(isset($this->transfer)?"'".$this->db->escape($this->transfer)."'":"null").',';
		$sql .= ' bank = '.(isset($this->bank)?"'".$this->db->escape($this->bank)."'":"null").',';
		$sql .= ' note = '.(isset($this->note)?"'".$this->db->escape($this->note)."'":"null");

        
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
		//delete link url
		$sql="DELETE a.*
		FROM
			llx_bank_url AS a
		INNER JOIN llx_ctrl_refund_credit AS b ON a.url_id = b.rowid
		WHERE
			b.rowid =".$this->id;
		$result = $this->db->query($sql);

		//delete account line
		$sql="DELETE a.*
		FROM
			llx_bank AS a
		INNER JOIN llx_ctrl_refund_credit AS b ON a.rowid = b.fk_bank_line
		WHERE b.rowid=".$this->id;
		$result = $this->db->query($sql);

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
		$object = new Ctrlrefundcredit($this->db);

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

        $label = '<u>' . $langs->trans("ctrl_refund_tit_url") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrlrefundcredit_card.php?cid='.$this->id.'&action=view"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'projectpub', ($notooltip?'':'class="classfortooltip"')).$linkend);
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
		
		$this->fk_soc = '';
		$this->date_apply = '';
		$this->fk_paymen = '';
		$this->fk_bank_account = '';
		$this->num_paiment = '';
		$this->transfer = '';
		$this->bank = '';
		$this->note = '';

		
	}

}

/**
 * Class CtrlrefundcreditLine
 */
class CtrlrefundcreditLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_soc;
	public $date_apply = '';
	public $fk_paymen;
	public $fk_bank_account;
	public $num_paiment;
	public $transfer;
	public $bank;
	public $note;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
