// JavaScript Document

window.addEvent('domready', function() {
	$$('table.tabla_captura')[0].getElements('tr').addEvents({
		'mouseover': function() {
			this.addClass('highlight');
		},
		'mouseout': function() {
			this.removeClass('highlight');
		}
	});
	
	$('display').addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: function() {
			if (this.get('alt') == 'left') {
				$$('.show').removeClass('show').addClass('hide');
				
				this.set({
					src: '/lecaroz/iconos/arrow_right_blue_round.png',
					alt: 'right'
				});
			} else if (this.get('alt') == 'right') {
				$$('.hide').removeClass('hide').addClass('show');
				
				this.set({
					src: '/lecaroz/iconos/arrow_left_blue_round.png',
					alt: 'left'
				});
			}
		}
	});
	
	$('enviar').addEvent('click', Enviar);
});

var Enviar = function() {
	new Request({
		'url': 'ListaNegraTrabajadores.php',
		'data': 'accion=enviar',
		'onRequest': function() {
			new Element('img', {
				id: 'loading',
				src: 'imagenes/_loading.gif',
				width: 16,
				height: 16
			}).inject($('enviar'), 'after');
		},
		'onSuccess': function(result) {
			$('loading').destroy();
			
			alert('Listado enviado a las panaderías');
		}
	}).send();
}
