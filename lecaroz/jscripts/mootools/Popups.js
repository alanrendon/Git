// JavaScript Document

var Popup = new Class({
	
	Implements: Options,
	
	options: {
		efectDuration: 100,
		shadowOpacity: 0.8,
		scrollBars: true
	},
	
	initialize: function(content, title, width, height, onOpen, onClose, options) {
		this.setOptions(options);
		
		this.title = title;
		this.content = content;
		this.width = width;
		this.height = height;
		
		this.onOpen = onOpen;
		this.onClose = onClose;
		
		this.bodySize = window.getScrollSize();
		this.scrollPosition = window.getScroll();
		this.availScreenSize = window.getSize();
		
		this.shadowLayer = new Element('div', {
			'id': 'shadowLayer',
			'styles': {
				'width': this.bodySize.x + 'px',
				'height': this.bodySize.y + 'px'
			}
		}).inject(document.body);
		
		new Fx.Tween(this.shadowLayer, {
			'duration': this.options.efectDuration
		}).start('opacity', 0, this.options.shadowOpacity);
		
		this.popupWrapper = new Element('div', {
			'id': 'popupWrapper'
		});
		
		this.popupTitle = new Element('div', {
			'id': 'popupTitle',
			'html': this.title
		}).inject(this.popupWrapper);
		
		this.popupCloseIcon = new Element('div', {
			'id': 'popupCloseIcon',
			'html': '&nbsp;'
		}).addEvent('click', function() {
			this.Close();
		}.bind(this)).inject(this.popupTitle);
		
		this.CloseIcon = new Element('img', {
			'src': 'imagenes/popupClose.png',
			'width': 16,
			'height': 16
		}).inject(this.popupCloseIcon);
		
		this.popupContent = new Element('div', {
			'id': 'popupContent',
			'align': 'center',
			'html': this.content,
			'styles': {
				'width': (this.width - 1) + 'px',
				'height': (this.height - 1) + 'px',
				'overflow': this.options.scrollBars ? 'auto' : 'visible'
			}
		}).inject(this.popupWrapper);
		
		this.popupLayer = new Element('div', {
			'id': 'popupLayer',
			'styles': {
				'margin-left': '-' + (this.width / 2) + 'px',
				'top': (this.scrollPosition.y + (this.availScreenSize.y / 2) - (this.height / 2) - 15) + 'px'
			}
		}).inject(this.shadowLayer, 'after');
		
		new Fx.Tween(this.popupLayer, {
			'duration': this.options.efectDuration
		}).start('width', 4, this.width).addEvent('complete', function() {
			new Fx.Tween(this.popupLayer, {
				'duration': 100
			}).start('height', 4, this.height + 30).addEvent('complete', function() {
				this.popupWrapper.inject(this.popupLayer).set('styles', {
					'opacity': 0,
					'display': 'block'
				});
				
				var popupWrapperEffect = new Fx.Tween(this.popupWrapper, {
					'duration': this.options.efectDuration
				}).start('opacity', 0, 1);
				
				if ($chk(this.onOpen)) {
					popupWrapperEffect.addEvent('complete', onOpen);
				}
			}.bind(this));
		}.bind(this));
	},
	
	Close: function() {
		var shadowEffect;
		
		new Fx.Tween(this.popupLayer, {
			'duration': this.options.efectDuration
		}).start('opacity', 1, 0).addEvent('complete', function() {
			shadowEffect = new Fx.Tween(this.shadowLayer, {
				'duration': this.options.efectDuration
			}).start('opacity', this.options.shadowOpacity, 0).addEvent('complete', function() {
				this.shadowLayer.destroy();
				this.popupLayer.destroy();
				
				if ($chk(this.onClose)) {
					this.onClose();
				}
			}.bind(this));
		}.bind(this));
		
		return shadowEffect;
	}
	
});
