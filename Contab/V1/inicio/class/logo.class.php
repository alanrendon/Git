<?php
#MAIN_INFO_SOCIETE_LOGO_MINI

require_once $url[0]."conex/conexion.php";
require_once "cat_cuentas.class.php";
require_once "asiento.class.php";


class Logo extends conexion {     
	public function __construct() { 
		parent::__construct(); 
	} 
	
    public function get_logo()
	{
		$img = false;
		$sql ='SELECT
					llx_const.`value`
				FROM
					llx_const
				WHERE
					llx_const.`name` = "MAIN_INFO_SOCIETE_LOGO_MINI"
				AND llx_const.entity = '.ENTITY;
		$query= $this->db->query($sql); 
		if ($query)
			$img = $query->fetch_object();

		if($img){
			$img = $this->get_url_img($img->value);
		}
		return $img;

	}

	public function get_url_img($name_img){
		return DOL_URL.'/viewimage.php?cache=1&modulepart=companylogo&file=thumbs%2F'.$name_img;
	}

}