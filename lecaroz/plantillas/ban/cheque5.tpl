<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	margin-left: 0cm;
	margin-top: 0cm;
	margin-right: 0cm;
	margin-bottom: 0cm;
}
-->
</style>
<link href="./styles/cheques4.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cheque -->
<table width="100%%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top">
	<table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td class="poliza"><table  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="c11bis">&nbsp;</td>
            <td class="c12bis">&nbsp;</td>
            <td colspan="6" class="c13bis"><strong>{nombre_cia}</strong></td>
            </tr>
          <tr>
            <td class="c21">&nbsp;</td>
            <td class="c22">&nbsp;</td>
            <td class="c23">&nbsp;</td>
            <td class="c24">&nbsp;</td>
            <td colspan="2" class="c25"><strong>{rfc}</strong></td>
            <td class="c27">&nbsp;</td>
            <td class="c28">&nbsp;</td>
          </tr>
          <tr>
            <td class="c31">&nbsp;</td>
            <td class="c32">&nbsp;</td>
            <td colspan="4" class="c33">
			<!-- START BLOCK : para_abono -->
			PARA ABONO EN CUENTA BANCARIA DE: 
			<!-- END BLOCK : para_abono -->
			</td>
            <td class="c37">&nbsp;</td>
            <td class="c38">&nbsp;</td>
          </tr>
          <tr>
            <td class="c41">&nbsp;</td>
            <td class="c42">&nbsp;</td>
            <td class="c43">&nbsp;</td>
            <td class="c44">&nbsp;</td>
            <td class="c45">&nbsp;</td>
            <td class="c46">&nbsp;</td>
            <td class="c47">&nbsp;</td>
            <td class="c48">{fecha}</td>
          </tr>
          <tr>
            <td class="c51">&nbsp;</td>
            <td colspan="6" class="c52">{a_nombre}</td>
            <td class="c58bis"><strong>*${importe}*</strong></td>
          </tr>
          <tr>
            <td class="c61">&nbsp;</td>
            <td colspan="6" class="c62">*{importe_escrito}*</td>
            <td class="c58">&nbsp;</td>
          </tr>
           
          <tr>
		  <tr>
            <td class="c71">&nbsp;</td>
            <td colspan="7" align="center" valign="bottom" class="c72">{pseudo_banda}</td>
            </tr>
          <tr>
            <td>&nbsp;</td>
            <td class="c82">&nbsp;</td>
            <td class="c83">&nbsp;</td>
            <td class="c84">&nbsp;</td>
            <td class="c85">&nbsp;</td>
            <td class="c86">&nbsp;</td>
            <td class="c87">&nbsp;</td>
            <td class="c88">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="7" class="c92"><strong>{facturas_poliza}</strong><br>
              <strong>{concepto}</strong></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td class="cheque"><table  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="f1">&nbsp;</td>
            <td colspan="2" valign="bottom" class="cia"><strong class="font_size_10">{nombre_cia}</strong></td>
            </tr>
          <tr>
            <td class="f2">&nbsp;</td>
            <td colspan="2" valign="top"><strong class="font_size_8">G.OAM&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{rfc}</strong></td>
            </tr>
          <tr>
            <td class="f3">&nbsp;</td>
            <td colspan="2">
			<!-- START BLOCK : para_abono_cheque -->
			<span class="font_size_10">PARA ABONO EN CUENTA BANCARIA DE:</span>
			<!-- END BLOCK : para_abono_cheque -->
			</td>
            </tr>
          <tr>
            <td class="f4">&nbsp;</td>
            <td align="right" class="lugar_expedicion"><span class="font_size_6">LUGAR DE EXPEDICI&Oacute;N&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </td>
            <td align="right"><span class="font_size_10">{fecha}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
          </tr>
          <tr>
            <td class="f5">&nbsp;</td>
            <td valign="bottom"><span class="font_size_10">{a_nombre}</span></td>
            <td valign="bottom" nowrap><code>{strini}</code><br>
              <code>{strimpini}{importe}{strimpfin}</code></td>
          </tr>
          <tr>
            <td class="f6">&nbsp;</td>
            <td colspan="2" valign="top"><span class="font_size_8">*{importe_escrito}*</span><br>
              <span class="font_size_10"><strong>BANCO MERCANTIL DEL NORTE, S.A.</strong></span><br>
              <span class="font_size_6">INSTITUCION DE BANCA MULTIPLE.<br>
              GRUPO FINANCIERO BANORTE.<br><br>SUC. 0679 LINDA VISTA MEXICO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>CTA {cuenta}</strong><br>MEXICO, D.F.</span></td>
            </tr>
		  <tr>
            <td class="f7">&nbsp;</td>
            <td colspan="2" valign="bottom" class="c72micr"><code>{strbanini}{banda_micr}{strbanfin}</code></td>
            </tr>
          <tr>
            <td class="f8">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            </tr>
          <tr>
            <td class="f9">&nbsp;</td>
            <td colspan="2" class="c92"><strong>{facturas_cheque}</strong><br>
              <strong>{concepto}</strong></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- END BLOCK : cheque -->
<!-- START BLOCK : no_imprimir -->
<script language="javascript" type="text/javascript">
	function alerta() {
		alert("Coloque los cheques en la impresora con los folios {num_cheque1} al {num_cheque2} y presione Aceptar");
		window.opener.document.location = "./ban_che_shift.php";
		
		return false;
	}
	
	window.onload = alerta();
</script>
<!-- END BLOCK : no_imprimir -->
<!-- START BLOCK : poliza -->
<script language="javascript" type="text/javascript">
	function alerta() {
		alert("Coloque las polizas en la impresora y presione Aceptar");
		window.opener.document.location = "./ban_che_shift.php";
		
		return false;
	}
	
	window.onload = alerta();
</script>
<!-- END BLOCK : poliza -->
<!-- START BLOCK : imprimir -->
<script language="javascript" type="text/javascript">
	function imprimir() {
		alert("Coloque los cheques en la impresora con los folios {num_cheque1} al {num_cheque2} y presione Aceptar");
		window.print();
		self.close();
	}
	
	window.onload = imprimir();
</script>
<!-- END BLOCK : imprimir -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">window.onload = self.close();</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
