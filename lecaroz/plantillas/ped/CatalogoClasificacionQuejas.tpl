<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cat&aacute;logo de Clasificaci&oacute;n de Quejas</title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css" media="print">
.noPrint {
	display: none;
}
</style>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Cat&aacute;logo de Clasificaci&oacute;n de Quejas de Pedidos </div>
  <div id="captura" align="center">
    <form action="CatalogoClasificacionQuejas.php" method="post" name="Formulario" class="formulario" id="Formulario">
	  <div style="display:none;">
	    <input name="concepto_copy" type="text" class="cap toText clean toUpper" id="concepto_copy" size="50" maxlength="100" />
	  </div>
	  <table class="tabla_captura">
        <tr>
	      <th scope="col">Concepto</th>
          <th scope="col" class="noPrint"><img src="imagenes/insert16x16.png" name="insert" width="16" height="16" id="insert" /></th>
        </tr>
        <!-- START BLOCK : fila -->
		<tr class="linea_{row_style}">
	      <td>{concepto}</td>
          <td align="center" class="noPrint"><img src="imagenes/pencil16x16.png" alt="{id}" name="mod" width="16" height="16" id="mod" /><img src="imagenes/delete16x16.png" alt="{id}" name="del" width="16" height="16" id="del" /></td>
        </tr>
		<!-- END BLOCK : fila -->
      </table>
	</form>
  </div>
</div>
<!-- START IGNORE -->
<script language="javascript" type="text/javascript">
<!--
var f;
var canModify = true;
var row_style = false;

window.addEvent('domready', function() {
	f = new Formulario('Formulario');
	
	$('insert').addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: modifyRec
	});
	
	$$('img[name=mod]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				el.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				el.setStyle('cursor', 'default');
			},
			click: modifyRec.pass([el.get('alt'), el.getParent('tr')])
		});
	});
	
	$$('img[name=del]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				el.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				el.setStyle('cursor', 'default');
			},
			click: deleteRec.pass([el.get('alt'), el.getParent('tr')])
		});
	});
});

var modifyRec = function() {
	if (!canModify) {
		alert('Otro registro ya esta siendo modificado');
		$('concepto').focus();
		return false;
	}
	
	if (arguments.length == 2) {
		var id = arguments[0];
		var tr = arguments[1];
		var concepto = arguments[1].getChildren('td')[0].get('text');
		
		tr.empty();
	}
	else {
		var table = this.getParent('table');
		
		var tr = new Element('tr', {
			'class': 'linea_' + (row_style ? 'on' : 'off')
		});
		
		row_style = !row_style;
	}
	
	canModify = false;
	
	var td = new Element('td', {
		align: 'center'
	});
	
	var input = $('concepto_copy').clone();
	input.cloneEvents($('concepto_copy'));
	input.addEvent('keydown', function(e) {
		if (e.key == 'enter')
			e.stop();
	});
	input.set({
		id: 'concepto',
		name: 'concepto'
	});
	if ($defined(concepto))
		input.set('value', concepto);
	input.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	
	var imgOk = new Element('img', {
		src: 'menus/insert.gif',
		id: 'ok',
		name: 'ok',
		width: 16,
		height: 16
	});
	var imgCancel = new Element('img', {
		src: 'imagenes/delete16x16.png',
		id: 'cancel',
		name: 'cancel',
		width: 16,
		height: 16
	});
	imgOk.addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		}
	});
	
	if ($defined(id))
		imgOk.addEvent('click', addRec.pass(id));
	else
		imgOk.addEvent('click', addRec.pass([]));
	
	imgCancel.addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		}
	});
	imgOk.inject(td);
	imgCancel.inject(td);
	td.inject(tr);
	
	if ($defined(id))
		imgCancel.addEvent('click', cancelMod.pass([td.getParent('tr'), id, concepto]));
	else
		imgCancel.addEvent('click', cancelMod.pass(td.getParent('tr')));
	
	if (!$defined(id))
		tr.inject(table);
	
	$('concepto').focus();
}

var cancelMod = function() {
	var tr = arguments[0];
	
	if (arguments.length > 1) {
		var td = new Element('td', {
			html: arguments[2]
		});
		
		tr.empty();
		td.inject(tr);
		
		var td = new Element('td', {
			align: 'center'
		});
		
		var imgMod = new Element('img', {
			src: 'imagenes/pencil16x16.png',
			id: 'mod',
			name: 'mod',
			width: 16,
			height: 16,
			alt: arguments[1]
		});
		var imgDel = new Element('img', {
			src: 'imagenes/delete16x16.png',
			id: 'del',
			name: 'del',
			width: 16,
			height: 16,
			alt: arguments[1]
		});
		imgMod.addEvents({
			mouseover: function() {
				this.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				this.setStyle('cursor', 'default');
			},
			click: modifyRec.pass([arguments[1], tr])
		});
		imgDel.addEvents({
			mouseover: function() {
				this.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				this.setStyle('cursor', 'default');
			},
			click: deleteRec.pass([arguments[1], tr])
		});
		imgMod.inject(td);
		imgDel.inject(td);
		td.inject(tr);
		
		canModify = true;
	}
	else {
		tr.destroy();
		row_style = !row_style;
	}
	
	canModify = true;
}

var addRec = function() {
	if ($('concepto').get('value') == '') {
		alert('El escribir el concepto');
		$('concepto').focus();
		return false;
	}
	
	var id_rec = arguments.length > 0 ? arguments[0].toInt() : 0;
	
	new Request({
		url: 'CatalogoClasificacionQuejas.php',
		method: 'post',
		data: {
			accion: id_rec > 0 ? 'update' : 'insert',
			id: id_rec > 0 ? id_rec : '',
			concepto: $('concepto').get('value')
		},
		onSuccess: function(id) {
			tr = $('concepto').getParent('tr');
			
			var td = new Element('td', {
				html: $('concepto').get('value')
			});
			
			tr.empty();
			td.inject(tr);
			
			var td = new Element('td', {
				align: 'center'
			});
			
			var imgMod = new Element('img', {
				src: 'imagenes/pencil16x16.png',
				id: 'mod',
				name: 'mod',
				width: 16,
				height: 16,
				alt: id
			});
			var imgDel = new Element('img', {
				src: 'imagenes/delete16x16.png',
				id: 'del',
				name: 'del',
				width: 16,
				height: 16,
				alt: id
			});
			imgMod.addEvents({
				mouseover: function() {
					this.setStyle('cursor', 'pointer');
				},
				mouseout: function() {
					this.setStyle('cursor', 'default');
				},
				click: modifyRec.pass([id, tr])
			});
			imgDel.addEvents({
				mouseover: function() {
					this.setStyle('cursor', 'pointer');
				},
				mouseout: function() {
					this.setStyle('cursor', 'default');
				},
				click: deleteRec.pass([id, tr])
			});
			imgMod.inject(td);
			imgDel.inject(td);
			td.inject(tr);
			
			canModify = true;
		}
	}).send();
}

var deleteRec = function() {
	var id = arguments[0];
	var tr = arguments[1];
	
	if (!confirm('¿Desea borrar el registro del catálogo?'))
		return false;
	
	new Request({
		url: 'CatalogoClasificacionQuejas.php',
		method: 'post',
		data: {
			accion: 'delete',
			id: id
		},
		onSuccess: function() {
			tr.getAllNext().each(function(el) {
				el.toggleClass('linea_off');
				el.toggleClass('linea_on');
			});
			
			tr.destroy();
		}
	}).send();
}
//-->
</script>
<!-- END IGNORE -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
