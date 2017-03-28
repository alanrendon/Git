<?php
$res=@include("../../main.inc.php");

$id=GETPOST('id');

//print $id."::<br>";
print "<table  width='100%'>";
	print "<tr>";
		print "<td colspan='16'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='16'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='16'>Datos de la empresa</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='16'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='2'>No. Proveedor</td>";
		print "<td colspan='2' style='border: 1px solid black;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		print "<td colspan='12'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='6' rowspan='4' width='20%' align='center' style='background-color: #6ea0c5'>".$conf->global->MAIN_INFO_SOCIETE_NOM."</td>";
		print "<td colspan='10'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='10'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='10'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='10'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='6' width='33%'>&nbsp;</td>";
		print "<td colspan='7' width='46%'>&nbsp;</td>";
		print "<td colspan='3' width='20%' style='color:red'>No. de Propuesta   &nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='16'>".$conf->global->MAIN_INFO_SOCIETE_ADDRESS." 
				".$conf->global->MAIN_INFO_SOCIETE_TOWN." C.P.".$conf->global->MAIN_INFO_SOCIETE_ZIP."</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='6' width='33%'>Tel. Oficina ".$conf->global->MAIN_INFO_SOCIETE_TEL." 
				Fax. ".$conf->global->MAIN_INFO_SOCIETE_FAX." Nextel</td>";
		print "<td colspan='7' width='46%'>&nbsp;</td>";
		print "<td colspan='3' width='20%' align='center' style='border: 1px solid black;'><strong>Fecha</strong></td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='16'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='6' rowspan='8' width='20%' >
				Cliente<br>
				Direccion<br>
				<br><br>
				No proyecto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
				Solicitante&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
				Nombre del Proy. <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
				</td>";
		print "<td colspan='10' >&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='6' >&nbsp;</td>";
	print "<td colspan='2' rowspan='5' align='right' style='border: 1px solid black;' width='15%'>
			Tiempo de entrega<br>
			Vigencia cotizaci&oacute;n<br>
			Moneda<br>
			Lugar de entrega<br>
			Condiciones de Pago
			</td>";
	print "<td colspan='2' rowspan='5' style='border: 1px solid black;' width='15%'>
			Dias<br>
			Dias<br>
			Pesos Mexicanos<br>
			Almacen Clte<br>
			&nbsp;
			</td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='6'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='6'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='6'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='6'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='12'>&nbsp;</td>";
	print "</tr>";
	print "<tr>";
	print "<td colspan='16'>&nbsp;</td>";
	print "</tr>";
print "</table>";