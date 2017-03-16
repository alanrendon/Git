// JavaScript Document

function pantalla_completa() {
	window.moveTo(0-top.window.innerWidth-top.window.outerWidth,0-top.window.innerHeight-top.window.outerHeight);
	if (document.all) {
		//top.window.resizeTo(screen.width,screen.height);
	}
	//top.window.moveBy(top.window.innerWidth-top.window.outerWidth,top.window.innerHeight-top.window.outerHeight);
	//top.window.moveBy(-5,-30);
	/*else if (document.layers||document.getElementById) {
		if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth) {
			top.window.outerHeight = screen.availHeight;
			top.window.outerWidth = screen.availWidth;
		}
	}*/

	//window.resizeTo(screen.availWidth, screen.availHeight);
	//window.moveTo(0,0);
}

function abrir_ventana(url, nombre, opciones) {
	ventana = open(url,name,opciones);
	ventana.moveTo(0,0);
	ventana.moveBy(-3,-30);
	//ventana.moveBy(-(ventana.outerWidth-ventana.innerWidth),-(ventana.outerHeight-ventana.innerHeight));
}

function maximizar_ventana() {
	window.moveTo(0,0);
	if (document.all) {
		top.window.resizeTo(screen.availWidth,screen.availHeight);
	}
	else if (document.layers||document.getElementById) {
		if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth) {
			top.window.outerHeight = screen.availHeight;
			top.window.outerWidth = screen.availWidth;
			document.write(window.outerHeight);
		}
	}
}
