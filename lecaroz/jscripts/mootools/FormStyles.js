// JavaScript Document

var FormStyles = new Class({
	
	Form: null,
	
	initialize: function(form) {
		if ($chk(form)) {
			this.Form = form;
			
			this.addEventsToElements();
		}
	},
	
	addEventsToElements: function() {
		var elements = this.Form.getElements('input[class~=valid], textarea[class~=valid], select, input[type=file]');
		
		elements.each(function(element) {
			this.addElementEvents(element);
		}, this);
	},
	
	addElementEvents: function(element) {
		element.addEvents({
			'mouseover': function() {
				element.addClass('over');
			},
			'mouseout': function() {
				element.removeClass('over');
			},
			'focus': function() {
				element.addClass('highlight');
			},
			'blur': function() {
				element.removeClass('highlight');
			}
		});
	}
	
});
