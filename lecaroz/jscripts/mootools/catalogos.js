// JavaScript Document

var consultaCatalogos = new Class({
	
	options:
	{
		method: 'get',
		update: null,
		evalScripts: true,
		onComplete: function() { console.log() },
		
		tabla: null,
		campos: [],
		condicion: {}
	},
	
	initialize: function(options)
	{
		this.setOptions(options);
		
		new Ajax('consultaCatalogos.php', {
			method: this.method,
			update: this.update,
			evalScripts: this.evalScripts,
			onComplete: this.onClomplete,
		});
	},
	
	obtenerRegistroCatalogo: function(){
									  }),
	
});