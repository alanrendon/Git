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
 * \file    ctrlanticipo/ctrladvanceproviderpayment.class.php
 * \ingroup ctrlanticipo
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
if (file_exists(DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'));
	require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
if (file_exists(DOL_DOCUMENT_ROOT . '/fourn/class/paiementfourn.class.php'));
	require_once DOL_DOCUMENT_ROOT . '/fourn/class/paiementfourn.class.php';
if (file_exists(DOL_DOCUMENT_ROOT . '/ctrlanticipo/class/ctrladvanceprovider.class.php'));
	require_once DOL_DOCUMENT_ROOT . '/ctrlanticipo/class/ctrladvanceprovider.class.php';
if (file_exists(DOL_DOCUMENT_ROOT . '/multicurrency/class/multicurrency.class.php'));
	require_once DOL_DOCUMENT_ROOT .'/multicurrency/class/multicurrency.class.php';
if (file_exists(DOL_DOCUMENT_ROOT . '/ctrlanticipo/class/ctrladvancecredit.class.php'));
	require_once DOL_DOCUMENT_ROOT .'/ctrlanticipo/class/ctrladvancecredit.class.php';	




/**
 * Class Ctrladvanceprovider
 *
 * Put here description of your class
 * @see CommonObject
 */

//pago del anticipo  llx_ctrl_paiementfourn
class PaiementAdvance extends PaiementFourn 
{
	
	public $table_element='ctrl_paiementfourn';
	function __construct($db)
	{
		 parent::__construct($db);

	}




	function delete($notrigger=0)
	{
		global $conf, $user, $langs;

		$error=0;

		$bank_line_id = $this->bank_line;

		$this->db->begin();

		$sql='SELECT
			a.fk_facturefourn
		FROM
			llx_ctrl_paiementfourn_facturefourn AS a
		WHERE
			a.fk_paiementfourn = '.$this->id;

		$resql = $this->db->query($sql);	
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num > 0)
			{
				$obj = $this->db->fetch_object($resql);
				$sql_prov=' 
				SELECT
					a.rowid
				FROM
					llx_ctrl_advance_credit AS a
				WHERE
					a.fk_advance = '.$obj->fk_facturefourn.'
				AND (a.statut=2 OR a.statut=3)';
				$resql_prov = $this->db->query($sql_prov);
				if ($resql_prov) {
					$num_prov = $this->db->num_rows($resql_prov);
					if ($num_prov > 0)
					{
						$this->error=$langs->trans("ctrl_credit_vinc_exist");
						$this->db->rollback();
						return -1;
					}

					//delete link credit
					$sql_del="DELETE FROM llx_ctrl_advance_credit WHERE fk_advance=".$obj->fk_facturefourn;
					$this->db->query($sql_del);
				}



				//delete link url
				$sql="DELETE a.*
				FROM
					llx_bank_url as a
				INNER JOIN llx_ctrl_paiementfourn AS b ON a.url_id = b.rowid
				WHERE b.rowid=".$this->id;
				$result = $this->db->query($sql);

				//delete account line
				$sql="DELETE a.*
				FROM
					llx_bank AS a
				INNER JOIN llx_ctrl_paiementfourn AS b ON a.rowid = b.fk_bank
				WHERE b.rowid=".$this->id;
				$result = $this->db->query($sql);

				// Delete payment (into paiement_facture and paiement)
				$sql = 'DELETE FROM llx_ctrl_paiementfourn';
				$sql.= ' WHERE rowid = '.$this->id;

				$result = $this->db->query($sql);
				$this->db->commit();

				if ($result)
				{
					$sql = 'DELETE FROM llx_ctrl_paiementfourn_facturefourn';
					$sql.= ' WHERE  fk_paiementfourn = '.$this->id;

					$result = $this->db->query($sql);
					

					

					$sql='
					SELECT
						a.rowid
					FROM
						llx_ctrl_paiementfourn_facturefourn AS a
					WHERE
						a.fk_facturefourn = '.$obj->fk_facturefourn;


					$resql2 = $this->db->query($sql);
					if ($resql2) {
						$num2 = $this->db->num_rows($resql2);
						//return $num2;
						if ($num2 > 0)
						{

							$sql='
							UPDATE llx_ctrl_advance_provider AS a
							SET a.statut=5
							WHERE a.rowid='.$obj->fk_facturefourn;
							$this->db->query($sql);
						}else{

							$sql='
							UPDATE llx_ctrl_advance_provider AS a
							SET a.statut=2
							WHERE a.rowid='.$obj->fk_facturefourn;
							$this->db->query($sql);
						}
					}


					if (! $result)
					{

						$this->error=$this->db->lasterror();
						$this->db->rollback();
						return -3;
					}

					$this->db->commit();
					return 1;
				}
				else
				{
					$this->error=$this->db->error;
					$this->db->rollback();
					return -5;
				}
			}
		}

	}

	function info($id)
	{
		$sql = 'SELECT p.rowid, p.datec, p.fk_user_author, p.fk_user_modif, p.tms';
		$sql.= ' FROM llx_ctrl_paiementfourn as p';
		$sql.= ' WHERE p.rowid = '.$id;
		dol_syslog(get_class($this).'::info', LOG_DEBUG);
		$result = $this->db->query($sql);

		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation     = $cuser;
				}
				if ($obj->fk_user_modif)
				{
					$muser = new User($this->db);
					$muser->fetch($obj->fk_user_modif);
					$this->user_modification = $muser;
				}
				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->tms);
			}
			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	function update_date($date,$user)
    {
        if (!empty($date) && $this->statut!=1)
        {
            $sql = "UPDATE llx_ctrl_paiementfourn ";
            $sql.= " SET fk_user_modif=".$user->id." , datep = '".$this->db->idate($date)."'";
            $sql.= " WHERE rowid = ".$this->id;

            dol_syslog(get_class($this)."::update_date", LOG_DEBUG);
            $result = $this->db->query($sql);
            if ($result)
            {
            	$this->datepaye = $date;
                $this->date = $date;
                return 0;
            }
            else
            {
                $this->error='Error -1 '.$this->db->error();
                return -2;
            }
        }
        return -1; //no date given or already validated
    }
    function update_num($num,$user)
    {
    	if(!empty($num) && $this->statut!=1)
        {
            $sql = "UPDATE llx_ctrl_paiementfourn";
            $sql.= " SET fk_user_modif=".$user->id." , num_paiement = '".$this->db->escape($num)."'";
            $sql.= " WHERE rowid = ".$this->id;

            dol_syslog(get_class($this)."::update_num", LOG_DEBUG);
            $result = $this->db->query($sql);
            if ($result)
            {
            	$this->numero = $this->db->escape($num);
                return 0;
            }
            else
            {
                $this->error='Error -1 '.$this->db->error();
                return -2;
            }
        }
        return -1; //no num given or already validated
    }

	function create_payment($user,$closepaidinvoices=0)
	{
		global $langs,$conf;

		$error = 0;
		$way = $this->getWay();

		// Clean parameters
		$totalamount = 0;
		$totalamount_converted = 0;
		
		if ($way == 'dolibarr')
		{
			$amounts = &$this->amounts;
			$amounts_to_update = &$this->multicurrency_amounts;
		}
		else
		{
			$amounts = &$this->multicurrency_amounts;
			$amounts_to_update = &$this->amounts;
		}
		
		foreach ($amounts as $key => $value)
		{
			$advance = new Ctrladvanceprovider($this->db);
			$advance->fetch($key);
			if ($value>$advance->total_import) {
				$this->error="ctrl_ammount_to_far";
				$error++;
				return -1;
			}
			$value_converted = Multicurrency::getAmountConversionFromInvoiceRate($key, $value, $way, 'ctrl_advance_provider');
			$totalamount_converted += $value_converted;
			$amounts_to_update[$key] = price2num($value_converted, 'MT');
			$newvalue = price2num($value,'MT');
			$amounts[$key] = $newvalue;
			$totalamount += $newvalue;
		}
		

		$totalamount = price2num($totalamount);
		$totalamount_converted = price2num($totalamount_converted);

		//$this->db->begin();

		if ($totalamount <> 0) // On accepte les montants negatifs
		{
			$ref = $this->getNextNumRef();
			$now=dol_now();
			
			if ($way == 'dolibarr')
			{
				$total = $totalamount;
				$mtotal = $totalamount_converted; // Maybe use price2num with MT for the converted value
			}
			else
			{
				$total = $totalamount_converted; // Maybe use price2num with MT for the converted value
				$mtotal = $totalamount;
			}
		
			$sql = 'INSERT INTO llx_ctrl_paiementfourn (';
			$sql.= 'ref, entity, datec, datep, amount, multicurrency_amount, fk_paiement, num_paiement, note, fk_user_author, fk_bank_account)';
			$sql.= " VALUES ('".$this->db->escape($ref)."', ".$conf->entity.", '".$this->db->idate($now)."',";
			$sql.= " '".$this->db->idate($this->datepaye)."', '".$total."', '".$mtotal."', ".$this->paiementid.", '".$this->num_paiement."', '".$this->db->escape($this->note)."', ".$user->id.", ".((!empty($this->bank_line) && isset($this->bank_line))?$this->bank_line:0).")";


			dol_syslog("PaiementFourn_ctrl::create", LOG_DEBUG);
			
			$resql = $this->db->query($sql);

			if ($resql)
			{
				$this->id = $this->db->last_insert_id('llx_ctrl_paiementfourn');

				// Insere tableau des montants / factures
				foreach ($this->amounts as $key => $amount)
				{
					$facid = $key;
					if (is_numeric($amount) && $amount <> 0)
					{
						$amount = price2num($amount);
						$sql = 'INSERT INTO llx_ctrl_paiementfourn_facturefourn (fk_facturefourn, fk_paiementfourn, amount, multicurrency_amount)';
						$sql .= ' VALUES ('.$facid.','. $this->id.',\''.$amount.'\', \''.$this->multicurrency_amounts[$key].'\')';
						$resql=$this->db->query($sql);
						$this->setstatut_paiment($facid,$user);

						if ($this->paiementid==7) {
							format_chec($facid);
						}
						
						if (!$resql){
							dol_syslog('Paiement::Create Erreur INSERT dans llx_ctrl_paiementfourn_facturefourn '.$facid);
							$error++;
						}
					}
					else
					{
						dol_syslog('PaiementFourn::Create Montant non numerique',LOG_ERR);
					}
				}
			}
			else
			{
				$this->error=$this->db->lasterror();
				$error++;
			}
		}
		else
		{
			$this->error="ErrorTotalIsNull";
			dol_syslog('PaiementFourn::Create Error '.$this->error, LOG_ERR);
			$error++;
		}

		if ($totalamount <> 0 && $error == 0) // On accepte les montants negatifs
		{
			$this->amount=$total;
		    $this->total=$total;
			$this->multicurrency_amount=$mtotal;
			$this->db->commit();
			dol_syslog('PaiementFourn_ctrl::Create Ok Total = '.$this->total);
			return $this->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}

	function setstatut_paiment($id,$user)
	{
		$sql="
		SELECT
			b.total_import-sum(a.amount) as total_sum
		FROM
			llx_ctrl_paiementfourn_facturefourn AS a
		INNER JOIN llx_ctrl_advance_provider as b on b.rowid=a.fk_facturefourn
		WHERE	a.fk_facturefourn = ".$id;

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num > 0)
			{
				$obj = $this->db->fetch_object($resql);
				$advance=new Ctrladvanceprovider($this->db);
				$advance->fetch($id);
				if ($obj->total_sum==0  ) {
					$advance->statut= 3;
					$credit = new Ctrladvancecredit($this->db);
					$adv    = new Ctrladvanceprovider($this->db);
					$adv->fetch($id);
					$credit->fk_advance=$id;
					$credit->fk_soc=$adv->fk_soc;
					$credit->date_c=dol_now();
					$credit->import=$advance->import;
					$credit->fk_tva=$advance->fk_tva;
					$credit->total_import=$advance->total_import;

					$res=$credit->create($user);
					
				}else{
					$advance->statut= 5;
				}

				$advance->update($user);
				

			}else{
				return -7;
			}
		}else{
			return -8;
		}
	}

	function getNextNumRef($mode='next')
	{
		global $conf, $db, $langs;
		$langs->load("bills");
		// Clean parameters (if not defined or using deprecated value)
		$mod_ref=$conf->global->SOCIETE_CODECTRLPAYMENT_ADDON;
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

		// For compatibility
		if (! $mybool)
		{
			$file = $mod_ref.".php";
			$classname = "mod_codepay_".$mod_ref;
			$classname = preg_replace('/\-.*$/','',$classname);
			// Include file with class
			foreach ($conf->file->dol_document_root as $dirroot)
			{
				$dir = $dirroot."/ctrlanticipo/core/modules/";
				// Load file with numbering class (if found)
				if (is_file($dir.$file) && is_readable($dir.$file)) {
					$mybool |= include_once $dir . $file;
				}
			}
		}
		if (! $mybool)
		{
			dol_print_error('',"Failed to include file ".$file);
			return '';
		}
		 

		$obj = new $classname();
		$numref = "";
		$numref = $obj->getNextValue($this);

		/**
		 * $numref can be empty in case we ask for the last value because if there is no invoice created with the
		 * set up mask.
		 */
		if ($mode != 'last' && !$numref) {
			dol_print_error($db,"SupplierPayment::getNextNumRef ".$obj->error);
			return "";
		}

		return $numref;

	}


	function fetch($id, $ref='', $fk_bank='')
	{
	    $error=0;
	    
		$sql = '
		SELECT
			p.rowid,
			p.ref,
			p.entity,
			p.datep AS dp,
			p.amount,
			p.statut,
			p.fk_paiement,
			p.fk_bank_account,
			p.fk_bank,
			c. CODE AS paiement_code,
			c.libelle AS paiement_type,
			p.num_paiement,
			p.note
		FROM
			llx_ctrl_paiementfourn AS p
		INNER JOIN '.MAIN_DB_PREFIX.'c_paiement AS c on p.fk_paiement = c.id';
		$sql.= ' WHERE ';
		if ($id > 0)
			$sql.= ' p.rowid = '.$id;
		else if ($ref)
			$sql.= ' p.rowid = '.$ref;
		else if ($fk_bank)
			$sql.= ' p.fk_bank = '.$fk_bank;
		$resql = $this->db->query($sql);

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num > 0)
			{
				$obj = $this->db->fetch_object($resql);
				$this->id             = $obj->rowid;
				$this->ref            = $obj->ref;
				$this->entity         = $obj->entity;
				$this->fk_paiement    = $obj->fk_paiement;
				$this->date           = $this->db->jdate($obj->dp);
				$this->numero         = $obj->num_paiement;
				$this->bank_account   = $obj->fk_bank_account;
				$this->bank_line      = $obj->fk_bank;
				$this->montant        = $obj->amount;
				$this->note           = $obj->note;
				$this->type_code      = $obj->paiement_code;
				$this->type_libelle   = $obj->paiement_type;
				$this->statut         = $obj->statut;
				$error = 1;
			}
			else
			{
				$error = -2;    // TODO Use 0 instead
			}
			$this->db->free($resql);
		}
		else
		{
			dol_print_error($this->db);
			$error = -1;
		}
		return $error;
	}


	function getNomUrl($withpicto=0,$option='')
	{
		global $langs;

		$result='';
        $text=$this->ref;   // Sometimes ref contains label
        if (preg_match('/^\((.*)\)$/i',$text,$reg)) {
            // Label generique car entre parentheses. On l'affiche en le traduisant
            if ($reg[1]=='paiement') $reg[1]='Payment';
            $text=$langs->trans($reg[1]);
        }

        
        $label = $langs->trans("ShowPayment").': '.$text;

        $sql = '
        SELECT
			b.ref,b.statut,a.*
		FROM
			llx_ctrl_paiementfourn_facturefourn AS a
		INNER JOIN llx_ctrl_advance_provider as b on a.fk_facturefourn=b.rowid
		WHERE
			a.fk_paiementfourn = '.$this->id;
		$resql=$this->db->query($sql);

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($i < $num){
				$objp = $this->db->fetch_object($resql);
				$label.="<br>".img_object('', 'service', '')."  ".$objp->ref."   ".img_picto($langs->trans('ctrl_action_statut'.$objp->statut),'statut'.(($objp->statut==5)?7:$objp->statut)      ) ."  ".$langs->trans('ctrl_action_statut'.$objp->statut);
				$i++;
			}
		}


        $link = '<a href="'.DOL_URL_ROOT.'/ctrlanticipo/view/ctrlpaiementfourn_card.php?id='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $linkend='</a>';


        if ($withpicto) $result.=($link.img_object($langs->trans("ShowPayment"), 'payment', 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.$text.$linkend;
		return $result;
	}

	function addPaymentToBank($user,$mode,$label,$accountid,$emetteur_nom,$emetteur_banque,$notrigger=0)
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
            $result=$acc->fetch($this->fk_account);
			
			$totalamount=$this->amount;
            if (empty($totalamount)) $totalamount=$this->total; // For backward compatibility
            
            // if dolibarr currency != bank currency then we received an amount in customer currency (currently I don't manage the case : my currency is USD, the customer currency is EUR and he paid me in GBP. Seems no sense for me)
            if (!empty($conf->multicurrency->enabled) && $conf->currency != $acc->currency_code) $totalamount=$this->multicurrency_amount;
			
            if ($mode == 'payment_supplier') $totalamount=-$totalamount;

            // Insert payment into llx_bank
            $bank_line_id = $acc->addline(
                $this->datepaye,
                $this->paiementid,  // Payment mode id or code ("CHQ or VIR for example")
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
            if (! $error && $label == 'ctrl_customer_payment')
			{
				$result=$acc->add_url_line(
					$bank_line_id,
					$this->id,
					DOL_URL_ROOT.'/ctrlanticipo/view/ctrlpaiementfourn_card.php?id=',
					$this->ref,
					'PaiementAdvance'
				);
			}
            if ($bank_line_id > 0)
            {
                $result=$this->update_fk_bank($bank_line_id,$user);
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

    function update_fk_bank($id_bank,$user)
	{
		$sql = 'UPDATE llx_ctrl_paiementfourn set fk_user_modif='.$user->id.' , fk_bank = '.$id_bank;
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

}
