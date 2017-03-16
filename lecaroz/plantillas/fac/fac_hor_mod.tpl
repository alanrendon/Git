<script type="text/javascript" language="JavaScript">
	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">MODIFICACI&Oacute;N AL CAT&Aacute;LOGO DE HORARIOS </P>
<form name="form" method="post" action="./actualiza_hor_pue.php?tabla={tabla}" >
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero
        <input name="tem" type="hidden" id="tem"></th>
      <th class="tabla" align="center">Descripci&oacute;n</th>
      <th class="tabla" align="center">Hora de entrada</th>
      <th class="tabla" align="center">Hora de salida</th>
      <th class="tabla" align="center">Eliminar</th>


    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{cod_horario} 
	    
        <input name="cod_horario{i}" type="hidden" id="cod_horario{i}" value="{cod_horario}"></th>

      <td class="tabla"><span class="rtabla">
        <input name="descripcion{i}" type="text" class="vinsert" id="descripcion{i}" value="{descripcion}" size="50" maxlength="50" onKeyDown="if (event.keyCode == 13) document.form.horaentrada{i}.select();">
      </span> </td>
      <td class="tabla"><span class="rtabla">
        <input name="horaentrada{i}" type="text" class="rinsert" id="horaentrada{i}" value="{horaentrada}" size="12" maxlength="12" onKeyDown="if (event.keyCode == 13) document.form.horasalida{i}.select();">
      </span></td>
      <td class="tabla"><span class="rtabla">
        <input name="horasalida{i}" type="text" class="rinsert" id="horasalida{i}" value="{horasalida}" size="12" maxlength="12" onKeyDown="if (event.keyCode == 13) document.form.descripcion{next}.select();">
      </span></td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.eliminar{i}.value=1; else if(this.checked==false)document.form.eliminar{i}.value=0;"><input type="hidden" name="eliminar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
	<!-- START BLOCK : contador -->
		<input name="cont" type="hidden" value="{cont}">
	<!-- END BLOCK : contador -->
</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>

