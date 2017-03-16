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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliar Retiro Manualmente</p>
<form name="form" method="post" action="./ban_mov_ret.php">
<input name="id_ban" type="hidden" value="{id_ban}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <tr>
      <td class="tabla">{fecha}</td>
      <td class="tabla"><strong>{folio}</strong></td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><strong>{importe}</strong></td>
      </tr>
  </table>  
  <hr>
  <!-- START BLOCK : cheques -->
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Movimientos equivalentes</font></p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Folio</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="id" type="radio" value="{id}"></td>
    <td class="tabla">{fecha}</td>
    <td class="tabla"><strong>{folio}</strong></td>
    <td class="tabla">{concepto}</td>
    <td class="tabla"><strong>{importe}</strong></td>
    </tr>
	<!-- END BLOCK : fila -->
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar" onClick="valida_registro()"> 
  </p>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		var count = 0;
		
		if (document.form.id.length == undefined) {
			if (document.form.id.checked == true)
				count++;
		}
		else {
			for (i=0; i<document.form.id.length; i++)
				if (document.form.id[i].checked == true)
					count++;
		}
		
		if (count == 0) {
			alert("Debe seleccionar alguno de los retiros");
			return false;
		}
		else
			document.form.submit();
	}
</script>
  <!-- END BLOCK : cheques -->
  
  <!-- START BLOCK : no_cheques -->
  <p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay cheques con el mismo folio e importe </font></strong></p>
  <p><strong><font color="#FF0000" face="Geneva, Arial, Helvetica, sans-serif">
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input name="button" type="button" class="boton" onClick="valida_registro()" value="Conciliar"> 
</font></strong></p>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (confirm("¿Desea conciliar el movimiento?"))
			document.form.submit();
		else
			return false;
	}
</script>
  <!-- END BLOCK : no_cheques -->
 </form>
  </td>
</tr>
</table>
