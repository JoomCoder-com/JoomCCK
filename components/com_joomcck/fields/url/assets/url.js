!(function($) {
	"use strict";

	function Url(options) {

		var self = this;

		options = options || {};

		this.limit = options.limit || 0;
		this.id = options.id || 0;
		this.alert = options.limit_alert || 'You have reached the limits';
		this.label = options.labels || 0;
		this.default_labels = options.default_labels || 'Label';
		this.label_change = options.labels_change || 0;
		this.label1 = options.label1 || 'Url';
		this.label2 = options.label2 || 'Label';
		this.values = [];
		this.num = 0;
		this.key = 0;

		$('#add-url' + this.id).click(function() {
			if(!self.checkLimit()) {
				alert(self.alert);
				return;
			}
			self.createBlock('', self.default_labels);
		});
		this.checkLimit();
	};

	Url.prototype.checkLimit = function() {
		if(this.limit && this.num >= this.limit) {
			$("#add-url" + this.id).hide();
			return false;
		} else {
			$("#add-url" + this.id).show();
			return true;
		}
	};

	var old_val = '';

	Url.prototype.keyup = function(input, key) {
		var value = clean(input.value);
		if(old_val == value) {
			return;
		}
		old_val = value;
		if(((value != input.value) || (value != this.values[key])) && this.label_change) {
			$($(input).parent('div.url-item').children('input')[1]).val('');
		}
		this.values[key] = value;
		input.value = value;
	};

	Url.prototype.onblur = function(input, key) {

		if(!this.label_change) {
			return;
		}

		var label = $($(input).parent('div.url-item').children('input')[1]);
		var value = input.value;

		if(!value) {
			label.val('');
			return;
		}

		var cval = clean(value);

		if(cval != value) {

			input.value = cval;
			label.val('');
		}
		if(cval != this.values[key]) {
			label.val('');
			this.values[key] = cval;
		}

		if(!label.val()) {
			label.css("background", "url('"+Joomla.getOptions('system.paths').root+"/media/mint/img/loading.gif') no-repeat right/80px").val('Looking for title...');
			$.ajax({
				url: Joomcck.field_call_url,
				type: "POST",
				dataType: 'json',
				data: {
					field_id: this.id,
					func: "_gettitle",
					field: "url",
					url: value
				}
			}).done(function(json) {
				label.css("background", "transparent");

				if(!json) {
					return;
				}

				if(!json.success) {
					alert(json.error);
					return;
				}
				label.val(json.result);
			});
		}
	};

	Url.prototype.createBlock = function(url, labels, hits) {

		this.num++;
		this.key++;
		var self = this;
		var list = $('#url-list' + this.id);
		var key = this.key;//$('#url-list'+ this.id).children('div.url-item').length;
		var container = $(document.createElement('div')).attr('class', 'url-item input-group mb-3');
		this.values[key] = '';

		$('<button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>').click(function() {
			container.slideUp('fast', function() {
				$(this).remove();
				self.num--;
				self.checkLimit();
			});
		}).appendTo(container);


		var input = $(document.createElement('input')).attr({
			class: "form-control",
			placeholder: this.label1,
			type: "text",
			name: "jform[fields][" + this.id + "][" + key + "][url]",
			value: url
		}).bind('keyup', function() {
			self.keyup(this, key);
		}).bind('blur', function() {
			self.onblur(this, key);
		}).appendTo(container);

		if($.isArray(labels)) {
			var j = this.num - 1;
			if(this.num > labels.length) {
				j = labels.length - 1;
			}
			var label = labels[j];
		}
		else {
			var label = labels;
		}

		if(this.label) {

			container.append('<input placeholder="'+this.label2+'" ' + (!this.label_change ? 'readonly="readonly"' : '') + ' class="form-control" type="text" name="jform[fields][' + this.id + '][' + key + '][label]" value="' + label + '" id="url' + this.id + '-label' + key + '">');
			container.append('<input type="hidden" name="jform[fields][' + this.id + '][' + key + '][hits]" value="' + hits + '">');
		}

		list.append(container);
		list.append('<div class="clearfix"></div>');
		this.checkLimit();
	};

	function clean(val) {
		if(val.match(/^(http:\/|https:\/)$/)) {
			val += '/';
		}
		if(!val.match(/^(?:http:\/\/)|(?:https:\/\/)/)) {
			val = 'https://' + val;
		}
		return val;
	}

	window.joomcckUrlField = Url;

}(jQuery));