<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location = "./ban_con_aut.php?resultados=1#{num_cia}";
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : depositos_banco -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Manual de Dep&oacute;sitos </p>
<form name="form" method="post" action="./ban_mnc_mde.php">
<input name="numfilas_ban" type="hidden" value="{numfilas_ban}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    </tr>
  <!-- START BLOCK : dep_ban -->
  <tr>
    <td class="tabla"><input name="id_ban{i}" type="checkbox" id="id_ban{i}" value="{id_ban}"></td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{cod_ban}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla"><strong>{importe}</strong></td>
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
    <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}"></td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{cod_mov}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla"><strong>{importe}</strong></td>
    <td class="rtabla"><input type="button" class="boton" onClick="divide_deposito({id})" value="Dividir"></td>
  </tr>
  <!-- END BLOCK : dep_lib -->
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
	
	function valida_registro() {
		document.form.submit();
	}
</script>
<!-- END BLOCK : depositos_libros -->
<!-- START BLOCK : no_depositos_libros -->
<p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay depósitos</font></strong></p>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
</p>
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
