<?php
/* Copyright (C) 2013-2014	Charles-FR BENKE	<charles.fr@benke.fr>
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
 *	\defgroup   factory     Module gestion de la fabrication
 *	\brief      Module pour gerer les process de fabrication
 *	\file       htdocs/factory/core/modules/modFactory.class.php
 *	\ingroup    factory
 *	\brief      Fichier de description et activation du module factory
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *	Classe de description et activation du module Propale
 */
class modFactory extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db = $db;
		$this->numero = 160310;

		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion de la Fabrication";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.6.+1.1.5';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='factory@factory';

		// Data directories to create when module is enabled
		$this->dirs = array("/factory/temp");

		// Constantes
		$this->const = array();
		$r=0;
		$this->const[$r][0] = "FACTORY_ADDON_PDF";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "capucin";
		$this->const[$r][4] = 1;
		$r++;
		$this->const[$r][0] = "FACTORY_ADDON";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "mandrill";
		$this->const[$r][4] = 1;


		// Dependancies
		$this->depends = array();
		$this->requiredby = array();
		$this->config_page_url = array("factory.php@factory");
		$this->langfiles = array("propal","order","project","companies","products","factory@factory");

		$this->need_dolibarr_version = array(3, 4);

		// hook pour la recherche
		$this->module_parts = array('hooks' => array('searchform'));
		
		// Boites
		$this->boxes = array();
		$r=0;
		$this->boxes[$r][1] = "box_factory.php@factory";

		// Constants
		$this->const = array();
		$r=0;

		// Permissions
		$this->rights = array();
		$this->rights_class = 'factory';
		$r=0;

		$r++;
		$this->rights[$r][0] = 160310; // id de la permission
		$this->rights[$r][1] = 'Lire les fabrications'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire';
		$r++;
		$this->rights[$r][0] = 160311; // id de la permission
		$this->rights[$r][1] = 'cr&eacute;er une fabrication'; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'creer';
		$r++;
		$this->rights[$r][0] = 160312; // id de la permission
		$this->rights[$r][1] = 'Supprimer la fabrication'; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'delete';

		$r++;
		$this->rights[$r][0] = 160313; // id de la permission
		$this->rights[$r][1] = 'Envoyer par mail un Ordre de Fabrication'; // libelle de la permission
		$this->rights[$r][2] = 'e'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'send';
		$r++;
		$this->rights[$r][0] = 160314; // id de la permission
		$this->rights[$r][1] = "voir la tarification d'un ordre de fabrication"; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'showprice';
		$r++;
		$this->rights[$r][0] = 160315; // id de la permission
		$this->rights[$r][1] = 'Exporter les fabrication'; // libelle de la permission
		$this->rights[$r][2] = 'e'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'export';

		// Réappro Feature
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=product',
					'type'=>'left',
					'titre'=>'Factory',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/factory/list.php',
					'langs'=>'factory@factory',
					'position'=>100,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=product',
					'type'=>'left',
					'titre'=>'Declinaison',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/factory/product/declinaison.php',
					'langs'=>'factory@factory',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
					
		// additional tabs
		$this->tabs = array(
			  'product:+factory:Factory:@Produit:/factory/product/index.php?id=__ID__'
			, 'project:+factory:ProductNeed:@project:/factory/project/productinproject.php?id=__ID__'
			, 'task:+factory:Factory:@tasks:/factory/project/factorytask.php?id=__ID__&withproject=1'
			, 'stock:+contact:Contact:@stock:/factory/product/stock/contact.php?id=__ID__'
			, 'stock:+factory:Factory:@stock:/factory/product/stock/list.php?id=__ID__'
		);


		// Exports
		//--------
		$r=0;

	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $conf;

		// Permissions
		$this->remove($options);

		$sql = array();
		
		$result=$this->load_tables();

		return $this->_init($sql,$options);
	}

    /**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
     */
    function remove($options='')
    {
		$sql = array();
		return $this->_remove($sql,$options);
    }
    
	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /mymodule/sql/
	 *		This function is called by this->init.
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/factory/sql/');
	}
}
?>
