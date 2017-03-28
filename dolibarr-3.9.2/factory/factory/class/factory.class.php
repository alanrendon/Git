<?php
/* Copyright (C) 2014-2015	Charles-Fr BENKE		<charles.fr@benke.fr>
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
 *	\file       htdocs/factory/class/factory.class.php
 *	\ingroup    categorie
 *	\brief      File of class to factory
 */

/**
 *	Class to manage Factory
 */
class Factory extends CommonObject
{
	public $element='factory';
	public $table_element='factory';
	public $fk_element='fk_factory';
	public $table_element_line='factorydet';
	
	var $id;
	var $ref;
	var $fk_product;
	var $fk_entrepot;
	var $description;
	var $fk_statut;
	var $model_pdf;
	// -----
	var $qty_planned;
	var $date_end_planned;	
	var $date_start_planned;	
	var $duration_planned;
	// -----
	var $qty_made;
	var $date_end_made;	
	var $date_start_made;	
	var $duration_made;
	
	var $is_sousproduit_qty=0;
	var $is_sousproduit_qtyglobal=0;
	var $is_sousproduit_description="";
	
	
	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db     Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		$this->statut = 0;

		// List of language codes for status
		$this->statuts[0]='Draft';
		$this->statuts[1]='Activated';
		$this->statuts[2]='Closed';
		$this->statuts[3]='Canceled';
		$this->statuts_short[0]='Draft';
		$this->statuts_short[1]='Activated';
		$this->statuts_short[2]='Closed';
		$this->statuts_short[3]='Canceled';
	}

	
	function createof()
	{
		$this->db->begin();
		global $user, $conf, $langs;

		$obj = $conf->global->FACTORY_ADDON;
		//print "<br>:::::::".$conf->global->FACTORY_ADDON."::::::";
		//print_r($obj);
		if($conf->global->FACTORY_ADDON=='._mod_babouin'){
			require_once(DOL_DOCUMENT_ROOT."/factory/core/modules/factory/mod_babouin.php");
			$modfactory = new mod_babouin($db);
		}else{
			if($conf->global->FACTORY_ADDON=='._mod_mandrill'){
				require_once(DOL_DOCUMENT_ROOT."/factory/core/modules/factory/mod_mandrill.php");
				$modfactory = new mod_mandrill($db);
			}else{
				$modfactory = new $obj;
			}
		}
		
		$refOF = $modfactory->getNextValue();

		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'factory (ref, fk_product, fk_entrepot, description, date_start_planned, date_end_planned, duration_planned, qty_planned, fk_user_author, no_serie)';
		$sql.= ' VALUES ("'.$refOF.'", '.$this->id.', '.$this->fk_entrepot.', "'.$this->db->escape($this->description).'"';
		$sql.= ', "'.($this->date_start_planned?$this->db->idate($this->date_start_planned):'null').'", "'.($this->date_end_planned?$this->db->idate($this->date_end_planned):'null').'"';
		$sql.= ', '.($this->duration_planned?$this->duration_planned:'null').', '.$this->qty_planned.', '.$user->id.', "'.$this->db->escape($this->series).'" )';
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
		else
		{
			// get the last inserted value
			$factoryid=$this->db->last_insert_id(MAIN_DB_PREFIX."factory");

			$tmpid = $this->id;
			// on mémorise les composants utilisé pour la fabrication
			$prodsfather = $this->getFather(); //Parent Products
			$this->get_sousproduits_arbo();
			// Number of subproducts
			$prods_arbo = $this->get_arbo_each_prod();
			// something wrong in recurs, change id of object
			$this->id = $tmpid ;

			// List of subproducts
			if (count($prods_arbo) > 0)
			{	// on boucle sur les composants	pour créer les lignes de détail
				foreach($prods_arbo as $value)
					$this->createof_component ($factoryid, $this->qty_planned, $value, 0 );
			}
		}
		
		$this->db->commit();
		return $factoryid;
	}
	
	function createof_component($fk_factory, $qty_build, $valuearray, $fk_mouvementstock=0 )
	{
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'factorydet (fk_factory, fk_product, qty_unit, qty_planned, pmp, price,';
		$sql .= ' fk_mvtstockplanned, globalqty, description)';
		// pour gérer les quantités
		if ($valuearray['globalqty'] == 0)
			$qty_planned=$qty_build * $valuearray['nb'];
		else
			$qty_planned=$valuearray['nb'];
		$sql .= ' VALUES ('.$fk_factory.', '.$valuearray['id'] .', '.$valuearray['nb'].', '.$qty_planned.', '.$valuearray['pmp'].', '.$valuearray['price'];
		$sql .= ', '.$fk_mouvementstock.', '.$valuearray['globalqty'].',"'.$this->db->escape($valuearray['description']).'" )';
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
		else
		{
			return 1;
		}
	}
	
	function fetch($rowid, $ref='')
	{
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."factory as f";
		if ($ref) $sql.= " WHERE f.ref='".$this->db->escape($ref)."'";
		else $sql.= " WHERE f.rowid=".$rowid;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id				= $obj->rowid;
				$this->ref				= $obj->ref;
				$this->description  	= $obj->description;
				$this->qty_planned		= $obj->qty_planned;
				$this->qty_made			= $obj->qty_made;
				$this->date_start_planned = $this->db->jdate($obj->date_start_planned);
				$this->date_start_made 	= $this->db->jdate($obj->date_start_made);
				$this->date_end_planned	= $this->db->jdate($obj->date_end_planned);
				$this->date_end_made	= $this->db->jdate($obj->date_end_made);
				$this->duration_planned	= $obj->duration_planned;
				$this->duration_made	= $obj->duration_made;
				$this->fk_product		= $obj->fk_product;
				$this->fk_entrepot		= $obj->fk_entrepot;
				$this->note_public		= $obj->note_public;
				$this->note_private		= $obj->note_private;
				$this->model_pdf		= $obj->model_pdf;
				$this->fk_statut		= $obj->fk_statut;
				$this->fk_statut_entrpot = $obj->fk_statut_entrpot;
				$this->no_serie 		= $obj->no_serie;

				$this->extraparams	= (array) json_decode($obj->extraparams, true);

				$this->db->free($resql);
				return 1;
			}
		}
		else
		{
			$this->error=$this->db->error();
			dol_syslog(get_class($this)."::fetch ".$this->error,LOG_ERR);
			return -1;
		}
	}	
	
	/**
	 * 	Information sur l'objet fiche intervention
	 *
	 *	@param	int		$id      Id de la fiche d'intervention
	 *	@return	void
	 */
	function info($id)
	{
		global $conf;

		$sql = "SELECT f.rowid,";
		$sql.= " date_start_planned,";
		$sql.= " date_start_made,";
		$sql.= " date_end_planned,";
		$sql.= " date_end_made,";
		$sql.= " fk_user_author,";
		$sql.= " fk_user_valid,";
		$sql.= " fk_user_close";
		$sql.= " FROM ".MAIN_DB_PREFIX."factory as f";
		$sql.= " WHERE f.rowid = ".$id;

		$result = $this->db->query($sql);

		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);

				$this->id	= $obj->rowid;
				
				$this->date_creation	= $this->db->jdate($obj->date_start_planned);

				if ($obj->date_start_made)
					$this->date_creation	= $this->db->jdate($obj->date_start_made);
					
				if ($obj->date_end_made)
					$this->date_cloture	= $this->db->jdate($obj->date_end_made);
				

				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation	= $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation	= $vuser;
				}

				if ($obj->fk_user_close)
				{
					$euser = new User($this->db);
					$euser->fetch($obj->fk_user_close);
					$this->user_cloture	= $euser;
				}
			}
			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}
	
	/**
	 *  Lie un produit associe au produit/service
	 *
	 *  @param      int		$id_pere    	Id du produit auquel sera lie le produit a lier
	 *  @param      int		$id_fils    	Id du produit a lier
	 *  @param		int		$qty			Quantity
	 *  @param		double	$pmp			buy price
	 *  @param		double	$price			sell price
	 *  @param		int		$qtyglobal		Quantity is a global value
	 *  @param		string	$description	descrption
	 *  @return     int        				< 0 if KO, > 0 if OK
	 */
	function add_component($fk_parent, $fk_child, $qty, $pmp=0, $price=0, $qtyglobal=0, $description='',$treatment=0)	
	{

		echo " tre ".$treatment;

		$sql = 'DELETE from '.MAIN_DB_PREFIX.'product_factory';
		$sql .= ' WHERE fk_product_father  = "'.$fk_parent.'" AND fk_product_children = "'.$fk_child.'"';
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
		else
		{
			echo " trate ".$treatment;
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_factory(fk_product_father, fk_product_children,';
			$sql .= 'qty, pmp, price, globalqty, description, treatment)';
			$sql .= ' VALUES ('.$fk_parent.', '.$fk_child.', '.price2num($qty).', '.price2num($pmp).', '.price2num($price);
			$sql .= ', '.($qtyglobal?$qtyglobal:'0').', "'.$this->db->escape($description).'",'.$treatment.'';
			$sql .= ' )';

			echo " ".$sql;
			if (! $this->db->query($sql))
			{
				dol_print_error($this->db);
				return -1;
			}
			else
			{
				return 1;
			}
		}
	}

	/**
	 *  Lie un produit associe au produit/service
	 *
	 *  @param      int		$id_pere    	Id du produit auquel sera lie le produit a lier
	 *  @param      int		$id_fils    	Id du produit a lier
	 *  @param		int		$qty			Quantity
	 *  @param		double	$pmp			buy price
	 *  @param		double	$price			sell price
	 *  @param		int		$qtyglobal		Quantity is a global value
	 *  @param		string	$description	descrption
	 *  @return     int        				< 0 if KO, > 0 if OK
	 */
	function add_componentOF($fk_factory, $fk_product, $qty, $pmp=0, $price=0, $qtyglobal=0, $description='')
	{
		$sql = 'DELETE from '.MAIN_DB_PREFIX.'factorydet';
		$sql .= ' WHERE fk_factory = '.$fk_factory.' AND fk_product= '.$fk_product;
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
		else
		{
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'factorydet(fk_factory, fk_product,';
			$sql .= 'qty_unit, qty_planned, pmp, price, globalqty, description)';
			$sql .= ' VALUES ('.$fk_factory.', '.$fk_product.', '.price2num($qty);
			if ($qtyglobal ==1)
				$sql .= ', '.price2num($qty);
			else
				$sql .= ', '.price2num($qty * $this->qty_planned) ;
			$sql .= ', '.price2num($pmp).', '.price2num($price);
			$sql .= ', '.($qtyglobal?$qtyglobal:'0').', "'.$this->db->escape($description).'"';
			$sql .= ' )';
			if (! $this->db->query($sql))
			{
				dol_print_error($this->db);
				return -1;
			}
			else
			{
				return 1;
			}
		}
	}

	/**
	 *  Lie un produit associe à une tache
	 *
	 *  @param      int	$id_pere    Id du produit auquel sera lie le produit a lier
	 *  @param      int	$id_fils    Id du produit a lier
	 *  @param		int	$qty		Quantity
	 *  @param		double	$pmp	buy price
	 *  @param		double	$price	sell price
	 *  @return     int        		< 0 if KO, > 0 if OK
	 */
	function add_componenttask($fk_task, $fk_product, $qty_planned, $pmp=0, $price=0)
	{
		// dans le doute on supprime toujours la ligne
		$sql = 'DELETE from '.MAIN_DB_PREFIX.'projet_taskdet';
		$sql .= ' WHERE fk_task = '.$fk_task.' AND fk_product = '.$fk_product;
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
		else
		{
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'projet_taskdet(fk_task, fk_product, qty_planned, pmp, price)';
			$sql .= ' VALUES ('.$fk_task.', '.$fk_product.', '.price2num($qty_planned).', '.price2num($pmp).', '.price2num($price).' )';
			if (! $this->db->query($sql))
			{
				dol_print_error($this->db);
				return -1;
			}
			else
			{
				return 1;
			}
		}
	}

	/**
	 *  Verifie si c'est un sous-produit
	 *
	 *  @param      int	$fk_parent		Id du produit auquel le produit est lie
	 *  @param      int	$fk_child		Id du produit lie
	 *  @param      int	$basetable		Id du produit lie
	 
	 *  @return     int			    	< 0 si erreur, > 0 si ok
	 */
	function is_sousproduit($fk_parent, $fk_child)
	{
		$sql = "SELECT qty, globalqty, description";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_factory";
		$sql.= " WHERE fk_product_father  = '".$fk_parent."'";
		$sql.= " AND fk_product_children = '".$fk_child."'";

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			if($num > 0)
			{
				$obj = $this->db->fetch_object($result);
				$this->is_sousproduit_qty = $obj->qty;
				$this->is_sousproduit_qtyglobal = $obj->globalqty;
				$this->is_sousproduit_description = $obj->description;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Verifie si c'est un sous-produit
	 *
	 *  @param      int	$fk_parent		Id du produit auquel le produit est lie
	 *  @param      int	$fk_child		Id du produit lie
	 *  @param      int	$basetable		Id du produit lie
	 
	 *  @return     int			    	< 0 si erreur, > 0 si ok
	 */
	function is_sousproduitOF($fk_factory, $fk_child)
	{
		$sql = "SELECT qty_unit, globalqty, description";
		$sql.= " FROM ".MAIN_DB_PREFIX."factorydet";
		$sql.= " WHERE fk_factory = ".$fk_factory;
		$sql.= " AND fk_product = ".$fk_child;

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			if($num > 0)
			{
				$obj = $this->db->fetch_object($result);
				$this->is_sousproduit_qty = $obj->qty_unit;
				$this->is_sousproduit_qtyglobal = $obj->globalqty;
				$this->is_sousproduit_description = $obj->description;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}



	/**
     *  Initialise an instance with random values.
     *  Used to build previews or test instances.
     *	id must be 0 if object instance is a specimen.
     *
     *  @return	void
	 */
	function initAsSpecimen()
	{
		global $user, $langs, $conf;

		$now=dol_now();

		// Initialise parametres
		$this->id=0;
		$this->ref = 'SPECIMEN';
		$this->specimen=1;
		$this->socid = 1;
		$this->date = $now;
		$this->note_public='SPECIMEN';
		$this->duree = 0;
		$nbp = 5;
		$xnbp = 0;
		while ($xnbp < $nbp)
		{

			$this->lines[$xnbp]=$line;
			$xnbp++;

			$this->duree+=$line->duration;
		}
	}


	/**
	 *  Retire le lien entre un sousproduit et un produit/service
	 *
	 *  @param      int	$fk_parent		Id du produit auquel ne sera plus lie le produit lie
	 *  @param      int	$fk_child		Id du produit a ne plus lie
	 *  @return     int			    	< 0 si erreur, > 0 si ok
	 */
	function del_component($fk_parent, $fk_child)
	{
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_factory";
		$sql.= " WHERE fk_product_father  = '".$fk_parent."'";
		$sql.= " AND fk_product_children = '".$fk_child."'";

		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}

		return 1;
	}
	
		/**
	 *  Retire le lien entre un sousproduit et un produit/service
	 *
	 *  @param      int	$fk_parent		Id du produit auquel ne sera plus lie le produit lie
	 *  @param      int	$fk_child		Id du produit a ne plus lie
	 *  @return     int			    	< 0 si erreur, > 0 si ok
	 */
	function del_componentOF($fk_factory, $fk_product)
	{
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."factorydet";
		$sql.= " WHERE fk_factory = ".$fk_factory;
		$sql.= " AND fk_product = ".$fk_product;

		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}

		return 1;
	}

	/**
	 *  Retire le lien entre un sousproduit et un produit/service
	 *
	 *  @param      int	$fk_parent		Id du produit auquel ne sera plus lie le produit lie
	 *  @param      int	$fk_child		Id du produit a ne plus lie
	 *  @return     int			    	< 0 si erreur, > 0 si ok
	 */
	function del_componenttask($fk_task, $fk_product)
	{
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."projet_taskdet";
		$sql.= " WHERE fk_task  = ".$fk_task;
		$sql.= " AND fk_product = ".$fk_product;

		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}

		return 1;
	}


	/**
	 *	Return number of product buildable in entrepot 
	  *
	 * 	@param	int		$entrepotid		id of the entrepot
	 * 	@param	int		$productid		id of the product to build
	 *  @return	int						number of product buildable
	 */
	function getNbProductBuildable($entrepotid, $productid)
	{
		$this->id=$productid;
		//$this->fetch($productid);
		
		$fabricable=0;
		$this->get_sousproduits_arbo();
		$prods_arbo = $this->get_arbo_each_prod();
		if (count($prods_arbo) > 0)
		{
			$fabricable=-1;
			foreach($prods_arbo as $value)
			{
				$productstatic = new Product($this->db);
				$productstatic->id=$value['id'];
				$productstatic->fetch($value['id']);
				if ($value['type']==0)
				{
					$productstatic->load_stock();
					// for the first loop, buildable is the stock divide by number need
					if ($fabricable==-1)
					{
						$fabricable=$productstatic->stock_warehouse[$entrepotid]->real/$value['nb'];
					}
					else
					{
						// other loop, buildable changed only if the number is smaller
						if ($fabricable >= $productstatic->stock_reel/$value['nb'])
							$fabricable=$productstatic->stock_warehouse[$entrepotid]->real/$value['nb'];
					}
				}
			}
		}
		// attention buildable product are always an integer
		return (int) $fabricable;
	}
	
	function get_nb_ProductInTask($taskid, $productid)
	{
		$sql = "SELECT qty_planned as qtyplanned";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_taskdet as ptd";
		$sql.= " WHERE ptd.fk_task = ".$taskid;
		$sql.= " AND ptd.fk_product=".$productid;

		$res = $this->db->query($sql);
		if ($res)
		{
			//$objp = $this->db->fetch_array($res);
			$objp = $this->db->fetch_object($res);
			return $objp->qtyplanned;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	function getQtyFromStock($projectid, $productid)
	{

		$sql = "SELECT sum(qty_from_stock) as nbinproject ";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_stock as ps";
		$sql.= " WHERE ps.fk_project = ".$projectid;
		$sql.= " AND ps.fk_product= ".$productid;
		$res = $this->db->query($sql);
		
		if ($res)
		{
			$obj = $this->db->fetch_object($res);
			return $obj->nbinproject;
		}
		return 0;
	}
	
	function get_value_ProductInTask($taskid, $productid, $valuetype, $defaultvalue=0)
	{
		$sql = "SELECT pmp, price FROM ".MAIN_DB_PREFIX."projet_taskdet as ptd";
		$sql.= " WHERE ptd.fk_task = ".$taskid;
		$sql.= " AND ptd.fk_product=".$productid;
		$res = $this->db->query($sql);
		if ($res)
		{
			//$objp = $this->db->fetch_array($res);
			$objp = $this->db->fetch_object($res);
			if ($valuetype=='pmp')
				return ($objp->pmp ? $objp->pmp : $defaultvalue);
			else
				return ($objp->price ? $objp->price : $defaultvalue);
		}
		return 0;
	}
	
	/**
	 *  Fonction recursive uniquement utilisee par get_arbo_each_prod, recompose l'arborescence des sousproduits
	 * 	Define value of this->res
	 *
	 *	@param		array	$prod			Products array
	 *	@param		string	$compl_path		Directory path
	 *	@param		int		$multiply		Because each sublevel must be multiplicated by parent nb
	 *	@param		int		$level			Init level
	 *  @return 	void
	 */
	function fetch_prod_arbo($prod, $compl_path="", $multiply=1, $level=1)
	{
		global $conf,$langs;

		foreach($prod as $nom_pere => $desc_pere)
		{

			$product = new Product($this->db);

			if (is_array($desc_pere))	// If this parent desc is an array, this is an array of childs
			{
				//var_dump($desc_pere);
				$id=(! empty($desc_pere[0]) ? $desc_pere[0] :'');
				$nb=(! empty($desc_pere[1]) ? $desc_pere[1] :'0');
				$type=(! empty($desc_pere[2]) ? $desc_pere[2] :'');
				$label=(! empty($desc_pere[3]) ? $desc_pere[3] :'');
				$pmp=(! empty($desc_pere[4]) ? $desc_pere[4] :'0');
				$price=(! empty($desc_pere[5]) ? $desc_pere[5] :'0');
				$globalqty=(! empty($desc_pere[6]) ? $desc_pere[6] :'0');
				$description=(! empty($desc_pere[7]) ? $desc_pere[7] :'');

				if ($multiply)
				{
					//print "XXX ".$desc_pere[1]." nb=".$nb." multiply=".$multiply."<br>";
					$img="";
					$product->fetch($id);
					$product->load_stock();
					if ($product->stock_warehouse[1]->real < $this->seuil_stock_alerte)
					{
						$img=img_warning($langs->trans("StockTooLow"));
					}
					// si en quantité global on ne gère pas de la même façon les quantités
					if ($globalqty == 0)
						$nb*$multiply;
					else
						$nb;
					$this->res[]= array(
							'id'=>$id,									// Id product
							'label'=>$label,							// label product
							'pmp'=>$pmp,								// pmp of the product
							'price'=>$price,							// price of the product
							'nb'=>$nb,									// Nb of units that compose parent product
							'nb_total'=>$nb*$multiply,					// Nb of units for all nb of product
							'stock'=>$this->stock_warehouse[1]->real,	// Stock
							'stock_alert'=>$this->seuil_stock_alerte,	// Stock alert
							'fullpath' => $compl_path.$nom_pere,		// Label
							'type'=>$type,								// Nb of units that compose parent product
							'globalqty'=>$globalqty,					// Nb of units that compose parent product
							'description'=>$description					// description additionnel sur l'of
					);
				}
				else
				{
					$product->fetch($desc_pere[0]);
					$product->load_stock();
					$$this->res[]= array(
							'id'=>$id,									// Id product
							'label'=>$label,							// Id product
							'pmp'=>$pmp,								// Nb of units that compose parent product
							'price'=>$price,							// Nb of units that compose parent product
							'nb'=>$nb,									// Nb of units that compose parent product
							'nb_total'=>$nb,							// Nb of units for all nb of product
							'stock'=>$this->stock_warehouse[1]->real,	// Stock
							'stock_alert'=>$this->seuil_stock_alerte,	// Stock alert
							'fullpath' => $compl_path.$nom_pere,		// Label
							'type'=>$type,								// Nb of units that compose parent product
							'globalqty'=>$globalqty,					// Nb of units that compose parent product
							'description'=>$description					// description additionnel sur l'of
					);
				}
			}
			else if($nom_pere != "0" && $nom_pere != "1")
			{
				$this->product[]= array($compl_path.$nom_pere,$desc_pere);
			}
		}
	}

	/**
	 *  fonction recursive uniquement utilisee par get_each_prod, ajoute chaque sousproduits dans le tableau res
	 *
	 *	@param	array	$prod	Products array
	 *  @return void
	 */
	function fetch_prods($prod)
	{
		$this->res = array();
		foreach($prod as $nom_pere => $desc_pere)
		{
			// on est dans une sous-categorie
			if(is_array($desc_pere))
			$this->res[]= array($desc_pere[1],$desc_pere[0]);
			if(count($desc_pere) >1)
			{
				$this->fetch_prods($desc_pere);
			}
		}
	}

	/**
	 *  reconstruit l'arborescence des composants sous la forme d'un tableau
	 *
	 *	@param		int		$multiply		Because each sublevel must be multiplicated by parent nb
	 *  @return 	array 					$this->res
	 */
	function get_arbo_each_prod($multiply=1)
	{
		$this->res = array();
		if (isset($this->sousprods) && is_array($this->sousprods))
		{
			foreach($this->sousprods as $nom_pere => $desc_pere)
			{
				if (is_array($desc_pere)) $this->fetch_prod_arbo($desc_pere,"",$multiply);
			}
		}
		return $this->res;
	}

	/**
	 *  Renvoie tous les sousproduits dans le tableau res, chaque ligne de res contient : id -> qty
	 *
	 *  @return array $this->res
	 */
	function get_each_prod()
	{
		$this->res = array();
		if(is_array($this->sousprods))
		{
			foreach($this->sousprods as $nom_pere => $desc_pere)
				if(count($desc_pere) >1)
					$this->fetch_prods($desc_pere);
			sort($this->res);
		}
		return $this->res;
	}


	/**
	 *  Return all Father products fo current product
	 *
	 *  @return 	array prod
	 */
	function getFather()
	{
		$sql = "SELECT p.label as label, p.rowid, pf.fk_product_father as id, p.fk_product_type";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_factory as pf,";
		$sql.= " ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE p.rowid = pf.fk_product_father";
		$sql.= " AND pf.fk_product_children=".$this->id;

		$res = $this->db->query($sql);
		if ($res)
		{
			$prods = array ();
			while ($record = $this->db->fetch_array($res))
			{
				$prods[$record['id']]['id'] =  $record['rowid'];
				$prods[$record['id']]['label'] =  $this->db->escape($record['label']);
				$prods[$record['id']]['fk_product_type'] =  $record['fk_product_type'];
			}
			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


	/**
	 *  Return all parent products fo current product
	 *
	 *  @return 	array prod
	 */
	function getParent()
	{
		$sql = "SELECT p.label as label, p.rowid, pf.fk_product_father as id, p.fk_product_type";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_factory as pf,";
		$sql.= " ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE p.rowid = pf.fk_product_father";
		$sql.= " AND p.rowid = ".$this->id;

		$res = $this->db->query($sql);
		if ($res)
		{
			$prods = array ();
			while ($record = $this->db->fetch_array($res))
				$prods[$this->db->escape($record['label'])] = array(0=>$record['id']);
			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return all parent products fo current product
	 *
	 *  @return 	array prod
	 */
	function getComponentOF($factoryid)
	{
		$sql = "SELECT p.rowid, p.label as label, fd.qty_planned as qty, fd.pmp as pmp, fd.price as price";
		$sql.= " FROM ".MAIN_DB_PREFIX."factorydet as fd,";
		$sql.= " ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE fd.fk_product = p.rowid";
		$sql.= " AND fd.fk_factory = ".$factoryid;
		$res = $this->db->query($sql);
		if ($res)
		{
			$prods = array ();
			while ($record = $this->db->fetch_array($res))
				$prods[$this->db->escape($record['label'])] = array(0=>$record['id']);
			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *	Returns the label status
	 *
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$noentities 0=use classic translation, 1=use noentities translation (for pdf print)
	 *	@return     string      		Label
	 */
	function getLibStatut($mode=0,$noentities=0)
	{
		return $this->LibStatut($this->fk_statut,$mode,$noentities);
	}
	

	/**
	 *	Returns the label of a statut
	 *
	 *	@param      int		$statut     id statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@return     string      		Label
	 */
	 
	function LibStatut($statut,$mode=0, $noentities=0)
	{
		global $langs;

		if ($mode == 0)
		{
			if ($noentities == 0)
				return $langs->trans($this->statuts[$statut]);
			else
				return $langs->transnoentities($this->statuts[$statut]);
		}
		if ($mode == 1)
		{
			return $langs->trans($this->statuts_short[$statut]);
		}
		if ($mode == 2)
		{
			if ($statut==0) return img_picto($langs->trans($this->statuts_short[$statut]),'statut0').' '.$langs->trans($this->statuts_short[$statut]);
			if ($statut==1) return img_picto($langs->trans($this->statuts_short[$statut]),'statut4').' '.$langs->trans($this->statuts_short[$statut]);
			if ($statut==2) return img_picto($langs->trans($this->statuts_short[$statut]),'statut6').' '.$langs->trans($this->statuts_short[$statut]);
			if ($statut==3) return img_picto($langs->trans($this->statuts_short[$statut]),'statut5').' '.$langs->trans($this->statuts_short[$statut]);
		}
		if ($mode == 3)
		{
			if ($statut==0) return img_picto($langs->trans($this->statuts_short[$statut]),'statut0');
			if ($statut==1) return img_picto($langs->trans($this->statuts_short[$statut]),'statut4');
			if ($statut==2) return img_picto($langs->trans($this->statuts_short[$statut]),'statut6');
			if ($statut==3) return img_picto($langs->trans($this->statuts_short[$statut]),'statut5');
		}
		if ($mode == 4)
		{
			if ($statut==0) return img_picto($langs->trans($this->statuts_short[$statut]),'statut0').' '.$langs->trans($this->statuts[$statut]);
			if ($statut==1) return img_picto($langs->trans($this->statuts_short[$statut]),'statut4').' '.$langs->trans($this->statuts[$statut]);
			if ($statut==2) return img_picto($langs->trans($this->statuts_short[$statut]),'statut6').' '.$langs->trans($this->statuts_short[$statut]);
			if ($statut==3) return img_picto($langs->trans($this->statuts_short[$statut]),'statut5').' '.$langs->trans($this->statuts_short[$statut]);
		}
		if ($mode == 5)
		{
			if ($statut==0) return $langs->trans($this->statuts_short[$statut]).' '.img_picto($langs->trans($this->statuts_short[$statut]),'statut0');
			if ($statut==1) return $langs->trans($this->statuts_short[$statut]).' '.img_picto($langs->trans($this->statuts_short[$statut]),'statut4');
			if ($statut==2) return $langs->trans($this->statuts_short[$statut]).' '.img_picto($langs->trans($this->statuts_short[$statut]),'statut6');
			if ($statut==3) return $langs->trans($this->statuts_short[$statut]).' '.img_picto($langs->trans($this->statuts_short[$statut]),'statut5');
		}
	}

	/**
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param		int			$withpicto		0=_No picto, 1=Includes the picto in the linkn, 2=Picto only
	 *	@return		string						String with URL
	 */
	function getNomUrl($withpicto=0)
	{
		global $langs;

		$result='';

		$lien = '<a href="'.dol_buildpath('/factory/fiche.php?id='.$this->id,1).'">';
		$lienfin='</a>';

		$picto='factory@factory';

		$label=$langs->trans("Show").': '.$this->ref;

		if ($withpicto) $result.=($lien.img_object($label,$picto).$lienfin);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$lien.$this->ref.$lienfin;
		return $result;
	}

	/**
	 *	Return clicable link of object (with eventually picto)
	 *
	 *	@param		int		$withpicto		Add picto into link
	 *	@param		string	$option			Where point the link
	 *	@param		int		$maxlength		Maxlength of ref
	 *	@return		string					String with URL
	 */
	function getNomUrlFactory($id, $withpicto=0, $option='',$maxlength=0)
	{
		global $langs;
		global $conf;

		$tmpproduct = new Product($this->db);
		$result='';
		$tmpproduct->fetch($id);

		if ($option == 'index')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/factory/product/index.php?id='.$id.'">';
			$lienfin='</a>';
		}
		else if ($option == 'fiche')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/factory/product/fiche.php?id='.$id.'">';
			$lienfin='</a>';
		}
		else if ($option == 'direct')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/factory/product/direct.php?id='.$id.'">';
			$lienfin='</a>';
		}
		else
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/product/fiche.php?id='.$id.'">';
			$lienfin='</a>';
		}
		$newref=$tmpproduct->ref;
		if ($maxlength) $newref=dol_trunc($newref,$maxlength,'middle');

		if ($withpicto ) {
			if ($tmpproduct->type == 0) $result.=($lien.img_object($langs->trans("ShowProduct").' '.$tmpproduct->ref,'product').$lienfin.' ');
			if ($tmpproduct->type == 1) $result.=($lien.img_object($langs->trans("ShowService").' '.$tmpproduct->ref,'service').$lienfin.' ');
		}
		$result.=$lien.$newref.$lienfin;
		return $result;
	}

	/**
	 *	Return clicable link of object (with eventually picto)
	 *
	 *	@param		int		$withpicto		Add picto into link
	 *	@param		string	$option			Where point the link
	 *	@return		string					String with URL
	 */
	function PopupProduct($id, $idsecond="")
	{
		global $conf;

		$tmpproduct = new Product($this->db);
		$result='';
		$tmpproduct->fetch($id);

		if ($tmpproduct->is_photo_available($conf->product->multidir_output [$tmpproduct->entity])) {
			// pour gérer le cas d'une même photo sur un meme document
			if ($idsecond)
				$id.="-".$idsecond;
			$result='<a id="trigger'.$id.'" >'.img_down().'</a>';
			$result.='<div id="pop-up'.$id.'" style="display: none;  position: absolute;   padding: 2px;  background: #eeeeee;  color: #000000;  border: 1px solid #1a1a1a;" >';
			
			$result.=$tmpproduct->show_photos($conf->product->multidir_output [$tmpproduct->entity], 1, 1, 0, 0, 0, 80);
			$result.='</div>';
			$result.='<script>$(function() {';
  			$result.="$('a#trigger".$id."').hover(function() {";
    		$result.="$('div#pop-up".$id."').show();";
  			$result.="},";
  			$result.="function() {";
    		$result.="$('div#pop-up".$id."').hide();";
  			$result.="});   });"; 			
  			$result.='</script>';
  			
		}
		
      	
		return $result;
	}


	/**
	 *	Return clicable link of object (with eventually picto)
	 *
	 *	@param		int		$withpicto		Add picto into link
	 *	@param		string	$option			Where point the link
	 *	@param		int		$maxlength		Maxlength of ref
	 *	@return		string					String with URL
	 */
	function getUrlStock($id, $withpicto=0, $nbStock=0)
	{
		global $langs;

		$tmpproduct = new Product($this->db);
		$result='';
		$tmpproduct->fetch($id);

		$lien = '<a href="'.DOL_URL_ROOT.'/product/stock/product.php?id='.$id.'">';
		$lienfin='</a>';

		$result.=$lien.$nbStock.$lienfin;
		return $result;
	}


	/**
	 *  Return childs of product with if fk_parent
	 *
	 * 	@param		int		$fk_parent	Id of product to search childs of
	 *  @return     array       		Prod
	 */
	function getChildsArbo($fk_parent)
	{
		$sql = "SELECT p.rowid, p.label as label, p.fk_product_type,";
		$sql.= " pf.qty as qty, pf.pmp as pmp, pf.price as price, pf.fk_product_children as id,";
		$sql.= " pf.globalqty as globalqty, pf.description as description ";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= ", ".MAIN_DB_PREFIX."product_factory as pf";
		$sql.= " WHERE p.rowid = pf.fk_product_children";
		$sql.= " AND pf.fk_product_father = ".$fk_parent;

		$res  = $this->db->query($sql);
		if ($res)
		{
			$prods = array();
			while ($rec = $this->db->fetch_array($res))
			{
				$prods[$rec['rowid']]= array(0=>$rec['id'],
											1=>$rec['qty'],
											2=>$rec['fk_product_type'],
											3=>$this->db->escape($rec['label']),
											4=>$rec['pmp'],
											5=>$rec['price'],
											6=>$rec['globalqty'],
											7=>$rec['description'],
											8=>array()			// pour stocker les enfants sans fiche le basard
										);
				$listofchilds=$this->getChildsArbo($rec['id']);
				foreach($listofchilds as $keyChild => $valueChild)
					$prods[$rec['rowid']][8] = $valueChild;  // on stock les enfants dans le 6e tableau
			}

			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	function getChildsOF($fk_factory)
	{
		$sql = "SELECT fd.fk_product as id, p.label as label, fd.qty_unit as qtyunit, fd.qty_planned as qtyplanned,";
		$sql.= " fd.qty_used as qtyused, fd.qty_deleted as qtydeleted, fd.globalqty, fd.description,";
		$sql.= " fd.fk_mvtstockplanned as mvtstockplanned, fd.fk_mvtstockused as mvtstockused,";
		$sql.= " fd.pmp as pmp, fd.price as price, p.ref, p.fk_product_type";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= ", ".MAIN_DB_PREFIX."factorydet as fd";
		$sql.= " WHERE p.rowid = fd.fk_product";
		$sql.= " AND fd.fk_factory = ".$fk_factory;

		$res  = $this->db->query($sql);
		if ($res)
		{
			$prods = array();
			while ($rec = $this->db->fetch_array($res))
			{
					$prods[]= array(
							'id'=>$rec['id'],					// Id product
							'refproduct'=>$rec['ref'],			// label product
							'label'=>$rec['label'],				// label product
							'pmp'=>$rec['pmp'],					// pmp of the product
							'price'=>$rec['price'],				// price of the product
							'price'=>$rec['price'],				// price of the product
							'nb'=>$rec['qtyunit'],				// Nb of units that compose parent product
							'globalqty'=>$rec['globalqty'],		// 
							'description'=>$rec['description'],	// 
							'qtyused'=>$rec['qtyused'],			// Nb of units that compose parent product
							'qtydeleted'=>$rec['qtydeleted'],	// Nb of units that compose parent product
							'qtyplanned'=>$rec['qtyplanned'],	// Nb of units that compose parent product
							'mvtstockplanned'=>$rec['mvtstockplanned'],	// Nb of units that compose parent product
							'mvtstockused'=>$rec['mvtstockused'],		// Nb of units that compose parent product
							'type'=>$rec['fk_product_type']		// Nb of units that compose parent product
					);
			}

			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


	function getChildsTasks($fk_project, $fk_task)
	{
		$sql = "SELECT ptd.fk_product as id, p.label as label, p.fk_product_type, ";
		$sql.= " pt.rowid as idtask, pt.ref as reftask, p.ref as refproduct, ";
		$sql.= " ptd.qty_planned as qtyplanned, ptd.qty_used as qtyused, ";
		$sql.= " ptd.qty_deleted as qtydeleted, ptd.pmp as pmp, ptd.price as price";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= ", ".MAIN_DB_PREFIX."projet_taskdet as ptd";
		$sql.= ", ".MAIN_DB_PREFIX."projet_task as pt";
		$sql.= " WHERE p.rowid = ptd.fk_product";
		$sql.= " AND pt.rowid = ptd.fk_task";
		if ($fk_task > 0)
			$sql.= " AND ptd.fk_task = ".$fk_task;
		else
			$sql.= " AND pt.fk_projet = ".$fk_project;
		$sql.= " ORDER BY p.ref, pt.ref";

		$res  = $this->db->query($sql);
		if ($res)
		{
			$prods = array();
			while ($rec = $this->db->fetch_array($res))
			{
				$prods[]= array(
					'id'=>$rec['id'],					// Id product
					'refproduct'=>$rec['refproduct'],	// ref of  product
					'label'=>$rec['label'],				// label of product
					'idtask'=>$rec['idtask'],			// ref of task
					'reftask'=>$rec['reftask'],			// ref of task
					'pmp'=>$rec['pmp'],					// pmp of the product
					'price'=>$rec['price'],				// price of the product
					'nb'=>1,							// Nb of units that compose parent product
					'qtyplanned'=>$rec['qtyplanned'],	// Nb of units planned to use on build
					'qtyused'=>$rec['qtyused'],			// Nb of units realy used on build
					'qtydeleted'=>$rec['qtydeleted'],	// Nb of units deleted during ther build
					'type'=>$rec['fk_product_type']		// type of product (materiel or service)
				);
			}

			return $prods;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return childs of prodcut with if fk_parent
	 *
	 * 	@param		int		$fk_parent	Id of product to search childs of
	 *  @return     array       		Prod
	 */
	function clonefromvirtual()
	{
		$sql = "SELECT fk_product_fils, qty";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_association as pa";
		$sql.= " WHERE pa.fk_product_pere = ".$this->id;

		$res  = $this->db->query($sql);
		if ($res)
		{
			while ($rec = $this->db->fetch_array($res))
			{
				$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_factory (fk_product_father, fk_product_children, qty)';
				$sql .= ' VALUES ('.$this->id.','.$rec['fk_product_fils'].','.$rec['qty'].')';
				if (! $this->db->query($sql))
				{
					dol_print_error($this->db);
					return -1;
				}
			}
			
			// à la fin du transfert on supprime le paramétrage du produit virtuel
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_association as pa";
			$sql.= " WHERE pa.fk_product_pere = ".$this->id;
			$res  = $this->db->query($sql);

			
			return 0;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return childs of prodcut with if fk_parent
	 *
	 * 	@param		void
	 *  @return     array       		Prod
	 */
	function getdefaultprice()
	{
		$sql = "SELECT p.rowid, p.pmp, p.price";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_factory as pf, ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE pf.fk_product_children = p.rowid";
		$sql.= " and pf.fk_product_father = ".$this->id;

		$res  = $this->db->query($sql);
		if ($res)
		{
			while ($rec = $this->db->fetch_array($res))
			{
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'product_factory';
				$sql .= ' SET pmp= '.$rec['pmp'].', price='.$rec['price'];
				$sql .= ' where fk_product_father= '.$this->id ;
				$sql .= ' and fk_product_children= '.$rec['rowid'] ;

				if (! $this->db->query($sql))
				{
					dol_print_error($this->db);
					return -1;
				}
			}
			return 0;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


	function getdefaultpricetask($fk_task)
	{
		$sql = "SELECT p.rowid, p.pmp, p.price";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_taskdet as ptd, ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE ptd.fk_product = p.rowid";
		$sql.= " and ptd.fk_task = ".$fk_task;

		$res  = $this->db->query($sql);
		if ($res)
		{
			while ($rec = $this->db->fetch_array($res))
			{
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_taskdet';
				$sql .= ' SET pmp= '.$rec['pmp'].', price='.$rec['price'];
				$sql .= ' where fk_product='.$rec['rowid'];
				$sql .= ' and fk_task='. $fk_task;

				if (! $this->db->query($sql))
				{
					dol_print_error($this->db);
					return -1;
				}
			}
			return 0;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return childs of product with if fk_parent
	 *
	 * 	@param		int		$fk_parent	Id of product to search childs of
	 *  @return     array       		Prod
	 */
	function updatefactoryprices($fk_product_children, $pmp, $price)
	{
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'product_factory';
		$sql .= ' SET pmp= '.$pmp.', price='.$price;
		$sql .= ' where fk_product_father= '.$this->id;
		$sql .= ' and fk_product_children= '.$fk_product_children ;
//print $sql."<br>";
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return childs of prodcut with if fk_parent
	 *
	 * 	@param		int		$fk_parent	Id of product to search childs of
	 *  @return     array       		Prod
	 */
	function updatefactorytaskprices($fk_task, $fk_product, $pmp, $price)
	{
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_taskdet';
		$sql .= ' SET pmp='.$pmp.', price='.$price;
		$sql .= ' where fk_task= '.$fk_task;
		$sql .= ' and fk_product= '.$fk_product ;
//print $sql."<br>";
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return childs of product with if fk_parent
	 *
	 * 	@param		int		$fk_parent	Id of product to search childs of
	 *  @return     array       		Prod
	 */
	function updatefactorytaskqty($fk_task, $fk_product, $qtyused, $qtydeleted)
	{
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_taskdet';
		$sql .= ' SET qty_used='.($qtyused ? $qtyused : 'null').', qty_deleted='.($qtydeleted ? $qtydeleted : 'null');
		$sql .= ' where fk_task= '.$fk_task;
		$sql .= ' and fk_product= '.$fk_product ;
//print $sql."<br>";
		if (! $this->db->query($sql))
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 * 	Return tree of all subproducts for product. Tree contains id, name and quantity.
	 * 	Set this->sousprods
	 *
	 *  @return    	void
	 */
	function get_sousproduits_arbo()
	{
		$parent = $this->getParent();
		foreach($parent as $key => $value)
		{
			foreach($this->getChildsArbo($value[0]) as $keyChild => $valueChild)
			{
				$parent[$key][$keyChild] = $valueChild;
			}
		}
		foreach($parent as $key => $value)
		{
			$this->sousprods[$key] = $value;
		}
	}
	
	function set_datestartmade($user, $datestartmade)
	{
		global $conf, $langs;


		// c'est lors de la première validation que l'on effectue les mouvements de stocks des composants
		if ($user->rights->factory->creer)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
			$sql.= " SET date_start_made = ".($datestartmade ? $this->db->idate($datestartmade) :'null');
			$sql.= " , fk_statut =".($datestartmade ? '1' : '0');
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->date_start_made = $datestartmade;
				if ($datestartmade)
					$this->fk_statut  = 1;
				else
					$this->fk_statut  = 0;

				// on récupère les composants et on mouvemente le stock si cela n'est pas encore fait (idmvt à 0)
				$sql = "select * from ".MAIN_DB_PREFIX."factorydet where fk_factory=".$this->id;
				$sql.= " and fk_mvtstockplanned=0";
		
				$res  = $this->db->query($sql);
				if ($res)
				{
					require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
					$mouvP = new MouvementStock($this->db);
					while ($rec = $this->db->fetch_array($res))
					{
						$idmv=$mouvP->livraison($user, $rec['fk_product'], $this->fk_entrepot, $rec['qty_planned'], $rec['price'], $langs->trans("UsedforFactory",$this->ref), $this->date_start_made);
						// on indique que l'on a mouvementé le produit
						if ($idmv > 0 )
						{
							// on mémorise que l'on a fait le mouvement de stock (pour ne pas le faire plusieurs fois)
							$sql = "update ".MAIN_DB_PREFIX."factorydet set fk_mvtstockplanned=".$idmv;
							$sql.= " where rowid=".$rec['rowid'];
							$this->db->query($sql);
						}
					}
				}
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_datestartmade Erreur SQL ".$this->error,LOG_ERR);
				return -1;
			}
		}
	}

	function set_datestartplanned($user, $datestartplanned)
	{
		global $conf;

		if ($user->rights->factory->creer)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
			$sql.= " SET date_start_planned = ".($datestartplanned? $this->db->idate($datestartplanned) :'null');
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->date_start_planned = $datestartplanned;
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_datestartplanned Erreur SQL ".$this->error,LOG_ERR);
				return -1;
			}
		}
	}
	
	function set_dateendplanned($user, $dateendplanned)
	{
		global $conf;

		if ($user->rights->factory->creer)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
			$sql.= " SET date_end_planned = ".($dateendplanned? $this->db->idate($dateendplanned) :'null');
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->date_end_planned = $dateendplanned;
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_dateendplanned Erreur SQL ".$this->error,LOG_ERR);
				return -1;
			}
		}
	}
	
	function set_durationplanned($user, $durationplanned)
	{
		global $conf;

		if ($user->rights->factory->creer)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
			$sql.= " SET duration_planned = ".($durationplanned ? $durationplanned :'null');
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->duration_planned = $durationplanned;
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_durationplanned Erreur SQL ".$this->error,LOG_ERR);
				return -1;
			}
		}
	}
	function set_description($user, $description)
	{
		global $conf;

		if ($user->rights->factory->creer)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."factory ";
			$sql.= " SET description = '".$this->db->escape($description)."'";
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->description = $description;
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_description Erreur SQL ".$this->error,LOG_ERR);
				return -1;
			}
		}
	}
	
	
	/**
	 *  Return list of contacts emails or mobile existing for third party
	 *
	 *  @param	string	$mode       		'email' or 'mobile'
	 * 	@param	int		$hidedisabled		1=Hide contact if disabled
	 *  @return array       				Array of contacts emails or mobile
	*/
	function contact_entrepot_email_array($entrepotid, $mode='email', $hidedisabled=0)
	{
		$contact_property = array();
		
		// récupération des contact associé à l'entrepot
		$sql = "SELECT s.rowid, s.email, s.statut, s.lastname, s.firstname";
		$sql.= " FROM ".MAIN_DB_PREFIX."socpeople as s";
		$sql.= " ,".MAIN_DB_PREFIX."element_contact as ec";
		$sql.= " ,".MAIN_DB_PREFIX."c_type_contact  as tc";
		$sql.= " WHERE ec.element_id= ".$entrepotid;
		$sql.= " AND ec.fk_c_type_contact = tc.rowid";
		$sql.= " AND ec.fk_socpeople = s.rowid";
		$sql.= " AND tc.element =  'stock'";
		$sql.= " AND tc.active =1";
		
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$nump = $this->db->num_rows($resql);
			if ($nump)
			{
				$i = 0;
				while ($i < $nump)
				{
					$obj = $this->db->fetch_object($resql);
				
					// Show all contact. If hidedisabled is 1, showonly contacts with status = 1
					if ($obj->statut == 1 || empty($hidedisabled))
					{
						$contact_property[$obj->rowid] = trim(dolGetFirstLastname($obj->firstname,$obj->lastname))." &lt;".$obj->email."&gt;";
					}
				 	$i++;
				}
			}
		}
		else
		{
			dol_print_error($this->db);
		}
		return $contact_property;
	}

	function createmvtproject($projectid, $productid, $entrepotid, $qtylefted, $idmvt=-1)
	{
		global $user;
		
		$pmp=0;
		$price=0;
		
		// on récupère le pmp et le price pour une utilisation juste des prix
		$sql = "SELECT p.rowid, p.pmp, p.price";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE p.rowid=".$productid;

		$resql  = $this->db->query($sql);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			$pmp=$obj->pmp;
			$price=$obj->price;

		}

		// et on conserve le mouvement
		$sql = "insert into ".MAIN_DB_PREFIX."projet_stock";
		$sql.= " (fk_project, fk_product, fk_entrepot, qty_from_stock, date_creation, fk_user_author, pmp, price, fk_product_stock)";
		$sql.= " values (".$projectid.", ".$productid.", ".$entrepotid.", ".$qtylefted;
		$sql.= ", '".$this->db->idate(dol_now())."'"; // date de création alimenté automatiquement
		$sql.= ", ".$user->id;
		$sql.= ", ".$pmp.", ".$price.", ".$idmvt.")";
		//print $sql;
		$this->db->query($sql);

	}

	
}
?>
