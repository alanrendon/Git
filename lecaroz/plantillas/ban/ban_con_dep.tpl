<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : mes -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Conciliaci&oacute;n de Efectivos </p>
<form name="form" method="get" action="./ban_con_dep.php">
<table class="tabla">
  <tr>
    <!--
	<th class="vtabla" scope="col">Mes</th>
    <td class="vtabla" scope="col"><select name="mes" class="insert" id="mes">
      <option value="1" {1}>ENERO</option>
      <option value="2" {2}>FEBRERO</option>
      <option value="3" {3}>MARZO</option>
      <option value="4" {4}>ABRIL</option>
      <option value="5" {5}>MAYO</option>
      <option value="6" {6}>JUNIO</option>
      <option value="7" {7}>JULIO</option>
      <option value="8" {8}>AGOSTO</option>
      <option value="9" {9}>SEPTIEMBRE</option>
      <option value="10" {10}>OCTUBRE</option>
      <option value="11" {11}>NOVIEMBRE</option>
      <option value="12" {12}>DICIEMBRE</option>
    </select></td>
    <th class="vtabla" scope="col">A&ntilde;o</th>
    <td class="vtabla" scope="col"><input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.num_cia0.select();" value="{anio}" size="10" maxlength="10"></td>
  </tr>
  -->
  <th class="vtabla">Fecha de Corte </th>
  <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia0.select()" value="{fecha}" size="10" maxlength="10"></td>
</table>

<br>
<table class="tabla">
  <tr>
    <th colspan="10" class="tabla" scope="col">Compa&ntilde;&iacute;as que no se tomara en cuenta sus dep&oacute;sitos</th>
    </tr>
  <tr>
    <td class="tabla"><input name="num_cia0" type="text" class="insert" id="num_cia0" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia1.select();
else if (event.keyCode == 37) form.num_cia9.select();" value="147" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia1" type="text" class="insert" id="num_cia1" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia2.select();
else if (event.keyCode == 37) form.num_cia0.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia2" type="text" class="insert" id="num_cia2" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia3.select();
else if (event.keyCode == 37) form.num_cia1.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia3" type="text" class="insert" id="num_cia3" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia4.select();
else if (event.keyCode == 37) form.num_cia2.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia4" type="text" class="insert" id="num_cia4" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia5.select();
else if (event.keyCode == 37) form.num_cia3.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia5" type="text" class="insert" id="num_cia5" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia6.select();
else if (event.keyCode == 37) form.num_cia4.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia6" type="text" class="insert" id="num_cia6" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia7.select();
else if (event.keyCode == 37) form.num_cia5.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia7" type="text" class="insert" id="num_cia7" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia8.select();
else if (event.keyCode == 37) form.num_cia6.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia8" type="text" class="insert" id="num_cia8" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia9.select();
else if (event.keyCode == 37) form.num_cia7.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia9" type="text" class="insert" id="num_cia9" onFocus="for.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia0.select();
else if (event.keyCode == 37) form.num_cia8.select();" size="3" maxlength="3"></td>
  </tr>
</table>
<p>
  <input name="Button" type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		form.submit();
	}
	
	window.onload = document.form.fecha.select();
</script>
<!-- START BLOCK : mes -->

<!-- START BLOCK : cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Conciliaci&oacute;n de Efectivos </p>
<form name="form" method="get">
<input name="temp" type="hidden">
<input name="accion" type="hidden">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">Operadora</th>
  </tr>
  <tr>
    <td class="tabla"><strong><font size="+1">{num_cia} - {nombre_cia} </font></strong></td>
    <td class="tabla"><strong><font size="+1">{mes_escrito} </font></strong></td>
    <td class="tabla"><strong><font size="+1" color="#0000FF">{operadora}</font></strong></td>
  </tr>
</table>
<!-- START BLOCK : tabla -->
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">D&iacute;a</th>
    <th class="tabla" scope="col">Efectivo</th>
    <!-- START BLOCK : num_dep -->
    <th class="tabla" scope="col">Dep&oacute;sito {num_dep}</th>
    <!-- END BLOCK : num_dep -->
    <th class="tabla" scope="col"><font color="#FFFF00">Otros dep&oacute;sitos</font></th>
    <th class="tabla" scope="col">Diferencia</th>
    <th class="tabla" scope="col">Total Dep&oacute;sitos</th>
    <th class="tabla" scope="col"><input type="button" class="boton" onClick="recorrer(this.form)" value="R">    </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla"><input name="button" type="button" class="boton" onClick="mostrar({dia})" value="..."></td>
    <th class="tabla">{dia}</th>
    <td class="rtabla" {bgcolor}><strong>{font1}{efectivo}{font2}</strong></td>
    <!-- START BLOCK : depositos -->
    <td class="rtabla" {bgcolor}><strong>{deposito}</strong></td>
    <!-- END BLOCK : depositos -->
    <td class="rtabla"><strong>{otro_deposito}</strong></td>
    <td class="rtabla"><strong><font color="#{dif_color}">{diferencia}</font></strong></td>
    <th class="rtabla">{total}</th>
    <th class="tabla"><input name="dia[]" type="checkbox" id="dia" value="{dia}"></th>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="tabla">Totales</th>
    <th class="rtabla"><font size="+1">{total_efectivos}</font></th>
    <!-- START BLOCK : total_depositos -->
    <th class="rtabla"><font size="+1">{total_depositos}</font></th>
    <!-- END BLOCK : total_depositos -->
    <th class="rtabla"><font size="+1" color="#FFFF00">{total_otros_depositos}</font></th>
    <th class="rtabla"><font size="+1">{total_diferencias}</font></th>
    <th class="rtabla"><font size="+1">{gran_total}</font></th>
    <th class="tabla">
      <input type="button" class="boton" onClick="recorrer(this.form)" value="R">    </th>
  </tr>
  <tr>
    <th colspan="2" class="tabla">Porcentajes</th>
    <th class="rtabla">&nbsp;</th>
    <!-- START BLOCK : por_dep -->
    <th class="rtabla"><font size="+1" color="#0000CC">{por_dep}</font></th>
    <!-- END BLOCK : por_dep -->
    <th class="rtabla"><font size="+1" color="#990000">{por_otros}%</font></th>
    <th class="rtabla">&nbsp;</th>
    <th class="rtabla">&nbsp;</th>
    <th class="rtabla">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="2" class="tabla">Promedios</th>
    <th class="rtabla"><font size="+1">{promedio_efectivos}</font></th>
    <!-- START BLOCK : promedio_depositos -->
    <th class="rtabla"><font size="+1">{promedio_depositos}</font></th>
    <!-- END BLOCK : promedio_depositos -->
    <th class="rtabla"><font size="+1">{promedio_otros_depositos}</font></th>
    <th class="rtabla">&nbsp;</th>
    <th class="rtabla"><font size="+1">{promedio_total}</font></th>
    <th class="rtabla">&nbsp;</th>
  </tr>
