MintBBEditor = function() 
{
	this.texts = {
		promtImageURL: 'Enter the Image URL',
		promtURL: 'Enter the URL'
	},

	this.textarea = null;

	this.init = function(textAreaId) {
		this.textarea = document.getElementById(textAreaId);
	};
	
	this.doFontSize = function(size) {
		size = parseInt( size );
		
		// Code for IE
		if (document.selection) {
			this.textarea.focus();
			var sel = document.selection.createRange();
			//alert(sel.text);
			sel.text = '[size="' + size + '"]' + sel.text + '[/size]';
		}
		else 
		{  // Code for Mozilla Firefox
			var len 	= this.textarea.value.length;
			var start 	= this.textarea.selectionStart;
			var end 	= this.textarea.selectionEnd;
			
			
			var scrollTop 	= this.textarea.scrollTop;
			var scrollLeft 	= this.textarea.scrollLeft;

			
			var sel = this.textarea.value.substring(start, end);
			
			var rep = '[size="' + size + '"]' + sel + '[/size]';
			this.textarea.value =  this.textarea.value.substring(0,start) + rep + this.textarea.value.substring(end,len);
			
			this.textarea.scrollTop = scrollTop;
			this.textarea.scrollLeft = scrollLeft;
		}
	},
	
	this.doColor = function(color) {
		// Code for IE
		if (document.selection) {
			this.textarea.focus();
			var sel = document.selection.createRange();
			//alert(sel.text);
			sel.text = '[color="' + color + '"]' + sel.text + '[/color]';
		}
		else 
		{  // Code for Mozilla Firefox
			var len 	= this.textarea.value.length;
			var start 	= this.textarea.selectionStart;
			var end 	= this.textarea.selectionEnd;
			
			
			var scrollTop 	= this.textarea.scrollTop;
			var scrollLeft 	= this.textarea.scrollLeft;
			
			
			var sel = this.textarea.value.substring(start, end);
			
			var rep = '[color="' + color + '"]' + sel + '[/color]';
			this.textarea.value =  this.textarea.value.substring(0,start) + rep + this.textarea.value.substring(end,len);
			
			this.textarea.scrollTop = scrollTop;
			this.textarea.scrollLeft = scrollLeft;
		}
	},

	this.doImage = function()
	{
		var url 		= prompt(this.texts.promtImageURL + ':', 'http://');
		var scrollTop 	= this.textarea.scrollTop;
		var scrollLeft 	= this.textarea.scrollLeft;
		
		if (!url)
			return;

		if (document.selection) 
		{
			this.textarea.focus();
			var sel = document.selection.createRange();
			sel.text = '[img]' + url + '[/img]';
		}
		else 
		{
			var len = this.textarea.value.length;
			var start = this.textarea.selectionStart;
			var end = this.textarea.selectionEnd;
			
			var sel = this.textarea.value.substring(start, end);
			//alert(sel);
			var rep = '[img]' + url + '[/img]';
			this.textarea.value =  this.textarea.value.substring(0,start) + rep + this.textarea.value.substring(end,len);
			
				
			this.textarea.scrollTop = scrollTop;
			this.textarea.scrollLeft = scrollLeft;
		}

	},

	this.doURL = function()
	{
		var url 		= prompt(this.texts.promtURL + ':', 'http://');
		var scrollTop 	= this.textarea.scrollTop;
		var scrollLeft 	= this.textarea.scrollLeft;
		
		if (!url)
			return;

		if (document.selection) 
		{
			this.textarea.focus();
			var sel = document.selection.createRange();
			
			if(sel.text==""){
				sel.text = '[url]'  + url + '[/url]';
			} else {
				sel.text = '[url=' + url + ']' + sel.text + '[/url]';
			}
		}
		else 
		{
			var len = this.textarea.value.length;
			var start = this.textarea.selectionStart;
			var end = this.textarea.selectionEnd;
			
			var sel = this.textarea.value.substring(start, end);
			var rep = null;
			
			if (sel == ""){
				rep = '[url]' + url + '[/url]';
			} else {
				rep = '[url=' + url + ']' + sel + '[/url]';
			}
			//alert(sel);
			
			this.textarea.value =  this.textarea.value.substring(0,start) + rep + this.textarea.value.substring(end,len);
				
			this.textarea.scrollTop = scrollTop;
			this.textarea.scrollLeft = scrollLeft;
		}
	},

	this.doAddTags = function(tag1, tag2)
	{
		// Code for IE
		if (document.selection) {
			this.textarea.focus();
			var sel = document.selection.createRange();
			//alert(sel.text);
			sel.text = tag1 + sel.text + tag2;
		}
		else 
		{  // Code for Mozilla Firefox
			var len 	= this.textarea.value.length;
			var start 	= this.textarea.selectionStart;
			var end 	= this.textarea.selectionEnd;
			
			
			var scrollTop 	= this.textarea.scrollTop;
			var scrollLeft 	= this.textarea.scrollLeft;

			
			var sel = this.textarea.value.substring(start, end);
			//alert(sel);
			var rep = tag1 + sel + tag2;
			this.textarea.value =  this.textarea.value.substring(0,start) + rep + this.textarea.value.substring(end,len);
			
			this.textarea.scrollTop = scrollTop;
			this.textarea.scrollLeft = scrollLeft;
		}
	}	
}