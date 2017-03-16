<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Distribuci&oacute;n de Gas</title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Porcentajes de Distribuci&oacute;n de Gas</div>
  <div id="captura" align="center">
    <form action="DistribucionGas.php" method="post" name="DistribucionGas" class="formulario" id="DistribucionGas">
	  <table class="tabla_captura">
        <tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Rosticeria</th>
          <!-- <th scope="col">%</th> -->
          <th scope="col">Rosticeria</th>
          <!-- <th scope="col">%</th> -->
          <th scope="col">Rosticeria</th>
          <!-- <th scope="col">%</th> -->
          <th scope="col">Rosticeria</th>
          <!-- <th scope="col">%</th> -->
          <th scope="col">Rosticeria</th>
          <!-- <th scope="col">%</th> -->
        </tr>
        <!-- START BLOCK : fila -->
		<tr class="{estilo_linea}">
          <td><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
            {num_cia} {nombre_cia}</td>
          <td><input name="num_cia1[]" type="text" class="cap toPosInt alignRight Blue" id="num_cia1" onchange="cambiaCia({i}, 'num_cia1','nombre_cia1')" onkeydown="movCursor(event.keyCode,{i},false,'num_cia2',null,'num_cia2','num_cia1','num_cia1')" value="{num_cia1}" size="1" /><input name="nombre_cia1[]" type="text" class="disabled Blue" id="nombre_cia1" value="{nombre_cia1}" size="10" /></td>
          <!-- <td><input name="porc1[]" type="text" class="cap numPosFormat2 alignRight Blue" id="porc1" onkeydown="movCursor(event.keyCode,{i},false,'num_cia2','num_cia1','num_cia2','porc1','porc1')" value="{porc1}" size="3" maxlength="5" /></td> -->
		  <td><input name="num_cia2[]" type="text" class="cap toPosInt alignRight Red" id="num_cia2" onchange="cambiaCia({i}, 'num_cia2','nombre_cia2')" onkeydown="movCursor(event.keyCode,{i},false,'num_cia3','num_cia1','num_cia3','num_cia2','num_cia2')" value="{num_cia2}" size="1" /><input name="nombre_cia2[]" type="text" class="disabled Red" id="nombre_cia2" value=" {nombre_cia2}" size="10" /></td>
          <!-- <td><input name="porc2[]" type="text" class="cap numPosFormat2 alignRight Red" id="porc2" onkeydown="movCursor(event.keyCode,{i},false,'num_cia3','num_cia2','num_cia3','porc2','porc2')" value="{porc2}" size="3" maxlength="5" /></td> -->
		  <td><input name="num_cia3[]" type="text" class="cap toPosInt alignRight Blue" id="num_cia3" onchange="cambiaCia({i}, 'num_cia3','nombre_cia3')" onkeydown="movCursor(event.keyCode,{i},false,'num_cia4','num_cia2','num_cia4','num_cia3','num_cia3')" value="{num_cia3}" size="1" /><input name="nombre_cia3[]" type="text" class="disabled Blue" id="nombre_cia3" value="{nombre_cia3}" size="10" /></td>
          <!-- <td><input name="porc3[]" type="text" class="cap numPosFormat2 alignRight Blue" id="porc3" onkeydown="movCursor(event.keyCode,{i},false,'num_cia4','num_cia3','num_cia4','porc3','porc3')" value="{porc3}" size="3" maxlength="5" /></td> -->
		  <td><input name="num_cia4[]" type="text" class="cap toPosInt alignRight Red" id="num_cia4" onchange="cambiaCia({i}, 'num_cia4','nombre_cia4')" onkeydown="movCursor(event.keyCode,{i},false,'num_cia5','num_cia3','num_cia5','num_cia4','num_cia4')" value="{num_cia4}" size="1" /><input name="nombre_cia4[]" type="text" class="disabled Red" id="nombre_cia4" value="{nombre_cia4}" size="10" /></td>
          <!-- <td><input name="porc4[]" type="text" class="cap numPosFormat2 alignRight Red" id="porc4" onkeydown="movCursor(event.keyCode,{i},false,'num_cia5','num_cia4','num_cia5','porc4','porc4')" value="{porc4}" size="3" maxlength="5" /></td> -->
		  <td><input name="num_cia5[]" type="text" class="cap toPosInt alignRight Blue" id="num_cia5" onchange="cambiaCia({i}, 'num_cia5','nombre_cia5')" onkeydown="movCursor(event.keyCode,{i},true,'num_cia1','num_cia4',null,'num_cia5','num_cia5')" value="{num_cia5}" size="1" /><input name="nombre_cia5[]" type="text" class="disabled Blue" id="nombre_cia5" value="{nombre_cia5}" size="10" /></td>
          <!-- <td><input name="porc5[]" type="text" class="cap numPosFormat2 alignRight Blue" id="porc5" onkeydown="movCursor(event.keyCode,{i},true,'num_cia1','num_cia5',null,'porc5','porc5')" value="{porc5}" size="3" maxlength="5" /></td> -->
        </tr>
		<!-- END BLOCK : fila -->
      </table>
      <p>
        <input type="button" class="boton" value="Siguiente" onclick="validar()" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('DistribucionGas');
	
	// f.form.getElements('input[id=porc1]').each(function(el) {
	// 	el.addEvent('change', function() {
	// 		if (this.value.getVal() > 100) {
	// 			alert('El valor de porcentaje no puede ser mayor al 100%');
	// 			this.value = '';
	// 		}
	// 	});
	// });
	
	// f.form.getElements('input[id=porc2]').each(function(el) {
	// 	el.addEvent('change', function() {
	// 		if (this.value.getVal() > 100) {
	// 			alert('El valor de porcentaje no puede ser mayor al 100%');
	// 			this.value = '';
	// 		}
	// 	});
	// });
	
	// f.form.getElements('input[id=porc3]').each(function(el) {
	// 	el.addEvent('change', function() {
	// 		if (this.value.getVal() > 100) {
	// 			alert('El valor de porcentaje no puede ser mayor al 100%');
	// 			this.value = '';
	// 		}
	// 	});
	// });
	
	// f.form.getElements('input[id=porc4]').each(function(el) {
	// 	el.addEvent('change', function() {
	// 		if (this.value.getVal() > 100) {
	// 			alert('El valor de porcentaje no puede ser mayor al 100%');
	// 			this.value = '';
	// 		}
	// 	});
	// });
	
	// f.form.getElements('input[id=porc5]').each(function(el) {
	// 	el.addEvent('change', function() {
	// 		if (this.value.getVal() > 100) {
	// 			alert('El valor de porcentaje no puede ser mayor al 100%');
	// 			this.value = '';
	// 		}
	// 	});
	// });
	
	f.form.num_cia1[0].select();
});