</table>
<br>
<table width="100%">
<tr>
<td width="30%" align="center"><input type="button" class="boton" value="Imprimir" onClick="imprimir()"></td>
<td width="40%" align="center">
<table class="tabla">
  <tr>
    <td bgcolor="#FF6600">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td>&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif">Dep&oacute;sito no conciliado</font></td>
  </tr>
  <tr>
    <td bgcolor="#66CC00">&nbsp;</td>
    <td>&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif">Efectivo incompleto</font> </td>
  </tr>
  <tr>
    <td bgcolor="#FFFF00">&nbsp;</td>
    <td>&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif">Efectivo directo </font>  </td>
  </tr>
</table></td>
<td width="30%" align="center"><font face="Geneva, Arial, Helvetica, sans-serif" size="+1">Ir a:</font><br>
  <a href="./ban_esc_con.php?listado=cia&num_cia={num_cia}&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&tipo=todos&cuenta=1"><font face="Geneva, Arial, Helvetica, sans-serif" size="+1">Estado de Cuenta</font></a><br>
  <a href="./ban_dot_con.php?num_cia={num_cia}&mes={mes}&anio={anio}&tipo=desglozado&con=1"><font face="Geneva, Arial, Helvetica, sans-serif" size="+1">Otros Dep&oacute;sitos</font></a><br>
  <a href="./ban_dep_alt.php?num_cia={num_cia}&mes={mes}&anio={anio}&con=1"><font face="Geneva, Arial, Helvetica, sans-serif" size="+1">Depósitos Alternativos</font></a></td>
</tr>
</table><br>
<!-- END BLOCK : tabla -->
<!-- START BLOCK : vacia -->
<p><strong><font color="#FF0000" face="Geneva, Arial, Helvetica, sans-serif">No hay efectivos para la compañía</font></strong></p>
<!-- END BLOCK : vacia -->
  <table class="tabla">
  <tr>
    <td colspan="2" class="vtabla"><strong>Usted est&aacute; en: {num_cia} - {nombre_cia}</strong></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
  <td class="vtabla"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Ir a:</font></strong>
  <input name="idcia" type="hidden">
  <input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_compania(this,form.idcia,form.nombre_cia)">
  <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="40" maxlength="40"></td>
 <td class="vtabla"><input type="button" class="boton" value="Siguiente >>" onClick="siguiente()"></td>
</tr>
</table>
<p>
  <input type="button" class="boton" value="Terminar" onClick="terminar()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		var print_efectivos = window.open("./ban_con_dep_print.php","print","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		//print_efectivos.print();
		//print_efectivos.close();
	}
	
	function actualiza_compania(num_cia, id, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		idcia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		idcia[{num_cia}] = '{idcia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				id.value      = idcia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function mostrar(dia) {
		window.open("./ban_dep_dia.php?dia="+dia,"mostrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	}
	
	function recorrer(form) {
		var count = 0;
		
		if (form.dia.length == undefined) {
			count += form.dia.checked ? 1 : 0;
		}
		else {
			for (i = 0; i < form.dia.length; i++) {
				count += form.dia[i].checked ? 1 : 0;
			}
		}
		
		if (count == 0) {
			alert("Debe seleccionar al menos un dia");
			return false;
		}
		else {
			window.open("", "recorrer", "top=264,left=352,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=320,height=240");
			form.target = "recorrer";
			form.action = "ban_dep_rec.php";
			form.submit();
			form.target = "mainFrame";
			form.action = "./ban_con_dep.php";
		}
		
	}
	
	function siguiente() {
		if (document.form.num_cia.value > 0) {
			document.form.accion.value = "ir_a";
			document.form.submit();
		}
		else {
			document.form.accion.value = "siguiente";
			document.form.submit();
		}
	}
	
	function terminar() {
		document.form.accion.value = "terminar";
		document.form.submit();
	}
</script>
<!-- END BLOCK : cia -->

</body>
</html>
