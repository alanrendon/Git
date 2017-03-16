<?

/**
 * Smarty_Lecaroz class.
 *
 * @version 1.0.0
 * @author Carlos A. Candelario Corona <carlos.candelario@live.com.mx>
 * @access public
 *
 */

// Cargar librería Smarty
require ('/usr/local/lib/php/Smarty/Smarty.class.php');

/**
 * extiende la clase Smarty a Smarty_Lecaroz e inicializa las rutas de los directorios
 * de plantillas y cache
 */
class Smarty_Lecaroz extends Smarty
{

	/**#@-*/
    /**
     * El constructor de la clase.
     */
	function Smarty_Lecaroz()
	{
		// Extender el contructor de la clase Smarty
		$this->Smarty();
		
		// Definir las rutas de acceso a las plantillas y el cache para el Sistema Lecaroz
		$this->template_dir = '/var/www/html/lecaroz/smarty/templates/';
		$this->compile_dir  = '/var/www/html/lecaroz/smarty/templates_c/';
		$this->config_dir   = '/var/www/html/lecaroz/smarty/configs/';
		$this->cache_dir    = '/var/www/html/lecaroz/smarty/cache/';
		
		$this->caching = true;
   }

}

?>