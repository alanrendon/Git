<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/efectivos.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" valign="top">
      <!-- START BLOCK : tabla -->
      <br>
      <table width="90%" cellpadding="0" cellspacing="0" class="print">
        <tr>
          <th height="75" colspan="2" class="tl" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{num_cia}</th>
          <th colspan="4" class="tc" scope="col"><font size="+2">{nombre_cia}</font><br>
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
          <td class="rprint" {bgcolor}><strong>{efectivo0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia1}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia2}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia3}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia4}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia5}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia6}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia7}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia8}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia9}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia10}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia11}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia12}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia13}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia14}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia15}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia16}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina16}</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia17}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia18}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia19}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia20}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total20}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia21}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total21}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia22}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total22}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia23}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total23}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia24}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total24}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia25}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total25}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia26}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total26}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia27}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total27}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia28}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total28}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia29}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total29}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="print">{dia30}</td>
          <td class="rprint" {bgcolor}><strong>{efectivo30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{deposito30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint" {bgcolor}><strong>{mayoreo30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong>{oficina30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
          <td class="rprint"><strong><font color="#{dif_color}">{diferencia30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></strong></td>
          <td class="rprint"><strong>{total30}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
        </tr>
        <tr>
          <th class="rprint">Tot.</th>
          <th class="rprint_total">{total_efectivos}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_depositos}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_mayoreo}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_oficina}&nbsp;&nbsp;&nbsp;</th>
          <th class="rprint_total">{total_diferencias}&nbsp;&nbsp;&nbsp;</th>
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
        <tr>
          <td colspan="4" class="top_lv2" align="right">Porcentaje de dep&oacute;sito / efectivo </td>
          <td class="top_lv2" align="right">{porcentaje_depositos} %&nbsp;&nbsp;&nbsp;</td>
          <td class="tl_lv2">General</td>
          <td class="tr_lv2" align="right">{general}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="middle_lv2" align="right">Porcentaje de oficinas / efectivo </td>
          <td class="middle_lv2" align="right">{porcentaje_oficinas} %&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">Repartido</td>
          <td class="mr_lv2" align="right">{repartido}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="middle_lv2" align="right">Suma de procentajes </td>
          <td class="bottom_lv2" align="right">{suma_porcentajes} %&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">Diferencia</td>
          <td class="mr_lv2" align="right">{diferencia}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="middle_lv2" align="right">Total de efectivo </td>
          <td class="box_lv2" align="right">{total_efectivos}&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">Ventas</td>
          <td class="mr_lv2" align="right">{ventas}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="middle_lv2" align="right">Total de gastos </td>
          <td class="box_lv2" align="right">{total_gastos}&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">MP/Ventas</td>
          <td class="mr_lv2" align="right">{mp_ventas}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="middle_lv2" align="right">Gastos pagados </td>
          <td class="box_lv2" align="right">{total_egreso}&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">UT/Producci&oacute;n</td>
          <td class="mr_lv2" align="right">{ut_produccion}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="bottom_lv2" align="right">Gastos retirados </td>
          <td class="box_lv2" align="right">{total_ingreso}&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv2">MP/Producci&oacute;n</td>
          <td class="mr_lv2" align="right">{mp_produccion}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <th colspan="5" rowspan="3" class="gastos">GASTOS</th>
          <td class="ml_lv2">Producci&oacute;n</td>
          <td class="mr_lv2" align="right">{produccion}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td class="ml_lv2">Faltante pan</td>
          <td class="mr_lv2" align="right">{faltante_pan}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td class="ml_lv2">Rezago</td>
          <td class="mr_lv2" align="right">{rezago}&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td class="g_top" align="center"><strong>Mov.</strong></td>
          <td colspan="2" class="g_top" align="center"><strong>Concepto</strong></td>
          <td class="g_top" align="center"><strong>Ingreso</strong></td>
          <td class="g_top" align="center">Egreso</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
        <tr>
          <td class="g_middle" align="center">{mov0}</td>
          <td colspan="2" class="g_middle">{concepto0}</td>
          <td class="g_middle" align="right">{ingreso0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso0}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
        <tr>
          <td class="g_middle" align="center">{mov1}</td>
          <td colspan="2" class="g_middle">{concepto1}</td>
          <td class="g_middle" align="right">{ingreso1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso1}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov2}</td>
          <td colspan="2" class="g_middle">{concepto2}</td>
          <td class="g_middle" align="right">{ingreso2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov3}</td>
          <td colspan="2" class="g_middle">{concepto3}</td>
          <td class="g_middle" align="right">{ingreso3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso3}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov4}</td>
          <td colspan="2" class="g_middle">{concepto4}</td>
          <td class="g_middle" align="right">{ingreso4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso4}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov5}</td>
          <td colspan="2" class="g_middle">{concepto5}</td>
          <td class="g_middle" align="right">{ingreso5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso5}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov6}</td>
          <td colspan="2" class="g_middle">{concepto6}</td>
          <td class="g_middle" align="right">{ingreso6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso6}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov7}</td>
          <td colspan="2" class="g_middle">{concepto7}</td>
          <td class="g_middle" align="right">{ingreso7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso7}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov8}</td>
          <td colspan="2" class="g_middle">{concepto8}</td>
          <td class="g_middle" align="right">{ingreso8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso8}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov9}</td>
          <td colspan="2" class="g_middle">{concepto9}</td>
          <td class="g_middle" align="right">{ingreso9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso9}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov10}</td>
          <td colspan="2" class="g_middle">{concepto10}</td>
          <td class="g_middle" align="right">{ingreso10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso10}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov11}</td>
          <td colspan="2" class="g_middle">{concepto11}</td>
          <td class="g_middle" align="right">{ingreso11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso11}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov12}</td>
          <td colspan="2" class="g_middle">{concepto12}</td>
          <td class="g_middle" align="right">{ingreso12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso12}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov13}</td>
          <td colspan="2" class="g_middle">{concepto13}</td>
          <td class="g_middle" align="right">{ingreso13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso13}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov14}</td>
          <td colspan="2" class="g_middle">{concepto14}</td>
          <td class="g_middle" align="right">{ingreso14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso14}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov15}</td>
          <td colspan="2" class="g_middle">{concepto15}</td>
          <td class="g_middle" align="right">{ingreso15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso15}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov16}</td>
          <td colspan="2" class="g_middle">{concepto16}</td>
          <td class="g_middle" align="right">{ingreso16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso16}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov17}</td>
          <td colspan="2" class="g_middle">{concepto17}</td>
          <td class="g_middle" align="right">{ingreso17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso17}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
		<tr>
          <td class="g_middle" align="center">{mov18}</td>
          <td colspan="2" class="g_middle">{concepto18}</td>
          <td class="g_middle" align="right">{ingreso18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_middle" align="right">{egreso18}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
        <tr>
          <td class="g_bottom" align="center">{mov19}</td>
          <td colspan="2" class="g_bottom">{concepto19}</td>
          <td class="g_bottom" align="right">{ingreso19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="g_bottom" align="right">{egreso19}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td class="ml_lv1">&nbsp;</td>
          <td class="mr_lv1">&nbsp;</td>
        </tr>
        <tr>
          <td rowspan="2" align="right" class="bl">&nbsp;</td>
          <td colspan="2" align="right" class="tr_lv2"><strong>Sub Total ..............$</strong></td>
          <td class="box_lv2" align="right">{total_ingreso}&nbsp;&nbsp;&nbsp;</td>
          <td class="box_lv2" align="right">{total_egreso}&nbsp;&nbsp;&nbsp;</td>
          <td class="bl_lv1">&nbsp;</td>
          <td class="br_lv1">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="right" class="br_lv2"><strong>Gran Total ..............$</strong></td>
          <th class="rprint">{total_gastos}&nbsp;&nbsp;&nbsp;</th>
          <th class="print">Total $  </th>
          <th class="print">&nbsp;</th>
          <th class="br">{repartido}&nbsp;&nbsp;&nbsp;</th>
        </tr>
    </table></td>
  </tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		//self.close();
	}
	window.onload = imprimir();
</script>
</body>
</html>
