function InitAutocomplete(id) {
	var flags = [];
	var labels = [];
	var mapped = {};
	jQuery('#field_' + id + '_cnt').typeahead({
		items: 10,
		source: function(query, process) {
			return jQuery.get(Joomcck.field_call_url,
				{
					field_id: id,
					func: 'onGetCountriesCode',
					field: 'telephone',
					q: query
				},

				function(data) {
					if(!data) {
						return;
					}

					if(!data.result) {
						return;
					}

					labels = [];
					mapped = {};

					jQuery.each(data.result, function(i, item) {
						mapped[item.label] = item.value;
						flags[item.label] = item.flag;
						labels.push(item.label)
					});
					return process(labels);
				},
				'json'
			);
		},
		updater: function(item) {
			jQuery('#flag' + id).html(flags[item]);
			return mapped[item];
		},
		highlighter: function(item) {
			var flag = flags[item];
			var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
			highligted = item.replace(new RegExp('(' + query + ')', 'ig'), function($1, match) {
				return '<strong>' + match + '</strong>'
			});
			return flag + ' <span>' + highligted + '</span>';
		},
	});
}

function FilterTelephone(id, sid) {
	jQuery('#flt' + id).typeahead({
		items: 10,
		source: function(query, process) {
			return jQuery.get(Joomcck.field_call_url,
				{
					field_id: id,
					func: 'onFilterData',
					field: 'telephone',
					section: sid,
					q: query
				},

				function(data) {
					if(!data) {
						return;
					}

					if(!data.result) {
						return;
					}

					labels = [];
					mapped = {};

					jQuery.each(data.result, function(i, item) {
						mapped[item.label] = item.value
						labels.push(item.label)
					});
					return process(labels);
				},
				'json'
			);
		},
		updater: function(item) {
			return mapped[item];
		}
	});
}