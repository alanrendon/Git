<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location = "./ban_mov_pen.php#{num_cia}";
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : depositos_banco -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Manual de Dep&oacute;sitos </p>
<form name="form" method="post" action="./ban_mov_dep.php">
<input name="numfilas_ban" type="hidden" value="{numfilas_ban}">
<input name="importe_libros" type="hidden" value="0">
<input name="importe_bancos" type="hidden" value="{importe_bancos}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">Inv</th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : dep_ban -->
  <tr>
    <td class="tabla"><input name="importe_ban{i}" type="hidden" value="{importe}"><input name="id_ban{i}" type="checkbox" id="id_ban{i}" value="{id_ban}" checked onClick="calcular(this,form.importe_ban{i},form.importe_bancos)"></td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{cod_ban}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla"><strong>{fimporte}</strong></td>
    <td class="rtabla"><input name="inv{i}" type="checkbox" id="inv{i}" value="TRUE"></td>
    <td class="rtabla">      <input name="button" type="button" class="boton" onClick="modifica_deposito({id_ban})" value="Modificar"></td>
  </tr>
  <!-- END BLOCK : dep_ban -->
</table>

  <hr>
<!-- START BLOCK : depositos_libros -->
<input name="numfilas_lib" type="hidden" value="{numfilas_lib}">
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Movimientos equivalentes</font></p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : dep_lib -->
  <tr>
    <td class="tabla"><input name="importe{i}" type="hidden" value="{importe}"><input name="id{i}" type="checkbox" id="id{i}" onClick="calcular(this,form.importe{i},form.importe_libros,total)" value="{id}"></td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{cod_mov}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla"><strong>{fimporte}</strong></td>
    <td class="rtabla"><input type="button" class="boton" onClick="divide_deposito({id})" value="Dividir"></td>
  </tr>
  <!-- END BLOCK : dep_lib -->
   <tr>
    <th colspan="4" class="tabla">Suma de dep&oacute;sitos seleccionados </th>
    <th class="rtabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" value="0.00" size="10" maxlength="10"></th>
    <td class="rtabla">&nbsp;</td>
  </tr>
</table>
  <p>
  <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar" onClick="valida_registro()">
</p>
<script language="javascript" type="text/javascript">
	function divide_deposito(id) {
		window.open("./ban_mnc_ded.php?id="+id,"borrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
	}
	
	function modifica_deposito(id) {
		window.open("./ban_dep_mov_minimod.php?id="+id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
	}
	
	function valida_registro() {
		if (parseFloat(document.form.importe_libros.value) > 0) {
			if (parseFloat(document.form.importe_libros.value) == parseFloat(document.form.importe_bancos.value)) {
				if (confirm("¿Desea conciliar el deposito?"))
					document.form.submit();
				else
					return false;
			}
			else {
				alert("Los importes de los registros seleccionados no coinciden");
				return false;
			}
		}
		else if (parseFloat(document.form.importe_libros.value) <= 0) {
			if (confirm("¿Desea conciliar el deposito?"))
				document.form.submit();
			else
				return false;
		}
	}
	
	function calcular(opcion, importe, total, suma) {
		var temp_importe = parseFloat(importe.value);
		var temp_total   = parseFloat(total.value);
		
		if (opcion.checked == true) {
			opcion.checked == false;
			temp_total = temp_total + temp_importe;
			total.value = temp_total.toFixed(2);
			suma.value = temp_total.toFixed(2);
		}
		else if (opcion.checked == false) {
			opcion.checked == true;
			temp_total = temp_total - temp_importe;
			total.value = temp_total.toFixed(2);
			suma.value = temp_total.toFixed(2);
		}
	}
</script>
<!-- END BLOCK : depositos_libros -->
<!-- START BLOCK : no_depositos_libros -->
<p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay depósitos</font></strong></p>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  &nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar" onClick="valida_registro()">
</p>
<script language="javascript" type="text/javascript">
	function modifica_deposito(id) {
		window.open("./ban_dep_mov_minimod.php?id="+id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
	}
	
	function valida_registro() {
		document.form.submit();
	}
</script>
<!-- END BLOCK : no_depositos_libros -->
</form>
</td>
</tr>
</table>
<!-- END BLOCK : depositos_banco -->

<!-- START BLOCK : no_depositos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No seleccionó depósitos</font></strong></p>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : no_depositos -->
