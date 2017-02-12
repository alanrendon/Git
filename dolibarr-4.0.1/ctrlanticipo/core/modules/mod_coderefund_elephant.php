<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2006-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Juanjo Menent	    <jmenent@2byte.es>
 * Copyright (C) 2013 	   Philippe Grand      	<philippe.grand@atoo-net.com>
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
 *       \file       htdocs/core/modules/societe/mod_codeclient_elephant.php
 *       \ingroup    societe
 *       \brief      File of class to manage third party code with elephant rule
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php';


/**
 *	Class to manage third party code with elephant rule
 */
class mod_coderefund_elephant extends ModeleThirdPartyCode
{
	var $nom='Personalizada';				// Nom du modele
	var $name='Personalizada';				// Nom du modele
	var $code_modifiable;				// Code modifiable
	var $code_modifiable_invalide;		// Code modifiable si il est invalide
	var $code_modifiable_null;			// Code modifiables si il est null
	var $code_null;						// Code facultatif
	var $version='dolibarr';    		// 'development', 'experimental', 'dolibarr'
	var $code_auto;                     // Numerotation automatique

	var $searchcode; // String de recherche
	var $numbitcounter; // Nombre de chiffres du compteur
	var $prefixIsRequired; // Le champ prefix du tiers doit etre renseigne quand on utilise {pre}


	/**
	 *	Constructor
	 */
	function __construct()
	{
		$this->code_null = 0;
		$this->code_modifiable = 1;
		$this->code_modifiable_invalide = 1;
		$this->code_modifiable_null = 1;
		$this->code_auto = 1;
		$this->prefixIsRequired = 0;
	}


	/**		Return description of module
	 *
	 * 		@param	Translate	$langs		Object langs
	 * 		@return string      			Description of module
	 */
	function info($langs)
	{
		global $conf, $mc;
		global $form;

		$langs->load("companies");

		$disabled = ((! empty($mc->sharings['referent']) && $mc->sharings['referent'] != $conf->entity) ? ' disabled' : '');

		$texte = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$texte.= '<input type="hidden" name="action" value="setModuleOptions">';
		$texte.= '<input type="hidden" name="param1" value="COMPANY_ELEPHANT_MASK_REFUND">';
		$texte.= '<table class="nobordernopadding" width="100%">';

		$tooltip=$langs->trans("GenericMaskCodes",$langs->transnoentities("ThirdParty"),$langs->transnoentities("ThirdParty"));
		//$tooltip.=$langs->trans("GenericMaskCodes2");	Not required for third party numbering
		$tooltip.=$langs->trans("GenericMaskCodes3");
		$tooltip.=$langs->trans("GenericMaskCodes4b");
		$tooltip.=$langs->trans("GenericMaskCodes5");

		// Parametrage du prefix customers
		$texte.= '<tr><td>'.$langs->trans("Mask").' '.$langs->trans("ctrl_conf_tit_ref_refund").':</td>';
		$texte.= '<td align="right">'.$form->textwithpicto('<input type="text" class="flat" size="24" name="value1" value="'.$conf->global->COMPANY_ELEPHANT_MASK_REFUND.'"'.$disabled.'>',$tooltip,1,1).'</td>';

		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"'.$disabled.'></td>';

		$texte.= '</tr>';

		$texte.= '</table>';
		$texte.= '</form>';

		return $texte;
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
		if ($type == 0 || $type == -1)
		{
			$examplecust = $this->getNextValue($objsoc,0);
			if (! $examplecust)
			{
				$examplecust = $langs->trans('NotConfigured');
			}
			if($examplecust=="ErrorBadMask")
			{
				$langs->load("errors");
				$examplecust=$langs->trans($examplecust);
			}
			if($examplecust=="ErrorCantUseRazIfNoYearInMask")
			{
				$langs->load("errors");
				$examplecust=$langs->trans($examplecust);
			}
			if($examplecust=="ErrorCantUseRazInStartedYearIfNoYearMonthInMask")
			{
				$langs->load("errors");
				$examplecust=$langs->trans($examplecust);
			}
		}
		if ($type == 1 || $type == -1)
		{
			$examplesup = $this->getNextValue($objsoc,1);
			if (! $examplesup)
			{
				$examplesup = $langs->trans('NotConfigured');
			}
			if($examplesup=="ErrorBadMask")
			{
				$langs->load("errors");
				$examplesup=$langs->trans($examplesup);
			}
			if($examplesup=="ErrorCantUseRazIfNoYearInMask")
			{
				$langs->load("errors");
				$examplesup=$langs->trans($examplesup);
			}
			if($examplesup=="ErrorCantUseRazInStartedYearIfNoYearMonthInMask")
			{
				$langs->load("errors");
				$examplesup=$langs->trans($examplesup);
			}
		}

		if ($type == 0) return $examplecust;
		if ($type == 1) return $examplesup;
		return $examplesup;
	}

	/**
	 * Return next value
	 *
	 * @param	Societe		$objsoc     Object third party
	 * @param  	int		    $type       Client ou fournisseur (0:customer, 1:supplier)
	 * @return 	string      			Value if OK, '' if module not configured, <0 if KO
	 */
	function getNextValue($objsoc=0,$type=-1)
	{
		global $db,$conf;

		require_once DOL_DOCUMENT_ROOT .'/core/lib/functions2.lib.php';

		// Get Mask value
		$mask = '';
		if (isset($conf->global->COMPANY_ELEPHANT_MASK_REFUND)) {
			$mask = $conf->global->COMPANY_ELEPHANT_MASK_REFUND;
		}else{
			return '';
		}
		
		if (! $mask)
		{
			$this->error='NotConssfigured';
			return '';
		}

		$field='';$where='';

		$field = 'Ref';

		$now=dol_now();

		$numFinal=get_next_value($db,$mask,'ctrl_refund_credit',$field,$where,'',$now,'next',false);

		return  $numFinal;
	}


	/**
	 *   Check if mask/numbering use prefix
	 *
	 *   @return	int			0 or 1
	 */
	function verif_prefixIsUsed()
	{
		global $conf;

		$mask = $conf->global->COMPANY_ELEPHANT_MASK_REFUND;
		if (preg_match('/\{pre\}/i',$mask)) return 1;
		return 0;
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

		require_once DOL_DOCUMENT_ROOT .'/core/lib/functions2.lib.php';

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
			// Get Mask value
			$mask = '';
			$mask = empty($conf->global->COMPANY_ELEPHANT_MASK_REFUND)?'':$conf->global->COMPANY_ELEPHANT_MASK_REFUND;
			if (! $mask)
			{
				$this->error='NotConfigured';
				return '';
			}

			$result=check_value($mask,$code);
		}

		dol_syslog("mod_codeclient_elephant::verif type=".$type." result=".$result);
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
		$sql = "SELECT ref FROM llx_ctrl_refund_credit";
		$sql.= " WHERE ref = '".$code."'";
		if ($soc->id > 0) $sql.= " AND rowid <> ".$soc->id;

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

}

