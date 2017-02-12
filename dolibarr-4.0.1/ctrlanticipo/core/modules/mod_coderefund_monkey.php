<?php
/* Copyright (C) 2004		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2007	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2006-2012	Regis Houssin			<regis.houssin@capnetworks.com>
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
 * or see http://www.gnu.org/
 */

/**
 *       \file       htdocs/core/modules/societe/mod_codeclient_monkey.php
 *       \ingroup    societe
 *       \brief      Fichier de la classe des gestion lion des codes clients
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php';


/**
 *	Classe permettant la gestion monkey des codes tiers
 */
class mod_coderefund_monkey extends ModeleThirdPartyCode
{
	var $nom='Monkey';					// Nom du modele
	var $name='Monkey';					// Nom du modele
	var $code_modifiable;				// Code modifiable
	var $code_modifiable_invalide;		// Code modifiable si il est invalide
	var $code_modifiable_null;			// Code modifiables si il est null
	var $code_null;						// Code facultatif
	var $version='dolibarr';	    	// 'development', 'experimental', 'dolibarr'
	var $code_auto;                     // Numerotation automatique

	var $prefixcustomer='RP';
	var $prefixsupplier='Prov-';
	var $prefixIsRequired; // Le champ prefix du tiers doit etre renseigne quand on utilise {pre}


	/**
	 * 	Constructor
	 */
	function __construct()
	{
		$this->nom                      = "Secuencial";
		$this->name                     = "Secuencial";
		$this->version                  = "dolibarr";
		$this->code_null                = 1;
		$this->code_modifiable          = 1;
		$this->code_modifiable_invalide = 1;
		$this->code_modifiable_null     = 1;
		$this->code_auto                = 1;
		$this->prefixIsRequired         = 0;
	}


	/**		Return description of module
	 *
	 * 		@param	Translate	$langs	Object langs
	 * 		@return string      		Description of module
	 */
	function info($langs)
	{
		return $langs->trans("ctrl_conf_ref_def_refund");
	}


	/**
	 * Return an example of result returned by getNextValue
	 *
	 * @param	Translate	$langs		Object langs
	 * @param	societe		$objsoc		Object thirdparty
	 * @param	int			$type		Type of third party (1:customer, 2:supplier, -1:autodetect)
	 * @return	string					Return string example
	 */
	function getExample($langs,$objsoc=0,$type=-1)
	{
		$date=dol_now();
		$yymm = strftime("%y%m",$date);
		return $this->prefixcustomer.$yymm.'-0001';
	}


	/**
	 *  Return next value
	 *
	 * 	@param	Societe		$objsoc     Object third party
	 *	@param  int			$type       Client ou fournisseur (1:client, 2:fournisseur)
	 *  @return string      			Value if OK, '' if module not configured, <0 if KO
	 */
	function getNextValue()
	{
		global $db, $conf, $mc;

		$field='';
		$field = 'ref';

		$posindice=8;

		$prefix=$this->prefixcustomer;

		// D'abord on recupere la valeur max (reponse immediate car champ indexe)

        $sql = "SELECT MAX(CAST(SUBSTRING(".$field." FROM ".$posindice.") AS SIGNED)) as max";   // This is standard SQL
		$sql.= " FROM llx_ctrl_refund_credit";
		$sql.= " WHERE ".$field." LIKE '".$prefix."____%'";
		dol_syslog(get_class($this)."::getNextValue", LOG_DEBUG);
		
		
		$resql=$db->query($sql);
		if ($resql)
		{
			$obj = $db->fetch_object($resql);
			if ($obj) $max = intval($obj->max);
			else $max=0;
		}
		else
		{
			return -1;
		}

		$date	= dol_now();
		$yymm	= strftime("%y%m",$date);

		if ($max >= (pow(10, 4) - 1)) $num=$max+1;	// If counter > 9999, we do not format on 4 chars, we take number as it is
		else {
			$num = sprintf("%04s",$max+1);
		}

		dol_syslog(get_class($this)."::getNextValue return ".$prefix.$yymm."-".$num);
		return $prefix.$yymm."-".$num;
	}


