<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/efectivos.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : tabla -->
<!--<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" valign="top">-->
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="print">
	<tr>
		<th height="35" colspan="2" class="tl" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{num_cia}</th>
		<th colspan="5" rowspan="2" class="tc" scope="col"><font size="+2">{nombre_cia}</font><br>
			({nombre_corto})</th>
		<th class="tr" scope="col">{num_cia}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
	</tr>
	<tr>
		<th height="35" colspan="2" class="tl" scope="col">&nbsp;</th>
		<th class="tr" scope="col" style="font-size:6pt;">{mes_escrito} {anio_escrito} </th>
	</tr>
	<tr>
		<th width="5%" class="print" scope="col">D&iacute;a</th>
		<th width="15%" class="print" scope="col">Efectivo</th>
		<th width="15%" class="print" scope="col">Dep&oacute;sito</th>
		<th width="15%" class="print" scope="col">Mayoreo</th>
		<th width="15%" class="print" scope="col">Oficina</th>
		<th width="15%" class="print" scope="col">Faltantes</th>
		<th width="15%" class="print" scope="col">Diferencia</th>
		<th class="print" scope="col">Total Dep&oacute;sitos</th>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia0}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color0}">{faltante0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color0}">{diferencia0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia1}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color1}">{faltante1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color1}">{diferencia1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia2}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color2}">{faltante2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color2}">{diferencia2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia3}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color3}">{faltante3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color3}">{diferencia3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia4}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color4}">{faltante4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color4}">{diferencia4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia5}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color5}">{faltante5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color5}">{diferencia5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia6}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color6}">{faltante6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color6}">{diferencia6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia7}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color7}">{faltante7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color7}">{diferencia7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia8}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color8}">{faltante8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color8}">{diferencia8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia9}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color9}">{faltante9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color9}">{diferencia9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia10}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color10}">{faltante10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color10}">{diferencia10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia11}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color11}">{faltante11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color11}">{diferencia11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia12}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color12}">{faltante12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color12}">{diferencia12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia13}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color13}">{faltante13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color13}">{diferencia13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia14}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color14}">{faltante14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color14}">{diferencia14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia15}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color15}">{faltante15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color15}">{diferencia15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia16}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina16}</font></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="rprint"><strong><font color="#{fal_color16}">{faltante16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color16}">{diferencia16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia17}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color17}">{faltante17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color17}">{diferencia17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia18}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color18}">{faltante18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color18}">{diferencia18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia19}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color19}">{faltante19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color19}">{diferencia19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia20}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color20}">{faltante20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color20}">{diferencia20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia21}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color21}">{faltante21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color21}">{diferencia21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia22}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color22}">{faltante22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color22}">{diferencia22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia23}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color23}">{faltante23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color23}">{diferencia23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia24}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color24}">{faltante24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color24}">{diferencia24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia25}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color25}">{faltante25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color25}">{diferencia25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia26}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color26}">{faltante26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color26}">{diferencia26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia27}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color27}">{faltante27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color27}">{diferencia27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia28}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color28}">{faltante28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color28}">{diferencia28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia29}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color29}">{faltante29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color29}">{diferencia29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');">
		<td class="print">{dia30}</td>
		<td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{deposito30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint" {bgcolor}><strong>{mayoreo30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#FFCC00">{oficina30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{fal_color30}">{faltante30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		<td class="rprint"><strong><font color="#{dif_color30}">{diferencia30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
		<td class="rprint"><strong><font color="#000099">{total30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr>
		<th class="rprint">Tot.</th>
		<th class="rprint_total">{total_efectivos}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{total_depositos}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{total_mayoreo}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total"><font color="#FFCC00">{total_oficina}</font>&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total"><font color="#{color_fal}">{total_faltantes}</font>&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total"><font color="#{color_dif}">{total_diferencias}</font>&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{gran_total}&nbsp;&nbsp;&nbsp;</th>
	</tr>
	<tr>
		<th class="rprint">Prom:</th>
		<th class="rprint_total">{promedio_efectivos}&nbsp;&nbsp;&nbsp; </th>
		<th class="rprint_total">{promedio_depositos}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{promedio_mayoreo}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{promedio_oficina}&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">&nbsp;&nbsp;&nbsp;</th>
		<th class="rprint_total">{promedio_total}&nbsp;&nbsp;&nbsp;</th>
	</tr>
	<tr>
		<td colspan="4" class="top_lv2" align="right">Porcentaje de dep&oacute;sito / efectivo </td>
		<td class="top_lv2" align="right">{porcentaje_depositos} %&nbsp;&nbsp;&nbsp;</td>
		<td class="tl_lv2" colspan="2">General</td>
		<td class="tr_lv2" align="right">{general}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="middle_lv2" align="right">Porcentaje de oficinas / efectivo </td>
		<td class="middle_lv2" align="right">{porcentaje_oficinas} %&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">Repartido</td>
		<td class="mr_lv2" align="right">{repartido}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="middle_lv2" align="right">Suma de Porcentajes </td>
		<td class="bottom_lv2" align="right">{suma_porcentajes} %&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">Diferencia</td>
		<td class="mr_lv2" align="right">{diferencia}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="middle_lv2" align="right">Total de Efectivo </td>
		<td class="box_lv2" align="right">{total_efectivos}&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">{tventas}</td>
		<td class="mr_lv2" align="right">{ventas}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="middle_lv2" align="right">Total de Gastos </td>
		<td class="box_lv2" align="right">{total_gastos}&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">{tmp_ventas}</td>
		<td class="mr_lv2" align="right"><font color="#0000FF">{mp_ventas}</font>&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="middle_lv2" align="right">Gastos Pagados </td>
		<td class="box_lv2" align="right">{total_egreso}&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">{tut_produccion}</td>
		<td class="mr_lv2" align="right"><font color="#0000FF">{ut_produccion}</font>&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="bottom_lv2" align="right">Gastos Retirados </td>
		<td class="box_lv2" align="right">{total_ingreso}&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv2" colspan="2">{tmp_produccion}</td>
		<td class="mr_lv2" align="right"><font color="#0000FF">{mp_produccion}</font>&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<th colspan="5" rowspan="3" class="gastos">GASTOS</th>
		<td class="ml_lv2" colspan="2">{tproduccion}</td>
		<td class="mr_lv2" align="right">{produccion}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="ml_lv2" colspan="2">{tfaltante}</td>
		<td class="mr_lv2" align="right">{faltante_pan}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="ml_lv2" colspan="2">{trezago}</td>
		<td class="mr_lv2" align="right">{rezago}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="g_top" align="center"><strong>Mov.</strong></td>
		<td colspan="2" class="g_top" align="center"><strong>Concepto</strong></td>
		<td class="g_top" align="center"><strong>Ingreso</strong></td>
		<td class="g_top" align="center">Egreso</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov0}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto0}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov1}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto1}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov2}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto2}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov3}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto3}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov4}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto4}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov5}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto5}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov6}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto6}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov7}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto7}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov8}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto8}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov9}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto9}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov10}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto10}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov11}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto11}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov12}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto12}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov13}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto13}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov14}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto14}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov15}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto15}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov16}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto16}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov17}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto17}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td class="g_middle" align="center">&nbsp;{mov18}</td>
		<td colspan="2" class="g_middle">&nbsp;{concepto18}</td>
		<td class="g_middle" align="right"><font color="#0000FF">{ingreso18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="g_middle" align="right"><font color="#FF0000">{egreso18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="ml_lv1" colspan="2">&nbsp;</td>
		<td class="mr_lv1">&nbsp;</td>
	</tr>
	<!--<tr>
          <td class="g_bottom" align="center">&nbsp;{mov19}</td>
          <td colspan="2" class="g_bottom">&nbsp;{concepto19}</td>
          <td class="g_bottom" align="right"><font color="#0000FF">{ingreso19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_bottom" align="right"><font color="#FF0000">{egreso19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>-->
	<tr>
		<td rowspan="2" align="right" class="bl">&nbsp;</td>
		<td colspan="2" align="right" class="tr_lv2"><strong>Sub Total ..............$</strong></td>
		<td class="box_lv2" align="right"><font color="#0000FF">{total_ingreso}</font>&nbsp;&nbsp;&nbsp;</td>
		<td class="box_lv2" align="right"><font color="#FF0000">{total_egreso}</font>&nbsp;&nbsp;&nbsp;</td>
		<td class="bl_lv1" colspan="2">&nbsp;</td>
		<td class="br_lv1">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="right" class="br_lv2"><strong>Gran Total ..............$</strong></td>
		<th class="rprint"><font color="#{total_gastos_color}">{total_gastos}</font>&nbsp;&nbsp;&nbsp;</th>
		<th class="print">Total $ </th>
		<th class="print" colspan="2">&nbsp;</th>
		<th class="br"><font color="#{repartido_color}">{repartido}</font>&nbsp;&nbsp;&nbsp;</th>
	</tr>
</table>
<!--</td>
  </tr>
</table>-->
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->
<!-- END BLOCK : tabla -->
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		//self.close();
	}
	//window.onload = imprimir();
</script>
</body>
</html>
