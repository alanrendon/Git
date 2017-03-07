<?php
require_once $url[0]."conex/conexion.php";

class Facturas_Cliente extends conexion {
	public function __construct() {
		parent::__construct();
	}

	public function get_facturasClientes() {
		$rows = array();
		$sql = 'SELECT
							facnumber,
							f.rowid,
							total_ttc,
							nom
						FROM
							llx_facture AS f
						LEFT JOIN '.PREFIX.'societe s ON f.fk_soc = s.rowid
						LEFT JOIN '.PREFIX.'contab_poliza_facture AS pf ON pf.id_facture = f.rowid
						WHERE
							fk_statut NOT IN (0, 3)
						AND
							pf.id_poliza IS NULL
					';
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}

		}
		return $rows;
	}

	public function get_facturasProveedor() {
		$rows = array();
		$sql = 'SELECT
							ref AS facnumber,
							f.rowid,
							total_ttc,
							nom
						FROM
							llx_facture_fourn f
						LEFT JOIN llx_societe s ON f.fk_soc = s.rowid
						LEFT JOIN llx_contab_poliza_facture AS pf ON pf.id_facture = f.rowid
						WHERE
							fk_statut NOT IN (0, 3)
						AND pf.id_poliza IS NULL';
		$query= $this->db->query($sql);
		if ($query) {
			$rows = array();
			while($row = $query->fetch_assoc())
			{
				$rows[] = $row;
			}

		}
		return $rows;
	}
}