function cambiaCia(i, c, n) {
	var num_cia = eval('f.form.' + c)[i];
	var nombre_cia = eval('f.form.' + n)[i];
	
	if (num_cia.value.getVal() > 0) {
		new Request({
			url: 'DistribucionGas.php',
			method: 'get',
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('La compañía ' + num_cia.value + ' no se encuentra en el catálogo');
					
					num_cia.value = '';
					nombre_cia.value = '';
				}
				else
					nombre_cia.value = nombre;
			}
		}).send('c=' + num_cia.value.getVal());
	}
	else {
		num_cia.value = null;
		nombre_cia.value = null;
	}
}

function validar() {
	// f.form.getElements('input[id=num_cia]').each(function(el, i) {
	// 	var total = 0;
		
	// 	total = f.form.porc1[i].value.getVal() + f.form.porc2[i].value.getVal() + f.form.porc3[i].value.getVal() + f.form.porc4[i].value.getVal() + f.form.porc5[i].value.getVal();
		
	// 	if (total > 100) {
	// 		alert('[Compañía ' + el.value + '] La suma de los porcentajes no debe ser mayor al 100%');
	// 		return false;
	// 	}
	// });

	var cias = [];
	var repetidos = [];

	$$('[id=num_cia1],[id=num_cia2],[id=num_cia3],[id=num_cia4],[id=num_cia5]').get('value').filter(function(value){return value.toInt()>0;}).each(function(cia, i) {
		if ( ! $chk(cias[cia])) {
			cias[cia.toInt()] = 1;
		} else {
			repetidos[cia.toInt()] = { num_cia: cia.toInt(), nombre: $$('[id=nombre_cia1],[id=nombre_cia2],[id=nombre_cia3],[id=nombre_cia4],[id=nombre_cia5]')[i].get('value') };
		}
	});

	// if ( !! repetidos) {
	// 	var string = 'Las siguientes compañías aparecen dos o más veces en el listado:\n\n';

	// 	repetidos.each(function(row) {
	// 		string += row.num_cia/* + ' ' + row.nombre*/ + '\n';
	// 	});

	// 	alert(string);

	// 	return false;
	// }
	
	if (confirm('¿Son correctos todos los datos?'))
		f.form.submit();
}

function movCursor(keyCode, index, next, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null) {
		if (index != null) {
			if (eval('f.form.' + enter + '.length') == undefined)
				eval('f.form.' + enter + '.select()');
			else if (eval('f.form.' + enter + '[' + (next ? index + 1 : index) + ']') != undefined)
				eval('f.form.' + enter + '[' + (next ? index + 1 : index) + '].select()');
		}
		else if ($(enter))
				$(enter).select();
	}
	else if (keyCode == 37 && lt != null) {
		if (index != null) {
			if (eval('f.form.' + lt + '.length') == undefined)
				eval('f.form.' + lt + '.select()');
			else if (eval('f.form.' + lt + '[' + index + ']') != undefined)
				eval('f.form.' + lt + '[' + index + '].select()');
		}
		else if ($(lt))
			$(lt).select();
	}
	else if (keyCode == 39 && rt != null) {
		if (index != null) {
			if (eval('f.form.' + rt + '.length') == undefined)
				eval('f.form.' + rt + '.select()');
			else if (eval('f.form.' + rt + '[' + index + ']') != undefined)
				eval('f.form.' + rt + '[' + index + '].select()');
		}
		else if ($(rt))
			$(rt).select();
	}
	else if (keyCode == 38 && up != null) {
		if (index != null) {
			if (eval('f.form.' + up + '.length') != undefined && eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + ']') != undefined)
				eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + '].select()');
		}
		else if ($(up))
			$(up).select();
	}
	else if (keyCode == 40 && dn != null) {
		if (index != null) {
			if (eval('f.form.' + dn + '.length') != undefined && eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + ']') != undefined)
				eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + '].select()');
		}
		else if ($(dn))
			$(dn).select();
	}
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
