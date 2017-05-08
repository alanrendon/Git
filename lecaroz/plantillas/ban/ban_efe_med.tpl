<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/efectivos.css" rel="stylesheet" type="text/css">
</head>

<body>

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<!-- START BLOCK : mitad -->
<td width="50%" align="center" valign="middle">
<table width="90%" cellpadding="0" cellspacing="0" class="print">
        <tr>
          <th height="75" colspan="2" class="tl" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{num_cia}</th>
          <th colspan="4" class="tc" scope="col"><font size="+1">{nombre_cia}</font><br>
          ({nombre_corto})</th>
          <th class="tr" scope="col">{num_cia}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
        <tr>
          <th width="5%" class="print" scope="col">D&iacute;a</th>
          <th width="15%" class="print" scope="col">Efectivo</th>
          <th width="15%" class="print" scope="col">Dep&oacute;sito</th>
          <th width="15%" class="print" scope="col">Mayoreo</th>
          <th width="15%" class="print" scope="col">Oficina</th>
          <th width="15%" class="print" scope="col">Diferencia</th>
          <th class="print" scope="col">Total Dep&oacute;sitos</th>
        </tr>
        <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia0}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color0}">{diferencia0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total0}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia1}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color1}">{diferencia1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total1}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia2}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color2}">{diferencia2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total2}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia3}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color3}">{diferencia3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total3}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia4}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color4}">{diferencia4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total4}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia5}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color5}">{diferencia5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total5}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia6}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color6}">{diferencia6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total6}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia7}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color7}">{diferencia7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total7}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia8}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color8}">{diferencia8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total8}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia9}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color9}">{diferencia9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total9}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia10}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color10}">{diferencia10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total10}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia11}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color11}">{diferencia11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total11}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia12}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color12}">{diferencia12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total12}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia13}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color13}">{diferencia13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total13}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia14}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color14}">{diferencia14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total14}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia15}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color15}">{diferencia15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total15}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia16}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina16}</font></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="rprint"><strong><font color="#{dif_color16}">{diferencia16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total16}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia17}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color17}">{diferencia17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total17}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia18}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color18}">{diferencia18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total18}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia19}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color19}">{diferencia19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total19}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia20}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color20}">{diferencia20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total20}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia21}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color21}">{diferencia21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total21}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia22}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color22}">{diferencia22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total22}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia23}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color23}">{diferencia23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total23}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia24}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color24}">{diferencia24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total24}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia25}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color25}">{diferencia25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total25}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia26}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color26}">{diferencia26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total26}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia27}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color27}">{diferencia27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total27}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia28}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color28}">{diferencia28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total28}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia29}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color29}">{diferencia29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total29}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia30}</td>
          <td class="rprint" {bgcolor}><strong><font color="#000099">{efectivo30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#FFCC00">{oficina30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color30}">{diferencia30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong><font color="#000099">{total30}</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
        <tr>
          <th class="rprint">Tot.</th>
          <th class="rprint_total">{total_efectivos}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_depositos}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_mayoreo}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total"><font color="#FFCC00">{total_oficina}</font>&nbsp;&nbsp;&nbsp;</th>
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
          <th class="rprint_total">{promedio_total}&nbsp;&nbsp;&nbsp;</th>
        </tr>
    </table>
</td>
<!-- END BLOCK : mitad -->
</tr>
</table>
<!-- END BLOCK : hoja -->
</body>
</html>
