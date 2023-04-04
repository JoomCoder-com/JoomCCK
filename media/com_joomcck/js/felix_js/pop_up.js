felixPopUp_v2 = {
	newPopUp : function(params) {
		var popUpWorkObject = {
			'pos_mode' : 'right',
			'slide_mode' : 'horizontal',
			'duration' : 500,
			'duration_close' : 500,
			'scope_class' : 'popup_scope',
			'width' : '',
			'height' : '',
			'fps' : 24,

			'attachments' : {},

			'state' : 'hidden',
			'animatingAttachment' : null,
			'toState' : null,
			'nextToSlideIn' : null,
			'onAfterSlideOut' : "",
			'onAfterSlideIn' : "",

			'constructor' : function(params) {
				for ( var key in params) {
					this[key] = params[key];
				}
			},

			'setAttachChildTryAgain' : function(p, c, params) {
				var today = new Date();
				var uniq_id = today.getUTCMilliseconds();
				uniq_id += today.getUTCSeconds() * 1000;

				uniq_id = "uniq_id" + uniq_id + "_"
						+ Math.round(Math.random() * 1000);
				eval('document.' + uniq_id + " = this;");
				eval('document.' + uniq_id
						+ "_params = {'p' : p, 'c' : c, 'params' : params };");

				window.setTimeout('document.' + uniq_id
						+ ".attachChildTryAgain('" + uniq_id + "', document."
						+ uniq_id + "_params )", 300);
			},

			'attachChildTryAgain' : function(uniq_id, params_dump) {
				eval('delete document.' + uniq_id + ";");
				this.attachChild(params_dump.p, params_dump.c,
						params_dump.params);
			},

			'attachChild' : function(p, c, params) {
				var c_id = c;
		        var c = document.getElementById( c );
		        
		        c.style.zIndex = '999999';
		        
		        var p_elem = document.getElementById( p );
		        
		        if( c == null || p_elem == null ){
		          this.setAttachChildTryAgain( p, c_id, params );
		          return;
		        }
		        
		        c.style.display = 'block';
		        
		        var storedAttachment = this.getAttachmentByMenuId( c.id );
		        
		         
		        if( storedAttachment != false ) {
		          this.attachments[ p ] = storedAttachment;
		        } else {
		          this.attachments[ p ] = this.prepareChild( c, p, params );
		          this.hideMenu( p );
		          this.hideScope( p );
		        }
		        
		        this.attachments[ p ].menu[ p+'_'+this.attachments[ p ].menu.id ] = this;
			},

			'getAttachmentByMenuId' : function(id) {
				for ( var key in this.attachments) {
					if (this.attachments[key].menu.id == id) {
						return this.attachments[key];
					}
				}
				return false;
			},

			'prepareChild' : function(scope, p, params) {
				var menu = scope.cloneNode(true);
				
				while (scope.childNodes.length)
					scope.removeChild(scope.childNodes[scope.childNodes.length - 1]);

				scope.parentNode.removeChild(scope);
				document.body.appendChild(scope);
				
				scope.appendChild(menu);
				scope.style.position = "absolute";
				scope.style.overflow = "hidden";
				scope.className = this.scope_class;
				scope.id = "";
				
				if (this.width) {
					menu.style.width = this.width + 'px';
				}
				if (this.height) {
					menu.style.height = this.height + 'px';
				}

				var attachment = {
					'menu' : menu,
					'scope' : scope
				};
				if (typeof params != "undefined") {
					for ( var key in params) {
						attachment[key] = params[key];
					}
				}

				return attachment;
			},

			'slideIn' : function(p, onAfterSlideIn) {
				
				if (!this.canAnimate('slideIn'))
					return;

				this.state = "moving";
				this.toState = "showed";

				if (typeof p == 'string') {
					var p = document.getElementById(p);
				}

				this.animatingAttachment = p.id;

				this.positioningBox(p, this.attachments[p.id].scope);

				this.unhideScope(p.id);
				this.hideMenu(p.id);

				if (typeof onAfterSlideIn != "undefined") {
					this.onAfterSlideIn = onAfterSlideIn;
				}

				this.animate(p.id);
			},

			'slideOut' : function(p, onAfterSlideOut) {
				if (!this.canAnimate('slideOut'))
					return;

				this.state = "moving";
				
				this.toState = "hidden";

				if (typeof p == 'string') {
					var p = document.getElementById(p);
				}

				this.animatingAttachment = p.id;

				if (typeof onAfterSlideOut != "undefined") {
					this.onAfterSlideOut = onAfterSlideOut;
				}

				FelixLib_v1.unsetEvent(document.body, 'click',
						this.tmpEventfunc);
				this.animate(p.id);
			},

			'toggle' : function(p) {
				if (!this.canAnimate('toggle'))
					return;

				if (this.state == "hidden") {
					this.slideIn(p);
					return;
				}

				if (typeof p == 'string') {
					var p = document.getElementById(p);
				}

				if (this.animatingAttachment != p.id) {
					this.nextToSlideIn = p.id;
					this.slideOut(this.animatingAttachment);
				} else {
					this.slideOut(p);
				}
			},

			'canAnimate' : function(action) {
				if (this.state == "moving")
					return false;

				switch (action) {
				case 'slideIn':
					if (this.state == "showed")
						return false;
					break;
				case 'slideIn':
					if (this.state == "hidden")
						return false;
					break;
				}
				return true;
			},

			'hideMenu' : function(p) {
				var m = this.attachments[p].menu;

				switch (this.slide_mode) {
				case 'horizontal':
					m.style.marginLeft = -m.offsetWidth + 'px';
					break;
				case 'vertical':
					m.style.marginTop = -m.offsetHeight + 'px';
					break;
				}
			},

			'hideScope' : function(p) {
				var s = this.attachments[p].scope.style.display = "none";
			},
			'unhideScope' : function(p) {
				var s = this.attachments[p].scope.style.display = "block";
			},

			'clickOut' : function(event, p) {
				var target = event.target ? event.target
						: event.srcElement ? event.srcElement : '';

				var in_menu = false;

				for ( var op = target; op; op = op.offsetParent) {
					if (op == this.attachments[p].scope) {
						in_menu = true;
					}
				}

				this.slideOut(p);
				if (!in_menu) {
					FelixLib_v1.unsetEvent(document.body, 'click',
							this.tmpEventfunc);
					this.tmpEventfunc = null;
				}
			},

			'afterSlideOut' : function(p) {
				this.state = "hidden";
				this.hideScope(p);

				if (this.onAfterSlideOut) {
					eval(this.onAfterSlideOut);
					this.onAfterSlideOut = "";
				} else if (typeof this.attachments[p].onAfterSlideOut != "undefined") {
					eval(this.attachments[p].onAfterSlideOut);
				}
			},

			'afterSlideIn' : function(p) {
				this.state = "showed";

				var menu_id = this.attachments[this.animatingAttachment].menu.id;

				eval("this.tmpEventfunc = function( event ){"
						+ "  document.getElementById('" + menu_id + "')." + p
						+ "_" + menu_id + ".clickOut( event, '" + p + "' );"
						+ "};");

				FelixLib_v1.setEvent(document.body, 'click', this.tmpEventfunc);
				
				var item = document.getElementById(menu_id);

				if (this.onAfterSlideIn) {
					eval(this.onAfterSlideIn);
					this.onAfterSlideIn = "";
				} else if (typeof this.attachments[p].onAfterSlideIn != "undefined") {
					eval(this.attachments[p].onAfterSlideIn);
				}
			},

			'animate' : function(p) {
				var menu = this.attachments[p].menu;
				var sProperty = (this.slide_mode == 'horizontal' ? 'marginLeft'
						: 'marginTop');
				var offset = (this.slide_mode == 'horizontal' ? menu.offsetWidth
						: menu.offsetHeight);

				var anim_params = {};

				var menu_id = this.attachments[this.animatingAttachment].menu.id;

				if (this.toState == 'hidden') {
					var to_cnd = -offset;
					var step = offset
							/ (this.duration_close / (1000 / this.fps));
					anim_params.onAfterAnimate = escape("document.getElementById('"
							+ menu_id
							+ "')."
							+ p
							+ "_"
							+ menu_id
							+ ".afterSlideOut('" + p + "')");
				} else {
					var to_cnd = 0;
					var step = offset / (this.duration / (1000 / this.fps));
					anim_params.onAfterAnimate = escape("document.getElementById('"
							+ menu_id
							+ "')."
							+ p
							+ "_"
							+ menu_id
							+ ".afterSlideIn('" + p + "')");
				}
				FelixLib_v1.styleAnimate(menu_id, sProperty, to_cnd, step,
						anim_params);
			},

			'positioningBox' : function(p, c) {
				var top = (this.pos_mode == "bottom") ? p.offsetHeight + 2 : 0;
				var left = (this.pos_mode == "right") ? p.offsetWidth + 2 : 0;

				for (; p; p = p.offsetParent) {
					top += p.offsetTop;
					left += p.offsetLeft;
				}

				c.style.position = "absolute";
				c.style.top = top + 'px';
				c.style.left = left + 'px';
			}
		}
		popUpWorkObject.constructor(params);
		return popUpWorkObject;
	}
};