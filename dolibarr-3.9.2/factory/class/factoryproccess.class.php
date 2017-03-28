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
 * \file    root/factoryproccess.class.php
 * \ingroup root
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Factoryproccess
 *
 * Put here description of your class
 * @see CommonObject
 */
class Factoryproccess extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'factoryproccess';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'factory_proccess';

	/**
	 * @var FactoryproccessLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_propal;
	public $fk_product;
	public $fk_operator;
	public $dateStart = '';
	public $dateEnd = '';

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
		
		if (isset($this->fk_propal)) {
			 $this->fk_propal = trim($this->fk_propal);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_operator)) {
			 $this->fk_operator = trim($this->fk_operator);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'rowid,';
		$sql.= 'fk_propal,';
		$sql.= 'fk_product,';
		$sql.= 'fk_operator,';
		$sql.= 'dateStart,';
		$sql.= 'dateEnd,';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->rowid)?'NULL':$this->rowid).',';
		$sql .= ' '.(! isset($this->fk_propal)?'NULL':$this->fk_propal).',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->fk_operator)?'NULL':$this->fk_operator).',';
		$sql .= ' '.(! isset($this->dateStart) || dol_strlen($this->dateStart)==0?'NULL':"'".$this->db->idate($this->dateStart)."'").',';
		$sql .= ' '.(! isset($this->dateEnd) || dol_strlen($this->dateEnd)==0?'NULL':"'".$this->db->idate($this->dateEnd)."'");

		
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
		
		$sql .= " t.fk_propal,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_operator,";
		$sql .= " t.dateStart,";
		$sql .= " t.dateEnd";

		
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
				
				$this->fk_propal = $obj->fk_propal;
				$this->fk_product = $obj->fk_product;
				$this->fk_operator = $obj->fk_operator;
				$this->dateStart = $this->db->jdate($obj->dateStart);
				$this->dateEnd = $this->db->jdate($obj->dateEnd);

				
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
		
		$sql .= " t.fk_propal,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_operator,";
		$sql .= " t.dateStart,";
		$sql .= " t.dateEnd";

		
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
				$line = new FactoryproccessLine();

				$line->id = $obj->rowid;
				
				$line->fk_propal = $obj->fk_propal;
				$line->fk_product = $obj->fk_product;
				$line->fk_operator = $obj->fk_operator;
				$line->dateStart = $this->db->jdate($obj->dateStart);
				$line->dateEnd = $this->db->jdate($obj->dateEnd);

				

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
		
		if (isset($this->fk_propal)) {
			 $this->fk_propal = trim($this->fk_propal);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_operator)) {
			 $this->fk_operator = trim($this->fk_operator);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' rowid = '.(isset($this->rowid)?$this->rowid:"null").',';
		$sql .= ' fk_propal = '.(isset($this->fk_propal)?$this->fk_propal:"null").',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' fk_operator = '.(isset($this->fk_operator)?$this->fk_operator:"null").',';
		$sql .= ' dateStart = '.(! isset($this->dateStart) || dol_strlen($this->dateStart) != 0 ? "'".$this->db->idate($this->dateStart)."'" : 'null').',';
		$sql .= ' dateEnd = '.(! isset($this->dateEnd) || dol_strlen($this->dateEnd) != 0 ? "'".$this->db->idate($this->dateEnd)."'" : 'null');

        
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
	public function delete($idProp, $idProd)
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
			$sql .= ' WHERE fk_product=' . $idProd.' and fk_propal='.$idProp;

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
		$object = new Factoryproccess($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/root/card.php?id='.$this->id.'"';
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
	function LibStatut($statut)
	{
		switch ($statut) {
			case 1:				
				$statut= '<span class="hideonsmartphone">'."Inicializado".' </span>'.img_picto('Listo para producci&oacute;n','statut0');
				break;
			case 2:				
				$statut= '<span class="hideonsmartphone">'."Finalizado".' </span>'.img_picto('En producci&oacute;n','statut1');
				break;
			case 3:				
				$statut= '<span class="hideonsmartphone">'."Interrumpido".' </span>'.img_picto('En producci&oacute;n','statut2');
				break;
			case 4:				
				$statut= '<span class="hideonsmartphone">'."Listo para tratamiento".' </span>'.img_picto('En producci&oacute;n','statut4');
				break;
			default:
				$statut='';
				break;
		}

		return $statut;
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
		
		$this->fk_propal = '';
		$this->fk_product = '';
		$this->fk_operator = '';
		$this->dateStart = '';
		$this->dateEnd = '';

		
	}

	function get_propals(){
		$list = array();
		$sql='SELECT
				a.rowid, a.ref
			FROM
				llx_propal AS a
			WHERE
				a.fk_status_factory = 2
			AND a.fk_statut = 2;';
				//c.rowid;';
				//echo $sql.'<br/>';
		$query=$this->db->query($sql);
		$n=$this->db->num_rows($query);
		if($n>0){
			$band=0;
			while ($dat=$this->db->fetch_object($query)) {
				$list[]=$dat;
			}
		}
		return $list;
	}

	function get_propals_pen(){
		$list = array();
		$sql='SELECT
			pro.rowid,
			pro.ref
		FROM
			llx_propal as pro 
		INNER JOIN	llx_propaldet AS a on pro.rowid=a.fk_propal
		LEFT JOIN llx_factory_proccess AS b ON a.fk_propal = b.fk_propal
		AND a.fk_product = b.fk_product
		WHERE
		pro.fk_status_factory = 2
		AND pro.fk_statut >= 2
		AND b.rowid IS NULL
		GROUP BY
			pro.rowid;';
				//c.rowid;';
				//echo $sql.'<br/>';
		$query=$this->db->query($sql);
		$n=$this->db->num_rows($query);
		if($n>0){
			$band=0;
			while ($dat=$this->db->fetch_object($query)) {
				$list[]=$dat;
			}
		}
		return $list;
	}

	function addProcesos($idPropal, $idProduct, $idOperator, $fechaStart){
		$fecha = trim($fechaStart);
		$aux=str_replace('/','-',$fecha);
		$datStart=date('Y-m-d H:i',strtotime($aux));
		

		$sql='insert into llx_factory_proccess
				(fk_propal, fk_product, fk_operator, dateStart, status)
				values('.$idPropal.','.$idProduct.','.$idOperator.',"'.$datStart.'",1);	';

		$query=$this->db->query($sql);
		
	echo $sql;
	}

}

/**
 * Class FactoryproccessLine
 */
class FactoryproccessLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_propal;
	public $fk_product;
	public $fk_operator;
	public $dateStart = '';
	public $dateEnd = '';

	/**
	 * @var mixed Sample line property 2
	 */
	
}
