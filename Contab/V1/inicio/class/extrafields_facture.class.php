<?php
/**
* Clase para determinar la Divisa Actual
*/
require_once $url[0]."conex/conexion.php";
class ExtraFieldFacture extends conexion
{
	public $fk_object;
	public $tc;
	public $tms;

	public function __construct() {
		parent::__construct();
	}

	public function check_has_tc()
	{
		$active =false;
		$sql    = 'SELECT * 
				FROM information_schema.COLUMNS 
				WHERE 
				 TABLE_NAME = "llx_facture_extrafields"
				AND COLUMN_NAME = "tc"';

		$result = $this->db->query($sql);

		if ($result->num_rows>0) 
			$active =true;
		return $active;

	}

	public function check_has_tc_fourn()
	{
		$active =false;
		$sql    = 'SELECT * 
				FROM information_schema.COLUMNS 
				WHERE 
				 TABLE_NAME = "llx_facture_fourn_extrafields"
				AND COLUMN_NAME = "tc"';

		$result = $this->db->query($sql);

		if ($result->num_rows>0) 
			$active =true;
		return $active;

	}


	public function get_tc_facture()
	{
		$row= false;
		$sql = 'SELECT
					fe.fk_object,
					fe.tc,
					fe.tms
				FROM
						llx_facture_extrafields AS fe
				WHERE
					fe.fk_object = '.$this->fk_object.'
					';
			
		$query = $this->db->query($sql);
		if ($query) 
			$row = $query->fetch_object();
		return $row;
	}

	public function get_tc_facture_fourn()
	{
		$row= false;
		$sql = 'SELECT
					fe.fk_object,
					fe.tc,
					fe.tms
				FROM
						llx_facture_fourn_extrafields AS fe
				WHERE
					fe.fk_object = '.$this->fk_object.'
					';
		$query = $this->db->query($sql);
		if ($query) 
			$row = $query->fetch_object();
		return $row;
	}

	

}
?>