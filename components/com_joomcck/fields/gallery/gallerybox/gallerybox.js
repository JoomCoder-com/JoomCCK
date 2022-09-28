
var Gallerybox = new Class({


	Implements: Options,

	options: {
		resizeDuration: 250,
		resizeTransition: Fx.Transitions.Circ.easeOut,
		initialWidth: 250,
		initialHeight: 250,
		padding: 10,
		animateCaption: true,
		show_rate: 1,

		texts:{
			counter: "Image {NUM} of {TOTAL}",
			sure: "Are you sure?"
		}
	},

	initialize: function(options){

		this.setOptions(options);
		this.ajax_anchors = Array();
		this.hrefs = Array();
		this.currentIndex = 0;
		key = "a[rel=gallerybox"+this.options.field_id+"_"+this.options.record_id+"]";
		this.anchors = $$(key);
		this.anchors.each(function(a){
			a.store("caption", a.get("title") || a.getElement("img").get("alt"));
			a.addEvent("click", this.open.pass([a], this));
		}, this);

		this.active = false;
		this.delay = false;
		if(!this.options.show_comments)
		{
			document.addEvent("mousewheel", function(event){
				this.mouseWheelListener.call(this, event);
			}.bind(this));
		}
		document.addEvent("keydown", function(event){
			this.keyboardListener.call(this, event);
			}.bind(this));

		window.addEvent("resize", this.recenter.pass([], this));
		window.addEvent("scroll", this.recenter.pass([], this));
	},

	createUI: function(){
		document.getElementById(document.body).adopt(
				$$([
					this.overlay = new Element("div", {id: "gmbOverlay"}).addEvent("click", this.close.pass([], this)),
					this.center = new Element("div", {id: "gmbCenter"})
				]).setStyle("display", "none")
			);

		this.container = new Element("div", {id: "gmbContainer"}).inject(this.center, "inside");

		this.closeLink = new Element("a", {id: "gmbCloseLink", href: "javascript:void(0);"}).addEvent("click", this.close.pass([], this)).inject(this.container, "inside");

		this.leftLink = new Element ("div" , {id: "leftLink"}).inject(this.container, "inside");
		this.prevLink = new Element("a", {id: "gmbPrevLink", href: "javascript:void(0);"}).addEvent("click", this.changeImage.pass([-1], this)).inject(this.leftLink, "inside");

		this.comments_list = new Element('ul',{'id':'comments_list'});
		this.commentsform = new Element('div',{'id':'commentsform'});
		this.media = new Element("div", {id: "gmbMedia"}).inject(this.container, "inside");

		this.title = new Element("div", {id: "gmbTitle"});
		this.imageManager = new Element ("div" , {id: "gmbImageManager"}).inject(this.container, "inside").adopt(
				this.title
			);
//		this.description = new Element("div", {id: "gmbDescription"}).inject(this.container, "inside");
		this.rating = new Element("div" , {id: "gmbRating"}).inject(this.container, "inside");
		this.download = new Element("div" , {id: "gmbDownload"}).inject(this.container, "inside");
		this.counter = new Element("div", {id: "gmbCounter"}).inject(this.container);

		this.rightLink = new Element ("div" , {id: "rightLink"}).inject(this.container, "inside");
		this.nextLink = new Element("a", {id: "gmbNextLink", href: "javascript:void(0);"}).addEvent("click", this.changeImage.pass([1], this)).inject(this.rightLink, "inside");
		this.accordion = new Element("div" , {id: "gmbAccordion", 'class':'accordion'}).inject(this.imageManager, "inside");
		this.infopanel = new Element('div', {id:'infopanel'});

		this.bottom = new Element("div", {id: "gmbBottom"}).inject(this.container, "inside");
		this.scroll = new Element("div", {id: "gmbScroll", style:"width:auto;"}).inject(this.bottom, "inside");

		new Scroller(this.bottom,{area: 150, velocity:0.05}).start();


		if(this.options.show_comments)
		{
			this.accordion.adopt(
				new Element('div',{'class':'accordion-group'}).adopt(
					new Element('div',{'class':'accordion-heading'}).adopt(
						new Element('a',{'class':'accordion-toggle', 'data-toggle':'collapse',
								'data-parent':'#gmbAccordion', 'href':'#commentsblock',
								'html':'Comments'
							})
					),
					new Element('div',{'id':'commentsblock', 'class':'accordion-body collapse in'}).adopt(
							this.comments_list,
							this.commentsform
					)
				)
			);
		}
		if(this.options.show_info)
		{
			var cls = '';
			if(!this.options.show_comments)
			{
				cls = ' in';
			}
			this.accordion.adopt(
				new Element('div',{'class':'accordion-group'}).adopt(
					new Element('div',{'class':'accordion-heading'}).adopt(
						new Element('a',{'class':'accordion-toggle', 'data-toggle':'collapse',
								'data-parent':'#gmbAccordion', 'href':'#imageinfo',
								'html':'Info'
							})
					),
					new Element('div',{'id':'imageinfo', 'class':'accordion-body collapse' + cls}).adopt(this.infopanel)
				)
			);
		}

    	var nextEffect = this.nextEffect.bind(this);

		this.fx = {
			overlay: new Fx.Tween(this.overlay, {
				property: "opacity"
			}),
			resize: new Fx.Morph(this.center, {
				duration: this.options.resizeDuration,
				transition: this.options.resizeTransition,
				onComplete: nextEffect
			}),

			show: new Fx.Tween(this.media, {
				property: "opacity",
				onComplete: nextEffect
			})
		};

	},

	open: function(link){
		this.createUI();
		this.active = true;
		this.thumbloaded = false;
		var scrollSize = window.getScrollSize();

		var top = window.getScrollTop() + (window.getHeight()/2);
		var left = window.getScrollLeft() + (window.getWidth()/2);

		this.overlay.setStyles({
			opacity: 0,
			visibility: 'visible',
			display: "block",
			width: scrollSize.x,
			height: scrollSize.y
		});
		this.center.setStyles({top: top, left: left, width: 100, height: 100, display: ""});

		this.centerResize();
		this.fx.overlay.start(0.8);

		this.startLoad(link);

		return false;
	},

	centerResize: function()
	{
		centerWidthMargin = this.center.getStyle('padding-left').toInt()+this.center.getStyle('padding-right').toInt();
		centerHeightMargin = this.center.getStyle('padding-top').toInt()+this.center.getStyle('padding-bottom').toInt();

		centerOutMargin = 10;

		centerWidth = window.getWidth() - centerWidthMargin - centerOutMargin * 2;
		centerHeight = window.getHeight() - centerHeightMargin - centerOutMargin * 2;

		bottomheight = 30;

		this.generalheight = centerHeight - this.center.getStyle('padding-bottom').toInt() - bottomheight;

		this.leftLink.setStyle("height" , this.generalheight);
		this.rightLink.setStyle("height" , this.generalheight);
		this.imageManager.setStyle("height" , this.generalheight);

		mediaWidth = centerWidth - this.center.getStyle('padding-left').toInt() - 100 - 250 - 10;
		mediaHeight = this.generalheight;

		this.media.setStyles({width: mediaWidth+"px", height: mediaHeight+"px"});
		this.fx.resize.start({width: centerWidth, height: centerHeight, marginTop: 0, marginLeft: 0,top: window.getScrollTop() + centerOutMargin, left: centerOutMargin});

		this.counter.setStyle('left', this.media.getSize().x - 50);
		this.counter.setStyle('top', 20);

		if(this.options.show_rate)
		{
			this.rating.setStyle('left', this.media.getSize().x - 130);
			this.rating.setStyle('top', this.media.getSize().y - 50);
		}
//		console.log(this.options);
//		if(this.options.allow_add_descr)
//		{
//			this.description.setStyle('left', this.media.getSize().x - 520);
//			this.description.setStyle('top', this.media.getSize().y - 50);
//		}

		this.download.setStyle('left', 70);
		this.download.setStyle('top', this.media.getSize().y - 45);
	},

	saveTitle: function() {
		var text = $('savetitletext');

		new Request.JSON({
			url: this.options.httpurl,
			method:'post',
			data:{
				field_id: this.options.field_id,
				func:'onSaveData',
				field:'gallery',
				record_id:this.options.record_id,
				context:text.value,
				type:'title',
				image_index:this.currentIndex
			},
			onComplete: function(json) {
				text.value = '';
				$$('titlebox','titleedit').toggleClass('disactiveInput');
				if(!json.success) { alert(json.error); return; }
				$('titletextspan').set('html', json.result);
			}
		}).send();
	},

	editTitle: function() {
		$('savetitletext').value = $('titletextspan').get('html');
		$$('titlebox','titleedit').toggleClass('disactiveInput');
	},

	deleteComment : function(id){
		if(!confirm(this.options.texts.sure))
		{
			return;
		}

		new Request.JSON({
			url: this.options.httpurl,
			method:'post',
			data:{
				field_id: this.options.field_id,
				func:'onDeleteComment',
				field:'gallery',
				record_id:this.options.record_id,
				context:id,
				image_index:this.currentIndex
			},
			onComplete: function(json) {
				if(!json.success)
    			{
    				alert(json.error);
    				return;
    			}
				$('comment'+id).destroy();
			}
		}).send();
	},

	saveComment: function() {
		var text = $('savecommenttext');
		new Request.JSON({
			url: this.options.httpurl,
			method:'post',
			data:{
				field_id: this.options.field_id,
				func:'onSaveData',
				field:'gallery',
				record_id:this.options.record_id,
				context:text.value,
				type:'comment',
				image_index:this.currentIndex
			},
			onComplete: function(json) {
				text.value = '';
				if(!json.success) { alert(json.error); return; }
				this.comments_list.grab(new Element('li', {id: 'comment'+json.result.key}).set('html', json.result.text));
			}.bind(this)
		}).send();
	},

	startLoad: function(link, preload){
		if(!link) return;

		if(this.thumbLoaded)
		{
			this.currentIndex = this.ajax_anchors.indexOf(link);
		}
		else
		{
			this.currentIndex = link.get("id");

		}

		var image = new Asset.image(link.get("href"), {
			onload: function(){
				if(!preload && this.currentLink == link) this.nextEffect();
			}.bind(this)
		});
		if(!preload){
			this.media.addClass("loading");
			this.media.setStyle("display", "block");
			this.media.empty();

			//this.bottom.setStyle("opacity", 0);
			this.prevLink.setStyle("display", "none");
			this.nextLink.setStyle("display", "none");
			this.currentLink = link;
			this.currentCaption = link.retrieve("caption");
			this.currentImage = image;
			this.step = 1;
		}
	},

	keyboardListener: function(event){
		if(!this.active) return;
		//if(event.key != "f5") event.preventDefault();
		switch (event.key){
			case "esc": this.close(); break;
			case "left": this.changeImage(-1, event); break;
			case "right": this.changeImage(1, event);
		}
	},

	mouseWheelListener: function(event){
		if(!this.active) return;
		if(event.wheel > 0) this.changeImage(-1, event);
		if(event.wheel < 0) this.changeImage(1, event);
	},

	changeImage: function(step, event){
		if(event)
			event.stop();
		if(this.thumbLoaded)
		{
			var link = this.ajax_anchors[parseInt(this.currentIndex)+step];
		}
		else
		{
			var link = this.anchors[parseInt(this.currentIndex)+step];
		}
		if(!link) return false;
		for(var f in this.fx) this.fx[f].cancel();
		this.startLoad(link);
	},

	resizeComments: function()
	{
		//console.log(this.options.show_comments);
		if(this.options.show_comments)
		{
			comments_height = this.generalheight - (this.bottom.getSize().y + this.commentsform.getSize().y + $$('#gmbAccordion .accordion-heading')[0].getSize().y * $$('#gmbAccordion .accordion-group').length);
//			comments_height -= ($$('#gmbAccordion a')[0].getSize().y + $$('#gmbAccordion a')[0].getStyle('padding-top').toInt() + $$('#gmbAccordion a')[0].getStyle('padding-bottom').toInt());
			this.comments_list.setStyle('height', comments_height);
		}
	},

	resizeInfo: function()
	{
		if(this.options.show_info)
		{
			info_height = this.generalheight - (this.bottom.getSize().y + $$('#gmbAccordion .accordion-heading')[0].getSize().y * $$('#gmbAccordion .accordion-group').length);
			this.infopanel.setStyle('height', info_height);
		}
	},

	resizeImage: function()
	{
		newHeight = this.currentImage.height;
		newWidth = this.currentImage.width;

		boxWidth = this.media.getWidth() - 20;
		boxHeight = this.media.getHeight() - 20;


		if(newWidth > boxWidth )
		{
			image_ratio =  newHeight / newWidth;
			newHeight = boxWidth * image_ratio;
			newWidth = boxWidth;
		}

		if( newHeight > boxHeight )
		{
			ratio = newWidth / newHeight;
			newWidth = boxHeight * ratio;
			newHeight = boxHeight;
		}


		this.currentImage.width = Math.abs(newWidth);
		this.currentImage.height = Math.abs(newHeight);
	},

	recenter: function(){	// Thanks to Garo Hussenjian (Xapnet Productions http://www.xapnet.com) for suggesting this addition
		if(this.active){
			if(this.delay){
				window.clearTimeout(this.delay);
			}
			this.delay = window.setTimeout(function(){
				this.centerResize();
				this.resizeComments();
				this.resizeInfo();
				this.resizeImage();
			}.bind(this), 200);
		}
	},

	nextEffect: function(){

		switch(this.step++){

			case 1:

				if(!this.thumbLoaded)
				{
					new Request.JSON({
			    		url: this.options.httpurl,
			    		method:'post',
			    		async: false,
			    		data:{
			    			field_id: this.options.field_id,
							func:'onGetThumbs',
							field:'gallery',
							record_id:this.options.record_id
						},
			    		onComplete: function(json) {
			    			if(!json)
			    			{
			    				return;
			    			}
			    			this.thumbLoaded = true;
			    			if(!json.success)
			    			{
			    				alert(json.error);
			    				return;
			    			}
			    			this.scroll.set('html', json.result.text);

			    			//this.scroll.setStyle('width', json.result.width + json.result.count * 4 );
			    			key = "a[rel=gallerybox_ajax"+this.options.field_id+"_"+this.options.record_id+"]";
			    			this.ajax_anchors = $$(key);
			    			this.ajax_anchors.each(function(a){
			    				a.store("caption", a.get("title") || a.getElement("img").get("alt"));
			    				this.hrefs.push(a.get('href'));
			    				a.removeEvent('click');
			    				a.addEvent("click", function(){
			    						this.startLoad(a);
			    						return false;
			    					}.bind(this));
			    			}, this);

			    			var total = this.ajax_anchors.length;
			    			//this.currentIndex = this.ajax_anchors.indexOf(link);
			    			//console.log(this.ajax_anchors.indexOf(this.currentLink.href));
			    			//console.log(this.currentLink,this.ajax_anchors);
			    			this.currentIndex = this.currentLink.id;
							var num = this.currentIndex.toInt() + 1;
							var counterText = this.options.texts.counter;
							counterText = counterText.replace(/\{NUM\}/, num);
							counterText = counterText.replace(/\{TOTAL\}/, total);
							this.counter.set("text", counterText);

			    		}.bind(this)
					}).send();

				}

				new Request.JSON({
					url: this.options.httpurl,
	        		method:'post',
	        		async: false,
	        		data:{field_id: this.options.field_id,
						func:'onGetImageInfo',
						field:'gallery',
						record_id:this.options.record_id,
						image_index:this.currentIndex
					},
	        		onComplete: function(json) {
	        			if(!json)
	        			{
	        				return;
	        			}
	        			if(!json.success)
	        			{
	        				alert(json.error);
	        				return;
	        			}
	        			this.title.set('html', json.result.title);
//	        			this.description.set('html', json.result.description);

	        			if(this.options.show_comments)
	    				{
	        				if(json.result.commentsform)
	        				{
	        					this.commentsform.set('html', json.result.commentsform);
	        				}
	        				this.resizeComments();
		        			this.comments_list.set('html', json.result.comments);
	    				}
	        			if(this.options.show_info)
	    				{
	        				this.infopanel.set('html', json.result.info);

		        			if(json.result.latlng.lat > 0 && this.options.show_location)
		        			{
		        				var maplayout = new Element('div', {id:'googlemap'});
		        				this.infopanel.grab(maplayout);
		        				myLatlng = new google.maps.LatLng(json.result.latlng.lat, json.result.latlng.lng);
		    					myOptions = {
		        				   zoom: 14,
		        				   center: myLatlng,
		        				   mapTypeId: google.maps.MapTypeId.ROADMAP,
		        				   draggable:true,
		        				   streetViewControl:false,
		        				   panControl: false,
		        				   rotateControl: false,
		        				   overviewMapControl:false
		        				 };
		   						var googlemap = new google.maps.Map(maplayout, myOptions);
			   					new google.maps.Marker({
			   					  'position' : myLatlng,
			   					  'map' : googlemap,
			   					  'draggable' : false
			   					 });
		        			}
		        			this.resizeInfo();
	    				}
	        			if(this.options.show_rate)
	        			{
		        			this.rating.set('html', json.result.rating);
		        			script = this.rating.getElement('script');
		        			if(script)
		    				{
		        				eval(script.get('html'));
		    				}
	        			}

	        			this.download.set('html', json.result.download);
	        			$$('#gmbContainer .hasTip').each(function(el) {
	        				var title = el.get('title');
	        				if (title) {
	        					var parts = title.split('::', 2);
	        					el.store('tip:title', parts[0]);
	        					el.store('tip:text', parts[1]);
	        				}
	        			});
	        			var JTooltips = new Tips($$('#gmbContainer .hasTip'),
		        			{
		        				maxTitleChars: 50,
		        				fixed: false,
		        				onShow: function(){
		        					this.tip.setStyle('display', 'block');
		        					this.tip.setStyle('z-index', '10000');
		        				}
		        			}
	        			);
	        		}.bind(this)
				}).send();
				this.nextEffect();
				break;
			case 2:
				this.media.removeClass("loading");
				this.media.setStyle("opacity", 0);

				newHeight = this.currentImage.height;
				newWidth = this.currentImage.width;

				boxWidth = this.media.getWidth() - 20;
				boxHeight = this.media.getHeight() - 20;
				if(newWidth > boxWidth )
				{
					image_ratio =  newHeight / newWidth;
					newHeight = boxWidth * image_ratio;
					newWidth = boxWidth;
				}

				if( newHeight > boxHeight )
				{
					ratio = newWidth / newHeight;
					newWidth = boxHeight * ratio;
					newHeight = boxHeight;
				}

				this.currentImage.width = Math.abs(newWidth);
				this.currentImage.height = Math.abs(newHeight);

				this.currentImage.inject(this.media);
				this.fx.show.start(1);
				break;
			case 3:

				var total = this.ajax_anchors.length;
				var num = this.currentIndex.toInt() + 1;
				var counterText = this.options.texts.counter;
				counterText = counterText.replace(/\{NUM\}/, num);
				counterText = counterText.replace(/\{TOTAL\}/, total);
				this.counter.set("text", counterText);

				this.prevLink.setStyle("display", "block");
				this.nextLink.setStyle("display", "block");
				break;
			case 4:
				if(this.thumbLoaded)
				{
					this.startLoad(this.ajax_anchors[this.currentIndex-1], true);
					this.startLoad(this.ajax_anchors[this.currentIndex+1], true);
				}
				else
				{
					this.startLoad(this.anchors[this.currentIndex-1], true);
					this.startLoad(this.anchors[this.currentIndex+1], true);
				}
				break;
		}
	},

	close: function(){
		this.center.destroy();
		this.overlay.destroy();
		this.active = false;
		this.thumbLoaded = false;
	}
});

