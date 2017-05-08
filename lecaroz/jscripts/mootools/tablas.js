// JavaScript Document

// JavaScript Document
var Tabla = new Class({
	
	initialize: function()
	{
		// Obtener elementos 'input' tipo 'text' del formulario
		this.rows = $$('.linea_off, .linea_on');
		// Añadir estilos y eventos a los campos de captura 'cap'
		
		this.rows.each(function(row) {
			row.addEvents({
				'mouseover': function()
				{
					this.addClass('linea_highlight');
				},
				'mouseout': function()
				{
					this.removeClass('linea_highlight');
				},
			});
		});
	}

});
