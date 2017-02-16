<?php
class Actionscclinico
{ 
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectOptions($parameters, $object, $action, $hookmanager)
	{
		global $conf;
		dol_include_once('/cclinico/class/actionpacientes.class.php');
		dol_include_once('/cclinico/class/consultas.class.php');
		dol_include_once('/cclinico/class/pacientes.class.php');

		if (in_array('invoicecard', explode(':', $parameters['context'])))
		{
			$sql="
			SELECT
				a.fk_consulta
			FROM
				llx_facturas_consulta AS a
			INNER JOIN llx_facture as b on a.fk_factura=b.rowid
			WHERE
				a.statut = 1
			AND a.entity=".$conf->entity."
			AND b.entity=".$conf->entity."
			AND a.fk_factura = ".$object->id;
			$resql1=$object->db->query($sql);
			if ($resql1){
		        $num2 = $object->db->num_rows($resql1);
		        if ($num2>0)
		        {
		        	$res=$object->db->fetch_object($res);
		        	$consultas=new Consultas($object->db);
					$consultas->fetch($res->fk_consulta);
					print '<tr><td class="nowrap">Consulta</td><td>';
						print $consultas->getNomUrl(1);
					print '</td></tr>';
		        }
		    }
			

		}

		if ($parameters['id']>0 && $action!="edit")
		{
			$res=0;
			$consulta=new Consultas($object->db);
			$paciente=new Pacientes($object->db);

			$array=$consulta->listar_evento($object->id);
			
			if ($array->fk_consulta>0) {
				$consulta->fetch($array->fk_consulta);
				print '<tr><td class="tdtop">Consulta</td><td colspan="3">';
					print $consulta->getNomUrl(0);
				print '</td></tr>';
			}
			if ($array->fk_paciente>0) {
				$paciente->fetch($array->fk_paciente);
				print '<tr><td class="tdtop">Paciente</td><td colspan="3">';
					print $paciente->getNomUrl(0);
				print '</td></tr>';
			}
			
		}
		
		if (!isset($parameters['id']) && $action=="create") {
			$consultas=new Consultas($object->db);
			print '<tr><td class="nowrap">Paciente</td><td>';
				print $consultas->select_dolpacientes(GETPOST("paciente"), 'paciente', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
			print '</td></tr>';
		}
		if ($parameters['id']>0 && $action=="edit")
		{
			$consultas=new Consultas($object->db);
			print '<tr><td class="nowrap">Paciente</td><td>';

			$paciente=GETPOST("paciente","int");
			if (!$paciente>0 ) {
				$res=$object->db->query("SELECT a.fk_paciente FROM llx_eventos_consultas as a WHERE a.fk_evento=".$object->id);

				$res=$object->db->fetch_object($res);

				$paciente=$res->fk_paciente;
			}
				print $consultas->select_dolpacientes($paciente, 'paciente', 1, '', 0, '', 0, $conf->entity, 0, 0, '', 0, '', 'maxwidth300');
			print '</td></tr>';
			
		}

 
		if (! $error)
		{
			$this->results = array('myreturn' => $myvalue);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	function insertExtraFields($parameters, $object, $action, $hookmanager)
	{
		echo "string";
		if ($parameters['actcomm']>0 && $_POST["paciente"]>0 && $action="create") {
			$object->db->query("INSERT INTO llx_eventos_consultas (fk_paciente,fk_evento) values (".$_POST["paciente"].",".$parameters['actcomm'].");");
		}
		if ($parameters['actcomm']>0 && isset($_POST["paciente"]) && $action="update") {
			$object->db->query("UPDATE llx_eventos_consultas as a SET a.fk_paciente=".$_POST["paciente"]." WHERE a.fk_evento=".$parameters['actcomm']);
		}
	}



}

?>