	function getToolTipp($langs,$soc,$type)
    {
        global $conf;

        $langs->load("admin");
        $langs->load("ctrlanticipo@ctrlanticipo");

        $s='';
        if ($type == -1) $s.=$langs->trans("Name").': <b>'.$this->getNom($langs).'</b><br>';
        if ($type == -1) $s.=$langs->trans("Version").': <b>'.$this->getVersion().'</b><br>';
        if ($type == 0)  $s.=$langs->trans("CustomerCodeDesc").'<br>';
        if ($type == 1)  $s.=$langs->trans("SupplierCodeDesc").'<br>';
        if ($type != -1) $s.=$langs->trans("ValidityControledByModule").': <b>'.$this->getNom($langs).'</b><br>';
        $s.='<br>';
        $s.='<u>'.$langs->trans("ThisIsModuleRules").':</u><br>';
        if ($type == 0)
        {
            $s.=$langs->trans("RequiredIfCustomer").': ';
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='<strike>';
            $s.=yn(!$this->code_null,1,2);
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='</strike> '.yn(1,1,2).' ('.$langs->trans("ForcedToByAModule",$langs->transnoentities("yes")).')';
            $s.='<br>';
        }
        if ($type == 1)
        {
            $s.=$langs->trans("RequiredIfSupplier").': ';
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='<strike>';
            $s.=yn(!$this->code_null,1,2);
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='</strike> '.yn(1,1,2).' ('.$langs->trans("ForcedToByAModule",$langs->transnoentities("yes")).')';
            $s.='<br>';
        }
        if ($type == -1)
        {
            $s.=$langs->trans("Required").': ';
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='<strike>';
            $s.=yn(!$this->code_null,1,2);
            if (! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED) && ! empty($this->code_null)) $s.='</strike> '.yn(1,1,2).' ('.$langs->trans("ForcedToByAModule",$langs->transnoentities("yes")).')';
            $s.='<br>';
        }
        $s.=$langs->trans("CanBeModifiedIfOk").': ';
        $s.=yn($this->code_modifiable,1,2);
        $s.='<br>';
        $s.=$langs->trans("CanBeModifiedIfKo").': '.yn($this->code_modifiable_invalide,1,2).'<br>';
        $s.=$langs->trans("AutomaticCode").': '.yn($this->code_auto,1,2).'<br>';
        $s.='<br>';
        if ($type == 0 || $type == -1)
        {
            $nextval=$this->getNextValue($soc,0);
            if (empty($nextval)) $nextval=$langs->trans("Undefined");
            $s.=$langs->trans("NextValue").($type == -1?" ".$langs->trans("ctrl_conf_ref_refund"):'').': <b>'.$nextval.'</b><br>';
        }


        return $s;
    }



	/**
	 * 	Check validity of code according to its rules
	 *
	 *	@param	DoliDB		$db		Database handler
	 *	@param	string		$code	Code to check/correct
	 *	@param	Societe		$soc	Object third party
	 *  @param  int		  	$type   0 = customer/prospect , 1 = supplier
	 *  @return int					0 if OK
	 * 								-1 ErrorBadCustomerCodeSyntax
	 * 								-2 ErrorCustomerCodeRequired
	 * 								-3 ErrorCustomerCodeAlreadyUsed
	 * 								-4 ErrorPrefixRequired
	 */
	function verif($db, &$code, $soc, $type)
	{
		global $conf;

		$result=0;
		$code = strtoupper(trim($code));

		if (empty($code) && $this->code_null && empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED))
		{
			$result=0;
		}
		else if (empty($code) && (! $this->code_null || ! empty($conf->global->MAIN_COMPANY_CODE_ALWAYS_REQUIRED)) )
		{
			$result=-2;
		}
		else
		{
			if ($this->verif_syntax($code) >= 0)
			{
				$is_dispo = $this->verif_dispo($db, $code, $soc);
				if ($is_dispo <> 0)
				{
					$result=-3;
				}
				else
				{
					$result=0;
				}
			}
			else
			{
				if (dol_strlen($code) == 0)
				{
					$result=-2;
				}
				else
				{
					$result=-1;
				}
			}
		}

		dol_syslog(get_class($this)."::verif type=".$type." result=".$result);
		return $result;
	}


	/**
	 *		Renvoi si un code est pris ou non (par autre tiers)
	 *
	 *		@param	DoliDB		$db			Handler acces base
	 *		@param	string		$code		Code a verifier
	 *		@param	Societe		$soc		Objet societe
	 *		@return	int						0 if available, <0 if KO
	 */
	function verif_dispo($db, $code, $soc)
	{
		global $conf, $mc;

		$sql = "SELECT Ref FROM llx_consultas";
		$sql.= " WHERE Ref = '".$code."'";
		if ($soc->id > 0) $sql.= " where rowid <> ".$soc->id;

		dol_syslog(get_class($this)."::verif_dispo", LOG_DEBUG);
		$resql=$db->query($sql);
		if ($resql)
		{
			if ($db->num_rows($resql) == 0)
			{
				return 0;
			}
			else
			{
				return -1;
			}
		}
		else
		{
			return -2;
		}

	}


	/**
	 *	Renvoi si un code respecte la syntaxe
	 *
	 *	@param	string		$code		Code a verifier
	 *	@return	int						0 si OK, <0 si KO
	 */
	function verif_syntax($code)
	{
		$res = 0;

		if (dol_strlen($code) < 11)
		{
			$res = -1;
		}
		else
		{
			$res = 0;
		}
		return $res;
	}

}

